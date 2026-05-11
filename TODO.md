# Tasks App — TODO

## Nápady / budoucí rozvoj

- [ ] Push notifikace / reminder pro overdue tasky
- [ ] Export tasků (CSV, JSON)
- [ ] Sdílení tasku (read-only link)
- [ ] Tmavý režim
- [ ] Odškrtnutí v Dnes — odškrtne task globálně (zrcadlí stav v matici)
- [ ] Reset Dnes ráno — dialog při otevření nového dne: co přesunout, co zahodit
- [ ] AI kontext z DB (fáze 1) — rozšířit AI prompt o 1on1 záznamy, profily lidí, Daktela cache
- [ ] Chat okýnko (fáze 2) — Copilot-style sidebar pro přirozené dotazy
- [ ] Denní cron — aktualizace personal context (tasks-context.md → MCP)
- [ ] Samoučení — porovnávat naplánováno vs. splněno, zpřesňovat odhady

## Dokončeno

### Core
- [x] Setup server, nginx, DB, secrets
- [x] Auth (login.php + session guard, 30 dní)
- [x] Backend (tasks CRUD, checklist, Daktela proxy, Calendar, AI)
- [x] Frontend (Eisenhower matrix, záložky, sidebar, checklist, historie)
- [x] Drag & drop mezi kvadranty
- [x] Quick capture (Cmd+K)
- [x] Overdue highlighting + ⚡ brzy badge (≤3 dny)
- [x] Inline edit názvu (dvojklik)
- [x] Task count badge na záložkách
- [x] Q1 alert badge (popover s deadlinem)
- [x] Fulltext search v headeru
- [x] DaktelaPanel: přiřazené tickety za collapse tlačítkem
- [x] Daktela tickety proklikatelné (správný URL /tickets/update/)
- [x] Více ticketů na jeden task
- [x] Vrátit task z historie do aktivních
- [x] Delete task (hard delete s potvrzením)
- [x] Opakující se tasky (weekly/monthly/custom interval + recurrence_day)
- [x] Google Calendar OAuth (curl, SameSite=Lax fix)
- [x] Daktela tickety DB cache (bez tokenu po refresh stránky)
- [x] AI návrh priorit (Claude Sonnet 4.6, max_tokens 4096)
- [x] Settings — změna username/password (addcslashes, preg_replace_callback)
- [x] Favicon
- [x] Checklist — editace položek (double-click)

### Záložka Dnes
- [x] Záložka Dnes — daily_order INT NULL v DB
- [x] +D tlačítko v matici → přidat task do Dnes
- [x] Drag & drop reorder v Dnes
- [x] **Dnes = Timeline split view** — levý sloupec s denním kalendářem po hodinách, pravý sloupec s tasky + počítadlo volného času (2026-05-11)
- [x] **Ranní ritual** — overlay při prvním otevření 6–10h s prázdným plánem; doporučené Q1+Q2 tasky, batch přidání do Dnes, localStorage throttle (2026-05-11)
- [x] **"Co mám dělat teď?"** — WhatNowWidget v Dnes záložce, Claude Haiku, kontext čas+příští schůzka+Q1, inline výsledek (2026-05-11)

### 1on1 modul
- [x] 1on1 záložka — osoby, timeline, action items, mood 1–5, tagy
- [x] Dashboard: otevřené action items celkem, osoby bez 1on1 >30 dní
- [x] Profil osoby (výkon, potenciál, náročnost, silná stránka, rozvoj)
- [x] Daktela agenti auto-načítání ze skupiny
- [x] **ActionItemsPopover** — červený badge → dropdown skupinami per osoba → klik naviguje na osobu (2026-05-11)
- [x] **PrepDocModal** — tlačítko "📋 Podklady" v detailu osoby; shrnutí nálada/tagy/action items/profil; AI témata přes Claude Haiku (2026-05-11)

### UX & vizuál
- [x] **Stale task indicator** — task karty starší 7d/21d dostávají vizuální aging (proužek zdola + opacity) (2026-05-11)
- [x] **Task description v kartě** — 1 řádek šedě pod názvem (2026-05-11)
