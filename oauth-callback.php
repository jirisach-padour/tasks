<?php
require_once __DIR__ . '/config.php';

$code  = $_GET['code']  ?? '';
$error = $_GET['error'] ?? '';

if ($error) {
    die('Google OAuth chyba: ' . htmlspecialchars($error));
}
if (!$code) {
    die('Chybí auth code.');
}

// Vyměň code za tokeny (curl — file_get_contents nefunguje spolehlivě s POST)
$postData = http_build_query([
    'code'          => $code,
    'client_id'     => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'grant_type'    => 'authorization_code',
]);
$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $postData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$resp = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if (!$resp) {
    error_log('CALENDAR_OAUTH curl error: ' . $curlError);
    die('Chyba při výměně tokenu: curl selhal — ' . htmlspecialchars($curlError));
}

$data = json_decode($resp, true);
if (!isset($data['access_token'])) {
    error_log('CALENDAR_OAUTH Google error: ' . $resp);
    die('Chyba při výměně tokenu: ' . htmlspecialchars($resp));
}

$expiresAt = date('Y-m-d H:i:s', time() + ($data['expires_in'] ?? 3600));

// Ulož tokeny (vždy jen jeden řádek)
DB::q("DELETE FROM calendar_tokens");
DB::q(
    "INSERT INTO calendar_tokens (access_token, refresh_token, expires_at) VALUES (?, ?, ?)",
    [$data['access_token'], $data['refresh_token'] ?? null, $expiresAt]
);

header('Location: /tasks/index.php?calendar=connected');
exit;
