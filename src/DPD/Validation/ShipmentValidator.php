<?php

namespace FBF\DPD\Validation;

use FBF\DPD\Exception\ValidationException;
use FBF\DPD\Request\ShipmentRequest;

class ShipmentValidator
{
    public function validate(ShipmentRequest $request): void
    {
        if (empty($request->reference)) {
            throw new ValidationException('Reference cannot be empty.');
        }

        if (! preg_match('/^[A-Z0-9]{4,}$/i', $request->delisId)) {
            throw new ValidationException('Invalid DelisID.');
        }

        if (empty($request->parcels)) {
            throw new ValidationException('Shipment must contain at least one parcel.');
        }

        foreach ($request->parcels as $parcel) {
            if ($parcel->weight <= 0) {
                throw new ValidationException('Parcel weight must be greater than 0.');
            }
        }
    }
}
