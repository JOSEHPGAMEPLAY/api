<?php
// db.php: Conexión a la base de datos

$host = 'localhost';
$db = 'tienda';
$user = 'root'; // Cambia esto si tienes un usuario distinto
$pass = ''; // Cambia esto si tienes una contraseña

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Establecer el modo de error de PDO a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}
?>