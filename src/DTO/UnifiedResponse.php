<?php

namespace App\DTO;

use DateTimeInterface;

class UnifiedResponse
{
    public function __construct(
        public readonly string $transactionId,
        public readonly DateTimeInterface $createdAt,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $cardBin
    ) {}

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
            'amount' => $this->amount,
            'currency' => $this->currency,
            'card_bin' => $this->cardBin,
        ];
    }
}