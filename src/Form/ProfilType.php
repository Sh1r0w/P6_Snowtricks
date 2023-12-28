<?php

namespace App\Form;

use App\Entity\Connect;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'=> 'Votre Email',
            ])
            ->add('username', TextType::class, [
                'label'=> 'Votre Pseudo',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'Les mots de passe ne corresponde pas',
                'first_options'  => ['label' => 'Votre nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmer votre nouveau mot de passe'],
            ])
            ->add('imguser', FileType::class, [
                'label'=> 'Votre Avatar',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Connect::class,
        ]);
    }
}
