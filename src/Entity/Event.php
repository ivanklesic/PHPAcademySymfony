<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
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
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Start time must have valid date and time")
     * @Assert\NotNull(message="Start time cannot be empty")
     * @Assert\GreaterThan("+30 minutes", message="Events need to be set at least half an hour before they start")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="End time must have valid date and time")
     * @Assert\NotNull(message="End time cannot be empty")
     * @Assert\Expression("value > this.getStartTime()", message="End time must be greater than start time")
     */
    private $endTime;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mailSent = 0;

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

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getMailSent(): ?bool
    {
        return $this->mailSent;
    }

    public function setMailSent(bool $mailSent): self
    {
        $this->mailSent = $mailSent;

        return $this;
    }
}
