<?php
$body = getJsonBody();

if (!defined('ANTHROPIC_API_KEY') || ANTHROPIC_API_KEY === 'PLACEHOLDER_ANTHROPIC_KEY') {
    http_response_code(503);
    echo json_encode(['error' => 'AI není nakonfigurováno']);
    exit;
}

$message = trim($body['message'] ?? '');
$history = $body['history'] ?? [];
if (!$message) { http_response_code(400); echo json_encode(['error' => 'Prázdná zpráva']); exit; }

// Načti kontext — tasky, 1on1, Daktela
$tasks = DB::q("SELECT id, title, quadrant, type, due_date, status, daily_order FROM tasks WHERE status = 'open' ORDER BY quadrant, sort_order")->fetchAll();

$onenon = [];
try {
    $people = DB::q("SELECT name FROM onenon_people ORDER BY name")->fetchAll();
    foreach ($people as $p) {
        $lastNote = DB::q("SELECT meeting_date, action_items FROM onenon_notes WHERE person = ? ORDER BY meeting_date DESC LIMIT 1", [$p['name']])->fetch();
        $openItems = [];
        if ($lastNote && $lastNote['action_items']) {
            foreach (json_decode($lastNote['action_items'], true) ?: [] as $item) {
                if (!($item['done'] ?? false)) $openItems[] = $item['text'];
            }
        }
        if ($openItems) $onenon[] = $p['name'] . ': ' . implode('; ', array_slice($openItems, 0, 2));
    }
} catch (Throwable) {}

$daktelaTickets = [];
try {
    $rows = DB::q("SELECT name, title, sla_deadline FROM daktela_cache ORDER BY sla_deadline ASC LIMIT 10")->fetchAll();
    foreach ($rows as $r) $daktelaTickets[] = $r['name'] . ' — ' . $r['title'] . ($r['sla_deadline'] ? ' [SLA: ' . $r['sla_deadline'] . ']' : '');
} catch (Throwable) {}

// Sestav systémový kontext
$today = date('Y-m-d');
$taskLines = [];
foreach ($tasks as $t) {
    $dnes = ($t['daily_order'] !== null) ? ' [V DNES]' : '';
    $taskLines[] = '- [' . strtoupper($t['quadrant']) . '] ' . $t['title'] . ($t['due_date'] ? ' (deadline ' . $t['due_date'] . ')' : '') . $dnes;
}

$systemPrompt = "Jsi asistent Jiřího Šacha, manažera L1 support týmu v Daktela. Pomáháš s prioritizací, rozhodnutími a plánováním.\n\n";
$systemPrompt .= "Dnešní datum: {$today}\n\n";
$systemPrompt .= "Otevřené tasky (" . count($tasks) . "):\n" . implode("\n", array_slice($taskLines, 0, 30)) . "\n";
if ($onenon) $systemPrompt .= "\nOtevřené action items (1on1):\n- " . implode("\n- ", $onenon) . "\n";
if ($daktelaTickets) $systemPrompt .= "\nDaktela fronta:\n- " . implode("\n- ", $daktelaTickets) . "\n";
$systemPrompt .= "\nOdpovídej stručně, v češtině, konkrétně. Bez zbytečného opakování kontextu.";

// Sestav messages — history + nová zpráva
$messages = [];
foreach ($history as $h) {
    if (isset($h['role'], $h['content']) && in_array($h['role'], ['user', 'assistant'])) {
        $messages[] = ['role' => $h['role'], 'content' => $h['content']];
    }
}
$messages[] = ['role' => 'user', 'content' => $message];

$payload = [
    'model'      => 'claude-haiku-4-5-20251001',
    'max_tokens' => 800,
    'system'     => $systemPrompt,
    'messages'   => $messages,
];

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20,
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

if ($code !== 200) { http_response_code(502); echo json_encode(['error' => 'AI chyba: ' . $code]); exit; }

$resp = json_decode($out, true);
$reply = $resp['content'][0]['text'] ?? '';
echo json_encode(['reply' => $reply]);
