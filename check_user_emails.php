<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\DBAL\DriverManager;

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');
$dotenv->loadEnv(__DIR__.'/.env.local');

// Database connection parameters from environment variables
$databaseUrl = $_ENV['DATABASE_URL'] ?? null;

if (!$databaseUrl) {
    echo "ERROR: DATABASE_URL environment variable is not set!\n";
    exit(1);
}

try {
    // Parse database URL
    $params = parse_url($databaseUrl);
    $dbParams = [
        'driver' => 'pdo_mysql',
        'host' => $params['host'] ?? 'localhost',
        'port' => $params['port'] ?? 3306,
        'dbname' => substr($params['path'], 1),
        'user' => $params['user'],
        'password' => $params['pass'],
        'charset' => 'utf8mb4',
    ];

    // Connect to database
    $connection = DriverManager::getConnection($dbParams);
    echo "Connected to the database successfully!\n\n";

    // Query to check user emails
    $result = $connection->executeQuery('SELECT id, Nom, Prenom, Email FROM user');
    $users = $result->fetchAllAssociative();

    if (count($users) === 0) {
        echo "No users found in the database.\n";
    } else {
        echo "User Email Information:\n";
        echo "---------------------\n";
        
        $validEmails = 0;
        $missingEmails = 0;
        
        foreach ($users as $user) {
            $id = $user['id'];
            $nom = $user['Nom'] ?? 'N/A';
            $prenom = $user['Prenom'] ?? 'N/A';
            $email = $user['Email'];
            
            echo "User ID: $id\n";
            echo "Name: $prenom $nom\n";
            echo "Email: " . ($email ? $email : "MISSING") . "\n";
            
            if (!empty($email)) {
                $validEmails++;
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "    WARNING: Invalid email format!\n";
                }
            } else {
                $missingEmails++;
            }
            
            echo "---------------------\n";
        }
        
        echo "\nSummary:\n";
        echo "Total users: " . count($users) . "\n";
        echo "Users with valid emails: $validEmails\n";
        echo "Users missing emails: $missingEmails\n";
    }
    
    // Check reclamations with adherents
    echo "\n\nChecking Reclamations and Adherents:\n";
    echo "---------------------\n";
    
    $result = $connection->executeQuery('
        SELECT r.IdReclamation, r.Id_adherent, u.Email
        FROM reclamation r
        LEFT JOIN user u ON r.Id_adherent = u.id
        ORDER BY r.IdReclamation DESC
        LIMIT 10
    ');
    
    $reclamations = $result->fetchAllAssociative();
    
    if (count($reclamations) === 0) {
        echo "No recent reclamations found.\n";
    } else {
        echo "Latest Reclamations:\n";
        
        foreach ($reclamations as $reclamation) {
            $id = $reclamation['IdReclamation'];
            $adherentId = $reclamation['Id_adherent'];
            $email = $reclamation['Email'];
            
            echo "Reclamation ID: $id\n";
            echo "Adherent ID: $adherentId\n";
            echo "Adherent Email: " . ($email ? $email : "MISSING") . "\n";
            echo "---------------------\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 