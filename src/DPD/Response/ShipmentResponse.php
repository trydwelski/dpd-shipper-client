<?php

namespace FBF\DPD\Response;

use FBF\DPD\Response\Shipment\ShipmentResult;

class ShipmentResponse
{
    public array $results = [];

    public bool $success;

    public function __construct(array $response)
    {
        $this->success = (bool) $response['success'];
        foreach ($response['data']['result']['result'] as $shipment) {
            $this->results[] = new ShipmentResult(...$shipment);
        }
    }

    public function first(): ?ShipmentResult
    {
        return $this->results ? reset($this->results) : null;
    }
}
