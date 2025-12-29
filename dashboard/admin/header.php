<?php
require_once 'authentication/admin-class.php';
include_once '../../config/settings-configuration.php';
include_once '../../config/header.php';
include_once '../../config/footer.php';
require_once 'sidebar.php';

$currentPage = basename($_SERVER['PHP_SELF'], ".php"); // Gets the current page name without the extension
$sidebar = new SideBar($config, $currentPage);

$config = new SystemConfig();
$header_dashboard = new HeaderDashboard($config);
$footer_dashboard = new FooterDashboard();
$user = new ADMIN();

if (!$user->isUserLoggedIn()) {
    $user->redirect('../../');
}

// retrieve user data
$stmt = $user->runQuery("SELECT * FROM users WHERE id=:uid");
$stmt->execute(array(":uid" => $_SESSION['adminSession']));
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// retrieve profile user and full name
$user_id                = $user_data['id'];
$user_profile           = $user_data['profile'];
$user_fname             = $user_data['first_name'];
$user_mname             = $user_data['middle_name'];
$user_lname             = $user_data['last_name'];
$user_fullname          = $user_data['last_name'] . ", " . $user_data['first_name'];
$user_sex               = $user_data['sex'];
$user_birth_date        = $user_data['date_of_birth'];
$user_age               = $user_data['age'];
$user_civil_status      = $user_data['civil_status'];
$user_phone_number      = $user_data['phone_number'];
$user_email             = $user_data['email'];
$user_last_update       = $user_data['updated_at'];
$user_type             = $user_data['user_type'];

// =====================
$stmt_slots = $user->runQuery("SELECT * FROM medicine_slots");
$stmt_slots->execute();
$medicines_slot_data = $stmt_slots->fetchAll(PDO::FETCH_ASSOC);

// =====================
// Fetch all medicines
// =====================
$stmt_medicines = $user->runQuery("SELECT * FROM medicines");
$stmt_medicines->execute();
$all_medicines = $stmt_medicines->fetchAll(PDO::FETCH_ASSOC);

// =====================
// Fetch all days
// =====================
$stmt_days = $user->runQuery("SELECT * FROM day");
$stmt_days->execute();
$all_days = $stmt_days->fetchAll(PDO::FETCH_ASSOC);

// =====================
// Index slot data by slot_id for easy access
// =====================
$slotData = [];
foreach ($medicines_slot_data as $row) {
    $slotData[$row['slot_id']] = $row;
}
?>
