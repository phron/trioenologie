<?php

namespace App\Form\Admin;

use App\Entity\Status;
use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                "constraints" => [
                    new Regex([
                        "pattern" => "/^[a-z]+$/i",
                        "message" => "Le nom ne doit contenir que des lettres."
                    ])
                    ],
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('firstName', TextType::class, [
                "constraints" => [
                    new Regex([
                        "pattern" => "/^[a-z]+$/i",
                        "message" => "Le prénom ne doit contenir que des lettres."
                    ])
                    ],
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('address', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                "required" => false
            ])

            ->add('address2', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                "required" => false
            ])

            ->add('zipcode', TextType::class,[
                "required" => false,
                "constraints" => [
                    new Regex([
                        "pattern" => "/[0-9]{5}/",
                        "message" => "Le code postal ne doit contenir que 5 chiffres."
                    ])
                    ],
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('city', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ],
                "required" => false
            ])

            ->add('status', EntityType::class,[
                'class' => Status::class ,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'labelradio form-control'
                ],
                'expanded' => true, 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
