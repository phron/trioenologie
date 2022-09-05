<?php

namespace App\Form;

use App\Entity\Status;
use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                "attr" => [
                    "class" => "form-control",
                ],
                "label" => "Nom de famille",
                "constraints" => [
                    new Regex([
                        "pattern" => "/^[a-z]+$/i",
                        "message" => "Le nom ne doit contenir que des lettres."
                    ])
                    ],
                "required" => false,
            ])
            ->add('firstName', TextType::class, [
                "attr" => [
                    "class" => "form-control",
                ],
                "label" => "Prénom",
                "constraints" => [
                    new Regex([
                        "pattern" => "/^[a-z]+$/i",
                        "message" => "Le prénom ne doit contenir que des lettres."
                    ])
                    ],
                "required" => false,
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => "Téléphone",
                "required" => false,                
                "constraints" => [
                    new Regex([
                        "pattern" => "/[0-9]{10}/",
                        "message" => "le téléphone ne doit contenir que 10 chiffres."
                    ]),
                    ],
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('address', TextType::class, [
                "label" => "Adresse",
                "attr" => [
                    "class" => "form-control"
                ], 
                "required" => false,
            ])

            ->add('address2', TextType::class, [
                "label" => "Adresse complémentaire",
                "attr" => [
                    "class" => "form-control"
                ],
                "required" => false,
            ])

            ->add('zipcode', TextType::class,[
                "attr" => [
                    "class" => "form-control"
                ],
                "label" => "Code postal",
                "required" => false,
                "constraints" => [
                    new Regex([
                        "pattern" => "/[0-9]{5}/",
                        "message" => "Le code postal ne doit contenir que 5 chiffres."
                    ])
                    ]
            ])

            ->add('city', TextType::class, [
                "label" => "Ville",
                "attr" => [
                    "class" => "form-control"
                ],
                "required" => false,
            ])

            ->add('status', EntityType::class,[
                "label" => "Statut du membre",
                'class' => Status::class ,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'labelradio'
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
