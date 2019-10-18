<?php
include_once './library/autoloader.php';

$encryption = new \SecureFilebase\Encryption();
include_once __DIR__ . '../config.php';
// echo $secureFilebaseKey;

$db = new \SecureFilebase\Database([
    'dir' => './database'
]);

$usr = $db->get("asdf");
// echo $usr->key_1;
$usr->key_1 = "123";
$usr->key_2 = "value 2";
$usr->save();
