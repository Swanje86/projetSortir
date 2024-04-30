<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::Class,[
                'label' => 'Pseudo',
            ])
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('mail')
            ->add('motDePasse')
            ->add('administrateur')
            ->add('actif')
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'id',
            ])
            ->add('sorties', EntityType::class, [
                'class' => Sortie::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
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
