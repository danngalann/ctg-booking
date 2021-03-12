<?php

namespace App\Entity;

use App\Repository\InfectionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InfectionRepository::class)
 */
class Infection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="infections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="date")
     */
    private $diagnosedOn;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDiagnosedOn(): ?\DateTimeInterface
    {
        return $this->diagnosedOn;
    }

    public function setDiagnosedOn(\DateTimeInterface $diagnosedOn): self
    {
        $this->diagnosedOn = $diagnosedOn;

        return $this;
    }
}
