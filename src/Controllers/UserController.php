<?php

require_once '../config/database.php';
require_once '../src/Models/User.php';
require_once '../src/dto/CreateUserDTO.php';
require_once '../src/dto/PatchUserDTO.php';
require_once '../src/dto/UpdateUserDTO.php';

use App\DTO\CreateUserDTO;
use App\DTO\PatchUserDTO;
use App\DTO\UpdateUserDTO;

// User Controller
class UserController {
    private $user;

    public function __construct($pdo) {
        $this->user = new User($pdo);
    }

    // Function to validate the token
    private function checkBearerToken() {
        $headers = apache_request_headers();
        
        //Token not provided
        if (!isset($headers['Authorization'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Token de autorización no proporcionado']);
            exit();
        }

        // Token extraction
        $authHeader = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $authHeader);
         
        // empty token
        if (empty($token)) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Token de autorización inválido']);
            exit();
        }

        // Hash  "IDTalento"
        $expectedToken = hash('sha256', 'IDTalento'); // Generate the SHA-256 hash of "TalentID"
        
        // Validate that the provided token matches the expected hash
        if ($token !== $expectedToken) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Token inválido']);
            exit();
        }
    }

    // Get from all users
    public function getAll() {
        $this->checkBearerToken();  // Check token on every request

        try {

            $usuarios = $this->user->getAll();

            //Validate users existence
            if (count($usuarios) > 0) {
                http_response_code(200);
                echo json_encode([
                    'message' => 'Usuarios obtenidos correctamente',
                    'data' => $usuarios
                ]);
            } else {
                http_response_code(204);
                echo json_encode(['message' => 'No hay usuarios registrados']);
            }
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    // Get users by id
    public function getById($id) {
        $this->checkBearerToken();  // Check token on every request

        try {
            $usuario = $this->user->getById($id);

            //Validate users existence
            if ($usuario) {
                http_response_code(200);
                echo json_encode([
                    'message' => 'Usuario encontrado',
                    'data' => $usuario
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Usuario no encontrado']);
            }
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    // Create User
    public function create() {
        $this->checkBearerToken();  // Check token on every request

        header('Content-Type: application/json');
        try {
            $data = $this->getRequestData();
            $userDTO = new CreateUserDTO($data); // Create DTO and validate data

            $usuarioCreado = $this->user->create($userDTO->toArray()); // Save to database

            http_response_code(201); // Created
            echo json_encode([
                'message' => 'Usuario creado exitosamente',
                'data' => $usuarioCreado
            ]);
        } catch (InvalidArgumentException $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    // Update user
    public function update($id) {
        $this->checkBearerToken();  // Check token on every request
    
        header('Content-Type: application/json');
        try {
            // Check if the user exists
            $usuarioExistente = $this->user->getById($id);
            if (!$usuarioExistente) {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'El usuario no existe']);
                return;
            }
    
            $data = $this->getRequestData();
            $userDTO = new UpdateUserDTO($data); // Create DTO and validate data
    
            $usuarioActualizado = $this->user->update($id, $userDTO->toArray());
            if ($usuarioActualizado) {
                http_response_code(200); // OK
                echo json_encode([
                    'message' => 'Usuario actualizado exitosamente',
                    'data' => $usuarioActualizado
                ]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'No se encontró el usuario para actualizar']);
            }
        } catch (InvalidArgumentException $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    // Partial update
    public function patch($id) {
        $this->checkBearerToken(); // Check token on every request
        
        header('Content-Type: application/json');
        try {
            // Check if the user exists
            $usuarioExistente = $this->user->getById($id);
            if (!$usuarioExistente) {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'El usuario no existe']);
                return;
            }
    
            $data = $this->getRequestData();
    
            // Filter received data to avoid invalid fields
            $allowedFields = ['name', 'email', 'phone', 'address', 'password', 'cc'];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));
    
            if (empty($filteredData)) {
                throw new InvalidArgumentException('No se proporcionaron campos válidos para actualizar');
            }
    
            // Convert existing user to an array
            $usuarioExistenteArray = (array) $usuarioExistente;
    
            // Mix received data with existing data
            $mergedData = array_merge($usuarioExistenteArray, $filteredData);
    
            // Validate the updated data with the DTO
            $userDTO = new PatchUserDTO($mergedData);
    
            // Perform the update
            $usuarioActualizado = $this->user->patch($id, $userDTO->toArray());
    
            http_response_code(200); // OK
            echo json_encode([
                'message' => 'Usuario actualizado parcialmente con éxito',
                'data' => $usuarioActualizado
            ]);
        } catch (InvalidArgumentException $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
    
    // Delete User
    public function delete($id) {
        $this->checkBearerToken();  // Check token on every request

        header('Content-Type: application/json');
        try {
            $rowCount = $this->user->delete($id);
            if ($rowCount > 0) {
                http_response_code(200); // OK
                echo json_encode(['message' => 'Usuario eliminado exitosamente']);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['error' => 'No se encontró el usuario para eliminar']);
            }
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    private function getRequestData(): array {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            throw new InvalidArgumentException('El cuerpo de la solicitud debe estar en formato JSON');
        }
        return $data;
    }

    private function handleError($e) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'error' => 'Ocurrió un error interno',
            'details' => $e->getMessage()
        ]);
    }
}
