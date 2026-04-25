<?php
require_once __DIR__ . '/config.php';
requireAuth();
require_once __DIR__ . '/lib/DB.php';

$code  = $_GET['code']  ?? '';
$error = $_GET['error'] ?? '';

if ($error || !$code) {
    header('Location: index.php?cal_error=1');
    exit;
}

if (!defined('GOOGLE_CLIENT_ID') || GOOGLE_CLIENT_ID === 'PLACEHOLDER_GOOGLE_CLIENT_ID') {
    header('Location: index.php?cal_error=config');
    exit;
}

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code',
    ]),
]);
$out  = curl_exec($ch);
curl_close($ch);
$data = json_decode($out, true);

if (!isset($data['access_token'])) {
    header('Location: index.php?cal_error=token');
    exit;
}

$expiresAt = date('Y-m-d H:i:s', time() + ($data['expires_in'] ?? 3600) - 60);

// Ulož nebo aktualizuj token
$existing = DB::q("SELECT id FROM calendar_tokens LIMIT 1")->fetch();
if ($existing) {
    $update = ['access_token' => $data['access_token'], 'expires_at' => $expiresAt];
    if (!empty($data['refresh_token'])) $update['refresh_token'] = $data['refresh_token'];
    DB::update('calendar_tokens', $update, $existing['id']);
} else {
    DB::insert('calendar_tokens', [
        'access_token'  => $data['access_token'],
        'refresh_token' => $data['refresh_token'] ?? '',
        'expires_at'    => $expiresAt,
    ]);
}

header('Location: index.php?cal_connected=1');
