<?php
include_once __DIR__ . '/../../../database/dbconfig.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

class MedicineSlot
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->dbConnection();
    }

    // Fetch all day mappings once
    private function getDayMapping()
    {
        $query = "SELECT id, day FROM day";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $days = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $days[trim($row['id'])] = trim($row['day']);
        }
        return $days;
    }

    // Fetch all slots with human-readable days and medicine name
    public function getAllSlots()
    {
        $query = "SELECT 
                    ms.slot_id, 
                    m.medicine_name, 
                    ms.dispense_time, 
                    ms.dispense_days
                  FROM medicine_slots ms
                  LEFT JOIN medicines m ON ms.medicine_id = m.id
                  ORDER BY ms.slot_id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        // Load day name mapping
        $dayMapping = $this->getDayMapping();
        $slots = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dispenseDaysRaw = trim($row["dispense_days"] ?? '');

            // Handle empty or malformed data
            if (empty($dispenseDaysRaw)) {
                $dayNames = [];
            } else {
                $dayIds = array_filter(explode(',', $dispenseDaysRaw));
                $dayNames = array_map(function ($id) use ($dayMapping) {
                    $id = trim($id);
                    return $dayMapping[$id] ?? "Unknown";
                }, $dayIds);
            }

            $slots[] = [
                "slot_id" => (int)$row["slot_id"],
                "medicine_name" => $row["medicine_name"] ?? "No Medicine Assigned",
                "dispenseTime" => $row["dispense_time"] ?? "00:00:00",
                "dispenseDays" => $dayNames
            ];
        }

        return $slots;
    }
}

// ===========================
// MAIN EXECUTION
// ===========================

$medicineSlot = new MedicineSlot();
$slots = $medicineSlot->getAllSlots();

$response = [
    "current_day" => date('l'),
    "current_time" => date('H:i:s'),
    "slots" => $slots
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>