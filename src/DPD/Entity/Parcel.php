<?php

namespace FBF\DPD\Entity;

use InvalidArgumentException;

class Parcel
{
    public function __construct(
        public readonly float $weight,
        public readonly int $height,
        public readonly int $width,
        public readonly int $depth,
        public readonly ?string $reference1 = null,
        public readonly ?string $reference2 = null,
        public readonly ?string $reference3 = null,
        public readonly ?string $parcelno = null,
    ) {
        if ($this->weight <= 0) {
            throw new InvalidArgumentException("Parcel: 'weight' must be greater than 0.");
        }
        if ($this->height <= 0 || $this->width <= 0 || $this->depth <= 0) {
            throw new InvalidArgumentException('Parcel: Dimensions must be positive integers.');
        }
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this));
    }
}
