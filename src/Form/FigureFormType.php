<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Figure;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FigureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', options: [
                'label' => 'Titre de la figure'
                ])
            ->add('description')
            ->add('categories', EntityType::class, [
                'class' => Categories::Class,
                'choice_label' => 'category',
                'label' => 'Choix de la catégorie'
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                ])
            ->add('videos', TextType::class, [
                'label' => 'Lien video',
                'mapped' => false,
                'required' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
