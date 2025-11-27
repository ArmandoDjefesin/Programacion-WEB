<?php
session_start(); // Inicia la sesión para poder acceder a ella


$_SESSION = array();

// 2. Borrar la cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finalmente, destruir la sesión
session_destroy();

// 4. Redirigir al index
header('Location: ../../login.html');
exit;
?>