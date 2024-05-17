<?php

namespace App\Service;

use App\DTO\CSVRecord;
use App\Enum\Area;
use App\Service\BinDictionary\BinDictionaryServiceInterface;
use App\Service\Cache\CountryCache;
use App\Service\ExchangeRate\ExchangeRateServiceInterface;

readonly class CommissionCalculator
{
    public function __construct(
        public CountryCache $cache,
        public ExchangeRateServiceInterface  $exchangeRateService,
        public BinDictionaryServiceInterface $binLookupService
    ) {
    }

    public function calculateCommissions(string $inputFile): array
    {
        $results = [];
        $handle = fopen($inputFile, 'r');
        if ($handle === false) {
            $this->cache->clearCache();
            throw new \RuntimeException('Unable to open input file');
        }

        while (($line = fgets($handle)) !== false) {
            $record = json_decode($line, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException("Invalid JSON in ROW: $line");
            }

            $csvRecord = new CSVRecord($record['bin'], (float)$record['amount'], $record['currency']);

            $rate = $this->exchangeRateService->getExchangeRate($csvRecord->currency);
            $amountInEur = $csvRecord->currency === 'EUR' ? $csvRecord->amount : $csvRecord->amount / $rate;

            $countryCode = $this->binLookupService->getCountryCode($csvRecord->bin);

            $area = Area::fromCountryCode($countryCode);
            $this->cache->addCountryToArea($countryCode, $area->value);

            $commissionRate = $area->getCommissionRate();

            $commission = ceil($amountInEur * $commissionRate * 100) / 100;
            $results[] = $commission;
        }

        fclose($handle);

        return $results;
    }
}