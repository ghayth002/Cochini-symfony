<?php

namespace App\Service;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

class EmailService
{
    private Mailer $mailer;
    private string $fromEmail;
    private ?LoggerInterface $logger;
    private bool $debugMode = false;

    // Mailtrap credentials - hardcoded
    private string $smtpHost = 'sandbox.smtp.mailtrap.io';
    private int $smtpPort = 2525;
    private string $smtpUsername = '58758cbf58c694';
    private string $smtpPassword = '310f4b9ed48247';

    public function __construct(
        ParameterBagInterface $params,
        ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
        
        // Set up transport directly with hardcoded credentials
        $dsn = "smtp://{$this->smtpUsername}:{$this->smtpPassword}@{$this->smtpHost}:{$this->smtpPort}";
        $transport = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);
        
        // Default sender email address (can be configured in services.yaml)
        $this->fromEmail = $params->has('app.email_from') 
            ? $params->get('app.email_from') 
            : 'reclamation@yourdomain.com';
            
        // Log the from email being used to help with debugging
        if ($this->logger) {
            $this->logger->info('Email service initialized with from address: ' . $this->fromEmail);
            $this->logger->info('Using Mailtrap SMTP: ' . $this->smtpHost . ':' . $this->smtpPort);
        }
    }

    /**
     * Send an email notification about a response to a reclamation
     * 
     * @throws \Exception if email cannot be sent
     */
    public function sendReclamationResponseEmail(Reclamation $reclamation, Reponse $reponse): void
    {
        $adherent = $reclamation->getAdherent();
        
        // If no email address is available, we can't send the notification
        if (!$adherent) {
            $errorMsg = 'Cannot send email: No adherent found for reclamation #' . $reclamation->getIdReclamation();
            if ($this->logger) {
                $this->logger->error($errorMsg);
            }
            throw new \Exception($errorMsg);
        }
        
        $toEmail = $adherent->getEmail();
        if (!$toEmail) {
            $errorMsg = 'Cannot send email: Adherent (ID: ' . $adherent->getId() . ') has no email address';
            if ($this->logger) {
                $this->logger->error($errorMsg);
            }
            throw new \Exception($errorMsg);
        }
        
        try {
            if ($this->logger) {
                $this->logger->info('Preparing to send email notification for reclamation #' . 
                    $reclamation->getIdReclamation() . ' from ' . $this->fromEmail . ' to ' . $toEmail);
            }
            
            // Build email with all details
            $email = new Email();
            $email->from($this->fromEmail)
                  ->to($toEmail)
                  ->subject('Réponse à votre réclamation #' . $reclamation->getIdReclamation())
                  ->html($this->getResponseEmailTemplate($reclamation, $reponse));
            
            // Add detailed headers to help debug
            $email->getHeaders()
                  ->addTextHeader('X-Mailer', 'FitHabit-Mailer')
                  ->addTextHeader('X-Reclamation-ID', $reclamation->getIdReclamation())
                  ->addTextHeader('X-Adherent-ID', $adherent->getId());
                
            // Send the email
            $this->mailer->send($email);
            
            if ($this->logger) {
                $this->logger->info('Email notification sent successfully for reclamation #' . $reclamation->getIdReclamation());
            }
        } catch (\Exception $e) {
            $errorMsg = 'Failed to send email for reclamation #' . $reclamation->getIdReclamation() . ': ' . $e->getMessage();
            if ($this->logger) {
                $this->logger->error($errorMsg);
                $this->logger->error('Stack trace: ' . $e->getTraceAsString());
            }
            throw new \Exception($errorMsg, 0, $e);
        }
    }
    
    /**
     * Generate the HTML template for the response email
     */
    private function getResponseEmailTemplate(Reclamation $reclamation, Reponse $reponse): string
    {
        $template = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réponse à votre réclamation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #4a86e8;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777;
        }
        .response {
            background-color: #f5f5f5;
            padding: 15px;
            border-left: 4px solid #4a86e8;
            margin: 20px 0;
        }
        .reclamation {
            background-color: #f8f8f8;
            padding: 15px;
            border-left: 4px solid #ccc;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Réponse à votre réclamation</h1>
    </div>
    <div class="content">
        <p>Bonjour {$reclamation->getAdherent()->getPrenom()},</p>
        
        <p>Nous avons traité votre réclamation du {$reclamation->getDate()->format('d/m/Y')}.</p>
        
        <h2>Votre réclamation</h2>
        <div class="reclamation">
            <p><strong>Type:</strong> {$reclamation->getTypeR()}</p>
            <p><strong>Description:</strong><br>{$reclamation->getDescription()}</p>
        </div>
        
        <h2>Notre réponse</h2>
        <div class="response">
            <p>{$reponse->getContenu()}</p>
            <p><em>Date de réponse: {$reponse->getDateReponse()->format('d/m/Y')}</em></p>
        </div>
        
        <p>Si vous avez d'autres questions, n'hésitez pas à nous contacter.</p>
        
        <p>Cordialement,<br>L'équipe FitHabit</p>
    </div>
    <div class="footer">
        <p>Ceci est un message automatique, merci de ne pas y répondre.</p>
        <p>&copy; 2023 FitHabit - Tous droits réservés</p>
    </div>
</body>
</html>
HTML;

        return $template;
    }
} 