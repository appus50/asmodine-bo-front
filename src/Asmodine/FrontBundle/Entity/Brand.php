<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\CommonBundle\DTO\BrandDTO;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Brand.
 *
 * Import from Asmodine BACK
 *
 * @ORM\Table(name="front_orm_fromback_brand")
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\BrandRepository")
 */
class Brand
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Assert\NotBlank
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="iframe", type="boolean")
     */
    private $iframe;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=512)
     * @Assert\NotBlank
     */
    private $logo;

    /**
     * Update Entity with DTO.
     *
     * @param BrandDTO $dto
     *
     * @return Brand
     */
    public function update(BrandDTO $dto): self
    {
        $this->backId = $dto->id;
        $this->name = $dto->name;
        $this->slug = $dto->slug;
        $this->description = $dto->description;
        $this->iframe = $dto->iframe;
        $this->enabled = $dto->enabled;
        $this->logo = $dto->logo;

        return $this;
    }

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
     * Set backId.
     *
     * @param int $backId
     *
     * @return Brand
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
    public function getBackId()
    {
        return $this->backId;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Brand
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Brand
     */
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Brand
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set iframe.
     *
     * @param bool $iframe
     *
     * @return Brand
     */
    public function setIframe($iframe): self
    {
        $this->iframe = $iframe;

        return $this;
    }

    /**
     * Get iframe.
     *
     * @return bool
     */
    public function getIframe()
    {
        return $this->iframe;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Brand
     */
    public function setEnabled($enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set logo.
     *
     * @param string $logo
     *
     * @return Brand
     */
    public function setLogo($logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo.
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }
}
