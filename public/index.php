<?php
require_once '../src/Controllers/UserController.php';

//Intercepts requests and attends to them depending on whether they have any parameters

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);


if (isset($uri[2])) {
    $id = $uri[2];
    $controller = new UserController($pdo);

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $controller->getById($id);
            break;
        case 'PUT':
            $controller->update($id);
            break;
        case 'PATCH':
            $controller->patch($id); 
            break;
        case 'DELETE':
            $controller->delete($id);
            break;
        default:
            http_response_code(405); 
            echo json_encode(['error' => 'Método no permitido']);
    }
} else {
    $controller = new UserController($pdo);

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $controller->getAll();
            break;
        case 'POST':
            $controller->create();
            break;
        default:
            http_response_code(405); 
            echo json_encode(['error' => 'Método no permitido']);
    }
}
