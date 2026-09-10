<?php
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
		header('Location:index.php');
		exit;
	}
 	$db = new DB();
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'add_block'){
		array_walk($_POST, 'cleanVar');
		
	$count = $db->get_block_count('');
	if($count < 6){
		$added = $db->add_block();
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in adding block please try again';
		if($added){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Block information added successfully';
		}
	}
	else{
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Can not add more than 6 Blocks';
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
  <title>Block Add — <?php echo APP_NAME; ?></title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- MD3 Tokens -->
  <link href="css/md3-theme.css" rel="stylesheet">

  <!-- App CSS -->
  <link href="css/target-admin.css" rel="stylesheet">
</head>
<body>
<?php
  include_once "includes/navbar.php";
?>

<div class="md3-page-header">
  <div>
    <h1 class="md3-page-title">Add new Block</h1>
    <p class="md3-page-subtitle">Create a new block record</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">dashboard</span>
    <h2>Add new Block</h2>
  </div>
  <div class="md3-form-card__body">
  <?php
    if(isset($_SESSION['message'])) {
      echo '<div class="alert alert-'.$_SESSION['message_type'].'">
      <a class="close" data-dismiss="alert" href="#" aria-hidden="true">×</a>
      '.$_SESSION['message'].'
      </div>';
    }
  ?>
    <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">
      <div class="md3-field">
        <input type="text" id="Block_Name" name="Block_Name" data-required="true" placeholder=" ">
        <label for="Block_Name">Block Name</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Block_Surface" name="Block_Surface" data-required="true" placeholder=" ">
        <label for="Block_Surface">Block Surface (Ha)</label>
      </div>
      <div class="md3-field">
        <textarea id="Block_Description" name="Block_Description" data-required="true" placeholder=" "></textarea>
        <label for="Block_Description">Block Description</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Block_License_Cost_Exp" name="Block_License_Cost_Exp" data-required="true" placeholder=" ">
        <label for="Block_License_Cost_Exp">Block License Cost Exp (USD/Ha)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Block_License_cost_Prod" name="Block_License_cost_Prod" data-required="true" placeholder=" ">
        <label for="Block_License_cost_Prod">Block License cost Prod (USD/Ha)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Probability_to_find" name="Probability_to_find" data-required="true" placeholder=" ">
        <label for="Probability_to_find">Probability to find (%)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Signature_Bonus" name="Signature_Bonus" data-required="true" placeholder=" ">
        <label for="Signature_Bonus">Signature Bonus ($)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Block_Survey_Cost" name="Block_Survey_Cost" data-required="true" placeholder=" ">
        <label for="Block_Survey_Cost">Block Survey Cost (USD/Ha)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Block_Survey_interpretation_cost" name="Block_Survey_interpretation_cost" data-required="true" placeholder=" ">
        <label for="Block_Survey_interpretation_cost">Block Survey interpretation cost (USD/Ha)</label>
      </div>
      <div class="md3-field">
        <label class="md3-field__label">Block License Status</label>
        <div class="md3-radio-group">
          <label class="md3-radio-item">
            <input type="radio" name="Block_License_Status" data-required="true" value="False" checked>
            <span>False</span>
          </label>
          <label class="md3-radio-item">
            <input type="radio" name="Block_License_Status" data-required="true" value="True">
            <span>True</span>
          </label>
        </div>
      </div>
      <div class="md3-field">
        <label class="md3-field__label">Block Survey Status</label>
        <div class="md3-radio-group">
          <label class="md3-radio-item">
            <input type="radio" name="Block_Survey_Status" data-required="true" value="False" checked>
            <span>False</span>
          </label>
          <label class="md3-radio-item">
            <input type="radio" name="Block_Survey_Status" data-required="true" value="True">
            <span>True</span>
          </label>
        </div>
      </div>
      <div class="md3-field">
        <label class="md3-field__label">Block Survey Interpretation Status</label>
        <div class="md3-radio-group">
          <label class="md3-radio-item">
            <input type="radio" name="Block_Survey_Interpretation_Status" data-required="true" value="False" checked>
            <span>False</span>
          </label>
          <label class="md3-radio-item">
            <input type="radio" name="Block_Survey_Interpretation_Status" data-required="true" value="True">
            <span>True</span>
          </label>
        </div>
      </div>
      <div class="md3-field md3-field--select">
        <select id="Field" name="Field" data-required="true">
          <option value="">Select Field</option>
          <?php
            foreach($fields as $field) {
              echo "<option value='".$field['fid']."'>".$field['Field_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field">Field</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-form-card__actions">
        <input type="hidden" name="action" value="add_block">
        <button type="submit" class="md3-btn md3-btn--filled">Save</button>
      </div>
    </form>
  </div>
</div>

<?php include "includes/footer.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
  <script src="./js/plugins/parsley/parsley.js"></script>
  <script src="./js/plugins/icheck/jquery.icheck.js"></script>
  <script src="./js/plugins/datepicker/bootstrap-datepicker.js"></script>
  <script src="./js/plugins/timepicker/bootstrap-timepicker.js"></script>
  <script src="./js/plugins/simplecolorpicker/jquery.simplecolorpicker.js"></script>
  <script src="./js/plugins/select2/select2.js"></script>
  <script src="./js/target-admin.js"></script>
  <script src="./js/demos/form-validation.js"></script>
</body>
</html>
