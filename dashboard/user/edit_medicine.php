<?php
include_once 'header.php';

$medicine_id = $_GET['id'];

$stmt = $user->runQuery("SELECT * FROM medicines WHERE id=:id");
$stmt->execute(array(":id" => $medicine_id));
$medicine_data = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $header_dashboard->getHeaderDashboard() ?>

    <title>Edit Medicine Data</title>
</head>

<body>

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
                                        <input type="hidden" name="medicine_id" value="<?php echo $medicine_id; ?>">
                                        <div class="col-md-6">
                                            <label for="medicine_name" class="form-label">Medicine Name<span> *</span></label>
                                            <input type="text" class="form-control" autocapitalize="on" autocomplete="off" name="medicine_name" id="medicine_name" value="<?php echo $medicine_data['medicine_name'] ?>" required>
                                            <div class="invalid-feedback">
                                                Please provide a Medicine Name.
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="medicine_description" class="form-label">Medicine Description<span> *</span></label>
                                            <input type="text" class="form-control" autocapitalize="on" autocomplete="off" name="medicine_description" id="medicine_description" value="<?php echo $medicine_data['description'] ?>" required>
                                            <div class="invalid-feedback">
                                                Please provide a Medicine Description.
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="dosage" class="form-label">Dosage<span> *</span></label>
                                            <input type="text" class="form-control" autocapitalize="on" autocomplete="off" name="dosage" id="dosage" value="<?php echo $medicine_data['dosage'] ?>" required>
                                            <div class="invalid-feedback">
                                                Please provide a Dosage.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="addBtn">
                                        <button type="submit" class="btn-primary" name="btn-update-medicine" id="btn-add" onclick="return IsEmpty(); sexEmpty();">Update</button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $footer_dashboard->getFooterDashboard() ?>
    <?php include_once '../../config/sweetalert.php'; ?>
    <script>
        //Load Modal
        $(window).on('load', function() {
            $('#medicineModal').modal('show');
        });
    </script>
</body>

</html>