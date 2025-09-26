<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitchCollector
{
    private HttpClientInterface $client;
    private string $clientId;
    private string $accessToken;
    private string $outputDir;
 

    public function __construct(HttpClientInterface $client, string $clientId, string $accessToken, string $outputDir = 'data')
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->accessToken = $accessToken;
    }

   public function collect(array $gameIds): array
    {
        $allRows = [];
        $timestamp = date('Y-m-d H:i:s');

        foreach ($gameIds as $gameId) {
            $url = 'https://api.twitch.tv/helix/streams?game_id=' . $gameId . '&language=fr&first=100';

            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Client-ID' => $this->clientId,
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
            ]);

            $data = $response->toArray();
            $rank = 1;

            foreach ($data['data'] as $stream) {
                $allRows[] = [
                    'timestamp' => $timestamp,
                    'user_name' => $stream['user_name'],
                    'viewer_count' => $stream['viewer_count'],
                    'title' => $stream['title'],
                    'started_at' => $stream['started_at'],
                    'rank' => $rank,
                    'game_id' => $gameId,
			        'game_name' => $stream['game_name'],
                ];
                $rank++;
            }
        }

         return $allRows;       
    }
}
