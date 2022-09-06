<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PictureType extends AbstractType
{
     
    public function buildForm(FormBuilderInterface $builder, array $options, ): void        
    {
        // en création on ne montre que le champ d'upload
        if($options['add'])
        {
            $builder
                ->add('pictureFile', FileType::class,[
                    "label" => "Image",
                    'required'=>false,
                    'multiple'=>true,
                    'mapped' => false,
                    'attr' => [
                        'class'=>'form-control'
                    ]
                ])
                ->add('title', TextType::class, [
                    'required' => true, 
                    "label" => "Titre de l'image",
                    "attr" => [
                        "class" => "form-control"
                    ]
                ])
                ->add('altText', TextType::class, [
                    "label" => "Description",
                    "required" => true,
                    "attr" => [
                        "class" => "form-control"
                    ]
                ]) ;
        }
        
        // en édition on ne montre que les champs éditable (altText, width, height, legend)
        if($options['edit'])
        {
           $builder
            ->add('title', TextType::class, [
                'required' => true, 
                "label" => "Titre de l'image",
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('altText', TextType::class, [
                "label" => "Description",
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ]) 
            ->add('legend', TextareaType::class, [
                "label" => "Légende",
                "required" => false,
                "attr" => [
                    "class" => "form-control",
                    'rows' => 2,
                ]
            ]) 
            ->add('height', TextType::class,[
                "label" => "Hauteur",
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ]) 
            ->add('width', TextType::class, [
                "label" => "Largeur",
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ]) 
            ;
        }
    }
        

    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            'add' => false,
            'edit' => false
        ]);
    }
}
