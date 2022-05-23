<?php

namespace Asmodine\FrontBundle\Entity;

use Asmodine\FrontBundle\Entity\Traits\LifeCycle;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * User.
 *
 * @ORM\Table(name="front_orm_user")
 * @ORM\Entity(repositoryClass="Asmodine\FrontBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    use LifeCycle;

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var PhysicalProfile[]
     *
     * @ORM\OneToMany(targetEntity="PhysicalProfile", mappedBy="user")
     */
    private $physicalProfiles;

    /**
     * @var Tracking[]
     *
     * @ORM\OneToMany(targetEntity="Tracking", mappedBy="user")
     */
    private $trackings;

    /**
     * @var Wishlist[]
     *
     * @ORM\OneToMany(targetEntity="Wishlist", mappedBy="author")
     */
    private $wishlists;

    /**
     * @var Wishlist[]
     *
     * @ORM\ManyToMany(targetEntity="Wishlist", mappedBy="subscribers")
     */
    private $wishlistsLiked;

    /**
     * @var Review[]
     *
     * @ORM\OneToMany(targetEntity="Review", mappedBy="user")
     */
    private $reviews;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=128, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=128, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=16)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=512, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="zipCode", type="string", length=32, nullable=true)
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="date", nullable=true)
     */
    private $birthDate;

    /**
     * @var string
     * @Groups({"user"})
     */
    protected $username;

    /**
     * @var string
     * @Groups({"user"})
     */
    protected $email;

    /**
     * @var string
     * @Groups({"user-write"})
     */
    protected $plainPassword;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fastProfile", type="date", nullable=true)
     */
    private $fastProfile;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="appProfile", type="date", nullable=true)
     */
    private $appProfile;



    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles = ['ROLE_USER'];
        $this->physicalProfiles = new ArrayCollection();
        $this->trackings = new ArrayCollection();
        $this->wishlists = new ArrayCollection();
        $this->wishlistsLiked = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    /**
     * @param UserInterface|null $user
     *
     * @return bool
     */
    public function isUser(UserInterface $user = null): bool
    {
        return $user instanceof self && $user->id === $this->id;
    }

    /**
     * Set firstname.
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname.
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname.
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set gender.
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return User
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
     * Set address.
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set zipCode.
     *
     * @param string $zipCode
     *
     * @return User
     */
    public function setZipCode($zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode.
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set city.
     *
     * @param string $city
     *
     * @return User
     */
    public function setCity($city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set birthDate.
     *
     * @param \DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate($birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate.
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set fastProfile.
     *
     * @param \DateTime $fastProfile
     *
     * @return User
     */
    public function setFastProfile($fastProfile): self
    {
        $this->fastProfile = $fastProfile;

        return $this;
    }

    /**
     * Get fastProfile.
     *
     * @return \DateTime
     */
    public function getFastProfile()
    {
        return $this->fastProfile;
    }


    /**
     * Set appProfile.
     *
     * @param \DateTime $appProfile
     *
     * @return User
     */
    public function setAppProfile($appProfile): self
    {
        $this->appProfile = $appProfile;

        return $this;
    }

    /**
     * Get appProfile.
     *
     * @return \DateTime
     */
    public function getAppProfile()
    {
        return $this->appProfile;
    }

    /**
     * Add physicalProfile.
     *
     * @param PhysicalProfile $physicalProfile
     *
     * @return User
     */
    public function addPhysicalProfile(PhysicalProfile $physicalProfile)
    {
        $this->physicalProfiles->add($physicalProfile);

        return $this;
    }

    /**
     * Remove physicalProfile.
     *
     * @param PhysicalProfile $physicalProfile
     */
    public function removePhysicalProfile(PhysicalProfile $physicalProfile)
    {
        $this->physicalProfiles->removeElement($physicalProfile);
    }

    /**
     * Get physicalProfiles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhysicalProfiles()
    {
        return $this->physicalProfiles;
    }

    /**
     * Add tracking.
     *
     * @param Tracking $tracking
     *
     * @return User
     */
    public function addTracking(Tracking $tracking): self
    {
        $this->trackings->add($tracking);

        return $this;
    }

    /**
     * Remove tracking.
     *
     * @param Tracking $tracking
     *
     * @return $this
     */
    public function removeTracking(Tracking $tracking): self
    {
        $this->trackings->removeElement($tracking);

        return $this;
    }

    /**
     * Get trackings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTrackings()
    {
        return $this->trackings;
    }

    /**
     * Add wishlist.
     *
     * @param Wishlist $wishlist
     *
     * @return User
     */
    public function addWishlist(Wishlist $wishlist): self
    {
        $this->wishlists->add($wishlist);

        return $this;
    }

    /**
     * Remove wishlist.
     *
     * @param Wishlist $wishlist
     */
    public function removeWishlist(Wishlist $wishlist)
    {
        $this->wishlists->removeElement($wishlist);
    }

    /**
     * Get wishlists.
     *
     * @return Collection
     */
    public function getWishlists(): Collection
    {
        if (is_null($this->wishlists)) {
            $this->wishlists = new ArrayCollection();
        }

        return $this->wishlists;
    }

    /**
     * Add wishlistsLiked.
     *
     * @param Wishlist $wishlistsLiked
     *
     * @return User
     */
    public function addWishlistsLiked(Wishlist $wishlistsLiked): self
    {
        $this->wishlistsLiked->add($wishlistsLiked);

        return $this;
    }

    /**
     * Remove wishlistsLiked.
     *
     * @param Wishlist $wishlistsLiked
     *
     * @return User
     */
    public function removeWishlistsLiked(Wishlist $wishlistsLiked): self
    {
        $this->wishlistsLiked->removeElement($wishlistsLiked);

        return $this;
    }

    /**
     * Get wishlistsLiked.
     *
     * @return Collection
     */
    public function getWishlistsLiked(): Collection
    {
        if (is_null($this->wishlistsLiked)) {
            $this->wishlistsLiked = new ArrayCollection();
        }

        return $this->wishlistsLiked;
    }

    /**
     * Add review.
     *
     * @param Review $review
     *
     * @return User
     */
    public function addReview(Review $review): self
    {
        $this->reviews->add($review);

        return $this;
    }

    /**
     * Remove review.
     *
     * @param Review $review
     *
     * @return User
     */
    public function removeReview(Review $review): self
    {
        $this->reviews->removeElement($review);

        return $this;
    }

    /**
     * Get reviews.
     *
     * @return Collection
     */
    public function getReviews(): Collection
    {
        if (is_null($this->reviews)) {
            $this->reviews = new ArrayCollection();
        }

        return $this->reviews;
    }


    /**
     * Set height.
     *
     * @param int $height
     *
     * @return User
     */
    public function setHeight($height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set weight.
     *
     * @param int $weight
     *
     * @return User
     */
    public function setWeight($weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight.
     *
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }
}
