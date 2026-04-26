<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tasks — Ukázka aplikace</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%231B3468'/><rect x='6' y='6' width='9' height='9' rx='2' fill='%23E05C4E'/><rect x='17' y='6' width='9' height='9' rx='2' fill='rgba(255,255,255,0.4)'/><rect x='6' y='17' width='9' height='9' rx='2' fill='rgba(255,255,255,0.4)'/><rect x='17' y='17' width='9' height='9' rx='2' fill='rgba(255,255,255,0.15)'/></svg>">
<style>
:root{
  --red:#E05C4E;--red-hover:#C94F42;--navy:#1B3468;--navy-light:#2a4a80;
  --grey-bg:#F4F5F7;--grey-border:#DDE1E7;--grey-text:#5E6778;
  --white:#FFFFFF;--radius:8px;
  --font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--font);font-size:14px;background:var(--grey-bg);color:var(--navy)}

/* ── HERO ─────────────────────────────────── */
.hero{background:linear-gradient(135deg,#1B3468 0%,#0f2044 100%);padding:60px 24px 48px;text-align:center;position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")}
.hero-icon{width:64px;height:64px;margin:0 auto 24px;display:block}
.hero h1{font-size:36px;font-weight:800;color:#fff;letter-spacing:-.5px;line-height:1.1}
.hero h1 span{color:var(--red)}
.hero p{color:rgba(255,255,255,.65);font-size:16px;margin-top:12px;max-width:520px;margin-left:auto;margin-right:auto;line-height:1.6}
.hero-cta{display:inline-flex;align-items:center;gap:8px;margin-top:28px;background:var(--red);color:#fff;padding:13px 28px;border-radius:var(--radius);font-size:15px;font-weight:700;text-decoration:none;transition:background .15s}
.hero-cta:hover{background:var(--red-hover)}
.hero-sub{display:flex;align-items:center;gap:6px;justify-content:center;margin-top:14px;color:rgba(255,255,255,.4);font-size:12px}
.hero-sub span{width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.25)}

/* ── NAV DOTS ─────────────────────────────── */
.nav-dots{display:flex;justify-content:center;gap:8px;padding:20px;position:sticky;top:0;z-index:100;background:rgba(244,245,247,.95);backdrop-filter:blur(8px);border-bottom:1px solid var(--grey-border)}
.nav-dot{padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid var(--grey-border);background:var(--white);color:var(--grey-text);cursor:pointer;transition:all .15s;text-decoration:none}
.nav-dot:hover,.nav-dot.active{background:var(--navy);color:#fff;border-color:var(--navy)}

/* ── SECTION ──────────────────────────────── */
.section{max-width:1100px;margin:0 auto;padding:56px 24px}
.section-header{text-align:center;margin-bottom:40px}
.section-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--red);margin-bottom:8px}
.section-header h2{font-size:28px;font-weight:800;color:var(--navy);letter-spacing:-.3px}
.section-header p{color:var(--grey-text);font-size:15px;margin-top:8px;max-width:540px;margin-left:auto;margin-right:auto;line-height:1.6}
.divider{border:none;border-top:1px solid var(--grey-border);max-width:1100px;margin:0 auto}

/* ── FEATURE CARDS ────────────────────────── */
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px}
.feat-card{background:var(--white);border:1px solid var(--grey-border);border-radius:12px;padding:24px;transition:box-shadow .2s,transform .2s}
.feat-card:hover{box-shadow:0 8px 32px rgba(27,52,104,.1);transform:translateY(-2px)}
.feat-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:16px}
.feat-icon.red{background:#FEE8E7}
.feat-icon.navy{background:#E0E8F5}
.feat-icon.amber{background:#FFF4E0}
.feat-icon.green{background:#E3F5E8}
.feat-icon.purple{background:#F0E8FF}
.feat-icon.teal{background:#E0F5F5}
.feat-card h3{font-size:16px;font-weight:700;margin-bottom:8px}
.feat-card p{font-size:13px;color:var(--grey-text);line-height:1.6}
.feat-badge{display:inline-block;margin-top:10px;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;background:var(--grey-bg);color:var(--grey-text)}
.feat-badge.new{background:#E3F5E8;color:#2E7D3F}

/* ── INTERACTIVE DEMO ─────────────────────── */
.demo-wrapper{background:var(--white);border:1px solid var(--grey-border);border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}
.demo-header{background:linear-gradient(135deg,#1B3468 0%,#152a52 100%);padding:12px 20px 0}
.demo-header-top{display:flex;align-items:center;justify-content:space-between;padding-bottom:12px}
.demo-title{color:#fff;font-size:16px;font-weight:700}
.demo-title small{color:rgba(255,255,255,.5);font-size:11px;font-weight:400;margin-left:6px}
.demo-actions{display:flex;gap:6px}
.demo-btn-sm{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:6px;font-size:11px;font-weight:600;padding:5px 10px;cursor:pointer;transition:background .15s}
.demo-btn-sm:hover{background:rgba(255,255,255,.25)}
.demo-tabs{display:flex;gap:0}
.demo-tab{padding:10px 18px;font-size:12px;font-weight:600;border:none;background:transparent;color:rgba(255,255,255,.5);cursor:pointer;border-bottom:3px solid transparent;transition:all .15s;white-space:nowrap}
.demo-tab.active{color:#fff;border-bottom:3px solid var(--red)}
.demo-tab:hover:not(.active){color:rgba(255,255,255,.8)}
.demo-body{display:grid;grid-template-columns:200px 1fr 200px;gap:14px;padding:16px;background:var(--grey-bg);min-height:420px}
.demo-panel{background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:14px;font-size:12px}
.demo-panel+.demo-panel{margin-top:12px}
.dp-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--grey-text);margin-bottom:10px}
.kpi-mini{display:flex;gap:8px;margin-bottom:10px}
.kpi-box{flex:1;background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:6px;padding:8px;text-align:center}
.kpi-box .val{font-size:18px;font-weight:800;color:var(--navy)}
.kpi-box .lbl{font-size:9px;color:var(--grey-text);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-top:2px}
.cl-mini-item{display:flex;align-items:center;gap:6px;padding:5px 0;border-bottom:1px solid var(--grey-border);font-size:11px}
.cl-mini-item:last-child{border-bottom:none}
.cl-mini-item input{accent-color:var(--red);flex-shrink:0}
.cl-mini-item.done span{text-decoration:line-through;color:var(--grey-text)}

/* matrix */
.mini-matrix{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.mq{background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:10px;min-height:140px;transition:background .15s}
.mq.drag-over{background:#f0f4ff;border-color:var(--navy)}
.mq.mq-q1{border-left:3px solid var(--red)}
.mq.mq-q2{border-left:3px solid var(--navy)}
.mq.mq-q3{border-left:3px solid #E8A020}
.mq.mq-q4{border-left:3px solid var(--grey-border)}
.mq-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--grey-text);margin-bottom:8px;display:flex;align-items:center;justify-content:space-between}
.mq-label .cnt{background:var(--grey-bg);color:var(--grey-text);font-size:9px;font-weight:700;padding:1px 6px;border-radius:10px}
.mq-q1 .mq-label .cnt{background:#FEE8E7;color:#E63327}
.tc{background:var(--white);border:1px solid var(--grey-border);border-radius:5px;padding:6px 8px;margin-bottom:5px;font-size:11px;cursor:grab;transition:box-shadow .15s,opacity .15s;display:flex;align-items:flex-start;gap:6px;user-select:none}
.tc:active{cursor:grabbing}
.tc.dragging{opacity:.4;box-shadow:0 4px 16px rgba(0,0,0,.15)}
.tc-check{accent-color:var(--red);flex-shrink:0;width:13px;height:13px;margin-top:1px}
.tc-body{flex:1;min-width:0}
.tc-title{font-weight:500;line-height:1.3}
.tc-meta{font-size:10px;color:var(--grey-text);margin-top:2px;display:flex;gap:4px;align-items:center;flex-wrap:wrap}
.tc-badge{font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px}
.tc-badge.w{background:#E0E8F5;color:#1B3468}
.tc-badge.p{background:#E8F5E9;color:#2E7D3F}
.tc-badge.d{background:#FFF4E0;color:#A06000}
.tc-badge.ai{background:#E8F0FE;color:#1a56db}
.overdue{color:#E63327;font-weight:700}
.soon{color:#E8A020;font-weight:700}
.quick-chip{display:inline-flex;align-items:center;gap:4px;background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:600;padding:3px 10px;border-radius:20px;border:1px solid rgba(255,255,255,.2)}

/* tickets sidebar */
.ticket-mini{display:flex;align-items:center;gap:6px;padding:5px 0;border-bottom:1px solid var(--grey-border);font-size:11px}
.ticket-mini:last-child{border-bottom:none}
.stage-chip{font-size:9px;font-weight:700;padding:1px 5px;border-radius:3px;flex-shrink:0}
.stage-chip.OPEN{background:#E3F5E8;color:#2E7D3F}
.stage-chip.WAIT{background:#FFF4E0;color:#A06000}
.stage-chip.CLOSE{background:var(--grey-bg);color:var(--grey-text)}
.cal-mini-item{display:flex;gap:7px;padding:5px 0;border-bottom:1px solid var(--grey-border);font-size:11px;align-items:center}
.cal-mini-item:last-child{border-bottom:none}
.cal-mini-time{color:var(--grey-text);font-weight:600;font-size:10px;min-width:32px;flex-shrink:0}

/* AI modal overlay */
.ai-overlay{display:none;position:absolute;inset:0;background:rgba(0,0,0,.5);z-index:50;align-items:center;justify-content:center;padding:20px;border-radius:16px}
.ai-overlay.show{display:flex}
.ai-modal{background:var(--white);border-radius:12px;padding:24px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(0,0,0,.2)}
.ai-modal h3{font-size:15px;font-weight:700;margin-bottom:4px;display:flex;align-items:center;gap:8px}
.ai-modal .subtitle{font-size:12px;color:var(--grey-text);margin-bottom:16px}
.ai-sugg{background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:8px;padding:12px;margin-bottom:8px}
.ai-sugg:last-of-type{margin-bottom:16px}
.ai-sugg-title{font-size:12px;font-weight:600}
.ai-sugg-reason{font-size:11px;color:var(--grey-text);margin-top:3px;line-height:1.5}
.ai-sugg-change{font-size:10px;margin-top:6px;display:flex;align-items:center;gap:6px}
.q-chip{font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px}
.q-chip.q1{background:#FEE8E7;color:#E63327}
.q-chip.q2{background:#E0E8F5;color:#1B3468}
.q-chip.q3{background:#FFF4E0;color:#A06000}
.q-chip.q4{background:var(--grey-bg);color:var(--grey-text)}
.ai-modal-btn{background:var(--red);color:#fff;border:none;border-radius:var(--radius);font-size:13px;font-weight:700;padding:9px 20px;cursor:pointer;margin-right:8px}
.ai-modal-btn.sec{background:var(--white);color:var(--navy);border:1px solid var(--grey-border)}
.ai-modal-btn:hover{opacity:.9}

/* quick capture overlay */
.qc-overlay{display:none;position:absolute;inset:0;background:rgba(0,0,0,.5);z-index:50;align-items:flex-start;justify-content:center;padding:60px 20px;border-radius:16px}
.qc-overlay.show{display:flex}
.qc-box{background:var(--white);border-radius:12px;padding:20px;width:100%;max-width:480px;box-shadow:0 8px 40px rgba(0,0,0,.25)}
.qc-box h4{font-size:14px;font-weight:700;margin-bottom:12px;display:flex;align-items:center;gap:6px}
.qc-box h4 kbd{font-size:10px;background:var(--grey-bg);border:1px solid var(--grey-border);padding:2px 6px;border-radius:4px;font-family:monospace;color:var(--grey-text);font-weight:400}
.qc-input{width:100%;height:40px;padding:0 12px;border:1px solid var(--grey-border);border-radius:var(--radius);font-size:14px;font-family:var(--font);outline:none;margin-bottom:10px}
.qc-input:focus{border-color:var(--navy)}
.qc-row{display:flex;gap:8px;flex-wrap:wrap}
.qc-select{flex:1;height:34px;padding:0 8px;border:1px solid var(--grey-border);border-radius:6px;font-size:12px;font-family:var(--font);outline:none}
.qc-submit{height:34px;padding:0 16px;background:var(--red);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer}
.qc-close{height:34px;padding:0 12px;background:var(--white);color:var(--navy);border:1px solid var(--grey-border);border-radius:6px;font-size:12px;font-weight:600;cursor:pointer}

.demo-relative{position:relative}
.demo-label{display:inline-flex;align-items:center;gap:5px;background:rgba(27,52,104,.08);color:var(--navy);font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;margin-bottom:20px}
.demo-label::before{content:'▶';font-size:9px}

/* ── SCREENSHOTS / FEATURE TABS ──────────── */
.feat-tabs{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-bottom:28px}
.feat-tab{padding:8px 18px;border-radius:20px;font-size:13px;font-weight:600;border:1px solid var(--grey-border);background:var(--white);color:var(--grey-text);cursor:pointer;transition:all .15s}
.feat-tab.active{background:var(--navy);color:#fff;border-color:var(--navy)}
.feat-panel{display:none}
.feat-panel.active{display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:center}
@media(max-width:700px){.feat-panel.active{grid-template-columns:1fr}}
.feat-panel-img{background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:12px;overflow:hidden}
.feat-panel-desc h3{font-size:20px;font-weight:800;margin-bottom:10px}
.feat-panel-desc p{font-size:14px;color:var(--grey-text);line-height:1.7;margin-bottom:16px}
.feat-list{list-style:none;display:flex;flex-direction:column;gap:8px}
.feat-list li{display:flex;align-items:flex-start;gap:10px;font-size:13px;color:var(--grey-text);line-height:1.5}
.feat-list li::before{content:'✓';color:var(--red);font-weight:800;flex-shrink:0;margin-top:1px}

/* ── QUICK STATS ──────────────────────────── */
.stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:56px}
.stat-card{background:var(--white);border:1px solid var(--grey-border);border-radius:12px;padding:20px 24px;text-align:center}
.stat-num{font-size:36px;font-weight:800;color:var(--navy)}
.stat-num span{color:var(--red)}
.stat-label{font-size:13px;color:var(--grey-text);margin-top:4px}

/* ── FOOTER ───────────────────────────────── */
.footer{background:var(--navy);padding:32px 24px;text-align:center;color:rgba(255,255,255,.4);font-size:12px}
.footer strong{color:rgba(255,255,255,.7)}

/* responsive demo body */
@media(max-width:900px){
  .demo-body{grid-template-columns:1fr;grid-template-rows:auto auto auto}
  .demo-sidebar-left,.demo-sidebar-right{display:none}
}
@media(max-width:600px){
  .hero h1{font-size:26px}
  .section{padding:40px 16px}
  .mini-matrix{grid-template-columns:1fr}
}
</style>
</head>
<body>

<!-- HERO -->
<section class="hero">
  <svg class="hero-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="64" height="64" rx="12" fill="#1B3468"/>
    <rect x="10" y="10" width="19" height="19" rx="4" fill="#E05C4E"/>
    <rect x="35" y="10" width="19" height="19" rx="4" fill="rgba(255,255,255,0.35)"/>
    <rect x="10" y="35" width="19" height="19" rx="4" fill="rgba(255,255,255,0.35)"/>
    <rect x="35" y="35" width="19" height="19" rx="4" fill="rgba(255,255,255,0.15)"/>
  </svg>
  <h1>Tasks <span>App</span></h1>
  <p>Osobní task manager postavený na Eisenhowerově matici. Propojený s Daktelou, Google Kalendářem a AI prioritizací.</p>
  <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:24px">
    <a href="#demo" class="hero-cta">Vyzkoušet interaktivní demo</a>
    <a href="#features" class="hero-cta" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2)">Přehled funkcí</a>
  </div>
  <div class="hero-sub">
    <span>Eisenhowerova matice</span>
    <span style="width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.25)"></span>
    <span>Daktela integrace</span>
    <span style="width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.25)"></span>
    <span>Google Kalendář</span>
    <span style="width:4px;height:4px;border-radius:50%;background:rgba(255,255,255,.25)"></span>
    <span>AI prioritizace</span>
  </div>
</section>

<!-- NAV -->
<nav class="nav-dots" id="navDots">
  <a class="nav-dot" href="#demo">Demo</a>
  <a class="nav-dot" href="#features">Funkce</a>
  <a class="nav-dot" href="#integrations">Integrace</a>
  <a class="nav-dot" href="#workflow">Jak to funguje</a>
</nav>

<!-- STATS -->
<section class="section" style="padding-bottom:0">
  <div class="stats-row">
    <div class="stat-card"><div class="stat-num">4<span>×</span></div><div class="stat-label">kvadranty Eisenhower matice</div></div>
    <div class="stat-card"><div class="stat-num"><span>⌘</span>K</div><div class="stat-label">rychlé přidání tasku kdekoliv</div></div>
    <div class="stat-card"><div class="stat-num">AI</div><div class="stat-label">návrh priorit od Claude Sonnet</div></div>
    <div class="stat-card"><div class="stat-num">∞</div><div class="stat-label">opakující se tasky</div></div>
  </div>
</section>

<!-- INTERACTIVE DEMO -->
<section class="section" id="demo">
  <div class="section-header">
    <div class="section-label">Živé demo</div>
    <h2>Vyzkoušej si to přímo tady</h2>
    <p>Drag & drop tasky mezi kvadranty, otevři AI návrhy, přidej task rychlým zachycením.</p>
  </div>

  <div class="demo-label">Interaktivní ukázka — žádná data se neukládají</div>

  <div class="demo-wrapper demo-relative" id="demoWrapper">

    <!-- Quick Capture overlay -->
    <div class="qc-overlay" id="qcOverlay">
      <div class="qc-box">
        <h4>⚡ Rychlé přidání <kbd>⌘K</kbd></h4>
        <input class="qc-input" id="qcInput" placeholder="Co potřebuješ udělat?" />
        <div class="qc-row">
          <select class="qc-select" id="qcQuadrant">
            <option value="q1">🔴 Udělat hned (Q1)</option>
            <option value="q2" selected>🔵 Naplánovat (Q2)</option>
            <option value="q3">🟡 Delegovat (Q3)</option>
            <option value="q4">⚪ Eliminovat (Q4)</option>
          </select>
          <select class="qc-select" id="qcType">
            <option value="w">Pracovní</option>
            <option value="p">Osobní</option>
          </select>
          <button class="qc-submit" onclick="quickCaptureSave()">Přidat</button>
          <button class="qc-close" onclick="closeQC()">Zrušit</button>
        </div>
      </div>
    </div>

    <!-- AI overlay -->
    <div class="ai-overlay" id="aiOverlay">
      <div class="ai-modal">
        <h3>🤖 AI návrh priorit</h3>
        <p class="subtitle">Claude Sonnet analyzoval tvoje tasky a navrhuje přeřazení:</p>
        <div class="ai-sugg">
          <div class="ai-sugg-title">Review SLA reportu za Q1</div>
          <div class="ai-sugg-reason">Deadline je zítra a ovlivňuje strategické rozhodování týmu. Doporučuji zvýšit prioritu.</div>
          <div class="ai-sugg-change">
            <span class="q-chip q3">Q3 Delegovat</span>
            <span style="color:var(--grey-text)">→</span>
            <span class="q-chip q1">Q1 Udělat hned</span>
          </div>
        </div>
        <div class="ai-sugg">
          <div class="ai-sugg-title">Naučit se Excel pivot tabulky</div>
          <div class="ai-sugg-reason">Vzdělávací task bez urgence — ideální pro Q2 naplánování, ne Q1.</div>
          <div class="ai-sugg-change">
            <span class="q-chip q1">Q1 Udělat hned</span>
            <span style="color:var(--grey-text)">→</span>
            <span class="q-chip q2">Q2 Naplánovat</span>
          </div>
        </div>
        <button class="ai-modal-btn" onclick="applyAI()">Použít návrhy</button>
        <button class="ai-modal-btn sec" onclick="closeAI()">Zrušit</button>
      </div>
    </div>

    <!-- HEADER -->
    <div class="demo-header">
      <div class="demo-header-top">
        <div>
          <div class="demo-title">Tasks <small>— osobní task manager</small></div>
        </div>
        <div class="demo-actions">
          <button class="demo-btn-sm" onclick="openQC()">⚡ ⌘K</button>
          <button class="demo-btn-sm" onclick="openAI()">🤖 AI Priority</button>
        </div>
      </div>
      <div style="display:flex;gap:0">
        <button class="demo-tab active" onclick="switchTab(this,'matrix')">Vše</button>
        <button class="demo-tab" onclick="switchTab(this,'work')">Pracovní</button>
        <button class="demo-tab" onclick="switchTab(this,'personal')">Osobní</button>
        <button class="demo-tab" onclick="switchTab(this,'history')">Historie</button>
        <button class="demo-tab" onclick="switchTab(this,'onenon')">1on1</button>
        <div class="quick-chip" style="margin:6px 0 8px 10px">🔴 3 urgentní</div>
      </div>
    </div>

    <!-- BODY -->
    <div class="demo-body" id="demoBody">
      <!-- LEFT SIDEBAR -->
      <div class="demo-sidebar-left">
        <div class="demo-panel">
          <div class="dp-title">KPI — dnes</div>
          <div class="kpi-mini">
            <div class="kpi-box"><div class="val">87<small style="font-size:12px">%</small></div><div class="lbl">SLA</div></div>
            <div class="kpi-box"><div class="val">4.2<small style="font-size:10px">h</small></div><div class="lbl">First resp.</div></div>
          </div>
          <div class="dp-title" style="margin-top:4px">Checklist dnešku</div>
          <div class="cl-mini-item"><input type="checkbox" checked onchange="return false"> <span class="done">Denní standup</span></div>
          <div class="cl-mini-item"><input type="checkbox" checked onchange="return false"> <span class="done">SLA report odeslán</span></div>
          <div class="cl-mini-item"><input type="checkbox" onchange="return false"> <span>Review ticketů eskalace</span></div>
          <div class="cl-mini-item"><input type="checkbox" onchange="return false"> <span>1on1 s Martinem</span></div>
          <div class="cl-mini-item"><input type="checkbox" onchange="return false"> <span>Schůzka hiring</span></div>
        </div>
      </div>

      <!-- MATRIX / TABS -->
      <div id="matrixView">
        <div class="mini-matrix" id="matrix">
          <!-- Q1 -->
          <div class="mq mq-q1" id="q1" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q1')">
            <div class="mq-label">🔴 Udělat hned — Důležité & Urgentní <span class="cnt" id="cnt-q1">3</span></div>
            <div id="cards-q1"></div>
          </div>
          <!-- Q2 -->
          <div class="mq mq-q2" id="q2" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q2')">
            <div class="mq-label">🔵 Naplánovat — Důležité & Není urgentní <span class="cnt" id="cnt-q2">4</span></div>
            <div id="cards-q2"></div>
          </div>
          <!-- Q3 -->
          <div class="mq mq-q3" id="q3" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q3')">
            <div class="mq-label">🟡 Delegovat — Urgentní & Není důležité <span class="cnt" id="cnt-q3">2</span></div>
            <div id="cards-q3"></div>
          </div>
          <!-- Q4 -->
          <div class="mq mq-q4" id="q4" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q4')">
            <div class="mq-label">⚪ Eliminovat — Není důležité & Není urgentní <span class="cnt" id="cnt-q4">1</span></div>
            <div id="cards-q4"></div>
          </div>
        </div>
        <div style="text-align:center;margin-top:12px;font-size:11px;color:var(--grey-text)">
          💡 Přetáhni task do jiného kvadrantu myší
        </div>
      </div>

      <!-- HISTORY VIEW -->
      <div id="historyView" style="display:none;background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:14px">
        <div class="dp-title">Dokončené tasky</div>
        <div style="margin-bottom:10px">
          <div style="font-size:10px;font-weight:700;color:var(--grey-text);text-transform:uppercase;padding:6px 0 4px;border-bottom:1px solid var(--grey-border);margin-bottom:6px">Dnes</div>
          <div style="display:flex;align-items:center;gap:8px;padding:5px 6px;border-radius:5px;font-size:12px;color:var(--grey-text)">
            <span>✓</span><span style="flex:1">Denní standup s týmem</span><span style="font-size:10px">08:30</span>
            <button style="font-size:10px;padding:2px 7px;border:1px solid var(--grey-border);border-radius:4px;background:var(--grey-bg);cursor:pointer;color:var(--navy)" onclick="toast('Task přidán zpět do matice')">↩ obnovit</button>
          </div>
          <div style="display:flex;align-items:center;gap:8px;padding:5px 6px;border-radius:5px;font-size:12px;color:var(--grey-text)">
            <span>✓</span><span style="flex:1">SLA report odeslán vedení</span><span style="font-size:10px">09:15</span>
            <button style="font-size:10px;padding:2px 7px;border:1px solid var(--grey-border);border-radius:4px;background:var(--grey-bg);cursor:pointer;color:var(--navy)" onclick="toast('Task přidán zpět do matice')">↩ obnovit</button>
          </div>
        </div>
        <div>
          <div style="font-size:10px;font-weight:700;color:var(--grey-text);text-transform:uppercase;padding:6px 0 4px;border-bottom:1px solid var(--grey-border);margin-bottom:6px">Včera</div>
          <div style="display:flex;align-items:center;gap:8px;padding:5px 6px;border-radius:5px;font-size:12px;color:var(--grey-text)">
            <span>✓</span><span style="flex:1">Příprava podkladů na hiring schůzku</span><span style="font-size:10px">14:00</span>
            <button style="font-size:10px;padding:2px 7px;border:1px solid var(--grey-border);border-radius:4px;background:var(--grey-bg);cursor:pointer;color:var(--navy)" onclick="toast('Task přidán zpět do matice')">↩ obnovit</button>
          </div>
        </div>
        <div style="margin-top:14px;padding-top:10px;border-top:1px solid var(--grey-border);font-size:11px;color:var(--grey-text);text-align:center">
          Celkem dokončeno tento týden: <strong style="color:var(--navy)">14 tasků</strong>
        </div>
      </div>

      <!-- 1on1 VIEW -->
      <div id="onenon" style="display:none;background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:14px">
        <div class="dp-title">1on1 záznamy</div>
        <div style="display:flex;flex-direction:column;gap:8px">
          <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:7px;padding:10px 12px;cursor:pointer" onclick="toast('Otevřen detail 1on1 — Martin K.')">
            <div style="display:flex;align-items:center;justify-content:space-between">
              <div style="font-size:13px;font-weight:600">Martin K.</div>
              <div style="font-size:10px;color:var(--grey-text)">před 5 dny</div>
            </div>
            <div style="font-size:11px;color:var(--grey-text);margin-top:3px">3 action items · nálada 4/5</div>
          </div>
          <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:7px;padding:10px 12px;cursor:pointer" onclick="toast('Otevřen detail 1on1 — Jana S.')">
            <div style="display:flex;align-items:center;justify-content:space-between">
              <div style="font-size:13px;font-weight:600">Jana S.</div>
              <div style="font-size:10px;color:var(--grey-text)">před 12 dny</div>
            </div>
            <div style="font-size:11px;color:var(--grey-text);margin-top:3px">1 action item · nálada 3/5</div>
          </div>
          <div style="background:#FEE8E7;border:1px solid #fcc;border-radius:7px;padding:10px 12px;cursor:pointer" onclick="toast('Upozornění: Pavel H. bez 1on1 35 dní!')">
            <div style="display:flex;align-items:center;justify-content:space-between">
              <div style="font-size:13px;font-weight:600">Pavel H. <span style="font-size:10px;color:var(--red);font-weight:700">⚠ 35 dní</span></div>
              <div style="font-size:10px;color:var(--red)">přeplánovat!</div>
            </div>
            <div style="font-size:11px;color:var(--grey-text);margin-top:3px">0 naplánovaných · bez záznamu</div>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDEBAR -->
      <div class="demo-sidebar-right">
        <div class="demo-panel">
          <div class="dp-title" style="display:flex;align-items:center;justify-content:space-between">
            Daktela tickety
            <span style="font-size:10px;color:var(--grey-text)">3 přiřazeny</span>
          </div>
          <div class="ticket-mini">
            <span class="stage-chip OPEN">OPEN</span>
            <span style="flex:1;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">#12847 — Login problém portal</span>
          </div>
          <div class="ticket-mini">
            <span class="stage-chip WAIT">WAIT</span>
            <span style="flex:1;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">#12801 — Integrace CRM Salesforce</span>
          </div>
          <div class="ticket-mini">
            <span class="stage-chip OPEN">OPEN</span>
            <span style="flex:1;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">#12756 — Výpadek API webhooky</span>
          </div>
          <div style="margin-top:10px;padding-top:8px;border-top:1px solid var(--grey-border)">
            <input style="width:100%;height:28px;padding:0 8px;border:1px solid var(--grey-border);border-radius:5px;font-size:11px;outline:none" placeholder="Hledat ticket #..." onclick="toast('Vyhledávání v Daktela ticketech...')">
          </div>
        </div>
        <div class="demo-panel" style="margin-top:12px">
          <div class="dp-title">Google Kalendář</div>
          <div style="font-size:10px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px">Dnes</div>
          <div class="cal-mini-item"><span class="cal-mini-time">09:00</span><span>Standup L1 tým</span></div>
          <div class="cal-mini-item"><span class="cal-mini-time">11:30</span><span>Schůzka hiring výbor</span></div>
          <div class="cal-mini-item"><span class="cal-mini-time">14:00</span><span>1on1 Martin K.</span></div>
          <div style="font-size:10px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.4px;margin-top:8px;margin-bottom:4px">Zítra</div>
          <div class="cal-mini-item"><span class="cal-mini-time">10:00</span><span>Review Q1 výsledky</span></div>
          <div class="cal-mini-item"><span class="cal-mini-time">15:30</span><span>Weekly leadership</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<hr class="divider">

<!-- FEATURES OVERVIEW -->
<section class="section" id="features">
  <div class="section-header">
    <div class="section-label">Co všechno umí</div>
    <h2>Přehled funkcí</h2>
    <p>Vše co potřebuješ pro řízení vlastní práce na jednom místě.</p>
  </div>
  <div class="features-grid">
    <div class="feat-card">
      <div class="feat-icon red">🎯</div>
      <h3>Eisenhowerova matice</h3>
      <p>Čtyři kvadranty pro jasné třídění priorit: co udělat hned, co naplánovat, co delegovat a co eliminovat. Drag & drop mezi kvadranty.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon navy">⚡</div>
      <h3>Rychlé přidání (⌘K)</h3>
      <p>Odkudkoli v aplikaci otevři zachytávací dialog klávesovou zkratkou a okamžitě přidej nový task bez ztráty kontextu.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon purple">🤖</div>
      <h3>AI prioritizace</h3>
      <p>Claude Sonnet analyzuje tvoje tasky, deadliny a kontext a navrhne přeřazení do správných kvadrantů s odůvodněním.</p>
      <span class="feat-badge new">Claude Sonnet 4.6</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon amber">🔁</div>
      <h3>Opakující se tasky</h3>
      <p>Nastav libovolný interval — denně, týdně, měsíčně nebo vlastní počet dní. Task se sám znovu vytvoří po dokončení.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon green">📅</div>
      <h3>Google Kalendář</h3>
      <p>Přehled dnešních a zítřejších událostí přímo v sidebaru. OAuth propojení bez ukládání hesla — jen token.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon teal">🎫</div>
      <h3>Daktela tickety</h3>
      <p>Přiřaď Daktela ticket k tasku. Sidebar zobrazí všechny otevřené a čekající tickety, proklikatelné přímo do Daktely.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon navy">✅</div>
      <h3>Checklist dnešku</h3>
      <p>Denní checklist v levém sidebaru — opakující se povinnosti jako standup, SLA report nebo review ticketů eskalace.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon red">📊</div>
      <h3>KPI přehled</h3>
      <p>Aktuální Incident SLA a First Response čas přímo v sidebaru — bez nutnosti otevírat Daktela reporty.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon green">👥</div>
      <h3>1on1 záznamy</h3>
      <p>Evidence schůzek s přímými, timeline zápisů a action items. Upozornění pokud uplynulo &gt;30 dní od posledního setkání.</p>
      <span class="feat-badge new">Nová funkce</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon amber">🕐</div>
      <h3>Historie & obnovení</h3>
      <p>Přehled dokončených tasků seřazený po dnech. Libovolný task lze obnovit zpět do matice jedním kliknutím.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon purple">🔍</div>
      <h3>Fulltext vyhledávání</h3>
      <p>Okamžité hledání přes všechny tasky v hlavičce aplikace. Filtruje v reálném čase bez načítání.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon teal">🏷️</div>
      <h3>Tagy a kategorie</h3>
      <p>Označení tasků jako pracovní nebo osobní. Filtrování zobrazení podle kategorie přes záložky v hlavičce.</p>
    </div>
  </div>
</section>

<hr class="divider">

<!-- INTEGRATIONS -->
<section class="section" id="integrations" style="background:var(--white);border-radius:0">
  <div class="section-header">
    <div class="section-label">Napojení</div>
    <h2>Integrace</h2>
    <p>Tasks se propojuje s nástroji, které už používáš.</p>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px">
    <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:12px;padding:24px;text-align:center">
      <div style="font-size:36px;margin-bottom:12px">🎫</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px">Daktela CRM</h3>
      <p style="font-size:13px;color:var(--grey-text);line-height:1.6">Přiřazení ticketů k taskům, live přehled OPEN/WAIT ticketů, přímý proklik do Daktely. Aktualizace každé 2 minuty.</p>
    </div>
    <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:12px;padding:24px;text-align:center">
      <div style="font-size:36px;margin-bottom:12px">📅</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px">Google Kalendář</h3>
      <p style="font-size:13px;color:var(--grey-text);line-height:1.6">OAuth 2.0 propojení. Dnes + zítra události v sidebaru, kontext pro AI prioritizaci. Žádné heslo se neukládá.</p>
    </div>
    <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:12px;padding:24px;text-align:center">
      <div style="font-size:36px;margin-bottom:12px">🤖</div>
      <h3 style="font-size:16px;font-weight:700;margin-bottom:8px">Claude AI</h3>
      <p style="font-size:13px;color:var(--grey-text);line-height:1.6">Anthropic Claude Sonnet 4.6 pro analýzu priorit. Zohledňuje SLA kontext, deadliny a roli support manažera.</p>
    </div>
  </div>
</section>

<hr class="divider">

<!-- HOW IT WORKS -->
<section class="section" id="workflow">
  <div class="section-header">
    <div class="section-label">Workflow</div>
    <h2>Jak to funguje v praxi</h2>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:0;border:1px solid var(--grey-border);border-radius:12px;overflow:hidden;background:var(--white)">
    <div style="padding:24px;border-right:1px solid var(--grey-border)">
      <div style="width:32px;height:32px;border-radius:50%;background:var(--red);color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;margin-bottom:14px">1</div>
      <h4 style="font-size:14px;font-weight:700;margin-bottom:6px">Zachyť task</h4>
      <p style="font-size:12px;color:var(--grey-text);line-height:1.6">⌘K kdekoliv nebo klikni + v kvadrantu. Název, kategorie, deadline — a je hotovo.</p>
    </div>
    <div style="padding:24px;border-right:1px solid var(--grey-border)">
      <div style="width:32px;height:32px;border-radius:50%;background:var(--navy);color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:circle;display:flex;align-items:center;justify-content:center;margin-bottom:14px">2</div>
      <h4 style="font-size:14px;font-weight:700;margin-bottom:6px">Zařaď do matice</h4>
      <p style="font-size:12px;color:var(--grey-text);line-height:1.6">Drag & drop nebo při vytváření vyber kvadrant. AI navrhne přeřazení pokud to neodpovídá.</p>
    </div>
    <div style="padding:24px;border-right:1px solid var(--grey-border)">
      <div style="width:32px;height:32px;border-radius:50%;background:#E8A020;color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;margin-bottom:14px">3</div>
      <h4 style="font-size:14px;font-weight:700;margin-bottom:6px">Propoj s kontextem</h4>
      <p style="font-size:12px;color:var(--grey-text);line-height:1.6">Přiřaď Daktela ticket nebo Google Calendar event. Task dostane kontext a AI ho lépe pochopí.</p>
    </div>
    <div style="padding:24px">
      <div style="width:32px;height:32px;border-radius:50%;background:#2E7D3F;color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;margin-bottom:14px">4</div>
      <h4 style="font-size:14px;font-weight:700;margin-bottom:6px">Dokončuj a sleduj</h4>
      <p style="font-size:12px;color:var(--grey-text);line-height:1.6">Zaškrtni hotový task. Přejde do Historie, kde ho můžeš kdykoli obnovit nebo se podívat co jsi udělal.</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <strong>Tasks App</strong> — osobní task manager pro support manažery<br>
  <span style="margin-top:6px;display:block">Postaveno na PHP 8.5 · React 18 · MariaDB · Claude Sonnet API</span>
</footer>

<!-- TOAST -->
<div id="toast" style="position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--navy);color:#fff;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;opacity:0;transition:all .25s;pointer-events:none;z-index:999;white-space:nowrap"></div>

<script>
// ── DATA ──────────────────────────────────────────────
const TASKS = {
  q1: [
    {id:1, title:'Vyřešit eskalaci #12847 — portal login', meta:'dnes', badge:'d', badgeLabel:'Daktela', overdue:true},
    {id:2, title:'Připravit SLA report pro management', meta:'dnes 14:00', badge:'w', badgeLabel:'Pracovní'},
    {id:3, title:'1on1 s Martinem — příprava agendy', meta:'dnes 14:00', badge:'w', badgeLabel:'Pracovní'},
  ],
  q2: [
    {id:4, title:'Review procesu onboardingu nových agentů', meta:'pátek', badge:'w', badgeLabel:'Pracovní'},
    {id:5, title:'Nastavit automatické SLA upomínky', meta:'příští týden', badge:'w', badgeLabel:'Pracovní', ai:true},
    {id:6, title:'Přečíst knihu o vedení týmů', meta:'bez deadline', badge:'p', badgeLabel:'Osobní'},
    {id:7, title:'Naplánovat teambuilding Q2', meta:'do konce dubna', badge:'w', badgeLabel:'Pracovní'},
  ],
  q3: [
    {id:8, title:'Review SLA reportu za Q1', meta:'zítra', badge:'w', badgeLabel:'Pracovní', soon:true},
    {id:9, title:'Odpovědět na dotazy HR', meta:'tento týden', badge:'w', badgeLabel:'Pracovní'},
  ],
  q4: [
    {id:10, title:'Naučit se Excel pivot tabulky', meta:'bez deadline', badge:'p', badgeLabel:'Osobní'},
  ],
};

let tasks = JSON.parse(JSON.stringify(TASKS));
let draggingId = null;
let draggingFrom = null;

// ── RENDER ────────────────────────────────────────────
function renderAll() {
  ['q1','q2','q3','q4'].forEach(q => {
    const el = document.getElementById('cards-' + q);
    if (!el) return;
    el.innerHTML = '';
    tasks[q].forEach(t => {
      const d = document.createElement('div');
      d.className = 'tc' + (t.done ? ' done' : '');
      d.draggable = true;
      d.dataset.id = t.id;
      d.dataset.q = q;
      d.innerHTML =
        '<input class="tc-check" type="checkbox"' + (t.done?' checked':'') + ' onclick="checkTask(' + t.id + ',\'' + q + '\')">' +
        '<div class="tc-body">' +
          '<div class="tc-title" style="' + (t.done?'text-decoration:line-through;color:var(--grey-text)':'') + '">' + t.title + '</div>' +
          '<div class="tc-meta">' +
            '<span class="tc-badge ' + t.badge + '">' + t.badgeLabel + '</span>' +
            (t.meta ? '<span class="' + (t.overdue?'overdue':t.soon?'soon':'') + '">' + (t.overdue?'⚠ ':t.soon?'⚡ ':'') + t.meta + '</span>' : '') +
            (t.ai ? '<span class="tc-badge ai">AI</span>' : '') +
          '</div>' +
        '</div>';
      d.addEventListener('dragstart', e => { draggingId = t.id; draggingFrom = q; d.classList.add('dragging'); });
      d.addEventListener('dragend', () => { d.classList.remove('dragging'); });
      el.appendChild(d);
    });
    document.getElementById('cnt-' + q).textContent = tasks[q].filter(t => !t.done).length;
  });
}

function checkTask(id, q) {
  const t = tasks[q].find(x => x.id === id);
  if (!t) return;
  t.done = !t.done;
  if (t.done) {
    setTimeout(() => {
      tasks[q] = tasks[q].filter(x => x.id !== id);
      renderAll();
      toast('Task dokončen — přesunut do Historie');
    }, 400);
  } else renderAll();
}

// ── DRAG & DROP ───────────────────────────────────────
function dragOver(e) { e.preventDefault(); e.currentTarget.classList.add('drag-over'); }
function dragLeave(e) { e.currentTarget.classList.remove('drag-over'); }
function drop(e, targetQ) {
  e.preventDefault();
  e.currentTarget.classList.remove('drag-over');
  if (!draggingId || draggingFrom === targetQ) return;
  const idx = tasks[draggingFrom].findIndex(t => t.id === draggingId);
  if (idx === -1) return;
  const t = tasks[draggingFrom].splice(idx, 1)[0];
  tasks[targetQ].push(t);
  renderAll();
  const qLabels = {q1:'Udělat hned',q2:'Naplánovat',q3:'Delegovat',q4:'Eliminovat'};
  toast('Task přesunut → ' + qLabels[targetQ]);
  draggingId = null; draggingFrom = null;
}

// ── TABS ──────────────────────────────────────────────
function switchTab(btn, tab) {
  document.querySelectorAll('.demo-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('matrixView').style.display = (tab==='matrix'||tab==='work'||tab==='personal') ? '' : 'none';
  document.getElementById('historyView').style.display = tab==='history' ? '' : 'none';
  document.getElementById('onenon').style.display = tab==='onenon' ? '' : 'none';

  if (tab==='work') { filterMatrix('w'); }
  else if (tab==='personal') { filterMatrix('p'); }
  else if (tab==='matrix') { filterMatrix(null); }
}

function filterMatrix(type) {
  document.querySelectorAll('.tc').forEach(c => {
    if (!type) { c.style.display=''; return; }
    const badge = c.querySelector('.tc-badge');
    c.style.display = (badge && badge.classList.contains(type)) ? '' : 'none';
  });
}

// ── QUICK CAPTURE ─────────────────────────────────────
function openQC() { document.getElementById('qcOverlay').classList.add('show'); document.getElementById('qcInput').focus(); }
function closeQC() { document.getElementById('qcOverlay').classList.remove('show'); document.getElementById('qcInput').value=''; }
function quickCaptureSave() {
  const title = document.getElementById('qcInput').value.trim();
  if (!title) return;
  const q = document.getElementById('qcQuadrant').value;
  const type = document.getElementById('qcType').value;
  const labels = {w:'Pracovní',p:'Osobní'};
  const maxId = Math.max(...Object.values(tasks).flat().map(t=>t.id), 0);
  tasks[q].push({id: maxId+1, title, meta:'bez deadline', badge:type, badgeLabel:labels[type]});
  renderAll();
  closeQC();
  toast('Task přidán do ' + {q1:'Q1',q2:'Q2',q3:'Q3',q4:'Q4'}[q]);
}

// ── AI ────────────────────────────────────────────────
function openAI() { document.getElementById('aiOverlay').classList.add('show'); }
function closeAI() { document.getElementById('aiOverlay').classList.remove('show'); }
function applyAI() {
  // Move "Review SLA reportu" from q3 to q1
  const idx = tasks.q3.findIndex(t => t.title.includes('Review SLA'));
  if (idx > -1) { const t = tasks.q3.splice(idx,1)[0]; tasks.q1.push(t); }
  // Move "Naučit se Excel" from q4 to q2
  const idx2 = tasks.q4.findIndex(t => t.title.includes('Excel'));
  if (idx2 > -1) { const t = tasks.q4.splice(idx2,1)[0]; tasks.q2.push(t); }
  closeAI();
  renderAll();
  toast('AI návrhy aplikovány — 2 tasky přeřazeny');
}

// ── TOAST ─────────────────────────────────────────────
function toast(msg) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.style.opacity = '1';
  el.style.transform = 'translateX(-50%) translateY(0)';
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => {
    el.style.opacity='0';
    el.style.transform='translateX(-50%) translateY(20px)';
  }, 2500);
}

// ── KEYBOARD ──────────────────────────────────────────
document.addEventListener('keydown', e => {
  if ((e.metaKey || e.ctrlKey) && e.key==='k') { e.preventDefault(); openQC(); }
  if (e.key==='Escape') { closeQC(); closeAI(); }
  if (e.key==='Enter' && document.getElementById('qcOverlay').classList.contains('show')) quickCaptureSave();
});

// ── NAV HIGHLIGHT ─────────────────────────────────────
const sections = ['demo','features','integrations','workflow'];
window.addEventListener('scroll', () => {
  let cur = '';
  sections.forEach(id => {
    const el = document.getElementById(id);
    if (el && window.scrollY >= el.offsetTop - 100) cur = id;
  });
  document.querySelectorAll('.nav-dot').forEach(d => {
    d.classList.toggle('active', d.getAttribute('href')==='#'+cur);
  });
});

// ── INIT ──────────────────────────────────────────────
renderAll();
</script>
</body>
</html>
