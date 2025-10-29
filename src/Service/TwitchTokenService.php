<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

class TwitchTokenService
{
    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private string $twitchClientId,
        private string $twitchClientSecret,
        private ?LoggerInterface $logger = null
    ) {}

    public function getToken(): string
    {
        $tokenItem = $this->cache->getItem('twitch_app_token');

        if ($tokenItem->isHit()) {
            $token = $tokenItem->get();

            // Checks the token validity
            if ($this->isTokenValid($token)) {
                return $token;
            } else {
                $this->logger?->warning('Twitch token invalid — regenerating...');
            }
        }

        // If the token is no longer valid => generates a new one
        $newToken = $this->refreshToken();
        $tokenItem->set($newToken);
        $tokenItem->expiresAfter(3600 * 24 * 59);
        $this->cache->save($tokenItem);

        return $newToken;
    }

    private function refreshToken(): string
    {
        $response = $this->client->request('POST', 'https://id.twitch.tv/oauth2/token', [
            'query' => [
                'client_id' => $this->twitchClientId,
                'client_secret' => $this->twitchClientSecret,
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = $response->toArray();
        return $data['access_token'];
    }

    private function isTokenValid(string $token): bool
    {
        try {
            $response = $this->client->request('GET', 'https://id.twitch.tv/oauth2/validate', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                // optionnel : log ou exploiter expires_in
                $this->logger?->info('Twitch token valid', ['expires_in' => $data['expires_in']]);
                return true;
            }
        } catch (\Throwable $e) {
            $this->logger?->error('Token validation failed: '.$e->getMessage());
        }

        return false;
    }
}
