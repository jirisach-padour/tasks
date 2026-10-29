// Čisté funkce nového GUI Tasks (29. 10.). V prohlížeči window.TL, v node module.exports (tests/logika_test.js).
(function (exp) {
    'use strict';

    const FOCUS_RE = /focus|soustředěn|soustreden|deep work/i;
    const CEKA = [1, 2, 3];   // SLA v hodinách, zákazník čeká, dlužíme

    /** 'zavreno' | 'focus' | 'prace'. Po–pá 7–18 práce; po < 12 a pá ≥ 12 focus; focus i blok v kalendáři. */
    function rezim(ted, udalosti, volby) {
        volby = volby || {};
        const den = ted.getDay(), h = ted.getHours();
        if ((den === 0 || den === 6 || h < 7 || h >= 18) && !volby.ukazPraci) return 'zavreno';
        if (volby.konecFocus) return 'prace';
        if ((den === 1 && h < 12) || (den === 5 && h >= 12)) return 'focus';
        const ms = ted.getTime();
        for (const e of udalosti || []) {
            if (e.allDay || !FOCUS_RE.test(e.title || '')) continue;
            if (new Date(e.start).getTime() <= ms && ms < new Date(e.end).getTime()) return 'focus';
        }
        return 'prace';
    }

    /** Rozdělení TOP: hlavní, další (všichni čekající zákazníci, nebo 2), zbytek do 10. */
    function ted(polozky) {
        const p = polozky || [];
        if (!p.length) return { hlavni: null, dalsi: [], zbytek: [], cekaji: false };
        const hlavni = p[0];
        const cekajici = p.slice(1).filter(x => CEKA.includes(x.skupina));
        if (cekajici.length >= 2) {
            const zbyle = p.slice(1).filter(x => !CEKA.includes(x.skupina));
            return { hlavni, dalsi: cekajici, zbytek: zbyle.slice(0, Math.max(0, 9 - cekajici.length)), cekaji: true };
        }
        return { hlavni, dalsi: p.slice(1, 3), zbytek: p.slice(3, 10), cekaji: false };
    }

    /** Značky proti minulému pořadí {klic: index}: 'nove' | 'nahoru'. Bez minulého stavu nic. */
    function znacky(polozky, minule) {
        const z = {};
        if (!minule) return z;
        polozky.forEach((x, i) => {
            if (!(x.klic in minule)) z[x.klic] = 'nove';
            else if (minule[x.klic] - i >= 2) z[x.klic] = 'nahoru';
        });
        return z;
    }

    function slackSouhrn(zpravy) {
        const s = { karel: 0, kanaly: 0, dm: 0 };
        for (const z of zpravy || []) s[['karel', 'kanaly', 'dm'][Number(z.poradi)] || 'kanaly']++;
        return s;
    }

    function datum(iso) {
        if (!iso) return '';
        const [, m, d] = String(iso).slice(0, 10).split('-');
        return Number(d) + '. ' + Number(m) + '.';
    }

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }

    Object.assign(exp, { rezim, ted, znacky, slackSouhrn, datum, esc });
})(typeof module !== 'undefined' ? module.exports : (window.TL = {}));
