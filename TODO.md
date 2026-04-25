# Tasks App — TODO

## Otevřené
- [ ] Nastavit APP heslo: `ssh hetzner-personal "sudo php /tmp/set_app_password.php"`
- [ ] Doplnit ANTHROPIC_API_KEY do `/etc/tasks/secrets.php` pro AI funkci
- [ ] Propojit Google Calendar (v aplikaci → Calendar panel → Propojit)
- [ ] Pro Google Calendar: vytvořit OAuth projekt na console.cloud.google.com a doplnit GOOGLE_CLIENT_ID + GOOGLE_CLIENT_SECRET do secrets.php

## Dokončeno
- [x] Setup server, nginx, DB, secrets
- [x] Auth (login.php + session guard)
- [x] Backend (tasks CRUD, checklist, Daktela proxy, Calendar, AI)
- [x] Frontend (Eisenhower matrix, záložky, sidebar, checklist, historie)
