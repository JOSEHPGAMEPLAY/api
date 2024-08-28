<?php
// restablish_password.php: Maneja cuando el usuario olvida la contraseña

// Incluir el archivo de conexión a la base de datos
require '../bd.php';

// Verificar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Verificar si el campo 'username' esta presente en la solicitud POST
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Capturar el valor username en la solicitud POST
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            // Preparar la consulta SQL para insertar un nuevo usuario en la base de datos
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username ;");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            // Ejecutar la consulta
            $stmt->execute();
            // Responder con un mensaje de éxito en formato JSON
            echo json_encode(["message" => "Cambio de contraseña exitoso"]);
        } catch (PDOException $e) {
            // Si ocurre un error, responder con un mensaje de error en formato JSON
            echo json_encode(["error" => "Error al cambiar la contraseña: " . $e->getMessage()]);
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
