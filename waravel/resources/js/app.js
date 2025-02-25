import './bootstrap';
import './carousel'
import Echo from 'laravel-echo';
import Pusher from 'pusher-js'
import 'flowbite';

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
});

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true
});

window.Echo.private('user.' + userId)
    .notification('.App\\Notifiactions\\Notificacion',(notification)=> {
        console.log('Notificacion recibida:', notification)
    })
if (typeof userId !== 'undefined' && userId) {
    window.Echo.private('user.' + userId)
        .notification((notification) => {
            console.log('Notificación recibida:', notification);

            // Crear la alerta con Bootstrap
            let alerta = `
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">${notification.mensaje}</span>
                    <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" aria-label="Close" onclick="this.parentElement.remove();">
                        <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Cerrar</title>
                            <path d="M14.348 5.652a1 1 0 011.415 1.415L11.414 11l4.349 4.348a1 1 0 01-1.415 1.415L10 12.414l-4.348 4.349a1 1 0 01-1.415-1.415L8.586 11 4.237 6.652a1 1 0 011.415-1.415L10 9.586l4.348-4.349z"/>
                        </svg>
                    </button>
                </div>
            `;

            // Añadir la alerta al contenedor de notificaciones
            document.getElementById('notificaciones').innerHTML += alerta;

            // Ocultar automáticamente la alerta después de 5 segundos
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => alert.classList.remove('show'));
            }, 5000);
        });
}
