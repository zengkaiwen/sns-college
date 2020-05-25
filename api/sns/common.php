<?php
/**
 * Created by PhpStorm.
 * User: macbook
 * Date: 2020-02-16
 * Time: 11:03
 */

function upyun_sign($key, $secret, $method, $uri, $date, $policy=null, $md5=null)
{
    $elems = array();
    foreach (array($method, $uri, $date, $policy, $md5) as $v)
    {
        if ($v)
        {
            $elems[] = $v;
        }
    }
    $value = implode('&', $elems);
    $sign = base64_encode(hash_hmac('sha1', $value, $secret, true));
    return $sign;
}

function get_wxapp_access_token() {

}

function upyun_check_content($text) {

    $key = '6F1MBSDvNdFuI0R4YeCyEjiImVF0Hhoo';
    $secret = 'Hyvt6SV6SqDM9ppW1wQqqR4QtxloNOfs';
    $api_url = 'http://banma.api.upyun.com/text/check?act=spam';

    $uri = '/text/check?act=spam';

    $date = gmdate('D, d M Y H:i:s \G\M\T');

    $sign = upyun_sign($key, $secret, 'POST', $uri, $date);

    $header = [
        "Authorization:UPYUN {$key}:{$sign}",
        "x-date:{$date}",
        "Content-Type:application/json",
        "Accept:application/json"
    ];

    $opt_data = [
        'text'  => $text,
    ];
    $opt_data = json_encode($opt_data);

    $curl = curl_init();
    curl_setopt($curl,CURLOPT_URL, $api_url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
    curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
    curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl,CURLOPT_POSTFIELDS, $opt_data);

    $result = curl_exec($curl);
    $result = json_decode($result);
    if ($result->status_code == 200) {
        $label = $result->spam->label;
        if ($label == 0) {
            return 1; // 正常
        }
        if ($label == 1) {
            return 2; // 违规
        }
        if ($label == 2) {
            return 3; // 疑似
        }
    } else {
        return 0;
    }
}

//时间格式化（时间戳）
function uc_time_ago($ptime) {
    date_default_timezone_set('PRC');
    //$ptime = strtotime($ptime);
    $etime = time() - $ptime;
    switch ($etime){
        case $etime <= 60:
            $msg = '刚刚';
            break;
        case $etime > 60 && $etime <= 60 * 60:
            $msg = floor($etime / 60) . ' 分钟前';
            break;
        case $etime > 60 * 60 && $etime <= 24 * 60 * 60:
            $msg = date('Ymd',$ptime)==date('Ymd',time()) ? '今天 '.date('H:i',$ptime) : '昨天 '.date('H:i',$ptime);
            break;
        case $etime > 24 * 60 * 60 && $etime <= 2 * 24 * 60 * 60:
            $msg = date('Ymd',$ptime)+1==date('Ymd',time()) ? '昨天 '.date('H:i',$ptime) : '前天 '.date('H:i',$ptime);
            break;
        case $etime > 2 * 24 * 60 * 60 && $etime <= 12 * 30 * 24 * 60 * 60:
            $msg = date('Y',$ptime)==date('Y',time()) ? date('m-d H:i',$ptime) : date('Y-m-d H:i',$ptime);
            break;
        default: $msg = date('Y-m-d H:i',$ptime);
    }
    return $msg;
}