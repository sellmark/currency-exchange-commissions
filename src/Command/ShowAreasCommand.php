<?php

namespace App\Command;

use App\Service\Cache\CountryCache;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:show-areas')]
class ShowAreasCommand extends Command
{
    public function __construct(private readonly CountryCache $cache)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Shows the defined areas and their countries.');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $areas = $this->cache->getAreas();

        foreach ($areas as $area => $countries) {
            $output->writeln(sprintf('%s: %s', $area, implode(', ', $countries)));
        }

        return Command::SUCCESS;
    }
}
