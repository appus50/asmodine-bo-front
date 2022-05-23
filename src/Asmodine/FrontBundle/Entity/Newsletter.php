<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\FrontBundle\Entity\Traits\LifeCycle;
use Doctrine\ORM\Mapping as ORM;

/**
 * Newsletter.
 *
 * @ORM\Table(
 *     name="front_orm_newsletter",
 *     indexes={
 *          @ORM\Index(name="enabled_idx", columns={"enabled"}),
 *      },
 * )
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\NewsletterRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Newsletter
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
     * @ORM\OneToOne(targetEntity="User", inversedBy="newsletter")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

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
     * Set email.
     *
     * @param string $email
     *
     * @return Newsletter
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return Newsletter
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
     * Set user.
     *
     * @param User $user
     *
     * @return Newsletter
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
