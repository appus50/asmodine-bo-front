<?php

namespace Asmodine\FrontBundle\Service;

use Asmodine\CommonBundle\Service\EmailService as BaseEmailService;
use Asmodine\FrontBundle\Entity\User;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EmailService
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var BaseEmailService
     */
    private $commonService;

    /**
     * EmailService constructor.
     *
     * @param BaseEmailService    $commonService
     * @param EngineInterface     $templating
     * @param TranslatorInterface $translator
     */
    public function __construct(BaseEmailService $commonService, EngineInterface $templating, TranslatorInterface $translator)
    {
        $this->commonService = $commonService;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    /**
     * Send Email Registration.
     *
     * @param User $user
     *
     * @return bool
     */
    public function sendRegistration(User $user): bool
    {
        $subject = $this->translator->trans('email.registration.subject');
        $htmlTemplate = 'AsmodineFrontBundle:_Mail:registration.html.twig';
        $txtTemplate = 'AsmodineFrontBundle:_Mail:registration.txt.twig';
        $parameters = [
            'user_id' => $user->getId(),
            'token' => $user->getConfirmationToken(),
            'user_firstname' => $user->getFirstname(),
        ];
        $htmlBody = $this->templating->render($htmlTemplate, $parameters);
        $txtBody = $this->templating->render($txtTemplate, $parameters);

        $nbMail = $this->commonService->send($subject, $user->getEmail(), $user->getFirstname().' '.$user->getLastname(), $htmlBody, $txtBody);

        return 1 === $nbMail;
    }

    /**
     * Send Email Registration.
     *
     * @param User $user
     *
     * @return bool
     */
    public function sendRegistrationTemp(User $user,$pass): bool
    {
        $subject = $this->translator->trans('email.registration.subject');
        $htmlTemplate = 'AsmodineFrontBundle:_Mail:registrationTemp.html.twig';
        $txtTemplate = 'AsmodineFrontBundle:_Mail:registrationTemp.txt.twig';
        $parameters = [
            'user_id' => $user->getId(),
            'token' => $user->getConfirmationToken(),
            'pass' => $pass,
        ];
        $htmlBody = $this->templating->render($htmlTemplate, $parameters);
        $txtBody = $this->templating->render($txtTemplate, $parameters);

        $nbMail = $this->commonService->send($subject, $user->getEmail(), $user->getFirstname().' '.$user->getLastname(), $htmlBody, $txtBody);

        return 1 === $nbMail;
    }
}
