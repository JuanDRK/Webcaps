<?php
ini_set('session.use_only_cookies', 1);
ini_set('session.use_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_trans_sid', 0);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

session_start();
if (!empty($_SESSION["id"])) {
    header("location: panel/productos/");
    exit;
}

// Generar token CSRF para el formulario de login
$csrfToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

include('templates/cabecera.php');

$error = $_GET['error'] ?? '';
?>

<section class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
  <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
    <!-- Branding -->
    <div class="bg-brand-900 p-10 flex flex-col justify-center items-center text-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <img src="assets/images/carrusel/6.jpg" class="w-full h-full object-cover" alt="">
      </div>
      <div class="relative z-10">
        <img src="assets/images/logo caps circulo.png" alt="logo" class="h-20 w-20 rounded-full object-cover mx-auto mb-4">
        <h2 class="text-2xl font-black text-white tracking-wider">CAPS CULTURE</h2>
        <p class="text-gray-400 text-sm mt-2">Panel de administración</p>
        <div class="mt-6 text-xs text-gray-500 uppercase tracking-widest">Streetwear con identidad</div>
      </div>
    </div>

    <!-- Formulario -->
    <div class="p-10 flex flex-col justify-center">
      <h1 class="text-2xl font-black text-brand-900 mb-1">Bienvenido</h1>
      <p class="text-gray-500 text-sm mb-6">Ingresa tus credenciales de administrador</p>

      <?php if ($error === 'vacio') { ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
          <i class="fa-solid fa-circle-exclamation mr-2"></i>Completa todos los campos.
        </div>
      <?php } elseif ($error === 'denegado') { ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
          <i class="fa-solid fa-circle-exclamation mr-2"></i>Usuario o contraseña incorrectos.
        </div>
      <?php } ?>

      <form method="post" action="panel/acciones.php" class="space-y-4" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Usuario</label>
          <div class="relative">
            <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="usuario" required autofocus autocomplete="username" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition text-sm">
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Contraseña</label>
          <div class="relative">
            <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="password" name="contraseña" required autocomplete="current-password" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl outline-none focus:border-brand-accent focus:ring-1 focus:ring-brand-accent transition text-sm">
          </div>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked class="rounded text-brand-accent focus:ring-brand-accent">
            <span class="text-gray-600">Recordarme</span>
          </label>
        </div>

        <input type="submit" name="accion" value="Conectar" class="w-full bg-brand-accent text-white py-3 rounded-xl font-bold hover:bg-rose-700 transition shadow-lg shadow-rose-900/20 cursor-pointer">
      </form>

      <a href="index.php" class="mt-6 text-center text-sm text-gray-400 hover:text-brand-accent transition">
        <i class="fa-solid fa-arrow-left mr-1"></i>Volver al sitio
      </a>
    </div>
  </div>
</section>

<?php include('templates/footer.php'); ?>
