<?php
declare(strict_types=1);

function connect(): PDO{
    static $pdo = null;

    if($pdo === null){
        $pdo = new PDO(
            'mysql:host=localhost;dbname=atmdb;charset=utf8mb4',
            'root',
            '',
            options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $pdo; 
}