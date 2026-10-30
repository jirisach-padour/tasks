// Nové GUI Tasks (29. 10.). Osobní úkoly a kalendář: api.php (hetzner, session). Pracovní data: Priority na operations
// (Bearer klíč jen v localStorage, VPN). Čisté funkce v logika.js (window.TL), testy tests/logika_test.js.
'use strict';

const TG_PR = 'https://operations.daktela.com/padour/priority/api.php';
const TG_PRIORITY_WEB = 'https://operations.daktela.com/padour/priority/';
const esc = TL.esc;
const tg = { kalPrace: null, klic: null, prace: null, ted: null, osobni: [], prUkoly: [], udalosti: [], lide: [], strat: null, chybaPrace: null, otevrenoSlack: true, tym: {}, tymDni: Number(tgLsBezpecne('tg_tym_dni')) || 30 };   // JS 30. 10.: co je bez reakce, musí být vidět

// ---------- úložiště a klíč ----------
function tgLsBezpecne(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
function tgLs(k, v) {
    try { if (v === undefined) return localStorage.getItem(k); if (v === null) localStorage.removeItem(k); else localStorage.setItem(k, v); } catch (e) { return null; }
}
function tgSs(k, v) {
    try { if (v === undefined) return sessionStorage.getItem(k); if (v === null) sessionStorage.removeItem(k); else sessionStorage.setItem(k, v); } catch (e) { return null; }
}
(function tgPrevezmiKlic() {
    const m = location.hash.match(/pr_klic=([a-f0-9]{64})/);
    if (m) { tgLs('tg_pr_klic', m[1]); history.replaceState(null, '', location.pathname + '#ted'); }
    tg.klic = tgLs('tg_pr_klic');
})();

// ---------- API ----------
function tgHetzner(akce, metoda, data) {
    const o = { method: metoda || 'GET', headers: { Accept: 'application/json' } };
    if (data) { o.headers['Content-Type'] = 'application/json'; o.body = JSON.stringify(data); }
    return fetch('api.php?action=' + akce, o).then(r => { if (r.status === 401) { location.href = 'login.php'; throw new Error('login'); } return r.json(); });
}
function tgPriority(akce, data) {
    if (!tg.klic) return Promise.reject(new Error('bez_klice'));
    const o = { method: data ? 'POST' : 'GET', headers: { Authorization: 'Bearer ' + tg.klic } };
    if (data) { o.headers['Content-Type'] = 'application/json'; o.body = JSON.stringify(data); }
    return fetch(TG_PR + '?action=' + akce, o).then(r => {
        if (r.status === 401) { tgLs('tg_pr_klic', null); tg.klic = null; throw new Error('klic_neplatny'); }
        return r.json();
    });
}

// ---------- načtení ----------
function tgNacti() {
    const osobni = tgHetzner('tasks&type=personal').then(d => { tg.osobni = d.tasks || []; }).catch(() => { tg.osobni = []; });
    const kal = tgHetzner('calendar').then(d => { tg.udalosti = d.events || []; }).catch(() => { tg.udalosti = []; });
    const prace = !tg.klic ? Promise.resolve(tg.prace = false) : Promise.all([
        tgPriority('ted'), tgPriority('ukoly'), tgPriority('lide'), tgPriority('strategicky'), tgPriority('kalendar'),
    ]).then(([t, u, l, s, k]) => {
        tg.prace = true; tg.chybaPrace = null;
        tg.ted = t; tg.prUkoly = u.ukoly || []; tg.lide = l.lide || []; tg.strat = s;
        tg.kalPrace = (k && k.events && k.events.length) ? k.events : null;   // pracovní kalendář z operations má přednost
    }).catch(e => { tg.prace = false; tg.chybaPrace = e.message; });
    return Promise.all([osobni, kal, prace]).then(() => { if (tg.prace && tg.kalPrace) tg.udalosti = tg.kalPrace; }).then(tgVykresli);
}

// ---------- obecné ----------
function tgToast(text, zpet) {
    const t = document.getElementById('tg-toast');
    t.querySelector('span').textContent = text;
    const b = t.querySelector('button');
    b.hidden = !zpet;
    b.onclick = () => { t.classList.remove('on'); if (zpet) zpet(); };
    t.classList.add('on');
    clearTimeout(tgToast.cas);
    tgToast.cas = setTimeout(() => t.classList.remove('on'), 6000);
}
function tgDny(iso) { return iso ? Math.floor((Date.now() - new Date(String(iso).replace(' ', 'T')).getTime()) / 86400000) : 0; }
function tgDnesIso() { const d = new Date(); return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
function tgPohled() { const h = location.hash.replace('#', ''); return h.startsWith('osoba=') ? 'osoba' : (['ted', 'ukoly', 'lide', 'tym'].includes(h) ? h : 'ted'); }

function tgStav() {
    const c = document.getElementById('tg-conn');
    c.className = 'tg-conn ' + (tg.prace ? 'ok' : 'off');
    c.querySelector('span').textContent = tg.prace ? 'Práce připojena · ' + (tg.ted && tg.ted.sber ? tg.ted.sber.kdy.slice(11, 16) : '') : (tg.klic ? 'Práce nedostupná (VPN?)' : 'Práce nepropojena');
    const b = document.getElementById('tg-banner');
    if (!tg.prace) {
        b.hidden = false;
        b.innerHTML = '<div>' + (tg.klic ? 'Pracovní data z operations nejsou dostupná — jsi na VPN? Ukazuji jen osobní úkoly.' :
            'Pracovní data nejsou propojená. Na VPN otevři Priority a klikni „Propojit tento prohlížeč s Tasks“.') +
            ' <a class="tg-btn mini" href="' + TG_PRIORITY_WEB + '" target="_blank" rel="noopener">Otevřít Priority</a></div>';
    } else b.hidden = true;
}

// ---------- vykreslení ----------
function tgVykresli() {
    tgStav();
    const v = tgPohled();
    document.querySelectorAll('.tg-nav a').forEach(a => a.classList.toggle('on', a.dataset.v === v || (v === 'osoba' && a.dataset.v === 'lide')));
    const main = document.getElementById('tg-main');
    main.classList.toggle('siroke', v === 'ukoly' || v === 'tym');
    if (v === 'ukoly') main.innerHTML = renderUkolyPohled();
    else if (v === 'tym') main.innerHTML = renderTymPohled();
    else if (v === 'lide') main.innerHTML = renderLidePohled();
    else if (v === 'osoba') { main.innerHTML = '<p class="tg-muted">Načítám…</p>'; tgNactiOsobu(decodeURIComponent(location.hash.slice(7))); return; }
    else main.innerHTML = renderTedPohled();
    document.getElementById('tg-quick').hidden = v === 'osoba';
}

function tgRezim() {
    return TL.rezim(new Date(), tg.udalosti.filter(e => e.date === tgDnesIso()), { ukazPraci: tgSs('tg_ukaz_praci') === '1', konecFocus: tgSs('tg_konec_focus') === tgDnesIso() });
}

function renderTedPohled() {
    const rezim = tgRezim();
    if (rezim === 'zavreno') return renderZavreno();
    const p = tg.prace && tg.ted ? tg.ted.polozky : [];
    const minule = (() => { try { return JSON.parse(tgSs('tg_minule') || 'null'); } catch (e) { return null; } })();
    const znacky = TL.znacky(p, minule);
    const r = TL.ted(p);
    tgSs('tg_minule', JSON.stringify(Object.fromEntries(p.slice(0, 30).map((x, i) => [x.klic, i]))));
    tg.zobrazene = p;
    const fokus = rezim === 'focus';
    let html = '';
    if (fokus) html += '<div class="tg-banner" style="margin:0 0 14px;padding:0"><div>Focus time — vidíš jen strategický blok a to, co hoří v řádu hodin. <button data-akce="konec-focus">Ukončit focus</button></div></div>';
    if (!fokus) html += renderDlazdice();
    html += '<div class="tg-wrap"><div class="tg-col">';
    if (tg.prace) {
        const hlavniVidet = r.hlavni && (!fokus || r.hlavni.skupina === 1);
        html += '<section class="tg-card"><header><h2>Teď</h2><span class="tg-grow"></span><span class="tg-sub">' + esc(new Date().toLocaleDateString('cs-CZ', { weekday: 'long', day: 'numeric', month: 'long' })) + '</span></header>';
        if (!r.hlavni) html += '<div class="tg-next"><div class="big">Nic nehoří.</div><div class="why">Žádný zákazník na tebe nečeká.</div></div>';
        else if (hlavniVidet) html += renderHlavni(r.hlavni, znacky);
        else html += '<div class="tg-next"><div class="why">Nic nehoří v řádu hodin. Pracuj na strategickém bloku.</div></div>';
        if (!fokus && r.dalsi.length) {
            html += '<div class="tg-waiting' + (r.cekaji ? ' cekaji' : '') + '"><h3>' + (r.cekaji ? 'Čekají na tebe <span>další ' + r.dalsi.length + '</span>' : 'Další <span>v pořadí</span>') + '</h3>' + renderRadky(r.dalsi, 2, znacky) + '</div>';
        }
        if (!fokus && r.zbytek.length) html += '<details class="tg-more"><summary>Celý TOP 10 — dalších ' + r.zbytek.length + ' ▾</summary>' + renderRadky(r.zbytek, 2 + r.dalsi.length, znacky) + '</details>';
        if (!fokus) html += renderSlack();
        html += '</section>';
        html += renderStrategicky();
        if (!fokus) html += renderCekam();
    }
    if (!fokus) html += renderMojeUkoly();
    html += '</div><aside class="tg-col">' + renderDnes() + (fokus ? '' : renderPriprava()) + '</aside></div>';
    return html;
}

/** Důvod bez opakování názvu skupiny („Zákazník čeká: Zákazník čeká 26 h“ → „26 h“). */
function tgDuvod(x) {
    const d = String(x.duvod || ''), s = String(x.skupina_nazev || '');
    return d.toLowerCase().startsWith(s.toLowerCase()) ? d.slice(s.length).replace(/^[\s:·-]+/, '') : d;
}
function tgJeHot(x) { return [1, 2, 3].includes(x.skupina); }
function tgTier(x) { return x.tier ? '<span class="tg-tag' + (/1[AB]/.test(x.tier) ? ' tier1' : '') + '">' + esc(x.tier) + '</span>' : ''; }
function tgZnacka(z) { return z === 'nove' ? '<span class="tg-tag nove">nové</span>' : z === 'nahoru' ? '<span class="tg-tag nahoru">↑ posunuto</span>' : ''; }
function tgIdx(x) { return tg.zobrazene.indexOf(x); }

function renderHlavni(x, znacky) {
    const duvod = tgDuvod(x);
    return '<div class="tg-next' + (tgJeHot(x) ? ' hot' : '') + '"><div class="lbl">' + esc(x.skupina_nazev) + (duvod ? ' · ' + esc(duvod) : '') + tgZnacka(znacky[x.klic]) + '</div>' +
        '<div class="big">' + esc(x.titul) + '</div>' +
        '<div class="why">' + (x.zakaznik ? '<b>' + esc(x.zakaznik) + '</b>' : '') + (x.tier ? (x.zakaznik ? ' · ' : '') + esc(x.tier) : '') + '</div>' +
        (x.citace ? '<div class="tg-cite">' + esc(x.citace) + '</div>' : '') +
        '<div class="tg-acts">' + (x.url ? '<a class="tg-btn pri" href="' + esc(x.url) + '" target="_blank" rel="noopener">Otevřít</a>' : '') +
        '<button class="tg-btn" data-akce="hotovo" data-i="' + tgIdx(x) + '">✓ Hotovo</button>' +
        (x.slack_zdroj ? '<button class="tg-btn" data-akce="slack-ukol" data-i="' + tgIdx(x) + '">+ úkol</button>' : '') +
        '<button class="tg-btn ghost" data-akce="spatne" data-i="' + tgIdx(x) + '">Špatně zařazeno…</button></div></div>';
}

function renderRadky(pole, od, znacky) {
    return '<ul class="tg-rows">' + pole.map((x, i) => '<li class="' + (tgJeHot(x) ? 'hot' : '') + '" data-rozbal="1"><span class="r">' + (od + i) + '</span>' +
        '<span class="t">' + (x.url ? '<a href="' + esc(x.url) + '" target="_blank" rel="noopener">' + esc(x.titul) + '</a>' : esc(x.titul)) + tgTier(x) + tgZnacka(znacky[x.klic]) + '</span>' +
        '<span class="w">' + [x.skupina_nazev + (tgDuvod(x) ? ' ' + tgDuvod(x) : ''), x.zakaznik].filter(Boolean).map(esc).join(' · ') + '</span>' +
        (x.citace ? '<span class="c tg-cite">' + esc(x.citace) + '</span>' : '') +
        '<span class="a">' + (x.slack_zdroj ? '<button class="tg-ib" data-akce="slack-ukol" data-i="' + tgIdx(x) + '" title="Udělat z toho úkol">+</button>' : '') + '<button class="tg-ib ok" data-akce="hotovo" data-i="' + tgIdx(x) + '" title="Hotovo">✓</button><button class="tg-ib" data-akce="spatne" data-i="' + tgIdx(x) + '" title="Špatně zařazeno">⋯</button></span></li>').join('') + '</ul>';
}

function renderSlack() {
    const z = (tg.ted && tg.ted.slack) || [];
    if (!z.length) return '<div class="tg-slack"><span class="tg-sub">Slack: na všechno jsi reagoval.</span></div>';
    const s = TL.slackSouhrn(z);
    let h = '<div class="tg-slack"><span class="tg-sub">Slack</span><button data-akce="slack">Bez tvé reakce:' + (s.karel ? ' Karel <b>' + s.karel + '</b>' : '') + (s.kanaly ? ' kanály <b>' + s.kanaly + '</b>' : '') + (s.dm ? ' DM <b>' + s.dm + '</b>' : '') + '</button></div>';
    if (tg.otevrenoSlack) h += '<div class="tg-slackpop"><ul class="tg-rows">' + TL.slackSkupiny(z).map(sk => sk.i.map((i, n) => tgSlackRadek(z[i], i, n, sk)).join('')).join('') + '</ul></div>';
    return h;
}

/** Řádek Slacku: n = 0 hlavní (nejstarší) řádek skupiny, jeho ✓ vyřídí celou konverzaci; novější odsazené pod ním. */
function tgSlackRadek(m, i, n, sk) {
    const vic = sk.i.length > 1, hlavni = vic && n === 0;
    return '<li class="' + (vic && n > 0 ? 'tg-sub-radek' : '') + (hlavni ? ' tg-hlavni' : '') + '"><span class="r">' + (hlavni ? sk.i.length : '·') + '</span><span class="t"><a href="' + esc(m.url) + '" target="_blank" rel="noopener">' + esc(m.autor_jmeno) + '</a> <span class="tg-sub">' + esc(m.kanal_nazev) + ' · ' + esc(m.kdy.slice(5, 16)) + (hlavni ? ' · ' + sk.i.length + ' zpráv' : '') + '</span></span>' +
        '<span class="w">' + esc(m.text.length > 180 ? m.text.slice(0, 180) + '…' : m.text) + '</span><span class="a">' + (m.ma_ukol ? '<span class="tg-tag">úkol</span>' : '<button class="tg-ib" data-akce="slack-ukol-m" data-i="' + i + '" title="Udělat z toho úkol">+</button>') +
        (hlavni ? '<button class="tg-ib ok" data-akce="slack-hotovo-vse" data-kanal="' + esc(sk.kanal) + '" title="Vyřízeno vše v konverzaci (' + sk.i.length + ')">✓✓</button>' : '') +
        '<button class="tg-ib ok" data-akce="slack-hotovo" data-i="' + i + '" title="Vyřízeno">✓</button></span></li>';
}

function renderStrategicky() {
    const s = tg.strat;
    if (!s || !s.kandidati.length) return '';
    const v = s.vybrany || s.kandidati[0];
    const vybrany = !!s.vybrany;
    return '<section class="tg-card tg-strat"><header><h2>Strategický blok</h2><span class="tg-grow"></span><span class="tg-sub">' + (vybrany ? 'vybráno na tento týden · patří do pondělního focus time' : 'návrh: nejdéle ležící důležitá věc') + '</span></header>' +
        '<div class="tg-pad"><div class="nazev">' + esc(v.title) + '</div><div class="tg-muted" style="font-size:13.5px">Leží ' + v.dni + ' dní — jediná věc, kterou tento týden posouváš dlouhodobě.</div>' +
        '<div class="tg-acts">' + (vybrany ? '<button class="tg-btn" data-akce="prace-hotovo" data-id="' + v.id + '">Hotovo</button>' : '<button class="tg-btn pri" data-akce="strat-vyber" data-id="' + v.id + '">Vybrat na tento týden</button>') +
        '<select data-akce="strat-jiny" class="tg-btn"><option value="">Vybrat jiný…</option>' + s.kandidati.map(k => '<option value="' + k.id + '">' + esc(k.title.slice(0, 60)) + ' (' + k.dni + ' d)</option>').join('') + '</select></div></div></section>';
}

function renderCekam() {
    const c = tg.prUkoly.filter(u => u.status === 'open' && u.ceka_na);
    if (!c.length) return '';
    return '<section class="tg-card"><header><h2>Čekám na</h2><span class="tg-grow"></span><span class="tg-sub">vrátí se samo v den připomenutí</span></header><div class="tg-pad">' +
        c.map(u => renderUkol(u, 'work', '<div class="m who">' + esc(u.ceka_na) + (u.pripomenout ? ' · připomenout ' + TL.datum(u.pripomenout) : ' · bez data připomenutí') + '</div>')).join('') + '</div></section>';
}

function renderUkol(u, typ, meta) {
    const dnes = tgDnesIso();
    const m = meta !== undefined ? meta : (u.due_date ? '<div class="m' + (u.due_date < dnes ? ' late' : '') + '">' + (u.due_date < dnes ? 'po termínu ' : 'termín ') + TL.datum(u.due_date) + '</div>' : '<div class="m">' + (typ === 'personal' ? 'osobní' : 'práce') + '</div>');
    return '<div class="tg-task' + (u.status === 'done' ? ' done' : '') + '"><input type="checkbox" data-akce="ukol-hotovo" data-typ="' + typ + '" data-id="' + u.id + '"' + (u.status === 'done' ? ' checked' : '') + '>' +
        '<span class="t" data-akce="ukol-uprav" data-typ="' + typ + '" data-id="' + u.id + '">' + esc(u.title) + m + '</span></div>';
}

function renderMojeUkoly() {
    const prace = tg.prUkoly.filter(u => u.status === 'open' && !u.ceka_na).sort((a, b) => (a.due_date || '9') < (b.due_date || '9') ? -1 : 1).slice(0, 8);
    const osobni = tg.osobni.filter(u => u.status !== 'done').slice(0, 6);
    return '<section class="tg-card"><header><h2>Moje úkoly</h2><span class="tg-grow"></span><span class="tg-sub">Práce ' + tg.prUkoly.filter(u => u.status === 'open').length + ' · Osobní ' + osobni.length + ' · <a href="#ukoly">vše</a></span></header><div class="tg-pad">' +
        (prace.length || osobni.length ? prace.map(u => renderUkol(u, 'work')).join('') + osobni.map(u => renderUkol(u, 'personal')).join('') : '<span class="tg-muted">Nic.</span>') + '</div></section>';
}

function tgCas(e) { return e.allDay ? 'celý den' : (e.time || ''); }
function renderDnes() {
    const dnes = tgDnesIso();
    const ev = tg.udalosti.filter(e => e.date === dnes);
    const ted = Date.now();
    const busy = ev.filter(e => !e.allDay).reduce((s, e) => s + (Number(e.durationH) || 0), 0);
    return '<section class="tg-card"><header><h2>Dnes</h2><span class="tg-grow"></span><span class="tg-sub">' + (ev.length ? Math.max(0, 8 - busy).toFixed(1).replace('.0', '') + ' h volna' : 'kalendář prázdný') + '</span></header><div class="tg-pad">' +
        (ev.length ? ev.map(e => {
            const bezi = !e.allDay && new Date(e.start).getTime() <= ted && ted < new Date(e.end).getTime();
            const pozn = e.rsvp === 'accepted' ? '<div class="p">potvrzeno · připrav se</div>' : e.rsvp ? '<div class="q">' + ({ needsAction: 'nepotvrzeno', tentative: 'možná', declined: 'odmítnuto' }[e.rsvp] || e.rsvp) + '</div>' : '';
            return '<div class="tg-ev' + (bezi ? ' now' : '') + '"><span class="h">' + esc(tgCas(e)) + '</span><span><span class="n">' + esc(e.title) + '</span>' + pozn + '</span></div>';
        }).join('') : '<span class="tg-muted">Žádné schůzky.</span>') + '</div></section>';
}

/** Příprava na nejbližší potvrzenou schůzku, jejíž název obsahuje jméno člověka z 1on1. */
function renderPriprava() {
    if (!tg.prace || !tg.lide.length) return '';
    const dnes = tgDnesIso(), ted = Date.now();
    const ev = tg.udalosti.filter(e => e.date === dnes && e.rsvp === 'accepted' && !e.allDay && new Date(e.end).getTime() > ted);
    for (const e of ev) {
        const t = (e.title || '').toLowerCase();
        const o = tg.lide.find(l => l.name.split(' ').some(c => c.length > 2 && t.includes(c.toLowerCase())));
        if (!o) continue;
        return '<section class="tg-card"><header><h2>Příprava — ' + esc(o.name) + '</h2><span class="tg-grow"></span><span class="tg-sub">' + esc(tgCas(e)) + '</span></header><div class="tg-pad tg-prep" id="tg-prep" data-osoba="' + esc(o.name) + '">' +
            '<div class="k">Feedback, který dlužíš</div><div>Napiš si 1–2 konkrétní věci: co ocenit a co říct na rovinu.</div>' +
            '<div class="k">Otevřené z minula</div><div class="tg-muted">' + (o.otevrenych ? o.otevrenych + ' úkolů' : 'nic') + '</div>' +
            '<div class="tg-acts"><a class="tg-btn" href="#osoba=' + encodeURIComponent(o.name) + '">Celá historie</a></div></div></section>';
    }
    return '';
}

function renderZavreno() {
    const prvni = tg.prace && tg.ted && tg.ted.polozky[0];
    const osobni = tg.osobni.filter(u => u.status !== 'done');
    return '<div class="tg-zavreno"><div class="tg-card"><div class="k-t">Práce je na dnes zavřená.</div>' +
        (prvni ? '<div class="tg-muted">Zítra začni: <b>' + esc(prvni.titul) + '</b> (' + esc(prvni.duvod) + ').</div>' : '') +
        '<div class="tg-muted">Pracovní položky se ukážou zase v 7:00 — nic ti mezitím neuteče.</div>' +
        (osobni.length ? '<div>' + osobni.map(u => renderUkol(u, 'personal')).join('') + '</div>' : '') +
        '<div><button class="tg-btn" data-akce="ukaz-praci">Přesto ukázat práci</button></div></div></div>';
}

function renderDlazdice() {
    const d = TL.dlazdice(tg.prace ? tg.ted : null, tg.prUkoly, tg.osobni, tg.lide, tgDnesIso());
    const dl = (n, popis, cil, hot) => '<a class="tg-tile' + (hot && n ? ' hot' : '') + (n ? '' : ' nula') + '" href="' + cil + '"><b>' + n + '</b><span>' + popis + '</span></a>';
    return '<div class="tg-tiles">' + (tg.prace ? dl(d.cekaji, 'zákazníků čeká', '#ted', true) + dl(d.dluh, 'dlužíme', '#ted', true) +
        dl(d.slack, 'Slack bez reakce', '#ted-slack', false) : '') + dl(d.poTerminu, 'po termínu', '#ukoly', true) + (tg.prace ? dl(d.jedenOn1, 'úkolů z 1on1', '#lide', false) : '') + '</div>';
}

const TG_SLOUPCE = { urgent_important: 'Hoří', important: 'Důležité', urgent: 'Urgentní', other: 'Ostatní', ceka: 'Čekám na', osobni: 'Osobní' };
function renderUkolyPohled() {
    const karty = { urgent_important: [], important: [], urgent: [], other: [], ceka: [], osobni: [] };
    if (tg.prace) tg.prUkoly.filter(u => u.status === 'open').forEach(u => karty[TL.sloupec(u, 'work')].push(['work', u]));
    tg.osobni.filter(u => u.status !== 'done').forEach(u => karty.osobni.push(['personal', u]));
    const dnes = tgDnesIso();
    const karta = ([typ, u]) => '<div class="tg-kanban-karta" draggable="true" data-typ="' + typ + '" data-id="' + u.id + '">' +
        '<div class="tg-task"><input type="checkbox" data-akce="ukol-hotovo" data-typ="' + typ + '" data-id="' + u.id + '"><span class="t" data-akce="ukol-uprav" data-typ="' + typ + '" data-id="' + u.id + '">' + esc(u.title) +
        (u.ceka_na ? '<div class="m who">' + esc(u.ceka_na) + (u.pripomenout ? ' · ' + TL.datum(u.pripomenout) : '') + '</div>' : u.due_date ? '<div class="m' + (u.due_date < dnes ? ' late' : '') + '">' + (u.due_date < dnes ? 'po termínu ' : '') + TL.datum(u.due_date) + '</div>' : '') +
        '</span></div></div>';
    const sloupce = tg.prace ? TL.SLOUPCE : ['osobni'];
    return '<div class="tg-kanban-hlava"><h2>Úkoly</h2><span class="tg-sub">přetáhni kartu do jiného sloupce · klik na název = upravit</span></div>' +
        '<div class="tg-kanban">' + sloupce.map(k => '<section class="tg-kanban-sloupec" data-sloupec="' + k + '"><header><span>' + TG_SLOUPCE[k] + '</span><span class="tg-sub">' + karty[k].length + '</span></header>' +
        '<div class="tg-kanban-karty">' + (karty[k].map(karta).join('') || '<div class="tg-kanban-prazdno">Přetáhni sem</div>') + '</div></section>').join('') + '</div>';
}

function renderLidePohled() {
    if (!tg.prace) return '<p class="tg-muted">1on1 je na operations — připoj se na VPN.</p>';
    return '<section class="tg-card"><header><h2>1on1</h2><span class="tg-sub">' + tg.lide.length + ' lidí</span></header><div class="tg-pad"><ul class="tg-rows">' +
        tg.lide.map(l => '<li><span class="r"></span><span class="t"><a href="#osoba=' + encodeURIComponent(l.name) + '">' + esc(l.name) + '</a>' + (l.otevrenych ? '<span class="tg-tag tier1">' + l.otevrenych + ' otevřených</span>' : '') + '</span>' +
            '<span class="w">' + (l.posledni ? 'poslední ' + TL.datum(l.posledni) + ' · ' + l.zapisu + ' zápisů' + (tgDny(l.posledni) > 45 ? ' · dlouho bez 1on1' : '') : 'bez schůzky') + '</span><span class="a"></span></li>').join('') + '</ul></div></section>';
}

// ---------- Tým: srovnání z kompletní historie (deník v2, JS 30. 10.) ----------
const TG_TYM_STAV = { ok: ['v normě', 'ok'], pod: ['pod týmem', 'warn'], vyrazne: ['výrazně pod', 'hot'], malo_dat: ['málo dat', ''] };
const TG_TYM_OBDOBI = [[30, '30 dní'], [90, '3 měsíce'], [180, 'půl roku'], [365, 'rok']];
function tgNactiTym(dni) {
    tg.tym[dni] = 'nacitam';
    tgPriority('tym&dni=' + dni).then(d => { tg.tym[dni] = d.chyba ? null : d; if (tgPohled() === 'tym') tgVykresli(); }).catch(() => { tg.tym[dni] = null; });
}
function tgCislo(x, des) { return Number(x).toLocaleString('cs-CZ', { minimumFractionDigits: des, maximumFractionDigits: des }); }
function tgTrend(x) {
    if (x === null || x === undefined) return '<span class="tg-muted">—</span>';
    if (Math.abs(x) < 0.05) return '<span class="tg-muted" title="proti předchozímu období">→</span>';
    return '<span class="tg-trend ' + (x > 0 ? 'nahoru' : 'dolu') + '" title="proti předchozímu stejně dlouhému období">' + (x > 0 ? '▲ +' : '▼ ') + tgCislo(x, 2) + '</span>';
}
function renderTymPohled() {
    if (!tg.prace) return '<p class="tg-muted">Srovnání týmu je na operations — připoj se na VPN.</p>';
    const d = tg.tym[tg.tymDni];
    if (d === undefined) tgNactiTym(tg.tymDni);
    const prepinac = '<div class="tg-obdobi">' + TG_TYM_OBDOBI.map(([n, t]) => '<button data-akce="tym-dni" data-d="' + n + '" class="' + (n === tg.tymDni ? 'on' : '') + '">' + t + '</button>').join('') + '</div>';
    if (!d || d === 'nacitam') return '<section class="tg-card"><header><h2>Tým CZ-L1</h2>' + prepinac + '</header><div class="tg-pad"><span class="tg-muted">' + (d === null ? 'Nepodařilo se načíst.' : 'Načítám…') + '</span></div></section>';
    const max = Math.max(1.5, ...d.lide.map(x => x.index || 0)), m = d.median;
    const radek = x => {
        const st = TG_TYM_STAV[x.stav] || TG_TYM_STAV.malo_dat, o = x.ohlasy || {};
        const jm = x.osoba ? '<a href="#osoba=' + encodeURIComponent(x.osoba) + '">' + esc(x.jmeno) + '</a>' : esc(x.jmeno);
        return '<tr class="' + esc(x.stav) + '"><td class="jm">' + jm + (x.kapacita < 1 ? ' <span class="tg-sub">úvazek ' + tgCislo(x.kapacita, 1) + '</span>' : '') + '</td>' +
            '<td class="idx"><div class="tg-idx" title="index ' + (x.index === null ? '—' : tgCislo(x.index, 2)) + ' (1,00 = medián týmu)"><i style="width:' + Math.round((x.index || 0) / max * 100) + '%"></i><b style="left:' + Math.round(1 / max * 100) + '%"></b></div>' +
            '<span class="n">' + (x.index === null ? '—' : tgCislo(x.index, 2)) + '</span></td>' +
            '<td><span class="tg-tag ' + st[1] + '">' + st[0] + '</span></td>' +
            '<td class="c">' + tgCislo(x.tk, 1) + '</td><td class="c">' + tgCislo(x.kom, 1) + '</td><td class="c">' + tgCislo(x.koment, 1) + '</td><td class="c">' + tgCislo(x.cas, 1) + '</td>' +
            '<td class="c">' + x.dnu + '</td><td class="c">' + tgTrend(x.trend) + '</td>' +
            '<td class="c">' + tgZatez(x, m) + '</td><td class="c">' + (x.prideleno === null ? '—' : tgCislo(x.prideleno, 1)) + '</td>' +
            '<td class="c">' + (x.bez_prace === null ? '—' : x.bez_prace ? '<span class="tg-tag ' + (x.bez_prace >= 3 ? 'hot' : 'warn') + '">' + x.bez_prace + '</span>' : '0') + '</td>' +
            '<td class="c">' + tgNalada(x.nalada) + '</td>' +
            '<td class="c">' + (o.pochvala ? '<span class="tg-tag ok">★ ' + o.pochvala + '</span>' : '') + (o.stiznost ? '<span class="tg-tag hot">! ' + o.stiznost + '</span>' : '') + (o.prusvih ? '<span class="tg-tag hot">průšvih ' + o.prusvih + '</span>' : '') + '</td></tr>';
    };
    return '<section class="tg-card"><header><h2>Tým CZ-L1</h2><span class="tg-sub">' + TL.datum(d.od) + ' – ' + TL.datum(d.do) + '</span>' + prepinac + '</header><div class="tg-pad tg-tym-wrap">' +
        '<table class="tg-tym"><thead><tr><th>Člověk</th><th>Index proti týmu</th><th></th><th class="c" title="tickety, na kterých pracoval">Tickety</th><th class="c" title="e-maily, hovory, chaty, SMS, WhatsApp">Komunikace</th><th class="c">Komentáře</th><th class="c" title="Project Time, jedna aktivita nejvýš 4 h">Vykázáno h</th><th class="c" title="dní s aspoň jednou aktivitou">Dní</th><th class="c">Trend</th><th class="c" title="zátěž teď podle Ticket Routeru (otevřené tickety); v závorce průměr období">Zátěž</th><th class="c" title="přidělené tickety na den s prací a úvazek">Přiděleno</th><th class="c" title="pracovní dny bez jediné aktivity a bez evidované absence">Bez práce</th><th class="c" title="nálada z posledního 1on1 (1–5)">Nálada</th><th class="c">Ohlasy</th></tr></thead><tbody>' +
        d.lide.map(radek).join('') + '<tr class="median"><td class="jm">Medián týmu</td><td class="idx"><span class="n">1,00</span></td><td></td><td class="c">' + tgCislo(m.tk, 1) + '</td><td class="c">' + tgCislo(m.kom, 1) + '</td><td class="c">' + tgCislo(m.koment, 1) + '</td><td class="c">' + tgCislo(m.cas, 1) + '</td><td colspan="3"></td></tr></tbody></table>' +
        '<p class="tg-pozn">Hodnoty na den s prací a na úvazek. Index = průměr poměrů tickety, komunikace a vykázaný čas k mediánu týmu; v normě od 0,90, pod týmem 0,70–0,90. Den bez aktivity se nepočítá (dovolená ani nemoc v datech nejsou) — počet dní je ve sloupci Dní. Zdroj: aktivity v Daktele, jen čtení.</p></div></section>';
}

/** Dlouhodobě: index člověka po měsících proti tehdejšímu týmu (sloupce, čára = medián). */
function renderHistorie(h) {
    if (!h || !h.length) return '';
    const max = Math.max(1.5, ...h.map(x => x.index || 0));
    const sl = h.map(x => '<div class="tg-graf-sl" title="' + esc(x.mesic + ': index ' + (x.index === null ? '—' : tgCislo(x.index, 2)) + ', tickety ' + tgCislo(x.tk, 1) + '/den (tým ' + tgCislo(x.median.tk, 1) + '), komunikace ' + tgCislo(x.kom, 1) + ' (tým ' + tgCislo(x.median.kom, 1) + '), vykázáno ' + tgCislo(x.cas, 1) + ' h (tým ' + tgCislo(x.median.cas, 1) + '), ' + x.dnu + ' dní') + '">' +
        '<span class="v ' + esc(x.stav) + '" style="height:' + Math.round((x.index || 0) / max * 100) + '%"></span><span class="d">' + Number(x.mesic.slice(5)) + '</span></div>').join('');
    const posl = h.slice(-6).reverse();
    return '<section class="tg-card"><header><h2>Dlouhodobě</h2><span class="tg-sub">index po měsících · čára = medián týmu</span></header><div class="tg-pad">' +
        '<div class="tg-graf tg-graf-idx"><b class="tg-median" style="bottom:' + Math.round(1 / max * 100) + '%"></b>' + sl + '</div>' +
        '<table class="tg-tym mini"><thead><tr><th>Měsíc</th><th class="c">Index</th><th class="c">Tickety</th><th class="c">Komunikace</th><th class="c">Vykázáno h</th><th class="c">Dní</th></tr></thead><tbody>' +
        posl.map(x => '<tr class="' + esc(x.stav) + '"><td>' + esc(x.mesic) + '</td><td class="c">' + (x.index === null ? '—' : tgCislo(x.index, 2)) + '</td><td class="c">' + tgCislo(x.tk, 1) + ' <span class="tg-sub">/ ' + tgCislo(x.median.tk, 1) + '</span></td><td class="c">' + tgCislo(x.kom, 1) + ' <span class="tg-sub">/ ' + tgCislo(x.median.kom, 1) + '</span></td><td class="c">' + tgCislo(x.cas, 1) + ' <span class="tg-sub">/ ' + tgCislo(x.median.cas, 1) + '</span></td><td class="c">' + x.dnu + '</td></tr>').join('') +
        '</tbody></table><p class="tg-pozn">Hodnota / medián týmu v tom měsíci, na den s prací a úvazek.</p></div></section>';
}

const TG_DRUH = { stiznost: ['Stížnost', 'hot'], prusvih: ['Průšvih', 'hot'], pochvala: ['Pochvala', 'ok'] };
/** Deník člověka: sloupcový graf aktivity a uzavřených ticketů za 14 dní + události (stížnost/průšvih/pochvala). */
function renderDenik(d) {
    if (!d) return '';
    const max = Math.max(1, ...d.dny.map(x => Number(x.aktivit)));
    const pocty = { stiznost: 0, prusvih: 0, pochvala: 0 };
    d.udalosti.forEach(u => { pocty[u.druh] = (pocty[u.druh] || 0) + 1; });
    const graf = d.dny.length ? '<div class="tg-graf">' + d.dny.map(x => '<div class="tg-graf-sl" title="' + esc(TL.datum(x.den) + ': ' + x.aktivit + ' aktivit, ' + x.uzavreno + ' uzavřeno, ' + x.emaily_in + ' e-mailů od zákazníků') + '">' +
        '<span class="v" style="height:' + Math.round(Number(x.aktivit) / max * 100) + '%"></span><span class="d">' + Number(String(x.den).slice(8, 10)) + '</span></div>').join('') + '</div>' : '<span class="tg-muted">Zatím bez dat.</span>';
    const sum = d.dny.reduce((s, x) => ({ a: s.a + Number(x.aktivit), u: s.u + Number(x.uzavreno), e: s.e + Number(x.emaily_in) }), { a: 0, u: 0, e: 0 });
    return '<section class="tg-card"><header><h2>Aktivita</h2><span class="tg-sub">' + d.dny.length + ' dní · ' + sum.a + ' aktivit · ' + sum.u + ' uzavřeno · ' + sum.e + ' e-mailů od zákazníků</span></header><div class="tg-pad">' + graf +
        '<div class="tg-druhy">' + Object.keys(TG_DRUH).map(k => '<span class="tg-tag ' + (pocty[k] ? TG_DRUH[k][1] : '') + '">' + TG_DRUH[k][0] + ' ' + pocty[k] + '</span>').join('') + '</div>' +
        (d.udalosti.length ? '<ul class="tg-rows">' + d.udalosti.map(u => '<li data-rozbal="1"><span class="r">' + (u.druh === 'pochvala' ? '★' : '!') + '</span><span class="t"><a href="https://daktela.daktela.com/tickets/update/' + Number(u.ticket) + '" target="_blank" rel="noopener">' + esc(TG_DRUH[u.druh][0]) + ' · #' + Number(u.ticket) + '</a> <span class="tg-sub">' + esc(u.titul || '') + '</span></span>' +
            '<span class="w">' + esc(String(u.kdy).slice(0, 16)) + ' · jistota ' + Math.round(Number(u.skore) * 100) + ' %</span><span class="c tg-cite">' + esc(u.citace || '') + '</span><span class="a"></span></li>').join('') + '</ul>' : '<div class="tg-muted" style="font-size:13.5px;margin-top:8px">Žádná stížnost, průšvih ani pochvala.</div>') +
        '</div></section>';
}

function tgNactiOsobu(jmeno) {
    tgPriority('osoba&jmeno=' + encodeURIComponent(jmeno)).then(o => {
        const main = document.getElementById('tg-main');
        if (o.chyba) { main.innerHTML = '<p class="tg-err">' + esc(o.chyba) + '</p>'; return; }
        const otevrene = [];
        o.zapisy.forEach(z => z.action_items.forEach((a, i) => { if (!a.done) otevrene.push({ z, a, i }); }));
        const ukol = (z, a, i) => '<div class="tg-task' + (a.done ? ' done' : '') + '"><input type="checkbox" data-akce="1on1" data-k="' + z.id + ':' + i + '"' + (a.done ? ' checked' : '') + '><span class="t">' + esc(a.text) + '</span></div>';
        main.innerHTML = '<div class="tg-detail-hlava"><a href="#lide" class="tg-muted">← 1on1</a><h2>' + esc(o.jmeno) + '</h2>' + (o.popis ? '<span class="tg-muted">' + esc(o.popis) + '</span>' : '') + '</div>' +
            '<div class="tg-wrap"><div class="tg-col">' + renderHistorie(o.denik && o.denik.historie) +
            '<section class="tg-card"><header><h2>Nový zápis</h2></header><div class="tg-pad"><form class="tg-form" id="tg-zapis"><input type="date" name="d" value="' + tgDnesIso() + '" required>' +
            '<textarea name="n" rows="4" placeholder="Co jste probírali…"></textarea><textarea name="u" rows="2" placeholder="Úkoly — jeden na řádek (jen u aktuální schůzky)"></textarea>' +
            '<div><button class="tg-btn pri" type="submit">Uložit zápis</button> <span class="tg-err" id="tg-zapis-chyba"></span></div></form></div></section>' +
            '<section class="tg-card"><header><h2>Historie (' + o.zapisy.length + ')</h2></header><div class="tg-pad">' + (o.zapisy.map(z =>
                '<article class="tg-zapis"><b>' + TL.datum(z.meeting_date) + ' ' + z.meeting_date.slice(0, 4) + '</b>' + (z.tags.length ? ' <span class="tg-sub">' + z.tags.map(esc).join(', ') + '</span>' : '') +
                (z.notes ? '<div class="tg-zapis-text">' + esc(z.notes) + '</div>' : '') + (z.action_items.length ? '<div style="margin-top:6px">' + z.action_items.map((a, i) => ukol(z, a, i)).join('') + '</div>' : '') + '</article>').join('') || '<span class="tg-muted">Bez zápisů.</span>') + '</div></section>' +
            '</div><aside class="tg-col"><section class="tg-card"><header><h2>Otevřené úkoly (' + otevrene.length + ')</h2></header><div class="tg-pad">' + (otevrene.map(x => ukol(x.z, x.a, x.i)).join('') || '<span class="tg-muted">Nic otevřeného.</span>') + '</div></section>' +
            renderDenik(o.denik) +
            (o.kontext && o.kontext.length ? '<section class="tg-card"><header><h2>Z portfolia</h2><span class="tg-sub">review, tým, rozvoj</span></header><div class="tg-pad">' +
                o.kontext.map((k, i) => '<details class="tg-kontext"' + (i === 0 ? '' : '') + '><summary>' + esc(k.zdroj) + '</summary><div class="tg-zapis-text">' + esc(k.text) + '</div></details>').join('') + '</div></section>' : '') +
            '<section class="tg-card"><header><h2>Profil</h2></header><div class="tg-pad">' + (o.profil.length ? '<dl class="tg-profil">' + o.profil.map(p => '<dt>' + esc(p.popis) + '</dt><dd>' + esc(p.text) + '</dd>').join('') + '</dl>' : '<span class="tg-muted">Prázdný.</span>') + '</div></section></aside></div>';
        document.getElementById('tg-zapis').addEventListener('submit', e => {
            e.preventDefault();
            const f = e.target;
            tgPriority('zapis_pridat', { person: o.jmeno, meeting_date: f.d.value, notes: f.n.value, action_items: f.u.value.split('\n') })
                .then(d => { if (d.chyba) document.getElementById('tg-zapis-chyba').textContent = d.chyba; else { tgToast('Zápis uložen.'); tgNactiOsobu(o.jmeno); } });
        });
    }).catch(() => { document.getElementById('tg-main').innerHTML = '<p class="tg-muted">1on1 je na operations — připoj se na VPN.</p>'; });
}

// ---------- akce ----------
function tgHotovoPolozka(x, hotovo) {
    return tgPriority('polozka_hotovo', { zdroj: x.zdroj, klic: x.klic, udalost: x.udalost, hotovo: hotovo }).then(tgNacti);
}

function tgUkolHotovo(typ, id, hotovo) {
    const p = typ === 'personal' ? tgHetzner('tasks&id=' + id, 'PUT', { status: hotovo ? 'done' : 'open' }) : tgPriority('ukol_hotovo', { id: Number(id), hotovo: hotovo });
    return p.then(tgNacti);
}

function tgDialog(html, onSubmit) {
    const d = document.getElementById('tg-dlg'), f = document.getElementById('tg-dlg-form');
    f.innerHTML = html;
    f.onsubmit = e => { if (e.submitter && e.submitter.value === 'zrusit') return; e.preventDefault(); onSubmit(f).then(ok => { if (ok !== false) d.close(); }); };
    d.showModal();
}

function tgUpravUkol(typ, id) {
    const u = (typ === 'personal' ? tg.osobni : tg.prUkoly).find(x => String(x.id) === String(id));
    if (!u) return;
    const kv = [['urgent_important', 'Urgentní a důležité'], ['important', 'Důležité (strategické)'], ['urgent', 'Urgentní'], ['other', 'Ostatní']];
    tgDialog('<h3>' + (typ === 'personal' ? 'Osobní úkol' : 'Pracovní úkol') + '</h3><div class="tg-form">' +
        '<label>Název<input name="title" value="' + esc(u.title) + '" required></label>' +
        '<label>Popis<textarea name="description" rows="3">' + esc(u.description || '') + '</textarea></label>' +
        '<div class="row"><label>Kvadrant<select name="quadrant">' + kv.map(([k, n]) => '<option value="' + k + '"' + (u.quadrant === k ? ' selected' : '') + '>' + n + '</option>').join('') + '</select></label>' +
        '<label>Termín<input type="date" name="due_date" value="' + esc(u.due_date || '') + '"></label></div>' +
        (typ === 'work' ? '<div class="row"><label>Čekám na (kdo)<input name="ceka_na" value="' + esc(u.ceka_na || '') + '"></label><label>Připomenout<input type="date" name="pripomenout" value="' + esc(u.pripomenout || '') + '"></label></div>' : '') +
        '<span class="tg-err" id="tg-dlg-chyba"></span></div><div class="tg-acts"><button class="tg-btn" value="zrusit" formnovalidate>Zrušit</button><button class="tg-btn pri" value="ok">Uložit</button></div>', f => {
        const d = { title: f.title.value, description: f.description.value, quadrant: f.quadrant.value, due_date: f.due_date.value || null };
        if (typ === 'work') { d.ceka_na = f.ceka_na.value; d.pripomenout = f.pripomenout.value || null; d.id = Number(id); }
        const p = typ === 'personal' ? tgHetzner('tasks&id=' + id, 'PUT', d) : tgPriority('ukol_ulozit', d);
        return p.then(r => { if (r.chyba || r.error) { document.getElementById('tg-dlg-chyba').textContent = r.chyba || r.error; return false; } tgNacti(); return true; });
    });
}

function tgSpatne(x) {
    tgDialog('<h3>Špatně zařazeno</h3><p class="tg-muted" style="margin-top:0">' + esc(x.titul) + '<br>teď: ' + esc(x.skupina_nazev) + ' — ' + esc(x.duvod) + '</p>' +
        '<div class="tg-form"><label>Kam to patří a proč<textarea name="duvod" rows="3" required placeholder="např. zákazník nečeká, odpověď šla telefonem; patří do Hlídat"></textarea></label><span class="tg-err" id="tg-dlg-chyba"></span></div>' +
        '<div class="tg-acts"><button class="tg-btn" value="zrusit" formnovalidate>Zrušit</button><button class="tg-btn pri" value="ok">Odeslat</button></div>', f =>
        tgPriority('feedback', { zdroj: x.zdroj, klic: x.klic, skupina: x.skupina, titul: x.titul, duvod: f.duvod.value }).then(r => {
            if (r.chyba) { document.getElementById('tg-dlg-chyba').textContent = r.chyba; return false; }
            tgToast('Díky — z opravy udělám pravidlo.'); return true;
        }));
}

document.addEventListener('click', e => {
    const el = e.target.closest('[data-akce]');
    if (!el) {
        const li = e.target.closest('li[data-rozbal]');
        if (li && !e.target.closest('a')) li.classList.toggle('open');
        return;
    }
    const a = el.dataset.akce;
    if (a === 'tym-dni') { tg.tymDni = Number(el.dataset.d); tgLs('tg_tym_dni', String(tg.tymDni)); tgVykresli(); return; }
    if (a === 'hotovo') {
        const x = tg.zobrazene[Number(el.dataset.i)];
        tgHotovoPolozka(x, true).then(() => tgToast(x.zdroj === 'ticket' ? 'Hotovo — vrátí se při nové aktivitě v ticketu.' : 'Hotovo.', () => tgHotovoPolozka(x, false)));
    } else if (a === 'spatne') tgSpatne(tg.zobrazene[Number(el.dataset.i)]);
    else if (a === 'slack') { tg.otevrenoSlack = !tg.otevrenoSlack; tgVykresli(); }
    else if (a === 'slack-hotovo') {
        const m = tg.ted.slack[Number(el.dataset.i)], x = { zdroj: 'slack', klic: m.klic, udalost: 'x' };
        tgHotovoPolozka(x, true).then(() => tgToast('Vyřízeno — na Slack se nic nezapsalo.', () => tgHotovoPolozka(x, false)));
    } else if (a === 'slack-hotovo-vse') {
        const xs = tg.ted.slack.filter(m => m.kanal === el.dataset.kanal).map(m => ({ zdroj: 'slack', klic: m.klic, udalost: 'x' }));
        const vse = hotovo => Promise.all(xs.map(x => tgPriority('polozka_hotovo', { zdroj: x.zdroj, klic: x.klic, udalost: x.udalost, hotovo: hotovo }))).then(tgNacti);
        vse(true).then(() => tgToast('Vyřízeno ' + xs.length + ' zpráv — na Slack se nic nezapsalo.', () => vse(false)));
    } else if (a === 'slack-ukol' || a === 'slack-ukol-m') {
        const m = a === 'slack-ukol' ? tg.zobrazene[Number(el.dataset.i)] : tg.ted.slack[Number(el.dataset.i)];
        const text = m.text || m.citace || m.titul, zdroj = m.slack_zdroj || ('slack:' + m.klic);
        const kdo = m.autor_jmeno || 'Karel';
        tgPriority('ukol_ulozit', { title: (kdo + ' — ' + String(text).replace(/\s+/g, ' ')).slice(0, 120), description: String(text) + '\n' + (m.url || ''), quadrant: 'urgent_important', zdroj })
            .then(r => { if (r.chyba) tgToast(r.chyba); else { tgToast('Úkol založen — zpráva už v TOP nesvítí.'); tgNacti(); } });
    } else if (a === 'ukol-uprav') tgUpravUkol(el.dataset.typ, el.dataset.id);
    else if (a === 'prace-hotovo') tgUkolHotovo('work', el.dataset.id, true).then(() => tgToast('Hotovo.', () => tgUkolHotovo('work', el.dataset.id, false)));
    else if (a === 'strat-vyber') tgPriority('strategicky', { id: Number(el.dataset.id) }).then(tgNacti);
    else if (a === 'ukaz-praci') { tgSs('tg_ukaz_praci', '1'); tgVykresli(); }
    else if (a === 'konec-focus') { tgSs('tg_konec_focus', tgDnesIso()); tgVykresli(); }
});

document.addEventListener('change', e => {
    const el = e.target;
    if (el.dataset.akce === 'ukol-hotovo') {
        const typ = el.dataset.typ, id = el.dataset.id, h = el.checked;
        tgUkolHotovo(typ, id, h).then(() => { if (h) tgToast('Úkol hotový.', () => tgUkolHotovo(typ, id, false)); });
    } else if (el.dataset.akce === '1on1') {
        tgPriority('polozka_hotovo', { zdroj: '1on1', klic: el.dataset.k, hotovo: el.checked }).then(() => tgNactiOsobu(decodeURIComponent(location.hash.slice(7))));
    } else if (el.dataset.akce === 'strat-jiny' && el.value) tgPriority('strategicky', { id: Number(el.value) }).then(tgNacti);
});

document.getElementById('tg-quick-in').addEventListener('keydown', e => {
    if (e.key !== 'Enter' || !e.target.value.trim()) return;
    const title = e.target.value.trim(), typ = document.getElementById('tg-quick-typ').value;
    const p = typ === 'personal' ? tgHetzner('tasks', 'POST', { title, type: 'personal' }) : tgPriority('ukol_ulozit', { title });
    p.then(r => {
        if (r.chyba || r.error) { tgToast(r.chyba || r.error); return; }
        e.target.value = ''; tgToast('Uloženo.'); tgNacti();
    }).catch(() => tgToast('Pracovní úkol nejde uložit — jsi na VPN?'));
});

document.getElementById('tg-motiv').addEventListener('click', () => {
    const r = document.documentElement;
    const tmavy = r.dataset.theme ? r.dataset.theme === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
    r.dataset.theme = tmavy ? 'light' : 'dark';
    tgLs('tg_motiv', r.dataset.theme);
});

// ---------- přetahování na tabuli ----------
let tgTah = null;
document.addEventListener('dragstart', e => {
    const k = e.target.closest && e.target.closest('.tg-kanban-karta');
    if (!k) return;
    tgTah = { typ: k.dataset.typ, id: k.dataset.id };
    k.classList.add('tah'); e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', k.dataset.id);
});
document.addEventListener('dragend', e => { const k = e.target.closest && e.target.closest('.tg-kanban-karta'); if (k) k.classList.remove('tah'); document.querySelectorAll('.tg-kanban-sloupec.nad').forEach(s => s.classList.remove('nad')); });
document.addEventListener('dragover', e => {
    const s = e.target.closest && e.target.closest('.tg-kanban-sloupec');
    if (!s || !tgTah) return;
    e.preventDefault();
    document.querySelectorAll('.tg-kanban-sloupec.nad').forEach(x => { if (x !== s) x.classList.remove('nad'); });
    s.classList.add('nad');
});
document.addEventListener('drop', e => {
    const s = e.target.closest && e.target.closest('.tg-kanban-sloupec');
    if (!s || !tgTah) return;
    e.preventDefault();
    const { typ, id } = tgTah; tgTah = null;
    const u = (typ === 'personal' ? tg.osobni : tg.prUkoly).find(x => String(x.id) === String(id));
    const zmena = u && TL.presun(u, typ, s.dataset.sloupec);
    if (!zmena) { tgVykresli(); if (u && (typ === 'personal') !== (s.dataset.sloupec === 'osobni')) tgToast('Mezi prací a osobním přesouvat nejde.'); return; }
    if (zmena.zeptat) {
        tgDialog('<h3>Čekám na</h3><p class="tg-muted" style="margin-top:0">' + esc(u.title) + '</p><div class="tg-form"><div class="row"><label>Na koho čekáš<input name="kdo" required></label>' +
            '<label>Připomenout<input type="date" name="kdy"></label></div><span class="tg-err" id="tg-dlg-chyba"></span></div><div class="tg-acts"><button class="tg-btn" value="zrusit" formnovalidate>Zrušit</button><button class="tg-btn pri" value="ok">Uložit</button></div>',
            f => tgPriority('ukol_ulozit', { id: Number(id), ceka_na: f.kdo.value, pripomenout: f.kdy.value || null }).then(r => { if (r.chyba) { document.getElementById('tg-dlg-chyba').textContent = r.chyba; return false; } tgNacti(); return true; }));
        tgVykresli();
        return;
    }
    Object.assign(u, zmena); tgVykresli();   // hned ukázat, uložit na pozadí
    tgPriority('ukol_ulozit', Object.assign({ id: Number(id) }, zmena)).then(r => { if (r.chyba) tgToast(r.chyba); tgNacti(); });
});

window.addEventListener('hashchange', () => {
    if (location.hash === '#ted-slack') { tg.otevrenoSlack = true; history.replaceState(null, '', '#ted'); }
    tgVykresli();
});
document.addEventListener('visibilitychange', () => { if (!document.hidden) tgNacti(); });
setInterval(() => { if (!document.hidden && !document.getElementById('tg-dlg').open) tgNacti(); }, 5 * 60 * 1000);
tgNacti();
