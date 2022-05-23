<?php

namespace Asmodine\FrontBundle\Form;

use Asmodine\CommonBundle\Model\Morphotype\Eye;
use Asmodine\CommonBundle\Model\Morphotype\Hair;
use Asmodine\CommonBundle\Model\Morphotype\Skin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhysicalProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('height')
            ->add('weight')
            ->add('arm')
            ->add('armSpine')
            ->add('armTurn')
            ->add('bra')
            ->add('chest')
            ->add('calf')
            //->add('finger')
            ->add('footLength')
            ->add('footWidth')
            ->add('head')
            ->add('hip')
            ->add('hip2d')
            ->add('insideLeg')
            ->add('legLength')
            ->add('neck')
            ->add('shoulder')
            ->add('thigh')
            ->add('waist')
            ->add('waist2d')
            ->add('waistTop')
            ->add('waistBottom')
            ->add('wrist')
            //->add('hollowToFloor')
            //->add('shoulderToHip')
            ->add('skin', ChoiceType::class, $this->colorOptions('skin', Skin::getSlugs()))
            ->add('hair', ChoiceType::class, $this->colorOptions('hair', Hair::getSlugs()))
            ->add('eyes', ChoiceType::class, $this->colorOptions('eyes', Eye::getSlugs()))
            ->add('submit_measure', SubmitType::class, ['label' => 'form.profile.submit_measure'])
            ->add('submit_morphological', SubmitType::class, ['label' => 'form.profile.submit_morphological']);
    }

    /**
     * Return specific option of color.
     *
     * @param string $type
     * @param        $slugs
     *
     * @return array
     */
    private function colorOptions(string $type, $slugs): array
    {
        return [
            'label' => 'form.profile.label.choice-'.$type,
            'choices' => $slugs,
            'expanded' => true,
            'multiple' => false,
            'choice_label' => function () {
                return ' ';
            },
            'choice_attr' => function ($key, $value) use ($type) {
                return ['class' => 'bubble-choice '.$key.' '.$type];
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Asmodine\FrontBundle\Entity\PhysicalProfile',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'physicalprofile';
    }
}
