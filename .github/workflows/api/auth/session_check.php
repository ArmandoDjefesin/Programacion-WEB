<?php

/**
 * Nuestro "Guardián" de seguridad para PHP/MySQL.
 * Inicia la sesión y valida el rol permitido.
 *
 * @param string $allowed_role El rol requerido (ej: 'E' para Empleado)
 * @return void (Termina la ejecución si falla)
 */
function require_session(string $allowed_role): void {
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Revisar Autenticación 
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Content-Type: application/json');
        http_response_code(401); 
        echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado. Se requiere iniciar sesión.']);
        exit;
    }

    // 2. Revisar Autorización 
    if ($_SESSION['rol'] !== $allowed_role) {
        header('Content-Type: application/json');
        http_response_code(403); 
        echo json_encode(['status' => 'error', 'message' => 'Acceso denegado. No tienes permisos para esta acción.']);
        exit;
    }

    // 3. Si todo está bien, simplemente continúa la ejecución del script que lo llamó.
}

header('Content-Type: application/json');