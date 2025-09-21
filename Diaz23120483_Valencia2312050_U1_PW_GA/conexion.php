<?php
// Headers para permitir CORS
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Desactivar visualización de errores para el usuario final
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Configuración de la conexión a la base de datos
$servername = "10.31.2.139";
$username = "root";
$password = "";
$database = "tiendaelectrosoft";

// Inicializar respuesta
$response = array();

try {
    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $database);

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Conexión fallida: " . $conn->connect_error);
    }
    
    $response = array(
        "status" => "success",
        "message" => "Conexión exitosa a la base de datos"
    );
    
    // También podemos intentar una consulta simple para mayor verificación
    //$query = "SELECT COUNT(*) as total FROM productos";
    $query = "SELECT descripcion  as total FROM productos where id_producto = '1' ";
    $result = $conn->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $response["data"] = "Me estoy conectando a la base de Armando total de productos en su base de datos: " . $row['total'];
    } else {
        $response["data"] = "Conexión exitosa pero error en consulta: " . $conn->error;
    }
    
    $conn->close();

} catch (Exception $e) {
    $response = array(
        "status" => "error",
        "message" => $e->getMessage()
    );
}

// Devolver respuesta en formato JSON
echo json_encode($response);
exit();
?>