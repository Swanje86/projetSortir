<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('siteOrganisateur', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nomSite',
            ])

            ->add('searchTerm', TextType::class, [
                'required' => false,
                'mapped' => false, //false comme pas en lien avec l'entity Sortie
            ])
            ->add('dateStartFilter', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'mapped'=> false,//false comme pas en lien avec l'entity Sortie
            ])
            ->add('dateEndFilter', DateType::class, [
            'required' => false,
            'widget' => 'single_text',
            'mapped'=> false,//false comme pas en lien avec l'entity Sortie
    ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
