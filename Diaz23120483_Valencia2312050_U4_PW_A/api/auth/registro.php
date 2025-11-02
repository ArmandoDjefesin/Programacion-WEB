<?php
// api/auth/registro.php
require_once __DIR__ . '/../DBManager.php';

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

// 1. Validar Entradas
if (empty($input['nombre']) || empty($input['password']) || empty($input['rol'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son requeridos.']);
    exit;
}
if (strlen($input['password']) < 8) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres.']);
    exit;
}

try {
    // 2. Conectar a la BD
    $pdo = DBManager::getInstance()->getConn();

    // 3. Hashear la contraseña (¡NUNCA guardes texto plano!)
    // password_hash() usa el algoritmo Bcrypt por defecto.
    $hash_contraseña = password_hash($input['password'], PASSWORD_DEFAULT);

    // 4. Insertar el nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, contraseña, rol) VALUES (:nombre, :password, :rol)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':nombre' => $input['nombre'],
        ':password' => $hash_contraseña,
        ':rol' => $input['rol']
    ]);

    // 5. Enviar respuesta
    echo json_encode([
        'status' => 'success',
        'message' => 'Usuario registrado exitosamente. Ahora puedes iniciar sesión.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    // Error de duplicado (si 'nombre' es UNIQUE en tu BD)
    if ($e->errorInfo[1] == 1062) { 
        echo json_encode(['status' => 'error', 'message' => 'Error: El nombre de usuario ya existe.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>