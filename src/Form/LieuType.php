<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Repository\SerieRepository;
use App\Repository\VilleRepository;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('rue',TextType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('latitude',NumberType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('longitude',NumberType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
            ->add('ville', EntityType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'class' => Ville::class,
                'choice_label' => 'nom',
                'query_builder' => function(VilleRepository $villeRepository) {
                    return $villeRepository->createQueryBuilder('v');
                },
                'attr' => ['class' => 'border-2 border-slate-400 ml-2 rounded-lg']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
