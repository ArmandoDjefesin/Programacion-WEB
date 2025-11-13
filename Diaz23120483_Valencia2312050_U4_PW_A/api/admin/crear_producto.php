<?php
// api/admin/crear_producto.php
require_once __DIR__ . '/../auth/session_check.php';
require_once __DIR__ . '/../DBManager.php';

// 1. SEGURIDAD: Exigimos que el rol sea 'E'
require_session('E');

header('Content-Type: application/json');


$default_image_base64 = "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";
$default_image_data = base64_decode($default_image_base64);

// 2. VALIDACIÓN DE CAMPOS OBLIGATORIOS
$campos_requeridos = [
    'descripcion', 'categoria', 'tipo', 'marca', 'proveedor', 
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

// Añadimos los campos opcionales
$datos_producto['modelo_version'] = filter_input(INPUT_POST, 'modelo_version', FILTER_SANITIZE_STRING);
$datos_producto['codigo_licencia'] = null; // No está en el formulario de altas

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => implode(' ', $errores)]);
    exit;
}

try {
    $pdo = DBManager::getInstance()->getConn();

    // 3. PROCESAR IMAGEN
    $imagen_data = $default_image_data; // Usamos la imagen por defecto

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['imagen']['tmp_name'];
        $file_size = $_FILES['imagen']['size'];
        $file_type = $_FILES['imagen']['type'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($file_type, $allowed_types) && $file_size < 5 * 1024 * 1024) { // 5MB
            $imagen_data = file_get_contents($file_tmp_path);
        } else {
            error_log("Imagen subida inválida (tipo: $file_type, tamaño: $file_size). Usando imagen por defecto.");
        }
    }

    // 4. INSERTAR PRODUCTO
    $sql = "INSERT INTO productos 
            (descripcion, categoria, tipo, marca, proveedor, cantidad_stock, precio_unitario, fecha_ingreso, estado, modelo_version, imagen) 
            VALUES 
            (:descripcion, :categoria, :tipo, :marca, :proveedor, :cantidad_stock, :precio_unitario, :fecha_ingreso, :estado, :modelo_version, :imagen)";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind de los datos validados
    $stmt->bindParam(':descripcion', $datos_producto['descripcion']);
    $stmt->bindParam(':categoria', $datos_producto['categoria']);
    $stmt->bindParam(':tipo', $datos_producto['tipo']);
    $stmt->bindParam(':marca', $datos_producto['marca']);
    $stmt->bindParam(':proveedor', $datos_producto['proveedor']);
    $stmt->bindParam(':cantidad_stock', $datos_producto['cantidad_stock'], PDO::PARAM_INT);
    $stmt->bindParam(':precio_unitario', $datos_producto['precio_unitario']);
    $stmt->bindParam(':fecha_ingreso', $datos_producto['fecha_ingreso']);
    $stmt->bindParam(':estado', $datos_producto['estado']);
    $stmt->bindParam(':modelo_version', $datos_producto['modelo_version']);
    $stmt->bindParam(':imagen', $imagen_data, PDO::PARAM_LOB);

    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Producto agregado exitosamente.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>