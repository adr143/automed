<?php
require_once __DIR__ . '/../../../database/dbconfig.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once __DIR__ . '/../../../config/settings-configuration.php';
require_once __DIR__ . '/../../../src/vendor/autoload.php';

class USER
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $db       = $database->dbConnection();
        $this->conn = $db;
        // Do NOT start the session here; settings-configuration.php handles it.
    }

    public function runQuery($sql)
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }

    public function siteSecretKey() {
        $config = new SystemConfig();
        return $config->getSSKey();
    }

    public function smtpEmail() {
        $smtp = new SystemConfig();
        return $smtp->getSmtpEmail();
    }

    public function smtpPassword() {
        $smtp = new SystemConfig();
        return $smtp->getSmtpPassword();
    }

    public function systemName() {
        $systemname = new SystemConfig();
        return $systemname->getSystemName();
    }

    public function emailConfig() {
        // kept for compatibility
        $email       = $this->smtpEmail();
        $password    = $this->smtpPassword();
        $system_name = $this->systemName();
    }

    public function mainUrl() {
        $main_url = new MainUrl();
        return $main_url->getUrl();
    }

    public function proxyUrl() {
        $ServerUrl = new ProxyServerUrl();
        return $ServerUrl->getUrl();
    }

    public function lasdID()
    {
        return $this->conn->lastInsertId();
    }

    public function register($first_name, $middle_name, $last_name, $email, $hash_password, $tokencode, $user_type)
    {
        try {
            $password = md5($hash_password);
            $stmt = $this->conn->prepare(
                "INSERT INTO users(first_name, middle_name, last_name, email, password, tokencode, user_type)
                 VALUES(:first_name, :middle_name, :last_name, :email, :password, :tokencode, :user_type)"
            );

            $stmt->bindparam(":first_name",  $first_name);
            $stmt->bindparam(":middle_name", $middle_name);
            $stmt->bindparam(":last_name",   $last_name);
            $stmt->bindparam(":email",       $email);
            $stmt->bindparam(":password",    $password);
            $stmt->bindparam(":tokencode",   $tokencode);
            $stmt->bindparam(":user_type",   $user_type);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function login($email, $upass)
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT * FROM users 
                 WHERE email = :email AND account_status = :account_status AND user_type = :user_type"
            );
            $stmt->execute([
                ":email"          => $email,
                ":account_status" => "active",
                ":user_type"      => 2
            ]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() == 1) {
                if ($userRow['account_status'] === "active") {
                    if ($userRow['password'] === md5($upass)) {
                        date_default_timezone_set('Asia/Manila');
                        $activity = "Has successfully signed in";
                        $user_id  = $userRow['id'];

                        $this->logs($activity, $user_id);

                        $_SESSION['userSession'] = $userRow['id'];
                        return true;
                    } else {
                        $_SESSION['status_title'] = "Oops !";
                        $_SESSION['status']       = "Email or Password is incorrect.";
                        $_SESSION['status_code']  = "error";
                        $_SESSION['status_timer'] = 1000000;
                        header("Location: ../../../");
                        exit;
                    }
                } else {
                    $_SESSION['status_title'] = "Sorry !";
                    $_SESSION['status']       = "Entered email is not verify, please go to your email and verify it. Thank you !";
                    $_SESSION['status_code']  = "error";
                    $_SESSION['status_timer'] = 10000000;
                    header("Location: ../../../");
                    exit;
                }
            } else {
                $_SESSION['status_title'] = "Sorry !";
                $_SESSION['status']       = "No account found or your account has been remove!";
                $_SESSION['status_code']  = "error";
                $_SESSION['status_timer'] = 10000000;
                header("Location: ../../../");
                exit;
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function isUserLoggedIn()
    {
        return isset($_SESSION['userSession']);
    }

    public function redirect($url)
    {
        header("Location: $url");
    }

    public function logout()
    {
        if (isset($_SESSION['userSession'])) {
            $activity = "Has successfully signed out";
            $user_id  = $_SESSION['userSession'];

            $this->logs($activity, $user_id);

            unset($_SESSION['userSession']);
        }

        $_SESSION['status_title'] = 'Logout!';
        $_SESSION['status']       = 'Thank you for using AutoMed';
        $_SESSION['status_code']  = 'success';
        $_SESSION['status_timer'] = 40000;
        header('Location: ../../../');
    }

    // ✅ New: Permanently delete the currently logged-in user account
    public function deleteAccount()
    {
        if (!isset($_SESSION['userSession'])) {
            return false;
        }

        $user_id = (int)$_SESSION['userSession'];

        try {
            // Log request before deletion
            $this->logs('Requested account deletion', $user_id);

            // Delete user record
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Log that account was deleted (keeps audit trail with same user_id)
            $this->logs('Account permanently deleted', $user_id);

            // Clear session
            unset($_SESSION['userSession']);
            session_regenerate_id(true);

            return true;
        } catch (PDOException $ex) {
            // optionally log error somewhere
            return false;
        }
    }

    // Generic logger: use this for inventory, schedules, etc.
    public function logs($activity, $user_id)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO logs (user_id, activity) VALUES (:user_id, :activity)"
        );
        $stmt->execute([
            ":user_id"  => $user_id,
            ":activity" => $activity
        ]);
    }

    public function send_mail($email, $message, $subject, $smtp_email, $smtp_password, $system_name)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = "tls";
        $mail->Host       = "smtp.gmail.com";
        $mail->Port       = 587;
        $mail->AddAddress($email);
        $mail->Username   = $smtp_email;
        $mail->Password   = $smtp_password;
        $mail->SetFrom($smtp_email, $system_name);
        $mail->Subject    = $subject;
        $mail->MsgHTML($message);
        $imagePath = __DIR__ . '/../../../src/img/wattzup-high-resolution-logo-transparent.png';
        $mail->AddEmbeddedImage($imagePath, 'logo', 'logo.png');
        $mail->Send();
    }
}
?>
