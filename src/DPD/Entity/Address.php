<?php

namespace FBF\DPD\Entity;

use FBF\DPD\Util\CountryCodeMapper;
use InvalidArgumentException;

class Address
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $type = null,
        public readonly ?string $name = null,
        public readonly ?string $street = null,
        public readonly ?string $houseNumber = null,
        public readonly ?string $zip = null,
        public readonly ?string $country = null,
        public readonly ?string $city = null,
        public readonly ?string $phone = null,
        public readonly ?string $email = null
    ) {
        if (! $this->id && (! $this->name || ! $this->street || ! $this->zip || ! $this->city || ! $this->country)) {
            throw new InvalidArgumentException("Address: Either 'id' or all fields must be provided.");
        }

        if ($this->zip && ! preg_match('/^\d{3,10}$/', $this->zip)) {
            throw new InvalidArgumentException("Address: 'zip' must be numeric.");
        }
    }

    public function toArray(): array
    {
        $data = array_filter(get_object_vars($this));

        if (! empty($data['country']) && strlen($data['country']) === 2) {
            $mapped = CountryCodeMapper::map($data['country']);
            if ($mapped) {
                $data['country'] = $mapped;
            }
        }

        return $data;
    }
}
