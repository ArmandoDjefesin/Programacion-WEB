// js/index.js
document.addEventListener('DOMContentLoaded', () => {

    const catalogo = document.querySelector('.catalogo-container');

    // Usamos delegación de eventos por si se cargan más productos dinámicamente
    catalogo.addEventListener('click', async (e) => {
        // Verificar si el clic fue en un botón de comprar
        if (e.target.classList.contains('btn-comprar')) {
            
            const boton = e.target;
            const idProducto = boton.dataset.id;
            
            if (!confirm(`¿Estás seguro de que quieres comprar este producto (ID: ${idProducto})?`)) {
                return;
            }

            boton.disabled = true;
            boton.textContent = 'Procesando...';

            try {
                const response = await fetch('api/comprador/comprar_producto.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_producto: idProducto })
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Error en la compra');
                }

                alert(result.message); // "¡Compra realizada con éxito!"
                boton.textContent = '¡Comprado!';
                // Opcional: recargar la página para actualizar el stock visualmente
                // window.location.reload(); 

            } catch (error) {
                alert(`Error: ${error.message}`);
                boton.disabled = false;
                boton.textContent = 'Comprar';
            }
        }
    });
});