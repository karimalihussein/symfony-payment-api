<?php

namespace App\Tests\Service;

use App\Service\PaymentProcessor;
use App\PaymentGateway\PaymentGatewayInterface;
use App\Exception\InvalidGatewayException;
use App\Exception\PaymentProcessingException;
use App\DTO\UnifiedResponse;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PaymentProcessorTest extends TestCase
{
    private PaymentProcessor $paymentProcessor;
    private $mockGateway;
    private $mockLogger;

    protected function setUp(): void
    {
        $this->mockGateway = $this->createMock(PaymentGatewayInterface::class);
        $this->mockGateway->method('getGatewayName')->willReturn('mockGateway');

        $this->mockLogger = $this->createMock(LoggerInterface::class);

        $this->paymentProcessor = new PaymentProcessor([$this->mockGateway], $this->mockLogger);
    }

    public function testProcessPaymentSuccess()
    {
        $mockResponse = new UnifiedResponse(
            transactionId: 'txn_123456',
            createdAt: new \DateTimeImmutable(),
            amount: 100,
            currency: 'USD',
            cardBin: '411111'
        );

        $this->mockGateway->method('processPayment')->willReturn($mockResponse);

        $result = $this->paymentProcessor->processPayment('mockGateway', 100, 'USD');

        $this->assertArrayHasKey('transaction_id', $result);
        $this->assertSame('txn_123456', $result['transaction_id']);
    }

    public function testProcessPaymentWithInvalidGateway()
    {
        $this->expectException(InvalidGatewayException::class);
        $this->paymentProcessor->processPayment('invalidGateway', 100, 'USD');
    }

    public function testProcessPaymentFailure()
    {
        $this->mockGateway->method('processPayment')->willThrowException(new PaymentProcessingException("Payment failed"));

        $this->expectException(PaymentProcessingException::class);
        $this->paymentProcessor->processPayment('mockGateway', 100, 'USD');
    }
}