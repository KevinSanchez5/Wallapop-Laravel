<footer class="bg-[#BFF205] text-black py-6">
    <div class="container mx-auto flex flex-col sm:flex-row justify-center sm:justify-between items-center px-6">
        <!-- Logo y nombre -->
        <div class="flex items-center mb-4 sm:mb-0 justify-center sm:justify-start">
            <div class="flex justify-center mb-4 sm:mb-0 text-black">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 34.45 30.81" class="w-12 h-12 mr-4" fill="currentColor">
                    <circle cx="8.18" cy="6.32" r="6.32"/>
                    <circle cx="26.27" cy="6.32" r="6.32"/>
                    <path d="M14.91 30.81a1.44 1.44 0 0 0 1.44-1.44V15.9a1.44 1.44 0 0 0-1.44-1.44H1.44A1.44 1.44 0 0 0 0 15.9c0 7.1 7.6 14.91 14.91 14.91Z"/>
                    <path d="M19.54 30.81a1.44 1.44 0 0 1-1.44-1.44V15.9a1.44 1.44 0 0 1 1.44-1.44h13.47a1.44 1.44 0 0 1 1.44 1.44c0 7.1-7.6 14.91-14.91 14.91Z"/>
                </svg>
            </div>
            <span class="text-3xl font-semibold text-3xl font-extrabold text-gray-800" style="font-family: 'Inter', sans-serif;">Waravel</span>
        </div>

        <!-- Enlaces -->
        <div class="flex flex-wrap justify-center space-x-8 sm:space-x-6 mb-4 sm:mb-0">
            <a href="./pages/quienesSomos.html" class="font-medium hover:text-white transition-colors">Quiénes somos</a>
            <a href="" class="font-medium hover:text-white transition-colors">Contacto</a>
            <a href="./pages/terminos.html" class="font-medium hover:text-white transition-colors">Términos y condiciones</a>
        </div>

        <!-- Redes Sociales -->
        <div class="flex justify-center space-x-6">
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

    <!-- Texto de derechos reservados -->
    <div class="mt-4 text-center">
        <p class="text-sm font-medium">&copy; {{ date('Y') }} Waravel. Todos los derechos reservados.</p>
    </div>
</footer>
