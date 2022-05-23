<?php

namespace Asmodine\FrontBundle\Controller;

use Asmodine\CommonBundle\Controller\Controller as AsmodineController;
use Asmodine\CommonBundle\Model\Morphoprofile\Gender;
use Asmodine\FrontBundle\Entity\PhysicalProfile;
use Asmodine\FrontBundle\Entity\User;
use Asmodine\FrontBundle\Form\PhysicalProfileType;
use Asmodine\FrontBundle\Form\SubscriptionType;
use Asmodine\FrontBundle\Form\TempProfileType;
use Asmodine\FrontBundle\Service\EmailService;
use Asmodine\FrontBundle\Entity\Listener\PhysicalProfileListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class PageController.
 *
 * @Route("/mon-espace")
 */
class UserController extends AsmodineController
{
    const APPLICATION_DATAS = 'APPLI_DATAS';

    /**
     * @Route("/activate/{id}/token/{token}", name="asmodinefront_user_activate")
     * @ParamConverter("user", class="AsmodineFrontBundle:User")
     *
     * @param User   $user
     * @param string $token
     *
     * @return RedirectResponse
     */
    public function activateAction(User $user, string $token)
    {
        if (($token != $user->getConfirmationToken()) || $user->isEnabled()) {
            return new RedirectResponse($this->generateUrl('fos_user_security_login'));
        }
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'public', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $session = $this->get('session');
        $session->set('activated', 'true');
        $user->setEnabled(true);
        $this->persist($user, true);

        $appDatas = $this->get('session')->get(self::APPLICATION_DATAS, []);
        if (count($appDatas) > 0) {
            $this->addFlash('success', 'Merci de compléter votre profil pour profiter pleinement d\'Asmodine.');

            return $this->redirectToRoute('asmodinefront_morpho_step', ['mode' => 'full', 'step' => 'skin']);
        }

        return new RedirectResponse($this->generateUrl('asmodinefront_morpho_step'));
    }

    /**
     * Return json view of reset password form.
     *
     * @Route("/reset-password", name="asmodinefront_user_resetpassword")
     *
     * @param Request $request
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['html' => $this->renderView('AsmodineFrontBundle:User:reset-password.html.twig')]);
        }

        return $this->render('AsmodineFrontBundle:User:reset-password.html.twig');
    }

    /**
     * Add a new user.
     *
     * @Route("/subscription", name="asmodinefront_user_subscription")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function subscriptionAction(Request $request)
    {
        $user = new User();

        return $this->manageRegistrationForm($request, $user, false);
    }

    /**
     * Add a new user.
     *
     * @Route("/tempsubscription", name="asmodinefront_user_tempsubscription")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function tempsubscriptionAction(Request $request)
    {
        $user = new User();

        return $this->manageTempProfileForm($request, $user, false);
    }



    /**
     * Add a new user.
     *
     * @Route("/pre-subscription", name="asmodinefront_user_pre_subscription")
     * @Template()
     * @return array
     */
    public function preSubscriptionAction()
    {
        return [];
    }

    /**
     * @Route("/proposal_subscription", name="asmodinefront_user_proposal")
     * @Template()
     */
    public function proposalSubscriptionAction()
    {
        return [];
    }

    /**
     * @Route("/temp_subscription", name="asmodinefront_user_temp")
     * @Template()
     * @return array
     */
    public function tempAction()
    {
        return [];
    }

    /**
     * @Route("/end_subscription", name="asmodinefront_user_end")
     * @Template()
     * @return array
     */
    public function endAction()
    {
        return [];
    }

    /**
     * Proposition de création de profil par l'appli ou le site
     *
     * @Route("/pre_subscription_mobile", name="asmodinefront_user_presubscription_mobile")
     * @Template()
     *
     * @return array
     */
    public function preSubscriptionMobileAction()
    {
        return [];
    }



    /**
     * Add a new user.
     *
     * @Route("/subscription_mobile", name="asmodinefront_user_subscription_mobile")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function subscriptionMobileAction(Request $request)
    {
        $user = new User();

        return $this->manageRegistrationForm($request, $user, true);
    }

    /**
     * Show the profile.
     *
     * @Route("/mon-profil/{tab}", name="asmodinefront_user_profile", defaults={"tab" = "profile"})
     * @Security("has_role('ROLE_USER')")
     * @Template
     *
     * @param string $tab
     *
     * @return array|RedirectResponse
     */
    public function profileAction(string $tab)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var PhysicalProfile $currentProfile */
        $currentProfile = $this->getManager()
            ->getRepository('AsmodineFrontBundle:PhysicalProfile')
            ->findOneBy(['user' => $user, 'current' => true]);
        if (is_null($currentProfile)) {
            $currentProfile = PhysicalProfile::create($user);
        }
        if (0 == $currentProfile->getPercentageOfFilling()) {
            return $this->redirect($this->generateUrl('asmodinefront_morpho_step'));
        }

        return ['tab' => $tab];
    }

    /**
     * Show the profile.
     *
     * @Route("/_profile", name="asmodinefront_user_profilefragment")
     * @Security("has_role('ROLE_USER')")
     * @Template
     */
    public function profilefragmentAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var PhysicalProfile $currentProfile */
        $currentProfile = $this->getManager()
            ->getRepository('AsmodineFrontBundle:PhysicalProfile')
            ->findOneBy(['user' => $user, 'current' => true]);
        if (is_null($currentProfile)) {
            $currentProfile = PhysicalProfile::create($user);
        }

        $appDatas = $this->get('session')->get(self::APPLICATION_DATAS, []);
        if (count($appDatas) > 0) {
            $this->get('session')->remove(self::APPLICATION_DATAS);
            $currentProfile->setApplicationDatas($appDatas);
            $this->persist($currentProfile, true);
        }

        $formUser = $this->createForm(SubscriptionType::class, $user, ['action' => $this->generateUrl('asmodinefront_user_profilefragment')]);
        $formUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $user = $formUser->getData();
            $this->persist($user, true);

            return $this->redirect($this->generateUrl('asmodinefront_user_profile'));
        }

        $formMorpho = $this->createForm(PhysicalProfileType::class, $currentProfile, ['action' => $this->generateUrl('asmodinefront_user_profilefragment')]);
        if (Gender::isMale($user->getGender())) {
            $formMorpho->remove('bra');
        }
        $formMorpho->handleRequest($request);
        if ($formMorpho->isSubmitted() && $formMorpho->isValid()) {
            $currentProfile = $formMorpho->getData();
            $this->persist($currentProfile, true);

            return $this->redirect($this->generateUrl('asmodinefront_user_profile'));
        }

        if ($formUser->isSubmitted() || $formMorpho->isSubmitted()) {
            //    print_r($formUser->getErrors());
        //TODO
         //   die();
        }

        return [
            'form_user' => $formUser->createView(),
            'form_profile' => $formMorpho->createView(),
            'profile_percent' => $currentProfile->getPercentageOfFilling($formMorpho->count()),
        ];
    }

    /**
     * @Route("/application",name="asmodinefront_user_application")
     *
     * @return RedirectResponse
     */
    public function applicationAction(Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');

        $queries = $request->query->all();
        $session->set(self::APPLICATION_DATAS, $queries);

        return $this->redirectToRoute('asmodinefront_user_profile');
    }

    /**
     * Gestion du formulaire d'inscription.
     *
     * @param Request $request
     * @param User    $user
     * @param bool    $forceTemplate
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function manageRegistrationForm(Request $request, User $user, bool $forceTemplate)
    {
        $form = $this->createForm(SubscriptionType::class, $user, ['action' => $this->generateUrl($forceTemplate ? 'asmodinefront_user_subscription_mobile' : 'asmodinefront_user_subscription')]);

        $template = $forceTemplate ? 'AsmodineFrontBundle:User:form.registration_full.html.twig' : 'AsmodineFrontBundle:User:form.registration.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $token = sha1(mt_rand(10000, 99999).time());
            $user->setEnabled(false);
            $user->setConfirmationToken($token);
            $user->setUsername($user->getEmail());
            $user->setRoles([User::ROLE_DEFAULT]);
            /*if (!$user->getImage()) {
                $user->setImage($this->default_user_image);
            }*/
            $this->persist($user, true);

            // Shunt Application
            if (count($this->get('session')->get(self::APPLICATION_DATAS, [])) > 0) {
                $activate = $this->generateUrl('asmodinefront_user_activate', ['id' => $user->getId(), 'token' => $user->getConfirmationToken()]);
                $success = true;
                $forceTemplate = true;
            } else {
                $activate = null;
                /** @var EmailService $emailService */
                $emailService = $this->get('asmodine.front.email');
                $success = $emailService->sendRegistration($user);
            }

            return new JsonResponse(['html' => $this->renderView($forceTemplate ? 'AsmodineFrontBundle:User:form.registration_end_mobile.html.twig' : 'AsmodineFrontBundle:User:form.registration_end.html.twig', ['success' => $success, 'activate' => $activate])]);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['html' => $this->renderView($template, ['form' => $form->createView()])]);
        }

        return $this->render($template, ['form' => $form->createView()]);
    }

    // Génération d'une chaine aléatoire
    private function chaine_aleatoire($nb_car, $chaine = 'azertyuiopqsdfghjklmwxcvbn123456789')
    {
        $nb_lettres = strlen($chaine) - 1;
        $generation = '';
        for($i=0; $i < $nb_car; $i++)
        {
            $pos = mt_rand(0, $nb_lettres);
            $car = $chaine[$pos];
            $generation .= $car;
        }
        return $generation;
    }

    /**
     * Gestion du formulaire d'inscription.
     *
     * @param Request $request
     * @param User    $user
     * @param bool    $forceTemplate
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    private function manageTempProfileForm(Request $request, User $user, bool $forceTemplate)
    {
        $form = $this->createForm(TempProfileType::class, $user, ['action' => $this->generateUrl($forceTemplate ? 'asmodinefront_user_subscription_mobile' : 'asmodinefront_user_tempsubscription')]);

        $template =  'AsmodineFrontBundle:User:form.tempregistration.html.twig';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $token = sha1(mt_rand(10000, 99999).time());
            $user->setEnabled(false);
            $user->setConfirmationToken($token);
            $user->setUsername($user->getEmail());
            $user->setRoles([User::ROLE_DEFAULT]);
            $h=$form->getData();
            $pass=$this->chaine_aleatoire(6);
            $user->setPlainPassword($pass);
            $height= (int) $_POST["user_registration"]["height"] *10;
            $weight= $_POST["user_registration"]["weight"];
            $mail= $_POST["user_registration"]["email"]["first"];
            $this->persist($user, true);
            $g=$user->getGender();

            $sql = "SELECT user_id FROM front_orm_physical_profile JOIN front_orm_user on (front_orm_physical_profile.user_id=front_orm_user.id) WHERE gender='$g' and height is not null and weight is not null and neck is not null and waist_top is not null and waist_bottom is not null and chest is not null and hip is not null and hip_2d is not null and inside_leg is not null and shoulder is not null and waist is not null and waist_2d is not null and size is not null and morphotype is not null and morphoprofile is not null and morpho_weight is not null order by ABS(height-'$height')+ABS(weight-'$weight') limit 1";
            $connection = $this->getDoctrine()->getManager()->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('depth', 1);
            $result=$statement->execute();
            $value =( int ) $statement->fetch()["user_id"];





            $sql = "SELECT id FROM front_orm_user WHERE email='$mail' ";
            $connection = $this->getDoctrine()->getManager()->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('depth', 1);
            $result=$statement->execute();
            $value2 =( int ) $statement->fetch()["id"];
            $created =date("Y-m-d H:i:s");
            $updated = date("Y-m-d H:i:s");


            $sql = "SELECT * FROM front_orm_physical_profile WHERE user_id = '$value' limit 1";
            $connection = $this->getDoctrine()->getManager()->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('depth', 1);
            $result=$statement->execute();
            $profi =$statement->fetch();
            $waist_2d=$profi["waist_2d"];
            $hip_2d=$profi["hip_2d"];
            $arm=$profi["arm"];
            $chest=$profi["chest"];
            $calf=$profi["calf"];
            $foot_length=$profi["foot_length"];
            $foot_width=  $profi["foot_width"];
            $head=  $profi["head"];
            $hip=  $profi["hip"];
            $inside_leg=  $profi["inside_leg"];
            $neck=  $profi["neck"];
            $shoulder=  $profi["shoulder"];
            $thigh=  $profi["thigh"];
            $waist=  $profi["waist"];
            $wrist=  $profi["wrist"];
            $skin=  $profi["skin"];
            $hair=  $profi["hair"];
            $eyes=  $profi["eyes"];
            $size=  $profi["size"];
            $morphotype=  $profi["morphotype"];
            $morphoprofile=  $profi["morphoprofile"];
            $morpho_weight=  $profi["morpho_weight"];
            $leg_length= $profi["leg_length"] ? $profi["leg_length"]: NULL ;
            $waist_top= $profi["waist_top"] ? $profi["waist_top"] : NULL ;
            $waist_bottom= $profi["waist_bottom"] ? $profi["waist_bottom"] : NULL ;
            $arm_spine= $profi["arm_spine"] ? $profi["arm_spine"] : NULL ;


            $sql = "INSERT INTO front_orm_physical_profile (user_id,current,created_at,updated_at) VALUES ('$value2',1,'$created','$updated')";
            $connection = $this->getDoctrine()->getManager()->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('depth', 1);
            $result=$statement->execute();

            /*
            $sql = "UPDATE front_orm_physical_profile
            SET waist_2d= '$profi[waist_2d]',
            hip_2d='$profi[hip_2d]',
            arm='$profi[arm]',
            chest='$profi[chest]',
            calf='$profi[calf]',
            foot_length='$profi[foot_length]',
            foot_width=  '$profi[foot_width]',
            head=  '$profi[head]',
            hip=  '$profi[hip]',
            inside_leg=  '$profi[inside_leg]',
            neck=  '$profi[neck]',
            shoulder=  '$profi[shoulder]',
            thigh=  '$profi[thigh]',
            waist=  '$profi[waist]',
            wrist=  '$profi[wrist]',
            size=  '$profi[size]',
            morphotype=  '$profi[morphotype]',
            morphoprofile=  '$profi[morphoprofile]',
            morpho_weight=  '$profi[morpho_weight]',
            leg_length= '$profi[leg_length]',
            waist_top=  '$profi[waist_top]',
            waist_bottom=  '$profi[waist_bottom]',
            arm_spine= '$profi[arm_spine]'
            WHERE user_id = '$value2'";
            */
            $sql = "UPDATE front_orm_physical_profile
            SET waist_2d= '$profi[waist_2d]',
            hip_2d='$profi[hip_2d]',
            height='$height',
            weight='$weight',
            chest='$profi[chest]',
            hip=  '$profi[hip]',
            inside_leg=  '$profi[inside_leg]',
            shoulder=  '$profi[shoulder]',
            waist=  '$profi[waist]',
            neck=  '$profi[neck]',
            waist_top=  '$profi[waist_top]',
            waist_bottom=  '$profi[waist_bottom]',
            size=  '$profi[size]',
            morphotype=  '$profi[morphotype]',
            morphoprofile=  '$profi[morphoprofile]',
            morpho_weight=  '$profi[morpho_weight]'
            WHERE user_id = '$value2'";

            $connection = $this->getDoctrine()->getManager()->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('depth', 1);
            $result=$statement->execute();
            /*if (!$user->getImage()) {
                $user->setImage($this->default_user_image);
            }*/
            // Shunt Application

            $activate = null;
            /** @var EmailService $emailService */
            $emailService = $this->get('asmodine.front.email');
            $success = $emailService->sendRegistrationTemp($user,$pass);
            $test= "" ? "oui" : "nn";
            $token = new UsernamePasswordToken($user, $user->getPassword(), 'public', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $session = $this->get('session');
            $session->set('activated', 'true');
            $session->set('temporary', true);
            $session->set('tempUser',  $value);
            $session->set('tempProfile',  $profi);
            $session->set('created', $waist_bottom);
            $session->set('newID', $value2);
            $session->set('tmpR', true);

            $user->setEnabled(true);
            $user->setFastProfile( new \DateTime());
            $this->persist($user, true);
            $currentProfile = $this->getManager()
                ->getRepository('AsmodineFrontBundle:PhysicalProfile')
                ->findOneBy(['user' => $user, 'current' => true]);
            $this->get('asmodine.front.back_api')->updatePhysicalProfile($currentProfile);
            $this->persist($user, true);

            //return new JsonResponse(['html' => $this->renderView($forceTemplate ? 'AsmodineFrontBundle:User:form.registration_end_mobile.html.twig' : 'AsmodineFrontBundle:User:form.registration_end.html.twig', ['success' => $success, 'activate' => $activate])]);
            //return $this->renderView('AsmodineFrontBundle:User:form.registration_end.html.twig', ['success' => $success, 'activate' => $activate]);
            return $this->redirectToRoute('asmodinefront_main_home');
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['html' => $this->renderView($template, ['form' => $form->createView()])]);
        }
        return $this->render($template, ['form' => $form->createView()]);
    }
}
