<?php

// Database connection data in MySQL deployed in AWS
$host = 'bdapirest.c3oaumayk5ln.us-east-1.rds.amazonaws.com';
$dbname = 'apirestuser';
$username = 'admin';
$password = 'bdapirest_prueba1';

//Connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}
