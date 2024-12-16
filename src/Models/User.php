<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Get all users (without passwords)
    public function getAll() {
        $stmt = $this->pdo->query("SELECT Id, Name, Email, Phone, Address, CC FROM user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a user by ID (without password)
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT Id, Name, Email, Phone, Address, CC FROM user WHERE Id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new user
    public function create($data) {
        // Validate required fields
        if (!isset($data['name'], $data['email'], $data['phone'], $data['address'], $data['password'], $data['cc'])) {
            throw new Exception('Todos los campos (name, email, phone, address, password, cc) son requeridos');
        }

        // Validate duplicates (email, telephone or ID)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user WHERE Email = :email OR Phone = :phone OR CC = :cc");
        $stmt->execute(['email' => $data['email'], 'phone' => $data['phone'], 'cc' => $data['cc']]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('La cédula, el correo o el teléfono ya están registrados.');
        }

        // Insert the new user
        $stmt = $this->pdo->prepare("INSERT INTO user (Name, Email, Phone, Address, Password, CC) VALUES (:name, :email, :phone, :address, :password, :cc)");
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'cc' => $data['cc']
        ]);

        // Return the new user's data (without password)
        $newId = $this->pdo->lastInsertId();
        return $this->getById($newId);
    }

    // Update an existing user
    public function update($id, $data) {
        // Validate required fields
        if (!isset($data['name'], $data['email'], $data['phone'], $data['address'], $data['password'], $data['cc'])) {
            throw new Exception('Todos los campos (name, email, phone, address, password, cc) son requeridos');
        }

        // Validate duplicates (email, telephone or ID) excluding the current user
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user WHERE (Email = :email OR Phone = :phone OR CC = :cc) AND Id != :id");
        $stmt->execute([
            'email' => $data['email'],
            'phone' => $data['phone'],
            'cc' => $data['cc'],
            'id' => $id
        ]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception('La cédula, el correo o el teléfono ya están registrados.');
        }

        // Update user
        $stmt = $this->pdo->prepare("UPDATE user SET Name = :name, Email = :email, Phone = :phone, Address = :address, Password = :password, CC = :cc WHERE Id = :id");
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'cc' => $data['cc']
        ]);

        // Return updated user data (without password)
        return $this->getById($id);
    }

    // Actualizar parcialmente un usuario
    public function patch($id, $updates) {
        // Partially update a user
        $usuarioExistente = $this->getById($id);
        if (!$usuarioExistente) {
            throw new Exception('El usuario no existe.');
        }

        // Validate fields allowed for partial update
        $allowedFields = ['name', 'email', 'phone', 'address', 'password', 'cc'];
        $fields = [];
        $params = ['id' => $id];

        foreach ($updates as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = :$key";
                $params[$key] = $key === 'password' ? password_hash($value, PASSWORD_DEFAULT) : $value;
            }
        }

        if (empty($fields)) {
            throw new Exception('No se proporcionaron campos válidos para actualizar.');
        }

        // Build and run the partial update query
        $query = "UPDATE user SET " . implode(', ', $fields) . " WHERE Id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        // Return updated user data (without password)

        return $this->getById($id);
    }

    // Delete User
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM user WHERE Id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}

