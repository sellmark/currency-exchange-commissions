<?php

namespace App\Command;

use App\Enum\Area;
use App\Service\Cache\CountryCache;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:add-country')]
class AddCountryCommand extends Command
{
    public function __construct(private readonly CountryCache $cache)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Adds a country to an area.')
            ->addArgument('countryCode', InputArgument::REQUIRED, 'Country code')
            ->addArgument('area', InputArgument::REQUIRED, 'Area name (EU or NON_EU)');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countryCode = $input->getArgument('countryCode');
        $area = $input->getArgument('area');

        try {
            $areaEnum = Area::from($area);
        } catch (\ValueError) {
            $output->writeln(sprintf('<error>Invalid area: %s. Must be "EU" or "NON_EU".</error>', $area));
            return Command::INVALID;
        }

        $areas = $this->cache->getAreas();
        if (!array_key_exists($areaEnum->value, $areas)) {
            return Command::INVALID;
        }

        foreach ($areas as $code => $countries) {
            if (in_array($countryCode, array_values($countries))) {
                $output->writeln(sprintf('Country %s already exists in area %s.', $countryCode, $code));
                return Command::FAILURE;
            }
        }

        $this->cache->addCountryToArea($countryCode, $areaEnum);
        $output->writeln(sprintf('Added country %s to area %s.', $countryCode, $areaEnum->value));

        return Command::SUCCESS;
    }
}
