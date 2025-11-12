<?php

require_once __DIR__ . '/../config/database.php';


class productosController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getProductos() {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM productos ORDER BY id_Producto");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($productos);
    }

    public function getProductoById() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['idProducto'];

        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id del producto no encontrado, envia el id por el body"]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM productos WHERE id_Producto = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            echo json_encode($producto);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Producto no encontrado"]);
        }
    }

    public function postProducto() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        if (!isset($data["nombre"]) || !isset($data["precio"])) {
            http_response_code(400);
            echo json_encode(["error" => "Nombre y precio son obligatorios"]);
            return;
        }

        try {
            $sql = "INSERT INTO productos 
                        (nombre_Producto, descripcion_Producto, sabor_Producto, 
                        precio_Producto, stock_Producto, imagen_Producto, categoria_Producto)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre"],
                $data["descripcion"],
                $data["sabor"],
                $data["precio"],
                $data["stock"],
                $data["imagen"],
                $data["categoria"]
            ]);

            echo json_encode(["mensaje" => "Producto creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear producto: " . $e->getMessage()]);
        }
    }

    public function putProducto() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['idProducto'];

        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id del producto no encontrado, envia el id por el body"]);
            return;
        }

        if (!$data) {
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        try {

            if (!$this->isProductoId($id)) {
                http_response_code(404);
                echo json_encode(["error" => "El producto con ID $id no existe"]);
                return;
            };

            $sql = "UPDATE productos SET 
                        nombre_Producto = ?, descripcion_Producto = ?, sabor_Producto = ?, 
                        precio_Producto = ?, stock_Producto = ?, imagen_Producto = ?, categoria_Producto = ?
                    WHERE id_Producto = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre"],
                $data["descripcion"],
                $data["sabor"],
                $data["precio"],
                $data["stock"],
                $data["imagen"],
                $data["categoria"],
                $id
            ]);

            echo json_encode(["mensaje" => "Producto actualizado correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al actualizar producto: " . $e->getMessage()]);
        }
    }

    public function deleteProducto() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['idProducto'];

        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id del producto no encontrado, envia el id por el body"]);
            return;
        }

        try {

            if (!$this->isProductoId($id)) {
                http_response_code(404);
                echo json_encode(["error" => "El producto con ID $id no existe"]);
                return;
            };
            
            $stmt = $this->conn->prepare("DELETE FROM productos WHERE id_Producto = ?");
            $stmt->execute([$id]);
            echo json_encode(["mensaje" => "Producto eliminado correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al eliminar producto: " . $e->getMessage()]);
        }
    }

    private function isProductoId($id) {
        try {
            $sql = "SELECT COUNT(*) FROM productos WHERE id_Producto = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}

?>