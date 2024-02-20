<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
//            ->add('roles')
            ->add('password')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
//            ->add('administrateur')
            ->add('actif')
//            ->add('sorties', EntityType::class, [
//                'class' => Sortie::class,
//'choice_label' => 'id',
//'mapped'=>false,
//'multiple' => true,
//            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
'choice_label' => 'id'
            ])
//            ->add('Site', ChoiceType::class, [
//                'choices' => [
//                    'Nantes'=>'Nantes',
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
