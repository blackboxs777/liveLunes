<?php

// 设置时区，确保时间准确
date_default_timezone_set('Asia/Shanghai');

// 1. 配置区域
$config = [
    //
    [
        'x-sign' => '3voqtlxIxUA/itlqnA3BJ3Re5cAe3+HgomLecWH4P7laRtGDy2jYfupsY4Oj0nnSI+HTmhlQR7rgN+0tB83wxby+xT/C3EJiOzUOiOlZR/ZXls6Xat1CY1ZwhrNl6tREme+0Vw1iyI9IV8yNuxBJTDMyiPkWZHIvTIIfM0M8UVk=',
        'cookie' => '__tctmc=217272534.241284496; __tctmd=217272534.737325; __tctmu=217272534.0.0; __tctmz=217272534.1775260624434.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none); longKey=1775260624167764; __tctrack=0; mw_fp=b8Z3p6sf80XXaZP8hjp55FKb7efCgWzi; __tctma=217272534.1775260624167764.1775260624434.1775260624434.1775347356082.2; __tctmb=217272534.1544873156810219.1775347356082.1775347356082.1',
        'data'   => ['cityCode' => '310100', 'activityNo' => '2925c', 'unionId' => '', 'traceId' => 'a20499d0-b845-47c1-a280-97fd522e0264', 'os' => 'android']
    ],
    //0121 
    [
        'x-sign' => '1123VWR5D6OBCc1rYXJdHZpxli0rC2HEnvT6/SrEbhbo/3FTvXoTAwI5LdQJmkqPABgO4D+Rw5kn98aboHc+3Bt4q6ZDs8flPr/B6LHXaaN+gAolK59agtPL3IKmBBg7Um2bKAC3pamyZioeNIzUgTCK8f3YTAAovp+21s64oRA=',
        'cookie' => '__tctmd=217272534.737325; __tctmu=217272534.0.0; __tctmz=217272534.1774923350010.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none); longKey=1774923350787108; __tctrack=0; mw_fp=aGJhywe08EkHTJcciPvQrc3BqpQJpett; __tctma=217272534.1774923350787108.1774923350010.1775174478018.1775177486310.17; __tctmc=217272534.19092906; __tctmb=217272534.1663797681773094.1775177486310.1775177577201.2',
        'data'   => ['cityCode' => '310100', 'activityNo' => '2925c', 'unionId' => 'ohmdTtyjUrIq_2Pm51yusTuCxoa8', 'traceId' => 'd3e7c5e8-a847-4a79-8a2a-5b8e79670185', 'os' => 'android']
    ],
    //6697 
    [
        'x-sign' => 'MhrCN/RJFVfsNHRREPO19HsU+pFiMIEabLGSLTAPhtBesQ6mJBSLNWuZnn23LASeI+HTmhlQR7rgN+0tB83wxde/yTAUq/oHO/lCzRQKMjQeqEAOaPvXjnOdW/fioegbY9+sOLoJkotW2vJs6tdReTMyiPkWZHIvTIIfM0M8UVk=',
        'cookie' => '__tctmc=217272534.241284496; __tctmd=217272534.737325; __tctma=217272534.1775348439995606.1775348439416.1775348439416.1775348439416.1; __tctmb=217272534.3076135827080486.1775348439416.1775348439416.1; __tctmu=217272534.0.0; __tctmz=217272534.1775348439416.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none); longKey=1775348439995606; __tctrack=0; mw_fp=a4mLl6J6pcFQqISY44M6O57iSQSXcO24',
        'data'   => ['cityCode' => '310100', 'activityNo' => '2925c', 'unionId' => '', 'traceId' => '5bccd516-15e4-4c4b-9648-09ec6a6f6f5f', 'os' => 'android']
    ],
];

// 2. 日志记录函数
function logger($msg) {
    $logFile = __DIR__ . '/log/sign_' . date('Y-m-d') . '.log';
    $time = date('Y-m-d H:i:s');
    $content = "[$time] $msg" . PHP_EOL;
    echo $content;
    file_put_contents($logFile, $content, FILE_APPEND);
}

// 3. 执行循环
$url = 'https://cstapp.17u.cn/cstapi/activity/sign/checkIn';

foreach ($config as $index => $item) {
    $seq = $index + 1;
    logger("开始执行第 {$seq} 个账号...");

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item['data']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Linux; Android 13; 2112123AC Build/TKQ1.221114.001; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/146.0.7680.164 Mobile Safari/537.36; os/android; cstTravelApp; CstVer/1.3.2; cstChannel/XIAOMI;',
        'Content-Type: application/json',
        'x-sign: ' . $item['x-sign'],
        'Cookie: ' . $item['cookie'],
        'Referer: https://cstapp.17u.cn/wap/integral/sign?platcode=10255'
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        logger("第 {$seq} 个账号请求失败: $error");
    } else {
        $result = json_decode($response, true);
        if (isset($result['success']) && $result['success'] === true) {
            logger("第 {$seq} 个账号签到成功: " . $response);
        } else {
            logger("第 {$seq} 个账号签到返回信息: " . $response);
        }
    }
    sleep(1); // 避免请求过快
}