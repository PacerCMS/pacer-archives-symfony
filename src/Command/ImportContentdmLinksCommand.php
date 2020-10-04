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

class ImportContentdmLinksCommand extends Command
{
    const CONTENTDM_BASE_URL = 'https://cdm17288.contentdm.oclc.org';

    const CONTENTDM_COLLECTION_ID = 'p17288coll4';

    protected static $defaultName = 'app:import-contentdm-links';

    protected $entityManager;

    protected $httpClient;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->httpClient = HttpClient::create();
    }

    protected function configure()
    {
        $this
            ->setDescription('Imports data from UT Martin\'s CONTENTdm instance.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $collectionId = 'p17288coll4';
        $maxResults = 50;
        $start = 1;

        while (true) {
            $contentDmUrl = sprintf(
                '%s/digital/bl/dmwebservices/index.php?q=dmQuery/%s/fields/title!date/date/%d/%d/json',
                self::CONTENTDM_BASE_URL,
                self::CONTENTDM_COLLECTION_ID,
                $maxResults,
                $start
            );

            $response = $this->httpClient->request('GET', $contentDmUrl);
            $result = json_decode($response->getContent());

            if (!$result || !property_exists($result, 'records') || empty($result->records))
            {
                $io->error('No response from API.');
                return 1;
            }

            foreach ($result->records as $item) {
                $issue = $this->entityManager->getRepository(Issue::class)->findOneBy([
                    'issueDate' => new \DateTime($item->date)
                ]);
                // See if we have a matching issue by date
                if (is_null($issue)) {
                    // No match, see if we already have this URL saved
                    $issue = $this->entityManager->getRepository(Issue::class)->findOneBy([
                        'utmDigitalArchiveUrl' => $this->buildUtmArchiveUrl($item)
                    ]);
                    if ($issue) {
                        $io->writeln(sprintf(
                            'No match on date: %s; Found match on: %s; Record: %s',
                            $item->date,
                            $issue->getIssueDate()->format('Y-m-d'),
                            $this->buildUtmArchiveUrl($item)
                        ));
                        continue;
                    }
                    // No match, add error message.
                    $io->error(sprintf('No match on date: %s; Record: %s', $item->date, $this->buildUtmArchiveUrl($item)));
                    continue;
                } else {
                    $issue->setUtmDigitalArchiveUrl($this->buildUtmArchiveUrl($item));
                    $this->entityManager->persist($issue);
                }
            }

            // Determine if we're leaving the loop
            $start += $maxResults;
            if ($start > $result->pager->total) {
                break;
            }
        }
        $this->entityManager->flush();
        $io->success('Done!');
        return 0;
    }

    private function buildUtmArchiveUrl($item): string
    {
        return sprintf(
            '%s/digital/collection/%s/id/%d',
            self::CONTENTDM_BASE_URL,
            self::CONTENTDM_COLLECTION_ID,
            $item->pointer
        );
    }

}
