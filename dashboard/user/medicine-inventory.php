<?php
include_once 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php echo $header_dashboard->getHeaderDashboard() ?>
	<link href='https://fonts.googleapis.com/css?family=Antonio' rel='stylesheet'>
	<title>Medicine Inventory</title>
</head>

<body>

	<!-- Loader -->
	<div class="loader"></div>

	<!-- SIDEBAR -->
	<?php echo $sidebar->getSideBar(); ?> <!-- This will render the sidebar -->
	<!-- SIDEBAR -->

	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu'></i>
			<form action="#">
				<div class="form-input">
					<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
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
					<h1>Medecine Inventory</h1>
					<ul class="breadcrumb">
						<li>
							<a class="active" href="./">Home</a>
						</li>
						<li>|</li>
						<li>
							<a href="">Medecine Inventory</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="modal-button">
				<button type="button" data-bs-toggle="modal" data-bs-target="#medicineModal" class="btn-primary"><i class='bx bxs-plus-circle'></i> Add Medicine</button>
			</div>

			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3><i class='bx bxs-folder-open'></i> Medicine Data</h3>
					</div>
					<!-- BODY -->
					<section class="data-table">
						<div class="searchBx">
							<input type="input" placeholder="Search Medicine . . ." class="search" name="search_box" id="search_box"><button class="searchBtn"><i class="bx bx-search icon"></i></button>
						</div>

						<div class="table">
							<div id="dynamic_content">
							</div>

					</section>
				</div>
			</div>
			<!-- add product list -->
			<div class="class-modal">
				<div class="modal fade" id="medicineModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true" data-bs-backdrop="static">
					<div class="modal-dialog modal-dialog-centered modal-lg">
						<div class="modal-content">
							<div class="header"></div>
							<div class="modal-header">
								<h5 class="modal-title" id="classModalLabel"><i class='bx bxs-capsule'></i> Add Medicine</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeButton"></button>
							</div>
							<div class="modal-body">
								<section class="data-form-modals">
									<div class="registration">
										<form action="controller/medicine-controller.php" method="POST" class="row gx-5 needs-validation" name="form" onsubmit="return validate()" novalidate style="overflow: hidden;">
											<div class="row gx-5 needs-validation">
												<div class="col-md-6">
													<label for="medicine_name" class="form-label">Medicine Name<span> *</span></label>
													<input type="text" class="form-control" autocapitalize="on" autocomplete="off" name="medicine_name" id="medicine_name" required>
													<div class="invalid-feedback">
														Please provide a Medicine Name.
													</div>
												</div>

												<div class="col-md-6">
													<label for="medicine_description" class="form-label">Medicine Description<span> *</span></label>
													<input type="text" class="form-control" autocapitalize="on" autocomplete="off" name="medicine_description" id="medicine_description" required>
													<div class="invalid-feedback">
														Please provide a Medicine Description.
													</div>
												</div>

												<div class="col-md-6">
													<label for="dosage" class="form-label">Dosage<span> *</span></label>
													<input type="text" class="form-control" autocapitalize="on" autocomplete="off" name="dosage" id="dosage" required>
													<div class="invalid-feedback">
														Please provide a Dosage.
													</div>
												</div>
											</div>
											<div class="addBtn">
												<button type="submit" class="btn-primary" name="btn-add-medicine" id="btn-add" onclick="return IsEmpty(); sexEmpty();">Add</button>
											</div>
										</form>
									</div>
								</section>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->

	<?php echo $footer_dashboard->getFooterDashboard() ?>
	<?php include_once '../../config/sweetalert.php'; ?>
	<script>
		//live search---------------------------------------------------------------------------------------//
		$(document).ready(function() {

			load_data(1);

			function load_data(page, query = '') {
				$.ajax({
					url: "tables/medicine-table.php",
					method: "POST",
					data: {
						page: page,
						query: query
					},
					success: function(data) {
						$('#dynamic_content').html(data);
					}
				});
			}

			$(document).on('click', '.page-link', function() {
				var page = $(this).data('page_number');
				var query = $('#search_box').val();
				load_data(page, query);
			});

			$('#search_box').keyup(function() {
				var query = $('#search_box').val();
				load_data(1, query);
			});

		});
	</script>
</body>

</html>