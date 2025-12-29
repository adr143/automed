<?php
if (class_exists('SideBar')) {
    return; // Prevent redeclaration
}

class SideBar
{
    private $config;
    private $currentPage;

    public function __construct($config, $currentPage)
    {
        $this->config = $config;
        $this->currentPage = $currentPage;
    }

    private function isActive($pageName)
    {
        return $this->currentPage === $pageName ? 'active' : '';
    }

    public function getSideBar()
    {
        return '
        <style>
            /* === SIDEBAR SCROLL FIX === */
            #sidebar {
                height: 100vh;
                overflow-y: auto;
                overflow-x: hidden;
            }

            /* Scrollbar styling */
            #sidebar::-webkit-scrollbar {
                width: 6px;
            }

            #sidebar::-webkit-scrollbar-thumb {
                background: rgba(12, 70, 114, 0.5);
                border-radius: 10px;
            }

            #sidebar::-webkit-scrollbar-track {
                background: transparent;
            }
        </style>

        <section id="sidebar">
            <a href="index.php" class="brand">
                <img src="../../src/img/smart-medicine-logo.png" alt="logo">
                <span class="text">AUTOMED<br>
                    <p>SMART MED DISPENSER</p>
                </span>
            </a>

            <ul class="side-menu top">
                <li class="' . $this->isActive('index') . '">
                    <a href="index.php">
                        <i class="bx bxs-dashboard"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <li class="' . $this->isActive('medicine-inventory') . '">
                    <a href="medicine-inventory.php">
                        <i class="bx bxs-box"></i>
                        <span class="text">Inventory</span>
                    </a>
                </li>

                <li class="' . $this->isActive('schedule-management') . '">
                    <a href="schedule-management.php">
                        <i class="bx bxs-calendar"></i>
                        <span class="text">Schedule</span>
                    </a>
                </li>

                <li class="' . $this->isActive('dispensing-log') . '">
                    <a href="dispensing-log.php">
                        <i class="bx bxs-notepad"></i>
                        <span class="text">Dispensing Logs</span>
                    </a>
                </li>

                <li class="' . $this->isActive('audit-trail') . '">
                    <a href="audit-trail.php">
                        <i class="bx bx-list-check"></i>
                        <span class="text">Audit Trail</span>
                    </a>
                </li>
            </ul>

            <ul class="side-menu">
                <li class="' . $this->isActive('about') . '">
                    <a href="about.php">
                        <i class="bx bx-info-circle"></i>
                        <span class="text">About</span>
                    </a>
                </li>

                <li class="' . $this->isActive('terms') . '">
                    <a href="terms.php">
                        <i class="bx bx-file"></i>
                        <span class="text">Terms of Service</span>
                    </a>
                </li>

                <li class="' . $this->isActive('privacy') . '">
                    <a href="privacy.php">
                        <i class="bx bx-shield-quarter"></i>
                        <span class="text">Privacy Policy</span>
                    </a>
                </li>

                <!-- Real-Time Clock -->
                <li style="
                    padding: 10px 15px;
                    color: #0c4672ff;
                    font-weight: bold;
                    font-size: 14px;
                    text-align: center;
                    border-top: 1px solid rgba(255,255,255,0.2);
                    margin-top: 10px;">
                    <div id="realTimeClock">Loading time...</div>
                </li>

                <!-- Sign Out -->
                <li>
                    <a href="authentication/user-signout.php" class="btn-signout">
                        <i class="bx bxs-log-out-circle"></i>
                        <span class="text">Sign Out</span>
                    </a>
                </li>
            </ul>
        </section>';
    }
}
?>
