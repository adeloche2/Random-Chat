<?php
require_once __DIR__ . '/includes/functions.php';
start_chat_session();
$config = app_config();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($config['app_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<main class="chat-shell container py-4">
    <section class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-0">Random-Chat</h1>
                <small id="status" class="text-muted">Ready to connect</small>
            </div>
            <span id="timer" class="badge text-bg-primary">10:00</span>
        </div>
        <div id="messages" class="card-body messages" aria-live="polite"></div>
        <div class="px-3 pb-2"><em id="typing" class="text-muted d-none">Stranger is typing now...</em></div>
        <form id="chat-form" class="card-footer d-flex gap-2">
            <input id="message" class="form-control" maxlength="1000" autocomplete="off" placeholder="Type a message" disabled>
            <button class="btn btn-primary" type="submit" disabled>Send</button>
            <button id="next" class="btn btn-outline-secondary" type="button">Someone else</button>
            <button id="disconnect" class="btn btn-outline-danger" type="button">Exit</button>
        </form>
    </section>
</main>
<script src="assets/js/ajax.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
