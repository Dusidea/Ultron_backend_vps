<?php

namespace App\Controller;

use App\Repository\StreamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{
   #[Route('/api/streams', name: 'api_streams')]
    public function streams(Request $request, StreamsRepository $repo): JsonResponse
    {
        //default values if no parameters are mentionned in the request (fallback)
        $gameName = $request->query->get('game', 'Hades II'); 
        $from     = new \DateTime($request->query->get('from', '2025-09-01'));
        $to       = new \DateTime($request->query->get('to', '2025-09-30'));

        $streams = $repo->findByGameAndPeriod($gameName, $from, $to);

        $data = array_map(fn($s) => [
            'id'            => $s->getId(),
            'collectedAt'   => $s->getCollectedAt()->format('Y-m-d H:i:s'),
            'streamerName'  => $s->getStreamerName(),
            'viewerCount'   => $s->getViewerCount(),
            'streamTitle'   => $s->getStreamTitle(),
            'startedAt'     => $s->getStartedAt()->format('Y-m-d H:i:s'),
            'rank'          => $s->getRank(),
            'gameId'        => $s->getGameId(),
            'gameName'      => $s->getGameName(),
        ], $streams);

        return $this->json($data);
    }

    #[Route('/api/games', name: 'api_games')]
    public function games(StreamsRepository $repo): JsonResponse
    {
        $games = $repo->findDistinctGames();

        // On simplifie le format pour le front (optionnel)
        $data = array_map(fn($g) => [
            'id' => $g['gameId'],
            'name' => $g['gameName'],
        ], $games);

        return $this->json($data);
    }

 
}