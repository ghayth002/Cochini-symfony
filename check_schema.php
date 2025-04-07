<?php
require_once 'vendor/autoload.php';

// Database connection
$user = 'root';
$pass = '';
$host = '127.0.0.1';
$port = '3306';
$dbname = 'pi_dev';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Get actual database structure
    $stmt = $pdo->query("SHOW COLUMNS FROM reclamation");
    $dbColumns = [];
    
    echo "Database columns in reclamation table:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dbColumns[] = $row['Field'];
        echo "- " . $row['Field'] . "\n";
    }
    
    // 2. Check for typeR/type_r inconsistency
    echo "\nChecking for typeR or type_r column:\n";
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_schema = 'pi_dev' AND table_name = 'reclamation' AND (column_name = 'typeR' OR column_name = 'type_r')");
    $typeColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($typeColumns as $column) {
        echo "- Found column: " . $column . "\n";
    }
    
    // 3. Check for statut column specifically
    echo "\nChecking for statut column:\n";
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_schema = 'pi_dev' AND table_name = 'reclamation' AND column_name = 'statut'");
    $statutColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($statutColumns) > 0) {
        echo "- Statut column exists as: " . $statutColumns[0] . "\n";
        
        // Get sample data
        $stmt = $pdo->query("SELECT IdReclamation, statut FROM reclamation LIMIT 3");
        $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($samples) > 0) {
            echo "\nSample data:\n";
            foreach ($samples as $sample) {
                echo "- ID: " . $sample['IdReclamation'] . ", Statut: " . $sample['statut'] . "\n";
            }
        }
    } else {
        echo "- Statut column NOT found in database!\n";
    }
    
    // 4. Try to execute the problematic query
    echo "\nTrying to execute query with t0.statut:\n";
    try {
        $stmt = $pdo->query("SELECT t0.IdReclamation, t0.statut FROM reclamation t0 LIMIT 3");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($results) > 0) {
            echo "- Query succeeded!\n";
            foreach ($results as $result) {
                echo "  - ID: " . $result['IdReclamation'] . ", Statut: " . $result['statut'] . "\n";
            }
        } else {
            echo "- Query succeeded but returned no results.\n";
        }
    } catch (PDOException $e) {
        echo "- Query failed: " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo 'Database Error: ' . $e->getMessage() . "\n";
} 