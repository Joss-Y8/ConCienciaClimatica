<?php
namespace CLIMATICA\API\USUARIO;

require_once __DIR__ . '/../DataBase.php';

use CLIMATICA\API\DataBase;

class Usuario extends DataBase {
    private $data;

    public function __construct($db, $user='root', $pass='') {
        parent::__construct($db, $user, $pass);
        $this->data = [];
    }

    public function login($jsonOBJ) {
        $this->data = ['status' => 'error', 'message' => 'Usuario o contraseña inválidos'];

        if (isset($jsonOBJ->correo) && isset($jsonOBJ->password)) {
            $correo = $this->conexion->real_escape_string($jsonOBJ->correo);
            $sql = "SELECT * FROM usuarios WHERE correo = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $usuario = $result->fetch_assoc();

                if (password_verify($jsonOBJ->password, $usuario['password'])) {
                    $_SESSION['id_usuario'] = $usuario['id']; 
                    $this->data = [
                        'status' => 'success',
                        'message' => 'Inicio de sesión exitoso',
                        'usuario' => [
                            'nombre' => $usuario['nombre'],
                            'apellido' => $usuario['apellido'],
                            'correo' => $usuario['correo']
                        ]
                    ];
                }
            }

            $stmt->close();
        }

        return $this->data;
    }

    public function signup($jsonOBJ) {
        $this->data = ['status' => 'error', 'message' => 'El correo ya está registrado'];

        if (isset($jsonOBJ->correo) && isset($jsonOBJ->password)) {
            $correo = $this->conexion->real_escape_string($jsonOBJ->correo);
            $sql = "SELECT * FROM usuarios WHERE correo = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $nombre   = $this->conexion->real_escape_string($jsonOBJ->nombre);
                $apellido = $this->conexion->real_escape_string($jsonOBJ->apellido ?? '');
                $password = password_hash($jsonOBJ->password, PASSWORD_DEFAULT);
                $edad     = intval($jsonOBJ->edad ?? 0);
                $zona     = $this->conexion->real_escape_string($jsonOBJ->zona ?? '');

                $insert = "INSERT INTO usuarios (nombre, apellido, correo, password, edad, zona)
                           VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->conexion->prepare($insert);
                $stmt->bind_param("ssssis", $nombre, $apellido, $correo, $password, $edad, $zona);

                if ($stmt->execute()) {
                    $this->data = [
                        'status' => 'success',
                        'message' => 'Usuario registrado correctamente'
                    ];
                } else {
                    $this->data['message'] = 'Error al registrar usuario';
                }
            }

            $stmt->close();
        }

        return $this->data;
    }

    public function logout(){
        session_destroy(); 
        return['status' => 'succes', 'message' => 'Tu sesión ha sido cerrada correctamente. ¡Vuelve pronto']; 
    }

    //Utilizamos la función para obtener los datos del perfil
    public function obtenerPerfil($idUsuario){
        $sql = "SELECT id, nombre, apellido, correo, edad, zona FROM usuarios WHERE id=?"; 
        $stmt = $this->conexion->prepare($sql); 
        $stmt->bind_param("i", $idUsuario); 
        $stmt->execute(); 
        $result = $stmt->get_result(); 

        if($result->num_rows === 1){
            $this->data = $result->fetch_assoc(); 
        }else {
            $this->data = ['status' => 'error', 'messagge'=> 'Usuario no encontrado']; 
        }

        $stmt->close(); 
        return $this->data; 
    }

    public function actualizarPerfil($idUsuario, $jsonOBJ){
        $nombre   = $this->conexion->real_escape_string($jsonOBJ->nombre ?? '');
        $apellido = $this->conexion->real_escape_string($jsonOBJ->apellido ?? '');
        $correo   = $this->conexion->real_escape_string($jsonOBJ->correo ?? '');
        $edad     = intval($jsonOBJ->edad ?? 0);
        $zona     = $this->conexion->real_escape_string($jsonOBJ->zona ?? '');

        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, edad = ?, zona = ? WHERE id= ?"; 
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssisi", $nombre, $apellido, $correo, $edad, $zona, $idUsuario); 

        if($stmt->execute()){
            $this->data = ['status' => 'success', 'message' => 'Perfil actualizado correctamente']; 
        } else {
            $this->data = ['status' => 'error', 'message' => 'Error al actualizar']; 
        }

        $stmt->close(); 
        return $this->data; 
    }
}
?>
