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
process.exit(chyb ? 1 : 0);
