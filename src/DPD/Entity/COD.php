<?php

namespace FBF\DPD\Entity;

use InvalidArgumentException;

class COD
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $variableSymbol,
        public readonly int $paymentMethod,
        public readonly int $bankAccountId
    ) {
        if ($this->amount <= 0) {
            throw new InvalidArgumentException('COD amount must be greater than 0.');
        }

        if (empty($this->currency)) {
            throw new InvalidArgumentException('COD currency is required.');
        }

        if (! preg_match('/^\d{1,10}$/', $this->variableSymbol)) {
            throw new InvalidArgumentException('COD variableSymbol must be a numeric string of 1 to 10 digits.');
        }

        if (! in_array($this->paymentMethod, [0, 1], true)) {
            throw new InvalidArgumentException('COD paymentMethod must be 0 (cash) or 1 (card).');
        }

        if ($this->bankAccountId <= 0) {
            throw new InvalidArgumentException('COD bankAccountId must be a positive integer.');
        }
    }

    public function toArray(): array
    {
        return [
            'amount' => number_format($this->amount, 2, '.', ''),
            'currency' => $this->currency,
            'variableSymbol' => $this->variableSymbol,
            'paymentMethod' => $this->paymentMethod,
            'bankAccount' => ['id' => $this->bankAccountId],
        ];
    }
}
