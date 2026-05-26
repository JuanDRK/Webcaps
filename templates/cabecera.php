<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CAPS Culture — Streetwear</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter','sans-serif'] },
          colors: {
            brand: { 900:'#0f0f0f', 800:'#1a1a1a', 700:'#2a2a2a', accent:'#e11d48' }
          }
        }
      }
    }
  </script>
  <style>
    body{font-family:'Inter',sans-serif;}
    /* Toast system */
    .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
    .toast { background: white; border-left: 4px solid #e11d48; padding: 1rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 0.75rem; transform: translateX(120%); transition: transform 0.3s ease; }
    .toast.show { transform: translateX(0); }
    .toast.success { border-left-color: #22c55e; }
    .toast.error { border-left-color: #ef4444; }
    .toast.info { border-left-color: #3b82f6; }
    /* Skeleton loaders */
    .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 0.5rem; }
    @keyframes skeleton-loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    /* Dark mode */
    .dark body { background-color: #0f0f0f; color: #f3f4f6; }
    .dark .bg-white { background-color: #1a1a1a !important; color: #f3f4f6 !important; }
    .dark .bg-white h3,
    .dark .bg-white h2,
    .dark .bg-white h1,
    .dark .bg-white p,
    .dark .bg-white span { color: #f3f4f6 !important; }
    .dark .text-brand-900 { color: #f3f4f6 !important; }
    .dark .text-gray-800 { color: #f3f4f6 !important; }
    .dark .text-gray-700 { color: #d1d5db !important; }
    .dark .text-gray-600 { color: #9ca3af !important; }
    .dark .text-gray-500 { color: #9ca3af !important; }
    .dark .text-gray-400 { color: #6b7280 !important; }
    .dark .text-gray-300 { color: #4b5563 !important; }
    .dark .border-gray-100 { border-color: #2a2a2a !important; }
    .dark .border-gray-200 { border-color: #333 !important; }
    .dark .bg-gray-50 { background-color: #111 !important; }
    .dark .bg-gray-100 { background-color: #1f1f1f !important; }
    .dark .bg-gray-200 { background-color: #2a2a2a !important; }
    .dark input,
    .dark select,
    .dark textarea { background-color: #1a1a1a !important; color: #f3f4f6 !important; border-color: #333 !important; }
    .dark .toast { background: #1a1a1a !important; color: #f3f4f6 !important; }
    .dark .skeleton { background: linear-gradient(90deg, #1f1f1f 25%, #2a2a2a 50%, #1f1f1f 75%); background-size: 200% 100%; }
  </style>
</head>
<body class="bg-gray-50 text-gray-900">

<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<!-- NAVBAR -->
<nav class="bg-brand-900 text-white sticky top-0 z-50 shadow-lg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <a href="/index.php" class="flex items-center gap-3 hover:opacity-80 transition">
        <img src="/assets/images/logo caps circulo.png" alt="logo" class="h-10 w-10 rounded-full object-cover">
        <span class="font-black text-xl tracking-wider">CAPS</span>
      </a>

      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center gap-8">
        <a href="/outfits.php" class="text-sm font-medium text-gray-300 hover:text-white transition"><i class="fa-solid fa-shirt mr-1"></i>Outfits</a>
        <a href="/conocenos.php" class="text-sm font-medium text-gray-300 hover:text-white transition"><i class="fa-solid fa-people-group mr-1"></i>Conócenos</a>
        <!-- Mini Cart -->
        <div class="relative" id="mini-cart-wrapper">
          <button id="mini-cart-toggle" class="text-sm font-medium text-gray-300 hover:text-white transition relative">
            <i class="fa-solid fa-cart-shopping mr-1"></i>Carrito
            <span id="nav-cart-count" class="absolute -top-2 -right-3 bg-brand-accent text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden"></span>
          </button>
          <div id="mini-cart-dropdown" class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden z-50">
            <div class="p-4 border-b border-gray-100">
              <h3 class="font-bold text-brand-900">Tu carrito</h3>
            </div>
            <div id="mini-cart-items" class="max-h-64 overflow-y-auto p-3 space-y-3">
              <!-- JS renders items here -->
            </div>
            <div id="mini-cart-footer" class="p-4 border-t border-gray-100 hidden">
              <div class="flex justify-between font-bold text-brand-900 mb-3">
                <span>Total</span>
                <span id="mini-cart-total">$0</span>
              </div>
              <a href="/carro/index.php" class="block w-full bg-brand-accent text-white text-center py-2 rounded-lg font-bold hover:bg-rose-700 transition text-sm mb-2">Ver carrito</a>
              <a href="/finalizar.php" class="block w-full border border-gray-200 text-gray-700 text-center py-2 rounded-lg font-semibold hover:border-brand-accent hover:text-brand-accent transition text-sm">Finalizar compra</a>
            </div>
            <div id="mini-cart-empty" class="p-6 text-center">
              <i class="fa-solid fa-cart-shopping text-3xl text-gray-300 mb-2"></i>
              <p class="text-gray-500 text-sm">Tu carrito está vacío</p>
              <a href="/index.php#productos" class="inline-block mt-3 text-brand-accent font-semibold text-sm hover:underline">Ver productos</a>
            </div>
          </div>
        </div>
        <a href="/acceso.php" class="bg-white text-brand-900 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200 transition">ADMIN</a>
        <button id="dark-mode-toggle" class="text-gray-300 hover:text-white transition" title="Modo oscuro">
          <i class="fa-solid fa-moon"></i>
        </button>
      </div>

      <!-- Mobile button -->
      <button id="mobile-menu-btn" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
        <i class="fa-solid fa-bars text-xl"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden md:hidden bg-brand-800 border-t border-brand-700">
    <div class="px-4 py-3 space-y-2">
      <a href="/outfits.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-brand-700"><i class="fa-solid fa-shirt mr-2"></i>Outfits</a>
      <a href="/conocenos.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-brand-700"><i class="fa-solid fa-people-group mr-2"></i>Conócenos</a>
      <a href="/carro/index.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-brand-700"><i class="fa-solid fa-cart-shopping mr-2"></i>Carrito</a>
      <a href="/acceso.php" class="block px-3 py-2 rounded-md text-base font-medium text-brand-accent hover:text-white"><i class="fa-solid fa-lock mr-2"></i>Admin</a>
    </div>
  </div>
</nav>

<script>
  window.__CART_DATA__ = <?= json_encode($_SESSION['carrito'] ?? []) ?>;
</script>

<script>
  // Mobile menu
  document.getElementById('mobile-menu-btn').addEventListener('click', () => {
    document.getElementById('mobile-menu').classList.toggle('hidden');
  });

  // Toast system
  window.showToast = function(message, type = 'info', duration = 3000) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    const icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-circle-exclamation' : 'fa-info-circle';
    toast.innerHTML = '<i class="fa-solid ' + icon + ' text-' + (type==='success'?'green':type==='error'?'red':'blue') + '-500"></i><span>' + message + '</span>';
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); }, duration);
  };

  // Dark mode
  (function() {
    const toggle = document.getElementById('dark-mode-toggle');
    if (!toggle) return;
    const html = document.documentElement;
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') html.classList.add('dark');
    toggle.addEventListener('click', () => {
      html.classList.toggle('dark');
      localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
    });
  })();

  // Mini Cart
  (function() {
    const toggle = document.getElementById('mini-cart-toggle');
    const dropdown = document.getElementById('mini-cart-dropdown');
    const itemsContainer = document.getElementById('mini-cart-items');
    const footer = document.getElementById('mini-cart-footer');
    const empty = document.getElementById('mini-cart-empty');
    const totalEl = document.getElementById('mini-cart-total');
    const countBadge = document.getElementById('nav-cart-count');
    if (!toggle) return;

    function fmt(n) {
      return '$' + Number(n).toLocaleString('es-CO', {maximumFractionDigits:0});
    }

    function render() {
      const cart = window.__CART_DATA__ || {};
      const items = Object.values(cart);
      if (items.length === 0) {
        itemsContainer.innerHTML = '';
        footer.classList.add('hidden');
        empty.classList.remove('hidden');
        countBadge.classList.add('hidden');
        return;
      }
      empty.classList.add('hidden');
      footer.classList.remove('hidden');
      let total = 0;
      let totalQty = 0;
      itemsContainer.innerHTML = items.map(item => {
        total += item.precio * item.cantidad;
        totalQty += parseInt(item.cantidad) || 1;
        const talla = item.talla ? '<span class="text-xs text-gray-400 ml-2">Talla: ' + item.talla + '</span>' : '';
        return '<div class="flex gap-3 items-center">' +
          '<img src="/upload/' + (item.foto || 'sinfoto.jpg') + '" class="w-12 h-12 rounded-lg object-cover bg-gray-100" alt="">' +
          '<div class="flex-1 min-w-0">' +
            '<p class="text-sm font-semibold text-brand-900 truncate">' + item.referencia + '</p>' +
            '<p class="text-xs text-gray-500">' + item.cantidad + ' x ' + fmt(item.precio) + talla + '</p>' +
          '</div>' +
          '<span class="text-sm font-bold text-brand-900">' + fmt(item.precio * item.cantidad) + '</span>' +
        '</div>';
      }).join('');
      totalEl.textContent = fmt(total);
      countBadge.textContent = totalQty;
      countBadge.classList.remove('hidden');
    }

    async function fetchCart() {
      try {
        const res = await fetch('/api/carrito.php');
        if (!res.ok) return;
        window.__CART_DATA__ = await res.json();
        render();
      } catch (e) { /* fallback: usar datos locales */ render(); }
    }
    window.refreshMiniCart = fetchCart;

    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('hidden');
      if (!dropdown.classList.contains('hidden')) fetchCart();
    });
    document.addEventListener('click', (e) => {
      if (!dropdown.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });

    render();
  })();
</script>