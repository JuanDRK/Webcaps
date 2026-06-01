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
        <form id="checkout-form" action="procesar_checkout.php" method="post">
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

          <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Dirección completa</label>
            <textarea name="direccion" rows="3" required placeholder="Barrio, calle, número de casa, apartamento, puntos de referencia..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition"></textarea>
          </div>

          <!-- Cupón de descuento -->
          <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">¿Tienes un cupón?</label>
            <div class="flex gap-2">
              <input type="text" id="cuponInput" name="cupon" placeholder="Ingresa tu código" class="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition uppercase">
              <button type="button" onclick="aplicarCupon()" class="bg-gray-800 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-gray-700 transition">Aplicar</button>
            </div>
            <div id="cuponMensaje" class="mt-1 text-sm hidden"></div>
          </div>

          <input type="hidden" id="descuentoValue" name="descuento" value="0">

          <!-- Opciones de pago -->
          <div class="space-y-3 mb-6">
            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
              <input type="radio" name="metodo_pago" value="contraentrega" checked class="w-4 h-4 text-brand-accent">
              <div>
                <p class="font-semibold text-brand-900">Pago contra entrega</p>
                <p class="text-xs text-gray-500">Paga en efectivo cuando recibas tu pedido</p>
              </div>
            </label>
            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
              <input type="radio" name="metodo_pago" value="online" class="w-4 h-4 text-brand-accent">
              <div>
                <p class="font-semibold text-brand-900">Pago con tarjeta</p>
                <p class="text-xs text-gray-500">Paga ahora de forma segura con tu tarjeta</p>
              </div>
            </label>
          </div>

          <button type="submit" class="w-full bg-brand-accent text-white py-3.5 rounded-xl font-bold text-lg hover:bg-rose-700 transition shadow-lg shadow-rose-900/20">
            <i class="fa-solid fa-check mr-2"></i>Continuar
          </button>
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
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600">Subtotal</span>
            <span id="subtotalDisplay" class="font-semibold">$<?= number_format(calcularTotal(), 0, ',', '.') ?></span>
          </div>
          <div id="descuentoRow" class="flex justify-between hidden">
            <span class="text-green-600">Descuento (<span id="descuentoLabel">0%</span>)</span>
            <span id="descuentoDisplay" class="font-semibold text-green-600">-$0</span>
          </div>
        </div>
        <div class="border-t border-gray-100 my-3"></div>
        <div class="flex justify-between items-center">
          <span class="font-bold text-brand-900">Total</span>
          <span class="text-2xl font-black text-brand-accent">$<span id="totalDisplay"><?= number_format(calcularTotal(), 0, ',', '.') ?></span> <span class="text-sm text-gray-400 font-normal">COP</span></span>
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
const subtotal = <?= calcularTotal() ?>;
let descuento = 0;
let cuponAplicado = null;

async function aplicarCupon() {
  const input = document.getElementById('cuponInput');
  const mensaje = document.getElementById('cuponMensaje');
  const codigo = input.value.trim();
  
  if (!codigo) return;
  
  try {
    const res = await fetch(`/api/validar_cupon.php?codigo=${encodeURIComponent(codigo)}&total=${subtotal}`);
    const data = await res.json();
    
    mensaje.classList.remove('hidden', 'text-red-500', 'text-green-600');
    
    if (data.valido) {
      cuponAplicado = data;
      descuento = data.descuento;
      
      // Actualizar UI
      document.getElementById('descuentoValue').value = descuento;
      document.getElementById('descuentoLabel').textContent = data.tipo === 'porcentaje' ? data.valor + '%' : '$' + Number(data.valor).toLocaleString('es-CO');
      document.getElementById('descuentoDisplay').textContent = '-$' + Number(descuento).toLocaleString('es-CO');
      document.getElementById('descuentoRow').classList.remove('hidden');
      document.getElementById('totalDisplay').textContent = Number(subtotal - descuento).toLocaleString('es-CO');
      
      mensaje.textContent = `¡Cupón aplicado! Ahorras $${Number(descuento).toLocaleString('es-CO')}`;
      mensaje.classList.add('text-green-600');
      input.disabled = true;
    } else {
      mensaje.textContent = data.mensaje || 'Cupón inválido';
      mensaje.classList.add('text-red-500');
    }
  } catch (e) {
    mensaje.textContent = 'Error al validar cupón';
    mensaje.classList.remove('hidden');
    mensaje.classList.add('text-red-500');
  }
}

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