<?php

namespace App\Service\BinDictionary;

interface BinDictionaryServiceInterface
{
    public function getCountryCode(string $bin): string;
}
