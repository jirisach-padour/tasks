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
    'ai_suggest'     => require __DIR__ . '/api/ai.php',
    'onenon'         => require __DIR__ . '/api/onenon.php',
    'logout'         => (function() {
        session_destroy();
        echo json_encode(['ok' => true]);
    })(),
    default => (function() {
        http_response_code(404);
        echo json_encode(['error' => 'Unknown action']);
    })(),
};
