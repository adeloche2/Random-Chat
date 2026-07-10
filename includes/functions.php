<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/security.php';

function app_config(): array
{
    static $config;
    if ($config === null) {
        $config = require __DIR__ . '/../config/config.php';
    }
    return $config;
}

function db(): PDO
{
    static $pdo;
    if ($pdo === null) {
        $pdo = require __DIR__ . '/../config/database.php';
    }
    return $pdo;
}

function create_temporary_user(): int
{
    $token = bin2hex(random_bytes(16));
    $stmt = db()->prepare('INSERT INTO users (session_token, status, last_seen) VALUES (?, "online", NOW())');
    $stmt->execute([$token]);
    return (int) db()->lastInsertId();
}

function get_or_create_user(): int
{
    $userId = current_user_id();
    if ($userId) {
        db()->prepare('UPDATE users SET status = "online", last_seen = NOW() WHERE id = ?')->execute([$userId]);
        return $userId;
    }
    $userId = create_temporary_user();
    set_current_user($userId);
    return $userId;
}

function expire_old_rooms(): void
{
    $config = app_config();
    db()->prepare('UPDATE rooms SET status = "ended", ended_at = NOW() WHERE status = "active" AND (TIMESTAMPDIFF(SECOND, started_at, NOW()) >= ? OR id IN (SELECT room_id FROM users WHERE room_id IS NOT NULL AND TIMESTAMPDIFF(SECOND, last_seen, NOW()) > ?))')
        ->execute([$config['chat_duration_seconds'], $config['heartbeat_timeout_seconds']]);
    db()->exec('UPDATE users SET status = "offline", room_id = NULL WHERE TIMESTAMPDIFF(SECOND, last_seen, NOW()) > 20');
}

function find_or_create_room(int $userId): array
{
    expire_old_rooms();
    $pdo = db();
    $pdo->beginTransaction();
    $waiting = $pdo->prepare('SELECT id FROM users WHERE status = "waiting" AND id <> ? AND room_id IS NULL ORDER BY RAND() LIMIT 1 FOR UPDATE');
    $waiting->execute([$userId]);
    $partner = $waiting->fetch();

    if ($partner) {
        $roomStmt = $pdo->prepare('INSERT INTO rooms (user_one_id, user_two_id, status, started_at) VALUES (?, ?, "active", NOW())');
        $roomStmt->execute([$partner['id'], $userId]);
        $roomId = (int) $pdo->lastInsertId();
        $pdo->prepare('UPDATE users SET status = "chatting", room_id = ? WHERE id IN (?, ?)')->execute([$roomId, $partner['id'], $userId]);
        $pdo->commit();
        return ['status' => 'matched', 'room_id' => $roomId];
    }

    $pdo->prepare('UPDATE users SET status = "waiting", room_id = NULL, last_seen = NOW() WHERE id = ?')->execute([$userId]);
    $pdo->commit();
    return ['status' => 'waiting', 'room_id' => null];
}

function active_room_for_user(int $userId): ?array
{
    $stmt = db()->prepare('SELECT r.* FROM rooms r WHERE r.status = "active" AND (r.user_one_id = ? OR r.user_two_id = ?) LIMIT 1');
    $stmt->execute([$userId, $userId]);
    return $stmt->fetch() ?: null;
}

function end_current_chat(int $userId): void
{
    $room = active_room_for_user($userId);
    if ($room) {
        db()->prepare('UPDATE rooms SET status = "ended", ended_at = NOW() WHERE id = ?')->execute([$room['id']]);
        db()->prepare('UPDATE users SET status = "online", room_id = NULL, is_typing = 0 WHERE room_id = ?')->execute([$room['id']]);
    }
}
