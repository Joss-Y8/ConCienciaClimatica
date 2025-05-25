<?php
session_start();
if (!isset($_SESSION['usuario_correo'])) {
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
    exit;
}

require __DIR__ . "/../../vendor/autoload.php";
use CLIMATICA\API\Database as DataBase;

$db = new DataBase('nombre_de_tu_base_de_datos');
$conexion = $db->getConnection();

$correo = $_SESSION['usuario_correo'];
$stmt = $conexion->prepare("SELECT nombre, correo, zona FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($usuario = $result->fetch_assoc()) {
    echo json_encode(['status' => 'success', 'perfil' => $usuario]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Perfil no encontrado']);
}

$stmt->close();
$conexion->close();
?>
