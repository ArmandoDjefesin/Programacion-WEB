<?php
// api/auth/login.php
require_once __DIR__ . '/../DBManager.php';

session_start();
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

// 1. Validar Entradas
if (empty($input['nombre']) || empty($input['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nombre de usuario y contraseña son requeridos.']);
    exit;
}

try {
    // 2. Conectar a la BD (usando 'admin_app')
    $pdo = DBManager::getInstance()->getConn();

    // 3. Buscar al usuario por su nombre
    $sql = "SELECT nombre, contraseña, rol FROM usuarios WHERE nombre = :nombre";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nombre' => $input['nombre']]);
    $usuario = $stmt->fetch();

    // 4. Verificar la contraseña
    if ($usuario && password_verify($input['password'], $usuario['contraseña'])) {
        
        // ¡ÉXITO! Credenciales correctas.
        session_regenerate_id(true); // Previene fijación de sesión
        
        // 5. Almacenar datos en la Sesión
        $_SESSION['loggedin'] = true;
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol']; // 'E' o 'C'

        // 6. Enviar respuesta exitosa
        echo json_encode([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso.',
            'rol' => $usuario['rol']
        ]);

    } else {
        // FALLO. Credenciales incorrectas.
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'Nombre de usuario o contraseña incorrectos.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>