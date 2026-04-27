<?php
// config/db.php

$host = 'localhost';
$db = 'foodshare';
$user = 'root';
$pass = 'Iph0ne@use'; // Default XAMPP password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // In a real app, you'd log this and show a generic error
     die("Connection failed: " . $e->getMessage());
}
?>