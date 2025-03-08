<?php

namespace App\PaymentGateway;

use App\DTO\UnifiedResponse;
use App\Exception\PaymentProcessingException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\{ClientExceptionInterface, ServerExceptionInterface, TransportExceptionInterface};
use Psr\Log\LoggerInterface;

final class ACIGateway implements PaymentGatewayInterface
{
    private const DEFAULT_CURRENCY = 'EUR';
    private const BASE_URL = 'https://eu-test.oppwa.com/v1';
    private const TEST_CARD = [
        'number' => '4111111111111111',
        'holder' => 'Test User',
        'expMonth' => '12',
        'expYear' => '2025',
        'cvc' => '123',
    ];

    public function __construct(
        private HttpClientInterface $client,
        private LoggerInterface $logger,
        private string $entityId,
        private string $apiKey
    ) {}

    public function getGatewayName(): string
    {
        return 'aci';
    }

    /**
     * Process a payment through ACI.
     *
     * @param float $amount The amount to process.
     * @param string $currency The currency for the transaction.
     * @param array $cardDetails Optional card details.
     * @return UnifiedResp  onse
     * @throws PaymentProcessingException
     */
    public function processPayment(float $amount, string $currency = self::DEFAULT_CURRENCY, array $cardDetails = []): UnifiedResponse
    {
        $card = !empty($cardDetails) ? $cardDetails : self::TEST_CARD;
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
            $this->logger->error('ACI Payment Failed', [
                'error' => $e->getMessage(),
                'gateway' => $this->getGatewayName(),
                'amount' => $amount,
                'currency' => $currency,
                'payload' => $payload
            ]);

            throw new PaymentProcessingException('ACI payment failed: ' . $e->getMessage());
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
            'entityId' => $this->entityId,
            'amount' => number_format($amount, 2, '.', ''),
            'currency' => strtoupper($currency),
            'paymentBrand' => 'VISA',
            'paymentType' => 'DB',
            'card.number' => $card['number'],
            'card.holder' => 'Jane Jones',
            'card.expiryMonth' => $card['expMonth'],
            'card.expiryYear' => $card['expYear'],
            'card.cvv' => $card['cvc'],
        ];
    }

    /**
     * Send the request to the ACI API.
     *
     * @param array $payload
     * @return array
     * @throws PaymentProcessingException
     */
    private function sendRequest(array $payload): array
    {
        try {
            $response = $this->client->request('POST', self::BASE_URL . '/payments', [
                'headers' => [
                    'Authorization' => 'Bearer ' . trim($this->apiKey),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => $payload,
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