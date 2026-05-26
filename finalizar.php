<?php
session_start();
include('templates/cabecera.php');
require 'carro/funcionesCarrito.php';
require 'vendor/autoload.php';

if (empty($_SESSION['carrito'])) {
  header('Location: index.php');
  exit;
}
?>

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <div class="mb-8">
    <a href="carro/index.php" class="text-sm text-gray-500 hover:text-brand-accent transition"><i class="fa-solid fa-arrow-left mr-1"></i>Volver al carrito</a>
    <h1 class="text-3xl font-black text-brand-900 mt-2">Finalizar compra</h1>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
    <!-- Formulario -->
    <div class="lg:col-span-3">
      <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
        <h2 class="font-bold text-lg text-brand-900 mb-6">Datos de envío</h2>
        <form id="checkout-form" action="completar_pedido.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre</label>
              <input type="text" name="nombre" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Apellidos</label>
              <input type="text" name="apellidos" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Correo electrónico</label>
              <input type="email" name="email" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono</label>
              <input type="tel" name="telefono" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition">
            </div>
          </div>

          <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Dirección completa</label>
            <textarea name="direccion" rows="3" required placeholder="Barrio, calle, número de casa, apartamento, puntos de referencia..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition"></textarea>
          </div>

          <button type="button" onclick="openConfirmModal()" class="w-full bg-brand-accent text-white py-3.5 rounded-xl font-bold text-lg hover:bg-rose-700 transition shadow-lg shadow-rose-900/20">
            <i class="fa-solid fa-check mr-2"></i>Confirmar pedido
          </button>
          <p class="text-center text-xs text-gray-400 mt-3"><i class="fa-solid fa-shield-halved mr-1"></i>Pago contra entrega. Sin tarjetas.</p>
        </form>
      </div>
    </div>

    <!-- Resumen -->
    <div class="lg:col-span-2">
      <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
        <h2 class="font-bold text-lg text-brand-900 mb-4">Tu pedido</h2>
        <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
          <?php foreach ($_SESSION['carrito'] as $value) {
            $fotoUrl = '/upload/' . htmlspecialchars($value['foto'], ENT_QUOTES, 'UTF-8');
          ?>
            <div class="flex gap-3 items-center">
              <div class="w-14 h-14 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                <img src="<?= $fotoUrl ?>" class="w-full h-full object-cover" alt="">
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-brand-900 text-sm truncate"><?= htmlspecialchars($value['referencia'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-xs text-gray-500"><?= (int)$value['cantidad'] ?> x $<?= number_format($value['precio'], 0, ',', '.') ?></p>
              </div>
              <span class="font-bold text-sm">$<?= number_format($value['precio'] * $value['cantidad'], 0, ',', '.') ?></span>
            </div>
          <?php } ?>
        </div>
        <div class="border-t border-gray-100 my-4"></div>
        <div class="flex justify-between items-center">
          <span class="font-bold text-brand-900">Total</span>
          <span class="text-2xl font-black text-brand-accent">$<?= number_format(calcularTotal(), 0, ',', '.') ?> <span class="text-sm text-gray-400 font-normal">COP</span></span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Confirm Modal -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeConfirmModal()">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl text-center">
      <div class="w-16 h-16 rounded-full bg-green-50 text-green-500 flex items-center justify-center mx-auto mb-5">
        <i class="fa-solid fa-check text-2xl"></i>
      </div>
      <h3 class="text-xl font-black text-brand-900 mb-2">¿Confirmar tu pedido?</h3>
      <p class="text-gray-500 text-sm mb-8">Revisa que tus datos estén correctos antes de continuar.</p>
      <div class="flex gap-3 justify-center">
        <button onclick="closeConfirmModal()" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition">Cancelar</button>
        <button onclick="submitOrder()" class="px-6 py-2.5 rounded-xl bg-brand-accent text-white font-bold hover:bg-rose-700 transition shadow-lg shadow-rose-900/20">Confirmar</button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-8 shadow-2xl text-center">
      <div class="w-16 h-16 rounded-full bg-green-50 text-green-500 flex items-center justify-center mx-auto mb-5">
        <i class="fa-solid fa-check text-2xl"></i>
      </div>
      <h3 class="text-xl font-black text-brand-900 mb-2">¡Pedido realizado!</h3>
      <p class="text-gray-500 text-sm mb-6">Gracias por tu compra. Te contactaremos pronto.</p>
      <button onclick="document.getElementById('checkout-form').submit()" class="w-full px-6 py-2.5 rounded-xl bg-brand-accent text-white font-bold hover:bg-rose-700 transition">OK</button>
    </div>
  </div>
</div>

<script>
function openConfirmModal() {
  document.getElementById('confirmModal').classList.remove('hidden');
}
function closeConfirmModal() {
  document.getElementById('confirmModal').classList.add('hidden');
}
function submitOrder() {
  closeConfirmModal();
  document.getElementById('successModal').classList.remove('hidden');
}
</script>

<?php include('templates/footer.php'); ?>