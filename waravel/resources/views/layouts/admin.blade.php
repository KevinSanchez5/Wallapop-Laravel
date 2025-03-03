<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Mi Sitio'))</title>

    <!-- Íconos y Scripts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS y JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen m-0 p-0">

<div class="flex min-h-screen">

    <!-- Modo oscuro -->
    <button id="modoOscuroBtn" class="z-40 fixed top-4 right-4 p-2 rounded-lg bg-white border border-gray-700 hover:text-white hover:bg-black dark:bg-black dark:text-white dark:border-gray-500 dark:hover:text-black dark:hover:bg-white transform" aria-label="Cambiar modo de tema">
        <svg id="modoOscuroIconLuz" class="hidden w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
        <svg id="modoOscuroIconNoche" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
        </svg>
    </button>

    <!-- Barra Lateral -->
    <div id="sidebar" class="w-64 bg-[#BFF205] text-black p-6 flex flex-col min-h-screen h-full fixed top-0 left-0 z-20">
        <h2 class="text-center text-2xl font-extrabold mb-6">Administración</h2>

        <!-- Enlaces de Navegación -->
        <nav class="flex-1">
            <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-tachometer-alt"></i> &nbsp; Dashboard
            </a>
            <a href="{{ route('admin.clients') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-users"></i> &nbsp; Gestionar Clientes
            </a>
            <a href="{{ route('admin.products') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-box"></i> &nbsp; Gestionar Productos
            </a>
            <a href="{{ route('admin.reviews') }}" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-star"></i> &nbsp;Ver Valoraciones
            </a>
            <a href="" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-shopping-cart"></i> &nbsp; Gestionar Ventas
            </a>
            <a href="#" id="openModal" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white">
                <i class="fas fa-user-plus"></i> &nbsp; Añadir Admin
            </a>
            <div class="relative">
                <a href="#" class="block py-2 px-4 rounded-lg hover:bg-black hover:text-white" id="backupMenuButton">
                    <i class="fas fa-database"></i> &nbsp; Copia de Seguridad
                </a>
                <div class="absolute right-0 hidden mt-2 space-y-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg w-48" id="backupMenu">
                    <a href="{{ route('admin.backup') }}" id="exportBackupButton" class="block py-2 px-4 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        <i class="fas fa-arrow-down"></i> &nbsp; Exportar
                    </a>
                    <a href="" id="importBackupButton" class="block py-2 px-4 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">
                        <i class="fas fa-arrow-up"></i> &nbsp; Importar
                    </a>
                </div>
            </div>
        </nav>

        <!-- Botón Cerrar Sesión -->
        <form action="{{ route('logout') }}" method="POST" class="mt-auto">
            @csrf
            <button type="submit" class="w-full bg-black text-white py-2 rounded-lg hover:bg-white hover:text-black">
                <b>Cerrar Sesión</b>
            </button>
        </form>

        <!-- Modal Backup-->
        <div id="backupModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-lg w-1/3 relative">
                <button id="closeBackupModal" class="absolute top-2 right-2 text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 text-2xl" onclick="document.getElementById('backupModal').classList.add('hidden')">
                    &times;
                </button>
                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Restaurar Backup</h2>
                <ul id="backupList" class="space-y-2 text-gray-900 dark:text-gray-100">
                </ul>
                <button id="closeBackupModal" class="mt-4 px-4 py-2 bg-gray-500 dark:bg-gray-700 text-white dark:text-gray-300 hover:bg-gray-600 dark:hover:bg-gray-600 rounded transition">
                    Cerrar
                </button>
            </div>
        </div>

        <!-- Modal Añadir Administrador -->
        <div id="adminModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="bg-white dark:bg-gray-900 p-5 rounded-lg shadow-lg w-1/3 relative">
                <!-- Botón de cerrar como X en la esquina superior derecha -->
                <button id="closeAdminModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl" onclick="document.getElementById('adminModal').classList.add('hidden')">
                    &times;
                </button>

                <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Añadir Administrador</h2>

                <form id="adminForm">
                    @csrf
                    <label class="block mb-2 text-gray-700 dark:text-gray-300">Nombre:</label>
                    <input type="text" id="name" name="name" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white mb-3" required>

                    <label class="block mb-2 text-gray-700 dark:text-gray-300">Email:</label>
                    <input type="email" id="email" name="email" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white mb-3" required>

                    <label class="block mb-2 text-gray-700 dark:text-gray-300">Contraseña:</label>
                    <input type="password" id="password" name="password" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white mb-3" required>

                    <label class="block mb-2 text-gray-700 dark:text-gray-300">Confirmar Contraseña:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white mb-3" required>

                    <button type="submit" class="w-full bg-[#BFF205] text-black py-2 rounded-lg hover:bg-[#96BF03] transition">
                        <b>Guardar Administrador</b>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <div class="w-64 h-full"></div>

    <!-- Contenido Principal -->
    <main class="p-6 bg-gray-100 dark:bg-gray-900 dark:text-white flex-1 min-h-screen">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script>
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

        document.getElementById('backupMenuButton').addEventListener('click', function (e) {
            e.preventDefault();
            const menu = document.getElementById('backupMenu');
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', function (e) {
            const menu = document.getElementById('backupMenu');
            if (!menu.contains(e.target) && e.target !== document.getElementById('backupMenuButton')) {
                menu.classList.add('hidden');
            }
        });

        document.getElementById('importBackupButton').addEventListener('click', function (event) {
            event.preventDefault(); // Evita cualquier comportamiento extraño
            fetch('{{ route("admin.backups.list") }}')
                .then(response => response.json())
                .then(data => {
                    let list = document.getElementById('backupList');
                    list.innerHTML = ""; // Limpia la lista antes de agregar elementos nuevos

                    if (!data || data.length === 0) {
                        list.innerHTML = "<li class='p-2 text-gray-500'>No hay backups disponibles.</li>";
                    } else {
                        data.forEach(backup => {
                            let li = document.createElement('li');
                            li.classList.add("p-2", "bg-gray-200", "rounded", "cursor-pointer", "hover:bg-gray-300", "dark:bg-gray-700", "dark:hover:bg-gray-600", "dark:text-white");
                            li.textContent = backup;
                            li.addEventListener('click', function () {
                                restoreBackup(backup);
                            });
                            list.appendChild(li);
                        });
                    }

                    document.getElementById('backupModal').classList.remove('hidden'); // Muestra la modal
                });
        });

        document.getElementById('backupModal').addEventListener('click', function (event) {
            let modalContent = document.querySelector('#backupModal > div');
            if (!modalContent.contains(event.target)) {
                document.getElementById('backupModal').classList.add('hidden');
            }
        });

        document.getElementById('closeBackupModal').addEventListener('click', function () {
            document.getElementById('backupModal').classList.add('hidden');
        });

        document.getElementById('closeAdminModal').addEventListener('click', function () {
            document.getElementById('adminModal').classList.add('hidden');
        });

        window.addEventListener('click', function (event) {
            let modal = document.getElementById('backupModal');
            if (!modal.classList.contains('hidden') && !modal.querySelector('.bg-white').contains(event.target)) {
                modal.classList.add('hidden');
            }
        });

        document.getElementById('openModal').addEventListener('click', function () {
            document.getElementById('adminModal').classList.remove('hidden');
        });

        document.getElementById('adminForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("{{ route('admin.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                    }
                    if (data.admin) {
                        document.getElementById('adminModal').classList.add('hidden');
                        location.reload(); // Recargar la página para actualizar la lista de admins
                    }
                })
                .catch(error => {
                    console.error("Error al agregar administrador:", error);
                    alert("Error al procesar la solicitud.");
                });
        });

        function restoreBackup(filename) {
            if (confirm("¿Estás seguro de que deseas restaurar este backup? Se perderán los datos actuales.")) {
                fetch(`{{ url('/admin/backup/restore') }}/${filename}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message || "Error al restaurar el backup");
                        document.getElementById('backupModal').classList.add('hidden');
                    })
                    .catch(error => {
                        console.error("Error al restaurar backup:", error);
                        alert("Ocurrió un error al restaurar el backup.");
                    });
            }
        }
    </script>

</div>
</body>
</html>

