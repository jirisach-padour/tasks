<?php
// Nové GUI Tasks (2026-10-29): osobní úkoly z hetzneru, pracovní data z Priority na operations (prohlížeč, klíč, VPN).
require_once __DIR__ . '/config.php';
requireAuth();
header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
?><!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Tasks</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 32 32%27%3E%3Crect width=%2732%27 height=%2732%27 rx=%278%27 fill=%27%237048E8%27/%3E%3Cpath d=%27M9 16.5l4.5 4.5L23 11%27 fill=%27none%27 stroke=%27white%27 stroke-width=%273.2%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27/%3E%3C/svg%3E">

<script>try { const m = localStorage.getItem('tg_motiv'); if (m) document.documentElement.dataset.theme = m; } catch (e) {}</script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/novy.css?v=11">
</head>
<body>
<header class="tg-top">
  <h1>Tasks</h1>
  <nav class="tg-nav">
    <a href="#ted" data-v="ted">Teď</a><a href="#ukoly" data-v="ukoly">Úkoly</a><a href="#lide" data-v="lide">1on1</a><a href="#tym" data-v="tym">Tým</a>
  </nav>
  <span class="tg-grow"></span>
  <span class="tg-conn" id="tg-conn"><i></i><span>…</span></span>
  <button class="tg-ikona" id="tg-motiv" title="Světlý / tmavý režim">◐</button>
  <a class="tg-ikona" href="stary.php" title="Stará verze">↩</a>
</header>

<div class="tg-banner" id="tg-banner" hidden></div>

<div class="tg-quick" id="tg-quick">
  <input id="tg-quick-in" placeholder="Rychlý úkol… (Enter uloží)" autocomplete="off">
  <select id="tg-quick-typ"><option value="work">Práce</option><option value="personal">Osobní</option></select>
</div>

<main id="tg-main"></main>

<dialog id="tg-duk"></dialog>
<dialog id="tg-dlg"><form method="dialog" id="tg-dlg-form"></form></dialog>
<div class="tg-toast" id="tg-toast"><span></span><button type="button">Zpět</button></div>

<script src="js/logika.js?v=3"></script>
<script src="js/novy.js?v=15"></script>
</body>
</html>
