<?php

namespace App\Form;

use App\Entity\Issue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('issueDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('issueNumber')
            ->add('pageCount')
            ->add('archiveKey')
            ->add('archiveNotes')
            ->add('utmDigitalArchiveUrl')
            ->add('volume')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
        ]);
    }
}
