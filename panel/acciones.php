<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Helpers/Paths.php';

// Lazy instantiation para evitar errores al cargar la pagina
$producto = null;
$categoria = null;
$noticia = null;

function getProducto() { global $producto; if (!$producto) $producto = new capsweb\Productos; return $producto; }
function getCategoria() { global $categoria; if (!$categoria) $categoria = new capsweb\Categorias; return $categoria; }
function getNoticia() { global $noticia; if (!$noticia) $noticia = new capsweb\noticias; return $noticia; }

// Si acceden por GET sin accion=Eliminar, redirigir al login
if ($_SERVER['REQUEST_METHOD'] === 'GET' && (!isset($_GET['accion']) || $_GET['accion'] !== 'Eliminar')) {
    header('Location: ../acceso.php');
    exit;
}

function validarCsrf(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Login tiene su propia protección de sesión, no requiere CSRF previo
  if (isset($_POST['accion']) && $_POST['accion'] === 'Conectar') {
    // Se procesa abajo en la sección de login
  } elseif (!validarCsrf()) {
    http_response_code(403);
    header('Location: ../acceso.php?error=denegado');
    exit;
  }

  if ($_POST['accion'] === 'Registrar') {

    if (empty($_POST['referencia']))
      exit('Completar referencia');

    if (empty($_POST['precio']))
      exit('Completar precio');

    if (empty($_POST['categoria_id']))
      exit('Seleccionar una Categoria');

    if (empty($_POST['tallas']))
      exit('Seleccionar una talla al menos');
    else {
      $tallasSeleccionadas = implode(',', $_POST['tallas']);
    }

    if (empty($_POST['stock']))
      exit('digita una cantidad en stock válida');

    try {
      $fotoSubida = subirFoto();
    } catch (\RuntimeException $e) {
      exit($e->getMessage());
    }

    $_params = array(
      'referencia' => $_POST['referencia'],
      'categoria_id' => $_POST['categoria_id'],
      'foto' => $fotoSubida,
      'precio' => $_POST['precio'],
      'tallas' => $tallasSeleccionadas,
      'stock' => $_POST['stock'],
      'fecha' => date('y-m-d')
    );

    $rpt = getProducto()->registrar($_params);

    if ($rpt)
      header('Location: productos/index.php');
    else
      print 'Error al registrar el producto';
  }

  if ($_POST['accion'] === 'Actualizar') {

    if (empty($_POST['referencia']))
      exit('Completar referencia');

    if (empty($_POST['categoria_id']))
      exit('Seleccionar una Categoria');

    if (empty($_POST['precio']))
      exit('Completar precio');

    if (empty($_POST['stock']))
      exit('digita una Cantidad válida');

    /**if (empty($_POST['tallasNuevas']))
      exit('Seleccionar una talla al menos');
    else {
      $tallasNuevas = implode(',', $_POST['tallasNuevas']);
    }**/
    $_params = array(
      'id' => $_POST['id'],
      'referencia' => $_POST['referencia'],
      'categoria_id' => $_POST['categoria_id'],
      'precio' => $_POST['precio'],
      'stock' => $_POST['stock'],
      'tallas' => isset($_POST['tallasNuevas']) ? implode(',', explode(',', $_POST['tallasNuevas'])) : '',
      'fecha' => date('Y-m-d')
    );

    if (!empty($_POST['foto_temp']))
      $_params['foto'] = $_POST['foto_temp'];

    if (!empty($_FILES['foto']['name'])) {
      try {
        $_params['foto'] = subirFoto();
      } catch (\RuntimeException $e) {
        exit($e->getMessage());
      }
    }

    $rpt = getProducto()->actualizar($_params);
    if ($rpt)
      header('Location: productos/index.php');
    else
      print 'Error al actualizar el producto';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'Eliminar') {
  $id = $_GET['id'];
  $rpt = getProducto()->eliminar($id);
  if ($rpt)
    header('Location: productos/index.php');
  else
    echo 'Error al eliminar el producto';
}

//REGISTRAR / AÑADIR UNA CATEGORIA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if ($_POST['accion'] === 'Añadir') {

    if (empty($_POST['categoria']))
      exit('Completar categoria');

    $_params = array(
      'categoria' => $_POST['categoria']
    );

    $rpt = getCategoria()->registrar($_params);

    if ($rpt)
      header('Location: productos/index.php');
    else
      print 'Error al añadir la categoria';
  }
}

// CAMBIAR LAS NOTICIAS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if ($_POST['accion'] === 'Cambiar') {

    if (empty($_POST['lema']))
      exit('Completar la noticia');

    $_params = array(
      'lema' => $_POST['lema']
    );

    $rpt = getNoticia()->actualizarNoticias($_params);

    if ($rpt)
      header('Location: productos/index.php');
    else
      print 'Error al cambiar la noticia';
  }
}

// //SUBIR UNA FOTO
function subirFoto()
{
  if (!isset($_FILES['foto'])) {
    throw new RuntimeException('No se recibio ningun archivo.');
  }

  $nombreOriginal = $_FILES['foto']['name'];
  $temporal = $_FILES['foto']['tmp_name'];
  $error = $_FILES['foto']['error'];
  $tamano = $_FILES['foto']['size'];

  // Compatibilidad con input tipo multiple: foto[]
  if (is_array($nombreOriginal)) {
    $nombreOriginal = $nombreOriginal[0] ?? '';
    $temporal = $temporal[0] ?? '';
    $error = $error[0] ?? UPLOAD_ERR_NO_FILE;
    $tamano = $tamano[0] ?? 0;
  }

  if ($error !== UPLOAD_ERR_OK) {
    throw new RuntimeException('Error al subir la imagen.');
  }

  if (!is_uploaded_file($temporal)) {
    throw new RuntimeException('Archivo no valido.');
  }

  $maxBytes = 2 * 1024 * 1024; // 2MB
  if ($tamano <= 0 || $tamano > $maxBytes) {
    throw new RuntimeException('La imagen supera el tamano permitido (2MB).');
  }

  $mime = mime_content_type($temporal);
  $mimesPermitidos = array(
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
  );

  if (!isset($mimesPermitidos[$mime])) {
    throw new RuntimeException('Formato de imagen no permitido.');
  }

  $extension = $mimesPermitidos[$mime];
  $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $extension;

  $carpeta = storage_upload_path();
  if (!is_dir($carpeta)) {
    if (!mkdir($carpeta, 0755, true) && !is_dir($carpeta)) {
      throw new RuntimeException('No se pudo crear la carpeta de subida.');
    }
  }

  $destino = $carpeta . $nombreSeguro;
  if (!move_uploaded_file($temporal, $destino)) {
    throw new RuntimeException('No se pudo guardar la imagen.');
  }

  return $nombreSeguro;
}

//INICIO DE SECCION
if (isset($_POST['accion']) && $_POST['accion'] === 'Conectar') {
  session_start();

  $usuario = isset($_POST['usuario']) ? trim((string) $_POST['usuario']) : '';
  $contrasena = isset($_POST['contraseña']) ? (string) $_POST['contraseña'] : '';

  if ($usuario === '' || $contrasena === '') {
    header('Location: ../acceso.php?error=vacio');
    exit;
  }

  if (strlen($usuario) > 100 || strlen($contrasena) > 255) {
    header('Location: ../acceso.php?error=denegado');
    exit;
  }

  $config = parse_ini_file(config_path('config.ini'));
  $cn = new \PDO($config['dns'], $config['usuario'], $config['clave'], array(
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
  ));

  $sql = $cn->prepare("SELECT * FROM administradores WHERE usuario = :usuario LIMIT 1");
  $sql->bindValue(':usuario', $usuario, \PDO::PARAM_STR);
  $sql->execute();
  $datos = $sql->fetchObject();

  if ($datos && validarContrasenaAdmin($contrasena, $datos->contraseña)) {
    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;
    $_SESSION["id"] = $datos->id;
    $_SESSION["nombre"] = $datos->nombre;
    $_SESSION["email"] = $datos->correo;
    $_SESSION["numero"] = $datos->telefono;

    header('Location: ' . rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/productos/index.php');
    exit;
  }

  header('Location: ../acceso.php?error=denegado');
  exit;
}

function validarContrasenaAdmin($contrasenaIngresada, $contrasenaGuardada)
{
  $infoHash = password_get_info($contrasenaGuardada);
  if (!empty($infoHash['algo'])) {
    return password_verify($contrasenaIngresada, $contrasenaGuardada);
  }

  // Compatibilidad temporal con contrasenas antiguas en texto plano.
  return hash_equals((string) $contrasenaGuardada, (string) $contrasenaIngresada);
}
