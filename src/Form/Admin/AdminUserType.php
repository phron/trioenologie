<?php

namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => "E-mail",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('roles', ChoiceType::class, [
                "label" => "Rôle de l'utilisateur:",
                "choices" => [
                    "Administrateur" => "ROLE_ADMIN",
                    "Editeur" => "ROLE_EDITOR",
                    "Utilisateur" => "ROLE_USER"
                ],
                "expanded" => true,
                "multiple" => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
