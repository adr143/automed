<?php
include_once 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $header_dashboard->getHeaderDashboard(); ?>
    <title>About Us</title>

    <style>
        .page-card {
            background: rgba(255, 255, 255, 0.92);
            padding: 28px;
            border-radius: 10px;
            max-width: 900px;
            margin: 30px auto;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            line-height: 1.6;
        }
        .copy { text-align:center; margin-top: 20px; color:#777; }
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
                Hello, <label><?php echo $user_fname; ?></label>
            </div>
            <a href="profile" class="profile">
                <img src="../../src/img/<?php echo $user_profile; ?>">
            </a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>About Us</h1>
                    <ul class="breadcrumb">
                        <li><a class="active" href="./">Home</a></li>
                        <li>|</li>
                        <li><a href="">About</a></li>
                    </ul>
                </div>
            </div>

            <div class="page-card">
                <h2>AutoMed</h2>
                <p>
                    AutoMed is a smart medication scheduler and pill reminder system designed
                    to help senior citizens, caregivers, and medical assistants stay on track
                    with their medication routines.
                </p>

                <p><strong>Email:</strong> automedsmartmedicine@gmail.com</p>

                <p class="copy">© 2025 AutoMed — All Rights Reserved</p>
            </div>
        </main>
    </section>

    <?php echo $footer_dashboard->getFooterDashboard(); ?>
    <?php include_once '../../config/sweetalert.php'; ?>
</body>
</html>
