<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\FrontBundle\Entity\Traits\LifeCycle;
use Doctrine\ORM\Mapping as ORM;

/**
 * Review.
 *
 * @ORM\Table(
 *     name="front_orm_review",
 *     indexes={
 *          @ORM\Index(name="model_idx", columns={"model_id", "enabled"}),
 *      },
 * )
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\ReviewRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Review
{
    use LifeCycle;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(inversedBy="reviews", targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="model_id", type="string", length=64)
     */
    private $modelId;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="note", type="smallint")
     */
    private $note;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set modelId.
     *
     * @param string $modelId
     *
     * @return Review
     */
    public function setModelId(string $modelId): self
    {
        $this->modelId = $modelId;

        return $this;
    }

    /**
     * Get modelId.
     *
     * @return string
     */
    public function getModelId(): string
    {
        return $this->modelId;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Review
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Review
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Review
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set note.
     *
     * @param int $note
     *
     * @return Review
     */
    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note.
     *
     * @return int
     */
    public function getNote(): int
    {
        return $this->note;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Review
     */
    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
