# secure-flatfile-db

### flat file database system
##### based on https://github.com/tmarois/Filebase

- secured by AES encryption (AES-128-CTR | SHA512)
- JSON format
- easy, simplified interface
- removed composer, simple autoloading
- mainly built for usage in: gymh-cloud project

### HOWTO
```
<?php
include_once './library/autoloader.php';
$encryption = new \SecureFilebase\Encryption();
include_once __DIR__ . '../config.php';
$db = new \SecureFilebase\Database(['dir' => './database']);
$usr = $db->get("ID");
echo $usr->key_1;//output data
$usr->key_1 = "new_data_for_key";//set data for key
$usr->save();
```

### REMINDER
- please make sure to change the password found in `config.php` before using this project in a production system