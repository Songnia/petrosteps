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

	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update_parameters'){
		array_walk($_POST, 'cleanVar');
		$update = $db->update_parameters();
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in updating Parameters, Entry already exists';
		if($update){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Parameters updated successfully';
		}

		header('Location:parameter_edit.php');
		exit;
	}
	$parameters = $db->get_parameters();
?>
<?php $active = 'parameters'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Edit Parameters — <?php echo APP_NAME; ?></title>

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
    <h1 class="md3-page-title">Edit Parameters</h1>
    <p class="md3-page-subtitle">Update system-wide configuration parameters</p>
  </div>
</div>

<div class="md3-form-card" style="max-width: 640px;">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">tune</span>
    <h2>Admin Parameters</h2>
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

	<form id="validate-basic" class="form parsley-form" data-validate="parsley" method="post">

<?php
	foreach($parameters as $param){
	echo '<div class="md3-field">
                <input type="text" class="form-control" placeholder=" " name="'.$param['name'].'" data-required="true" value="'.$param['value'].'">
                <label for="'.$param['name'].'">'.$param['display_name'].' '.$param['unit'].'</label>
		</div>';
	}
?>

			<div class="md3-form-card__actions">
              <button type="submit" class="md3-btn md3-btn--filled">Update</button>
			  <input type="hidden" name="action" value="update_parameters">
            </div>

          </form>
  </div>
</div>

<?php include "includes/footer.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
  <script src="./js/plugins/parsley/parsley.js"></script>
  <script src="./js/plugins/select2/select2.js"></script>
  <script src="./js/target-admin.js"></script>
  <script src="./js/demos/form-validation.js"></script>

</body>
</html>
