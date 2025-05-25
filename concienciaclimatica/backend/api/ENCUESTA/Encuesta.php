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

    /*public function addEncuesta($jsonOBJ) {
    $this->data = [
        'status' => 'error',
        'message' => 'No se pudo registrar la encuesta'
    ];

    $this->conexion->set_charset("utf8");

    // Validar que id_usuario no esté vacío
    if (!isset($jsonOBJ->id_usuario)) {
        $this->data['message'] = 'Falta id_usuario';
        return $this->data;
    }

    // Convertir arreglos a JSON si están definidos
    $vw_conocidas   = json_encode($jsonOBJ->vw_conocidas ?? []);
    $audi_conocidas = json_encode($jsonOBJ->audi_conocidas ?? []);
    $medios         = json_encode($jsonOBJ->medios ?? []);
    $mejoras        = json_encode($jsonOBJ->mejoras ?? []);

    $suficiencia = $this->conexion->real_escape_string($jsonOBJ->suficiencia ?? '');
    $relevancia  = $this->conexion->real_escape_string($jsonOBJ->relevancia ?? '');

    $sql = "INSERT INTO encuestas VALUES (
        null,
        {$jsonOBJ->id_usuario},
        {$jsonOBJ->vw_iniciativa},
        {$jsonOBJ->audi_iniciativa},
        '$vw_conocidas',
        '$audi_conocidas',
        {$jsonOBJ->info_vw},
        {$jsonOBJ->info_audi},
        '$medios',
        '$suficiencia',
        '$relevancia',
        '$mejoras'
    )";

    if ($this->conexion->query($sql)) {
        $this->data['status'] = 'success';
        $this->data['message'] = 'Encuesta agregada correctamente';
    } else {
        $this->data['message'] = 'ERROR SQL: ' . $this->conexion->error;
    }

    return $this->data;
}*/

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
        return $this->data;
    }
//Huella de Carbono
    public function addHuellaCarbono($jsonOBJ){
        $this->data = array(
            'status' => 'error',
            'message' => 'No se pudo registrar la huella de carbono'
        );
        $this->conexion->set_charset("utf8");

        $contadorResp = 0;
        foreach($jsonOBJ->respuestas as $respuesta){

        $sql = "INSERT INTO huella_usuario_respuestas (id_usuario, numero_pregunta, puntaje) VALUES (
        {$jsonOBJ->id_usuario},
        {$respuesta-> numero_pregunta},
        {$respuesta->puntaje}
        )"; 

        if($this->conexion->query($sql)){
            $contadorResp++;
        } 
        }
        
        if ($contadorResp > 0) {
        $this->data['status'] = 'success';
        $this->data['message'] = "Se guardaron {$contadorResp} respuestas correctamente";
        $this->data['respuestas_guardadas'] = $contadorResp;
        } 

        return $this->data; 
    }
}
