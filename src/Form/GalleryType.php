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
            ])
            
            ->add('savedPictures', EntityType::class,[
                'label' => 'Choisissez parmi les images enregistrées',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'class' => Picture::class,
                'choice_label' => function($picture){                 
                    return $picture->getTitle();
                }
            ])

            ->add('pictures', FileType::class, [
                'label' => false,
                'multiple' => true,
                'required' =>false,
                'mapped' => false
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
