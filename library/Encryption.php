<?php

namespace SecureFilebase;

class Encryption
{
    public $key;
    function aes_sha512_encrypt($token)
    {
        $cipher_method = 'AES-128-CTR';
        $enc_key = openssl_digest($this->key, 'SHA512', TRUE);
        $enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));
        $crypted_token = openssl_encrypt($token, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
        unset($token, $cipher_method, $enc_key, $enc_iv);
        return $crypted_token;
    }
    function aes_sha512_decrypt($crypted_token)
    {
        list($crypted_token, $enc_iv) = explode("::", $crypted_token);;
        $cipher_method = 'AES-128-CTR';
        $enc_key = openssl_digest($this->key, 'SHA512', TRUE);
        $token = openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv));
        unset($crypted_token, $cipher_method, $enc_key, $enc_iv);
        return $token;
    }
}
