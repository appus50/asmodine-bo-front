<?php

namespace Asmodine\FrontBundle\Controller\FOSUser;

use Asmodine\FrontBundle\Controller\UserController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\Controller\SecurityController as FOSSecurityController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController.
 *
 * @Route("/security")
 */
class SecurityController extends FOSSecurityController
{
    /** @var bool $mobile */
    public $mobile = false;

    /**
     * Override to use in a modal.
     *
     * @Route("/login", name="asmodinefront_fosuser_security_login")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        return parent::loginAction($request);
    }

    /**
     * Override to use in a modal.
     *
     * @Route("/login_mobile", name="asmodinefront_fosuser_security_login_mobile")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginMobileAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('asmodinefront_main_home');
        }
        $this->mobile = true;

        return parent::loginAction($request);
    }

    /**
     * Override to use in a modal.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        if ($this->mobile || count($this->get('session')->get(UserController::APPLICATION_DATAS, [])) > 0) {
            return $this->render('AsmodineFrontBundle:FOSUser:Security/login_mobile.html.twig', $data);
        }

        return $this->render('AsmodineFrontBundle:FOSUser:Security/login.html.twig', $data);
    }
}
