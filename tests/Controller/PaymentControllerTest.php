<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentControllerTest extends WebTestCase
{
    public function testProcessPaymentSuccess()
    {
        $client = static::createClient();
        $client->request('POST', '/api/payment/process', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'gateway' => 'shift4',
            'amount' => 150,
            'currency' => 'USD'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertSame('success', $responseData['status']);
    }

    public function testProcessPaymentWithInvalidData()
    {
        $client = static::createClient();
        $client->request('POST', '/api/payment/process', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'gateway' => '',
            'amount' => -100,
        ]));

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertSame('error', $responseData['status']);
    }
}