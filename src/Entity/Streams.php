<?php

namespace App\Entity;

use App\Repository\StreamsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StreamsRepository::class)]
class Streams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(name: "collected_at", type: "datetime")]
    private \DateTimeInterface $collectedAt;

    #[ORM\Column(length: 255)]
    private string $streamer_name;

    #[ORM\Column(name: "viewer_count", type: "integer")]
    private int $viewerCount;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $stream_title = null;

    #[ORM\Column(name: "started_at", type: "datetime")]
    private \DateTimeInterface $startedAt;

    #[ORM\Column(name: "rank", type: "integer")]
    private int $rank;

     #[ORM\Column(name: "game_id", length: 50)]
    private string $gameId;

    #[ORM\Column(name: "game_name", length: 255)]
    private string $gameName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCollectedAt(): ?\DateTime
    {
        return $this->collectedAt;
    }

    public function setCollectedAt(\DateTime $collectedAt): static
    {
        $this->collectedAt = $collectedAt;

        return $this;
    }

    public function getStreamerName(): ?string
    {
        return $this->streamer_name;
    }

    public function setStreamerName(string $streamer_name): static
    {
        $this->streamer_name = $streamer_name;

        return $this;
    }

    public function getViewerCount(): ?int
    {
        return $this->viewerCount;
    }

    public function setViewerCount(int $viewerCount): static
    {
        $this->viewerCount = $viewerCount;

        return $this;
    }

    public function getStreamTitle(): ?string
    {
        return $this->stream_title;
    }

    public function setStreamTitle(?string $stream_title): static
    {
        $this->stream_title = $stream_title;

        return $this;
    }

    public function getStartedAt(): ?\DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTime $startedAt): static
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(int $rank): static
    {
        $this->rank = $rank;

        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->gameId;
    }

    public function setGameId(string $gameId): static
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getGameName(): ?string
    {
        return $this->gameName;
    }

    public function setGameName(string $gameName): static
    {
        $this->gameName = $gameName;

        return $this;
    }

}
