<?php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',EmailType::class, [
                'label' => "E-mail",
                "attr" => [
                    "class" => "form-control",
                ]
            ])
            ->add('subject',ChoiceType::class, [
                "choices" => [
                    "Visites" => "Visites",
                    "Cours" => "Cours",
                    "Dégustations" => "Dégustations",
                    "L'association" => "L'association",
                    "Autre" => "Autre"
                ],
                "multiple" => false,
                'label' => "Sujet",
                "attr" => [
                    "class" => "form-control",
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => "Votre message",
                'attr' => [
                    'rows' => 6,
                    'class' => 'form-control'
            ],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}