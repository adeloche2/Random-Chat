# Random-Chat

Random-Chat is a random anonymous chat application built with PHP, MySQL, AJAX, and Bootstrap. It creates temporary users, places them in a waiting queue, matches two strangers into a private room, and ends chats after 10 minutes or when either user exits.

## Features

- MySQL schema for users, random rooms, and messages
- Environment-based MySQL connection configuration
- Temporary anonymous user sessions
- Waiting queue and random matching
- Private chat rooms with a 10-minute duration
- AJAX message sending and polling
- "Stranger is typing now..." indicator
- "Someone else" button to end the current chat and find a new match
- Heartbeat and disconnect handling when a user leaves
- Admin dashboard for basic statistics and recent room management visibility

## Project Structure

```text
Random-Chat/
├── index.php
├── .htaccess
├── README.md
├── admin/
│   └── index.php
├── config/
│   ├── config.php
│   └── database.php
├── database/
│   └── random_chat.sql
├── api/
│   ├── connect.php
│   ├── disconnect.php
│   ├── send.php
│   ├── receive.php
│   ├── next.php
│   ├── typing.php
│   └── heartbeat.php
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   ├── js/ajax.js
│   └── img/
├── includes/
│   ├── functions.php
│   ├── session.php
│   └── security.php
├── storage/logs/
└── uploads/
```

## Requirements

- PHP 7.4 or newer with PDO MySQL enabled
- MySQL or MariaDB
- A PHP-capable web server such as Apache or Nginx

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/adeloche2/Random-Chat.git
   cd Random-Chat
   ```

2. Create the database and tables:

   ```bash
   mysql -u root -p < database/random_chat.sql
   ```

3. Configure MySQL connection values with environment variables, or edit `config/database.php` for local development:

   ```bash
   export DB_HOST=127.0.0.1
   export DB_PORT=3306
   export DB_DATABASE=random_chat
   export DB_USERNAME=root
   export DB_PASSWORD=secret
   ```

4. Set admin credentials before using the dashboard:

   ```bash
   export ADMIN_USERNAME=admin
   export ADMIN_PASSWORD='change-this-password'
   ```

5. Serve the application from your web server document root, then open `index.php` in two browser sessions to test matching and messaging.

## Admin Dashboard

Open `/admin/index.php` and log in with the configured admin credentials. The dashboard shows total users, waiting users, active rooms, total messages, and the most recent rooms.

## Security Notes

- Change the default admin password before deployment.
- Keep database credentials in environment variables in production.
- Use HTTPS so session cookies and messages are not sent over plain text.

## License

No license file is currently included. Add a license before distributing or reusing this project publicly.
