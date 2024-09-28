<?php
// Archivo: db.php

$host = 'localhost';
$db = 'pizarra'; // Nombre de tu base de datos
$user = 'root'; // Usuario de MySQL
$pass = ''; // Contraseña del usuario

try {
    // Crear una nueva conexión usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    // Establecer el modo de error de PDO a Excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si ocurre un error en la conexión, se muestra el mensaje
    echo 'Error de conexión: ' . $e->getMessage();
    exit;
}
?>
