<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Form\ProfilType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class
SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    #[IsGranted('ROLE_USER')]
    public function affichage_sorties(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $participant = $security->getUser();



        $sortie = new Sortie();
        //cree un form et le link avec l'entity Sortie
        $form = $this->createForm(SortieType::class, $sortie);
        //gere les donnees du form
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //va chercher les diff methodes depuis le form
            $searchTerm = $form->get('searchTerm')->getData();
            $dateStartFilter = $form->get('startDate')->getData();
            $dateEndFilter = $form->get('endDate')->getData();

            // appelle la methode findSortieBySearchTerm depuis le repository
            $sorties = $em->getRepository(Sortie::class)->findSortieBySearchTerm($searchTerm);
            //appelle la methode findSortieByDateRange depuis le repository et merge le resultat avec ceux d'avant
            $sorties = array_merge($sorties, $em->getRepository(Sortie::class)->findSortieByDateRange($dateStartFilter, $dateEndFilter));





            //renvoi le form avec les sorties
            return $this->render('sortie/index.html.twig', [
                'controller_name' => 'SortieController',
                'dateTime' => (new \DateTime())->format('d/m/Y'),
                'form' => $form->createView(),
                'sorties' => $sorties,
            ]);
        }


        //si le form n'est pas valide ou pas soumis, renvoi le template avec le form <-- pour les methodes qui ne font pas parties de l'entity
        return $this->render('sortie/index.html.twig', [
            'participant' => $participant,
            'controller_name' => 'SortieController',
            'dateTime' => (new \DateTime())->format('d/m/Y'),
            'form' => $form->createView(),
        ]);


    }
}