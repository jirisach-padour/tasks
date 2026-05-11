<?php
$body = getJsonBody();

if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY === 'PLACEHOLDER_ANTHROPIC_KEY') {
    http_response_code(503);
    echo json_encode(['error' => 'AI není nakonfigurováno']);
    exit;
}

$time = $body['time'] ?? date('H:i');
$nextEvent = $body['nextEvent'] ?? null;
$topQ1 = $body['topQ1'] ?? [];
$dailyTasks = $body['dailyTasks'] ?? [];

$q1List = array_map(fn($t) => '- ' . $t['title'] . ($t['due_date'] ? ' (deadline ' . $t['due_date'] . ')' : ''), $topQ1);
$dailyList = array_map(fn($t) => '- ' . $t['title'] . ' (' . $t['quadrant'] . ')', $dailyTasks);

$prompt = "Čas: " . $time . "\n";
if ($nextEvent) $prompt .= "Příští schůzka: " . $nextEvent . "\n";
$prompt .= $q1List ? "Q1 tasky:\n" . implode("\n", $q1List) . "\n" : "Žádné Q1 tasky.\n";
$prompt .= $dailyList ? "Dnešní plán:\n" . implode("\n", $dailyList) . "\n" : "";
$prompt .= "\nV 2-3 větách řekni Jiřímu co dělat teď. Buď konkrétní (uveď název tasku). Vrať JSON: {\"text\": \"...\", \"task_title\": \"přesný název tasku nebo null\", \"task_quadrant\": \"Q1/Q2/null\"}";

$payload = [
    'model'      => 'claude-haiku-4-5-20251001',
    'max_tokens' => 300,
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

if (!$result) { echo json_encode(['text' => trim($text), 'task_title' => null]); exit; }
echo json_encode($result);
