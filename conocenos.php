<?php include('templates/cabecera.php'); ?>

<!-- Hero -->
<section class="relative h-80 md:h-96 flex items-center justify-center overflow-hidden">
  <div class="absolute inset-0">
    <img src="assets/images/carrusel/6.jpg" class="w-full h-full object-cover" alt="">
    <div class="absolute inset-0 bg-brand-900/70"></div>
  </div>
  <div class="relative z-10 text-center px-4">
    <h1 class="text-4xl md:text-6xl font-black text-white uppercase tracking-wider">Conócenos</h1>
    <p class="text-gray-300 mt-3 text-lg">Streetwear con identidad desde Palmira</p>
  </div>
</section>

<!-- Historia / Misión / Visión -->
<section class="max-w-6xl mx-auto px-4 py-16 space-y-20">
  <!-- Historia -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
    <div class="rounded-2xl overflow-hidden shadow-lg h-64 md:h-80">
      <img src="https://lh3.googleusercontent.com/p/AF1QipO6A1Tm5GVp_ih06-8K_Zf18BrPDZgkg8zgbbbl=s680-w680-h510" class="w-full h-full object-cover hover:scale-105 transition duration-700" alt="">
    </div>
    <div>
      <span class="text-brand-accent font-bold text-sm uppercase tracking-widest">Desde 2017</span>
      <h2 class="text-3xl font-black text-brand-900 mt-2 mb-4">Nuestra Historia</h2>
      <p class="text-gray-600 leading-relaxed">
        Caps Culture comenzó en el 2017 con la venta de gorras en un pequeño local de la galería central de Palmira, Valle. Con nuestro valor diferencial en productos y campañas publicitarias logramos en poco tiempo pasar a una mejor ubicación más central. En la temporada de cuarentena nos apoyamos en la virtualidad y logramos un crecimiento exponencial que nos ayudó a fidelizar nuestro nicho de mercado.
      </p>
    </div>
  </div>

  <!-- Misión -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
    <div class="order-2 md:order-1">
      <span class="text-brand-accent font-bold text-sm uppercase tracking-widest">Lo que nos mueve</span>
      <h2 class="text-3xl font-black text-brand-900 mt-2 mb-4">Misión</h2>
      <p class="text-gray-600 leading-relaxed">
        Somos una marca que busca satisfacer los gustos y tendencias de los jóvenes que se sienten diferentes a su entorno. Queremos que por medio de su ropa se identifiquen y sientan cómodos respecto a sus gustos, encontrando más grupos en común gracias a nuestra red de comunicación y eventos.
      </p>
    </div>
    <div class="rounded-2xl overflow-hidden shadow-lg h-64 md:h-80 order-1 md:order-2">
      <img src="assets/images/carrusel/6.jpg" class="w-full h-full object-cover hover:scale-105 transition duration-700" alt="">
    </div>
  </div>

  <!-- Visión -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
    <div class="rounded-2xl overflow-hidden shadow-lg h-64 md:h-80">
      <img src="assets/images/carrusel/6.jpg" class="w-full h-full object-cover hover:scale-105 transition duration-700" alt="">
    </div>
    <div>
      <span class="text-brand-accent font-bold text-sm uppercase tracking-widest">Hacia dónde vamos</span>
      <h2 class="text-3xl font-black text-brand-900 mt-2 mb-4">Visión</h2>
      <p class="text-gray-600 leading-relaxed">
        Caps Culture desea ser una de las principales empresas fabricantes de moda diferente en Colombia, reconocida por sus campañas publicitarias, diseños únicos y precios asequibles. Generando empleo por medio de nuestros satélites de talleres y apoyando a otros emprendimientos de la ciudad.
      </p>
    </div>
  </div>
</section>

<!-- Equipo -->
<section class="bg-gray-100 py-16">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-black text-brand-900 text-center mb-12">Nuestro equipo</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <?php for ($i = 0; $i < 3; $i++) { ?>
        <div class="bg-white rounded-2xl overflow-hidden shadow hover:shadow-xl transition group">
          <div class="h-64 overflow-hidden">
            <img src="https://th.bing.com/th/id/OIP.ORrrSIMue2BJCegJasODkwHaNL?pid=ImgDet&rs=1" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="">
          </div>
          <div class="p-6 text-center">
            <h3 class="font-bold text-brand-900 text-lg uppercase">Fundador</h3>
            <p class="text-gray-500 text-sm mt-2">Apasionado por el streetwear y la cultura urbana. Liderando Caps Culture desde sus inicios.</p>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<?php include('templates/footer.php'); ?>