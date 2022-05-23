<?php

namespace Asmodine\FrontBundle\Entity\Listener;

use Asmodine\CommonBundle\Model\Morphoprofile\Morphoprofile;
use Asmodine\CommonBundle\Model\Morphoprofile\Size;
use Asmodine\CommonBundle\Model\Morphoprofile\Weight as MorphoWeight;
use Asmodine\CommonBundle\Model\Morphotype\Morphotype;
use Asmodine\FrontBundle\Entity\PhysicalProfile;
use Asmodine\FrontBundle\Service\BackApiService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class PhysicalProfileListener.
 */
class PhysicalProfileListener extends ORM\DefaultEntityListenerResolver
{
    /**
     * @var BackApiService
     */
    private $backApiService;

    /**
     * @var Session
     */
    private $session;


    /**
     * PhysicalProfileListener constructor.
     *
     * @param BackApiService $backApiService
     */
    public function __construct(BackApiService $backApiService,Session $session)
    {
        $this->backApiService = $backApiService;
        $this->session= $session;
    }

    /**
     * Use HasLifecycleCallbacks.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     *
     * @param PhysicalProfile    $physicalProfile
     * @param LifecycleEventArgs $event
     *
     * @throws \Asmodine\CommonBundle\Exception\EnumParameterException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function preUpdate(PhysicalProfile $physicalProfile, LifecycleEventArgs $event)
    {
        $gender = null;
        $user = $physicalProfile->getUser();
        if ($this->session->get('temporary')){
            $this->session->set('temporary', false);
        }
        if (!is_null($user)) {
            $gender = $user->getGender();
        }

        $size = Size::get($gender, $physicalProfile->getHeight() * 10);
        $morphotype = Morphotype::get($physicalProfile->getSkin(), $physicalProfile->getHair(), $physicalProfile->getEyes());
        $morphoweight = MorphoWeight::get($gender, $physicalProfile->getHeight() * 10, $physicalProfile->getShoulder() * 10, $physicalProfile->getChest() * 10, $physicalProfile->getWaist() * 10, $physicalProfile->getHip() * 10);
        $morphoprofile = Morphoprofile::get($gender, $physicalProfile->getShoulder() * 10, $physicalProfile->getWaist2D() * 10, $physicalProfile->getHip2D() * 10, $morphoweight);

        $physicalProfile->setSize($size);
        $physicalProfile->setMorphotype($morphotype);
        $physicalProfile->setMorphoWeight($morphoweight);
        $physicalProfile->setMorphoprofile($morphoprofile);

        $this->checkCurrent($physicalProfile, $event->getEntityManager());
    }

    /**
     * Send PhysicalProfile To Back.
     *
     * @ORM\PostPersist
     * @ORM\PostUpdate
     *
     * @param PhysicalProfile    $physicalProfile
     * @param LifecycleEventArgs $event
     */
    public function postPersist(PhysicalProfile $physicalProfile, LifecycleEventArgs $event)
    {
        $this->backApiService->updatePhysicalProfile($physicalProfile);
    }

    /**
     * In a new version, all the others physical profile must be set to false.
     *
     * @param PhysicalProfile $physicalProfile
     * @param EntityManager   $em
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    private function checkCurrent(PhysicalProfile $physicalProfile, EntityManager $em)
    {
        if ($physicalProfile->getCurrent()) {
            $physicalProfiles = $em->getRepository('AsmodineFrontBundle:PhysicalProfile')->findBy(['user' => $physicalProfile->getUser()]);
            /** @var PhysicalProfile $aPhysicalProfile */
            foreach ($physicalProfiles as $aPhysicalProfile) {
                if ($physicalProfile->getId() !== $aPhysicalProfile->getId() && $aPhysicalProfile->getCurrent()) {
                    $aPhysicalProfile->setCurrent(false);
                    $em->persist($aPhysicalProfile);
                    $em->flush();
                }
            }
        }
    }
}
