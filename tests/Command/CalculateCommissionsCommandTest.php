<?php

namespace App\Tests\Command;

use App\Command\CalculateCommissionsCommand;
use App\Command\AddCountryCommand;
use App\Service\CommissionCalculator;
use App\Service\ExchangeRate\ExchangeRateService;
use App\Service\BinDictionary\BinLookupService;
use App\Service\Cache\CountryCache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CalculateCommissionsCommandTest extends TestCase
{
    public function testExecuteWithEUCommissionRate(): void
    {
        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method('get')->willReturnMap([
            ['mock_service_url', 'http://mock/mock_binlist.php'],
            ['exchange_rate_api_url', 'https://api.exchangeratesapi.io/latest']
        ]);

        $binLookupService = $this->createMock(BinLookupService::class);
        $binLookupService->method('getCountryCode')->willReturn('DE'); // Return 'DE' for any BIN

        $exchangeRateService = $this->createMock(ExchangeRateService::class);
        $exchangeRateService->method('getExchangeRate')->willReturnMap([
            ['USD', 1.1497],
            ['JPY', 129.53],
            ['EUR', 1.00],
            ['GBP', 0.8586]
        ]);

        $cache = $this->createMock(CountryCache::class);
        $cache->method('getAreas')->willReturn([
            'EU' => ['DE']
        ]);
        $cache->expects($this->never())
            ->method('clearCache');

        $calculator = new CommissionCalculator($cache, $exchangeRateService, $binLookupService);

        $application = new Application();
        $application->add(new AddCountryCommand($cache));
        $application->add(new CalculateCommissionsCommand($calculator));

        $commandTesterAdd = new CommandTester($application->find('app:add-country'));
        $commandTesterAdd->execute([
            'countryCode' => 'DE',
            'area' => 'EU'
        ]);

        $commandTesterCalculate = new CommandTester($application->find('app:calculate-commissions'));
        $commandTesterCalculate->execute([
            'inputFile' => __DIR__ . '/../../data/input.txt'
        ]);

        $output = $commandTesterCalculate->getDisplay();
        $expectedOutput = "1\n0.44\n0.78\n1.14\n23.3\n";
        $this->assertSame($expectedOutput, $output);
    }

    public function testExecuteWithNonEUCommissionRate(): void
    {
        $parameterBag = $this->createMock(ParameterBagInterface::class);
        $parameterBag->method('get')->willReturnMap([
            ['mock_service_url', 'http://mock/mock_binlist.php'],
            ['exchange_rate_api_url', 'https://api.exchangeratesapi.io/latest']
        ]);

        $binLookupService = $this->createMock(BinLookupService::class);
        $binLookupService->method('getCountryCode')->willReturn('US'); // Return 'US' for any BIN

        $exchangeRateService = $this->createMock(ExchangeRateService::class);
        $exchangeRateService->method('getExchangeRate')->willReturnMap([
            ['USD', 1.1497],
            ['JPY', 129.53],
            ['EUR', 1.00],
            ['GBP', 0.8586]
        ]);

        $cache = $this->createMock(CountryCache::class);
        $cache->method('getAreas')->willReturn([
            'EU' => ['DE']
        ]);
        $cache->expects($this->never())
            ->method('clearCache');

        $calculator = new CommissionCalculator($cache, $exchangeRateService, $binLookupService);

        $application = new Application();
        $application->add(new AddCountryCommand($cache));
        $application->add(new CalculateCommissionsCommand($calculator));

        $commandTesterCalculate = new CommandTester($application->find('app:calculate-commissions'));
        $commandTesterCalculate->execute([
            'inputFile' => __DIR__ . '/../../data/input.txt'
        ]);

        $output = $commandTesterCalculate->getDisplay();
        $expectedOutput = "2\n0.87\n1.55\n2.27\n46.59\n"; // Update this according to your commission calculation
        $this->assertSame($expectedOutput, $output);
    }
}