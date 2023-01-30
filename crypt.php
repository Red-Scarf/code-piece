<?php

$GLOBALS['auth_key'] = 'uje8hj98du8jf097tcgy3';
/**
 * 加密方法
 * @param string $string 待加密/解密字符串
 * @param string $operation 加密/解密操作 DECODE=解密
 * @param string $expiry 有效期
 */
function crypt($string, $operation = 'DECODE', $expiry = 0)
{
    // 动态秘钥长度
    $ckey_length = 4;
    // 秘钥
    $key = $GLOBALS['auth_key'];
    // 秘钥a参与加解密
    $keya = md5(substr($key, 0, 16));
    // 秘钥b验证数据完整性
    $keyb = md5(substr($key, 16, 16));
    // 秘钥c转化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // if ($ckey_length) {
    //     if ($operation == 'DECODE') {
    //         $keyc = substr($string, 0, $ckey_length);
    //     } else {
    //         $keyc = substr(md5(microtime()), -$ckey_length);
    //     }
    // } else {
    //     $keyc = '';
    // }
    // 参与运算的秘钥
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文 前10位保存时间戳，验证数据有效性 10~26位保存秘钥b
    // 解密时利用秘钥b验证数据完整性
    // 解密从 ckey_length 位开始 前 ckey_length 位保存动态秘钥
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string, $keyb), 0, 16) . $string;
    // if ($operation == 'DECODE') {
    //     $string = base64_decode(substr($string, $ckey_length));
    // } else {
    //     // 前10位
    //     $prefix = sprintf('%010d', $expiry ? $expiry + time() : 0);
    //     // 后续 16 位
    //     $secondfix = substr(md5($string, $keyb), 0, 16);
    //     // 最后接上加密数据
    //     $string = $prefix . $secondfix . $string;
    // }
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = [];
    // 产生密码簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 固定算法打乱密码簿 增加随机性 实际上不会增加密文强度
    for ($i = $j = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        // $temp = $box[$i];
        // $box[$i] = $box[$j];
        // $box[$j] = $temp;
        // 交换值
        list($box[$i], $box[$j]) = [$box[$j], $box[$i]];
    }
    // 核心加密部分
    for ($a = $i = $j = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        list($box[$a], $box[$j]) = [$box[$j], $box[$a]];
        // 密码簿得出密钥进行异或 再转成字符串
        $boxTempInd = ($box[$a] + $box[$i]) % 256;
        $result = chr(ord($string[$i]) ^ ($box[$boxTempInd]));
    }
    if ($operation == 'DECODE') {
        // 没设置过期时间或者还未过期
        $condition_1 = substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0;
        // 
        $condition_2 = substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16);
        if ($condition_1 && $condition_2) {
            return substr($string, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}