<?php

namespace App\Service\BinDictionary;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinLookupService implements BinDictionaryServiceInterface
{
    private HttpClientInterface $client;
    private string $mockServiceUrl;

    public function __construct(HttpClientInterface $client, string $mockServiceUrl)
    {
        $this->client = $client;
        $this->mockServiceUrl = $mockServiceUrl;
    }

    public function getCountryCode(string $bin): string
    {
        $binResults = $this->client->request('GET', $this->mockServiceUrl . '?bin=' . $bin)->toArray();

//        print_r($binResults);
        return $binResults['country']['alpha2'];
    }
}
