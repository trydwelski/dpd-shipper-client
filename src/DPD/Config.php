<?php

namespace FBF\DPD;

use FBF\DPD\Enum\Environment;
use FBF\DPD\Enum\Language;

class Config
{
    public function __construct(
        public string $clientKey,
        public string $email,
        public ?string $endpoint = null,
        public Environment $environment = Environment::Production,
        public Language $language = Language::EN,
        public string $apiVersion = '2.0'
    ) {}

    public function getEndpoint(): string
    {
        return $this->endpoint ?? ($this->environment === Environment::Sandbox
            ? 'https://capi.dpd.sk/shipment/json'
            : 'https://api.dpd.sk/shipment/json');
    }
}
