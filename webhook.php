<?php
require_once 'config.php';

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update || !isset($update['message']['reply_to_message'])) {
    exit;
}

$replyText = strtoupper(trim($update['message']['text']));
$parentMsgId = $update['message']['reply_to_message']['message_id'];
$chatId = $update['message']['chat']['id'];

// Check if valid status command
$validStatuses = ['TP', 'SL', 'BE'];
if (!in_array($replyText, $validStatuses)) {
    exit;
}

try {
    $pdo = getDB();
    
    // Find trade by msg_id
    $stmt = $pdo->prepare("SELECT id, pair FROM trades WHERE tg_message_id = ?");
    $stmt->execute([$parentMsgId]);
    $trade = $stmt->fetch();

    if ($trade) {
        // Update Status
        $updateStmt = $pdo->prepare("UPDATE trades SET status = ? WHERE id = ?");
        if ($updateStmt->execute([$replyText, $trade['id']])) {
            
            // Confirm to User
            $confirmation = "✔️ Trade #{$trade['id']} ({$trade['pair']}) updated to: *{$replyText}*";
            file_get_contents("https://api.telegram.org/bot" . TG_BOT_TOKEN . "/sendMessage?chat_id={$chatId}&text=" . urlencode($confirmation) . "&parse_mode=Markdown");
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}
