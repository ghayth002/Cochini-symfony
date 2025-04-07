<?php
// Script to test reclamation creation with proper type conversion
require_once __DIR__.'/vendor/autoload.php';

use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;

// Load environment variables
(new Dotenv())->bootEnv(__DIR__.'/.env');

// Get the Symfony kernel for the current environment
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

// Get the container and the entity manager
$container = $kernel->getContainer();
$entityManager = $container->get('doctrine.orm.entity_manager');

// Find user with ID 2 (which we know exists from our previous checks)
$user = $entityManager->getRepository(User::class)->find(2);

if (!$user) {
    echo "Error: User with ID 2 not found.\n";
    exit(1);
}

echo "Found user: {$user->getNom()} {$user->getPrenom()} (ID: {$user->getId()})\n";

try {
    // Create a reclamation with proper type values
    $reclamation = new Reclamation();
    $reclamation->setAdherent($user);   // Set adherent
    $reclamation->setCoach($user);      // Set coach (required by DB)
    $reclamation->setDate(new \DateTime());
    $reclamation->setStatut(false);
    $reclamation->setDescription("Test reclamation description - created with proper type conversion");
    
    // Set the type as one of the enum values accepted by the database
    // The form gives us user-friendly values, but DB needs enum values
    $formTypeValue = 'Problème avec un adhérant';
    $dbTypeValue = null;
    
    // Map user-friendly type to database enum value
    switch ($formTypeValue) {
        case 'Problème avec un adhérant':
            $dbTypeValue = 'ADHERENT';
            break;
        case 'Problème avec un coach':
            $dbTypeValue = 'COACH';
            break;
        case 'Problème avec un produit':
            $dbTypeValue = 'PRODUIT';
            break;
        case 'Problème avec un événement':
            $dbTypeValue = 'EVENEMENT';
            break;
        default:
            $dbTypeValue = 'PRODUIT'; // Default value
    }
    
    // Set the proper enum value
    $reclamation->setTypeR($dbTypeValue);
    
    // Persist and flush
    $entityManager->persist($reclamation);
    $entityManager->flush();
    
    echo "SUCCESS: Reclamation created successfully with ID: " . $reclamation->getIdReclamation() . "\n";
    echo "Type value stored in database: " . $reclamation->getTypeR() . "\n";
} catch (\Exception $e) {
    echo "ERROR: Failed to create reclamation: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} 