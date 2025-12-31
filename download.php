<?php
// Telegram settings
$botToken = '8504534618:AAGlMKtUFposJ4pt3FpOsgDX9khJ6SxZvuM';
$chatId = '-1003380435747';

// Get user IP
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$time = date('Y-m-d H:i:s');

// File to store IP logs
$logFile = 'ip_log.txt';

// Check if this IP downloaded in last 24 hours
function shouldSendLog($ip, $logFile) {
    if (!file_exists($logFile)) {
        return true;
    }
    
    $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $currentTime = time();
    
    foreach ($logs as $log) {
        list($loggedIp, $loggedTime) = explode('|', $log);
        if ($loggedIp === $ip) {
            // Check if more than 24 hours passed
            if (($currentTime - intval($loggedTime)) < 86400) { // 86400 seconds = 24 hours
                return false; // Don't send log
            }
        }
    }
    return true; // Send log
}

// Send log only if IP is new or 24h passed
if (shouldSendLog($ip, $logFile)) {
    // Telegram message (ENGLISH ONLY - short)
    $message = "⬇️ Download\nIP: $ip\nTime: $time";
    
    // Send to Telegram
    $telegramUrl = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);
    @file_get_contents($telegramUrl);
    
    // Log this IP with timestamp
    $logEntry = $ip . '|' . time() . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Download APK file
$file = 'Base.apk';

if (file_exists($file)) {
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="joratx.apk"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    echo "فایل یافت نشد";
}
?>
