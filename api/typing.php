<?php
require_once __DIR__ . '/../includes/functions.php';
$userId = get_or_create_user();
$isTyping = ($_POST['typing'] ?? '0') === '1';
db()->prepare('UPDATE users SET is_typing = ?, last_seen = NOW() WHERE id = ?')->execute([$isTyping ? 1 : 0, $userId]);
json_response(['success' => true]);
