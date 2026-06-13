<?php
namespace capsweb\Services;

require_once __DIR__ . '/../Helpers/Paths.php';

/**
 * Servicio básico para integración con Wompi (Colombia)
 * Requiere: WOMPI_PUBLIC_KEY y WOMPI_PRIVATE_KEY en config.ini
 */
class WompiService
{
    private string $publicKey;
    private string $privateKey;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $config = parse_ini_file(config_path('config.ini'));
        $this->publicKey = $config['wompi_public_key'] ?? '';
        $this->privateKey = $config['wompi_private_key'] ?? '';
        $this->sandbox = ($config['wompi_sandbox'] ?? 'true') === 'true';
        $this->baseUrl = $this->sandbox 
            ? 'https://sandbox.wompi.co/v1' 
            : 'https://production.wompi.co/v1';
    }

    /**
     * Crear una transacción con tarjeta de crédito
     */
    public function crearTransaccion(array $datos): array
    {
        $payload = [
            'acceptance_token' => $datos['acceptance_token'],
            'amount_in_cents' => $datos['amount'] * 100, // Wompi usa centavos
            'currency' => 'COP',
            'customer_email' => $datos['email'],
            'payment_method' => [
                'type' => 'CARD',
                'token' => $datos['card_token'],
                'installments' => $datos['installments'] ?? 1,
            ],
            'reference' => $datos['reference'],
            'customer_data' => [
                'phone_number' => $datos['phone'],
                'full_name' => $datos['name'],
            ],
        ];

        $ch = curl_init("{$this->baseUrl}/transactions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->privateKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? ['error' => 'Error en comunicación'];
    }

    /**
     * Obtener token de aceptación (términos y condiciones)
     */
    public function obtenerAcceptanceToken(): ?string
    {
        $ch = curl_init("{$this->baseUrl}/merchants/{$this->publicKey}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['data']['presigned_acceptance']['acceptance_token'] ?? null;
    }

    /**
     * Crear token de tarjeta (tokenización)
     */
    public function tokenizarTarjeta(array $cardData): ?string
    {
        $payload = [
            'number' => $cardData['number'],
            'cvc' => $cardData['cvc'],
            'exp_month' => $cardData['exp_month'],
            'exp_year' => $cardData['exp_year'],
            'card_holder' => $cardData['holder'],
        ];

        $ch = curl_init("{$this->baseUrl}/tokens/cards");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->publicKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['data']['id'] ?? null;
    }

    /**
     * Verificar estado de transacción
     */
    public function verificarTransaccion(string $transactionId): array
    {
        $ch = curl_init("{$this->baseUrl}/transactions/{$transactionId}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->privateKey,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
