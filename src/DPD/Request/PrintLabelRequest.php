<?php

namespace FBF\DPD\Request;

class PrintLabelRequest
{
    public function __construct(
        public readonly array $parcels,
        public readonly string $pageSize = 'A6',
        public readonly int $position = 1
    ) {}

    public function toArray(): array
    {
        $parcels = [];
        foreach ($this->parcels as $parcel) {
            $parcels[]['parcelno'] = $parcel;
        }

        return [
            'parcels' => [
                'parcel' => $parcels,
            ],
            'pageSize' => $this->pageSize,
            'position' => $this->position,
        ];
    }
}
