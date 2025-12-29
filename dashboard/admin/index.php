<?php
include_once 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $header_dashboard->getHeaderDashboard() ?>
    <link href="https://fonts.googleapis.com/css?family=Antonio" rel="stylesheet">
    <title>Dashboard</title>

    <style>
        /* Override global body background for this page */
        body {
            margin: 0;
            min-height: 100vh;
            color: #fff;
            background:
                linear-gradient(rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.35)),
                url("../../src/img/background.jpg") center/cover no-repeat fixed;
        }

        /* Let main sit on top of the image */
        #content main {
            background: transparent !important;
            display: flex;
            flex-direction: column;          /* title on top, slogan below */
            min-height: calc(100vh - 56px);  /* full height minus navbar */
        }

        .head-title {
            padding-top: 20px;
            padding-bottom: 10px;
        }

        /* Force 'Dashboard' title and breadcrumb text to white */
        .head-title .left h1 {
            color: #ffffff !important;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8);
        }

        .head-title .left .breadcrumb li,
        .head-title .left .breadcrumb li a {
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
        }

        /* Center slogan area under the title */
        .dashboard-hero-wrapper {
            flex: 1;
            display: flex;
            align-items: center;             /* vertical center */
            justify-content: center;         /* horizontal center */
            text-align: center;
        }

        .dashboard-hero {
            max-width: 900px;
            padding: 0 40px 40px;
        }

        .dashboard-hero p {
            font-family: 'Antonio', sans-serif;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.5;
            color: #ffffff;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8);
            margin: 0;
        }
    </style>
</head>

<body>
    <!-- Loader -->
    <div class="loader"></div>

    <!-- SIDEBAR -->
    <?php echo $sidebar->getSideBar(); ?>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
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
                <span>Hello, <label for=""><?php echo $user_fname ?></label></span>
            </div>
            <a href="profile" class="profile" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Profile">
                <img src="../../src/img/<?php echo $user_profile ?>">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">
                        <li><a class="active" href="./">Home</a></li>
                        <li>|</li>
                        <li><a href="">Dashboard</a></li>
                    </ul>
                </div>
            </div>

            <div class="dashboard-hero-wrapper">
                <div class="dashboard-hero">
                    <p>
                        AutoMed helps seniors and patients by using innovation and
                        connection to make taking medicine simple.
                    </p>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <?php echo $footer_dashboard->getFooterDashboard() ?>
    <?php include_once '../../config/sweetalert.php'; ?>
</body>
</html>
