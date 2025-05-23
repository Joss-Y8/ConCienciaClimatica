<?php
    namespace CLIMATICA\API;
    require_once __DIR__ . '../vendor/autoload.php';

    abstract class Database{
        protected $conexion;

        public function __construct($db, $user, $pass){
            $this->conexion = @mysqli_connect(
                'localhost',
                $user,
                $pass,
                $db
            );

            if(!$this->conexion){
                die('Base de datos no encontrada');
            }
        }
    }
?>