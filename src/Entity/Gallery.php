<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'Une galerie avec ce titre existe déjà')]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé')]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 10000,
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $description = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'gallery', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'galleries')]
    private ?Image $featuredImage = null;

    #[ORM\ManyToOne(inversedBy: 'galleries')]
    #[Assert\NotNull(message: 'Veuillez sélectionner une catégorie')]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le slug ne peut pas être vide')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le slug doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le slug ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[a-z0-9\-]+$/',
        message: 'Le slug ne peut contenir que des lettres minuscules, des chiffres et des tirets'
    )]
    private ?string $slug = null;

    #[Assert\All([
        new Assert\Image(mimeTypes: ['image/png', 'image/jpeg', 'image/jpg'])
    ])]
    private ?array $imagesFiles = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

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
        $this->title = trim($title);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description !== null ? trim($description) : null;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setGallery($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getGallery() === $this) {
                $image->setGallery(null);
            }
        }

        return $this;
    }

    public function getFeaturedImage(): ?Image
    {
        return $this->featuredImage;
    }

    public function setFeaturedImage(?Image $featuredImage): static
    {
        $this->featuredImage = $featuredImage;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = trim(strtolower($slug));

        return $this;
    }

    /**
     * Méthode utilitaire pour définir l'image mise en avant
     * Si aucune image n'est spécifiée, utilise la première image de la galerie
     */
    public function updateFeaturedImage(): void
    {
        // Si une image mise en avant est déjà définie, on ne fait rien
        if ($this->featuredImage !== null) {
            return;
        }

        // Si la galerie a des images, on utilise la première comme image mise en avant
        if (!$this->images->isEmpty()) {
            $this->featuredImage = $this->images->first();
        }
    }

    public function getImagesFiles(): ?array
    {
        return $this->imagesFiles;
    }

    public function setImagesFiles(?array $imagesFiles): static
    {
        foreach ($imagesFiles as $imagesFile) {
            $image = new Image();
            $image->setImageFile($imagesFile);
            $this->addImage($image);
        }
        $this->imagesFiles = $imagesFiles;
        return $this;
    }
}
