<?php
// RSVP z Google Kalendáře (29. 10.): příprava jen u potvrzených schůzek
if (PHP_SAPI !== "cli") { http_response_code(404); exit; }
require __DIR__ . "/../lib/Kalendar.php";
$chyb = 0; $t = function (bool $ok, string $co) use (&$chyb) { if (!$ok) { $chyb++; echo "CHYBA: $co\n"; } };
$t(kalRsvp(["attendees" => [["email" => "a", "responseStatus" => "accepted"], ["self" => true, "responseStatus" => "accepted"]]]) === "accepted", "potvrzeno");
$t(kalRsvp(["attendees" => [["self" => true, "responseStatus" => "needsAction"]]]) === "needsAction", "nepotvrzeno");
$t(kalRsvp(["attendees" => [["email" => "x", "responseStatus" => "accepted"]]]) === null, "bez vlastního záznamu = null");
$t(kalRsvp([]) === null, "sólo blok bez účastníků = null");
exit($chyb ? 1 : 0);
