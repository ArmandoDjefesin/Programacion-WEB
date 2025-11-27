// js/altas.js
document.addEventListener('DOMContentLoaded', () => {
    const formAltas = document.getElementById('formAltas');
    const mensajeGeneral = document.getElementById('mensajeGeneral');
    const btnSubmit = document.getElementById('btn-submit');
    
    // --- Lógica de Vista Previa de Imagen ---
    const inputImagen = document.getElementById('imagen');
    const previewImagen = document.getElementById('preview-imagen');
    const infoArchivo = document.getElementById('info-archivo');
    const btnSeleccionarImagen = document.getElementById('btn-seleccionar-imagen');
    const btnEliminarImagen = document.getElementById('btn-eliminar-imagen');
    const btnReset = document.getElementById('btn-reset');
    
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
    btnReset.addEventListener('click', limpiarImagen);
    // --- Fin Lógica de Imagen ---


    // --- Envío del Formulario ---
    formAltas.addEventListener('submit', async (e) => {
        e.preventDefault();
        mensajeGeneral.classList.remove('mostrar', 'campo-valido', 'campo-invalido');
        mensajeGeneral.textContent = '';
        
        // Validación HTML simple
        if (!formAltas.checkValidity()) {
            mensajeGeneral.textContent = 'Por favor, completa todos los campos requeridos.';
            mensajeGeneral.classList.add('mostrar', 'campo-invalido');
            // Forzar que el navegador muestre sus propios pop-ups de validación
            formAltas.reportValidity(); 
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Registrando...';

        // Usamos FormData para enviar datos con archivos
        const formData = new FormData(formAltas);

        try {
            const response = await fetch('api/admin/crear_producto.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error desconocido al registrar.');
            }

            mensajeGeneral.textContent = result.message;
            mensajeGeneral.classList.add('mostrar', 'campo-valido'); // Muestra mensaje de éxito
            formAltas.reset(); // Limpia el formulario
            limpiarImagen(); // Limpia la vista previa de la imagen

        } catch (error) {
            mensajeGeneral.textContent = error.message;
            mensajeGeneral.classList.add('mostrar', 'campo-invalido'); // Muestra mensaje de error
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Registrar Producto';
        }
    });
});