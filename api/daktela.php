<?php
$method = $_SERVER['REQUEST_METHOD'];
$body   = getJsonBody();
$action = $_GET['action'] ?? '';

// Cache — čti nebo obnov DB cache ticketů
if ($action === 'daktela_cache') {
    if ($method === 'GET') {
        $tickets = DB::q("SELECT * FROM daktela_cache ORDER BY title ASC")->fetchAll();
        $meta = DB::q("SELECT * FROM daktela_cache_meta WHERE id=1")->fetch();
        echo json_encode([
            'tickets'      => $tickets,
            'refreshed_at' => $meta['refreshed_at'] ?? null,
            'count'        => count($tickets),
        ]);
        exit;
    }
    if ($method === 'POST') {
        $token = $body['accessToken'] ?? '';
        if (!$token) { http_response_code(400); echo json_encode(['error' => 'Chybí accessToken']); exit; }
        $params = [
            'filter[logic]' => 'and',
            'filter[filters][0][logic]' => 'and',
            'filter[filters][0][filters][0][field]' => 'user',
            'filter[filters][0][filters][0][operator]' => 'in',
            'filter[filters][0][filters][0][value][0]' => 'sachj',
            'filter[filters][0][filters][1][field]' => 'stage',
            'filter[filters][0][filters][1][operator]' => 'in',
            'filter[filters][0][filters][1][value][0]' => 'OPEN',
            'filter[filters][1][field]' => '_ticketView',
            'filter[filters][1][operator]' => 'eq',
            'filter[filters][1][value]' => 'default',
            'filter[filters][2][field]' => 'id_merge',
            'filter[filters][2][operator]' => 'isnull',
            'fields[0]' => 'name',
            'fields[1]' => 'title',
            'fields[2]' => 'stage',
            'fields[3]' => 'sla_deadline',
            'take' => 100,
        ];
        $params['accessToken'] = $token;
        $qs = '?' . buildQuery($params);
        $url = 'https://daktela.daktela.com/api/v6/tickets.json' . $qs;
        $resp = daktelaRequest('GET', $url, $token);
        $items = $resp['result']['data'] ?? [];
        if (!is_array($items)) { http_response_code(502); echo json_encode(['error' => 'Daktela API chyba']); exit; }
        // Ulož do cache
        DB::q("DELETE FROM daktela_cache");
        foreach ($items as $t) {
            DB::q(
                "INSERT INTO daktela_cache (name, title, stage, sla_deadline) VALUES (?,?,?,?)",
                [$t['name'] ?? '', $t['title'] ?? '', $t['stage'] ?? '', $t['sla_deadline'] ?? '']
            );
        }
        $now = (new DateTimeImmutable('now', new DateTimeZone('Europe/Prague')))->format('Y-m-d H:i:s');
        DB::q("UPDATE daktela_cache_meta SET refreshed_at=?, ticket_count=? WHERE id=1", [$now, count($items)]);
        $tickets = DB::q("SELECT * FROM daktela_cache ORDER BY title ASC")->fetchAll();
        echo json_encode(['tickets' => $tickets, 'refreshed_at' => $now, 'count' => count($tickets)]);
        exit;
    }
}

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
$allowed = ['tickets', 'tickets/', 'activities', 'users', 'groups'];
$safe = false;
foreach ($allowed as $a) {
    if (str_starts_with(ltrim($endpoint, '/'), $a)) { $safe = true; break; }
}
if (!$safe) {
    http_response_code(403);
    echo json_encode(['error' => 'Endpoint není povolen']);
    exit;
}

// accessToken jako query param (nutné pro Daktela API)
$params['accessToken'] = $token;
$qs = '?' . buildQuery($params);
$url = 'https://daktela.daktela.com/api/v6/' . ltrim($endpoint, '/') . '.json' . $qs;
ini_set('memory_limit', '256M');
error_log('DAKTELA_PROXY URL: ' . $url);
$resp = daktelaRequest('GET', $url, $token);
error_log('DAKTELA_PROXY count raw: ' . (isset($resp['result']['data']) ? count($resp['result']['data']) : 'no data'));
// Zkrátit odpověď — ponechat jen result.data pole + serverové filtrování
if (isset($resp['result']['data']) && is_array($resp['result']['data'])) {
    $data = $resp['result']['data'];
    error_log('DAKTELA_PROXY count after filter: ' . count($data));
    $resp = ['result' => ['data' => $data]];
}
$out = json_encode($resp, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
if ($out === false) {
    error_log('daktela json_encode error: ' . json_last_error_msg() . ' data: ' . print_r($resp, true));
    echo json_encode(['error' => 'Chyba kódování odpovědi: ' . json_last_error_msg()]);
} else {
    echo $out;
}

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
