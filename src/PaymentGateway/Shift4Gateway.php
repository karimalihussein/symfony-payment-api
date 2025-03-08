<?php

namespace App\PaymentGateway;

use App\DTO\UnifiedResponse;
use App\Exception\PaymentProcessingException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\{ClientExceptionInterface, ServerExceptionInterface, TransportExceptionInterface};
use Psr\Log\LoggerInterface;

final class Shift4Gateway implements PaymentGatewayInterface
{
    private const DEFAULT_CURRENCY = 'USD';

    // Default test card details
    private const DEFAULT_CARD = [
        'number' => '4242424242424242',
        'expMonth' => '12',
        'expYear' => '2025',
        'cvc' => '123',
    ];

    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private string $baseUrl,
        private string $apiKey
    ) {}

    public function getGatewayName(): string
    {
        return 'shift4';
    }

    /**
     * Process a payment through Shift4.
     *
     * @param float $amount The amount to process.
     * @param string $currency The currency for the transaction.
     * @param array $cardDetails Optional card details (overrides default test card).
     * @return UnifiedResponse
     * @throws PaymentProcessingException
     */
    public function processPayment(float $amount, string $currency = self::DEFAULT_CURRENCY, array $cardDetails = []): UnifiedResponse
    {
        $card = !empty($cardDetails) ? $cardDetails : self::DEFAULT_CARD;
        $payload = $this->buildPayload($amount, $currency, $card);

        try {
            $data = $this->sendRequest($payload);

            return new UnifiedResponse(
                transactionId: $data['id'] ?? 'N/A',
                createdAt: new \DateTimeImmutable(),
                amount: $amount,
                currency: $currency,
                cardBin: substr($card['number'], 0, 6)
            );
        } catch (\Exception $e) {
            $this->logger->error('Shift4 Payment Failed', [
                'error' => $e->getMessage(),
                'gateway' => $this->getGatewayName(),
                'amount' => $amount,
                'currency' => $currency,
                'payload' => $payload
            ]);

            throw new PaymentProcessingException('Shift4 payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Build the request payload.
     *
     * @param float $amount
     * @param string $currency
     * @param array $card
     * @return array
     */
    private function buildPayload(float $amount, string $currency, array $card): array
    {
        return [
            'amount' => (int) ($amount * 100), // Convert to cents
            'currency' => strtoupper($currency),
            'card' => $card,
            'description' => 'Payment processed via Shift4',
        ];
    }

    /**
     * Send the request to the Shift4 API.
     *
     * @param array $payload
     * @return array
     * @throws PaymentProcessingException
     */
    private function sendRequest(array $payload): array
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/charges', [
                'auth_basic' => [$this->apiKey, ''],
                'json' => $payload,
            ]);

            return $response->toArray();
        } catch (ClientExceptionInterface $e) {
            throw new PaymentProcessingException('Client Error: ' . $e->getMessage());
        } catch (ServerExceptionInterface $e) {
            throw new PaymentProcessingException('Server Error: ' . $e->getMessage());
        } catch (TransportExceptionInterface $e) {
            throw new PaymentProcessingException('Network Error: ' . $e->getMessage());
        }
    }
}