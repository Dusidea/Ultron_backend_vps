<?php

namespace App\Command;

use App\Service\TwitchCollector;
use App\Service\StatsRecorder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'twitch:collect-viewers',
    description: 'Collecte les données Twitch pour plusieurs catégories',
)]
class TwitchCollectCommand extends Command
{
    private TwitchCollector $collector;
    private StatsRecorder $recorder;
    private array $categories;

     public function __construct(TwitchCollector $collector, StatsRecorder $recorder, array $categories)
    {
        parent::__construct();
        $this->collector = $collector;
        $this->recorder = $recorder;
        $this->categories = $categories;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->categories as $categoryName => $gameIds) {
            try {
                $rows = $this->collector->collect($gameIds);
                // CSV backup, deactivated
                // $this->recorder->recordCsv($rows, $categoryName);

                // DB insert
                $timestamp = date('Y-m-d H:i:s');
                $this->recorder->recordDb($rows, $timestamp);

                $io->success("✅ Données enregistrées pour $categoryName");
            } catch (\Throwable $e) {
                $io->error("❌ Erreur sur $categoryName : " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
