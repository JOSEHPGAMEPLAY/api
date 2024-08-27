<?php
// sale.php: Maneja la venta por producto

// Incluir el archivo de conexión a la base de datos
require 'bd.php';

// Verificar el tipo de solicitud
switch ($_SERVER['REQUEST_METHOD']) {
        // Agrega la venta a la base de datos
    case 'POST':
        if (isset($_POST['amount']) && isset($_POST['product'])) {
            $amount = $_POST['amount'];
            $productid = $_POST['product'];

            try {
                //Busca el producto, para calcular el total de la venta segun el precio del producto
                $stmt = $pdo->prepare("SELECT id, amount, name, price FROM products WHERE id = :id");
                $stmt->bindParam(':id', $productid);
                $stmt->execute();
                $input = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $data = $input[0];
                $total = $data['price'] * $amount;
                if ($data['amount']>=$amount){
                    try {
                        // Preparar la consulta SQL para agregar el producto
                        $stmt = $pdo->prepare("INSERT INTO sale (amount, product, total) VALUES (:amount, :product, :total)");
                        $stmt->bindParam(':amount', $amount);
                        $stmt->bindParam(':product', $productid);
                        $stmt->bindParam(':total', $total);
                        $stmt->execute();

                        $finalamount = $data['amount']-$amount;
                        // Preparar la consulta SQL actualizar producto de la base de datos por el id
                        $stmt = $pdo->prepare("UPDATE products SET amount = :amount WHERE id = :id");
                        $stmt->bindParam(':id', $productid);
                        $stmt->bindParam(':amount', $finalamount);
                        $stmt->execute();
                        echo json_encode(["message" => "Producto actualizado correctamente"]);
                        // Envio de respuesta exitosa
                        echo json_encode(["message" => "Venta agregada correctamente"]);
                    } catch (PDOException $e) {
                        // Envio de respuesta error
                        echo json_encode(["error" => "Error al agregar la venta: " . $e->getMessage()]);
                    }
                }else{
                    echo json_encode(["error" => "Error no hay productos suficientes"]);
                }
            } catch (PDOException $e) {
                echo json_encode(["error" => "Error el producto no existe" . $e->getMessage()]);
            }
        } else {
            echo json_encode(["error" => "Faltan campos obligatorios"]);
        }
        break;
        // Busca la venta o ventas en la base de datos
    case 'GET':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        if ($id) {
            // Preparar la consulta SQL obtener producto de la base de datos por el id
            $stmt = $pdo->prepare("SELECT sale.id, sale.amount, products.id, products.name, sale.total
                                    FROM sale
                                    LEFT JOIN products ON sale.product = products.id
                                    WHERE sale.id = :id
                                    ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $product = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($product) {
                echo json_encode($product);
            } else {
                echo json_encode(["error" => "Error al obtener la venta"]);
            }
        } else {
            // Preparar la consulta SQL obtener todas las ventas de la base de datos
            $stmt = $pdo->query("SELECT sale.id, sale.amount, products.id, products.name, sale.total
                                FROM sale
                                LEFT JOIN products ON sale.product = products.id");
            $stmt->execute();
            $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($sales) {
                echo json_encode($sales);
            } else {
                echo json_encode(["error" => "Error al obtener las ventas"]);
            }
        }
        break;
        // Eliminar la venta de la base de datos
    case 'DELETE':
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        if ($id) {
            try {
                // Preparar la consulta SQL eliminar la venta de la base de datos por el id
                $stmt = $pdo->prepare("DELETE FROM sale WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                echo json_encode(["message" => "Venta eliminada correctamente"]);
            } catch (PDOException $e) {
                echo json_encode(["error" => "Error al eliminar la venta ", $e]);
            }
        } else {
            echo json_encode(["error" => "Faltan campos obligatorios"]);
        }

        break;
        // Responder con un mensaje de error si el método de solicitud no corresponde
    default:
        echo json_encode(["error" => "Método de solicitud no permitido"]);
        break;
}
