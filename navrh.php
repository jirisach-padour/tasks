<?php
// Náhled nového GUI v2 (2026-10-29) — jen ukázková data, za přihlášením. Po schválení nahradí index.php.
require_once __DIR__ . '/config.php';
requireAuth();
header('Cache-Control: no-store');
?><!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Tasks — náhled v2</title>
<style>
:root {
    --bg: #F6F6F7; --surface: #FFFFFF; --surface-2: #F1F1F3; --border: #E4E4E7;
    --text: #18181B; --text-2: #5F5F68; --text-3: #A1A1AA;
    --accent: #2563EB; --accent-bg: #EEF4FF;
    --hot: #DC2626; --hot-bg: #FEF2F2;
    --ok: #15803D; --ok-bg: #EEFBF2; --purple: #7C3AED; --purple-bg: #F4F0FF;
    --radius: 12px; --shadow: 0 1px 2px rgba(0,0,0,.04), 0 1px 8px rgba(0,0,0,.03);
}
@media (prefers-color-scheme: dark) {
    :root {
        --bg: #0E0E11; --surface: #17171B; --surface-2: #1F1F24; --border: #2A2A31;
        --text: #F2F2F4; --text-2: #A3A3AD; --text-3: #6B6B75;
        --accent: #6EA8FF; --accent-bg: #16233D; --hot: #F87171; --hot-bg: #2B1414;
        --ok: #4ADE80; --ok-bg: #11251A; --purple: #B79CFF; --purple-bg: #221A38; --shadow: none;
    }
}
* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font: 14.5px/1.5 -apple-system, "Segoe UI", Roboto, sans-serif; }
a { color: inherit; }
button { font: inherit; cursor: pointer; }

.top { position: sticky; top: 0; z-index: 5; display: flex; align-items: center; gap: 14px; padding: 10px 20px; background: var(--surface); border-bottom: 1px solid var(--border); }
.top h1 { font-size: 16px; margin: 0; }
.top nav { display: flex; gap: 2px; margin-left: 12px; }
.top nav a { text-decoration: none; padding: 6px 12px; border-radius: 8px; color: var(--text-2); }
.top nav a.on { background: var(--accent-bg); color: var(--accent); font-weight: 600; }
.grow { flex: 1; }
.conn { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--text-2); }
.conn i { width: 8px; height: 8px; border-radius: 50%; background: var(--ok); }

.demo { max-width: 1240px; margin: 12px auto 0; padding: 0 20px; display: flex; flex-wrap: wrap; gap: 8px; font-size: 12.5px; color: var(--text-3); align-items: center; }
.demo button { border: 1px dashed var(--border); background: transparent; color: var(--text-2); border-radius: 99px; padding: 3px 10px; font-size: 12.5px; }
.demo button.on { border-style: solid; border-color: var(--accent); color: var(--accent); }

.quick { display: flex; gap: 8px; padding: 12px 20px 0; max-width: 1240px; margin: 0 auto; }
.quick input { flex: 1; font: inherit; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text); }
.quick select { font: inherit; padding: 0 10px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text); }

.wrap { max-width: 1240px; margin: 0 auto; padding: 16px 20px 40px; display: grid; grid-template-columns: minmax(0, 1fr) 330px; gap: 18px; }
@media (max-width: 960px) { .wrap { grid-template-columns: 1fr; } .top nav { display: none; } }
.col { display: grid; gap: 18px; align-content: start; }

.card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); }
.card > header { display: flex; align-items: baseline; gap: 10px; padding: 14px 18px 8px; }
.card > header h2 { font-size: 13px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-2); margin: 0; }
.sub { font-size: 12.5px; color: var(--text-3); }

/* Teď */
.next { padding: 4px 18px 16px; }
.next .lbl { font-size: 12px; color: var(--text-3); text-transform: uppercase; letter-spacing: .05em; }
.next .big { font-size: 20px; font-weight: 650; letter-spacing: -.01em; margin: 2px 0 4px; }
.next .why { color: var(--text-2); }
.next .why b { color: var(--hot); }
.next .cite { margin-top: 8px; font-size: 13px; color: var(--text-2); border-left: 2px solid var(--border); padding-left: 10px; }
.next .acts { margin-top: 12px; display: flex; gap: 8px; flex-wrap: wrap; }
.btn { border: 1px solid var(--border); background: var(--surface); color: var(--text); border-radius: 8px; padding: 5px 12px; font-size: 13px; text-decoration: none; }
.btn.pri { background: var(--accent); border-color: var(--accent); color: #fff; }
.btn.ghost { border-color: transparent; color: var(--text-3); }

.waiting { border-top: 1px solid var(--border); padding: 12px 18px 14px; }
.waiting h3 { margin: 0 0 6px; font-size: 13px; color: var(--hot); display: flex; gap: 8px; }
.waiting h3 span { color: var(--text-3); font-weight: 500; }

.rows { list-style: none; margin: 0; padding: 0; }
.rows li { display: grid; grid-template-columns: 22px minmax(0, 1fr) auto; gap: 2px 10px; padding: 9px 0; border-top: 1px solid var(--border); cursor: pointer; }
.rows li:first-child { border-top: 0; }
.rows .r { color: var(--text-3); font-weight: 700; font-variant-numeric: tabular-nums; }
.rows .t { font-weight: 600; }
.rows .w { grid-column: 2; font-size: 13px; color: var(--text-2); }
.rows .c { grid-column: 2; font-size: 12.5px; color: var(--text-2); border-left: 2px solid var(--border); padding-left: 10px; margin-top: 4px; display: none; }
.rows li.open .c { display: block; }
.rows .a { grid-row: 1 / span 2; grid-column: 3; display: flex; gap: 6px; align-items: start; }
.rows li.hot .r, .rows li.hot .w b { color: var(--hot); }
.tag { font-size: 11px; font-weight: 600; padding: 1px 7px; border-radius: 99px; background: var(--surface-2); color: var(--text-2); margin-left: 6px; vertical-align: 1px; }
.tag.new { background: var(--accent-bg); color: var(--accent); }
.tag.up { background: var(--ok-bg); color: var(--ok); }

details.more { border-top: 1px solid var(--border); }
details.more summary { list-style: none; cursor: pointer; padding: 10px 18px; font-size: 13px; color: var(--text-2); }
details.more summary::-webkit-details-marker { display: none; }
details.more[open] summary { color: var(--text); }
details.more .rows { padding: 0 18px 10px; }

.slack { display: flex; flex-wrap: wrap; gap: 8px; padding: 0 18px 14px; }
.slack a { text-decoration: none; display: inline-flex; gap: 8px; align-items: center; padding: 5px 10px; border-radius: 99px; background: var(--purple-bg); color: var(--purple); font-size: 13px; }
.slack a b { background: var(--purple); color: var(--surface); border-radius: 99px; padding: 0 7px; font-size: 11.5px; }

.strat { padding: 4px 18px 16px; }
.strat .t { font-weight: 600; }
.strat .m { font-size: 13px; color: var(--text-2); }

.task.done { opacity: .45; text-decoration: line-through; }
.next.done .big { text-decoration: line-through; opacity: .5; }
.task { display: flex; gap: 10px; align-items: start; padding: 7px 0; border-top: 1px solid var(--border); }
.task:first-child { border-top: 0; }
.task input { margin-top: 3px; accent-color: var(--accent); }
.task .t { flex: 1; }
.task .m { font-size: 12px; color: var(--text-3); }
.list { padding: 0 18px 12px; }

.ev { display: grid; grid-template-columns: 48px 1fr; gap: 10px; padding: 7px 0; border-top: 1px dashed var(--border); }
.ev:first-child { border-top: 0; }
.ev .h { font-variant-numeric: tabular-nums; color: var(--text-2); font-size: 13px; }
.ev .n { font-weight: 600; }
.ev .p { font-size: 12px; color: var(--accent); font-weight: 600; }
.ev .q { font-size: 12px; color: var(--text-3); }
.ev.focus { background: var(--accent-bg); margin: 0 -18px; padding: 7px 18px; }

.feedback { padding: 2px 18px 14px; font-size: 13px; }
.feedback .k { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-3); }
.feedback ul { margin: 4px 0 0; padding-left: 18px; }

.rows li.done { opacity: .45; }
.rows li.done .t { text-decoration: line-through; }
.toast { position: fixed; left: 50%; bottom: 24px; transform: translateX(-50%); background: var(--text); color: var(--surface); border-radius: 10px; padding: 10px 14px; font-size: 13.5px; display: none; gap: 12px; align-items: center; box-shadow: 0 6px 20px rgba(0,0,0,.2); }
.toast.on { display: flex; }
.toast button { background: none; border: 0; color: var(--accent); font-weight: 600; }
.slpop { padding: 0 18px 14px; display: none; }
.slpop.on { display: block; }
body.fokus .hide-in-focus { display: none; }
.fokus-banner { display: none; max-width: 1240px; margin: 12px auto 0; padding: 0 20px; }
.fokus-banner div { background: var(--accent-bg); color: var(--accent); border-radius: 10px; padding: 10px 14px; font-size: 13.5px; }
body.fokus .fokus-banner { display: block; }
.note { max-width: 1240px; margin: 0 auto; padding: 0 20px 30px; color: var(--text-3); font-size: 12px; }
</style>
</head>
<body>
<header class="top">
  <h1>Tasks</h1>
  <nav><a class="on" href="#">Teď</a><a href="#">Úkoly</a><a href="#">1on1</a><a href="#">Týden</a></nav>
  <span class="grow"></span>
  <span class="conn"><i></i>Práce připojena · 14:35</span>
</header>

<div class="demo">Ukázka: <button id="d-zak" class="on">5 zákazníků čeká</button><button id="d-zak1">1 zákazník čeká</button><button id="d-fokus">Focus time (po dopoledne)</button></div>
<div class="fokus-banner"><div>Focus time do 12:00 — vidíš jen strategický blok a to, co hoří v řádu hodin. Ostatní počká.</div></div>

<div class="quick hide-in-focus">
  <input placeholder="Rychlý úkol… (Enter uloží, ⌘K odkudkoli)">
  <select><option>Práce</option><option>Osobní</option></select>
</div>

<div class="wrap">
  <main class="col">
    <section class="card" id="ted">
      <header><h2>Teď</h2><span class="grow"></span><span class="sub">pořadí se mění jen při události</span></header>
      <div class="next">
        <div class="lbl">Další krok</div>
        <div class="big">Výpadek SMS — vyjádření pro zákazníka</div>
        <div class="why"><b>Zákazník čeká 26 h</b> na omluvu po incidentu · SLA za 3 h · Tier 4</div>
        <div class="cite">„Potřebujeme oficiální vyjádření k včerejšímu výpadku.“ — e-mail zákazníka, ticket #922235</div>
        <div class="acts"><a class="btn pri" href="#">Otevřít ticket</a><button class="btn done-big">Hotovo</button><button class="btn">Odložit</button><button class="btn ghost">Špatně zařazeno…</button></div>
      </div>

      <div class="waiting" id="cekaji">
        <h3>Čekají na tebe <span id="cekaji-n">další 4 zákazníci</span></h3>
        <ul class="rows">
          <li class="hot"><span class="r">2</span><span class="t">Nacenění SMS notifikace — FITHAM<span class="tag">Tier 1B</span></span><span class="w"><b>Dlužíme:</b> kolega slíbil návrh 26. 8., reopen parentu dnes 10:00</span><span class="c">„Projednám detaily interně a ozvu se Vám s návrhem.“ — Robert, parent #922615</span><span class="a"><a class="btn" href="#">Otevřít</a><button class="btn done">Hotovo</button></span></li>
          <li class="hot"><span class="r">3</span><span class="t">WhatsApp hromadné zprávy — iNET<span class="tag">Tier 4</span><span class="tag new">nové</span></span><span class="w"><b>Dlužíme:</b> slíbeno určení pracnosti a ceny, zákazník urgoval</span><span class="c">„Předal jsem žádost manažerovi support týmu, který určí pracnost a cenu.“ — Martin</span><span class="a"><a class="btn" href="#">Otevřít</a><button class="btn done">Hotovo</button></span></li>
          <li class="hot x-more"><span class="r">4</span><span class="t">Status produktové featury — ROBOT WORLD<span class="tag">Tier 1B</span></span><span class="w"><b>Zákazník čeká</b> 29 dní na realistický termín</span><span class="c">„Budu rád, když mi dáte vědět, až to bude hotové.“ — zákazník</span><span class="a"><a class="btn" href="#">Otevřít</a><button class="btn done">Hotovo</button></span></li>
          <li class="hot x-more"><span class="r">5</span><span class="t">Upgrade zdarma — instrukce GEMINI<span class="tag">Tier 2</span><span class="tag up">↑ posunuto</span></span><span class="w"><b>Dlužíme:</b> kolega slíbil instrukce, reopen parentu zítra 8:00</span><span class="c">„Instrukce pošlu. Upgrade vám uděláme zdarma.“ — Tomáš, parent #924731</span><span class="a"><a class="btn" href="#">Otevřít</a><button class="btn done">Hotovo</button></span></li>
        </ul>
      </div>

      <details class="more hide-in-focus">
        <summary>Celý TOP 10 — dalších 5 ▾</summary>
        <ul class="rows">
          <li><span class="r">6</span><span class="t">Billing za říjen — SR bez fakturace</span><span class="w">Karel v #support_management, do konce měsíce · task existuje</span><span class="c">„Projděte SR s project time ≥ 1 h bez Billed=Yes.“ — Karel</span><span class="a"><button class="btn done">Hotovo</button></span></li>
          <li><span class="r">7</span><span class="t">EWS červená — Outdoor Concept<span class="tag">Tier 2</span></span><span class="w">Skóre klesá 7 dní, incident Voice Bot čeká 2 dny na update</span><span class="c">Poslední komentář 27. 10. — Jitka: „čekáme na dev“</span><span class="a"><a class="btn" href="#">Otevřít</a><button class="btn done">Hotovo</button></span></li>
          <li><span class="r">8</span><span class="t">Change request — routing dle lokality<span class="tag">Tier 3</span></span><span class="w">Zákazník chce posouzení, nic slíbeno</span><span class="c">„Jde to automaticky podle lokality uživatele?“</span><span class="a"><a class="btn" href="#">Otevřít</a><button class="btn done">Hotovo</button></span></li>
          <li><span class="r">9</span><span class="t">Valentín — check-in</span><span class="w">Úkol z 1on1 29. 10. · termín 4. 11.</span><span class="c">Ulevit jinde než support weekem</span><span class="a"><button class="btn done">Hotovo</button></span></li>
          <li><span class="r">10</span><span class="t">RFC v 5. Review — kontrola nasazení</span><span class="w">Nízko: nasazeno, zákazník nečeká</span><span class="c">„Hotovo, prosím o kontrolu.“</span><span class="a"><a class="btn" href="#">Otevřít</a><button class="btn done">Hotovo</button></span></li>
        </ul>
      </details>
      <div class="slack hide-in-focus">
        <a href="#" class="sl">Bez tvé reakce: Karel <b>2</b></a><a href="#" class="sl">#support-tls <b>1</b></a><a href="#" class="sl">DM kolegů <b>3</b></a>
      </div>
      <div class="slpop" id="slpop"><ul class="rows">
        <li><span class="r">·</span><span class="t">Karel — #support_management 11:02</span><span class="w">„Do pátku potřebuju čísla k fakturaci za říjen.“</span><span class="a"><a class="btn" href="#">Slack</a><button class="btn done">Vyřízeno</button></span></li>
        <li><span class="r">·</span><span class="t">Karel — DM 13:40</span><span class="w">„Máš chvilku zítra ráno?“</span><span class="a"><a class="btn" href="#">Slack</a><button class="btn done">Vyřízeno</button></span></li>
      </ul></div>
    </section>

    <section class="card">
      <header><h2>Strategický blok</h2><span class="grow"></span><span class="sub">vybráno v pátek · po 9:00–12:00</span></header>
      <div class="strat">
        <div class="t">AI workshop pro nováčky — osnova</div>
        <div class="m">Leží 40 dní. Jediná věc, kterou tento týden posouváš dlouhodobě.</div>
        <div class="acts" style="margin-top:10px;display:flex;gap:8px"><button class="btn pri">Začít</button><button class="btn">Přesunout na pátek</button></div>
      </div>
    </section>

    <section class="card hide-in-focus">
      <header><h2>Čekám na</h2><span class="grow"></span><span class="sub">vrátí se samo v den připomenutí</span></header>
      <div class="list">
        <label class="task"><input type="checkbox"><span class="t">Ilia — fix filtrů CRM Kulina<div class="m">od 6. 10. · připomenout dnes</div></span></label>
        <label class="task"><input type="checkbox"><span class="t">Karel — sériové číslo monitoru<div class="m">od 20. 10. · připomenout 3. 11.</div></span></label>
      </div>
    </section>

    <section class="card hide-in-focus">
      <header><h2>Moje úkoly</h2><span class="grow"></span><span class="sub">Práce 16 · Osobní 2</span></header>
      <div class="list">
        <label class="task"><input type="checkbox"><span class="t">Napsat Vaškovi a René: která infra témata je zajímají<div class="m">po termínu 21. 10.</div></span></label>
        <label class="task"><input type="checkbox"><span class="t">CSAT kontrola odeslání<div class="m">denně</div></span></label>
        <label class="task"><input type="checkbox"><span class="t">Servis auta<div class="m">osobní · pátek</div></span></label>
      </div>
    </section>
  </main>

  <aside class="col">
    <section class="card">
      <header><h2>Dnes</h2><span class="grow"></span><span class="sub">3 h 30 min volna</span></header>
      <div class="list">
        <div class="ev focus"><span class="h">09:00</span><span><span class="n">Focus time</span><div class="p">strategický blok</div></span></div>
        <div class="ev"><span class="h">13:00</span><span><span class="n">1:1 — Lucie</span><div class="p">potvrzeno · příprava níže</div></span></div>
        <div class="ev"><span class="h">14:00</span><span><span class="n">online pohovor — kandidát</span><div class="p">potvrzeno · CV z Recruitis</div></span></div>
        <div class="ev"><span class="h">15:00</span><span><span class="n">Provozní meeting</span><div class="q">nepotvrzeno</div></span></div>
      </div>
    </section>
    <section class="card hide-in-focus">
      <header><h2>Příprava 1:1 — Lucie</h2></header>
      <div class="feedback">
        <div class="k">Feedback, který dlužíš</div>
        <ul><li>Konkrétně ocenit kontrolu instalací za září</li><li>Říct na rovinu: buddy pro Václava stojí od června</li></ul>
        <div class="k" style="margin-top:10px">Otevřené z minula</div>
        <ul><li>Václav — buddy na zvedačku</li></ul>
      </div>
    </section>
  </aside>
</div>
<div class="toast" id="toast"><span id="toast-t"></span><button id="toast-z">Zpět</button></div>
<p class="note">Náhled v2 s ukázkovými daty. Čeká-li víc zákazníků, ukážou se všichni; jinak „Teď“ = jedna věc + dvě další. Klik na řádek ukáže citaci.</p>
<script>
document.querySelectorAll('.rows li').forEach(li => li.addEventListener('click', e => {
    if (e.target.closest('a,button')) return;
    li.classList.toggle('open');
}));
function navrhZakaznici(vic) {
    document.querySelectorAll('#cekaji .x-more').forEach(li => li.style.display = vic ? '' : 'none');
    document.getElementById('cekaji-n').textContent = vic ? 'další 4 zákazníci' : 'v pořadí';
    document.querySelector('#cekaji h3').firstChild.textContent = vic ? 'Čekají na tebe ' : 'Další ';
    document.getElementById('d-zak').classList.toggle('on', vic);
    document.getElementById('d-zak1').classList.toggle('on', !vic);
}
document.getElementById('d-zak').onclick = () => navrhZakaznici(true);
document.getElementById('d-zak1').onclick = () => navrhZakaznici(false);
document.getElementById('d-fokus').onclick = e => { document.body.classList.toggle('fokus'); e.target.classList.toggle('on'); };
let navrhPosledni = null, navrhTimer = null;
function navrhHotovo(el, text) {
    el.classList.add('done');
    navrhPosledni = el;
    document.getElementById('toast-t').textContent = text;
    document.getElementById('toast').classList.add('on');
    clearTimeout(navrhTimer);
    navrhTimer = setTimeout(() => { document.getElementById('toast').classList.remove('on'); if (navrhPosledni) navrhPosledni.style.display = 'none'; navrhPosledni = null; }, 5000);
}
document.getElementById('toast-z').onclick = () => { if (navrhPosledni) navrhPosledni.classList.remove('done'); navrhPosledni = null; document.getElementById('toast').classList.remove('on'); };
document.querySelectorAll('.btn.done').forEach(b => b.addEventListener('click', e => {
    e.stopPropagation();
    navrhHotovo(b.closest('li'), 'Hotovo — vrátí se jen při nové události.');
}));
document.querySelector('.btn.done-big').onclick = () => navrhHotovo(document.querySelector('.next'), 'Hotovo — na řadě je další položka.');
document.querySelectorAll('.task input').forEach(i => i.addEventListener('change', () => { if (i.checked) navrhHotovo(i.closest('.task'), 'Úkol hotový.'); }));
document.querySelectorAll('.slack .sl').forEach(a => a.addEventListener('click', e => { e.preventDefault(); document.getElementById('slpop').classList.toggle('on'); }));
</script>
</body>
</html>
