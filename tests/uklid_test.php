<?php
// Pracovní data patří jen do Priority (30. 10. 2026): tady žádné pracovní úkoly, žádné 1on1 tabulky, nový úkol je vždy osobní.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$chyb = 0;
$t = function (bool $ok, string $co) use (&$chyb) { if (!$ok) { $chyb++; echo "CHYBA: $co\n"; } };
$src = file_get_contents(dirname(__DIR__) . '/api/tasks.php');
$t(str_contains($src, "'type'            => 'personal',") && !preg_match("/: 'work',/", $src), 'POST tasks ukládá jen osobní úkol');
$t(str_contains(file_get_contents(dirname(__DIR__) . '/api/onenon.php'), 'http_response_code(410)'), 'api/onenon vrací 410');
$t(!is_file(dirname(__DIR__) . '/tasks-context.md'), 'tasks-context.md (1on1 úkoly) neexistuje');
if (is_readable('/etc/tasks/secrets.php')) {
    require_once '/etc/tasks/secrets.php'; require_once dirname(__DIR__) . '/lib/DB.php';
    $db = DB::get();
    $t((int)$db->query("SELECT COUNT(*) FROM tasks WHERE type = 'work'")->fetchColumn() === 0, 'žádné pracovní úkoly v DB hetzneru');
    $t(!$db->query("SHOW TABLES LIKE 'onenon%'")->fetchAll(), 'žádné 1on1 tabulky v DB hetzneru');
}
exit($chyb ? 1 : 0);
