<?php
$secrets = '/etc/tasks/secrets.php';
if (!file_exists($secrets)) {
    http_response_code(500);
    die('Chyba konfigurace serveru.');
}
require_once $secrets;

function requireAuth(): void {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 2592000); // 30 dní
    session_set_cookie_params(['lifetime' => 2592000, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Strict']);
    session_start();
    if (empty($_SESSION['authenticated'])) {
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        header('Location: ' . $base . '/login.php');
        exit;
    }
}

function buildQuery(array $params): string {
    $parts = [];
    foreach ($params as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $v) { $parts[] = $key . '=' . urlencode((string)$v); }
        } else {
            $parts[] = $key . '=' . urlencode((string)$value);
        }
    }
    return implode('&', $parts);
}

function getJsonBody(): array {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
