<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'label' => 'Nom',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']
            ])
            ->add('dateHeureDebut',DateTimeType::class, [
                'label' => 'Date et Heure de début',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('duree',NumberType::class, [
                'label' => 'Duree (En minutes)',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('dateLimiteInscription',DateTimeType::class, [
                'label' => 'Date et Heure limite d\'inscription',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('nbInscriptionsMax',NumberType::class, [
                'label' => 'Nombre d\'inscriptions maximales',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Détail de la sortie',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('AjouterLieu', SubmitType::class, [
                'label' => 'Ajouter un lieu manuelement',
                'row_attr' => ['class' => 'w-fit mx-auto  flex flex-col'
                ],
                'attr' => ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm  sm:w-auto px-5 py-2.5 mt-5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
                    'formnovalidate' => 'formnovalidate']

            ]);
            if ($options['addLieu'] || $options['InsererLieu']) { // ajoute un formulaire de création de lieu si l'utilisateur clique sur le bouton d'ajout de lieu

                $builder->add('SelectionnerLieuxDisponibles', SubmitType::class, [
                    'label' => 'Sélectionner parmis les lieux disponibles',
                    'row_attr' => [
                        'class' => 'w-fit mx-auto  flex flex-col'],
                    'attr' => ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm sm:w-auto px-5 py-2.5 mt-5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
                        'formnovalidate' => 'formnovalidate']
                ]);

                $builder->add('lieu', LieuType::class, [
                    'label' => 'Lieu',
                    'row_attr' => ['class' => 'my-1 flex flex-col'],
                    'label_attr' => ['class' => ' text-gray-600'],
                    'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']
                    ,'inherit_data' => false,

                ]);

                $builder->remove('AjouterLieu');


            } else
            {
                $builder->add('lieu', EntityType::class, [
                    'label' => 'Lieu',
                    'class' => Lieu::class,
                    'choice_label' => 'nom',
                    'query_builder' => function(LieuRepository $lieuRepository) {
                        return $lieuRepository->createQueryBuilder('l')
                            ->andWhere('l.actif = :value')
                            ->setParameter('value', 1);
                    },
                    'row_attr' => ['class' => 'my-1 flex flex-col'],
                    'label_attr' => ['class' => ' text-gray-600'],
                    'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

                ]);

            }

            // Si l'utilisateur veut ajouter un lieu on mets un bouton différent qui permettra au controller de faire l'insertion au lieu de la mise a jour
            if($options['addLieu'])
            {
                $builder->add('publier', CheckboxType::class, [
                    'label' => 'Publier sortie',
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']

                ]);
                $builder->add('InsererLieu', SubmitType::class, [
                    'label' => 'Envoyer',
                    'row_attr' => [
                        'class' => 'w-fit mx-auto  flex flex-col'
                    ],
                    'attr' => ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 mt-5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800']
                ])
                ;
            }
            else
            {
                $builder->add('publier', CheckboxType::class, [
                    'label' => 'Publier sortie',
                    'label_attr' => ['class' => 'ms-2 text-sm font-medium text-gray-900 dark:text-gray-300'],
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600']
                ]);
                $builder->add('submit', SubmitType::class, [
                    'label' => 'Envoyer',
                    'row_attr' => [
                        'class' => 'w-fit mx-auto  flex flex-col'
                    ],
                    'attr' => ['class' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 mt-5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800']
                ])
                ;

            }






    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'addLieu' => false,
            'InsererLieu' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
