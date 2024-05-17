<?php

namespace App\Service\Cache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class CountryCache
{
    private string $cacheFile;
    private Filesystem $filesystem;

    public function __construct(string $cacheDir = '/app/var/cache')
    {
        $this->filesystem = new Filesystem();
        $this->cacheFile = rtrim($cacheDir, '/') . '/country_cache.json';
    }

    public function getAreas(): array
    {
        if ($this->filesystem->exists($this->cacheFile)) {
            try {
                $content = file_get_contents($this->cacheFile);
                $areas = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Invalid JSON in cache file');
                }

                return $areas;
            } catch (\Exception $e) {
                return $this->recreateCacheFile();
            }
        }

        return $this->recreateCacheFile();
    }

    public function saveAreas(array $areas): void
    {
        try {
            $this->filesystem->dumpFile($this->cacheFile, json_encode($areas));
        } catch (IOExceptionInterface $exception) {
            throw new \RuntimeException(sprintf('Unable to write to cache file: %s', $exception->getMessage()));
        }
    }

    public function addCountryToArea(string $countryCode, string $area): void
    {
        $areas = $this->getAreas();
        if (!isset($areas[$area])) {
            $areas[$area] = [];
        }
        if (!in_array($countryCode, $areas[$area], true)) {
            $areas[$area][] = $countryCode;
            $this->saveAreas($areas);
        }
    }

    public function clearCache(): void
    {
        if ($this->filesystem->exists($this->cacheFile)) {
            $this->filesystem->remove($this->cacheFile);
        }
    }

    private function recreateCacheFile(): array
    {
        $areas = [
            'EU' => [
                'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR',
                'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL',
                'PT', 'RO', 'SE', 'SI', 'SK'
            ],
            'NON_EU' => []
        ];
        $this->saveAreas($areas);
        return $areas;
    }
}
