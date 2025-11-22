<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . "/../models/pdfFactura.php";

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
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($detalles)) {
                echo json_encode([
                    "detalle" => [],
                    "resumen" => [
                        "productos_diferentes" => 0,
                        "total_items" => 0,
                        "valor_total" => 0
                    ]
                ]);
                return;
            }

            $stmtResumen = $this->conn->prepare("
                SELECT 
                    COUNT(*) AS productos_diferentes,
                    SUM(cd.cantidad) AS total_items,
                    SUM(cd.subtotal) AS valor_total
                FROM carrito c
                INNER JOIN carrito_detalle cd ON c.id_Carrito = cd.id_Carrito
                WHERE c.id_Usuario = ?
            ");
            $stmtResumen->execute([$id]);
            $resumen = $stmtResumen->fetch(PDO::FETCH_ASSOC);

            // Respuesta combinada
            echo json_encode([
                "detalle" => $detalles,
                "resumen" => $resumen
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al obtener detalles: " . $e->getMessage()]);
        }
    }

    public function getCantidadProductos() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['idUsuario'];

        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) AS productos_diferentes,
                    COALESCE(SUM(cd.cantidad), 0) AS total_items
                FROM carrito c
                INNER JOIN carrito_detalle cd ON c.id_Carrito = cd.id_Carrito
                WHERE c.id_Usuario = ?
            ");
            $stmt->execute([$id]);
            $cantidad = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode($cantidad);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al obtener cantidad: " . $e->getMessage()]);
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

    public function updateCantidadCarrito() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['idUsuario']) || !isset($data['idProducto']) || !isset($data['cantidad'])) {
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

            $stmt = $this->conn->prepare("SELECT id_Carrito FROM carrito WHERE id_Usuario = ?");
            $stmt->execute([$idUsuario]);
            $carrito = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$carrito) {
                $stmt = $this->conn->prepare("INSERT INTO carrito (id_Usuario) VALUES (?)");
                $stmt->execute([$idUsuario]);
                $idCarrito = $this->conn->lastInsertId();
            } else {
                $idCarrito = $carrito['id_Carrito'];
            }

            $stmt = $this->conn->prepare("
                SELECT id, cantidad 
                FROM carrito_detalle 
                WHERE id_Carrito = ? AND id_Producto = ?
            ");
            $stmt->execute([$idCarrito, $idProducto]);
            $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cantidad == 0) {
                if ($detalle) {
                    $stmt = $this->conn->prepare("DELETE FROM carrito_detalle WHERE id = ?");
                    $stmt->execute([$detalle['id']]);
                }
                echo json_encode(["mensaje" => "Producto eliminado del carrito"]);
                return;
            }

            $stmt = $this->conn->prepare("SELECT precio_Producto FROM productos WHERE id_Producto = ?");
            $stmt->execute([$idProducto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                echo json_encode(["error" => "Producto no encontrado"]);
                return;
            }

            $precioUnitario = $producto['precio_Producto'];

            if ($detalle) {
                $stmt = $this->conn->prepare("
                    UPDATE carrito_detalle 
                    SET cantidad = ?, precio_unitario = ?
                    WHERE id = ?
                ");
                $stmt->execute([$cantidad, $precioUnitario, $detalle['id']]);

                echo json_encode(["mensaje" => "Cantidad actualizada"]);
            } 

            else {
                $stmt = $this->conn->prepare("
                    INSERT INTO carrito_detalle (id_Carrito, id_Producto, cantidad, precio_unitario)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$idCarrito, $idProducto, $cantidad > 0 ? $cantidad : 1, $precioUnitario]);

                echo json_encode(["mensaje" => "Producto agregado al carrito"]);
            }

        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["error" => "Error al actualizar el carrito: " . $e->getMessage()]);
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

    public function generarFacturaPDF() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $idUsuario = $data["idUsuario"] ?? null;

        if (!$idUsuario) {
            echo json_encode(["status" => "error", "mensaje" => "Falta el idUsuario"]);
            return;
        }

        try {
            
            ob_start();
            $this->getDetallesByUser();
            $detallesJSON = ob_get_clean();
            $productos = json_decode($detallesJSON, true);

            if (!is_array($productos)) {
                echo json_encode(["status" => "error", "mensaje" => "Error obteniendo detalles"]);
                return;
            }

            ob_start();
            $this->getTotalCarrito();
            $totalJSON = ob_get_clean();
            $infoCarrito = json_decode($totalJSON, true);

            $total = $infoCarrito["total"];
            $idCarrito = $infoCarrito["idCarrito"];

            $sqlUser = "SELECT nombre_Usuario FROM usuarios WHERE id_Usuario = ?";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->execute([$idUsuario]);
            $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC)["nombre_Usuario"];

            $html = PdfFacturaModel::generarHTML($usuario, $productos, $total);

            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $nombrePDF = "Factura_User_" . $idUsuario . "_" . time() . ".pdf";
            $rutaArchivo = __DIR__ . "/../facturas/" . $nombrePDF;
            file_put_contents($rutaArchivo, $dompdf->output());

            $url = $_ENV["HOSTNAME"] . ":" . $_ENV["PORT_BACKEND"] . "/facturas/" . $nombrePDF;

            echo json_encode([
                "status" => "ok",
                "mensaje" => "Factura generada correctamente",
                "urlFactura" => $url
            ]);

        } catch (Exception $e) {
            echo json_encode(["status" => "error", "mensaje" => "Error al generar factura: " . $e->getMessage()]);
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