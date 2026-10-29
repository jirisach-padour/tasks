<?php
// Náhled nového GUI (2026-10-29) — jen ukázková data, za přihlášením. Po schválení nahradí index.php.
require_once __DIR__ . '/config.php';
requireAuth();
header('Cache-Control: no-store');
?><!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Tasks — náhled</title>
<style>
:root {
    --bg: #F6F6F7; --surface: #FFFFFF; --surface-2: #F1F1F3; --border: #E4E4E7;
    --text: #18181B; --text-2: #63636B; --text-3: #A1A1AA;
    --accent: #2563EB; --accent-bg: #EEF4FF;
    --hot: #DC2626; --hot-bg: #FEF2F2; --warn: #B45309; --warn-bg: #FFF7E6;
    --ok: #15803D; --ok-bg: #EEFBF2; --purple: #7C3AED; --purple-bg: #F4F0FF;
    --radius: 12px; --shadow: 0 1px 2px rgba(0,0,0,.04), 0 1px 8px rgba(0,0,0,.03);
}
@media (prefers-color-scheme: dark) {
    :root {
        --bg: #0E0E11; --surface: #17171B; --surface-2: #1F1F24; --border: #2A2A31;
        --text: #F2F2F4; --text-2: #A3A3AD; --text-3: #6B6B75;
        --accent: #6EA8FF; --accent-bg: #16233D;
        --hot: #F87171; --hot-bg: #2B1414; --warn: #F5B754; --warn-bg: #2A2112;
        --ok: #4ADE80; --ok-bg: #11251A; --purple: #B79CFF; --purple-bg: #221A38;
        --shadow: none;
    }
}
* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font: 14.5px/1.5 -apple-system, "Segoe UI", Roboto, sans-serif; }
a { color: inherit; }
button { font: inherit; cursor: pointer; }

.top { position: sticky; top: 0; z-index: 5; display: flex; align-items: center; gap: 14px; padding: 10px 20px;
       background: var(--surface); border-bottom: 1px solid var(--border); }
.top h1 { font-size: 16px; margin: 0; letter-spacing: -.01em; }
.top nav { display: flex; gap: 2px; margin-left: 12px; }
.top nav a { text-decoration: none; padding: 6px 12px; border-radius: 8px; color: var(--text-2); }
.top nav a.on { background: var(--accent-bg); color: var(--accent); font-weight: 600; }
.top .grow { flex: 1; }
.conn { display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--text-2); }
.conn i { width: 8px; height: 8px; border-radius: 50%; background: var(--ok); display: inline-block; }
.quick { display: flex; gap: 8px; padding: 14px 20px 0; max-width: 1240px; margin: 0 auto; }
.quick input { flex: 1; font: inherit; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text); }
.quick select { font: inherit; padding: 0 10px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--text); }

.wrap { max-width: 1240px; margin: 0 auto; padding: 16px 20px 40px; display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 18px; }
@media (max-width: 960px) { .wrap { grid-template-columns: 1fr; } .top nav { display: none; } }

.card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); }
.card > header { display: flex; align-items: baseline; gap: 10px; padding: 14px 18px 10px; }
.card > header h2 { font-size: 13px; text-transform: uppercase; letter-spacing: .05em; color: var(--text-2); margin: 0; }
.card > header .sub { font-size: 12.5px; color: var(--text-3); }
.card > header .grow { flex: 1; }

/* TOP 10 */
.top10 { list-style: none; margin: 0; padding: 0 8px 8px; counter-reset: r; }
.top10 li { display: grid; grid-template-columns: 28px minmax(0, 1fr) auto; gap: 4px 12px; padding: 12px 10px; border-radius: 10px; }
.top10 li + li { border-top: 1px solid var(--border); border-radius: 0; }
.top10 li:hover { background: var(--surface-2); }
.rank { counter-increment: r; font-weight: 700; color: var(--text-3); font-variant-numeric: tabular-nums; padding-top: 1px; }
.rank::before { content: counter(r); }
.top10 li.hot .rank { color: var(--hot); }
.title { font-weight: 600; }
.why { grid-column: 2; color: var(--text-2); font-size: 13px; }
.why b { color: var(--text); font-weight: 600; }
.cite { grid-column: 2; font-size: 12.5px; color: var(--text-2); border-left: 2px solid var(--border); padding: 2px 0 2px 10px; margin-top: 4px; display: none; }
.top10 li.open .cite { display: block; }
.act { grid-row: 1 / span 2; grid-column: 3; display: flex; gap: 6px; align-items: start; }
.btn { border: 1px solid var(--border); background: var(--surface); color: var(--text); border-radius: 8px; padding: 4px 10px; font-size: 12.5px; }
.btn.pri { background: var(--accent); border-color: var(--accent); color: #fff; }
.chips { display: inline-flex; gap: 6px; margin-left: 8px; vertical-align: 1px; }
.chip { font-size: 11px; font-weight: 600; padding: 1px 7px; border-radius: 99px; background: var(--surface-2); color: var(--text-2); }
.chip.src-d { background: var(--accent-bg); color: var(--accent); }
.chip.src-s { background: var(--purple-bg); color: var(--purple); }
.chip.src-t { background: var(--ok-bg); color: var(--ok); }
.chip.src-c { background: var(--warn-bg); color: var(--warn); }
.chip.tier { background: var(--hot-bg); color: var(--hot); }

/* Slack bez reakce */
.slack { display: flex; flex-wrap: wrap; gap: 8px; padding: 0 18px 14px; }
.slack a { text-decoration: none; display: inline-flex; gap: 8px; align-items: center; padding: 6px 10px; border-radius: 99px; background: var(--purple-bg); color: var(--purple); font-size: 13px; }
.slack a b { background: var(--purple); color: var(--surface); border-radius: 99px; padding: 0 7px; font-size: 11.5px; }

/* Úkoly */
.lanes { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0; border-top: 1px solid var(--border); }
@media (max-width: 640px) { .lanes { grid-template-columns: 1fr; } }
.lane { padding: 12px 18px 14px; }
.lane:nth-child(odd) { border-right: 1px solid var(--border); }
.lane:nth-child(n+3) { border-top: 1px solid var(--border); }
.lane h3 { font-size: 12.5px; margin: 0 0 6px; color: var(--text-2); display: flex; gap: 8px; align-items: center; }
.lane h3 .n { color: var(--text-3); font-weight: 500; }
.task { display: flex; gap: 10px; align-items: start; padding: 6px 0; }
.task input { margin-top: 3px; accent-color: var(--accent); }
.task .t { flex: 1; }
.task .m { font-size: 12px; color: var(--text-3); }
.task .m.late { color: var(--hot); }
.task .m.who { color: var(--purple); }

/* Pravý sloupec */
.side { display: grid; gap: 18px; align-content: start; }
.day { padding: 0 18px 14px; }
.ev { display: grid; grid-template-columns: 48px 1fr; gap: 10px; padding: 7px 0; border-top: 1px dashed var(--border); }
.ev:first-child { border-top: 0; }
.ev .h { font-variant-numeric: tabular-nums; color: var(--text-2); font-size: 13px; }
.ev .n { font-weight: 600; }
.ev .p { font-size: 12px; color: var(--warn); font-weight: 600; }
.ev.now { background: linear-gradient(90deg, var(--accent-bg), transparent); margin: 0 -18px; padding: 7px 18px; }
.free { font-size: 12.5px; color: var(--text-2); padding: 0 18px 14px; }
.people { list-style: none; margin: 0; padding: 0 18px 14px; }
.people li { display: flex; justify-content: space-between; gap: 8px; padding: 6px 0; border-top: 1px solid var(--border); font-size: 13.5px; }
.people li:first-child { border-top: 0; }
.people .badge { font-size: 11.5px; padding: 1px 8px; border-radius: 99px; background: var(--hot-bg); color: var(--hot); }
.people .m { color: var(--text-3); font-size: 12px; }
.note { max-width: 1240px; margin: 0 auto; padding: 0 20px; color: var(--text-3); font-size: 12px; }
</style>
</head>
<body>
<header class="top">
  <h1>Tasks</h1>
  <nav>
    <a class="on" href="#">Dnes</a><a href="#">Úkoly</a><a href="#">1on1</a><a href="#">Historie</a>
  </nav>
  <span class="grow"></span>
  <span class="conn" title="Pracovní data z operations (VPN)"><i></i>Práce připojena · před 2 min</span>
</header>

<div class="quick">
  <input placeholder="Rychlý úkol… (Enter uloží, ⌘K odkudkoli)">
  <select><option>Práce</option><option>Osobní</option></select>
</div>

<div class="wrap">
  <main style="display:grid;gap:18px;align-content:start">
    <section class="card">
      <header><h2>TOP 10</h2><span class="sub">čtvrtek 29. 10. · přepočteno 14:35</span><span class="grow"></span><span class="sub">klik = důkaz</span></header>
      <ol class="top10">
        <li class="hot open">
          <span class="rank"></span>
          <span class="title">Nacenění SMS notifikace — FITHAM<span class="chips"><span class="chip src-d">Daktela</span><span class="chip tier">Tier 1B</span></span></span>
          <span class="why"><b>Dlužíme zákazníkovi:</b> kolega slíbil návrh v parentu 26. 8., reopen parentu dnes 10:00</span>
          <span class="cite">„Projednám detaily interně a ozvu se Vám s návrhem na realizaci.“ — Robert, parent #922615</span>
          <span class="act"><a class="btn pri" href="#">Otevřít</a></span>
        </li>
        <li class="hot">
          <span class="rank"></span>
          <span class="title">Výpadek SMS — vyjádření pro zákazníka<span class="chips"><span class="chip src-d">Daktela</span><span class="chip">Tier 4</span></span></span>
          <span class="why"><b>Zákazník čeká 26 h</b> na omluvu po incidentu · SLA za 3 h</span>
          <span class="cite">„Potřebujeme oficiální vyjádření k včerejšímu výpadku.“ — e-mail zákazníka</span>
          <span class="act"><a class="btn pri" href="#">Otevřít</a></span>
        </li>
        <li>
          <span class="rank"></span>
          <span class="title">Billing za říjen — projít SR bez fakturace<span class="chips"><span class="chip src-s">Slack</span><span class="chip src-t">Tasks</span></span></span>
          <span class="why"><b>Karel</b> v #support_management, termín do konce měsíce · task existuje</span>
          <span class="cite">„Prosím projděte SR s project time ≥ 1 h bez Billed=Yes.“ — Karel Vůjtek</span>
          <span class="act"><a class="btn" href="#">Hotovo</a></span>
        </li>
        <li>
          <span class="rank"></span>
          <span class="title">EWS — Outdoor Concept červená ↓10<span class="chips"><span class="chip src-c">EWS</span><span class="chip">Tier 2</span></span></span>
          <span class="why"><b>Krize:</b> EWS červená, skóre klesá 7 dní, incident Voice Bot čeká 2 dny na update</span>
          <span class="cite">Poslední komentář 27. 10. — Jitka: „čekáme na dev“</span>
          <span class="act"><a class="btn pri" href="#">Otevřít</a></span>
        </li>
        <li>
          <span class="rank"></span>
          <span class="title">Change request — routing dle lokality<span class="chips"><span class="chip src-d">Daktela</span><span class="chip">Tier 3</span></span></span>
          <span class="why"><b>Zákazník chce:</b> posouzení changu, kolega nic neslíbil</span>
          <span class="cite">„Jde to udělat automaticky podle lokality uživatele?“ — zákazník</span>
          <span class="act"><a class="btn pri" href="#">Otevřít</a></span>
        </li>
        <li>
          <span class="rank"></span>
          <span class="title">Valentín — check-in<span class="chips"><span class="chip src-t">1on1</span></span></span>
          <span class="why"><b>Úkol z 1on1</b> 29. 10. · termín 4. 11.</span>
          <span class="cite">Ulevit jinde než support weekem: zvedačky, backlog, příliv ticketů</span>
          <span class="act"><a class="btn" href="#">Hotovo</a></span>
        </li>
        <li>
          <span class="rank"></span>
          <span class="title">RFC v 5. Review — kontrola nasazení<span class="chips"><span class="chip src-d">Daktela</span></span></span>
          <span class="why"><b>Nízko:</b> nasazeno, zákazník nečeká, jen formální schválení</span>
          <span class="cite">„Hotovo, prosím o kontrolu.“ — realizátor</span>
          <span class="act"><a class="btn pri" href="#">Otevřít</a></span>
        </li>
      </ol>
      <div class="slack">
        <a href="#">Bez tvé reakce: Karel <b>2</b></a>
        <a href="#">#support-tls <b>1</b></a>
        <a href="#">DM kolegů <b>3</b></a>
      </div>
    </section>

    <section class="card">
      <header><h2>Úkoly</h2><span class="grow"></span><span class="sub">Práce 18 · Osobní 2</span></header>
      <div class="lanes">
        <div class="lane">
          <h3>Hoří <span class="n">2</span></h3>
          <label class="task"><input type="checkbox"><span class="t">Napsat Vaškovi a René: která infra témata je konkrétně zajímají<div class="m late">po termínu 21. 10.</div></span></label>
          <label class="task"><input type="checkbox"><span class="t">Projít CVs v Recruitis<div class="m">práce · 8 týdnů staré</div></span></label>
        </div>
        <div class="lane">
          <h3>Tento týden <span class="n">3</span></h3>
          <label class="task"><input type="checkbox"><span class="t">CSAT kontrola odeslání — denní rutina<div class="m">práce · opakuje se</div></span></label>
          <label class="task"><input type="checkbox"><span class="t">Bowling — rozlučka, návrh do Slacku<div class="m">práce</div></span></label>
          <label class="task"><input type="checkbox"><span class="t">Servis auta<div class="m">osobní · pá</div></span></label>
        </div>
        <div class="lane">
          <h3>Čekám na <span class="n">2</span></h3>
          <label class="task"><input type="checkbox"><span class="t">Ilia — fix filtrů CRM Kulina<div class="m who">od 6. 10. · připomenout zítra</div></span></label>
          <label class="task"><input type="checkbox"><span class="t">Karel — monitor, sériové číslo<div class="m who">od 20. 10.</div></span></label>
        </div>
        <div class="lane">
          <h3>Strategické <span class="n">2</span></h3>
          <label class="task"><input type="checkbox"><span class="t">AI workshop pro nováčky<div class="m late">leží 40 dní · patří do pondělního focus time</div></span></label>
          <label class="task"><input type="checkbox"><span class="t">Každý kvartál skupina 2 k zákazníkovi<div class="m">leží 106 dní</div></span></label>
        </div>
      </div>
    </section>
  </main>

  <aside class="side">
    <section class="card">
      <header><h2>Dnes</h2><span class="grow"></span><span class="sub">3 h 30 min volna</span></header>
      <div class="day">
        <div class="ev"><span class="h">09:00</span><span><span class="n">L1 team talk</span><div class="p">potřebuje přípravu</div></span></div>
        <div class="ev now"><span class="h">10:30</span><span><span class="n">1:1 — Lucie</span><div class="p">příprava: 2 otevřené úkoly, poslední 4. 6.</div></span></div>
        <div class="ev"><span class="h">13:00</span><span><span class="n">online pohovor — kandidát</span><div class="p">CV z Recruitis</div></span></div>
        <div class="ev"><span class="h">15:00</span><span><span class="n">Provozní meeting</span><div class="m" style="font-size:12px;color:var(--text-3)">nepotvrzeno</div></span></div>
      </div>
    </section>
    <section class="card">
      <header><h2>1on1</h2><span class="grow"></span><span class="sub">15 lidí</span></header>
      <ul class="people">
        <li><span>Valentín<div class="m">29. 10.</div></span><span class="badge">6 otevřených</span></li>
        <li><span>René<div class="m">20. 10.</div></span><span></span></li>
        <li><span>Václav Čada<div class="m">26. 6. · dlouho bez 1on1</div></span><span class="badge">2 otevřené</span></li>
        <li><span>Lucie<div class="m">4. 6. · dnes 10:30</div></span><span></span></li>
      </ul>
    </section>
  </aside>
</div>
<p class="note">Náhled s ukázkovými daty. Klik na položku TOP 10 ukáže citaci, ze které pořadí vychází.</p>
<script>
document.querySelectorAll('.top10 li').forEach(li => li.addEventListener('click', e => {
    if (e.target.closest('a,button')) return;
    li.classList.toggle('open');
}));
</script>
</body>
</html>
