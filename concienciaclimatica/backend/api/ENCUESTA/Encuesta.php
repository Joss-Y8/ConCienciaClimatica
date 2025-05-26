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
        $puntajeTotal = 0;
        $contadorResp = 0;
        foreach($jsonOBJ->respuestas as $respuesta){
        $sql = "INSERT INTO huella_usuario_respuestas (id_usuario, numero_pregunta, puntaje) VALUES (
        {$jsonOBJ->id_usuario},
        {$respuesta-> numero_pregunta},
        {$respuesta->puntaje}
        )"; 

        if($this->conexion->query($sql)){
            $contadorResp++;
            $puntajeTotal += $respuesta-> puntaje;
        } 
        }
        
        if ($contadorResp > 0) {
            $puntajeTotal = array_sum(array_column($jsonOBJ->respuestas, 'puntaje'));
            $categoria = $this->clasificarHuella($puntajeTotal);
            $this->data = array (
                'status' => 'success',
                'message' =>'Encuesta Completada',
                'resultado' => array(
                    'puntaje_total' => $puntajeTotal,
                    'categoria'=> $categoria['nombre'],
                    'color' => $categoria['color'],
                    'mensaje_positivo' => $this->getMensajePositivo($categoria['nombre']),
                    'imagen' => $categoria['imagen']
                )
                );
    }
        return $this->data; 
    }

    private function getMensajePositivo($categoria) {
        $mensajes = [
            'Muy baja' => '¡Eres un Amigo del Clima! Hay muchas cosas que estás haciendo bien, sigue asi, cuidemos el planeta.',
            'Moderada' => '¡Buen trabajo! Estás en el camino correcto, sigue mejorarando tus hábitos, cuidemos el planeta.',
            'Alta' => '¡Tu impacto es Alto pero tranquilo, tienes oportunidad de mejorar, cuidemos el planeta!'
        ];
        return $mensajes[$categoria] ?? '';
    }
    public function respuestaExistente($idUsuario) {
        $this->conexion->set_charset("utf8");
        
        $sql = "SELECT COUNT(*) as total FROM huella_usuario_respuestas WHERE id_usuario = {$idUsuario}";
        $resultado = $this->conexion->query($sql);
        
        if ($resultado && $fila = $resultado->fetch_assoc()) {
            return $fila['total'] > 0;
        }
        
        return false;
    }

    private function clasificarHuella($puntaje){
        if ($puntaje <= 70) {
            return ['nombre' => 'Muy baja', 'color' => '#2ecc71', 'imagen' => 'eco-hero.png'];
        } elseif ($puntaje > 70 && $puntaje <= 100) {
            return ['nombre' => 'Moderada', 'color' => '#f39c12', 'imagen' => 'eco-moderate.png'];
            } else {
            return ['nombre' => 'Alta', 'color' => '#e74c3c', 'imagen' => 'eco-high.png'];
            }
    }
}
