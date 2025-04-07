<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PdfService;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation_index')]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        // Get all reclamations instead of filtering by user
        $reclamations = $reclamationRepository->findAll();

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/reclamation/new', name: 'app_reclamation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        
        // Mock user data - in a real app, this would be the logged-in user
        $user = new User();
        $user->setId(1);
        
        $reclamation->setAdherent($user);
        $reclamation->setDate(new \DateTime());
        $reclamation->setStatut(false); // Initialize as not treated
        
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Réclamation envoyée avec succès !');
            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/{id}', name: 'app_reclamation_show')]
    public function show(int $id, ReclamationRepository $reclamationRepository, ReponseRepository $reponseRepository): Response
    {
        // Manually fetch the reclamation entity
        $reclamation = $reclamationRepository->find($id);
        
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }
        
        // Get all responses for this reclamation
        $reponses = $reponseRepository->findBy(['reclamation' => $reclamation->getIdReclamation()]);

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
            'reponses' => $reponses
        ]);
    }

    #[Route('/reclamation/{id}/edit', name: 'app_reclamation_edit')]
    public function edit(Request $request, int $id, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager): Response
    {
        // Manually fetch the reclamation entity
        $reclamation = $reclamationRepository->find($id);
        
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }
        
        // Check if the reclamation has been processed already
        if ($reclamation->getStatut()) {
            $this->addFlash('warning', 'Une réclamation traitée ne peut plus être modifiée.');
            return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getIdReclamation()]);
        }
        
        // Create form
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Réclamation modifiée avec succès !');
            return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getIdReclamation()]);
        }
        
        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/reclamation/{id}/delete', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager): Response
    {
        // Manually fetch the reclamation entity
        $reclamation = $reclamationRepository->find($id);
        
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }
        
        // Check CSRF token for security
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdReclamation(), $request->request->get('_token'))) {
            // Check if the reclamation has been processed already
            if ($reclamation->getStatut()) {
                $this->addFlash('warning', 'Une réclamation traitée ne peut plus être supprimée.');
                return $this->redirectToRoute('app_reclamation_index');
            }
            
            $entityManager->remove($reclamation);
            $entityManager->flush();
            
            $this->addFlash('success', 'Réclamation supprimée avec succès !');
        }
        
        return $this->redirectToRoute('app_reclamation_index');
    }

    #[Route('/admin/reclamation', name: 'app_admin_reclamation_index')]
    public function adminIndex(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        // Get date filter parameters from request
        $startDate = null;
        $endDate = null;
        
        if ($request->query->has('startDate') && $request->query->get('startDate')) {
            try {
                $startDate = new \DateTime($request->query->get('startDate'));
            } catch (\Exception $e) {
                // Invalid date format, ignore
            }
        }
        
        if ($request->query->has('endDate') && $request->query->get('endDate')) {
            try {
                $endDate = new \DateTime($request->query->get('endDate'));
            } catch (\Exception $e) {
                // Invalid date format, ignore
            }
        }
        
        // Get filtered reclamations
        if ($startDate || $endDate) {
            $reclamations = $reclamationRepository->findByDateRange($startDate, $endDate);
        } else {
            // If no date filters, get all reclamations
            $reclamations = $reclamationRepository->findAll();
        }

        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'startDate' => $startDate ? $startDate->format('Y-m-d') : '',
            'endDate' => $endDate ? $endDate->format('Y-m-d') : '',
        ]);
    }

    #[Route('/admin/reclamation/{id}', name: 'app_admin_reclamation_show')]
    public function adminShow(int $id, ReclamationRepository $reclamationRepository, Request $request, EntityManagerInterface $entityManager, ReponseRepository $reponseRepository): Response
    {
        // Manually fetch the reclamation entity
        $reclamation = $reclamationRepository->find($id);
        
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }
        
        // Create a new response
        $reponse = new Reponse();
        $reponse->setReclamation($reclamation);
        $reponse->setDateReponse(new \DateTime());
        $reponse->setStatus('en attente');

        // Create a simple form for the response
        $form = $this->createFormBuilder($reponse)
            ->add('contenu', null, [
                'label' => 'Votre réponse',
                'attr' => ['rows' => 5]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Change status to "valider" when admin responds
            $reponse->setStatus('valider');
            
            // Set the reclamation status to true (treated)
            $reclamation->setStatut(true);
            
            $entityManager->persist($reponse);
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success', 'Réponse envoyée avec succès !');
            return $this->redirectToRoute('app_admin_reclamation_show', ['id' => $reclamation->getIdReclamation()]);
        }

        // Get all responses for this reclamation
        $reponses = $reponseRepository->findBy(['reclamation' => $reclamation->getIdReclamation()]);

        return $this->render('admin/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
            'reponses' => $reponses,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/reclamation/list/pdf', name: 'app_admin_reclamation_list_pdf')]
    public function generateReclamationListPdf(ReclamationRepository $reclamationRepository, PdfService $pdfService): Response
    {
        // Get all reclamations
        $reclamations = $reclamationRepository->findAll();
        
        // Generate HTML content for PDF
        $html = $this->renderView('admin/reclamation/pdf_list_template.html.twig', [
            'reclamations' => $reclamations
        ]);
        
        // Generate the PDF
        return new Response(
            $pdfService->generateBinaryPDF($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="liste-reclamations.pdf"'
            ]
        );
    }

    #[Route('/admin/reclamation/{id}/pdf', name: 'app_admin_reclamation_pdf')]
    public function generateReclamationPdf(int $id, ReclamationRepository $reclamationRepository, ReponseRepository $reponseRepository, PdfService $pdfService): Response
    {
        // Manually fetch the reclamation entity
        $reclamation = $reclamationRepository->find($id);
        
        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation non trouvée');
        }
        
        // Get all responses for this reclamation
        $reponses = $reponseRepository->findBy(['reclamation' => $reclamation->getIdReclamation()]);
        
        // Generate HTML content for PDF
        $html = $this->renderView('admin/reclamation/pdf_template.html.twig', [
            'reclamation' => $reclamation,
            'reponses' => $reponses
        ]);
        
        // Generate the PDF
        return new Response(
            $pdfService->generateBinaryPDF($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="reclamation-' . $reclamation->getIdReclamation() . '.pdf"'
            ]
        );
    }
} 