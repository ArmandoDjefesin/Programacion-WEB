// js/modificaciones.js
document.addEventListener('DOMContentLoaded', () => {

    // --- ELEMENTOS DEL DOM ---
    const formBuscar = document.getElementById('formBuscar');
    const btnBuscar = document.getElementById('btn-buscar');
    const inputBuscarId = document.getElementById('buscar_id_producto');
    
    const formModificar = document.getElementById('formModificar');
    const btnModificar = document.getElementById('btn-modificar');
    const btnCancelar = document.getElementById('btn-cancelar');
    
    const seccionModificar = document.getElementById('seccion-modificar');
    const productoEncontrado = document.getElementById('producto-encontrado');
    const infoProducto = document.getElementById('info-producto');
    const mensajeModificar = document.getElementById('mensajeModificar');

    // --- Lógica de Imagen ---
    const inputImagen = document.getElementById('imagen');
    const previewImagen = document.getElementById('preview-imagen');
    const infoArchivo = document.getElementById('info-archivo');
    const btnSeleccionarImagen = document.getElementById('btn-seleccionar-imagen');
    const btnEliminarImagen = document.getElementById('btn-eliminar-imagen');

    btnSeleccionarImagen.addEventListener('click', () => inputImagen.click());

    inputImagen.addEventListener('change', () => {
        const archivo = inputImagen.files[0];
        if (!archivo) return;
        if (archivo.size > 5 * 1024 * 1024) { // 5MB
            alert('Tamaño máximo permitido: 5MB');
            limpiarImagen();
            return;
        }
        const lector = new FileReader();
        lector.onload = e => {
            previewImagen.src = e.target.result;
            previewImagen.style.display = 'block';
            infoArchivo.textContent = `Archivo: ${archivo.name}`;
            btnEliminarImagen.style.display = 'inline-block';
        };
        lector.readAsDataURL(archivo);
    });

    function limpiarImagen() {
        inputImagen.value = '';
        previewImagen.src = '';
        previewImagen.style.display = 'none';
        infoArchivo.textContent = '';
        btnEliminarImagen.style.display = 'none';
    }
    btnEliminarImagen.addEventListener('click', limpiarImagen);
    // --- Fin Lógica de Imagen ---

    // --- FUNCIÓN 1: BUSCAR PRODUCTO ---
    formBuscar.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = inputBuscarId.value;
        if (!id) {
            alert('Por favor, ingrese un ID.');
            return;
        }

        btnBuscar.disabled = true;
        btnBuscar.textContent = 'Buscando...';
        productoEncontrado.classList.remove('mostrar');
        seccionModificar.style.display = 'none';
        mensajeModificar.classList.remove('mostrar');

        try {
            const response = await fetch(`api/admin/buscar_producto.php?id=${id}`);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message);
            }

            // Éxito: Rellenar el formulario de modificación
            const producto = result.data;
            document.getElementById('modificar_id_producto').value = producto.id_producto;
            document.getElementById('descripcion').value = producto.descripcion;
            document.getElementById('categoria').value = producto.categoria;
            document.getElementById('tipo').value = producto.tipo;
            document.getElementById('marca').value = producto.marca;
            document.getElementById('modelo_version').value = producto.modelo_version;
            document.getElementById('proveedor').value = producto.proveedor;
            document.getElementById('cantidad_stock').value = producto.cantidad_stock;
            document.getElementById('precio_unitario').value = producto.precio_unitario;
            document.getElementById('codigo_licencia').value = producto.codigo_licencia;
            document.getElementById('fecha_ingreso').value = producto.fecha_ingreso;
            document.getElementById('estado').value = producto.estado;

            // Mostrar la imagen existente
            limpiarImagen(); // Limpiar por si acaso
            if (producto.imagen_base64) {
                previewImagen.src = `data:image/jpeg;base64,${producto.imagen_base64}`;
                previewImagen.style.display = 'block';
                infoArchivo.textContent = 'Imagen actual. (Seleccione una nueva para reemplazar)';
            } else {
                 infoArchivo.textContent = 'No hay imagen actual. (Opcional)';
            }

            infoProducto.textContent = `Producto Encontrado: ${producto.descripcion} (ID: ${producto.id_producto})`;
            productoEncontrado.classList.add('mostrar');
            seccionModificar.style.display = 'block';

        } catch (error) {
            alert(`Error: ${error.message}`);
        } finally {
            btnBuscar.disabled = false;
            btnBuscar.textContent = 'Buscar Producto';
        }
    });

    // --- FUNCIÓN 2: MODIFICAR PRODUCTO ---
    formModificar.addEventListener('submit', async (e) => {
        e.preventDefault();
        mensajeModificar.classList.remove('mostrar');

        // =======================================================
        // ¡VALIDACIÓN DEL LADO DEL CLIENTE AÑADIDA!
        // =======================================================
        if (!formModificar.checkValidity()) {
            mensajeModificar.textContent = 'Por favor, completa todos los campos requeridos.';
            mensajeModificar.classList.add('mostrar', 'campo-invalido');
            formModificar.reportValidity(); // Muestra los pop-ups de error del navegador
            return;
        }
        
        if (!confirm('¿Estás seguro de que deseas guardar los cambios?')) {
            return;
        }

        btnModificar.disabled = true;
        btnModificar.textContent = 'Guardando...';

        const formData = new FormData(formModificar);

        try {
            const response = await fetch('api/admin/modificar_producto.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message);
            }
            
            alert(result.message); // "Producto modificado exitosamente"
            // Resetear todo
            formBuscar.reset();
            formModificar.reset();
            limpiarImagen();
            seccionModificar.style.display = 'none';
            productoEncontrado.classList.remove('mostrar');

        } catch (error) {
            mensajeModificar.textContent = error.message;
            mensajeModificar.classList.add('mostrar', 'campo-invalido');
        } finally {
            btnModificar.disabled = false;
            btnModificar.textContent = 'Guardar Cambios';
        }
    });
    
    // --- FUNCIÓN 3: CANCELAR/RESET ---
    btnCancelar.addEventListener('click', () => {
        if (confirm('¿Desea cancelar la modificación? Se perderán los cambios.')) {
            formModificar.reset();
            limpiarImagen();
            seccionModificar.style.display = 'none';
            productoEncontrado.classList.remove('mostrar');
            formBuscar.reset();
        }
    });
});