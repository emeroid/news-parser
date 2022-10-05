<?php

namespace App\Command;

use App\Message\ParseNewsPageMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand( name: 'newsparser', description: 'Save news to rabbitMQ' )]

class ParseNewsCommand extends Command
{
    protected static $defaultName = 'newsparser';
    protected static $defaultDescription = 'Save news to rabbitMQ';

    public function __construct(HttpClientInterface $httpClient, MessageBusInterface $messageBus)
    {
        $this->httpClient = $httpClient;
        $this->messageBus = $messageBus;

        parent::__construct();

    }

    private function queueData()
    {
        $page = 0;
        $content = true;
        while ($content) {

            $response = $this->httpClient->request('POST',
                'https://highload.today/wp-content/themes/supermc/ajax/loadarchive.php', [
                    'body' => [
                        'action' => 'archiveload',
                        'stick' => 35,
                        'page' => $page,
                        'cat' => 537
                    ]
                ]);

            $content = $response->getContent();
            $page++;
            if ($content){
                yield $response->getContent()??$response->getContent();
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        foreach ($this->queueData() as $content) {
            $this->messageBus->dispatch(new ParseNewsPageMessage($content));
        }

        return Command::SUCCESS;

    }
}
