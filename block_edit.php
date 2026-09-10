<?php
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
		header('Location:index.php');
		exit;
	}

	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SERVER['REQUEST_METHOD'] != "POST"){
		header('Location:index.php');
		exit;
	}
	$db = new DB();
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete_block') {
		extract($_POST);
		$delete = $db->delete_block($bid);
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in deleting Block please try again';
		if($delete)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Block deleted successfully';
		}
		header('Location:block_list.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'edit_block'){
		extract($_POST);
		$block = $db->get_block($bid);
		if(!$bid) {
			header('Location:block_list.php');
			exit;
		}
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'update_block') {
		array_walk($_POST, 'cleanVar');
		$updated = $db->update_block();
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in updating block please try again';
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Block information updated successfully';
		}
		header('Location:block_list.php');
		exit;
	}
	$fields = $db->get_all_data('tbl_field');
?>
<?php $active = 'blocks'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Block Edit — <?php echo APP_NAME; ?></title>
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
    <h1 class="md3-page-title">Edit Block</h1>
    <p class="md3-page-subtitle">Update block data, costs and operational status</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">dashboard</span>
    <h2><?php echo htmlspecialchars($block['Block_Name'], ENT_QUOTES, 'UTF-8'); ?></h2>
  </div>
  <div class="md3-form-card__body">
    <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">
      <?php
        $text_fields = array(
          'Block_Name' => 'Block Name',
          'Block_Surface' => 'Block Surface (Ha)',
          'Block_License_Cost_Exp' => 'Block License Cost Exp (USD/Ha)',
          'Block_License_cost_Prod' => 'Block License Cost Prod (USD/Ha)',
          'Probability_to_find' => 'Probability to find (%)',
          'Signature_Bonus' => 'Signature Bonus ($)',
          'Block_Survey_Cost' => 'Block Survey Cost (USD/Ha)',
          'Block_Survey_interpretation_cost' => 'Block Survey interpretation cost (USD/Ha)'
        );
        foreach ($text_fields as $name => $label) {
          echo '<div class="md3-field">
            <input type="text" id="'.$name.'" name="'.$name.'" data-required="true" placeholder=" " value="'.htmlspecialchars($block[$name], ENT_QUOTES, 'UTF-8').'">
            <label for="'.$name.'">'.$label.'</label>
          </div>';
        }
      ?>

      <div class="md3-field">
        <textarea id="Block_Description" name="Block_Description" data-required="true" placeholder=" "><?php echo htmlspecialchars($block['Block_Description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        <label for="Block_Description">Block Description</label>
      </div>

      <?php
        $status_fields = array(
          'Block_License_Status' => 'Block License Status',
          'Block_Survey_Status' => 'Block Survey Status',
          'Block_Survey_Interpretation_Status' => 'Block Survey Interpretation Status'
        );
        foreach ($status_fields as $name => $label) {
      ?>
        <div class="md3-field">
          <label class="md3-field__label"><?php echo $label; ?></label>
          <div class="md3-radio-group">
            <label class="md3-radio-item">
              <input type="radio" name="<?php echo $name; ?>" data-required="true" value="False" <?php if ($block[$name] === 'False') echo 'checked'; ?>>
              <span>False</span>
            </label>
            <label class="md3-radio-item">
              <input type="radio" name="<?php echo $name; ?>" data-required="true" value="True" <?php if ($block[$name] === 'True') echo 'checked'; ?>>
              <span>True</span>
            </label>
          </div>
        </div>
      <?php } ?>

      <div class="md3-field md3-field--select">
        <select id="Field" name="Field" data-required="true">
          <option value="">Select Field</option>
          <?php foreach ($fields as $field) { ?>
            <option value="<?php echo (int)$field['fid']; ?>" <?php if ($field['fid'] == $block['Field']) echo 'selected'; ?>><?php echo htmlspecialchars($field['Field_Name'], ENT_QUOTES, 'UTF-8'); ?></option>
          <?php } ?>
        </select>
        <label for="Field">Field</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>

      <div class="md3-form-card__actions">
        <a href="block_list.php" class="md3-btn md3-btn--outline">Cancel</a>
        <input type="hidden" name="action" value="update_block">
        <input type="hidden" name="bid" value="<?php echo (int)$block['bid']; ?>">
        <button type="submit" class="md3-btn md3-btn--filled">Update Block</button>
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
