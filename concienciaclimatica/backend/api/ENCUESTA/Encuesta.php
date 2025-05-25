<?php
namespace CLIMATICA\API\Encuesta;
require_once __DIR__ . '/../../vendor/autoload.php';
use CLIMATICA\API\Database as DataBase;

class Encuesta extends DataBase {
    private $data;

    public function __construct($db, $user = 'root', $pass = '') {
        $this->data = array();
        parent::__construct($db, $user, $pass);
    }

    public function addEncuesta($jsonOBJ) {
        $this->data = array(
            'status' => 'error',
            'message' => 'No se pudo registrar la encuesta'
        );

        $this->conexion->set_charset("utf8");

        // De array a json
        $vw_conocidas = json_encode($jsonOBJ->vw_conocidas);
        $audi_conocidas = json_encode($jsonOBJ->audi_conocidas);
        $medios = json_encode($jsonOBJ->medios);
        $mejoras = json_encode($jsonOBJ->mejoras);

        $sql = "INSERT INTO encuestas VALUES (
            null,
            {$jsonOBJ->id_usuario},
            {$jsonOBJ->vw_iniciativa},
            {$jsonOBJ->audi_iniciativa},
            '{$vw_conocidas}',
            '{$audi_conocidas}',
            {$jsonOBJ->info_vw},
            {$jsonOBJ->info_audi},
            '{$medios}',
            '{$jsonOBJ->suficiencia}',
            '{$jsonOBJ->relevancia}',
            '{$mejoras}'
        )";

        if ($this->conexion->query($sql)) {
            $this->data['status'] = 'success';
            $this->data['message'] = 'Encuesta agregada correctamente';
        } else {
            $this->data['message'] = 'ERROR: ' . $this->conexion->error;
        }

        $this->conexion->close();
    }

    public function getData() {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }
}
