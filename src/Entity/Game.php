<?php

namespace App\Entity;

use App\Repository\GameRepository;
use App\Traits\SoftDeleteTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @UniqueEntity("name")
 */
class Game
{
    use SoftDeleteTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"search"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Name must be at least {{ limit }} characters long",
     *      maxMessage = "Name cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     * @Groups({"search"})
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     * @Assert\Type("\DateTimeInterface")
     * @Groups({"search"})
     */
    private $releaseDate;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="games")
     */
    private $genres;

    /**
     * @ORM\Column(type="float")
     * @Assert\PositiveOrZero
     */
    private $cpuFreq;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $cpuCores;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $gpuVram;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $ram;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $storageSpace;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="game", orphanRemoval=true)
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"search"})
     */
    private $imageUrl;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    public function getCpuFreq(): ?float
    {
        return $this->cpuFreq;
    }

    public function setCpuFreq(float $cpuFreq): self
    {
        $this->cpuFreq = $cpuFreq;

        return $this;
    }

    public function getCpuCores(): ?int
    {
        return $this->cpuCores;
    }

    public function setCpuCores(int $cpuCores): self
    {
        $this->cpuCores = $cpuCores;

        return $this;
    }

    public function getGpuVram(): ?int
    {
        return $this->gpuVram;
    }

    public function setGpuVram(int $gpuVram): self
    {
        $this->gpuVram = $gpuVram;

        return $this;
    }

    public function getRam(): ?int
    {
        return $this->ram;
    }

    public function setRam(int $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getStorageSpace(): ?int
    {
        return $this->storageSpace;
    }

    public function setStorageSpace(int $storageSpace): self
    {
        $this->storageSpace = $storageSpace;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setGame($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getGame() === $this) {
                $review->setGame(null);
            }
        }

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * @VirtualProperty()
     * @Type("float")
     * @SerializedName("average_rating")
     * @Groups({"search"})
     * @return float|null
     */
    public function getAverageRating() : ?float
    {
        $reviews = $this->getReviews();

        if($reviews->isEmpty())
        {
            return null;
        }
        $sum = 0;
        foreach($reviews as $review)
        {
            $sum += $review->getRating();
        }

        return $sum/count($reviews);
    }

    /**
     * @VirtualProperty()
     * @Type("int")
     * @SerializedName("total_ratings")
     * @Groups({"search"})
     * @return int|null
     */

    public function getTotalRatings() : ?int
    {
        $reviews = $this->getReviews();

        if($reviews->isEmpty())
        {
            return null;
        }

        return count($reviews);
    }
}
