<?php

namespace Asmodine\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MorphoFirstType.
 */
class MorphoFirstType extends AbstractType
{
    /**
     * @see AbstractType::buildForm()
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('height', NumberType::class, [
            'attr' => [
                'placeholder' => 'profile.step.title_measure.height',
                'min' => 0,
                'max' => 300,
                'step' => '0.1',
            ],
            'label' => false,
            'scale' => 1,
            'data' => $options['data']['height'],
        ]);

        $builder->add('weight', NumberType::class, [
            'attr' => [
                'placeholder' => 'profile.step.title_measure.weight',
                'min' => 0,
                'max' => 300,
                'step' => '0.1',
            ],
            'scale' => 1,
            'label' => false,
            'data' => $options['data']['weight'],
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'form.submit',
        ]);
    }

    /**
     * @see AbstractType::getBlockPrefix()
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'morphoprofile_steps';
    }
}
