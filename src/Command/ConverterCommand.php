<?php

namespace App\Command;

use App\Domain\Dto\Converter\ConverterRequestDto;
use App\Service\Converter\ConverterService;
use App\Service\Converter\Handler\JsonHandler;
use App\Service\Converter\Handler\XmlHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('filename', InputArgument::OPTIONAL, 'Enter the file name'),
                    new InputOption('sort', 's', InputOption::VALUE_OPTIONAL, 'What will you like to sort the data with? e.g bin/console trivago:convert hotelValidate.json name'),
                    new InputOption('filter', 'f', InputOption::VALUE_OPTIONAL, 'What will you like to filter the data with?'),
                    new InputOption('group', 'g', InputOption::VALUE_OPTIONAL, 'How will you like to group the data? e.g')
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        new ConsoleLogger($output);

        $name = $input->getArgument('filename');
        $sortBy = $input->getOption('sort');
        $filterBy = $input->getOption('filter');
        $filterValue = '';
        $groupBy = $input->getOption('group');

        $io->title('CLI Data Converter');
        $io->text('Converts json and xml files to csv format');
        $io->warning('Only JSON,XML and CSV files are currently supported');

        if (is_null($name)) {
            $io->caution([
                'Please add file name e.g',
                'bin/console trivago:convert filename.json'
            ]);
            return Command::FAILURE;
        }

        if (isset($filterBy)) {
            $filterValue = $io->ask('What value do you want to filter the ' . $filterBy . ' with');
            if (!isset($filterValue)) {
                $io->error('Sorry, you need to include a value before you use the filter option');
                return Command::FAILURE;
            }
        }
        $io->text('Your file name is ' . $name);


        $dto = new ConverterRequestDto($name, $sortBy, $filterBy, $filterValue, $groupBy);

        $result = $this->converterService->index($dto);

        if (!$result->status) {
            $io->error($result->message);
            return Command::FAILURE;
        }

        $io->success([
            $result->message,
            'The new csv is stored in: ' . $result->path
        ]);

        return Command::SUCCESS;
    }
}
