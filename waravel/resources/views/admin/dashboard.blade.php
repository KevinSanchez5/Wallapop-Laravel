@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-semibold mb-6">Dashboard de Administración</h1>

    <!-- Contenedor Flex de 3 Columnas -->
    <div class="flex flex-row gap-6">

        <!-- Columna 1: Usuarios -->
        <div class="flex-1 flex flex-col gap-6">
            <!-- Tarjeta: Total de Usuarios -->
            <div class="bg-white shadow-lg rounded-lg p-6 min-h-[100px] flex flex-col justify-between">
                <div>
                    <h5 class="text-lg font-semibold mb-2">Usuarios Registrados</h5>
                    <p class="text-2xl font-bold">{{ $totalUsers }}</p>
                </div>
                <a href="{{ route('clients.list') }}" class="block text-sm text-black bg-[#BFF205] hover:bg-[#96bf03] py-2 px-4 rounded-lg text-center mt-4 transform hover:scale-105 transition-all duration-300">
                    <b>Ver Todos</b>
                </a>
            </div>
            <!-- Tarjeta: Últimos Clientes -->
            <div class="bg-white shadow-lg rounded-lg p-6 min-h-[250px] flex flex-col justify-between">
                <div>
                    <h5 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-users text-[#BFF205]"></i> Últimos Clientes
                    </h5>
                    <ul class="space-y-2">
                        @foreach ($latestClients as $client)
                            <li class="text-black">
                                {{ $client->name }} ({{ $client->email }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Columna 2: Productos -->
        <div class="flex-1 flex flex-col gap-6">
            <!-- Tarjeta: Total de Productos -->
            <div class="bg-white shadow-lg rounded-lg p-6 min-h-[100px] flex flex-col justify-between">
                <div>
                    <h5 class="text-lg font-semibold mb-2">Productos Activos</h5>
                    <p class="text-2xl font-bold">{{ $totalProducts }}</p>
                </div>
                <a href="{{ route('products.list') }}" class="block text-sm text-black bg-[#BFF205] hover:bg-[#96bf03] py-2 px-4 rounded-lg text-center mt-4 transform hover:scale-105 transition-all duration-300">
                    <b>Ver Todos</b>
                </a>
            </div>
            <!-- Tarjeta: Últimos Productos -->
            <div class="bg-white shadow-lg rounded-lg p-6 min-h-[250px] flex flex-col justify-between">
                <div>
                    <h5 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-box text-[#BFF205]"></i> Últimos Productos
                    </h5>
                    <ul class="space-y-2">
                        @foreach ($latestProducts as $product)
                            <li class="text-black">
                                {{ $product->nombre }} - ${{ number_format($product->precio, 2) }} - Cliente: {{ $product->vendedor->nombre }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Columna 3: Valoraciones -->
        <div class="flex-1 flex flex-col gap-6">
            <!-- Tarjeta: Valoraciones Totales -->
            <div class="bg-white shadow-lg rounded-lg p-6 min-h-[100px] flex flex-col justify-between">
                <div>
                    <h5 class="text-lg font-semibold mb-2">Valoraciones Totales</h5>
                    <p class="text-2xl font-bold">{{ array_sum($puntuaciones) }}</p>
                </div>
                <a href="#" class="block text-sm text-black bg-[#BFF205] hover:bg-[#96bf03] py-2 px-4 rounded-lg text-center mt-4 transform hover:scale-105 transition-all duration-300">
                    <b>Ver Todos</b>
                </a>
            </div>
            <!-- Tarjeta: Gráfica de Valoraciones -->
            <div class="bg-white shadow-lg rounded-lg p-6 min-h-[250px] flex flex-col justify-between">
                <div>
                    <h5 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-chart-bar text-[#BFF205]"></i> Gráfico de Valoraciones
                    </h5>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="reviewsChart"></canvas>
                    </div>
                </div>
            </div>
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
