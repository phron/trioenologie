<?php

namespace App\Form;

use App\Entity\Gallery;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                'label' => "Titre de l'image",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            
            ->add('imgDesc', TextareaType::class, [
                'label' => "Description de l'image",
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                    "rows" => 4,
                ]
            ])

            ->add('endAt', DateTimeType::class, [
                'label' => "Fin de publication",
                "required" => false,
                "date_widget" => "single_text",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            
            ->add('savedPictures', EntityType::class,[
                'label' => 'Choisissez parmi les images enregistrées',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                "attr" => [
                    "class" => "form-control"
                ],
                'class' => Picture::class,
                'choice_label' => function($picture){                 
                    return $picture->getTitle();
                }
            ])

            ->add('pictures', FileType::class, [
                'label' => "Ajoutez une nouvelle image",
                'multiple' => true,
                'required' =>false,
                'mapped' => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gallery::class
        ]);
    }
}
