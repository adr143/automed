<?php
include_once '../../../config/settings-configuration.php';
include_once __DIR__ . '/../../../database/dbconfig.php';
require_once __DIR__ . '/../authentication/user-class.php';

class Medicine
{
    private $conn;
    private $admin;

    public function __construct()
    {
        $database = new Database();
        $db       = $database->dbConnection();
        $this->conn  = $db;
        $this->admin = new USER();
    }

    // ==============================
    // Insert or Update Slot Details
    // ==============================
    public function updateSlot($slot_id, $medicine_id, $dispense_time, $dispense_days)
    {
        try {
            // Check if medicine is already assigned to another slot
            $dup_sql  = "SELECT slot_id FROM medicine_slots WHERE medicine_id = :medicine_id AND slot_id != :slot_id";
            $dup_stmt = $this->conn->prepare($dup_sql);
            $dup_stmt->bindParam(':medicine_id', $medicine_id);
            $dup_stmt->bindParam(':slot_id', $slot_id);
            $dup_stmt->execute();

            if ($dup_stmt->rowCount() > 0) {
                $_SESSION['status_title'] = "Duplicate Medicine!";
                $_SESSION['status']       = "This medicine is already assigned to another slot.";
                $_SESSION['status_code']  = "warning";
                $_SESSION['status_timer'] = 4000;
                header('Location: ../schedule-management');
                exit;
            }

            // Check if slot already exists
            $check_sql  = "SELECT * FROM medicine_slots WHERE slot_id = :slot_id";
            $check_stmt = $this->conn->prepare($check_sql);
            $check_stmt->bindParam(':slot_id', $slot_id);
            $check_stmt->execute();

            if ($check_stmt->rowCount() > 0) {
                // Get existing record
                $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);

                // Check if any value has actually changed
                if (
                    $existing['medicine_id']    == $medicine_id &&
                    $existing['dispense_time']  == $dispense_time &&
                    $existing['dispense_days']  == $dispense_days
                ) {
                    $_SESSION['status_title'] = "No Changes Made";
                    $_SESSION['status']       = "The slot details are already up-to-date.";
                    $_SESSION['status_code']  = "info";
                    $_SESSION['status_timer'] = 3000;
                    header('Location: ../schedule-management');
                    exit;
                }

                // Proceed with update if data has changed
                $update_sql = "UPDATE medicine_slots 
                               SET medicine_id = :medicine_id, 
                                   dispense_time = :dispense_time, 
                                   dispense_days = :dispense_days
                               WHERE slot_id = :slot_id";
                $stmt = $this->conn->prepare($update_sql);
                $stmt->bindParam(':medicine_id',   $medicine_id);
                $stmt->bindParam(':dispense_time', $dispense_time);
                $stmt->bindParam(':dispense_days', $dispense_days);
                $stmt->bindParam(':slot_id',       $slot_id);
                $exec = $stmt->execute();

                $activity = "Updated slot $slot_id with new medicine and schedule.";
            } else {
                // Insert new record
                $insert_sql = "INSERT INTO medicine_slots (slot_id, medicine_id, dispense_time, dispense_days) 
                               VALUES (:slot_id, :medicine_id, :dispense_time, :dispense_days)";
                $stmt = $this->conn->prepare($insert_sql);
                $stmt->bindParam(':slot_id',       $slot_id);
                $stmt->bindParam(':medicine_id',   $medicine_id);
                $stmt->bindParam(':dispense_time', $dispense_time);
                $stmt->bindParam(':dispense_days', $dispense_days);
                $exec = $stmt->execute();

                $activity = "Added new medicine schedule for slot $slot_id.";
            }

            if ($exec) {
                $user_id = $_SESSION['userSession']; // ✅ use userSession
                $this->admin->logs($activity, $user_id);

                $_SESSION['status_title'] = "Success!";
                $_SESSION['status']       = $activity;
                $_SESSION['status_code']  = "success";
                $_SESSION['status_timer'] = 4000;
            } else {
                $_SESSION['status_title'] = "Error!";
                $_SESSION['status']       = "Failed to update slot data.";
                $_SESSION['status_code']  = "error";
                $_SESSION['status_timer'] = 4000;
            }
        } catch (PDOException $e) {
            $_SESSION['status_title'] = "Database Error!";
            $_SESSION['status']       = $e->getMessage();
            $_SESSION['status_code']  = "error";
            $_SESSION['status_timer'] = 4000;
        }

        header('Location: ../schedule-management');
        exit;
    }

    public function deleteMedicine($medicine_id)
    {
        try {
            // Start transaction for consistency
            $this->conn->beginTransaction();

            // Step 1: Set medicine_id = NULL in all related slots
            $update_sql  = "UPDATE medicine_slots SET medicine_id = NULL WHERE medicine_id = :medicine_id";
            $update_stmt = $this->conn->prepare($update_sql);
            $update_stmt->bindParam(':medicine_id', $medicine_id);
            $update_stmt->execute();

            // Step 2: Delete the medicine from medicines table
            $delete_sql  = "DELETE FROM medicines WHERE id = :medicine_id";
            $delete_stmt = $this->conn->prepare($delete_sql);
            $delete_stmt->bindParam(':medicine_id', $medicine_id);
            $exec = $delete_stmt->execute();

            if ($exec) {
                // Commit transaction
                $this->conn->commit();

                $user_id  = $_SESSION['userSession']; // ✅ use userSession
                $activity = "Deleted medicine with ID $medicine_id and cleared related slots.";
                $this->admin->logs($activity, $user_id);

                $_SESSION['status_title'] = "Deleted!";
                $_SESSION['status']       = "Medicine record deleted and related slots cleared.";
                $_SESSION['status_code']  = "success";
                $_SESSION['status_timer'] = 4000;
            } else {
                // Rollback on failure
                $this->conn->rollBack();

                $_SESSION['status_title'] = "Error!";
                $_SESSION['status']       = "Failed to delete medicine record.";
                $_SESSION['status_code']  = "error";
                $_SESSION['status_timer'] = 4000;
            }
        } catch (PDOException $e) {
            // Rollback on exception
            $this->conn->rollBack();

            $_SESSION['status_title'] = "Database Error!";
            $_SESSION['status']       = $e->getMessage();
            $_SESSION['status_code']  = "error";
            $_SESSION['status_timer'] = 4000;
        }

        header('Location: ../medicine-inventory');
        exit;
    }

    public function addMedicine($medicine_name, $medicine_description, $dosage)
    {
        try {
            // Step 1: Check if medicine already exists
            $check_sql  = "SELECT COUNT(*) FROM medicines WHERE LOWER(medicine_name) = LOWER(:medicine_name)";
            $check_stmt = $this->conn->prepare($check_sql);
            $check_stmt->bindParam(':medicine_name', $medicine_name);
            $check_stmt->execute();

            $exists = $check_stmt->fetchColumn();

            if ($exists > 0) {
                $_SESSION['status_title'] = "Duplicate Entry!";
                $_SESSION['status']       = "The medicine '<b>$medicine_name</b>' already exists in the inventory.";
                $_SESSION['status_code']  = "warning";
                $_SESSION['status_timer'] = 4000;

                header('Location: ../medicine-inventory');
                exit;
            }

            // Step 2: Insert new medicine
            $insert_sql = "INSERT INTO medicines (medicine_name, description, dosage) 
                           VALUES (:medicine_name, :medicine_description, :dosage)";
            $stmt = $this->conn->prepare($insert_sql);
            $stmt->bindParam(':medicine_name',        $medicine_name);
            $stmt->bindParam(':medicine_description', $medicine_description);
            $stmt->bindParam(':dosage',               $dosage);
            $exec = $stmt->execute();

            if ($exec) {
                $user_id  = $_SESSION['userSession']; // ✅ use userSession
                $activity = "Added new medicine: $medicine_name.";
                $this->admin->logs($activity, $user_id);

                $_SESSION['status_title'] = "Success!";
                $_SESSION['status']       = "New medicine '<b>$medicine_name</b>' added successfully.";
                $_SESSION['status_code']  = "success";
                $_SESSION['status_timer'] = 4000;
            } else {
                $_SESSION['status_title'] = "Error!";
                $_SESSION['status']       = "Failed to add new medicine.";
                $_SESSION['status_code']  = "error";
                $_SESSION['status_timer'] = 4000;
            }
        } catch (PDOException $e) {
            $_SESSION['status_title'] = "Database Error!";
            $_SESSION['status']       = $e->getMessage();
            $_SESSION['status_code']  = "error";
            $_SESSION['status_timer'] = 4000;
        }

        header('Location: ../medicine-inventory');
        exit;
    }

    public function updateMedicine($medicine_id, $medicine_name, $medicine_description, $dosage)
    {
        try {
            // Step 1: Fetch current record
            $current_sql  = "SELECT medicine_name, description, dosage FROM medicines WHERE id = :medicine_id";
            $current_stmt = $this->conn->prepare($current_sql);
            $current_stmt->bindParam(':medicine_id', $medicine_id);
            $current_stmt->execute();
            $current_data = $current_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$current_data) {
                $_SESSION['status_title'] = "Not Found!";
                $_SESSION['status']       = "Medicine record not found.";
                $_SESSION['status_code']  = "error";
                $_SESSION['status_timer'] = 4000;
                header('Location: ../medicine-inventory');
                exit;
            }

            // Step 2: Check if no changes were made
            if (
                trim(strtolower($current_data['medicine_name'])) === trim(strtolower($medicine_name)) &&
                trim($current_data['description'])              === trim($medicine_description) &&
                trim($current_data['dosage'])                   === trim($dosage)
            ) {
                $_SESSION['status_title'] = "No Changes!";
                $_SESSION['status']       = "No updates were made since the data is identical.";
                $_SESSION['status_code']  = "info";
                $_SESSION['status_timer'] = 4000;
                header('Location: ../medicine-inventory');
                exit;
            }

            // Step 3: Check duplicate name
            $check_sql  = "SELECT COUNT(*) FROM medicines 
                           WHERE LOWER(medicine_name) = LOWER(:medicine_name) 
                           AND id != :medicine_id";
            $check_stmt = $this->conn->prepare($check_sql);
            $check_stmt->bindParam(':medicine_name', $medicine_name);
            $check_stmt->bindParam(':medicine_id',   $medicine_id);
            $check_stmt->execute();
            $exists = $check_stmt->fetchColumn();

            if ($exists > 0) {
                $_SESSION['status_title'] = "Duplicate Entry!";
                $_SESSION['status']       = "Another medicine with the name '<b>$medicine_name</b>' already exists.";
                $_SESSION['status_code']  = "warning";
                $_SESSION['status_timer'] = 4000;
                header('Location: ../medicine-inventory');
                exit;
            }

            // Step 4: Perform update
            $update_sql = "UPDATE medicines 
                           SET medicine_name = :medicine_name, 
                               description   = :medicine_description, 
                               dosage        = :dosage 
                           WHERE id = :medicine_id";
            $stmt = $this->conn->prepare($update_sql);
            $stmt->bindParam(':medicine_name',        $medicine_name);
            $stmt->bindParam(':medicine_description', $medicine_description);
            $stmt->bindParam(':dosage',               $dosage);
            $stmt->bindParam(':medicine_id',          $medicine_id);
            $exec = $stmt->execute();

            if ($exec) {
                $user_id  = $_SESSION['userSession']; // ✅ use userSession
                $activity = "Updated medicine ID $medicine_id to '$medicine_name'.";
                $this->admin->logs($activity, $user_id);

                $_SESSION['status_title'] = "Updated!";
                $_SESSION['status']       = "Medicine record updated successfully.";
                $_SESSION['status_code']  = "success";
                $_SESSION['status_timer'] = 4000;
            } else {
                $_SESSION['status_title'] = "Error!";
                $_SESSION['status']       = "Failed to update medicine record.";
                $_SESSION['status_code']  = "error";
                $_SESSION['status_timer'] = 4000;
            }
        } catch (PDOException $e) {
            $_SESSION['status_title'] = "Database Error!";
            $_SESSION['status']       = $e->getMessage();
            $_SESSION['status_code']  = "error";
            $_SESSION['status_timer'] = 4000;
        }

        header('Location: ../medicine-inventory');
        exit;
    }

    public function runQuery($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
}

// ==============================
// Handle Form Submission
// ==============================
if (isset($_POST['btn-update-thresholds'])) {
    $slot_id       = $_POST['slot_id']        ?? null;
    $medicine_id   = $_POST['medicine_name']  ?? null;
    $dispense_time = $_POST['schedule_time']  ?? null;
    $dispense_days = isset($_POST['days']) && is_array($_POST['days']) ? implode(', ', $_POST['days']) : '';

    $medicinedata = new Medicine();
    $medicinedata->updateSlot($slot_id, $medicine_id, $dispense_time, $dispense_days);
}

if (isset($_POST['btn-add-medicine'])) {
    $medicine_name        = $_POST['medicine_name']        ?? null;
    $medicine_description = $_POST['medicine_description'] ?? null;
    $dosage               = $_POST['dosage']               ?? null;

    $addMedicine = new Medicine();
    $addMedicine->addMedicine($medicine_name, $medicine_description, $dosage);
}

if (isset($_POST['btn-update-medicine'])) {
    $medicine_id          = $_POST['medicine_id']          ?? null;
    $medicine_name        = $_POST['medicine_name']        ?? null;
    $medicine_description = $_POST['medicine_description'] ?? null;
    $dosage               = $_POST['dosage']               ?? null;

    $addMedicine = new Medicine();
    $addMedicine->updateMedicine($medicine_id, $medicine_name, $medicine_description, $dosage);
}

if (isset($_GET['delete_medicine'])) {
    $medicine_id = $_GET["id"];
    $deleteMedicine = new Medicine();
    $deleteMedicine->deleteMedicine($medicine_id);
}
