// js/login.js
document.addEventListener('DOMContentLoaded', () => {
    const formLogin = document.getElementById('formLogin');
    const msgError = document.getElementById('mensajeErrorGeneral');
    const btnSubmit = document.getElementById('btn-submit');

    formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        msgError.classList.remove('mostrar');
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Verificando...';

        const data = {
            nombre: document.getElementById('nombre').value,
            password: document.getElementById('password').value
        };

        try {
            const response = await fetch('api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error desconocido');
            }

            // ¡Éxito! Redirigir a la página principal
            // El index.php ahora leerá la sesión y mostrará los botones correctos.
            window.location.href = 'index.php'; // Redirigimos a index.php

        } catch (error) {
            msgError.textContent = error.message;
            msgError.classList.add('mostrar');
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Entrar';
        }
    });
});