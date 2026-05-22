<?php
$body   = getJsonBody();
$method = $_SERVER['REQUEST_METHOD'];

// ── onenon_mappings ────────────────────────────────────────────────────────
if ($method === "GET" && ($_GET["sub"] ?? "") === "onenon_mappings") {
    $rows = DB::q("SELECT event_keyword, person FROM calendar_1on1_mappings WHERE active=1 ORDER BY event_keyword")->fetchAll();
    echo json_encode(["mappings" => $rows]);
    exit;
}

if ($method === "POST" && ($_GET["sub"] ?? "") === "onenon_mappings") {
    $inp = json_decode(file_get_contents("php://input"), true) ?? [];
    $mappings = $inp["mappings"] ?? [];
    DB::q("DELETE FROM calendar_1on1_mappings");
    foreach ($mappings as $m) {
        if (empty($m["event_keyword"]) || empty($m["person"])) continue;
        DB::q("INSERT INTO calendar_1on1_mappings (event_keyword, person) VALUES (?,?) ON DUPLICATE KEY UPDATE person=VALUES(person), active=1",
            [trim($m["event_keyword"]), trim($m["person"])]);
    }
    echo json_encode(["ok" => true]);
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$oldPass  = $body['old_password']  ?? '';
$newUser  = trim($body['new_username'] ?? '');
$newPass  = $body['new_password']  ?? '';

// Ověř staré heslo
if (!password_verify($oldPass, APP_PASS_HASH)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nesprávné stávající heslo']);
    exit;
}

if ($newUser === '' && $newPass === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Zadej nové jméno nebo heslo']);
    exit;
}

$secretsPath = '/etc/tasks/secrets.php';
$content = file_get_contents($secretsPath);
if ($content === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Nelze číst secrets.php']);
    exit;
}

// Nahraď username pokud zadán
if ($newUser !== '') {
    $content = preg_replace_callback(
        "/define\('APP_USER',\s*'[^']*'\);/",
        function() use ($newUser) { return "define('APP_USER', '" . addcslashes($newUser, "'\\") . "');"; },
        $content
    );
}

// Nahraď heslo pokud zadáno
if ($newPass !== '') {
    if (strlen($newPass) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'Heslo musí mít alespoň 8 znaků']);
        exit;
    }
    $hash = password_hash($newPass, PASSWORD_BCRYPT);
    $content = preg_replace_callback(
        "/define\('APP_PASS_HASH',\s*'[^']*'\);/",
        function() use ($hash) { return "define('APP_PASS_HASH', '" . $hash . "');"; },
        $content
    );
}

if (file_put_contents($secretsPath, $content) === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Nelze zapsat secrets.php — ověř permissions']);
    exit;
}

echo json_encode(['ok' => true, 'username' => $newUser ?: APP_USER]);


