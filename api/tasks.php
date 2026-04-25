<?php
// Voláno z api.php — DB a auth již inicializovány
$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($method) {
    case 'GET':
        $status = $_GET['status'] ?? 'open';
        $type   = $_GET['type']   ?? '';   // 'work' | 'personal' | ''
        $history = $_GET['history'] ?? ''; // 'today' | 'week' | ''

        if ($history) {
            // Dokončené tasky pro historii
            $dateFilter = $history === 'today'
                ? 'AND DATE(done_at) = CURDATE()'
                : 'AND done_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
            $typeWhere = $type ? 'AND type = ?' : '';
            $params = $type ? [$type] : [];
            $rows = DB::q(
                "SELECT * FROM tasks WHERE status = 'done' $dateFilter $typeWhere ORDER BY done_at DESC",
                $params
            )->fetchAll();
            foreach ($rows as &$r) {
                $r['daktela_tickets'] = $r['daktela_tickets'] ? json_decode($r['daktela_tickets']) : [];
            }
            echo json_encode(['tasks' => $rows]);
            break;
        }

        // Fulltext search
        if ($search = trim($_GET['search'] ?? '')) {
            $like = '%' . $search . '%';
            $rows = DB::q(
                "SELECT * FROM tasks WHERE (title LIKE ? OR description LIKE ? OR ai_context LIKE ?) ORDER BY status ASC, quadrant ASC LIMIT 50",
                [$like, $like, $like]
            )->fetchAll();
            foreach ($rows as &$r) {
                $r['daktela_tickets'] = $r['daktela_tickets'] ? json_decode($r['daktela_tickets']) : [];
            }
            echo json_encode(['tasks' => $rows]);
            break;
        }

        // Fulltext search
        if ($search = trim($_GET['search'] ?? '')) {
            $like = '%' . $search . '%';
            $rows = DB::q(
                "SELECT * FROM tasks WHERE (title LIKE ? OR description LIKE ? OR ai_context LIKE ?) ORDER BY status ASC, quadrant ASC LIMIT 50",
                [$like, $like, $like]
            )->fetchAll();
            foreach ($rows as &$r) {
                $r['daktela_tickets'] = $r['daktela_tickets'] ? json_decode($r['daktela_tickets']) : [];
            }
            echo json_encode(['tasks' => $rows]);
            break;
        }

        $typeWhere = $type ? 'AND type = ?' : '';
        $params = $type ? [$type] : [];
        $rows = DB::q(
            "SELECT * FROM tasks WHERE status = 'open' $typeWhere ORDER BY quadrant, sort_order, created_at",
            $params
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['daktela_tickets'] = $r['daktela_tickets'] ? json_decode($r['daktela_tickets']) : [];
        }

        // KPI: dnešní hotové
        $todayDone = (int) DB::q(
            "SELECT COUNT(*) FROM tasks WHERE status = 'done' AND DATE(done_at) = CURDATE()"
        )->fetchColumn();

        echo json_encode(['tasks' => $rows, 'today_done' => $todayDone]);
        break;

    case 'POST':
        $title   = trim($body['title'] ?? '');
        if (!$title) { http_response_code(400); echo json_encode(['error' => 'Chybí název']); break; }
        $id = DB::insert('tasks', [
            'title'           => $title,
            'description'     => $body['description'] ?? '',
            'ai_context'      => $body['ai_context'] ?? '',
            'quadrant'        => in_array($body['quadrant'] ?? '', ['urgent_important','important','urgent','other'])
                                  ? $body['quadrant'] : 'other',
            'type'            => ($body['type'] ?? '') === 'personal' ? 'personal' : 'work',
            'due_date'        => $body['due_date'] ?: null,
            'daktela_tickets'  => json_encode($body['daktela_tickets'] ?? []),
            'sort_order'       => (int)($body['sort_order'] ?? 0),
            'recurrence'       => in_array($body['recurrence'] ?? '', ['weekly','monthly']) ? $body['recurrence'] : 'none',
            'recurrence_day'   => isset($body['recurrence_day']) ? (int)$body['recurrence_day'] : null,
        ]);
        $row = DB::q("SELECT * FROM tasks WHERE id = ?", [$id])->fetch();
        $row['daktela_tickets'] = json_decode($row['daktela_tickets']);
        echo json_encode(['task' => $row]);
        break;

    case 'PUT':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); break; }

        $data = [];
        $allowed = ['title','description','ai_context','quadrant','type','due_date','sort_order','daktela_tickets','recurrence','recurrence_day','recurrence_interval','recurrence_unit'];
        foreach ($allowed as $f) {
            if (array_key_exists($f, $body)) {
                $data[$f] = $f === 'daktela_tickets' ? json_encode($body[$f]) : ($body[$f] ?: null);
            }
        }
        // Dokončení tasku
        if (isset($body['status'])) {
            $data['status'] = $body['status'] === 'done' ? 'done' : 'open';
            $data['done_at'] = $body['status'] === 'done' ? date('Y-m-d H:i:s') : null;
        }
        if ($data) DB::update('tasks', $data, $id);

        // Opakující se task — vytvoř nový po dokončení
        if (($body['status'] ?? '') === 'done') {
            $orig = DB::q("SELECT * FROM tasks WHERE id = ?", [$id])->fetch();
            $rec  = $orig['recurrence'] ?? 'none';
            if ($rec !== 'none' && $orig['due_date']) {
                $base = new DateTime($orig['due_date']);
                if ($rec === 'weekly') {
                    $base->modify('+7 days');
                } elseif ($rec === 'monthly') {
                    $base->modify('+1 month');
                } elseif ($rec === 'custom') {
                    $interval = max(1, (int)($orig['recurrence_interval'] ?? 1));
                    $unit = in_array($orig['recurrence_unit'], ['days','weeks','months']) ? $orig['recurrence_unit'] : 'days';
                    $base->modify("+$interval $unit");
                }
                DB::q(
                    "INSERT INTO tasks (title, description, ai_context, quadrant, type, due_date, daktela_tickets, recurrence, recurrence_interval, recurrence_unit)
                     VALUES (?,?,?,?,?,?,?,?,?,?)",
                    [$orig['title'], $orig['description'], $orig['ai_context'], $orig['quadrant'], $orig['type'],
                     $base->format('Y-m-d'), $orig['daktela_tickets'], $rec,
                     $orig['recurrence_interval'] ?? 1, $orig['recurrence_unit'] ?? 'weeks']
                );
            }
        }

        $row = DB::q("SELECT * FROM tasks WHERE id = ?", [$id])->fetch();
        $row['daktela_tickets'] = json_decode($row['daktela_tickets'] ?? '[]');
        echo json_encode(['task' => $row]);
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); break; }
        DB::q("DELETE FROM tasks WHERE id = ?", [$id]);
        echo json_encode(['ok' => true]);
        break;
}
