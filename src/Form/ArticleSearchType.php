<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleSearchType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return '';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['action'])
            ->add('s', SearchType::class, [
                'label' => 'Search Term',
                'mapped' => false,
                'data' => $options['s']
            ])
            ->add('index', ChoiceType::class, [
                'choices' => [
                    'Article Content' => 'content',
                    'Author Bylines' => 'author'
                ],
                'label' => 'Search Index',
                'property_path' => 'index',
                'expanded' => true,
                'data' => $options['index'],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'action' => '',
            'method' => 'get',
            'mapped' => false,
            's' => '',
            'index' => 'content'
        ]);
    }
}
