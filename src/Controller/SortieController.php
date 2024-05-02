<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Sortie;
use App\Form\SortieSearchType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
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
        //Affichage date du jour
        $dateJour = new \DateTime();

        //liste déroulante
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);

        //zone de recherche
        $searchForm = $this->createForm(SortieSearchType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchTerm = $searchForm->get('searchTerm')->getData();
            $sorties = $em->getRepository(Sortie::class)->findSortieBySearchTerm($searchTerm);
        } else {
            $sorties = $em->getRepository(Sortie::class)->findAll();
        }







            return $this->render('sortie/index.html.twig', [
                'controller_name' => 'SortieController',
                'dateTime' => $dateJour->format('d/m/Y'),
                'form' => $form,
                'searchForm' => $searchForm->createView(),
                'sorties' => $sorties,
            ]);
        }
    }
