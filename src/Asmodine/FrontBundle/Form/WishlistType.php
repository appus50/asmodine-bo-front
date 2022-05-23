<?php

namespace Asmodine\FrontBundle\Form;

use Asmodine\FrontBundle\Entity\Wishlist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WishlistType.
 */
class WishlistType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                'label' => false,
                'attr' => [
                    'placeholder' => 'form.wishlist.name',
                    'class' => 'form-control',
                ],
            ]
            )
            ->add(
                'image',
                FileType::class,
                [
                'label' => 'form.common.image.add',
                'label_attr' => ['class' => 'file-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ]
            )
         //TODO   ->add('models')
            ->add(
                'submit',
                SubmitType::class,
                [
                'label' => 'form.wishlist.submit',
                'attr' => [
                    'class' => 'w-100 btn cta2',
                ],
            ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Wishlist::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wishlist';
    }
}
