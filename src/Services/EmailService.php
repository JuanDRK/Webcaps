<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../Helpers/Paths.php';


function sendEmailStock()
{
    $config = parse_ini_file(config_path('config.ini'));
    $conexion = new PDO(
        $config['dns'],
        $config['usuario'],
        $config['clave'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    );

    $sql = $conexion->prepare("SELECT `referencia`,`stock` FROM `productos` WHERE productos.stock <= :stock");
    $sql->execute(array(':stock' => 5));

    if ($datos = $sql->fetch(PDO::FETCH_ASSOC)) {
        $phpmailer = buildMailer();
        $phpmailer->Subject = 'Productos con stock menor o igual a cinco';
        $phpmailer->Body = "El/los producto/s con referencia --" . $datos['referencia'] . "," . ", -- tienen casi agotado su stock, menor o igual a 5 se suguiere aumentar el stock, ya que no seran mostrados en la pagina para su compra ";
        $phpmailer->isHTML(true);

        if ($phpmailer->send() == false) {
            echo "No se envio  ";
            echo $phpmailer->ErrorInfo;
        } else {
            echo "se envio";
        };
    }
}


function sendEmailPedido($id)
{
    $pedido = new capsweb\pedidos;
    $info_pedido = $pedido->mostrarPorId($id);
    $info_detalle_pedido = $pedido->mostrarDetallePorIdPedido($id);

    $count = count($info_detalle_pedido);

    if ($count > 0) {
        $c = 0;
        for ($x = 0; $x < $count; $x++) {
            $c++;
            $item = $info_detalle_pedido[$x];
            $total = $item['precio'] * $item['cantidad'];

            $phpmailer = buildMailer();
            $phpmailer->Subject = 'NUEVO PEDIDO/COMPRA!';
            $phpmailer->isHTML(true);
            $phpmailer->Body =  "Informacion de la Compra \n ". $info_pedido['id'] ."\n"."El cliente ".
                "Nombre: " . $info_pedido['nombre'] . " " . $info_pedido['apellidos'] ."\n". "con correo".
                "Correo: " . $info_pedido['email'] . "\n" ."realizo un nuevo pedido en la ".
                "Fecha: " . $info_pedido['fecha'] . "\n" ;
                
            if ($phpmailer->send() == false) {
                echo "No se envio  ";
                echo $phpmailer->ErrorInfo;
            } else {
                echo "se envio";
            };
        }
    }
}

function envValue($key, $default = '')
{
    $value = getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
}

function buildMailer()
{
    loadEnvFile(project_path('.env'));

    $phpmailer = new PHPMailer();
    $phpmailer->isSMTP();
    $phpmailer->Host = envValue('SMTP_HOST', 'localhost');
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = (int) envValue('SMTP_PORT', 2525);
    $phpmailer->Username = envValue('SMTP_USERNAME');
    $phpmailer->Password = envValue('SMTP_PASSWORD');
    $phpmailer->setFrom(
        envValue('SMTP_FROM_EMAIL', 'admin@example.com'),
        envValue('SMTP_FROM_NAME', 'Webcaps')
    );
    $phpmailer->addAddress(
        envValue('SMTP_TO_EMAIL', 'alerts@example.com'),
        envValue('SMTP_TO_NAME', 'Admin')
    );

    return $phpmailer;
}

function loadEnvFile($path)
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '#') === 0) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if ($key === '') {
            continue;
        }

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
