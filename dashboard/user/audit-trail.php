<?php
// audit-trail.php (user dashboard Audit Trail page)

// bootstrap
require_once 'authentication/user-class.php';
include_once '../../config/settings-configuration.php';
include_once '../../config/header.php';
include_once '../../config/footer.php';
require_once 'sidebar.php';

// settings-configuration.php should already start the session
// so do NOT call session_start() here again.

$config           = new SystemConfig();
$header_dashboard = new HeaderDashboard($config);
$footer_dashboard = new FooterDashboard();
$user             = new USER();

if (!$user->isUserLoggedIn()) {
    $user->redirect('../../');
}

$stmt = $user->runQuery("SELECT * FROM users WHERE id = :uid");
$stmt->execute([":uid" => $_SESSION['userSession']]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$user_id      = $user_data['id'];
$user_profile = $user_data['profile'];
$user_fname   = $user_data['first_name'];

$currentPage  = basename($_SERVER['PHP_SELF'], '.php');
$sidebar      = new SideBar($config, $currentPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $header_dashboard->getHeaderDashboard(); ?>
    <title>Audit Trail</title>
    <style>
        .activity-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 500;
        }
        .login { background: #d4edda; color: #155724; }
        .logout { background: #f8d7da; color: #721c24; }
        .password { background: #fff3cd; color: #856404; }
        .schedule { background: #cce5ff; color: #004085; }
        .inventory { background: #d1ecf1; color: #0c5460; }
        .user { background: #f8f9fa; color: #383d41; }
        .status { background: #e2e3e5; color: #495057; }
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
                <span>Hello, <label><?php echo htmlspecialchars($user_fname); ?></label></span>
            </div>
            <a href="profile" class="profile" data-bs-toggle="tooltip"
               data-bs-placement="bottom" data-bs-title="Profile">
                <img src="../../src/img/<?php echo htmlspecialchars($user_profile); ?>">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Audit Trail</h1>
                    <ul class="breadcrumb">
                        <li><a class="active" href="./">Home</a></li>
                        <li>|</li>
                        <li><a href="">Audit Trail</a></li>
                    </ul>
                </div>
                <div class="right">
                    <button class="refresh-btn" onclick="load_data(1, $('#search_box').val())">
                        <i class='bx bx-refresh'></i> Refresh
                    </button>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3><i class='bx bxl-blogger'></i> All User Activities</h3>
                    </div>

                    <!-- BODY -->
                    <section class="data-table">
                        <div class="searchBx">
                            <input type="input" placeholder="Search logs by activity or date..." class="search"
                                   name="search_box" id="search_box">
                            <button class="searchBtn" onclick="load_data(1, $('#search_box').val())">
                                <i class="bx bx-search icon"></i>
                            </button>
                        </div>

                        <div class="table">
                            <div id="dynamic_content">
                                <div class="loading">Loading audit trail...</div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <?php echo $footer_dashboard->getFooterDashboard(); ?>
    <?php include_once '../../config/sweetalert.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var userId = <?php echo json_encode($user_id); ?>;

            load_data(1);

            function load_data(page, query = '') {
                $.ajax({
                    url: "tables/user-logs-table.php",
                    method: "POST",
                    data: {
                        page: page,
                        query: query,
                        user_id: userId
                    },
                    beforeSend: function() {
                        $('#dynamic_content').html('<div class="loading">Loading activities...</div>');
                    },
                    success: function(data) {
                        $('#dynamic_content').html(data);
                    },
                    error: function(xhr) {
                        $('#dynamic_content').html('<div class="error">Failed to load audit trail.</div>');
                    }
                });
            }

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                var page = $(this).data('page_number');
                var query = $('#search_box').val();
                load_data(page, query);
            });

            $('#search_box').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    load_data(1, $(this).val());
                }
            });

            // Auto refresh every 5 minutes if no search filter
            setInterval(function() {
                if ($('#search_box').val().trim() === '') {
                    load_data(1, '');
                }
            }, 300000);
        });
    </script>
</body>
</html>
