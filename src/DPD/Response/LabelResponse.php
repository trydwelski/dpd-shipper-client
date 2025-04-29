<?php

namespace FBF\DPD\Response;

class LabelResponse
{
    public readonly bool $success;

    public readonly array $data;

    public function __construct(array $response)
    {
        $this->success = $response['success'] ?? false;
        $this->data = $response['data'] ?? '';
    }
}
