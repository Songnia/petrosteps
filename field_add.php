<?php
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
		header('Location:index.php');
		exit;
	}
 	$db = new DB();
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'add_field'){
		array_walk($_POST, 'cleanVar');
	$count = $db->get_field_count('');
	if($count < 6){
		$added = $db->add_field();
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in adding block please try again';
		if($added){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Field information added successfully';
		}
	}
	else{
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Can not add more than 6 Fields';
	}
		header('Location:field_list.php');
		exit;
	}
	$wells = $db->get_all_data('tbl_well');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Field Add - <?php echo APP_NAME; ?></title>

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
    <h1 class="md3-page-title">Add new Field</h1>
    <p class="md3-page-subtitle">Add field information</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">terrain</span>
    <h2>Add new Field</h2>
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
        <input type="text" id="Field_Name" name="Field_Name" data-required="true" placeholder=" ">
        <label for="Field_Name">Field Name</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Average_TD" name="Field_Average_TD" data-required="true" placeholder=" ">
        <label for="Field_Average_TD">Field Average TD (Meters)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Road_Cost" name="Field_Road_Cost" data-required="true" placeholder=" ">
        <label for="Field_Road_Cost">Field Road Cost (USD)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Accommodation_Cost" name="Field_Accommodation_Cost" data-required="true" placeholder=" ">
        <label for="Field_Accommodation_Cost">Field Accommodation Cost (USD)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Drilling_Cost" name="Field_Drilling_Cost" data-required="true" placeholder=" ">
        <label for="Field_Drilling_Cost">Field Drilling cost (USD/M)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Formation_Eval_cost_Exp" name="Field_Formation_Eval_cost_Exp" data-required="true" placeholder=" ">
        <label for="Field_Formation_Eval_cost_Exp">Field Formation Eval cost Exp (USD/M)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Formation_Eval_cost_Dev" name="Field_Formation_Eval_cost_Dev" data-required="true" placeholder=" ">
        <label for="Field_Formation_Eval_cost_Dev">Field Formation Eval cost Dev (USD/M)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Casing_Cement_cost" name="Field_Casing_Cement_cost" data-required="true" placeholder=" ">
        <label for="Field_Casing_Cement_cost">Field Casing Cement cost (USD/M)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Testing_cost" name="Field_Testing_cost" data-required="true" placeholder=" ">
        <label for="Field_Testing_cost">Field Testing cost (USD/WELL)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Completion_Cost" name="Field_Completion_Cost" data-required="true" placeholder=" ">
        <label for="Field_Completion_Cost">Field Completion Cost (USD/WELL)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Other_Gen_Sup_Cost" name="Other_Gen_Sup_Cost" data-required="true" placeholder=" ">
        <label for="Other_Gen_Sup_Cost">Other Gen Sup Cost (USD)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Other_Tech_Sup_Cost" name="Other_Tech_Sup_Cost" data-required="true" placeholder=" ">
        <label for="Other_Tech_Sup_Cost">Other Tech Sup Cost (USD)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Abandonment_cost" name="Field_Abandonment_cost" data-required="true" placeholder=" ">
        <label for="Field_Abandonment_cost">Field Abandonment cost (USD/WELL)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Water_Saturation" name="Field_Water_Saturation" data-required="true" placeholder=" ">
        <label for="Field_Water_Saturation">Field Water Saturation (%)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Field_Porosity" name="Field_Field_Porosity" data-required="true" placeholder=" ">
        <label for="Field_Field_Porosity">Field Porosity (%)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_BO" name="Field_BO" data-required="true" placeholder=" ">
        <label for="Field_BO">Field BO (B/STB)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Reservoir_Volume" name="Field_Reservoir_Volume" data-required="true" placeholder=" ">
        <label for="Field_Reservoir_Volume">Field Reservoir Volume (B)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Field_Recovery_Factor" name="Field_Recovery_Factor" data-required="true" placeholder=" ">
        <label for="Field_Recovery_Factor">Field Recovery Factor (%)</label>
      </div>
      <div class="md3-field md3-field--select">
        <select name="Field_ExpWell1" data-required="true">
          <?php
            foreach($wells as $well) {
              echo "<option value='".$well['Wid']."'>".$well['Well_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field_ExpWell1">Field ExpWell 1</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-field md3-field--select">
        <select name="Field_ExpWell2" data-required="true">
          <?php
            foreach($wells as $well) {
              echo "<option value='".$well['Wid']."'>".$well['Well_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field_ExpWell2">Field ExpWell 2</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-field md3-field--select">
        <select name="Field_AppWell1" data-required="true">
          <?php
            foreach($wells as $well) {
              echo "<option value='".$well['Wid']."'>".$well['Well_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field_AppWell1">Field AppWell 1</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-field md3-field--select">
        <select name="Field_AppWell2" data-required="true">
          <?php
            foreach($wells as $well) {
              echo "<option value='".$well['Wid']."'>".$well['Well_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field_AppWell2">Field AppWell 2</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-field md3-field--select">
        <select name="Field_DevWell1" data-required="true">
          <?php
            foreach($wells as $well) {
              echo "<option value='".$well['Wid']."'>".$well['Well_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field_DevWell1">Field DevWell 1</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-field md3-field--select">
        <select name="Field_DevWell2" data-required="true">
          <?php
            foreach($wells as $well) {
              echo "<option value='".$well['Wid']."'>".$well['Well_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field_DevWell2">Field DevWell 2</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-field md3-field--select">
        <select name="Field_DevWell3" data-required="true">
          <?php
            foreach($wells as $well) {
              echo "<option value='".$well['Wid']."'>".$well['Well_Name']."</option>";
            }
          ?>
        </select>
        <label for="Field_DevWell3">Field DevWell 3</label>
        <span class="md3-field__trailing material-symbols-outlined">expand_more</span>
      </div>
      <div class="md3-form-card__actions">
        <button type="submit" class="md3-btn md3-btn--filled">Save</button>
        <input type="hidden" name="action" value="add_field">
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
