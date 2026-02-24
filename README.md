# 🏛️ SMC Gatekeeper - Institutional Trade Engine

A high-performance PWA designed for SMC (Smart Money Concepts) traders to validate, log, and monitor institutional orderflow. This system prevents "emotional entries" by applying a strict 50% penalty to any trade that violates the Premium vs. Discount (Equilibrium) rule.

## 🚀 Features

- **Dynamic Dealing Range Engine**: Automatically calculates Equilibrium. Flags and penalizes entries in the wrong zone.
- **Confluence Metric**: 100-point weighting system (HTF, POI, Liquidity, MSS).
- **Automated Lot Sizing**: Real-time risk-based calculation.
- **PWA Ready**: Installable on iOS/Android as a standalone app.
- **Telegram Vault Integration**: 
    - Logs trades to a MySQL DB.
    - Sends instant Markdown entry alerts.
    - **Synchronous Webhooks**: Reply to the Telegram message with `TP`, `SL`, or `BE` to update the DB status.

## 🛠️ Prerequisites

- **Web Server**: Apache/Nginx with PHP 7.4+ support.
- **Database**: MySQL/MariaDB.
- **Bot**: A Telegram Bot Token from `@BotFather`.
- **SSL**: Required for PWA features and Telegram Webhooks.

## 📦 Installation

1. **Database Setup**:
    - Import `database.sql` into your PHPMyAdmin or MySQL CLI.

2. **Configuration**:
    - Open `config.php`.
    - Set your DB credentials.
    - Add your `TG_BOT_TOKEN`.
    - Add your `TG_CHAT_ID`.

3. **Upload**:
    - Upload all files to your web server directory.

4. **Set Webhook (Optional for Status Updates)**:
    - Run this in your browser: `https://api.telegram.org/botYOUR_BOT_TOKEN/setWebhook?url=https://yourdomain.com/webhook.php`

## 🕹️ How to Use

1. **Define the Range**: Enter the HTF Range High and Range Low.
2. **Execute Engine**: Input your current Entry and Stop Loss.
3. **Validate**: Toggle checkboxes for institutional confluence.
4. **Commit**: Click 'Commit Entry'. If your bias is LONG but your price is in PREMIUM (>50%), the engine will crash your 'Discipline Grade' by 50%.
5. **Manage**: When the trade hits a level, find the message in Telegram and reply with 'TP'. The vault will update automatically.

## 📁 Project Structure

- `index.html`: The UI Engine (JS + Tailwind).
- `api.php`: Handles data storage and Telegram notification logic.
- `webhook.php`: Listens for Telegram replies to update trade status.
- `config.php`: Central settings.
- `database.sql`: MySQL structure.
- `manifest.json`: PWA metadata.

## ⚠️ Troubleshooting

- **Telegram notifications not sending**: Ensure `curl` is enabled in PHP and your `chat_id` and `token` are correct.
- **Grade showing 0%**: Ensure you have checked at least one confluence box.
- **PWA not installing**: Ensure you are accessing the app over HTTPS.
