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
use App\Service\BadWordApiService;
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
use App\Service\EmailService;

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
    public function new(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, BadWordApiService $badWordApiService): Response
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
                
                // Additional profanity check with detailed feedback
                $description = $reclamation->getDescription();
                $profanityCheck = $badWordApiService->checkProfanity($description);
                
                if ($profanityCheck['success'] && $profanityCheck['hasProfanity']) {
                    // Add form error with specific message about profanity
                    $form->get('description')->addError(new FormError('Votre texte contient des mots inappropriés. Veuillez utiliser un langage respectueux.'));
                    
                    // Display the filtered version as suggestion
                    $this->addFlash('warning', 'Suggestion de texte filtré: ' . $profanityCheck['filteredText']);
                    
                    // Return the form with errors
                    return $this->render('reclamation/new.html.twig', [
                        'form' => $form->createView(),
                    ]);
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
    public function edit(Request $request, int $id, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager, BadWordApiService $badWordApiService): Response
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
                
                // Additional profanity check with detailed feedback
                $description = $reclamation->getDescription();
                $profanityCheck = $badWordApiService->checkProfanity($description);
                
                if ($profanityCheck['success'] && $profanityCheck['hasProfanity']) {
                    // Add form error with specific message about profanity
                    $form->get('description')->addError(new FormError('Votre texte contient des mots inappropriés. Veuillez utiliser un langage respectueux.'));
                    
                    // Display the filtered version as suggestion
                    $this->addFlash('warning', 'Suggestion de texte filtré: ' . $profanityCheck['filteredText']);
                    
                    // Return the form with errors
                    return $this->render('reclamation/edit.html.twig', [
                        'reclamation' => $reclamation,
                        'form' => $form->createView(),
                    ]);
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
            // Create a query builder for better control
            $qb = $entityManager->createQueryBuilder();
            $qb->select('r')
               ->from(Reclamation::class, 'r')
               ->orderBy('r.date', 'DESC');
            
            // Add date filters if provided
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
            
            // Execute the query
            $reclamations = $qb->getQuery()->getResult();
            
        } catch (\Exception $e) {
            // Log the error for debugging
            error_log('Date filtering error: ' . $e->getMessage());
            
            // Fallback to getting all reclamations
            $reclamations = $reclamationRepository->findBy([], ['date' => 'DESC']);
            $this->addFlash('warning', 'Filtrage de date temporairement indisponible. Affichage de toutes les réclamations.');
        }

        return $this->render('admin/reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'startDate' => $startDate ? $startDate->format('Y-m-d') : '',
            'endDate' => $endDate ? $endDate->format('Y-m-d') : '',
        ]);
    }

    #[Route('/admin/reclamation/{id}', name: 'app_admin_reclamation_show')]
    public function adminShow(int $id, ReclamationRepository $reclamationRepository, Request $request, EntityManagerInterface $entityManager, ReponseRepository $reponseRepository, EmailService $emailService): Response
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
                    // Log for debugging
                    error_log('Processing valid form submission for reclamation #' . $id);
                    
                    // Change status to "valider" when admin responds
                    $reponse->setStatus('valider');
                    
                    // Set the reclamation status to true (treated)
                    $reclamation->setStatut(true);
                    
                    // First persist the entities
                    $entityManager->persist($reponse);
                    $entityManager->persist($reclamation);
                    $entityManager->flush();
                    
                    error_log('Reclamation and reponse saved to database');

                    // Verify adherent has an email
                    $adherent = $reclamation->getAdherent();
                    $adherentEmail = $adherent ? $adherent->getEmail() : null;
                    
                    error_log('Adherent email: ' . ($adherentEmail ?: 'NULL'));
                    
                    // Then try to send the email - USE DIRECT APPROACH for debugging
                    try {
                        // 1. First try the email service
                        error_log('Attempting to send email using EmailService');
                        $emailService->sendReclamationResponseEmail($reclamation, $reponse);
                        $this->addFlash('success', 'Réponse envoyée avec succès et un email de notification a été envoyé au client !');
                        error_log('Email sent successfully using EmailService');
                    } catch (\Exception $emailServiceException) {
                        error_log('EmailService failed: ' . $emailServiceException->getMessage());
                        
                        // 2. If that fails, try direct mailer approach
                        try {
                            error_log('Attempting direct mailer send as fallback');
                            
                            // Get mailer DSN and from address
                            $mailerDsn = $_ENV['MAILER_DSN'] ?? null;
                            $fromEmail = $_ENV['MAILER_FROM'] ?? 'noreply@fithabit.com';
                            
                            if ($mailerDsn && $adherentEmail) {
                                error_log('DSN and adherent email available, proceeding with direct send');
                                
                                // Create transport and mailer
                                $transport = \Symfony\Component\Mailer\Transport::fromDsn($mailerDsn);
                                $mailer = new \Symfony\Component\Mailer\Mailer($transport);
                                
                                // Build the email
                                $email = new \Symfony\Component\Mime\Email();
                                $email->from($fromEmail)
                                      ->to($adherentEmail)
                                      ->subject('Réponse à votre réclamation #' . $reclamation->getIdReclamation())
                                      ->text('Votre réclamation a été traitée. Contenu: ' . $reponse->getContenu())
                                      ->html($this->renderView('emails/reclamation_response.html.twig', [
                                          'reclamation' => $reclamation,
                                          'reponse' => $reponse
                                      ]));
                                
                                // Send the email
                                $mailer->send($email);
                                
                                error_log('Direct email send successful');
                                $this->addFlash('info', 'Réponse enregistrée et email envoyé via méthode alternative.');
                            } else {
                                error_log('Missing DSN or adherent email for direct send');
                                if (!$mailerDsn) error_log('DSN is missing');
                                if (!$adherentEmail) error_log('Adherent email is missing');
                                
                                $this->addFlash('warning', 'Réponse enregistrée mais impossible d\'envoyer l\'email: Configuration incomplète');
                            }
                        } catch (\Exception $directMailerException) {
                            error_log('Direct mailer also failed: ' . $directMailerException->getMessage());
                            $this->addFlash('warning', 'Réponse enregistrée mais l\'envoi de l\'email a échoué malgré plusieurs tentatives: ' . $directMailerException->getMessage());
                        }
                    }
                    
                    return $this->redirectToRoute('app_admin_reclamation_show', ['id' => $reclamation->getIdReclamation()]);
                } catch (\Exception $e) {
                    error_log('General exception: ' . $e->getMessage());
                    error_log($e->getTraceAsString());
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
    public function generateReclamationListPdf(Request $request, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager, PdfService $pdfService): Response
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
            // Use the same query builder approach as in adminIndex
            $qb = $entityManager->createQueryBuilder();
            $qb->select('r')
               ->from(Reclamation::class, 'r')
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
            
        } catch (\Exception $e) {
            // Fallback to getting all reclamations
            $reclamations = $reclamationRepository->findBy([], ['date' => 'DESC']);
        }
        
        // Generate HTML content for PDF
        $html = $this->renderView('admin/reclamation/pdf_list_template.html.twig', [
            'reclamations' => $reclamations,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'filterActive' => ($startDate || $endDate)
        ]);
        
        // Generate the PDF
        $filename = 'liste-reclamations';
        if ($startDate && $endDate) {
            $filename .= '-du-' . $startDate->format('d-m-Y') . '-au-' . $endDate->format('d-m-Y');
        } elseif ($startDate) {
            $filename .= '-depuis-' . $startDate->format('d-m-Y');
        } elseif ($endDate) {
            $filename .= '-jusquau-' . $endDate->format('d-m-Y');
        }
        
        return new Response(
            $pdfService->generateBinaryPDF($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"'
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