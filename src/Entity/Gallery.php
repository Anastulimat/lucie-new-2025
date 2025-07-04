<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[UniqueEntity(fields: ['title'], message: 'Une galerie avec ce titre existe déjà')]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé')]
#[Vich\Uploadable]
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
    #[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
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

    #[ORM\Column]
    private ?bool $visibleInNavigation = null;

    // Nouveaux champs pour l'image mise en avant
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $featuredImageFilename = null;

    #[Vich\UploadableField(mapping: 'gallery_featured_images', fileNameProperty: 'featuredImageFilename')]
    private ?File $featuredImageFile = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;


    // Constantes pour les positions du titre
    public const TITLE_POSITION_LEFT_TOP = 'left-top';
    public const TITLE_POSITION_RIGHT_TOP = 'right-top';
    public const TITLE_POSITION_CENTER_TOP = 'center-top';
    public const TITLE_POSITION_CENTER = 'center';
    public const TITLE_POSITION_LEFT_CENTER = 'left-center';
    public const TITLE_POSITION_RIGHT_CENTER = 'right-center';
    public const TITLE_POSITION_LEFT_BOTTOM = 'left-bottom';
    public const TITLE_POSITION_RIGHT_BOTTOM = 'right-bottom';
    public const TITLE_POSITION_CENTER_BOTTOM = 'center-bottom';

    public const TITLE_POSITIONS = [
        'En haut à gauche' => self::TITLE_POSITION_LEFT_TOP,
        'En haut à droite' => self::TITLE_POSITION_RIGHT_TOP,
        'En haut au centre' => self::TITLE_POSITION_CENTER_TOP,
        'Au centre' => self::TITLE_POSITION_CENTER,
        'Au centre à gauche' => self::TITLE_POSITION_LEFT_CENTER,
        'Au centre à droite' => self::TITLE_POSITION_RIGHT_CENTER,
        'En bas à gauche' => self::TITLE_POSITION_LEFT_BOTTOM,
        'En bas à droite' => self::TITLE_POSITION_RIGHT_BOTTOM,
        'En bas au centre' => self::TITLE_POSITION_CENTER_BOTTOM,
    ];


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titlePosition = self::TITLE_POSITION_CENTER_BOTTOM;

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

    // Nouveaux getters/setters pour l'image mise en avant
    public function getFeaturedImageFilename(): ?string
    {
        return $this->featuredImageFilename;
    }

    public function setFeaturedImageFilename(?string $featuredImageFilename): static
    {
        $this->featuredImageFilename = $featuredImageFilename;
        return $this;
    }

    public function getFeaturedImageFile(): ?File
    {
        return $this->featuredImageFile;
    }

    public function setFeaturedImageFile(?File $featuredImageFile): static
    {
        $this->featuredImageFile = $featuredImageFile;
        if ($featuredImageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Méthode pour obtenir l'image mise en avant (fichier uploadé) ou la première image de la galerie
     */
    public function getDisplayFeaturedImage(): ?string
    {
        // Si une image mise en avant est uploadée, on l'utilise
        if ($this->featuredImageFilename) {
            return $this->featuredImageFilename;
        }

        // Sinon, on utilise la première image de la galerie
        if (!$this->images->isEmpty()) {
            $firstImage = $this->images->first();
            return $firstImage->getFilename();
        }

        return null;
    }

    /**
     * Vérifie si la galerie a une image mise en avant définie
     */
    public function hasFeaturedImage(): bool
    {
        return $this->featuredImageFilename !== null;
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
        if ($imagesFiles) {
            foreach ($imagesFiles as $imagesFile) {
                $image = new Image();
                $image->setImageFile($imagesFile);
                $this->addImage($image);
            }
        }
        $this->imagesFiles = $imagesFiles;
        return $this;
    }

    public function isVisibleInNavigation(): ?bool
    {
        return $this->visibleInNavigation;
    }

    public function setVisibleInNavigation(bool $visibleInNavigation): static
    {
        $this->visibleInNavigation = $visibleInNavigation;
        return $this;
    }

    /**
     * Obtient la prochaine position disponible pour une nouvelle image
     */
    private function getNextPosition(): int
    {
        $maxPosition = 0;
        foreach ($this->images as $image) {
            if ($image->getPosition() !== null && $image->getPosition() > $maxPosition) {
                $maxPosition = $image->getPosition();
            }
        }
        return $maxPosition + 1;
    }

    /**
     * Réorganise les positions des images
     */
    public function reorderImages(array $imageIds): void
    {
        $position = 1;
        foreach ($imageIds as $imageId) {
            foreach ($this->images as $image) {
                if ($image->getId() == $imageId) {
                    $image->setPosition($position);
                    $position++;
                    break;
                }
            }
        }
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getTitlePosition(): ?string
    {
        return $this->titlePosition;
    }

    public function setTitlePosition(string $titlePosition): static
    {
        if (!in_array($titlePosition, self::TITLE_POSITIONS)) {
            throw new \InvalidArgumentException('Position de titre invalide');
        }

        $this->titlePosition = $titlePosition;
        return $this;
    }

    public function getTitlePositionClasses(): string
    {
        $classes = [];

        // Position horizontale
        if (str_contains($this->titlePosition, 'left')) {
            $classes[] = 'justify-content-start';
        } elseif (str_contains($this->titlePosition, 'right')) {
            $classes[] = 'justify-content-end';
        } else {
            $classes[] = 'justify-content-center';
        }

        // Position verticale
        if (str_contains($this->titlePosition, 'top')) {
            $classes[] = 'align-items-start';
        } elseif (str_contains($this->titlePosition, 'bottom')) {
            $classes[] = 'align-items-end';
        } else {
            $classes[] = 'align-items-center';
        }

        return implode(' ', $classes);
    }
}