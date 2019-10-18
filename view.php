<?php
include_once './library/autoloader.php';
$encryption = new \SecureFilebase\Encryption();
$encryption->key = "123456";

$db = new \SecureFilebase\Database([
    'dir'            => './database'
]);

$usr = $db->get("asd");
echo $usr->key_1;