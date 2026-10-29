#!/bin/bash
# Bez přihlášení nesmí web vydat dokumentaci, kontext, cron, knihovny ani ukázky (únik 2026-10-29), API musí přesměrovat na login.
B=${TASKS_URL:-https://padour.duckdns.org/tasks}
chyb=0
for f in DOCS.md DOKUMENTACE.md TODO.md tasks-context.md config.php lib/DB.php cron/daily_context.php demo.php preview.html debug.php tests/run.sh api/ai.php api/tasks.php; do
  c=$(curl -s -o /dev/null -w "%{http_code}" "$B/$f")
  case $c in 404|403) ;; *) echo "CHYBA: $f vrací $c"; chyb=1;; esac
done
for a in tasks onenon calendar todo; do
  c=$(curl -s -o /dev/null -w "%{http_code}" "$B/api.php?action=$a")
  case $c in 302|401|403) ;; *) echo "CHYBA: api $a bez přihlášení vrací $c"; chyb=1;; esac
done
exit $chyb
