// Script to check if user with ID 1 exists
// Load Symfony's bootstrap
require_once dirname(__DIR__).'/config/bootstrap.php';

use App\Entity\Reclamation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Dotenv\Dotenv;

// Load environment variables
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

// Get the Symfony kernel for the current environment
$kernel = new \App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

// Get the container and the entity manager
$container = $kernel->getContainer();
$entityManager = $container->get('doctrine.orm.entity_manager');

// Check if User with ID 1 exists
$user = $entityManager->getRepository(User::class)->find(1);

if ($user) {
    echo "User found: " . $user->getNom() . " " . $user->getPrenom() . " (ID: " . $user->getId() . ")\n";
    
    // Try to create a reclamation
    try {
        $reclamation = new Reclamation();
        $reclamation->setAdherent($user);
        $reclamation->setDate(new \DateTime());
        $reclamation->setStatut(false);
        $reclamation->setDescription("Test reclamation description");
        $reclamation->setTypeR("Problème avec un adhérant");
        
        // Persist and flush
        $entityManager->persist($reclamation);
        $entityManager->flush();
        
        echo "Reclamation created successfully! ID: " . $reclamation->getIdReclamation() . "\n";
    } catch (\Exception $e) {
        echo "Failed to create reclamation: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
} else {
    echo "No user found with ID 1. Creating a test user...\n";
    
    // Create a test user
    try {
        $newUser = new User();
        $newUser->setNom("Test");
        $newUser->setPrenom("User");
        $newUser->setEmail("test@example.com");
        
        // Persist and flush
        $entityManager->persist($newUser);
        $entityManager->flush();
        
        echo "Test user created with ID: " . $newUser->getId() . "\n";
        
        // Try to create a reclamation with the new user
        $reclamation = new Reclamation();
        $reclamation->setAdherent($newUser);
        $reclamation->setDate(new \DateTime());
        $reclamation->setStatut(false);
        $reclamation->setDescription("Test reclamation description");
        $reclamation->setTypeR("Problème avec un adhérant");
        
        // Persist and flush
        $entityManager->persist($reclamation);
        $entityManager->flush();
        
        echo "Reclamation created successfully! ID: " . $reclamation->getIdReclamation() . "\n";
    } catch (\Exception $e) {
        echo "Failed to create user or reclamation: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
} 