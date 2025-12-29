<?php
require_once '../authentication/user-class.php';
include_once '../../config/settings-configuration.php';

$user = new USER();

if (!$user->isUserLoggedIn()) {
    $user->redirect('../../');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check using token from settings-configuration.php
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['status_title'] = 'Security warning';
        $_SESSION['status']       = 'Invalid request token.';
        $_SESSION['status_code']  = 'error';
        $_SESSION['status_timer'] = 4000;
        header('Location: ../profile');
        exit;
    }

    $deleted = $user->deleteAccount();

    if ($deleted) {
        $_SESSION['status_title'] = 'Account deleted';
        $_SESSION['status']       = 'Your account and related information have been removed.';
        $_SESSION['status_code']  = 'success';
        $_SESSION['status_timer'] = 5000;
        header('Location: ../../'); // Back to login/landing
        exit;
    } else {
        $_SESSION['status_title'] = 'Error';
        $_SESSION['status']       = 'Failed to delete your account. Please try again.';
        $_SESSION['status_code']  = 'error';
        $_SESSION['status_timer'] = 4000;
        header('Location: ../profile');
        exit;
    }
} else {
    header('Location: ../profile');
    exit;
}
