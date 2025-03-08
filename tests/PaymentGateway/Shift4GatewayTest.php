<?php

namespace App\Tests\PaymentGateway;

use App\PaymentGateway\Shift4Gateway;
use App\Exception\PaymentProcessingException;
use App\DTO\UnifiedResponse;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Psr\Log\LoggerInterface;

class Shift4GatewayTest extends KernelTestCase
{
    private Shift4Gateway $shift4Gateway;
    private $mockHttpClient;
    private $mockLogger;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);

        $this->shift4Gateway = new Shift4Gateway(
            $this->mockHttpClient,
            $this->mockLogger,
            'https://api.shift4.com',
            'test_api_key'
        );
    }

    public function testProcessPaymentSuccess()
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn([
            'id' => 'txn_123456',
            'currency' => 'USD'
        ]);

        $this->mockHttpClient->method('request')->willReturn($mockResponse);

        $response = $this->shift4Gateway->processPayment(100, 'USD');

        $this->assertInstanceOf(UnifiedResponse::class, $response);
        $this->assertSame('txn_123456', $response->transactionId);
    }
}