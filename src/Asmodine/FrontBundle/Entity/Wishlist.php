<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\FrontBundle\Entity\Traits\LifeCycle;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Wishlist.
 *
 * @ORM\Table(name="front_orm_wishlist")
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\WishlistRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Wishlist
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wishlists")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id")
     */
    private $author;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="wishlistsLiked")
     * @ORM\JoinTable(name="front_orm_wishlist_liked")
     */
    private $subscribers;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=512, nullable=true)
     */
    private $image;

    /**
     * @var array
     *
     * @ORM\Column(name="models", type="array", nullable=true, options={"comment":"A model is not a product !"})
     */
    private $models;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->subscribers = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Wishlist
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Wishlist
     */
    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Set models.
     *
     * @param array $models
     *
     * @return Wishlist
     */
    public function setModels($models): self
    {
        $this->models = $models;

        return $this;
    }

    /**
     * Get models.
     *
     * @return array
     */
    public function getModels(): array
    {
        if (is_null($this->models)) {
            $this->models = [];
        }

        return $this->models;
    }

    /**
     * Set author.
     *
     * @param User $author
     *
     * @return Wishlist
     */
    public function setAuthor(User $author = null): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * Add subscriber.
     *
     * @param User $subscriber
     *
     * @return Wishlist
     */
    public function addSubscriber(User $subscriber): self
    {
        $this->subscribers[] = $subscriber;

        return $this;
    }

    /**
     * Remove subscriber.
     *
     * @param User $subscriber
     *
     * @return Wishlist
     */
    public function removeSubscriber(User $subscriber): self
    {
        $this->subscribers->removeElement($subscriber);

        return $this;
    }

    /**
     * Get subscribers.
     *
     * @return Collection
     */
    public function getSubscribers(): Collection
    {
        if (is_null($this->subscribers)) {
            $this->subscribers = new ArrayCollection();
        }

        return $this->subscribers;
    }
}
