<?php

namespace App\Command;

use App\Service\CommissionCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:show-provisions')]
class ShowProvisionsCommand extends Command
{
    private CommissionCalculator $calculator;

    public function __construct(CommissionCalculator $calculator)
    {
        parent::__construct();
        $this->calculator = $calculator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Shows provisions for countries.')
            ->addArgument('inputFile', InputArgument::REQUIRED, 'Path to the input CSV file');
    }

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
