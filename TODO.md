# Tasks App — TODO

## Bugs — k opravě

- [x] **Action items v 1on1 se otevírají špatně** — opraveno 2026-05-23 (popover right:0 → left:0, otevírá se doprava)
- [ ] **Responsivní design — mobil** — vyladit zobrazení na mobilních zařízeních BEZ dopadu na PC; konkrétně: FAB tlačítko pro přidání rychlého tasku se překrývá se spodním fixním pilem; projít celý layout na <600px

- [x] **Připravit schůzku v 1on1** — opraveno 2026-05-23 (portal do #modals, modal-box CSS, signal chips, profil sekce, z-index)
- [x] **Checklist a Daktela tickety v matici** — opraveno 2026-05-23
- [x] **Volný čas v denním kalendáři** — OK 2026-05-23
- [x] **Důkladné porovnání grafiky: realita vs. návrh** — hotovo 2026-05-23 — projít každou obrazovku (Matice, Dnes, 1on1, Ranní rituál, Modaly, Sidebar) a zdokumentovat všechny odchylky od designu; opravit po jednom

## Refaktor 2026-10 — schváleno 2026-10-29 (nezahájeno)

Rozhodnuto 2026-10-29: Tasks = soukromý projekt bez firemních dat; TOP 10 počítá nová služba „priority“ na operations (VPN),
tasky si tahá přes read-only token. Nové GUI ve vanilla JS, vše pokryté testy. Pravidla: portfolio projects/prioritizace_pravidla.md.

- [ ] F0 Úklid: vyhodit Anthropic API (ai, what_now, prep_topics, chat + klíč), Daktela proxy a cache
- [ ] F4c Schůzky: příprava jen u RSVP accepted (calendar.php doplnit attendees/self/responseStatus)
- [ ] F4d Slack zpráva od Karla/vedení s úkolem bez tasku → upozornit (C.17) — v priority na operations
- [ ] Kompatibilita: tabulky tasks a onenon_* čtou Team Hub MCP (~/team-hub-mcp/server.py) a hourly-radar; daktela_tickets = JSON ["name"] — sloupce jen přidávat
- [ ] Export API: GET otevřených tasků s tokenem, povolená jen IP operations
- [ ] Nové GUI (vanilla JS) + tests/run.sh + smoke test UI
- [ ] GUI návrh v2 (UX revize 29. 10.): režim „Teď“ = 1 další věc + 2 v pořadí, TOP 10 sbalený; stabilní pořadí (posun jen při události, štítky nové/posunuto, nepřeskládat pod rukama)
- [ ] Focus time z kalendáře (po dopoledne, pá odpoledne): skrýt vše kromě SLA v hodinách, ukázat strategický blok
- [ ] Ranní výběr 3 závazků (2 min) + páteční výběr 1–2 strategických věcí do pondělního focus time (timebox do kalendáře)
- [ ] Čekám na: datum připomenutí, sám se vrátí do TOP 10 v den připomenutí
- [ ] Tlačítko „špatně zařazeno“ + důvod → kandidát na pravidlo (Claude ho na vyžádání převede do prioritizace_pravidla.md)
- [ ] Příprava 1on1: nahoře „konkrétní feedback, který dlužíš“
- [ ] Červená jen pro SLA v hodinách a zákazníka, který čeká; ostatní neutrálně (bez alarmové únavy)

- [ ] F0 Úklid: tests/run.sh, oprava cronu daily_context (tasks-context.md stojí od 17. 5.), heslo DB ven ze skillu prioritizuj
- [ ] F1 Datový model: položka se zdrojem (ručně/Fellow/Daktela/CHS/1on1) + odkaz, koš HOŘÍ/DNES/TÝDEN/DELEGOVAT/ČEKÁM NA, osoba, termín slibu
- [ ] F2 Sběr bez AI: cron na hetzneru volá Daktela API (čtecí token) a Fellow Developer API; operations pushuje CHS/response monitor/czdelivery přes import endpoint s tokenem
- [ ] F3 Fellow přes Developer API (cron, webhooky): action items a poznámky; dokončení položky z appky zpět do Fellow
- [ ] F4 TOP 10 napříč zdroji podle portfolio projects/prioritizace_pravidla.md: Jev čte poslední komentář + parent (kdo drží krok, kategorie, slib), PHP řadí podle žebříčku, tier z accountu; u každé položky důvod + citace zdroje; Claude jen na vyžádání
- [ ] F4b Slack: zprávy bez reakce v DM + 4 management kanálech (Karel nahoře) — jen čtení, user token
- [ ] F5 UI: ranní pohled = 3–5 závazků + timeline + „čekám na“; matice jen jako pomocný pohled
- [ ] F6 Vyhodit Anthropic API (ai, what_now, prep_topics, chat) — pravidlo z 19. 10.

## Fronta

- [ ] **Daktela problémové tickety v 1on1** — POZDĚJŠÍ: v detailu osoby zobrazit OPEN tickety kde je agent přiřazen a jsou OPEN/SLA risk; vyžaduje Daktela token + mapování person→daktela login
- [ ] **Hezčí globální nastavení** — redesign Settings modalu; změna avatara (upload nebo výběr barvy/iniciál)
- [ ] **Mood trend chart v 1on1** — mini bar chart nálady (posledních 4 záznamy) v person detailu pod open items
- [ ] Push notifikace / reminder pro overdue tasky
- [ ] Export tasků (CSV, JSON)
- [ ] Tmavý režim
- [ ] Export do Google Docs — po dokončení dne/týdne generovat přehled

## Dokončeno 2026-05-23

- [x] **Ranní rituál — Přeskočit nefunguje** — opraveno 2026-05-23 (forceShowMorning prop + onForceDone callback)
- [x] **Ovládací prvky v Tasks neodpovídají redesignu** — opraveno 2026-05-23 (header: AI + Nový task modrý + avatar; NavSidebar: ⚡ + ↩; search vlevo nahoře; všechny btn-primary modré)

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
