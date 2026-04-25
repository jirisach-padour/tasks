<?php
require_once __DIR__ . '/../config.php';

function getCalendarToken(): ?string {
    $row = DB::q("SELECT * FROM calendar_tokens ORDER BY id DESC LIMIT 1")->fetch();
    if (!$row) return null;
    if (strtotime($row['expires_at']) > time() + 60) return $row['access_token'];
    if (!$row['refresh_token']) return null;

    $resp = @file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'refresh_token' => $row['refresh_token'],
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'grant_type'    => 'refresh_token',
        ]),
    ]]));
    $data = $resp ? json_decode($resp, true) : null;
    if (!isset($data['access_token'])) return null;

    $exp = date('Y-m-d H:i:s', time() + ($data['expires_in'] ?? 3600));
    DB::q("UPDATE calendar_tokens SET access_token=?, expires_at=? WHERE id=?",
        [$data['access_token'], $exp, $row['id']]);
    return $data['access_token'];
}

function fetchEvents(string $token): array {
    $tz      = 'Europe/Prague';
    $today   = (new DateTime('today',    new DateTimeZone($tz)))->format(DateTime::RFC3339);
    $dayAfter = (new DateTime('tomorrow +1 day', new DateTimeZone($tz)))->format(DateTime::RFC3339);
    $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?'
        . http_build_query(['timeMin'=>$today,'timeMax'=>$dayAfter,'singleEvents'=>'true','orderBy'=>'startTime','maxResults'=>20,'fields'=>'items(id,summary,start,end)']);
    $resp = @file_get_contents($url, false, stream_context_create(['http'=>['header'=>'Authorization: Bearer '.$token]]));
    if (!$resp) return [];
    $data = json_decode($resp, true);
    $events = [];
    foreach ($data['items'] ?? [] as $e) {
        $startRaw = $e['start']['dateTime'] ?? $e['start']['date'] ?? '';
        $endRaw   = $e['end']['dateTime']   ?? $e['end']['date']   ?? '';
        $allDay   = !isset($e['start']['dateTime']);
        $date     = substr($startRaw, 0, 10);
        $time     = $allDay ? '' : substr($startRaw, 11, 5);
        $events[] = ['id'=>$e['id'],'title'=>$e['summary']??'(bez názvu)','date'=>$date,'time'=>$time,'allDay'=>$allDay,'start'=>$startRaw,'end'=>$endRaw];
    }
    return $events;
}

$method = $_SERVER['REQUEST_METHOD'];
$sub    = $_GET['sub'] ?? '';

// Výchozí — load stavu + eventů
if (!$sub && $method === 'GET') {
    $token = getCalendarToken();
    if (!$token) { echo json_encode(['connected'=>false,'events'=>[]]); exit; }
    echo json_encode(['connected'=>true,'events'=>fetchEvents($token)]);
    exit;
}

// Přihlášení — vrátí redirect URL na Google OAuth
if ($sub === 'connect' && $method === 'GET') {
    $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope'         => 'https://www.googleapis.com/auth/calendar.readonly',
        'access_type'   => 'offline',
        'prompt'        => 'consent',
    ]);
    echo json_encode(['redirect' => $url]);
    exit;
}

// Odpojit
if ($sub === 'disconnect' && $method === 'POST') {
    DB::q("DELETE FROM calendar_tokens");
    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
