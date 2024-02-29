<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email',
                    'class' => 'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2
                     border-gray-600 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-600 focus:outline-none focus:ring-0 focus:border-blue-600 peer'
                    ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre adresse mail, un lien de réinitialisation vous sera envoyé',

                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
