<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function affichage_sorties(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $participant = $security->getUser();


        // Création d'une nouvelle instance de Sortie
        $sortie = new Sortie();

        // Création du formulaire et liaison avec l'instance de Sortie
        $form = $this->createForm(SortieType::class, $sortie,['attr' => ['id' => 'form_sortie']]);

        // Gestion des données du formulaire
        $form->handleRequest($request);


        //$searchTerm = $form->get('searchTerm')->getData();


        // Vérification si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $siteOrganisateur = $form->get('siteOrganisateur')->getData();
            $searchTerm = $form->get('searchTerm')->getData();
            $dateStartFilter = $form->get('dateStartFilter')->getData();
            $dateEndFilter = $form->get('dateEndFilter')->getData();

            // Create a filters object and set its properties
            $filters = new Sortie();
            $filters->setSiteOrganisateur($siteOrganisateur);
            $filters->setSearchTerm($searchTerm);
            $filters->setDateStartFilter($dateStartFilter);
            $filters->setDateEndFilter($dateEndFilter);

            // Utilisation des données du formulaire pour modifier la requête qui récupère les sorties à afficher
            $sorties = $em->getRepository(Sortie::class)->findSortiesWithFilters($filters);

            //test code
/*
            $dateStartFilterTest = new \DateTime('2024-10-07');
            $dateEndFilterTest = new \DateTime('2024-10-18');
            $testSorties = $em->getRepository(Sortie::class)->findSortieByDateRange($dateStartFilterTest, $dateEndFilterTest);
            dump($testSorties);*/

        } else {
            // Si le formulaire n'est pas soumis, affichez toutes les sorties
            $sorties = $em->getRepository(Sortie::class)->findSomeFields();
        }

        // Rendu du template avec les données nécessaires
        return $this->render('sortie/index.html.twig', [
            'participant' => $participant,
            'controller_name' => 'SortieController',
            'dateTime' => (new \DateTime())->format('d/m/Y'),
            'form' => $form->createView(),
            'sorties' => $sorties,
        ]);

    }

    #[Route('/details/{id}', name: 'app_sortie_details', requirements: ['id' => '\d+'] )]
    public function sortieDetails (Sortie $sortie, Security $security): Response
    {
        $participant = $security->getUser();

        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/sortie/{id}/inscription", name="sortie_inscription", methods={"POST"})
     */
    #[Route('/sortie/{id}/inscription', name: 'app_sortie_inscription', methods:['POST'])]
    public function inscription(Sortie $sortie, EntityManagerInterface $entityManager): JsonResponse
    {
        $participant = $this->getUser();
        if (!$participant instanceof Participant) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not found or not a participant'], 400);
        }

        if (!$sortie->getParticipant()->contains($participant)) {
            $sortie->addParticipant($participant);
            $entityManager->persist($sortie);

            $entityManager->flush();
            return new JsonResponse(['status' => 'inscrit'], 200);
        }

        return new JsonResponse(['status' => 'already_inscrit'], 400);
    }

    /**
     *
     * @Route("/sortie/{id}/desinscription", name="sortie_desinscription", methods={"POST"})
     */
    #[Route('/sortie/{id}/desistement', name: 'app_sortie_desistement', methods:['POST'])]
    public function desistement(Sortie $sortie, EntityManagerInterface $entityManager): JsonResponse
    {
        $participant = $this->getUser();
        if (!$participant instanceof Participant) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not found or not a participant'], 400);
        }

        if ($sortie->getParticipant()->contains($participant)) {
            $sortie->removeParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return new JsonResponse(['status' => 'desinscrit'], 200);
        }

        return new JsonResponse(['status' => 'not_inscrit'], 400);
    }

}