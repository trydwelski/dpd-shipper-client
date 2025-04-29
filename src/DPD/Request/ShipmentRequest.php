<?php

namespace FBF\DPD\Request;

use FBF\DPD\Entity\Address;
use FBF\DPD\Entity\COD;
use FBF\DPD\Entity\Parcel;
use FBF\DPD\Entity\Pickup;

class ShipmentRequest
{
    public function __construct(
        public string $reference,
        public string $delisId,
        public int $product,
        public Address $addressSender,
        public Address $addressRecipient,
        public array $parcels,
        public ?COD $cod = null,
        public ?Pickup $pickup = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'reference' => $this->reference,
            'delisId' => $this->delisId,
            'product' => $this->product,
            'pickup' => $this->pickup?->toArray(),
            'addressSender' => $this->addressSender?->toArray(),
            'addressRecipient' => $this->addressRecipient?->toArray(),
            'parcels' => ['parcel' => array_values(array_map(fn (Parcel $p) => $p->toArray(), $this->parcels))],
        ];

        if ($this->cod) {
            $data['services'] = ['cod' => $this->cod->toArray()];
        }

        return $data;
    }
}
