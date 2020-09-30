<?php

namespace App\Command;

use App\Service\Converter\ConverterService;
use App\Service\Converter\Handler\JsonHandler;
use App\Service\Converter\Handler\XmlHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConverterCommand extends Command
{
    protected static $defaultName = 'trivago:convert';

    private ParameterBagInterface $parameterBag;

    protected ConverterService $converterService;

    public function __construct(
        ParameterBagInterface $parameterBag,
        ConverterService $converterService
    ) {
        parent::__construct();
        $this->parameterBag = $parameterBag;
        $this->converterService = $converterService;
    }


    protected function configure()
    {
        $this->setDescription('A CLI data converter')
            ->setHelp('Add the file location')
            ->addArgument('filename', InputArgument::REQUIRED, 'Enter the file name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        new ConsoleLogger($output);
        $path = $input->getArgument('filename');
        $io->title('CLI Data Converter');
        $io->text('Converts json and xml files to csv format');
        $io->caution('Only JSON,XML and CSV files are currently supported');
        $io->text('Your file path is ' . $path);
        

        $result = $this->converterService->index($path);

        if (!$result->status) {
            $io->error($result->message);
            return Command::FAILURE;
        }

        $io->success([
            $result->message,
            'The new csv is stored in: '. $result->path
            ]);

        return Command::SUCCESS;
    }
}
