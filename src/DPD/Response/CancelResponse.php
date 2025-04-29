<?php

namespace FBF\DPD\Response;

class CancelResponse
{
    public readonly bool $success;

    public readonly array $data;

    public readonly ?array $error;

    public function __construct(array $response)
    {
        $this->success = $response['success'] ?? false;
        $this->data = $response['data'] ?? [];
        $this->error = $response['error'] ?? null;
    }
}
