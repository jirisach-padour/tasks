<?php
require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

if ($method === 'GET') {
    $person = trim($_GET['person'] ?? '');
    if ($person) {
        $rows = DB::q("SELECT * FROM onenon_notes WHERE person=? ORDER BY meeting_date DESC", [$person])->fetchAll();
        foreach ($rows as &$r) $r['action_items'] = json_decode($r['action_items'] ?? '[]', true);
        echo json_encode(['notes' => $rows]);
    } else {
        $people = DB::q("SELECT DISTINCT person FROM onenon_notes ORDER BY person ASC")->fetchAll(PDO::FETCH_COLUMN);
        $counts = [];
        foreach ($people as $p) {
            $n = DB::q("SELECT COUNT(*) FROM onenon_notes WHERE person=?", [$p])->fetchColumn();
            $counts[] = ['person' => $p, 'count' => (int)$n];
        }
        echo json_encode(['people' => $counts]);
    }
    exit;
}

if ($method === 'POST') {
    $person  = trim($body['person'] ?? '');
    $date    = trim($body['meeting_date'] ?? date('Y-m-d'));
    $notes   = $body['notes'] ?? '';
    $items   = $body['action_items'] ?? [];
    if (!$person) { http_response_code(400); echo json_encode(['error' => 'Chybí jméno']); exit; }
    DB::q("INSERT INTO onenon_notes (person, meeting_date, notes, action_items) VALUES (?,?,?,?)",
        [$person, $date, $notes, json_encode($items)]);
    $id = DB::lastInsertId();
    echo json_encode(['ok' => true, 'id' => $id]);
    exit;
}

if ($method === 'PUT') {
    $id    = (int)($_GET['id'] ?? 0);
    $notes = $body['notes'] ?? null;
    $items = $body['action_items'] ?? null;
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); exit; }
    if ($notes !== null) DB::q("UPDATE onenon_notes SET notes=? WHERE id=?", [$notes, $id]);
    if ($items !== null) DB::q("UPDATE onenon_notes SET action_items=? WHERE id=?", [json_encode($items), $id]);
    echo json_encode(['ok' => true]);
    exit;
}

if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'Chybí id']); exit; }
    DB::q("DELETE FROM onenon_notes WHERE id=?", [$id]);
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
