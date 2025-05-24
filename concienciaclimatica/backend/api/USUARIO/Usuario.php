<?php
namespace CLIMATICA\API\LOGIN;

require_once __DIR__ . '/../Database.php';

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
}
?>
