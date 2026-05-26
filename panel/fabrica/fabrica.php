<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Fábrica | CAPS Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>tailwind.config={theme:{extend:{colors:{brand:{900:'#0f0f0f',accent:'#e11d48'}}}}}</script>
  <style>@media print { nav, .no-print { display: none !important; } }</style>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="flex min-h-screen">
<?php $activePage = 'fabrica'; include __DIR__ . '/../templates/sidebar.php'; ?>
<main class="flex-1 p-6 lg:p-8 overflow-y-auto">
  <h1 class="text-2xl font-black text-brand-900 mb-2 text-center uppercase">Calculadora de fábrica</h1>
  <p class="text-gray-500 text-sm text-center mb-8">Primera columna: nombre/color del rollo. Segunda: cantidad de rollos.</p>

  <div class="bg-white rounded-xl border border-gray-100 overflow-x-auto mb-6">
    <table class="w-full text-sm" id="data-table">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Rollo</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Cantidad</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Camisetas</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Precio camisas</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Precio rollos</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot class="bg-gray-50 border-t border-gray-100 font-bold">
        <tr>
          <td class="px-4 py-3">Total</td>
          <td class="px-4 py-3"></td>
          <td class="px-4 py-3" id="total-camisetas">0</td>
          <td class="px-4 py-3" id="total-precio-camisas">0</td>
          <td class="px-4 py-3" id="total-precio-rollos">0</td>
          <td class="px-4 py-3"></td>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="flex justify-center gap-3 no-print">
    <button type="button" id="add-row" class="bg-green-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-green-700 transition"><i class="fa-solid fa-plus mr-1"></i>Agregar fila</button>
    <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-5 py-2 rounded-lg font-semibold hover:bg-gray-700 transition"><i class="fa-solid fa-print mr-1"></i>Imprimir</button>
  </div>
</div>

<script>
  let rowIndex = 0;
  const tbody = document.querySelector('#data-table tbody');

  function formatMoney(n) { return '$' + Number(n).toLocaleString('es-CO'); }

  function calcularTotal() {
    let tc = 0, tpc = 0, tpr = 0;
    document.querySelectorAll('.camisetas').forEach(el => tc += parseInt(el.textContent) || 0);
    document.querySelectorAll('.precio-camisas').forEach(el => tpc += parseInt(el.dataset.raw) || 0);
    document.querySelectorAll('.precio-rollos').forEach(el => tpr += parseInt(el.dataset.raw) || 0);
    document.getElementById('total-camisetas').textContent = tc;
    document.getElementById('total-precio-camisas').textContent = formatMoney(tpc);
    document.getElementById('total-precio-rollos').textContent = formatMoney(tpr);
  }

  function updateRow(input) {
    const row = input.closest('tr');
    const cantidad = parseInt(input.value) || 0;
    const camisetas = cantidad * 20;
    const precioCamisas = camisetas * 35000;
    const precioRollos = cantidad * 50000;
    row.querySelector('.camisetas').textContent = camisetas;
    const pc = row.querySelector('.precio-camisas'); pc.textContent = formatMoney(precioCamisas); pc.dataset.raw = precioCamisas;
    const pr = row.querySelector('.precio-rollos'); pr.textContent = formatMoney(precioRollos); pr.dataset.raw = precioRollos;
    calcularTotal();
  }

  document.getElementById('add-row').addEventListener('click', () => {
    rowIndex++;
    const tr = document.createElement('tr');
    tr.id = 'row' + rowIndex;
    tr.className = 'border-b border-gray-50';
    tr.innerHTML = `
      <td class="px-4 py-2"><input type="text" name="rollo[]" class="border border-gray-200 rounded px-2 py-1 text-sm w-full outline-none focus:border-brand-accent" placeholder="Color"></td>
      <td class="px-4 py-2"><input type="number" name="cantidad[]" class="cantidad border border-gray-200 rounded px-2 py-1 text-sm w-full outline-none focus:border-brand-accent" min="0"></td>
      <td class="px-4 py-2"><span class="camisetas text-gray-600">0</span></td>
      <td class="px-4 py-2"><span class="precio-camisas text-gray-600" data-raw="0">$0</span></td>
      <td class="px-4 py-2"><span class="precio-rollos text-gray-600" data-raw="0">$0</span></td>
      <td class="px-4 py-2"><button type="button" class="delete-row text-red-500 hover:text-red-700 text-sm font-semibold" data-row="${rowIndex}"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    tr.querySelector('.cantidad').addEventListener('input', function() { updateRow(this); });
    tr.querySelector('.delete-row').addEventListener('click', function() {
      document.getElementById('row' + this.dataset.row).remove();
      calcularTotal();
    });
  });
</script>
</main>
</div>
</body>
</html>