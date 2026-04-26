<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tasks — přehled aplikace</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%231B3468'/><rect x='6' y='6' width='9' height='9' rx='2' fill='%23E05C4E'/><rect x='17' y='6' width='9' height='9' rx='2' fill='rgba(255,255,255,0.4)'/><rect x='6' y='17' width='9' height='9' rx='2' fill='rgba(255,255,255,0.4)'/><rect x='17' y='17' width='9' height='9' rx='2' fill='rgba(255,255,255,0.15)'/></svg>">
<style>
:root{
  --red:#E05C4E;--red-hover:#C94F42;--navy:#1B3468;
  --grey-bg:#F4F5F7;--grey-border:#DDE1E7;--grey-text:#5E6778;
  --white:#FFFFFF;--radius:8px;
  --font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--font);font-size:14px;background:var(--grey-bg);color:var(--navy)}

/* ── HEADER ────────────────────────────────── */
.page-header{background:linear-gradient(135deg,#1B3468 0%,#0f2044 100%);padding:40px 24px 32px;text-align:center}
.page-header-icon{width:52px;height:52px;margin:0 auto 16px;display:block}
.page-header h1{font-size:28px;font-weight:800;color:#fff;letter-spacing:-.3px}
.page-header p{color:rgba(255,255,255,.6);font-size:14px;margin-top:8px;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.6}
.header-pills{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:16px}
.header-pill{background:rgba(255,255,255,.12);color:rgba(255,255,255,.8);font-size:11px;font-weight:600;padding:4px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.15)}

/* ── NAV ───────────────────────────────────── */
.nav{display:flex;justify-content:center;gap:6px;padding:14px 16px;position:sticky;top:0;z-index:100;background:rgba(244,245,247,.96);backdrop-filter:blur(8px);border-bottom:1px solid var(--grey-border);flex-wrap:wrap}
.nav a{padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid var(--grey-border);background:var(--white);color:var(--grey-text);cursor:pointer;transition:all .15s;text-decoration:none}
.nav a:hover,.nav a.active{background:var(--navy);color:#fff;border-color:var(--navy)}

/* ── SECTION ───────────────────────────────── */
.section{max-width:1060px;margin:0 auto;padding:48px 24px}
.section-header{margin-bottom:28px}
.section-header h2{font-size:20px;font-weight:800;color:var(--navy)}
.section-header p{color:var(--grey-text);font-size:13px;margin-top:5px;line-height:1.6}
.divider{border:none;border-top:1px solid var(--grey-border);max-width:1060px;margin:0 auto}

/* ── DEMO WRAPPER ──────────────────────────── */
.demo-wrapper{background:var(--white);border:1px solid var(--grey-border);border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);position:relative}
.demo-note{text-align:center;font-size:11px;color:var(--grey-text);margin-top:10px}
.demo-header{background:linear-gradient(135deg,#1B3468 0%,#152a52 100%);padding:10px 16px 0}
.demo-header-top{display:flex;align-items:center;justify-content:space-between;padding-bottom:10px}
.demo-title{color:#fff;font-size:15px;font-weight:700}
.demo-title small{color:rgba(255,255,255,.45);font-size:11px;font-weight:400;margin-left:6px}
.demo-actions{display:flex;gap:6px}
.demo-btn{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:6px;font-size:11px;font-weight:600;padding:5px 10px;cursor:pointer;transition:background .15s}
.demo-btn:hover{background:rgba(255,255,255,.25)}
.demo-tab{padding:9px 16px;font-size:12px;font-weight:600;border:none;background:transparent;color:rgba(255,255,255,.5);cursor:pointer;border-bottom:3px solid transparent;transition:all .15s;white-space:nowrap}
.demo-tab.active{color:#fff;border-bottom:3px solid var(--red)}
.demo-tab:hover:not(.active){color:rgba(255,255,255,.8)}
.demo-body{display:grid;grid-template-columns:200px 1fr 196px;gap:12px;padding:14px;background:var(--grey-bg);min-height:380px}
.demo-panel{background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:12px;font-size:12px}
.demo-panel+.demo-panel{margin-top:10px}
.dp-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--grey-text);margin-bottom:8px}

/* KPI */
.kpi-mini{display:flex;gap:7px;margin-bottom:10px}
.kpi-box{flex:1;background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:6px;padding:7px;text-align:center}
.kpi-box .val{font-size:17px;font-weight:800;color:var(--navy)}
.kpi-box .lbl{font-size:9px;color:var(--grey-text);font-weight:600;text-transform:uppercase;letter-spacing:.3px;margin-top:1px}

/* Checklist */
.cl-item{display:flex;align-items:center;gap:6px;padding:4px 0;border-bottom:1px solid var(--grey-border);font-size:11px}
.cl-item:last-child{border-bottom:none}
.cl-item input{accent-color:var(--red);flex-shrink:0}
.cl-item.done span{text-decoration:line-through;color:var(--grey-text)}

/* Matrix */
.mini-matrix{display:grid;grid-template-columns:1fr 1fr;gap:9px}
.mq{background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:9px;min-height:130px;transition:background .15s}
.mq.drag-over{background:#f0f4ff;border-color:var(--navy)}
.mq.mq-q1{border-left:3px solid var(--red)}
.mq.mq-q2{border-left:3px solid var(--navy)}
.mq.mq-q3{border-left:3px solid #E8A020}
.mq-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--grey-text);margin-bottom:7px;display:flex;align-items:center;justify-content:space-between}
.mq-cnt{background:var(--grey-bg);color:var(--grey-text);font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px}
.mq-q1 .mq-cnt{background:#FEE8E7;color:#E63327}

/* Task card */
.tc{background:var(--white);border:1px solid var(--grey-border);border-radius:5px;padding:5px 7px;margin-bottom:4px;font-size:11px;cursor:grab;transition:box-shadow .15s,opacity .15s;display:flex;align-items:flex-start;gap:5px;user-select:none}
.tc:active{cursor:grabbing}
.tc.dragging{opacity:.4}
.tc-check{accent-color:var(--red);flex-shrink:0;width:13px;height:13px;margin-top:1px}
.tc-title{font-weight:500;line-height:1.3;margin-bottom:2px}
.tc-meta{font-size:10px;color:var(--grey-text);display:flex;gap:4px;align-items:center;flex-wrap:wrap}
.tc-badge{font-size:9px;font-weight:700;padding:1px 5px;border-radius:10px}
.tc-badge.w{background:#E0E8F5;color:#1B3468}
.tc-badge.p{background:#E8F5E9;color:#2E7D3F}
.tc-badge.d{background:#FFF4E0;color:#A06000}
.overdue{color:#E63327;font-weight:700}
.soon{color:#E8A020;font-weight:700}

/* Tickets sidebar */
.ticket-mini{display:flex;align-items:center;gap:5px;padding:5px 0;border-bottom:1px solid var(--grey-border);font-size:11px}
.ticket-mini:last-child{border-bottom:none}
.stage-chip{font-size:9px;font-weight:700;padding:1px 5px;border-radius:3px;flex-shrink:0}
.stage-chip.OPEN{background:#E3F5E8;color:#2E7D3F}
.stage-chip.WAIT{background:#FFF4E0;color:#A06000}

/* Calendar */
.cal-item{display:flex;gap:7px;padding:4px 0;border-bottom:1px solid var(--grey-border);font-size:11px;align-items:center}
.cal-item:last-child{border-bottom:none}
.cal-time{color:var(--grey-text);font-weight:600;font-size:10px;min-width:32px;flex-shrink:0}

/* 1on1 layout — přesně jako v reálné apce */
.onenon-layout{display:flex;gap:0;min-height:360px}
.onenon-sidebar{width:200px;flex-shrink:0;padding:12px;border-right:1px solid var(--grey-border);background:var(--white)}
.onenon-main{flex:1;padding:14px;background:var(--white);overflow-y:auto}
.onenon-dashboard{background:var(--grey-bg);border-radius:6px;padding:8px 10px;margin-bottom:10px;font-size:11px}
.onenon-warn{color:#C94F42;font-weight:700}
.onenon-row{display:flex;justify-content:space-between;gap:6px}
.onenon-person-row{display:flex;align-items:center;gap:3px;margin-bottom:3px}
.onenon-person-btn{flex:1;padding:7px 10px;border-radius:6px;cursor:pointer;font-weight:600;font-size:12px;display:flex;align-items:center;justify-content:space-between;background:var(--grey-bg);color:var(--navy);border:none;text-align:left;transition:background .15s}
.onenon-person-btn:hover{background:#e0e8f5}
.onenon-person-btn.active{background:var(--navy);color:#fff}
.onenon-person-warn{width:7px;height:7px;border-radius:50%;background:var(--red);flex-shrink:0}
.onenon-note-card{background:var(--white);border:1px solid var(--grey-border);border-radius:7px;padding:12px;margin-bottom:10px}
.onenon-note-date{font-weight:700;color:var(--navy);font-size:12px;margin-bottom:4px}
.onenon-mood{color:#F5A623;font-size:12px;letter-spacing:1px}
.onenon-tag{display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin:2px 3px 4px 0;background:#EEF2FF;color:#3B5BDB}
.onenon-action{display:flex;align-items:center;gap:7px;padding:3px 0;font-size:12px}
.onenon-check{width:13px;height:13px;border-radius:3px;border:2px solid var(--navy);display:inline-block;flex-shrink:0}
.onenon-check.done{border-color:var(--grey-text);background:var(--grey-bg)}
.onenon-action-text.done{color:var(--grey-text);text-decoration:line-through}

/* Overlays */
.overlay{display:none;position:absolute;inset:0;background:rgba(0,0,0,.48);z-index:50;align-items:center;justify-content:center;padding:20px;border-radius:12px}
.overlay.show{display:flex}
.modal-box{background:var(--white);border-radius:10px;padding:22px;width:100%;max-width:420px;box-shadow:0 8px 40px rgba(0,0,0,.2)}
.modal-box h3{font-size:14px;font-weight:700;margin-bottom:4px}
.modal-sub{font-size:11px;color:var(--grey-text);margin-bottom:14px}
.ai-sugg{background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:7px;padding:10px;margin-bottom:7px}
.ai-sugg-title{font-size:12px;font-weight:600}
.ai-sugg-reason{font-size:11px;color:var(--grey-text);margin-top:3px;line-height:1.5}
.ai-sugg-change{font-size:10px;margin-top:5px;display:flex;align-items:center;gap:5px}
.q-chip{font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px}
.q-chip.q1{background:#FEE8E7;color:#E63327}
.q-chip.q2{background:#E0E8F5;color:#1B3468}
.q-chip.q3{background:#FFF4E0;color:#A06000}
.modal-btns{display:flex;gap:8px;margin-top:14px}
.mbtn{height:34px;padding:0 16px;border-radius:var(--radius);font-size:12px;font-weight:700;cursor:pointer;border:none}
.mbtn-primary{background:var(--red);color:#fff}
.mbtn-sec{background:var(--white);color:var(--navy);border:1px solid var(--grey-border)!important;border:none}

/* QC overlay */
.qc-box{background:var(--white);border-radius:10px;padding:18px;width:100%;max-width:460px;box-shadow:0 8px 40px rgba(0,0,0,.22)}
.qc-box h4{font-size:13px;font-weight:700;margin-bottom:10px}
.qc-box h4 kbd{font-size:10px;background:var(--grey-bg);border:1px solid var(--grey-border);padding:1px 5px;border-radius:3px;font-family:monospace;color:var(--grey-text);font-weight:400}
.qc-input{width:100%;height:38px;padding:0 10px;border:1px solid var(--grey-border);border-radius:var(--radius);font-size:13px;font-family:var(--font);outline:none;margin-bottom:8px}
.qc-input:focus{border-color:var(--navy)}
.qc-row{display:flex;gap:7px;flex-wrap:wrap}
.qc-select{flex:1;height:32px;padding:0 7px;border:1px solid var(--grey-border);border-radius:5px;font-size:11px;font-family:var(--font);outline:none}

/* Features grid */
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}
.feat-card{background:var(--white);border:1px solid var(--grey-border);border-radius:10px;padding:20px}
.feat-icon{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:12px}
.feat-icon.red{background:#FEE8E7}
.feat-icon.navy{background:#E0E8F5}
.feat-icon.amber{background:#FFF4E0}
.feat-icon.green{background:#E3F5E8}
.feat-icon.purple{background:#F0E8FF}
.feat-icon.teal{background:#E0F5F5}
.feat-card h3{font-size:14px;font-weight:700;margin-bottom:6px}
.feat-card p{font-size:12px;color:var(--grey-text);line-height:1.6}
.feat-new{display:inline-block;margin-top:7px;font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;background:#E3F5E8;color:#2E7D3F}

/* How it works */
.steps{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));border:1px solid var(--grey-border);border-radius:10px;overflow:hidden;background:var(--white)}
.step{padding:20px;border-right:1px solid var(--grey-border)}
.step:last-child{border-right:none}
.step-num{width:28px;height:28px;border-radius:50%;color:#fff;font-size:13px;font-weight:800;display:flex;align-items:center;justify-content:center;margin-bottom:12px}
.step h4{font-size:13px;font-weight:700;margin-bottom:5px}
.step p{font-size:12px;color:var(--grey-text);line-height:1.5}

/* Toast */
#toast{position:fixed;bottom:20px;left:50%;transform:translateX(-50%) translateY(16px);background:var(--navy);color:#fff;padding:9px 18px;border-radius:7px;font-size:12px;font-weight:600;opacity:0;transition:all .22s;pointer-events:none;z-index:999;white-space:nowrap}

@media(max-width:860px){.demo-body{grid-template-columns:1fr}.demo-sidebar-l,.demo-sidebar-r{display:none}}
@media(max-width:600px){.mini-matrix{grid-template-columns:1fr}.steps{grid-template-columns:1fr}.step{border-right:none;border-bottom:1px solid var(--grey-border)}.step:last-child{border-bottom:none}}
</style>
</head>
<body>

<!-- HEADER -->
<div class="page-header">
  <svg class="page-header-icon" viewBox="0 0 52 52" fill="none">
    <rect width="52" height="52" rx="10" fill="#1B3468"/>
    <rect x="8" y="8" width="15" height="15" rx="3" fill="#E05C4E"/>
    <rect x="28" y="8" width="15" height="15" rx="3" fill="rgba(255,255,255,.32)"/>
    <rect x="8" y="28" width="15" height="15" rx="3" fill="rgba(255,255,255,.32)"/>
    <rect x="28" y="28" width="15" height="15" rx="3" fill="rgba(255,255,255,.14)"/>
  </svg>
  <h1>Tasks</h1>
  <p>Osobní task manager na Eisenhowerově matici. Propojený s Daktelou, Google Kalendářem a AI prioritizací — ukazuje co je teď nejdůležitější.</p>
  <div class="header-pills">
    <span class="header-pill">Eisenhowerova matice</span>
    <span class="header-pill">Daktela tickety</span>
    <span class="header-pill">Google Kalendář</span>
    <span class="header-pill">AI prioritizace</span>
    <span class="header-pill">1on1 záznamy</span>
  </div>
</div>

<!-- NAV -->
<nav class="nav" id="nav">
  <a href="#demo">Demo</a>
  <a href="#features">Co umí</a>
  <a href="#integrations">Integrace</a>
  <a href="#security">Bezpečnost</a>
  <a href="#how">Jak funguje</a>
</nav>

<!-- DEMO -->
<section class="section" id="demo">
  <div class="section-header">
    <h2>Interaktivní ukázka</h2>
    <p>Drag & drop tasky mezi kvadranty, vyzkoušej ⌘K quick capture nebo AI návrh priorit. Žádná data se neukládají.</p>
  </div>

  <div class="demo-wrapper" id="demoWrapper">

    <!-- QC overlay -->
    <div class="overlay" id="qcOverlay" style="align-items:flex-start;padding-top:50px">
      <div class="qc-box">
        <h4>Rychlé přidání <kbd>⌘K</kbd></h4>
        <input class="qc-input" id="qcInput" placeholder="Co potřebuješ udělat?">
        <div class="qc-row">
          <select class="qc-select" id="qcQ">
            <option value="q1">🔴 Udělat hned</option>
            <option value="q2" selected>🔵 Naplánovat</option>
            <option value="q3">🟡 Delegovat</option>
            <option value="q4">⚪ Eliminovat</option>
          </select>
          <select class="qc-select" id="qcType">
            <option value="w">Pracovní</option>
            <option value="p">Osobní</option>
          </select>
          <button class="mbtn mbtn-primary" onclick="qcSave()">Přidat</button>
          <button class="mbtn mbtn-sec" style="border:1px solid var(--grey-border)" onclick="closeQC()">Zrušit</button>
        </div>
      </div>
    </div>

    <!-- AI overlay -->
    <div class="overlay" id="aiOverlay">
      <div class="modal-box">
        <h3>AI návrh priorit</h3>
        <div class="modal-sub">Claude Sonnet analyzoval tasky a navrhuje přeřazení:</div>
        <div class="ai-sugg">
          <div class="ai-sugg-title">Review SLA reportu za Q1</div>
          <div class="ai-sugg-reason">Deadline je zítra a výsledky ovlivňují plánování týmu. Patří do Q1.</div>
          <div class="ai-sugg-change">
            <span class="q-chip q3">Q3 Delegovat</span>
            <span style="color:var(--grey-text)">→</span>
            <span class="q-chip q1">Q1 Udělat hned</span>
          </div>
        </div>
        <div class="ai-sugg">
          <div class="ai-sugg-title">Naučit se Excel pivot tabulky</div>
          <div class="ai-sugg-reason">Vzdělávací task bez urgence — vhodné naplánovat, ne řešit hned.</div>
          <div class="ai-sugg-change">
            <span class="q-chip q1">Q1 Udělat hned</span>
            <span style="color:var(--grey-text)">→</span>
            <span class="q-chip q2">Q2 Naplánovat</span>
          </div>
        </div>
        <div class="modal-btns">
          <button class="mbtn mbtn-primary" onclick="applyAI()">Použít návrhy</button>
          <button class="mbtn mbtn-sec" style="border:1px solid var(--grey-border)" onclick="closeAI()">Zrušit</button>
        </div>
      </div>
    </div>

    <!-- Demo header -->
    <div class="demo-header">
      <div class="demo-header-top">
        <div class="demo-title">Tasks <small>— osobní task manager</small></div>
        <div class="demo-actions">
          <button class="demo-btn" onclick="openQC()">⚡ ⌘K</button>
          <button class="demo-btn" onclick="openAI()">🤖 AI Priority</button>
        </div>
      </div>
      <div style="display:flex;gap:0">
        <button class="demo-tab active" onclick="switchTab(this,'all')">Vše</button>
        <button class="demo-tab" onclick="switchTab(this,'work')">Pracovní</button>
        <button class="demo-tab" onclick="switchTab(this,'personal')">Osobní</button>
        <button class="demo-tab" onclick="switchTab(this,'history')">Historie</button>
        <button class="demo-tab" onclick="switchTab(this,'onenon')">1on1</button>
      </div>
    </div>

    <!-- Demo body -->
    <div class="demo-body" id="demoBody">

      <!-- LEFT sidebar -->
      <div class="demo-sidebar-l">
        <div class="demo-panel">
          <div class="dp-title">KPI — dnes</div>
          <div class="kpi-mini">
            <div class="kpi-box"><div class="val">14</div><div class="lbl">Hotovo</div></div>
            <div class="kpi-box"><div class="val">3</div><div class="lbl">Otevřeno</div></div>
          </div>
          <div class="dp-title" style="margin-top:4px">Checklist dnešku</div>
          <div class="cl-item done"><input type="checkbox" checked onclick="return false"> <span>Denní standup</span></div>
          <div class="cl-item done"><input type="checkbox" checked onclick="return false"> <span>SLA report odeslán</span></div>
          <div class="cl-item"><input type="checkbox" onclick="return false"> <span>Review eskalací</span></div>
          <div class="cl-item"><input type="checkbox" onclick="return false"> <span>1on1 s Martinem</span></div>
          <div class="cl-item"><input type="checkbox" onclick="return false"> <span>Schůzka hiring</span></div>
        </div>
      </div>

      <!-- CENTER: matrix / history / 1on1 -->
      <div id="matrixView">
        <div class="mini-matrix" id="matrix">
          <div class="mq mq-q1" id="q1" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q1')">
            <div class="mq-label">🔴 Udělat hned <span class="mq-cnt" id="cnt-q1"></span></div>
            <div id="cards-q1"></div>
          </div>
          <div class="mq mq-q2" id="q2" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q2')">
            <div class="mq-label">🔵 Naplánovat <span class="mq-cnt" id="cnt-q2"></span></div>
            <div id="cards-q2"></div>
          </div>
          <div class="mq mq-q3" id="q3" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q3')">
            <div class="mq-label">🟡 Delegovat <span class="mq-cnt" id="cnt-q3"></span></div>
            <div id="cards-q3"></div>
          </div>
          <div class="mq mq-q4" id="q4" ondragover="dragOver(event)" ondragleave="dragLeave(event)" ondrop="drop(event,'q4')">
            <div class="mq-label">⚪ Eliminovat <span class="mq-cnt" id="cnt-q4"></span></div>
            <div id="cards-q4"></div>
          </div>
        </div>
        <div style="text-align:center;margin-top:8px;font-size:11px;color:var(--grey-text)">
          Přetáhni task do jiného kvadrantu myší · zaškrtni pro dokončení
        </div>
      </div>

      <!-- HISTORY view -->
      <div id="historyView" style="display:none;background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:12px">
        <div class="dp-title">Dokončené tasky</div>
        <div style="font-size:10px;font-weight:700;color:var(--grey-text);text-transform:uppercase;padding:5px 0 4px;border-bottom:1px solid var(--grey-border);margin-bottom:5px">Dnes</div>
        <div style="display:flex;align-items:center;gap:7px;padding:4px 5px;font-size:11px;color:var(--grey-text);border-radius:4px">
          <span>✓</span><span style="flex:1">Denní standup s týmem</span><span style="font-size:10px">08:30</span>
          <button style="font-size:10px;padding:2px 6px;border:1px solid var(--grey-border);border-radius:4px;background:var(--grey-bg);cursor:pointer;color:var(--navy)" onclick="toast('Task obnoven zpět do matice')">↩</button>
        </div>
        <div style="display:flex;align-items:center;gap:7px;padding:4px 5px;font-size:11px;color:var(--grey-text);border-radius:4px">
          <span>✓</span><span style="flex:1">SLA report odeslán vedení</span><span style="font-size:10px">09:15</span>
          <button style="font-size:10px;padding:2px 6px;border:1px solid var(--grey-border);border-radius:4px;background:var(--grey-bg);cursor:pointer;color:var(--navy)" onclick="toast('Task obnoven zpět do matice')">↩</button>
        </div>
        <div style="font-size:10px;font-weight:700;color:var(--grey-text);text-transform:uppercase;padding:8px 0 4px;border-bottom:1px solid var(--grey-border);margin-bottom:5px">Včera</div>
        <div style="display:flex;align-items:center;gap:7px;padding:4px 5px;font-size:11px;color:var(--grey-text);border-radius:4px">
          <span>✓</span><span style="flex:1">Příprava podkladů na hiring schůzku</span><span style="font-size:10px">14:00</span>
          <button style="font-size:10px;padding:2px 6px;border:1px solid var(--grey-border);border-radius:4px;background:var(--grey-bg);cursor:pointer;color:var(--navy)" onclick="toast('Task obnoven zpět do matice')">↩</button>
        </div>
        <div style="margin-top:10px;padding-top:8px;border-top:1px solid var(--grey-border);font-size:11px;color:var(--grey-text)">
          Tento týden dokončeno: <strong style="color:var(--navy)">14 tasků</strong>
        </div>
      </div>

      <!-- 1on1 view — layout přesně jako v reálné apce -->
      <div id="onenonView" style="display:none">
        <div class="onenon-layout" style="border:1px solid var(--grey-border);border-radius:var(--radius);overflow:hidden">
          <!-- sidebar: seznam lidí -->
          <div class="onenon-sidebar">
            <div class="onenon-dashboard">
              <div class="onenon-row"><span>Otevřené action items:</span><span class="onenon-warn">4</span></div>
              <div class="onenon-row" style="margin-top:3px"><span class="onenon-warn">⚠ Bez 1on1 &gt;30 dní:</span><span style="font-weight:600">Pavel H.</span></div>
            </div>
            <div class="dp-title" style="margin-bottom:6px">Lidé</div>
            <div class="onenon-person-row">
              <button class="onenon-person-btn active" id="person-martin" onclick="selectPerson('martin')">
                <span>Martin K. <span style="opacity:.6;font-weight:400">(3)</span></span>
              </button>
            </div>
            <div class="onenon-person-row">
              <button class="onenon-person-btn" id="person-jana" onclick="selectPerson('jana')">
                <span>Jana S. <span style="opacity:.6;font-weight:400">(2)</span></span>
              </button>
            </div>
            <div class="onenon-person-row">
              <button class="onenon-person-btn" id="person-pavel" onclick="selectPerson('pavel')">
                <span>Pavel H. <span style="opacity:.6;font-weight:400">(1)</span></span>
                <span class="onenon-person-warn" title="35 dní bez 1on1"></span>
              </button>
            </div>
            <button class="mbtn mbtn-primary" style="width:100%;margin-top:10px;font-size:11px;height:30px" onclick="toast('Formulář nové schůzky — dostupné v reálné apce')">+ Nová schůzka</button>
            <!-- profil vybrané osoby + dokončené úkoly -->
            <div id="onenonProfile" style="margin-top:12px"></div>
          </div>
          <!-- main: záznamy vybrané osoby -->
          <div class="onenon-main" id="onenonMain">
            <!-- naplněno Javascriptem -->
          </div>
        </div>
      </div>

      <!-- RIGHT sidebar -->
      <div class="demo-sidebar-r">
        <div class="demo-panel">
          <div class="dp-title" style="display:flex;align-items:center;justify-content:space-between">
            Daktela tickety <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:10px">3 přiřazeny</span>
          </div>
          <div class="ticket-mini"><span class="stage-chip OPEN">OPEN</span><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">#12847 — Login problém portal</span></div>
          <div class="ticket-mini"><span class="stage-chip WAIT">WAIT</span><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">#12801 — Integrace CRM</span></div>
          <div class="ticket-mini"><span class="stage-chip OPEN">OPEN</span><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">#12756 — Výpadek webhooky</span></div>
          <input style="width:100%;height:26px;padding:0 7px;border:1px solid var(--grey-border);border-radius:4px;font-size:10px;outline:none;margin-top:8px" placeholder="Hledat ticket #..." onclick="toast('Vyhledávání v Daktela ticketech')">
        </div>
        <div class="demo-panel" style="margin-top:10px">
          <div class="dp-title">Google Kalendář</div>
          <div style="font-size:9px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px">Dnes</div>
          <div class="cal-item"><span class="cal-time">09:00</span><span>Standup L1 tým</span></div>
          <div class="cal-item"><span class="cal-time">11:30</span><span>Schůzka hiring</span></div>
          <div class="cal-item"><span class="cal-time">14:00</span><span>1on1 Martin K.</span></div>
          <div style="font-size:9px;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:.4px;margin-top:7px;margin-bottom:3px">Zítra</div>
          <div class="cal-item"><span class="cal-time">10:00</span><span>Review Q1 výsledky</span></div>
          <div class="cal-item"><span class="cal-time">15:30</span><span>Weekly leadership</span></div>
        </div>
      </div>
    </div>
  </div>
  <div class="demo-note">Interaktivní ukázka · žádná data se neukládají · pracuje jen v tomto prohlížeči</div>
</section>

<hr class="divider">

<!-- FEATURES -->
<section class="section" id="features">
  <div class="section-header">
    <h2>Co umí</h2>
    <p>Přehled hlavních funkcí aplikace.</p>
  </div>
  <div class="features-grid">
    <div class="feat-card">
      <div class="feat-icon red">🎯</div>
      <h3>Eisenhowerova matice</h3>
      <p>Čtyři kvadranty pro třídění tasků: udělat hned, naplánovat, delegovat, eliminovat. Drag & drop mezi kvadranty.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon navy">⚡</div>
      <h3>Rychlé přidání (⌘K)</h3>
      <p>Klávesová zkratka odkudkoli v apce otevře dialog pro okamžité přidání tasku bez ztráty kontextu.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon purple">🤖</div>
      <h3>AI prioritizace</h3>
      <p>Claude Sonnet analyzuje tasky a navrhne přeřazení do správných kvadrantů s odůvodněním. Zohledňuje SLA kontext a deadliny.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon amber">🔁</div>
      <h3>Opakující se tasky</h3>
      <p>Denně, týdně, měsíčně nebo vlastní interval. Task se sám znovu vytvoří po dokončení.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon green">📅</div>
      <h3>Google Kalendář</h3>
      <p>Dnes + zítra události v sidebaru. OAuth propojení — žádné heslo se neukládá.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon teal">🎫</div>
      <h3>Daktela tickety</h3>
      <p>Přiřaď ticket k tasku. Sidebar zobrazí všechny otevřené tickety, proklikatelné přímo do Daktely.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon navy">✅</div>
      <h3>Checklist dnešku</h3>
      <p>Opakující se denní povinnosti — standup, SLA report, review eskalací. Resetuje se každý den.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon red">📊</div>
      <h3>KPI přehled</h3>
      <p>Aktuální Incident SLA a First Response čas přímo v sidebaru — bez nutnosti otevírat Daktela reporty.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon green">👥</div>
      <h3>1on1 záznamy</h3>
      <p>Evidence schůzek s přímými — datum, nálada, tagy (výkon, SLA, rozvoj...), action items. Upozornění pokud uplynulo &gt;30 dní.</p>
      <span class="feat-new">Nová funkce</span>
    </div>
    <div class="feat-card">
      <div class="feat-icon amber">🕐</div>
      <h3>Historie & obnovení</h3>
      <p>Dokončené tasky seřazené po dnech. Libovolný task lze obnovit zpět do matice.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon purple">🔍</div>
      <h3>Fulltext vyhledávání</h3>
      <p>Okamžité hledání přes všechny tasky v hlavičce. Filtruje v reálném čase.</p>
    </div>
    <div class="feat-card">
      <div class="feat-icon teal">🏷️</div>
      <h3>Kategorie a filtrování</h3>
      <p>Pracovní a osobní tasky odděleně. Záložky v hlavičce přepínají pohled.</p>
    </div>
  </div>
</section>

<hr class="divider">

<!-- INTEGRATIONS -->
<section class="section" id="integrations" style="background:var(--white)">
  <div class="section-header">
    <h2>Integrace</h2>
    <p>Napojení na nástroje, které denně používáme.</p>
  </div>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px">
    <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:10px;padding:20px">
      <div style="font-size:28px;margin-bottom:10px">🎫</div>
      <h3 style="font-size:14px;font-weight:700;margin-bottom:6px">Daktela CRM</h3>
      <p style="font-size:12px;color:var(--grey-text);line-height:1.6">Přiřazení ticketů k taskům, live přehled OPEN/WAIT, přímý proklik do Daktely. Aktualizace každé 2 minuty z cache.</p>
    </div>
    <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:10px;padding:20px">
      <div style="font-size:28px;margin-bottom:10px">📅</div>
      <h3 style="font-size:14px;font-weight:700;margin-bottom:6px">Google Kalendář</h3>
      <p style="font-size:12px;color:var(--grey-text);line-height:1.6">OAuth 2.0. Dnes + zítra v sidebaru, kontext pro AI. Žádné heslo se neukládá, jen OAuth token.</p>
    </div>
    <div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:10px;padding:20px">
      <div style="font-size:28px;margin-bottom:10px">🤖</div>
      <h3 style="font-size:14px;font-weight:700;margin-bottom:6px">Claude AI</h3>
      <p style="font-size:12px;color:var(--grey-text);line-height:1.6">Anthropic Claude Sonnet 4.6. Navrhuje přeřazení tasků, zohledňuje SLA, deadliny a roli support manažera.</p>
    </div>
  </div>
</section>

<hr class="divider">

<!-- SECURITY -->
<section class="section" id="security">
  <div class="section-header">
    <h2>Bezpečnost</h2>
    <p>Co tato ukázková stránka dělá a co ne — a jak je zabezpečena reálná aplikace.</p>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
    <div style="background:#E3F5E8;border:1px solid #a8d9b4;border-radius:10px;padding:20px">
      <div style="font-size:13px;font-weight:700;color:#1a5c2a;margin-bottom:12px">Tato ukázková stránka (demo.php)</div>
      <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;color:#1a5c2a">
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Čistý HTML soubor — žádná PHP logika, žádné databázové dotazy</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Všechna data jsou <strong>pevně zapsaná</strong> přímo v kódu stránky — fiktivní jména, fiktivní tasky</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Veškerá interaktivita běží <strong>jen v tvém prohlížeči</strong> — nic se neposílá na server</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Po zavření záložky zmizí vše — stránka neukládá žádná cookies ani localStorage</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Přístup bez přihlášení je záměrný — stránka neobsahuje žádná citlivá data</span></div>
      </div>
    </div>
    <div style="background:#E0E8F5;border:1px solid #a8bcd9;border-radius:10px;padding:20px">
      <div style="font-size:13px;font-weight:700;color:#1B3468;margin-bottom:12px">Reálná aplikace (/tasks/)</div>
      <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;color:#1B3468">
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Přístup jen po přihlášení — vlastní heslo s bcrypt hashem, bez třetích stran</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Session cookie: HttpOnly, Secure, SameSite=Lax — nelze číst z JavaScriptu</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Secrets (hesla, API klíče) mimo webroot v <code style="background:rgba(0,0,0,.08);padding:1px 4px;border-radius:3px">/etc/tasks/secrets.php</code> — git je nevidí</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>HTTPS povinné — server odmítá nešifrované spojení</span></div>
        <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Brute-force throttling na přihlašování — 1s zpoždění při nesprávném hesle</span></div>
      </div>
      <div style="margin-top:14px;padding-top:12px;border-top:1px solid #c0d0e8">
        <div style="font-size:11px;font-weight:700;color:#1B3468;margin-bottom:8px;text-transform:uppercase;letter-spacing:.4px">Daktela integrace</div>
        <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;color:#1B3468">
          <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Přihlašuje se <strong>tvým vlastním Daktela loginem a heslem</strong> — stejně jako do Daktely v prohlížeči</span></div>
          <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Přihlašovací údaje putují přímo na Daktela API přes HTTPS — aplikace je nikde neukládá</span></div>
          <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Výsledný přístupový token se ukládá jen do session (platí do zavření prohlížeče)</span></div>
        </div>
      </div>
      <div style="margin-top:14px;padding-top:12px;border-top:1px solid #c0d0e8">
        <div style="font-size:11px;font-weight:700;color:#1B3468;margin-bottom:8px;text-transform:uppercase;letter-spacing:.4px">Google Kalendář</div>
        <div style="display:flex;flex-direction:column;gap:8px;font-size:12px;color:#1B3468">
          <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Propojení přes OAuth 2.0 — přihlašuješ se přes Google, aplikace nikdy nevidí tvé Google heslo</span></div>
          <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>Oprávnění je <strong>pouze pro čtení</strong> — aplikace zobrazuje události, nemůže nic vytvořit, upravit ani smazat</span></div>
          <div style="display:flex;gap:8px"><span style="font-weight:800;flex-shrink:0">✓</span><span>OAuth token uložen jen v databázi, nikoli v cookie nebo URL</span></div>
        </div>
      </div>
    </div>
  </div>

  <div style="background:#F4F5F7;border:1px solid var(--grey-border);border-radius:10px;padding:16px 20px;font-size:12px;color:var(--grey-text);line-height:1.7">
    <strong style="color:var(--navy)">Shrnutí:</strong>
    Tato stránka je veřejná ukázka bez jakýchkoli reálných dat — klidně ji pošli komukoliv.
    Reálná aplikace na <code style="background:rgba(0,0,0,.06);padding:1px 5px;border-radius:3px">/tasks/</code> je za přihlášením a tvá data jsou viditelná pouze tobě.
    Obě stránky jsou na stejném serveru, ale jsou od sebe zcela odděleny — demo nemá přístup k databázi ani session reálné aplikace.
  </div>
</section>

<hr class="divider">

<!-- HOW IT WORKS -->
<section class="section" id="how">
  <div class="section-header">
    <h2>Jak to funguje</h2>
  </div>
  <div class="steps">
    <div class="step">
      <div class="step-num" style="background:var(--red)">1</div>
      <h4>Zachyť task</h4>
      <p>⌘K nebo klikni + v kvadrantu. Název, kategorie, deadline — a je hotovo.</p>
    </div>
    <div class="step">
      <div class="step-num" style="background:var(--navy)">2</div>
      <h4>Zařaď do matice</h4>
      <p>Drag & drop nebo při vytváření vyber kvadrant. AI navrhne korekci pokud to neodpovídá.</p>
    </div>
    <div class="step">
      <div class="step-num" style="background:#E8A020">3</div>
      <h4>Propoj s kontextem</h4>
      <p>Přiřaď Daktela ticket nebo Calendar event. Task dostane kontext a AI ho lépe pochopí.</p>
    </div>
    <div class="step">
      <div class="step-num" style="background:#2E7D3F">4</div>
      <h4>Dokončuj a sleduj</h4>
      <p>Zaškrtni hotový task. Přejde do Historie — lze kdykoli obnovit nebo zobrazit statistiku.</p>
    </div>
  </div>
</section>

<!-- TOAST -->
<div id="toast"></div>

<script>
// ── DATA ──────────────────────────────────────────
const INIT_TASKS = {
  q1:[
    {id:1,title:'Vyřešit eskalaci #12847 — portal login',meta:'dnes',badge:'d',badgeLabel:'Daktela',overdue:true},
    {id:2,title:'Připravit SLA report pro management',meta:'dnes 14:00',badge:'w',badgeLabel:'Pracovní'},
    {id:3,title:'1on1 s Martinem — příprava agendy',meta:'dnes 14:00',badge:'w',badgeLabel:'Pracovní'},
  ],
  q2:[
    {id:4,title:'Review procesu onboardingu nových agentů',meta:'pátek',badge:'w',badgeLabel:'Pracovní'},
    {id:5,title:'Nastavit automatické SLA upomínky',meta:'příští týden',badge:'w',badgeLabel:'Pracovní'},
    {id:6,title:'Přečíst knihu o vedení týmů',meta:'bez deadline',badge:'p',badgeLabel:'Osobní'},
    {id:7,title:'Naplánovat teambuilding Q2',meta:'do konce dubna',badge:'w',badgeLabel:'Pracovní'},
  ],
  q3:[
    {id:8,title:'Review SLA reportu za Q1',meta:'zítra',badge:'w',badgeLabel:'Pracovní',soon:true},
    {id:9,title:'Odpovědět na dotazy HR',meta:'tento týden',badge:'w',badgeLabel:'Pracovní'},
  ],
  q4:[
    {id:10,title:'Naučit se Excel pivot tabulky',meta:'bez deadline',badge:'p',badgeLabel:'Osobní'},
  ],
};

let tasks = JSON.parse(JSON.stringify(INIT_TASKS));
let draggingId = null, draggingFrom = null;

// ── RENDER ────────────────────────────────────────
function renderAll() {
  ['q1','q2','q3','q4'].forEach(q => {
    const el = document.getElementById('cards-' + q);
    if (!el) return;
    el.innerHTML = '';
    tasks[q].forEach(t => {
      const d = document.createElement('div');
      d.className = 'tc';
      d.draggable = true;
      d.innerHTML =
        '<input class="tc-check" type="checkbox"' + (t.done?' checked':'') + ' onclick="checkTask(' + t.id + ',\'' + q + '\')">' +
        '<div style="flex:1;min-width:0">' +
          '<div class="tc-title" style="' + (t.done?'text-decoration:line-through;color:var(--grey-text)':'') + '">' + t.title + '</div>' +
          '<div class="tc-meta">' +
            '<span class="tc-badge ' + t.badge + '">' + t.badgeLabel + '</span>' +
            (t.meta ? '<span class="' + (t.overdue?'overdue':t.soon?'soon':'') + '">' + (t.overdue?'⚠ ':t.soon?'⚡ ':'') + t.meta + '</span>' : '') +
          '</div>' +
        '</div>';
      d.addEventListener('dragstart', () => { draggingId = t.id; draggingFrom = q; d.classList.add('dragging'); });
      d.addEventListener('dragend', () => d.classList.remove('dragging'));
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
    setTimeout(() => { tasks[q] = tasks[q].filter(x => x.id !== id); renderAll(); toast('Task dokončen → Historie'); }, 350);
  } else renderAll();
}

// ── DRAG & DROP ───────────────────────────────────
function dragOver(e) { e.preventDefault(); e.currentTarget.classList.add('drag-over'); }
function dragLeave(e) { e.currentTarget.classList.remove('drag-over'); }
function drop(e, tq) {
  e.preventDefault(); e.currentTarget.classList.remove('drag-over');
  if (!draggingId || draggingFrom === tq) return;
  const idx = tasks[draggingFrom].findIndex(t => t.id === draggingId);
  if (idx === -1) return;
  const t = tasks[draggingFrom].splice(idx, 1)[0];
  tasks[tq].push(t);
  renderAll();
  toast('Přesunuto → ' + {q1:'Udělat hned',q2:'Naplánovat',q3:'Delegovat',q4:'Eliminovat'}[tq]);
  draggingId = null; draggingFrom = null;
}

// ── TABS ──────────────────────────────────────────
function switchTab(btn, tab) {
  document.querySelectorAll('.demo-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  const matrix = document.getElementById('matrixView');
  const history = document.getElementById('historyView');
  const onenon = document.getElementById('onenonView');
  const sideL = document.querySelector('.demo-sidebar-l');
  const sideR = document.querySelector('.demo-sidebar-r');
  const body = document.getElementById('demoBody');

  const isOnenon = tab === 'onenon';
  matrix.style.display = ['all','work','personal'].includes(tab) ? '' : 'none';
  history.style.display = tab === 'history' ? '' : 'none';
  onenon.style.display = isOnenon ? '' : 'none';

  // v 1on1 skryjeme oba sidebary a rozšíříme layout
  if (sideL) sideL.style.display = isOnenon ? 'none' : '';
  if (sideR) sideR.style.display = isOnenon ? 'none' : '';
  body.style.gridTemplateColumns = isOnenon ? '1fr' : '';

  if (tab === 'work') filterMatrix('w');
  else if (tab === 'personal') filterMatrix('p');
  else filterMatrix(null);
}
function filterMatrix(type) {
  document.querySelectorAll('.tc').forEach(c => {
    if (!type) { c.style.display = ''; return; }
    const b = c.querySelector('.tc-badge');
    c.style.display = (b && b.classList.contains(type)) ? '' : 'none';
  });
}

// ── 1on1 ──────────────────────────────────────────
const ONENON_DATA = {
  martin: {
    profile: {performance:4, potential:'high', mgmt_effort:'low', strength:'Empatie se zákazníky, klidný pod tlakem', development:'Delegování — sklony řešit vše sám'},
    completedItems: ['Follow-up e-mail zákazníkovi Notino','Sdílet šablonu pro eskalace','Přečíst článek o FCR metrikách'],
    notes: [
      {date:'2026-04-22', mood:4, tags:['výkon','feedback'], text:'Probírali Q1 výsledky. Zlepšení FCR na 74 %. Pochvala za zvládnutí výpadku API.',
       actions:[{text:'Připravit podklady pro Q2 review', done:false},{text:'Sdílet šablonu pro eskalace', done:true}]},
      {date:'2026-04-08', mood:3, tags:['rozvoj'], text:'Řešili delegování. Martin má tendenci řešit vše sám — domluven experiment: 1 týden předávat Q3 tasky kolegům.',
       actions:[{text:'Follow-up e-mail zákazníkovi', done:true}]},
    ]
  },
  jana: {
    profile: {performance:5, potential:'high', mgmt_effort:'low', strength:'Rychlé učení, iniciativa', development:'Prezentační dovednosti'},
    completedItems: ['Vytvořit plán mentoringu','Absolvovat onboarding junior agentů'],
    notes: [
      {date:'2026-04-14', mood:5, tags:['rozvoj'], text:'Jana projevila zájem o mentoring juniorních agentů. Domluven pilotní projekt na Q2.',
       actions:[{text:'Vytvořit plán mentoringu', done:true}]},
    ]
  },
  pavel: {
    profile: {performance:3, potential:'medium', mgmt_effort:'high', strength:'Technické znalosti produktu', development:'Komunikace se zákazníky při eskalacích'},
    completedItems: [],
    notes: [
      {date:'2026-03-21', mood:3, tags:['osobní'], text:'Krátká schůzka. Pavel řešil osobní situaci, domluvena flexibilita na 2 týdny.',
       actions:[{text:'Zkontrolovat situaci po 2 týdnech', done:false}]},
    ]
  },
};

let selectedPerson = 'martin';

function selectPerson(name) {
  selectedPerson = name;
  document.querySelectorAll('.onenon-person-btn').forEach(b => b.classList.remove('active'));
  const btn = document.getElementById('person-' + name);
  if (btn) btn.classList.add('active');
  renderOnenon();
}

function renderOnenon() {
  const main = document.getElementById('onenonMain');
  const data = ONENON_DATA[selectedPerson];
  const names = {martin:'Martin K.',jana:'Jana S.',pavel:'Pavel H.'};

  // Levý panel: profil osoby
  const p = data.profile;
  const badgeColor = {low:'#4CAF50',medium:'#F5A623',high:'#E05C4E'};
  const potLabel = {low:'Nízký',medium:'Střední',high:'Vysoký'};
  const effortLabel = {low:'Nízká',medium:'Střední',high:'Vysoká'};
  const profileHtml =
    '<div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:7px;padding:10px 12px;margin-bottom:12px;font-size:12px">' +
      '<div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--grey-text);margin-bottom:8px">Profil</div>' +
      (p.performance ? '<div style="display:flex;justify-content:space-between;margin-bottom:5px"><span style="color:var(--grey-text)">Výkon</span><span style="color:#F5A623">' + '★'.repeat(p.performance) + '☆'.repeat(5-p.performance) + '</span></div>' : '') +
      (p.potential ? '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px"><span style="color:var(--grey-text)">Potenciál</span><span style="background:' + badgeColor[p.potential] + ';color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px">' + (potLabel[p.potential]||p.potential) + '</span></div>' : '') +
      (p.mgmt_effort ? '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px"><span style="color:var(--grey-text)">Náročnost</span><span style="background:' + badgeColor[p.mgmt_effort] + ';color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px">' + (effortLabel[p.mgmt_effort]||p.mgmt_effort) + '</span></div>' : '') +
      (p.strength ? '<div style="margin-bottom:5px"><div style="color:var(--grey-text);font-size:10px;margin-bottom:1px">Silná stránka</div>' + p.strength + '</div>' : '') +
      (p.development ? '<div><div style="color:var(--grey-text);font-size:10px;margin-bottom:1px">Oblast rozvoje</div>' + p.development + '</div>' : '') +
    '</div>';

  // Dokončené úkoly (místo SLA)
  const completed = data.completedItems || [];
  const completedHtml = completed.length
    ? '<div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:7px;padding:10px 12px;font-size:12px">' +
        '<div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--grey-text);margin-bottom:8px">Dokončené úkoly</div>' +
        completed.map(t => '<div style="display:flex;align-items:center;gap:6px;padding:3px 0;border-bottom:1px solid var(--grey-border)"><span style="color:#2E7D3F;font-size:11px">✓</span><span>' + t + '</span></div>').join('') +
      '</div>'
    : '<div style="background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:7px;padding:10px 12px;font-size:12px;color:var(--grey-text)">Žádné dokončené úkoly</div>';

  document.getElementById('onenonProfile').innerHTML = profileHtml + completedHtml;

  let html = '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">' +
    '<div style="font-size:13px;font-weight:700;color:var(--navy)">' + names[selectedPerson] + '</div>' +
    '<button class="mbtn mbtn-primary" style="font-size:11px;height:28px;padding:0 12px" onclick="toast(\'Formulář nové schůzky — dostupné v reálné apce\')">+ Schůzka</button>' +
    '</div>';
  data.notes.forEach(n => {
    const mood = n.mood ? '★'.repeat(n.mood) + '☆'.repeat(5-n.mood) : '';
    const tags = (n.tags||[]).map(t => '<span class="onenon-tag">' + t + '</span>').join('');
    const actions = (n.actions||[]).map(a =>
      '<div class="onenon-action"><span class="onenon-check' + (a.done?' done':'') + '"></span>' +
      '<span class="onenon-action-text' + (a.done?' done':'') + '">' + a.text + '</span></div>'
    ).join('');
    html += '<div class="onenon-note-card">' +
      '<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px">' +
        '<div><div class="onenon-note-date">' + n.date + '</div>' +
        (mood ? '<div class="onenon-mood">' + mood + '</div>' : '') +
        (tags ? '<div style="margin-top:3px">' + tags + '</div>' : '') + '</div>' +
        '<button style="background:none;border:none;font-size:11px;color:var(--grey-text);cursor:pointer" onclick="toast(\'Editace záznamu — dostupné v reálné apce\')">Upravit</button>' +
      '</div>' +
      (n.text ? '<div style="font-size:12px;color:var(--navy);white-space:pre-wrap;margin-bottom:8px">' + n.text + '</div>' : '') +
      (n.actions && n.actions.length ? '<div style="font-size:10px;font-weight:700;color:var(--grey-text);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px">Action items</div>' + actions : '') +
      '</div>';
  });
  main.innerHTML = html;
}

// ── QC ────────────────────────────────────────────
function openQC() { document.getElementById('qcOverlay').classList.add('show'); document.getElementById('qcInput').focus(); }
function closeQC() { document.getElementById('qcOverlay').classList.remove('show'); document.getElementById('qcInput').value=''; }
function qcSave() {
  const title = document.getElementById('qcInput').value.trim();
  if (!title) return;
  const q = document.getElementById('qcQ').value;
  const type = document.getElementById('qcType').value;
  const labels = {w:'Pracovní',p:'Osobní'};
  const maxId = Math.max(...Object.values(tasks).flat().map(t=>t.id), 0);
  tasks[q].push({id:maxId+1,title,meta:'bez deadline',badge:type,badgeLabel:labels[type]});
  renderAll(); closeQC();
  toast('Task přidán → ' + {q1:'Udělat hned',q2:'Naplánovat',q3:'Delegovat',q4:'Eliminovat'}[q]);
}

// ── AI ────────────────────────────────────────────
function openAI() { document.getElementById('aiOverlay').classList.add('show'); }
function closeAI() { document.getElementById('aiOverlay').classList.remove('show'); }
function applyAI() {
  const i1 = tasks.q3.findIndex(t => t.title.includes('Review SLA'));
  if (i1 > -1) { const t = tasks.q3.splice(i1,1)[0]; tasks.q1.push(t); }
  const i2 = tasks.q4.findIndex(t => t.title.includes('Excel'));
  if (i2 > -1) { const t = tasks.q4.splice(i2,1)[0]; tasks.q2.push(t); }
  closeAI(); renderAll(); toast('AI návrhy aplikovány — 2 tasky přeřazeny');
}

// ── TOAST ─────────────────────────────────────────
function toast(msg) {
  const el = document.getElementById('toast');
  el.textContent = msg; el.style.opacity='1'; el.style.transform='translateX(-50%) translateY(0)';
  clearTimeout(window._tt);
  window._tt = setTimeout(() => { el.style.opacity='0'; el.style.transform='translateX(-50%) translateY(16px)'; }, 2400);
}

// ── KEYBOARD ──────────────────────────────────────
document.addEventListener('keydown', e => {
  if ((e.metaKey||e.ctrlKey) && e.key==='k') { e.preventDefault(); openQC(); }
  if (e.key==='Escape') { closeQC(); closeAI(); }
  if (e.key==='Enter' && document.getElementById('qcOverlay').classList.contains('show')) qcSave();
});

// ── NAV ───────────────────────────────────────────
window.addEventListener('scroll', () => {
  let cur = '';
  ['demo','features','integrations','security','how'].forEach(id => {
    const el = document.getElementById(id);
    if (el && window.scrollY >= el.offsetTop - 80) cur = id;
  });
  document.querySelectorAll('.nav a').forEach(a => a.classList.toggle('active', a.getAttribute('href')==='#'+cur));
});

// ── INIT ──────────────────────────────────────────
renderAll();
selectPerson('martin');
</script>
</body>
</html>
