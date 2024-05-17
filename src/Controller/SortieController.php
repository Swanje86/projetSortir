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
    public function affichage_sorties(Request $request, EntityManagerInterface $em, Security $security, Sortie $sortie): Response
    {
        $participant = $security->getUser();


        // Création d'une nouvelle instance de Sortie
        $sortie = new Sortie();

        // Création du formulaire et liaison avec l'instance de Sortie
        $form = $this->createForm(SortieType::class, $sortie);

        // Gestion des données du formulaire
        $form->handleRequest($request);


        $searchTerm = $form->get('searchTerm')->getData();

        // Vérification si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $data = $form->getData();
            //var_dump($data);
            // Utilisation des données du formulaire pour modifier la requête qui récupère les sorties à afficher
            $sorties = $em->getRepository(Sortie::class)->findSomeFields($sortie, $searchTerm);
        } else {
            // Si le formulaire n'est pas soumis, affichez toutes les sorties
            $sorties = $em->getRepository(Sortie::class)->findSomeFields(); // Utilisation de la nouvelle méthode pour inclure les participants

        }

        //dump($sorties);
        // var_dump($sorties);
        // Rendu du template avec les données nécessaires
        return $this->render('sortie/index.html.twig', [
            'participant' => $participant,
            'controller_name' => 'SortieController',
            'dateTime' => (new \DateTime())->format('d/m/Y'),
            'form' => $form->createView(),
            'sorties' => $sorties,
            'sortie' => $sortie,


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

        $now = new \DateTime();
        if ($now > $sortie->getDateLimiteInscription()) {
            return new JsonResponse(['status' => 'error', 'message' => 'Trop tard ! La date limite d\'inscription est dépassée !' ], 400);
        }

        if (!$sortie->getParticipants()->contains($participant)) {
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

        if ($sortie->getParticipants()->contains($participant)) {
            $sortie->removeParticipant($participant);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return new JsonResponse(['status' => 'desinscrit'], 200);
        }

        return new JsonResponse(['status' => 'not_inscrit'], 400);

    }

    #[Route('/sortie/{id}/annulation', name: 'app_annulation', methods: ['POST'])]
    public function annulation(Sortie $sortie, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer tous les participants de la sortie à annuler
        $participants = $sortie->getParticipants();

        // Supprimer tous les participants
        foreach ($participants as $participant) {
            $sortie->removeParticipant($participant);
            // Supprimer le participant de la base de données si nécessaire
            $entityManager->remove($participant);
        }

        // Supprimer la sortie elle-même
        $entityManager->remove($sortie);

        // Sauvegarder les modifications
        $entityManager->flush();

        // Renvoyer une réponse JSON pour indiquer que l'annulation a réussi
        return new JsonResponse(['status' => 'success', 'message' => 'La sortie et ses participants ont été annulés avec succès.']);
    }


        /*// Création d'une nouvelle instance de Sortie
        $sortie = new Sortie();

        // Création du formulaire et liaison avec l'instance de Sortie
        $form = $this->createForm(SortieType::class, $sortie);

        // Gestion des données du formulaire
        $form->handleRequest($request);

        // Vérification si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $siteOrganisateur = $form->get('siteOrganisateur')->getData();
            $searchTerm = $form->get('searchTerm')->getData();
            $dateStartFilter = $form->get('dateStartFilter')->getData();
            $dateEndFilter = $form->get('dateEndFilter')->getData();

            // Utilisation des données du formulaire pour modifier la requête qui récupère les sorties à afficher
            $sorties = $em->getRepository(Sortie::class)->findSortiesWithFilters($siteOrganisateur, $searchTerm, $dateStartFilter, $dateEndFilter);
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
        ]);*/












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
                ]);


    }*/

}


