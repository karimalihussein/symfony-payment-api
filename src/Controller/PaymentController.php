<?php

namespace App\Controller;

use App\Service\PaymentProcessor;
use App\DTO\PaymentRequestDTO;
use App\Exception\InvalidGatewayException;
use App\Exception\PaymentProcessingException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
        $paymentRequest = new PaymentRequestDTO($data);

        // Validate the DTO
        $errors = $this->validateRequest($paymentRequest);
        if (!empty($errors)) {
            return $this->json(['status' => 'error', 'errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $response = $this->paymentProcessor->processPayment(
                gatewayName: $paymentRequest->gateway,
                amount: $paymentRequest->amount,
                currency: $paymentRequest->currency,
                cardDetails: $paymentRequest->getCardDetails()
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

    private function validateRequest(PaymentRequestDTO $paymentRequest): array
    {
        $violations = $this->validator->validate($paymentRequest);

        if (count($violations) === 0) {
            return [];
        }

        $errors = [];
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}