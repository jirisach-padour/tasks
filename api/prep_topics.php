<?php
$body = getJsonBody();

if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY === 'PLACEHOLDER_ANTHROPIC_KEY') {
    http_response_code(503);
    echo json_encode(['error' => 'AI není nakonfigurováno']);
    exit;
}

$person = $body['person'] ?? 'kolega';
$profile = $body['profile'] ?? [];
$openItems = $body['openItems'] ?? [];
$recentTags = $body['recentTags'] ?? [];
$moodTrend = $body['moodTrend'] ?? null;
$lastDate = $body['lastNoteDate'] ?? null;

$prompt = "Připravuji 1on1 s " . $person . ".\n";
if ($lastDate) $prompt .= "Poslední schůzka: " . $lastDate . "\n";
if ($moodTrend) $prompt .= "Nálada trend: " . $moodTrend . "\n";
if ($recentTags) $prompt .= "Časté tagy: " . implode(', ', $recentTags) . "\n";
if ($openItems) $prompt .= "Otevřené action items: " . implode('; ', array_slice($openItems, 0, 5)) . "\n";
if (!empty($profile['performance'])) $prompt .= "Výkon: " . $profile['performance'] . "/5\n";
if (!empty($profile['potential'])) $prompt .= "Potenciál: " . $profile['potential'] . "\n";
if (!empty($profile['strength'])) $prompt .= "Silná stránka: " . $profile['strength'] . "\n";
if (!empty($profile['development'])) $prompt .= "Oblast rozvoje: " . $profile['development'] . "\n";

$prompt .= "\nNavrhni 3 konkrétní témata/otázky pro 1on1 schůzku. Vrať JSON: {\"topics\": [\"...\", \"...\", \"...\"]}";

$payload = [
    'model'      => 'claude-haiku-4-5-20251001',
    'max_tokens' => 400,
    'messages'   => [['role' => 'user', 'content' => $prompt]],
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_HTTPHEADER     => [
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
        'content-type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);
$out  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) { http_response_code(502); echo json_encode(['error' => 'AI chyba']); exit; }

$resp = json_decode($out, true);
$text = $resp['content'][0]['text'] ?? '';
preg_match('/\{.*\}/s', $text, $m);
$result = $m ? json_decode($m[0], true) : null;
echo json_encode($result ?: ['topics' => []]);
