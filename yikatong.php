<?php

// 设置时区，确保时间准确
date_default_timezone_set('Asia/Shanghai');

// 自动创建日志目录
$logDir = __DIR__ . '/log';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/activity.log';

/**
 * 记录日志函数
 */
function writeLog($message) {
    global $logFile;
    $time = date('Y-m-d H:i:s');
    $content = "[$time] $message" . PHP_EOL;
    file_put_contents($logFile, $content, FILE_APPEND);
    echo $content;
}

// 账号配置 (建议单独放在一个 json 文件中管理)
$accounts = [
    [
        'cookie' => '你的Cookie1',
        'data' => ["cityCode" => "310100", "activityNo" => "2925c", "unionId" => "...", "traceId" => "...", "os" => "android"]
    ],
    // 更多账号...
];

$url = 'https://cstapp.17u.cn/cstapi/activity/sign/checkIn';

writeLog(">>> 脚本开始，共 " . count($accounts) . " 个账号。");

foreach ($accounts as $index => $account) {
    $accountNum = $index + 1;
    
    $ch = curl_init($url);
    $headers = [
        'User-Agent: Mozilla/5.0 (Linux; Android 13; 2112123AC Build/TKQ1.221114.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/146.0.7680.164 Mobile Safari/537.36; os/android; cstTravelApp; CstVer/1.3.2; cstChannel/XIAOMI;',
        'Content-Type: application/json',
        'Cookie: ' . $account['cookie'],
        'x-sign: 1123VWR5D6OBCc1rYXJdHZpxli0rC2HEnvT6/SrEbhbo/3FTvXoTAwI5LdQJmkqPABgO4D+Rw5kn98aboHc+3Bt4q6ZDs8flPr/B6LHXaaN+gAolK59agtPL3IKmBBg7Um2bKAC3pamyZioeNIzUgTCK8f3YTAAovp+21s64oRA='
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($account['data']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    if ($error) {
        writeLog("账号 {$accountNum} 发生网络错误: " . $error);
    } else {
        $resData = json_decode($response, true);
        
        // 核心判断逻辑
        if ($resData && isset($resData['success'])) {
            if ($resData['success'] === true) {
                writeLog("账号 {$accountNum} 签到成功！");
            } else {
                // 处理失败情况 (包含重复签到)
                $msg = $resData['message'] ?? '未知错误';
                $code = $resData['code'] ?? '无错误码';
                writeLog("账号 {$accountNum} 签到结果: [{$code}] {$msg}");
            }
        } else {
            writeLog("账号 {$accountNum} 响应解析失败: " . $response);
        }
    }

    curl_close($ch);
    if ($index < count($accounts) - 1) sleep(2); 
}

writeLog(">>> 所有任务执行完毕。" . PHP_EOL);