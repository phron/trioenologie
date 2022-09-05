<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PictureType extends AbstractType
{
     
    public function buildForm(FormBuilderInterface $builder, array $options, ): void        
    {
        // en création on ne montre que le champ d'upload
        if($options['add'])
        {
            $builder
                ->add('pictureFile', FileType::class,[
                    'required'=>false,
                    'multiple'=>true,
                    'mapped' => false,
                    'attr' => [
                        // 'onchange'=>'loadFile(event)',
                        'class'=>'form-control'
                    ]
                
                ]);
        }
        
        // en édition on ne montre que les champs éditable (altText, width, height, legend)
        if($options['edit'])
        {
           $builder
            ->add('altText', TextType::class, [
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ]) 
            ->add('legend', TextType::class, [
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ]) 
            ->add('height', TextType::class,[
                "required" => false,
                "attr" => [
                    "class" => "form-control"
                ]
            ]) 
            ->add('width', TextType::class, [
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
