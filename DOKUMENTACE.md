# Tasks App — technická dokumentace

## URL a přístup
- **App:** `https://padour.duckdns.org/tasks/`
- **GitHub:** `github.com:jirisach-padour/tasks.git` (git root = `/var/www/app/tasks/`)
- **Server:** hetzner-personal (`ssh hetzner-personal`)

## Stack
- PHP 8.5 + React 18 (Babel standalone, inline v index.php — žádný build)
- MariaDB `tasks`, user `tasks`
- nginx na Hetzner (`padour.duckdns.org`), SSH alias `hetzner-personal`
- Secrets: `/etc/tasks/secrets.php` (MIMO git, MIMO webroot, root:apache 640)

---

## Souborová struktura

```
/var/www/app/tasks/
  index.php          (~2900 ř) — celé React UI (Babel standalone inline)
  api.php            (~35 ř)   — router match($action) → api/ podsložka
  config.php         (~45 ř)   — requireAuth(), buildQuery(), DB konstanty
  login.php                    — přihlašovací stránka (bez auth guardu)
  oauth-callback.php (~55 ř)   — Google OAuth callback (curl, SameSite=Lax)
  tasks-context.md             — denní export pro MCP (cron 6:00, apache vlastník)
  TODO.md                      — otevřené úkoly a bugy
  DOKUMENTACE.md               — tento soubor
  api/
    tasks.php        — CRUD tasků + recurrence logika + month_accuracy endpoint
    checklist.php    — CRUD checklist items
    daktela.php      — proxy Daktela API v6 + DB cache (daktela_cache)
    calendar.php     — Google Calendar token refresh + pull eventů
    ai.php           — Claude Sonnet 4.6 → návrh kvadrantů (+ 1on1 kontext, Daktela cache, accuracy)
    what_now.php     — Claude Haiku → "Co teď?" widget
    prep_topics.php  — Claude Haiku → témata pro 1on1
    chat.php         — Claude Haiku → stateless chat s kontextem tasků/1on1/Daktela
    onenon.php       — CRUD 1on1 schůzek (osoby, poznámky, action items, profily)
    settings.php     — změna username/hesla
  cron/
    daily_context.php — export tasks-context.md; crontab: 0 6 * * * jako apache
  lib/
    DB.php           — PDO wrapper: DB::q(), DB::insert(), DB::update()

/etc/tasks/secrets.php   — APP_USER, APP_PASS_HASH, DB_*, GOOGLE_CLIENT_ID,
                           GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI,
                           ANTHROPIC_API_KEY (MIMO git, MIMO webroot)
```

---

## DB schema (databáze `tasks`)

```sql
tasks
  id INT PK, title VARCHAR(255) NOT NULL,
  description TEXT, ai_context TEXT,
  quadrant ENUM('urgent_important','important','urgent','other') DEFAULT 'other',
  status ENUM('open','done') DEFAULT 'open',
  type ENUM('work','personal') DEFAULT 'work',
  due_date DATE NULL,
  daktela_tickets JSON,           -- ["ticket_name1", "ticket_name2", ...]
  recurrence VARCHAR(20),         -- 'none'|'weekly'|'monthly'|'custom'
  recurrence_day TINYINT,         -- weekly: 0-6 (Ne=0), monthly: 1-31
  recurrence_interval TINYINT,
  recurrence_unit ENUM('days','weeks','months'),
  sort_order INT DEFAULT 0,
  daily_order INT NULL,           -- NULL = není v Dnes; hodnota = pořadí
  done_at TIMESTAMP NULL,
  estimated_minutes SMALLINT NULL, -- uživatelský odhad (samoučení)
  actual_minutes SMALLINT NULL,    -- skutečný čas (samoučení)
  created_at TIMESTAMP, updated_at TIMESTAMP ON UPDATE

checklist_items
  id INT PK, title VARCHAR(255) NOT NULL,
  done TINYINT(1) DEFAULT 0, done_at TIMESTAMP NULL,
  sort_order INT DEFAULT 0, created_at TIMESTAMP

calendar_tokens
  id INT PK, access_token TEXT, refresh_token TEXT,
  expires_at DATETIME, updated_at TIMESTAMP ON UPDATE
  -- vždy jen jeden řádek (DELETE + INSERT při propojení)

daktela_cache
  name VARCHAR(100) PK, title VARCHAR(500),
  stage VARCHAR(20), sla_deadline VARCHAR(50)
  -- DB cache ticketů sachj (OPEN) — refresh ručně nebo po přihlášení

daktela_cache_meta
  id INT PK DEFAULT 1, refreshed_at TIMESTAMP NULL, ticket_count INT DEFAULT 0

onenon_notes
  id INT PK, person VARCHAR(100) NOT NULL,
  meeting_date DATE NOT NULL, notes TEXT,
  action_items JSON,              -- [{"text":"...", "done": false}, ...]
  mood TINYINT NULL,              -- 1-5
  tags JSON NULL,                 -- ["vykon","sla","osobni","rozvoj","feedback"]
  created_at TIMESTAMP

onenon_people
  id INT PK, name VARCHAR(100) UNIQUE,
  description TEXT,
  profile JSON NULL,              -- {performance,potential,strength,development,...}
  created_at, updated_at TIMESTAMP ON UPDATE
```

---

## API endpointy (`api.php?action=X`)

| action | Metoda | Popis |
|--------|--------|-------|
| `tasks` | GET | seznam tasků (+ today_done count) |
| `tasks` | GET `?search=q` | fulltext LIKE v title+description+ai_context |
| `tasks` | GET `?daily=1` | tasky v denním plánu (daily_order IS NOT NULL) |
| `tasks` | GET `?history=month_accuracy` | přesnost odhadů za 30 dní `{accuracy, count}` |
| `tasks` | POST | vytvoření tasku |
| `tasks` | PUT `?id=N` | update; při status=done + recurrence → INSERT nový |
| `tasks` | DELETE `?id=N` | hard delete |
| `checklist` | GET/POST/PUT/DELETE | CRUD checklist items |
| `daktela_login` | POST | proxy Daktela login → accessToken |
| `daktela` | POST | proxy Daktela API v6 (whitelist endpointů) |
| `daktela_cache` | GET | tickety z DB cache (bez tokenu) |
| `daktela_cache` | POST `{accessToken}` | refresh cache z Daktela API |
| `calendar` | GET | `{connected, events[{date,time,title,allDay,durationH}]}` |
| `calendar?sub=connect` | GET | `{redirect: google_oauth_url}` |
| `calendar?sub=disconnect` | POST | smaže token z DB |
| `ai_suggest` | POST | Claude Sonnet 4.6 → `[{id, quadrant, reason}]` |
| `what_now` | POST | Claude Haiku → `{text, task_title, task_quadrant}` |
| `prep_topics` | POST | Claude Haiku → `{topics: [...]}` |
| `chat` | POST `{message, history[]}` | Claude Haiku → `{reply}` (stateless, kontext z DB) |
| `onenon` | GET/POST/PUT/DELETE | 1on1 CRUD; GET list vrací open_action_items per osoba |
| `settings` | POST | změna username/hesla |
| `logout` | POST | session_destroy() |

---

## React komponenty (index.php)

### Helpers
- `apiFetch(action, method, body, params)` — fetch na api.php + JSON, throws on error
- `toast(msg)` — dočasná notifikace (2s)
- `buildQuery(params)` — builduje query string pro Daktela API (arrays správně)

### Komponenty
| Komponenta | Popis |
|------------|-------|
| `App` | Root: state, načítání dat, handlery |
| `TabBar` | Work / Osobní / Vše / Dnes / Hotovo / 1on1 |
| `EisenhowerMatrix` | 2×2 grid kvadrantů |
| `Quadrant` | Jeden kvadrant s drag&drop a inline přidáváním |
| `TaskCard` | Karta tasku: checkbox, title, description, due_date, badges |
| `DoneTimeModal` | Po dokončení tasku: "Za jak dlouho?" — ukládá actual_minutes |
| `DnesResetModal` | Nový den: výběr co přenést do Dnes; hotové se odstraní automaticky |
| `TaskModal` | Detail/edit tasku: název, popis, AI context, kvadrant, typ, termín, **odhad (min)**, Daktela tickety, opakování |
| `DaktelaPanel` | Sidebar: tickety z DB cache, přiřazené za collapse, Obnovit + timestamp |
| `DaktelaAuthModal` | Přihlášení do Daktely (user+pass → accessToken) |
| `CalendarPanel` | Sidebar: dnešní/zítřejší Google Calendar události |
| `ChecklistPanel` | Sidebar: rychlé zaškrtávací položky |
| `KpiPanel` | Sidebar: dnešní výkon + **přesnost odhadů za 30 dní** |
| `ChatPanel` | Sidebar: AI Asistent chat (Claude Haiku, history v React state, collapse) |
| `AiSuggestModal` | Výsledky AI návrhu kvadrantů — přijmout/odmítnout |
| `SearchResults` | Fulltext výsledky (tasky z DB + checklist + Daktela) |
| `OneOnOneView` | 1on1 záložka: seznam osob + timeline schůzek |
| `OneOnOneModal` | Detail/edit schůzky + action items checklist |
| `DnesView` | Záložka Dnes: timeline split view + WhatNowWidget |
| `MorningRitual` | Ranní overlay 6-10h: doporučené Q1+Q2 tasky, batch přidání do Dnes |
| `WhatNowWidget` | "Co teď?" tlačítko v Dnes → Claude Haiku → inline výsledek (30s) |

---

## Daktela integrace

**Auth:** user+pass → Daktela Login API → accessToken v sessionStorage (nikdy perzistentní).

**DB cache:** Tickety sachj (OPEN) v `daktela_cache` tabulce. Načítají se bez tokenu.

**Nested filter formát** (jedině fungující):
```
filter[logic]=and
filter[filters][0][logic]=and
filter[filters][0][filters][0][field]=user  +[operator]=in  +[value][0]=sachj
filter[filters][0][filters][1][field]=stage +[operator]=in  +[value][0]=OPEN
filter[filters][1][field]=_ticketView +[operator]=eq +[value]=default
filter[filters][2][field]=id_merge +[operator]=isnull
fields[0]=name&fields[1]=title&fields[2]=stage&fields[3]=sla_deadline
```

---

## Google Calendar integrace

**OAuth flow:**
1. "Propojit" → `?action=calendar&sub=connect` → Google redirect
2. Google callback → `oauth-callback.php`
3. PHP vymění code za tokeny přes **curl** (file_get_contents na FPM nefunguje)
4. Tokeny uloženy v `calendar_tokens`; auto-refresh při expiraci

**Kritické:**
- `SameSite=Lax` — Google redirect je cross-site, Strict zablokuje session cookie → HTTP 500
- `require lib/DB.php` v oauth-callback.php — jinak Fatal: Class DB not found
- Scope: `calendar.readonly`, Internal audience (daktela.com workspace)

---

## AI integrace

| Endpoint | Model | Účel | max_tokens |
|---|---|---|---|
| `ai_suggest` | claude-sonnet-4-6 | Kvadranty pro všechny tasky | 4096 |
| `what_now` | claude-haiku-4-5 | "Co dělat teď?" kontext | 300 |
| `prep_topics` | claude-haiku-4-5 | Témata pro 1on1 | 400 |
| `chat` | claude-haiku-4-5 | Chat asistent (stateless) | 800 |

**ai_suggest kontext (od 2026-05-17):**
- Tasky + deadline + daktela_tickets
- Google Calendar dnešní eventy
- 1on1 záznamy: nálada, otevřené action items, dny od poslední schůzky
- Daktela cache tickety (SLA deadline)
- Přesnost odhadů (accuracy ratio za 30 dní)
- Prompt caching: system prompt + 1on1 blok jako ephemeral cache

---

## Samoučení (od 2026-05-17)

- `estimated_minutes` v TaskModal — uživatel odhadne čas při vytvoření/editaci
- `actual_minutes` — zadává se po dokončení přes DoneTimeModal (volitelné, lze přeskočit)
- **KpiPanel** zobrazí "Přesnost odhadů: X%" pokud ≥2 záznamy za 30 dní
  - Zelená ≤110%, oranžová 111–130%, červená >130%
- **AI** dostane accuracy note v promptu (pokud odchylka >15%)

---

## Záložka Dnes

**DnesResetModal** (od 2026-05-17):
- Trigger: `localStorage['lastDnesCheck'] !== today` AND existují tasky s daily_order
- Zobrazí se PŘED morning ritual
- Hotové tasky z Dnes → automaticky odebere (daily_order = null)
- Nedokončené → checkboxy (default zaškrtnuté = přenést), odškrtnutím se odeberou
- Po potvrzení: nastaví lastDnesCheck = today, pak zkontroluje showMorning (6–10h)

**Morning Ritual:**
- `localStorage['lastMorningCheck'] !== today` AND hodina 6–10
- Spouští se po DnesResetModal

**Denní cron:**
- `cron/daily_context.php` → `/var/www/app/tasks/tasks-context.md`
- Crontab: `0 6 * * * php /var/www/app/tasks/cron/daily_context.php` (user apache)
- Obsah: denní plán, kvadranty counts, overdue, 1on1 action items

---

## Opakující se tasky (recurrence)

- `recurrence`: 'none' | 'weekly' | 'monthly' | 'custom'
- 'custom' + `recurrence_interval` + `recurrence_unit` = každých N dní/týdnů/měsíců
- Při `status=done`: api/tasks.php INSERTuje nový task s posunutým `due_date`

---

## Auth

PHP session, jeden uživatel. Credentials v secrets.php (bcrypt hash).
Session: 30d lifetime, HttpOnly, Secure, SameSite=Lax.
`requireAuth()` v config.php — na začátku každého PHP souboru (kromě login.php, oauth-callback.php).

---

## Deploy

```bash
# Python patch (bezpečné pro víceřádkové edity):
scp /tmp/patch.py hetzner-personal:/tmp/
ssh hetzner-personal "python3 /tmp/patch.py && php -l /var/www/app/tasks/index.php"

# Cron ručně:
ssh hetzner-personal "sudo -u apache php /var/www/app/tasks/cron/daily_context.php"

# PHP error log:
ssh hetzner-personal "sudo tail -30 /var/log/php-fpm/www-error.log"

# DB:
ssh hetzner-personal "sudo mariadb tasks"

# Git (auto-commit hook commituje každou změnu automaticky):
ssh hetzner-personal "cd /var/www/app/tasks && git log --oneline -5"
```

---

## Kompletní redesign — 2026-05-22–23

### Design systém (nové CSS proměnné)

Starý design: navy gradient header, `--red`, `--navy`, `--grey-bg`.
Nový design: čistě bílý, neutrální, akcent modrá.

```
--bg: #F7F7F8            stránkové pozadí (světle šedé)
--surface: #FFFFFF        karty, panely, modaly
--surface-2: #F0F0F2      Q4 kvadrant, sekundární pozadí
--border: #E4E4E7         všechny bordery
--text: #18181B           primární text
--text-2: #71717A         sekundární (popisky, meta)
--text-3: #A1A1AA         placeholder, disabled
--accent: #2563EB         primární modrá (tlačítka, active nav)
--accent-bg: #EFF6FF      světlé pozadí pro accent (Q2 kvadrant)
--danger: #DC2626 / --danger-bg: #FFF5F5    Q1, overdue
--warning: #D97706 / --warning-bg: #FFFBEB  Q3, stale, termíny
--success: #16A34A / --success-bg: #F0FDF4  hotovo, přesné odhady
--purple: #7C3AED / --purple-bg: #F5F3FF    all-day events, high potential
--shadow-sm: 0 1px 3px rgba(0,0,0,.06)
--shadow-md: 0 4px 12px rgba(0,0,0,.08)
--radius: 10px / --radius-sm: 6px
```

### Layout

```
┌────────────────────────────────────────────────────────┐
│  app-header (52px bílý, shadow-sm, border-bottom)      │
│  logo | sep | search | AI btn | Nový task | avatar     │
├──────┬─────────────────────────────────────┬───────────┤
│ nav  │                                     │  sidebar  │
│ 60px │  main-content (flex-1)              │  pravý    │
│ side │  záložky: Matice / Dnes / 1on1      │  panely   │
│ bar  │                                     │           │
└──────┴─────────────────────────────────────┴───────────┘
```

**NavSidebar** (levý 60px pruh, bílý, border-right):
- `.nav-item` = 44×44px icon button
- Aktivní: `color: --accent; background: --accent-bg` + `::before` pruh 3px --accent vlevo
- `nav-sep` = 28px horizontální oddělovač

**app-header** (52px): bílé, border-bottom, shadow-sm; prvky: název (15px bold), separator (1px 20px), search box (max 340px, výška 34px), AI btn, "Nový task" (btn-primary), avatar.

### Eisenhower matrix — kvadranty vizuální hierarchie

| Kvadrant | Pozadí | Top border | Label barva |
|---|---|---|---|
| Q1 Urgentní+Důležité | `--danger-bg` | 3px `--danger` | `--danger` bold 800 |
| Q2 Důležité | `--surface` bílé | 3px `--accent` | `--accent` bold 700 |
| Q3 Urgentní | `--warning-bg` | 3px `--warning` | `--warning` bold 700 |
| Q4 Ostatní | `--surface-2` šedé | žádný | `--text-3` |

Q-count badge barvy odpovídají kvadrantu.

### Modal portal pattern

Modaly renderovat přes `ReactDOM.createPortal` do `<div id="modals">` (v body mimo React root).

**Důvod:** Modaly uvnitř `.quadrant` nebo `.sidebar` dědí jejich stacking context → zobrazí se pod jiným prvkem nebo jsou oříznuté.

```jsx
// v App return (mimo root content):
<div id="modals"></div>

// v komponentě:
{open && ReactDOM.createPortal(
  <MyModal onClose={() => setOpen(false)} />,
  document.getElementById('modals')
)}
```

Týká se PrepDocModal, TaskModal, OneOnOneModal — vše co musí být nad vším ostatním.

**`.modal-box` vs `.modal`:**
- `.modal` = padding:28px, jednoduché
- `.modal-box` = bez paddingu, `display:flex; flex-direction:column; overflow:hidden` — pro modaly s fixním headerem a scrollovatelným obsahem

### 1on1 redesign

**Layout:** CSS grid `260px auto` — levý sloupec osoby, pravý detail.

**SignalChip komponenta:**
- `type`: `'ok'` (zelená), `'warn'` (oranžová), `'info'` (modrá), `'purple'`
- Zobrazuje: dny od poslední schůzky, počet open items, nálada trend (↑↓→), high potential

**ActionItemsPopover fix:**
- Popover se otevírá DOPRAVA (`left:0`), ne doleva (`right:0`) — jinak přeteče mimo viewport

### Calendar 1on1 mapování

**DB tabulka:**
```sql
calendar_1on1_mappings (
  id INT PK,
  event_keyword VARCHAR(100) UNIQUE,  -- klíčové slovo v názvu Google Cal eventu
  person VARCHAR(100),                 -- jméno v onenon_people
  active TINYINT(1) DEFAULT 1
)
```

**API:**
- `calendar?sub=onenon_scan` GET — vrátí eventy matchující klíčová slova
- `settings?sub=onenon_mappings` GET — vrátí mapování
- `settings?sub=onenon_mappings` POST — přepíše mapování (DELETE + INSERT)

**OneOnOneMappingModal** — UI pro správu mapování, přístup z 1on1 záložky.

### Ranní rituál fix

Tlačítko "Přeskočit" nenavigoval na Dnes záložku → `forceShowMorning` prop + `onForceDone` callback:
```jsx
<DnesView forceShowMorning={true} onForceDone={() => setActiveTab('dnes')} />
```

---

## Babel standalone omezení

- **IIFE v JSX je fatální** (blank page bez erroru) — logiku vždy do proměnných před `return`
- **Nested template literals jsou fatální** — použít string concatenation uvnitř backtick stringu
- **`[...set]` spread Set** → použít `Array.from(set)`
- **`const Foo = () => (...)` jako JSX komponenta** → použít `const fooJsx = (...)` a vrátit přímo
