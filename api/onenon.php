<?php
require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$body   = getJsonBody();
$action = $_GET['action'] ?? '';

// ── Přejmenování / update popisu osoby ────────────────────────────────────────
if ($method === 'PUT' && ($_GET['sub'] ?? '') === 'update_person') {
    $oldName = trim($body['old_name'] ?? '');
    $newName = trim($body['new_name'] ?? $oldName);
    $desc    = $body['description'] ?? null;
    $profile = isset($body['profile']) ? json_encode($body['profile']) : null;
    if (!$oldName) { http_response_code(400); echo json_encode(['error' => 'Chybí old_name']); exit; }

    if ($newName && $newName !== $oldName) {
        DB::q("UPDATE onenon_notes SET person=? WHERE person=?", [$newName, $oldName]);
        DB::q("UPDATE onenon_people SET name=? WHERE name=?", [$newName, $oldName]);
    }
    $target = $newName ?: $oldName;
    $sets = [];
    $vals = [];
    if ($desc !== null)    { $sets[] = 'description=?'; $vals[] = $desc; }
    if ($profile !== null) { $sets[] = 'profile=?';     $vals[] = $profile; }
    if ($sets) {
        $vals[] = $target; $vals[] = implode(',', $sets);
        DB::q("INSERT INTO onenon_people (name) VALUES (?) ON DUPLICATE KEY UPDATE " . implode(',', $sets), array_merge([$target], array_slice($vals, 0, count($sets))));
    } else {
        DB::q("INSERT IGNORE INTO onenon_people (name) VALUES (?)", [$target]);
    }
    echo json_encode(['ok' => true, 'name' => $target]);
    exit;
}

if ($method === 'GET') {
    $person = trim($_GET['person'] ?? '');
    if ($person) {
        $rows = DB::q("SELECT * FROM onenon_notes WHERE person=? ORDER BY meeting_date DESC", [$person])->fetchAll();
        foreach ($rows as &$r) {
            $r['action_items'] = json_decode($r['action_items'] ?? '[]', true);
            $r['tags']         = json_decode($r['tags'] ?? '[]', true);
        }
        $pd = DB::q("SELECT description, profile FROM onenon_people WHERE name=?", [$person])->fetch();
        $prof = $pd['profile'] ?? null;
        echo json_encode(['notes' => $rows, 'description' => $pd['description'] ?? '', 'profile' => $prof ? json_decode($prof, true) : null]);
    } else {
        $rows = DB::q(
            "SELECT n.person,
                COUNT(*) as count,
                MAX(n.meeting_date) as last_meeting_date,
                SUM(JSON_LENGTH(n.action_items)) as total_items,
                SUM((SELECT COUNT(*) FROM JSON_TABLE(n.action_items, '\$[*]' COLUMNS(done JSON PATH '\$.done')) jt WHERE done = 'false' OR done = false)) as open_items,
                p.description, p.profile
             FROM onenon_notes n
             LEFT JOIN onenon_people p ON p.name = n.person
             GROUP BY n.person, p.description, p.profile
             ORDER BY n.person ASC"
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['count']      = (int)$r['count'];
            $r['open_items'] = (int)($r['open_items'] ?? 0);
            $daysAgo = $r['last_meeting_date']
                ? (int)((strtotime('today') - strtotime($r['last_meeting_date'])) / 86400)
                : null;
            $r['days_since'] = $daysAgo;
            $r['profile']    = isset($r['profile']) ? json_decode($r['profile'], true) : null;
        }
        echo json_encode(['people' => $rows]);
    }
    exit;
}

if ($method === 'POST') {
    $person = trim($body['person'] ?? '');
    $date   = trim($body['meeting_date'] ?? date('Y-m-d'));
    $notes  = $body['notes'] ?? '';
    $items  = $body['action_items'] ?? [];
    $mood   = isset($body['mood']) ? (int)$body['mood'] : null;
    $tags   = $body['tags'] ?? [];
    if (!$person) { http_response_code(400); echo json_encode(['error' => 'Chybí jméno']); exit; }
    DB::q("INSERT IGNORE INTO onenon_people (name) VALUES (?)", [$person]);
    DB::q(
        "INSERT INTO onenon_notes (person, meeting_date, notes, action_items, mood, tags) VALUES (?,?,?,?,?,?)",
        [$person, $date, $notes, json_encode($items), $mood, json_encode($tags)]
    );
    echo json_encode(['ok' => true, 'id' => DB::get()->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    $id    = (int)($_GET['id'] ?? 0);
    $notes = $body['notes'] ?? null;
    $items = $body['action_items'] ?? null;
    $mood  = array_key_exists('mood', $body) ? ($body['mood'] !== null ? (int)$body['mood'] : null) : 'skip';
    $tags  = $body['tags'] ?? null;
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); exit; }
    if ($notes !== null) DB::q("UPDATE onenon_notes SET notes=? WHERE id=?", [$notes, $id]);
    if ($items !== null) DB::q("UPDATE onenon_notes SET action_items=? WHERE id=?", [json_encode($items), $id]);
    if ($mood !== 'skip') DB::q("UPDATE onenon_notes SET mood=? WHERE id=?", [$mood, $id]);
    if ($tags !== null) DB::q("UPDATE onenon_notes SET tags=? WHERE id=?", [json_encode($tags), $id]);
    echo json_encode(['ok' => true]);
    exit;
}

if ($method === 'DELETE') {
    $person = trim($_GET['person'] ?? '');
    if ($person) {
        DB::q("DELETE FROM onenon_notes WHERE person=?", [$person]);
        DB::q("DELETE FROM onenon_people WHERE name=?", [$person]);
        echo json_encode(['ok' => true]);
        exit;
    }
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); exit; }
    DB::q("DELETE FROM onenon_notes WHERE id=?", [$id]);
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
