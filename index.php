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
.cal-day-label{font-size:10px;color:var(--grey-text);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;margin-top:6px}
.cal-item-btn{background:none;border:1px solid var(--grey-border);border-radius:3px;cursor:pointer;font-size:10px;color:var(--grey-text);padding:1px 4px;flex-shrink:0;opacity:0;transition:opacity .15s}
.cal-item:hover .cal-item-btn{opacity:1}
.cal-day-section{margin-bottom:8px}
.cal-day-header{font-size:10px;color:var(--navy);font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin:8px 0 3px;padding-bottom:3px;border-bottom:1px solid var(--grey-border)}
.cal-day-section:first-child .cal-day-header{margin-top:2px}
.cal-event-row{display:flex;align-items:center;gap:8px;padding:5px 0;border-bottom:1px solid var(--grey-border);font-size:12px;position:relative}
.cal-event-row:last-child{border-bottom:none}
.cal-task-btn{background:none;border:1px solid var(--grey-border);border-radius:3px;cursor:pointer;font-size:10px;color:var(--navy);padding:1px 5px;flex-shrink:0;opacity:0;transition:opacity .15s;margin-left:auto}
.cal-event-row:hover .cal-task-btn{opacity:1}
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
.onenon-layout{display:flex;gap:20px;padding:20px;height:100%}
.onenon-sidebar{width:220px;flex-shrink:0}
.onenon-dashboard{background:var(--grey-bg);border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:12px}
.onenon-dashboard-row{display:flex;justify-content:space-between;align-items:center;gap:8px}
.onenon-warn{color:#C94F42;font-weight:700}
.onenon-person-item{padding:8px 12px;border-radius:6px;cursor:pointer;margin-bottom:4px;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:space-between;background:var(--grey-bg);color:var(--navy);border:none;width:100%;text-align:left}
.onenon-person-item.active{background:var(--navy);color:#fff}
.onenon-person-warn{width:8px;height:8px;border-radius:50%;background:#E05C4E;flex-shrink:0}
.onenon-person-row{display:flex;align-items:center;gap:2px;margin-bottom:4px}
.onenon-person-row.active .onenon-person-item-btn{background:var(--navy);color:#fff}
.onenon-person-item-btn{padding:8px 10px;border-radius:6px;cursor:pointer;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:space-between;background:var(--grey-bg);color:var(--navy);border:none;flex:1;text-align:left}
.onenon-person-edit-btn{background:none;border:none;cursor:pointer;font-size:13px;color:var(--grey-text);padding:4px 6px;border-radius:4px;opacity:0;flex-shrink:0}
.onenon-person-row:hover .onenon-person-edit-btn{opacity:1}
.onenon-person-edit-form{background:var(--grey-bg);border-radius:6px;padding:8px;margin-bottom:6px}
.onenon-person-edit-form input{width:100%;margin-bottom:4px;font-size:12px;padding:5px 8px;border:1px solid var(--grey-border);border-radius:4px;box-sizing:border-box;background:var(--surface);color:var(--text)}
.onenon-person-edit-form textarea{width:100%;font-size:12px;padding:5px 8px;border:1px solid var(--grey-border);border-radius:4px;box-sizing:border-box;resize:vertical;background:var(--surface);color:var(--text);font-family:inherit}
.onenon-person-desc{font-size:13px;color:var(--grey-text);margin-bottom:14px;line-height:1.5;white-space:pre-wrap;font-style:italic}
.onenon-main{flex:1;min-width:0;overflow-y:auto}
.onenon-note-card{background:#fff;border:1px solid var(--grey-border);border-radius:8px;padding:16px;margin-bottom:12px}
.onenon-note-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px}
.onenon-note-date{font-weight:700;color:var(--navy);font-size:13px}
.onenon-note-meta{display:flex;align-items:center;gap:10px}
.onenon-note-actions{display:flex;gap:8px}
.onenon-mood{color:#F5A623;font-size:14px;letter-spacing:1px}
.onenon-tag-chip{display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin:2px 3px 2px 0;background:#EEF2FF;color:#3B5BDB}
.onenon-action-item{display:flex;align-items:center;gap:8px;padding:4px 0;cursor:pointer;font-size:13px}
.onenon-action-check{width:14px;height:14px;border-radius:3px;border:2px solid var(--navy);background:none;display:inline-block;flex-shrink:0}
.onenon-action-check.done{border-color:var(--grey-text);background:var(--grey-bg)}
.onenon-action-text{color:var(--text)}
.onenon-action-text.done{color:var(--grey-text);text-decoration:line-through}
.onenon-mood-btn{background:none;border:none;cursor:pointer;font-size:20px;padding:2px;opacity:.35;transition:opacity .1s}
.onenon-mood-btn.active,.onenon-mood-btn:hover{opacity:1}
.onenon-tag-toggle{font-size:11px;padding:3px 10px;border-radius:10px;border:1px solid var(--grey-border);background:none;cursor:pointer;color:var(--grey-text);font-weight:600}
.onenon-tag-toggle.active{background:#EEF2FF;border-color:#3B5BDB;color:#3B5BDB}
.onenon-ai-row{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px}
.sr-section-label{font-size:11px;font-weight:700;color:var(--grey-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.sr-item{display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:6px;cursor:pointer;margin-bottom:4px;border:1px solid var(--grey-border);background:var(--grey-bg)}
.sr-task-title{font-size:13px;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--navy)}
.sr-task-title.done{text-decoration:line-through;color:var(--grey-text)}
.sr-quadrant-badge{font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;white-space:nowrap;flex-shrink:0}
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
function TaskModal({ task, defaultQuadrant, defaultType, defaultTickets, availableTickets, assignedMap, onSave, onClose, onDelete }) {
  const initial = task || {};
  const [title, setTitle] = useState(initial.title || '');
  const [description, setDescription] = useState(initial.description || '');
  const [aiContext, setAiContext] = useState(initial.ai_context || '');
  const [quadrant, setQuadrant] = useState(initial.quadrant || defaultQuadrant || 'other');
  const [type, setType] = useState(initial.type || defaultType || 'work');
  const [dueDate, setDueDate] = useState(initial.due_date || '');
  const [daktelaTickets, setDaktelaTickets] = useState(() => {
    const dt = initial.daktela_tickets;
    if (Array.isArray(dt)) return dt;
    if (dt) { try { return JSON.parse(dt); } catch(e) { return []; } }
    return defaultTickets || [];
  });
  const [recurrence, setRecurrence] = useState(initial.recurrence || 'none');
  const [recurrenceDay, setRecurrenceDay] = useState(initial.recurrence_day !== undefined && initial.recurrence_day !== null ? initial.recurrence_day : null);
  const [recurrenceInterval, setRecurrenceInterval] = useState(initial.recurrence_interval || 1);
  const [recurrenceUnit, setRecurrenceUnit] = useState(initial.recurrence_unit || 'weeks');
  const [saving, setSaving] = useState(false);

  function removeTicket(name) {
    setDaktelaTickets(prev => prev.filter(n => n !== name));
  }

  function addTicket(ticket) {
    if (!daktelaTickets.includes(ticket.name)) {
      setDaktelaTickets(prev => [...prev, ticket.name]);
    }
  }

  const assignedElsewhere = assignedMap || {};
  const attachable = (availableTickets || []).filter(t => !daktelaTickets.includes(t.name) && (!assignedElsewhere[t.name] || (task && assignedElsewhere[t.name] === task.title)));

  async function handleSave() {
    if (!title.trim()) return;
    setSaving(true);
    try {
      await onSave({ title, description, ai_context: aiContext, quadrant, type, due_date: dueDate, daktela_tickets: daktelaTickets, recurrence, recurrence_day: recurrenceDay, recurrence_interval: recurrenceInterval, recurrence_unit: recurrenceUnit });
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
          <label>{recurrence !== 'none' ? 'Termín / start opakování' : 'Termín'}</label>
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
              {daktelaTickets.map(name => {
                const ticketInfo = (availableTickets || []).find(t => t.name === name);
                const ticketTitle = ticketInfo ? ticketInfo.title : name;
                return (
                  <span key={name} title={ticketTitle} style={{display:'inline-flex',alignItems:'center',gap:4,background:'#FFF4E0',color:'#A06000',fontSize:'11px',fontWeight:700,padding:'3px 8px',borderRadius:20,cursor:'default'}}>
                    <a href={'https://daktela.daktela.com/tickets/update/' + name} target="_blank" rel="noreferrer" style={{color:'#A06000',textDecoration:'none'}}>{name}</a>
                    <button onClick={() => removeTicket(name)} style={{background:'none',border:'none',cursor:'pointer',color:'#A06000',fontSize:'13px',lineHeight:1,padding:0,marginLeft:2}} title="Odebrat ticket">×</button>
                  </span>
                );
              })}
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
        <div className="form-group">
          <label>Opakování</label>
          <select value={recurrence} onChange={e => { setRecurrence(e.target.value); setRecurrenceDay(null); }}>
            <option value="none">Nikdy</option>
            <option value="weekly">Týdně</option>
            <option value="monthly">Měsíčně</option>
            <option value="custom">Vlastní</option>
          </select>
          {recurrence === 'weekly' && (
            <div style={{marginTop:6}}>
              <div style={{fontSize:'11px',color:'var(--grey-text)',marginBottom:4}}>Den v týdnu (volitelné)</div>
              <div style={{display:'flex',gap:4}}>
                {['Ne','Po','Út','St','Čt','Pá','So'].map((d, i) => (
                  <button key={i} type="button" onClick={() => setRecurrenceDay(recurrenceDay === i ? null : i)}
                    style={{padding:'3px 6px',border:'1px solid '+(recurrenceDay===i?'var(--navy)':'var(--grey-border)'),borderRadius:4,cursor:'pointer',fontSize:'11px',fontWeight:recurrenceDay===i?700:400,background:recurrenceDay===i?'var(--navy)':'none',color:recurrenceDay===i?'#fff':'var(--navy)'}}>
                    {d}
                  </button>
                ))}
              </div>
              {recurrenceDay !== null && <div style={{fontSize:'11px',color:'var(--grey-text)',marginTop:4}}>Opakuje se každý {['neděli','pondělí','úterý','středu','čtvrtek','pátek','sobotu'][recurrenceDay]}</div>}
            </div>
          )}
          {recurrence === 'monthly' && (
            <div style={{marginTop:6}}>
              <div style={{fontSize:'11px',color:'var(--grey-text)',marginBottom:4}}>Den v měsíci (volitelné)</div>
              <div style={{display:'flex',gap:6,alignItems:'center'}}>
                <input type="number" min="1" max="31" value={recurrenceDay !== null ? recurrenceDay : ''} placeholder="1–31"
                  onChange={e => { const v = parseInt(e.target.value); setRecurrenceDay(v >= 1 && v <= 31 ? v : null); }}
                  style={{width:70,padding:'4px 8px',border:'1px solid var(--grey-border)',borderRadius:4,fontSize:'13px'}} />
                {recurrenceDay !== null && <span style={{fontSize:'11px',color:'var(--grey-text)'}}>Opakuje se každý {recurrenceDay}. den v měsíci</span>}
              </div>
            </div>
          )}
          {recurrence === 'custom' && (
            <div style={{display:'flex',gap:8,marginTop:6,alignItems:'center'}}>
              <span style={{fontSize:'12px',color:'var(--grey-text)'}}>Každých</span>
              <input type="number" min="1" max="365" value={recurrenceInterval} onChange={e => setRecurrenceInterval(parseInt(e.target.value) || 1)} style={{width:60,padding:'4px 6px',border:'1px solid var(--grey-border)',borderRadius:4,fontSize:'13px'}} />
              <select value={recurrenceUnit} onChange={e => setRecurrenceUnit(e.target.value)} style={{padding:'4px 6px',border:'1px solid var(--grey-border)',borderRadius:4,fontSize:'13px'}}>
                <option value="days">dní</option>
                <option value="weeks">týdnů</option>
                <option value="months">měsíců</option>
              </select>
            </div>
          )}
        </div>
        <div className="modal-actions" style={{justifyContent:'space-between'}}>
          <div>
            {task && <button className="btn" style={{color:'#E05C4E',border:'1px solid #E05C4E',background:'none'}} onClick={() => onDelete(task)}>Smazat</button>}
          </div>
          <div style={{display:'flex',gap:8}}>
            <button className="btn btn-secondary" onClick={onClose}>Zrušit</button>
            <button className="btn btn-primary" onClick={handleSave} disabled={saving}>
              {saving ? 'Ukládám...' : 'Uložit'}
            </button>
          </div>
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
  const daysUntil = task.due_date ? Math.ceil((new Date(task.due_date) - new Date(today)) / 86400000) : null;
  const isSoon = !isOverdue && task.status === 'open' && daysUntil !== null && daysUntil <= 3;

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
              {isOverdue ? 'Po termínu: ' : isSoon ? '⚡ ' : ''}{task.due_date}
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
function DaktelaPanel({ tickets, refreshedAt, token, onConnectClick, onRefresh, onCreateTask, assignedMap }) {
  const [showAssigned, setShowAssigned] = useState(false);
  const [refreshing, setRefreshing] = useState(false);

  async function handleRefresh() {
    if (!token) { onConnectClick(); return; }
    setRefreshing(true);
    try { await onRefresh(token); } catch(e) { toast('Chyba: ' + e.message); }
    setRefreshing(false);
  }

  function formatRefreshed(ts) {
    if (!ts) return 'nikdy';
    const d = new Date(ts);
    return d.toLocaleDateString('cs-CZ', {day:'numeric',month:'numeric'}) + ' ' + d.toLocaleTimeString('cs-CZ', {hour:'2-digit',minute:'2-digit'});
  }

  return (
    <div className="panel">
      <div className="section-title">
        Daktela tickety
        <span style={{display:'flex',gap:6,alignItems:'center'}}>
          <button onClick={handleRefresh} disabled={refreshing} style={{background:'none',border:'none',cursor:'pointer',fontSize:'12px',color:'var(--grey-text)',padding:0}} title="Obnovit z Daktely">{refreshing ? '...' : '↻'}</button>
          {!token && <button onClick={onConnectClick} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--navy)',fontWeight:700,textDecoration:'underline',padding:0}}>Připojit</button>}
        </span>
      </div>
      {tickets.length === 0 && !token && (
        <div className="daktela-connect">
          <p style={{fontSize:'12px',color:'var(--grey-text)',marginBottom:'10px'}}>Připoj Daktelu pro načtení ticketů. Poté se zobrazí bez nutnosti přihlášení.</p>
          <button className="btn btn-secondary" style={{width:'100%',fontSize:'12px'}} onClick={onConnectClick}>Připojit Daktelu</button>
        </div>
      )}
      {(() => {
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
      <div style={{fontSize:'11px',color:'var(--grey-text)',marginTop:8,display:'flex',justifyContent:'space-between',alignItems:'center'}}>
        <span>Obnoveno: {formatRefreshed(refreshedAt)}</span>
        {token && <button onClick={onConnectClick} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--grey-text)',textDecoration:'underline',padding:0}}>přihlásit znovu</button>}
      </div>
    </div>
  );
}

// ---- Calendar Panel ----
function CalendarPanel({ events, connected, onConnect, onDisconnect, onCreateTask }) {
  const dayGroups = events.reduce((acc, e) => {
    const key = e.dayLabel + '|' + e.date;
    if (!acc.find(g => g.key === key)) acc.push({ key, label: e.dayLabel, date: e.date, events: [] });
    acc.find(g => g.key === key).events.push(e);
    return acc;
  }, []);

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
      {connected && events.length === 0 && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Žádné nadcházející události</div>}
      {connected && dayGroups.map(group => (
        <div key={group.key} className="cal-day-section">
          <div className="cal-day-header">{group.label}</div>
          {group.events.map((e, i) => (
            <div key={i} className="cal-event-row">
              <span className="cal-time">{e.time || 'celý den'}</span>
              <span className="cal-title">{e.title}</span>
              <button className="cal-task-btn" onClick={() => onCreateTask(e)}>+ Task</button>
            </div>
          ))}
        </div>
      ))}
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
    if (query.length < 2) { setDbTasks([]); setLoading(false); return; }
    let cancelled = false;
    setLoading(true);
    apiFetch('tasks', 'GET', null, { search: query })
      .then(d => { if (!cancelled) setDbTasks(d.tasks || []); })
      .catch(() => {})
      .finally(() => { if (!cancelled) setLoading(false); });
    return () => { cancelled = true; };
  }, [query]);

  const clMatches = checklistItems.filter(i => i.title.toLowerCase().includes(lq));
  const tkMatches = daktelaTickets.filter(t =>
    (t.title || '').toLowerCase().includes(lq) || t.name.toLowerCase().includes(lq)
  );

  const Q_COLOR = { urgent_important: '#E63327', important: '#1B3468', urgent: '#A06000', other: '#5E6778' };
  const Q_BG = { urgent_important: '#FEE8E7', important: '#E0E8F5', urgent: '#FFF4E0', other: '#F4F5F7' };

  return (
    <div className="panel">
      {loading && <div className="sr-section-label">Hledám...</div>}
      {dbTasks.length > 0 && (
        <>
          <div className="sr-section-label">Tasky ({dbTasks.length})</div>
          {dbTasks.map(t => (
            <div key={t.id} onClick={() => onEditTask(t)} className="sr-item">
              <span className="sr-quadrant-badge" style={{background: Q_BG[t.quadrant] || '#F4F5F7',color: Q_COLOR[t.quadrant] || '#5E6778'}}>{Q_LABELS[t.quadrant] || t.quadrant}</span>
              <span className={'sr-task-title' + (t.status === 'done' ? ' done' : '')}>{t.title}</span>
            </div>
          ))}
        </>
      )}
      {clMatches.length > 0 && (
        <>
          <div className="sr-section-label" style={{margin:'12px 0 8px'}}>Checklist ({clMatches.length})</div>
          {clMatches.map(i => (
            <div key={i.id} onClick={() => onToggleCl(i, !i.done)} className="sr-item">
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

// ---- OneOnOneView ----
function PersonEditForm({ person, onSave, onCancel, onDelete }) {
  const [name, setName] = React.useState(person.name);
  const initProfile = person.profile || {};
  const [perf, setPerf] = React.useState(initProfile.performance || 0);
  const [potential, setPotential] = React.useState(initProfile.potential || '');
  const [mgmtEffort, setMgmtEffort] = React.useState(initProfile.mgmt_effort || '');
  const [strength, setStrength] = React.useState(initProfile.strength || '');
  const [development, setDevelopment] = React.useState(initProfile.development || '');
  const [commStyle, setCommStyle] = React.useState(initProfile.comm_style || '');
  const [motivation, setMotivation] = React.useState(initProfile.motivation || '');
  const [notes, setNotes] = React.useState(initProfile.notes || '');

  function handleSave() {
    const n = name.trim();
    if (!n) return;
    const profile = { performance: perf, potential, mgmt_effort: mgmtEffort, strength: strength.trim(), development: development.trim(), comm_style: commStyle.trim(), motivation: motivation.trim(), notes: notes.trim() };
    onSave(n, profile);
  }

  const btnOpt = (val, cur, set) => (
    <button onClick={() => set(cur === val ? '' : val)} style={{fontSize:11,padding:'2px 8px',borderRadius:4,cursor:'pointer',border:'1px solid',background: cur === val ? 'var(--navy)' : 'none',color: cur === val ? '#fff' : 'var(--navy)',borderColor:'var(--navy)'}}>
      {val === 'low' ? 'Nízký' : val === 'medium' ? 'Střední' : 'Vysoký'}
    </button>
  );

  return (
    <div className="onenon-person-edit-form">
      <input value={name} onChange={e => setName(e.target.value)} placeholder="Jméno" autoFocus style={{marginBottom:10}} />
      <div style={{fontSize:11,color:'var(--grey-text)',fontWeight:600,marginBottom:4}}>Výkon</div>
      <div style={{display:'flex',gap:3,marginBottom:8}}>
        {[1,2,3,4,5].map(i => (
          <span key={i} onClick={() => setPerf(perf === i ? 0 : i)} style={{cursor:'pointer',fontSize:16,color: i <= perf ? '#F5A623' : '#ccc'}}>{'★'}</span>
        ))}
      </div>
      <div style={{fontSize:11,color:'var(--grey-text)',fontWeight:600,marginBottom:4}}>Potenciál</div>
      <div style={{display:'flex',gap:4,marginBottom:8}}>{btnOpt('low',potential,setPotential)}{btnOpt('medium',potential,setPotential)}{btnOpt('high',potential,setPotential)}</div>
      <div style={{fontSize:11,color:'var(--grey-text)',fontWeight:600,marginBottom:4}}>Manažerská náročnost</div>
      <div style={{display:'flex',gap:4,marginBottom:8}}>{btnOpt('low',mgmtEffort,setMgmtEffort)}{btnOpt('medium',mgmtEffort,setMgmtEffort)}{btnOpt('high',mgmtEffort,setMgmtEffort)}</div>
      <input value={strength} onChange={e => setStrength(e.target.value)} placeholder="Silná stránka" style={{marginBottom:6}} />
      <input value={development} onChange={e => setDevelopment(e.target.value)} placeholder="Oblast rozvoje" style={{marginBottom:6}} />
      <input value={commStyle} onChange={e => setCommStyle(e.target.value)} placeholder="Styl komunikace" style={{marginBottom:6}} />
      <input value={motivation} onChange={e => setMotivation(e.target.value)} placeholder="Motivace" style={{marginBottom:6}} />
      <textarea value={notes} onChange={e => setNotes(e.target.value)} placeholder="Volné poznámky..." rows={3} style={{marginBottom:6}} />
      <div style={{display:'flex',gap:6,marginTop:4}}>
        <button className="btn btn-primary" style={{fontSize:11,flex:1}} onClick={handleSave}>Uložit</button>
        <button className="btn btn-secondary" style={{fontSize:11}} onClick={onCancel}>Zrušit</button>
        <button className="btn" style={{fontSize:11,color:'#E05C4E',border:'1px solid #E05C4E',background:'none'}} onClick={onDelete}>Smazat</button>
      </div>
    </div>
  );
}

function PersonProfile({ profile }) {
  if (!profile) return null;
  const labels = { potential: { low: 'Nízký', medium: 'Střední', high: 'Vysoký' }, mgmt_effort: { low: 'Nízká', medium: 'Střední', high: 'Vysoká' } };
  const badgeColor = { low: '#4CAF50', medium: '#F5A623', high: '#E05C4E' };
  const badge = (val, map) => val && labels[map] && labels[map][val]
    ? <span style={{background: badgeColor[val] || '#888',color:'#fff',fontSize:10,fontWeight:700,padding:'1px 7px',borderRadius:10,marginLeft:4}}>{labels[map][val]}</span>
    : null;
  const row = (label, val) => val ? <div style={{fontSize:12,marginBottom:3}}><span style={{color:'var(--grey-text)',minWidth:130,display:'inline-block'}}>{label}:</span>{val}</div> : null;
  return (
    <div className="onenon-person-desc" style={{fontStyle:'normal'}}>
      {profile.performance > 0 && <div style={{fontSize:12,marginBottom:3}}><span style={{color:'var(--grey-text)',minWidth:130,display:'inline-block'}}>Výkon:</span>{'★'.repeat(profile.performance)}{'☆'.repeat(5-profile.performance)}</div>}
      {profile.potential && <div style={{fontSize:12,marginBottom:3}}><span style={{color:'var(--grey-text)',minWidth:130,display:'inline-block'}}>Potenciál:</span>{badge(profile.potential,'potential')}</div>}
      {profile.mgmt_effort && <div style={{fontSize:12,marginBottom:3}}><span style={{color:'var(--grey-text)',minWidth:130,display:'inline-block'}}>Náročnost na řízení:</span>{badge(profile.mgmt_effort,'mgmt_effort')}</div>}
      {row('Silná stránka', profile.strength)}
      {row('Oblast rozvoje', profile.development)}
      {row('Styl komunikace', profile.comm_style)}
      {row('Motivace', profile.motivation)}
      {row('Poznámky', profile.notes)}
    </div>
  );
}

function OneOnOneView({ daktelaToken }) {
  const [people, setPeople] = React.useState([]);
  const [selected, setSelected] = React.useState(null);
  const [selectedDesc, setSelectedDesc] = React.useState('');
  const [selectedProfile, setSelectedProfile] = React.useState(null);
  const [notes, setNotes] = React.useState([]);
  const [modal, setModal] = React.useState(null);
  const [daktelaAgents, setDaktelaAgents] = React.useState([]);
  const [editingPerson, setEditingPerson] = React.useState(null);

  React.useEffect(() => { loadPeople(); }, []);

  React.useEffect(() => {
    if (!daktelaToken) return;
    apiFetch('daktela', 'POST', { accessToken: daktelaToken, endpoint: 'groups', params: { 'filter[0][field]': 'name', 'filter[0][operator]': 'eq', 'filter[0][value]': 'groups_62715929ce76e354293456' } })
      .then(gd => {
        const memberNames = ((gd.result && gd.result.data && gd.result.data[0] && gd.result.data[0].membersName) || []);
        return apiFetch('daktela', 'POST', { accessToken: daktelaToken, endpoint: 'users', params: { take: 200, fields: ['name', 'alias', 'title', 'status'] } })
          .then(ud => {
            const allUsers = (ud.result && ud.result.data) || [];
            const agents = allUsers
              .filter(u => u.status !== 'DELETED' && memberNames.includes(u.name))
              .map(u => ({ name: u.name, label: u.title || u.alias || u.name }));
            setDaktelaAgents(agents);
          });
      }).catch(() => {});
  }, [daktelaToken]);

  async function loadPeople() {
    const d = await apiFetch('onenon', 'GET');
    setPeople(d.people || []);
  }

  async function loadNotes(person) {
    setSelected(person);
    const d = await apiFetch('onenon', 'GET', null, { person });
    setNotes(d.notes || []);
    setSelectedDesc(d.description || '');
    setSelectedProfile(d.profile || null);
  }

  async function handleDeletePerson(name) {
    if (!window.confirm('Smazat osobu ' + name + ' včetně všech zápisů z 1on1?')) return;
    await apiFetch('onenon', 'DELETE', null, { person: name });
    setEditingPerson(null);
    if (selected === name) { setSelected(null); setNotes([]); setSelectedDesc(''); setSelectedProfile(null); }
    loadPeople();
  }

  async function handleUpdatePerson(oldName, newName, profile) {
    const d = await apiFetch('onenon', 'PUT', { old_name: oldName, new_name: newName, profile }, { sub: 'update_person' });
    setEditingPerson(null);
    await loadPeople();
    if (selected === oldName) {
      setSelected(d.name || newName);
      loadNotes(d.name || newName);
    }
  }

  async function handleSave(data) {
    if (data.id) {
      await apiFetch('onenon', 'PUT', { notes: data.notes, action_items: data.action_items, mood: data.mood, tags: data.tags }, { id: data.id });
    } else {
      await apiFetch('onenon', 'POST', { person: data.person, meeting_date: data.meeting_date, notes: data.notes, action_items: data.action_items, mood: data.mood, tags: data.tags });
    }
    loadPeople();
    if (selected) loadNotes(selected);
    setModal(null);
  }

  async function handleDelete(id) {
    if (!window.confirm('Smazat záznam?')) return;
    await apiFetch('onenon', 'DELETE', null, { id });
    loadNotes(selected);
    loadPeople();
  }

  async function toggleActionItem(note, idx) {
    const items = (note.action_items || []).map((it, i) => i === idx ? {...it, done: !it.done} : it);
    await apiFetch('onenon', 'PUT', { action_items: items }, { id: note.id });
    loadNotes(selected);
  }

  const totalOpen = people.reduce((s, p) => s + (p.open_items || 0), 0);
  const warnPeople = people.filter(p => (p.days_since || 0) > 30);

  function renderMood(mood) {
    if (!mood) return null;
    return <span className="onenon-mood">{'★'.repeat(mood)}{'☆'.repeat(5 - mood)}</span>;
  }

  return (
    <div className="onenon-layout">
      <div className="onenon-sidebar">
        {(totalOpen > 0 || warnPeople.length > 0) && (
          <div className="onenon-dashboard">
            {totalOpen > 0 && <div className="onenon-dashboard-row"><span>Otevřené action items:</span><span className="onenon-warn">{totalOpen}</span></div>}
            {warnPeople.length > 0 && <div className="onenon-dashboard-row" style={{marginTop:4}}><span className="onenon-warn">⚠ Bez 1on1 &gt;30 dní:</span><span>{warnPeople.map(p => p.person).join(', ')}</span></div>}
          </div>
        )}
        <div className="section-title" style={{marginBottom:8}}>Lidé</div>
        {people.map(p => {
          const isEditing = editingPerson && editingPerson.name === p.person;
          return (
            <div key={p.person}>
              {isEditing ? (
                <PersonEditForm
                  person={editingPerson}
                  onSave={(newName, profile) => handleUpdatePerson(p.person, newName, profile)}
                  onCancel={() => setEditingPerson(null)}
                  onDelete={() => handleDeletePerson(p.person)}
                />
              ) : (
                <div className={'onenon-person-row' + (selected === p.person ? ' active' : '')}>
                  <button className="onenon-person-item-btn" onClick={() => loadNotes(p.person)}>
                    <span>{p.person} <span style={{opacity:.6,fontWeight:400}}>({p.count})</span></span>
                    {p.days_since > 30 && <span className="onenon-person-warn" title={p.days_since + ' dní bez 1on1'} />}
                  </button>
                  <button className="onenon-person-edit-btn" title="Upravit" onClick={e => { e.stopPropagation(); setEditingPerson({ name: p.person, description: p.description || '' }); }}>✎</button>
                </div>
              )}
            </div>
          );
        })}
        <button className="btn btn-primary" style={{width:'100%',marginTop:12,fontSize:12}}
          onClick={() => setModal({ person: selected || '' })}>+ Nová schůzka</button>
      </div>
      <div className="onenon-main">
        {!selected && <div style={{color:'var(--grey-text)',fontSize:13}}>Vyber osobu vlevo</div>}
        {selected && (
          <>
            <div style={{display:'flex',justifyContent:'space-between',alignItems:'center',marginBottom:selectedDesc ? 6 : 16}}>
              <div className="section-title">{selected}</div>
              <button className="btn btn-secondary" style={{fontSize:12}} onClick={() => setModal({ person: selected })}>+ Schůzka</button>
            </div>
            {selectedDesc && <div className="onenon-person-desc">{selectedDesc}</div>}
            {selectedProfile && <PersonProfile profile={selectedProfile} />}
            {notes.length === 0 && <div style={{color:'var(--grey-text)',fontSize:13}}>Zatím žádné záznamy</div>}
            {notes.map(n => (
              <div key={n.id} className="onenon-note-card">
                <div className="onenon-note-header">
                  <div>
                    <div className="onenon-note-date">{n.meeting_date}</div>
                    <div className="onenon-note-meta">
                      {renderMood(n.mood)}
                      {(n.tags || []).map(t => <span key={t} className="onenon-tag-chip">{t}</span>)}
                    </div>
                  </div>
                  <div className="onenon-note-actions">
                    <button onClick={() => setModal(n)} style={{background:'none',border:'none',cursor:'pointer',fontSize:11,color:'var(--grey-text)'}}>Upravit</button>
                    <button onClick={() => handleDelete(n.id)} style={{background:'none',border:'none',cursor:'pointer',fontSize:11,color:'#E05C4E'}}>Smazat</button>
                  </div>
                </div>
                {n.notes && <div style={{fontSize:13,color:'var(--text)',whiteSpace:'pre-wrap',marginBottom:8}}>{n.notes}</div>}
                {(n.action_items || []).length > 0 && (
                  <div>
                    <div style={{fontSize:11,fontWeight:700,color:'var(--grey-text)',textTransform:'uppercase',letterSpacing:'.4px',marginBottom:4}}>Action items</div>
                    {(n.action_items || []).map((it, idx) => (
                      <div key={idx} className="onenon-action-item" onClick={() => toggleActionItem(n, idx)}>
                        <span className={'onenon-action-check' + (it.done ? ' done' : '')} />
                        <span className={'onenon-action-text' + (it.done ? ' done' : '')}>{it.text}</span>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            ))}
          </>
        )}
      </div>
      {modal !== null && <OneOnOneModal note={modal} agents={daktelaAgents} existingPeople={people.map(p => p.person)} onSave={handleSave} onClose={() => setModal(null)} />}
    </div>
  );
}

const ONENON_TAGS = ['výkon', 'SLA', 'osobní', 'rozvoj', 'feedback'];

function OneOnOneModal({ note, agents, existingPeople, onSave, onClose }) {
  const [personMode, setPersonMode] = React.useState(note.person ? 'known' : 'select');
  const [person, setPerson] = React.useState(note.person || '');
  const [manualPerson, setManualPerson] = React.useState('');
  const [date, setDate] = React.useState(note.meeting_date || new Date().toISOString().split('T')[0]);
  const [txt, setTxt] = React.useState(note.notes || '');
  const [items, setItems] = React.useState(note.action_items || []);
  const [newItem, setNewItem] = React.useState('');
  const [mood, setMood] = React.useState(note.mood || null);
  const [tags, setTags] = React.useState(note.tags || []);

  // Sloučit Daktela agenty + existující osoby do jednoho seznamu
  const allOptions = [...new Set([...existingPeople, ...agents.map(a => a.label)])].sort();

  function addItem() {
    if (!newItem.trim()) return;
    setItems(prev => [...prev, { text: newItem.trim(), done: false }]);
    setNewItem('');
  }

  function toggleTag(t) {
    setTags(prev => prev.includes(t) ? prev.filter(x => x !== t) : [...prev, t]);
  }

  function resolvedPerson() {
    if (note.person) return note.person;
    if (personMode === 'manual') return manualPerson.trim();
    return person;
  }

  function handleSave() {
    const p = resolvedPerson();
    if (!p) { toast('Zadej jméno osoby'); return; }
    onSave({ id: note.id, person: p, meeting_date: date, notes: txt, action_items: items, mood, tags });
  }

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal">
        <h2>{note.id ? 'Upravit záznam' : 'Nová schůzka'}</h2>
        {!note.id && (
          <div className="form-group">
            <label>Osoba</label>
            {personMode !== 'manual' ? (
              <div style={{display:'flex',gap:6}}>
                <select value={person} onChange={e => setPerson(e.target.value)} style={{flex:1}}>
                  <option value="">-- Vyber osobu --</option>
                  {allOptions.map(o => <option key={o} value={o}>{o}</option>)}
                </select>
                <button type="button" className="btn" style={{fontSize:12,flexShrink:0}} onClick={() => { setPersonMode('manual'); }}>Jiný...</button>
              </div>
            ) : (
              <div style={{display:'flex',gap:6}}>
                <input value={manualPerson} onChange={e => setManualPerson(e.target.value)} placeholder="Jméno osoby" autoFocus style={{flex:1}} />
                <button type="button" className="btn" style={{fontSize:12,flexShrink:0}} onClick={() => setPersonMode('select')}>←</button>
              </div>
            )}
          </div>
        )}
        <div className="form-group">
          <label>Datum</label>
          <input type="date" value={date} onChange={e => setDate(e.target.value)} />
        </div>
        <div className="form-group">
          <label>Nálada schůzky</label>
          <div className="onenon-ai-row">
            {[1,2,3,4,5].map(v => (
              <button key={v} type="button" className={'onenon-mood-btn' + (mood === v ? ' active' : '')}
                onClick={() => setMood(mood === v ? null : v)} title={v + '/5'}>
                {mood && v <= mood ? '★' : '☆'}
              </button>
            ))}
            {mood && <span style={{fontSize:12,color:'var(--grey-text)',alignSelf:'center'}}>{mood}/5</span>}
          </div>
        </div>
        <div className="form-group">
          <label>Tagy</label>
          <div className="onenon-ai-row">
            {ONENON_TAGS.map(t => (
              <button key={t} type="button" className={'onenon-tag-toggle' + (tags.includes(t) ? ' active' : '')}
                onClick={() => toggleTag(t)}>{t}</button>
            ))}
          </div>
        </div>
        <div className="form-group">
          <label>Poznámky</label>
          <textarea value={txt} onChange={e => setTxt(e.target.value)} rows={4} placeholder="Co jsme řešili..." />
        </div>
        <div className="form-group">
          <label>Action items</label>
          {items.map((it, i) => (
            <div key={i} style={{display:'flex',alignItems:'center',gap:6,marginBottom:4}}>
              <input type="text" value={it.text} onChange={e => setItems(prev => prev.map((x,j) => j===i?{...x,text:e.target.value}:x))}
                style={{flex:1,fontSize:13,padding:'4px 8px',border:'1px solid var(--grey-border)',borderRadius:4}} />
              <button onClick={() => setItems(prev => prev.filter((_,j) => j!==i))}
                style={{background:'none',border:'none',cursor:'pointer',color:'#E05C4E',fontSize:16}}>×</button>
            </div>
          ))}
          <div style={{display:'flex',gap:6,marginTop:4}}>
            <input value={newItem} onChange={e => setNewItem(e.target.value)} placeholder="Nový action item..."
              onKeyDown={e => e.key==='Enter' && addItem()}
              style={{flex:1,fontSize:13,padding:'4px 8px',border:'1px solid var(--grey-border)',borderRadius:4}} />
            <button onClick={addItem} className="btn btn-secondary" style={{fontSize:12}}>Přidat</button>
          </div>
        </div>
        <div className="form-actions">
          <button className="btn" onClick={onClose}>Zrušit</button>
          <button className="btn btn-primary" onClick={handleSave}>Uložit</button>
        </div>
      </div>
    </div>
  );
}

// ---- SettingsModal ----
function SettingsModal({ onClose }) {
  const [oldPass, setOldPass] = React.useState('');
  const [newUser, setNewUser] = React.useState('');
  const [newPass, setNewPass] = React.useState('');
  const [newPass2, setNewPass2] = React.useState('');
  const [saving, setSaving] = React.useState(false);
  const [showOld, setShowOld] = React.useState(false);
  const [showNew, setShowNew] = React.useState(false);

  async function handleSave() {
    if (newPass && newPass !== newPass2) { toast('Hesla se neshodují'); return; }
    if (!oldPass) { toast('Zadej stávající heslo'); return; }
    setSaving(true);
    try {
      await apiFetch('settings', 'POST', { old_password: oldPass, new_username: newUser, new_password: newPass });
      toast('Uloženo');
      onClose();
    } catch(e) { toast('Chyba: ' + e.message); }
    setSaving(false);
  }

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal">
        <h2>Nastavení účtu</h2>
        <div className="form-group">
          <label>Stávající heslo</label>
          <div style={{position:'relative'}}>
            <input type={showOld ? 'text' : 'password'} value={oldPass} onChange={e => setOldPass(e.target.value)} placeholder="Povinné" style={{paddingRight:36}} />
            <button type="button" onClick={() => setShowOld(v => !v)} style={{position:'absolute',right:8,top:'50%',transform:'translateY(-50%)',background:'none',border:'none',cursor:'pointer',fontSize:15,padding:0,color:'var(--grey-text)'}}>{showOld ? '🙈' : '👁'}</button>
          </div>
        </div>
        <div className="form-group">
          <label>Nové uživatelské jméno <span style={{fontWeight:400,color:'var(--grey-text)'}}>(nechej prázdné pro zachování)</span></label>
          <input value={newUser} onChange={e => setNewUser(e.target.value)} placeholder="Nové jméno..." />
        </div>
        <div className="form-group">
          <label>Nové heslo <span style={{fontWeight:400,color:'var(--grey-text)'}}>(min. 8 znaků, nechej prázdné pro zachování)</span></label>
          <div style={{position:'relative'}}>
            <input type={showNew ? 'text' : 'password'} value={newPass} onChange={e => setNewPass(e.target.value)} placeholder="Nové heslo..." style={{paddingRight:36}} />
            <button type="button" onClick={() => setShowNew(v => !v)} style={{position:'absolute',right:8,top:'50%',transform:'translateY(-50%)',background:'none',border:'none',cursor:'pointer',fontSize:15,padding:0,color:'var(--grey-text)'}}>{showNew ? '🙈' : '👁'}</button>
          </div>
        </div>
        {newPass && (
          <div className="form-group">
            <label>Potvrdit nové heslo</label>
            <input type={showNew ? 'text' : 'password'} value={newPass2} onChange={e => setNewPass2(e.target.value)} placeholder="Znovu nové heslo..." />
          </div>
        )}
        <div className="form-actions">
          <button className="btn" onClick={onClose}>Zrušit</button>
          <button className="btn btn-primary" onClick={handleSave} disabled={saving}>{saving ? 'Ukládám...' : 'Uložit'}</button>
        </div>
      </div>
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
  const [daktelaRefreshedAt, setDaktelaRefreshedAt] = useState(null);
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

  // Load Daktela cache from DB (no token needed)
  async function loadDaktelaCache() {
    try {
      const data = await apiFetch('daktela_cache');
      setDaktelaTickets(data.tickets || []);
      setDaktelaRefreshedAt(data.refreshed_at || null);
    } catch(e) {}
  }

  // Refresh Daktela cache via API (requires token)
  async function refreshDaktelaCache(token) {
    const data = await apiFetch('daktela_cache', 'POST', { accessToken: token });
    setDaktelaTickets(data.tickets || []);
    setDaktelaRefreshedAt(data.refreshed_at || null);
    toast('Tickety aktualizovány (' + (data.count || 0) + ')');
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
    Promise.all([loadTasks(), loadChecklist(), loadCalendar(), loadDaktelaCache()])
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
    if (!confirm('Smazat task "' + task.title + '"?')) return false;
    await apiFetch('tasks', 'DELETE', null, { id: task.id });
    setTasks(prev => prev.filter(t => t.id !== task.id));
    toast('Task smazán');
    return true;
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
    { key: 'onenon', label: '1on1', count: null },
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
          <button className="btn btn-ghost" style={{fontSize:'12px'}} onClick={() => setModal({ type: 'settings' })}>⚙</button>
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
            tickets={daktelaTickets}
            refreshedAt={daktelaRefreshedAt}
            token={daktelaToken}
            onConnectClick={() => setModal({ type: 'daktela' })}
            onRefresh={refreshDaktelaCache}
            onCreateTask={handleDaktelaCreateTask}
            assignedMap={(() => { const m = {}; tasks.forEach(task => { try { (task.daktela_tickets || []).forEach(n => { m[n] = task.title; }); } catch(e){} }); return m; })()}
          />
          <CalendarPanel
            events={calEvents}
            connected={calConnected}
            onConnect={handleCalConnect}
            onDisconnect={handleCalDisconnect}
            onCreateTask={e => setModal({ type: 'task', defaults: { title: e.title, due_date: e.date, quadrant: 'important', type: 'work' } })}
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
          : activeTab === 'onenon'
          ? <OneOnOneView daktelaToken={daktelaToken} />
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
          assignedMap={(() => { const m = {}; tasks.forEach(task => { try { (task.daktela_tickets || []).forEach(n => { m[n] = task.title; }); } catch(e){} }); return m; })()}
          onSave={handleSaveTask}
          onDelete={async t => { const done = await handleDeleteTask(t); if (done) setModal(null); }}
          onClose={() => setModal(null)}
        />
      )}
      {modal?.type === 'daktela' && (
        <DaktelaAuthModal
          onConnected={token => { setDaktelaToken(token); sessionStorage.setItem('daktela_token', token); refreshDaktelaCache(token); }}
          onClose={() => setModal(null)}
        />
      )}
      {modal?.type === 'settings' && (
        <SettingsModal onClose={() => setModal(null)} />
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
