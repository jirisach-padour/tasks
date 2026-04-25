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
  index.php          (~1515 ř) — celé React UI (Babel standalone inline)
  api.php            (~30 ř)   — router match($action) → api/ podsložka
  config.php         (~45 ř)   — requireAuth(), buildQuery(), DB konstanty
  login.php                    — přihlašovací stránka (bez auth guardu)
  oauth-callback.php (~55 ř)   — Google OAuth callback (curl, SameSite=Lax)
  TODO.md                      — otevřené úkoly a bugy
  DOKUMENTACE.md               — tento soubor
  api/
    tasks.php    (151 ř) — CRUD tasků + recurrence logika
    checklist.php        — CRUD checklist items
    daktela.php  (151 ř) — proxy Daktela API v6 + DB cache (daktela_cache)
    calendar.php  (83 ř) — Google Calendar token refresh + pull eventů
    ai.php        (96 ř) — Claude Sonnet 4.6 → návrh kvadrantů
    onenon.php    (58 ř) — CRUD 1on1 schůzek (osoby, poznámky, action items)
  lib/
    DB.php               — PDO wrapper: DB::q(), DB::insert(), DB::update()

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
  recurrence_interval TINYINT,    -- pro 'custom' — počet jednotek
  recurrence_unit ENUM('days','weeks','months'),
  sort_order INT DEFAULT 0,
  done_at TIMESTAMP NULL,
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
  created_at TIMESTAMP
```

---

## API endpointy (`api.php?action=X`)

| action | Metoda | Popis |
|--------|--------|-------|
| `tasks` | GET | seznam tasků (+ today_done count) |
| `tasks` | GET `?search=q` | fulltext LIKE v title+description+ai_context |
| `tasks` | POST | vytvoření tasku |
| `tasks` | PUT `?id=N` | update; při status=done + recurrence → INSERT nový |
| `tasks` | DELETE `?id=N` | hard delete |
| `checklist` | GET/POST/PUT/DELETE | CRUD checklist items |
| `daktela_login` | POST | proxy Daktela login → accessToken |
| `daktela` | POST | proxy Daktela API v6 (whitelist endpointů) |
| `daktela_cache` | GET | tickety z DB cache (bez tokenu) |
| `daktela_cache` | POST `{accessToken}` | refresh cache z Daktela API |
| `calendar` | GET | `{connected, events[{date,time,title,allDay}]}` |
| `calendar?sub=connect` | GET | `{redirect: google_oauth_url}` |
| `calendar?sub=disconnect` | POST | smaže token z DB |
| `ai_suggest` | POST | Claude Sonnet 4.6 → `[{id, quadrant, reason}]` |
| `onenon` | GET | seznam osob s počty schůzek |
| `onenon?person=X` | GET | schůzky pro konkrétní osobu |
| `onenon` | POST | vytvoření záznamu |
| `onenon?id=N` | PUT/DELETE | update/delete záznamu |
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
| `TabBar` | Work / Osobní / Vše / Hotovo / 1on1 |
| `EisenhowerMatrix` | 2×2 grid kvadrantů |
| `Quadrant` | Jeden kvadrant s drag&drop a inline přidáváním |
| `TaskCard` | Karta tasku: checkbox, title, due_date, badges, ⚡ brzy |
| `TaskModal` | Detail/edit tasku: název, popis, AI context, kvadrant, typ, termín, Daktela tickety, opakování |
| `DaktelaPanel` | Sidebar: tickety z DB cache, přiřazené za collapse, Obnovit + timestamp |
| `DaktelaAuthModal` | Přihlášení do Daktely (user+pass → accessToken) |
| `CalendarPanel` | Sidebar: dnešní/zítřejší Google Calendar události |
| `ChecklistPanel` | Sidebar: rychlé zaškrtávací položky |
| `KpiPanel` | Sidebar: dnešní výkon (dokončené tasky + checklist) |
| `AiSuggestModal` | Výsledky AI návrhu kvadrantů — přijmout/odmítnout |
| `SearchResults` | Fulltext výsledky (tasky z DB + checklist + Daktela) |
| `OneOnOneView` | 1on1 záložka: seznam osob + timeline schůzek |
| `OneOnOneModal` | Detail/edit schůzky + action items checklist |

---

## Daktela integrace

**Auth:** user+pass → Daktela Login API → accessToken v sessionStorage (nikdy perzistentní).

**DB cache:** Tickety sachj (OPEN) v `daktela_cache` tabulce. Načítají se bez tokenu. Refresh vyžaduje token — buď z sessionStorage nebo po novém přihlášení.

**Nested filter formát** (jedině fungující — flat `filter[N][field]` Daktela ignoruje):
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
- `oauth-callback.php` používá `SameSite=Lax` — Google redirect je cross-site, Strict zablokuje session cookie → HTTP 500
- `oauth-callback.php` musí `require lib/DB.php` (jinak Fatal: Class DB not found)
- Scope: `calendar.readonly`, Internal audience (daktela.com workspace)

---

## Opakující se tasky (recurrence)

- `recurrence`: 'none' | 'weekly' | 'monthly' | 'custom'
- 'custom' + `recurrence_interval` + `recurrence_unit` = každých N dní/týdnů/měsíců
- Při `status=done`: api/tasks.php INSERTuje nový task s posunutým `due_date`
- `due_date` = start datum opakování

---

## Auth

PHP session, jeden uživatel (Jiří Šach). Credentials v secrets.php (bcrypt hash).  
Session: 8h lifetime, HttpOnly, Secure, SameSite=Strict.  
`requireAuth()` v config.php — volat na začátku každého PHP souboru (kromě login.php a oauth-callback.php).

---

## Deploy

```bash
# Editace (velké bloky):
scp /tmp/patch.py hetzner-personal:/tmp/
ssh hetzner-personal "python3 /tmp/patch.py && php -l /var/www/app/tasks/index.php"

# Git:
ssh hetzner-personal "cd /var/www/app/tasks && git add -A && git commit -m '...' && git push"

# PHP error log:
ssh hetzner-personal "sudo tail -30 /var/log/php-fpm/www-error.log"

# DB přístup:
ssh hetzner-personal "sudo mariadb tasks"
```

## Nginx routing
```nginx
location /tasks/api/ { try_files $uri $uri/ /tasks/api.php?$args; }
location /tasks/     { try_files $uri $uri/ /tasks/index.php?$args; }
location ~ \.php$    { fastcgi_pass unix:/run/php-fpm/www.sock; ... }
```

## Babel standalone omezení
- **IIFE v JSX je fatální** (blank page bez erroru) — logiku vždy do proměnných před `return`
- **Nested template literals jsou fatální** — použít string concatenation uvnitř backtick stringu
