<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Volume;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolumeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('volumeNumber', null, [
                'label' => 'Volume'
            ])
            ->add('volumeStartDate', DateType::class, [
                'label' => 'Start Date',
                'widget' => 'single_text'
            ])
            ->add('volumeEndDate', DateType::class, [
                'label' => 'End Date',
                'widget' => 'single_text'
            ])
            ->add('nameplateKey', ChoiceType::class, [
                'label' => 'Nameplate',
                'choices' => [
                    'The Pacer' => 'pacer',
                    'The Volette' => 'volette'
                ]
            ]);
        if ($entity->getId()) {
            $builder
                ->add('coverIssue', EntityType::class, [
                    'label' => 'Cover Issue',
                    'class' => Issue::class,
                    'query_builder' => function (EntityRepository $er) use ($entity) {
                        return $er->createQueryBuilder('i')
                            ->where('i.volume = :volume')
                            ->setParameter('volume', $entity)
                            ->orderBy('i.issueDate', 'ASC');
                    },
                    'required' => false
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Volume::class,
        ]);
    }
}
