<?php
	include('includes/db.class.php');
	if (!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || !isset($_SESSION['user_type']) || $_SESSION['user_type'] == 'Participant') {
		header('Location:index.php');
		exit;
	}

	$db = new DB();
	$action = isset($_POST['action']) ? $_POST['action'] : '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'delete_field') {
		$fid = isset($_POST['fid']) ? (int)$_POST['fid'] : 0;
		$delete = $fid ? $db->delete_field($fid) : false;
		$_SESSION['message_type'] = $delete ? 'success' : 'danger';
		$_SESSION['message'] = $delete ? 'Field deleted successfully' : 'Error in deleting Field please try again';
		header('Location:field_list.php');
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'update_field') {
		array_walk($_POST, 'cleanVar');
		$updated = $db->update_field();
		$_SESSION['message_type'] = $updated ? 'success' : 'danger';
		$_SESSION['message'] = $updated ? 'Field information updated successfully' : 'Error in updating field please try again';
		header('Location:field_list.php');
		exit;
	}

	if ($_SERVER['REQUEST_METHOD'] != 'POST' || $action != 'edit_field') {
		header('Location:field_list.php');
		exit;
	}

	$fid = isset($_POST['fid']) ? (int)$_POST['fid'] : 0;
	$field = $fid ? $db->get_field($fid) : false;
	if (!$field) {
		header('Location:field_list.php');
		exit;
	}

	$wells = $db->get_all_data('tbl_well');
	$active = 'fields';
	$text_fields = array(
		'Field_Name' => 'Field Name',
		'Field_Average_TD' => 'Field Average TD (Meters)',
		'Field_Road_Cost' => 'Field Road Cost (USD)',
		'Field_Accommodation_Cost' => 'Field Accommodation Cost (USD)',
		'Field_Drilling_Cost' => 'Field Drilling cost (USD/M)',
		'Field_Formation_Eval_cost_Exp' => 'Field Formation Eval cost Exp (USD/M)',
		'Field_Formation_Eval_cost_Dev' => 'Field Formation Eval cost Dev (USD/M)',
		'Field_Casing_Cement_cost' => 'Field Casing Cement cost (USD/M)',
		'Field_Testing_cost' => 'Field Testing cost (USD/WELL)',
		'Field_Completion_Cost' => 'Field Completion Cost (USD/WELL)',
		'Other_Gen_Sup_Cost' => 'Other Gen Sup Cost (USD)',
		'Other_Tech_Sup_Cost' => 'Other Tech Sup Cost (USD)',
		'Field_Abandonment_cost' => 'Field Abandonment cost (USD/WELL)',
		'Field_Water_Saturation' => 'Field Water Saturation (%)',
		'Field_Field_Porosity' => 'Field Porosity (%)',
		'Field_BO' => 'Field BO (B/STB)',
		'Field_Reservoir_Volume' => 'Field Reservoir Volume (B)',
		'Field_Recovery_Factor' => 'Field Recovery Factor (%)'
	);
	$well_fields = array(
		'Field_ExpWell1' => 'Field ExpWell 1',
		'Field_ExpWell2' => 'Field ExpWell 2',
		'Field_AppWell1' => 'Field AppWell 1',
		'Field_AppWell2' => 'Field AppWell 2',
		'Field_DevWell1' => 'Field DevWell 1',
		'Field_DevWell2' => 'Field DevWell 2',
		'Field_DevWell3' => 'Field DevWell 3'
	);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Field Edit - <?php echo APP_NAME; ?></title>
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
    <h1 class="md3-page-title">Edit Field</h1>
    <p class="md3-page-subtitle">Update field costs, reservoir data and assigned wells</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">terrain</span>
    <h2><?php echo htmlspecialchars($field['Field_Name'], ENT_QUOTES, 'UTF-8'); ?></h2>
  </div>
  <div class="md3-form-card__body">
    <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">
      <?php foreach ($text_fields as $name => $label) { ?>
        <div class="md3-field">
          <input type="text" id="<?php echo $name; ?>" name="<?php echo $name; ?>" data-required="true" placeholder=" " value="<?php echo htmlspecialchars($field[$name], ENT_QUOTES, 'UTF-8'); ?>">
          <label for="<?php echo $name; ?>"><?php echo $label; ?></label>
        </div>
      <?php } ?>

      <?php foreach ($well_fields as $name => $label) { ?>
        <div class="md3-field md3-field--select">
          <select id="<?php echo $name; ?>" name="<?php echo $name; ?>" data-required="true">
            <option value="">Select Well</option>
            <?php foreach ($wells as $well) { ?>
              <option value="<?php echo (int)$well['Wid']; ?>" <?php if ($well['Wid'] == $field[$name]) echo 'selected'; ?>><?php echo htmlspecialchars($well['Well_Name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php } ?>
          </select>
          <label for="<?php echo $name; ?>"><?php echo $label; ?></label>
          <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
        </div>
      <?php } ?>

      <div class="md3-form-card__actions">
        <a href="field_list.php" class="md3-btn md3-btn--outline">Cancel</a>
        <input type="hidden" name="action" value="update_field">
        <input type="hidden" name="fid" value="<?php echo (int)$field['fid']; ?>">
        <button type="submit" class="md3-btn md3-btn--filled">Update Field</button>
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
