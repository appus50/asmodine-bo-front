<?php

namespace Asmodine\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MorphoSingleType.
 */
class MorphoSingleType extends AbstractType
{
    const MIN_VALUE = 1;
    const MAX_VALUE = 300;

    /**
     * @see AbstractType::buildForm()
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['data']['color']) {
            $builder->add('value', NumberType::class, [
            'attr' => [
                'placeholder' => 'profile.step.title_measure.'.$options['data']['step'],
                'max' => self::MAX_VALUE,
                'min' => self::MIN_VALUE,
                'step' => '0.1',
                'class' => 'input-bordered form-control',
            ],
            'label' => false,
            'scale' => 1,
            'data' => $options['data']['data'],
        ]);
        }
        if ($options['data']['color']) {
            $builder->add('value', ChoiceType::class, [
                'label' => 'profile.step.title_measure.'.$options['data']['type'],
                'choices' => $options['data']['choices'],
                'data' => $options['data']['data'],
                'expanded' => true,
                'multiple' => false,
                'choice_label' => function () {
                    return ' ';
                },
                'choice_attr' => function ($key, $value) use ($options) {
                    return ['class' => 'bubble-choice '.$key.' '.$options['data']['type']];
                },
            ]);
        }
        $builder->add('submit', SubmitType::class, [
            'label' => 'profile.step.next_step',
        ]);
    }

    /**
     *  @see AbstractType::getBlockPrefix()
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'measure_length';
    }
}
