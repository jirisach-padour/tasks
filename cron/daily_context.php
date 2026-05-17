<?php
// Spouštět jako: php /var/www/app/tasks/cron/daily_context.php
// Cron: 0 6 * * * php /var/www/app/tasks/cron/daily_context.php

define('TASKS_ROOT', dirname(__DIR__));
require_once TASKS_ROOT . '/lib/DB.php';

$today = date('Y-m-d');
$now   = date('Y-m-d H:i');

$lines = ["# Tasks kontext — {$now}", ""];

// Denní plán (Dnes záložka)
$dnesTasks = DB::q("SELECT title, quadrant, due_date FROM tasks WHERE status = 'open' AND daily_order IS NOT NULL ORDER BY daily_order")->fetchAll();
$lines[] = "## Denní plán (" . count($dnesTasks) . " tasků)";
if ($dnesTasks) {
    foreach ($dnesTasks as $t) {
        $due = $t['due_date'] ? " [{$t['due_date']}]" : "";
        $lines[] = "- [{$t['quadrant']}] {$t['title']}{$due}";
    }
} else {
    $lines[] = "_Prázdný_";
}
$lines[] = "";

// Přehled kvadrantů
$counts = DB::q("SELECT quadrant, COUNT(*) AS cnt FROM tasks WHERE status = 'open' GROUP BY quadrant")->fetchAll();
$lines[] = "## Otevřené tasky dle kvadrantů";
$qMap = ['urgent_important' => 'Q1', 'important' => 'Q2', 'urgent' => 'Q3', 'other' => 'Q4'];
$total = 0;
foreach ($counts as $c) {
    $label = $qMap[$c['quadrant']] ?? $c['quadrant'];
    $lines[] = "- {$label}: {$c['cnt']}";
    $total += $c['cnt'];
}
$lines[] = "- **Celkem: {$total}**";
$lines[] = "";

// Overdue tasky
$overdue = DB::q("SELECT title, due_date, quadrant FROM tasks WHERE status = 'open' AND due_date < ? ORDER BY due_date", [$today])->fetchAll();
$lines[] = "## Prošlé tasky (" . count($overdue) . ")";
if ($overdue) {
    foreach ($overdue as $t) {
        $lines[] = "- [{$t['quadrant']}] {$t['title']} — prošlo {$t['due_date']}";
    }
} else {
    $lines[] = "_Žádné prošlé_";
}
$lines[] = "";

// Otevřené action items z 1on1
$lines[] = "## Otevřené 1on1 action items";
try {
    $people = DB::q("SELECT name FROM onenon_people ORDER BY name")->fetchAll();
    $anyItems = false;
    foreach ($people as $p) {
        $note = DB::q("SELECT action_items FROM onenon_notes WHERE person = ? ORDER BY meeting_date DESC LIMIT 1", [$p['name']])->fetch();
        if (!$note || !$note['action_items']) continue;
        $items = json_decode($note['action_items'], true) ?: [];
        $open = array_filter($items, fn($i) => !($i['done'] ?? false));
        if (!$open) continue;
        $anyItems = true;
        $lines[] = "**{$p['name']}:**";
        foreach ($open as $item) $lines[] = "- {$item['text']}";
    }
    if (!$anyItems) $lines[] = "_Žádné otevřené_";
} catch (Throwable $e) {
    $lines[] = "_Chyba načítání: {$e->getMessage()}_";
}
$lines[] = "";

$output = implode("\n", $lines);
file_put_contents(TASKS_ROOT . '/tasks-context.md', $output);
echo "OK — exportováno do tasks-context.md (" . strlen($output) . " B)\n";
