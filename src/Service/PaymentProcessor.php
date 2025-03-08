<?php

namespace App\Service;

use App\PaymentGateway\PaymentGatewayInterface;
use App\Exception\InvalidGatewayException;
use App\Exception\PaymentProcessingException;
use Psr\Log\LoggerInterface;

class PaymentProcessor
{
    /** @var array<string, PaymentGatewayInterface> */
    private array $gateways = [];

    public function __construct(iterable $gateways, private LoggerInterface $logger)
    {
        foreach ($gateways as $gateway) {
            $this->gateways[$gateway->getGatewayName()] = $gateway;
        }
    }

    /**
     * Process a payment through a specified gateway.
     *
     * @param string $gatewayName The name of the payment gateway.
     * @param float $amount The payment amount.
     * @param string $currency The currency of the payment.
     * @param array $cardDetails (Optional) Card details for processing payment.
     * @return array The processed payment response.
     * @throws InvalidGatewayException If the specified gateway is not supported.
     * @throws PaymentProcessingException If payment processing fails.
     */
    public function processPayment(string $gatewayName, float $amount, string $currency = 'USD', array $cardDetails = []): array
    {
        if (!isset($this->gateways[$gatewayName])) {
            throw new InvalidGatewayException("Gateway '$gatewayName' is not supported.");
        }

        $this->logger->info("Processing payment", [
            'gateway' => $gatewayName,
            'amount' => $amount,
            'currency' => $currency
        ]);

        try {
            $response = $this->gateways[$gatewayName]->processPayment($amount, $currency, $cardDetails);
            return $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error("Payment failed", ['error' => $e->getMessage()]);
            throw new PaymentProcessingException("Payment failed: " . $e->getMessage());
        }
    }
}