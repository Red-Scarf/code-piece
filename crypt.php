<?php

$GLOBALS['auth_key'] = 'uje8hj98du8jf097tcgy3';
/**
 * 加密方法
 * @param string $string 待加密/解密字符串
 * @param string $operation 加密/解密操作 DECODE=解密
 * @param string $expiry 有效期
 */
function encode($string, $operation = 'DECODE', $expiry = 0)
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
    //     $string = $prefix . $secondfix . $string;
    // }
}
