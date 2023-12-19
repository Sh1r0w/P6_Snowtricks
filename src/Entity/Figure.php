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

    #[ORM\ManyToOne(targetEntity: Categories::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $Categories = null;

    #[ORM\ManyToOne(targetEntity: Connect::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Connect $Connect = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime_add = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'figure', targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private Collection $image;

    #[ORM\OneToMany(mappedBy: 'figure', targetEntity: Video::class, cascade: ['persist', 'remove'])]
    private Collection $videos;

    #[ORM\OneToMany(mappedBy: 'figure', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    private Collection $comments;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_update = null;

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

        $this->image = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * @return Collection<int, Image>
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Image $medium): static
    {
        if (!$this->image->contains($medium)) {
            $this->image->add($medium);
            $medium->setFigure($this);
        }

        return $this;
    }

    public function removeImage(Image $medium): static
    {
        if ($this->image->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getFigure() === $this) {
                $medium->setFigure(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setFigure($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getFigure() === $this) {
                $video->setFigure(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setFigure($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getFigure() === $this) {
                $comment->setFigure(null);
            }
        }

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(\DateTimeInterface $date_update): static
    {
        $this->date_update = $date_update;

        return $this;
    }




}
