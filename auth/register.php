<?php
// register.php: Maneja el registro de nuevos usuarios

// Incluir el archivo de conexión a la base de datos
require '../bd.php';

// Verificar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Verificar si los campos 'username' y 'password' están presentes en la solicitud POST
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Capturar los valores de username y password desde la solicitud POST
        $username = $_POST['username'];
        // Hashear la contraseña para almacenarla de manera segura en la base de datos
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            // Preparar la consulta SQL para insertar un nuevo usuario en la base de datos
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            // Ejecutar la consulta
            $stmt->execute();
            // Responder con un mensaje de éxito en formato JSON
            echo json_encode(["message" => "Registro exitoso"]);
        } catch (PDOException $e) {
            // Si ocurre un error, responder con un mensaje de error en formato JSON
            echo json_encode(["error" => "Error al registrar el usuario: " . $e->getMessage()]);
        }
    } else {
        // Responder con un mensaje de error si faltan campos obligatorios
        echo json_encode(["error" => "Faltan campos obligatorios"]);
    }
} else {
    // Responder con un mensaje de error si el método de solicitud no es POST
    echo json_encode(["error" => "Método de solicitud no permitido"]);
}
?>
