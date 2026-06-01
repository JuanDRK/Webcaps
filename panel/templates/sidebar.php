<?php
// $activePage debe estar definida antes de incluir este archivo
// valores: 'productos', 'pedidos', 'fabrica'
?>
<aside class="w-64 bg-brand-900 text-white flex-shrink-0 flex flex-col sticky top-0 h-screen no-print">
  <div class="p-6">
    <a href="/index.php" class="font-black text-xl tracking-wider hover:opacity-80 transition block">CAPS <span class="text-brand-accent">ADMIN</span></a>
  </div>

  <nav class="flex-1 px-4 space-y-1">
    <a href="/panel/productos/index.php" class="<?= ($activePage ?? '') === 'productos' ? 'bg-brand-accent text-white' : 'text-white/70 hover:text-white hover:bg-white/10' ?> flex items-center gap-3 px-4 py-3 rounded-lg transition">
      <i class="fa-solid fa-box w-5 text-center"></i>
      <span class="font-medium">Productos</span>
    </a>
    <a href="/panel/pedidos/index.php" class="<?= ($activePage ?? '') === 'pedidos' ? 'bg-brand-accent text-white' : 'text-white/70 hover:text-white hover:bg-white/10' ?> flex items-center gap-3 px-4 py-3 rounded-lg transition">
      <i class="fa-solid fa-shopping-bag w-5 text-center"></i>
      <span class="font-medium">Pedidos</span>
    </a>
    <a href="/panel/fabrica/fabrica.php" class="<?= ($activePage ?? '') === 'fabrica' ? 'bg-brand-accent text-white' : 'text-white/70 hover:text-white hover:bg-white/10' ?> flex items-center gap-3 px-4 py-3 rounded-lg transition">
      <i class="fa-solid fa-industry w-5 text-center"></i>
      <span class="font-medium">Fábrica</span>
    </a>
    <a href="/panel/cupones/index.php" class="<?= ($activePage ?? '') === 'cupones' ? 'bg-brand-accent text-white' : 'text-white/70 hover:text-white hover:bg-white/10' ?> flex items-center gap-3 px-4 py-3 rounded-lg transition">
      <i class="fa-solid fa-ticket w-5 text-center"></i>
      <span class="font-medium">Cupones</span>
    </a>
  </nav>

  <div class="px-4 pb-2">
    <a href="/index.php" target="_blank" class="flex items-center gap-3 px-4 py-3 text-white/60 hover:text-white hover:bg-white/10 transition rounded-lg">
      <i class="fa-solid fa-store w-5 text-center"></i>
      <span class="font-medium">Ver tienda</span>
    </a>
  </div>

  <div class="p-4 border-t border-white/10">
    <a href="/cerrarSeccion.php" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:text-red-300 transition rounded-lg">
      <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
      <span class="font-medium">Salir</span>
    </a>
  </div>
</aside>
<?php unset($activePage); ?>
