<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/personne', name: 'app_personne_index', methods: ['GET'])]
    public function index(PersonneRepository $personneRepository): Response
    {
        return $this->render('personne/index.html.twig', [
            'personnes' => $personneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_personne_new', methods: ['POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, SerializerSerializerInterface $serializer): Response
{
    // Désérialisation des données JSON en une entité Personne
    $personne = $serializer->deserialize($request->getContent(), Personne::class, 'json');

    try {
        // Début de la transaction
        $entityManager->beginTransaction();

        // Persistez l'entité Personne
        $entityManager->persist($personne);
        $entityManager->flush();

        // Commit de la transaction
        $entityManager->commit();

        // Retournez une réponse avec la nouvelle entité créée et le code 201 Created
        return $this->json($personne, Response::HTTP_CREATED);

    } catch (\Exception $e) {
        // En cas d'erreur, rollback de la transaction
        $entityManager->rollback();

        // Gérer l'exception, par exemple en renvoyant une réponse d'erreur avec le code 500 Internal Server Error
        return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    #[Route('/{id}', name: 'app_personne_show', methods: ['GET'])]
    public function show(Personne $personne): Response
    {
        return $this->render('personne/show.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_personne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_personne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personne/edit.html.twig', [
            'personne' => $personne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_personne_delete', methods: ['DELETE'])]
    public function delete(Personne $personne, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($personne);
            $entityManager->flush();

            return $this->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
