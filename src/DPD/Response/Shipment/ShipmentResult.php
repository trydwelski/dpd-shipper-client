<?php

namespace FBF\DPD\Response\Shipment;

class ShipmentResult
{
    public function __construct(
        public readonly string $reference,
        public readonly string $ackCode,
        public readonly array $messages,
        public readonly string $mpsid,
        public readonly string $label,
        public readonly bool $success,
    ) {}
}
