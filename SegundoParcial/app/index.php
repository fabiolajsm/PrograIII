<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

require '../clases/UsuarioDAO.php';
require_once '../clases/UsuarioController.php';
require '../clases/ProductoDAO.php';
require_once '../clases/ProductoController.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->get('[/]', function (Request $request, Response $response) {
    $payload = json_encode(array('method' => 'GET', 'msg' => "Bienvenido a mi primera chambaa"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$pdo = new PDO('mysql:host=localhost;dbname=segundoparcial;charset=utf8', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
$usuarioDAO = new UsuarioDAO($pdo);
$usuarioController = new UsuarioController($usuarioDAO);

$app->post('/usuarios', function (Request $request, Response $response, $args) use ($usuarioController) {
    $request = $request->withParsedBody($request->getParsedBody());
    return $usuarioController->crearUsuario($request, $response);
});

$app->get('/usuarios', function (Request $request, Response $response, $args) use ($usuarioController) {
    return $usuarioController->listarUsuarios($response);
});

$productoDAO = new ProductoDAO($pdo); 
$productoController = new ProductoController($productoDAO); 
$app->post('/productos', function (Request $request, Response $response) use ($productoController) {
    $request = $request->withParsedBody($request->getParsedBody());
    return $productoController->crearProducto($request, $response);
});

$app->get('/productos', function (Request $request, Response $response) use ($productoController) {
    return $productoController->listarProductos($response);
});

$app->run();
?>