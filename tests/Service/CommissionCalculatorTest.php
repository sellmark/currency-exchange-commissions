<?php

namespace App\Tests\Service;

use App\Service\BinDictionary\BinLookupService;
use App\Service\Cache\CountryCache;
use App\Service\CommissionCalculator;
use App\Service\ExchangeRate\ExchangeRateService;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    private CommissionCalculator $calculator;
    private CountryCache $cache;
    private ExchangeRateService $exchangeRateService;
    private BinLookupService $binLookupService;

    private const EXCHANGE_RATES = [
        'USD' => 1.1497,
        'JPY' => 129.53,
        'EUR' => 1.00,
        'GBP' => 0.8586,
    ];

    private const EXPECTED_EU_RESULTS = [1.0, 0.44, 0.78, 1.14, 23.3];
    private const EXPECTED_NON_EU_RESULTS = [2.0, 0.87, 1.55, 2.27, 46.59];

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CountryCache::class);
        $this->exchangeRateService = $this->createMock(ExchangeRateService::class);
        $this->binLookupService = $this->createMock(BinLookupService::class);

        $this->calculator = new CommissionCalculator(
            $this->cache,
            $this->exchangeRateService,
            $this->binLookupService
        );
    }

    private function setupMocks(string $countryCode): void
    {
        $this->binLookupService->method('getCountryCode')->willReturn($countryCode);
        $this->exchangeRateService->method('getExchangeRate')->willReturnMap(array_map(
            fn ($rate) => [$rate, self::EXCHANGE_RATES[$rate]],
            array_keys(self::EXCHANGE_RATES)
        ));
    }

    public function testCalculateCommissionsWithEUCommissionRate(): void
    {
        $this->setupMocks('DE');
        $inputFile = __DIR__.'/../../../data/input.txt';

        $results = $this->calculator->calculateCommissions($inputFile);

        $this->assertEquals(self::EXPECTED_EU_RESULTS, $results);
    }

    public function testCalculateCommissionsWithNonEUCommissionRate(): void
    {
        $this->setupMocks('US');
        $inputFile = __DIR__.'/../../../data/input.txt';

        $results = $this->calculator->calculateCommissions($inputFile);

        $this->assertEquals(self::EXPECTED_NON_EU_RESULTS, $results);
    }
}
