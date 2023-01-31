<?php

$GLOBALS['auth_key'] = 'uje8hj98du8jf097tcgy3';
/**
 * 加密
 * 先对数据base64加密，然后使用密码本进行异或运算
 * 利用异或的自反性实现加密解密
 * 加密后的数据中 前32位为原数据的md5值 用于打乱密码本
 * @param string $string 待加密/解密字符串
 * @param string $operation 加密/解密操作 DECODE=解密
 */
function specifyCrypt($string, $operation = 'DECODE')
{
    if ($operation == 'DECODE') {
        $stringMD5 = substr($string, 0, 32);
        $string = substr($string, 32);
        $string = base64_decode($string);
    } else {
        $stringMD5 = md5($string);
    }

    // 参与运算的秘钥
    $cryptkey = $GLOBALS['auth_key'];
    $key_length = strlen($cryptkey);
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = [];
    // 产生密码本
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 固定算法打乱密码本 增加随机性 实际上不会增加密文强度
    for ($i = $j = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        // 交换值
        list($box[$i], $box[$j]) = [$box[$j], $box[$i]];
    }
    // 核心加密部分
    for ($a = $i = $j = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        list($box[$a], $box[$j]) = [$box[$j], $box[$a]];
        // 密码本得出密钥进行异或 再转成字符串
        $boxTempInd = ($box[$a] + $box[$i]) % 256;
        $result .= chr(ord($string[$i]) ^ ($box[$boxTempInd]));
    }
    if ($operation == 'DECODE') {
        return $result;
    } else {
        return $stringMD5 . str_replace('=', '', base64_encode($result));
    }
}