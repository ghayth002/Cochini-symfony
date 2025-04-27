<?php

// This is a standalone script to test sending reclamation emails

require __DIR__.'/vendor/autoload.php';

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

// Load environment variables
$dotenv = new Dotenv();
if (file_exists(__DIR__.'/.env')) {
    $dotenv->loadEnv(__DIR__.'/.env');
}
if (file_exists(__DIR__.'/.env.local')) {
    $dotenv->load(__DIR__.'/.env.local');
}

// Add debug environment
$_ENV['MAILER_DEBUG'] = 'true';

// Display configuration
echo "Configuration:\n";
echo "MAILER_DSN: " . $_ENV['MAILER_DSN'] . "\n";
echo "MAILER_FROM: " . ($_ENV['MAILER_FROM'] ?? 'Not set') . "\n";
echo "MAILER_DEBUG: " . ($_ENV['MAILER_DEBUG'] ?? 'false') . "\n\n";

// Create mock User (adherent)
$user = new User();
$user->setId(999);
$user->setNom('Test');
$user->setPrenom('User');
$user->setEmail('test-recipient@mailtrap.io');

// Create mock Reclamation
$reclamation = new Reclamation();
$reclamation->setIdReclamation(888);
$reclamation->setAdherent($user);
$reclamation->setDate(new \DateTime());
$reclamation->setTypeR('PRODUIT');
$reclamation->setDescription('Ceci est une réclamation de test pour vérifier l\'envoi d\'emails.');

// Create mock Reponse
$reponse = new Reponse();
$reponse->setReclamation($reclamation);
$reponse->setDateReponse(new \DateTime());
$reponse->setContenu('Ceci est une réponse de test pour vérifier l\'envoi d\'emails. Nous vous remercions pour votre réclamation.');

try {
    // Generate email HTML (simplified version of what's in EmailService)
    $emailHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réponse à votre réclamation</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background-color: #4a86e8; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; }
        .response { background-color: #f5f5f5; padding: 15px; margin: 20px 0; }
        .reclamation { background-color: #f8f8f8; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Réponse à votre réclamation (TEST)</h1>
    </div>
    <div class="content">
        <p>Bonjour {$user->getPrenom()},</p>
        
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
</body>
</html>
HTML;

    // Create Transport and Mailer
    $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
    $mailer = new Mailer($transport);
    
    // Create Email
    $email = (new Email())
        ->from($_ENV['MAILER_FROM'] ?? 'test@fithabit.com')
        ->to('test-recipient@mailtrap.io')
        ->subject('[TEST] Réponse à votre réclamation #888')
        ->html($emailHtml);
    
    // Add headers to help debug
    $email->getHeaders()
          ->addTextHeader('X-Mailer', 'FitHabit-Test-Mailer')
          ->addTextHeader('X-Test-Mode', 'true');
    
    // Send Email
    $mailer->send($email);
    
    echo "Test reclamation email sent successfully! Please check your Mailtrap inbox.\n";
    echo "Email was sent from: " . ($_ENV['MAILER_FROM'] ?? 'test@fithabit.com') . "\n";
    echo "Email was sent to: test-recipient@mailtrap.io\n";
} catch (\Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} 