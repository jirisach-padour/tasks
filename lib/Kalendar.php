<?php
/** Čisté funkce pro kalendář. */
if (get_included_files()[0] === __FILE__) { http_response_code(404); exit; }

/** Odpověď JS na pozvánku (accepted|needsAction|tentative|declined), null = bez účastníků nebo bez vlastního záznamu. */
function kalRsvp(array $e): ?string {
    foreach ($e["attendees"] ?? [] as $a) if (!empty($a["self"])) return (string)($a["responseStatus"] ?? "") ?: null;
    return null;
}
