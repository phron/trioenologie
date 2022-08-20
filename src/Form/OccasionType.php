<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Occasion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('description', TextareaType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('startDate', DateTimeType::class, [
                "date_widget" => "single_text",
                "hours" => [8,9,10,11,12,13,14,15,16,17,18, 19],
                "minutes" => [0,15,30,45],
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('endDate', DateTimeType::class, [
                "date_widget" => "single_text",
                "hours" => [8,9,10,11,12,13,14,15,16,17,18, 19],
                "minutes" => [0,15,30,45],
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('category', EntityType::class, [
                "class" => Category::class,
                "choice_label" => "name",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('minParts', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('maxParts', IntegerType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
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
