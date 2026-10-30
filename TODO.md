# Tasks App — TODO

## Bugs — k opravě

- [x] **Action items v 1on1 se otevírají špatně** — opraveno 2026-05-23 (popover right:0 → left:0, otevírá se doprava)
- [ ] Mobil: projít nové GUI na telefonu (staré FAB už není)

- [x] **Připravit schůzku v 1on1** — opraveno 2026-05-23 (portal do #modals, modal-box CSS, signal chips, profil sekce, z-index)
- [x] **Checklist a Daktela tickety v matici** — opraveno 2026-05-23
- [x] **Volný čas v denním kalendáři** — OK 2026-05-23
- [x] **Důkladné porovnání grafiky: realita vs. návrh** — hotovo 2026-05-23 — projít každou obrazovku (Matice, Dnes, 1on1, Ranní rituál, Modaly, Sidebar) a zdokumentovat všechny odchylky od designu; opravit po jednom

## Refaktor 2026-10 — schváleno 2026-10-29 (nezahájeno)

Rozhodnuto 2026-10-29: Tasks = soukromý projekt bez firemních dat; TOP 10 počítá nová služba „priority“ na operations (VPN),
tasky si tahá přes read-only token. Nové GUI ve vanilla JS, vše pokryté testy. Pravidla: portfolio projects/prioritizace_pravidla.md.

- [x] F0: Anthropic API pryč (endpointy 410, klíč smazán, záloha /etc/tasks/secrets.php.bak-20261029) — 2026-10-29; Daktela proxy zůstává jen pro stary.php
- [x] RSVP v calendar.php (lib/Kalendar.php) + příprava jen u potvrzených — 2026-10-29
- [ ] Kompatibilita: dokud běží hourly-radar, nechat tabulku tasks na hetzneru beze změny sloupců (Team Hub už čte Priority)
- [~] Export API — nahrazeno: GUI bere pracovní data z Priority prohlížečem (klíč), hetzner nic firemního nedrží
- [x] Nové GUI (index.php + js/novy.js + js/logika.js, stará verze stary.php) + testy na serveru (logika, GUI v node, kalendář, web) — 2026-10-29
- [x] GUI návrh v2 (Teď, všichni čekající, stabilní pořadí, štítky nové/posunuto) — 2026-10-29
- [x] Focus time (po dopoledne, pá odpoledne, blok v kalendáři) — 2026-10-29
- [ ] Ranní výběr 3 závazků (2 min) + páteční výběr 1–2 strategických věcí do pondělního focus time (timebox do kalendáře)
- [x] Čekám na s připomenutím, vrací se do TOP 10 — 2026-10-29
- [x] Špatně zařazeno → pr_feedback (kandidáti na pravidla) — 2026-10-29
- [x] Příprava 1on1 s „feedback, který dlužíš“ u potvrzené schůzky — 2026-10-29
- [x] Po pracovní době práce zavřená, „přesto ukázat“ — 2026-10-29
- [x] Červená jen pro čekající zákazníky a SLA — 2026-10-29

- [x] F0: tests/run.sh, Anthropic pryč; cron daily_context zrušen (nepotřebný) — 2026-10-29
- [ ] Heslo DB ven ze skillu prioritizuj (skill zastaralý → smazat nebo přepsat na Priority API)
- [x] F1 datový model v Priority (pr_tasks, čekám na, pr_tickety, pr_slack, pr_hotovo) — 2026-10-29
- [x] F2 sběr bez Claude: cron na operations (Daktela + Jev + Slack) — 2026-10-29
- [ ] F3 Fellow přes Developer API (cron, webhooky): action items a poznámky; dokončení položky z appky zpět do Fellow
- [x] F4 TOP 10 podle pravidel (lib/poradi.php) — 2026-10-29
- [x] F4b Slack bez reakce (kanály, DM, Karel) — 2026-10-29
- [x] F5 nové GUI (Teď, dlaždice, tabule s přetahováním, Dnes, 1on1) — 2026-10-29
- [x] F6 Anthropic API pryč, klíč smazán — 2026-10-29

## Po přepnutí 29. 10. — zbývá
- [ ] Ověřit v prohlížeči: propojení klíčem (Priority → Propojit), CORS/Private Network v Chrome na VPN, odkliknutí, úprava úkolu
- [ ] Smazat pracovní tasky na hetzneru (164, shodné s pr_tasks 29. 10.) — jen po potvrzení JS
- [ ] Tento týden: podíl času zákazníci × nástroje — chybí zdroj dat (navrhnout)
- [ ] Kalendář přesunout na operations (OAuth klient z kpi-dashboardu, redirect URI) — dnes jede z hetzneru
- [ ] Páteční výběr strategického bloku + timebox do kalendáře (zatím ruční výběr v GUI)
- [ ] stary.php + api/daktela.php + onenon_* tabulky na hetzneru smazat po týdnu bez potřeby

## Fronta

- [ ] **Daktela problémové tickety v 1on1** — POZDĚJŠÍ: v detailu osoby zobrazit OPEN tickety kde je agent přiřazen a jsou OPEN/SLA risk; vyžaduje Daktela token + mapování person→daktela login
- [ ] **Hezčí globální nastavení** — redesign Settings modalu; změna avatara (upload nebo výběr barvy/iniciál)
- [ ] **Mood trend chart v 1on1** — mini bar chart nálady (posledních 4 záznamy) v person detailu pod open items
- [ ] Push notifikace / reminder pro overdue tasky
- [ ] Export tasků (CSV, JSON)
- [x] Tmavý režim — 2026-10-29
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
