<?php
// api/admin/eliminar_producto.php
require_once __DIR__ . '/../auth/session_check.php';
require_once __DIR__ . '/../DBManager.php';
// --- (Se eliminó: require_once log_event.php) ---

// 1. SEGURIDAD: Exigimos rol 'E' (Empleado)
require_session('E');

header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

// --- (Se eliminó: $id_empleado = $_SESSION['id_empleado'];) ---

// 2. VALIDACIÓN DE DATOS
if (empty($input['id_producto']) || empty($input['motivo'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos (ID del producto o motivo).']);
    exit;
}

$id_producto = $input['id_producto'];
// (La lógica del motivo se mantiene por si acaso, aunque no se registra)
$motivo = $input['motivo'];
if ($motivo === 'otro' && !empty($input['otro_motivo'])) {
    $motivo = "Otro: " . $input['otro_motivo'];
}

try {
    $pdo = DBManager::getInstance()->getConn();
    $pdo->beginTransaction();

    // 3. Verificar si el producto tiene ventas asociadas
    $sql_check = "SELECT COUNT(*) FROM ventas WHERE id_producto = :id_producto";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([':id_producto' => $id_producto]);
    
    if ($stmt_check->fetchColumn() > 0) {
        throw new Exception('No se puede borrar: Este producto ya tiene ventas registradas. Márcalo como "Agotado" en su lugar.');
    }

    // 4. Borrar el producto
    $sql_delete = "DELETE FROM productos WHERE id_producto = :id_producto";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute([':id_producto' => $id_producto]);

    if ($stmt_delete->rowCount() === 0) {
        throw new Exception('No se encontró ningún producto con ese ID.');
    }

    // --- (Se eliminó: la llamada a logEvent()) ---
    
    // 5. Confirmar transacción
    $pdo->commit();

    echo json_encode(['status' => 'success', 'message' => 'Producto eliminado exitosamente.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>