<?php
namespace CLIMATICA\API;

abstract class Database {
    protected $conexion;

    public function __construct($db, $user, $pass) {
        $this->conexion = @mysqli_connect('localhost', $user, $pass, $db);

        if (!$this->conexion) {
            die('Base de datos no encontrada');
        }
    }
}
?>