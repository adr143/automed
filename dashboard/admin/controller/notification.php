<?php
include_once __DIR__ . '/../../../database/dbconfig.php';
require_once '../authentication/admin-class.php';
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

// Directory for quick JSON cache
$dataDir = 'medicine_data/';
$timeoutDuration = 60; // seconds (1 minute timeout)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0777, true);
    }

    $data = file_get_contents('php://input');
    $medicineData = json_decode($data, true);

    if (isset($medicineData['medicine_name']) && isset($medicineData['dispenseTime']) && isset($medicineData['day'])) {
        $medicineName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $medicineData['medicine_name']);
        $dataFile = $dataDir . $medicineName . '.json';

        $medicineData['timestamp'] = time();

        file_put_contents($dataFile, json_encode($medicineData, JSON_PRETTY_PRINT));

        try {
            $database = new Database();
            $conn = $database->dbConnection();

            $query = "INSERT INTO medicine_logs (medicine_name, dispense_time, day, taken) 
                      VALUES (:medicine_name, :dispense_time, :day, :taken)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':medicine_name', $medicineData['medicine_name']);
            $stmt->bindParam(':dispense_time', $medicineData['dispenseTime']);
            $stmt->bindParam(':day', $medicineData['day']);
            $stmt->bindValue(':taken', $medicineData['taken'] ? 1 : 0, PDO::PARAM_INT);
            $stmt->execute();

            // ===============================
            // EMAIL NOTIFICATION SECTION
            // ===============================
            $user = new ADMIN();
            $smtp_email = $user->smtpEmail();
            $smtp_password = $user->smtpPassword();
            $system_name = $user->systemName();

            $stmt = $user->runQuery("SELECT * FROM users WHERE id=:uid");
            $stmt->execute(array(":uid"=>1));
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            $email = $user_data['email'];
            $subject = "Medicine Alert: {$medicineData['medicine_name']}";

            $statusMsg = $medicineData['taken'] ? "Medicine successfully taken." : "Medicine missed or not taken.";
            $message = "
            <html>
            <head><title>Medicine Dispense Alert</title></head>
            <body>
                <p>Dear User,</p>
                <p>The medicine <b>{$medicineData['medicine_name']}</b> was scheduled at <b>{$medicineData['dispenseTime']}</b> on <b>{$medicineData['day']}</b>.</p>
                <p>Status: <b>$statusMsg</b></p>
                <p>Thank you,<br>$system_name</p>
            </body>
            </html>";

            // Send email
            $user->send_mail($email, $message, $subject, $smtp_email, $smtp_password, $system_name);

            echo json_encode(["status" => "success", "message" => "Medicine data saved and email sent"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid or missing fields"]);
    }

} else {
    // GET REQUEST – list latest medicine status
    $allData = [];
    foreach (glob($dataDir . '*.json') as $filename) {
        $deviceData = json_decode(file_get_contents($filename), true);
        $currentTime = time();
        $dataAge = $currentTime - ($deviceData['timestamp'] ?? 0);

        if ($dataAge <= $timeoutDuration) {
            $allData[] = $deviceData;
        } else {
            $allData[] = [
                'wifi_status' => 'No recent update',
                'medicine_name' => basename($filename, '.json'),
                'dispenseTime' => '',
                'day' => '',
                'taken' => false,
                'timestamp' => 0
            ];
        }
    }

    echo json_encode($allData, JSON_PRETTY_PRINT);
}
?>