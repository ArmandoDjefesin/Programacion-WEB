<?php
// api/admin/modificar_producto.php
require_once __DIR__ . '/../auth/session_check.php';
require_once __DIR__ . '/../DBManager.php';

// 1. SEGURIDAD: Exigimos rol 'E' (Empleado)
require_session('E');

header('Content-Type: application/json');

// 2. VALIDACIÓN DE CAMPOS OBLIGATORIOS
$campos_requeridos = [
    'id_producto', 'descripcion', 'categoria', 'tipo', 'marca', 'proveedor', 
    'cantidad_stock', 'precio_unitario', 'fecha_ingreso', 'estado'
];

$datos_producto = [];
$errores = [];

foreach ($campos_requeridos as $campo) {
    $valor = filter_input(INPUT_POST, $campo, FILTER_SANITIZE_STRING);
    if (empty($valor)) {
        $errores[] = "El campo '$campo' es obligatorio.";
    }
    $datos_producto[$campo] = $valor;
}

// Añadimos campos opcionales
$datos_producto['modelo_version'] = filter_input(INPUT_POST, 'modelo_version', FILTER_SANITIZE_STRING);
$datos_producto['codigo_licencia'] = filter_input(INPUT_POST, 'codigo_licencia', FILTER_SANITIZE_STRING);

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errores)]);
    exit;
}

try {
    $pdo = DBManager::getInstance()->getConn();

    // 3. CONSTRUIR LA CONSULTA UPDATE
    $sql = "UPDATE productos SET 
                descripcion = :descripcion, 
                categoria = :categoria, 
                tipo = :tipo, 
                marca = :marca, 
                modelo_version = :modelo_version,
                proveedor = :proveedor, 
                cantidad_stock = :cantidad_stock, 
                precio_unitario = :precio_unitario, 
                codigo_licencia = :codigo_licencia,
                fecha_ingreso = :fecha_ingreso, 
                estado = :estado";

    // 4. LÓGICA DE IMAGEN: Solo actualizar si se sube una nueva
    $imagen_data = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['imagen']['tmp_name'];
        $imagen_data = file_get_contents($file_tmp_path);
        
        $sql .= ", imagen = :imagen"; // Añadir imagen al UPDATE
    }

    $sql .= " WHERE id_producto = :id_producto";
    
    $stmt = $pdo->prepare($sql);

    // 5. BIND DE PARÁMETROS
    $stmt->bindParam(':descripcion', $datos_producto['descripcion']);
    $stmt->bindParam(':categoria', $datos_producto['categoria']);
    $stmt->bindParam(':tipo', $datos_producto['tipo']);
    $stmt->bindParam(':marca', $datos_producto['marca']);
    $stmt->bindParam(':modelo_version', $datos_producto['modelo_version']);
    $stmt->bindParam(':proveedor', $datos_producto['proveedor']);
    $stmt->bindParam(':cantidad_stock', $datos_producto['cantidad_stock'], PDO::PARAM_INT);
    $stmt->bindParam(':precio_unitario', $datos_producto['precio_unitario']);
    $stmt->bindParam(':codigo_licencia', $datos_producto['codigo_licencia']);
    $stmt->bindParam(':fecha_ingreso', $datos_producto['fecha_ingreso']);
    $stmt->bindParam(':estado', $datos_producto['estado']);
    $stmt->bindParam(':id_producto', $datos_producto['id_producto'], PDO::PARAM_INT);

    // Bind de la imagen solo si se subió una nueva
    if ($imagen_data !== null) {
        $stmt->bindParam(':imagen', $imagen_data, PDO::PARAM_LOB);
    }

    // 6. EJECUTAR
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Producto modificado exitosamente.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>