<?php
return [
    'app_name' => 'Random-Chat',
    'base_url' => '',
    'chat_duration_seconds' => 600,
    'heartbeat_timeout_seconds' => 20,
    'admin_username' => getenv('ADMIN_USERNAME') ?: 'admin',
    'admin_password' => getenv('ADMIN_PASSWORD') ?: 'change-me',
];
