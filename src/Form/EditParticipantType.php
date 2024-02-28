<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditParticipantType extends AbstractType
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
                    'class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-white border-0 border-b-2 
                    border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Telephone',
                'attr' => [
                    'class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-white border-0 border-b-2 
                    border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ]
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-white border-0 border-b-2 
                    border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ]
            ])
            ->add('image', HiddenType::class)
            ->add('poster_file', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => false,
                'attr' => [
                    'class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 
                     border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => "Le format n'est pas supporté",
                        'maxSizeMessage' => "Le fichier est trop volumineux"
                    ])
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                if ($user && $user->getImage()) {
                    $form = $event->getForm();
                    $form->add('deleteImage', CheckboxType::class, [
                        'required' => false,
                        'mapped' => false,
                    ]);
                }
            });


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
