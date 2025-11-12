<?php

require_once __DIR__ . '/../config/database.php';


class carritoController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getCarrito() {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM carrito ORDER BY id_Carrito");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($productos);
    }

    public function getCarritoDetalles() {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM carrito_detalle ORDER BY id");
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($productos);
    }

    public function getCarritoById() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['idCarrito'];

        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id del carrito no encontrado, envia el id por el body"]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM carrito WHERE id_Carrito = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            echo json_encode($producto);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Carrito no encontrado"]);
        }
    }

    public function getDetallesByUser() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['idUsuario'];

        try {
            $stmt = $this->conn->prepare("
                SELECT cd.*, p.nombre_Producto, p.imagen_Producto
                FROM carrito c
                INNER JOIN carrito_detalle cd ON c.id_Carrito = cd.id_Carrito
                INNER JOIN productos p ON cd.id_Producto = p.id_Producto
                WHERE c.id_Usuario = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                http_response_code(404);
                echo json_encode(["mensaje" => "El usuario no tiene productos en el carrito"]);
                return;
            }

            echo json_encode($result);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al obtener detalles: " . $e->getMessage()]);
        }
    }

    public function getTotalCarrito(){
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['idUsuario'];

        try {

            $sqlCarrito = "SELECT id_Carrito FROM carrito WHERE id_Usuario = ?";
            $stmtCarrito = $this->conn->prepare($sqlCarrito);
            $stmtCarrito->execute([$id]);
            $carrito = $stmtCarrito->fetch(PDO::FETCH_ASSOC);

            if (!$carrito) {
                http_response_code(404);
                echo json_encode(["mensaje" => "El usuario no tiene un carrito activo"]);
                return;
            }

            $idCarrito = $carrito['id_Carrito'];

            $sqlTotal = "SELECT SUM(subtotal) AS total FROM carrito_detalle WHERE id_Carrito = ?";
            $stmtTotal = $this->conn->prepare($sqlTotal);
            $stmtTotal->execute([$idCarrito]);
            $resultado = $stmtTotal->fetch(PDO::FETCH_ASSOC);

            $total = $resultado['total'] ? floatval($resultado['total']) : 0.00;

            echo json_encode([
                "idUsuario" => $id,
                "idCarrito" => $idCarrito,
                "total" => $total
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error al obtener el total del carrito: " . $e->getMessage()]);
        }
    }

    public function postCarrito() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        if (!$data || !isset($data['idUsuario']) || !isset($data['idProducto']) || !isset($data['cantidad'])) {
            echo json_encode(["error" => "Faltan datos: idUsuario, idProducto o cantidad"]);
            return;
        }

        $idUsuario = $data['idUsuario'];
        $idProducto = $data['idProducto'];
        $cantidad = $data['cantidad'];

        try {

            if (!$this->isUsuarioId($idUsuario)) {
                echo json_encode(["error" => "Usuario no encontrado"]);
                return;
            }

            $sqlCarrito = "SELECT id_Carrito FROM carrito WHERE id_Usuario = ?";
            $stmt = $this->conn->prepare($sqlCarrito);
            $stmt->execute([$idUsuario]);
            $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$carrito) {
                $sqlCrear = "INSERT INTO carrito (id_Usuario) VALUES (?)";
                $stmtCrear = $this->conn->prepare($sqlCrear);
                $stmtCrear->execute([$idUsuario]);
                $idCarrito = $this->conn->lastInsertId();
            } else {
                $idCarrito = $carrito['id_Carrito'];
            }

            $sqlPrecio = "SELECT precio_Producto FROM productos WHERE id_Producto = ?";
            $stmtPrecio = $this->conn->prepare($sqlPrecio);
            $stmtPrecio->execute([$idProducto]);
            $producto = $stmtPrecio->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                echo json_encode(["error" => "Producto no encontrado"]);
                return;
            }

            $precioUnitario = $producto['precio_Producto'];

            $sqlDetalle = "INSERT INTO carrito_detalle (id_Carrito, id_Producto, cantidad, precio_unitario)
                           VALUES (?, ?, ?, ?)";
            $stmtDetalle = $this->conn->prepare($sqlDetalle);
            $stmtDetalle->execute([$idCarrito, $idProducto, $cantidad, $precioUnitario]);

            echo json_encode(["mensaje" => "Producto agregado al carrito correctamente"]);

        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear carrito: " . $e->getMessage()]);
        }
    }

    public function deleteDetalleCarrito() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $idCarrito = $data['idCarrito'];

        if (!$data || !$idCarrito) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        try {

            if (!$this->isCarritoId($idCarrito)) {
                echo json_encode(["error" => "Detalle del Carrito no encontrado"]);
                return;
            }

            $sql = "DELETE FROM carrito_detalle WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idCarrito]);

            echo json_encode(["mensaje" => "Producto eliminado del carrito correctamente"]);

        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al eliminar producto: " . $e->getMessage()]);
        }
    }

    public function vaciarCarrito() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $idUsuario = $data['idUsuario'];
        
        if (!$data || !$idUsuario) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        try {

            if (!$this->isUsuarioId($idUsuario)) {
                echo json_encode(["error" => "Usuario no encontrado"]);
                return;
            }

            $sql = "DELETE d FROM carrito_detalle d
                    INNER JOIN carrito c ON d.id_Carrito = c.id_Carrito
                    WHERE c.id_Usuario = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$idUsuario]);
            echo json_encode(["mensaje" => "Carrito vaciado correctamente"]);

        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al vaciar carrito: " . $e->getMessage()]);
        }
    }

    private function isUsuarioId($id) {
        try {
            $sqlUsuario = "SELECT id_Usuario FROM usuarios WHERE id_Usuario = ?";
            $stmtUsuario = $this->conn->prepare($sqlUsuario);
            $stmtUsuario->execute([$id]);
            $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
            return $usuario;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function isCarritoId($id) {
        try {
            $sqlCarrito = "SELECT id FROM carrito_detalle WHERE id = ?";
            $stmtCarrito = $this->conn->prepare($sqlCarrito);
            $stmtCarrito ->execute([$id]);
            $Carrito = $stmtCarrito->fetch(PDO::FETCH_ASSOC);
            return $Carrito;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>