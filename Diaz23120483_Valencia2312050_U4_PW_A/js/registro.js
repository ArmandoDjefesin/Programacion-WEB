// js/registro.js
document.addEventListener('DOMContentLoaded', () => {
    const formRegistro = document.getElementById('formRegistro');
    const msgError = document.getElementById('mensajeErrorGeneral');
    const btnSubmit = document.getElementById('btn-submit');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const errorConfirmar = document.getElementById('error-confirmar');

    formRegistro.addEventListener('submit', async (e) => {
        e.preventDefault();
        msgError.classList.remove('mostrar');
        errorConfirmar.classList.remove('mostrar');

        // Validación de contraseña
        if (password.value !== confirmPassword.value) {
            errorConfirmar.classList.add('mostrar');
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Registrando...';

        const data = {
            nombre: document.getElementById('nombre').value,
            password: password.value,
            rol: document.getElementById('rol').value
        };

        try {
            const response = await fetch('api/auth/registro.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Error desconocido');
            }

            // ¡Éxito! Redirigir al login
            alert(result.message); // "Usuario registrado exitosamente."
            window.location.href = 'login.html';

        } catch (error) {
            msgError.textContent = error.message;
            msgError.classList.add('mostrar');
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Registrar Usuario';
        }
    });
});