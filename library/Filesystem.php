<?php

namespace SecureFilebase;

class Filesystem
{
    public static function read($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        $file = fopen($path, 'r');
        $contents = fread($file, filesize($path));
        fclose($file);


        global $encryption;
        $contents = $encryption->aes_sha512_decrypt($contents);

        // $contents = base64_decode($contents);

        return $contents;
    }

    public static function write($path, $contents)
    {
        $fp = fopen($path, 'w+');

        if (!flock($fp, LOCK_EX)) {
            return false;
        }

        global $encryption;
        $contents = $encryption->aes_sha512_encrypt($contents);

        // $contents = base64_encode($contents);

        $result = fwrite($fp, $contents);

        flock($fp, LOCK_UN);
        fclose($fp);

        return $result !== false;
    }

    public static function delete($path)
    {
        return unlink($path);
    }

    public static function validateName($name)
    {
        if (!preg_match('/^[0-9A-Za-z\_\-]{1,63}$/', $name)) {
            throw new \Exception(sprintf('`%s` is not a valid file name.', $name));
        }

        return $name;
    }

    public static function getAllFiles($path = '', $ext = 'json')
    {
        $files = [];
        $_files = glob($path . '*.' . $ext);
        foreach ($_files as $file) {
            $files[] = str_replace('.' . $ext, '', basename($file));
        }

        return $files;
    }
}
