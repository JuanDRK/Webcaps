<?php
/**
 * Seeder de datos de prueba para Webcaps
 * Uso: php database/seeder.php
 * Requiere: composer install (fakerphp/faker)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use capsweb\Helpers\Database;

function seed(): void
{
    $db = Database::getConnection();

    // Datos realistas sin Faker (funciona inmediatamente)
    $categorias = [
        'Camiseta', 'Hoodie', 'Gorra', 'Jean', 'Jogger', 'Chaqueta', 'Básica', 'Pantaloneta', 'Sudadera', 'Overshirt'
    ];

    $nombresProducto = [
        'Camiseta Oversize Negra', 'Hoodie Caps Beige', 'Gorra Trucker Vintage', 'Jean Skinny Azul',
        'Jogger Gris Clásico', 'Chaqueta Bomber Negra', 'Camiseta Básica Blanca', 'Pantaloneta Deportiva',
        'Sudadera NASA Reflectiva', 'Overshirt Cuadros', 'Camiseta Feid Tour', 'Hoodie Oversized Gris',
        'Gorra Dad Hat', 'Jean Ancho Negro', 'Jogger Cargo Verde', 'Chaqueta Denim',
        'Camiseta Tie-Dye', 'Sudadera Essential', 'Camiseta Edición Limitada', 'Hoodie Zip-Up Negro'
    ];

    $tallas = ['S,M,L,XL', 'S,M,L', 'M,L,XL', '30,32,34', 'Única'];

    $imagenes = [
        'https://picsum.photos/seed/caps1/400/600',
        'https://picsum.photos/seed/caps2/400/600',
        'https://picsum.photos/seed/caps3/400/600',
        'https://picsum.photos/seed/caps4/400/600',
        'https://picsum.photos/seed/caps5/400/600',
    ];

    // Insertar categorías si no existen
    $stmt = $db->prepare("INSERT IGNORE INTO categorias (categoria) VALUES (?)");
    foreach ($categorias as $cat) {
        $stmt->execute([$cat]);
    }
    echo "Categorías insertadas.\n";

    // Obtener IDs de categorías
    $catIds = $db->query("SELECT id FROM categorias ORDER BY id ASC")->fetchAll(PDO::FETCH_COLUMN);

    // Insertar productos
    $stmt = $db->prepare("INSERT INTO productos (referencia, categoria_id, foto, tallas, precio, stock, estado, fecha, descripcion) VALUES (?,?,?,?,?,?,?,?,?)");
    $fecha = date('Y-m-d');

    for ($i = 0; $i < 20; $i++) {
        $nombre = $nombresProducto[$i];
        $catId = $catIds[array_rand($catIds)];
        $foto = $imagenes[$i % count($imagenes)];
        $talla = $tallas[array_rand($tallas)];
        $precio = rand(30, 150) * 1000;
        $stock = rand(5, 50);
        $estado = rand(0, 1);
        $desc = 'Producto de alta calidad. Edición exclusiva CAPS.';
        $stmt->execute([$nombre, $catId, $foto, $talla, $precio, $stock, $estado, $fecha, $desc]);
    }
    echo "20 productos insertados.\n";

    // Insertar noticias
    $noticias = ['Envío gratis en compras +$150.000', 'Nuevo drop disponible', '2x1 en básicas este mes'];
    $stmt = $db->prepare("INSERT INTO noticias (lema) VALUES (?)");
    foreach ($noticias as $n) {
        $stmt->execute([$n]);
    }
    echo "Noticias insertadas.\n";

    // Insertar pedidos de ejemplo
    $clientesNombres = ['Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Sofía', 'Pedro', 'Laura'];
    $apellidos = ['García', 'Rodríguez', 'Martínez', 'López', 'González', 'Pérez', 'Sánchez', 'Ramírez'];

    for ($i = 0; $i < 10; $i++) {
        $nombre = $clientesNombres[array_rand($clientesNombres)];
        $apellido = $apellidos[array_rand($apellidos)];
        $email = strtolower($nombre . '.' . $apellido . '@email.com');
        $tel = rand(3000000000, 3209999999);
        $dir = 'Calle ' . rand(1, 100) . ' # ' . rand(1, 50) . '-' . rand(1, 20);

        $db->beginTransaction();
        $stmtCliente = $db->prepare("INSERT INTO clientes (nombre, apellidos, email, telefono, direccion, pedido_id) VALUES (?,?,?,?,?,0)");
        $stmtCliente->execute([$nombre, $apellido, $email, $tel, $dir]);
        $clienteId = (int) $db->lastInsertId();

        $total = rand(50, 300) * 1000;
        $fechaPedido = date('Y-m-d', strtotime("-" . rand(0, 30) . " days"));
        $stmtPedido = $db->prepare("INSERT INTO pedidos (cliente_id, total, fecha, estado) VALUES (?,?,?,0)");
        $stmtPedido->execute([$clienteId, $total, $fechaPedido]);
        $pedidoId = (int) $db->lastInsertId();

        // Actualizar pedido_id del cliente
        $db->prepare("UPDATE clientes SET pedido_id = ? WHERE id = ?")->execute([$pedidoId, $clienteId]);

        // Detalle del pedido (1-3 productos)
        $productos = $db->query("SELECT id, precio FROM productos ORDER BY RAND() LIMIT " . rand(1, 3))->fetchAll();
        $stmtDetalle = $db->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, precio, talla, cantidad) VALUES (?,?,?,?,?)");
        foreach ($productos as $prod) {
            $talla = ['S','M','L','XL'][array_rand(['S','M','L','XL'])];
            $stmtDetalle->execute([$pedidoId, $prod['id'], $prod['precio'], $talla, rand(1, 3)]);
        }

        $db->commit();
    }
    echo "10 pedidos de ejemplo insertados.\n";
    echo "Seeder completado exitosamente.\n";
}

try {
    seed();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
