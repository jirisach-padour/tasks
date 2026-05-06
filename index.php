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
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%231B3468'/><rect x='6' y='6' width='9' height='9' rx='2' fill='%23E05C4E'/><rect x='17' y='6' width='9' height='9' rx='2' fill='rgba(255,255,255,0.4)'/><rect x='6' y='17' width='9' height='9' rx='2' fill='rgba(255,255,255,0.4)'/><rect x='17' y='17' width='9' height='9' rx='2' fill='rgba(255,255,255,0.15)'/></svg>">
<script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<style>
:root{--red:#E05C4E;--red-hover:#C94F42;--navy:#1B3468;--grey-bg:#F4F5F7;--grey-border:#DDE1E7;--grey-text:#5E6778;--white:#FFFFFF;--radius:8px;--font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{overflow-x:hidden}
body{font-family:var(--font);font-size:14px;background:var(--grey-bg);color:var(--navy);overflow-x:hidden}
/* Header */
.app-header{background:linear-gradient(135deg,#1B3468 0%,#152a52 100%);padding:12px 0 0}
.container{max-width:1600px;margin:0 auto;padding:0 24px}
.header-inner{display:flex;align-items:center;justify-content:space-between;padding-bottom:14px}
.app-header h1{color:#fff;font-size:18px;font-weight:700;letter-spacing:-.2px}
.header-desc{color:rgba(255,255,255,.5);font-size:11px;margin-top:1px}
.header-actions{display:flex;gap:8px;align-items:center}
.header-desktop-only{display:inline-flex}
/* Tabs */
.tab-bar{display:flex;overflow-x:auto;-webkit-overflow-scrolling:touch}
.tab-bar::-webkit-scrollbar{display:none}
.tab{padding:10px 20px;font-size:13px;font-weight:600;border:none;background:transparent;color:rgba(255,255,255,.55);cursor:pointer;border-bottom:3px solid transparent;white-space:nowrap;transition:all .15s;font-family:var(--font)}
.tab.active{color:#fff;border-bottom:3px solid var(--red)}
.tab:hover:not(.active){color:rgba(255,255,255,.85)}
/* Layout */
.layout{display:grid;grid-template-columns:clamp(220px,18%,300px) 1fr clamp(220px,18%,300px);gap:16px;margin-top:16px;padding-bottom:80px;align-items:start}
.layout.onenon-mode{grid-template-columns:clamp(220px,18%,300px) 1fr}
.layout.onenon-mode #sidebarRight{display:none}
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
.quadrant.q-urgent_important{border-left:3px solid var(--red)}
.quadrant.q-important{border-left:3px solid var(--navy)}
.quadrant.q-urgent{border-left:3px solid #E8A020}
.q-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.q-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--grey-text);display:flex;align-items:center;gap:5px}
.q-add-btn{background:none;border:none;cursor:pointer;color:var(--grey-text);font-size:18px;line-height:1;padding:0 2px;font-weight:300}
.q-add-btn:hover{color:var(--navy)}
/* Task card */
.task-card{display:flex;align-items:flex-start;gap:8px;padding:8px 10px;background:var(--white);border-radius:6px;margin-bottom:6px;border:1px solid var(--grey-border);cursor:pointer;transition:border-color .1s;box-shadow:0 1px 3px rgba(0,0,0,.05)}
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
.cl-item-edit{flex:1;font-size:13px;font-family:var(--font);border:1px solid var(--navy);border-radius:4px;padding:0 4px;height:22px;outline:none;min-width:0}
/* Daktela tickets */
.ticket-row{display:flex;align-items:center;gap:7px;padding:7px 0;border-bottom:1px solid var(--grey-border)}
.ticket-row:last-child{border-bottom:none}
.stage-pill{font-size:10px;font-weight:600;padding:2px 7px;border-radius:4px;white-space:nowrap;flex-shrink:0}
.stage-OPEN{background:#E3F5E8;color:#2E7D3F}
.stage-WAIT{background:#FFF4E0;color:#A06000}
.ticket-title{font-size:12px;font-weight:500;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ticket-add-btn{font-size:11px;padding:3px 8px;background:var(--grey-bg);border:1px solid var(--grey-border);border-radius:4px;cursor:pointer;color:var(--navy);font-weight:600;white-space:nowrap;flex-shrink:0}
.ticket-add-btn:hover{background:var(--grey-border)}
.tickets-scroll{max-height:320px;overflow-y:auto}
.tickets-scroll::-webkit-scrollbar{width:3px}
.tickets-scroll::-webkit-scrollbar-thumb{background:var(--grey-border);border-radius:2px}
.cal-scroll{max-height:280px;overflow-y:auto}
.cal-scroll::-webkit-scrollbar{width:3px}
.cal-scroll::-webkit-scrollbar-thumb{background:var(--grey-border);border-radius:2px}
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
.todo-footer-link{display:block;text-align:center;padding:10px 0 16px;font-size:11px;color:var(--grey-text);opacity:.6;cursor:pointer;user-select:none;text-decoration:none}.todo-footer-link:hover{opacity:1;color:var(--navy)}
.todo-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:2000;display:flex;align-items:center;justify-content:center}
.todo-modal-box{background:#fff;border-radius:10px;width:min(720px,95vw);max-height:85vh;display:flex;flex-direction:column;box-shadow:0 8px 40px rgba(0,0,0,.25)}
.todo-modal-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--grey-border);font-weight:600;font-size:15px}
.todo-modal-body{overflow-y:auto;padding:18px 22px;font-size:13px;line-height:1.7;white-space:pre-wrap;font-family:monospace}
.fab{display:none;position:fixed;bottom:20px;right:20px;width:52px;height:52px;border-radius:50%;background:var(--red);color:#fff;font-size:24px;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.25);z-index:100;align-items:center;justify-content:center;font-family:var(--font)}
/* Sidebar hamburger (tablet) */
.sidebar-toggle{display:none;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:20px;cursor:pointer;padding:6px 10px;border-radius:6px;font-family:var(--font)}
.sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:149}
.sidebar-backdrop.active{display:block}
.sidebar-mobile-panels{display:none}
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
.q1-alert{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#E63327;color:#fff;font-size:10px;font-weight:700;border-radius:50%;margin-left:6px;animation:pulse 1.5s ease-in-out infinite;cursor:pointer;position:relative;flex-shrink:0}
.q1-popover{position:absolute;top:calc(100% + 6px);right:0;background:#fff;border:1px solid var(--grey-border);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);min-width:260px;max-width:340px;z-index:9999;overflow:hidden}
.q1-popover-header{padding:8px 12px;font-size:11px;font-weight:700;color:#E63327;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--grey-border);background:#FEF8F8}
.q1-popover-item{display:flex;align-items:center;gap:8px;padding:8px 12px;cursor:pointer;border-bottom:1px solid var(--grey-border);transition:background .1s}
.q1-popover-item:last-child{border-bottom:none}
.q1-popover-item:hover{background:#FEF8F8}
.q1-popover-title{flex:1;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.q1-popover-deadline{font-size:11px;font-weight:700;padding:1px 6px;border-radius:4px;flex-shrink:0}
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
@media(max-width:1100px){
  .layout{grid-template-columns:220px 1fr}
  .sidebar-right{display:none}
}
@media(max-width:900px){
  .layout{grid-template-columns:1fr}
  .sidebar-left{display:none}
  .sidebar-left.open{display:block;position:fixed;left:0;top:0;width:min(300px,85vw);height:100vh;z-index:150;overflow-y:auto;background:var(--white);padding:16px;box-shadow:4px 0 20px rgba(0,0,0,.2)}
  .sidebar-mobile-panels{display:block}
  .sidebar-toggle{display:inline-flex;align-items:center}
  .fab{display:flex}
  .header-desktop-only{display:none}
  .header-actions input[type=search]{width:120px !important}
  .onenon-layout{flex-direction:column;padding:12px;gap:12px}
  .onenon-sidebar{width:100%}
  .onenon-main{overflow-y:visible}
}
@media(max-width:600px){
  .matrix{grid-template-columns:1fr}
  .form-row{grid-template-columns:1fr}
}
</style>
</head>
<body>

<header class="app-header">
  <div class="container">
    <div class="header-inner">
      <div style="display:flex;align-items:center;gap:12px">
        <button class="sidebar-toggle" id="sidebarToggle">☰</button>
        <svg width="26" height="26" viewBox="0 0 32 32" style="flex-shrink:0;border-radius:5px">
          <rect width="32" height="32" rx="6" fill="rgba(255,255,255,0.12)"/>
          <rect x="6" y="6" width="9" height="9" rx="2" fill="#E05C4E"/>
          <rect x="17" y="6" width="9" height="9" rx="2" fill="rgba(255,255,255,0.45)"/>
          <rect x="6" y="17" width="9" height="9" rx="2" fill="rgba(255,255,255,0.45)"/>
          <rect x="17" y="17" width="9" height="9" rx="2" fill="rgba(255,255,255,0.18)"/>
        </svg>
        <div>
          <h1>Tasks</h1>
          <p class="header-desc">Jiří Šach · Prioritizace</p>
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
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>
    <aside class="sidebar-left" id="sidebarLeft"></aside>
    <div id="mainContent"></div>
    <aside class="sidebar-right" id="sidebarRight"></aside>
  </div>
</main>

<a class="todo-footer-link" id="todoFooterLink">TODO</a>
<button class="fab" id="fab">+</button>
<div id="modals"></div>
<div class="toast" id="toast"></div>

<script>const CURRENT_USER = <?= json_encode($_SESSION['user'] ?? '') ?>;</script>
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
function TaskCard({ task, onToggleDone, onEdit, onDelete, onInlineEdit, onDragStart, onAddToDaily }) {
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
  const isTouch = typeof window !== 'undefined' && ('ontouchstart' in window);

  return (
    <div
      className={cardClass}
      onClick={() => !editing && onEdit(task)}
      draggable={!isTouch}
      onDragStart={e => { if (isTouch) return; e.dataTransfer.setData('taskId', task.id); e.currentTarget.classList.add('dragging'); if (onDragStart) onDragStart(task); }}
      onDragEnd={e => { if (isTouch) return; e.currentTarget.classList.remove('dragging'); }}
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
      {onAddToDaily && (
        <button className="task-del" title="Přidat do Dnes" onClick={e => { e.stopPropagation(); onAddToDaily(task); }} style={{color:'var(--navy)',fontSize:'11px',fontWeight:700,marginLeft:'-2px'}}>+D</button>
      )}
    </div>
  );
}

// ---- Quadrant ----
function Quadrant({ q, tasks, filter, onToggleDone, onEdit, onDelete, onAddTask, onInlineEdit, onMoveTask, onAddToDaily }) {
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
      className={'quadrant q-' + q.key + (dragOver ? ' drag-over' : '')}
      onDragOver={handleDragOver}
      onDragLeave={() => setDragOver(false)}
      onDrop={handleDrop}
    >
      <div className="q-header">
        <div className="q-label">{q.label} {visible.length > 0 && <span style={{fontSize:'10px',fontWeight:400,color:'var(--grey-text)'}}>({visible.length})</span>}</div>
      </div>
      {visible.map(t => (
        <TaskCard key={t.id} task={t} onToggleDone={onToggleDone} onEdit={onEdit} onDelete={onDelete} onInlineEdit={onInlineEdit} onAddToDaily={onAddToDaily} />
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
function ChecklistPanel({ items, todayDone, onAdd, onToggle, onDelete, onEdit }) {
  const [newTitle, setNewTitle] = useState('');
  const [editingId, setEditingId] = useState(null);
  const [editVal, setEditVal] = useState('');

  async function handleAdd() {
    if (!newTitle.trim()) return;
    await onAdd(newTitle);
    setNewTitle('');
  }

  function startEdit(i) {
    setEditingId(i.id);
    setEditVal(i.title);
  }

  async function commitEdit(i) {
    const val = editVal.trim();
    if (val && val !== i.title) await onEdit(i, val);
    setEditingId(null);
  }

  function renderItem(i, isDone) {
    return (
      <div key={i.id} className="cl-item">
        <input type="checkbox" checked={isDone} onChange={() => onToggle(i, !isDone)} />
        {editingId === i.id
          ? <input
              className="cl-item-edit"
              value={editVal}
              autoFocus
              onChange={e => setEditVal(e.target.value)}
              onBlur={() => commitEdit(i)}
              onKeyDown={e => { if (e.key === 'Enter') commitEdit(i); if (e.key === 'Escape') setEditingId(null); }}
            />
          : <span className={'cl-item-title' + (isDone ? ' done' : '')} onDoubleClick={() => !isDone && startEdit(i)}>{i.title}</span>
        }
        <button className="cl-del" onClick={() => onDelete(i)}>×</button>
      </div>
    );
  }

  const open = items.filter(i => !i.done);
  const done  = items.filter(i => i.done);

  return (
    <div className="panel">
      <div className="section-title">
        Rychlý checklist
        {todayDone > 0 && <span className="badge badge-work">{todayDone} dnes</span>}
      </div>
      {open.map(i => renderItem(i, false))}
      {done.length > 0 && done.map(i => renderItem(i, true))}
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

// ---- Panel height hook ----
const PANEL_HEIGHTS = [200, 320, 480, 9999];
const PANEL_HEIGHT_LABELS = ['S', 'M', 'L', '∞'];
function usePanelHeight(key, defaultIdx) {
  const stored = parseInt(localStorage.getItem('panelH_' + key) || defaultIdx, 10);
  const [idx, setIdx] = useState(isNaN(stored) ? defaultIdx : stored);
  function cycle() {
    const next = (idx + 1) % PANEL_HEIGHTS.length;
    setIdx(next);
    localStorage.setItem('panelH_' + key, next);
  }
  return [PANEL_HEIGHTS[idx] === 9999 ? 'none' : PANEL_HEIGHTS[idx] + 'px', PANEL_HEIGHT_LABELS[idx], cycle];
}

// ---- Daktela Panel ----
function DaktelaPanel({ tickets, refreshedAt, token, onConnectClick, onRefresh, onCreateTask, assignedMap }) {
  const [refreshing, setRefreshing] = useState(false);
  const [maxH, sizeLabel, cycleSize] = usePanelHeight('daktela', 1);

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

  const [showAssigned, setShowAssigned] = useState(false);
  const am = assignedMap || {};
  const free = tickets.filter(t => !am[t.name]);
  const assigned = tickets.filter(t => am[t.name]);

  return (
    <div className="panel">
      <div className="section-title">
        Daktela tickety
        <span style={{display:'flex',gap:6,alignItems:'center'}}>
          <button onClick={cycleSize} title="Výška panelu" style={{background:'var(--grey-bg)',border:'1px solid var(--grey-border)',borderRadius:4,cursor:'pointer',fontSize:'10px',color:'var(--grey-text)',padding:'1px 6px',fontWeight:700,fontFamily:'var(--font)'}}>{sizeLabel}</button>
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
      <div className="tickets-scroll" style={{maxHeight:maxH}}>
        {free.length === 0 && assigned.length === 0 && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Žádné otevřené tickety</div>}
        {free.map(t => (
          <div key={t.name} className="ticket-row">
            <span className={'stage-pill stage-' + (t.stage || 'OPEN')}>{t.stage || 'OPEN'}</span>
            <a className="ticket-title" href={'https://daktela.daktela.com/tickets/update/' + t.name} target="_blank" rel="noreferrer" title={t.title + ' (' + t.name + ')'}>{t.title}</a>
            <button className="ticket-add-btn" onClick={() => onCreateTask(t)}>+ Task</button>
          </div>
        ))}
      </div>
      {assigned.length > 0 && (
        <div style={{marginTop:6,borderTop:'1px solid var(--grey-border)',paddingTop:6}}>
          <button onClick={() => setShowAssigned(v => !v)} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--grey-text)',padding:'2px 0',width:'100%',textAlign:'left',fontFamily:'var(--font)',display:'flex',alignItems:'center',gap:4}}>
            <span style={{fontSize:'9px'}}>{showAssigned ? '▾' : '▸'}</span>
            <span>Přiřazené</span>
            <span style={{background:'var(--grey-bg)',border:'1px solid var(--grey-border)',borderRadius:10,fontSize:'10px',fontWeight:700,padding:'0 6px',color:'var(--navy)',marginLeft:2}}>{assigned.length}</span>
          </button>
          {showAssigned && (
            <div style={{marginTop:4}}>
              {assigned.map(t => (
                <div key={t.name} className="ticket-row" style={{fontSize:'11px'}}>
                  <span className={'stage-pill stage-' + (t.stage || 'OPEN')} style={{flexShrink:0}}>{t.stage || 'OPEN'}</span>
                  <a href={'https://daktela.daktela.com/tickets/update/' + t.name} target="_blank" rel="noreferrer" style={{flex:1,minWidth:0,overflow:'hidden',textOverflow:'ellipsis',whiteSpace:'nowrap',color:'var(--navy)',textDecoration:'none',fontWeight:500}}>{t.title}</a>
                  <span style={{color:'var(--grey-text)',flexShrink:0,whiteSpace:'nowrap',fontSize:'10px'}}>→ {am[t.name]}</span>
                </div>
              ))}
            </div>
          )}
        </div>
      )}
      <div style={{fontSize:'11px',color:'var(--grey-text)',marginTop:8,display:'flex',justifyContent:'space-between',alignItems:'center'}}>
        <span>Obnoveno: {formatRefreshed(refreshedAt)}</span>
        {token && <button onClick={onConnectClick} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--grey-text)',textDecoration:'underline',padding:0}}>přihlásit znovu</button>}
      </div>
    </div>
  );
}

// ---- Calendar Panel ----
function CalendarPanel({ events, connected, onConnect, onDisconnect, onCreateTask, onRefresh }) {
  const [maxH, sizeLabel, cycleSize] = usePanelHeight('calendar', 1);
  const [refreshing, setRefreshing] = React.useState(false);
  async function handleRefresh() {
    setRefreshing(true);
    try { await onRefresh(); } catch(e) {}
    setRefreshing(false);
  }
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
        <span style={{display:'flex',gap:6,alignItems:'center'}}>
          {connected && <button onClick={cycleSize} title="Výška panelu" style={{background:'var(--grey-bg)',border:'1px solid var(--grey-border)',borderRadius:4,cursor:'pointer',fontSize:'10px',color:'var(--grey-text)',padding:'1px 6px',fontWeight:700,fontFamily:'var(--font)'}}>{sizeLabel}</button>}
          {connected && <button onClick={handleRefresh} disabled={refreshing} style={{background:'none',border:'none',cursor:'pointer',fontSize:'12px',color:'var(--grey-text)',padding:0}} title="Obnovit kalendář">{refreshing ? '...' : '↻'}</button>}
          {connected
            ? <button onClick={onDisconnect} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--grey-text)'}}>Odpojit</button>
            : <button onClick={onConnect} style={{background:'none',border:'none',cursor:'pointer',fontSize:'11px',color:'var(--navy)',fontWeight:700}}>Propojit</button>
          }
        </span>
      </div>
      {!connected && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Propoj Google Calendar pro zobrazení událostí.</div>}
      {connected && events.length === 0 && <div style={{fontSize:'12px',color:'var(--grey-text)'}}>Žádné nadcházející události</div>}
      {connected && <div className="cal-scroll" style={{maxHeight:maxH}}>{dayGroups.map(group => (
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
      ))}</div>}
    </div>
  );
}

// ---- DnesView ----
function DnesView({ tasks, onToggleDone, onEdit, onRemoveFromDaily, onReorder }) {
  const [dragId, setDragId] = useState(null);
  const [dragOverId, setDragOverId] = useState(null);

  const dnesTasks = tasks
    .filter(t => t.status === 'open' && t.daily_order !== null && t.daily_order !== undefined)
    .sort((a, b) => a.daily_order - b.daily_order);

  const today = new Date().toISOString().split('T')[0];

  function handleDragStart(id) { setDragId(id); }
  function handleDragOver(e, id) { e.preventDefault(); setDragOverId(id); }
  function handleDrop(e, targetId) {
    e.preventDefault();
    setDragOverId(null);
    if (dragId && dragId !== targetId) onReorder(dragId, targetId);
    setDragId(null);
  }
  function handleDragEnd() { setDragId(null); setDragOverId(null); }

  if (dnesTasks.length === 0) {
    return (
      <div style={{padding:'40px 24px',textAlign:'center',color:'var(--grey-text)'}}>
        <div style={{fontSize:'32px',marginBottom:'12px'}}>📋</div>
        <div style={{fontWeight:600,marginBottom:'6px'}}>Denní plán je prázdný</div>
        <div style={{fontSize:'13px'}}>Přidej tasky pomocí <strong>+D</strong> tlačítka v matici</div>
      </div>
    );
  }

  return (
    <div style={{padding:'16px 0'}}>
      <div style={{fontSize:'11px',fontWeight:700,color:'var(--grey-text)',letterSpacing:'0.05em',textTransform:'uppercase',padding:'0 24px 12px'}}>
        Dnes — {dnesTasks.length} {dnesTasks.length === 1 ? 'task' : dnesTasks.length < 5 ? 'tasky' : 'tasků'}
      </div>
      {dnesTasks.map((t, idx) => {
        const isOverdue = t.due_date && t.due_date < today;
        const daysUntil = t.due_date ? Math.ceil((new Date(t.due_date) - new Date(today)) / 86400000) : null;
        const isSoon = !isOverdue && daysUntil !== null && daysUntil <= 3;
        return (
          <div
            key={t.id}
            draggable
            onDragStart={() => handleDragStart(t.id)}
            onDragOver={e => handleDragOver(e, t.id)}
            onDrop={e => handleDrop(e, t.id)}
            onDragEnd={handleDragEnd}
            style={{
              display:'flex',alignItems:'center',gap:'10px',
              padding:'10px 24px',
              borderBottom:'1px solid var(--grey-border)',
              background: dragOverId === t.id ? '#EBF0FF' : 'transparent',
              opacity: dragId === t.id ? 0.4 : 1,
              cursor:'grab',
            }}
          >
            <span style={{color:'var(--grey-text)',fontSize:'12px',fontWeight:700,width:'18px',flexShrink:0}}>{idx + 1}</span>
            <input
              type="checkbox"
              checked={false}
              style={{accentColor:'var(--red)',width:'15px',height:'15px',flexShrink:0,cursor:'pointer'}}
              onChange={() => onToggleDone(t)}
            />
            <span
              onClick={() => onEdit(t)}
              style={{flex:1,fontSize:'14px',cursor:'pointer',color: isOverdue ? '#c0392b' : 'inherit',fontWeight: isOverdue ? 600 : 'normal'}}
            >{t.title}</span>
            {t.due_date && (
              <span style={{fontSize:'11px',fontWeight:700,padding:'1px 6px',borderRadius:'4px',
                background: isOverdue ? '#FEE8E7' : isSoon ? '#FFF4E0' : '#F4F5F7',
                color: isOverdue ? '#E63327' : isSoon ? '#A06000' : 'var(--grey-text)',
                flexShrink:0}}>
                {isOverdue ? 'Prošlé' : daysUntil === 0 ? 'Dnes' : daysUntil + 'd'}
              </span>
            )}
            <span className={'badge ' + (t.type === 'personal' ? 'badge-personal' : 'badge-work')} style={{flexShrink:0}}>
              {t.type === 'personal' ? 'osobní' : 'práce'}
            </span>
            <button
              title="Odebrat z denního plánu"
              onClick={() => onRemoveFromDaily(t)}
              style={{background:'none',border:'none',cursor:'pointer',color:'var(--grey-border)',fontSize:'14px',flexShrink:0,padding:'0 2px'}}
            >×</button>
          </div>
        );
      })}
    </div>
  );
}

// ---- Q1AlertBadge ----
function Q1AlertBadge({ tasks, onEditTask }) {
  const [open, setOpen] = useState(false);
  const ref = useRef(null);
  const today = new Date().toISOString().slice(0, 10);

  useEffect(() => {
    if (!open) return;
    function handleClick(e) { if (ref.current && !ref.current.contains(e.target)) setOpen(false); }
    document.addEventListener('mousedown', handleClick);
    return () => document.removeEventListener('mousedown', handleClick);
  }, [open]);

  return (
    <span ref={ref} className="q1-alert" onClick={() => setOpen(v => !v)} title="Q1 tasky s deadlinem — klikni pro detail">
      {tasks.length}
      {open && (
        <div className="q1-popover" onClick={e => e.stopPropagation()}>
          <div className="q1-popover-header">Urgentní tasky s deadlinem</div>
          {tasks.map(t => {
            const daysUntil = Math.ceil((new Date(t.due_date) - new Date(today)) / 86400000);
            const isOverdue = daysUntil < 0;
            const label = isOverdue ? 'Prošlé ' + Math.abs(daysUntil) + 'd' : daysUntil === 0 ? 'Dnes' : 'Za ' + daysUntil + 'd';
            return (
              <div key={t.id} className="q1-popover-item" onClick={() => { onEditTask(t); setOpen(false); }}>
                <span className="q1-popover-title">{t.title}</span>
                <span className="q1-popover-deadline" style={{background: isOverdue ? '#FEE8E7' : '#FFF4E0', color: isOverdue ? '#E63327' : '#A06000'}}>{label}</span>
              </div>
            );
          })}
        </div>
      )}
    </span>
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

function OneOnOneContextPanel({ ctx }) {
  if (!ctx || !ctx.person) {
    return (
      <div className="panel" style={{color:'var(--grey-text)',fontSize:12,textAlign:'center',padding:20}}>
        Vyber osobu pro zobrazení kontextu
      </div>
    );
  }
  const { person, profile, lastNote, openItems } = ctx;
  const badgeColor = { low: '#4CAF50', medium: '#F5A623', high: '#E05C4E' };
  const potentialLabel = { low: 'Nízký', medium: 'Střední', high: 'Vysoký' };
  const effortLabel = { low: 'Nízká', medium: 'Střední', high: 'Vysoká' };
  return (
    <div className="panel">
      <div style={{fontWeight:700,fontSize:14,color:'var(--navy)',marginBottom:12}}>{person}</div>
      {lastNote && (
        <div style={{marginBottom:12}}>
          <div style={{fontSize:10,fontWeight:700,textTransform:'uppercase',letterSpacing:'.4px',color:'var(--grey-text)',marginBottom:4}}>Poslední 1on1</div>
          <div style={{fontSize:12,color:'var(--navy)'}}>{lastNote.meeting_date}</div>
          {lastNote.mood > 0 && <div style={{color:'#F5A623',fontSize:13,marginTop:2}}>{'★'.repeat(lastNote.mood)}{'☆'.repeat(5-lastNote.mood)}</div>}
          {(lastNote.tags || []).length > 0 && <div style={{marginTop:4}}>{lastNote.tags.map(t => <span key={t} className="onenon-tag-chip">{t}</span>)}</div>}
        </div>
      )}
      {openItems > 0 && (
        <div style={{marginBottom:12,background:'#FFF4E0',border:'1px solid #F5A623',borderRadius:6,padding:'6px 10px',fontSize:12}}>
          <span style={{fontWeight:700,color:'#A06000'}}>Otevřené action items: {openItems}</span>
        </div>
      )}
      {profile && (
        <div>
          <div style={{fontSize:10,fontWeight:700,textTransform:'uppercase',letterSpacing:'.4px',color:'var(--grey-text)',marginBottom:6}}>Profil</div>
          {profile.performance > 0 && <div style={{fontSize:12,marginBottom:5,display:'flex',justifyContent:'space-between'}}><span style={{color:'var(--grey-text)'}}>Výkon</span><span style={{color:'#F5A623'}}>{'★'.repeat(profile.performance)}{'☆'.repeat(5-profile.performance)}</span></div>}
          {profile.potential && <div style={{fontSize:12,marginBottom:5,display:'flex',justifyContent:'space-between',alignItems:'center'}}><span style={{color:'var(--grey-text)'}}>Potenciál</span><span style={{background:badgeColor[profile.potential]||'#888',color:'#fff',fontSize:10,fontWeight:700,padding:'1px 7px',borderRadius:10}}>{potentialLabel[profile.potential]||profile.potential}</span></div>}
          {profile.mgmt_effort && <div style={{fontSize:12,marginBottom:5,display:'flex',justifyContent:'space-between',alignItems:'center'}}><span style={{color:'var(--grey-text)'}}>Náročnost</span><span style={{background:badgeColor[profile.mgmt_effort]||'#888',color:'#fff',fontSize:10,fontWeight:700,padding:'1px 7px',borderRadius:10}}>{effortLabel[profile.mgmt_effort]||profile.mgmt_effort}</span></div>}
          {profile.strength && <div style={{fontSize:12,marginBottom:5}}><div style={{color:'var(--grey-text)',fontSize:10,marginBottom:1}}>Silná stránka</div>{profile.strength}</div>}
          {profile.development && <div style={{fontSize:12,marginBottom:5}}><div style={{color:'var(--grey-text)',fontSize:10,marginBottom:1}}>Oblast rozvoje</div>{profile.development}</div>}
        </div>
      )}
    </div>
  );
}

function OneOnOneView({ daktelaToken, onContextChange, onConnectDaktela }) {
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
    const fetchedNotes = d.notes || [];
    const fetchedProfile = d.profile || null;
    setNotes(fetchedNotes);
    setSelectedDesc(d.description || '');
    setSelectedProfile(fetchedProfile);
    if (onContextChange) {
      const openItems = fetchedNotes.reduce((s, n) => s + (n.action_items || []).filter(i => !i.done).length, 0);
      onContextChange({ person, profile: fetchedProfile, lastNote: fetchedNotes[0] || null, openItems });
    }
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
        {!daktelaToken && (
          <div style={{background:'var(--grey-bg)',border:'1px solid var(--grey-border)',borderRadius:6,padding:'8px 10px',marginBottom:10,fontSize:12}}>
            <div style={{color:'var(--grey-text)',marginBottom:6}}>Pro načtení agentů připoj Daktelu.</div>
            <button className="btn btn-secondary" style={{width:'100%',fontSize:11}} onClick={onConnectDaktela}>Připojit Daktelu</button>
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
                  <button className="onenon-person-edit-btn" title="Upravit" onClick={e => { e.stopPropagation(); setEditingPerson({ name: p.person, description: p.description || '', profile: p.profile || null }); }}>✎</button>
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
  // Sekce: uživatelské jméno
  const [newUser, setNewUser] = React.useState('');
  const [userOldPass, setUserOldPass] = React.useState('');
  const [userSaving, setUserSaving] = React.useState(false);
  const [userMsg, setUserMsg] = React.useState(null); // {ok, text}

  // Sekce: heslo
  const [oldPass, setOldPass] = React.useState('');
  const [newPass, setNewPass] = React.useState('');
  const [newPass2, setNewPass2] = React.useState('');
  const [passSaving, setPassSaving] = React.useState(false);
  const [passMsg, setPassMsg] = React.useState(null);

  // Viditelnost hesel
  const [showUserOld, setShowUserOld] = React.useState(false);
  const [showOld, setShowOld] = React.useState(false);
  const [showNew, setShowNew] = React.useState(false);

  async function handleUserSave() {
    if (!newUser.trim()) { setUserMsg({ok:false, text:'Zadej nové uživatelské jméno'}); return; }
    if (!userOldPass) { setUserMsg({ok:false, text:'Zadej stávající heslo'}); return; }
    setUserSaving(true); setUserMsg(null);
    try {
      await apiFetch('settings', 'POST', { old_password: userOldPass, new_username: newUser.trim(), new_password: '' });
      setUserMsg({ok:true, text:'Uživatelské jméno změněno'});
      setNewUser(''); setUserOldPass('');
    } catch(e) { setUserMsg({ok:false, text: e.message || 'Chyba'}); }
    setUserSaving(false);
  }

  async function handlePassSave() {
    if (!oldPass) { setPassMsg({ok:false, text:'Zadej stávající heslo'}); return; }
    if (newPass.length < 8) { setPassMsg({ok:false, text:'Nové heslo musí mít alespoň 8 znaků'}); return; }
    if (newPass !== newPass2) { setPassMsg({ok:false, text:'Hesla se neshodují'}); return; }
    setPassSaving(true); setPassMsg(null);
    try {
      await apiFetch('settings', 'POST', { old_password: oldPass, new_username: '', new_password: newPass });
      setPassMsg({ok:true, text:'Heslo změněno'});
      setOldPass(''); setNewPass(''); setNewPass2('');
    } catch(e) { setPassMsg({ok:false, text: e.message || 'Chyba'}); }
    setPassSaving(false);
  }

  function pwInput(val, set, show, setShow, placeholder, autocomplete) {
    return (
      <div style={{position:'relative'}}>
        <input type={show ? 'text' : 'password'} value={val} onChange={e => set(e.target.value)}
          placeholder={placeholder} autocomplete={autocomplete}
          style={{width:'100%',paddingRight:36}} />
        <button type="button" onClick={() => setShow(v => !v)}
          style={{position:'absolute',right:8,top:'50%',transform:'translateY(-50%)',background:'none',border:'none',cursor:'pointer',fontSize:15,padding:0,color:'var(--grey-text)'}}>
          {show ? '🙈' : '👁'}
        </button>
      </div>
    );
  }

  function Feedback({msg}) {
    if (!msg) return null;
    return <div style={{fontSize:12,marginTop:6,color:msg.ok ? '#2a7a2a' : 'var(--red)',fontWeight:500}}>{msg.text}</div>;
  }

  const sectionStyle = {background:'var(--grey-bg)',borderRadius:8,padding:'16px 18px',marginBottom:16};
  const labelStyle = {display:'block',fontSize:11,fontWeight:600,color:'var(--grey-text)',textTransform:'uppercase',letterSpacing:'.4px',marginBottom:5};
  const fgStyle = {marginBottom:12};

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal">
        <div style={{display:'flex',alignItems:'center',justifyContent:'space-between',marginBottom:20}}>
          <h2 style={{margin:0}}>Nastavení účtu</h2>
          <button className="btn btn-ghost" onClick={onClose} style={{fontSize:18,lineHeight:1,padding:'2px 8px'}}>×</button>
        </div>

        <div style={sectionStyle}>
          <div style={{fontSize:13,fontWeight:700,marginBottom:12,color:'var(--navy)'}}>Uživatelské jméno</div>
          <div style={{fontSize:12,color:'var(--grey-text)',marginBottom:12}}>Aktuálně: <strong style={{color:'var(--navy)'}}>{CURRENT_USER}</strong></div>
          <div style={fgStyle}>
            <label style={labelStyle}>Nové uživatelské jméno</label>
            <input value={newUser} onChange={e => setNewUser(e.target.value)}
              placeholder="Nové jméno..." autocomplete="username" autocapitalize="off" autocorrect="off" />
          </div>
          <div style={fgStyle}>
            <label style={labelStyle}>Stávající heslo (potvrzení)</label>
            {pwInput(userOldPass, setUserOldPass, showUserOld, setShowUserOld, 'Heslo pro potvrzení', 'current-password')}
          </div>
          <Feedback msg={userMsg} />
          <button className="btn btn-primary" onClick={handleUserSave} disabled={userSaving || !newUser.trim()}
            style={{marginTop:4,width:'100%'}}>{userSaving ? 'Ukládám...' : 'Změnit uživatelské jméno'}</button>
        </div>

        <div style={sectionStyle}>
          <div style={{fontSize:13,fontWeight:700,marginBottom:12,color:'var(--navy)'}}>Heslo</div>
          <div style={fgStyle}>
            <label style={labelStyle}>Stávající heslo</label>
            {pwInput(oldPass, setOldPass, showOld, setShowOld, 'Stávající heslo', 'current-password')}
          </div>
          <div style={fgStyle}>
            <label style={labelStyle}>Nové heslo</label>
            {pwInput(newPass, setNewPass, showNew, setShowNew, 'Min. 8 znaků', 'new-password')}
          </div>
          <div style={fgStyle}>
            <label style={labelStyle}>Potvrdit nové heslo</label>
            {pwInput(newPass2, setNewPass2, showNew, setShowNew, 'Znovu nové heslo', 'new-password')}
          </div>
          <Feedback msg={passMsg} />
          <button className="btn btn-primary" onClick={handlePassSave} disabled={passSaving || !oldPass || !newPass}
            style={{marginTop:4,width:'100%'}}>{passSaving ? 'Ukládám...' : 'Změnit heslo'}</button>
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
  const [onenonCtx, setOnenonCtx] = useState(null);

  useEffect(() => {
    const layout = document.getElementById('layout');
    if (layout) layout.classList.toggle('onenon-mode', activeTab === 'onenon');
    if (activeTab !== 'onenon') setOnenonCtx(null);
  }, [activeTab]);

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
    document.getElementById('todoFooterLink').onclick = () => setModal({ type: 'todo' });

    // Sidebar toggle
    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebar = document.getElementById('sidebarLeft');
    function toggleSidebar(){
      sidebar.classList.toggle('open');
      backdrop.classList.toggle('active');
    }
    document.getElementById('sidebarToggle').onclick = toggleSidebar;
    backdrop.onclick = () => { sidebar.classList.remove('open'); backdrop.classList.remove('active'); };

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

  async function handleEditCl(item, newTitle) {
    const result = await apiFetch('checklist', 'PUT', { title: newTitle }, { id: item.id });
    setChecklistItems(prev => prev.map(i => i.id === item.id ? result.item : i));
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
  async function handleAddToDaily(task) {
    const maxOrder = tasks.reduce((m, t) => (t.daily_order !== null && t.daily_order !== undefined) ? Math.max(m, t.daily_order) : m, 0);
    const result = await apiFetch('tasks', 'PUT', { daily_order: maxOrder + 1 }, { id: task.id });
    setTasks(prev => prev.map(t => t.id === task.id ? result.task : t));
    toast('Přidáno do Dnes');
  }

  async function handleRemoveFromDaily(task) {
    const result = await apiFetch('tasks', 'PUT', { daily_order: null }, { id: task.id });
    setTasks(prev => prev.map(t => t.id === task.id ? result.task : t));
  }

  async function handleDnesReorder(dragId, targetId) {
    const dnesSorted = tasks
      .filter(t => t.status === 'open' && t.daily_order !== null && t.daily_order !== undefined)
      .sort((a, b) => a.daily_order - b.daily_order);
    const fromIdx = dnesSorted.findIndex(t => t.id === dragId);
    const toIdx   = dnesSorted.findIndex(t => t.id === targetId);
    if (fromIdx < 0 || toIdx < 0) return;
    const reordered = [...dnesSorted];
    const [moved] = reordered.splice(fromIdx, 1);
    reordered.splice(toIdx, 0, moved);
    const updates = reordered.map((t, i) => ({ id: t.id, daily_order: i + 1 }));
    setTasks(prev => prev.map(t => {
      const u = updates.find(u => u.id === t.id);
      return u ? { ...t, daily_order: u.daily_order } : t;
    }));
    await Promise.all(updates.map(u => apiFetch('tasks', 'PUT', { daily_order: u.daily_order }, { id: u.id })));
  }

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
  const q1Today = new Date().toISOString().slice(0, 10);
  const q1DeadlineTasks = openTasks.filter(t => t.quadrant === 'urgent_important' && t.due_date && t.due_date <= q1Today);

  const dnesCount = openTasks.filter(t => t.daily_order !== null && t.daily_order !== undefined).length;

  const TABS = [
    { key: 'all', label: 'Vše', count: openTasks.length },
    { key: 'work', label: 'Pracovní', count: workCount },
    { key: 'personal', label: 'Osobní', count: personalCount },
    { key: 'dnes', label: 'Dnes', count: dnesCount },
    { key: 'history', label: 'Historie', count: null },
    { key: 'onenon', label: '1on1', count: null },
  ];

  const assignedMap = React.useMemo(() => {
    const m = {};
    tasks.forEach(task => {
      (task.daktela_tickets || []).forEach(n => { m[n] = task.title; });
    });
    return m;
  }, [tasks]);

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
          <button className="btn btn-ghost header-desktop-only" onClick={handleAiSuggest} disabled={aiLoading}>
            {aiLoading ? '...' : '✦ AI priority'}
          </button>
          <button className="btn btn-primary" onClick={() => setModal({ type: 'task' })}>+ Task</button>
          <button className="btn btn-ghost" style={{fontSize:'12px',padding:'6px 10px'}} onClick={() => setQuickCapture(true)} title="Cmd+K">⚡</button>
          <button className="btn btn-ghost header-desktop-only" style={{fontSize:'12px'}} onClick={() => setModal({ type: 'settings' })}>⚙</button>
          <button className="btn btn-ghost header-desktop-only" style={{fontSize:'12px'}} onClick={async () => {
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
          {q1DeadlineTasks.length > 0 && (
            <Q1AlertBadge tasks={q1DeadlineTasks} onEditTask={handleEditTask} />
          )}
        </div>,
        document.getElementById('tabBar')
      )}

      {/* Levý sidebar: KPI + Checklist nebo 1on1 kontext */}
      {ReactDOM.createPortal(
        activeTab === 'onenon'
          ? <OneOnOneContextPanel ctx={onenonCtx} />
          : <>
              <KpiPanel todayDone={todayDone + clTodayDone} totalOpen={totalOpen} />
              <ChecklistPanel
                items={checklistItems}
                todayDone={clTodayDone}
                onAdd={handleAddCl}
                onToggle={handleToggleCl}
                onDelete={handleDeleteCl}
                onEdit={handleEditCl}
              />
              <div className="sidebar-mobile-panels">
                <DaktelaPanel
                  tickets={daktelaTickets}
                  refreshedAt={daktelaRefreshedAt}
                  token={daktelaToken}
                  onConnectClick={() => setModal({ type: 'daktela' })}
                  onRefresh={refreshDaktelaCache}
                  onCreateTask={handleDaktelaCreateTask}
                  assignedMap={assignedMap}
                />
                <CalendarPanel
                  events={calEvents}
                  connected={calConnected}
                  onConnect={handleCalConnect}
                  onDisconnect={handleCalDisconnect}
                  onRefresh={loadCalendar}
                  onCreateTask={e => setModal({ type: 'task', defaults: { title: e.title, due_date: e.date, quadrant: 'important', type: 'work' } })}
                />
              </div>
            </>,
        document.getElementById('sidebarLeft')
      )}

      {/* Pravý sidebar: Daktela + Calendar */}
      {ReactDOM.createPortal(
        <>
          <DaktelaPanel
            tickets={daktelaTickets}
            refreshedAt={daktelaRefreshedAt}
            token={daktelaToken}
            onConnectClick={() => setModal({ type: 'daktela' })}
            onRefresh={refreshDaktelaCache}
            onCreateTask={handleDaktelaCreateTask}
            assignedMap={assignedMap}
          />
          <CalendarPanel
            events={calEvents}
            connected={calConnected}
            onConnect={handleCalConnect}
            onDisconnect={handleCalDisconnect}
            onRefresh={loadCalendar}
            onCreateTask={e => setModal({ type: 'task', defaults: { title: e.title, due_date: e.date, quadrant: 'important', type: 'work' } })}
          />
        </>,
        document.getElementById('sidebarRight')
      )}

      {/* Main content */}
      {ReactDOM.createPortal(
        searchQuery.length >= 2
          ? <SearchResults query={searchQuery} checklistItems={checklistItems} daktelaTickets={daktelaTickets} onEditTask={handleEditTask} onToggleCl={handleToggleCl} />
          : activeTab === 'history'
          ? <HistoryView filter="all" onReopen={handleReopenTask} />
          : activeTab === 'onenon'
          ? <OneOnOneView daktelaToken={daktelaToken} onContextChange={setOnenonCtx} onConnectDaktela={() => setModal({ type: 'daktela' })} />
          : activeTab === 'dnes'
          ? <DnesView tasks={tasks} onToggleDone={handleToggleDone} onEdit={handleEditTask} onRemoveFromDaily={handleRemoveFromDaily} onReorder={handleDnesReorder} />
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
                    onAddToDaily={handleAddToDaily}
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

      {/* Modals — portal do body kvůli stacking context na mobilu */}
      {modal?.type === 'task' && ReactDOM.createPortal(
        <TaskModal
          task={modal.task || null}
          defaultQuadrant={(modal.defaults || {}).quadrant}
          defaultType={(modal.defaults || {}).type}
          defaultTickets={(modal.defaults || {}).daktela_tickets}
          availableTickets={daktelaTickets}
          assignedMap={assignedMap}
          onSave={handleSaveTask}
          onDelete={async t => { const done = await handleDeleteTask(t); if (done) setModal(null); }}
          onClose={() => setModal(null)}
        />,
        document.body
      )}
      {modal?.type === 'daktela' && ReactDOM.createPortal(
        <DaktelaAuthModal
          onConnected={token => { setDaktelaToken(token); sessionStorage.setItem('daktela_token', token); refreshDaktelaCache(token); }}
          onClose={() => setModal(null)}
        />,
        document.body
      )}
      {modal?.type === 'settings' && ReactDOM.createPortal(
        <SettingsModal onClose={() => setModal(null)} />,
        document.body
      )}
      {modal?.type === 'ai' && ReactDOM.createPortal(
        <AiSuggestModal
          suggestions={modal.suggestions}
          tasks={tasks}
          onApply={handleApplyAi}
          onClose={() => setModal(null)}
        />,
        document.body
      )}

      {modal?.type === 'todo' && ReactDOM.createPortal(
        <TodoModal onClose={() => setModal(null)} />,
        document.body
      )}

      {quickCapture && ReactDOM.createPortal(
        <QuickCapture
          onSave={handleAddTask}
          onClose={() => setQuickCapture(false)}
        />,
        document.body
      )}

      {loading && ReactDOM.createPortal(
        <div className="loading-overlay">
          <div className="spinner"></div>
        </div>,
        document.body
      )}
    </>
  );
}

function TodoModal({ onClose }) {
  const [content, setContent] = React.useState('Načítám...');
  React.useEffect(() => {
    fetch('api.php?action=todo')
      .then(r => r.json())
      .then(d => setContent(d.content || '(prázdné)'))
      .catch(() => setContent('Chyba načítání'));
  }, []);
  return (
    <div className="todo-modal-overlay" onClick={onClose}>
      <div className="todo-modal-box" onClick={e => e.stopPropagation()}>
        <div className="todo-modal-header">
          <span>TODO.md</span>
          <button className="btn-close" onClick={onClose}>✕</button>
        </div>
        <div className="todo-modal-body">{content}</div>
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('app-root')).render(<App />);
</script>
</body>
</html>
