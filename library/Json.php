<?php

namespace SecureFilebase\Format;

class Json implements FormatInterface
{
    public static function getFileExtension()
    {
        return 'json';
    }
    public static function encode($data = [], $pretty = true)
    {
        $p = 1;
        if ($pretty == true) $p = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

        return json_encode($data, $p);
    }
    public static function decode($data)
    {
        return json_decode($data, 1);
    }
}
