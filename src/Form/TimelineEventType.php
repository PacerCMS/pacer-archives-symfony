<?php

namespace App\Form;

use App\Entity\TimelineEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimelineEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('startDatePrecision', ChoiceType::class, [
                'choices' => TimelineEvent::DATE_PRECISIONS
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('endDatePrecision', ChoiceType::class, [
                'choices' => TimelineEvent::DATE_PRECISIONS
            ])
            ->add('displayDate')
            ->add('headline')
            ->add('text')
            ->add('media')
            ->add('mediaCredit')
            ->add('mediaCaption')
            ->add('mediaThumbnail')
            ->add('type')
            ->add('eventGroup')
            ->add('background')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TimelineEvent::class,
        ]);
    }
}
