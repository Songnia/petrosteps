<?php

	include('includes/db.class.php');
  $db = new DB();
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
		header('Location:index.php');
		exit;
	}
  $session_id = $db->get_active_session($_SESSION['user_id']);
  if($_SESSION['session_id'] !== $session_id){
    header('Location:login.php');
    exit;
  }
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update_step_duration'){
		array_walk($_POST, 'cleanVar');
		$update = $db->update_project_year();
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in updating Parameters, Entry already exists';
		if($update){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Parameters updated successfully';
		}
		header('Location:parameter_step.php');
		exit;
	}
	$parameters = $db->get_project_years();
?>
<?php $active = 'parameters'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step Duration — <?php echo APP_NAME; ?></title>

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
    <h1 class="md3-page-title">Step Duration</h1>
    <p class="md3-page-subtitle">Configure duration for each project step</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">schedule</span>
    <h2>Step Duration</h2>
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
	<form id="validate-basic" class="form-horizontal" data-validate="parsley" class="form parsley-form" method="post" >
<?php
$step_array = array(1=>'STEP 1 LICENSE', 2=> 'STEP 2 SURVEY', 3=> 'STEP 3 EXPLORATION', 4=> 'STEP 4 APPRAISAL', 5=> 'STEP 5 DEVELOPMENT', 6=> 'STEP 6 PRODUCTION');
	foreach($parameters as $param){
	echo '<div class="md3-field">
              <input onkeypress="return isNumber(event)" type="text" maxlength="3" id="step_'.$param['step'].'" name="'.$param['step_name'].'" required placeholder=" " data-required="true" value="'.$param['project_year'].'">
              <label for="step_'.$param['step'].'">'.$step_array[$param['step']].' Duration (years)</label>
            </div>';
	}
?>
  <div class="md3-form-card__actions">
    <button type="submit" class="md3-btn md3-btn--filled">
      <span class="material-symbols-outlined">save</span>
      Update
    </button>
    <input type="hidden" name="action" value="update_step_duration">
  </div>
        </form>
  </div>
</div>

<?php include "includes/footer.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
  <script src="./js/libs/jquery-ui-1.9.2.custom.min.js"></script>
  <script src="./js/plugins/parsley/parsley.js"></script>
  <script src="./js/plugins/select2/select2.js"></script>
  <script src="./js/target-admin.js"></script>
  <script src="./js/demos/form-validation.js"></script>
  <script>
  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
  }
  </script>
</body>
</html>
