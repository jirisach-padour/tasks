<?php
require_once __DIR__ . '/config.php';
requireAuth();
require_once __DIR__ . '/lib/DB.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';

match($action) {
    'tasks'          => require __DIR__ . '/api/tasks.php',
    'checklist'      => require __DIR__ . '/api/checklist.php',
    'daktela_login'  => require __DIR__ . '/api/daktela.php',
    'daktela'        => require __DIR__ . '/api/daktela.php',
    'daktela_cache'  => require __DIR__ . '/api/daktela.php',
    'calendar'       => require __DIR__ . '/api/calendar.php',
    // 29. 10.: AI funkce (Anthropic API) zrušeny, 1on1 se přesunulo do Priority na operations
    'ai_suggest', 'what_now', 'prep_topics', 'chat', 'onenon' => (function() {
        http_response_code(410);
        echo json_encode(['error' => 'Zrušeno: 1on1 je v Priority na operations, AI funkce nejsou.']);
    })(),
    'settings'        => require __DIR__ . '/api/settings.php',
    'todo'           => (function() {
        $md = file_get_contents(__DIR__ . '/TODO.md');
        echo json_encode(['content' => $md]);
    })(),
    'logout'         => (function() {
        session_destroy();
        echo json_encode(['ok' => true]);
    })(),
    default => (function() {
        http_response_code(404);
        echo json_encode(['error' => 'Unknown action']);
    })(),
};
