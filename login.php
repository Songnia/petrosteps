<?php

/***********************************************************************************

 * Purpose		: Login Page

 * Created On	: 02-08-2016

 ***********************************************************************************/

 include('includes/db.class.php');

  session_regenerate_id();

 

if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'login'){

	$db = new DB();

	$user = $db->verify_login($_POST['username'],$_POST['password']);

	setcookie ("username", "", time() - 3600*24*30);

	if($user) {

    $session_id = session_id();

    $db->set_active_session($user['id'], $session_id);

		$_SESSION['user_id'] = $user['id'];

		$_SESSION['user_type'] = $user['user_type'];

    $_SESSION['username'] = $user['username'];

		$_SESSION['session_id'] = $session_id;

		if(isset($_POST['remember']) &&$_POST['remember'] == 1)

		{

			setcookie("username", $_POST['username'], time() + 3600*24*30);

		}

		header('Location: index.php');

		exit;

	}

	$_SESSION['message_type'] = 'danger';

	$_SESSION['message'] = 'Error in login please check credentials';

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Petrosteps — Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0..1,-50..200" rel="stylesheet">
  <link href="css/md3-theme.css" rel="stylesheet">
</head>
<body class="login-page">

<div class="login-split">

  <!-- ======= PANNEAU GAUCHE ======= -->
  <div class="login-split__left">

    <!-- Image plein panneau -->
    <div class="login-left__img-placeholder">
      <img src="img/avatars/brand-image.jpg" alt="Oilfield simulation">
    </div>

    <!-- Texte en overlay absolu sur le bas de l'image -->
    <div class="login-left__text">
      <h2>Welcome to Petrosteps</h2>
      <p>Petrosteps is a training software by Consoltia, for trainees to simulate and understand the life cycle of a standard Oilfield. Budget, License, Survey, Exploration, Appraisal, Development, Production, Stimulation and Abandonment steps involved.</p>
    </div>

  </div>

  <!-- ======= PANNEAU DROIT ======= -->
  <div class="login-split__right">

    <div class="login-card">

      <div class="login-brand">
        <span class="login-brand__name"><span class="brand-wordmark" aria-label="Petrosteps">Petro<span class="brand-wordmark__dollar" aria-hidden="true">$</span>teps</span></span>
        <span class="login-brand__tagline">Oilfield lifecycle training simulator</span>
      </div>

<?php
if(isset($_SESSION['message']))
{
  $alert_class = ($_SESSION['message_type'] == 'success') ? 'login-alert--success' : 'login-alert--error';
  $alert_icon = ($_SESSION['message_type'] == 'success') ? 'check_circle' : 'error';
?>
      <div class="login-alert <?php echo $alert_class; ?>">
        <span class="material-symbols-outlined"><?php echo $alert_icon; ?></span>
        <?php echo $_SESSION['message']; ?>
      </div>
<?php
}
?>

      <form method="POST" action="login.php">
        <div class="md3-field">
          <input type="text" id="username" name="username" required placeholder=" "
                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
          <label for="username">Username</label>
        </div>

        <div class="md3-field" style="margin-top:16px;">
          <input type="password" id="password" name="password" required placeholder=" ">
          <label for="password">Password</label>
        </div>

        <button type="submit" class="login-submit">
          <span class="material-symbols-outlined">login</span>
          Sign in
        </button>

        <input type="hidden" name="action" value="login" />
      </form>

    </div>

    <p class="login-footer"><span class="brand-wordmark" aria-label="Petrosteps">Petro<span class="brand-wordmark__dollar" aria-hidden="true">$</span>teps</span> &copy; Consoltia Inc. &mdash; v2.0</p>

  </div>

</div>

</body>
</html>

<?php

	unset($_SESSION['message']);

	unset($_SESSION['message_type']);

?>
