<?php

namespace App\Service;

use App\Entity\Streams;
use Doctrine\ORM\EntityManagerInterface;

class StatsRecorder
{
    private string $outputDir;
    private ?EntityManagerInterface $em;

    public function __construct(string $outputDir = 'data', ?EntityManagerInterface $em = null)
    {
        $this->outputDir = $outputDir;
        $this->em = $em;
    }

    /**
     * Saves data in CSV files.
     *
     * @param array $rows        List of lines to write
     * @param string $category   Category name for both folder and file names
     */
    public function recordCsv(array $rows, string $category): void
    {
        $dir = $this->outputDir . '/' . $category;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $filePath = $dir . '/' . $category . '.csv';

        $file = fopen($filePath, 'a');

        foreach ($rows as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }

     /**
     * Saves data in DataBase
     */
    public function recordDb(array $rows, string $timestamp): void
    {
        if ($this->em === null) {
            throw new \RuntimeException('EntityManager not available: database recording is disabled.');
        }

        foreach ($rows as $row) {
            $stat = new Streams();
            $stat->setStreamerName($row['user_name']);
            $stat->setViewerCount($row['viewer_count']);
            $stat->setStreamTitle($row['title'] ?? null);
            $stat->setStartedAt(new \DateTime($row['started_at']));
            $stat->setCollectedAt(new \DateTime($timestamp));
            $stat->setGameId($row['game_id']);
            $stat->setGameName($row['game_name']);
            $stat->setRank($row['rank']);

            $this->em->persist($stat);
        }

        $this->em->flush();
    }
}

