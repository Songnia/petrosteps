<?php
	include('includes/db.class.php');
	if (!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || !isset($_SESSION['user_type']) || $_SESSION['user_type'] == 'Participant') {
		header('Location:index.php');
		exit;
	}

	$db = new DB();
	$action = isset($_POST['action']) ? $_POST['action'] : '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'delete_production_facility') {
		$pfid = isset($_POST['pfid']) ? (int)$_POST['pfid'] : 0;
		$deleted = $pfid ? $db->delete_production_facility($pfid) : false;
		$_SESSION['message_type'] = $deleted ? 'success' : 'danger';
		$_SESSION['message'] = $deleted
			? 'Production facility deleted successfully'
			: 'Error in deleting production facility please try again';
		header('Location:production_facility_list.php');
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'update_production_facility') {
		array_walk($_POST, 'cleanVar');
		$updated = $db->update_production_facility();
		$_SESSION['message_type'] = $updated ? 'success' : 'danger';
		$_SESSION['message'] = $updated
			? 'Production facility information updated successfully'
			: 'Error in updating production facility please try again';
		header('Location:production_facility_list.php');
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] != 'POST' || $action != 'edit_production_facility') {
		header('Location:production_facility_list.php');
		exit;
	}

	$pfid = isset($_POST['pfid']) ? (int)$_POST['pfid'] : 0;
	$production_facility = $pfid ? $db->get_production_facility($pfid) : false;
	if (!$production_facility) {
		header('Location:production_facility_list.php');
		exit;
	}
	$active = 'pfacilities';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Production Facility Edit - <?php echo APP_NAME; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/md3-theme.css" rel="stylesheet">
  <link href="css/target-admin.css" rel="stylesheet">
</head>
<body>
<?php include_once "includes/navbar.php"; ?>

<div class="md3-page-header">
  <div>
    <h1 class="md3-page-title">Edit Production Facility</h1>
    <p class="md3-page-subtitle">Update capacity, investment cost and operating status</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">factory</span>
    <h2><?php echo htmlspecialchars($production_facility['Prod_Facilities_Name'], ENT_QUOTES, 'UTF-8'); ?></h2>
  </div>
  <div class="md3-form-card__body">
    <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">
      <div class="md3-field">
        <input type="text" id="Prod_Facilities_Name" name="Prod_Facilities_Name" data-required="true" placeholder=" " value="<?php echo htmlspecialchars($production_facility['Prod_Facilities_Name'], ENT_QUOTES, 'UTF-8'); ?>">
        <label for="Prod_Facilities_Name">Production Facilities Name</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Prod_Facilities_Capacity" name="Prod_Facilities_Capacity" data-required="true" placeholder=" " value="<?php echo htmlspecialchars($production_facility['Prod_Facilities_Capacity'], ENT_QUOTES, 'UTF-8'); ?>">
        <label for="Prod_Facilities_Capacity">Production Facilities Capacity (B/D)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Prod_Facilities_Cost" name="Prod_Facilities_Cost" data-required="true" placeholder=" " value="<?php echo htmlspecialchars($production_facility['Prod_Facilities_Cost'], ENT_QUOTES, 'UTF-8'); ?>">
        <label for="Prod_Facilities_Cost">Production Facilities Cost (USD)</label>
      </div>

      <div class="md3-field">
        <label class="md3-field__label">Production Facility Status</label>
        <div class="md3-radio-group">
          <label class="md3-radio-item">
            <input type="radio" name="Production_Facility_Satus" data-required="true" value="On" <?php if ($production_facility['Production_Facility_Satus'] === 'On') echo 'checked'; ?>>
            <span>On</span>
          </label>
          <label class="md3-radio-item">
            <input type="radio" name="Production_Facility_Satus" data-required="true" value="Off" <?php if ($production_facility['Production_Facility_Satus'] === 'Off') echo 'checked'; ?>>
            <span>Off</span>
          </label>
        </div>
      </div>

      <div class="md3-field">
        <input type="text" id="Prod_Facilities_decommissioning_cost" name="Prod_Facilities_decommissioning_cost" data-required="true" placeholder=" " value="<?php echo htmlspecialchars($production_facility['Prod_Facilities_decommissioning_cost'], ENT_QUOTES, 'UTF-8'); ?>">
        <label for="Prod_Facilities_decommissioning_cost">Production Facilities Decommissioning Cost (USD)</label>
      </div>

      <div class="md3-form-card__actions">
        <a href="production_facility_list.php" class="md3-btn md3-btn--outline">Cancel</a>
        <input type="hidden" name="action" value="update_production_facility">
        <input type="hidden" name="pfid" value="<?php echo (int)$production_facility['pfid']; ?>">
        <button type="submit" class="md3-btn md3-btn--filled">Update Facility</button>
      </div>
    </form>
  </div>
</div>

<?php include "includes/footer.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
  <script src="./js/plugins/parsley/parsley.js"></script>
  <script src="./js/target-admin.js"></script>
  <script src="./js/demos/form-validation.js"></script>
</body>
</html>
