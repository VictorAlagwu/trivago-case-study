<?php

namespace App\Command;

use App\Service\Converter\ConverterService;
use App\Service\Converter\Handler\JsonHandler;
use App\Service\Converter\Handler\XmlHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConverterCommand extends Command
{
    protected static $defaultName = 'trivago:convert';

    private ParameterBagInterface $parameterBag;

    protected JsonHandler $jsonHandler;
    protected XmlHandler $xmlHandler;

    public function __construct(
        ParameterBagInterface $parameterBag,
        JsonHandler $jsonHandler,
        XmlHandler $xmlHandler
    ) {
        parent::__construct();
        $this->parameterBag = $parameterBag;
        $this->jsonHandler = $jsonHandler;
        $this->xmlHandler = $xmlHandler;
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

        $output->writeln('Starting conversion' . $input->getArgument('location'));

        $converterService = new ConverterService(
            $this->parameterBag,
            $this->jsonHandler,
            $this->xmlHandler
        );

        $result = $converterService->index($input->getArgument('location'));
        $output->writeln('Converting...');
        
        if (!$result->status) {
            $io->error($result->message);
            return Command::FAILURE;
        }

        $io->success($result->message);

        return Command::SUCCESS;
    }
}
