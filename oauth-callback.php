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

// Vyměň code za tokeny
$resp = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]),
    ],
]));

$data = json_decode($resp, true);
if (!isset($data['access_token'])) {
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
