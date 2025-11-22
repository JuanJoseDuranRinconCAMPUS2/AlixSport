<?php

require_once __DIR__ . '/../config/database.php';


class categoriasController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getCategorias() {
        header('Content-Type: application/json');
        $stmt = $this->conn->prepare("SELECT * FROM categorias ORDER BY id_Categoria");
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($categorias);
    }

    public function getCategoriaById() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        $id = $data['idCategoria'];
        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id de la categoria no encontrado, envia el id por el body"]);
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM categorias WHERE id_Categoria = ?");
        $stmt->execute([$id]);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($categoria) {
            echo json_encode($categoria);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "categoria no encontrada"]);
        }
    }

    public function postCategoria() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        if (!isset($data["nombre"])) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre de la categoria es obligatorio"]);
            return;
        }

        try {
            $sql = "INSERT INTO categorias 
                        (nombre_Categoria)
                    VALUES (?)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre"]
            ]);

            echo json_encode(["mensaje" => "categoria creado correctamente"]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(["error" => "Error al crear la categoria: " . $e->getMessage()]);
        }
    }

    public function putCategoria() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        $id = $data['idCategoria'];
        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id de la categoria no encontrado, envia el id por el body"]);
            return;
        }

        try {

            if (!$this->isCategoriaId($id)) {
                http_response_code(404);
                echo json_encode(["error" => "La categoria con ID $id no existe"]);
                return;
            };

            $sql = "UPDATE categorias SET 
                        nombre_Categoria = ?
                    WHERE id_Categoria = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $data["nombre"],
                $id
            ]);

            echo json_encode(["mensaje" => "categoria actualizada correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al actualizar la categoria: " . $e->getMessage()]);
        }
    }

    public function deleteCategoria() {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            echo json_encode(["error" => "Datos inválidos o vacíos"]);
            return;
        }

        $id = $data['idCategoria'];
        if (!$id) {
            http_response_code(404);
            echo json_encode(["error" => "id de la categoria no encontrado, envia el id por el body"]);
            return;
        }

        try {

            if (!$this->isCategoriaId($id)) {
                http_response_code(404);
                echo json_encode(["error" => "El categoria con ID $id no existe"]);
                return;
            };
            
            $stmt = $this->conn->prepare("DELETE FROM categorias WHERE id_Categoria = ?");
            $stmt->execute([$id]);
            echo json_encode(["mensaje" => "categoria eliminada correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error al eliminar categoria: " . $e->getMessage()]);
        }
    }

    private function isCategoriaId($id) {
        try {
            $sql = "SELECT COUNT(*) FROM categorias WHERE id_Categoria = ?";
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