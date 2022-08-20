<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom de l'évènement",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('bgColor', ColorType::class, [
                "label" => "Couleur de fond",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('bdColor', ColorType::class, [
                "label" => "Couleur de bordure",                
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('textColor', ColorType::class, [
                "label" => "Couleur du texte",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
