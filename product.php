<?php
// product.php: Maneja los productos

// Incluir el archivo de conexión a la base de datos
require 'bd.php';

// Verificar el tipo de solicitud
switch ($_SERVER['REQUEST_METHOD']) {
    // Agrega el producto a la base de datos
    case 'POST':
        if (isset($_POST['amount']) && isset($_POST['name']) && isset($_POST['price'])) {
            $amount = $_POST['amount'];
            $name = $_POST['name'];
            $price = $_POST['price'];

            try {
                // Preparar la consulta SQL para agregar el producto
                $stmt = $pdo->prepare("INSERT INTO products (amount, name, price) VALUES (:amount, :name, :price)");
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':price', $price);
                $stmt->execute();                
                // Envio de respuesta exitosa
                echo json_encode(["message" => "Producto agregado correctamente"]);
            } catch (PDOException $e) {
                // Envio de respuesta error
                echo json_encode(["error" => "Error al agregar el producto: " . $e->getMessage()]);
            }
        } elseif (isset($_POST['amount']) && isset($_POST['name'])){
            $amount = $_POST['amount'];
            $name = $_POST['name'];
            
            try {
                // Preparar la consulta SQL para agregar el producto
                $stmt = $pdo->prepare("INSERT INTO products (amount, name) VALUES (:amount, :name)");
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':name', $name);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                // Envio de respuesta exitosa
                echo json_encode(["message" => "Producto agregado correctamente, NO OLVIDES AGREGARLE EL PRECIO"]);
            } catch (PDOException $e) {
                // Envio de respuesta error
                echo json_encode(["error" => "Error al agregar el producto: " . $e->getMessage()]);
            }
        }else{
            echo json_encode(["error" => "Faltan campos obligatorios"]);
        }
        break;
    // Busca el producto o productos en la base de datos
    case 'GET':
        $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        $name = isset($_REQUEST['name'])? $_REQUEST['name'] : null;
        if ($id) {
            // Preparar la consulta SQL obtener producto de la base de datos por el id
            $stmt = $pdo->prepare("SELECT id, amount, name, price FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $product = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($product){
                echo json_encode($product);
            }else {
                echo json_encode(["error"=>"Error al obtener el producto"]);
            }
        } elseif ($name) {
                // Preparar la consulta SQL obtener producto de la base de datos por el nombre
                $stmt = $pdo->prepare("SELECT id, amount, name, price FROM products WHERE name = :name");
                $stmt->bindParam(':name', $name);
                $stmt->execute();
                $product = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($product){
                    echo json_encode($product);
                }else {
                    echo json_encode(["error"=>"Error al obtener el producto"]);
                }
        } else {
            // Preparar la consulta SQL obtener todos los productos de la base de datos
            $stmt = $pdo->query("SELECT id, amount, name, price FROM products");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($products){
                echo json_encode($products  );
            }else {
                echo json_encode(["error"=>"Error al obtener los productos"]);
            }
        }        
        break;
        // Actualiza el producto en la base de datos
        case 'PUT':
            $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
            if($id){
                // Obtener el cuerpo de la solicitud
                $input = file_get_contents("php://input");
                
                parse_str($input, $data);
                try {
                    
                    // Actualiza la cantidad, el nombre y el precio del producto 
                    if (isset($data['amount']) && isset($data['name']) && isset($data['price'])){
                        $amount = $data['amount'];
                        $name = $data['name'];
                        $price = $data['price'];
                        
                        // Preparar la consulta SQL actualizar producto de la base de datos por el id
                        $stmt = $pdo->prepare("UPDATE products SET amount = :amount, name = :name, price = :price WHERE id = :id");
                        $stmt->bindParam(':id', $id);
                        $stmt->bindParam(':amount', $amount);
                        $stmt->bindParam(':name', $name);
                        $stmt->bindParam(':price', $price);
                        $stmt->execute();
                        echo json_encode(["message" => "Producto actualizado correctamente"]);
                    } 
                // Actualiza la cantidad del producto 
                elseif (isset($data['amount'])) {
                    $amount = $data['amount'];
                    // Preparar la consulta SQL actualizar producto de la base de datos por el id
                    $stmt = $pdo->prepare("UPDATE products SET amount = :amount WHERE id = :id");
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':amount', $amount);
                    $stmt->execute();
                    echo json_encode(["message" => "Producto actualizado correctamente"]);
                }
                // Actualiza el nombre del producto 
                elseif(isset($data['name'])) {
                    $name = $data['name'];
                    // Preparar la consulta SQL actualizar producto de la base de datos por el id
                    $stmt = $pdo->prepare("UPDATE products SET name = :name WHERE id = :id");
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':name', $name);
                    $stmt->execute();
                    echo json_encode(["message" => "Producto actualizado correctamente"]);
                }
                // Actualiza el precio del producto 
                elseif(isset($data['price'])) {
                    $price = $data['price'];
                    // Preparar la consulta SQL actualizar producto de la base de datos por el id
                    $stmt = $pdo->prepare("UPDATE products SET price = :price WHERE id = :id");
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':price', $price);
                    $stmt->execute();
                    echo json_encode(["message" => "Producto actualizado correctamente"]);
                } else {
                    echo json_encode(["error" => "Faltan campos obligatorios"]);
                }
            } catch (PDOException $e) {
                echo json_encode(["error" => "Error al actualizar el producto: " . $e->getMessage()]);
            }
            } else {
                echo json_encode(["error" => "El producto no se encuentra en la base de datos"]);
            }        
        break;
    // Eliminar el producto de la base de datos
    case 'DELETE':
        $id = isset($_REQUEST['id'])? $_REQUEST['id'] : null;
        if ($id) {
            try {
                // Preparar la consulta SQL eliminar el producto de la base de datos por el id
                $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                echo json_encode(["message" => "Producto eliminado correctamente"]);
            } catch (PDOException $e) {
                echo json_encode(["error"=>"Error al eliminar el producto: ", $e]);
            }
        }else{
            echo json_encode(["error" => "Faltan campos obligatorios"]);
        }

        break;
    // Responder con un mensaje de error si el método de solicitud no corresponde
    default:
        echo json_encode(["error" => "Método de solicitud no permitido"]);
        break;
}   
?>
