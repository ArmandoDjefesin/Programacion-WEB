<?php
// api/admin/buscar_producto.php
require_once __DIR__ . '/../auth/session_check.php';
require_once __DIR__ . '/../DBManager.php';

// 1. SEGURIDAD: Exigimos rol 'E'
require_session('E');

header('Content-Type: application/json');

// 2. Obtenemos el ID desde la URL
$id_producto = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

if (empty($id_producto)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No se proporcionó un ID de producto.']);
    exit;
}

try {
    $pdo = DBManager::getInstance()->getConn();

    // 3. Buscar el producto por ID
    $sql = "SELECT * FROM productos WHERE id_producto = :id_producto";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_producto' => $id_producto]);
    $producto = $stmt->fetch();

    if ($producto) {
        // 4. Si hay imagen, codificarla en base64 para enviarla en JSON
        if (!empty($producto['imagen'])) {
            $producto['imagen_base64'] = base64_encode($producto['imagen']);
        } else {
            $producto['imagen_base64'] = null;
        }
        unset($producto['imagen']); // No enviar el binario crudo

        echo json_encode(['status' => 'success', 'data' => $producto]);
    } else {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Producto no encontrado.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>