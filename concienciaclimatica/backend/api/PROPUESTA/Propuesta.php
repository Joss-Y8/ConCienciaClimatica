<?php
namespace CLIMATICA\API\PROPUESTA; 

require_once __DIR__ . '/../../vendor/autoload.php';
use CLIMATICA\API\DataBase as DataBase;

class Propuesta extends DataBase {
    private $data; 

    public function __construct($db, $user = 'root', $pass='') {
        parent::__construct($db, $user, $pass); 
        $this->data = []; 
    }

    // Registrar nueva propuesta
    public function registrar() {
        $this->data = ['success' => false, 'message' => 'Datos inválidos'];
        
        $input = $_POST;
        $files = $_FILES;

        $nombre = $input['nombre'] ?? null;
        $descripcion = $input['descripcion'] ?? null;
        $niveles = $input['niveles'] ?? null;
        $imagen = $files['imagen'] ?? null;

        if (!$nombre || !$descripcion || !$niveles || !$imagen) {
            $this->data['message'] = 'Faltan datos';
            return;
        }

        if ($imagen['error'] !== UPLOAD_ERR_OK) {
            $this->data['message'] = 'Error al subir imagen';
            return;
        }

        $nombreArchivo = basename($imagen['name']);
        $rutaFisica = __DIR__ . "/../../../assets/img/insignias/" . $nombreArchivo;
        move_uploaded_file($imagen['tmp_name'], $rutaFisica);

        $rutaRelativa = "assets/img/insignias/" . $nombreArchivo;

        $stmt = $this->conexion->prepare("INSERT INTO propuestas (id_usuario, nombre, descripcion, niveles, imagen, completadas) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("issss", $_SESSION['id_usuario'], $nombre, $descripcion, $niveles, $rutaRelativa);

        if ($stmt->execute()) {
            $this->data = ['success' => true, 'message' => 'Propuesta registrada'];
        } else {
            $this->data['message'] = 'Error en BD: ' . $stmt->error;
        }

        $stmt->close();
    }

    public function getData() {
        return json_encode($this->data);
    }

    // Obtener todas las propuestas
    public function obtenerTodas() {
        $sql = "SELECT id, nombre, descripcion, niveles, imagen, id_usuario FROM propuestas ORDER BY id DESC";
        $result = $this->conexion->query($sql);

        $propuestas = [];
        while ($row = $result->fetch_assoc()) {
            $propuestas[] = $row;
        }

        return $propuestas;
    }

    // Obtener una propuesta por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM propuestas WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            return $res->fetch_assoc();
        } else {
            return ['error' => 'Propuesta no encontrada'];
        }
    }

    // Usuario se une a una propuesta
    public function unirse($idUsuario, $idPropuesta) {
        $sql = "INSERT INTO usuarios_propuestas (id_usuario, id_propuesta) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idUsuario, $idPropuesta);

        try {
            $stmt->execute();
            return ['success' => true, 'message' => 'Unido a la propuesta'];
        } catch (\mysqli_sql_exception $e) {
            return ['success' => false, 'message' => 'Ya estás unido o error: ' . $e->getMessage()];
        }
    }

    // Usuario completa una propuesta
    public function completar($idUsuario, $idPropuesta) {
        $this->conexion->begin_transaction();

        try {
            // 1. Marcar propuesta como completada
            $sql = "UPDATE usuarios_propuestas 
                    SET completada = 1, fecha_fin = CURRENT_TIMESTAMP()
                    WHERE id_usuario = ? AND id_propuesta = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("ii", $idUsuario, $idPropuesta);
            $stmt->execute();

            // 2. Sumar al contador global de completadas
            $sql2 = "UPDATE propuestas 
                     SET completadas = completadas + 1 
                     WHERE id = ?";
            $stmt2 = $this->conexion->prepare($sql2);
            $stmt2->bind_param("i", $idPropuesta);
            $stmt2->execute();

            // 3. Verificar si se alcanza la meta de 15 completadas
            $sql3 = "SELECT completadas, id_usuario FROM propuestas WHERE id = ?";
            $stmt3 = $this->conexion->prepare($sql3);
            $stmt3->bind_param("i", $idPropuesta);
            $stmt3->execute();
            $res3 = $stmt3->get_result();
            $datos = $res3->fetch_assoc();
            
            //segun yo puede asignar la insginia pero hay que revisar bien porque no probe esto
            if ($datos && intval($datos['completadas']) >= 15) {
                $idCreador = intval($datos['id_usuario']);
                $insigniaId = 10;

                $check = "SELECT 1 FROM usuarios_insignias WHERE id_usuario = ? AND id_insignia = ?";
                $stmt4 = $this->conexion->prepare($check);
                $stmt4->bind_param("ii", $idCreador, $insigniaId);
                $stmt4->execute();
                $res4 = $stmt4->get_result();

                if ($res4->num_rows === 0) {
                    $insert = "INSERT INTO usuarios_insignias (id_usuario, id_insignia) VALUES (?, ?)";
                    $stmt5 = $this->conexion->prepare($insert);
                    $stmt5->bind_param("ii", $idCreador, $insigniaId);
                    $stmt5->execute();
                }
            }

            $this->conexion->commit();
            return ['success' => true, 'message' => 'Propuesta completada'];
        } catch (\mysqli_sql_exception $e) {
            $this->conexion->rollback();
            return ['success' => false, 'message' => 'Error al completar: ' . $e->getMessage()];
        }
    }

    // Obtener propuestas junto a estado del usuario (unido/completado)
    public function obtenerPropuestas($idUsuario) {
        $sql = "
            SELECT p.*, 
                   up.completada IS NOT NULL AS unido,
                   up.completada = 1 AS completado
            FROM propuestas p
            LEFT JOIN usuarios_propuestas up 
                ON up.id_propuesta = p.id AND up.id_usuario = ?
            ORDER BY p.id DESC
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $res = $stmt->get_result();

        $propuestas = [];
        while ($row = $res->fetch_assoc()) {
            $propuestas[] = $row;
        }

        return $propuestas;
    }
}
?>
