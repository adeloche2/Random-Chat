<?php
require_once __DIR__ . '/../includes/functions.php';
$userId = current_user_id();
if ($userId) {
    end_current_chat($userId);
    db()->prepare('UPDATE users SET status = "offline", room_id = NULL, is_typing = 0 WHERE id = ?')->execute([$userId]);
}
clear_current_user();
json_response(['success' => true]);
