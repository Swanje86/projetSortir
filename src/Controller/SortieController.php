<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function affichage_sorties(Request $request, EntityManagerInterface $em): Response
    {

        // Création d'une nouvelle instance de Sortie
        $sortie = new Sortie();

        // Création du formulaire et liaison avec l'instance de Sortie
        $form = $this->createForm(SortieType::class, $sortie);

        // Gestion des données du formulaire
        $form->handleRequest($request);

        // Vérification si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $data = $form->getData();

            // Utilisation des données du formulaire pour modifier la requête qui récupère les sorties à afficher
            $sorties = $em->getRepository(Sortie::class)->findSortiesWithFilters($data);
        } else {
            // Si le formulaire n'est pas soumis, affichez toutes les sorties
            $sorties = $em->getRepository(Sortie::class)->findSomeFields();
        }

        // Rendu du template avec les données nécessaires
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'dateTime' => (new \DateTime())->format('d/m/Y'),
            'form' => $form->createView(),
            'sorties' => $sorties,
        ]);
        /*$sortie = new Sortie();
        //cree un form et le link avec l'entity Sortie
        $form = $this->createForm(SortieType::class, $sortie);
        //gere les donnees du form
        $form->handleRequest($request);

        $sorties = $em->getRepository(Sortie::class)->findSomeFields();

       // $metadata = $em->getClassMetadata(Sortie::class);

        if ($form->isSubmitted() && $form->isValid()) {
            $siteOrganisateur = $form->get('siteOrganisateur')->getData();

            //va chercher les diff methodes depuis le form
            $searchTerm = $form->get('searchTerm')->getData();
            $dateStartFilter = $form->get('startDate')->getData();
            $dateEndFilter = $form->get('endDate')->getData();

            // gestion des filtres pour le tableau

            if ($siteOrganisateur){
                $sorties = $em->getRepository(Sortie::class)->findSortieBySite($siteOrganisateur);
            }


            // appelle la methode findSortieBySearchTerm depuis le repository
            $sorties = $em->getRepository(Sortie::class)->findSortieBySearchTerm($searchTerm);
            //appelle la methode findSortieByDateRange depuis le repository et merge le resultat avec ceux d'avant
            $sorties = array_merge($sorties, $em->getRepository(Sortie::class)->findSortieByDateRange($dateStartFilter, $dateEndFilter));
        }
            //renvoi le form avec les sorties
            return $this->render('sortie/index.html.twig', [
                'controller_name' => 'SortieController',
                'dateTime' => (new \DateTime())->format('d/m/Y'),
                'form' => $form->createView(),
                'sorties' => $sorties,
               // 'fields' => $fields
            ]);*/


/*
        //si le form n'est pas valide ou pas soumis, renvoi le template avec le form <-- pour les methodes qui ne font pas parties de l'entity
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'dateTime' => (new \DateTime())->format('d/m/Y'),
            'form' => $form->createView(),
        ]);*/


    }

}