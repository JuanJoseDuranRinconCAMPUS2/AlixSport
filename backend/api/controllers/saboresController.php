<?php

require_once __DIR__ . '/../config/database.php';


class saboresController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getSabores() {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM sabores ORDER BY id_Sabor");
        $stmt->execute();
        $sabores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($sabores);
    }

    public function getSaborById() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['idSabor'];

        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id del sabores no encontrado, envia el id por el body"]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM sabores WHERE id_Sabor = ?");
        $stmt->execute([$id]);
        $sabor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($sabor) {
            echo json_encode($sabor);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Sabor no encontrado"]);
        }
    }

    public function postSabor() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        if (!isset($data["nombre"])) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre del Sabor es obligatorio"]);
            return;
        }

        try {
            $sql = "INSERT INTO sabores 
                        (nombre_Sabor)
                    VALUES (?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre"]
            ]);

            echo json_encode(["mensaje" => "Sabor creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear el sabor: " . $e->getMessage()]);
        }
    }

    public function putSabor() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['idSabor'];

        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id del sabor no encontrado, envia el id por el body"]);
            return;
        }

        if (!$data) {
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        try {

            if (!$this->isSaborId($id)) {
                http_response_code(404);
                echo json_encode(["error" => "El sabor con ID $id no existe"]);
                return;
            };

            $sql = "UPDATE sabores SET 
                        nombre_Sabor = ?
                    WHERE id_Sabor = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre"],
                $id
            ]);

            echo json_encode(["mensaje" => "Sabor actualizado correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al actualizar el sabor: " . $e->getMessage()]);
        }
    }

    public function deleteSabor() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['idSabor'];

        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id del Sabor no encontrado, envia el id por el body"]);
            return;
        }

        try {

            if (!$this->isSaborId($id)) {
                http_response_code(404);
                echo json_encode(["error" => "El Sabor con ID $id no existe"]);
                return;
            };
            
            $stmt = $this->conn->prepare("DELETE FROM sabores WHERE id_Sabor = ?");
            $stmt->execute([$id]);
            echo json_encode(["mensaje" => "Sabor eliminado correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al eliminar Sabor: " . $e->getMessage()]);
        }
    }

    private function isSaborId($id) {
        try {
            $sql = "SELECT COUNT(*) FROM sabores WHERE id_Sabor = ?";
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