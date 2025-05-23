<?php
    namespace CLIMATICA\API\LOGIN;
    require __DIR__ . "../../vendor/autoload.php";
    use CLIMATICA\API\Database as DataBase;

    class Usuario extends DataBase{
        private $data;

        public function __construct($db, $user='root', $pass='') {
            $this->data = array();
            parent::__construct($db, $user, $pass);
        }

        public function login($jsonOBJ){
            $this->data = [
                'status' => 'error',
                'messege' => 'usuario invalido'
            ];

            if(isset($jsonOBJ->correo) && isset($jsonOBJ->password)){
                $sql = "SELECT * FROM usuarios WHERE correo = {$jsonOBJ->correo}";
                $result = $this->conexion->query($sql);

                if($result && $result->num_rows ===1){
                    $usuario = $result->fetch_assoc();

                    if (password_verify($jsonOBJ->password, $usuario['password'])){
                    $this->data['status'] = 'success';
                    $this->data['message'] = 'Inicio exitoso';
                    }
                }
                $result->free();
            }
            $this->conexion->close();
        }

        public function signup($jsonOBJ){
            $this->data = [
                'status' => 'error',
                'messege' => 'Correo ya registrado'
            ];

            if(isset($jsonOBJ->correo)){
                $sql = "SELECT * FROM usuarios WHERE correo = {$jsonOBJ->correo}";
                $result = $this->conexion->query($sql);

                if($result->num_rows === 0){
                    $this->conexion->set_charset("utf8");
                    $sql = "INSERT INTO productos VALUES ('{$jsonOBJ->nombre}', '{$jsonOBJ->correo}', '{$jsonOBJ->password}', {$jsonOBJ->edad}, '{$jsonOBJ->zona}'";
                }
            }
        }
    }
?>