<?php

require_once __DIR__ . '/../config/database.php';


class loginUserController {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function saludar() {
        header('Content-Type: application/json');

        $db = new Database();
        $conn = $db->getConnection();

        if ($conn) {
            echo "✅ Conexión exitosa\n";
            $stmt = $conn->query("SHOW DATABASES;");
            $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
            print_r($dbs);

            $this->enviarCorreo('juanjoseduranrincon404@gmail.com', "emailRegistro.html", "Registro exitoso en AlixSpor", [
                'nombre' => 'Juan J',
                'email' => 'juanjoseduranrincon404@gmail.com'
            ]);
        } else {
            echo "❌ Error al conectar a MySQL.";
        }

        echo json_encode([
            "mensaje" => "CORREO ENVIADO CORRECTAMENTE"
        ]);
    }

    public function registrarUsuario() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data["nombre"]) || !isset($data["email"]) || !isset($data["password"])) {
            echo json_encode(["status" => "error", "mensaje" => "Datos incompletos"]);
            return;
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email_Usuario = ?");
            $stmt->execute([$data["email"]]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "error", "mensaje" => "El correo ya está registrado, intenta ingresar a la aplicación"]);
                return;
            }

            $passwordHash = password_hash($data["password"], PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuarios (nombre_Usuario, email_Usuario, password_Usuario, rol) VALUES (?, ?, ?, 'CsWscYwyevms1983')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$data["nombre"], $data["email"], $passwordHash]);

            $this->enviarCorreo(
                $data["email"],
                "emailRegistro.html", 
                "Registro exitoso en AlixSpor", 
                [
                    'nombre' => $data["nombre"],
                    'email' => $data["email"]
                ]
            );

            echo json_encode(["status" => "ok", "mensaje" => "El Usuario se ha creado correctamente, revisa el correo para ver mas información"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
        }
    }

    public function loginUsuario() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data["email"]) || !isset($data["password"])) {
            echo json_encode(["status" => "error", "mensaje" => "Datos incompletos"]);
            return;
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email_Usuario = ?");
            $stmt->execute([$data["email"]]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                echo json_encode(["status" => "error", "mensaje" => "No existe un usuario con ese correo"]);
                return;
            }

            if (!password_verify($data["password"], $usuario["password_Usuario"])) {
                echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta"]);
                return;
            }

            echo json_encode([
                "status" => "ok",
                "id" => $usuario["id_Usuario"],
                "nombre" => $usuario["nombre_Usuario"],
                "email" => $usuario["email_Usuario"],
                "rol" => $usuario["rol"]
            ]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
        }
    }

     public function sendCodigoRep() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data["email"];

        if (!$email) {
            echo json_encode(["status" => "error", "mensaje" => "Debes enviar el correo"]);
            return;
        }

        try {

            $stmtUser = $this->conn->prepare("SELECT nombre_Usuario FROM usuarios WHERE email_Usuario = ?");
            $stmtUser->execute([$email]);
            $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                echo json_encode(["status" => "error", "mensaje" => "No existe ningún usuario con el correo proporcionado"]);
                return;
            }

            $nombre = $usuario["nombre_Usuario"];

            $stmt = $this->conn->prepare("
                SELECT id, codigo, fecha_expiracion 
                FROM codigos_recuperacion 
                WHERE email = ? 
                ORDER BY id DESC 
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $codigoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($codigoExistente && strtotime($codigoExistente["fecha_expiracion"]) > time()) {
                $codigo = $codigoExistente["codigo"];

                $this->enviarCorreo(
                    $email,
                    'emailRecovery.html',
                    'Codigo de recuperacion de contrasenia',
                    [
                        'nombre' => $nombre,
                        'codigo' => $codigo,
                        'fecha_envio' => date("d/m/Y H:i:s")
                    ]
                );

                echo json_encode([
                    "status" => "ok",
                    "mensaje" => "Ya existe un código vigente. Se ha reenviado el mismo código al correo " . $email . ". Si no encuentras el correo revisa también tu carpeta de spam"
                ]);
                return;
            } else {
                $stmtDel = $this->conn->prepare("DELETE FROM codigos_recuperacion WHERE id = ?");
                $stmtDel->execute([$codigoExistente["id"]]);
                $codigoExistente = null;
            }

            $codigo = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 6);
            $expiracion = date("Y-m-d H:i:s", strtotime("+6 minutes"));

            $stmt = $this->conn->prepare("INSERT INTO codigos_recuperacion (email, codigo, fecha_expiracion) VALUES (?, ?, ?)");
            $stmt->execute([$email, $codigo, $expiracion]);

            $this->enviarCorreo(
                $email,
                'emailRecovery.html',
                'Codigo de recuperacion de contrasenia',
                [
                    'nombre' => $nombre,
                    'codigo' => $codigo,
                    'fecha_envio' => date("d/m/Y H:i:s")
                ]
            );

            echo json_encode([
                "status" => "ok",
                "mensaje" => "Se ha enviado un nuevo código de recuperación al correo " . $email . ". Si no encuentras el correo revisa también tu carpeta de spam"
            ]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
        }
    }

    public function changePassword() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["email"]) || !isset($data["codigo"]) || !isset($data["nuevaPassword"])) {
            echo json_encode(["status" => "error", "mensaje" => "Datos incompletos"]);
            return;
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM codigos_recuperacion WHERE email = ? AND codigo = ?");
            $stmt->execute([$data["email"], $data["codigo"]]);
            $codigoData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$codigoData) {
                echo json_encode(["status" => "error", "mensaje" => "Código inválido"]);
                return;
            }

            if (strtotime($codigoData["fecha_expiracion"]) < time()) {
                echo json_encode(["status" => "error", "mensaje" => "El código ha expirado"]);
                return;
            }

            $nuevaPasswordHash = password_hash($data["nuevaPassword"], PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("UPDATE usuarios SET password_Usuario = ? WHERE email_Usuario = ?");
            $stmt->execute([$nuevaPasswordHash, $data["email"]]);

            echo json_encode(["status" => "ok", "mensaje" => "Contraseña actualizada correctamente"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "mensaje" => $e->getMessage()]);
        }
    }

    private function enviarCorreo($email, $templateFile, $titleEmail, $params = []) {
        require_once __DIR__ . '/../config/mailerConfig.php';
        $mail = MailerConfig::getMailer();

        try {

            $templatePath = __DIR__ . '/../models/' . $templateFile;

            if (!file_exists($templatePath)) {
                throw new Exception("La plantilla '$templateFile' no existe en /models/");
            }
            
            $htmlTemplate = file_get_contents($templatePath);
            foreach ($params as $key => $value) {
                $htmlTemplate = str_replace('{{' . $key . '}}', htmlspecialchars($value), $htmlTemplate);
            }

            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = $titleEmail;
            $mail->Body = $htmlTemplate;

            $mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar correo de registro: " . $mail->ErrorInfo);
        }
    }

}

?>