<?php

namespace App\Form;

use App\Entity\Picture;
use App\Entity\Category;
use App\Entity\Occasion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class OccasionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => "Titre",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('description', TextareaType::class, [
                "label" => "Description",
                "attr" => [
                    "class" => "form-control",
                    "rows" => "5"
                ]
            ])
            ->add('startDate', DateTimeType::class, [
                "label" => "Début de l'évènement",
                "date_widget" => "single_text",
                "hours" => [8,9,10,11,12,13,14,15,16,17,18, 19],
                "minutes" => [0,15,30,45],
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('endDate', DateTimeType::class, [
                "label" => "Fin de l'évènement",
                "date_widget" => "single_text",
                "hours" => [8,9,10,11,12,13,14,15,16,17,18, 19],
                "minutes" => [0,15,30,45],
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('category', EntityType::class, [
                "label" => "Catégorie d'évènement",
                "class" => Category::class,
                "choice_label" => "name",
                "attr" => [
                    "class" => "form-control text-center"
                ]
            ])
            ->add('minParts', IntegerType::class, [
                "label" => "Minimum de participants",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('maxParts', IntegerType::class, [
                "label" => "Maximum de participants",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            
            ->add('savedPictures', EntityType::class,[
                'label' => 'Images enregistrées',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                // 'expanded' => true,
                "attr" => [
                    "class" => ""
                ],
                'class' => Picture::class,
                'choice_label' => "title"
            ])

            ->add('pictures', FileType::class, [
                'label' => 'Ajouter une image',
                'multiple' => true,
                'required' =>false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Occasion::class,
        ]);
    }
}
