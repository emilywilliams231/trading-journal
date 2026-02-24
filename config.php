<?php
// Configuration File - Update with your credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'smc_vault');
define('DB_USER', 'root');
define('DB_PASS', '');

// Telegram Bot Configuration
// To get your Chat ID: Message @userinfobot or check @IDBot
define('TG_BOT_TOKEN', 'YOUR_BOT_TOKEN_HERE');
define('TG_CHAT_ID', 'YOUR_CHAT_ID_HERE');

/**
 * PDO Connection Singleton
 */
function getDB() {
    static $pdo;
    if (!$pdo) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die(json_encode(["status" => "error", "message" => "DB Connection Failed"]));
        }
    }
    return $pdo;
}
