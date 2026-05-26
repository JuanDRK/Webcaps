<?php
include('templates/cabecera.php');
require 'vendor/autoload.php';

$productos = new capsweb\Productos;
$info_productos = $productos->mostrarProductos();
$cantidad = count($info_productos);

$basicas = $productos->mostrarPrendasBasicas();

$noticia = new capsweb\noticias;
$notice = $noticia->mostrar();
?>

<!-- HERO SECTION -->
<section class="relative bg-brand-900 overflow-hidden">
  <div class="absolute inset-0">
    <img src="assets/images/carrusel/6.jpg" class="w-full h-full object-cover opacity-40" alt="hero">
    <div class="absolute inset-0 bg-gradient-to-t from-brand-900 via-brand-900/50 to-transparent"></div>
  </div>
  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 md:py-48">
    <div class="max-w-2xl">
      <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight mb-6">
        STREETWEAR<br><span class="text-brand-accent">CON IDENTIDAD</span>
      </h1>
      <p class="text-gray-300 text-lg mb-8 leading-relaxed">
        Descubre nuestra colección exclusiva de prendas urbanas. Diseñadas en Palmira para quienes marcan la diferencia.
      </p>
      <div class="flex flex-wrap gap-4">
        <a href="#productos" class="bg-brand-accent text-white px-8 py-3 rounded-lg font-bold hover:bg-rose-700 transition shadow-lg shadow-rose-900/30">
          <i class="fa-solid fa-bag-shopping mr-2"></i>Ver Colección
        </a>
        <a href="/conocenos.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-bold hover:bg-white hover:text-brand-900 transition">
          <i class="fa-solid fa-users mr-2"></i>Conócenos
        </a>
      </div>
    </div>
  </div>
</section>

<!-- PROMO BAR -->
<?php if (count($notice) > 0) { ?>
<div class="bg-brand-accent text-white overflow-hidden py-3">
  <div class="whitespace-nowrap animate-marquee inline-block">
    <?php
    $textos = [];
    foreach ($notice as $n) {
      if (!empty($n['lema'])) {
        foreach (explode(',', $n['lema']) as $campo) {
          $textos[] = trim($campo);
        }
      }
    }
    echo '<span class="inline-block mx-8 text-sm font-semibold tracking-wider">★ ' . implode(' ★ ', $textos) . ' ★</span>';
    ?>
  </div>
</div>
<style>
@keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
.animate-marquee { animation: marquee 20s linear infinite; }
</style>
<?php } ?>

<!-- PRODUCTOS -->
<section id="productos" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <!-- Header -->
  <div class="text-center mb-12">
    <h2 class="text-3xl md:text-4xl font-black text-brand-900 mb-3">NUESTROS PRODUCTOS</h2>
    <div class="w-20 h-1 bg-brand-accent mx-auto rounded-full"></div>
  </div>

  <!-- Búsqueda -->
  <div class="max-w-xl mx-auto mb-8">
    <div class="relative">
      <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
      <input type="text" id="searchInput" placeholder="Buscar productos..." class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition text-sm" oninput="filterSearch(this.value)">
    </div>
  </div>

  <!-- Filtros -->
  <div class="flex justify-center gap-3 mb-10 flex-wrap">
    <button onclick="filterProducts('all')" class="filter-btn active bg-brand-900 text-white px-6 py-2 rounded-full text-sm font-semibold transition hover:bg-brand-accent" data-filter="all">Todos</button>
    <button onclick="filterProducts('basicas')" class="filter-btn bg-gray-200 text-gray-700 px-6 py-2 rounded-full text-sm font-semibold transition hover:bg-brand-accent hover:text-white" data-filter="basicas">Básicas</button>
  </div>

  <!-- Skeleton Loaders -->
  <div id="skeleton-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    <?php for ($s = 0; $s < 8; $s++) { ?>
      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="aspect-[3/4] skeleton"></div>
        <div class="p-5 space-y-3">
          <div class="h-3 w-1/3 skeleton"></div>
          <div class="h-5 w-3/4 skeleton"></div>
          <div class="h-6 w-1/2 skeleton"></div>
        </div>
      </div>
    <?php } ?>
  </div>

  <!-- Grid Todos -->
  <div id="grid-all" class="hidden grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    <?php if ($cantidad > 0) { ?>
      <?php foreach ($info_productos as $item) { ?>
        <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100">
          <div class="relative overflow-hidden aspect-[3/4]">
            <?php
            $fotoPath = 'public/upload/' . $item['foto'];
            $fotoUrl  = 'upload/' . $item['foto'];
            ?>
            <?php if (file_exists($fotoPath)) { ?>
              <a href="infoPrenda.php?id=<?= (int)$item['id'] ?>">
                <img src="<?= $fotoUrl ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="<?= htmlspecialchars($item['referencia'], ENT_QUOTES, 'UTF-8') ?>">
              </a>
            <?php } else { ?>
              <img src="assets/images/sinfoto.jpg" class="w-full h-full object-cover" alt="sin foto">
            <?php } ?>
            <button data-add-cart="<?= (int)$item['id'] ?>" class="absolute bottom-4 right-4 bg-brand-accent text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition duration-300 hover:bg-rose-700 cursor-pointer">
              <i class="fa-solid fa-plus"></i>
            </button>
          </div>
          <div class="p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1"><?= htmlspecialchars($item['categoria'] ?? 'CAPS', ENT_QUOTES, 'UTF-8') ?></p>
            <h3 class="font-bold text-brand-900 text-lg mb-2 truncate"><?= htmlspecialchars($item['referencia'], ENT_QUOTES, 'UTF-8') ?></h3>
            <div class="flex items-center justify-between">
              <span class="text-brand-accent font-black text-xl">$<?= number_format($item['precio'], 0, ',', '.') ?></span>
              <span class="text-gray-400 text-xs">COP</span>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php } else { ?>
      <div class="col-span-full text-center py-20">
        <i class="fa-solid fa-box-open text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-500">Próximamente nueva colección</h3>
        <p class="text-gray-400 mt-2">Vuelve pronto para descubrir nuestras prendas.</p>
      </div>
    <?php } ?>
  </div>

  <!-- Grid Básicas (hidden by default) -->
  <div id="grid-basicas" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    <?php if (count($basicas) > 0) { ?>
      <?php foreach ($basicas as $item) { ?>
        <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100">
          <div class="relative overflow-hidden aspect-[3/4]">
            <?php
            $fotoPath = 'public/upload/' . $item['foto'];
            $fotoUrl  = 'upload/' . $item['foto'];
            ?>
            <?php if (file_exists($fotoPath)) { ?>
              <a href="infoPrenda.php?id=<?= (int)$item['id'] ?>">
                <img src="<?= $fotoUrl ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="<?= htmlspecialchars($item['referencia'], ENT_QUOTES, 'UTF-8') ?>">
              </a>
            <?php } else { ?>
              <img src="assets/images/sinfoto.jpg" class="w-full h-full object-cover" alt="sin foto">
            <?php } ?>
            <button data-add-cart="<?= (int)$item['id'] ?>" class="absolute bottom-4 right-4 bg-brand-accent text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition duration-300 hover:bg-rose-700 cursor-pointer">
              <i class="fa-solid fa-plus"></i>
            </button>
          </div>
          <div class="p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">BÁSICA</p>
            <h3 class="font-bold text-brand-900 text-lg mb-2 truncate"><?= htmlspecialchars($item['referencia'], ENT_QUOTES, 'UTF-8') ?></h3>
            <div class="flex items-center justify-between">
              <span class="text-brand-accent font-black text-xl">$<?= number_format($item['precio'], 0, ',', '.') ?></span>
              <span class="text-gray-400 text-xs">COP</span>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php } else { ?>
      <div class="col-span-full text-center py-20">
        <h3 class="text-xl font-bold text-gray-500">No hay básicas disponibles</h3>
      </div>
    <?php } ?>
  </div>
</section>

<!-- NEWSLETTER / CTA -->
<section class="bg-brand-900 py-20">
  <div class="max-w-4xl mx-auto px-4 text-center">
    <h2 class="text-3xl md:text-4xl font-black text-white mb-4">ÚNETE A LA CULTURA</h2>
    <p class="text-gray-400 mb-8">Síguenos en redes para enterarte de lanzamientos exclusivos y descuentos.</p>
    <div class="flex justify-center gap-4">
      <a href="https://www.instagram.com/caps_palmira/" target="_blank" class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-8 py-3 rounded-full font-bold hover:opacity-90 transition">
        <i class="fa-brands fa-instagram mr-2"></i>Instagram
      </a>
      <a href="https://www.tiktok.com/@capsculture" target="_blank" class="bg-white text-brand-900 px-8 py-3 rounded-full font-bold hover:bg-gray-200 transition">
        <i class="fa-brands fa-tiktok mr-2"></i>TikTok
      </a>
    </div>
  </div>
</section>

<script>
function filterProducts(type) {
  document.querySelectorAll('.filter-btn').forEach(btn => {
    if (btn.dataset.filter === type) {
      btn.classList.add('bg-brand-900', 'text-white');
      btn.classList.remove('bg-gray-200', 'text-gray-700');
    } else {
      btn.classList.remove('bg-brand-900', 'text-white');
      btn.classList.add('bg-gray-200', 'text-gray-700');
    }
  });
  if (type === 'all') {
    document.getElementById('grid-all').classList.remove('hidden');
    document.getElementById('grid-all').classList.add('grid');
    document.getElementById('grid-basicas').classList.add('hidden');
    document.getElementById('grid-basicas').classList.remove('grid');
  } else {
    document.getElementById('grid-all').classList.add('hidden');
    document.getElementById('grid-all').classList.remove('grid');
    document.getElementById('grid-basicas').classList.remove('hidden');
    document.getElementById('grid-basicas').classList.add('grid');
  }
}

// Ocultar skeletons al cargar
window.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    document.getElementById('skeleton-grid').classList.add('hidden');
    const gridAll = document.getElementById('grid-all');
    gridAll.classList.remove('hidden');
    gridAll.classList.add('grid');
  }, 600);
});

// Agregar al carrito vía AJAX
document.querySelectorAll('[data-add-cart]').forEach(btn => {
  btn.addEventListener('click', async (e) => {
    e.preventDefault();
    e.stopPropagation();
    const id = btn.dataset.addCart;
    try {
      await fetch('/carro/index.php?id=' + encodeURIComponent(id));
      if (typeof window.refreshMiniCart === 'function') window.refreshMiniCart();
      if (typeof showToast === 'function') showToast('Producto agregado al carrito', 'success');
    } catch (err) {
      window.location.href = '/carro/index.php?id=' + id;
    }
  });
});

// Búsqueda full-text en catálogo
function filterSearch(query) {
  const q = query.toLowerCase().trim();
  const cards = document.querySelectorAll('#grid-all .group, #grid-basicas .group');
  let visible = 0;
  cards.forEach(card => {
    const title = card.querySelector('h3').textContent.toLowerCase();
    const cat = card.querySelector('p.text-xs').textContent.toLowerCase();
    const match = title.includes(q) || cat.includes(q);
    card.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  const noResults = document.getElementById('no-results');
  if (visible === 0 && q !== '') {
    if (!noResults) {
      const div = document.createElement('div');
      div.id = 'no-results';
      div.className = 'col-span-full text-center py-12';
      div.innerHTML = '<i class="fa-solid fa-magnifying-glass text-4xl text-gray-300 mb-3"></i><p class="text-gray-500 font-semibold">No se encontraron productos</p>';
      document.getElementById('grid-all').appendChild(div);
    }
  } else if (noResults) {
    noResults.remove();
  }
}
</script>

<?php if (isset($_SESSION['pedido_ok'])) { unset($_SESSION['pedido_ok']); ?>
<script>
if (typeof showToast === 'function') showToast('¡Pedido realizado con éxito! Te contactaremos pronto.', 'success');
</script>
<?php } ?>

<?php include('templates/footer.php'); ?>