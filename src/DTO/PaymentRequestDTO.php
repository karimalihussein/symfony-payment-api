<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['aci', 'shift4'], message: 'Invalid gateway')]
    public string $gateway;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric', message: 'Amount must be a number')]
    #[Assert\Positive(message: 'Amount must be positive')]
    public float $amount;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(min: 3, max: 3, exactMessage: 'Currency must be a 3-letter code')]
    #[Assert\Regex(pattern: '/^[A-Z]{3}$/', message: 'Invalid currency format')]
    public ?string $currency = 'USD';

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(min: 13, max: 19, exactMessage: 'Invalid card number length')]
    #[Assert\Regex(pattern: '/^\d+$/', message: 'Card number must be numeric')]
    public ?string $cardNumber;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Regex(pattern: '/^\d{4}$/', message: 'Invalid expiration year format')]
    public ?string $cardExpYear;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Regex(pattern: '/^\d{2}$/', message: 'Invalid expiration month format')]
    #[Assert\Range(min: 1, max: 12, notInRangeMessage: 'Expiration month must be between 1 and 12')]
    public ?string $cardExpMonth;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Regex(pattern: '/^\d{3,4}$/', message: 'Invalid CVV format')]
    public ?string $cardCvv;

    public function __construct(array $data)
    {
        $this->gateway = $data['gateway'] ?? '';
        $this->amount = (float) ($data['amount'] ?? 0);
        $this->currency = strtoupper($data['currency'] ?? 'USD');
        $this->cardNumber = $data['cardNumber'] ?? null;
        $this->cardExpYear = $data['cardExpYear'] ?? null;
        $this->cardExpMonth = $data['cardExpMonth'] ?? null;
        $this->cardCvv = $data['cardCvv'] ?? null;
    }

    public function getCardDetails(): array
    {
        return array_filter([
            'number' => $this->cardNumber,
            'expYear' => $this->cardExpYear,
            'expMonth' => $this->cardExpMonth,
            'cvc' => $this->cardCvv,
        ]);
    }
}