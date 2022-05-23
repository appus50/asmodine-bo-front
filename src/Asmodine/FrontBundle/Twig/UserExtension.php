<?php
/**
 * Created by PhpStorm.
 * User: appus
 * Date: 29/08/2017
 * Time: 10:28.
 */

namespace Asmodine\FrontBundle\Twig;

use Asmodine\FrontBundle\Entity\PhysicalProfile;
use Asmodine\FrontBundle\Entity\User;
use Asmodine\FrontBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class UserExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserExtension constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository('AsmodineFrontBundle:User');
    }

    /**
     * @see \Twig_Extension::getFilters()
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('current_profile', [$this, 'getCurrentProfile']),
            new \Twig_SimpleFilter('image', [$this, 'getImage']),
            new \Twig_SimpleFilter('currency', [$this, 'showPriceWithCurrency']),
            new \Twig_SimpleFilter('note_color', [$this, 'getColorNote']),
            new \Twig_SimpleFilter('sort_alpha', [$this, 'getSortAlpha']),
        ];
    }

    /**
     * @param User $user
     *
     * @return PhysicalProfile|null
     */
    public function getCurrentProfile(User $user): ?PhysicalProfile
    {
        return $this->entityManager
            ->getRepository('AsmodineFrontBundle:PhysicalProfile')
            ->findOneBy(['user' => $user, 'current' => true]);
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function getImage(User $user): string
    {
        //TODO
        return '/img/user/default.jpg';
    }

    /**
     * @param float  $price
     * @param string $currency
     *
     * @return string
     */
    public function showPriceWithCurrency(float $price, string $currency): string
    {
        if ('EUR' == strtoupper($currency)) {
            return number_format($price, 2, ',', ' ').'€';
        }

        return $currency.' '.number_format($price);
    }

    /**
     * Get Note's color.
     *
     * @param float|null $note
     *
     * @return string
     */
    public function getColorNote(?float $note): string
    {
        if (is_null($note)) {
            return 'hide';
        }
        if ($note >= 2.25) {
            return 'green';
        }
        if ($note >= 1.87) {
            return 'yellow';
        }
        if ($note >= 0) {
            return 'red';
        }

        return 'hide';
    }

    /**
     * @param array $hash
     *
     * @return array
     */
    public function getSortAlpha(array $hash): array
    {
        ksort($hash);

        return $hash;
    }
}
