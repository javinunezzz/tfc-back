<?php

namespace App\Http\Controllers;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalHttp\HttpRequest;

class PayPalClient
{
    /**
     * Cliente HTTP de PayPal para realizar peticiones.
     *
     * @var PayPalHttpClient
     */
    private $client;

    /**
     * Constructor.
     * Inicializa el cliente de PayPal en entorno Sandbox usando credenciales de entorno.
     */
    public function __construct()
    {
        $environment = new SandboxEnvironment(
            env('PAYPAL_CLIENT_ID'),
            env('PAYPAL_CLIENT_SECRET')
        );
        $this->client = new PayPalHttpClient($environment);
    }

    /**
     * Cancela una suscripción de PayPal dado su ID.
     *
     * @param string $subscriptionId ID de la suscripción a cancelar.
     * @return array Resultado de la operación con:
     *               - 'success' (bool): indica si la cancelación fue exitosa.
     *               - 'status' (int): código HTTP de la respuesta o del error.
     *               - 'data' (mixed): datos decodificados de la respuesta (solo si éxito).
     *               - 'error' (string): mensaje de error (solo si fallo).
     */
    public function cancelSubscription($subscriptionId)
    {
        try {
            $request = new HttpRequest(
                "/v1/billing/subscriptions/{$subscriptionId}/cancel",
                "POST"
            );
            $request->headers["Content-Type"] = "application/json";
            $request->headers["Accept"] = "application/json";

            $response = $this->client->execute($request);

            return [
                'success' => true,
                'status' => $response->statusCode,
                'data' => json_decode($response->result)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => $e->getCode()
            ];
        }
    }
}
