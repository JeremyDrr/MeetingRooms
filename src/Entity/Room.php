<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 * @UniqueEntity(fields={"name"}, message="Une salle comportant ce nom existe déjà")
 */
class Room
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
     * @ORM\OneToMany(targetEntity=Booking::class, mappedBy="room", orphanRemoval=true)
     */
    private $bookings;

    /**
     * @ORM\Column(type="integer")
     */
    private $seats;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasProjector;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    /**
     * Savoir si la salle dans le créneau demandé est déjà occupée
     *
     */
    public function isAvailable(\DateTimeInterface $checkin, \DateTimeInterface $checkout): bool
    {
        $res = true;

        foreach ($this->bookings as $booking){

            if($checkin >= $booking->getStartDate() && $checkout <= $booking->getEndDate()){
                return false;
            }
        }

        return $res;
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

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setRoom($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getRoom() === $this) {
                $booking->setRoom(null);
            }
        }

        return $this;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(int $seats): self
    {
        $this->seats = $seats;

        return $this;
    }

    public function getHasProjector(): ?bool
    {
        return $this->hasProjector;
    }

    public function setHasProjector(bool $hasProjector): self
    {
        $this->hasProjector = $hasProjector;

        return $this;
    }
}
