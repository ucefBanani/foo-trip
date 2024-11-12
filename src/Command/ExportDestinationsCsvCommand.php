<?php


namespace App\Command;

use App\Repository\DestinationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExportDestinationsCsvCommand extends Command
{
    private $destinationRepository;
    private $uploadDirectory;

    public function __construct(DestinationRepository $destinationRepository, ParameterBagInterface $params)
    {
        parent::__construct();

        $this->destinationRepository = $destinationRepository;
         $this->uploadDirectory = $params->get('kernel.project_dir') . '/public/uploads/cvs/'; 
    }

    protected function configure(): void
    {
        $this->setName('export:destinations:csv')
            ->setDescription('Export the list of destinations to a CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $destinations = $this->destinationRepository->findAll();

        $fileName = 'destinations_' . date('Y-m-d') . '.csv';
        $filePath = $this->uploadDirectory . $fileName;

        $file = fopen($filePath, 'w');

        if (!$file) {
            $output->writeln('<error>Failed to open the file for writing</error>');
            return Command::FAILURE;
        }

        $header = ['Name', 'Description', 'Price', 'Duration'];
        fputcsv($file, $header);

        foreach ($destinations as $destination) {
            $data = [
                $destination->getName(),
                $destination->getDescription(),
                $destination->getPrice(),
                $destination->getDuration(),
             ];
            fputcsv($file, $data);
        }

        fclose($file);

        $output->writeln('<info>Destinations exported successfully to ' . $filePath . '</info>');

        return Command::SUCCESS;
    }
}
