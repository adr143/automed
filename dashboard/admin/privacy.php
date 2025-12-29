<?php
// bootstrap
require_once __DIR__ . '/authentication/admin-class.php';
include_once '../../config/settings-configuration.php';
include_once '../../config/header.php';
include_once '../../config/footer.php';
require_once 'sidebar.php';

$config = new SystemConfig();
$header_dashboard = new HeaderDashboard($config);
$footer_dashboard = new FooterDashboard();
$admin = new ADMIN();

if (!$admin->isUserLoggedIn()) {
    $admin->redirect('../../');
}

$stmt = $admin->runQuery("SELECT * FROM users WHERE id=:uid");
$stmt->execute(array(":uid" => $_SESSION['adminSession']));
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$user_id      = $user_data['id'];
$user_profile = $user_data['profile'];
$user_fname   = $user_data['first_name'];

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$sidebar = new SideBar($config, $currentPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $header_dashboard->getHeaderDashboard(); ?>
    <title>Privacy Policy</title>

    <style>
        .page-card {
            background: rgba(255, 255, 255, 0.92);
            padding: 28px;
            border-radius: 10px;
            max-width: 900px;
            margin: 30px auto;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            line-height: 1.7;
        }
        .section-title {
            font-weight: 700;
            margin-top: 18px;
            color:#0a4fa3;
        }
    </style>
</head>
<body>
    <div class="loader"></div>

    <?php echo $sidebar->getSideBar(); ?>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <button type="submit" class="search-btn">
                        <i class='bx bx-search'></i>
                    </button>
                </div>
            </form>
            <div class="username">
                <span>Hello, <label><?php echo htmlspecialchars($user_fname); ?></label></span>
            </div>
            <a href="profile" class="profile" data-bs-toggle="tooltip"
               data-bs-placement="bottom" data-bs-title="Profile">
                <img src="../../src/img/<?php echo htmlspecialchars($user_profile); ?>">
            </a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Privacy Policy</h1>
                    <ul class="breadcrumb">
                        <li><a class="active" href="./">Home</a></li>
                        <li>|</li>
                        <li><a href="">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="page-card">
                <h2>Privacy Policy</h2>

                <p>
                    This page describes how AutoMed collects, uses, and protects the information
                    of its users.
                </p>

                <h4 class="section-title">Information We Collect</h4>
                <ul>
                    <li>Account information such as name, email, and contact details.</li>
                    <li>Medication schedules and related health reminders.</li>
                </ul>

                <h4 class="section-title">How We Use Information</h4>
                <ul>
                    <li>To provide medication reminders and scheduling services.</li>
                    <li>To improve the performance and reliability of AutoMed.</li>
                </ul>

                <h4 class="section-title">Data Protection</h4>
                <ul>
                    <li>Access to your account is protected by your credentials.</li>
                    <li>Data is handled following standard security practices as configured
                        for the system.</li>
                </ul>

                <h4 class="section-title">Contact</h4>
                <p>Email: <strong>automedsmartmedicine@gmail.com</strong></p>
            </div>
        </main>
    </section>

    <?php echo $footer_dashboard->getFooterDashboard(); ?>
    <?php include_once '../../config/sweetalert.php'; ?>
</body>
</html>
