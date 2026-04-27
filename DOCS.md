# Tasks App [OSOBNÍ]

**Server:** hetzner-personal (95.217.15.95)  
**Cesta:** `/var/www/app/tasks/`  
**URL:** https://padour.duckdns.org/tasks/  
**Git:** github.com/jirisach-padour  
**Tech:** PHP 8.5, React 18 přes Babel standalone (inline v index.php), MariaDB `tasks`, bez Composeru

---

## Co dělá

Osobní task manager na Eisenhowerově matici (urgent/important). Integruje Daktela tickety, Google Calendar a Claude AI pro návrh priorit. Záložky Work / Osobní / Vše / Hotovo / 1on1.

---

## Secrets

`/etc/tasks/secrets.php` — mimo webroot, mimo git, přístup root:apache 640.

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
| `index.php` | Celé React UI (~1800+ řádků, Babel standalone inline) |
| `api.php` | Router — match() dispatch na api/ podsložku |
| `config.php` | Auth guard requireAuth(), buildQuery(), konstanta DB |
| `login.php` | Přihlašovací stránka (bez auth guardu) |
| `oauth-callback.php` | Google OAuth callback — výměna code za tokeny (curl, SameSite=Lax) |
| `api/tasks.php` | CRUD tasků + recurrence logika |
| `api/checklist.php` | CRUD checklist items |
| `api/daktela.php` | Proxy na Daktela API v6 + DB cache (daktela_cache tabulka); whitelist: tickets, activities, users, groups |
| `api/calendar.php` | Google Calendar API — token refresh + pull eventů |
| `api/ai.php` | Claude API — návrh kvadrantů s odůvodněním |
| `api/onenon.php` | CRUD 1on1 schůzek + manažerský profil osob |
| `api/settings.php` | Změna username/hesla (přepis secrets.php) |
| `lib/DB.php` | PDO wrapper (kopie z bookshelf) |

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
    recurrence_interval TINYINT,   -- pro 'custom'
    recurrence_unit ENUM('days','weeks','months'),
    sort_order INT,
    done_at TIMESTAMP,
    created_at, updated_at
)

checklist_items (id, title, done, done_at, sort_order, created_at)

calendar_tokens (id, access_token, refresh_token, expires_at, updated_at)

daktela_cache (name PK, title, stage, sla_deadline)  -- DB cache ticketů sachj
daktela_cache_meta (id=1, refreshed_at, ticket_count)

onenon_notes (id, person, meeting_date, notes TEXT, action_items JSON,
    mood TINYINT NULL,   -- 1-5 hodnocení schůzky
    tags JSON NULL,      -- ["vykon","sla","osobni","rozvoj","feedback"]
    created_at)

onenon_people (id, name VARCHAR(100) UNIQUE, description TEXT,
    profile JSON NULL,   -- manažerský profil: {performance,potential,mgmt_effort,strength,development,comm_style,motivation,notes}
    created_at, updated_at)
```

---

## API endpointy

Všechny přes `api.php?action=X`, session auth.

| action | Metoda | Popis |
|---|---|---|
| `tasks` | GET | seznam tasků (+ today_done count) |
| `tasks` | GET `?search=q` | fulltext hledání |
| `tasks` | POST | vytvoření tasku |
| `tasks` | PUT `?id=N` | update tasku; při status=done + recurrence → INSERT nový |
| `tasks` | DELETE `?id=N` | hard delete |
| `checklist` | GET/POST/PUT/DELETE | CRUD checklist items |
| `daktela_login` | POST | výměna user+pass za Daktela accessToken (proxy) |
| `daktela` | POST | proxy na Daktela API v6 (whitelist: tickets, activities, users, groups) |
| `daktela_cache` | GET | tickety z DB cache bez tokenu |
| `daktela_cache` | POST `{accessToken}` | refresh cache z Daktela API |
| `calendar` | GET | `{connected, events}` (auto-refresh tokenu) |
| `calendar?sub=connect` | GET | `{redirect: google_oauth_url}` |
| `calendar?sub=disconnect` | POST | smazání tokenu z DB |
| `ai_suggest` | POST | Claude AI → návrh kvadrantů pro všechny tasky |
| `onenon` | GET | seznam lidí s počty schůzek + description + profile |
| `onenon?person=X` | GET | poznámky + description + profile pro konkrétní osobu |
| `onenon` | POST | vytvoření záznamu schůzky |
| `onenon?id=N` | PUT/DELETE | update/delete záznamu schůzky |
| `onenon?sub=update_person` | PUT | přejmenování osoby + uložení profile JSON |
| `onenon?person=X` | DELETE | smazání osoby + všech jejích zápisů |
| `settings` | POST | změna username/hesla (přepis secrets.php) |
| `logout` | POST | session_destroy() |

---

## Daktela integrace

**Auth flow:**
```
UI (sachj + heslo) → POST daktela_login → proxy na Daktela Login API
→ accessToken uložen do sessionStorage (nikdy DB)
→ "Obnovit" v DaktelaPanel refreshne DB cache přes POST daktela_cache
```

**DB cache:** Tickety sachj (stage=OPEN) jsou uloženy v `daktela_cache`. Zobrazují se bez tokenu. Refresh požaduje token ze sessionStorage. Timestamp v `daktela_cache_meta.refreshed_at`.

**Nested filter formát (jedině fungující):**
```
filter[logic]=and
filter[filters][0][logic]=and
filter[filters][0][filters][0][field]=user
filter[filters][0][filters][0][operator]=in
filter[filters][0][filters][0][value][0]=sachj
filter[filters][0][filters][1][field]=stage
filter[filters][0][filters][1][operator]=in
filter[filters][0][filters][1][value][0]=OPEN
fields[0]=name&fields[1]=title&fields[2]=stage&fields[3]=sla_deadline
```
Flat `filter[N][field]` formát Daktela ignoruje — vždy použít nested.

---

## Google Calendar integrace

**OAuth flow:**
1. UI klikne "Propojit" → GET `calendar?sub=connect` → redirect na Google OAuth
2. Google callback → `oauth-callback.php` (SameSite=Lax pro cross-site redirect)
3. PHP vymění code za access+refresh token přes curl
4. Tokeny v `calendar_tokens` tabulce
5. Při každém GET calendar: auto-refresh pokud token expiruje za <60s

**CalendarPanel:** Eventy seskupené po dnech (dayLabel ze serveru). Každý event má hover tlačítko "+ Task" → otevře TaskModal s prefilled title + due_date.

**Scope:** `calendar.readonly` (read-only, jen osobní kalendář)  
**Google projekt:** Internal audience (daktela.com workspace) — bez externího přístupu

---

## AI integrace

Claude Sonnet 4.6, API klíč v `/etc/tasks/secrets.php`.  
Vstup: všechny otevřené tasky + detail Daktela ticketů.  
Výstup: navrhovaný kvadrant + 1–2 věty zdůvodnění pro každý task.  
Uživatel přijme/odmítne jeden po druhém.

---

## 1on1 záložka

- **Lidé:** sidebar s přehledem osob, počty schůzek, varování >30 dní bez 1on1
- **Nová schůzka:** dropdown načítá L1 skupinu Daktely (`groups_62715929ce76e354293456`) přes dvoustupňový dotaz: groups endpoint → membersName → users. Zobrazuje `user.title`. Fallback: existující osoby z DB.
- **Manažerský profil osoby:** editace přes ✎ tlačítko — výkon (1–5 ★), potenciál (low/medium/high), manažerská náročnost (low/medium/high), silná stránka, oblast rozvoje, styl komunikace, motivace, volné poznámky. Uloženo jako JSON v `onenon_people.profile`.
- **Mood 1–5** hvězdičky per schůzka
- **Tagy**: výkon / SLA / osobní / rozvoj / feedback
- **Dashboard header**: otevřené action items + osoby bez 1on1 >30 dní

---

## Auth

PHP session, jeden uživatel (Jiří). Credentials v secrets.php.  
Session: 30 dní lifetime, HttpOnly, Secure.  
`requireAuth()` v config.php na začátku každého PHP souboru (kromě login.php).  
`oauth-callback.php` výjimka: SameSite=Lax (jinak Google redirect nepošle cookie).

---

## Deploy

```bash
ssh hetzner-personal
cd /var/www/app/tasks
git pull
sudo php -l index.php api.php api/tasks.php  # syntax check
```

---

## Triky a gotchas

- **Babel standalone IIFE trap:** žádné IIFE v JSX — blank page bez chybové hlášky
- **Nested template literals:** zakázané v JSX — ukončí vnější backtick string
- **Python patch pattern** pro server edity (heredoc špatně escapuje `$`, `"`, `\`)
- **buildQuery()** místo `http_build_query()` pro Daktela API (array fields[])
- **SameSite=Strict** nefunguje ani pro login — mobile Safari ztrácí session cookie; login.php i config.php používají Lax
- **autocapitalize na username input:** `autocapitalize="off" autocorrect="off"` — iOS jinak odesílá `Jiri` místo `jiri`
- **preg_replace + bcrypt hash:** `$2y$12$...` obsahuje `$` které preg_replace interpretuje jako backreference → použít `preg_replace_callback`
- **curl** místo `file_get_contents` pro HTTP POST v PHP-FPM prostředí
- **apiFetch secondary param:** použít `sub` místo `action` — spread přepíše primární `action` v URLSearchParams
- **Daktela L1 users:** dvoustupňový dotaz — nejdřív `groups` endpoint pro `membersName`, pak `users` filtrovat client-side; `groups_members` jako filter field na users endpoint nefunguje
- **TaskModal + DaktelaPanel assignedMap:** vypočítat přes `React.useMemo(() => {...}, [tasks])` před return, pak předat jako proměnnou — IIFE v JSX prop je Babel trap (opraveno 2026-04-26)
- **Monthly recurrence day overflow:** `DateTime::setDate(Y, n, 31)` přeteče do příštího měsíce pokud měsíc má méně dní — clampovat přes `(int)(new DateTime('last day of Y-m'))->format('d')`
- **LIKE wildcard v search:** escapovat `%` a `_` v user inputu přes `str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search)` + `ESCAPE '\\'` v SQL
- **settings.php preg_replace pro APP_USER:** použít `preg_replace_callback` + `addcslashes($val, "'\\")` místo `addslashes()` — stejný pattern jako u bcrypt hashe
- **daktela.php error_log:** nelogovat celé URL (obsahuje accessToken jako query param) — logovat jen endpoint název
