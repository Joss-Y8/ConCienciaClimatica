<?php
namespace CLIMATICA\API\MAPA; 
require_once __DIR__ . '/../../vendor/autoload.php';

USE CLIMATICA\API\DataBase as DataBase;

class Mapa extends DataBase{
    private $data; 

    public function __construct($db, $user = 'root', $pass=''){
        parent::__construct($db, $user, $pass); 
        $this->data=[]; 
    }

    // Obtener eventos vigentes
    public function obtenerEventos() {
        $sql = "SELECT id, nombre, descripcion, latitud, longitud, ubicacion, fecha 
                FROM eventos_mapa 
                WHERE eliminado = 0 AND fecha >= CURDATE()
                ORDER BY fecha ASC";

        $result = $this->conexion->query($sql);
        $eventos = [];

        while ($row = $result->fetch_assoc()) {
            $eventos[] = $row;
        }

        return $eventos;
    }

    // Eliminar definitivamente eventos pasados
    public function eliminarEventosPasados() {
        $sql = "DELETE FROM eventos_mapa WHERE fecha < CURDATE()";

        try {
            $this->conexion->query($sql);
            return ['success' => true, 'message' => 'Eventos pasados eliminados'];
        } catch (\mysqli_sql_exception $e) {
            return ['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }
}
?>