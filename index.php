<?php
include_once './library/autoloader.php';
$encryption = new \SecureFilebase\Encryption();
$encryption->key = "123456";

$db = new \SecureFilebase\Database([
    'dir' => './database'
]);

$usr = $db->get("asdf");
$usr->key_1 = "123";
$usr->key_2 = "value 2";
$usr->save();
