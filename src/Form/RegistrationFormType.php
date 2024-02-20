<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextareaType::class, [
                'label' => 'email',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'class="relative z-0 w-full mb-5 group"'
                ]
            ])
            ->add('prenom', TextareaType::class, [
                'label' => 'prenom',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'class="relative z-0 w-full mb-5 group"'
                ]])
            ->add('nom', TextareaType::class, [
                'label' => 'nom',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'class="relative z-0 w-full mb-5 group"'
                ]])
            ->add('telephone', TextareaType::class, [
                'label' => 'telephone',
                'attr' => [
                    'maxlength' => 255,
                    'class' => 'class="relative z-0 w-full mb-5 group"'
                ]
            ])
            ->add('actif')
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'id'
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
