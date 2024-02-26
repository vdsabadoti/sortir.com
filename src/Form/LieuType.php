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
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('rue',TextType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('latitude',NumberType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('longitude',NumberType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer']

            ])
            ->add('ville', EntityType::class, [
                'row_attr' => ['class' => 'my-1 flex flex-col'],
                'class' => Ville::class,
                'choice_label' => 'nom',
                'query_builder' => function(VilleRepository $villeRepository) {
                    return $villeRepository->createQueryBuilder('v');
                },
                'label_attr' => ['class' => ' text-gray-600'],
                'attr' => ['class' => 'block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent  appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer']
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
