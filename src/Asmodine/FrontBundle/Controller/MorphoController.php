<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\CommonBundle\Model\Morphoprofile\Gender;
use Asmodine\CommonBundle\Model\Morphotype\Eye;
use Asmodine\CommonBundle\Model\Morphotype\Hair;
use Asmodine\CommonBundle\Model\Morphotype\Skin;
use Asmodine\CommonBundle\Model\Profile\Body;
use Asmodine\CommonBundle\Util\FileUtils;
use Asmodine\CommonBundle\Util\Str;
use Asmodine\FrontBundle\Entity\PhysicalProfile;
use Asmodine\FrontBundle\Entity\User;
use Asmodine\FrontBundle\Form\MorphoFirstType;
use Asmodine\FrontBundle\Form\MorphoSingleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MorphoprofileController.
 *
 * @Route("/morpho")
 */
class MorphoController extends AsmodineController
{
    /**
     * @Route("/etape/{mode}/{step}", name="asmodinefront_morpho_step", defaults={"mode" = "light", "step" = ""})
     * @Security("has_role('ROLE_USER')")
     * @Template
     *
     * @param Request $request
     * @param string  $mode
     * @param string  $step
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Asmodine\CommonBundle\Exception\NullException
     */
    public function stepAction(Request $request, string $mode, string $step)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var PhysicalProfile $physicalProfile */
        $physicalProfile = $this->getDoctrine()
            ->getRepository('AsmodineFrontBundle:PhysicalProfile')
            ->findOneBy(['user' => $user, 'current' => true]);
        if (is_null($physicalProfile)) {
            $physicalProfile = PhysicalProfile::create($user);
        }

        $steps = $this->getSteps($user->getGender(), 'light' == $mode);
        $stepSnake = str_replace('2d', '_2d', Str::toSnakeCase($step));
        $stepSnake = str_replace('__', '_', $stepSnake);
        $position = array_search($stepSnake, $steps);
        $popup = false === $position;
        if ($popup) {
            $position = 0;
            $popup = $this->createForm(MorphoFirstType::class, [
                'height' => $physicalProfile->getHeight(),
                'weight' => $physicalProfile->getWeight(),
            ]);
            $popup->handleRequest($request);
            if ($popup->isSubmitted() && $popup->isValid()) {
                $datas = $popup->getData();
                $physicalProfile->setHeight($datas['height']);
                $physicalProfile->setWeight($datas['weight']);
                $this->persist($physicalProfile, true);
                $nextStep = $this->generateUrl('asmodinefront_morpho_step', ['mode' => 'light' == $mode ? 'light' : 'full', 'step' => $steps[0]]);

                return $this->redirect($nextStep);
            }
            $popup = $popup->createView();
        }

        $form = $this->createForm(
            MorphoSingleType::class,
            $this->getStepFormOptions($physicalProfile, $steps[$position]),
            ['action' => $this->generateUrl('asmodinefront_morpho_step', ['mode' => 'light' == $mode ? 'light' : 'full', 'step' => $steps[$position]])]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();
            $physicalProfile->set($datas['step'], $datas['value']);
            $this->persist($physicalProfile, true);

            $nextStep = $position < count($steps) - 1
                ? $this->generateUrl('asmodinefront_morpho_step', ['mode' => 'light' == $mode ? 'light' : 'full', 'step' => $steps[$position + 1]])
                : $this->generateUrl('asmodinefront_user_profile');

            return $this->redirect($nextStep);
        }

        $video = '/videos/measure/'.$steps[$position].'_'.(Gender::isMale($user->getGender()) ? 'men' : 'women').'.mp4';
        $file = new FileUtils($this->getParameter('kernel.root_dir').'/../web/'.$video);
        $video = $file->exists() ? $video : false;

        $image = '/videos/measure/'.$steps[$position].'_'.(Gender::isMale($user->getGender()) ? 'men' : 'women').'.jpg';
        $file = new FileUtils($this->getParameter('kernel.root_dir').'/../web/'.$image);
        $image = $file->exists() ? $image : false;

        $previous_step = $position > 0 ? $this->generateUrl('asmodinefront_morpho_step', ['mode' => 'light' == $mode ? 'light' : 'full', 'step' => $steps[$position - 1]]) : false;

        return [
            'form' => $form->createView(),
            'form_popup' => $popup,
            'nb_step' => count($steps),
            'current_step' => $steps[$position],
            'current_step_position' => $position,
            'previous_step' => $previous_step,
            'light' => 'light' == $mode,
            'video' => $video,
            'image' => $image,
            'video_name' => $file->getFilename(),
        ];
    }

    /**
     * @Route("/etape_mobile/{mode}", name="asmodinefront_morpho_mobilefirststep", defaults={"mode" = "light"})
     * @Security("has_role('ROLE_USER')")
     * @Template
     *
     * @param Request $request
     * @param string  $mode
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function firststepmobileAction(Request $request, string $mode)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var PhysicalProfile $physicalProfile */
        $physicalProfile = $this->getDoctrine()
            ->getRepository('AsmodineFrontBundle:PhysicalProfile')
            ->findOneBy(['user' => $user, 'current' => true]);
        if (is_null($physicalProfile)) {
            $physicalProfile = PhysicalProfile::create($user);
        }

        $steps = $this->getSteps($user->getGender(), 'light' == $mode);
        $popup = $this->createForm(MorphoFirstType::class, [
                'height' => $physicalProfile->getHeight(),
                'weight' => $physicalProfile->getWeight(),
            ]);
        $popup->handleRequest($request);
        if ($popup->isSubmitted() && $popup->isValid()) {
            $datas = $popup->getData();
            $physicalProfile->setHeight($datas['height']);
            $physicalProfile->setWeight($datas['weight']);
            $this->persist($physicalProfile, true);
            $nextStep = $this->generateUrl('asmodinefront_morpho_step', ['mode' => 'light' == $mode ? 'light' : 'full', 'step' => $steps[0]]);

            return $this->redirect($nextStep);
        }
        $popup = $popup->createView();

        return [
            'form_popup' => $popup,
            'light' => 'light' == $mode,
        ];
    }

    /**
     * Get Form Options.
     *
     * @param PhysicalProfile $physicalProfile
     * @param string          $step
     *
     * @return array
     *
     * @throws \Asmodine\CommonBundle\Exception\NullException
     */
    private function getStepFormOptions(PhysicalProfile $physicalProfile, string $step)
    {
        if (!in_array($step, ['skin', 'eyes', 'hair'])) {
            return ['step' => $step, 'data' => $physicalProfile->get($step), 'color' => false];
        }
        if ('skin' == $step) {
            return ['step' => $step, 'data' => $physicalProfile->get($step), 'color' => true, 'choices' => Skin::getSlugs(), 'type' => 'skin'];
        }
        if ('eyes' == $step) {
            return ['step' => $step, 'data' => $physicalProfile->get($step), 'color' => true, 'choices' => Eye::getSlugs(), 'type' => 'eyes'];
        }
        if ('hair' == $step) {
            return ['step' => $step, 'data' => $physicalProfile->get($step), 'color' => true, 'choices' => Hair::getSlugs(), 'type' => 'hair'];
        }
    }

    /**
     * Get Steps.
     *
     * @param string $gender
     * @param bool   $isLight
     *
     * @return array
     */
    private function getSteps(string $gender, bool $isLight)
    {
        $steps = [
            Body::NECK,
            Body::SHOULDER,
            Body::CHEST,
            Body::BRA,
            Body::ARM,
            Body::ARM_SPINE,
            Body::ARM_TURN,
            Body::WRIST,
            Body::WAIST_TOP,
            Body::WAIST,
            Body::WAIST_BOTTOM,
            'waist_2d',
            Body::HIP,
            'hip_2d',
            Body::THIGH,
            Body::CALF,
            Body::INSIDE_LEG,
            Body::LEG_LENGTH,
            Body::HEAD,
            Body::FOOT_LENGTH,
            Body::FOOT_WIDTH,
            //Body::SHOULDER_TO_HIP,
            //Body::HOLLOW_TO_FLOOR,
            'skin',
            'eyes',
            'hair',
        ];
        if (Gender::isMale($gender)) {
            $steps = array_values(array_diff(
                $steps,
                [Body::BRA, Body::HOLLOW_TO_FLOOR]
            ));
        }
        if (!$isLight) {
            return array_values($steps);
        }

        return array_values(array_diff(
            $steps,
            [Body::THIGH, Body::FOOT_LENGTH, Body::FOOT_WIDTH, Body::HEAD, Body::WRIST, Body::BRA, Body::CALF, Body::ARM_SPINE, Body::ARM_TURN]
        ));
    }
}
