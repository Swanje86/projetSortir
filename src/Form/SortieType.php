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
                'label'=> false,
                'choice_value' => function (?Site $site) {
                    return $site ? $site->getId() : '';
                }
            ])
            ->add('searchTerm', TextType::class, [
                'required' => false,
                'label'=> false,
                'mapped' => false, //false comme pas en lien avec l'entity Sortie
            ])
            ->add('dateStartFilter', DateType::class, [
                'required' => false,
                'label'=> false,
                'widget' => 'single_text',
                'mapped'=> false,//false comme pas en lien avec l'entity Sortie
            ])
            ->add('dateEndFilter', DateType::class, [
                'required' => false,
                'label'=> false,
                'widget' => 'single_text',
                'mapped'=> false,//false comme pas en lien avec l'entity Sortie
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
                    'class' => 'checkbox-vertical'
                ]
            ]);



           /* ->add('siteOrganisateur', EntityType::class, [
                'class' => Site::class,
                'label'=> false,
                'choice_label' => 'nomSite',
                'choice_value' => function (?Site $site) {
                    return $site ? $site->getId() : '';
                }
            ])
            ->add('searchTerm', TextType::class, [
                'required' => false,
                'label'=> false,
                'mapped' => false, //false comme pas en lien avec l'entity Sortie
            ])
            ->add('dateStartFilter', DateType::class, [
                'required' => false,
                'label'=> false,
                'widget' => 'single_text',
                'mapped'=> false,//false comme pas en lien avec l'entity Sortie
            ])
            ->add('dateEndFilter', DateType::class, [
                'required' => false,
                'label'=> false,
                'widget' => 'single_text',
                'mapped'=> false,//false comme pas en lien avec l'entity Sortie
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
                    'class' => 'checkbox-vertical'
                ]
            ]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
