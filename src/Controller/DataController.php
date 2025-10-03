<?php

namespace App\Controller;

use App\Repository\StreamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{   
    #this route allows finding streams between to dates in a given category
   #[Route('/api/streams', name: 'api_streams')]
    public function streams(Request $request, StreamsRepository $repo): JsonResponse
    {

        // Secure date conversion
        try {
            $from = $request->query->get('from')
                ? new \DateTimeImmutable($request->query->get('from'))
                : new \DateTimeImmutable('2025-09-01');

            $to = $request->query->get('to')
                ? new \DateTimeImmutable($request->query->get('to'))
                : new \DateTimeImmutable('2025-09-30');
        } catch (\Exception $e) {
            // Retourne une erreur claire au frontend si le format est invalide
            return $this->json(['error' => 'Invalid date format'], 400);
        }
        //default values if no parameters are mentionned in the request (fallback)
        $gameName = $request->query->get('game', 'Hades II'); 
        // $from     = new \DateTime($request->query->get('from', '2025-09-01'));
        // $to       = new \DateTime($request->query->get('to', '2025-09-30'));

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

         $response = $this->json([
            'meta' => [
                'count' => count($data),
                'sizeMB' => round(strlen(json_encode($data)) / 1024 / 1024, 2),
            ],
            'data' => $data
        ]);

        return $response;
    }

    #this route allows finding streams at given timestamp (date+ hour + minute)
     #[Route('/api/streamtime', name: 'api_streamtime')]
    public function streamtime(Request $request, StreamsRepository $repo): JsonResponse
    {

      
        //default values if no parameters are mentionned in the request (fallback)
        $gameName = $request->query->get('game', 'Hades II'); 
       
        // paramètre "time" (obligatoire pour cibler une minute précise)
    $timeParam = $request->query->get('time', (new \DateTime())->format('Y-m-d H:i:s'));
    $atTimeStamp = new \DateTime($timeParam);

        $streams = $repo->findByGameAndHour($gameName, $atTimeStamp);

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

         $response = $this->json([
            'meta' => [
                'count' => count($data),
                'sizeMB' => round(strlen(json_encode($data)) / 1024 / 1024, 2),
            ],
            'data' => $data
        ]);

        return $response;
    }

    //this route is to retrieve the list of categories available within the DataBase
    #[Route('/api/games', name: 'api_games')]
    public function games(StreamsRepository $repo): JsonResponse
    {
        $games = $repo->findDistinctGames();
        
        $data = array_map(fn($g) => [
            'id' => $g['gameId'],
            'name' => $g['gameName'],
        ], $games);

        return $this->json($data);
    }

 
}