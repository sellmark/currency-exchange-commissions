<?php

namespace App\Command;

use App\Service\CommissionCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:calculate-commissions')]
class CalculateCommissionsCommand extends Command
{
    public function __construct(private readonly CommissionCalculator $calculator)
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->setDescription('Calculates commissions for transactions.')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'Path to the input CSV file');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getArgument('inputFile');
        $results = $this->calculator->calculateCommissions($inputFile);

        foreach ($results as $result) {
            $output->writeln($result);
        }

        return Command::SUCCESS;
    }
}
