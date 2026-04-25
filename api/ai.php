<?php
$body = json_decode(file_get_contents('php://input'), true) ?? [];

if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY === 'PLACEHOLDER_ANTHROPIC_KEY') {
    http_response_code(503);
    echo json_encode(['error' => 'AI není nakonfigurováno — doplň ANTHROPIC_API_KEY do secrets.php']);
    exit;
}

// Načti všechny otevřené tasky
$tasks = DB::q("SELECT id, title, description, ai_context, quadrant, type, due_date, daktela_tickets FROM tasks WHERE status = 'open' ORDER BY quadrant, sort_order")->fetchAll();

// Dnešní kalendář eventy pro kontext
$calEvents = [];
try {
    $tokenRow = DB::q("SELECT access_token, expires_at FROM calendar_tokens LIMIT 1")->fetch();
    if ($tokenRow && new DateTime() < new DateTime($tokenRow['expires_at'])) {
        $qs = buildQuery(['timeMin' => date('Y-m-d') . 'T00:00:00Z', 'timeMax' => date('Y-m-d') . 'T23:59:59Z', 'singleEvents' => 'true', 'maxResults' => '10']);
        $ch = curl_init('https://www.googleapis.com/calendar/v3/calendars/primary/events?' . $qs);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokenRow['access_token']], CURLOPT_TIMEOUT => 5]);
        $calData = json_decode(curl_exec($ch), true);
        curl_close($ch);
        foreach ($calData['items'] ?? [] as $e) {
            $calEvents[] = ($e['summary'] ?? '?') . ' ' . date('H:i', strtotime($e['start']['dateTime'] ?? $e['start']['date'] ?? 'now'));
        }
    }
} catch (Throwable) {}

// Sestav prompt
$taskList = '';
foreach ($tasks as $t) {
    $daktela = $t['daktela_tickets'] && $t['daktela_tickets'] !== '[]' ? ' [Daktela tickety připojeny]' : '';
    $context = $t['ai_context'] ? "\n   Kontext: " . $t['ai_context'] : '';
    $taskList .= "- ID {$t['id']}: {$t['title']} (typ: {$t['type']}, aktuální kvadrant: {$t['quadrant']}, deadline: " . ($t['due_date'] ?: 'není') . "){$daktela}{$context}\n";
}
$calStr = $calEvents ? implode(', ', $calEvents) : 'žádné';

$systemPrompt = <<<SYS
Jsi asistent pro osobní prioritizaci Jiřího Šacha, manažera L1 support týmu v Daktela.
Eisenhower matice: urgent_important (urgentní+důležité), important (důležité), urgent (urgentní), other (ostatní/backlog).
Odpovídej stručně v češtině. Vrať JSON pole suggestions.
SYS;

$userPrompt = <<<USR
Moje dnešní tasky:
{$taskList}

Dnešní kalendář: {$calStr}

Pro každý task navrhni vhodný kvadrant a 1 větu zdůvodnění. Vrať JSON:
{"suggestions": [{"id": 1, "quadrant": "urgent_important", "reason": "..."}]}
USR;

$payload = [
    'model'      => 'claude-sonnet-4-6',
    'max_tokens' => 1024,
    'system'     => [['type' => 'text', 'text' => $systemPrompt, 'cache_control' => ['type' => 'ephemeral']]],
    'messages'   => [['role' => 'user', 'content' => $userPrompt]],
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => [
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
        'anthropic-beta: prompt-caching-2024-07-31',
        'content-type: application/json',
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);
$out  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) {
    http_response_code(502);
    echo json_encode(['error' => 'AI API chyba: ' . $code]);
    exit;
}

$resp = json_decode($out, true);
$text = $resp['content'][0]['text'] ?? '';

// Extrahuj JSON z odpovědi
preg_match('/\{.*\}/s', $text, $m);
$suggestions = $m ? json_decode($m[0], true) : null;
if (!$suggestions || !isset($suggestions['suggestions'])) {
    http_response_code(502);
    echo json_encode(['error' => 'AI vrátilo neočekávaný formát', 'raw' => $text]);
    exit;
}

echo json_encode($suggestions);
