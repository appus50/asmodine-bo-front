<?php

namespace Asmodine\FrontBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RegistrationType.
 */
class SubscriptionType extends RegistrationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('role')
            ->remove('username')
            ->remove('address')
            ->remove('zip_code')
            ->remove('phone_number')
            ->remove('current_password');
    }
}
