<?php

namespace App\DTO;

readonly class CSVRecord
{
    public function __construct(
        public string $bin,
        public float $amount,
        public string $currency
    ) {
    }
}
