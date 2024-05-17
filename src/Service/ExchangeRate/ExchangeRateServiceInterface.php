<?php

namespace App\Service\ExchangeRate;

interface ExchangeRateServiceInterface
{
    public function getExchangeRate(string $currency): float;
}
