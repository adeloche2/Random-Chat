<?php
require_once __DIR__ . '/../includes/functions.php';
$userId = get_or_create_user();
expire_old_rooms();
$room = active_room_for_user($userId);
if (!$room) {
    json_response(['success' => true, 'status' => 'waiting', 'messages' => [], 'typing' => false, 'remaining_seconds' => 0]);
}
$afterId = max(0, (int) ($_GET['after_id'] ?? 0));
$stmt = db()->prepare('SELECT id, user_id, message, created_at FROM messages WHERE room_id = ? AND id > ? ORDER BY id ASC');
$stmt->execute([$room['id'], $afterId]);
$typingStmt = db()->prepare('SELECT COUNT(*) AS typing_count FROM users WHERE room_id = ? AND id <> ? AND is_typing = 1');
$typingStmt->execute([$room['id'], $userId]);
$elapsed = time() - strtotime($room['started_at']);
$remaining = max(0, app_config()['chat_duration_seconds'] - $elapsed);
json_response([
    'success' => true,
    'status' => 'chatting',
    'room_id' => (int) $room['id'],
    'messages' => $stmt->fetchAll(),
    'typing' => ((int) $typingStmt->fetch()['typing_count']) > 0,
    'remaining_seconds' => $remaining,
]);
