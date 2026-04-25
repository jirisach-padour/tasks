# Tasks App — TODO

## Otevřené — bugs

- [ ] **Double modal při mazání tasku** — dialog `window.confirm()` se zobrazí 2x, podruhé vypíše "undefined"; task se smaže ale UX je rozbité — zjistit příčinu (pravděpodobně onDelete je volán z TaskCard i TaskModal současně)
- [ ] **Odebrání ticketu z detailu uloženého tasku** — × tlačítko v TaskModal existuje, ale ověřit jestli save správně posílá aktualizovaný daktela_tickets array a zobrazuje přiřazené tickety při editaci existujícího tasku
- [ ] **Čas poslední aktualizace ticketů v Prague time** — "Obnoveno: {timestamp}" v DaktelaPanel zobrazuje UTC čas; opravit na Europe/Prague zobrazení

## Otevřené — nové funkce

- [ ] **Google Calendar — vymyslet práci s kalendářem** — aktuálně jen read-only přehled dnes/zítra; zvážit: import eventu jako tasku, blokace časových slotů, kontext pro AI, připomenutí
- [ ] **1on1 tab — dokončit a navrhnout strukturu** — aktuální implementace je základní; zvážit:
  - automatické natažení agentů L1 skupiny ze Daktely (API sachj)
  - hodnocení nálady 1–5 per schůzka
  - tagy: výkon / SLA / osobní / rozvoj / feedback
  - dashboard: kdy byl poslední 1on1 (highlight >30 dní), počet otevřených action items
  - mobile-friendly edit během schůzky
- [ ] **Opakování — konkrétní den ze kalendáře** — při weekly/monthly opakování vybrat konkrétní den (např. každý pátek, každý 1. v měsíci); propojit s Google Calendar jako start datum

## Nápady / budoucí rozvoj

- [ ] Push notifikace / reminder pro overdue tasky
- [ ] Export tasků (CSV, JSON)
- [ ] Sdílení tasku (read-only link)
- [ ] Tmavý režim

## Dokončeno

- [x] Setup server, nginx, DB, secrets
- [x] Auth (login.php + session guard)
- [x] Backend (tasks CRUD, checklist, Daktela proxy, Calendar, AI)
- [x] Frontend (Eisenhower matrix, záložky, sidebar, checklist, historie)
- [x] Drag & drop mezi kvadranty
- [x] Quick capture (Cmd+K)
- [x] Overdue highlighting + ⚡ brzy badge (≤3 dny)
- [x] Inline edit názvu (dvojklik)
- [x] Task count badge na záložkách
- [x] Q1 alert badge
- [x] Fulltext search v headeru
- [x] DaktelaPanel: přiřazené tickety za collapse tlačítkem
- [x] Daktela tickety proklikatelné (správný URL /tickets/update/)
- [x] Více ticketů na jeden task
- [x] Vrátit task z historie do aktivních
- [x] Delete task (hard delete s potvrzením)
- [x] Opakující se tasky (weekly/monthly/custom interval)
- [x] 1on1 záložka — základní implementace (osoby, timeline, action items)
- [x] Google Calendar OAuth (curl, SameSite=Lax fix)
- [x] Daktela tickety DB cache (bez tokenu po refresh stránky)
- [x] AI návrh priorit (Claude Sonnet 4.6)
- [x] Daktela nested filter formát (správný filter[filters][N] formát)
