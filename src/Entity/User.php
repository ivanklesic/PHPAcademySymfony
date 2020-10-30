<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Traits\SoftDelete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $cpuCores;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $cpuFreq;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $gpuVram;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $ram;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\PositiveOrZero()
     */
    private $storageSpace;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="users")
     */
    private $favoriteGenres;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="user", orphanRemoval=true)
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageUrl;

    /**
     * @ORM\OneToMany(targetEntity=Team::class, mappedBy="leader")
     */
    private $teamsLed;

    /**
     * @ORM\ManyToMany(targetEntity=Team::class, mappedBy="members")
     */
    private $teams;

    use SoftDelete;

    public function __construct()
    {
        $this->favoriteGenres = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->teamsLed = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCpuCores(): ?int
    {
        return $this->cpuCores;
    }

    public function setCpuCores(?int $cpuCores): self
    {
        $this->cpuCores = $cpuCores;

        return $this;
    }

    public function getCpuFreq(): ?float
    {
        return $this->cpuFreq;
    }

    public function setCpuFreq(?float $cpuFreq): self
    {
        $this->cpuFreq = $cpuFreq;

        return $this;
    }

    public function getGpuVram(): ?int
    {
        return $this->gpuVram;
    }

    public function setGpuVram(?int $gpuVram): self
    {
        $this->gpuVram = $gpuVram;

        return $this;
    }

    public function getRam(): ?int
    {
        return $this->ram;
    }

    public function setRam(?int $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getStorageSpace(): ?int
    {
        return $this->storageSpace;
    }

    public function setStorageSpace(?int $storageSpace): self
    {
        $this->storageSpace = $storageSpace;

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getFavoriteGenres(): Collection
    {
        return $this->favoriteGenres;
    }

    public function addFavoriteGenre(Genre $favoriteGenre): self
    {
        if (!$this->favoriteGenres->contains($favoriteGenre)) {
            $this->favoriteGenres[] = $favoriteGenre;
        }

        return $this;
    }

    public function removeFavoriteGenre(Genre $favoriteGenre): self
    {
        if ($this->favoriteGenres->contains($favoriteGenre)) {
            $this->favoriteGenres->removeElement($favoriteGenre);
        }

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
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
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

    public function getDeleted() : ?bool
    {
        return $this->deleted;
    }

    public function setDeleted($deleted) : self
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeamsLed(): Collection
    {
        return $this->teamsLed;
    }

    public function addTeamsLed(Team $teamsLed): self
    {
        if (!$this->teamsLed->contains($teamsLed)) {
            $this->teamsLed[] = $teamsLed;
            $teamsLed->setLeader($this);
        }

        return $this;
    }

    public function removeTeamsLed(Team $teamsLed): self
    {
        if ($this->teamsLed->contains($teamsLed)) {
            $this->teamsLed->removeElement($teamsLed);
            // set the owning side to null (unless already changed)
            if ($teamsLed->getLeader() === $this) {
                $teamsLed->setLeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addMember($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->contains($team)) {
            $this->teams->removeElement($team);
            $team->removeMember($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getFirstName() . ' ' .  $this->getLastName();
    }

}
