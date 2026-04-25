<?php
$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$sub    = $_GET['sub'] ?? 'events'; // events | connect | disconnect

// Zkontroluj zda jsou Google credentials nakonfigurovány
if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === 'PLACEHOLDER_GOOGLE_CLIENT_ID') {
    echo json_encode(['connected' => false, 'events' => [], 'error' => 'Google Calendar není nakonfigurován']);
    exit;
}

if ($sub === 'connect') {
    // Redirect na Google OAuth
    $params = buildQuery([
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope'         => 'https://www.googleapis.com/auth/calendar.readonly',
        'access_type'   => 'offline',
        'prompt'        => 'consent',
    ]);
    echo json_encode(['redirect' => 'https://accounts.google.com/o/oauth2/v2/auth?' . $params]);
    exit;
}

if ($sub === 'disconnect') {
    DB::q("DELETE FROM calendar_tokens");
    echo json_encode(['ok' => true]);
    exit;
}

// Zjisti zda jsou uloženy tokeny
$tokenRow = DB::q("SELECT * FROM calendar_tokens LIMIT 1")->fetch();
if (!$tokenRow) {
    echo json_encode(['connected' => false, 'events' => []]);
    exit;
}

// Refresh pokud token expiroval
$accessToken = $tokenRow['access_token'];
if (new DateTime() >= new DateTime($tokenRow['expires_at'])) {
    $refreshed = refreshGoogleToken($tokenRow['refresh_token']);
    if (isset($refreshed['access_token'])) {
        $accessToken = $refreshed['access_token'];
        $expiresAt   = date('Y-m-d H:i:s', time() + ($refreshed['expires_in'] ?? 3600) - 60);
        DB::update('calendar_tokens', ['access_token' => $accessToken, 'expires_at' => $expiresAt], $tokenRow['id']);
    } else {
        echo json_encode(['connected' => false, 'events' => [], 'error' => 'Token nelze obnovit, připoj znovu']);
        exit;
    }
}

// Stáhni dnešní + zítřejší eventy
$timeMin = date('Y-m-d') . 'T00:00:00Z';
$timeMax = date('Y-m-d', strtotime('+2 days')) . 'T00:00:00Z';
$qs = buildQuery([
    'calendarId'   => 'primary',
    'timeMin'      => $timeMin,
    'timeMax'      => $timeMax,
    'singleEvents' => 'true',
    'orderBy'      => 'startTime',
    'maxResults'   => '20',
]);
$url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?' . $qs;
$ch  = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
    CURLOPT_TIMEOUT        => 8,
]);
$out  = curl_exec($ch);
curl_close($ch);
$data = json_decode($out, true);

$events = [];
foreach ($data['items'] ?? [] as $item) {
    $start = $item['start']['dateTime'] ?? $item['start']['date'] ?? '';
    $events[] = [
        'title' => $item['summary'] ?? '(bez názvu)',
        'start' => $start,
        'time'  => $start ? date('H:i', strtotime($start)) : 'celodenní',
        'date'  => $start ? date('Y-m-d', strtotime($start)) : date('Y-m-d'),
    ];
}

echo json_encode(['connected' => true, 'events' => $events]);

function refreshGoogleToken(string $refreshToken): array {
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'refresh_token' => $refreshToken,
            'grant_type'    => 'refresh_token',
        ]),
    ]);
    $out = curl_exec($ch);
    curl_close($ch);
    return json_decode($out, true) ?? [];
}
