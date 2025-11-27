// js/index.js
document.addEventListener('DOMContentLoaded', () => {

    const catalogo = document.querySelector('.catalogo-container');

    catalogo.addEventListener('click', async (e) => {
        const boton = e.target.closest('.btn-comprar');
        if (!boton || boton.disabled) {
            return;
        }
            
        const idProducto = boton.dataset.id;
        
        // --- (INICIO DE LA CORRECCIÓN DE DEPURACIÓN) ---
        console.log('Botón clickeado. ID de producto (data-id):', idProducto);

        const cardInfo = boton.closest('.producto-info');
        if (!cardInfo) {
            console.error('Error: No se pudo encontrar el .producto-info padre.');
            alert('Error: No se pudo encontrar la tarjeta del producto.');
            return;
        }
        
        const cantidadInput = cardInfo.querySelector('.producto-cantidad');
        if (!cantidadInput) {
            console.error('Error: No se pudo encontrar el .producto-cantidad dentro de la tarjeta.');
            alert('Error: No se pudo encontrar el campo de cantidad.');
            return;
        }
        
        const cantidad = parseInt(cantidadInput.value, 10);
        const maxStock = parseInt(cantidadInput.max, 10);
        
        console.log('Cantidad leída del input:', cantidad);
        console.log('Stock máximo leído del input:', maxStock);
        // --- (FIN DE LA CORRECCIÓN DE DEPURACIÓN) ---
        
        if (isNaN(cantidad) || cantidad <= 0) {
            alert('Por favor, ingrese una cantidad válida (mayor a 0).');
            cantidadInput.value = 1;
            return;
        }
        if (cantidad > maxStock) {
            alert(`Error: No hay suficiente stock. Máximo disponible: ${maxStock}`);
            cantidadInput.value = maxStock;
            return;
        }
        
        if (!confirm(`¿Estás seguro de que quieres comprar ${cantidad} unidad(es) de este producto?`)) {
            return;
        }

        boton.disabled = true;
        boton.textContent = 'Procesando...';

        try {
            // Preparamos los datos
            const data = { 
                id_producto: idProducto,
                cantidad: cantidad 
            };

            // Imprimimos los datos justos antes de enviar
            console.log('Enviando al backend (JSON):', JSON.stringify(data));

            const response = await fetch('api/comprador/comprar_producto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error en la compra');
            }

            alert(result.message);
            window.location.reload(); 

        } catch (error) {
            console.error('Catch Error:', error); // Mostrar el error en la consola
            alert(`Error: ${error.message}`);
            boton.disabled = false;
            boton.textContent = 'Comprar';
        }
    });
});