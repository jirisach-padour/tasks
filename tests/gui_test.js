// GUI za běhu (29. 10.): novy.js v napodobeném prohlížeči s testovacími daty — každá obrazovka se vykreslí bez chyby.
// node tests/gui_test.js, výstup jen chyby. Žádná síť, žádná produkční data.
const vm = require('vm');
const fs = require('fs');
const path = require('path');
let chyb = 0;
const t = (ok, co) => { if (!ok) { chyb++; console.log('CHYBA: ' + co); } };

function prvek(id) {
    return {
        id, innerHTML: '', textContent: '', hidden: false, value: '', className: '', dataset: {}, open: false,
        classList: { add() {}, remove() {}, toggle() {} },
        addEventListener() {}, querySelector() { return prvek('q'); }, querySelectorAll() { return []; },
        showModal() { this.open = true; }, close() { this.open = false; },
    };
}

const dnes = new Date(); const iso = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
const DATA = {
    'api.php?action=tasks&type=personal': { tasks: [{ id: 1, title: 'Servis auta', status: 'open', due_date: null, quadrant: 'other' }] },
    'api.php?action=calendar': { connected: true, events: [
        { title: '1:1 — René', date: iso(dnes), time: '23:00', allDay: false, start: iso(dnes) + 'T23:00:00', end: iso(dnes) + 'T23:30:00', durationH: 0.5, rsvp: 'accepted' },
        { title: 'Provozní meeting', date: iso(dnes), time: '23:30', allDay: false, start: iso(dnes) + 'T23:30:00', end: iso(dnes) + 'T23:45:00', durationH: 0.25, rsvp: 'needsAction' }] },
    ted: { polozky: [
        { skupina: 2, skupina_nazev: 'Zákazník čeká', klic: '1', zdroj: 'ticket', udalost: 'a', titul: 'Výpadek <SMS>', duvod: 'Zákazník čeká 26 h', citace: '„Kdy?“', tier: 'Tier 1B', zakaznik: 'FITHAM', url: 'https://x/1' },
        { skupina: 3, skupina_nazev: 'Dlužíme', klic: '2', zdroj: 'ticket', udalost: 'b', titul: 'Nacenění', duvod: 'Dlužíme', citace: null, tier: 'Tier 3', zakaznik: 'X', url: 'https://x/2' },
        { skupina: 2, skupina_nazev: 'Zákazník čeká', klic: '3', zdroj: 'ticket', udalost: 'c', titul: 'Routing', duvod: 'čeká', citace: null, tier: 'bez tieru', zakaznik: 'Y', url: 'https://x/3' },
        { skupina: 7, skupina_nazev: 'Interní a úkoly', klic: '4', zdroj: 'ukol', udalost: '', titul: 'Billing', duvod: 'Termín', citace: null, tier: null, zakaznik: '', url: null }],
        slack: [{ klic: 'C:1', kanal_nazev: '#support_management', autor_jmeno: 'Karel Vůjtek', text: 'Dneska ten Solver?', kdy: '2026-10-29 12:27:13', poradi: 0, url: 'https://s/1' }],
        sber: { kdy: '2026-10-29 21:00:00', ticketu: 51, slack: 1 } },
    ukoly: { ukoly: [
        { id: 10, title: 'Infra témata', status: 'open', quadrant: 'urgent_important', due_date: '2026-10-21', ceka_na: null, pripomenout: null },
        { id: 11, title: 'Fix filtrů', status: 'open', quadrant: 'other', due_date: null, ceka_na: 'Ilia', pripomenout: '2026-11-03' }] },
    lide: { lide: [{ name: 'René Heryán', posledni: '2026-10-20', zapisu: 6, otevrenych: 0 }] },
    strategicky: { vybrany: null, kandidati: [{ id: 20, title: 'AI workshop', created_at: '2026-09-01', dni: 58 }] },
    osoba: { jmeno: 'René Heryán', popis: '', profil: [{ popis: 'Silné stránky', text: 'technika' }], zapisy: [{ id: 51, meeting_date: '2026-10-20', notes: 'text <b>', action_items: [{ text: 'a', done: false }], tags: ['rozvoj'] }] },
};

function spust(hash, opts) {
    opts = opts || {};
    const el = {};
    const ctx = {
        console, Promise, Date, JSON, Math, Object, Array, String, Number, RegExp, encodeURIComponent, decodeURIComponent,
        setTimeout: () => 0, clearTimeout() {}, setInterval: () => 0,
        location: { hash, pathname: '/tasks/', href: '' }, history: { replaceState() {} },
        matchMedia: () => ({ matches: false }),
        localStorage: { _d: opts.klic === false ? {} : { tg_pr_klic: 'a'.repeat(64) }, getItem(k) { return this._d[k] ?? null; }, setItem(k, v) { this._d[k] = String(v); }, removeItem(k) { delete this._d[k]; } },
        sessionStorage: { _d: opts.zavreno ? {} : { tg_ukaz_praci: '1', tg_konec_focus: iso(dnes) }, getItem(k) { return this._d[k] ?? null; }, setItem(k, v) { this._d[k] = String(v); }, removeItem(k) { delete this._d[k]; } },
        document: {
            hidden: false, documentElement: { dataset: {} },
            getElementById(id) { return el[id] ??= prvek(id); },
            querySelectorAll() { return []; }, querySelector() { return prvek('q'); }, addEventListener() {},
        },
        fetch(url) {
            const akce = String(url).includes('priority/api.php') ? String(url).split('action=')[1].split('&')[0] : String(url);
            if (opts.bezVpn && String(url).includes('priority')) return Promise.reject(new TypeError('Failed to fetch'));
            const d = DATA[akce] ?? DATA[akce.split('&id=')[0]] ?? {};
            return Promise.resolve({ status: 200, json: () => Promise.resolve(JSON.parse(JSON.stringify(d))) });
        },
    };
    ctx.window = ctx; ctx.addEventListener = () => {};
    vm.createContext(ctx);
    for (const f of ['js/logika.js', 'js/novy.js']) vm.runInContext(fs.readFileSync(path.join(__dirname, '..', f), 'utf8'), ctx, { filename: f });
    const hotovo = () => ({ html: el['tg-main'].innerHTML, banner: el['tg-banner'], ctx });
    return new Promise(r => setTimeout(() => r(hotovo()), 50));
}

(async () => {
    let r = await spust('#ted');
    t(r.html.includes('Výpadek &lt;SMS&gt;'), 'Teď: hlavní položka s escapovaným názvem');
    t(r.html.includes('tg-tiles') && r.html.includes('zákazníků čeká') && r.html.includes('po termínu'), 'Teď: dlaždice přehledu');
    t(r.html.includes('Čekají na tebe'), 'Teď: víc čekajících zákazníků → všichni');
    t(r.html.includes('Tier 1B') && !r.html.includes('Zákazník čeká · Zákazník čeká'), 'Teď: tier, důvod bez opakování');
    t(r.html.includes('Karel'), 'Teď: Slack pruh s Karlem');
    t(r.html.includes('Strategický blok') && r.html.includes('AI workshop'), 'Teď: strategický blok');
    t(r.html.includes('Čekám na') && r.html.includes('Ilia'), 'Teď: čekám na');
    t(r.html.includes('Servis auta'), 'Teď: osobní úkol');
    t(r.html.includes('potvrzeno · připrav se') && r.html.includes('nepotvrzeno'), 'Dnes: RSVP');
    t(r.html.includes('Příprava — René Heryán'), 'Příprava 1:1 podle potvrzené schůzky');
    t(!r.html.includes('<SMS>') && !r.html.includes('undefined'), 'Teď: žádný neescapovaný text ani undefined');
    t(r.banner.hidden === true, 'připojeno: bez banneru');

    r = await spust('#ukoly');
    t(r.html.includes('data-sloupec="urgent_important"') && r.html.includes('Infra témata'), 'Úkoly: tabule, práce ve sloupci Hoří');
    t(r.html.includes('data-sloupec="ceka"') && r.html.includes('Fix filtrů'), 'Úkoly: sloupec Čekám na');
    t(r.html.includes('data-sloupec="osobni"') && r.html.includes('Servis auta') && r.html.includes('draggable="true"'), 'Úkoly: osobní sloupec, karty přetahovatelné');

    r = await spust('#lide');
    t(r.html.includes('René Heryán'), '1on1: seznam lidí');

    r = await spust('#osoba=Ren%C3%A9%20Hery%C3%A1n');
    await new Promise(x => setImmediate(() => setImmediate(x)));
    r.html = r.ctx.document.getElementById('tg-main').innerHTML;
    t(r.html.includes('Historie (1)') && r.html.includes('text &lt;b&gt;') && r.html.includes('Silné stránky'), 'Detail osoby: historie, escape, profil');

    r = await spust('#ted', { zavreno: true });
    const h = new Date().getHours(), den = new Date().getDay();
    if (den === 0 || den === 6 || h < 7 || h >= 18) t(r.html.includes('Práce je na dnes zavřená') && r.html.includes('Zítra začni'), 'Po pracovní době: zavřeno');

    r = await spust('#ted', { bezVpn: true });
    t(r.banner.hidden === false && r.banner.innerHTML.includes('VPN'), 'Bez VPN: banner');
    t(r.html.includes('Servis auta') && !r.html.includes('Výpadek'), 'Bez VPN: jen osobní');

    r = await spust('#ted', { klic: false });
    t(r.banner.innerHTML.includes('Propojit'), 'Bez klíče: návod na propojení');
    process.exit(chyb ? 1 : 0);
})().catch(e => { console.log('CHYBA za běhu: ' + e.stack.split('\n').slice(0, 3).join(' | ')); process.exit(1); });
