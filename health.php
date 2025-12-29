<?php
/**
 * Health Check Endpoint for Railway Deployment
 */

header('Content-Type: application/json');

try {
    // Check if installation is complete
    $installed = file_exists('.installed');
    
    // Check database connection if installed
    $dbStatus = false;
    if ($installed) {
        require_once 'database/dbconfig.php';
        $database = new Database();
        $conn = $database->dbConnection();
        $dbStatus = $conn !== null;
    }

    $response = [
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'installation' => $installed ? 'complete' : 'pending',
        'database' => $dbStatus ? 'connected' : 'disconnected',
        'environment' => getenv('RAILWAY_ENVIRONMENT') ? 'railway' : 'local'
    ];

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'unhealthy',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>