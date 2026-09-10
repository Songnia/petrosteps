<?php
/***********************************************************************************
 * Purpose    : Create Participant user
 ***********************************************************************************/
  include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'])
	{
		header('Location:index.php');
		exit;
	}
  	$db = new DB();
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		array_walk($_POST, 'cleanVar');
		extract($_POST);
		// Participant accounts are created without an e-mail address.
		$email = '';
		if($confirm_pwd !== $pwd){
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Password and Confirm Password must be same.';
			header('Location:participant_add.php');
			exit;
		}
		$added = $db->add_user($firstname,$email,$username, $pwd, 'Participant', 0, '0000-00-00', $_SESSION['user_id']);
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = $db->last_error ?: 'Error in adding participant user please try again';
		if($added) {
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Participant user added successfully';
		}
    header('Location:participant_list.php');
    exit;
	}
 ?>
<?php $active = 'participants'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Create Participant — <?php echo APP_NAME; ?></title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- MD3 Tokens -->
  <link href="css/md3-theme.css?v=<?php echo filemtime('css/md3-theme.css'); ?>" rel="stylesheet">

  <!-- App CSS -->
  <link href="css/target-admin.css" rel="stylesheet">
</head>
<body>
<?php
  include_once "includes/navbar.php";
?>

<div class="md3-page-header">
  <div>
    <h1 class="md3-page-title">Create Participant User</h1>
    <p class="md3-page-subtitle">Add a new participant account</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">group</span>
    <h2>Create Participant</h2>
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
        <input type="text" id="name" name="firstname" data-required="true" placeholder=" ">
        <label for="name">Name</label>
      </div>
      <div class="md3-field">
        <input type="text" id="username" name="username" data-required="true" placeholder=" ">
        <label for="username">User Name</label>
      </div>
      <div class="md3-field md3-field--password">
        <input type="password" id="pwd" name="pwd" data-required="true" placeholder=" ">
        <label for="pwd">Password</label>
        <button type="button" class="md3-password-toggle" data-password-target="pwd" aria-label="Show password" aria-pressed="false">
          <span class="material-symbols-outlined">visibility</span>
        </button>
      </div>
      <div class="md3-field md3-field--password">
        <input type="password" id="confirm_pwd" name="confirm_pwd" data-required="true" placeholder=" ">
        <label for="confirm_pwd">Confirm Password</label>
        <button type="button" class="md3-password-toggle" data-password-target="confirm_pwd" aria-label="Show password" aria-pressed="false">
          <span class="material-symbols-outlined">visibility</span>
        </button>
      </div>
      <div class="md3-form-card__actions">
        <a href="participant_list.php" class="md3-btn md3-btn--outlined">Cancel</a>
        <button type="submit" class="md3-btn md3-btn--filled">Create Participant</button>
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
  <script src="./js/password-visibility.js"></script>
  <script src="./js/demos/form-validation.js"></script>
</body>
</html>
