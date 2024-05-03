<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    #[Route('/modify/{id}', name: 'app_Modify', requirements: ['id' => '\d+'])]
    public function modify(Request $request, EntityManagerInterface $entityManager, Participant $participant, UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $form = $this->createForm(ProfilType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $form->get('password')->getData()
                )
            );
            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success', 'Le profil a bien été modifié.');

            return $this->redirectToRoute('app_Profil', ['id' => $participant->getId()]);
        }


        return $this->render('profil/profilModify.html.twig', [
            'participant' => $participant,
            'participant_form' => $form,
        ]);
    }

    #[Route('/detailProfil/{id}', name: 'app_Profil', requirements: ['id' => '\d+'])]
    public function details(Participant $participant): Response
    {
        return $this ->render('profil/profilDetails.html.twig', [
            'participant' => $participant,
        ]);
    }
}
