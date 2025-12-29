<?php
include_once 'header.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php echo $header_dashboard->getHeaderDashboard() ?>
	<link href='https://fonts.googleapis.com/css?family=Antonio' rel='stylesheet'>
	<title>Threshold</title>
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
					<h1>Scheduling</h1>
					<ul class="breadcrumb">
						<li>
							<a class="active" href="./">Home</a>
						</li>
						<li>|</li>
						<li>
							<a href="">Scheduling</a>
						</li>
					</ul>
				</div>
			</div>

			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3><i class='bx bxs-cog'></i> Set Schedule</h3>
					</div>
					<!-- BODY -->
					<?php for ($slot = 1; $slot <= 15; $slot++): ?>
						<?php
						// Defaults if slot not set yet
						$medicineId = '';
						$schedule_time = '';
						$selected_days = [];

						if (isset($slotData[$slot])) {
							$data = $slotData[$slot];

							$medicineId = $data['medicine_id'];
							$schedule_time = $data['dispense_time'];
							$selected_days = array_map('trim', explode(',', $data['dispense_days']));
						}
						?>

						<section class="data-form mb-4">
							<div class="header"></div>
							<div class="registration">
								<form action="controller/medicine-controller.php" method="POST" class="row gx-5 needs-validation" name="form" novalidate id="sensorForm<?php echo $slot; ?>">
									<div class="row gx-5 needs-validation">
										<label class="form-label" style="text-align: left; padding-top: .5rem; padding-bottom: 2rem; font-size: 1rem; font-weight: bold;">
											<i class='bx bxs-cog'></i> Slot <?php echo $slot; ?> Schedule
										</label>

										<input type="hidden" name="slot_id" value="<?php echo $slot; ?>">

										<!-- Medicine Selection -->
										<div class="col-md-6">
											<label for="medicine_name<?php echo $slot; ?>" class="form-label">Medicine Name<span> *</span></label>
											<select class="form-select form-control" name="medicine_name" id="medicine_name<?php echo $slot; ?>" required>
												<option value="" <?php echo empty($medicineId) ? 'selected' : ''; ?> disabled>Select Medicine</option>
												<?php foreach ($all_medicines as $medicine): ?>
													<?php $selected = ($medicine['id'] == $medicineId) ? 'selected' : ''; ?>
													<option value="<?php echo htmlspecialchars($medicine['id']); ?>" <?php echo $selected; ?>>
														<?php echo htmlspecialchars($medicine['medicine_name']); ?>
													</option>
												<?php endforeach; ?>
											</select>
											<div class="invalid-feedback">
												Please select a medicine.
											</div>
										</div>

										<!-- Time Selection -->
										<div class="col-md-6">
											<label for="schedule_time<?php echo $slot; ?>" class="form-label">Select Time <span> *</span></label>
											<input type="time" step="1" class="form-control"
												name="schedule_time"
												id="schedule_time<?php echo $slot; ?>"
												value="<?php echo htmlspecialchars($schedule_time); ?>"
												required>
											<div class="invalid-feedback">
												Please select a schedule time.
											</div>
										</div>

										<!-- Day Selection -->
										<label class="form-label">Select Days to Dispense Medicine <span>*</span></label>
										<ul class="other-option clearfix">
											<?php foreach ($all_days as $day): ?>
												<?php
												$dayID = $day['id'];
												$dayName = $day['day'];
												$isChecked = in_array($dayID, $selected_days) ? 'checked' : '';
												?>
												<li>
													<div class="radio-box">
														<input type="checkbox" name="days[]" value="<?php echo $dayID; ?>" id="day_<?php echo $slot . '_' . $dayID; ?>" <?php echo $isChecked; ?>>
														<label for="day_<?php echo $slot . '_' . $dayID; ?>"><?php echo htmlspecialchars($dayName); ?></label>
													</div>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>

									<div class="addBtn mt-3">
										<button type="submit" class="btn-primary" name="btn-update-thresholds" id="btn-update<?php echo $slot; ?>">Set</button>
									</div>
								</form>
							</div>
						</section>

					<?php endfor; ?>

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
document.addEventListener('DOMContentLoaded', function() {

	// ==============================================
	// 1️⃣ Load existing slot schedules from PHP
	// ==============================================
	const existingSchedules = <?php echo json_encode($slotData); ?>; 
	// Example: { "1": { "dispense_time": "08:00", "dispense_days": "1,2" }, "2": {...} }

	// ==============================================
	// 2️⃣ Helper: Convert time to minutes
	// ==============================================
	function getTimeInMinutes(time) {
		const [h, m] = time.split(':').map(Number);
		return h * 60 + m;
	}

	// ==============================================
	// 3️⃣ Helper: Check if time conflicts
	// ==============================================
	function hasConflict(selectedTime, selectedDays, currentSlot) {
		const selectedMinutes = getTimeInMinutes(selectedTime);

		for (const slotId in existingSchedules) {
			if (slotId == currentSlot) continue;

			const sched = existingSchedules[slotId];
			if (!sched || !sched.dispense_time || !sched.dispense_days) continue;

			const otherDays = sched.dispense_days.split(',').map(d => d.trim());
			const overlap = selectedDays.some(d => otherDays.includes(d));
			if (!overlap) continue;

			const otherMinutes = getTimeInMinutes(sched.dispense_time);
			if (Math.abs(selectedMinutes - otherMinutes) < 60) {
				return true; // ⛔ conflict found
			}
		}
		return false;
	}

	// ==============================================
	// 4️⃣ Monitor all 15 slot forms dynamically
	// ==============================================
	for (let i = 1; i <= 15; i++) {
		const timeInput = document.getElementById(`schedule_time${i}`);
		const form = document.getElementById(`sensorForm${i}`);

		if (!timeInput || !form) continue;

		timeInput.addEventListener('change', function() {
			const selectedTime = this.value;
			const checkedDays = Array.from(form.querySelectorAll('input[name="days[]"]:checked')).map(d => d.value);

			if (selectedTime && checkedDays.length > 0) {
				if (hasConflict(selectedTime, checkedDays, i)) {
					// 🔔 Show SweetAlert warning
					swal({
						title: "Time Conflict!",
						text: "Another slot already has a medicine scheduled within 1 hour on the same day(s). Please choose a different time.",
						icon: "warning",
						button: "Okay",
					});
					
					this.value = ''; // Reset invalid time
				} else {
					// ✅ Update local record for live checking
					existingSchedules[i] = { 
						dispense_time: selectedTime, 
						dispense_days: checkedDays.join(',') 
					};
				}
			}
		});
	}
});
</script>

<?php
// =============================================
// ✅ Display session-based SweetAlerts (PHP)
// =============================================
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
?>
<script>
	swal({
		title: "<?php echo $_SESSION['status_title']; ?>",
		text: "<?php echo $_SESSION['status']; ?>",
		icon: "<?php echo $_SESSION['status_code']; ?>",
		button: false,
		timer: <?php echo $_SESSION['status_timer']; ?>,
	});
</script>
<?php
	unset($_SESSION['status']);
}
?>

</body>

</html>