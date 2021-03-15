<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $startTime;

    /**
     * @ORM\Column(type="time")
     */
    private $endTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxClients;

    /**
     * @ORM\ManyToMany(targetEntity=Client::class, mappedBy="bookings")
     */
    private $clients;

    public function __construct(
        string $name,
        DateTimeInterface $date,
        DateTimeInterface $startTime,
        DateTimeInterface $endTime,
        ?int $maxClients
    )
    {
        $this->name = $name;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->maxClients = $maxClients;

        $this->clients = new ArrayCollection();
    }

    public static function create(
        string $name,
        DateTimeInterface $date,
        DateTimeInterface $startTime,
        DateTimeInterface $endTime,
        int $maxClients = null
    ) {
      return new self (
          $name,
          $date,
          $startTime,
          $endTime,
          $maxClients
      );
    }

    public function update(
        string $name,
        DateTimeInterface $date,
        DateTimeInterface $startTime,
        DateTimeInterface $endTime,
        int $maxClients = null
    ) {
        $this->name = $name;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->maxClients = $maxClients;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getMaxClients(): ?int
    {
        return $this->maxClients;
    }

    public function setMaxClients(?int $maxClients): self
    {
        $this->maxClients = $maxClients;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->addBooking($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            $client->removeBooking($this);
        }

        return $this;
    }
}
