@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6 text-center text-gray-800 dark:text-gray-200">
        Panel de Administración de {{ Auth::user()->name }}
    </h1>

    <!-- Contenedor Flex de 3 Columnas -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Columna 1: Usuarios -->
        <div class="w-full md:flex-1 flex flex-col gap-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 min-h-[100px] flex flex-col justify-between transition">
                <div>
                    <h5 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-200">Clientes Registrados</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                </div>
                <a href="{{ route('admin.clients') }}" class="block text-sm text-black bg-[#BFF205] hover:bg-[#96bf03] py-2 px-4 rounded-lg text-center mt-4 transform hover:scale-105 transition-all duration-300">
                    <b>Ver Todos</b>
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 flex flex-col h-[375px] transition">
                <div>
                    <h5 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                        <i class="fas fa-users text-[#BFF205] mr-2"></i> Últimos Clientes
                    </h5>
                    <div class="overflow-y-auto max-h-[290px] pr-2">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($latestClients as $index => $client)
                                <li class="p-3 rounded-lg flex justify-between items-center
                        {{ $index % 2 == 0 ? 'bg-gray-100 dark:bg-gray-700' : 'bg-gray-50 dark:bg-gray-800' }}">
                                    <div>
                                        <a href="{{ route('cliente.ver', $client->guid) }}" class="text-black dark:text-white hover:underline font-medium">
                                            {{ $client->name }}
                                        </a>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $client->email }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300 flex items-center">
                                        <i class="far fa-calendar-alt mr-1"></i>&nbsp;
                                        {{ \Carbon\Carbon::parse($client->created_at)->format('d M Y') }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna 2: Productos -->
        <div class="w-full md:flex-1 flex flex-col gap-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 min-h-[100px] flex flex-col justify-between transition">
                <div>
                    <h5 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-200">Productos Activos</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalProducts }}</p>
                </div>
                <a href="{{ route('admin.products') }}" class="block text-sm text-black bg-[#BFF205] hover:bg-[#96bf03] py-2 px-4 rounded-lg text-center mt-4 transform hover:scale-105 transition-all duration-300">
                    <b>Ver Todos</b>
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 flex flex-col h-[375px] transition">
                <div>
                    <h5 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                        <i class="fas fa-box text-[#BFF205] mr-2"></i> Últimos Productos
                    </h5>
                    <div class="overflow-y-auto max-h-[290px] pr-2">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($latestProducts as $index => $product)
                                <li class="p-3 rounded-lg flex justify-between items-center
                        {{ $index % 2 == 0 ? 'bg-gray-100 dark:bg-gray-700' : 'bg-gray-50 dark:bg-gray-800' }}">
                                    <div>
                                        <a href="{{ route('producto.show', $product->guid) }}" class="text-black dark:text-white hover:underline font-bold">
                                            {{ $product->nombre }}
                                        </a>
                                        <span class="text-gray-600 dark:text-gray-400"> - {{ number_format($product->precio, 2) }}€ - </span>
                                        <a href="{{ route('cliente.ver', $product->vendedor->guid) }}" class="hover:underline text-blue-600 dark:text-blue-400 font-medium">
                                            {{ $product->vendedor->nombre }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna 3: Valoraciones -->
        <div class="w-full md:flex-1 flex flex-col gap-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 min-h-[100px] flex flex-col justify-between transition">
                <div>
                    <h5 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-200">Valoraciones Totales</h5>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ array_sum($puntuaciones) }}</p>
                </div>
                <a href="{{route('admin.reviews')}}" class="block text-sm text-black bg-[#BFF205] hover:bg-[#96bf03] py-2 px-4 rounded-lg text-center mt-4 transform hover:scale-105 transition-all duration-300">
                    <b>Ver Todos</b>
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 h-[375px] flex flex-col justify-between transition">
                <div>
                    <h5 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                        <i class="fas fa-chart-bar text-[#BFF205]"></i> &nbsp; Gráfico de Valoraciones
                    </h5>
                    <div class="chart-container h-[300px]">
                        <canvas id="reviewsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Otra Fila con 2 Columnas -->
    <div class="flex flex-col md:flex-row gap-6 mt-6">
        <!-- Columna 1 (2/3 del ancho) -->
        <div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 h-[375px] flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-200 flex items-center gap-2">
                    <i class=" text-[#BFF205] fas fa-user-plus"></i> &nbsp;
                    Administradores
                </h5>
            </div>

            <ul class="space-y-3 overflow-y-auto pr-2" style="max-height: 290px;">
                @foreach ($admins as $admin)
                    <li class="flex justify-between items-center p-3 bg-gray-100 dark:bg-gray-700 rounded-lg shadow">
                        <div class="flex flex-col">
                            <span class="text-gray-900 dark:text-gray-200 font-semibold">{{ $admin->name }}</span>
                            <span class="text-gray-600 dark:text-gray-400 text-sm">{{ $admin->email }}</span>
                            <span class="text-gray-500 dark:text-gray-400 text-xs">Creado: {{ $admin->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($admin->email != 'admin@example.com')
                            <form action="{{ route('admin.delete', $admin->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este administrador?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded-lg hover:bg-red-600 transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Columna 2 (1/3 del ancho) -->
        <div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 h-[375px] overflow-y-auto">
            <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                <i class="fas fa-money-bill text-[#BFF205]"></i> &nbsp;
                Ultimas Ventas</h5>
            <p class="text-gray-700 dark:text-gray-300">

            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const puntuaciones = @json($puntuaciones); // Esto convierte $puntuaciones a JSON

        const ctx = document.getElementById('reviewsChart').getContext('2d');
        const reviewsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1 Estrella', '2 Estrellas', '3 Estrellas', '4 Estrellas', '5 Estrellas'],
                datasets: [{
                    label: 'Número de Valoraciones',
                    data: Object.values(puntuaciones), // Accede a los valores del array
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(191, 242, 5, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(191, 242, 5, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 10,
                    borderSkipped: false,
                    barThickness: 30,
                    maxBarThickness: 35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { size: 14 },
                        bodyFont: { size: 14 },
                        padding: 10,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 14, family: 'Arial, sans-serif' },
                            color: '#555',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#ddd', lineWidth: 0.3 },
                        ticks: {
                            font: { size: 14, family: 'Arial, sans-serif' },
                            color: '#555',
                            stepSize: 1,
                        }
                    }
                }
            }
        });
    </script>
@endsection
