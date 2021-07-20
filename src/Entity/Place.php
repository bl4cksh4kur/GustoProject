<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 */
class Place
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
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=BookingPlace::class, mappedBy="place")
     */
    private $bookingPlaces;

    public function __toString()
    {
        return $this->title;
    }

    public function __construct()
    {
        $this->bookingPlaces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|BookingPlace[]
     */
    public function getBookingPlaces(): Collection
    {
        return $this->bookingPlaces;
    }

    public function addBookingPlace(BookingPlace $bookingPlace): self
    {
        if (!$this->bookingPlaces->contains($bookingPlace)) {
            $this->bookingPlaces[] = $bookingPlace;
            $bookingPlace->setPlace($this);
        }

        return $this;
    }

    public function removeBookingPlace(BookingPlace $bookingPlace): self
    {
        if ($this->bookingPlaces->removeElement($bookingPlace)) {
            // set the owning side to null (unless already changed)
            if ($bookingPlace->getPlace() === $this) {
                $bookingPlace->setPlace(null);
            }
        }

        return $this;
    }
}
