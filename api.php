<?php
header("Content-Type: application/json");
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(["status" => "error", "message" => "Unauthorized"]));
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    die(json_encode(["status" => "error", "message" => "Invalid Payload"]));
}

try {
    $pdo = getDB();
    
    // 1. Insert into Database
    $sql = "INSERT INTO trades (pair, bias, entry_price, stop_loss, take_profit, lot_size, smc_grade) 
            VALUES (:pair, :bias, :entry, :sl, :tp, :lotSize, :grade)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':pair'    => $data['pair'],
        ':bias'    => $data['bias'],
        ':entry'   => $data['entry'],
        ':sl'      => $data['sl'],
        ':tp'      => $data['tp'],
        ':lotSize' => $data['lotSize'],
        ':grade'   => $data['grade']
    ]);
    
    $tradeId = $pdo->lastInsertId();

    // 2. Prepare Telegram Notification
    $status_emoji = $data['grade'] >= 70 ? "✅ HIGH PROBABILITY" : "⚠️ LOW PROBABILITY";
    $msg = "🏛️ *INSTITUTIONAL ENTRY LOGGED*\n\n"
         . "Asset: `{$data['pair']}`\n"
         . "Bias: *{$data['bias']}*\n"
         . "Entry: `{$data['entry']}`\n"
         . "SL: `{$data['sl']}`\n"
         . "TP: `{$data['tp']}`\n\n"
         . "Grade: `{$data['grade']}%` {$status_emoji}\n"
         . "Risk: `{$data['lotSize']} Lots`\n\n"
         . "📱 _Reply to this message with TP, SL, or BE to update status._";

    $tgUrl = "https://api.telegram.org/bot" . TG_BOT_TOKEN . "/sendMessage";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tgUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'chat_id' => TG_CHAT_ID,
        'text' => $msg,
        'parse_mode' => 'Markdown'
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tgResponse = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // 3. Update with Message ID for future status updates
    if (isset($tgResponse['ok']) && $tgResponse['ok']) {
        $msgId = $tgResponse['result']['message_id'];
        $pdo->prepare("UPDATE trades SET tg_message_id = ? WHERE id = ?")->execute([$msgId, $tradeId]);
    }

    echo json_encode(["status" => "success", "trade_id" => $tradeId]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
