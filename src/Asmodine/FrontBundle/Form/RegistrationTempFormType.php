<?php

namespace Asmodine\FrontBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as FOSUserRegistrationFormType;

/**
 * Class RegistrationType.
 */
class RegistrationTempFormType extends FOSUserRegistrationFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('plainPassword');
    }
}
