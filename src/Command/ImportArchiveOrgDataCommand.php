<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\Issue;
use App\Entity\Volume;

class ImportArchiveOrgDataCommand extends Command
{
    protected static $defaultName = 'app:import-archive-org-data';

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->addOption('create-volumes', null, InputOption::VALUE_NONE, 'Create volumes.')
            ->setDescription('Imports data from Archive.org')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('create-volumes')) {
            // Create 100 Years of Volumes
            $v = 0;
            do {
                $start_year = $v + 1928;
                $end_year = $start_year + 1;

                $volume = new Volume();
                $volume->setVolumeNumber($v + 1);
                $volume->setVolumeStartDate(new \DateTime("$start_year-08-01"));
                $volume->setVolumeEndDate(new \DateTime("$end_year-07-31"));
                $volume->setNameplateKey('volette');
                if ($volume->getVolumeNumber() >= 44) {
                    $volume->setNameplateKey('pacer');
                }
                $this->entityManager->persist($volume);
                $v++;
            } while ($v <= 99);
        }

        $this->entityManager->flush();

        // Import Issues
        $url = 'https://archive.org/advancedsearch.php?' . http_build_query([
            'q' => 'collection:thepacer',
            'fl' => [
                'date',
                'identifier',
                'title',
                'volume',
                'issue',
                'pages',
                'notes',
                'utmdigitalarchive'
            ],
            'sort' => [
                'date asc'
            ],
            'rows' => 3000,
            'page' => 1,
            'output' => 'json'
        ]);

        $client = HttpClient::create();
        $result = $client->request('GET', $url);

        $json = json_decode($result->getContent());

        foreach ($json->response->docs as $doc) {
            // Find existing volume
            $volume = $this->entityManager->getRepository(Volume::class)->findOneBy([
                'volumeNumber' => (int) $doc->volume
            ]);

            if (!$volume) {
                $io->error(sprintf('No matching volume for %s', $doc->volume));
                continue;
            }

            // Find existing match based on identifer
            $issue = $this->entityManager->getRepository(Issue::class)->findOneBy([
                'archiveKey' => $doc->identifier
            ]);
            // Find existing match based on issue date
            if ($issue === null) {
                $issue = $this->entityManager->getRepository(Issue::class)->findOneBy([
                    'issueDate' => new \DateTime($doc->date)
                ]);
            }
            if ($issue === null) {
                $issue = new Issue();
            }

            $issue->setIssueNumber($doc->issue);
            $issue->setVolume($volume);
            $issue->setIssueDate(new \DateTime($doc->date));
            $issue->setArchiveKey($doc->identifier);
            $issue->setPageCount(isset($doc->pages) ? $doc->pages : 0);
            $issue->setArchiveNotes(isset($doc->notes) ? $doc->notes : '');
            if (property_exists($doc, 'utmdigitalarchive')) {
                $issue->setUtmDigitalArchiveUrl($doc->utmdigitalarchive);
            }
            $this->entityManager->persist($issue);
        }

        $this->entityManager->flush();

        $io->success('Done!');
        return 0;
    }
}
