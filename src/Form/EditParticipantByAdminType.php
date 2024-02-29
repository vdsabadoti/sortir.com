<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class EditParticipantByAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextareaType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'block py-0.5 px-0 w-full text-sm text-gray-900 bg-white border-0 border-b-2 
                    border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ]
            ])
            ->add('nom', TextareaType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'block py-1.5 px-0 w-full text-sm text-gray-900 bg-white border-0 border-b-2 
                    border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ]
            ])
            ->add('prenom', TextareaType::class, [
                'label' => 'Prenom',
                'attr' => [
                    'class' => 'block py-1.5 px-0 w-full text-sm text-gray-900 bg-white border-0 border-b-2 
                    border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ]
            ])

            ->add('telephone', TelType::class, [
                'label' => 'Telephone',
                'attr' => [
                    'class' => 'block py-1.5 px-0 w-full text-sm text-gray-900 bg-white border-0 border-b-2 
                    border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ],
                'constraints' => array(new Regex(
                    array('pattern' => '/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/',
                        'message' => 'Le numéro n\'est pas valide')
                ))
            ])

            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'placeholder'=>'Choisir ville',
                'label'=>false,
                'attr'=>[
                    'class'=>'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2
                     border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
