<?php
namespace CLIMATICA\API\PROPUESTA; 
require_once __DIR__ . '/../../vendor/autoload.php';

USE CLIMATICA\API\DataBase as DataBase;

class Propuesta extends DataBase{
    private $data; 

    public function __construct($db, $user = 'root', $pass=''){
        parent::__construct($db, $user, $pass); 
        $this->data=[]; 
    }

    
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

public function obtenerTodas() {
    $sql = "SELECT id, nombre, descripcion, niveles, imagen FROM propuestas ORDER BY id DESC";
    $result = $this->conexion->query($sql);

    $propuestas = [];
    while ($row = $result->fetch_assoc()) {
        $propuestas[] = $row;
    }

    return $propuestas; // se convierte a JSON en el endpoint
}



}
?>