// js/eliminacion.js
document.addEventListener('DOMContentLoaded', () => {
    
    // --- ELEMENTOS ---
    const form = document.getElementById('formEliminar');
    const motivo = document.getElementById('motivo');
    const otroMotivoContainer = document.getElementById('otro-motivo-container');
    const otroMotivo = document.getElementById('otro_motivo');
    const btnSubmit = document.getElementById('btn-submit');
    const mensajeGeneral = document.getElementById('mensajeGeneral');
    const inputId = document.getElementById('id_producto');
    
    // --- MOSTRAR/OCULTAR CAMPO 'OTRO' ---
    motivo.addEventListener('change', () => {
        if (motivo.value === 'otro') {
            otroMotivoContainer.style.display = 'block';
            otroMotivo.required = true;
        } else {
            otroMotivoContainer.style.display = 'none';
            otroMotivo.required = false;
        }
    });

    // --- ENVÍO DEL FORMULARIO ---
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        mensajeGeneral.classList.remove('mostrar', 'campo-invalido', 'campo-valido');
        
        // Validar que el formulario esté completo (HTML required lo hace, pero doble check)
        if (!form.checkValidity()) {
            mensajeGeneral.textContent = 'Por favor, complete todos los campos requeridos.';
            mensajeGeneral.classList.add('mostrar', 'campo-invalido');
            form.reportValidity();
            return;
        }

        // Doble confirmación visual
        if (!confirm(`¿Está 100% seguro de que desea eliminar el producto ID: ${inputId.value}? Esta acción no se puede deshacer.`)) {
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Eliminando...';

        // Preparar datos para enviar como JSON
        const data = {
            id_producto: inputId.value,
            motivo: motivo.value,
            otro_motivo: otroMotivo.value
        };

        try {
            const response = await fetch('api/admin/eliminar_producto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message);
            }

            // Éxito
            mensajeGeneral.textContent = result.message;
            mensajeGeneral.classList.add('mostrar', 'campo-valido');
            form.reset();
            otroMotivoContainer.style.display = 'none';

        } catch (error) {
            mensajeGeneral.textContent = error.message;
            mensajeGeneral.classList.add('mostrar', 'campo-invalido');
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Eliminar Registro';
        }
    });

    // --- BOTÓN CANCELAR ---
    document.getElementById('btn-cancelar').addEventListener('click', (e) => {
        e.preventDefault();
        if (confirm('¿Desea cancelar la eliminación?')) {
            form.reset();
            mensajeGeneral.classList.remove('mostrar');
            otroMotivoContainer.style.display = 'none';
        }
    });
});