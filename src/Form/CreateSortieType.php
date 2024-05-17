<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSortie', TextType::class, [
                'label' => 'Nom de la sortie :',
                'required' => true,
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places :',
                'required' => true,
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée en minutes :',
                'required' => true,
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos :',
                'required' => true,
            ])
            ->add('siteOrganisateur', EntityType::class, [
                'label' => 'Ville Organisatrise : ',
                'class' => Site::class,
                'choice_label' => 'nomSite',
                'disabled' => true,
                'required' => true,
            ])
            ->add('ville', EntityType::class, [
                'label' => 'Ville : ',
                'mapped' => false,
                'class' => Ville::class,
                'placeholder' => '-- Veuillez choisir une ville de Sortie --',
                'choice_label' => 'nomVille',
                'required' => true,
                'attr' => [
                    'id' => 'ville_ID',
                ],
                'choice_attr' => function(Ville $ville) {
                    return ['data-code-postal' => $ville->getCodePostal()];
                },
            ])
            ->add('codePostal', EntityType::class, [
                'label' => 'Code Postal :',
                'mapped' => false,
                'class' => Ville::class,
                'choice_label' => 'codePostal',
                'required' => true,
              'disabled' => true,
                'attr' => [
                    'id' => 'cp_ID',
                ],
            ])

            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvents) {
                $ville = $formEvents->getForm()->get('ville')->getData();
                $codePostal = ($ville) ? ($ville->getCodePostal()) : null;
                $formEvents-> getForm()->get('codePostal')->setData($codePostal);
            })


            ->add('lieu', EntityType::class, [
                'label' => 'Lieu : ',
                'class' => Lieu::class,
                'placeholder' => '-- Veuillez choisir un lieu de Sortie --',
                'choice_label' => 'nomLieu',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
