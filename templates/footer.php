<!-- FOOTER -->
<footer class="bg-brand-900 text-white mt-20">
  <!-- Mapa moderno -->
  <div class="relative w-full h-80 bg-brand-800">
    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15929.036855565007!2d-76.2980523!3d3.5274827!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3a057a956758ff%3A0x7ab362da06171ea4!2sCaps%20palmira!5e0!3m2!1ses!2sco!4v1681932814096!5m2!1ses!2sco"
      width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" class="grayscale hover:grayscale-0 transition duration-700"></iframe>
    <!-- Overlay info card -->
    <div class="absolute bottom-4 left-4 sm:bottom-8 sm:left-8 bg-white/95 backdrop-blur text-brand-900 rounded-xl shadow-2xl p-4 sm:p-5 max-w-xs">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 rounded-full bg-brand-accent text-white flex items-center justify-center flex-shrink-0">
          <i class="fa-solid fa-location-dot"></i>
        </div>
        <div>
          <p class="font-black text-sm">CAPS Palmira</p>
          <p class="text-xs text-gray-500">Cra. 27 #30-63</p>
        </div>
      </div>
      <a href="https://www.google.com/maps/dir/?api=1&destination=3.5274827,-76.2980523" target="_blank" class="block text-center bg-brand-900 text-white text-xs font-bold py-2 rounded-lg hover:bg-brand-accent transition mt-1">
        <i class="fa-solid fa-diamond-turn-right mr-1"></i>Cómo llegar
      </a>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
      <!-- Logo -->
      <div class="flex flex-col items-center md:items-start">
        <a href="/index.php" class="flex items-center gap-2 mb-4">
          <img src="/assets/images/logo caps circulo.png" alt="logo" class="h-12 w-12 rounded-full object-cover">
          <div>
            <div class="font-black text-xl tracking-wider">CAPS</div>
            <div class="text-xs text-gray-400 tracking-widest">CULTURE</div>
          </div>
        </a>
        <p class="text-gray-400 text-sm text-center md:text-left">Streetwear con identidad.<br>Hecho en Palmira, Colombia.</p>
      </div>

      <!-- Contacto -->
      <div class="text-center md:text-left">
        <h3 class="font-bold text-lg mb-4 uppercase tracking-wider">Contacto</h3>
        <ul class="space-y-3 text-gray-400 text-sm">
          <li><i class="fa-solid fa-location-dot mr-2 text-brand-accent"></i>Palmira, Valle del Cauca</li>
          <li><i class="fa-solid fa-road mr-2 text-brand-accent"></i>Cra. 27 #30-63</li>
          <li><i class="fa-solid fa-envelope mr-2 text-brand-accent"></i>capspalmira@gmail.com</li>
          <li><i class="fa-solid fa-phone mr-2 text-brand-accent"></i>+57 315 8726969</li>
        </ul>
      </div>

      <!-- Redes -->
      <div class="text-center md:text-left">
        <h3 class="font-bold text-lg mb-4 uppercase tracking-wider">Síguenos</h3>
        <div class="flex justify-center md:justify-start gap-4">
          <a href="https://www.facebook.com/Capscultura" target="_blank" class="w-10 h-10 rounded-full bg-brand-700 flex items-center justify-center hover:bg-brand-accent transition transform hover:scale-110">
            <i class="fa-brands fa-facebook-f text-white"></i>
          </a>
          <a href="https://www.instagram.com/caps_palmira/" target="_blank" class="w-10 h-10 rounded-full bg-brand-700 flex items-center justify-center hover:bg-brand-accent transition transform hover:scale-110">
            <i class="fa-brands fa-instagram text-white"></i>
          </a>
          <a href="https://www.tiktok.com/@capsculture" target="_blank" class="w-10 h-10 rounded-full bg-brand-700 flex items-center justify-center hover:bg-brand-accent transition transform hover:scale-110">
            <i class="fa-brands fa-tiktok text-white"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="border-t border-brand-700 mt-10 pt-6 text-center text-gray-500 text-sm">
      © <?= date('Y') ?> Caps Culture. Todos los derechos reservados.
    </div>
  </div>
</footer>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/573158726969" target="_blank" class="fixed bottom-6 right-6 z-50 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition transform hover:scale-110">
  <i class="fa-brands fa-whatsapp text-2xl"></i>
</a>

</body>
</html>