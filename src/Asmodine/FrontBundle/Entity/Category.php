<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\CommonBundle\DTO\CategoryDTO;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category.
 *
 * Import from Asmodine BACK
 *
 * @ORM\Table(name="front_orm_fromback_category")
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="back_id", type="integer", unique=true)
     */
    private $backId;

    /**
     * @var int
     * @ORM\Column(name="back_parent_id", type="integer", nullable=true)
     */
    private $backParentId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=16, nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=512, unique=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="depth", type="smallint")
     */
    private $depth;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="smallint", nullable=true)
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * Update Entity with DTO.
     *
     * @param CategoryDTO $dto
     *
     * @return Category
     */
    public function update(CategoryDTO $dto): self
    {
        $this->backId = $dto->id;
        $this->backParentId = $dto->parentId;
        $this->name = $dto->name;
        $this->gender = $dto->gender;
        $this->slug = $dto->slug;
        $this->description = $dto->description;
        $this->path = $dto->path;
        if ('/' === substr($this->path, 0, 1)) {
            $this->path = substr($this->path, 1);
        }
        $this->depth = $dto->depth;
        $this->position = $dto->position;
        $this->enabled = $dto->enabled;
        $this->icon = $dto->icon;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set backId.
     *
     * @param int $backId
     *
     * @return Category
     */
    public function setBackId(int $backId): self
    {
        $this->backId = $backId;

        return $this;
    }

    /**
     * Get backId.
     *
     * @return int
     */
    public function getBackId(): int
    {
        return $this->backId;
    }

    /**
     * Set backParentId.
     *
     * @param int $backParentId
     *
     * @return Category
     */
    public function setBackParentId(int $backParentId): self
    {
        $this->backParentId = $backParentId;

        return $this;
    }

    /**
     * Get backParentId.
     *
     * @return int
     */
    public function getBackParentId(): ?int
    {
        return $this->backParentId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Category
     */
    public function setDescription(string $description): self
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
     * Set gender.
     *
     * @param string $gender
     *
     * @return Category
     */
    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return Category
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set depth.
     *
     * @param int $depth
     *
     * @return Category
     */
    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Get depth.
     *
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return Category
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Category
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
     * Set icon.
     *
     * @param string $icon
     *
     * @return Category
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }
}
