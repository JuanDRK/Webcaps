<?php include('templates/cabecera.php'); ?>

<!-- Hero -->
<section class="relative h-72 md:h-80 flex items-center justify-center overflow-hidden">
  <div class="absolute inset-0">
    <img src="assets/images/carrusel/6.jpg" class="w-full h-full object-cover" alt="">
    <div class="absolute inset-0 bg-brand-900/60"></div>
  </div>
  <div class="relative z-10 text-center px-4">
    <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-wider">Outfits</h1>
    <p class="text-gray-300 mt-2">Inspírate con nuestros looks</p>
  </div>
</section>

<!-- Gallery -->
<section class="max-w-7xl mx-auto px-4 py-12">
  <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
    <?php for ($i = 0; $i < 12; $i++) { ?>
      <div class="break-inside-avoid rounded-xl overflow-hidden group relative">
        <a href="https://www.instagram.com/reel/CpoEMcCJPKa/" target="_blank">
          <img src="assets/images/sinfoto.jpg" class="w-full object-cover transition duration-500 group-hover:scale-110" alt="">
          <div class="absolute inset-0 bg-brand-900/0 group-hover:bg-brand-900/30 transition flex items-center justify-center">
            <i class="fa-brands fa-instagram text-white text-3xl opacity-0 group-hover:opacity-100 transition scale-75 group-hover:scale-100"></i>
          </div>
        </a>
      </div>
    <?php } ?>
  </div>
</section>

<?php include('templates/footer.php'); ?>