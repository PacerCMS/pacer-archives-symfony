<?php

declare(strict_types=1);

namespace App\Admin;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;


final class TimelineEventAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('startDate')
            ->add('endDate')
            ->add('type')
            ->add('eventGroup')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('startDate')
            ->add('endDate')
            ->add('headline')
            ->add('text')
            ->add('type')
            ->add('eventGroup')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('startDate', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('startDatePrecision', ChoiceType::class, [
                'choices' => [
                    'Day' => 'day',
                    'Month' => 'month',
                    'Year' => 'year'
                ]
            ])
            ->add('endDate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('endDatePrecision', ChoiceType::class, [
                'choices' => [
                    'Day' => 'day',
                    'Month' => 'month',
                    'Year' => 'year'
                ]
            ])
            ->add('displayDate')
            ->add('headline')
            ->add('text', TextareaType::class)
            ->add('media')
            ->add('mediaCredit')
            ->add('mediaCaption')
            ->add('mediaThumbnail')
            ->add('type')
            ->add('eventGroup')
            ->add('background')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('startDate')
            ->add('startDatePrecision')
            ->add('endDate')
            ->add('endDatePrecision')
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
}
