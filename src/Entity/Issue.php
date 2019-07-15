<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IssueRepository")
 */
class Issue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $issueDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $issueNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $pageCount;

    /**
    * @ORM\ManyToOne(targetEntity="Volume", inversedBy="issues")
    */
    private $volume;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $archiveKey;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $archiveNotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="issue")
     * @ORM\OrderBy({"printSection" = "ASC", "datePublished" = "DESC"})
     */
    private $articles;

    public function __construct()
    {
        $this->issueDate = new \DateTime();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIssueDate(): ?\DateTimeInterface
    {
        return $this->issueDate;
    }

    public function setIssueDate(\DateTimeInterface $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    public function getIssueNumber(): ?string
    {
        return $this->issueNumber;
    }

    public function setIssueNumber(string $issueNumber): self
    {
        $this->issueNumber = $issueNumber;

        return $this;
    }

    public function getPageCount(): ?int
    {
        return $this->pageCount;
    }

    public function setPageCount(int $pageCount): self
    {
        $this->pageCount = $pageCount;

        return $this;
    }

    public function getVolume(): ?Volume
    {
        return $this->volume;
    }

    public function setVolume(Volume $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getArchiveKey(): ?string
    {
        return $this->archiveKey;
    }

    public function setArchiveKey(?string $archiveKey): self
    {
        $this->archiveKey = $archiveKey;

        return $this;
    }

    public function getArchiveNotes(): ?string
    {
        return $this->archiveNotes;
    }

    public function setArchiveNotes(?string $archiveNotes): self
    {
        $this->archiveNotes = $archiveNotes;

        return $this;
    }

    /* model methods */

    public function getPublicationName()
    {
        switch ($this->volume->getNameplateKey()) {
            case 'volette':
                return 'The Volette';
                break;
            default:
                return 'The Pacer';
        }
    }

    public function __toString()
    {
        return $this->issueDate->format('F j, Y');
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setIssue($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getIssue() === $this) {
                $article->setIssue(null);
            }
        }

        return $this;
    }

    /* Model Methods */

    public function getAsTimelineEventArray(string $link): array
    {
        return [
            'group' => 'Newspaper Issues',
            'text' => [
                'headline' => sprintf(
                    '%s (Issue %s, Volume %s)',
                    $this->getPublicationName(),
                    $this->getIssueNumber() ? $this->getIssueNumber() : 'N/A',
                    $this->getVolume()->getVolumeNumber()
                ),
                'text' => sprintf(
                    'Published %s. %d pages. %s',
                    $this->getIssueDate()->format('F j, Y'),
                    $this->getPageCount(),
                    $this->getArchiveNotes()
                )
            ],
            'start_date' => [
                'year' => (string) $this->getIssueDate()->format('Y'),
                'month' => (string) $this->getIssueDate()->format('m'),
                'day' => (string) $this->getIssueDate()->format('d')
            ],
            'media' => [
                'thumbnail' => 'https://archive.org/services/img/' . $this->getArchiveKey(),
                'url' => 'https://archive.org/services/img/' . $this->getArchiveKey(),
                'alt' => 'Issue cover page.',
                'credit' => 'TSLA / UT Martin Library',
                'link' => $link,
                'link_target' => '_self'
            ]
        ];
    }
}
