<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitchCollector
{
    private HttpClientInterface $client;
    private string $clientId;
    private TwitchTokenService $tokenService;
    private string $outputDir;

    public function __construct(
        HttpClientInterface $client,
        string $clientId,
        TwitchTokenService $tokenService,
        string $outputDir = 'data'
    ) {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->tokenService = $tokenService;
        $this->outputDir = $outputDir;
    }

    public function collect(array $gameIds): array
    {
        $allRows = [];
        $timestamp = date('Y-m-d H:i:s');

        // 🔑 Récupère dynamiquement le token via ton service
        $accessToken = $this->tokenService->getToken();

        foreach ($gameIds as $gameId) {
            $url = sprintf(
                'https://api.twitch.tv/helix/streams?game_id=%s&language=fr&first=50',
                urlencode($gameId)
            );

            try {
                $response = $this->client->request('GET', $url, [
                    'headers' => [
                        'Client-ID' => $this->clientId,
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);

                $data = $response->toArray(false); // false => ne jette pas d'exception sur 4xx/5xx
            } catch (\Throwable $e) {
                // Si jamais le token est invalide ou Twitch renvoie une erreur
                // on tente une régénération et on refait un seul essai
                $accessToken = $this->tokenService->getToken();
                $response = $this->client->request('GET', $url, [
                    'headers' => [
                        'Client-ID' => $this->clientId,
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ]);
                $data = $response->toArray(false);
            }

            if (!isset($data['data'])) {
                continue; // pas de flux à traiter
            }

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
