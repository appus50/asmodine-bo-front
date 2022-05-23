<?php

namespace Asmodine\FrontBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class AuthenticationSuccessHandler.
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var UrlMatcherInterface
     */
    private $router;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * AuthenticationSuccessHandler constructor.
     *
     * @param SessionInterface              $session
     * @param UrlMatcherInterface           $router
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(SessionInterface $session, UrlMatcherInterface $router, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->session = $session;
        $this->router = $router;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @see AuthenticationSuccessHandlerInterface::onAuthenticationSuccess()
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return RedirectResponse|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $response = null;
        $this->session->set('asmo_user_id', $token->getUser()->getId());

        if ($request->isXmlHttpRequest()) {
            return new Response();
        }

        $referer = $request->get('_referer', $request->headers->get('referer'));
        $url = null;
        if ($referer && '/login' !== substr($referer, strlen($referer) - 6)) {
            $url = $referer;
        }
        if (is_null($url)) {
            $url = $this->router->generate('asmodinefront_main_home');
        }

        return new RedirectResponse($url);
    }
}
