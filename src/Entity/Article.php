<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\Table(indexes={
 *   @ORM\Index(
 *     columns={"article_body", "headline", "alternative_headline"},
 *     flags={"fulltext"}
 *   ),
 *   @ORM\Index(
 *     columns={"author_byline", "contributor_byline"},
 *     flags={"fulltext"}
 *   )
 * })
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $printColumn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $printPage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $printSection;

    /**
     * @ORM\Column(type="text")
     */
    private $articleBody;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $headline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alternativeHeadline;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authorByline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contributorByline;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePublished;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $dateModified;

    /**
     * @ORM\Column(type="text")
     */
    private $keywords;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modifiedBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Issue", inversedBy="articles")
     */
    private $issue;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $legacyId;

    /**
     * @Gedmo\Slug(fields={"headline", "id"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="article", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->datePublished = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrintColumn(): ?string
    {
        return $this->printColumn;
    }

    public function setPrintColumn(?string $printColumn): self
    {
        $this->printColumn = $printColumn;

        return $this;
    }

    public function getPrintPage(): ?string
    {
        return $this->printPage;
    }

    public function setPrintPage(?string $printPage): self
    {
        $this->printPage = $printPage;

        return $this;
    }

    public function getPrintSection(): ?string
    {
        return $this->printSection;
    }

    public function setPrintSection(string $printSection): self
    {
        $this->printSection = $printSection;

        return $this;
    }

    public function getArticleBody(): ?string
    {
        return $this->articleBody;
    }

    public function setArticleBody(string $articleBody): self
    {
        $this->articleBody = $articleBody;

        return $this;
    }

    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    public function setHeadline(?string $headline): self
    {
        $this->headline = $headline;

        return $this;
    }

    public function getAlternativeHeadline(): ?string
    {
        return $this->alternativeHeadline;
    }

    public function setAlternativeHeadline(?string $alternativeHeadline): self
    {
        $this->alternativeHeadline = $alternativeHeadline;

        return $this;
    }

    public function getAuthorByline(): ?string
    {
        return $this->authorByline;
    }

    public function setAuthorByline(?string $authorByline): self
    {
        $this->authorByline = $authorByline;

        return $this;
    }

    public function getContributorByline(): ?string
    {
        return $this->contributorByline;
    }

    public function setContributorByline(?string $contributorByline): self
    {
        $this->contributorByline = $contributorByline;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDatePublished(): ?\DateTimeInterface
    {
        return $this->datePublished;
    }

    public function setDatePublished(\DateTimeInterface $datePublished): self
    {
        $this->datePublished = $datePublished;

        return $this;
    }

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getModifiedBy(): ?string
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(string $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getIssue(): ?Issue
    {
        return $this->issue;
    }

    public function setIssue(?Issue $issue): self
    {
        $this->issue = $issue;

        return $this;
    }

    public function getLegacyId(): ?int
    {
        return $this->legacyId;
    }

    public function setLegacyId(int $legacyId): self
    {
        $this->legacyId = $legacyId;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setArticle($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getArticle() === $this) {
                $image->setArticle(null);
            }
        }

        return $this;
    }

    /* Model Methods */

    public function __toString()
    {
        return $this->datePublished->format('n/j/Y') . ': ' . $this->headline;
    }
}
