<?php
$body = getJsonBody();

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
$today = date('Y-m-d');
foreach ($tasks as $t) {
    $daktela = $t['daktela_tickets'] && $t['daktela_tickets'] !== '[]' ? ' [má přiřazené Daktela tickety]' : '';
    $context = $t['ai_context'] ? "\n   Kontext od uživatele: " . $t['ai_context'] : '';
    $deadline = $t['due_date'] ?: 'není';
    $daysLeft = $t['due_date'] ? (int)((strtotime($t['due_date']) - strtotime($today)) / 86400) : null;
    $deadlineStr = $t['due_date'] ? $deadline . " (za {$daysLeft} dní)" : 'není';
    $taskList .= "- ID {$t['id']}: {$t['title']} (typ: {$t['type']}, aktuální kvadrant: {$t['quadrant']}, deadline: {$deadlineStr}){$daktela}{$context}\n";
}
$calStr = $calEvents ? implode(', ', $calEvents) : 'žádné';

$systemPrompt = <<<SYS
Jsi asistent pro osobní prioritizaci Jiřího Šacha. Jiří je manažer L1 support týmu v Daktela (SaaS zákaznická podpora). Jeho klíčové odpovědnosti:
- SLA compliance: tickety musí být zodpovězeny v dohodnutých lhůtách, eskalace jsou citlivé
- 1on1 schůzky s agenty L1 týmu: příprava, action items, hodnocení
- Operativní rozhodnutí: incident management, reporty, hiring
- Strategické úkoly: procesy, dokumentace, rozvoj týmu

Eisenhower matice:
- urgent_important: deadline do 2 dní NEBO blokuje tým/zákazníka NEBO SLA/eskalace riziko
- important: strategické, bez bezprostředního deadline, důležité pro rozvoj nebo tým
- urgent: časově citlivé ale delegovatelné nebo rutinní operativní úkoly
- other: nice-to-have, backlog, nízká hodnota

Pravidla pro hodnocení:
- Daktela tickety na tasku = zákaznická nebo SLA citlivost → zvažuj urgent_important
- ai_context od uživatele je klíčový vstup — vždy ho zohledni
- Deadline za ≤2 dny = urgentní, za ≤7 dní = zvažuj, za >14 dní = ne urgentní
- 1on1 příprava před schůzkou v kalendáři = important nebo urgent_important
- Osobní tasky (typ: personal) patří většinou do important nebo other pokud nemají deadline

Odpovídej v češtině. Vrať pouze JSON, žádný text navíc.
SYS;

$userPrompt = <<<USR
Dnešní datum: {$today}

Moje otevřené tasky:
{$taskList}

Dnešní a zítřejší kalendář: {$calStr}

Pro každý task navrhni vhodný kvadrant a 1–2 věty zdůvodnění (konkrétní, ne obecné). Vrať JSON:
{"suggestions": [{"id": 1, "quadrant": "urgent_important", "reason": "..."}]}
USR;

$payload = [
    'model'      => 'claude-sonnet-4-6',
    'max_tokens' => 4096,
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
