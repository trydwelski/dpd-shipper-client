<?php

namespace FBF\DPD\Entity;

use InvalidArgumentException;

class Pickup
{
    public function __construct(
        public readonly string $date,
        public readonly string $beginning,
        public readonly string $end
    ) {
        if (! preg_match('/^\d{8}$/', $this->date)) {
            throw new InvalidArgumentException("Pickup: 'date' must be in YYYYMMDD format.");
        }
        if (! preg_match('/^\d{4}$/', $this->beginning) || ! preg_match('/^\d{4}$/', $this->end)) {
            throw new InvalidArgumentException("Pickup: 'beginning' and 'end' must be in HHMM format.");
        }
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'timeWindow' => [
                'beginning' => $this->beginning,
                'end' => $this->end,
            ],
        ];
    }
}
