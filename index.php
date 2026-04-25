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
  <div class="layout" id="layout">
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
function TaskModal({ task, defaultQuadrant, defaultType, onSave, onClose }) {
  const initial = task || {};
  const [title, setTitle] = useState(initial.title || '');
  const [description, setDescription] = useState(initial.description || '');
  const [aiContext, setAiContext] = useState(initial.ai_context || '');
  const [quadrant, setQuadrant] = useState(initial.quadrant || defaultQuadrant || 'other');
  const [type, setType] = useState(initial.type || defaultType || 'work');
  const [dueDate, setDueDate] = useState(initial.due_date || '');
  const [saving, setSaving] = useState(false);

  async function handleSave() {
    if (!title.trim()) return;
    setSaving(true);
    try {
      await onSave({ title, description, ai_context: aiContext, quadrant, type, due_date: dueDate });
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
function TaskCard({ task, onToggleDone, onEdit, onDelete }) {
  const tickets = task.daktela_tickets || [];
  return (
    <div className="task-card" onClick={() => onEdit(task)}>
      <input
        type="checkbox"
        className="task-checkbox"
        checked={task.status === 'done'}
        onClick={e => e.stopPropagation()}
        onChange={() => onToggleDone(task)}
      />
      <div className="task-body">
        <div className={'task-title' + (task.status === 'done' ? ' done-text' : '')}>{task.title}</div>
        <div className="task-meta">
          <span className={'badge ' + (task.type === 'personal' ? 'badge-personal' : 'badge-work')}>
            {task.type === 'personal' ? 'Osobní' : 'Work'}
          </span>
          {tickets.length > 0 && <span className="badge badge-daktela">Daktela ×{tickets.length}</span>}
          {task.due_date && <span>{task.due_date}</span>}
        </div>
      </div>
      <button className="task-del" onClick={e => { e.stopPropagation(); onDelete(task); }} title="Smazat">×</button>
    </div>
  );
}

// ---- Quadrant ----
function Quadrant({ q, tasks, filter, onToggleDone, onEdit, onDelete, onAddTask }) {
  const [addTitle, setAddTitle] = useState('');
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

  return (
    <div className="quadrant">
      <div className="q-header">
        <div className="q-label">{q.label}</div>
      </div>
      {visible.map(t => (
        <TaskCard key={t.id} task={t} onToggleDone={onToggleDone} onEdit={onEdit} onDelete={onDelete} />
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
function DaktelaPanel({ token, onConnectClick, onCreateTask }) {
  const [tickets, setTickets] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

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
      setTickets(Array.isArray(items) ? items.slice(0, 10) : []);
    } catch(e) {
      setError(e.message);
    }
    setLoading(false);
  }

  function buildDaktelaParams() {
    return {
      'filter[0][field]': 'user',
      'filter[0][operator]': 'eq',
      'filter[0][value]': 'sachj',
      'filter[1][field]': 'stage',
      'filter[1][operator]': 'in',
      'filter[1][value][]': ['OPEN', 'WAIT'],
      'fields[]': ['name', 'title', 'stage', 'sla_deadline'],
      'take': 15,
    };
  }

  return (
    <div className="panel">
      <div className="section-title">
        Daktela tickety
        {token && <button onClick={loadTickets} style={{background:'none',border:'none',cursor:'pointer',fontSize:'12px',color:'var(--grey-text)'}}>↺</button>}
      </div>
      {!token && (
        <div className="daktela-connect">
          <p style={{fontSize:'12px',color:'var(--grey-text)',marginBottom:'10px'}}>Připoj Daktelu pro zobrazení otevřených ticketů.</p>
          <button className="btn btn-secondary" style={{width:'100%',fontSize:'12px'}} onClick={onConnectClick}>Připojit Daktelu</button>
        </div>
      )}
      {token && loading && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Načítám...</div>}
      {token && error && <div style={{fontSize:'12px',color:'#E63327'}}>{error} <button onClick={onConnectClick} style={{background:'none',border:'none',cursor:'pointer',color:'var(--navy)',fontSize:'12px',textDecoration:'underline'}}>Přihlásit znovu</button></div>}
      {token && !loading && tickets.map(t => (
        <div key={t.name} className="ticket-row">
          <span className={'stage-pill stage-' + (t.stage || 'OPEN')}>{t.stage || 'OPEN'}</span>
          <span className="ticket-title" title={t.title}>{t.title}</span>
          <button className="ticket-add-btn" onClick={() => onCreateTask(t)}>+ Task</button>
        </div>
      ))}
      {token && !loading && tickets.length === 0 && !error && (
        <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Žádné otevřené tickety</div>
      )}
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
function HistoryView({ filter }) {
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
    // Seskup po dnech
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
              <span>{t.title}</span>
              <span className="history-time">{t.done_at ? t.done_at.split(' ')[1].slice(0,5) : ''}</span>
            </div>
          ))}
        </div>
      ))}
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

  const totalOpen = tasks.filter(t => t.status === 'open').length;
  const filter = activeTab === 'history' ? 'all' : (activeTab === 'all' ? 'all' : activeTab);

  const TABS = [
    { key: 'all', label: 'Vše' },
    { key: 'work', label: 'Pracovní' },
    { key: 'personal', label: 'Osobní' },
    { key: 'history', label: 'Historie' },
  ];

  return (
    <>
      {/* Header actions */}
      {ReactDOM.createPortal(
        <div style={{display:'flex',gap:8,alignItems:'center'}}>
          <button className="btn btn-ghost" onClick={handleAiSuggest} disabled={aiLoading}>
            {aiLoading ? '...' : '✦ AI priority'}
          </button>
          <button className="btn btn-primary" onClick={() => setModal({ type: 'task' })}>+ Task</button>
          <button className="btn btn-ghost" style={{fontSize:'12px'}} onClick={async () => {
            await apiFetch('logout', 'POST');
            window.location.href = '/tasks/login.php';
          }}>Odhlásit</button>
        </div>,
        document.getElementById('headerActions')
      )}

      {/* Tab bar */}
      {ReactDOM.createPortal(
        <div style={{display:'flex'}}>
          {TABS.map(t => (
            <button key={t.key} className={'tab' + (activeTab === t.key ? ' active' : '')} onClick={() => setActiveTab(t.key)}>
              {t.label}
            </button>
          ))}
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
        activeTab === 'history'
          ? <HistoryView filter="all" />
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

      {loading && (
        <div className="loading-overlay">
          <div className="spinner"></div>
        </div>
      )}
    </>
  );
}

ReactDOM.createRoot(document.getElementById('layout')).render(<App />);
</script>
</body>
</html>
