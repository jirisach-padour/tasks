# Tasks App [OSOBNÍ]

**Server:** hetzner-personal (95.217.15.95)  
**Cesta:** `/var/www/app/tasks/`  
**URL:** https://padour.duckdns.org/tasks/  
**Git:** github.com/jirisach-padour (auto-commit hook)  
**Tech:** PHP 8.5, React 18 přes Babel standalone (inline v index.php), MariaDB `tasks`, bez Composeru

---

## Co dělá

Osobní task manager na Eisenhowerově matici (urgent/important). Integruje Daktela tickety, Google Calendar a Claude AI pro návrh priorit. Záložky Vše / Práce / Osobní / Dnes / Historie / 1on1.

---

## Secrets

`/etc/tasks/secrets.php` — mimo webroot, mimo git, přístup root:apache 660 (660 kvůli PHP zápisu při změně hesla).

```php
define('APP_USER',             'jirisach');
define('APP_PASS_HASH',        '...');        // bcrypt
define('DB_HOST',              'localhost');
define('DB_NAME',              'tasks');
define('DB_USER',              'tasks');
define('DB_PASS',              '...');
define('GOOGLE_CLIENT_ID',     '961131189522-...');
define('GOOGLE_CLIENT_SECRET', 'GOCSPX-...');
define('GOOGLE_REDIRECT_URI',  'https://padour.duckdns.org/tasks/oauth-callback.php');
define('ANTHROPIC_API_KEY',    'sk-ant-api03-...');
```

---

## Klíčové soubory

| Soubor | Účel |
|---|---|
| `index.php` | Celé React UI (~2800+ řádků, Babel standalone inline) |
| `api.php` | Router — match() dispatch na api/ podsložku |
| `config.php` | Auth guard requireAuth(), buildQuery(), konstanta DB |
| `login.php` | Přihlašovací stránka (bez auth guardu) |
| `oauth-callback.php` | Google OAuth callback — výměna code za tokeny (curl, SameSite=Lax) |
| `api/tasks.php` | CRUD tasků + recurrence logika; SELECT * → vrací created_at, updated_at |
| `api/checklist.php` | CRUD checklist items |
| `api/daktela.php` | Proxy na Daktela API v6 + DB cache; whitelist: tickets, activities, users, groups |
| `api/calendar.php` | Google Calendar API — token refresh + pull eventů; vrací date, time, durationH, allDay |
| `api/ai.php` | Claude Sonnet 4.6 — návrh kvadrantů (+ 1on1 kontext, Daktela cache, accuracy od 2026-05-17) |
| `api/what_now.php` | Claude Haiku — "Co teď?" (time, nextEvent, topQ1, dailyTasks → 2-3 věty + task) |
| `api/prep_topics.php` | Claude Haiku — 3 témata pro 1on1 |
| `api/chat.php` | Claude Haiku — chat asistent (stateless, kontext tasků/1on1/Daktela z DB) |
| `api/onenon.php` | CRUD 1on1 schůzek + profily; GET list vrací open_action_items pole per osoba |
| `api/settings.php` | Změna username/hesla (přepis secrets.php) |
| `cron/daily_context.php` | Export tasks-context.md (denní plán, kvadranty, overdue, 1on1 items); crontab 6:00 apache |
| `lib/DB.php` | PDO wrapper |

---

## DB schema (MariaDB `tasks`, user `tasks`)

```sql
tasks (
    id, title, description, ai_context,
    quadrant ENUM('urgent_important','important','urgent','other'),
    status ENUM('open','done'),
    type ENUM('work','personal'),
    due_date DATE,
    daktela_tickets JSON,          -- ["ticket_name1", ...]
    recurrence VARCHAR(20),        -- 'none'|'weekly'|'monthly'|'custom'
    recurrence_day TINYINT,        -- weekly: 0-6 (Ne=0,Po=1,...,So=6), monthly: 1-31
    recurrence_interval TINYINT,
    recurrence_unit ENUM('days','weeks','months'),
    sort_order INT,
    daily_order INT NULL,          -- NULL = není v Dnes; hodnota = pořadí
    done_at TIMESTAMP,
    estimated_minutes SMALLINT NULL,  -- uživatelský odhad (samoučení)
    actual_minutes SMALLINT NULL,     -- skutečný čas (samoučení)
    created_at, updated_at
)

checklist_items (id, title, done, done_at, sort_order, created_at)
calendar_tokens (id, access_token, refresh_token, expires_at, updated_at)
daktela_cache (name PK, title, stage, sla_deadline)
daktela_cache_meta (id=1, refreshed_at Prague time, ticket_count)

onenon_notes (id, person, meeting_date, notes TEXT,
    action_items JSON,   -- [{text, done}]
    mood TINYINT NULL,   -- 1-5
    tags JSON NULL,      -- ["vykon","sla","osobni","rozvoj","feedback"]
    created_at)

onenon_people (id, name VARCHAR(100) UNIQUE, description TEXT,
    profile JSON NULL,   -- {performance,potential,mgmt_effort,strength,development,comm_style,motivation,notes}
    created_at, updated_at)
```

---

## API endpointy

Všechny přes `api.php?action=X`, session auth.

| action | Metoda | Popis |
|---|---|---|
| `tasks` | GET | seznam tasků + today_done count |
| `tasks` | GET `?search=q` | fulltext hledání |
| `tasks` | GET `?daily=1` | tasky v denním plánu |
| `tasks` | POST/PUT/DELETE | CRUD + recurrence |
| `checklist` | GET/POST/PUT/DELETE | CRUD, PUT podporuje i update title |
| `daktela_login` | POST | proxy Daktela login |
| `daktela` | POST | proxy Daktela API v6 |
| `daktela_cache` | GET/POST | DB cache ticketů sachj |
| `calendar` | GET/POST | events (date+time+durationH+allDay), connect/disconnect |
| `tasks` | GET `?history=month_accuracy` | přesnost odhadů za 30 dní `{accuracy, count}` |
| `ai_suggest` | POST | Claude Sonnet 4.6 → kvadranty (+ 1on1, Daktela cache, accuracy kontext) |
| `what_now` | POST | Claude Haiku → "Co teď?" s kontextem |
| `prep_topics` | POST | Claude Haiku → 3 témata pro 1on1 |
| `chat` | POST | Claude Haiku → `{reply}` (stateless history, kontext z DB) |
| `onenon` | GET/POST/PUT/DELETE | 1on1 CRUD; GET list vrací open_action_items |
| `settings` | POST | změna username/hesla |
| `logout` | POST | session_destroy() |

---

## Záložka Dnes (DnesView) — 2026-05-11

**Split layout:**
- Levý sloupec: denní timeline kalendáře (hodiny 8–17, bloky events)
- Pravý sloupec: dnešní tasky + WhatNowWidget
- Pokud žádné calendar events → jen pravý sloupec (flat view)
- "● Xh volného" = 8h − sum(durationH events)

**Ranní ritual (MorningRitual):**
- Zobrazí se pokud: hodina 6–10 AND `localStorage['lastMorningCheck'] !== today`
- Stats: Q1 count, po deadline, celkem open
- Doporučené tasky: top 5 Q1+Q2 sorted by deadline
- Klikatelné checkboxy → "Potvrdit" → `handleBatchAddToDaily(ids)` = batch PUT daily_order

**WhatNowWidget:**
- Tlačítko "✦ Co mám dělat teď?" v Dnes záložce
- POST `what_now` s: time, nextEvent, topQ1 (3 tasky), dailyTasks
- Claude Haiku → text (2-3 věty) + task_title + task_quadrant
- Výsledek inline pod tlačítkem, zmizí po 30s

---

## 1on1 modul

- **ActionItemsPopover**: červený badge v dashboard → dropdown skupinami per osoba → klik naviguje na osobu
  - Data: `people[].open_action_items` = pole textů otevřených items (z api/onenon.php GET)
- **PrepDocModal**: tlačítko "📋 Podklady" v detailu osoby
  - Generuje: nálada trend (prevNote vs lastNote.mood), open action items, profil
  - Tlačítko "✦ Vygenerovat pomocí AI" → POST prep_topics → 3 témata (Claude Haiku)
  - "Kopírovat" → `navigator.clipboard.writeText(lines.join('\n'))`

---

## Vizuální featury (2026-05-11)

**Stale task indicator:**
- `created_at` → daysOld; 7–20d = `.stale-mid` (žlutý proužek), 21+d = `.stale-old` (šedý + opacity 88%)
- Jen open tasky v matici

**Task description v kartě:**
- `{task.description && <div className="task-desc">...}` pod názvem tasku
- CSS: `font-size:11px; color:var(--grey-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis`

---

## Daktela integrace

- Token: sessionStorage (nikdy DB/localStorage)
- DB cache viditelná bez tokenu
- **Nested filter formát** — flat `filter[N][field]` Daktela ignoruje, musí být `filter[filters][N][filters][M][field]`
- L1 skupina ID: `groups_62715929ce76e354293456`

---

## AI integrace

| Endpoint | Model | Účel | max_tokens |
|---|---|---|---|
| ai_suggest | Claude Sonnet 4.6 | Kvadranty pro všechny tasky | 4096 |
| what_now | Claude Haiku | "Co dělat teď?" kontext | 300 |
| prep_topics | Claude Haiku | 3 témata pro 1on1 | 400 |
| chat | Claude Haiku | Chat asistent (stateless) | 800 |

**ai_suggest kontext od 2026-05-17:** tasky + calendar + 1on1 záznamy (nálada, action items, dny bez 1on1) + Daktela cache + accuracy ratio. Prompt caching pro system prompt + 1on1 blok.

---

## Triky a gotchas

- **Babel standalone IIFE trap:** žádné IIFE v JSX — blank page bez chybové hlášky
- **Babel: `const Foo = () => (...)` uvnitř komponenty jako JSX:** místo toho použít `const fooJsx = (...)` a vrátit proměnnou přímo (ne jako `<Foo />`)
- **Babel: nested object literal v style prop:** `style={{bg: {a:'x'}[key]}}` → extrahovat do proměnné
- **Babel: `[...set]` spread Set:** použít `Array.from(set)` — bezpečnější v Babel standalone
- **Literal newline v JS stringu přes Python patch:** `lines.join('\n')` musí být v Python patchi jako `'\\n'` jinak se zapíše reálný newline → Babel "Unterminated string constant"
- **Nested template literals:** zakázané v JSX — ukončí vnější backtick string
- **Python patch pattern** pro server edity (heredoc špatně escapuje `$`, `"`, `\`)
- **buildQuery()** místo `http_build_query()` pro Daktela API
- **SameSite=Strict** nefunguje pro OAuth callbacks — použít Lax
- **curl** místo `file_get_contents` pro HTTP POST v PHP-FPM
- **apiFetch secondary param:** použít `sub` místo `action` — spread přepíše primární `action`
- **Monthly recurrence day overflow:** clampovat přes `(int)(new DateTime('last day of Y-m'))->format('d')`
- **LIKE wildcard:** escapovat `%` a `_` + `ESCAPE '\\'` v SQL
- **settings.php:** `preg_replace_callback` + `addcslashes($val, "'\\")` — addslashes nestačí pro PHP string injection
- **daktela.php error_log:** nelogovat URL (obsahuje accessToken)
- **calendar.php durationH:** `round((strtotime($endRaw) - strtotime($startRaw)) / 3600, 1)`
- **cron/daily_context.php** musí spouštět jako apache (čte secrets.php); soubor tasks-context.md musí vlastnit apache

---

## Featury nasazené 2026-05-17

- **DnesResetModal** — nový den: výběr co přenést do Dnes, hotové se odstraní automaticky; `localStorage['lastDnesCheck']`; spouští se před MorningRitual
- **AI kontext z DB (fáze 1)** — ai.php rozšířen o 1on1 záznamy, Daktela cache, accuracy ratio; prompt caching
- **ChatPanel + api/chat.php** — Claude Haiku, stateless history, panel v pravém sidebaru pod CalendarPanel
- **Denní cron** — `cron/daily_context.php` → `tasks-context.md`; crontab `0 6 * * *` jako apache
- **Samoučení** — `estimated_minutes` + `actual_minutes`; `DoneTimeModal` po dokončení; KpiPanel přesnost odhadů; AI accuracy context

---

## Kompletní redesign — 2026-05-22–23

### Design systém

Starý design: navy gradient header. Nový: čistě bílý, neutrální, akcent modrá.

**CSS proměnné:**
- `--bg: #F7F7F8` (stránka), `--surface: #FFFFFF` (karty), `--surface-2: #F0F0F2` (Q4)
- `--text: #18181B`, `--text-2: #71717A`, `--text-3: #A1A1AA`
- `--accent: #2563EB`, `--accent-bg: #EFF6FF`
- `--danger: #DC2626 / --danger-bg: #FFF5F5` (Q1, overdue)
- `--warning: #D97706 / --warning-bg: #FFFBEB` (Q3, stale)
- `--success: #16A34A / --success-bg: #F0FDF4`
- `--purple: #7C3AED / --purple-bg: #F5F3FF` (all-day events, high potential)
- `--shadow-sm`, `--shadow-md`, `--radius: 10px`, `--radius-sm: 6px`

### Layout

Header 52px (bílý, border-bottom, shadow-sm) + NavSidebar 60px vlevo (bílý, border-right) + main + pravý sidebar.

**NavSidebar:** `.nav-item` 44×44px; aktivní = `--accent-bg` + `::before` 3px accent pruh vlevo.

### Kvadranty vizuální hierarchie

| Q | Pozadí | Top border |
|---|---|---|
| Q1 | `--danger-bg` | 3px `--danger` |
| Q2 | `--surface` bílé | 3px `--accent` |
| Q3 | `--warning-bg` | 3px `--warning` |
| Q4 | `--surface-2` šedé | žádný |

### Modal portal pattern — KRITICKÉ

Modaly MUSÍ renderovat přes `ReactDOM.createPortal(..., document.getElementById('modals'))`.
`<div id="modals">` v App return mimo content. Bez toho modaly dědí stacking context a zobrazí se pod jinými prvky.

**`.modal-box`** pro modaly s fixním headerem + scrollovatelným obsahem: `display:flex; flex-direction:column; overflow:hidden; max-height:90vh`.

### 1on1 redesign

- CSS grid `260px auto`, SignalChip (type: ok/warn/info/purple)
- ActionItemsPopover: `left:0` (ne right:0) — jinak přeteče mimo viewport
- `calendar_1on1_mappings` tabulka + `OneOnOneMappingModal` + `calendar?sub=onenon_scan`
- `forceShowMorning` prop + `onForceDone` callback pro správné přeskočení ranního rituálu
