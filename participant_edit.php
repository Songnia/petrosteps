<?php
/***********************************************************************************
 * Purpose    : Edit Participant
 * Created On : 11-11-2016
 ***********************************************************************************/
 include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SERVER['REQUEST_METHOD'] != "POST"){
		header('Location:index.php');
		exit;
	}
$db = new DB();
  $session_id = $db->get_active_session($_SESSION['user_id']);
  if($_SESSION['session_id'] !== $session_id){
    header('Location:login.php');
    exit;
  }
	
	#Update Participant INFOMATION
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update_user') {
		array_walk($_POST, 'cleanVar');
		extract($_POST);
		$update = $db->update_user($user_id, $username, $email, $firstname);
    if(@$_POST['change_password'] == 1){
      if($confirm_pwd !== $pwd){
      $_SESSION['message_type'] = 'danger';
      $_SESSION['message'] = 'Password and Confirm Password must be same.';
      header('Location:trainer_list.php');
      exit;
    }
    $pass_change = $db->change_password($current, $confirm_pwd, $user_id, $username, false);
    }

			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in updaing Participant, Entry already exists';
		if($update)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Participant updated successfully';
		}
		header('Location:participant_list.php');
		exit;
	}
	#DELETE User INFOMATION
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete_user')
	{
		extract($_POST);
		$delete = $db->delete_user($user_id);
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in deleting Participant please try again';
		if($delete)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Participant deleted successfully';
		}
		header('Location:participant_list.php');
		exit;
	}
	#EDIT User
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'edit_user') {
		extract($_POST);
		$user = $db->get_user($user_id);
		if(!$user_id)
		{
			header('Location:participant_list.php');
			exit;
		}
	}
  ?>
<?php $active = 'participants'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Edit Participant — <?php echo APP_NAME; ?></title>

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
    <h1 class="md3-page-title">Edit Participant</h1>
    <p class="md3-page-subtitle">Update participant information</p>
  </div>
</div>

<div class="md3-form-card" style="max-width:600px;">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">group</span>
    <h2>Participant Details</h2>
  </div>
  <div class="md3-form-card__body">
    <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">

      <div class="md3-field">
        <input type="text" id="name" name="firstname" required placeholder=" " data-required="true" value="<?php echo $user['firstname'];?>">
        <label for="name">Name</label>
      </div>

      <div class="md3-field">
        <input type="text" id="username" name="username" required placeholder=" " data-required="true" value="<?php echo $user['username'];?>">
        <label for="username">User Name</label>
      </div>

      <div style="padding:12px 0 8px;border-top:1px solid var(--md-outline-variant);margin-top:8px;">
        <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;color:var(--md-on-surface-variant);">
          <input type="checkbox" onclick="change_pass(this)" name="change_password" id="change_password" value="1" style="accent-color:var(--md-primary);width:16px;height:16px;">
          Change Password
        </label>
      </div>

      <div id="change_password_div" style="display:none;">
        <div class="md3-field">
          <input type="password" id="pwd" name="pwd" placeholder=" ">
          <label for="pwd">New Password</label>
        </div>
        <div class="md3-field">
          <input type="password" id="confirm_pwd" name="confirm_pwd" placeholder=" ">
          <label for="confirm_pwd">Confirm Password</label>
        </div>
      </div>

      <div class="md3-form-card__actions">
        <a href="participant_list.php" class="md3-btn md3-btn--outlined">Cancel</a>
        <button type="submit" class="md3-btn md3-btn--filled">
          <span class="material-symbols-outlined">save</span>
          Update User
        </button>
      </div>

      <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
      <input type="hidden" name="email" value="<?php echo $user['email']; ?>">
      <input type="hidden" name="action" value="update_user">
      <input type="hidden" name="current" value="<?php echo $user['pwd']; ?>">

    </form>
  </div>
</div>

<?php include "includes/footer.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>

  <!-- Plugin JS -->
  <script src="./js/plugins/parsley/parsley.js"></script>
  <script src="./js/plugins/icheck/jquery.icheck.js"></script>
  <script src="./js/plugins/datepicker/bootstrap-datepicker.js"></script>
  <script src="./js/plugins/timepicker/bootstrap-timepicker.js"></script>
  <script src="./js/plugins/simplecolorpicker/jquery.simplecolorpicker.js"></script>
  <script src="./js/plugins/select2/select2.js"></script>

  <!-- App JS -->
  <script src="./js/target-admin.js"></script>

  <script src="./js/demos/form-validation.js"></script>
 <script>
  function change_pass(checkbox){
    if(checkbox.checked){
      $('#change_password_div').css('display', 'block');
    }
    else{
      $('#change_password_div').css('display', 'none');
    }
  }
  </script>
</body>
</html>
