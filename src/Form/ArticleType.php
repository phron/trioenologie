<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                ]
            ])
            ->add('intro', TextareaType::class, [
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                    "rows" => 3
                ]
            ])
            ->add('author', TextType::class, [
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                ]
            ])
            ->add('content', TextareaType::class, [
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                    "rows" => 5
                ]
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,            
            'add' => false,
            "update" => false
        ]);
    }
}
