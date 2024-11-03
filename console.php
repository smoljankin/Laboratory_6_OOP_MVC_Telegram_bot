<?php


if ($argc < 2 || !in_array($argv[1], ['setup', 'add-user'])) {
    print <<<CLI
        Usage:
            > php ./console.php <command> [options]
            
        <command> is one of ["setup", "add-user"]

        Example:
            > php ./console.php setup
            > php ./console.php add-user admin my-secret-password

    CLI;
    exit();
}

$commandName = $argv[1];

if ($commandName === 'setup') {
    setupDB();
    exit();
}

if ($commandName === 'add-user') {
    addUser($argv[2], $argv[3]);
    exit();
}


function setupDB() {
    $pdo = getDBConnection();
    $pdo->exec(getDBSchema());
    print "DB is installed" . PHP_EOL;
    exit();
}

function getDBConnection() {
    return new PDO('sqlite:./db/mydb.sq3');
}

function getDBSchema() {
    $migrateSQL = <<<SQL
        DROP TABLE IF EXISTS users;
        DROP TABLE IF EXISTS category;
        DROP TABLE IF EXISTS product;
        DROP TABLE IF EXISTS warehouse;
        DROP TABLE IF EXISTS orders;
        DROP TABLE IF EXISTS order_items;

        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(32) NOT NULL,
            password VARCHAR(64) NOT NULL
        );

        CREATE TABLE category (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(32) NULL
        );

        CREATE TABLE product (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(32) NULL DEFAULT NULL,
            description VARCHAR(256) NULL,
            price FLOAT NULL,
            status BOOLEAN NULL,
            category_id INT NULL
        );

        CREATE TABLE warehouse (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            count_reserved INT NULL,
            count_stored INT NULL,
            product_id INT NULL
        );

        CREATE TABLE orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id	INTEGER,
            user_email VARCHAR(64) NULL,
            user_name VARCHAR(64) NULL,
            user_address VARCHAR(128) NULL,
            user_phone VARCHAR(16) NULL
        );

        CREATE TABLE order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            count INT NULL,
            order_id INT NULL,
            product_id INT NULL
        );
    SQL;
    return $migrateSQL;
}
    
function addUser($userName, $password) {
    if (empty($userName)) {
        print "username can't empty" . PHP_EOL;
        exit();
    }
    if (empty($password) || strlen($password) < 8) {
        print "password is too short" . PHP_EOL;
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $pdo = getDBConnection();
    $sth = $pdo->prepare('INSERT into users (name, password) VALUES(:name, :password)');
    $result = $sth->execute(['name' => $userName, 'password' => $hash]);

    if ($result) {
        print "User ($userName) was added." . PHP_EOL;
    } else {
        print "There was a problem during creating user ($userName)" . PHP_EOL;
    }
}
