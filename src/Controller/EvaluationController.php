<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\User;
use App\Form\EvaluationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/evaluation')]
class EvaluationController extends AbstractController
{
    #[Route('/', name: 'app_evaluation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        $evaluations = $entityManager
                ->getRepository(Evaluation::class)
                ->findAll();
       
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluations,
        ]);
    }
    
    
    
    
    
    

    #[Route('/new', name: 'app_evaluation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $id = $user->getId();
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);
        
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $evaluation->setUser($user);
            $entityManager->persist($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('index_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evaluation/new.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }
    
    
    
    

    #[Route('/{id}', name: 'app_evaluation_show', methods: ['GET'])]
    public function show(Evaluation $evaluation): Response
    {
        return $this->render('evaluation/show.html.twig', [
            'evaluation' => $evaluation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evaluation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evaluation $evaluation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evaluation/edit.html.twig', [
            'evaluation' => $evaluation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evaluation_delete', methods: ['POST'])]
    public function delete(Request $request, Evaluation $evaluation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evaluation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evaluation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evaluation_index', [], Response::HTTP_SEE_OTHER);
    }
}
