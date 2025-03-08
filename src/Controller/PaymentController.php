<?php

namespace App\Controller;

use App\Service\PaymentProcessor;
use App\Exception\InvalidGatewayException;
use App\Exception\PaymentProcessingException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Psr\Log\LoggerInterface;

#[Route('/api/payment')]
class PaymentController extends AbstractController
{
    public function __construct(
        private PaymentProcessor $paymentProcessor,
        private ValidatorInterface $validator,
        private LoggerInterface $logger
    ) {}

    #[Route('/process', name: 'process_payment', methods: ['POST'])]
    public function processPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate user input
        $errors = $this->validateInput($data);
        if (!empty($errors)) {
            return $this->json(['status' => 'error', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }


        try {
            $response = $this->paymentProcessor->processPayment(
                gatewayName: $data['gateway'],
                amount: (float) $data['amount'],
                currency: strtoupper($data['currency'] ?? 'USD'),
                cardDetails: $this->getCardDetails($data)
            );

            return $this->json([
                'status' => 'success',
                'message' => 'Payment processed successfully',
                'data' => $response
            ], JsonResponse::HTTP_OK);
        } catch (InvalidGatewayException | PaymentProcessingException $e) {
            $this->logger->error('Payment processing failed', ['error' => $e->getMessage()]);

            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate user input before processing payment.
     *
     * @param array $data
     * @return array|null
     */
    private function validateInput(array $data): ?array
    {
        $constraints = new Assert\Collection([
            'gateway' => new Assert\NotBlank(),
            'amount' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'numeric', 'message' => 'Amount must be a number']),
                new Assert\Positive(),
            ],
            'currency' => new Assert\Optional([
                new Assert\Length(['min' => 3, 'max' => 3]),
                new Assert\Regex('/^[A-Z]{3}$/')
            ]),
            'cardNumber' => new Assert\Optional([
                new Assert\Length(['min' => 13, 'max' => 19]),
                new Assert\Regex('/^\d+$/', 'Card number must be numeric')
            ]),
            'cardExpYear' => new Assert\Optional([
                new Assert\Regex('/^\d{4}$/', 'Invalid expiration year format')
            ]),
            'cardExpMonth' => new Assert\Optional([
                new Assert\Regex('/^\d{2}$/', 'Invalid expiration month format'),
                new Assert\Range(['min' => 1, 'max' => 12])
            ]),
            'cardCvv' => new Assert\Optional([
                new Assert\Regex('/^\d{3,4}$/', 'Invalid CVV format')
            ]),
        ]);

        $violations = $this->validator->validate($data, $constraints);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $errors;
        }
        return null;
    }

    /**
     * Extract card details from user input.
     *
     * @param array $data
     * @return array
     */
    private function getCardDetails(array $data): array
    {
        return array_filter([
            'number' => $data['cardNumber'] ?? null,
            'expYear' => $data['cardExpYear'] ?? null,
            'expMonth' => $data['cardExpMonth'] ?? null,
            'cvc' => $data['cardCvv'] ?? null,
        ]);
    }
}