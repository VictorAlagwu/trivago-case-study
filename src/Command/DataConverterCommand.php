<?php

namespace App\Command;

use App\Service\ConverterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DataConverterCommand extends Command
{
    protected static $defaultName = 'trivago:convert';

    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
    }
    
    protected function configure()
    {
        $this->setDescription('A CLI data converter')
            ->setHelp('Add the file location')
            ->addArgument('location', InputArgument::REQUIRED, 'Enter the file location');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->success([
            ' CLI Data Converter',
            '--converts any datafile to csv'
        ]);

        $io->success('Start converting..' . $input->getArgument('location'));

        $converterService = new ConverterService($this->parameterBag);
        $result = $converterService->getFile($input->getArgument('location'));

        $output->writeln('Result:' . $result);

        return Command::SUCCESS;
    }
}
