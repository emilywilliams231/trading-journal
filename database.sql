CREATE DATABASE IF NOT EXISTS smc_vault;
USE smc_vault;

CREATE TABLE IF NOT EXISTS trades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pair VARCHAR(20) NOT NULL,
    bias ENUM('LONG', 'SHORT') NOT NULL,
    entry_price DECIMAL(18, 8) NOT NULL,
    stop_loss DECIMAL(18, 8) NOT NULL,
    take_profit DECIMAL(18, 8) NOT NULL,
    lot_size DECIMAL(10, 2) NOT NULL,
    smc_grade INT NOT NULL,
    status ENUM('PENDING', 'TP', 'SL', 'BE') DEFAULT 'PENDING',
    tg_message_id VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Index for fast lookup via Telegram Webhook
CREATE INDEX idx_tg_msg ON trades(tg_message_id);
