<?php
$body   = getJsonBody();
$method = $_SERVER['REQUEST_METHOD'];

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
    $content = preg_replace(
        "/define\('APP_USER',\s*'[^']*'\);/",
        "define('APP_USER', '" . addslashes($newUser) . "');",
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
