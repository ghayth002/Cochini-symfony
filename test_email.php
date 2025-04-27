<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

// Load environment variables from .env.local
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');
$dotenv->loadEnv(__DIR__.'/.env.local');

// Display configuration 
echo "Configuration:\n";
echo "MAILER_DSN: " . $_ENV['MAILER_DSN'] . "\n";
echo "MAILER_FROM: " . ($_ENV['MAILER_FROM'] ?? 'Not set') . "\n\n";

try {
    // Create Transport and Mailer
    $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
    $mailer = new Mailer($transport);
    
    // Create Email
    $email = (new Email())
        ->from($_ENV['MAILER_FROM'] ?? 'test@fithabit.com')
        ->to('recipient@example.com')
        ->subject('Test Email from Symfony Mailer')
        ->text('This is a test email to verify Mailtrap configuration is working.')
        ->html('<p>This is a <strong>test email</strong> to verify Mailtrap configuration is working.</p>');
    
    // Send Email
    $mailer->send($email);
    
    echo "Test email sent successfully! Please check your Mailtrap inbox.\n";
} catch (\Exception $e) {
    echo "Error sending email: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} 