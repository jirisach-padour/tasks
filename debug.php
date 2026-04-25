<?php
require_once __DIR__ . '/config.php';
requireAuth();
require_once __DIR__ . '/lib/DB.php';

header('Content-Type: application/json');

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$token = $body['accessToken'] ?? '';

if (!$token) {
    echo json_encode(['error' => 'Chybí token']);
    exit;
}

// Test 1: raw response bez filtrů
$ch = curl_init('https://daktela.daktela.com/api/v6/tickets.json?filter[0][field]=user&filter[0][operator]=eq&filter[0][value]=sachj&take=3');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['X-Access-Token: ' . $token],
    CURLOPT_TIMEOUT => 10,
]);
$raw = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo json_encode([
    'http_code' => $code,
    'response'  => json_decode($raw),
]);
