# Tasks App — TODO

## Bugs — k opravě

- [ ] **Připravit schůzku v 1on1** — rozsypaná grafika tlačítka/dialogu
- [ ] **Ovládací prvky v Tasks neodpovídají redesignu** — divné/staré UI prvky přes celou appku
- [ ] **Checklist a Daktela tickety v matici** — vypadají jinak než v návrhu (srovnat s preview)
- [ ] **Volný čas v denním kalendáři** — nepočítá se správně, zkontrolovat dle návrhu
- [ ] **Ranní rituál — Přeskočit nefunguje** — po kliknutí na Přeskočit modal nezmizí

## Fronta

- [ ] **Daktela problémové tickety v 1on1** — POZDĚJŠÍ: v detailu osoby zobrazit OPEN tickety kde je agent přiřazen a jsou OPEN/SLA risk; vyžaduje Daktela token + mapování person→daktela login
- [ ] **Mood trend chart v 1on1** — mini bar chart nálady (posledních 4 záznamy) v person detailu pod open items
- [ ] Push notifikace / reminder pro overdue tasky
- [ ] Export tasků (CSV, JSON)
- [ ] Tmavý režim
- [ ] Export do Google Docs — po dokončení dne/týdne generovat přehled

## Dokončeno 2026-05-22

- [x] **FÁZE 1 — Nový design systém** — bílý header 52px, NavSidebar 60px, nové CSS proměnné (--bg, --surface, --border, --accent, --danger...), tab-bar hidden, default tab Dnes
- [x] **FÁZE 2 — Matice vizuální hierarchie** — Q1 červené pozadí, Q2 bílé+modrý accent, Q3 žluté, Q4 šedé; per-kvadrantní q-label; task karty shadow; stale = levý border + věk text; badge work/personal odebrány
- [x] **FÁZE 3 — Dnes redesign** — sekce s collapsible timeline, WhatNow pod tasky, morning ritual bílý modal, KPI/whatnow světlý styl
- [x] **FÁZE 4 — 1on1 redesign** — grid layout 260px+main, person karty se signály, SignalChip health indikátory, open action items panel, icon edit buttony
- [x] **FÁZE 5 — 1on1 auto-task z kalendáře** — DB calendar_1on1_mappings, calendar.php fetchEventsDays+onenon_scan, settings.php onenon_mappings, OneOnOneMappingModal, cron daily_context.php rozšíření

## Dokončeno dříve

- [x] Setup server, nginx, DB, secrets, Auth (session, 30 dní)
- [x] Tasks CRUD, Eisenhower matrix, drag&drop, záložky, sidebar, checklist, historie
- [x] Quick capture (Cmd+K), overdue highlight, inline edit, Q1 alert badge, fulltext search
- [x] Daktela tickety (proxy, cache, proklikatelné URL)
- [x] Opakující se tasky (weekly/monthly/custom)
- [x] Google Calendar OAuth (curl, SameSite=Lax fix), timeline v Dnes view
- [x] AI návrh priorit (Claude Sonnet 4.6), WhatNowWidget (Haiku), ChatPanel, PrepDocModal
- [x] Ranní ritual, DnesResetModal, samoučení (estimated/actual minutes, přesnost odhadů)
- [x] Denní cron (daily_context.php → tasks-context.md)
- [x] 1on1 modul — osoby, záznamy, action items, mood, tagy, profil, ActionItemsPopover, PrepDocModal
