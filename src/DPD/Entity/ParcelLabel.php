<?php

namespace FBF\DPD\Entity;

class ParcelLabel
{
    public function __construct(
        public readonly string $parcelno,
        public readonly string $pdfPath,
        public readonly ?string $jpgPath = null
    ) {}
}
