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

  <!-- Búsqueda Predictiva -->
  <div class="max-w-xl mx-auto mb-8 relative" id="searchContainer">
    <div class="relative">
      <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
      <input type="text" id="searchInput" placeholder="Buscar productos..." autocomplete="off" class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition text-sm">
      <button id="clearSearch" class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <!-- Dropdown sugerencias -->
    <div id="searchSuggestions" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 max-h-80 overflow-y-auto">
      <!-- Se llena via JS -->
    </div>
  </div>

  <!-- Filtros Avanzados -->
  <div class="bg-white rounded-xl border border-gray-100 p-4 mb-10 max-w-4xl mx-auto">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      <select id="filterCategoria" onchange="applyFilters()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent bg-white">
        <option value="">Todas las categorías</option>
        <?php 
        $catsUnicas = [];
        foreach ($info_productos as $p) {
          $cat = $p['categoria'] ?? 'CAPS';
          if (!in_array($cat, $catsUnicas)) {
            $catsUnicas[] = $cat;
            echo '<option value="' . htmlspecialchars($cat) . '">' . htmlspecialchars($cat) . '</option>';
          }
        }
        ?>
      </select>
      <select id="filterTalla" onchange="applyFilters()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent bg-white">
        <option value="">Todas las tallas</option>
        <option value="S">S</option>
        <option value="M">M</option>
        <option value="L">L</option>
        <option value="XL">XL</option>
        <option value="XXL">XXL</option>
        <option value="28">28</option>
        <option value="30">30</option>
        <option value="32">32</option>
        <option value="34">34</option>
        <option value="36">36</option>
      </select>
      <select id="filterPrecio" onchange="applyFilters()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent bg-white">
        <option value="">Cualquier precio</option>
        <option value="0-50000">Menos de $50.000</option>
        <option value="50000-100000">$50.000 - $100.000</option>
        <option value="100000-200000">$100.000 - $200.000</option>
        <option value="200000+">Más de $200.000</option>
      </select>
      <select id="filterOrden" onchange="applyFilters()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent bg-white">
        <option value="">Ordenar por</option>
        <option value="precio-asc">Precio: menor a mayor</option>
        <option value="precio-desc">Precio: mayor a menor</option>
        <option value="nombre">Nombre A-Z</option>
      </select>
      <button onclick="resetFilters()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
        <i class="fa-solid fa-rotate-left mr-1"></i>Limpiar
      </button>
    </div>
  </div>

  <!-- Tabs rápidos -->
  <div class="flex justify-center gap-3 mb-6 flex-wrap">
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
      const response = await fetch('/carro/index.php?id=' + encodeURIComponent(id), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!response.ok) throw new Error('Error al agregar');
      if (typeof window.refreshMiniCart === 'function') window.refreshMiniCart();
      if (typeof showToast === 'function') showToast('Producto agregado al carrito', 'success');
    } catch (err) {
      window.location.href = '/carro/index.php?id=' + id;
    }
  });
});

// Filtros avanzados
function applyFilters() {
  const categoria = document.getElementById('filterCategoria').value;
  const talla = document.getElementById('filterTalla').value;
  const precio = document.getElementById('filterPrecio').value;
  const orden = document.getElementById('filterOrden').value;
  
  const cards = document.querySelectorAll('#grid-all .group, #grid-basicas .group');
  let visible = 0;
  
  cards.forEach(card => {
    const cat = card.querySelector('p.text-xs').textContent.toLowerCase();
    const title = card.querySelector('h3').textContent.toLowerCase();
    const priceText = card.querySelector('.text-brand-accent').textContent.replace(/[^0-9]/g, '');
    const price = parseInt(priceText) || 0;
    
    let match = true;
    
    if (categoria && !cat.includes(categoria.toLowerCase())) match = false;
    if (talla && !title.includes(talla.toLowerCase())) match = false;
    
    if (precio) {
      if (precio === '200000+' && price <= 200000) match = false;
      else if (precio !== '200000+') {
        const [min, max] = precio.split('-').map(Number);
        if (price < min || price > max) match = false;
      }
    }
    
    card.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  
  // Ordenar si es necesario
  if (orden) {
    const grid = document.getElementById('grid-all');
    const sorted = Array.from(cards).filter(c => c.style.display !== 'none').sort((a, b) => {
      const priceA = parseInt(a.querySelector('.text-brand-accent').textContent.replace(/[^0-9]/g, '')) || 0;
      const priceB = parseInt(b.querySelector('.text-brand-accent').textContent.replace(/[^0-9]/g, '')) || 0;
      const nameA = a.querySelector('h3').textContent;
      const nameB = b.querySelector('h3').textContent;
      
      if (orden === 'precio-asc') return priceA - priceB;
      if (orden === 'precio-desc') return priceB - priceA;
      if (orden === 'nombre') return nameA.localeCompare(nameB);
      return 0;
    });
    sorted.forEach(c => grid.appendChild(c));
  }
  
  updateNoResults(visible);
}

function resetFilters() {
  document.getElementById('filterCategoria').value = '';
  document.getElementById('filterTalla').value = '';
  document.getElementById('filterPrecio').value = '';
  document.getElementById('filterOrden').value = '';
  document.getElementById('searchInput').value = '';
  document.getElementById('clearSearch').classList.add('hidden');
  applyFilters();
  filterSearch('');
}

function updateNoResults(visible) {
  const noResults = document.getElementById('no-results');
  if (visible === 0) {
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

// Búsqueda predictiva con autocompletado
(function() {
  const input = document.getElementById('searchInput');
  const suggestions = document.getElementById('searchSuggestions');
  const clearBtn = document.getElementById('clearSearch');
  let debounceTimer;
  
  if (!input) return;
  
  async function fetchSuggestions(q) {
    if (q.length < 2) {
      suggestions.classList.add('hidden');
      return;
    }
    try {
      const res = await fetch('/api/busqueda.php?q=' + encodeURIComponent(q));
      const data = await res.json();
      renderSuggestions(data, q);
    } catch (e) {
      suggestions.classList.add('hidden');
    }
  }
  
  function renderSuggestions(items, query) {
    if (items.length === 0) {
      suggestions.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">No se encontraron productos</div>';
      suggestions.classList.remove('hidden');
      return;
    }
    
    suggestions.innerHTML = items.map(item => `
      <a href="infoPrenda.php?id=${item.id}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
        <img src="/upload/${item.foto || 'sinfoto.jpg'}" class="w-12 h-12 rounded-lg object-cover bg-gray-100">
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-brand-900 text-sm truncate">${highlight(item.referencia, query)}</p>
          <p class="text-xs text-gray-500">${item.categoria} · $${Number(item.precio).toLocaleString('es-CO')}</p>
        </div>
        <i class="fa-solid fa-arrow-right text-gray-300"></i>
      </a>
    `).join('');
    suggestions.classList.remove('hidden');
  }
  
  function highlight(text, query) {
    const regex = new RegExp(`(${query})`, 'gi');
    return text.replace(regex, '<span class="text-brand-accent bg-brand-50">$1</span>');
  }
  
  input.addEventListener('input', (e) => {
    const q = e.target.value.trim();
    clearBtn.classList.toggle('hidden', q.length === 0);
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchSuggestions(q), 200);
    filterSearch(q);
  });
  
  clearBtn.addEventListener('click', () => {
    input.value = '';
    clearBtn.classList.add('hidden');
    suggestions.classList.add('hidden');
    filterSearch('');
  });
  
  document.addEventListener('click', (e) => {
    if (!document.getElementById('searchContainer').contains(e.target)) {
      suggestions.classList.add('hidden');
    }
  });
})();
</script>

<?php if (isset($_SESSION['pedido_ok'])) { unset($_SESSION['pedido_ok']); ?>
<script>
if (typeof showToast === 'function') showToast('¡Pedido realizado con éxito! Te contactaremos pronto.', 'success');
</script>
<?php } ?>

<?php include('templates/footer.php'); ?>