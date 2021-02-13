<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VolumeRepository")
 */
class Volume
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $volumeNumber;

    /**
     * @ORM\Column(type="date")
     */
    private $volumeStartDate;

    /**
     * @ORM\Column(type="date")
     */
    private $volumeEndDate;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="volume")
     * @ORM\OrderBy({"issueDate" = "ASC"})
     */
    private $issues;

    /**
     * @ORM\OneToOne(targetEntity="Issue", orphanRemoval=true)
     */
    private $coverIssue;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $nameplateKey;

    public function __construct()
    {
        $this->issues = new ArrayCollection();
        $this->volumeStartDate = new \DateTime();
        $this->volumeEndDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVolumeNumber(): ?string
    {
        return $this->volumeNumber;
    }

    public function setVolumeNumber(string $volumeNumber): self
    {
        $this->volumeNumber = $volumeNumber;

        return $this;
    }

    public function getVolumeStartDate(): ?\DateTimeInterface
    {
        return $this->volumeStartDate;
    }

    public function setVolumeStartDate(\DateTimeInterface $volumeStartDate): self
    {
        $this->volumeStartDate = $volumeStartDate;

        return $this;
    }

    public function getVolumeEndDate(): ?\DateTimeInterface
    {
        return $this->volumeEndDate;
    }

    public function setVolumeEndDate(\DateTimeInterface $volumeEndDate): self
    {
        $this->volumeEndDate = $volumeEndDate;

        return $this;
    }

    public function getIssues(): ?Collection
    {
        return $this->issues;
    }

    public function setIssues(Collection $issues): self
    {
        $this->issues = $issues;

        return $this;
    }

    public function setCoverIssue(Issue $issue): self
    {
        $this->coverIssue = $issue;

        return $this;
    }

    public function getCoverIssue(): ?Issue
    {
        if ($this->coverIssue) {
            return $this->coverIssue;
        }

        // No issues in volume
        if ($this->issues->isEmpty()) {
            return new Issue();
        }

        // Grab a random issue from the volume
        $issue = $this->issues->get(array_rand($this->issues->toArray()));
        return $issue;
    }

    public function getNameplateKey(): ?string
    {
        return $this->nameplateKey;
    }

    public function setNameplateKey(string $nameplateKey): self
    {
        $this->nameplateKey = $nameplateKey;

        return $this;
    }

    /* model methods */

    public function getNamePlateDisplay(): string
    {
        switch($this->nameplateKey) {
            case 'pacer':
                return 'The Pacer';
                break;
            case 'volette':
                return 'The Volette';
                break;
            default:
                return 'Unknown';
        }
    }

    public function __toString()
    {
        return sprintf(
            'Volume %s (%d-%d)',
            $this->volumeNumber,
            $this->volumeStartDate->format('Y'),
            $this->volumeEndDate->format('Y')
        );
    }
}
