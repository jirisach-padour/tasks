#!/bin/bash
# Celá sada Tasks: syntaxe + web bez přihlášení. Výstup jen chyby a souhrn.
cd "$(dirname "$0")/.."
chyb=0
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f" >/dev/null 2>&1 || { echo "CHYBA syntaxe: $f"; chyb=1; }; done
bash tests/web_test.sh || chyb=1
[ $chyb = 0 ] && echo "VŠE OK" || { echo "SELHALO"; exit 1; }
