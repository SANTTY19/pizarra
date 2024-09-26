<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pizarra";

// Configurar el reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Crear conexión con excepciones
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Establecer el conjunto de caracteres para la conexión
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
