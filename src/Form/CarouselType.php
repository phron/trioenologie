<?php

namespace App\Form;

use App\Entity\Carousel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CarouselType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            'data_class' => Carousel::class,            
            'add' => false,
            "update" => false
        ]);
    }
}
