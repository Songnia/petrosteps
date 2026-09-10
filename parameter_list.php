<?php

	include('includes/db.class.php');

	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){

		header('Location:index.php');

		exit;

	}

 	$db = new DB();

  $session_id = $db->get_active_session($_SESSION['user_id']);

  if($_SESSION['session_id'] !== $session_id){

    header('Location:login.php');

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
  <title>Parameters — <?php echo APP_NAME; ?></title>

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
    <h1 class="md3-page-title">Parameter List</h1>
    <p class="md3-page-subtitle">System-wide configuration parameters</p>
  </div>
</div>

<div class="md3-table-container">
  <div class="md3-table-toolbar">
    <h2 class="md3-table-title">Admin Parameters</h2>
    <div class="md3-table-actions">
      <a href="parameter_step.php" class="md3-btn md3-btn--outline"><span class="material-symbols-outlined">schedule</span> Step Duration</a>
      <a href="parameter_edit.php"><button class="md3-btn md3-btn--filled"><span class="material-symbols-outlined">edit</span> Edit Parameters</button></a>
    </div>
  </div>

  <div style="overflow-x:auto;">
    <table class="md3-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Parameter</th>
          <th>Unit</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
<?php
	foreach($parameters as $key => $param){
	$key1 = $key+1;
		echo '
			<tr>
                <td>'.$key1.'</td>
                <td>'.$param['display_name'].'</td>
                <td>'.$param['unit'].'</td>
                <td>'.$param['value'].'</td>
			</tr>';
	}
?>
      </tbody>
    </table>
  </div>
</div>

<?php include "includes/footer.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
  <script src="./js/target-admin.js"></script>

</body>
</html>
