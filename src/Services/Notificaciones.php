<?php
namespace capsweb\Services;

class Notificaciones
{
    private string $adminEmail;
    private string $tiendaNombre;

    public function __construct()
    {
        $this->adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'admin@caps.com';
        $this->tiendaNombre = 'CAPS CULTURE';
    }

    public function notificarNuevoPedido(array $pedido, array $cliente, array $items): bool
    {
        $subject = "Nuevo pedido #{$pedido['id']} - {$this->tiendaNombre}";
        
        $productosHtml = '';
        foreach ($items as $item) {
            $total = $item['precio'] * $item['cantidad'];
            $productosHtml .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>{$item['referencia']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>{$item['talla']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>{$item['cantidad']}</td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>$" . number_format($total, 0, ',', '.') . "</td>
            </tr>";
        }

        $html = "
        <html>
        <head><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333;}</style></head>
        <body style='max-width:600px;margin:0 auto;padding:20px;'>
            <h2 style='color:#e11d48;'>🛍️ Nuevo Pedido Recibido</h2>
            <p><strong>ID:</strong> #{$pedido['id']}</p>
            <p><strong>Cliente:</strong> {$cliente['nombre']} {$cliente['apellidos']}</p>
            <p><strong>Email:</strong> {$cliente['email']}</p>
            <p><strong>Teléfono:</strong> {$cliente['telefono']}</p>
            <p><strong>Dirección:</strong> {$cliente['direccion']}</p>
            
            <h3 style='margin-top:30px;'>Productos</h3>
            <table style='width:100%;border-collapse:collapse;'>
                <thead>
                    <tr style='background:#f5f5f5;'>
                        <th style='padding:8px;text-align:left;'>Producto</th>
                        <th style='padding:8px;'>Talla</th>
                        <th style='padding:8px;'>Cant</th>
                        <th style='padding:8px;text-align:right;'>Total</th>
                    </tr>
                </thead>
                <tbody>{$productosHtml}</tbody>
            </table>
            
            <p style='margin-top:20px;font-size:18px;text-align:right;'>
                <strong>Total: $" . number_format($pedido['total'], 0, ',', '.') . "</strong>
            </p>
            
            <p style='margin-top:30px;font-size:12px;color:#999;'>
                Este es un mensaje automático del sistema de {$this->tiendaNombre}.
            </p>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: {$this->tiendaNombre} <noreply@caps.com>\r\n";

        return mail($this->adminEmail, $subject, $html, $headers);
    }

    public function notificarClientePedido(array $pedido, array $cliente): bool
    {
        $subject = "Tu pedido #{$pedido['id']} ha sido recibido - {$this->tiendaNombre}";
        
        $html = "
        <html>
        <head><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333;}</style></head>
        <body style='max-width:600px;margin:0 auto;padding:20px;'>
            <h2 style='color:#e11d48;'>¡Gracias por tu compra!</h2>
            <p>Hola {$cliente['nombre']},</p>
            <p>Hemos recibido tu pedido <strong>#{$pedido['id']}</strong> correctamente.</p>
            <p><strong>Total:</strong> $" . number_format($pedido['total'], 0, ',', '.') . "</p>
            <p>Te contactaremos pronto para coordinar la entrega.</p>
            <p style='margin-top:30px;'>Saludos,<br>Equipo {$this->tiendaNombre}</p>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: {$this->tiendaNombre} <noreply@caps.com>\r\n";

        return mail($cliente['email'], $subject, $html, $headers);
    }
}
