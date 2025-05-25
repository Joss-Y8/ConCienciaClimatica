<?php
namespace CLIMATICA\API\ACTIVIDAD; 
require_once __DIR__ . '/../../vendor/autoload.php';

USE CLIMATICA\API\DataBase as DataBase;

class Actividad extends DataBase{
    private $data; 

    public function __construct($db, $user = 'root', $pass=''){
        parent::__construct($db, $user, $pass); 
        $this->data=[]; 
    }

    //Funcion que obtiene todas las actividades READ
    public function obtenerActividades($idUsuario) {
    $this->data = [];

    $sql = "SELECT 
                a.id, a.nombre, a.descripcion, a.imagen,
                ua.nivel_actual, ua.progreso_actual, ua.completada,
                an.meta, an.unidad
            FROM actividades a
            LEFT JOIN usuarios_actividades ua 
                ON a.id = ua.id_actividad AND ua.id_usuario = ?
            LEFT JOIN actividad_niveles an 
                ON an.id_actividad = a.id AND an.nivel = ua.nivel_actual";

    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $this->data[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'imagen' => $row['imagen'],
            'nivel_actual' => isset($row['nivel_actual']) ? (int)$row['nivel_actual'] : 0,
            'progreso_actual' => isset($row['progreso_actual']) ? (int)$row['progreso_actual'] : 0,
            'meta' => isset($row['meta']) ? (int)$row['meta'] : null,
            'unidad' => isset($row['unidad']) ? $row['unidad']: '',
            'iniciada' => isset($row['nivel_actual']),
            'completada' => isset($row['completada']) ? boolval($row['completada']) : false,
            'meta_alcanzada' => (isset($row['meta'], $row['progreso_actual']) && $row['progreso_actual'] >= $row['meta'])
        ];
    }

    return $this->data;
}

    // Iniciar actividad (nivel 1, progreso 0)
    public function iniciarActividad($idUsuario, $idActividad) {
        $sql = "SELECT * FROM usuarios_actividades WHERE id_usuario = ? AND id_actividad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idUsuario, $idActividad);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $insert = "INSERT INTO usuarios_actividades (id_usuario, id_actividad, nivel_actual, progreso_actual, completada, fecha_inicio) 
                       VALUES (?, ?, 1, 0, 0, NOW())";
            $stmt = $this->conexion->prepare($insert);
            $stmt->bind_param("ii", $idUsuario, $idActividad);
            if ($stmt->execute()) {
                return ['status' => 'success', 'message' => 'Actividad iniciada'];
            }
        }

        return ['status' => 'error', 'message' => 'La actividad ya fue iniciada'];
    }

    // Actualizar progreso sumando $cantidad
    public function actualizarProgreso($idUsuario, $idActividad, $cantidad) {
    $sql = "SELECT * FROM usuarios_actividades WHERE id_usuario = ? AND id_actividad = ?";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("ii", $idUsuario, $idActividad);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $datos = $res->fetch_assoc();
        $nivel = $datos['nivel_actual'];
        $progreso = $datos['progreso_actual'];

        // Obtener la meta de ese nivel
        $sqlMeta = "SELECT meta FROM actividad_niveles WHERE id_actividad = ? AND nivel = ?";
        $stmt = $this->conexion->prepare($sqlMeta);
        $stmt->bind_param("ii", $idActividad, $nivel);
        $stmt->execute();
        $resMeta = $stmt->get_result();

        if ($resMeta->num_rows === 1) {
            $meta = $resMeta->fetch_assoc()['meta'];
            $nuevoProgreso = $progreso + intval($cantidad);

            $update = "UPDATE usuarios_actividades SET progreso_actual = ? WHERE id_usuario = ? AND id_actividad = ?";
            $stmt = $this->conexion->prepare($update);
            $stmt->bind_param("iii", $nuevoProgreso, $idUsuario, $idActividad);

            if ($stmt->execute()) {
                if ($nuevoProgreso >= $meta) {
                    return [
                        'status' => 'success',
                        'estado' => 'meta_alcanzada',
                        'message' => 'Has alcanzado la meta del nivel',
                        'nivel_actual' => $nivel,
                        'progreso_actual' => $nuevoProgreso
                    ];
                } else {
                    return [
                        'status' => 'success',
                        'estado' => 'progreso_actualizado',
                        'message' => 'Progreso actualizado',
                        'nivel_actual' => $nivel,
                        'progreso_actual' => $nuevoProgreso
                    ];
                }
            }
        }
    }

    return ['status' => 'error', 'message' => 'No se pudo actualizar el progreso'];
}


    // Subir al siguiente nivel
    public function pasarAlSiguienteNivel($idUsuario, $idActividad) {
        $sql = "SELECT * FROM usuarios_actividades WHERE id_usuario = ? AND id_actividad = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idUsuario, $idActividad);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $datos = $res->fetch_assoc();
            $nivel = $datos['nivel_actual'];
            $progreso = $datos['progreso_actual'];

            // Obtener la meta actual
            $sqlMeta = "SELECT meta FROM actividad_niveles WHERE id_actividad = ? AND nivel = ?";
            $stmt = $this->conexion->prepare($sqlMeta);
            $stmt->bind_param("ii", $idActividad, $nivel);
            $stmt->execute();
            $resMeta = $stmt->get_result();

            if ($resMeta->num_rows === 1 && $progreso >= $resMeta->fetch_assoc()['meta']) {
                $nivelSiguiente = $nivel + 1;

                // ¿Existe siguiente nivel?
                $sqlSig = "SELECT 1 FROM actividad_niveles WHERE id_actividad = ? AND nivel = ?";
                $stmt = $this->conexion->prepare($sqlSig);
                $stmt->bind_param("ii", $idActividad, $nivelSiguiente);
                $stmt->execute();
                $resSig = $stmt->get_result();

                if ($resSig->num_rows > 0) {
                    // Sube de nivel
                    $update = "UPDATE usuarios_actividades SET nivel_actual = ?, progreso_actual = 0 WHERE id_usuario = ? AND id_actividad = ?";
                    $stmt = $this->conexion->prepare($update);
                    $stmt->bind_param("iii", $nivelSiguiente, $idUsuario, $idActividad);
                    if ($stmt->execute()) {
                        return ['status' => 'success', 'estado' => 'nivel_subido', 'message' => 'Avanzaste al siguiente nivel'];
                    }
                } else {
                    // No hay más niveles → actividad completada
                    $update = "UPDATE usuarios_actividades SET completada = 1, fecha_fin = NOW() WHERE id_usuario = ? AND id_actividad = ?";
                    $stmt = $this->conexion->prepare($update);
                    $stmt->bind_param("ii", $idUsuario, $idActividad);
                    if ($stmt->execute()) {
                        return ['status' => 'success', 'estado' => 'completado', 'message' => '¡Actividad completada!'];
                    }
                }
            } else {
                return ['status' => 'error', 'message' => 'Aún no cumples la meta actual'];
            }
        }

        return ['status' => 'error', 'message' => 'No se encontró el progreso del usuario'];
    }

}
?>