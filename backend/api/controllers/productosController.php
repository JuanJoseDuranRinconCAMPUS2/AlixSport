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
        $stmt = $this->conn->prepare("SELECT p.id_Producto, p.nombre_Producto, p.descripcion_Producto, p.sabor_Producto, c.nombre_Categoria AS categoria_Producto,
            p.precio_Producto, p.stock_Producto, p.imagen_Producto, p.fecha_Creacion_Producto
            FROM productos p
            INNER JOIN categorias c ON p.categoria_Producto = c.id_Categoria"
        );
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($productos as &$producto) {
            $saboresArray = [];

            if (!empty($producto['sabor_Producto'])) {
                $ids = explode(",", $producto['sabor_Producto']);
                $placeholders = implode(",", array_fill(0, count($ids), "?"));

                $stmtSab = $this->conn->prepare("
                    SELECT id_Sabor, nombre_Sabor 
                    FROM sabores 
                    WHERE id_Sabor IN ($placeholders)
                ");
                $stmtSab->execute($ids);
                $saboresArray = $stmtSab->fetchAll(PDO::FETCH_ASSOC);
            }

            $producto['sabores'] = $saboresArray;
            unset($producto['sabor_Producto']);
        }
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

        $stmt = $this->conn->prepare("SELECT p.id_Producto, p.nombre_Producto, p.descripcion_Producto, p.sabor_Producto, c.nombre_Categoria AS categoria_Producto,
            p.precio_Producto, p.stock_Producto, p.imagen_Producto, p.fecha_Creacion_Producto
            FROM productos p
            INNER JOIN categorias c ON p.categoria_Producto = c.id_Categoria
            WHERE id_Producto = ?"
        );
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $saboresArray = [];
            if (!empty($producto['sabor_Producto'])) {
                $ids = explode(",", $producto['sabor_Producto']);
                $placeholders = implode(",", array_fill(0, count($ids), "?"));

                $stmtSab = $this->conn->prepare("SELECT id_Sabor, nombre_Sabor FROM sabores WHERE id_Sabor IN ($placeholders)");
                $stmtSab->execute($ids);
                $saboresArray = $stmtSab->fetchAll(PDO::FETCH_ASSOC);
            }

            $producto['sabores'] = $saboresArray;
            unset($producto['sabor_Producto']);  
            echo json_encode($producto);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Producto no encontrado"]);
        }
    }

    public function postProducto() {
        header('Content-Type: application/json');

        if (!isset($_POST["producto"]) || !isset($_FILES["imagen"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "Datos o imagen faltante"]);
            return;
        }

        $data = json_decode($_POST["producto"], true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "JSON invalido"]);
            return;
        }

        $imagen = $_FILES["imagen"];
        $nombreImagen = time() . "_" . basename($imagen["name"]);
        $rutaDestino = __DIR__ . "/../../../frontend/src/assets/products/" . $nombreImagen;
        
        $carpeta = realpath(__DIR__ . '/../../../frontend/src/assets/products');

        if (!$carpeta) {
            mkdir(__DIR__ . '/../../../src/assets', 0777, true);
            $carpeta = realpath(__DIR__ . '/../../../frontend/src/assets/products');
        }

        $rutaDestino = $carpeta . "/" . $nombreImagen;

        if (!move_uploaded_file($imagen["tmp_name"], $rutaDestino)) {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "Error al guardar la imagen"]);
            return;
        }

        try {
            $sql = "INSERT INTO productos 
                        (nombre_Producto, descripcion_Producto, sabor_Producto, 
                        precio_Producto, stock_Producto, imagen_Producto, categoria_Producto)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre_Producto"],
                $data["descripcion_Producto"],
                $data["sabores"],
                $data["precio_Producto"],
                $data["stock_Producto"],
                $nombreImagen,
                $data["categoria_Producto"]
            ]);

            echo json_encode(["status" => "ok", "mensaje" => "Producto creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "Error al crear producto: " . $e->getMessage()]);
        }
    }

    public function putProducto()
    {
        header('Content-Type: application/json');

        if (!isset($_POST["producto"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "No se envió la información del producto"]);
            return;
        }

        $data = json_decode($_POST["producto"], true);

        if (!$data || !isset($data["id_Producto"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "Datos inválidos o falta ID del producto"]);
            return;
        }

        $id = $data["id_Producto"];

        try {

            $nombreImagenFinal = $data["imagen_Producto"];

            if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {

                $nombreImagenFinal = time() . "_" . basename($_FILES["imagen"]["name"]);

                $carpeta = realpath(__DIR__ . "/../../../frontend/src/assets/products");
                if (!$carpeta) {
                    mkdir(__DIR__ . "/../../../frontend/src/assets/products", 0777, true);
                    $carpeta = realpath(__DIR__ . "/../../../frontend/src/assets/products");
                }

                $rutaDestino = $carpeta . "/" . $nombreImagenFinal;

                if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "mensaje" => "Error al guardar la nueva imagen"]);
                    return;
                }
            }

            $sql = "UPDATE productos SET 
                        nombre_Producto = ?, 
                        descripcion_Producto = ?, 
                        sabor_Producto = ?, 
                        precio_Producto = ?, 
                        stock_Producto = ?, 
                        imagen_Producto = ?, 
                        categoria_Producto = ?
                    WHERE id_Producto = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre_Producto"],
                $data["descripcion_Producto"],
                $data["sabores"],
                $data["precio_Producto"],
                $data["stock_Producto"],
                $nombreImagenFinal,
                $data["categoria_Producto"],
                $id
            ]);

            echo json_encode(["status" => "ok", "mensaje" => "Producto actualizado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "mensaje" => "Error al actualizar producto: " . $e->getMessage()]);
        }
    }



    public function deleteProducto() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['idProducto'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(["status" => "error", "mensaje" => "ID del producto no enviado"]);
            return;
        }

        try {
            $stmt = $this->conn->prepare("SELECT imagen_Producto FROM productos WHERE id_Producto = ?");
            $stmt->execute([$id]);
            $nombreImagen = $stmt->fetchColumn();

            if (!$nombreImagen) {
                echo json_encode(["status" => "error", "mensaje" => "El producto no existe"]);
                return;
            }

            $stmt = $this->conn->prepare("DELETE FROM productos WHERE id_Producto = ?");
            $stmt->execute([$id]);

            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM productos WHERE imagen_Producto = ?");
            $stmt->execute([$nombreImagen]);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                $path = __DIR__ . "/../../../frontend/src/assets/products/" . $nombreImagen;
                if (file_exists($path)) unlink($path);
            }

            echo json_encode(["status" => "ok", "mensaje" => "Producto eliminado correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => "Error al eliminar producto: " . $e->getMessage()]);
        }
    }

}
?>