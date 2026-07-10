<?php
require_once __DIR__ . '/../includes/functions.php';
$userId = get_or_create_user();
$result = find_or_create_room($userId);
json_response(['success' => true, 'user_id' => $userId] + $result);
