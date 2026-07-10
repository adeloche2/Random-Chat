<?php
require_once __DIR__ . '/../includes/functions.php';
$userId = get_or_create_user();
$room = active_room_for_user($userId);
$message = clean_message($_POST['message'] ?? '');
if (!$room) {
    json_response(['success' => false, 'error' => 'No active room'], 409);
}
if ($message === '' || mb_strlen($message) > 1000) {
    json_response(['success' => false, 'error' => 'Message must be between 1 and 1000 characters'], 422);
}
db()->prepare('INSERT INTO messages (room_id, user_id, message) VALUES (?, ?, ?)')->execute([$room['id'], $userId, $message]);
db()->prepare('UPDATE users SET is_typing = 0, last_seen = NOW() WHERE id = ?')->execute([$userId]);
json_response(['success' => true]);
