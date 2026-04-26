<?php
$method = $_SERVER['REQUEST_METHOD'];
$body   = getJsonBody();

switch ($method) {
    case 'GET':
        $rows = DB::q(
            "SELECT * FROM checklist_items ORDER BY sort_order, created_at"
        )->fetchAll();
        $todayDone = (int) DB::q(
            "SELECT COUNT(*) FROM checklist_items WHERE done = 1 AND DATE(done_at) = CURDATE()"
        )->fetchColumn();
        echo json_encode(['items' => $rows, 'today_done' => $todayDone]);
        break;

    case 'POST':
        $title = trim($body['title'] ?? '');
        if (!$title) { http_response_code(400); echo json_encode(['error' => 'Chybí název']); break; }
        $maxOrder = (int) DB::q("SELECT COALESCE(MAX(sort_order),0) FROM checklist_items")->fetchColumn();
        $id = DB::insert('checklist_items', [
            'title'      => $title,
            'sort_order' => $maxOrder + 1,
        ]);
        echo json_encode(['item' => DB::q("SELECT * FROM checklist_items WHERE id=?", [$id])->fetch()]);
        break;

    case 'PUT':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); break; }
        $data = [];
        if (array_key_exists('title', $body)) $data['title'] = $body['title'];
        if (array_key_exists('done', $body)) {
            $data['done']    = $body['done'] ? 1 : 0;
            $data['done_at'] = $body['done'] ? date('Y-m-d H:i:s') : null;
        }
        if (array_key_exists('sort_order', $body)) $data['sort_order'] = (int)$body['sort_order'];
        if ($data) DB::update('checklist_items', $data, $id);
        echo json_encode(['item' => DB::q("SELECT * FROM checklist_items WHERE id=?", [$id])->fetch()]);
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); break; }
        DB::q("DELETE FROM checklist_items WHERE id = ?", [$id]);
        echo json_encode(['ok' => true]);
        break;
}
