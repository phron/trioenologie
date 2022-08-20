<?php

namespace App\Form;

use App\Entity\Gallery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GalleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imgTitle', TextType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            
            ->add('imgDesc', TextareaType::class, [
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                    "rows" => 4,
                ]
            ])

            ->add('endAt', DateTimeType::class, [
                "required" => false,
                "date_widget" => "single_text"
            ]);     

            if($options['add'])
            {
                $builder->add('img', FileType::class, [
                    "required" => false,
                    "attr" => [
                        'onchange' => "loadFile(event)",
                        "class" => "form-control"
                    ]
                ]);
            }
            if($options['update'])
            {
                $builder->add('imgUpdate', FileType::class, [
                    "required" => false,
                    "mapped" => false, // On va récupérer une propriété qui n'est pas dans l'entity
                    "attr" => [
                        'onchange' => "loadFile(event)",
                        "class" => "form-control"
                    ]
                ]);
            }
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class,
            'add' => false,
            "update" => false
        ]);
    }
}
