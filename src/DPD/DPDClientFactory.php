<?php

namespace FBF\DPD;

use Psr\Log\LoggerInterface;

class DPDClientFactory
{
    public static function create(Config $config, ?LoggerInterface $logger = null): ShipmentClient
    {
        return new ShipmentClient(
            clientKey: $config->clientKey,
            email: $config->email,
            endpoint: $config->getEndpoint(),
            logger: $logger
        );
    }
}
