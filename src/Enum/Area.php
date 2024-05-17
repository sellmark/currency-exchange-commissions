<?php

namespace App\Enum;

enum Area: string
{
    case EU = 'EU';
    case NON_EU = 'NON_EU';

    public static function isEuropean(string $countryCode): bool
    {
        $euCountries = [
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR',
            'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL',
            'PT', 'RO', 'SE', 'SI', 'SK',
        ];

        return in_array($countryCode, $euCountries, true);
    }

    public function getCommissionRate(): float
    {
        return match ($this) {
            self::EU => 0.01,
            self::NON_EU => 0.02,
        };
    }

    public static function fromCountryCode(string $countryCode): self
    {
        return self::isEuropean($countryCode) ? self::EU : self::NON_EU;
    }
}
