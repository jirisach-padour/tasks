<?php
require_once __DIR__ . '/config.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tasks</title>
<script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<style>
:root{--red:#E05C4E;--red-hover:#C94F42;--navy:#1B3468;--grey-bg:#F4F5F7;--grey-border:#DDE1E7;--grey-text:#5E6778;--white:#FFFFFF;--radius:8px;--font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--font);font-size:14px;background:var(--grey-bg);color:var(--navy)}
/* Header */
.app-header{background:var(--navy);padding:20px 0 0}
.container{max-width:1280px;margin:0 auto;padding:0 20px}
.header-inner{display:flex;align-items:center;justify-content:space-between;padding-bottom:14px}
.app-header h1{color:#fff;font-size:20px;font-weight:700;letter-spacing:-.2px}
.header-desc{color:rgba(255,255,255,.55);font-size:12px;margin-top:2px}
.header-actions{display:flex;gap:8px;align-items:center}
/* Tabs */
.tab-bar{display:flex;overflow-x:auto;-webkit-overflow-scrolling:touch}
.tab-bar::-webkit-scrollbar{display:none}
.tab{padding:10px 20px;font-size:13px;font-weight:600;border:none;background:transparent;color:rgba(255,255,255,.55);cursor:pointer;border-bottom:3px solid transparent;white-space:nowrap;transition:all .15s;font-family:var(--font)}
.tab.active{color:#fff;border-bottom:3px solid var(--red)}
.tab:hover:not(.active){color:rgba(255,255,255,.85)}
/* Layout */
.layout{display:grid;grid-template-columns:270px 1fr;gap:18px;margin-top:18px;padding-bottom:80px;align-items:start}
/* Panel */
.panel{background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:16px;box-shadow:0 1px 4px rgba(0,0,0,.07)}
.panel+.panel{margin-top:14px}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--grey-text);margin-bottom:12px;display:flex;align-items:center;justify-content:space-between}
/* KPI */
.kpi-row{display:flex;gap:10px;margin-bottom:12px}
.kpi-card{flex:1;background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:var(--radius);padding:10px 12px}
.kpi-label{font-size:10px;color:var(--grey-text);font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.kpi-value{font-size:22px;font-weight:700;color:var(--navy);line-height:1.1;margin-top:2px}
/* Eisenhower */
.matrix{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.quadrant{background:var(--white);border:1px solid var(--grey-border);border-radius:var(--radius);padding:14px;min-height:160px}
.q-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.q-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--grey-text);display:flex;align-items:center;gap:5px}
.q-add-btn{background:none;border:none;cursor:pointer;color:var(--grey-text);font-size:18px;line-height:1;padding:0 2px;font-weight:300}
.q-add-btn:hover{color:var(--navy)}
/* Task card */
.task-card{display:flex;align-items:flex-start;gap:8px;padding:8px 10px;background:var(--grey-bg);border-radius:6px;margin-bottom:6px;border:1px solid var(--grey-border);cursor:pointer;transition:border-color .1s}
.task-card:hover{border-color:#bbc}
.task-checkbox{width:16px;height:16px;flex-shrink:0;margin-top:2px;accent-color:var(--red);cursor:pointer}
.task-body{flex:1;min-width:0}
.task-title{font-size:13px;font-weight:500;line-height:1.4;word-break:break-word}
.task-title.done-text{text-decoration:line-through;color:var(--grey-text)}
.task-meta{font-size:11px;color:var(--grey-text);margin-top:3px;display:flex;gap:5px;flex-wrap:wrap;align-items:center}
.badge{font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;white-space:nowrap}
.badge-work{background:#E0E8F5;color:#1B3468}
.badge-personal{background:#E8F5E9;color:#2E7D3F}
.badge-daktela{background:#FFF4E0;color:#A06000}
.badge-ai{background:#E8F0FE;color:#1a56db}
.task-del{background:none;border:none;cursor:pointer;color:var(--grey-border);font-size:14px;padding:0;flex-shrink:0;margin-top:1px}
.task-del:hover{color:#E63327}
/* Add task inline */
.add-inline{display:flex;gap:6px;margin-top:8px}
.add-inline input{flex:1;height:30px;padding:0 8px;border:1px solid var(--grey-border);border-radius:6px;font-size:12px;font-family:var(--font);outline:none}
.add-inline input:focus{border-color:var(--navy)}
.add-inline button{height:30px;padding:0 12px;background:var(--navy);color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:700;cursor:pointer}
/* Checklist */
.cl-item{display:flex;align-items:center;gap:8px;padding:6px 2px;border-bottom:1px solid var(--grey-border)}
.cl-item:last-child{border-bottom:none}
.cl-item input[type=checkbox]{accent-color:var(--red);width:15px;height:15px;flex-shrink:0;cursor:pointer}
.cl-item-title{font-size:13px;flex:1;min-width:0}
.cl-item-title.done{text-decoration:line-through;color:var(--grey-text)}
.cl-del{background:none;border:none;cursor:pointer;color:var(--grey-border);font-size:13px;flex-shrink:0;padding:0}
.cl-del:hover{color:#E63327}
.cl-add-row{display:flex;gap:6px;margin-top:10px}
.cl-add-row input{flex:1;height:30px;padding:0 8px;border:1px solid var(--grey-border);border-radius:6px;font-size:13px;font-family:var(--font);outline:none}
.cl-add-row input:focus{border-color:var(--navy)}
.cl-add-row button{height:30px;padding:0 14px;background:var(--red);color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer}
/* Daktela tickets */
.ticket-row{display:flex;align-items:center;gap:7px;padding:7px 0;border-bottom:1px solid var(--grey-border)}
.ticket-row:last-child{border-bottom:none}
.stage-pill{font-size:10px;font-weight:600;padding:2px 7px;border-radius:4px;white-space:nowrap;flex-shrink:0}
.stage-OPEN{background:#E3F5E8;color:#2E7D3F}
.stage-WAIT{background:#FFF4E0;color:#A06000}
.ticket-title{font-size:12px;font-weight:500;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ticket-add-btn{font-size:11px;padding:3px 8px;background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:4px;cursor:pointer;color:var(--navy);font-weight:600;white-space:nowrap;flex-shrink:0}
.ticket-add-btn:hover{background:var(--grey-border)}
/* Calendar */
.cal-item{display:flex;gap:8px;padding:6px 0;border-bottom:1px solid var(--grey-border);font-size:12px}
.cal-item:last-child{border-bottom:none}
.cal-time{color:var(--grey-text);font-weight:600;min-width:36px;flex-shrink:0}
.cal-title{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:none;border-radius:var(--radius);font-size:13px;font-weight:600;font-family:var(--font);cursor:pointer;transition:background .15s}
.btn-primary{background:var(--red);color:#fff}.btn-primary:hover{background:var(--red-hover)}
.btn-secondary{background:var(--white);color:var(--navy);border:1px solid var(--grey-border)}.btn-secondary:hover{background:var(--grey-bg)}
.btn-ghost{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.2)}.btn-ghost:hover{background:rgba(255,255,255,.25)}
.action-row{display:flex;gap:10px;margin-top:14px;flex-wrap:wrap}
/* Historia */
.history-group{margin-bottom:16px}
.history-group-title{font-size:11px;font-weight:700;color:var(--grey-text);text-transform:uppercase;letter-spacing:.5px;padding:8px 0 6px;border-bottom:1px solid var(--grey-border);margin-bottom:8px;display:flex;align-items:center;justify-content:space-between}
.history-task{display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;color:var(--grey-text);font-size:13px}
.history-task:hover{background:var(--grey-bg)}
.history-time{font-size:11px;color:var(--grey-text);margin-left:auto;white-space:nowrap}
/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;display:flex;align-items:center;justify-content:center;padding:20px}
.modal{background:var(--white);border-radius:12px;padding:28px;width:100%;max-width:520px;box-shadow:0 8px 40px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto}
.modal h2{font-size:16px;font-weight:700;margin-bottom:20px}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--grey-text);margin-bottom:5px;text-transform:uppercase;letter-spacing:.3px}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:8px 10px;border:1px solid var(--grey-border);border-radius:var(--radius);font-size:13px;font-family:var(--font);outline:none;color:var(--navy);transition:border-color .15s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--navy)}
.form-group textarea{min-height:80px;resize:vertical}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.modal-actions{display:flex;gap:10px;margin-top:20px;justify-content:flex-end}
.modal-actions .btn{padding:8px 20px}
/* AI modal */
.ai-suggestion{padding:12px;background:var(--grey-bg);border-radius:var(--radius);margin-bottom:8px;border:1px solid var(--grey-border)}
.ai-suggestion-title{font-size:13px;font-weight:600}
.ai-suggestion-reason{font-size:12px;color:var(--grey-text);margin-top:4px}
.ai-suggestion-change{font-size:11px;margin-top:6px;display:flex;align-items:center;gap:6px}
.q-tag{font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;background:#E0E8F5;color:#1B3468}
.q-tag.urgent_important{background:#FEE8E7;color:#E63327}
.q-tag.important{background:#E0E8F5;color:#1B3468}
.q-tag.urgent{background:#FFF4E0;color:#A06000}
.q-tag.other{background:var(--grey-bg);color:var(--grey-text)}
/* Daktela auth modal */
.daktela-connect{text-align:center;padding:8px 0}
/* Floating + button (mobile) */
.fab{display:none;position:fixed;bottom:20px;right:20px;width:52px;height:52px;border-radius:50%;background:var(--red);color:#fff;font-size:24px;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.25);z-index:100;align-items:center;justify-content:center;font-family:var(--font)}
/* Sidebar hamburger (tablet) */
.sidebar-toggle{display:none;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:20px;cursor:pointer;padding:6px 10px;border-radius:6px;font-family:var(--font)}
/* Loading */
.loading-overlay{position:fixed;inset:0;background:rgba(255,255,255,.6);z-index:300;display:flex;align-items:center;justify-content:center}
.spinner{width:32px;height:32px;border:3px solid var(--grey-border);border-top-color:var(--navy);border-radius:50%;animation:spin .7s linear infinite}
input[type=search]::placeholder{color:rgba(255,255,255,.5)}
input[type=search]::-webkit-search-cancel-button{filter:invert(1);opacity:.6;cursor:pointer}
/* Overdue */
.task-card.overdue{border-color:#E63327;background:#FFF5F5}
.task-card.overdue .task-title{color:#c0392b}
.overdue-badge{font-size:10px;font-weight:700;color:#E63327;background:#FEE8E7;padding:1px 6px;border-radius:4px}
/* Drag & drop */
.task-card.dragging{opacity:.4}
.quadrant.drag-over{background:#EBF0FF;border-color:#1B3468}
/* Inline edit */
.task-title-input{font-size:13px;font-weight:500;width:100%;border:none;border-bottom:1px solid var(--navy);background:transparent;outline:none;font-family:var(--font);padding:0;color:var(--navy)}
/* Q1 alert badge */
.q1-alert{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#E63327;color:#fff;font-size:10px;font-weight:700;border-radius:50%;margin-left:6px;animation:pulse 1.5s ease-in-out infinite}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.15)}}
/* Quick capture modal */
.qc-overlay{position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:300;display:flex;align-items:flex-start;justify-content:center;padding-top:120px}
.qc-modal{background:#fff;border-radius:12px;padding:20px 24px;width:100%;max-width:480px;box-shadow:0 12px 48px rgba(0,0,0,.25)}
.qc-modal input{width:100%;font-size:16px;border:none;outline:none;font-family:var(--font);color:var(--navy);padding:4px 0}
.qc-hint{font-size:11px;color:var(--grey-text);margin-top:10px}
@keyframes spin{to{transform:rotate(360deg)}}
/* Toast */
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:var(--navy);color:#fff;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;z-index:400;pointer-events:none;opacity:0;transition:opacity .2s}
.toast.show{opacity:1}
/* Responsive */
@media(max-width:900px){
  .layout{grid-template-columns:1fr}
  .sidebar{display:none}.sidebar.open{display:block}
  .sidebar-toggle{display:inline-flex;align-items:center}
  .fab{display:flex}
}
@media(max-width:600px){
  .matrix{grid-template-columns:1fr}
  .form-row{grid-template-columns:1fr}
  .header-actions .btn{display:none}
}
</style>
</head>
<body>

<header class="app-header">
  <div class="container">
    <div class="header-inner">
      <div style="display:flex;align-items:center;gap:12px">
        <button class="sidebar-toggle" id="sidebarToggle">☰</button>
        <div>
          <h1>Tasks</h1>
          <p class="header-desc">Osobní prioritizace · Jiří Šach</p>
        </div>
      </div>
      <div class="header-actions" id="headerActions"></div>
    </div>
    <div class="tab-bar" id="tabBar"></div>
  </div>
</header>

<main class="container">
  <div id="app-root" style="position:absolute;width:0;height:0;overflow:visible"></div>
<div class="layout" id="layout">
    <aside class="sidebar" id="sidebar"></aside>
    <div id="mainContent"></div>
    <aside class="sidebar" id="sidebar"></aside>
    <div id="mainContent"></div>
  </div>
</main>

<button class="fab" id="fab">+</button>
<div id="modals"></div>
<div class="toast" id="toast"></div>

<script type="text/babel">
const { useState, useEffect, useRef, useCallback } = React;

// ---- Helpers ----
function esc(s) {
  return String(s || '');
}

function toast(msg) {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2500);
}

const QUADRANTS = [
  { key: 'urgent_important', label: '🔴 Urgentní + Důležité' },
  { key: 'important',        label: '🔵 Důležité' },
  { key: 'urgent',           label: '🟠 Urgentní' },
  { key: 'other',            label: '⚪ Backlog' },
];

const Q_LABELS = {
  urgent_important: 'Urgentní + Důležité',
  important: 'Důležité',
  urgent: 'Urgentní',
  other: 'Backlog',
};

async function apiFetch(action, method = 'GET', body = null, params = {}) {
  const qs = new URLSearchParams({ action, ...params }).toString();
  const opts = {
    method,
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
  };
  if (body) opts.body = JSON.stringify(body);
  const r = await fetch('/tasks/api.php?' + qs, opts);
  const json = await r.json();
  if (!r.ok) throw new Error(json.error || 'API chyba');
  return json;
}

// ---- TaskModal ----
function TaskModal({ task, defaultQuadrant, defaultType, defaultTickets, availableTickets, onSave, onClose }) {
  const initial = task || {};
  const [title, setTitle] = useState(initial.title || '');
  const [description, setDescription] = useState(initial.description || '');
  const [aiContext, setAiContext] = useState(initial.ai_context || '');
  const [quadrant, setQuadrant] = useState(initial.quadrant || defaultQuadrant || 'other');
  const [type, setType] = useState(initial.type || defaultType || 'work');
  const [dueDate, setDueDate] = useState(initial.due_date || '');
  const [daktelaTickets, setDaktelaTickets] = useState(() => {
    if (initial.daktela_tickets) {
      try { return JSON.parse(initial.daktela_tickets); } catch(e) { return []; }
    }
    return defaultTickets || [];
  });
  const [saving, setSaving] = useState(false);

  function removeTicket(name) {
    setDaktelaTickets(prev => prev.filter(n => n !== name));
  }

  function addTicket(ticket) {
    if (!daktelaTickets.includes(ticket.name)) {
      setDaktelaTickets(prev => [...prev, ticket.name]);
    }
  }

  const attachable = (availableTickets || []).filter(t => !daktelaTickets.includes(t.name));

  async function handleSave() {
    if (!title.trim()) return;
    setSaving(true);
    try {
      await onSave({ title, description, ai_context: aiContext, quadrant, type, due_date: dueDate, daktela_tickets: daktelaTickets });
      onClose();
    } catch(e) { toast('Chyba: ' + e.message); }
    setSaving(false);
  }

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal">
        <h2>{task ? 'Upravit task' : 'Nový task'}</h2>
        <div className="form-group">
          <label>Název</label>
          <input value={title} onChange={e => setTitle(e.target.value)} autoFocus placeholder="Co je třeba udělat?" onKeyDown={e => e.key === 'Enter' && handleSave()} />
        </div>
        <div className="form-row">
          <div className="form-group">
            <label>Kvadrant</label>
            <select value={quadrant} onChange={e => setQuadrant(e.target.value)}>
              {QUADRANTS.map(q => <option key={q.key} value={q.key}>{q.label}</option>)}
            </select>
          </div>
          <div className="form-group">
            <label>Typ</label>
            <select value={type} onChange={e => setType(e.target.value)}>
              <option value="work">Pracovní</option>
              <option value="personal">Osobní</option>
            </select>
          </div>
        </div>
        <div className="form-group">
          <label>Deadline</label>
          <input type="date" value={dueDate} onChange={e => setDueDate(e.target.value)} />
        </div>
        <div className="form-group">
          <label>Popis</label>
          <textarea value={description} onChange={e => setDescription(e.target.value)} placeholder="Volitelný popis..." />
        </div>
        <div className="form-group">
          <label>Kontext pro AI</label>
          <textarea value={aiContext} onChange={e => setAiContext(e.target.value)} placeholder="Popiš složitost, blokátory, závislosti — AI to použije při návrhu priorit..." />
        </div>
        <div className="form-group">
          <label>Daktela tickety</label>
          {daktelaTickets.length > 0 && (
            <div style={{display:'flex',flexWrap:'wrap',gap:6,marginBottom:8}}>
              {daktelaTickets.map(name => (
                <span key={name} style={{display:'inline-flex',alignItems:'center',gap:4,background:'#FFF4E0',color:'#A06000',fontSize:'11px',fontWeight:700,padding:'3px 8px',borderRadius:20}}>
                  <a href={'https://daktela.daktela.com/tickets/update/' + name} target="_blank" rel="noreferrer" style={{color:'#A06000',textDecoration:'none'}}>{name}</a>
                  <button onClick={() => removeTicket(name)} style={{background:'none',border:'none',cursor:'pointer',color:'#A06000',fontSize:'13px',lineHeight:1,padding:0,marginLeft:2}}>×</button>
                </span>
              ))}
            </div>
          )}
          {attachable.length > 0 && (
            <div style={{display:'flex',flexWrap:'wrap',gap:5}}>
              {attachable.map(t => (
                <button key={t.name} onClick={() => addTicket(t)} style={{fontSize:'11px',padding:'3px 8px',background:'var(--grey-bg)',border:'1px solid var(--grey-border)',borderRadius:4,cursor:'pointer',color:'var(--navy)',fontWeight:600}}>
                  + {t.title || t.name}
                </button>
              ))}
            </div>
          )}
          {daktelaTickets.length === 0 && attachable.length === 0 && (
            <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Připoj Daktelu v sidebaru pro výběr ticketů</div>
          )}
        </div>
        <div className="modal-actions">
          <button className="btn btn-secondary" onClick={onClose}>Zrušit</button>
          <button className="btn btn-primary" onClick={handleSave} disabled={saving}>
            {saving ? 'Ukládám...' : 'Uložit'}
          </button>
        </div>
      </div>
    </div>
  );
}

// ---- DaktelaAuthModal ----
function DaktelaAuthModal({ onConnected, onClose }) {
  const [username, setUsername] = useState('sachj');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function handleLogin() {
    if (!password) return;
    setLoading(true);
    setError('');
    try {
      const data = await apiFetch('daktela_login', 'POST', { username, password });
      sessionStorage.setItem('daktela_token', data.accessToken);
      onConnected(data.accessToken);
      onClose();
    } catch(e) {
      setError(e.message);
    }
    setLoading(false);
  }

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal" style={{maxWidth: '380px'}}>
        <h2>Připojit Daktelu</h2>
        <p style={{fontSize:'13px',color:'var(--grey-text)',marginBottom:'16px'}}>Přihlašovací údaje slouží pouze pro stažení ticketů — nejsou ukládány na server.</p>
        {error && <div style={{background:'#FEE8E7',color:'#E63327',padding:'10px 14px',borderRadius:'6px',fontSize:'13px',marginBottom:'14px'}}>{error}</div>}
        <div className="form-group">
          <label>Uživatelské jméno</label>
          <input value={username} onChange={e => setUsername(e.target.value)} />
        </div>
        <div className="form-group">
          <label>Heslo</label>
          <input type="password" value={password} onChange={e => setPassword(e.target.value)} autoFocus onKeyDown={e => e.key === 'Enter' && handleLogin()} />
        </div>
        <div className="modal-actions">
          <button className="btn btn-secondary" onClick={onClose}>Zrušit</button>
          <button className="btn btn-primary" onClick={handleLogin} disabled={loading}>
            {loading ? 'Přihlašuji...' : 'Připojit'}
          </button>
        </div>
      </div>
    </div>
  );
}

// ---- AiSuggestModal ----
function AiSuggestModal({ suggestions, tasks, onApply, onClose }) {
  const taskMap = {};
  tasks.forEach(t => { taskMap[t.id] = t; });
  const [selected, setSelected] = useState(
    suggestions.map(s => ({ ...s, accepted: s.quadrant !== (taskMap[s.id] || {}).quadrant }))
  );

  function toggleAccept(idx) {
    setSelected(prev => prev.map((s, i) => i === idx ? { ...s, accepted: !s.accepted } : s));
  }

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal" style={{maxWidth:'600px'}}>
        <h2>✦ AI návrh priorit</h2>
        <p style={{fontSize:'13px',color:'var(--grey-text)',marginBottom:'16px'}}>Odškrtni návrhy které chceš přijmout, pak klikni Použít.</p>
        {selected.map((s, i) => {
          const t = taskMap[s.id];
          if (!t) return null;
          const changed = s.quadrant !== t.quadrant;
          return (
            <div key={s.id} className="ai-suggestion" style={{opacity: s.accepted ? 1 : .5}}>
              <div style={{display:'flex',alignItems:'center',gap:8}}>
                <input type="checkbox" checked={s.accepted} onChange={() => toggleAccept(i)} style={{accentColor:'var(--red)',width:15,height:15}} />
                <span className="ai-suggestion-title">{t.title}</span>
              </div>
              <div className="ai-suggestion-reason">{s.reason}</div>
              {changed && (
                <div className="ai-suggestion-change">
                  <span className={'q-tag ' + t.quadrant}>{Q_LABELS[t.quadrant]}</span>
                  <span style={{color:'var(--grey-text)'}}>→</span>
                  <span className={'q-tag ' + s.quadrant}>{Q_LABELS[s.quadrant]}</span>
                </div>
              )}
              {!changed && <div style={{fontSize:'11px',color:'#2E7D3F',marginTop:4}}>✓ Kvadrant je správně</div>}
            </div>
          );
        })}
        <div className="modal-actions">
          <button className="btn btn-secondary" onClick={onClose}>Zrušit</button>
          <button className="btn btn-primary" onClick={() => { onApply(selected.filter(s => s.accepted)); onClose(); }}>
            Použít vybrané
          </button>
        </div>
      </div>
    </div>
  );
}

// ---- TaskCard ----
function TaskCard({ task, onToggleDone, onEdit, onDelete, onInlineEdit, onDragStart }) {
  const tickets = task.daktela_tickets || [];
  const [editing, setEditing] = useState(false);
  const [editVal, setEditVal] = useState(task.title);
  const inputRef = useRef(null);

  const today = new Date().toISOString().split('T')[0];
  const isOverdue = task.status === 'open' && task.due_date && task.due_date < today;

  function startEdit(e) {
    e.stopPropagation();
    setEditVal(task.title);
    setEditing(true);
    setTimeout(() => inputRef.current && inputRef.current.focus(), 0);
  }

  function commitEdit() {
    setEditing(false);
    if (editVal.trim() && editVal.trim() !== task.title) {
      onInlineEdit(task, editVal.trim());
    }
  }

  const cardClass = 'task-card' + (isOverdue ? ' overdue' : '');

  return (
    <div
      className={cardClass}
      onClick={() => !editing && onEdit(task)}
      draggable
      onDragStart={e => { e.dataTransfer.setData('taskId', task.id); e.currentTarget.classList.add('dragging'); if (onDragStart) onDragStart(task); }}
      onDragEnd={e => e.currentTarget.classList.remove('dragging')}
    >
      <input
        type="checkbox"
        className="task-checkbox"
        checked={task.status === 'done'}
        onClick={e => e.stopPropagation()}
        onChange={() => onToggleDone(task)}
      />
      <div className="task-body">
        {editing
          ? <input
              ref={inputRef}
              className="task-title-input"
              value={editVal}
              onChange={e => setEditVal(e.target.value)}
              onBlur={commitEdit}
              onKeyDown={e => { if (e.key === 'Enter') commitEdit(); if (e.key === 'Escape') setEditing(false); }}
              onClick={e => e.stopPropagation()}
            />
          : <div
              className={'task-title' + (task.status === 'done' ? ' done-text' : '')}
              onDoubleClick={startEdit}
              title="Dvojklik = rychlá editace názvu"
            >{task.title}</div>
        }
        <div className="task-meta">
          <span className={'badge ' + (task.type === 'personal' ? 'badge-personal' : 'badge-work')}>
            {task.type === 'personal' ? 'Osobní' : 'Work'}
          </span>
          {tickets.length === 1 && (
            <a href={'https://daktela.daktela.com/tickets/update/' + tickets[0]} target="_blank" rel="noreferrer" onClick={e => e.stopPropagation()} className="badge badge-daktela" style={{textDecoration:'none'}}>{tickets[0]}</a>
          )}
          {tickets.length > 1 && tickets.map(name => (
            <a key={name} href={'https://daktela.daktela.com/tickets/update/' + name} target="_blank" rel="noreferrer" onClick={e => e.stopPropagation()} className="badge badge-daktela" style={{textDecoration:'none'}}>{name}</a>
          ))}
          {task.due_date && (
            <span className={isOverdue ? 'overdue-badge' : ''}>
              {isOverdue ? 'Po termínu: ' : ''}{task.due_date}
            </span>
          )}
        </div>
      </div>
      <button className="task-del" onClick={e => { e.stopPropagation(); onDelete(task); }} title="Smazat">×</button>
    </div>
  );
}

// ---- Quadrant ----
function Quadrant({ q, tasks, filter, onToggleDone, onEdit, onDelete, onAddTask, onInlineEdit, onMoveTask }) {
  const [addTitle, setAddTitle] = useState('');
  const [dragOver, setDragOver] = useState(false);
  const visible = tasks.filter(t =>
    t.quadrant === q.key &&
    t.status === 'open' &&
    (filter === 'all' || t.type === filter)
  );

  async function handleQuickAdd() {
    if (!addTitle.trim()) return;
    await onAddTask({ title: addTitle, quadrant: q.key });
    setAddTitle('');
  }

  function handleDragOver(e) {
    e.preventDefault();
    setDragOver(true);
  }

  function handleDrop(e) {
    e.preventDefault();
    setDragOver(false);
    const taskId = parseInt(e.dataTransfer.getData('taskId'));
    if (taskId) onMoveTask(taskId, q.key);
  }

  return (
    <div
      className={'quadrant' + (dragOver ? ' drag-over' : '')}
      onDragOver={handleDragOver}
      onDragLeave={() => setDragOver(false)}
      onDrop={handleDrop}
    >
      <div className="q-header">
        <div className="q-label">{q.label} {visible.length > 0 && <span style={{fontSize:'10px',fontWeight:400,color:'var(--grey-text)'}}>({visible.length})</span>}</div>
      </div>
      {visible.map(t => (
        <TaskCard key={t.id} task={t} onToggleDone={onToggleDone} onEdit={onEdit} onDelete={onDelete} onInlineEdit={onInlineEdit} />
      ))}
      <div className="add-inline">
        <input
          value={addTitle}
          onChange={e => setAddTitle(e.target.value)}
          placeholder="Přidat task..."
          onKeyDown={e => e.key === 'Enter' && handleQuickAdd()}
        />
        <button onClick={handleQuickAdd}>+</button>
      </div>
    </div>
  );
}

// ---- Checklist Panel ----
function ChecklistPanel({ items, todayDone, onAdd, onToggle, onDelete }) {
  const [newTitle, setNewTitle] = useState('');

  async function handleAdd() {
    if (!newTitle.trim()) return;
    await onAdd(newTitle);
    setNewTitle('');
  }

  const open = items.filter(i => !i.done);
  const done  = items.filter(i => i.done);

  return (
    <div className="panel">
      <div className="section-title">
        Rychlý checklist
        {todayDone > 0 && <span className="badge badge-work">{todayDone} dnes</span>}
      </div>
      {open.map(i => (
        <div key={i.id} className="cl-item">
          <input type="checkbox" checked={false} onChange={() => onToggle(i, true)} />
          <span className="cl-item-title">{i.title}</span>
          <button className="cl-del" onClick={() => onDelete(i)}>×</button>
        </div>
      ))}
      {done.length > 0 && done.map(i => (
        <div key={i.id} className="cl-item">
          <input type="checkbox" checked={true} onChange={() => onToggle(i, false)} />
          <span className="cl-item-title done">{i.title}</span>
          <button className="cl-del" onClick={() => onDelete(i)}>×</button>
        </div>
      ))}
      <div className="cl-add-row">
        <input
          value={newTitle}
          onChange={e => setNewTitle(e.target.value)}
          placeholder="Přidat položku..."
          onKeyDown={e => e.key === 'Enter' && handleAdd()}
        />
        <button onClick={handleAdd}>+</button>
      </div>
    </div>
  );
}

// ---- Daktela Panel ----
function DaktelaPanel({ token, onConnectClick, onCreateTask, onTicketsLoaded, assignedMap }) {
  const [tickets, setTickets] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [showAssigned, setShowAssigned] = useState(false);

  useEffect(() => {
    if (token) loadTickets();
  }, [token]);

  async function loadTickets() {
    setLoading(true);
    setError('');
    try {
      const params = buildDaktelaParams();
      const data = await apiFetch('daktela', 'POST', {
        accessToken: token,
        endpoint: 'tickets',
        params,
      });
      const items = data.result?.data || data.result || [];
      const list = (Array.isArray(items) ? items : []).filter(t => t.stage === 'OPEN');
      setTickets(list);
      if (onTicketsLoaded) onTicketsLoaded(list);
    } catch(e) {
      setError(e.message);
    }
    setLoading(false);
  }

  function buildDaktelaParams() {
    return {
      'filter[logic]': 'and',
      'filter[filters][0][logic]': 'and',
      'filter[filters][0][filters][0][field]': 'user',
      'filter[filters][0][filters][0][operator]': 'in',
      'filter[filters][0][filters][0][value][0]': 'sachj',
      'filter[filters][0][filters][1][field]': 'stage',
      'filter[filters][0][filters][1][operator]': 'in',
      'filter[filters][0][filters][1][value][0]': 'OPEN',
      'filter[filters][1][field]': '_ticketView',
      'filter[filters][1][operator]': 'eq',
      'filter[filters][1][value]': 'default',
      'filter[filters][2][field]': 'id_merge',
      'filter[filters][2][operator]': 'isnull',
      'fields[]': ['name', 'title', 'stage', 'sla_deadline'],
      'take': 100,
    };
  }

  return (
    <div className="panel">
      <div className="section-title">
        Daktela tickety
        <span style={{display:'flex',gap:6}}>
          {token && <button onClick={loadTickets} style={{background:'none',border:'none',cursor:'pointer',fontSize:'12px',color:'var(--grey-text)'}}>↺</button>}
          {token && <button onClick={onConnectClick} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--grey-text)',textDecoration:'underline'}}>přihlásit znovu</button>}
        </span>
      </div>
      {!token && (
        <div className="daktela-connect">
          <p style={{fontSize:'12px',color:'var(--grey-text)',marginBottom:'10px'}}>Připoj Daktelu pro zobrazení otevřených ticketů.</p>
          <button className="btn btn-secondary" style={{width:'100%',fontSize:'12px'}} onClick={onConnectClick}>Připojit Daktelu</button>
        </div>
      )}
      {token && loading && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Načítám...</div>}
      {token && error && <div style={{fontSize:'12px',color:'#E63327'}}>{error} <button onClick={onConnectClick} style={{background:'none',border:'none',cursor:'pointer',color:'var(--navy)',fontSize:'12px',textDecoration:'underline'}}>Přihlásit znovu</button></div>}
      {token && !loading && (() => {
        const am = assignedMap || {};
        const free = tickets.filter(t => !am[t.name]);
        const assigned = tickets.filter(t => am[t.name]);
        return (
          <>
            {free.map(t => (
              <div key={t.name} className="ticket-row">
                <span className={'stage-pill stage-' + (t.stage || 'OPEN')}>{t.stage || 'OPEN'}</span>
                <a className="ticket-title" href={'https://daktela.daktela.com/tickets/update/' + t.name} target="_blank" rel="noreferrer" title={t.title + ' (' + t.name + ')'}>{t.title}</a>
                <button className="ticket-add-btn" onClick={() => onCreateTask(t)}>+ Task</button>
              </div>
            ))}
            {free.length === 0 && assigned.length === 0 && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Žádné otevřené tickety</div>}
            {assigned.length > 0 && (
              <>
                <button onClick={() => setShowAssigned(v => !v)} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--grey-text)',padding:'6px 0 2px',width:'100%',textAlign:'left',fontFamily:'var(--font)'}}>
                  {showAssigned ? '▾' : '▸'} Přiřazené ({assigned.length})
                </button>
                {showAssigned && (
                  <div style={{background:'var(--grey-bg)',borderRadius:6,padding:'6px 8px',marginTop:4}}>
                    {assigned.map(t => (
                      <div key={t.name} style={{display:'flex',alignItems:'center',gap:6,padding:'5px 0',borderBottom:'1px solid var(--grey-border)',fontSize:'12px'}}>
                        <span className={'stage-pill stage-' + (t.stage || 'OPEN')} style={{flexShrink:0}}>{t.stage || 'OPEN'}</span>
                        <a href={'https://daktela.daktela.com/tickets/update/' + t.name} target="_blank" rel="noreferrer" style={{flex:1,minWidth:0,overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap',color:'var(--navy)',textDecoration:'none',fontWeight:500}}>{t.title}</a>
                        <span style={{color:'var(--grey-text)',flexShrink:0,whiteSpace:'nowrap'}}>→ {am[t.name]}</span>
                      </div>
                    ))}
                  </div>
                )}
              </>
            )}
          </>
        );
      })()}
    </div>
  );
}

// ---- Calendar Panel ----
function CalendarPanel({ events, connected, onConnect, onDisconnect }) {
  const today = new Date().toISOString().split('T')[0];
  const todayEvents = events.filter(e => e.date === today);
  const tmrEvents   = events.filter(e => e.date !== today);

  return (
    <div className="panel">
      <div className="section-title">
        Kalendář
        {connected
          ? <button onClick={onDisconnect} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--grey-text)'}}>Odpojit</button>
          : <button onClick={onConnect} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--navy)',fontWeight:700}}>Propojit</button>
        }
      </div>
      {!connected && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Propoj Google Calendar pro zobrazení událostí.</div>}
      {connected && todayEvents.length === 0 && tmrEvents.length === 0 && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Dnes žádné události</div>}
      {connected && todayEvents.length > 0 && (
        <>
          <div style={{fontSize:'10px',color:'var(--grey-text)',fontWeight:700,textTransform:'uppercase',letterSpacing:'.4px',marginBottom:4}}>Dnes</div>
          {todayEvents.map((e, i) => (
            <div key={i} className="cal-item">
              <span className="cal-time">{e.time}</span>
              <span className="cal-title">{e.title}</span>
            </div>
          ))}
        </>
      )}
      {connected && tmrEvents.length > 0 && (
        <>
          <div style={{fontSize:'10px',color:'var(--grey-text)',fontWeight:700,textTransform:'uppercase',letterSpacing:'.4px',marginTop:10,marginBottom:4}}>Zítra</div>
          {tmrEvents.map((e, i) => (
            <div key={i} className="cal-item">
              <span className="cal-time">{e.time}</span>
              <span className="cal-title">{e.title}</span>
            </div>
          ))}
        </>
      )}
    </div>
  );
}

// ---- KPI Panel ----
function KpiPanel({ todayDone, totalOpen }) {
  return (
    <div className="panel">
      <div className="section-title">Dnešní výkon</div>
      <div className="kpi-row">
        <div className="kpi-card">
          <div className="kpi-label">Hotovo dnes</div>
          <div className="kpi-value" style={{color: todayDone > 0 ? '#2E7D3F' : 'var(--navy)'}}>{todayDone}</div>
        </div>
        <div className="kpi-card">
          <div className="kpi-label">Otevřené</div>
          <div className="kpi-value">{totalOpen}</div>
        </div>
      </div>
    </div>
  );
}

// ---- History View ----
function HistoryView({ filter, onReopen }) {
  const [groups, setGroups] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadHistory();
  }, [filter]);

  async function loadHistory() {
    setLoading(true);
    const type = filter === 'all' ? '' : filter;
    const data = await apiFetch('tasks', 'GET', null, { history: 'week', ...(type ? {type} : {}) });
    const tasks = data.tasks || [];
    const byDate = {};
    tasks.forEach(t => {
      const d = t.done_at ? t.done_at.split(' ')[0] : 'neznámé';
      if (!byDate[d]) byDate[d] = [];
      byDate[d].push(t);
    });
    const today = new Date().toISOString().split('T')[0];
    const yesterday = new Date(Date.now() - 86400000).toISOString().split('T')[0];
    const sorted = Object.entries(byDate).sort((a, b) => b[0].localeCompare(a[0]));
    setGroups(sorted.map(([date, tasks]) => ({
      label: date === today ? 'Dnes' : date === yesterday ? 'Včera' : date,
      tasks,
    })));
    setLoading(false);
  }

  async function handleReopen(task) {
    await onReopen(task);
    setGroups(prev => prev.map(g => ({ ...g, tasks: g.tasks.filter(t => t.id !== task.id) })).filter(g => g.tasks.length > 0));
  }

  if (loading) return <div style={{padding:'20px',color:'var(--grey-text)'}}>Načítám historii...</div>;
  if (!groups.length) return <div className="panel"><p style={{color:'var(--grey-text)',fontSize:'13px'}}>Žádné dokončené tasky tento týden.</p></div>;

  return (
    <div className="panel">
      {groups.map((g, i) => (
        <div key={i} className="history-group">
          <div className="history-group-title">
            {g.label}
            <span className="badge badge-work">{g.tasks.length}</span>
          </div>
          {g.tasks.map(t => (
            <div key={t.id} className="history-task">
              <span style={{color:'var(--grey-text)',fontSize:16,marginRight:4}}>✓</span>
              <span style={{flex:1}}>{t.title}</span>
              <button onClick={() => handleReopen(t)} title="Vrátit do aktivních" style={{background:'none',border:'1px solid var(--grey-border)',borderRadius:4,cursor:'pointer',fontSize:'11px',color:'var(--grey-text)',padding:'2px 7px',marginLeft:8,whiteSpace:'nowrap',flexShrink:0}}>↩ Znovu</button>
              <span className="history-time" style={{marginLeft:6}}>{t.done_at ? t.done_at.split(' ')[1].slice(0,5) : ''}</span>
            </div>
          ))}
        </div>
      ))}
    </div>
  );
}

// ---- QuickCapture ----
function QuickCapture({ onSave, onClose }) {
  const [title, setTitle] = useState('');
  const [saving, setSaving] = useState(false);

  async function handleSave() {
    if (!title.trim()) return;
    setSaving(true);
    await onSave({ title: title.trim(), quadrant: 'other' });
    onClose();
  }

  return (
    <div className="qc-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="qc-modal">
        <input
          autoFocus
          value={title}
          onChange={e => setTitle(e.target.value)}
          placeholder="Co chceš udělat? (Enter = uložit do Backlogu)"
          onKeyDown={e => { if (e.key === 'Enter') handleSave(); if (e.key === 'Escape') onClose(); }}
        />
        <div className="qc-hint">Esc = zrušit · Enter = uložit do Backlogu · pak roztřídíš v matici</div>
      </div>
    </div>
  );
}

// ---- SearchResults ----
function SearchResults({ query, checklistItems, daktelaTickets, onEditTask, onToggleCl }) {
  const [dbTasks, setDbTasks] = useState([]);
  const [loading, setLoading] = useState(false);
  const lq = query.toLowerCase();

  useEffect(() => {
    if (query.length < 2) { setDbTasks([]); return; }
    setLoading(true);
    apiFetch('tasks', 'GET', null, { search: query })
      .then(d => setDbTasks(d.tasks || []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [query]);

  const clMatches = checklistItems.filter(i => i.title.toLowerCase().includes(lq));
  const tkMatches = daktelaTickets.filter(t =>
    (t.title || '').toLowerCase().includes(lq) || t.name.toLowerCase().includes(lq)
  );

  const Q_COLOR = { urgent_important: '#E63327', important: '#1B3468', urgent: '#A06000', other: '#5E6778' };
  const Q_BG = { urgent_important: '#FEE8E7', important: '#E0E8F5', urgent: '#FFF4E0', other: '#F4F5F7' };

  return (
    <div className="panel">
      {loading && <div style={{fontSize:'12px',color:'var(--grey-text)',marginBottom:8}}>Hledám...</div>}
      {dbTasks.length > 0 && (
        <>
          <div style={{fontSize:'11px',fontWeight:700,color:'var(--grey-text)',textTransform:'uppercase',letterSpacing:'.5px',marginBottom:8}}>Tasky ({dbTasks.length})</div>
          {dbTasks.map(t => (
            <div key={t.id} onClick={() => onEditTask(t)} style={{display:'flex',alignItems:'center',gap:8,padding:'7px 8px',borderRadius:6,cursor:'pointer',marginBottom:4,border:'1px solid var(--grey-border)',background:'var(--grey-bg)'}}>
              <span style={{fontSize:'10px',fontWeight:700,padding:'2px 7px',borderRadius:4,background: Q_BG[t.quadrant] || '#F4F5F7',color: Q_COLOR[t.quadrant] || '#5E6778',whiteSpace:'nowrap',flexShrink:0}}>{Q_LABELS[t.quadrant] || t.quadrant}</span>
              <span style={{fontSize:'13px',flex:1,minWidth:0,overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap',textDecoration: t.status === 'done' ? 'line-through' : 'none',color: t.status === 'done' ? 'var(--grey-text)' : 'var(--navy)'}}>{t.title}</span>
            </div>
          ))}
        </>
      )}
      {clMatches.length > 0 && (
        <>
          <div style={{fontSize:'11px',fontWeight:700,color:'var(--grey-text)',textTransform:'uppercase',letterSpacing:'.5px',margin:'12px 0 8px'}}>Checklist ({clMatches.length})</div>
          {clMatches.map(i => (
            <div key={i.id} onClick={() => onToggleCl(i, !i.done)} style={{display:'flex',alignItems:'center',gap:8,padding:'6px 8px',borderRadius:6,cursor:'pointer',marginBottom:4,border:'1px solid var(--grey-border)',background:'var(--grey-bg)'}}>
              <input type="checkbox" checked={!!i.done} readOnly style={{accentColor:'var(--red)',width:14,height:14,flexShrink:0}} />
              <span style={{fontSize:'13px',textDecoration: i.done ? 'line-through' : 'none',color: i.done ? 'var(--grey-text)' : 'var(--navy)'}}>{i.title}</span>
            </div>
          ))}
        </>
      )}
      {tkMatches.length > 0 && (
        <>
          <div style={{fontSize:'11px',fontWeight:700,color:'var(--grey-text)',textTransform:'uppercase',letterSpacing:'.5px',margin:'12px 0 8px'}}>Daktela tickety ({tkMatches.length})</div>
          {tkMatches.map(t => (
            <a key={t.name} href={'https://daktela.daktela.com/tickets/update/' + t.name} target="_blank" rel="noreferrer" style={{display:'flex',alignItems:'center',gap:8,padding:'6px 8px',borderRadius:6,marginBottom:4,border:'1px solid var(--grey-border)',background:'var(--grey-bg)',textDecoration:'none'}}>
              <span className={'stage-pill stage-' + (t.stage || 'OPEN')}>{t.stage || 'OPEN'}</span>
              <span style={{fontSize:'13px',color:'var(--navy)',flex:1,minWidth:0,overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap'}}>{t.title}</span>
            </a>
          ))}
        </>
      )}
      {!loading && dbTasks.length === 0 && clMatches.length === 0 && tkMatches.length === 0 && (
        <div style={{color:'var(--grey-text)',fontSize:'13px'}}>Nic nenalezeno pro "{query}"</div>
      )}
    </div>
  );
}

// ---- App ----
function App() {
  const [tasks, setTasks] = useState([]);
  const [checklistItems, setChecklistItems] = useState([]);
  const [calEvents, setCalEvents] = useState([]);
  const [calConnected, setCalConnected] = useState(false);
  const [todayDone, setTodayDone] = useState(0);
  const [clTodayDone, setClTodayDone] = useState(0);
  const [loading, setLoading] = useState(true);
  const [aiLoading, setAiLoading] = useState(false);

  const [activeTab, setActiveTab] = useState('all'); // 'all' | 'work' | 'personal' | 'history'
  const [modal, setModal] = useState(null); // null | {type, ...}

  const [daktelaToken, setDaktelaToken] = useState(() => sessionStorage.getItem('daktela_token') || '');
  const [daktelaTickets, setDaktelaTickets] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [quickCapture, setQuickCapture] = useState(false);

  // Load tasks
  async function loadTasks() {
    const data = await apiFetch('tasks');
    setTasks(data.tasks || []);
    setTodayDone(data.today_done || 0);
  }

  // Load checklist
  async function loadChecklist() {
    const data = await apiFetch('checklist');
    setChecklistItems(data.items || []);
    setClTodayDone(data.today_done || 0);
  }

  // Load calendar
  async function loadCalendar() {
    try {
      const data = await apiFetch('calendar');
      setCalConnected(data.connected);
      setCalEvents(data.events || []);
    } catch(e) {}
  }

  useEffect(() => {
    Promise.all([loadTasks(), loadChecklist(), loadCalendar()])
      .finally(() => setLoading(false));

    // Check for calendar connection callback
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('cal_connected')) {
      toast('Google Calendar úspěšně propojen!');
      window.history.replaceState({}, '', '/tasks/');
      loadCalendar();
    }

    // FAB
    document.getElementById('fab').onclick = () => setModal({ type: 'task' });

    // Sidebar toggle
    document.getElementById('sidebarToggle').onclick = () => {
      document.getElementById('sidebar').classList.toggle('open');
    };

    // Cmd+K = quick capture
    function handleKeyDown(e) {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setQuickCapture(true);
      }
    }
    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, []);

  async function handleAddTask(data) {
    const type = activeTab === 'personal' ? 'personal' : 'work';
    const result = await apiFetch('tasks', 'POST', { ...data, type });
    setTasks(prev => [...prev, result.task]);
    toast('Task přidán');
  }

  async function handleToggleDone(task) {
    const newStatus = task.status === 'done' ? 'open' : 'done';
    const result = await apiFetch('tasks', 'PUT', { status: newStatus }, { id: task.id });
    setTasks(prev => prev.map(t => t.id === task.id ? result.task : t));
    if (newStatus === 'done') { setTodayDone(v => v + 1); toast('✓ Hotovo!'); }
    else setTodayDone(v => Math.max(0, v - 1));
  }

  async function handleEditTask(task) {
    setModal({ type: 'task', task });
  }

  async function handleSaveTask(data) {
    if (modal.task) {
      const result = await apiFetch('tasks', 'PUT', data, { id: modal.task.id });
      setTasks(prev => prev.map(t => t.id === modal.task.id ? result.task : t));
      toast('Task uložen');
    } else {
      await handleAddTask(data);
    }
  }

  async function handleDeleteTask(task) {
    if (!confirm('Smazat task "' + task.title + '"?')) return;
    await apiFetch('tasks', 'DELETE', null, { id: task.id });
    setTasks(prev => prev.filter(t => t.id !== task.id));
    toast('Task smazán');
  }

  // Checklist
  async function handleAddCl(title) {
    const result = await apiFetch('checklist', 'POST', { title });
    setChecklistItems(prev => [...prev, result.item]);
  }

  async function handleToggleCl(item, done) {
    const result = await apiFetch('checklist', 'PUT', { done }, { id: item.id });
    setChecklistItems(prev => prev.map(i => i.id === item.id ? result.item : i));
    if (done) { setClTodayDone(v => v + 1); toast('✓ Odškrtnuto'); }
  }

  async function handleDeleteCl(item) {
    await apiFetch('checklist', 'DELETE', null, { id: item.id });
    setChecklistItems(prev => prev.filter(i => i.id !== item.id));
  }

  // Daktela → create task
  function handleDaktelaCreateTask(ticket) {
    setModal({
      type: 'task',
      defaults: {
        title: ticket.title,
        quadrant: 'urgent_important',
        type: 'work',
        daktela_tickets: [ticket.name],
      },
    });
  }

  // Inline edit názvu
  async function handleInlineEdit(task, newTitle) {
    const result = await apiFetch('tasks', 'PUT', { title: newTitle }, { id: task.id });
    setTasks(prev => prev.map(t => t.id === task.id ? result.task : t));
  }

  // Drag & drop přesun
  async function handleMoveTask(taskId, newQuadrant) {
    const task = tasks.find(t => t.id === taskId);
    if (!task || task.quadrant === newQuadrant) return;
    const result = await apiFetch('tasks', 'PUT', { quadrant: newQuadrant }, { id: taskId });
    setTasks(prev => prev.map(t => t.id === taskId ? result.task : t));
    toast('Task přesunut');
  }

  // Znovu otevřít task z historie
  async function handleReopenTask(task) {
    const result = await apiFetch('tasks', 'PUT', { status: 'open' }, { id: task.id });
    setTasks(prev => {
      const existing = prev.find(t => t.id === task.id);
      if (existing) return prev.map(t => t.id === task.id ? result.task : t);
      return [...prev, result.task];
    });
    setTodayDone(v => Math.max(0, v - 1));
    toast('Task vrácen do aktivních');
  }

  // AI suggest
  async function handleAiSuggest() {
    setAiLoading(true);
    try {
      const data = await apiFetch('ai_suggest', 'POST');
      if (data.suggestions) {
        setModal({ type: 'ai', suggestions: data.suggestions });
      }
    } catch(e) {
      toast('AI chyba: ' + e.message);
    }
    setAiLoading(false);
  }

  async function handleApplyAi(accepted) {
    const updates = accepted.filter(s => {
      const t = tasks.find(t => t.id === s.id);
      return t && t.quadrant !== s.quadrant;
    });
    await Promise.all(updates.map(s =>
      apiFetch('tasks', 'PUT', { quadrant: s.quadrant }, { id: s.id })
        .then(r => setTasks(prev => prev.map(t => t.id === s.id ? r.task : t)))
    ));
    toast(updates.length + ' tasků přesunuto');
  }

  // Calendar
  async function handleCalConnect() {
    const data = await apiFetch('calendar', 'GET', null, { sub: 'connect' });
    if (data.redirect) window.location.href = data.redirect;
    else if (data.error) toast(data.error);
  }

  async function handleCalDisconnect() {
    await apiFetch('calendar', 'POST', null, { sub: 'disconnect' });
    setCalConnected(false);
    setCalEvents([]);
    toast('Google Calendar odpojen');
  }

  const openTasks = tasks.filter(t => t.status === 'open');
  const totalOpen = openTasks.length;
  const filter = activeTab === 'history' ? 'all' : (activeTab === 'all' ? 'all' : activeTab);
  const workCount = openTasks.filter(t => t.type === 'work').length;
  const personalCount = openTasks.filter(t => t.type === 'personal').length;
  const q1Count = openTasks.filter(t => t.quadrant === 'urgent_important').length;

  const TABS = [
    { key: 'all', label: 'Vše', count: openTasks.length },
    { key: 'work', label: 'Pracovní', count: workCount },
    { key: 'personal', label: 'Osobní', count: personalCount },
    { key: 'history', label: 'Historie', count: null },
  ];

  return (
    <>
      {/* Header actions */}
      {ReactDOM.createPortal(
        <div style={{display:'flex',gap:8,alignItems:'center'}}>
          <input
            type="search"
            value={searchQuery}
            onChange={e => setSearchQuery(e.target.value)}
            placeholder="Hledat..."
            style={{height:34,padding:'0 10px',border:'1px solid rgba(255,255,255,.25)',borderRadius:'var(--radius)',background:'rgba(255,255,255,.12)',color:'#fff',fontSize:'13px',fontFamily:'var(--font)',outline:'none',width:160,transition:'width .2s'}}
            onFocus={e => e.target.style.width='240px'}
            onBlur={e => e.target.style.width='160px'}
          />
          <button className="btn btn-ghost" onClick={handleAiSuggest} disabled={aiLoading}>
            {aiLoading ? '...' : '✦ AI priority'}
          </button>
          <button className="btn btn-primary" onClick={() => setModal({ type: 'task' })}>+ Task</button>
          <button className="btn btn-ghost" style={{fontSize:'12px',padding:'6px 10px'}} onClick={() => setQuickCapture(true)} title="Cmd+K">⚡</button>
          <button className="btn btn-ghost" style={{fontSize:'12px'}} onClick={async () => {
            await apiFetch('logout', 'POST');
            window.location.href = '/tasks/login.php';
          }}>Odhlásit</button>
        </div>,
        document.getElementById('headerActions')
      )}

      {/* Tab bar */}
      {ReactDOM.createPortal(
        <div style={{display:'flex',alignItems:'center'}}>
          {TABS.map(t => (
            <button key={t.key} className={'tab' + (activeTab === t.key ? ' active' : '')} onClick={() => setActiveTab(t.key)}>
              {t.label}{t.count !== null && t.count > 0 ? ' (' + t.count + ')' : ''}
            </button>
          ))}
          {q1Count >= 3 && (
            <span className="q1-alert" title={q1Count + ' urgentních+důležitých tasků!'} style={{marginLeft:8}}>{q1Count}</span>
          )}
        </div>,
        document.getElementById('tabBar')
      )}

      {/* Sidebar */}
      {ReactDOM.createPortal(
        <>
          <KpiPanel todayDone={todayDone + clTodayDone} totalOpen={totalOpen} />
          <DaktelaPanel
            token={daktelaToken}
            onConnectClick={() => setModal({ type: 'daktela' })}
            onCreateTask={handleDaktelaCreateTask}
            onTicketsLoaded={setDaktelaTickets}
            assignedMap={(() => { const m = {}; tasks.forEach(task => { try { (task.daktela_tickets || []).forEach(n => { m[n] = task.title; }); } catch(e){} }); return m; })()}
          />
          <CalendarPanel
            events={calEvents}
            connected={calConnected}
            onConnect={handleCalConnect}
            onDisconnect={handleCalDisconnect}
          />
          <ChecklistPanel
            items={checklistItems}
            todayDone={clTodayDone}
            onAdd={handleAddCl}
            onToggle={handleToggleCl}
            onDelete={handleDeleteCl}
          />
        </>,
        document.getElementById('sidebar')
      )}

      {/* Main content */}
      {ReactDOM.createPortal(
        searchQuery.length >= 2
          ? <SearchResults query={searchQuery} checklistItems={checklistItems} daktelaTickets={daktelaTickets} onEditTask={handleEditTask} onToggleCl={handleToggleCl} />
          : activeTab === 'history'
          ? <HistoryView filter="all" onReopen={handleReopenTask} />
          : (
            <>
              <div className="matrix">
                {QUADRANTS.map(q => (
                  <Quadrant
                    key={q.key}
                    q={q}
                    tasks={tasks}
                    filter={filter}
                    onToggleDone={handleToggleDone}
                    onEdit={handleEditTask}
                    onDelete={handleDeleteTask}
                    onAddTask={handleAddTask}
                    onInlineEdit={handleInlineEdit}
                    onMoveTask={handleMoveTask}
                  />
                ))}
              </div>
              <div className="action-row">
                <button className="btn btn-primary" onClick={() => setModal({ type: 'task' })}>+ Nový task</button>
                <button className="btn btn-secondary" onClick={handleAiSuggest} disabled={aiLoading}>
                  {aiLoading ? 'Analyzuji...' : '✦ AI návrh priorit'}
                </button>
              </div>
            </>
          ),
        document.getElementById('mainContent')
      )}

      {/* Modals */}
      {modal?.type === 'task' && (
        <TaskModal
          task={modal.task || null}
          defaultQuadrant={(modal.defaults || {}).quadrant}
          defaultType={(modal.defaults || {}).type}
          defaultTickets={(modal.defaults || {}).daktela_tickets}
          availableTickets={daktelaTickets}
          onSave={handleSaveTask}
          onClose={() => setModal(null)}
        />
      )}
      {modal?.type === 'daktela' && (
        <DaktelaAuthModal
          onConnected={token => { setDaktelaToken(token); sessionStorage.setItem('daktela_token', token); }}
          onClose={() => setModal(null)}
        />
      )}
      {modal?.type === 'ai' && (
        <AiSuggestModal
          suggestions={modal.suggestions}
          tasks={tasks}
          onApply={handleApplyAi}
          onClose={() => setModal(null)}
        />
      )}

      {quickCapture && (
        <QuickCapture
          onSave={handleAddTask}
          onClose={() => setQuickCapture(false)}
        />
      )}

      {loading && (
        <div className="loading-overlay">
          <div className="spinner"></div>
        </div>
      )}
    </>
  );
}

ReactDOM.createRoot(document.getElementById('app-root')).render(<App />);
</script>
</body>
</html>
