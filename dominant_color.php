<?php

$a = '邓志远去福州';
$key = 'U2FsdGVkX190Ix69';
$b = openssl_encrypt($a, 'aes-128-ecb', $key);
var_dump($b);
$c = openssl_decrypt($b, 'aes-128-ecb', $key);
var_dump($c);
// try {
//     $i = imagecreatefromjpeg($a);
//     echo $i;
// } catch (\Throwable $th) {
//     var_dump($th->getMessage());
// }