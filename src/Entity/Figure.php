<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FigureRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'Tricks déjà existant')]
class Figure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Categories::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $Categories = null;

    #[ORM\ManyToOne(targetEntity: Connect::class,  cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Connect $Connect = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime_add = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'figure', targetEntity: Media::class, cascade: ['persist'])]
    private Collection $media;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getCategories(): ?Categories
    {
        return $this->Categories;
    }

    public function setCategories(Categories $Categories): static
    {
        $this->Categories = $Categories;

        return $this;
    }

    public function getConnect(): ?Connect
    {
        return $this->Connect;
    }

    public function setConnect(Connect $Connect): static
    {
        $this->Connect = $Connect;

        return $this;
    }

    public function getDatetimeAdd(): ?\DateTimeInterface
    {
        return $this->datetime_add;
    }


    public function setDatetimeAdd(\DateTimeInterface $datetime_add): static
    {
        $this->datetime_add = $datetime_add;

        return $this;
    }

    public function __construct()
    {
        $this->datetime_add = new \DateTime();
        $this->media = new ArrayCollection();
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setFigure($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getFigure() === $this) {
                $medium->setFigure(null);
            }
        }

        return $this;
    }



}
