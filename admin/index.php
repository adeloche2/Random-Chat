<?php
require_once __DIR__ . '/../includes/functions.php';
start_chat_session();
$config = app_config();
$authenticated = isset($_SESSION['admin_authenticated']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authenticated = hash_equals($config['admin_username'], $_POST['username'] ?? '')
        && hash_equals($config['admin_password'], $_POST['password'] ?? '');
    $_SESSION['admin_authenticated'] = $authenticated;
}
if (!$authenticated): ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Admin Login</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="container py-5"><form method="post" class="card card-body mx-auto" style="max-width:400px"><h1 class="h4">Admin Login</h1><input class="form-control mb-2" name="username" placeholder="Username"><input class="form-control mb-3" name="password" type="password" placeholder="Password"><button class="btn btn-primary">Login</button></form></body></html>
<?php exit; endif;
$stats = [
    'users' => db()->query('SELECT COUNT(*) AS total FROM users')->fetch()['total'],
    'waiting' => db()->query('SELECT COUNT(*) AS total FROM users WHERE status = "waiting"')->fetch()['total'],
    'active_rooms' => db()->query('SELECT COUNT(*) AS total FROM rooms WHERE status = "active"')->fetch()['total'],
    'messages' => db()->query('SELECT COUNT(*) AS total FROM messages')->fetch()['total'],
];
$rooms = db()->query('SELECT id, user_one_id, user_two_id, status, started_at, ended_at FROM rooms ORDER BY id DESC LIMIT 25')->fetchAll();
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Random-Chat Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="container py-4"><h1>Random-Chat Admin</h1><div class="row g-3 my-3">
<?php foreach ($stats as $label => $value): ?><div class="col-md-3"><div class="card card-body"><strong><?= e(ucwords(str_replace('_', ' ', $label))) ?></strong><span class="display-6"><?= e((string) $value) ?></span></div></div><?php endforeach; ?>
</div><h2 class="h4">Recent Rooms</h2><table class="table table-striped"><thead><tr><th>ID</th><th>User 1</th><th>User 2</th><th>Status</th><th>Started</th><th>Ended</th></tr></thead><tbody>
<?php foreach ($rooms as $room): ?><tr><td><?= e((string) $room['id']) ?></td><td><?= e((string) $room['user_one_id']) ?></td><td><?= e((string) $room['user_two_id']) ?></td><td><?= e($room['status']) ?></td><td><?= e($room['started_at']) ?></td><td><?= e((string) $room['ended_at']) ?></td></tr><?php endforeach; ?>
</tbody></table></body></html>
