<?php
// Spouštět jako: php /var/www/app/tasks/cron/daily_context.php
// Cron: 0 6 * * * php /var/www/app/tasks/cron/daily_context.php

define('TASKS_ROOT', dirname(__DIR__));
require_once '/etc/tasks/secrets.php';
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

// ── 1on1 auto-tasky: den před schůzkou ────────────────────────────────────
try {
    $mappings = DB::q("SELECT event_keyword, person FROM calendar_1on1_mappings WHERE active=1")->fetchAll();
    if ($mappings) {
        $calToken = null;
        $tokenRow = DB::q("SELECT * FROM calendar_tokens ORDER BY id DESC LIMIT 1")->fetch();
        if ($tokenRow) {
            if (strtotime($tokenRow["expires_at"]) > time() + 60) {
                $calToken = $tokenRow["access_token"];
            } elseif ($tokenRow["refresh_token"]) {
                $ch = curl_init("https://oauth2.googleapis.com/token");
                curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query(["refresh_token" => $tokenRow["refresh_token"], "client_id" => GOOGLE_CLIENT_ID, "client_secret" => GOOGLE_CLIENT_SECRET, "grant_type" => "refresh_token"]), CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"], CURLOPT_TIMEOUT => 10]);
                $resp = curl_exec($ch); curl_close($ch);
                $td = $resp ? json_decode($resp, true) : null;
                if (isset($td["access_token"])) {
                    $calToken = $td["access_token"];
                    $exp = date("Y-m-d H:i:s", time() + ($td["expires_in"] ?? 3600));
                    DB::q("UPDATE calendar_tokens SET access_token=?, expires_at=? WHERE id=?", [$calToken, $exp, $tokenRow["id"]]);
                }
            }
        }
        if ($calToken) {
            $tz = "Europe/Prague";
            $tomorrow = (new DateTime("tomorrow", new DateTimeZone($tz)))->format("Y-m-d");
            $tEnd = (new DateTime("tomorrow +2 days", new DateTimeZone($tz)))->format(DateTime::RFC3339);
            $tStart = (new DateTime("tomorrow", new DateTimeZone($tz)))->format(DateTime::RFC3339);
            $url = "https://www.googleapis.com/calendar/v3/calendars/primary/events?" . http_build_query(["timeMin" => $tStart, "timeMax" => $tEnd, "singleEvents" => "true", "orderBy" => "startTime", "maxResults" => 20, "fields" => "items(summary,start)"]);
            $ch2 = curl_init($url);
            curl_setopt_array($ch2, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["Authorization: Bearer " . $calToken], CURLOPT_TIMEOUT => 8]);
            $resp2 = curl_exec($ch2); curl_close($ch2);
            $calData = $resp2 ? json_decode($resp2, true) : null;
            $tomorrowEvents = $calData ? array_map(fn($e) => $e["summary"] ?? "", $calData["items"] ?? []) : [];
            foreach ($mappings as $m) {
                $matched = array_filter($tomorrowEvents, fn($t) => stripos($t, $m["event_keyword"]) !== false);
                if (!$matched) continue;
                $title = "Připravit 1on1 s " . $m["person"];
                $exists = DB::q("SELECT id FROM tasks WHERE title=? AND due_date=? AND status=\"open\"", [$title, $tomorrow])->fetch();
                if ($exists) continue;
                $maxOrder = DB::q("SELECT COALESCE(MAX(daily_order),0) FROM tasks WHERE daily_order IS NOT NULL")->fetchColumn();
                DB::q("INSERT INTO tasks (title,quadrant,type,due_date,status,daily_order,created_at,updated_at) VALUES (?,?,?,?,?,?,NOW(),NOW())", [$title, "important", "work", $tomorrow, "open", (int)$maxOrder + 1]);
                error_log("[daily_context] Vytvořen 1on1 task pro " . $m["person"]);
                echo "1on1 task: {$title} ({$tomorrow})\n";
            }
        }
    }
} catch (Throwable $e) {
    error_log("[daily_context] 1on1 auto-task chyba: " . $e->getMessage());
}
