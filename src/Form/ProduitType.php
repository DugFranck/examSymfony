<?php

namespace App\Form;

use App\Entity\Produit;
use App\Service\FileUploader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    private $fileUploader;
    public function __construct(FileUploader $fileUploader){
        $this->fileUploader=$fileUploader;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prix')
            ->add('description', TextareaType::class)
            ->add('image', FileType::class, [
                'label' => 'télécharge image',
                'mapped' => false, // Tell that there is no Entity to link
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => "This document isn't valid.",
                    ])
                ],
            ])


            ->add('statut' , CheckboxType::class,[
                'label' => 'Mise en vente ',
                'required' => false
            ])
            ->add('submit',SubmitType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $formEvent){
                $fileName=$this->fileUploader->upload($formEvent->getForm()->get('image')->getData());
                $formEvent->getData()->setImage($fileName);

            })
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
