// Logika nového GUI (29. 10.) — node tests/logika_test.js, výstup jen chyby
const L = require('../js/logika.js');
let chyb = 0;
const t = (ok, co) => { if (!ok) { chyb++; console.log('CHYBA: ' + co); } };
const d = (s) => new Date(s);

// Režim: práce zavřená mimo 7–18 a o víkendu, focus po dopoledne / pá odpoledne nebo blok v kalendáři
t(L.rezim(d('2026-10-29T21:30:00'), []) === 'zavreno', 'večer zavřeno');
t(L.rezim(d('2026-10-31T10:00:00'), []) === 'zavreno', 'sobota zavřeno');
t(L.rezim(d('2026-10-29T10:00:00'), []) === 'prace', 'čtvrtek dopoledne práce');
t(L.rezim(d('2026-11-02T09:00:00'), []) === 'focus', 'pondělí dopoledne focus');
t(L.rezim(d('2026-10-30T13:00:00'), []) === 'focus', 'pátek odpoledne focus');
t(L.rezim(d('2026-10-29T14:30:00'), [{ title: 'Soustředěná práce', start: '2026-10-29T14:00:00', end: '2026-10-29T15:00:00', allDay: false }]) === 'focus', 'focus blok v kalendáři');
t(L.rezim(d('2026-10-29T21:30:00'), [], { ukazPraci: true }) === 'prace', 'přesto ukázat práci');
t(L.rezim(d('2026-11-02T09:00:00'), [], { konecFocus: true }) === 'prace', 'ukončený focus');

// Teď: hlavní věc + všichni čekající zákazníci, jinak 2 další
const p = (sk, k) => ({ skupina: sk, klic: String(k) });
let r = L.ted([p(2, 1), p(2, 2), p(3, 3), p(3, 4), p(6, 5), p(7, 6)]);
t(r.hlavni.klic === '1', 'hlavní = první');
t(r.cekaji === true && r.dalsi.length === 3, 'víc čekajících zákazníků → všichni');
t(r.zbytek.length === 2, 'zbytek TOP 10');
r = L.ted([p(2, 1), p(6, 2), p(6, 3), p(7, 4), p(7, 5)]);
t(r.cekaji === false && r.dalsi.length === 2 && r.zbytek.length === 2, 'jeden čekající → 1 + 2 další');
r = L.ted([]);
t(r.hlavni === null && r.dalsi.length === 0, 'prázdný seznam');
r = L.ted([p(2, 1), p(2, 2), p(2, 3), p(2, 4), p(2, 5), p(2, 6), p(2, 7), p(2, 8), p(2, 9), p(2, 10), p(2, 11), p(2, 12)]);
t(r.dalsi.length === 11 && r.zbytek.length === 0, 'všichni čekající i nad 10');
r = L.ted([p(8, 1), p(8, 2)]);
t(r.hlavni.klic === '1' && r.dalsi.length === 1, 'jen hlídat — pořád ukáže');

// Značky: nové / posunuto (stabilita bez přeskládání pod rukama)
const z = L.znacky([p(2, 'a'), p(2, 'b'), p(2, 'c')], { a: 0, c: 4 });
t(z.b === 'nove' && z.c === 'nahoru' && z.a === undefined, 'nové a posunuté nahoru');
t(Object.keys(L.znacky([p(2, 'a')], null)).length === 0, 'první načtení bez značek');

// Slack souhrn
const s = L.slackSouhrn([{ poradi: 0 }, { poradi: 0 }, { poradi: 1 }, { poradi: 2 }]);
t(s.karel === 2 && s.kanaly === 1 && s.dm === 1, 'slack souhrn');

// Datum
t(L.datum('2026-10-07') === '7. 10.', 'datum krátce');
t(L.esc('<a href="x">') === '&lt;a href=&quot;x&quot;&gt;', 'escape HTML');
// Dlaždice přehledu
const dl = L.dlazdice({ polozky: [p(1, 'a'), p(2, 'b'), p(2, 'c'), p(3, 'd'), p(6, 'e')], slack: [{}, {}] },
    [{ status: 'open', due_date: '2026-10-01' }, { status: 'open', due_date: null }], [{ status: 'open', due_date: '2026-10-28' }], [{ otevrenych: 2 }, { otevrenych: 1 }], '2026-10-29');
t(dl.cekaji === 3 && dl.dluh === 1 && dl.slack === 2 && dl.poTerminu === 2 && dl.jedenOn1 === 3, 'dlaždice počty');
t(L.dlazdice(null, [], [], [], '2026-10-29').cekaji === 0, 'dlaždice bez pracovních dat');

// Tabule: sloupec úkolu a přesun tažením
t(L.sloupec({ quadrant: 'important' }, 'work') === 'important', 'sloupec podle kvadrantu');
t(L.sloupec({ quadrant: 'important', ceka_na: 'Ilia' }, 'work') === 'ceka', 'čekám na má vlastní sloupec');
t(L.sloupec({ quadrant: 'urgent' }, 'personal') === 'osobni', 'osobní zvlášť');
t(JSON.stringify(L.presun({ quadrant: 'other' }, 'work', 'urgent_important')) === JSON.stringify({ quadrant: 'urgent_important' }), 'přesun mezi kvadranty');
t(JSON.stringify(L.presun({ quadrant: 'other', ceka_na: 'X', pripomenout: '2026-11-01' }, 'work', 'important')) === JSON.stringify({ quadrant: 'important', ceka_na: null, pripomenout: null }), 'z čekám na zpět do kvadrantu smaže čekání');
t(L.presun({ quadrant: 'other' }, 'work', 'ceka').zeptat === true, 'do čekám na → zeptat se kdo a kdy');
t(L.presun({ quadrant: 'other' }, 'work', 'osobni') === null && L.presun({ quadrant: 'other' }, 'personal', 'important') === null, 'mezi prací a osobním nejde');
t(L.presun({ quadrant: 'important' }, 'work', 'important') === null, 'stejný sloupec = nic');
process.exit(chyb ? 1 : 0);
