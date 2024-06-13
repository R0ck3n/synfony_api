<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(HttpFoundationRequest $request, EntityManagerInterface $entityManager, PersonneRepository $repo): Response
    {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personne->setComing(true);
            $entityManager->persist($personne);
            $entityManager->flush();

            return $this->redirectToRoute('app_main');
        }
        return $this->render('home.html.twig', [
            'personnes' => $repo->findAll(),
            'form' => $form,
        ]);
    }
    #[Route('delete/{id}', name: 'personne_delete', methods: ['GET'])]
    public function delete(HttpFoundationRequest $request, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$personne->getId(), $request->getPayload()->get('_token'))) {
        $entityManager->remove($personne);
        $entityManager->flush();
        // }

        return $this->redirectToRoute('app_main');
    }

    #[Route('/edit/{id}', name: 'personne_modifier', methods: ['GET'])]
    public function edit(Personne $personne, EntityManagerInterface $entityManager): Response
    {
        $personne->setComing(!$personne->isComing());
        $entityManager->flush();

        return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);


        // return $this->render('personne/edit.html.twig', [
        //     'personne' => $personne,
        //     'form' => $form,
        // ]);
    }

    #[Route('/list', name: 'app_list')]
    public function list( PersonneRepository $repo): Response
    {

        // Récupération de toutes les personnes depuis le repository
        $personnes = $repo->findAll();

        // Retourne une réponse JSON avec les données des personnes
        return $this->json($personnes);
    }

}
