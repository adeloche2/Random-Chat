<?php
require_once __DIR__ . '/../includes/functions.php';
$userId = get_or_create_user();
expire_old_rooms();
db()->prepare('UPDATE users SET last_seen = NOW() WHERE id = ?')->execute([$userId]);
$room = active_room_for_user($userId);
json_response(['success' => true, 'status' => $room ? 'chatting' : 'waiting', 'room_id' => $room['id'] ?? null]);
