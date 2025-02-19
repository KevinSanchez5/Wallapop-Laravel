import './bootstrap';
import './carousel'

document.addEventListener('DOMContentLoaded', function () {
    const html = document.documentElement;
    const botonModoOscuro = document.getElementById('modoOscuroBtn');
    const iconoLuz = document.getElementById('modoOscuroIconLuz');
    const iconoNoche = document.getElementById('modoOscuroIconNoche');

    if (localStorage.getItem('modoOscuro') === 'true') {
        html.classList.add('dark');
        iconoLuz.classList.remove('hidden');
        iconoNoche.classList.add('hidden');
    } else {
        html.classList.remove('dark');
        iconoLuz.classList.add('hidden');
        iconoNoche.classList.remove('hidden');
    }

    botonModoOscuro.addEventListener('click', function () {
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.setItem('modoOscuro', 'false');
            iconoLuz.classList.add('hidden');
            iconoNoche.classList.remove('hidden');
        } else {
            html.classList.add('dark');
            localStorage.setItem('modoOscuro', 'true');
            iconoLuz.classList.remove('hidden');
            iconoNoche.classList.add('hidden');
        }
    });

    function enviarCorreoCodigo(){
        let email = document.getElementById('email').value;
        let messageSpan = document.getElementById('sendCodeMessage');

        if (!email.trim()) {
            messageSpan.textContent = "Por favor, ingresa un correo válido.";
            messageSpan.style.color = "red";
            return;
        }

        fetch("/users/correo-codigo", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ email: email })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageSpan.textContent = data.message;
                    messageSpan.style.color = "green";
                } else {
                    messageSpan.textContent = "No se encontró un usuario con este correo.";
                    messageSpan.style.color = "red";
                }
            })
            .catch(error => {
                messageSpan.textContent = "Hubo un error al enviar el código.";
                messageSpan.style.color = "red";
            });
    }

});

document.getElementById('category-menu-button').addEventListener('click', function() {
    const menu = document.getElementById('category-menu');
    menu.classList.toggle('hidden');
});
