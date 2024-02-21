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
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('dateHeureDebut',DateTimeType::class, [
                'label' => 'Date et Heure de début',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('duree',NumberType::class, [
                'label' => 'Duree (En minutes)',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('dateLimiteInscription',DateTimeType::class, [
                'label' => 'Date et Heure limite d\'inscription',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('nbInscriptionsMax',NumberType::class, [
                'label' => 'Nombre d\'inscriptions maximales',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Détail de la sortie',
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('AjouterLieu', SubmitType::class, [
                'label' => 'Ajouter un lieu manuelement',
                'row_attr' => ['class' => 'w-fit mx-auto  flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 mt-6 rounded-lg',
                    'formnovalidate' => 'formnovalidate']

            ]);
            if ($options['addLieu']) { // ajoute un formulaire de création de lieu si l'utilisateur clique sur le bouton d'ajout de lieu

                $builder->add('SelectionnerLieuxDisponibles', SubmitType::class, [
                    'label' => 'Sélectionner parmis les lieux disponibles',
                    'row_attr' => ['class' => 'w-fit mx-auto  flex flex-col'],
                    'attr' => ['class' => 'border-2 border-slate-400 mt-6 rounded-lg',
                        'formnovalidate' => 'formnovalidate']
                ]);

                $builder->add('lieu', LieuType::class, [
                    'label' => 'Lieu',
                    'row_attr' => ['class' => 'my-1 flex flex-col'],
                    'attr' => ['class' => 'border-2 border-slate-400 ml-2 px-5 rounded-lg']
                ]);

                $builder->remove('AjouterLieu');


            } else
            {
                $builder->add('lieu', EntityType::class, [
                    'label' => 'Lieu',
                    'class' => Lieu::class,
                    'choice_label' => 'nom',
                    'query_builder' => function(LieuRepository $lieuRepository) {
                        return $lieuRepository->createQueryBuilder('l');
                    },
                    'row_attr' => ['class' => 'my-1 flex flex-col'],
                    'attr' => ['class' => 'border-2 border-slate-400 mt-6 rounded-lg']
                ]);

            }

            $builder->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'row_attr' => ['class' => 'w-fit mx-auto  flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 mt-6 rounded-lg']
            ])
        ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'addLieu' => false,
        ]);
    }
}
