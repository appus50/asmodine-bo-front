<?php

namespace Asmodine\FrontBundle\Form;

use Asmodine\FrontBundle\Entity\User;
use FOS\UserBundle\Form\Type\RegistrationFormType as FOSUserRegistrationFormType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * Class TempProfileType.
 */
class TempProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        $test= FOSUserRegistrationFormType::class;
        return $test;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'gender',
                ChoiceType::class,
                [
                    'label' => false,
                    'attr' => [
                        'class' => 'gender-choice',
                    ],
                    'choices' => [
                        'form.user.male' => User::GENDER_MALE,
                        'form.user.female' => User::GENDER_FEMALE,
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ]
            )
            ->add(
                'birthdate',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'label' => 'form.user.birthdate',
                    'attr' => ['placeholder' => 'form.user.birthdate', 'class' => 'form-control'],
                ]
            )
            ->add(
                'height',
                NumberType::class,
                [
                    'mapped' => false,
                    'label' => 'form.user.height',
                    'attr' => [
                        'placeholder' => 'form.user.height',
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'weight',
                NumberType::class,
                [
                    'mapped' => false,
                    'label' => 'form.user.weight',
                    'attr' => [
                        'placeholder' => 'form.user.weight',
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'email',
                RepeatedType::class,
                [
                    'type' => EmailType::class,
                    'required' => true,
                    'first_options' => [
                        'label' => 'form.user.email',
                        'attr' => [
                            'placeholder' => 'form.email',
                            'class' => 'form-control',
                        ],
                    ],
                    'second_options' => [
                        'label' => 'form.user.email_confirmation',
                        'attr' => [
                            'placeholder' => 'form.user.email_confirmation',
                            'class' => 'form-control',
                        ],
                    ],
                    'invalid_message' => 'form.user.email_different',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'form.submit',
                    'attr' => ['class' => 'w-100 btn cta2'],
                ]
            )
            ->remove('plainPassword')
            ->remove('username');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'constraints' => [
                    new UniqueEntity('email'),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user_registration';
    }
}
