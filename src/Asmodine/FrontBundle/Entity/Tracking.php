<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\FrontBundle\Entity\Traits\LifeCycle;
use Doctrine\ORM\Mapping as ORM;

/**
 * Tracking.
 *
 * @ORM\Table(name="front_orm_tracking")
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\TrackingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Tracking
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
     * @ORM\ManyToOne(inversedBy="trackings", targetEntity="User")
     * @ORM\JoinColumn(referencedColumnName="id", name="user_id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="model", type="text")
     */
    private $model;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="view", type="datetime")
     */
    private $view;

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
     * Set model.
     *
     * @param string $model
     *
     * @return Tracking
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Set view.
     *
     * @param \DateTime $view
     *
     * @return Tracking
     */
    public function setView(\DateTime $view = null): self
    {
        if (is_null($view)) {
            $view = new \DateTime();
        }
        $this->view = $view;

        return $this;
    }

    /**
     * Get view.
     *
     * @return \DateTime
     */
    public function getView(): \DateTime
    {
        return $this->view;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Tracking
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
    public function getUser(): User
    {
        return $this->user;
    }
}
