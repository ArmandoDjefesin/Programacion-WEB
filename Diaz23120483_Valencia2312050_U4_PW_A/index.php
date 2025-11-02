<?php
    // 1. INICIAR LA SESIÓN
    session_start();
    
    // 2. CONECTAR A LA BD PARA OBTENER PRODUCTOS
    require_once 'api/DBManager.php';
    $productos = [];
    try {
        $pdo = DBManager::getInstance()->getConn();
        
        // ¡SQL ACTUALIZADO! Ahora traemos todos los detalles
        $sql = "SELECT 
                    id_producto, descripcion, precio_unitario, imagen, 
                    tipo, marca, modelo_version, proveedor, estado 
                FROM productos 
                WHERE cantidad_stock > 0 AND (estado = 'Disponible' OR estado = 'Bajo Stock')";
                
        $stmt = $pdo->query($sql);
        $productos = $stmt->fetchAll();

    } catch (Exception $e) {
        // Manejar error si la BD falla
        $error_catalogo = "Error al cargar el catálogo: " . $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilo_base.css" type="text/css">
    <title>Omnipotent Tech - Punto de Venta</title>
    <style>
        /* CSS para el Carrusel/Catálogo */
        .catalogo-container {
            display: flex;
            overflow-x: auto;
            padding: 20px 10px;
            gap: 20px;
            background-color: #f0f4f8;
            border-radius: 8px;
        }
        .producto-card {
            flex: 0 0 280px; /* Un poco más ancho para la nueva info */
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .producto-imagen {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: #eee;
        }
        .producto-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .producto-info h4 {
            font-size: 1.1rem;
            color: #14213d;
            margin-bottom: 10px;
            /* Limitar a 2 líneas */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.4em; /* Altura aprox de 2 líneas */
        }
        .producto-info .precio {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }
        
        /* --- (NUEVO) Estilos para la lista de detalles --- */
        .producto-detalles {
            font-size: 0.85rem;
            color: #555;
            list-style: none;
            padding: 0;
            margin: 0 0 15px 0;
            border-top: 1px solid #f0f0f0;
            padding-top: 10px;
        }
        .producto-detalles li {
            margin-bottom: 4px;
        }
        .producto-detalles li strong {
            color: #333;
        }

        /* --- Estilos para Botones de Compra --- */
        .btn-comprar {
            padding: 10px;
            background: #4cc9f0;
            color: #fff;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            text-align: center;
        }
        .btn-comprar:hover {
            background: #3a86ff;
        }
        
        /* --- (NUEVO) Estilo para botón deshabilitado --- */
        .btn-comprar.btn-disabled {
            background-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .btn-comprar.btn-disabled:hover {
            background-color: #6c757d; /* No cambia de color */
        }

    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1>Omnipotent Tech</h1>
            <nav>
                <ul>
                    <?php
                        // LÓGICA DINÁMICA DE SESIÓN (Login/Logout)
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                            $nombreUsuario = htmlspecialchars($_SESSION['nombre']);
                            echo "<li style='color: #fff; margin-left: 20px;'>Hola, $nombreUsuario</li>";
                            echo '<li><a href="api/auth/logout.php" style="color:#ffc107; font-weight:bold;">Cerrar Sesión</a></li>';
                        } else {
                            echo '<li><a href="login.html">Iniciar Sesión</a></li>';
                        }
                    ?>
                    <?php
                        // LÓGICA DINÁMICA DE NAVEGACIÓN
                        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'E') {
                            echo '<li><a href="registro.html" style="color:#f1c40f;">Registrar Usuario</a></li>';
                            echo '<li><a href="altas.html">Altas</a></li>';
                            echo '<li><a href="modificaciones.html">Modificaciones</a></li>';
                            echo '<li><a href="eliminacion.html">Eliminación</a></li>';
                            
                        }
                    ?>
                    <li><a href="#contacto">Contacto</a></li>
                    

                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section id="catalogo">
            <div class="container">
                <h2>Catálogo de Productos</h2>
                <p>Explora nuestros productos y servicios disponibles.</p>
                
                <div class="catalogo-container">
                    <?php if (isset($error_catalogo)): ?>
                        <p style="color: red;"><?php echo $error_catalogo; ?></p>
                    <?php elseif (empty($productos)): ?>
                        <p>No hay productos disponibles en este momento.</p>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                            <div class="producto-card">
                                <?php if (!empty($producto['imagen'])): ?>
                                    <img class="producto-imagen" 
                                         src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>" 
                                         alt="<?php echo htmlspecialchars($producto['descripcion']); ?>">
                                <?php else: ?>
                                    <img class="producto-imagen" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22250%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20250%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_18fa41a131e%20text%20%7B%20fill%3A%23AAAAAA%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A13pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_18fa41a131e%22%3E%3Crect%20width%3D%22250%22%20height%3D%22200%22%20fill%3D%22%23EEEEEE%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2286.9296875%22%20y%3D%22105.8%22%3EImagen%20no%20disponible%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Imagen no disponible">
                                <?php endif; ?>
                                
                                <div class="producto-info">
                                    <div>
                                        <h4><?php echo htmlspecialchars($producto['descripcion']); ?></h4>
                                        <p class="precio">$<?php echo number_format($producto['precio_unitario'], 2); ?></p>
                                        
                                        <ul class="producto-detalles">
                                            <li><strong>Marca:</strong> <?php echo htmlspecialchars($producto['marca']); ?></li>
                                            <li><strong>Tipo:</strong> <?php echo htmlspecialchars($producto['tipo']); ?></li>
                                            
                                            <?php if (!empty($producto['modelo_version'])): ?>
                                                <li><strong>Modelo:</strong> <?php echo htmlspecialchars($producto['modelo_version']); ?></li>
                                            <?php endif; ?>
                                            
                                            <li><strong>Proveedor:</strong> <?php echo htmlspecialchars($producto['proveedor']); ?></li>
                                            
                                            <?php 
                                                // Lógica de color para el estado
                                                $color_estado = ($producto['estado'] == 'Disponible') ? 'green' : '#E67E22'; 
                                            ?>
                                            <li><strong>Estado:</strong> <span style="color: <?php echo $color_estado; ?>; font-weight: bold;"><?php echo htmlspecialchars($producto['estado']); ?></span></li>
                                        </ul>
                                    </div>
                                    
                                    <?php
                                        // 6. LÓGICA DE BOTÓN DE COMPRA (ACTUALIZADA)
                                        if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'C') {
                                            
                                            if ($producto['estado'] === 'Disponible') {
                                                // Producto disponible, botón activado
                                                echo '<button class="btn-comprar" data-id="' . $producto['id_producto'] . '">Comprar</button>';
                                            } else {
                                                // Producto en 'Bajo Stock' u otro, botón deshabilitado
                                                echo '<button class="btn-comprar btn-disabled" disabled>No disponible</button>';
                                            }
                                        }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
   </main>

    <footer>
        <p>Omnipotent Tech © 2025 - Todos los derechos reservados</p>
        <div class="container">
            <p>
                <a href="https://validator.w3.org/nu/?doc=https://armandodjefesin.github.io/Programacion-WEB/Diaz23120483_Valencia2312050_U2_PW_A/modificaciones.html" target="_blank">
                    <img src="https://www.w3.org/Icons/valid-html401" alt="Valid HTML">
                </a>
                <a href="https://jigsaw.w3.org/css-validator/validator?uri=https://armandodjefesin.github.io/Programacion-WEB/Diaz23120483_Valencia2312050_U2_PW_A/formulario.css" target="_blank">
                    <img src="https://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS">
                </a>
            </p>
        </div>
    </footer>
    
    <script src="js/index.js"></script>
</body>
</html>