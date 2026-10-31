// GUI za běhu (29. 10.): novy.js v napodobeném prohlížeči s testovacími daty — každá obrazovka se vykreslí bez chyby.
// node tests/gui_test.js, výstup jen chyby. Žádná síť, žádná produkční data.
const vm = require('vm');
const fs = require('fs');
const path = require('path');
let chyb = 0;
const t = (ok, co) => { if (!ok) { chyb++; console.log('CHYBA: ' + co); } };
// Regrese 31. 10.: druhá renderPriprava přepsala přípravu na schůzku v Teď — žádné dvě funkce/konstanty stejného jména
{ const jm = [...fs.readFileSync(path.join(__dirname, '..', 'js/novy.js'), 'utf8').matchAll(/^(?:function|const) (\w+)/gm)].map(x => x[1]);
  const dup = jm.filter((x, i) => jm.indexOf(x) !== i); t(!dup.length, 'novy.js: duplicitní názvy ' + dup.join(', ')); }

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
        { title: 'Jen záloha hetzner', date: iso(dnes), time: '22:00', allDay: false, start: iso(dnes) + 'T22:00:00', end: iso(dnes) + 'T22:15:00', durationH: 0.25, rsvp: null },
        { title: '1:1 — René', date: iso(dnes), time: '23:00', allDay: false, start: iso(dnes) + 'T23:00:00', end: iso(dnes) + 'T23:30:00', durationH: 0.5, rsvp: 'accepted' },
        { title: 'Provozní meeting', date: iso(dnes), time: '23:30', allDay: false, start: iso(dnes) + 'T23:30:00', end: iso(dnes) + 'T23:45:00', durationH: 0.25, rsvp: 'needsAction' }] },
    ted: { polozky: [
        { skupina: 2, skupina_nazev: 'Zákazník čeká', klic: '1', zdroj: 'ticket', udalost: 'a', titul: 'Výpadek <SMS>', duvod: 'Zákazník čeká 26 h', citace: '„Kdy?“', tier: 'Tier 1B', zakaznik: 'FITHAM', url: 'https://x/1' },
        { skupina: 3, skupina_nazev: 'Dlužíme', klic: '2', zdroj: 'ticket', udalost: 'b', titul: 'Nacenění', duvod: 'Dlužíme', citace: null, tier: 'Tier 3', zakaznik: 'X', url: 'https://x/2' },
        { skupina: 2, skupina_nazev: 'Zákazník čeká', klic: '3', zdroj: 'ticket', udalost: 'c', titul: 'Routing', duvod: 'čeká', citace: null, tier: 'bez tieru', zakaznik: 'Y', url: 'https://x/3' },
        { skupina: 7, skupina_nazev: 'Interní a úkoly', klic: 'C:1', zdroj: 'slack', udalost: 'x', titul: 'Karel — Dneska ten Solver?', duvod: 'Karel na Slacku, bez tvé reakce a bez úkolu', citace: null, tier: null, zakaznik: '', url: 'https://s/1', slack_zdroj: 'slack:C:1' },
        { skupina: 7, skupina_nazev: 'Interní a úkoly', klic: '4', zdroj: 'ukol', udalost: '', titul: 'Billing', duvod: 'Termín', citace: null, tier: null, zakaznik: '', url: null }],
        slack: [{ klic: 'C:1', kanal: 'C1', kanal_nazev: '#support_management', autor_jmeno: 'Karel Vůjtek', text: 'Dneska ten Solver?', kdy: '2026-10-29 12:27:13', poradi: 0, url: 'https://s/1', ma_ukol: false },
            { klic: 'G:2', kanal: 'G1', kanal_nazev: 'Skupinová zpráva', autor_jmeno: 'Jitka', text: 'Novější ve skupině', kdy: '2026-10-30 16:39:00', poradi: 2, url: 'https://s/2', ma_ukol: false },
            { klic: 'G:1', kanal: 'G1', kanal_nazev: 'Skupinová zpráva', autor_jmeno: 'Milan', text: 'Starší ve skupině', kdy: '2026-10-30 09:00:00', poradi: 2, url: 'https://s/3', ma_ukol: false }],
        sber: { kdy: '2026-10-29 21:00:00', ticketu: 51, slack: 1 } },
    ukoly: { ukoly: [
        { id: 10, title: 'Infra témata', status: 'open', quadrant: 'urgent_important', due_date: '2026-10-21', ceka_na: null, pripomenout: null },
        { id: 11, title: 'Fix filtrů', status: 'open', quadrant: 'other', due_date: null, ceka_na: 'Ilia', pripomenout: '2026-11-03' }] },
    kalendar: { connected: true, events: [
        { title: '1:1 — René', date: iso(dnes), time: '23:00', allDay: false, start: iso(dnes) + 'T23:00:00', end: iso(dnes) + 'T23:30:00', durationH: 0.5, rsvp: 'accepted' },
        { title: 'Provozní meeting', date: iso(dnes), time: '23:30', allDay: false, start: iso(dnes) + 'T23:30:00', end: iso(dnes) + 'T23:45:00', durationH: 0.25, rsvp: 'needsAction' },{ title: 'Z operations: L1 team talk', date: iso(dnes), time: '23:10', allDay: false, start: iso(dnes) + 'T23:10:00', end: iso(dnes) + 'T23:40:00', durationH: 0.5, rsvp: 'accepted' }] },
    tym: { od: '2026-10-01', do: '2026-10-29', dni: 30, tym: { n: 2700, rutina: 0.34, stredni: 0.27, slozite: 0.39, vaha: 2.05, akt: 11, inc: 0.3, sr: 0.5, sla: 0.1, mi: 3, predano: 0.14, klicovi: 0.2, temata: { telefonie: 0.13 }, kanaly: { email: 0.7, hovor: 0.2, chat: 0.1 } }, median: { tk: 9, kom: 7, koment: 4, cas: 3, zatez: 25, prideleno: 2, mimo: 0.05, slack: 2.5 }, lide: [
        { login: 'rene_heryan', jmeno: 'René Heryán', osoba: 'René Heryán', kapacita: 0.8, dnu: 20, tk: 10, kom: 8, koment: 4, cas: 3, index: 1.1, stav: 'ok', trend: 0.12, ohlasy: { pochvala: 2 }, zatez: 40, tickety: 21, zatez_prum: 35, prideleno: 2.5, bez_prace: 0, nalada: { hodnota: 4, kdy: '2026-10-20', predtim: 2 },
            sla_vina: 3, dluzne: 6, dluzne_prum: 4.2, slack: { zprav: 60, vlakna: 20, sdileni: 4, kanalu: 7 }, kontext: { zvedacka: 3, support_week: 7 },
            signal: { stav: 'pokles', prumer: 1.2, sd: 0.1, posledni: 0.95 }, rozbor: ['Dostává o 31 % víc ticketů <b>'],
            rozpad: { n: 241, rutina: 0.33, stredni: 0.25, slozite: 0.42, vaha: 2.09, akt: 10.9, inc: 0.3, sr: 0.5, sla: 0.1, mi: 0, predano: 0.27, klicovi: 0.2, temata: { telefonie: 0.2 }, kanaly: { email: 0.7, hovor: 0.2, chat: 0.1 } }, vyhoreni: { stav: 'riziko', duvody: ['mimo pracovní dobu 20 % aktivit (tým 5 %)', 'aspoň 120 dní bez volna <x>'] } },
        { login: 'x_y', jmeno: 'Flákač <b>', osoba: null, kapacita: 1, dnu: 18, tk: 4, kom: 3, koment: 1, cas: 1, index: 0.45, stav: 'vyrazne', trend: -0.3, ohlasy: { stiznost: 1 }, zatez: 10, tickety: 6, zatez_prum: null, prideleno: 1, bez_prace: 4, nalada: null, signal: { stav: 'zlepseni', prumer: 0.4, sd: 0.05, posledni: 0.6 }, vyhoreni: { stav: 'ok', duvody: [] } },
        { login: 'nov', jmeno: 'Nováček', osoba: null, kapacita: 1, dnu: 2, tk: 1, kom: 1, koment: 0, cas: 0, index: null, stav: 'malo_dat', trend: null, ohlasy: {}, zatez: null, tickety: null, zatez_prum: null, prideleno: null, bez_prace: null, nalada: null }] },
    lide: { lide: [{ name: 'René Heryán', posledni: '2026-10-20', zapisu: 6, otevrenych: 0 }] },
    strategicky: { vybrany: null, kandidati: [{ id: 20, title: 'AI workshop', created_at: '2026-09-01', dni: 58 }] },
    osoba: { jmeno: 'René Heryán', login: 'rene_heryan', popis: '',
        review: { h2_od: '2026-07', cile: [{ text: 'Standard dost dobré <i>', metriky: ['akt'] }, { text: 'Kancelář', metriky: [] }],
            casti: { citlive: ['INTERNÍ: neotvírat <x>'], silne: ['Komunikace #868551'], rozvoj: ['Efektivita'], skore: [{ kategorie: 'Overall', skore: '+1', poznamka: 'ok' }, { kategorie: 'Time', skore: '−1', poznamka: '' }],
                kpi: [], csat: ['4.6'], tickety: [], dojem: [], kontext: [], rozhovor: [] },
            metriky: { sr_fr: { klic: 'sr_fr', nazev: 'SR první odpověď do 3 h', h1: 73.9, h2: 60.2, smer: 'horsi', dukaz: 'sr_fr', mesice: [{ m: '2026-06', v: 73.9, n: 40, tym: null }, { m: '2026-07', v: 60.2, n: 30, tym: null }, { m: '2026-08', v: null, n: 0, tym: null }] },
                onenon: { klic: 'onenon', nazev: '1on1 schůzky', h1: 1, h2: 1.5, smer: 'lepsi', dukaz: null, mesice: [{ m: '2026-06', v: 1, n: 1, tym: null }, { m: '2026-07', v: 2, n: 2, tym: null }] } } },
        priprava: { posledni: '2026-09-01', dni: 60, ohlasy: [{ ticket: 5, druh: 'pochvala', citace: 'díky <3', titul: '' }],
            tym: { index: 1.1, signal: { stav: 'pokles', prumer: 1.2, sd: 0.1, posledni: 0.9 }, vyhoreni: { stav: 'sleduj', duvody: ['zátěž 1,4× medián'] }, sla_vina: 2, dluzne: 3, nalada: { hodnota: 2, kdy: '2026-09-01' } } }, profil: [{ popis: 'Silné stránky', text: 'technika' }], zapisy: [{ id: 51, meeting_date: '2026-10-20', notes: 'text <b>', action_items: [{ text: 'a', done: false }], tags: ['rozvoj'] }],
        denik: { login: 'rene_heryan', dny: [{ den: '2026-10-29', aktivit: 40, uzavreno: 6, emaily_in: 9, druhy: { EMAIL: 20 } }, { den: '2026-10-30', aktivit: 12, uzavreno: 1, emaily_in: 3, druhy: {} }],
            historie: [{ mesic: '2026-09', dnu: 20, tk: 9.5, kom: 7, koment: 4, cas: 3.1, index: 1.05, stav: 'ok', median: { tk: 9, kom: 7, koment: 4, cas: 3 } },
                { mesic: '2026-10', dnu: 21, tk: 5, kom: 4, koment: 2, cas: 1.5, index: 0.55, stav: 'vyrazne', median: { tk: 9, kom: 7, koment: 4, cas: 3 } }],
            tydny: { rada: [{ tyden: '2026-10-12', index: 1.1, dnu: 5 }, { tyden: '2026-10-19', index: 0.4, dnu: 1 }, { tyden: '2026-10-26', index: 0.9, dnu: 4 }], signal: { stav: 'stabilni', prumer: 1, sd: 0.1, posledni: 0.95 } },
            udalosti: [{ ticket: 961679, titul: 'Směrování', kdy: '2026-10-30 09:10:00', druh: 'pochvala', skore: '0.92', citace: 'Moc děkujeme <3' }, { ticket: 961640, titul: 'Event', kdy: '2026-10-29 14:00:00', druh: 'stiznost', skore: '0.81', citace: 'Pořád to nefunguje' }] },
        kontext: [{ zdroj: 'Review 2026 H1', text: '# Heryán René\nSilné stránky <i>' }, { zdroj: 'Lidé a rozvoj', text: 'René Heryán — rene_heryan — Rendo' }] },
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
    t(r.html.includes('tg-slackpop') && r.html.includes('Dneska ten Solver?'), 'Slack bez reakce je rozbalený hned, bez kliknutí (30. 10.)');
    t((r.html.match(/data-akce="slack-hotovo-vse"/g) || []).length === 1 && r.html.includes('data-kanal="G1"'), 'skupinová konverzace má jedno ✓✓ na vše, jednotlivá zpráva ne');
    t(r.html.indexOf('Starší ve skupině') < r.html.indexOf('Novější ve skupině') && (r.html.match(/tg-sub-radek/g) || []).length === 1, 'hlavní řádek nejstarší, novější odsazený');
    t((r.html.match(/data-akce="slack-hotovo" /g) || []).length === 3, 'každá zpráva má dál vlastní ✓');
    t(r.html.includes('data-akce="slack-ukol"'), 'C.17: tlačítko + úkol u Karlovy zprávy');
    t(r.html.includes('Strategický blok') && r.html.includes('AI workshop'), 'Teď: strategický blok');
    t(r.html.includes('Čekám na') && r.html.includes('Ilia'), 'Teď: čekám na');
    t(r.html.includes('Servis auta'), 'Teď: osobní úkol');
    t(r.html.includes('potvrzeno · připrav se') && r.html.includes('nepotvrzeno'), 'Dnes: RSVP');
    t(r.html.includes('Příprava — René Heryán'), 'Příprava 1:1 podle potvrzené schůzky');
    t(!r.html.includes('<SMS>') && !r.html.includes('undefined'), 'Teď: žádný neescapovaný text ani undefined');
    t(r.banner.hidden === true, 'připojeno: bez banneru');
    t(r.html.includes('Z operations: L1 team talk') && !r.html.includes('Jen záloha hetzner'), 'Dnes: pracovní kalendář z operations má přednost');

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
    t(r.html.includes('Z portfolia') && r.html.includes('Review 2026 H1') && r.html.includes('Silné stránky &lt;i&gt;'), 'Detail osoby: kontext z portfolia, escapovaný');
    t(r.html.includes('Aktivita') && r.html.includes('tg-graf') && r.html.includes('Pochvala') && r.html.includes('Moc děkujeme &lt;3') && r.html.includes('961640'), 'Detail osoby: deník — graf, události s citací a ticketem');

    r = await spust('#ted', { zavreno: true });
    const h = new Date().getHours(), den = new Date().getDay();
    if (den === 0 || den === 6 || h < 7 || h >= 18) t(r.html.includes('Práce je na dnes zavřená') && r.html.includes('Zítra začni'), 'Po pracovní době: zavřeno');

    r = await spust('#ted', { bezVpn: true });
    t(r.banner.hidden === false && r.banner.innerHTML.includes('VPN'), 'Bez VPN: banner');
    t(r.html.includes('Servis auta') && !r.html.includes('Výpadek'), 'Bez VPN: jen osobní');
    t(r.html.includes('Jen záloha hetzner'), 'Bez VPN: kalendář ze zálohy na hetzneru');

    r = await spust('#ted', { klic: false });
    t(r.banner.innerHTML.includes('Propojit'), 'Bez klíče: návod na propojení');
    // Tým (deník v2, 30. 10.): srovnání z historie, kdo je pod týmem, odkaz do 1on1, escapování
    r = await spust('#tym');
    r = await spust('#tym');
    t(r.html.includes('Tým CZ-L1') && r.html.includes('Medián týmu'), 'Tým: tabulka s mediánem');
    t(r.html.includes('href="#osoba=Ren%C3%A9%20Hery%C3%A1n"'), 'Tým: jméno vede na detail 1on1');
    t(r.html.includes('Flákač &lt;b&gt;') && !r.html.includes('Flákač <b>'), 'Tým: jména escapovaná');
    t(r.html.includes('tr class="vyrazne"') && r.html.includes('výrazně pod') && r.html.includes('málo dat'), 'Tým: stavy výrazně pod a málo dat');
    t(r.html.includes('▼ -0,30') && r.html.includes('▲ +0,12'), 'Tým: trend proti minulému období');
    t(r.html.includes('router 0,8') && r.html.includes('★ 2') && r.html.includes('! 1'), 'Tým: úvazek a ohlasy zákazníků');
    t(r.html.indexOf('René Heryán') < r.html.indexOf('Flákač'), 'Tým: pořadí podle API (nejlepší nahoře)');
    t(r.html.includes('tg-zatez hot') && r.html.includes('(35)'), 'Tým: zátěž nad mediánem zvýrazněná, průměr období');
    t(r.html.includes('tg-nalada n4') && r.html.includes('4/5') && r.html.includes('tg-trend nahoru">▲'), 'Tým: nálada z 1on1 se šipkou');
    t(r.html.includes('tg-tag hot">4<'), 'Tým: 4 pracovní dny bez práce zvýrazněné');
    // deník v3 (31. 10.): signál, kvalita, Slack, pohoda, souhrn „na co se podívat“ (riziko první, zlepšení k pochvale)
    t(r.html.includes('trvalý pokles') && r.html.includes('SLA 3') && r.html.includes('tg-tag hot">dluží 6'), 'Tým: signál a kvalita');
    t(r.html.includes('>3,0</span>') && r.html.includes('↗4') && r.html.includes('zv 3 sw'), 'Tým: Slack na den s prací, sdílení, kontext zvedačky');
    t(r.html.includes('class="tg-pozor"') && r.html.indexOf('>riziko<') < r.html.indexOf('>zlepšení<') && r.html.includes('příležitost pochválit'), 'Tým: souhrn seřazený podle naléhavosti');
    t(r.html.includes('&lt;x&gt;') && !r.html.includes('volna <x>'), 'Tým: důvody escapované');
    t(r.html.includes('data-akce="tym-rozbal" data-l="rene_heryan"') && !r.html.includes('tg-rozbal-radek'), 'Tým: rozpad zabalený, tlačítko rozbalit');
    t(typeof r.ctx.renderRozpad === 'function', 'Tým: renderer rozpadu');
    if (r.ctx.renderRozpad) {
        const h = r.ctx.renderRozpad(DATA.tym.lide[0], DATA.tym.tym, 30);
        t(h.includes('33 % / 25 % / 42 %') && h.includes('34 % / 27 % / 39 %') && h.includes('Předáno dál') && h.includes('27 %'), 'Rozpad: mix náročnosti člověk × tým');
        t(h.includes('&lt;b&gt;') && !h.includes('ticketů <b>'), 'Rozpad: rozbor escapovaný');
    }
    // důkazy na klik (31. 10.): čísla jsou tlačítka, panel ukáže zdroj, pravidlo a řádky s odkazy (escapované)
    t(r.html.includes('data-akce="dukazy" data-l="rene_heryan" data-co="sla_vina"') && r.html.includes('data-co="dluzne"') && r.html.includes('data-co="prideleno"') && r.html.includes('data-co="slack"') && r.html.includes('data-co="bez_prace"'), 'Tým: čísla rozkliknutelná na důkazy');
    if (r.ctx.renderDukazy) {
        const h = r.ctx.renderDukazy({ nadpis: 'Přidělené tickety', login: 'rene_heryan', celkem: 2, od: '2026-10-01', do: '2026-10-31', zdroj: 'Ticket Router', pravidlo: 'Přiděleno <x>', sloupce: ['Ticket', 'Název'], radky: [[{ t: '#1', u: 'https://daktela.daktela.com/tickets/update/1' }, 'a <b>']] });
        t(h.includes('href="https://daktela.daktela.com/tickets/update/1"') && h.includes('Jak se počítá') && h.includes('&lt;x&gt;') && h.includes('a &lt;b&gt;') && h.includes('tg-duk-filtr'), 'Důkazy: odkazy, zdroj, pravidlo, filtr, escapování');
    } else t(false, 'Důkazy: renderer');
    r = await spust('#osoba=Ren%C3%A9%20Hery%C3%A1n');
    t(r.html.includes('Týdny') && r.html.includes('tg-pasmo') && r.html.includes('v slaby'), 'Detail: týdny s pásmem, slabý týden světle');
    t(r.html.includes('name="m"') && r.html.includes('5 — výborná'), 'Detail: zápis 1on1 s náladou');
    t(r.html.includes('Dlouhodobě') && r.html.includes('tg-median') && r.html.includes('v vyrazne'), 'Detail: dlouhodobý graf po měsících s mediánem');
    t(r.html.indexOf('Dlouhodobě') < r.html.indexOf('2026-09'), 'Detail: tabulka měsíců od nejnovějšího');
    // 1on1 obohacené (31. 10.): příprava z dat, cíle H2 proti H1 s důkazy, hodnocení po částech, citlivé zvlášť
    t(r.html.includes('Příprava na 1on1') && r.html.indexOf('Příprava na 1on1') < r.html.indexOf('Cíle na H2'), 'Detail: příprava nahoře, pak cíle');
    t(r.html.includes('před 60 dny — domluvit') && r.html.includes('Projít 1 otevřených') && r.html.includes('— co se změnilo?') && r.html.includes('Ocenit: ') && r.html.includes('pod vlastním průměrem'), 'Příprava: body z dat (dlouho bez 1on1, úkoly, horší/lepší cíl, signál)');
    t(r.html.includes('3 zákazníků čeká') && r.html.includes('Pochválit') && r.html.includes('díky &lt;3') && r.html.includes('Nálada na minulém 1on1 2/5'), 'Příprava: dlužné, pochvala escapovaná, nálada');
    t(/data-co="sla_vina" data-d="30"/.test(r.html), 'Příprava: kvalita rozkliknutelná za 30 dní, ne za období Týmu');
    t(r.html.includes('Citlivé — neotvírat sám') && r.html.includes('neotvírat &lt;x&gt;'), 'Příprava: citlivé zvlášť a escapované');
    t(r.html.includes('data-co="sr_fr"') && r.html.includes('74 %') && r.html.includes('60 %') && r.html.includes('>horší<') && r.html.includes('tg-met-sl h2'), 'Cíle: metrika H1 → H2, směr, měsíce, důkazy');
    t(r.html.includes('tg-met-box') && r.html.includes('daty neověřitelné') && r.html.includes('dost dobré &lt;i&gt;'), 'Cíle: 1on1 bez důkazu, neměřitelný cíl označen, escapování');
    t(r.html.includes('Hodnocení H1 2026') && r.html.includes('tickets/update/868551') && r.html.includes('class="tg-plus">+1') && r.html.includes('class="tg-minus">−1'), 'Hodnocení: části, tickety jako odkazy, skóre barevně');
    process.exit(chyb ? 1 : 0);
})().catch(e => { console.log('CHYBA za běhu: ' + e.stack.split('\n').slice(0, 3).join(' | ')); process.exit(1); });
