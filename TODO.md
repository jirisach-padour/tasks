# Tasks App — TODO

## Otevřené
- [ ] Doplnit ANTHROPIC_API_KEY do `/etc/tasks/secrets.php` pro AI funkci
- [ ] Propojit Google Calendar (v aplikaci → Calendar panel → Propojit)
- [ ] Pro Google Calendar: vytvořit OAuth projekt na console.cloud.google.com a doplnit GOOGLE_CLIENT_ID + GOOGLE_CLIENT_SECRET do secrets.php

## Nápady / budoucí rozvoj
- [ ] 1on1 notes app (PHP+React, hetzner, nový projekt):
  - Agenti se natáhnou automaticky ze skupiny L1 v Daktele přes API (sachj token)
  - Na každého agenta samostatná stránka: přehled schůzek chronologicky
  - Každá schůzka: datum, volné poznámky (rich text nebo markdown), action items jako checklist, hodnocení nálady 1–5, tagy (výkon / SLA / osobní / rozvoj / feedback)
  - Dashboard přes všechny agenty: kdy byl poslední 1on1 (highlight pokud >30 dní), počet otevřených action items, timeline
  - Mobile-friendly — poznámky píšeš i z telefonu během schůzky
  - Stejný auth jako tasks app (APP_USER + bcrypt session)

## Dokončeno
- [x] Setup server, nginx, DB, secrets
- [x] Auth (login.php + session guard)
- [x] Backend (tasks CRUD, checklist, Daktela proxy, Calendar, AI)
- [x] Frontend (Eisenhower matrix, záložky, sidebar, checklist, historie)
- [x] Drag & drop mezi kvadranty
- [x] Quick capture (Cmd+K)
- [x] Overdue highlighting
- [x] Inline edit názvu (dvojklik)
- [x] Task count badge na záložkách
- [x] Q1 alert badge
- [x] Fulltext search v headeru
- [x] DaktelaPanel: přiřazené tickety za collapse tlačítkem
- [x] Daktela tickety proklikatelné do Daktely
- [x] Více ticketů na jeden task
- [x] Vrátit task z historie do aktivních
