<?php
//Sign In Controller
include_once 'dashboard/admin/authentication/admin-signin.php';
//configuration connection
include_once 'config/header.php';
include_once 'config/footer.php';

$config = new SystemConfig();
$header_signin = new HeaderSignin($config);
$footer_signin = new FooterSignin();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo $header_signin->getHeaderSignin() ?>
    <title>Sign In</title>
    <!-- PWA Meta Tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#007bff">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="src/img/smart-medicine-logo.png">
</head>
<body class="my-login-page">
	<section class="h-100">
		<div class="container h-100">
			<div class="row justify-content-md-center h-100">
				<div class="card-wrapper">
					<div class="brand">
						<img src="src/img/<?php echo $config->getSystemLogo() ?>" alt="logo">
					</div>
					<div class="card fat">
						<div class="card-body">
							<h4 class="card-title">Sign In</h4>
							<form action="dashboard/admin/authentication/admin-signin.php" method="POST" class="my-login-validation" novalidate="">
	<!-- recaptcah token -->	<input type="hidden" id="g-token" name="g-token">
	<!-- csrf token -->			<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

								<div class="form-group">
									<label for="email">E-mail</label>
									<input id="email" type="email" class="form-control" name="email" required autofocus>
									<div class="invalid-feedback">
										Email is required!
									</div>
								</div>

								<div class="form-group">
									<label for="password">Password
										<a href="forgot-password" class="float-right">
											Forgot Password?
										</a>
									</label>
									<input id="password" type="password" autocomplete="off" class="form-control" name="password" required data-eye>
								    <div class="invalid-feedback">
								    	Password is required!
							    	</div>
								</div>

								<div class="form-group m-0">
									<button type="submit"  id="submit" class="btn btn-primary btn-block" name="btn-signin">
										Sign In
									</button>
								</div>
							</form>
						</div>
						<h1 class="signup">Don't have an account?<a href="signup"> Click here to sign up!</a></h1>
					</div>
					<footer><?php echo $config->getSystemCopyright() ?></footer>
				</div>
			</div>
		</div>
	</section>
	<?php echo $footer_signin->getFooterSignin() ?>
	<script>
		// CAPTCHA
			grecaptcha.ready(function() {
			grecaptcha.execute('<?php echo $config->getSKey() ?>', {action: 'submit'}).then(function(token) {
				document.getElementById("g-token").value = token;
			});
			});
	</script>
	<!-- SWEET ALERT -->
    <?php include_once 'config/sweetalert.php'; ?>
    <!-- PWA Install Prompt -->
    <div id="installPrompt" style="display:none; position:fixed; top:20px; right:20px; background:#007bff; color:white; padding:15px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.2); z-index:1000;">
        <div style="margin-bottom:10px;">📱 Install App</div>
        <button id="installBtn" style="background:white; color:#007bff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; margin-right:8px;">Install</button>
        <button id="dismissBtn" style="background:transparent; color:white; border:1px solid white; padding:8px 16px; border-radius:4px; cursor:pointer;">Later</button>
    </div>
    <!-- PWA Service Worker -->
    <script>
        let deferredPrompt;
        
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
                .then(registration => console.log('SW registered'))
                .catch(error => console.log('SW registration failed'));
        }
        
        // Capture install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('installPrompt').style.display = 'block';
        });
        
        // Install button click
        document.getElementById('installBtn').addEventListener('click', () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    deferredPrompt = null;
                    document.getElementById('installPrompt').style.display = 'none';
                });
            }
        });
        
        // Dismiss button
        document.getElementById('dismissBtn').addEventListener('click', () => {
            document.getElementById('installPrompt').style.display = 'none';
        });
        
        // Hide prompt after install
        window.addEventListener('appinstalled', () => {
            document.getElementById('installPrompt').style.display = 'none';
        });
    </script>
</body>
</html>