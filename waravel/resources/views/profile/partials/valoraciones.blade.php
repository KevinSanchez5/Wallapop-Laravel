@extends('layouts.profile')

@section('title', 'Valoraciones')

@section('section')
    <section>
        <div class="flex flex-wrap justify-center gap-4 mb-4">
            <button onclick="window.location.href='{{ route('profile') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Productos</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.reviews') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#BFF205] hover:bg-[#A0D500]">
                <b>Valoraciones</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.orders') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Mis pedidos</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.sales') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Mis ventas</b>
            </button>
            <button onclick="window.location.href='{{ route('profile.favorites') }}'"
                    class="px-4 py-2 rounded-lg text-black bg-[#82ae00] hover:bg-[#BFF205]">
                <b>Favoritos</b>
            </button>
        </div>
        <!-- Valoraciones -->
        <div id="valoraciones">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Valoraciones</h2>
            <ul>
                @foreach ($valoraciones as $valoracion)
                    @if($loop->last)
                        <li class="p-4">
                    @else
                        <li class="p-4 border-b border-gray-300 dark:border-gray-700">
                    @endif
                        <div class="flex items-center gap-3">
                            <a href="{{ route('cliente.ver', $valoracion->creador->guid) }}"
                               class="text-blue-500 dark:text-blue-400 font-semibold hover:underline">
                                {{ optional($valoracion->creador)->nombre ?? 'Usuario eliminado' }}
                                {{ optional($valoracion->creador)->apellido ?? '' }}
                            </a>
                            <span class="text-yellow-500">{{ str_repeat('⭐', $valoracion->puntuacion) }}</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">{{ $valoracion->comentario }}</p>
                        <p class="text-gray-500 text-sm mt-1">
                            📅 {{ $valoracion->created_at->format('d/m/Y') }}
                        </p>
                    </li>
                @endforeach
            </ul>
            <!-- Paginación -->
            @if ($valoraciones->hasPages())
                <div class="mt-4 mx-auto text-center">
                    {{ $valoraciones->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </section>
@endsection
