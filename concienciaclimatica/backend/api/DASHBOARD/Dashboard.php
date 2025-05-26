<?php
namespace CLIMATICA\API\Dashboard;
require_once __DIR__ . '/../../vendor/autoload.php';
use CLIMATICA\API\Database as DataBase;

class Dashboard extends DataBase {
    private $data;

    public function __construct($db, $user = 'root', $pass = '') {
        $this->data = array();
        parent::__construct($db, $user, $pass);
    }

    public function obtenerDatos($pregunta){
        switch ($pregunta) {
            case 'p1':
                $sql = "SELECT CASE vw_iniciativa WHEN 1 THEN 'Sí' ELSE 'No' END AS respuesta, COUNT(*) AS total FROM encuestas GROUP BY vw_iniciativa";
                break;
            case 'p2':
                $sql = "SELECT CASE audi_iniciativa WHEN 1 THEN 'Sí' ELSE 'No' END AS respuesta, COUNT(*) AS total FROM encuestas GROUP BY audi_iniciativa";
                break;
            case 'p3':
                $sql = "SELECT valor AS respuesta, COUNT(*) AS total FROM (
                    SELECT JSON_UNQUOTE(JSON_EXTRACT(vw_conocidas, CONCAT('$[', n.n, ']'))) AS valor
                    FROM encuestas
                    JOIN (SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4) AS n
                    WHERE JSON_LENGTH(vw_conocidas) > n.n
                ) AS sub GROUP BY valor";
                break;
            case 'p4':
                $sql = "SELECT valor AS respuesta, COUNT(*) AS total FROM (
                    SELECT JSON_UNQUOTE(JSON_EXTRACT(audi_conocidas, CONCAT('$[', n.n, ']'))) AS valor
                    FROM encuestas
                    JOIN (SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4) AS n
                    WHERE JSON_LENGTH(audi_conocidas) > n.n
                ) AS sub GROUP BY valor";
                break;
            case 'p5':
                $sql = "SELECT info_vw AS respuesta, COUNT(*) AS total FROM encuestas GROUP BY info_vw";
                break;
            case 'p6':
                $sql = "SELECT info_audi AS respuesta, COUNT(*) AS total FROM encuestas GROUP BY info_audi";
                break;
            case 'p7':
                $sql = "SELECT valor AS respuesta, COUNT(*) AS total FROM (
                    SELECT JSON_UNQUOTE(JSON_EXTRACT(medios, CONCAT('$[', n.n, ']'))) AS valor
                    FROM encuestas
                    JOIN (SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5) AS n
                    WHERE JSON_LENGTH(medios) > n.n
                ) AS sub GROUP BY valor";
                break;
            case 'p8':
                $sql = "SELECT suficiencia AS respuesta, COUNT(*) AS total FROM encuestas GROUP BY suficiencia";
                break;
            case 'p9':
                $sql = "SELECT relevancia AS respuesta, COUNT(*) AS total FROM encuestas GROUP BY relevancia";
                break;
            case 'p10':
                $sql = "SELECT valor AS respuesta, COUNT(*) AS total FROM (
                    SELECT JSON_UNQUOTE(JSON_EXTRACT(mejoras, CONCAT('$[', n.n, ']'))) AS valor
                    FROM encuestas
                    JOIN (SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6) AS n
                    WHERE JSON_LENGTH(mejoras) > n.n
                ) AS sub GROUP BY valor";
                break;
            default:
                return ['error' => 'Pregunta no válida'];
        }

        $result = $this->conexion->query($sql);
        $this->data = [];

        while ($row = $result->fetch_assoc()) {
            $this->data[] = $row;
        }

        return $this->data;
    }

    public function getHuellaTotal() {
        $this->data = [];

        $sql = "SELECT numero_pregunta, ROUND(AVG(puntaje), 2) AS promedio 
                FROM huella_usuario_respuestas 
                GROUP BY numero_pregunta 
                ORDER BY numero_pregunta";

        $resultado = $this->conexion->query($sql);

        while ($fila = $resultado->fetch_assoc()) {
            $this->data[] = $fila;
        }

        return $this->data;
    }

    // Obtener huella por usuario
    public function getHuellaPorUsuario($idUsuario) {
        $this->data = [];

        $sql = "SELECT numero_pregunta, puntaje 
                FROM huella_usuario_respuestas 
                WHERE id_usuario = ? 
                ORDER BY numero_pregunta";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($fila = $resultado->fetch_assoc()) {
            $this->data[] = $fila;
        }

        return $this->data;
    }
        
}