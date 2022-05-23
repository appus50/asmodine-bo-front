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

/**
 * Class RegistrationType.
 */
class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FOSUserRegistrationFormType::class;
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
                'firstname',
                null,
                [
                    'label' => 'form.user.firstname',
                    'attr' => [
                        'placeholder' => 'form.user.firstname',
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'lastname',
                null,
                [
                    'label' => 'form.user.lastname',
                    'attr' => [
                        'placeholder' => 'form.user.lastname',
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'options' => [
                        'translation_domain' => 'FOSUserBundle',
                    ],
                    'first_options' => [
                        'label' => 'form.password',
                        'attr' => [
                            'placeholder' => 'form.password',
                            'class' => 'form-control',
                        ],
                    ],
                    'second_options' => [
                        'label' => 'form.password_confirmation',
                        'attr' => [
                            'placeholder' => 'form.password_confirmation',
                            'class' => 'form-control',
                        ],
                    ],
                    'invalid_message' => 'fos_user.password.mismatch',
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
                'address',
                null,
                [
                    'label' => 'form.user.address',
                    'attr' => [
                        'placeholder' => 'form.user.address',
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'zip_code',
                null,
                [
                    'label' => 'form.user.zip_code',
                    'attr' => [
                        'placeholder' => 'form.user.zip_code',
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'city',
                null,
                [
                    'label' => 'form.user.city',
                    'attr' => [
                        'placeholder' => 'form.user.city',
                        'class' => 'form-control',
                    ],
                ]
            )
            ->add(
                'phone_number',
                null,
                [
                    'label' => 'form.user.phone_number',
                    'attr' => [
                        'placeholder' => 'form.user.phone_number',
                        'class' => 'form-control',
                    ],
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
                'submit',
                SubmitType::class,
                [
                    'label' => 'form.submit',
                    'attr' => ['class' => 'w-100 btn cta2'],
                ]
            );
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
        return 'user_tempprofile';
    }
}
