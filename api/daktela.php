<?php
$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $_GET['action'] ?? '';

// Login — vyměn user+pass za accessToken
if ($action === 'daktela_login') {
    $user = trim($body['username'] ?? '');
    $pass = $body['password'] ?? '';
    if (!$user || !$pass) {
        http_response_code(400);
        echo json_encode(['error' => 'Chybí přihlašovací údaje']);
        exit;
    }
    $resp = daktelaRequest('POST', 'https://daktela.daktela.com/api/v6/login.json', null, [
        'username' => $user,
        'password' => $pass,
    ]);
    if (!isset($resp['result']['accessToken'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Nesprávné přihlašovací údaje do Daktely']);
        exit;
    }
    echo json_encode(['accessToken' => $resp['result']['accessToken']]);
    exit;
}

// Proxy — předej dotaz na Daktela API
$token    = $body['accessToken'] ?? '';
$endpoint = $body['endpoint']    ?? '';
$params   = $body['params']      ?? [];

if (!$token || !$endpoint) {
    http_response_code(400);
    echo json_encode(['error' => 'Chybí token nebo endpoint']);
    exit;
}

// Bezpečnostní whitelist endpointů
$allowed = ['tickets', 'tickets/', 'activities', 'users'];
$safe = false;
foreach ($allowed as $a) {
    if (str_starts_with(ltrim($endpoint, '/'), $a)) { $safe = true; break; }
}
if (!$safe) {
    http_response_code(403);
    echo json_encode(['error' => 'Endpoint není povolen']);
    exit;
}

$qs = $params ? ('?' . buildQuery($params)) : '';
$url = 'https://daktela.daktela.com/api/v6/' . ltrim($endpoint, '/') . '.json' . $qs;
$resp = daktelaRequest('GET', $url, $token);
echo json_encode($resp);

function daktelaRequest(string $method, string $url, ?string $token, array $postData = []): array {
    $ch = curl_init($url);
    $headers = ['Accept: application/json'];
    if ($token) $headers[] = 'X-Access-Token: ' . $token;
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $out  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($out === false) return ['error' => 'Daktela API nedostupná'];
    $decoded = json_decode($out, true);
    return is_array($decoded) ? $decoded : ['error' => 'Neplatná odpověď'];
}
