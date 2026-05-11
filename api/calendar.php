<?php
require_once __DIR__ . '/../config.php';

function getCalendarToken(): ?string {
    $row = DB::q("SELECT * FROM calendar_tokens ORDER BY id DESC LIMIT 1")->fetch();
    if (!$row) return null;
    if (strtotime($row['expires_at']) > time() + 60) return $row['access_token'];
    if (!$row['refresh_token']) return null;

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query(['refresh_token' => $row['refresh_token'], 'client_id' => GOOGLE_CLIENT_ID, 'client_secret' => GOOGLE_CLIENT_SECRET, 'grant_type' => 'refresh_token']),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
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
    $end7    = (new DateTime('today +7 days', new DateTimeZone($tz)))->format(DateTime::RFC3339);
    $todayDate = (new DateTime('today', new DateTimeZone($tz)))->format('Y-m-d');
    $tomorrowDate = (new DateTime('tomorrow', new DateTimeZone($tz)))->format('Y-m-d');
    $dayLabels = ['Ne','Po','Út','St','Čt','Pá','So'];
    $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?'
        . http_build_query(['timeMin'=>$today,'timeMax'=>$end7,'singleEvents'=>'true','orderBy'=>'startTime','maxResults'=>30,'fields'=>'items(id,summary,start,end)']);
    $ch2 = curl_init($url);
    curl_setopt_array($ch2, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token], CURLOPT_TIMEOUT => 8, CURLOPT_SSL_VERIFYPEER => true]);
    $resp = curl_exec($ch2);
    curl_close($ch2);
    if (!$resp) return [];
    $data = json_decode($resp, true);
    $events = [];
    foreach ($data['items'] ?? [] as $e) {
        $startRaw = $e['start']['dateTime'] ?? $e['start']['date'] ?? '';
        $endRaw   = $e['end']['dateTime']   ?? $e['end']['date']   ?? '';
        $allDay   = !isset($e['start']['dateTime']);
        $date     = substr($startRaw, 0, 10);
        $time     = $allDay ? '' : substr($startRaw, 11, 5);
        $dow      = (int)(new DateTime($date, new DateTimeZone($tz)))->format('w');
        $label    = $date === $todayDate ? 'Dnes' : ($date === $tomorrowDate ? 'Zítra' : $dayLabels[$dow] . ' ' . substr($date, 8, 2) . '.' . substr($date, 5, 2) . '.');
        $durationH = (!$allDay && $endRaw) ? round((strtotime($endRaw) - strtotime($startRaw)) / 3600, 1) : 1;
        $events[] = ['id'=>$e['id'],'title'=>$e['summary']??'(bez názvu)','date'=>$date,'time'=>$time,'allDay'=>$allDay,'start'=>$startRaw,'end'=>$endRaw,'dayLabel'=>$label,'durationH'=>$durationH];
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
