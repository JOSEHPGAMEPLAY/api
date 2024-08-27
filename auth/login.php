<?php
// login.php: Maneja la validación de usuario (inicio de sesión)

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si los campos 'username' y 'password' están presentes en la solicitud POST
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            // Preparar la consulta SQL para buscar el usuario en la base de datos
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si se encontró el usuario y si la contraseña es correcta
            if ($user && password_verify($password, $user['password'])) {
                echo json_encode(["message" => "Autenticacion satisfactoria"]);
            } else {
                echo json_encode(["error" => "Nombre de usuario o contraseña incorrectos"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al validar el usuario: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "Faltan campos obligatorios"]);
    }
} else {
    echo json_encode(["error" => "Metodo de solicitud no permitido"]);
}
?>
