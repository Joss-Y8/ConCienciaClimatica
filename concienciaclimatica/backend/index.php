<?php
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use CLIMATICA\API\USUARIO\Usuario;
use CLIMATICA\API\ENCUESTA\Encuesta;

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
$BD_PASS = ''; 

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

$app->run();
?>