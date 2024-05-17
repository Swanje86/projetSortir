<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('telephone', NumberType::class,[
                'label' => 'Téléphone',
            ])
            ->add('mail')

            ->add('password', PasswordType::class,[
                'label' => 'Mot de passe',
                'required' => true,
            ])
            ->add('confirmation', PasswordType::class,[
                'label' => 'Confirmation mot de passe',
                'required' => true,
                'mapped' => false,
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nomSite',
                'required' => true,

            ])
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Votre image est trop volumineuse.',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez respecter le format de l\'image.',
                    ]),
                ]
            ])


            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'onclick' => 'return confirm("Voulez-vous vraiment enregistrer les modifications ?")'
                ]
            ])
            ->add('reset', ResetType::class, [
                'label' => 'Annuler',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
