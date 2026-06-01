<?php
session_start();
if (empty($_SESSION['carrito'])) {
    header('Location: index.php');
    exit;
}
include('templates/cabecera.php');
require 'carro/funcionesCarrito.php';
require 'vendor/autoload.php';

$wompi = new capsweb\Services\WompiService();
$publicKey = $wompi->getPublicKey();
$isSandbox = $wompi->isSandbox();

$subtotal = calcularTotal();
$descuento = $_SESSION['descuento_pendiente'] ?? 0;
$total = $subtotal - $descuento;
?>

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <div class="mb-8">
    <a href="finalizar.php" class="text-sm text-gray-500 hover:text-brand-accent transition"><i class="fa-solid fa-arrow-left mr-1"></i>Volver</a>
    <h1 class="text-3xl font-black text-brand-900 mt-2">Pago con tarjeta</h1>
    <p class="text-gray-500 mt-1">Paga de forma segura con tu tarjeta de crédito o débito</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Formulario de pago -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <form id="paymentForm" class="space-y-4">
        <input type="hidden" id="acceptanceToken" value="">
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del titular</label>
          <input type="text" id="cardHolder" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent" placeholder="Como aparece en la tarjeta">
        </div>
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Número de tarjeta</label>
          <input type="text" id="cardNumber" required maxlength="19" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent" placeholder="0000 0000 0000 0000">
        </div>
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha expiración</label>
            <div class="flex gap-2">
              <input type="text" id="expMonth" required maxlength="2" placeholder="MM" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent text-center">
              <span class="self-center text-gray-400">/</span>
              <input type="text" id="expYear" required maxlength="2" placeholder="YY" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent text-center">
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">CVC</label>
            <input type="text" id="cvc" required maxlength="4" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent" placeholder="123">
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Cuotas</label>
          <select id="installments" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 outline-none focus:border-brand-accent bg-white">
            <option value="1">1 cuota</option>
            <option value="3">3 cuotas</option>
            <option value="6">6 cuotas</option>
            <option value="12">12 cuotas</option>
          </select>
        </div>

        <div class="flex items-start gap-2 mt-4">
          <input type="checkbox" id="acceptTerms" required class="mt-1">
          <label for="acceptTerms" class="text-xs text-gray-600">
            Acepto los <a href="#" class="text-brand-accent">términos y condiciones</a> y autorizo el procesamiento de mi pago.
          </label>
        </div>

        <button type="submit" id="payButton" class="w-full bg-brand-accent text-white py-3.5 rounded-xl font-bold text-lg hover:bg-rose-700 transition shadow-lg shadow-rose-900/20 mt-6">
          <i class="fa-solid fa-lock mr-2"></i>Pagar $<?= number_format($total, 0, ',', '.') ?>
        </button>
        
        <div class="flex items-center justify-center gap-2 mt-4 text-gray-400 text-xs">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Pago seguro con Wompi</span>
          <?php if ($isSandbox): ?>
            <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-[10px]">MODO PRUEBAS</span>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- Resumen -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
      <h2 class="font-bold text-lg text-brand-900 mb-4">Resumen</h2>
      <div class="space-y-3">
        <?php foreach ($_SESSION['carrito'] as $item): ?>
          <div class="flex justify-between text-sm">
            <span class="text-gray-600"><?= htmlspecialchars($item['referencia']) ?> x<?= $item['cantidad'] ?></span>
            <span class="font-semibold">$<?= number_format($item['precio'] * $item['cantidad'], 0, ',', '.') ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="border-t border-gray-100 my-4"></div>
      <div class="space-y-2 text-sm">
        <div class="flex justify-between">
          <span class="text-gray-600">Subtotal</span>
          <span class="font-semibold">$<?= number_format($subtotal, 0, ',', '.') ?></span>
        </div>
        <?php if ($descuento > 0): ?>
          <div class="flex justify-between text-green-600">
            <span>Descuento</span>
            <span class="font-semibold">-$<?= number_format($descuento, 0, ',', '.') ?></span>
          </div>
        <?php endif; ?>
      </div>
      <div class="border-t border-gray-100 my-3"></div>
      <div class="flex justify-between items-center">
        <span class="font-bold text-brand-900">Total a pagar</span>
        <span class="text-2xl font-black text-brand-accent">$<?= number_format($total, 0, ',', '.') ?> <span class="text-sm text-gray-400 font-normal">COP</span></span>
      </div>
    </div>
  </div>
</section>

<script src="https://checkout.wompi.co/widget.js"></script>
<script>
const total = <?= $total ?>;
const publicKey = '<?= $publicKey ?>';
const isSandbox = <?= $isSandbox ? 'true' : 'false' ?>;

// Obtener acceptance token al cargar
fetch('/api/wompi_acceptance_token.php')
  .then(r => r.json())
  .then(data => {
    if (data.token) {
      document.getElementById('acceptanceToken').value = data.token;
    }
  });

document.getElementById('paymentForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('payButton');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Procesando...';
  
  try {
    // Tokenizar tarjeta
    const cardData = {
      number: document.getElementById('cardNumber').value.replace(/\s/g, ''),
      cvc: document.getElementById('cvc').value,
      exp_month: document.getElementById('expMonth').value,
      exp_year: '20' + document.getElementById('expYear').value,
      holder: document.getElementById('cardHolder').value
    };
    
    const tokenRes = await fetch('/api/wompi_tokenizar.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(cardData)
    });
    const tokenData = await tokenRes.json();
    
    if (!tokenData.token) {
      throw new Error('Error al tokenizar tarjeta');
    }
    
    // Crear transacción
    const transactionRes = await fetch('/api/wompi_transaccion.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        amount: total,
        token: tokenData.token,
        installments: document.getElementById('installments').value,
        acceptance_token: document.getElementById('acceptanceToken').value
      })
    });
    const transData = await transactionRes.json();
    
    if (transData.success) {
      window.location.href = '/pedido_confirmado.php?id=' + transData.pedido_id;
    } else {
      throw new Error(transData.message || 'Error en el pago');
    }
    
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-lock mr-2"></i>Pagar $' + total.toLocaleString('es-CO');
    alert(err.message || 'Error al procesar el pago');
  }
});

// Formato número de tarjeta
document.getElementById('cardNumber').addEventListener('input', function(e) {
  let val = e.target.value.replace(/\D/g, '');
  val = val.substring(0, 16);
  e.target.value = val.replace(/(\d{4})(?=\d)/g, '$1 ');
});
</script>

<?php include('templates/footer.php'); ?>
