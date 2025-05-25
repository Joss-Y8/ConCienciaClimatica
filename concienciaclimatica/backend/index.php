<?php
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CLIMATICA\API\USUARIO\Usuario;
use CLIMATICA\API\ENCUESTA\Encuesta;
use CLIMATICA\API\ACTIVIDAD\Actividad; 
use CLIMATICA\API\PROPUESTA\Propuesta; 

// Autoload de Composer
require_once __DIR__ . '/vendor/autoload.php';

session_start();

// Crear la app Slim
$app = AppFactory::create();
// Ruta base del proyecto
$app->setBasePath("/ConCienciaClimatica/concienciaclimatica/backend");

// Middleware para CORS
$app->add(function (Request $request, $handler): Response {
    $response = $handler->handle($request);
    
    // Permitir CORS
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    
    // Manejar preflight requests
    if ($request->getMethod() === 'OPTIONS') {
        return $response->withStatus(200);
    }

    return $response;
});

$app->addRoutingMiddleware();

// VARIABLES DE CONEXIÓN
$BD_NAME = 'concienciaclimatica'; 
$BD_USER = 'root'; 
$BD_PASS = 'jojoyrl8'; 

// RUTAS

// Test
$app->get('/test', function (Request $request, Response $response) {
    $response->getBody()->write("¡Funciona!");
    return $response;
});

// Ruta de prueba
$app->get('/prueba', function (Request $request, Response $response) {
    $response->getBody()->write('Ruta de prueba funcionando.');
    return $response;
});

// Login
$app->post('/login', function(Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS){
    $datos = json_decode($request->getBody()); 
    $usuario = new Usuario($BD_NAME, $BD_USER, $BD_PASS); 
    $result = $usuario->login($datos); 
    $response->getBody()->write(json_encode($result)); 
    return $response->withHeader('Content-Type', 'application/json'); 
});

// Signup
$app->post('/signup', function(Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS){
    $datos = json_decode($request->getBody()); 
    $usuario = new Usuario($BD_NAME, $BD_USER, $BD_PASS); 
    $result = $usuario->signup($datos); 
    $response->getBody()->write(json_encode($result)); 
    return $response->withHeader('Content-Type', 'application/json'); 
});

$app->get('/perfil', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    if (!isset($_SESSION['id_usuario'])) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    $usuario = new Usuario($BD_NAME, $BD_USER, $BD_PASS);
    $result = $usuario->obtenerPerfil($_SESSION['id_usuario']);
    $insignias = $usuario->obtenerInsignias($_SESSION['id_usuario']);
    $result['insignias_ganadas'] = $insignias;
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});


// Actualizar perfil
$app->put('/perfil', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    if (!isset($_SESSION['id_usuario'])) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    $datos = json_decode($request->getBody());
    $usuario = new Usuario($BD_NAME, $BD_USER, $BD_PASS);
    $result = $usuario->actualizarPerfil($_SESSION['id_usuario'], $datos);
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

// Logout
$app->post('/logout', function (Request $request, Response $response) {
    session_destroy();
    $response->getBody()->write(json_encode(['status' => 'success', 'message' => 'Sesión cerrada']));
    return $response->withHeader('Content-Type', 'application/json');
});

//Encuesta
/*$app->post('/encuesta', function($request, $response, $args){

    if (!isset($_SESSION['id_usuario'])) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $data = json_decode($request->getBody()->getContents());
    $data->id_usuario = $_SESSION['id_usuario'];

    $encuesta = new Encuesta('concienciaclimatica');
    $resultado = $encuesta->addEncuesta($data);

    $response->getBody()->write(json_encode($resultado));
    return $response->withHeader('Content-Type', 'application/json');
});*/

//Encuesta
$app->post('/encuesta', function($request, $response, $args){
    if (!isset($_SESSION['id_usuario'])) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    $encuesta = new Encuesta('concienciaclimatica');
    $data = json_decode($request->getBody()->getContents());
    $data->id_usuario = $_SESSION['id_usuario'];
    $resultado = $encuesta->addEncuesta($data);
    $response->getBody()->write(json_encode($resultado));
    return $response->withHeader('Content-Type', 'application/json');
});

//Actividades
$app->get('/actividades', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    if (!isset($_SESSION['id_usuario'])) {
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json')
                        ->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
    }

    $actividad = new Actividad($BD_NAME, $BD_USER, $BD_PASS);
    $result = $actividad->obtenerActividades($_SESSION['id_usuario']);
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});


// Iniciar actividad
$app->post('/actividad/iniciar', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    if (!isset($_SESSION['id_usuario'])) {
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json')
                        ->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
    }

    $datos = json_decode($request->getBody());
    if (!isset($datos->id_actividad)) {
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json')
                        ->write(json_encode(['status' => 'error', 'message' => 'Falta ID de actividad']));
    }

    $actividad = new Actividad($BD_NAME, $BD_USER, $BD_PASS);
    $result = $actividad->iniciarActividad($_SESSION['id_usuario'], $datos->id_actividad);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

// Actualizar progreso
$app->put('/actividad/progreso', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    if (!isset($_SESSION['id_usuario'])) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    $datos = json_decode($request->getBody());
    if (!isset($datos->id_actividad)) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'ID de actividad no recibido']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $cantidad = isset($datos->cantidad) ? intval($datos->cantidad) : 1;

    $actividad = new \CLIMATICA\API\ACTIVIDAD\Actividad($BD_NAME, $BD_USER, $BD_PASS);
    $result = $actividad->actualizarProgreso($_SESSION['id_usuario'], $datos->id_actividad, $cantidad);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});


// Pasar al siguiente nivel
$app->put('/actividad/nivel', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    if (!isset($_SESSION['id_usuario'])) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'No autenticado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    $datos = json_decode($request->getBody());
    if (!isset($datos->id_actividad)) {
        $response->getBody()->write(json_encode(['status' => 'error', 'message' => 'Falta ID de actividad']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $actividad = new Actividad($BD_NAME, $BD_USER, $BD_PASS);
    $result = $actividad->pasarAlSiguienteNivel($_SESSION['id_usuario'], $datos->id_actividad);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/propuesta', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    $propuesta = new Propuesta($BD_NAME, $BD_USER, $BD_PASS);
    $propuesta->registrar(); // ← no le pasa nadaaaaa
    $response->getBody()->write($propuesta->getData());
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/propuestas', function (Request $request, Response $response) use ($BD_NAME, $BD_USER, $BD_PASS) {
    $propuesta = new \CLIMATICA\API\PROPUESTA\Propuesta($BD_NAME, $BD_USER, $BD_PASS);
    $resultado = $propuesta->obtenerTodas();
    $response->getBody()->write(json_encode($resultado));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
?>