<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => "Titre de l'article",
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                ]
            ])
            ->add('intro', TextareaType::class, [
                "label" => "Introduction",
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                    "rows" => 3
                ]
            ])
            ->add('author', TextType::class, [
                "label" => "Auteur",
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                ]
            ])
            ->add('content', TextareaType::class, [
                "label" => "Contenu",
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                    "rows" => 5
                ]
            ])
            
            ->add('savedPictures', EntityType::class,[
                'label' => 'Choisissez parmi les images enregistrées',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'class' => Picture::class,
                'choice_label' => function($picture){                 
                    return $picture->getTitle();
                }
            ])

            ->add('pictures', FileType::class, [
                'label' => 'Ajoutez une ou plusieurs nouvelle(s) image(s)',
                'multiple' => true,
                'required' =>false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class
        ]);
    }
}
