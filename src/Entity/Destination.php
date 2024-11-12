<?php

namespace App\Entity;

use App\Repository\DestinationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DestinationRepository::class)]
class Destination
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['destination:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Name is required")]
    #[Groups(['destination:read', 'destination:write'])]
    private string $name;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "Description is required")]
    #[Groups(['destination:read', 'destination:write'])]
    private string $description;

    #[ORM\Column(type: 'decimal', scale: 2)]
    #[Assert\NotBlank(message: "Price must be a number")]
    #[Assert\Positive]
    #[Groups(['destination:read', 'destination:write'])]
    private float $price;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Duration is not correctly formatted")]
    #[Assert\Positive]
    #[Groups(['destination:read', 'destination:write'])]
    private int $duration;

    #[ORM\Column(type: 'string')]
    #[Groups(['destination:read', 'destination:write'])]
    private string $image;

    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png", "image/jpg"],
        mimeTypesMessage: "Please upload a valid image file (JPEG, PNG)"
    )]
    #[Groups(['destination:write'])]
    private ?UploadedFile $imageFile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    public function setImageFile(?UploadedFile $imageFile): self
    {
        $this->imageFile = $imageFile;
        return $this;
    }
 
}
