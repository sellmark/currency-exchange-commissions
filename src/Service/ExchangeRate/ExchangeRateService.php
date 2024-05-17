<?php

namespace App\Service\ExchangeRate;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    private int $failedFetches = 0;
    private const string CACHE_KEY = 'exchange_rates';
    private const int CACHE_TTL = 60;

    public function __construct(private readonly HttpClientInterface $client, private readonly string $exchangeRateApiUrl, private readonly CacheInterface $cache, private readonly LoggerInterface $logger)
    {
    }

    #[\Override]
    public function getExchangeRate(string $currency): float
    {
        try {
            $rates = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) {
                $item->expiresAfter(self::CACHE_TTL);
                $response = $this->client->request('GET', $this->exchangeRateApiUrl)->toArray();
                $rates = $response['rates'] ?? null;

                if ($rates === null) {
                    throw new \RuntimeException('Rates key not found in the API response');
                }

                $this->failedFetches = 0;
                return $rates;
            });
        } catch (\Exception $e) {
            $this->failedFetches++;
            $this->logger->error('Failed to fetch exchange rates', ['exception' => $e, 'attempts' => $this->failedFetches]);

            if ($this->failedFetches >= 5) {
                $this->logger->critical('Failed to fetch exchange rates 5 times consecutively.');
            }

            //For sake of this task only it is kept like this. Usually we wouldn't make a transaction if exchangeRate
            // is outdated too much.
            $rates = $this->cache->get(self::CACHE_KEY, fn(ItemInterface $item) => [
                'USD' => 1.1497,
                'JPY' => 129.53,
                'GBP' => 0.8586
            ]);
        }

        return $rates[$currency] ?? 1;
    }
}
