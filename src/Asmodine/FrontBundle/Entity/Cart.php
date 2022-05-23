<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\FrontBundle\Entity\Traits\LifeCycle;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart.
 *
 * @ORM\Table(name="front_orm_cart")
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\CartRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Cart
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
     * @ORM\OneToOne(targetEntity="User", inversedBy="cart")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var array
     *
     * @ORM\Column(name="products", type="array", nullable=true)
     */
    private $products;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set products.
     *
     * @param array $products
     *
     * @return Cart
     */
    public function setProducts($products): self
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Get products.
     *
     * @return array
     */
    public function getProducts(): array
    {
        if (is_null($this->products)) {
            $this->products = [];
        }

        return $this->products;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Cart
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
