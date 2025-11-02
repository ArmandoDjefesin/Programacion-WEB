<?php
// api/comprador/comprar_producto.php
require_once __DIR__ . '/../auth/session_check.php';
require_once __DIR__ . '/../DBManager.php';

// 1. SEGURIDAD: Exigimos que el rol sea 'C' (Comprador)
require_session('C');

$input = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');

// 2. VALIDAR ENTRADA
if (empty($input['id_producto'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID de producto no proporcionado.']);
    exit;
}

$id_producto = (int)$input['id_producto'];
$id_comprador = $_SESSION['nombre']; // Usamos el nombre del comprador como cliente

$pdo = DBManager::getInstance()->getConn();

try {
    // 3. INICIAR TRANSACCIÓN
    $pdo->beginTransaction();

    // 4. Obtener datos del producto y bloquear la fila para evitar ventas duplicadas
    $sql_prod = "SELECT descripcion, precio_unitario, cantidad_stock 
                 FROM productos 
                 WHERE id_producto = :id_producto FOR UPDATE"; // FOR UPDATE es crucial
                 
    $stmt_prod = $pdo->prepare($sql_prod);
    $stmt_prod->execute([':id_producto' => $id_producto]);
    $producto = $stmt_prod->fetch();

    // 5. Verificar Stock
    if (!$producto || $producto['cantidad_stock'] <= 0) {
        throw new Exception('Producto no disponible o sin stock.');
    }

    // 6. Reducir el stock
    $sql_update = "UPDATE productos SET cantidad_stock = cantidad_stock - 1 
                   WHERE id_producto = :id_producto";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([':id_producto' => $id_producto]);

    // 7. Generar un ID de Venta único (ej: V011)
    // (Esta es una forma simple de hacerlo; en producción se usaría un UUID)
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM ventas");
    $num_venta = $stmt_count->fetchColumn() + 1;
    $id_venta = 'V' . str_pad($num_venta, 3, '0', STR_PAD_LEFT);

    // 8. Registrar la venta
    $sql_venta = "INSERT INTO ventas 
                    (id_venta, id_producto, nombre_producto, cantidad_vendida, precio_unitario, total, cliente, fecha_venta, estado)
                  VALUES 
                    (:id_venta, :id_producto, :nombre_producto, 1, :precio, :total, :cliente, CURDATE(), 'Pagada')";
    
    $stmt_venta = $pdo->prepare($sql_venta);
    $stmt_venta->execute([
        ':id_venta' => $id_venta,
        ':id_producto' => $id_producto,
        ':nombre_producto' => $producto['descripcion'],
        ':precio' => $producto['precio_unitario'],
        ':total' => $producto['precio_unitario'], // Asumimos cantidad = 1
        ':cliente' => $id_comprador,
    ]);

    // 9. Confirmar todo
    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => '¡Compra realizada con éxito! ID de Venta: ' . $id_venta
    ]);

} catch (Exception $e) {
    // 10. Si algo falla, deshacer todo
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>