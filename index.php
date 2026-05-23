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
:root{--bg:#F7F7F8;--surface:#FFFFFF;--surface-2:#F0F0F2;--border:#E4E4E7;--text:#18181B;--text-2:#71717A;--text-3:#A1A1AA;--accent:#2563EB;--accent-bg:#EFF6FF;--blue:#2563EB;--blue-hover:#1d4ed8;--danger:#DC2626;--danger-bg:#FFF5F5;--warning:#D97706;--warning-bg:#FFFBEB;--success:#16A34A;--success-bg:#F0FDF4;--purple:#7C3AED;--purple-bg:#F5F3FF;--navy:#1B3468;--radius:10px;--radius-sm:6px;--font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;--shadow-sm:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);--shadow-md:0 4px 12px rgba(0,0,0,.08),0 2px 4px rgba(0,0,0,.04);--red:#DC2626;--red-hover:#b91c1c;--grey-bg:#F7F7F8;--grey-border:#E4E4E7;--grey-text:#71717A;--white:#FFFFFF}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{overflow-x:hidden}
body{font-family:var(--font);font-size:14px;background:var(--bg);color:var(--text);overflow-x:hidden}
/* Header */
.app-header{background:#fff;border-bottom:1px solid var(--border);padding:0;box-shadow:var(--shadow-sm)}
.container{max-width:1600px;margin:0 auto;padding:0 24px}
.header-inner{display:flex;align-items:center;justify-content:flex-start;height:52px;padding:0 20px;gap:12px}
.app-header h1{color:var(--text);font-size:15px;font-weight:700;letter-spacing:-.2px}
.header-sep{width:1px;height:20px;background:var(--border);flex-shrink:0}
.header-search-wrap{flex:1;max-width:340px;display:flex;align-items:center;gap:8px;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:0 10px;height:34px;color:var(--text-2)}
.header-search-wrap input{background:none;border:none;outline:none;font-size:13px;color:var(--text);font-family:var(--font);flex:1;min-width:0}
.header-search-wrap input::placeholder{color:var(--text-3)}
.header-search-kbd{font-size:10px;color:var(--text-3);background:var(--surface-2);border:1px solid var(--border);border-radius:4px;padding:1px 5px;white-space:nowrap;flex-shrink:0}
.header-spacer{flex:1}
.header-desc{color:var(--text-2);font-size:11px;margin-top:1px}
.header-actions{display:flex;gap:8px;align-items:center}
.header-desktop-only{display:inline-flex}
/* Tabs — skryté, navigace přes NavSidebar */
.tab-bar{}
.tab{display:none}
.tab.active{display:none}
/* Nav sidebar */
.nav-sidebar{background:#fff;border-right:1px solid var(--border);display:flex;flex-direction:column;align-items:center;padding:10px 0;gap:2px}
.nav-item{width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:var(--radius-sm);cursor:pointer;color:var(--text-2);transition:all .15s;border:none;background:none;font-size:19px;position:relative;font-family:var(--font)}
.nav-item:hover{background:var(--bg);color:var(--text)}
.nav-item.active{color:var(--accent);background:var(--accent-bg)}
.nav-item.active::before{content:'';position:absolute;left:-8px;top:50%;transform:translateY(-50%);width:3px;height:20px;background:var(--accent);border-radius:0 3px 3px 0}
.bottom-tabs{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#1B3468;border-radius:30px;display:flex;align-items:center;padding:5px;gap:2px;box-shadow:0 4px 20px rgba(0,0,0,.25);z-index:200}
.bottom-tab{padding:8px 18px;border-radius:24px;border:none;background:transparent;color:rgba(255,255,255,.65);font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font);transition:all .15s;white-space:nowrap}
.bottom-tab.active{background:#fff;color:#1B3468}
.bottom-tab:hover:not(.active){color:#fff}
.nav-sep{width:28px;height:1px;background:var(--border);margin:4px 0}
.nav-spacer{flex:1}
/* Layout */
.layout{display:grid;grid-template-columns:60px 1fr 280px;gap:0;margin-top:0;padding-bottom:0;align-items:stretch;height:calc(100vh - 52px);overflow:hidden}
.layout.onenon-mode{grid-template-columns:60px 1fr 280px}
/* Panel */
.panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px;box-shadow:var(--shadow-sm)}
.panel+.panel{margin-top:14px}
.section-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text-2);margin-bottom:12px;display:flex;align-items:center;justify-content:space-between}
/* Right sidebar */
.sidebar-right{border-left:1px solid var(--border);background:var(--surface);overflow-y:auto;display:flex;flex-direction:column;position:sticky;top:52px;max-height:calc(100vh - 52px)}
.sidebar-left{display:none!important}
#mainContent{overflow-y:auto;padding:20px}
.rs-panel{border-bottom:1px solid var(--border);padding:14px 16px}
.rs-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-2);display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.rs-title-action{font-weight:600;font-size:11px;color:var(--accent);cursor:pointer;text-transform:none;letter-spacing:0}
/* KPI */
.kpi-row{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.kpi-stat{font-size:20px;font-weight:800;color:var(--text)}
.kpi-label-sm{font-size:11px;color:var(--text-2);margin-top:1px}
.kpi-sep{width:1px;height:30px;background:var(--border);flex-shrink:0}
.progress-bar{height:5px;background:var(--border);border-radius:3px;overflow:hidden;margin-bottom:4px}
.progress-fill{height:100%;background:var(--success);border-radius:3px;transition:width .4s}
.progress-label{font-size:11px;color:var(--text-2)}
.progress-label span{color:var(--success);font-weight:700}
/* Eisenhower */
.matrix{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.quadrant{border-radius:var(--radius);padding:14px;min-height:160px;min-width:0;overflow:hidden;box-shadow:var(--shadow-sm)}
.quadrant.q-urgent_important{background:var(--danger-bg);border:1px solid #FECACA;border-top:3px solid var(--danger)}
.quadrant.q-important{background:var(--surface);border:1px solid var(--border);border-top:3px solid var(--accent)}
.quadrant.q-urgent{background:var(--warning-bg);border:1px solid #FDE68A;border-top:3px solid var(--warning)}
.quadrant.q-other{background:var(--surface-2);border:1px solid var(--border)}
.q-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.q-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-2);display:flex;align-items:center;gap:5px}
.quadrant.q-urgent_important .q-label{color:var(--danger);font-weight:800}
.quadrant.q-important .q-label{color:var(--accent);font-weight:700}
.quadrant.q-urgent .q-label{color:var(--warning);font-weight:700}
.quadrant.q-other .q-label{color:var(--text-3);font-weight:600}
.q-count{font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px}
.quadrant.q-urgent_important .q-count{background:#FEE2E2;color:var(--danger)}
.quadrant.q-important .q-count{background:var(--accent-bg);color:var(--accent)}
.quadrant.q-urgent .q-count{background:#FEF3C7;color:var(--warning)}
.quadrant.q-other .q-count{background:var(--surface-2);color:var(--text-2)}
.q-add-btn{background:none;border:none;cursor:pointer;color:var(--text-2);font-size:18px;line-height:1;padding:0 2px;font-weight:300}
.q-add-btn:hover{color:var(--text)}
.quadrant.q-urgent_important .task-card{background:#fff;border-color:#FECACA}
.quadrant.q-urgent_important .task-card:hover{border-color:#FCA5A5;box-shadow:0 4px 12px rgba(0,0,0,.10);transform:translateY(-1px)}
.quadrant.q-urgent .task-card{background:#fff;border-color:#FDE68A}
/* Task card */
.task-card{display:flex;align-items:flex-start;gap:8px;padding:8px 10px;background:var(--surface);border-radius:var(--radius-sm);margin-bottom:6px;border:1px solid var(--border);cursor:pointer;transition:all .15s;box-shadow:var(--shadow-sm);position:relative;overflow:hidden}
.task-card:hover{border-color:#CBD5E1;box-shadow:0 4px 12px rgba(0,0,0,.10);transform:translateY(-1px)}.task-card:hover .task-add-daily{opacity:1!important}.task-card:hover .task-del{opacity:1}
.tc-cb{width:15px;height:15px;border-radius:4px;border:2px solid var(--border);flex-shrink:0;margin-top:2px;cursor:pointer;transition:all .15s}
.quadrant.q-urgent_important .tc-cb{border-color:#FCA5A5}
.quadrant.q-important .tc-cb{border-color:#93C5FD}
.quadrant.q-urgent .tc-cb{border-color:#FCD34D}
.tc-body{flex:1;min-width:0}
.tc-title{font-size:13px;font-weight:500;line-height:1.4;word-break:break-word}
.tc-title.done-text{text-decoration:line-through;color:var(--text-3)}
.tc-meta{font-size:11px;color:var(--text-2);margin-top:3px;display:flex;gap:6px;align-items:center;flex-wrap:wrap}
.tc-daktela{color:var(--text-3);text-decoration:none;font-size:11px}
.tc-daktela:hover{color:var(--accent);text-decoration:underline}
.tc-age{color:var(--warning);font-weight:600}
.tc-age.old{color:var(--danger)}
.tc-due{font-size:11px;font-weight:600;color:var(--text-3)}
.tc-due.overdue{color:var(--danger)}
.tc-due.soon{color:var(--warning)}
.badge{font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;white-space:nowrap}
.badge-daktela{background:#FFF4E0;color:#A06000}
.badge-ai{background:#E8F0FE;color:#1a56db}
.task-del{background:none;border:none;cursor:pointer;color:var(--grey-border);font-size:14px;padding:0;flex-shrink:0;margin-top:1px;opacity:0.25;transition:opacity .15s}
.task-del:hover{color:#E63327;opacity:1}
/* Add task inline */
.add-inline{display:flex;gap:6px;margin-top:8px}
.add-inline input{flex:1;height:28px;padding:0 8px;border:1px dashed var(--border);border-radius:var(--radius-sm);font-size:12px;font-family:var(--font);outline:none;background:transparent;color:var(--text)}
.add-inline input:focus{border-color:var(--accent);border-style:solid;background:var(--surface)}
.add-inline input::placeholder{color:var(--text-3)}
.add-inline button{display:none}
/* Checklist */
.cl-item{display:flex;align-items:center;gap:8px;padding:6px 2px;border-bottom:1px solid var(--grey-border)}
.cl-item:last-child{border-bottom:none}
.cl-item input[type=checkbox]{accent-color:var(--blue);width:15px;height:15px;flex-shrink:0;cursor:pointer}
.cl-item-title{font-size:13px;flex:1;min-width:0}
.cl-item-title.done{text-decoration:line-through;color:var(--grey-text)}
.cl-del{background:none;border:none;cursor:pointer;color:var(--grey-border);font-size:13px;flex-shrink:0;padding:0}
.cl-del:hover{color:#E63327}
.cl-add-row{display:flex;gap:6px;margin-top:10px}
.cl-add-row input{flex:1;height:30px;padding:0 8px;border:1px solid var(--grey-border);border-radius:6px;font-size:13px;font-family:var(--font);outline:none}
.cl-add-row input:focus{border-color:var(--navy)}
.cl-add-row button{height:30px;padding:0 14px;background:var(--blue);color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:700;cursor:pointer}
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
.btn-primary{background:var(--blue);color:#fff}.btn-primary:hover{background:var(--blue-hover)}
.btn-secondary{background:var(--white);color:var(--navy);border:1px solid var(--grey-border)}.btn-secondary:hover{background:var(--grey-bg)}
.btn-ghost{background:var(--surface);color:var(--text);border:1px solid var(--border)}.btn-ghost:hover{background:var(--bg)}
.action-row{display:flex;gap:10px;margin-top:14px;flex-wrap:wrap}
/* Historia */
.history-group{margin-bottom:16px}
.history-group-title{font-size:11px;font-weight:700;color:var(--grey-text);text-transform:uppercase;letter-spacing:.5px;padding:8px 0 6px;border-bottom:1px solid var(--grey-border);margin-bottom:8px;display:flex;align-items:center;justify-content:space-between}
.history-task{display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:6px;color:var(--grey-text);font-size:13px}
.history-task:hover{background:var(--grey-bg)}
.history-time{font-size:11px;color:var(--grey-text);margin-left:auto;white-space:nowrap}
/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px}
.modal{background:var(--white);border-radius:12px;padding:28px;width:100%;max-width:520px;box-shadow:0 8px 40px rgba(0,0,0,.2);max-height:90vh;overflow-y:auto}.modal-box{background:var(--white);border-radius:12px;width:100%;box-shadow:0 8px 40px rgba(0,0,0,.2);max-height:90vh;overflow:hidden;display:flex;flex-direction:column}
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
.fab{display:none;position:fixed;bottom:20px;right:20px;width:52px;height:52px;border-radius:50%;background:var(--blue);color:#fff;font-size:24px;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.25);z-index:100;align-items:center;justify-content:center;font-family:var(--font)}
/* Sidebar hamburger (tablet) */
.sidebar-toggle{display:none;background:var(--bg);border:1px solid var(--border);color:var(--text);font-size:20px;cursor:pointer;padding:6px 10px;border-radius:6px;font-family:var(--font)}
.sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:149}
.sidebar-backdrop.active{display:block}
.sidebar-mobile-panels{display:none}
/* Loading */
.loading-overlay{position:fixed;inset:0;background:rgba(255,255,255,.6);z-index:300;display:flex;align-items:center;justify-content:center}
.spinner{width:32px;height:32px;border:3px solid var(--grey-border);border-top-color:var(--navy);border-radius:50%;animation:spin .7s linear infinite}input:focus-visible,button:focus-visible,textarea:focus-visible,select:focus-visible{outline:2px solid var(--accent);outline-offset:2px}.btn:disabled{opacity:0.55;cursor:not-allowed}
input[type=search]::placeholder{color:var(--text-3)}
input[type=search]::-webkit-search-cancel-button{opacity:.4;cursor:pointer}
/* Overdue */
.task-card.overdue{border-color:#E63327;background:#FFF5F5}
.task-card.overdue .task-title{color:#c0392b}
.overdue-badge{font-size:10px;font-weight:700;color:#E63327;background:#FEE8E7;padding:1px 6px;border-radius:4px}
/* Drag & drop */
.task-card.dragging{opacity:.45;transform:scale(1.02);box-shadow:0 8px 24px rgba(0,0,0,.15)}
.quadrant.drag-over{background:#EBF0FF;border:2px dashed var(--accent)!important}
/* Inline edit */
.task-title-input{font-size:13px;font-weight:500;width:100%;border:none;border-bottom:1px solid var(--navy);background:transparent;outline:none;font-family:var(--font);padding:0;color:var(--navy)}
/* Q1 alert badge */
.q1-alert{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#E63327;color:#fff;font-size:10px;font-weight:700;border-radius:50%;margin-left:6px;animation:pulse 1.5s ease-in-out infinite;cursor:pointer;position:relative;flex-shrink:0}
.q1-popover{position:fixed;background:#fff;border:1px solid var(--grey-border);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);min-width:260px;max-width:340px;z-index:9999;overflow:hidden}
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
/* Stale task */
.task-card.stale-mid{border-left:3px solid var(--warning) !important;background:#FFFBEB}
.task-card.stale-old{border-left:3px solid var(--danger) !important;background:#FFF5F5}
.stale-age{font-size:10px;font-weight:600;padding:1px 5px;border-radius:4px;color:var(--warning)}
.stale-age.warn{color:var(--danger)}
/* Task description */
.task-desc{font-size:11px;color:var(--grey-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;margin-top:2px}.task-desc-link{color:var(--primary);text-decoration:none}.task-desc-link:hover{text-decoration:underline}
/* Dnes view */
.dnes-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.dnes-title{font-size:20px;font-weight:700;color:var(--text)}
.dnes-meta{font-size:13px;color:var(--text-2);margin-top:2px}
.dnes-free{color:var(--success);font-weight:600}
/* Timeline */
.timeline-section{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);margin-bottom:16px;box-shadow:var(--shadow-sm);overflow:hidden}
.timeline-header{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--border);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2)}
.timeline-body{padding:8px 0}
.timeline-row{display:grid;grid-template-columns:44px 1fr;align-items:flex-start;padding:0 14px;min-height:32px}
.t-time{font-size:11px;color:var(--text-3);font-weight:600;padding-top:8px}
.t-slot{border-top:1px solid var(--border);padding:6px 0;min-height:32px}
.cal-block{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:5px;font-size:12px;font-weight:600;margin-bottom:3px}
.cal-block.work{background:var(--accent-bg);color:var(--accent);border-left:3px solid var(--accent)}
.cal-block.all-day{background:var(--purple-bg);color:var(--purple);border-left:3px solid var(--purple)}
.cal-duration{font-size:10px;font-weight:400;opacity:.7}
/* Denní plán karta */
.dnes-plan{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:14px}
.dnes-plan-header{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border)}
.dnes-plan-title{font-size:13px;font-weight:700;color:var(--text)}
.dnes-plan-meta{font-size:12px;color:var(--text-2)}
.dnes-task-row{display:flex;align-items:center;gap:10px;padding:10px 16px;border-bottom:1px solid var(--border);transition:background .1s;cursor:grab}
.dnes-task-row:last-of-type{border-bottom:none}
.dnes-task-row:hover{background:var(--bg)}
.dnes-idx{font-size:11px;color:var(--text-3);font-weight:600;width:16px;flex-shrink:0}
.dnes-cb{width:17px;height:17px;border-radius:5px;border:2px solid var(--border);flex-shrink:0;transition:all .15s;cursor:pointer}
.dnes-cb.q1{border-color:var(--danger)}
.dnes-cb.q2{border-color:var(--accent)}
.dnes-cb.q3{border-color:var(--warning)}
.dnes-task-name{flex:1;font-size:13px;font-weight:500;color:var(--text)}
.dnes-due{font-size:11px;font-weight:600;padding:2px 7px;border-radius:4px;flex-shrink:0}
.dnes-due.overdue{background:#FEE2E2;color:var(--danger)}
.dnes-due.soon{background:var(--warning-bg);color:var(--warning)}
.dnes-due.normal{color:var(--text-3);background:none}
.dnes-remove{background:none;border:none;cursor:pointer;color:var(--text-3);font-size:16px;padding:0 2px;opacity:0;transition:opacity .15s}
.dnes-task-row:hover .dnes-remove{opacity:1}
/* Morning ritual */
.morning-ritual{background:var(--surface);border-radius:16px;padding:22px;box-shadow:var(--shadow-md);border:1px solid var(--border)}
.morning-title{font-size:18px;font-weight:800;margin-bottom:3px;color:var(--text)}
.morning-sub{font-size:12px;color:var(--text-2);margin-bottom:14px}
.morning-stats{display:flex;gap:10px;margin-bottom:14px}
.morning-stat{background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:7px 11px;font-size:11px;text-align:center;color:var(--text-2)}
.morning-stat-val{font-size:17px;font-weight:800;line-height:1.2;color:var(--text)}
.morning-tasks-list{background:var(--bg);border-radius:var(--radius-sm);padding:10px;margin-bottom:14px;border:1px solid var(--border)}
.morning-task-row{display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid var(--border);font-size:13px;cursor:pointer;user-select:none}
.morning-task-row:last-child{border-bottom:none;padding-bottom:0}
.morning-cb{width:18px;height:18px;border:2px solid var(--border);border-radius:5px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;font-size:11px;font-weight:700;color:#fff}
.morning-cb.on{background:var(--success);border-color:var(--success)}
.morning-task-name{flex:1;color:var(--text)}
.morning-task-q{font-size:10px;color:var(--text-3);font-weight:600}
.morning-btns{display:flex;gap:8px;margin-top:6px;padding:14px 22px;background:var(--bg);border-top:1px solid var(--border);margin-left:-22px;margin-right:-22px;margin-bottom:-22px}
.btn-morning-skip{background:var(--surface);color:var(--text-2);border:1px solid var(--border);padding:9px 16px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;font-family:var(--font)}
.btn-morning-go{background:var(--accent);color:#fff;border:none;padding:9px 20px;border-radius:var(--radius-sm);font-size:13px;font-weight:700;cursor:pointer;flex:1;font-family:var(--font)}
/* What Now bar */
.whatnow-bar{display:flex;align-items:center;gap:10px;padding:12px 16px;background:#FAFAFA;border:1px solid var(--border);border-radius:var(--radius);cursor:pointer;transition:all .15s}
.whatnow-bar:hover{border-color:#7C3AED;background:var(--purple-bg)}
.whatnow-spark{font-size:18px;flex-shrink:0}
.whatnow-text-head{font-size:13px;font-weight:600;color:var(--text)}
.whatnow-sub{font-size:12px;color:var(--text-2)}
.whatnow-result-inline{margin-top:10px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:13px 15px;box-shadow:var(--shadow-sm)}
.whatnow-text{font-size:13px;color:var(--text);line-height:1.6;margin-bottom:10px}
.whatnow-task{display:flex;align-items:center;gap:8px;background:var(--accent-bg);border:1px solid #BFDBFE;border-radius:var(--radius-sm);padding:9px 12px;font-size:13px;font-weight:600;color:var(--accent);cursor:pointer}
.whatnow-task:hover{background:#DBEAFE}
.whatnow-dismiss{font-size:11px;color:var(--text-3);cursor:pointer;text-align:right;margin-top:7px}
/* 1on1 Prep */
.prep-modal-body{padding:0;flex:1;overflow-y:auto;max-height:60vh}
.prep-header{background:var(--navy);color:#fff;padding:14px 18px;border-radius:12px 12px 0 0}
.prep-person-name{font-size:17px;font-weight:800;margin-bottom:2px}
.prep-chips{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.prep-chip{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;border:1px solid rgba(255,255,255,.3);color:rgba(255,255,255,.9);background:rgba(255,255,255,.12)}
.prep-date-line{font-size:12px;opacity:.65}
.prep-section{padding:12px 18px;border-bottom:1px solid var(--grey-bg)}
.prep-section:last-child{border-bottom:none}
.prep-section-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--grey-text);margin-bottom:7px}
.prep-ai-item{display:flex;align-items:flex-start;gap:8px;padding:5px 0;border-bottom:1px solid var(--grey-bg);font-size:13px}
.prep-ai-item:last-child{border-bottom:none}
.prep-ai-cb{width:14px;height:14px;border:2px solid #ccc;border-radius:3px;flex-shrink:0;margin-top:1px}
.prep-ai-cb.done{background:var(--green);border-color:var(--green)}
.prep-ai-done{text-decoration:line-through;color:#bbb}
.prep-ai-from{font-size:10px;color:#bbb;margin-left:auto;flex-shrink:0}
.prep-topic{display:flex;gap:7px;font-size:13px;padding:5px 0;color:#333}
.prep-topic-dot{color:var(--orange);font-size:15px;line-height:1.1;flex-shrink:0}
.prep-footer{display:flex;gap:8px;padding:10px 18px;background:var(--grey-bg);border-radius:0 0 12px 12px}
/* Toast */
/* 1on1 layout */
.onenon-layout{display:grid;grid-template-columns:260px 1fr;height:100%;overflow:hidden;gap:0}
.onenon-people-sidebar{border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden;background:var(--surface);position:relative}
.layout.onenon-active{grid-template-columns:60px 1fr!important}
.layout.onenon-active .sidebar-right{display:none!important}
.onenon-people-header{padding:14px 14px 10px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.onenon-people-list{flex:1;overflow-y:auto;padding:8px}
.onenon-main{flex:1;min-width:0;overflow-y:auto;padding:16px 20px 80px}
/* Person card */
.onenon-person-row{margin-bottom:4px}
.onenon-person-item-btn{width:100%;background:none;border:1px solid transparent;border-radius:var(--radius-sm);padding:8px 10px;cursor:pointer;text-align:left;font-family:var(--font);transition:all .15s}
.onenon-person-item-btn:hover{background:var(--bg);border-color:var(--border)}
.onenon-person-item-btn.active{background:var(--purple-bg);border-color:#C4B5FD}
.onenon-person-edit-btn{background:none;border:none;cursor:pointer;font-size:12px;color:var(--text-3);padding:4px 6px;border-radius:4px;opacity:0;flex-shrink:0}
.onenon-person-row:hover .onenon-person-edit-btn{opacity:1}
.onenon-person-edit-form{background:var(--bg);border-radius:var(--radius-sm);padding:8px;margin-bottom:6px;border:1px solid var(--border)}
.onenon-person-edit-form input{width:100%;margin-bottom:4px;font-size:12px;padding:5px 8px;border:1px solid var(--border);border-radius:var(--radius-sm);box-sizing:border-box;background:var(--surface);color:var(--text);font-family:var(--font)}
.onenon-person-edit-form textarea{width:100%;font-size:12px;padding:5px 8px;border:1px solid var(--border);border-radius:var(--radius-sm);box-sizing:border-box;resize:vertical;background:var(--surface);color:var(--text);font-family:inherit}
.onenon-person-desc{font-size:13px;color:var(--text-2);margin-bottom:14px;line-height:1.5;white-space:pre-wrap;font-style:italic}
/* Person header + health signals */
.onenon-person-header{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px;margin-bottom:14px;box-shadow:var(--shadow-sm)}
.onenon-signal-chips{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
.signal-chip{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;border:1px solid}
.signal-chip.ok{background:var(--success-bg);color:var(--success);border-color:#BBF7D0}
.signal-chip.warn{background:var(--warning-bg);color:var(--warning);border-color:#FDE68A}
.signal-chip.info{background:var(--accent-bg);color:var(--accent);border-color:#BFDBFE}
.signal-chip.purple{background:var(--purple-bg);color:var(--purple);border-color:#DDD6FE}
/* Note cards */
.onenon-note-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:14px;margin-bottom:10px;box-shadow:var(--shadow-sm)}
.onenon-note-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px}
.onenon-note-date{font-weight:700;color:var(--text);font-size:13px}
.onenon-note-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.onenon-note-actions{display:flex;gap:4px}
.onenon-mood{color:#F5A623;font-size:13px;letter-spacing:1px}
.onenon-tag-chip{display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin:2px 3px 2px 0;background:var(--accent-bg);color:var(--accent)}.onenon-tag-chip.tag-vykon{background:#DCFCE7;color:var(--success)}.onenon-tag-chip.tag-rozvoj{background:#F5F3FF;color:var(--purple)}.onenon-tag-chip.tag-osobni{background:#FEF3C7;color:var(--warning)}.onenon-tag-chip.tag-sla{background:#FEE2E2;color:var(--danger)}.onenon-tag-chip.tag-feedback{background:var(--accent-bg);color:var(--accent)}
.onenon-action-item{display:flex;align-items:center;gap:8px;padding:4px 0;cursor:pointer;font-size:13px}
.onenon-action-check{width:14px;height:14px;border-radius:3px;border:2px solid var(--accent);background:none;display:inline-block;flex-shrink:0}
.onenon-action-check.done{border-color:var(--border);background:var(--bg)}
.onenon-action-text{color:var(--text)}
.onenon-action-text.done{color:var(--text-3);text-decoration:line-through}
.onenon-mood-btn{background:none;border:none;cursor:pointer;font-size:20px;padding:2px;opacity:.35;transition:opacity .1s}
.onenon-mood-btn.active,.onenon-mood-btn:hover{opacity:1}
.onenon-tag-toggle{font-size:11px;padding:3px 10px;border-radius:10px;border:1px solid var(--border);background:none;cursor:pointer;color:var(--text-2);font-weight:600;font-family:var(--font)}
.onenon-tag-toggle.active{background:var(--accent-bg);border-color:#BFDBFE;color:var(--accent)}
.onenon-ai-row{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px}
/* Action items panel */
.onenon-open-items{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:14px;margin-bottom:14px;box-shadow:var(--shadow-sm)}
.onenon-open-item-row{display:flex;align-items:flex-start;gap:8px;padding:5px 0;border-bottom:1px solid var(--border);font-size:12px;cursor:pointer}
.onenon-open-item-row:last-child{border-bottom:none}
.onenon-open-item-from{font-size:10px;color:var(--text-3);margin-left:auto;flex-shrink:0}
/* AI badge */
.onenon-ai-badge{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;background:var(--danger);color:#fff;border-radius:10px;font-size:11px;font-weight:700;cursor:pointer}
.onenon-ai-popover{position:absolute;top:28px;right:0;min-width:280px;max-width:360px;max-height:380px;overflow-y:auto;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-md);z-index:1000;padding:6px 0}
.onenon-ai-group{border-bottom:1px solid var(--border)}
.onenon-ai-group:last-child{border-bottom:none}
.onenon-ai-group-header{display:flex;align-items:center;justify-content:space-between;padding:6px 14px;font-weight:700;font-size:13px;color:var(--text);cursor:pointer;background:var(--bg)}
.onenon-ai-group-header:hover{background:var(--surface-2)}
.onenon-ai-count{background:var(--danger);color:#fff;border-radius:10px;padding:1px 6px;font-size:11px}
.onenon-ai-item{display:flex;align-items:flex-start;padding:5px 14px 5px 24px;font-size:13px;color:var(--text);cursor:pointer;gap:6px}
.onenon-ai-item:hover{background:var(--bg)}
.onenon-ai-dot{width:6px;height:6px;border-radius:50%;background:var(--danger);flex-shrink:0;margin-top:5px}
.onenon-warn{color:var(--danger);font-weight:700}
.onenon-dashboard{background:var(--warning-bg);border:1px solid #FDE68A;border-radius:var(--radius-sm);padding:10px 12px;margin-bottom:10px;font-size:12px}
.onenon-dashboard-row{display:flex;justify-content:space-between;align-items:center;gap:8px}
.onenon-person-warn{width:7px;height:7px;border-radius:50%;background:var(--warning);flex-shrink:0}
.onenon-alert-banner{border-radius:var(--radius-sm);padding:7px 10px;font-size:12px;font-weight:600;margin:6px 8px 0;display:flex;align-items:center;gap:6px}
.onenon-alert-banner.red{background:#FEE2E2;color:var(--danger);border:1px solid #FECACA}
.onenon-alert-banner.orange{background:var(--warning-bg);color:var(--warning);border:1px solid #FDE68A}
.onenon-person-trend{font-size:13px;font-weight:700;flex-shrink:0;width:16px;text-align:center}
.onenon-person-trend.up{color:var(--success)}
.onenon-person-trend.down{color:var(--danger)}
.onenon-person-trend.flat{color:var(--text-3)}
.onenon-metrics-row{background:#FAFAFA;border:1px solid var(--border);border-radius:var(--radius);padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;gap:0;overflow:hidden}
.onenon-metric{display:flex;flex-direction:column;gap:3px;padding-right:20px;border-right:1px solid var(--border);margin-right:20px}
.onenon-metric:last-of-type{border-right:none;margin-right:0;padding-right:0}
.onenon-metric-label{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-3);font-weight:700}
.onenon-metric-val{font-size:13px;font-weight:600;color:var(--text)}
.onenon-metrics-collapse{margin-left:auto;font-size:11px;color:var(--text-3);cursor:pointer;white-space:nowrap;flex-shrink:0}
.onenon-ai-item-source{font-size:10px;color:var(--text-3);margin-top:2px}
.onenon-splneno-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:12px}
.onenon-splneno-header{display:flex;align-items:center;justify-content:space-between;padding:8px 14px;background:#F0FDF4;border-bottom:1px solid #BBF7D0;font-size:12px;font-weight:700;color:var(--success)}
.onenon-splneno-item{font-size:12px;color:var(--text-3);text-decoration:line-through;padding:4px 14px;border-bottom:1px solid var(--border)}
.onenon-splneno-item:last-child{border-bottom:none}
.onenon-bar-date{font-size:9px;color:var(--text-3);margin-top:2px;text-align:center;white-space:nowrap}
.onenon-bottom-tabs{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#1B3468;border-radius:30px;display:flex;align-items:center;padding:5px;gap:2px;box-shadow:0 4px 20px rgba(0,0,0,.25);z-index:201}
.onenon-btab{padding:8px 18px;border-radius:24px;border:none;background:transparent;color:rgba(255,255,255,.65);font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font);transition:all .15s;white-space:nowrap}
.onenon-btab.active{background:#fff;color:#1B3468}
.onenon-btab:hover:not(.active){color:#fff}
.onenon-note-actions{display:flex;gap:4px;align-items:center}
.onenon-note-link{background:none;border:none;cursor:pointer;font-size:12px;color:var(--text-3);padding:2px 6px;border-radius:4px;font-family:var(--font)}
.onenon-note-link:hover{color:var(--danger)}
.onenon-note-link.edit:hover{color:var(--accent)}
.onenon-sidebar-footer{border-top:1px solid var(--border);padding:10px 12px;background:var(--surface);flex-shrink:0}
.onenon-footer-btn{width:100%;padding:8px;border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:12px;font-weight:600;cursor:pointer;font-family:var(--font);transition:all .15s}
.onenon-footer-btn:hover{border-color:var(--accent);color:var(--accent)}
.onenon-cols{display:grid;grid-template-columns:1fr 1.6fr;gap:16px;align-items:flex-start}
.onenon-col-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow:hidden;margin-bottom:12px}
.onenon-col-card-header{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--border);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:var(--text-2)}
.onenon-ai-group-section{padding:10px 14px 0}
.onenon-ai-group-label{font-size:11px;font-weight:700;color:var(--text-2);margin-bottom:6px;display:flex;align-items:center;justify-content:space-between}
.onenon-ai-done-label{font-size:11px;font-weight:700;color:var(--success);margin:8px 0 4px;display:flex;align-items:center;gap:4px}
.onenon-ai-done-item{font-size:12px;color:var(--text-3);text-decoration:line-through;padding:3px 0}
.onenon-mood-chart{padding:12px 14px}
.onenon-mood-trend-label{font-size:11px;font-weight:700;color:var(--text-2);margin-bottom:6px;display:flex;align-items:center;justify-content:space-between}
.onenon-bars{display:flex;gap:8px;align-items:flex-end;height:44px;margin-top:4px}
.onenon-bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px}
.onenon-bar{width:100%;border-radius:3px 3px 0 0;min-height:4px;transition:height .2s}
.onenon-bar-label{font-size:9px;color:var(--text-3);white-space:nowrap}
.onenon-person-stars{font-size:11px;color:#F59E0B;letter-spacing:.5px}
.onenon-trend{font-size:13px;font-weight:700;flex-shrink:0}
.onenon-trend.up{color:var(--success)}
.onenon-trend.down{color:var(--danger)}
.onenon-note-tag-row{display:flex;gap:6px;align-items:center;flex-wrap:wrap;margin-bottom:6px}
.sr-section-label{font-size:11px;font-weight:700;color:var(--grey-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.sr-item{display:flex;align-items:center;gap:8px;padding:7px 8px;border-radius:6px;cursor:pointer;margin-bottom:4px;border:1px solid var(--grey-border);background:var(--grey-bg)}
.sr-task-title{font-size:13px;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--navy)}
.sr-task-title.done{text-decoration:line-through;color:var(--grey-text)}
.sr-quadrant-badge{font-size:10px;font-weight:700;padding:2px 7px;border-radius:4px;white-space:nowrap;flex-shrink:0}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:var(--navy);color:#fff;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;z-index:400;pointer-events:none;opacity:0;transition:opacity .2s}
.toast.show{opacity:1}
/* Responsive */
@media(max-width:1100px){
  .layout{grid-template-columns:60px 1fr;height:100vh}
  .sidebar-right{display:none}
}
@media(max-width:900px){
  .layout{grid-template-columns:60px 1fr;height:100vh}
  .sidebar-right{display:none}
  .fab{display:flex}
  .header-desktop-only{display:none}
  .header-actions input[type=search]{width:120px !important}
  .onenon-layout{grid-template-columns:1fr}
  .onenon-people-sidebar{max-height:200px}
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
  <div class="header-inner">
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
      <svg width="28" height="28" viewBox="0 0 32 32" style="flex-shrink:0;border-radius:7px">
        <rect width="32" height="32" rx="6" fill="#1B3468"/>
        <rect x="6" y="6" width="9" height="9" rx="2" fill="#E05C4E"/>
        <rect x="17" y="6" width="9" height="9" rx="2" fill="rgba(255,255,255,0.45)"/>
        <rect x="6" y="17" width="9" height="9" rx="2" fill="rgba(255,255,255,0.45)"/>
        <rect x="17" y="17" width="9" height="9" rx="2" fill="rgba(255,255,255,0.18)"/>
      </svg>
      <h1>Tasks</h1>
    </div>
    <div class="header-sep"></div>
    <div class="header-search-wrap" id="headerSearch">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
    </div>
    <div class="header-spacer"></div>
    <div class="header-actions" id="headerActions"></div>
  </div>
  <div class="tab-bar" id="tabBar"></div>
</header>

<main>
  <div id="app-root" style="position:absolute;width:0;height:0;overflow:visible"></div>
  <div class="layout" id="layout">
    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>
    <nav class="nav-sidebar" id="navSidebar"></nav>
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
  { key: 'urgent_important', label: 'Urgentní + Důležité' },
  { key: 'important',        label: 'Důležité' },
  { key: 'urgent',           label: 'Urgentní' },
  { key: 'other',            label: 'Backlog' },
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

// ---- NavSidebar ----
function NavSidebar({ activeTab, onTab }) {
  const isMatrix = ['all','work','personal'].includes(activeTab);
  const active = (key) => key === activeTab || (key === 'all' && isMatrix);
  return (
    <nav style={{display:'flex',flexDirection:'column',alignItems:'center',padding:'12px 0',gap:'4px',height:'100%'}}>
      <button className={'nav-item' + (active('dnes') ? ' active' : '')} onClick={() => onTab('dnes')} title="Dnes">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
      </button>
      <button className={'nav-item' + (active('all') ? ' active' : '')} onClick={() => onTab('all')} title="Matice">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      </button>
      <button className={'nav-item' + (active('history') ? ' active' : '')} onClick={() => onTab('history')} title="Historie">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </button>
      <button className={'nav-item' + (active('onenon') ? ' active' : '')} onClick={() => onTab('onenon')} title="1on1">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </button>
      <div className="nav-sep" />
      <div className="nav-spacer" />
      <button className="nav-item" title="AI Chat" style={{color:'#7C3AED'}} onClick={() => onTab('chat')}>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      </button>
      <button className="nav-item" title="Rychlé přidání (Cmd+K)" onClick={() => onTab('quickcapture')}>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      </button>
      <button className="nav-item" title="Nastavení" onClick={() => onTab('settings')}>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
      </button>
      <button className="nav-item" title="Odhlásit" onClick={() => onTab('logout')}>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>
    </nav>
  );
}

// ---- DoneTimeModal ----
function DoneTimeModal({ task, onSave, onSkip }) {
  const [minutes, setMinutes] = React.useState('');
  function handleSave() {
    const m = parseInt(minutes);
    onSave(m > 0 ? m : null);
  }
  return (
    <div className="modal-overlay" onClick={onSkip}>
      <div className="modal" style={{maxWidth:340}} onClick={e => e.stopPropagation()}>
        <div style={{fontWeight:700,fontSize:'15px',marginBottom:'6px'}}>Hotovo!</div>
        <div style={{fontSize:'12px',color:'var(--grey-text)',marginBottom:'16px'}}>
          Za jak dlouho jsi to udělal?
          {task.estimated_minutes && <span style={{marginLeft:6,color:'var(--blue)'}}>Odhad byl {task.estimated_minutes} min</span>}
        </div>
        <div style={{display:'flex',gap:'8px',alignItems:'center',marginBottom:'16px'}}>
          <input
            type="number" min="1" max="600" value={minutes} onChange={e => setMinutes(e.target.value)}
            placeholder="minut"
            style={{width:'90px',fontSize:'13px',padding:'6px 8px',border:'1px solid var(--grey-border)',borderRadius:'6px'}}
            autoFocus
            onKeyDown={e => e.key === 'Enter' && handleSave()}
          />
          <span style={{fontSize:'12px',color:'var(--grey-text)'}}>minut</span>
        </div>
        <div style={{display:'flex',gap:'8px',justifyContent:'flex-end'}}>
          <button onClick={onSkip} style={{background:'none',border:'1px solid var(--grey-border)',borderRadius:'6px',padding:'6px 14px',fontSize:'12px',cursor:'pointer',color:'var(--grey-text)'}}>Přeskočit</button>
          <button onClick={handleSave} style={{background:'var(--blue)',color:'#fff',border:'none',borderRadius:'6px',padding:'6px 14px',fontSize:'12px',cursor:'pointer',fontWeight:600}}>Uložit</button>
        </div>
      </div>
    </div>
  );
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
  const [estimatedMinutes, setEstimatedMinutes] = useState(initial.estimated_minutes || '');
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
      await onSave({ title, description, ai_context: aiContext, quadrant, type, due_date: dueDate, daktela_tickets: daktelaTickets, recurrence, recurrence_day: recurrenceDay, recurrence_interval: recurrenceInterval, recurrence_unit: recurrenceUnit, estimated_minutes: estimatedMinutes ? parseInt(estimatedMinutes) : null });
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
        <div className="form-group" style={{display:'flex',gap:'12px'}}>
          <div style={{flex:'2'}}>
            <label>{recurrence !== 'none' ? 'Termín / start opakování' : 'Termín'}</label>
            <input type="date" value={dueDate} onChange={e => setDueDate(e.target.value)} />
          </div>
          <div style={{flex:'1'}}>
            <label>Odhad (min)</label>
            <input type="number" min="1" max="480" value={estimatedMinutes} onChange={e => setEstimatedMinutes(e.target.value)} placeholder="?" style={{width:'100%'}} />
          </div>
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
                <input type="checkbox" checked={s.accepted} onChange={() => toggleAccept(i)} style={{accentColor:'var(--blue)',width:15,height:15}} />
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
function linkifyText(text) {
  const urlRe = /(https?:\/\/[^\s]+)/g;
  const parts = text.split(urlRe);
  return parts.map((part, i) => {
    if (urlRe.test(part)) {
      const display = part.length > 50 ? part.slice(0, 47) + '...' : part;
      return React.createElement('a', {key:i, href:part, target:'_blank', rel:'noreferrer', onClick:e=>e.stopPropagation(), className:'task-desc-link'}, display);
    }
    return part;
  });
}
function TaskCard({ task, onToggleDone, onEdit, onDelete, onInlineEdit, onDragStart, onAddToDaily }) {
  const tickets = task.daktela_tickets || [];
  const [editing, setEditing] = useState(false);
  const [editVal, setEditVal] = useState(task.title);
  const inputRef = useRef(null);

  const today = new Date().toISOString().split('T')[0];
  const isOverdue = task.status === 'open' && task.due_date && task.due_date < today;
  const daysUntil = task.due_date ? Math.ceil((new Date(task.due_date) - new Date(today)) / 86400000) : null;
  const isSoon = !isOverdue && task.status === 'open' && daysUntil !== null && daysUntil <= 3;
  const createdAt = task.created_at || task.updated_at || null;
  const daysOld = createdAt ? Math.floor((Date.now() - new Date(createdAt).getTime()) / 86400000) : 0;
  const isStaleOld = task.status === 'open' && daysOld >= 21;
  const isStaleMid = task.status === 'open' && !isStaleOld && daysOld >= 7;

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

  const qSuffix = task.quadrant === 'urgent_important' ? 'q1' : task.quadrant === 'important' ? 'q2' : task.quadrant === 'urgent' ? 'q3' : '';
  const cardClass = 'task-card' + (isOverdue ? ' overdue' : '') + (isStaleOld ? ' stale-old' : isStaleMid ? ' stale-mid' : '');
  const isTouch = typeof window !== 'undefined' && ('ontouchstart' in window);

  return (
    <div
      className={cardClass}
      onClick={() => !editing && onEdit(task)}
      draggable={!isTouch}
      onDragStart={e => { if (isTouch) return; e.dataTransfer.setData('taskId', task.id); e.currentTarget.classList.add('dragging'); if (onDragStart) onDragStart(task); }}
      onDragEnd={e => { if (isTouch) return; e.currentTarget.classList.remove('dragging'); }}
    >
      <div
        className={'tc-cb' + (qSuffix ? ' ' + qSuffix : '')}
        onClick={e => { e.stopPropagation(); onToggleDone(task); }}
      />
      <div className="tc-body">
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
              className={'tc-title' + (task.status === 'done' ? ' done-text' : '')}
              onDoubleClick={startEdit}
              title="Dvojklik = rychlá editace názvu"
            >{task.title}</div>
        }
        <div className="tc-meta">
          {tickets.map(name => (
            <a key={name} href={'https://daktela.daktela.com/tickets/update/' + name} target="_blank" rel="noreferrer" onClick={e => e.stopPropagation()} className="tc-daktela">{name}</a>
          ))}
          {(isStaleMid || isStaleOld) && <span className={'tc-age' + (isStaleOld ? ' old' : '')}>{daysOld} dní</span>}
          {task.due_date && (
            <span className={'tc-due' + (isOverdue ? ' overdue' : isSoon ? ' soon' : '')} style={{marginLeft:'auto'}}>
              {isOverdue ? 'po ' + task.due_date : task.due_date}
            </span>
          )}
        </div>
      </div>
      <button className="task-del" onClick={e => { e.stopPropagation(); onDelete(task); }} title="Smazat">×</button>
      {onAddToDaily && (
        <button className="task-del task-add-daily" title="Přidat do Dnes" onClick={e => { e.stopPropagation(); onAddToDaily(task); }} style={{color:'var(--navy)',fontSize:'11px',fontWeight:700,marginLeft:'-2px',opacity:0,transition:'opacity .15s'}}>+D</button>
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
        <div className="q-label">{q.label} <span className="q-count">{visible.length}</span></div>
      </div>
      {visible.map(t => (
        <TaskCard key={t.id} task={t} onToggleDone={onToggleDone} onEdit={onEdit} onDelete={onDelete} onInlineEdit={onInlineEdit} onAddToDaily={onAddToDaily} />
      ))}
      <div className="add-inline">
        <input
          value={addTitle}
          onChange={e => setAddTitle(e.target.value)}
          placeholder={'Přidat ' + (q.key === 'urgent_important' ? 'urgentní task…' : q.key === 'important' ? 'důležitý task…' : q.key === 'urgent' ? 'urgentní…' : 'do backlogu…')}
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
    <div className="rs-panel">
      <div className="rs-title">
        <span>Rychlý checklist {todayDone > 0 && <span style={{fontSize:10,background:'var(--accent-bg)',color:'var(--accent)',borderRadius:10,padding:'1px 7px',fontWeight:700,marginLeft:6}}>{todayDone} dnes</span>}</span>
        <span className="rs-title-action" onClick={() => document.querySelector('.cl-add-row input')?.focus()}>+ přidat</span>
      </div>
      {open.map(i => renderItem(i, false))}
      {done.length > 0 && done.map(i => renderItem(i, true))}
      <div className="cl-add-row">
        <input
          value={newTitle}
          onChange={e => setNewTitle(e.target.value)}
          placeholder="Nová položka…"
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
    <div className="rs-panel">
      <div className="rs-title">
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
    <div className="rs-panel">
      <div className="rs-title">
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

// ---- MorningRitual ----
function MorningRitual({ tasks, calEvents, onConfirm, onSkip }) {
  const today = new Date().toISOString().split('T')[0];
  const suggested = tasks
    .filter(t => t.status === 'open' && (t.quadrant === 'urgent_important' || t.quadrant === 'important'))
    .sort((a, b) => {
      if (a.quadrant !== b.quadrant) return a.quadrant === 'urgent_important' ? -1 : 1;
      if (a.due_date && b.due_date) return a.due_date < b.due_date ? -1 : 1;
      if (a.due_date) return -1;
      if (b.due_date) return 1;
      return 0;
    })
    .slice(0, 5);

  const [checked, setChecked] = React.useState(() => new Set(suggested.map(t => t.id)));
  const todayEvents = calEvents.filter(e => e.date === today);
  const freeHours = Math.max(0, 8 - todayEvents.reduce((s, e) => s + (e.durationH || 1), 0));

  const qLabel = { urgent_important: 'Q1', important: 'Q2', urgent: 'Q3', other: 'Q4' };
  const dayName = new Date().toLocaleDateString('cs-CZ', { weekday: 'long', day: 'numeric', month: 'numeric' });

  function toggle(id) {
    setChecked(prev => { const s = new Set(prev); s.has(id) ? s.delete(id) : s.add(id); return s; });
  }

  return (
    <div className="morning-ritual">
      <div style={{fontSize:'24px',marginBottom:'6px'}}>🌅</div>
      <div className="morning-title">Dobré ráno, {(CURRENT_USER||''). split('.')[0].charAt(0).toUpperCase() + (CURRENT_USER||''). split('.')[0].slice(1)} 👋</div>
      <div className="morning-sub">{dayName} · {todayEvents.length > 0 ? todayEvents.length + ' schůzek' : 'žádné schůzky'} · ~{freeHours}h volného</div>
      <div className="morning-stats">
        <div className="morning-stat">
          <div className="morning-stat-val">{tasks.filter(t => t.status === 'open' && t.quadrant === 'urgent_important').length}</div>
          <div>Q1 tasků</div>
        </div>
        <div className="morning-stat">
          <div className="morning-stat-val">{tasks.filter(t => t.status === 'open' && t.due_date && t.due_date <= today).length}</div>
          <div>po deadline</div>
        </div>
        <div className="morning-stat">
          <div className="morning-stat-val">{tasks.filter(t => t.status === 'open').length}</div>
          <div>celkem open</div>
        </div>
      </div>
      {suggested.length > 0 && (
        <div className="morning-tasks-list">
          <div style={{fontSize:'11px',fontWeight:700,opacity:.65,marginBottom:'7px',textTransform:'uppercase',letterSpacing:'.06em'}}>Doporučené na dnes:</div>
          {suggested.map(t => (
            <div key={t.id} className="morning-task-row" onClick={() => toggle(t.id)}>
              <div className={'morning-cb' + (checked.has(t.id) ? ' on' : '')}>{checked.has(t.id) ? '✓' : ''}</div>
              <div className="morning-task-name">{t.title}</div>
              <div className="morning-task-q">{qLabel[t.quadrant]}{t.due_date ? ' · ' + t.due_date : ''}</div>
            </div>
          ))}
        </div>
      )}
      <div className="morning-btns">
        <button className="btn-morning-skip" onClick={onSkip}>Přeskočit</button>
        <button className="btn-morning-go" onClick={() => onConfirm(Array.from(checked))}>
          Spustit den →
        </button>
      </div>
    </div>
  );
}

// ---- WhatNowWidget ----
function WhatNowWidget({ tasks, calEvents }) {
  const [loading, setLoading] = React.useState(false);
  const [result, setResult] = React.useState(null);
  const today = new Date().toISOString().split('T')[0];
  const now = new Date();
  const timeStr = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');

  const todayEvents = (calEvents || []).filter(e => e.date === today);
  const upcomingEvent = todayEvents.find(e => e.time && e.time > timeStr);
  const topQ1 = tasks.filter(t => t.status === 'open' && t.quadrant === 'urgent_important').slice(0, 3);
  const dailyTasks = tasks.filter(t => t.status === 'open' && t.daily_order !== null && t.daily_order !== undefined);

  async function handleClick() {
    setLoading(true);
    setResult(null);
    try {
      const d = await apiFetch('what_now', 'POST', {
        time: timeStr,
        nextEvent: upcomingEvent ? upcomingEvent.title + ' v ' + upcomingEvent.time : null,
        topQ1: topQ1.map(t => ({ title: t.title, due_date: t.due_date })),
        dailyTasks: dailyTasks.map(t => ({ title: t.title, quadrant: t.quadrant })),
      });
      setResult(d);
    } catch(e) { setResult({ error: 'Nepodařilo se načíst doporučení.' }); }
    setLoading(false);
  }

  React.useEffect(() => {
    if (!result) return;
    const t = setTimeout(() => setResult(null), 30000);
    return () => clearTimeout(t);
  }, [result]);

  return (
    <div>
      {!result && (
        <div className="whatnow-bar" onClick={!loading ? handleClick : undefined}>
          <span className="whatnow-spark">{loading ? '...' : '✦'}</span>
          <div>
            <div className="whatnow-text-head">Co mám dělat teď?</div>
            <div className="whatnow-sub">AI doporučení dle kalendáře a priorit</div>
          </div>
        </div>
      )}
      {result && !result.error && (
        <div className="whatnow-result-inline">
          <div className="whatnow-text">{result.text}</div>
          {result.task_title && (
            <div className="whatnow-task">
              <span style={{color:'var(--accent)',fontSize:15}}>→</span>
              {result.task_title}
            </div>
          )}
          <div className="whatnow-dismiss" onClick={() => setResult(null)}>Zavřít ×</div>
        </div>
      )}
      {result && result.error && (
        <div style={{fontSize:'12px',color:'var(--danger)',marginTop:6}}>{result.error}</div>
      )}
    </div>
  );
}

// ---- DnesResetModal ----
function DnesResetModal({ tasks, onConfirm, onSkip }) {
  const openTasks = tasks.filter(t => t.daily_order !== null && t.daily_order !== undefined && t.status === 'open');
  const doneTasks = tasks.filter(t => t.daily_order !== null && t.daily_order !== undefined && t.status === 'done');
  const [keep, setKeep] = React.useState(() => new Set(openTasks.map(t => t.id)));

  function toggleKeep(id) {
    setKeep(prev => {
      const s = new Set(prev);
      s.has(id) ? s.delete(id) : s.add(id);
      return s;
    });
  }

  function handleConfirm() {
    const removeIds = [
      ...doneTasks.map(t => t.id),
      ...openTasks.filter(t => !keep.has(t.id)).map(t => t.id),
    ];
    onConfirm(removeIds);
  }

  return (
    <div style={{maxWidth:480,margin:'40px auto',background:'var(--white)',borderRadius:'12px',boxShadow:'0 4px 24px rgba(0,0,0,0.10)',padding:'28px 28px 22px'}}>
      <div style={{fontWeight:700,fontSize:'16px',marginBottom:'4px'}}>Nový den — co s denním plánem?</div>
      <div style={{fontSize:'12px',color:'var(--grey-text)',marginBottom:'20px'}}>
        Zaškrtni tasky, které chceš přenést na dnes.
        {doneTasks.length > 0 && ' Hotové (' + doneTasks.length + ') se odeberou automaticky.'}
      </div>

      {openTasks.length > 0 ? (
        <div style={{marginBottom:'20px'}}>
          {openTasks.map(t => (
            <div key={t.id} style={{display:'flex',alignItems:'center',gap:'10px',padding:'7px 0',borderBottom:'1px solid var(--grey-border)'}}>
              <input
                type="checkbox"
                checked={keep.has(t.id)}
                onChange={() => toggleKeep(t.id)}
                style={{accentColor:'var(--blue)',width:'15px',height:'15px',flexShrink:0,cursor:'pointer'}}
              />
              <span style={{fontSize:'13px',flex:1}}>{t.title}</span>
              {t.due_date && (
                <span style={{fontSize:'10px',color:'var(--grey-text)'}}>{t.due_date}</span>
              )}
            </div>
          ))}
        </div>
      ) : (
        <div style={{color:'var(--grey-text)',fontSize:'13px',marginBottom:'20px'}}>Žádné nedokončené tasky v plánu.</div>
      )}

      <div style={{display:'flex',gap:'10px',justifyContent:'flex-end'}}>
        <button onClick={onSkip} style={{background:'none',border:'1px solid var(--grey-border)',borderRadius:'6px',padding:'7px 16px',fontSize:'13px',cursor:'pointer',color:'var(--grey-text)'}}>
          Přeskočit
        </button>
        <button onClick={handleConfirm} style={{background:'var(--blue)',color:'#fff',border:'none',borderRadius:'6px',padding:'7px 16px',fontSize:'13px',cursor:'pointer',fontWeight:600}}>
          Potvrdit
        </button>
      </div>
    </div>
  );
}

// ---- DnesView ----
function DnesView({ tasks, calEvents, onToggleDone, onEdit, onRemoveFromDaily, onReorder, onBatchAddToDaily, onBatchRemoveFromDaily, forceShowMorning, onForceDone }) {
  const [dragId, setDragId] = useState(null);
  const [dragOverId, setDragOverId] = useState(null);
  const todayStr = new Date().toISOString().split('T')[0];
  const hasDnesTasks = tasks.some(t => t.daily_order !== null && t.daily_order !== undefined);
  const [showDnesReset, setShowDnesReset] = useState(() => {
    return localStorage.getItem('lastDnesCheck') !== todayStr && hasDnesTasks;
  });
  const [showMorning, setShowMorning] = useState(false);
  function checkShowMorning() {
    const h = new Date().getHours();
    if (h >= 6 && h <= 10 && localStorage.getItem('lastMorningCheck') !== todayStr) {
      setShowMorning(true);
    }
  }

  const dnesTasks = tasks
    .filter(t => t.status === 'open' && t.daily_order !== null && t.daily_order !== undefined)
    .sort((a, b) => a.daily_order - b.daily_order);

  function handleDragStart(id) { setDragId(id); }
  function handleDragOver(e, id) { e.preventDefault(); setDragOverId(id); }
  function handleDrop(e, targetId) {
    e.preventDefault();
    setDragOverId(null);
    if (dragId && dragId !== targetId) onReorder(dragId, targetId);
    setDragId(null);
  }
  function handleDragEnd() { setDragId(null); setDragOverId(null); }

  function handleDnesResetConfirm(removeIds) {
    localStorage.setItem('lastDnesCheck', todayStr);
    setShowDnesReset(false);
    if (removeIds && removeIds.length && onBatchRemoveFromDaily) {
      onBatchRemoveFromDaily(removeIds).then(() => checkShowMorning());
    } else {
      checkShowMorning();
    }
  }
  function handleDnesResetSkip() {
    localStorage.setItem('lastDnesCheck', todayStr);
    setShowDnesReset(false);
    checkShowMorning();
  }

  function handleMorningConfirm(ids) {
    localStorage.setItem('lastMorningCheck', todayStr);
    setShowMorning(false);
    if (onBatchAddToDaily) onBatchAddToDaily(ids);
    if (forceShowMorning && onForceDone) onForceDone();
  }
  function handleMorningSkip() {
    localStorage.setItem('lastMorningCheck', todayStr);
    setShowMorning(false);
    if (forceShowMorning && onForceDone) onForceDone();
  }

  // Timeline: group today's calendar events by hour
  const todayEvents = (calEvents || []).filter(e => e.date === todayStr);
  const HOURS = [8,9,10,11,12,13,14,15,16,17];
  function freeMinutes() {
    const busyMins = todayEvents.reduce((s, e) => s + (e.durationH || 1) * 60, 0);
    return Math.max(0, 8 * 60 - busyMins);
  }
  const freeH = Math.floor(freeMinutes() / 60);
  const freeM = freeMinutes() % 60;
  const freeStr = freeH > 0 ? freeH + 'h' + (freeM > 0 ? ' ' + freeM + 'min' : '') : freeM + 'min';

  // Reset denního plánu
  if (showDnesReset) {
    return (
      <DnesResetModal
        tasks={tasks}
        onConfirm={handleDnesResetConfirm}
        onSkip={handleDnesResetSkip}
      />
    );
  }

  // Morning ritual overlay
  if (showMorning || forceShowMorning) {
    return (
      <MorningRitual
        tasks={tasks}
        calEvents={calEvents || []}
        onConfirm={handleMorningConfirm}
        onSkip={handleMorningSkip}
      />
    );
  }

  const todayFull = new Date().toLocaleDateString('cs-CZ', { weekday: 'long', day: 'numeric', month: 'long' });

  return (
    <React.Fragment>
      <div className="dnes-header">
        <div>
          <div className="dnes-title">Dnes</div>
          <div className="dnes-meta">
            {todayFull}
            {freeH > 0 && <span className="dnes-free"> · {freeStr} volného</span>}
          </div>
        </div>
      </div>

      {todayEvents.length > 0 && (
        <div className="timeline-section">
          <div className="timeline-header">
            Dnešní kalendář
            <span style={{fontWeight:400}}>{todayEvents.length} {todayEvents.length === 1 ? 'událost' : 'události'}</span>
          </div>
          <div className="timeline-body">
            {HOURS.map(h => {
              const hStr = h.toString().padStart(2,'0') + ':00';
              const evs = todayEvents.filter(e => e.time && parseInt(e.time.split(':')[0]) === h);
              return (
                <div key={h} className="timeline-row">
                  <div className="t-time">{hStr}</div>
                  <div className="t-slot">
                    {evs.map((e, i) => (
                      <div key={i} className={'cal-block ' + (e.allDay ? 'all-day' : 'work')}>
                        {e.title.length > 30 ? e.title.slice(0,28) + '…' : e.title}
                        {e.durationH && <span className="cal-duration">{e.durationH}h</span>}
                      </div>
                    ))}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      <div className="dnes-plan">
        <div className="dnes-plan-header">
          <div className="dnes-plan-title">Plán dne</div>
          <div className="dnes-plan-meta">{dnesTasks.length} {dnesTasks.length === 1 ? 'task' : dnesTasks.length < 5 ? 'tasky' : 'tasků'}</div>
        </div>
        {dnesTasks.length === 0 ? (
          <div style={{textAlign:'center',color:'var(--text-2)',padding:'30px 0'}}>
            <div style={{fontWeight:600,marginBottom:4}}>Denní plán je prázdný</div>
            <div style={{fontSize:12}}>Přidej tasky pomocí +D tlačítka v matici</div>
          </div>
        ) : dnesTasks.map((t, idx) => {
          const isOverdue = t.due_date && t.due_date < todayStr;
          const daysUntil = t.due_date ? Math.ceil((new Date(t.due_date) - new Date(todayStr)) / 86400000) : null;
          const isSoon = !isOverdue && daysUntil !== null && daysUntil <= 3;
          const qClass = t.quadrant === 'urgent_important' ? 'q1' : t.quadrant === 'important' ? 'q2' : t.quadrant === 'urgent' ? 'q3' : '';
          return (
            <div
              key={t.id}
              className="dnes-task-row"
              draggable
              onDragStart={() => handleDragStart(t.id)}
              onDragOver={e => handleDragOver(e, t.id)}
              onDrop={e => handleDrop(e, t.id)}
              onDragEnd={handleDragEnd}
              style={{opacity: dragId === t.id ? 0.4 : 1, background: dragOverId === t.id ? 'var(--accent-bg)' : ''}}
            >
              <div className="dnes-idx">{idx + 1}</div>
              <div className={'dnes-cb' + (qClass ? ' ' + qClass : '')} onClick={() => onToggleDone(t)} />
              <div className="dnes-task-name" onClick={() => onEdit(t)} style={{cursor:'pointer'}}>{t.title}</div>
              {t.due_date && (
                <div className={'dnes-due' + (isOverdue ? ' overdue' : isSoon ? ' soon' : ' normal')}>
                  {isOverdue ? 'prošlé' : daysUntil === 0 ? 'dnes' : daysUntil + 'd'}
                </div>
              )}
              <button className="dnes-remove" onClick={() => onRemoveFromDaily(t)}>×</button>
            </div>
          );
        })}
      </div>

      <WhatNowWidget tasks={tasks} calEvents={calEvents} />
    </React.Fragment>
  );
}

// ---- ActionItemsPopover (1on1 open action items) ----
function ActionItemsPopover({ people, onSelectPerson }) {
  const [open, setOpen] = useState(false);
  const ref = useRef(null);
  const groups = people.filter(p => (p.open_action_items || []).length > 0);
  const total = groups.reduce((s, p) => s + p.open_action_items.length, 0);
  useEffect(() => {
    if (!open) return;
    function h(e) { if (ref.current && !ref.current.contains(e.target)) setOpen(false); }
    document.addEventListener('mousedown', h);
    return () => document.removeEventListener('mousedown', h);
  }, [open]);
  if (total === 0) return null;
  return (
    <span ref={ref} style={{position:'relative',display:'inline-block'}}>
      <span className="onenon-ai-badge" onClick={() => setOpen(v => !v)}>{total}</span>
      {open && (
        <div className="onenon-ai-popover">
          {groups.map(p => (
            <div key={p.person} className="onenon-ai-group">
              <div className="onenon-ai-group-header" onClick={() => { onSelectPerson(p.person); setOpen(false); }}>
                {p.person}<span className="onenon-ai-count">{p.open_action_items.length}</span>
              </div>
              {p.open_action_items.map((text, i) => (
                <div key={i} className="onenon-ai-item" onClick={() => { onSelectPerson(p.person); setOpen(false); }}>
                  <span className="onenon-ai-dot" />{text}
                </div>
              ))}
            </div>
          ))}
        </div>
      )}
    </span>
  );
}

// ---- Q1AlertBadge ----
function Q1AlertBadge({ tasks, onEditTask }) {
  const [open, setOpen] = useState(false);
  const [pos, setPos] = useState({ top: 0, left: 0 });
  const ref = useRef(null);
  const today = new Date().toISOString().slice(0, 10);

  useEffect(() => {
    if (!open) return;
    function handleClick(e) { if (ref.current && !ref.current.contains(e.target)) setOpen(false); }
    document.addEventListener('mousedown', handleClick);
    return () => document.removeEventListener('mousedown', handleClick);
  }, [open]);

  function handleToggle() {
    if (!open && ref.current) {
      const r = ref.current.getBoundingClientRect();
      setPos({ top: r.bottom + 6, left: Math.min(r.right - 260, window.innerWidth - 348) });
    }
    setOpen(v => !v);
  }

  return (
    <span ref={ref} className="q1-alert" onClick={handleToggle} title="Q1 tasky s deadlinem — klikni pro detail">
      {tasks.length}
      {open && ReactDOM.createPortal(
        <div className="q1-popover" style={{top: pos.top, left: pos.left}} onClick={e => e.stopPropagation()}>
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
        </div>,
        document.body
      )}
    </span>
  );
}

// ---- KPI Panel ----
// ---- ChatPanel ----
function ChatPanel() {
  const [open, setOpen] = React.useState(false);
  const [input, setInput] = React.useState('');
  const [history, setHistory] = React.useState([]);
  const [loading, setLoading] = React.useState(false);
  const bottomRef = React.useRef(null);

  React.useEffect(() => {
    if (bottomRef.current) bottomRef.current.scrollIntoView({ behavior: 'smooth' });
  }, [history, loading]);

  async function handleSend() {
    const msg = input.trim();
    if (!msg || loading) return;
    const newHistory = [...history, { role: 'user', content: msg }];
    setHistory(newHistory);
    setInput('');
    setLoading(true);
    try {
      const data = await apiFetch('chat', 'POST', { message: msg, history: history });
      setHistory([...newHistory, { role: 'assistant', content: data.reply }]);
    } catch(e) {
      setHistory([...newHistory, { role: 'assistant', content: 'Chyba: ' + e.message }]);
    }
    setLoading(false);
  }

  function handleKey(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(); }
  }

  return (
    <div className="rs-panel">
      <div style={{display:'flex',alignItems:'center',justifyContent:'space-between',marginBottom:'10px'}}>
        <span style={{fontSize:'11px',fontWeight:700,color:'var(--text-2)',textTransform:'uppercase',letterSpacing:'0.06em'}}>AI Chat</span>
        <span style={{fontSize:'10px',background:'#F5F3FF',color:'#7C3AED',padding:'1px 6px',borderRadius:4,fontWeight:600}}>Haiku</span>
      </div>
      <div style={{maxHeight:'280px',overflowY:'auto',marginBottom:'8px',display:'flex',flexDirection:'column',gap:'6px'}}>
        {history.length === 0 && (
          <div style={{fontSize:'11px',color:'var(--grey-text)',fontStyle:'italic',padding:'8px 0'}}>
            Zeptej se na cokoli — priority, co teď dělat, jak formulovat mail...
          </div>
        )}
        {history.map((h, i) => (
          <div key={i} style={{
            fontSize:'12px',padding:'6px 9px',borderRadius:'8px',
            background: h.role === 'user' ? '#EBF0FF' : '#F4F5F7',
            alignSelf: h.role === 'user' ? 'flex-end' : 'flex-start',
            maxWidth:'90%',whiteSpace:'pre-wrap',lineHeight:'1.4',
          }}>
            {h.content}
          </div>
        ))}
        {loading && (
          <div style={{fontSize:'12px',padding:'6px 9px',borderRadius:'8px',background:'#F4F5F7',alignSelf:'flex-start',color:'var(--grey-text)'}}>...</div>
        )}
        <div ref={bottomRef} />
      </div>
      <div style={{display:'flex',gap:'6px'}}>
        <textarea
          value={input}
          onChange={e => setInput(e.target.value)}
          onKeyDown={handleKey}
          placeholder="Zeptej se AI…"
          rows={2}
          style={{flex:1,fontSize:'12px',padding:'6px 8px',borderRadius:'6px',border:'1px solid var(--grey-border)',resize:'none',fontFamily:'var(--font)'}}
        />
        <button
          onClick={handleSend}
          disabled={loading || !input.trim()}
          style={{background:'var(--blue)',color:'#fff',border:'none',borderRadius:'6px',padding:'0 10px',fontSize:'18px',cursor:'pointer',flexShrink:0,opacity: loading || !input.trim() ? 0.5 : 1}}
        >→</button>
      </div>
      {history.length > 0 && (
        <button onClick={() => setHistory([])} style={{marginTop:'6px',background:'none',border:'none',fontSize:'10px',color:'var(--grey-text)',cursor:'pointer',padding:0}}>Smazat historii</button>
      )}
    </div>
  );
}

function KpiPanel({ todayDone, totalOpen }) {
  const [accuracy, setAccuracy] = React.useState(null);

  React.useEffect(() => {
    apiFetch('tasks', 'GET', null, { history: 'month_accuracy' })
      .then(d => { if (d.accuracy !== undefined) setAccuracy(d.accuracy); })
      .catch(() => {});
  }, [todayDone]);

  return (
    <div className="rs-panel">
      <div className="rs-title">Výkon</div>
      <div className="kpi-row">
        <div>
          <div className="kpi-stat" style={{color:todayDone>0?'var(--success)':'var(--text)'}}>{todayDone}</div>
          <div className="kpi-label-sm">hotovo dnes</div>
        </div>
        <div className="kpi-sep" />
        <div>
          <div className="kpi-stat" style={{color:'var(--text-2)'}}>{totalOpen}</div>
          <div className="kpi-label-sm">otevřených</div>
        </div>
      </div>
      {accuracy !== null && (
        <React.Fragment>
          <div className="progress-bar"><div className="progress-fill" style={{width:Math.min(accuracy,100)+'%'}} /></div>
          <div className="progress-label">Přesnost odhadů <span>{accuracy}%</span></div>
        </React.Fragment>
      )}
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
              <input type="checkbox" checked={!!i.done} readOnly style={{accentColor:'var(--blue)',width:14,height:14,flexShrink:0}} />
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

// ---- PrepDocModal ----
function PrepDocModal({ person, notes, profile, onClose }) {
  const [aiTopics, setAiTopics] = React.useState(null);
  const [aiLoading, setAiLoading] = React.useState(false);

  const allActionItems = notes.flatMap(n =>
    (n.action_items || []).map(it => ({ ...it, from: n.meeting_date }))
  );
  const openItems = allActionItems.filter(it => !it.done);
  const doneItems = allActionItems.filter(it => it.done).slice(0, 3);

  const lastNote = notes[0] || null;
  const prevNote = notes[1] || null;
  const moodTrend = lastNote && prevNote && lastNote.mood && prevNote.mood
    ? (lastNote.mood > prevNote.mood ? 'zlepšení' : lastNote.mood < prevNote.mood ? 'zhoršení' : 'stabilní')
    : null;

  const recentTags = [...new Set(notes.slice(0, 3).flatMap(n => n.tags || []))];

  const today = new Date().toLocaleDateString('cs-CZ', { day: 'numeric', month: 'numeric', year: 'numeric' });
  const potentialBg = { low: '#E8F5EC', medium: '#FFF4E0', high: '#FEE8E7' };
  const daysSince = lastNote
    ? Math.floor((Date.now() - new Date(lastNote.meeting_date).getTime()) / 86400000)
    : null;

  async function loadAiTopics() {
    setAiLoading(true);
    try {
      const d = await apiFetch('prep_topics', 'POST', {
        person,
        profile,
        openItems: openItems.map(it => it.text),
        recentTags,
        moodTrend,
        lastNoteDate: lastNote ? lastNote.meeting_date : null,
      });
      setAiTopics(d.topics || []);
    } catch(e) { setAiTopics(['Nepodařilo se načíst návrhy.']); }
    setAiLoading(false);
  }

  function copyToClipboard() {
    const lines = [
      '1on1 s ' + person + ' — ' + today,
      '─'.repeat(40),
    ];
    if (lastNote) lines.push('Poslední schůzka: ' + lastNote.meeting_date + (daysSince !== null ? ' (' + daysSince + ' dní)' : ''));
    if (moodTrend) lines.push('Nálada: ' + moodTrend);
    if (recentTags.length) lines.push('Tagy: ' + recentTags.join(', '));
    if (openItems.length) {
      lines.push('', 'Otevřené action items (' + openItems.length + '):');
      openItems.forEach(it => lines.push('• ' + it.text + ' (z ' + it.from + ')'));
    }
    if (aiTopics && aiTopics.length) {
      lines.push('', 'Navrhovaná témata:');
      aiTopics.forEach(t => lines.push('• ' + t));
    }
    navigator.clipboard.writeText(lines.join('\n')).catch(() => {});
  }

  const potentialColor = { low: 'var(--success)', medium: 'var(--warning)', high: 'var(--accent)' };
  const potentialLabel = { low: 'Nízký potenciál', medium: 'Střední potenciál', high: 'Vysoký potenciál' };
  const narocnostLabel = { low: 'Nízká náročnost', medium: 'Střední náročnost', high: 'Vysoká náročnost' };
  const narocnostColor = { low: 'var(--accent)', medium: 'var(--warning)', high: 'var(--danger)' };

  return (
    <div className="modal-overlay" onClick={e => e.target === e.currentTarget && onClose()}>
      <div className="modal-box" style={{padding:0,maxWidth:520,width:'95vw'}}>
        <div className="prep-header">
          <div className="prep-person-name">1on1 s {person}</div>
          <div className="prep-date-line">{today}{daysSince !== null ? ' · Poslední schůzka: ' + daysSince + ' dní' : ''}</div>
          <div className="prep-chips">
            {daysSince !== null && <span className="prep-chip">● {daysSince} dní</span>}
            {openItems.length > 0 && <span className="prep-chip">⚡ {openItems.length} open action items</span>}
            {moodTrend && <span className="prep-chip">{moodTrend === 'zlepšení' ? '↑' : moodTrend === 'zhoršení' ? '↓' : '→'} nálada {moodTrend}</span>}
            {recentTags.slice(0,2).map(t => <span key={t} className="prep-chip">{t}</span>)}
          </div>
        </div>
        <div className="prep-modal-body">

          {profile && (
            <div className="prep-section">
              <div className="prep-section-label">Profil</div>
              <div style={{display:'flex',alignItems:'center',gap:8,flexWrap:'wrap',marginBottom:8}}>
                {profile.performance > 0 && <span style={{fontSize:13}}>{'★'.repeat(profile.performance)}{'☆'.repeat(5-profile.performance)}</span>}
                {profile.potential && <span style={{background: profile.potential==='high'?'#DCFCE7':profile.potential==='medium'?'#FEF3C7':'#F0F9FF',color:potentialColor[profile.potential]||'var(--text-2)',padding:'2px 9px',borderRadius:20,fontWeight:700,fontSize:11,border:'1px solid currentColor'}}>{potentialLabel[profile.potential]||profile.potential}</span>}
                {profile.management_difficulty && <span style={{background:'#EFF6FF',color:'var(--accent)',padding:'2px 9px',borderRadius:20,fontWeight:700,fontSize:11,border:'1px solid var(--accent)'}}>{narocnostLabel[profile.management_difficulty]||profile.management_difficulty}</span>}
              </div>
              {profile.strength && <div style={{fontSize:12,color:'var(--text)',marginBottom:3}}>💪 <strong>Silná stránka:</strong> {profile.strength}</div>}
              {profile.development && <div style={{fontSize:12,color:'var(--text)'}}>🎯 <strong>Rozvoj:</strong> {profile.development}</div>}
            </div>
          )}

          {(openItems.length > 0 || doneItems.length > 0) && (
            <div className="prep-section">
              <div className="prep-section-label">Open action items ({openItems.length})</div>
              {openItems.map((it, i) => (
                <div key={i} className="prep-ai-item">
                  <div className="prep-ai-cb" />
                  <div style={{flex:1,fontSize:13}}>{it.text}</div>
                  <div className="prep-ai-from">ze {it.from}</div>
                </div>
              ))}
              {doneItems.map((it, i) => (
                <div key={i} className="prep-ai-item">
                  <div className="prep-ai-cb done" />
                  <div className="prep-ai-done" style={{flex:1,fontSize:13}}>{it.text}</div>
                  <div className="prep-ai-from" style={{color:'var(--success)'}}>splněno</div>
                </div>
              ))}
            </div>
          )}

          <div className="prep-section">
            <div className="prep-section-label" style={{display:'flex',alignItems:'center',gap:6,marginBottom:8}}>
              Navrhovaná témata
              {aiTopics && <span style={{fontSize:9,background:'#F5F3FF',color:'#7C3AED',padding:'1px 5px',borderRadius:4,fontWeight:700,textTransform:'none',letterSpacing:0}}>AI</span>}
            </div>
            {!aiTopics && !aiLoading && (
              <button className="btn btn-secondary" style={{fontSize:12}} onClick={loadAiTopics}>✦ Vygenerovat pomocí AI</button>
            )}
            {aiLoading && <div style={{fontSize:12,color:'var(--grey-text)'}}>Generuji...</div>}
            {aiTopics && aiTopics.map((t, i) => (
              <div key={i} className="prep-topic">
                <span className="prep-topic-dot">·</span>{t}
              </div>
            ))}
          </div>
        </div>
        <div className="prep-footer">
          <button className="btn btn-secondary prep-btn" style={{fontSize:12}} onClick={copyToClipboard}>Kopírovat</button>
          <button className="btn btn-primary prep-btn" style={{fontSize:12,marginLeft:'auto'}} onClick={onClose}>Zavřít</button>
        </div>
      </div>
    </div>
  );
}

function SignalChip({ type, label }) {
  return <span className={'signal-chip ' + type}>{label}</span>;
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
  const [prepDoc, setPrepDoc] = React.useState(false);
  const [mappingModal, setMappingModal] = React.useState(false);
  const [contentTab, setContentTab] = React.useState('detail');
  const [metricsOpen, setMetricsOpen] = React.useState(true);

  React.useEffect(() => { loadPeople(); }, []);

  React.useEffect(() => {
    const layout = document.getElementById('layout');
    if (layout) layout.classList.add('onenon-active');
    return () => { const l = document.getElementById('layout'); if (l) l.classList.remove('onenon-active'); };
  }, []);

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

  const selectedPeopleData = people.find(p => p.person === selected);
  const allOpenItems = notes.flatMap(n =>
    (n.action_items || [])
      .filter(it => !it.done)
      .map((it, idx) => ({ ...it, from: n.meeting_date, noteId: n.id, idx }))
  );
  const allDoneItems = notes.flatMap(n =>
    (n.action_items || [])
      .filter(it => it.done)
      .map((it, idx) => ({ ...it, from: n.meeting_date, noteId: n.id, idx }))
  );
  const moodNotes = notes.filter(n => n.mood).slice(0, 4);
  const lastMoods = moodNotes.map(n => n.mood);
  const moodDates = moodNotes.map(n => {
    const d = new Date(n.meeting_date);
    return (d.getDate()) + '. ' + (d.getMonth()+1) + '.';
  });
  const moodTrend = lastMoods.length >= 2
    ? (lastMoods[0] > lastMoods[1] ? '↑' : lastMoods[0] < lastMoods[1] ? '↓' : '→')
    : null;
  const moodAvg = lastMoods.length ? (lastMoods.reduce((a,b) => a+b,0)/lastMoods.length).toFixed(1) : null;
  const moodBarColor = m => m >= 4 ? 'var(--success)' : m === 3 ? '#86EFAC' : m === 2 ? 'var(--warning)' : '#FCA5A5';
  const potLabels = { low: 'Nízký', medium: 'Střední', high: 'Vysoký' };
  const mgmtLabels = { low: 'Nízká', medium: 'Střední', high: 'Vysoká' };
  const trendArrow = { up: '↑', down: '↓', flat: '→' };

  return (
    <div className="onenon-layout">
      {/* Levý sidebar — seznam lidí */}
      <div className="onenon-people-sidebar">
        <div className="onenon-people-header">
          <span style={{fontSize:12,fontWeight:700,color:'var(--text-2)',textTransform:'uppercase',letterSpacing:'.04em'}}>Tým ({people.length})</span>
          <div style={{display:'flex',gap:4,alignItems:'center'}}>
            <button className="btn btn-ghost" style={{fontSize:16,padding:'2px 6px',lineHeight:1}} onClick={() => setMappingModal(true)} title="Auto-tasky z kalendáře">⚙</button>
            <button className="btn btn-primary" style={{fontSize:11,padding:'4px 10px'}} onClick={() => setModal({ person: '' })}>+ Přidat</button>
          </div>
        </div>
        {totalOpen > 0 && (
          <div className="onenon-alert-banner red">
            <ActionItemsPopover people={people} onSelectPerson={name => loadNotes(name)} />
            <span>{totalOpen} open action items celkem</span>
          </div>
        )}
        {warnPeople.length > 0 && (
          <div className="onenon-alert-banner orange">⚠ {warnPeople.length} bez 1on1 &gt;30 dní</div>
        )}
        <div className="onenon-people-list">
          {people.map(p => {
            const isEditing = editingPerson && editingPerson.name === p.person;
            const isActive = selected === p.person;
            const prof = p.profile || {};
            const trend = p.mood_trend;
            return (
              <div key={p.person} className="onenon-person-row">
                {isEditing ? (
                  <PersonEditForm
                    person={editingPerson}
                    onSave={(newName, profile) => handleUpdatePerson(p.person, newName, profile)}
                    onCancel={() => setEditingPerson(null)}
                    onDelete={() => handleDeletePerson(p.person)}
                  />
                ) : (
                  <div style={{display:'flex',alignItems:'center',gap:2}}>
                    <button className={'onenon-person-item-btn' + (isActive ? ' active' : '')} onClick={() => loadNotes(p.person)}>
                      <div style={{display:'flex',justifyContent:'space-between',alignItems:'center',marginBottom:3}}>
                        <span style={{fontWeight:600,fontSize:13,color:isActive ? 'var(--purple)' : 'var(--text)'}}>{p.person}</span>
                        <div style={{display:'flex',gap:4,alignItems:'center'}}>
                          {trend && <span className={'onenon-person-trend ' + trend}>{trendArrow[trend]}</span>}
                          {p.open_items > 0 && <span style={{background:'var(--danger)',color:'#fff',borderRadius:9,padding:'1px 5px',fontSize:10,fontWeight:700}}>{p.open_items}</span>}
                        </div>
                      </div>
                      <div style={{display:'flex',alignItems:'center',gap:6}}>
                        {prof.performance > 0 && <span className="onenon-person-stars">{'★'.repeat(prof.performance)}{'☆'.repeat(5-prof.performance)}</span>}
                        <span style={{fontSize:10,color:'var(--text-3)'}}>· {p.days_since} dní</span>
                      </div>
                    </button>
                    <button className="onenon-person-edit-btn" title="Upravit" onClick={e => { e.stopPropagation(); setEditingPerson({ name: p.person, description: p.description || '', profile: p.profile || null }); }}>✎</button>
                  </div>
                )}
              </div>
            );
          })}
        </div>
        {selected && (
          <div className="onenon-sidebar-footer">
            <button className="onenon-footer-btn" onClick={() => setModal({ person: selected })}>+ Nová schůzka s {selected}</button>
          </div>
        )}
      </div>

      {/* Hlavní obsah */}
      <div className="onenon-main">
        {!selected && (
          <div style={{color:'var(--text-3)',fontSize:13,padding:'40px 0',textAlign:'center'}}>
            <div style={{fontSize:28,marginBottom:8}}>♟</div>
            Vyber osobu vlevo
          </div>
        )}
        {selected && (
          <>
            {/* Person header */}
            <div className="onenon-person-header">
              <div style={{display:'flex',justifyContent:'space-between',alignItems:'flex-start'}}>
                <div>
                  <div style={{fontSize:22,fontWeight:800,color:'var(--text)'}}>{selected}</div>
                  {selectedDesc && <div style={{fontSize:13,color:'var(--text-2)',marginTop:3}}>{selectedDesc}</div>}
                </div>
                <div style={{display:'flex',gap:6}}>
                  <button className="btn btn-secondary" style={{fontSize:12}} onClick={() => setPrepDoc(true)}>📋 Připravit schůzku</button>
                  <button className="btn btn-primary" style={{fontSize:12,padding:'7px 16px'}} onClick={() => setModal({ person: selected })}>Nový zápis</button>
                </div>
              </div>
              <div className="onenon-signal-chips" style={{marginTop:10}}>
                {selectedPeopleData && (
                  <SignalChip type={selectedPeopleData.days_since > 30 ? 'warn' : 'ok'} label={'Poslední 1on1: ' + selectedPeopleData.days_since + ' dní'} />
                )}
                {allOpenItems.length > 0 && <SignalChip type="warn" label={allOpenItems.length + ' open action items'} />}
                {moodTrend && <SignalChip type={moodTrend === '↑' ? 'ok' : moodTrend === '↓' ? 'warn' : 'info'} label={'Nálada ' + (moodTrend === '↑' ? '↑ stoupá' : moodTrend === '↓' ? '↓ klesá' : '→ stabilní')} />}
                {selectedProfile && selectedProfile.potential === 'high' && <SignalChip type="purple" label="Vysoký potenciál" />}
              </div>
            </div>

            {/* Metrics row — collapsible */}
            {selectedProfile && (selectedProfile.performance > 0 || selectedProfile.potential || selectedProfile.strength || selectedProfile.mgmt_effort) && (
              <div className="onenon-metrics-row">
                {metricsOpen && (
                  <>
                    {selectedProfile.performance > 0 && (
                      <div className="onenon-metric">
                        <span className="onenon-metric-label">Výkon</span>
                        <span style={{fontSize:14,color:'#F59E0B',letterSpacing:.5}}>{'★'.repeat(selectedProfile.performance)}{'☆'.repeat(5-selectedProfile.performance)}</span>
                      </div>
                    )}
                    {selectedProfile.potential && (
                      <div className="onenon-metric">
                        <span className="onenon-metric-label">Potenciál</span>
                        <span style={{background:selectedProfile.potential==='high'?'var(--purple-bg)':selectedProfile.potential==='medium'?'var(--warning-bg)':'var(--accent-bg)',color:selectedProfile.potential==='high'?'var(--purple)':selectedProfile.potential==='medium'?'var(--warning)':'var(--accent)',border:selectedProfile.potential==='high'?'1px solid #DDD6FE':selectedProfile.potential==='medium'?'1px solid #FDE68A':'1px solid #BFDBFE',padding:'2px 9px',borderRadius:20,fontWeight:700,fontSize:12,display:'inline-block'}}>{potLabels[selectedProfile.potential] || selectedProfile.potential}</span>
                      </div>
                    )}
                    {selectedProfile.mgmt_effort && (
                      <div className="onenon-metric">
                        <span className="onenon-metric-label">Náročnost řízení</span>
                        <span style={{background:selectedProfile.mgmt_effort==='low'?'var(--success-bg)':selectedProfile.mgmt_effort==='medium'?'var(--warning-bg)':'#FEE2E2',color:selectedProfile.mgmt_effort==='low'?'var(--success)':selectedProfile.mgmt_effort==='medium'?'var(--warning)':'var(--danger)',border:selectedProfile.mgmt_effort==='low'?'1px solid #BBF7D0':selectedProfile.mgmt_effort==='medium'?'1px solid #FDE68A':'1px solid #FECACA',padding:'2px 9px',borderRadius:20,fontWeight:700,fontSize:12,display:'inline-block'}}>{mgmtLabels[selectedProfile.mgmt_effort] || selectedProfile.mgmt_effort}</span>
                      </div>
                    )}
                    {selectedProfile.strength && (
                      <div className="onenon-metric">
                        <span className="onenon-metric-label">Silná stránka</span>
                        <span className="onenon-metric-val">{selectedProfile.strength}</span>
                      </div>
                    )}
                    {selectedProfile.development && (
                      <div className="onenon-metric" style={{borderRight:'none',marginRight:0,paddingRight:0}}>
                        <span className="onenon-metric-label">Oblast rozvoje</span>
                        <span className="onenon-metric-val">{selectedProfile.development}</span>
                      </div>
                    )}
                  </>
                )}
                <span className="onenon-metrics-collapse" onClick={() => setMetricsOpen(v => !v)}>
                  {metricsOpen ? 'kliknutím sbalit ↑' : 'zobrazit profil ↓'}
                </span>
              </div>
            )}

            {prepDoc && ReactDOM.createPortal(<PrepDocModal person={selected} notes={notes} profile={selectedProfile} onClose={() => setPrepDoc(false)} />, document.getElementById('modals'))}

            {/* Dvousloupcový layout */}
            <div className="onenon-cols">
              {/* Levý sloupec: action items + mood chart */}
              <div>
                {/* Action items card */}
                <div className="onenon-col-card">
                  <div className="onenon-col-card-header">
                    <span>Open action items</span>
                    <button style={{background:'none',border:'none',cursor:'pointer',fontSize:12,color:'var(--accent)',fontWeight:700,fontFamily:'var(--font)'}} onClick={() => setModal({ person: selected })}>+ přidat</button>
                  </div>
                  <div style={{padding:'8px 14px 12px'}}>
                    {allOpenItems.length === 0 && <div style={{fontSize:12,color:'var(--text-3)',padding:'4px 0'}}>Žádné otevřené položky</div>}
                    {allOpenItems.length > 0 && (
                      <>
                        <div style={{fontSize:11,fontWeight:700,color:'var(--text-2)',marginBottom:6,display:'flex',alignItems:'center',justifyContent:'space-between'}}>
                          <span>Čeká na {selected}</span>
                          <span style={{background:'var(--danger)',color:'#fff',borderRadius:10,padding:'1px 7px',fontSize:11}}>{allOpenItems.length}</span>
                        </div>
                        {allOpenItems.map((it, i) => (
                          <div key={i} style={{display:'flex',alignItems:'flex-start',gap:8,padding:'6px 0',borderBottom:'1px solid var(--border)',cursor:'pointer'}} onClick={() => {
                            const note = notes.find(n => n.id === it.noteId);
                            if (note) toggleActionItem(note, it.idx);
                          }}>
                            <span className="onenon-action-check" style={{marginTop:2,flexShrink:0}} />
                            <div style={{flex:1}}>
                              <div style={{fontSize:13,color:'var(--text)'}}>{it.text}</div>
                              <div style={{fontSize:10,color:'var(--text-3)',marginTop:2}}>ze schůzky {it.from}</div>
                            </div>
                          </div>
                        ))}
                      </>
                    )}
                  </div>
                </div>

                {/* Splněno card */}
                {allDoneItems.length > 0 && (
                  <div className="onenon-splneno-card">
                    <div className="onenon-splneno-header">
                      <span>Splněno</span>
                      <span>{allDoneItems.length} splněno</span>
                    </div>
                    {allDoneItems.slice(0, 4).map((it, i) => (
                      <div key={i} className="onenon-splneno-item">{it.text}</div>
                    ))}
                    {allDoneItems.length > 4 && <div style={{fontSize:11,color:'var(--text-3)',padding:'4px 14px 8px'}}>… a {allDoneItems.length - 4} dalších</div>}
                  </div>
                )}

                {/* Mood bar chart */}
                {lastMoods.length >= 2 && (
                  <div className="onenon-col-card">
                    <div className="onenon-col-card-header">Nálada (poslední {lastMoods.length} schůzky)</div>
                    <div className="onenon-mood-chart">
                      <div className="onenon-mood-trend-label">
                        <span style={{fontWeight:700,color:moodTrend==='↑'?'var(--success)':moodTrend==='↓'?'var(--danger)':'var(--text-2)'}}>Trend: {moodTrend === '↑' ? '↑ stoupá' : moodTrend === '↓' ? '↓ klesá' : '→ stabilní'}</span>
                        {moodAvg && <span style={{color:'var(--text-2)'}}>avg {moodAvg}★</span>}
                      </div>
                      <div className="onenon-bars">
                        {[...lastMoods].reverse().map((m, i) => (
                          <div key={i} className="onenon-bar-wrap">
                            <div className="onenon-bar" style={{height: (m/5*40) + 'px', background: moodBarColor(m)}} />
                            <div className="onenon-bar-date">{[...moodDates].reverse()[i]}</div>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                )}
              </div>

              {/* Pravý sloupec: timeline zápisů */}
              <div style={{display:'flex',flexDirection:'column',minHeight:0}}>
                <div style={{display:'flex',alignItems:'center',justifyContent:'space-between',marginBottom:10,borderBottom:'1px solid var(--border)',paddingBottom:8}}>
                  <span style={{fontSize:11,fontWeight:700,textTransform:'uppercase',letterSpacing:'.05em',color:'var(--text-2)'}}>Timeline zápisů ({notes.length})</span>
                  <button style={{background:'none',border:'none',cursor:'pointer',fontSize:11,color:'var(--accent)',fontWeight:600,fontFamily:'var(--font)',padding:0}}>filtrovat</button>
                </div>
                {notes.length === 0 && <div style={{color:'var(--text-3)',fontSize:13}}>Zatím žádné záznamy</div>}
                {notes.map(n => {
                  const daysAgo = n.meeting_date ? Math.floor((Date.now() - new Date(n.meeting_date)) / 86400000) : null;
                  return (
                    <div key={n.id} className="onenon-note-card">
                      <div className="onenon-note-header">
                        <div>
                          <div style={{display:'flex',alignItems:'center',gap:8,marginBottom:3}}>
                            <span style={{fontSize:13,fontWeight:700,color:'var(--text)'}}>{n.meeting_date && new Date(n.meeting_date).toLocaleDateString('cs-CZ', {day:'numeric',month:'long',year:'numeric'})}</span>
                            {(n.tags || []).map(t => <span key={t} className={'onenon-tag-chip ' + ({výkon:'tag-vykon',rozvoj:'tag-rozvoj',osobní:'tag-osobni',SLA:'tag-sla',feedback:'tag-feedback'}[t]||'')}>{t}</span>)}
                          </div>
                          <div style={{display:'flex',alignItems:'center',gap:8}}>
                            {daysAgo !== null && <span style={{fontSize:11,color:'var(--text-3)'}}>{daysAgo} dní</span>}
                            {n.mood && <span style={{color:'#F59E0B',fontSize:13,letterSpacing:.5}}>{'★'.repeat(n.mood)}{'☆'.repeat(5-n.mood)}</span>}
                          </div>
                        </div>
                        <div style={{display:'flex',gap:8,alignItems:'center'}}>
                          <button className="onenon-note-link edit" onClick={() => setModal(n)}>Upravit</button>
                          <button className="onenon-note-link" onClick={() => handleDelete(n.id)}>Smazat</button>
                        </div>
                      </div>
                      {n.notes && <div style={{fontSize:13,color:'var(--text)',whiteSpace:'pre-wrap',marginBottom:8,lineHeight:1.6}}>{n.notes}</div>}
                      {(n.action_items || []).filter(it => it.done).map((it, idx) => (
                        <div key={idx} style={{display:'flex',alignItems:'center',gap:6,padding:'3px 0',fontSize:12}}>
                          <span style={{fontSize:11,color:'var(--success)'}}>✓</span>
                          <span style={{textDecoration:'line-through',color:'var(--text-3)'}}>{it.text}</span>
                        </div>
                      ))}
                      {(n.action_items || []).filter(it => !it.done).map((it, idx) => (
                        <div key={idx} className="onenon-action-item" onClick={() => toggleActionItem(n, (n.action_items || []).indexOf(it))}>
                          <span className="onenon-action-check" />
                          <span className="onenon-action-text">{it.text}</span>
                        </div>
                      ))}
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Bottom tabs */}
            <div className="onenon-bottom-tabs">
              <button className={'onenon-btab' + (contentTab === 'detail' ? ' active' : '')} onClick={() => setContentTab('detail')}>↑ 1on1 detail</button>
              <button className={'onenon-btab' + (contentTab === 'prep' ? ' active' : '')} onClick={() => { setContentTab('prep'); setPrepDoc(true); }}>📋 Prep modal</button>
            </div>
          </>
        )}
      </div>
      {modal !== null && <OneOnOneModal note={modal} agents={daktelaAgents} existingPeople={people.map(p => p.person)} onSave={handleSave} onClose={() => setModal(null)} />}
      {mappingModal && <OneOnOneMappingModal people={people} onClose={() => setMappingModal(false)} />}
    </div>
  );
}

// ---- OneOnOneMappingModal ----
function OneOnOneMappingModal({ people, onClose }) {
  const [events, setEvents] = React.useState([]);
  const [mappings, setMappings] = React.useState({});
  const [loading, setLoading] = React.useState(true);
  const [saving, setSaving] = React.useState(false);

  React.useEffect(() => {
    Promise.all([
      apiFetch('calendar', 'GET', null, { sub: 'onenon_scan' }),
      apiFetch('settings', 'GET', null, { sub: 'onenon_mappings' }),
    ]).then(function(results) {
      var calData = results[0];
      var mappingsData = results[1];
      setEvents(calData.events || []);
      var m = {};
      (mappingsData.mappings || []).forEach(function(r) { m[r.event_keyword] = r.person; });
      setMappings(m);
      setLoading(false);
    }).catch(function(err) {
      console.error('onenon_scan error', err);
      setLoading(false);
    });
  }, []);

  async function handleSave() {
    setSaving(true);
    var toSave = Object.entries(mappings)
      .filter(function(kv) { return kv[1] && kv[1] !== ''; })
      .map(function(kv) { return { event_keyword: kv[0], person: kv[1] }; });
    await apiFetch('settings', 'POST', { mappings: toSave }, { sub: 'onenon_mappings' });
    setSaving(false);
    toast('Mappings uloženy');
    onClose();
  }

  return (
    <div className="modal-overlay" onClick={function(e) { if (e.target === e.currentTarget) onClose(); }}>
      <div className="modal" style={{maxWidth:520}}>
        <h2 style={{marginBottom:4}}>Auto-tasky z kalendáře</h2>
        <p style={{fontSize:12,color:'var(--text-2)',marginBottom:16}}>
          Spáruj opakující se schůzky s lidmi. Den před schůzkou se automaticky vytvoří task "Připravit 1on1 s [osoba]".
        </p>
        {loading && <div style={{fontSize:13,color:'var(--text-2)',padding:'20px 0'}}>Načítám kalendář...</div>}
        {!loading && events.length === 0 && (
          <div style={{fontSize:13,color:'var(--text-2)'}}>Žádné eventy v příštích 30 dnech (nebo není připojen Google Calendar).</div>
        )}
        {!loading && events.map(function(e) {
          return (
            <div key={e.title} style={{display:'flex',alignItems:'center',gap:10,padding:'8px 0',borderBottom:'1px solid var(--border)'}}>
              <div style={{flex:1,fontSize:13,fontWeight:500,color:'var(--text)'}}>{e.title}</div>
              <select
                value={mappings[e.title] || ''}
                onChange={function(ev) {
                  var val = ev.target.value;
                  setMappings(function(prev) { var next = Object.assign({}, prev); next[e.title] = val; return next; });
                }}
                style={{fontSize:12,padding:'4px 8px',border:'1px solid var(--border)',borderRadius:'var(--radius-sm)',fontFamily:'var(--font)',color:'var(--text)',background:'var(--surface)'}}>
                <option value="">— ignorovat —</option>
                {people.map(function(p) { return <option key={p.person} value={p.person}>{p.person}</option>; })}
              </select>
            </div>
          );
        })}
        <div className="modal-actions" style={{marginTop:16}}>
          <button className="btn btn-secondary" onClick={onClose}>Zrušit</button>
          <button className="btn btn-primary" onClick={handleSave} disabled={saving}>
            {saving ? 'Ukládám...' : 'Uložit'}
          </button>
        </div>
      </div>
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
  const [doneTimeTask, setDoneTimeTask] = useState(null);
  const [clTodayDone, setClTodayDone] = useState(0);
  const [loading, setLoading] = useState(true);
  const [aiLoading, setAiLoading] = useState(false);

  const [activeTab, setActiveTab] = useState('dnes'); // 'all' | 'work' | 'personal' | 'dnes' | 'history' | 'onenon'
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
    const sidebarToggleBtn = document.getElementById('sidebarToggle'); if (sidebarToggleBtn) sidebarToggleBtn.onclick = toggleSidebar;
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
    if (newStatus === 'done') {
      setTodayDone(v => v + 1);
      setDoneTimeTask(result.task);
    } else {
      setTodayDone(v => Math.max(0, v - 1));
    }
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

  async function handleBatchAddToDaily(ids) {
    if (!ids || !ids.length) return;
    const open = tasks.filter(t => t.status === 'open' && t.daily_order === null);
    const toAdd = tasks.filter(t => ids.includes(t.id) && (t.daily_order === null || t.daily_order === undefined));
    const maxOrder = tasks.reduce((m, t) => t.daily_order !== null && t.daily_order !== undefined ? Math.max(m, t.daily_order) : m, -1);
    await Promise.all(toAdd.map((t, i) => apiFetch('tasks', 'PUT', { daily_order: maxOrder + 1 + i }, { id: t.id })));
    await loadTasks();
  }

  async function handleRemoveFromDaily(task) {
    const result = await apiFetch('tasks', 'PUT', { daily_order: null }, { id: task.id });
    setTasks(prev => prev.map(t => t.id === task.id ? result.task : t));
  }

  async function handleBatchRemoveFromDaily(ids) {
    if (!ids || !ids.length) return;
    await Promise.all(ids.map(id => apiFetch('tasks', 'PUT', { daily_order: null }, { id })));
    await loadTasks();
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
      {/* Header search */}
      {ReactDOM.createPortal(
        <>
          <input
            type="search"
            value={searchQuery}
            onChange={e => setSearchQuery(e.target.value)}
            placeholder="Hledat tasky…"
          />
          <span className="header-search-kbd">⌘K</span>
        </>,
        document.getElementById('headerSearch')
      )}

      {/* Header actions */}
      {ReactDOM.createPortal(
        <div style={{display:'flex',gap:8,alignItems:'center'}}>
          {q1DeadlineTasks.length > 0 && <Q1AlertBadge tasks={q1DeadlineTasks} onEditTask={handleEditTask} />}
          <button className="btn btn-ghost" onClick={handleAiSuggest} disabled={aiLoading} style={{fontSize:'13px',display:'flex',alignItems:'center',gap:'5px'}}>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            {aiLoading ? '...' : 'AI'}
          </button>
          <button className="btn btn-primary" onClick={() => setModal({ type: 'task' })} style={{display:'flex',alignItems:'center',gap:'5px'}}>
            <svg width="11" height="11" viewBox="0 0 12 12" fill="currentColor"><path d="M6 1v10M1 6h10"/></svg>
            Nový task
          </button>
          <div style={{width:30,height:30,borderRadius:'50%',background:'linear-gradient(135deg, #1B3468, #2563EB)',color:'#fff',display:'flex',alignItems:'center',justifyContent:'center',fontSize:'11px',fontWeight:700,cursor:'default',flexShrink:0,userSelect:'none'}} title={CURRENT_USER}>
            {(CURRENT_USER||'?').slice(0,2).toUpperCase()}
          </div>
        </div>,
        document.getElementById('headerActions')
      )}

      {/* Bottom tab bar */}
      {ReactDOM.createPortal(
        <div className="bottom-tabs" style={{display: activeTab === 'onenon' ? 'none' : undefined}}>
          {[
            { key: 'dnes', label: '✦ Dnes' },
            { key: 'all', label: '# Matice' },
            { key: 'morning', label: '🌅 Ranní rituál' },
          ].map(t => (
            <button
              key={t.key}
              className={'bottom-tab' + (activeTab === t.key || (t.key === 'all' && ['all','work','personal'].includes(activeTab)) ? ' active' : '')}
              onClick={() => setActiveTab(t.key)}
            >{t.label}</button>
          ))}
        </div>,
        document.getElementById('tabBar')
      )}

      {/* Nav sidebar */}
      {ReactDOM.createPortal(
        <NavSidebar activeTab={activeTab} onTab={tab => {
          if (tab === 'settings') { setModal({ type: 'settings' }); return; }
          if (tab === 'quickcapture') { setQuickCapture(true); return; }
          if (tab === 'logout') { apiFetch('logout', 'POST').then(() => { window.location.href = '/tasks/login.php'; }); return; }
          if (tab === 'chat') return;
          setActiveTab(tab);
        }} />,
        document.getElementById('navSidebar')
      )}

      {/* Levý sidebar — skrytý */}
      {ReactDOM.createPortal(<></>, document.getElementById('sidebarLeft'))}

      {/* Pravý sidebar: KPI + Checklist + Daktela + Calendar + Chat */}
      {ReactDOM.createPortal(
        <>
          <KpiPanel todayDone={todayDone + clTodayDone} totalOpen={totalOpen} />
          <ChecklistPanel
            items={checklistItems}
            todayDone={clTodayDone}
            onAdd={handleAddCl}
            onToggle={handleToggleCl}
            onDelete={handleDeleteCl}
            onEdit={handleEditCl}
          />
          <DaktelaPanel
            tickets={daktelaTickets}
            refreshedAt={daktelaRefreshedAt}
            token={daktelaToken}
            onConnectClick={() => setModal({ type: 'daktela' })}
            onRefresh={refreshDaktelaCache}
            onCreateTask={handleDaktelaCreateTask}
            assignedMap={assignedMap}
          />
          <ChatPanel />
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
          : activeTab === 'morning'
          ? <DnesView tasks={tasks} calEvents={calEvents} onToggleDone={handleToggleDone} onEdit={handleEditTask} onRemoveFromDaily={handleRemoveFromDaily} onReorder={handleDnesReorder} onBatchAddToDaily={handleBatchAddToDaily} onBatchRemoveFromDaily={handleBatchRemoveFromDaily} forceShowMorning={true} onForceDone={() => setActiveTab('dnes')} />
          : activeTab === 'dnes'
          ? <DnesView tasks={tasks} calEvents={calEvents} onToggleDone={handleToggleDone} onEdit={handleEditTask} onRemoveFromDaily={handleRemoveFromDaily} onReorder={handleDnesReorder} onBatchAddToDaily={handleBatchAddToDaily} onBatchRemoveFromDaily={handleBatchRemoveFromDaily} />
          : (
            <>
              <div style={{display:'flex',alignItems:'center',justifyContent:'space-between',marginBottom:'16px'}}>
                <div style={{fontSize:'20px',fontWeight:700,color:'var(--text)'}}>Eisenhowerova matice</div>
                <div style={{display:'flex',gap:'8px',alignItems:'center'}}>
                  {[{key:'work',label:'Pracovní'},{key:'all',label:'Vše'}].map(t => (
                    <button key={t.key} onClick={() => setActiveTab(t.key)}
                      style={{fontSize:'12px',padding:'6px 12px',border:'1px solid var(--border)',borderRadius:'var(--radius-sm)',background: activeTab===t.key ? 'var(--accent-bg)' : '#fff',color: activeTab===t.key ? 'var(--accent)' : 'var(--text-2)',fontFamily:'var(--font)',cursor:'pointer',fontWeight: activeTab===t.key ? 600 : 400}}>
                      {t.label}
                    </button>
                  ))}
                  <button className="btn btn-primary" onClick={handleAiSuggest} disabled={aiLoading}
                    style={{fontSize:'12px',padding:'6px 14px',display:'flex',alignItems:'center',gap:'5px'}}>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    {aiLoading ? 'Analyzuji...' : 'AI návrh'}
                  </button>
                </div>
              </div>
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

            </>
          ),
        document.getElementById('mainContent')
      )}

      {/* Modals — portal do body kvůli stacking context na mobilu */}
      {doneTimeTask && ReactDOM.createPortal(
        <DoneTimeModal
          task={doneTimeTask}
          onSave={async (minutes) => {
            if (minutes) await apiFetch('tasks', 'PUT', { actual_minutes: minutes }, { id: doneTimeTask.id });
            setDoneTimeTask(null);
            toast('✓ Hotovo!');
          }}
          onSkip={() => { setDoneTimeTask(null); toast('✓ Hotovo!'); }}
        />,
        document.body
      )}
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
          <div style={{textAlign:'center'}}><div className="spinner" style={{margin:'0 auto'}}></div><div style={{marginTop:10,fontSize:12,color:'var(--text-2)',fontWeight:500}}>Načítám...</div></div>
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
