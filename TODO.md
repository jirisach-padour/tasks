# Tasks App — TODO

## Otevřené — bugs

- [x] **Přihlašování — občas nepřihlásí** — po odeslání formuláře zůstane na login stránce bez chybové hlášky; zjistit příčinu (session race, cookie rejection, redirect issue)
- [x] **Tlačítko zobrazit/skrýt heslo** — ikona oko v password inputu
- [x] **Double modal při mazání tasku** — dialog `window.confirm()` se zobrazí 2x, podruhé vypíše "undefined"; task se smaže ale UX je rozbité — zjistit příčinu (pravděpodobně onDelete je volán z TaskCard i TaskModal současně)
- [x] **Odebrání ticketu z detailu uloženého tasku** — × tlačítko v TaskModal existuje, ale ověřit jestli save správně posílá aktualizovaný daktela_tickets array a zobrazuje přiřazené tickety při editaci existujícího tasku
- [x] **Čas poslední aktualizace ticketů v Prague time** — "Obnoveno: {timestamp}" v DaktelaPanel zobrazuje UTC čas; opravit na Europe/Prague zobrazení

## Otevřené — nové funkce

- [x] **Persistent session (30 dní)** — prodloužit session lifetime z 8h na 30 dní (gc_maxlifetime + cookie lifetime), session se prodlužuje při každé aktivitě; nejméně práce, pro osobní app dostačující
- [x] **Změna přihlašovacích údajů** — formulář pro změnu uživatelského jména a hesla (v nastavení nebo samostatná stránka)
- [x] **Vyladit AI prompt pro návrh priorit** — system prompt neobsahuje kontext role (SLA, eskalace, 1on1), chybí instrukce jak pracovat s ai_context polem a relativitou deadlinů
- [x] **Google Calendar — vylepšení** — aktuálně jen read-only přehled dnes/zítra; zvážit: import eventu jako tasku, blokace časových slotů, kontext pro AI, připomenutí
- [x] **1on1 tab — dokončeno** — aktuální implementace je základní; zvážit:
  - automatické natažení agentů L1 skupiny ze Daktely (API sachj)
  - hodnocení nálady 1–5 per schůzka
  - tagy: výkon / SLA / osobní / rozvoj / feedback
  - dashboard: kdy byl poslední 1on1 (highlight >30 dní), počet otevřených action items
  - mobile-friendly edit během schůzky
- [x] **Opakování — konkrétní den ze kalendáře** — při weekly/monthly opakování vybrat konkrétní den (např. každý pátek, každý 1. v měsíci); propojit s Google Calendar jako start datum

## Otevřené — bugs (2026-04-26)

- [x] **1on1 — profil osoby se nenačte do editace** — po uložení profilu, zavření formuláře a opětovném otevření (✎) jsou políčka prázdná;  objekt nemá  z  state (people list vrací profile, ale při setEditingPerson se nepředává)


## Otevřené — nové funkce (2026-04-26)

- [x] **1on1 záložka — skrýt pravý sidebar** — na 1on1 záložce schovat pravý sidebar (tickety + kalendář nejsou relevantní při vedení rozhovoru); zvážit jestli i levý (KPI + checklist) nebo jen pravý
- [x] **Favicon** — ikonka v záhlaví tabu prohlížeče; návrh: stylizované "T" v navy barvě nebo jednoduchá ikona matice

## Otevřené — nové funkce (2026-04-26 #2)

- [ ] **Alert na urgentní tasky** — upozornění v hlavičce nebo sidebaru pokud jsou v Q1 tasky s prošlým nebo dnešním deadlinem; inspirace z demo stránky kde to vypadalo užitečně

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

- [x] **Settings — redesign UX** — aktuální SettingsModal je generický formulář; inspirovat se dobrými systémy (např. GitHub/Linear settings): sekce Účet s avatar/jménem nahoře, inline editace jednotlivých polí (ne celý formulář najednou), potvrzení změny hesla přes současné heslo + nové heslo + zopakovat, success/error feedback přímo u pole (ne alert), tlačítka Uložit jen u změněného pole

## Otevřené — nové funkce (2026-04-27)

- [ ] **Rychlý checklist — editace položek** — možnost editovat text již vytvořené položky v rychlém checklistu (aktuálně lze jen přidat/odškrtnout/smazat)
