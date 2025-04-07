<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\ReclamationType;
use App\Form\ReponseType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PdfService;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\FormError;

class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'app_reclamation_index')]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        // Manually fetch all reclamations
        $reclamations = $reclamationRepository->findBy([], ['date' => 'DESC']);
        
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/reclamation/new', name: 'app_reclamation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $reclamation = new Reclamation();
        
        // Use an existing user from the database instead of a mock user
        $user = $userRepository->find(2); // Using user ID 2 which exists in the database
        
        if (!$user) {
            $this->addFlash('error', 'Erreur: Impossible de trouver un utilisateur valide.');
            return $this->redirectToRoute('app_reclamation_index');
        }
        
        $reclamation->setAdherent($user);
        $reclamation->setDate(new \DateTime());
        $reclamation->setStatut(false); // Initialize as not treated
        
        // The coach field is optional in the form, but the database requires a value
        // So we'll set a default coach (the same as the adherent for now)
        $reclamation->setCoach($user);
        
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Make sure all required fields are set according to DB schema
                if ($form->get('coach')->getData() === null) {
                    // If no coach was selected, use the default one
                    $reclamation->setCoach($user);
                }
                
                // Ensure typeR matches one of the enum values in the database
                $validTypes = ['PRODUIT', 'COACH', 'ADHERENT', 'EVENEMENT'];
                $submittedType = $reclamation->getTypeR();
                
                // Map the user-friendly type to database enum value
                switch ($submittedType) {
                    case 'Problème avec un adhérant':
                        $reclamation->setTypeR('ADHERENT');
                        break;
                    case 'Problème avec un coach':
                        $reclamation->setTypeR('COACH');
                        break;
                    case 'Problème avec un produit':
                        $reclamation->setTypeR('PRODUIT');
                        break;
                    case 'Problème avec un événement':
                        $reclamation->setTypeR('EVENEMENT');
                        break;
                    default:
                        $reclamation->setTypeR('PRODUIT'); // Default value
                }
                
                $entityManager->persist($reclamation);
                $entityManager->flush();

                $this->addFlash('success', 'Réclamation envoyée avec succès !');
                return $this->redirectToRoute('app_reclamation_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de la réclamation: ' . $e->getMessage());
            }
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
            try {
                // Ensure required fields are set
                if ($form->get('coach')->getData() === null) {
                    // If no coach was selected, use the adherent
                    $reclamation->setCoach($reclamation->getAdherent());
                }
                
                // Map the user-friendly type to database enum value
                $submittedType = $reclamation->getTypeR();
                switch ($submittedType) {
                    case 'Problème avec un adhérant':
                        $reclamation->setTypeR('ADHERENT');
                        break;
                    case 'Problème avec un coach':
                        $reclamation->setTypeR('COACH');
                        break;
                    case 'Problème avec un produit':
                        $reclamation->setTypeR('PRODUIT');
                        break;
                    case 'Problème avec un événement':
                        $reclamation->setTypeR('EVENEMENT');
                        break;
                    default:
                        $reclamation->setTypeR('PRODUIT'); // Default value
                }
                
                $entityManager->flush();
                
                $this->addFlash('success', 'Réclamation modifiée avec succès !');
                return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamation->getIdReclamation()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de la réclamation: ' . $e->getMessage());
            }
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
            $this->addFlash('error', 'Réclamation non trouvée.');
            return $this->redirectToRoute('app_reclamation_index');
        }
        
        // Check CSRF token for security
        $csrfToken = $request->request->get('_token');
        $expectedToken = 'delete' . $reclamation->getIdReclamation();
        
        if (!$this->isCsrfTokenValid($expectedToken, $csrfToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_reclamation_index');
        }
        
        // Check if the reclamation has been processed already
        if ($reclamation->getStatut()) {
            $this->addFlash('warning', 'Une réclamation traitée ne peut plus être supprimée.');
            return $this->redirectToRoute('app_reclamation_index');
        }
        
        try {
            // Try to remove any related replies first
            $reponses = $entityManager->getRepository(Reponse::class)
                ->findBy(['reclamation' => $reclamation->getIdReclamation()]);
            
            foreach ($reponses as $reponse) {
                $entityManager->remove($reponse);
            }
            
            // Then remove the reclamation
            $entityManager->remove($reclamation);
            $entityManager->flush();
            
            $this->addFlash('success', 'Réclamation #' . $id . ' supprimée avec succès !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la réclamation: ' . $e->getMessage());
        }
        
        return $this->redirectToRoute('app_reclamation_index');
    }

    #[Route('/admin/reclamation', name: 'app_admin_reclamation_index')]
    public function adminIndex(Request $request, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager): Response
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
        
        try {
            // Use a custom query to avoid column name issues
            if ($startDate || $endDate) {
                $qb = $entityManager->createQueryBuilder()
                    ->select('r')
                    ->from('App:Reclamation', 'r')
                    ->orderBy('r.date', 'DESC');
                
                if ($startDate) {
                    $startDate->setTime(0, 0, 0);
                    $qb->andWhere('r.date >= :startDate')
                       ->setParameter('startDate', $startDate);
                }
                
                if ($endDate) {
                    $endDate->setTime(23, 59, 59);
                    $qb->andWhere('r.date <= :endDate')
                       ->setParameter('endDate', $endDate);
                }
                
                $reclamations = $qb->getQuery()->getResult();
            } else {
                // If no date filters, get all reclamations using direct query
                $reclamations = $entityManager->createQuery(
                    'SELECT r FROM App:Reclamation r ORDER BY r.date DESC'
                )->getResult();
            }
        } catch (\Exception $e) {
            // Fallback to simple findAll if there's an issue with the query
            $reclamations = $reclamationRepository->findAll();
            $this->addFlash('warning', 'Filtrage de date temporairement indisponible. Affichage de toutes les réclamations.');
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

        // Create a form for the response with validation constraints
        $form = $this->createFormBuilder($reponse)
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre réponse',
                'attr' => [
                    'rows' => 5,
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre réponse ici...',
                    'maxlength' => 2000
                ],
                'help' => 'Minimum 5 caractères, maximum 2000 caractères.',
                'constraints' => [
                    new NotBlank(['message' => 'Le contenu de la réponse ne peut pas être vide.']),
                    new Length([
                        'min' => 5,
                        'max' => 2000,
                        'minMessage' => 'La réponse doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'La réponse ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    // Change status to "valider" when admin responds
                    $reponse->setStatus('valider');
                    
                    // Set the reclamation status to true (treated)
                    $reclamation->setStatut(true);
                    
                    $entityManager->persist($reponse);
                    $entityManager->persist($reclamation);
                    $entityManager->flush();

                    $this->addFlash('success', 'Réponse envoyée avec succès !');
                    return $this->redirectToRoute('app_admin_reclamation_show', ['id' => $reclamation->getIdReclamation()]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de la réponse: ' . $e->getMessage());
                }
            } else {
                // Add global form error message
                $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez les corriger avant de soumettre.');
            }
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