<?php

namespace App\Service\BinDictionary;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinLookupService implements BinDictionaryServiceInterface
{
    public function __construct(private readonly HttpClientInterface $client, private readonly string $mockServiceUrl)
    {
    }

    #[\Override]
    public function getCountryCode(string $bin): string
    {
        $binResults = $this->client->request('GET', $this->mockServiceUrl.'?bin='.$bin)->toArray();

        return $binResults['country']['alpha2'];
    }
}
