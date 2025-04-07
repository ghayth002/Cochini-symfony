<?php
$user = 'root';
$pass = '';
$host = '127.0.0.1';
$port = '3306';
$dbname = 'pi_dev';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check column names with case sensitivity
    $stmt = $pdo->query("SELECT COLUMN_NAME, DATA_TYPE 
                       FROM INFORMATION_SCHEMA.COLUMNS 
                       WHERE TABLE_SCHEMA = 'pi_dev' 
                       AND TABLE_NAME = 'reclamation'
                       ORDER BY ORDINAL_POSITION");
    
    echo "Columns in reclamation table (with exact case):\n";
    $columns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['COLUMN_NAME'];
        echo "- " . $row['COLUMN_NAME'] . " (" . $row['DATA_TYPE'] . ")\n";
    }
    
    // Try both type_r and typeR column
    echo "\nTesting case sensitivity in queries:\n";
    
    // Try with exact case (typeR)
    if (in_array('typeR', $columns)) {
        try {
            $stmt = $pdo->query("SELECT IdReclamation, typeR FROM reclamation LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "- Query with 'typeR' succeeded: " . print_r($result, true) . "\n";
        } catch (PDOException $e) {
            echo "- Query with 'typeR' failed: " . $e->getMessage() . "\n";
        }
    }
    
    // Try with lowercase (type_r)
    try {
        $stmt = $pdo->query("SELECT IdReclamation, type_r FROM reclamation LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "- Query with 'type_r' succeeded: " . print_r($result, true) . "\n";
    } catch (PDOException $e) {
        echo "- Query with 'type_r' failed: " . $e->getMessage() . "\n";
    }
    
    // Check Doctrine naming strategy by creating a table with underscore
    echo "\nTesting Doctrine naming strategy:\n";
    try {
        // Create a test table with underscore columns
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_naming (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    column_name VARCHAR(50),
                    columnName VARCHAR(50)
                   )");
        
        echo "- Created test_naming table\n";
        
        // Check columns
        $stmt = $pdo->query("SHOW COLUMNS FROM test_naming");
        echo "- Columns in test_naming table:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - " . $row['Field'] . "\n";
        }
        
        // Drop the table when done
        $pdo->exec("DROP TABLE test_naming");
        echo "- Dropped test_naming table\n";
    } catch (PDOException $e) {
        echo "- Error with test table: " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo 'Database Error: ' . $e->getMessage() . "\n";
} 