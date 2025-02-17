<footer class="bg-[#BFF205] text-black py-6">
    <div class="container mx-auto flex justify-between items-center px-6">
        <div class="flex items-center">
            <img src="{{ asset('imgs/logo_negro.png') }}" alt="Waravel Logo" class="w-12 h-12 mr-4">
            <span class="text-3xl font-semibold">Waravel</span>
        </div>

        <div class="flex space-x-8">
            <a href="" class="font-medium hover:text-white transition-colors">Quiénes somos</a>
            <a href="" class="font-medium hover:text-white transition-colors">Contacto</a>
            <a href="" class="font-medium hover:text-white transition-colors">Términos y condiciones</a>
        </div>

        <div class="flex space-x-6">
            <a href="" target="_blank" class="font-medium hover:text-white transition-colors">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="" target="_blank" class="font-medium hover:text-white transition-colors">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="" target="_blank" class="font-medium hover:text-white transition-colors">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://github.com/wolverine307mda/Wallapop-Laravel/tree/develop" target="_blank" class="font-medium hover:text-white transition-colors">
                <i class="fab fa-github"></i>
            </a>
            <a href="" target="_blank" class="font-medium hover:text-white transition-colors">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </div>

    <div class="mt-4 text-center">
        <p class="text-sm font-medium">&copy; {{ date('Y') }} Waravel. Todos los derechos reservados.</p>
    </div>
</footer>
