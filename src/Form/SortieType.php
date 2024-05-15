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
use Symfony\Component\Form\Extension\Core\Type\ResetType;
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
                'placeholder' => '-- Veuillez choisir un site --',
                'label'=> false,
                'required' => false,
                'attr' =>['id' => 'form_siteOrganisateur'],
            ])
            ->add('searchTerm', TextType::class, [
                'required' => false,
                'label'=> false,
                'mapped' => false, //false comme pas en lien avec l'entity Sortie
                'attr' =>['id' => 'form_searchTerm'],
            ])
            ->add('dateStartFilter', DateType::class, [
                'required' => false,
                'label'=> false,
                'widget' => 'single_text',
                'mapped'=> false,//false comme pas en lien avec l'entity Sortie
                'attr' =>['id' => 'form_dateStartFilter'],
            ])
            ->add('dateEndFilter', DateType::class, [
                'required' => false,
                'label'=> false,
                'widget' => 'single_text',
                'mapped'=> false,//false comme pas en lien avec l'entity Sortie
                'attr' =>['id' => 'form_dateEndFilter'],
            ])
            ->add('conditions', ChoiceType::class, [
                'choices'  => [
                    "Sorties dont je suis l'organisateur / trice" => "cdt1",
                    "Sorties auxquelles je suis inscrit/e" => "cdt2",
                    "Sorties auxquelles je ne suis pas inscrit/e" => "cdt3",
                    "Sorties passées" => "cdt4",
                ],
                'label'=> false,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'checkbox-vertical',
                    'id' => 'form_conditions'
                ]
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
        $resolver->setDefined(['sites']); // Define the 'sites' option

    }
}
