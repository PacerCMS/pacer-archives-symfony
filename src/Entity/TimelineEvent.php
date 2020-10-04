<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimelineEventRepository")
 */
class TimelineEvent
{
    public const DATE_PRECISIONS = [
        'Day' => 'day',
        'Month' => 'month',
        'Year' => 'year'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $startDatePrecision;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $endDatePrecision;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $displayDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $headline;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $media;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaCredit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaCaption;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mediaThumbnail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eventGroup;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $background;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getStartDatePrecision(): ?string
    {
        return $this->startDatePrecision;
    }

    public function setStartDatePrecision(string $startDatePrecision): self
    {
        if (!in_array($startDatePrecision, array_values(self::DATE_PRECISIONS))) {
            throw new \InvalidArgumentException('Invalid date precision.');
        }

        $this->startDatePrecision = $startDatePrecision;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getEndDatePrecision(): ?string
    {
        return $this->endDatePrecision;
    }

    public function setEndDatePrecision(?string $endDatePrecision): self
    {
        if (!in_array($endDatePrecision, array_values(self::DATE_PRECISIONS))) {
            throw new \InvalidArgumentException('Invalid date precision.');
        }

        $this->endDatePrecision = $endDatePrecision;

        return $this;
    }

    public function getDisplayDate(): ?string
    {
        return $this->displayDate;
    }

    public function setDisplayDate(string $displayDate): self
    {
        $this->displayDate = $displayDate;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getMediaCredit(): ?string
    {
        return $this->mediaCredit;
    }

    public function setMediaCredit(?string $mediaCredit): self
    {
        $this->mediaCredit = $mediaCredit;

        return $this;
    }

    public function getMediaCaption(): ?string
    {
        return $this->mediaCaption;
    }

    public function setMediaCaption(?string $mediaCaption): self
    {
        $this->mediaCaption = $mediaCaption;

        return $this;
    }

    public function getMediaThumbnail(): ?string
    {
        return $this->mediaThumbnail;
    }

    public function setMediaThumbnail(?string $mediaThumbnail): self
    {
        $this->mediaThumbnail = $mediaThumbnail;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEventGroup(): ?string
    {
        return $this->eventGroup;
    }

    public function setEventGroup(?string $eventGroup): self
    {
        $this->eventGroup = $eventGroup;

        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(?string $background): self
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Model Methods
     */

     public function __toString(): string
     {
         return $this->headline;
     }

    public function getAsTimelineEventArray(): array
    {
        $record = [];

        // Start date (required)
        $record['start_date'] = [];
        switch ($this->getStartDatePrecision()) {
            case 'minute':
                $record['start_date']['minute'] = $this->getStartDate()->format('i');
                // fall-through
            case 'hour':
                $record['start_date']['hour'] = $this->getStartDate()->format('H');
                // fall-through
            case 'day':
                $record['start_date']['day'] = $this->getStartDate()->format('d');
                // fall-through
            case 'month':
                $record['start_date']['month'] = $this->getStartDate()->format('m');
                // fall-through
            case 'year':
            default:
                $record['start_date']['year'] = $this->getStartDate()->format('Y');
                break;
        }

        // End date (optional)
        if ($this->getEndDate() && $this->getEndDatePrecision()) {
            $record['end_date'] = [];
            switch ($this->getEndDatePrecision()) {
                case 'minute':
                    $record['end_date']['minute'] = $this->getEndDate()->format('i');
                    // fall-through
                case 'hour':
                    $record['end_date']['hour'] = $this->getEndDate()->format('H');
                    // fall-through
                case 'day':
                    $record['end_date']['day'] = $this->getEndDate()->format('d');
                    // fall-through
                case 'month':
                    $record['end_date']['month'] = $this->getEndDate()->format('m');
                    // fall-through
                case 'year':
                default:
                    $record['end_date']['year'] = $this->getEndDate()->format('Y');
                    break;
            }
        }

        // Event text
        $record['text'] = [
            'headline' => $this->getHeadline(),
            'text' => $this->getText(),
        ];

        // Media
        if ($this->getMedia()) {
            $record['media'] = [
                'url' => $this->getMedia(),
                'caption' => $this->getMediaCaption(),
                'credit' => $this->getMediaCredit()
            ];
        }

        // Event grouping
        $record['group'] = $this->getEventGroup();

        // Background
        if ($this->getBackground()) {
            $record['background'] = ['color' => $this->getBackground()];
        }

        return $record;
    }
}
