<?php
// bootstrap
require_once 'authentication/user-class.php';
include_once '../../config/settings-configuration.php';
include_once '../../config/header.php';
include_once '../../config/footer.php';
require_once 'sidebar.php';

$config = new SystemConfig();
$header_dashboard = new HeaderDashboard($config);
$footer_dashboard = new FooterDashboard();
$user = new USER();

if (!$user->isUserLoggedIn()) {
    $user->redirect('../../');
}

$stmt = $user->runQuery("SELECT * FROM users WHERE id=:uid");
$stmt->execute(array(":uid" => $_SESSION['userSession']));
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$user_id           = $user_data['id'];
$user_profile      = $user_data['profile'];
$user_fname        = $user_data['first_name'];

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$sidebar = new SideBar($config, $currentPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $header_dashboard->getHeaderDashboard(); ?>
    <title>Terms of Service</title>

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
                    <button class="search-btn" type="submit">
                        <i class='bx bx-search'></i>
                    </button>
                </div>
            </form>
            <div class="username">
                Hello, <label><?php echo htmlspecialchars($user_fname); ?></label>
            </div>
            <a href="profile" class="profile">
                <img src="../../src/img/<?php echo htmlspecialchars($user_profile); ?>">
            </a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Terms of Service</h1>
                    <ul class="breadcrumb">
                        <li><a class="active" href="./">Home</a></li>
                        <li>|</li>
                        <li><a href="">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="page-card">
                <h2>Terms of Service</h2>

                <p>
                    By using AutoMed, you agree to these Terms. If you do not agree, please
                    stop using the website.
                </p>

                <h4 class="section-title">User Responsibilities</h4>
                <ul>
                    <li>You agree to use AutoMed only for legal and health-related purposes.</li>
                    <li>You are responsible for inputting correct medication information.</li>
                    <li>AutoMed is not a replacement for medical professionals.</li>
                </ul>

                <h4 class="section-title">Account Responsibilities</h4>
                <ul>
                    <li>Keep your login information secure.</li>
                    <li>Users assisting seniors must have consent.</li>
                </ul>

                <h4 class="section-title">Limitations of Liability</h4>
                <p>AutoMed is not responsible for:</p>
                <ul>
                    <li>Missed medication due to alarms not being heard.</li>
                    <li>User mistakes in data entry.</li>
                    <li>Issues caused by misuse or negligence.</li>
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
