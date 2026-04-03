<?php

// 设置默认时区为中国时间（如果需要其他时区可修改）
date_default_timezone_set('Asia/Shanghai');

/**
 * 写入日志函数
 * @param string $message 要记录的日志内容
 */
function writeLog($message) {
    // 日志文件路径：保存在与此脚本同级的目录下，文件名为 server_check.log
    $logFile = __DIR__ . '/live_check.log';
    
    // 获取当前时间戳
    $time = date('Y-m-d H:i:s');
    
    // 组合日志格式： [时间] 日志内容 + 换行符
    $logEntry = "[{$time}] " . trim($message) . PHP_EOL;
    
    // 写入文件（FILE_APPEND 表示追加，不会覆盖之前的日志）
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// 目标网址
$url = "https://betadash.lunes.host/servers/74653";

$ch = curl_init($url);

$headers = array(
    "Content-Type: application/json",
    "Cookie: session=x-Or82yV0rzVFPlZcxtrr3EXqXdkJN8Rzu9nNCgcmHA; cf_clearance=8sH.pQjv03FJ3LlI.vb2JTZhRY_xZpC8uJXMCzF8FMA-1775199387-1.2.1.1-3.rb_icbDq1Vb1p018u2om16NACv0KgvzjMMiuVELsTPN.FUqTbHlj_TVJ6Ea.CS9n.rpbEsZiJS2.QI6Y4TXCM71KtJd3l4YG1_5fYmGZnfTGI4f7l9EDNpUOlLdH4vKKMK_ecl60CiwSj61fEC.Rc7jODJt4g.heJUIUXJuW5Ec6xA4XuRC6B4rBrOhiJFYu.Ur_biynzn_li2phXXfy_c20uIk2ae07isGYX_I_I"
);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

// 忽略 SSL 证书验证
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// 允许 cURL 跟随重定向
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);

if(curl_errno($ch)){
    writeLog("请求发生错误: " . curl_error($ch));
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // 判断是否成功获取到面板
    if ($httpCode == 200 && strpos($response, 'Server Insights') !== false && strpos($response, 'Update Plan') !== false) {
        writeLog("✅ 请求成功！Cookie 有效，已成功获取服务器面板数据。");
        
        if (preg_match('/UUID\s+([a-zA-Z0-9]+)/', $response, $matches)) {
            writeLog("👉 状态: 服务器在线，获取到 UUID: " . $matches[1]);
        }
        
    } else {
        writeLog("❌ 请求失败！Cookie 可能已失效，或者被 Cloudflare 拦截。HTTP 状态码: " . $httpCode);
        
        // 诊断错误原因
        if (strpos($response, 'login') !== false || strpos($response, 'sign in') !== false) {
            writeLog("⚠️ 诊断: 页面跳转到了登录界面，请更新你的 Cookie (session)。");
        } elseif (strpos($response, 'cf-browser-verification') !== false || strpos($response, 'Cloudflare') !== false) {
            writeLog("⚠️ 诊断: 触发了 Cloudflare 验证拦截，请更新你的 cf_clearance。");
        } else {
            writeLog("⚠️ 诊断: 未知错误，无法解析响应页面。");
        }
    }
}

curl_close($ch);

?>