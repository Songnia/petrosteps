<?php
/*************************************************
 * File Name	: change_password.php
************************************************/
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id']){
		header('Location:index');
		exit;
	}
	$db = new DB();

  $session_id = $db->get_active_session($_SESSION['user_id']);
  if($_SESSION['session_id'] !== $session_id){
    header('Location:login.php');
    exit;
  }

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$pass_changed = false;
		array_walk($_POST, 'cleanVar');
		extract($_POST);
		if($new_password === $new_password_again && strlen($new_password) > 4)
			$pass_changed = $db->change_password($current_password,$new_password);
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in changing password please try again';
		if(strlen($new_password) <= 4)
		{
			$_SESSION['message'] = 'Passowrd must be greater than 4 characters';
		}
		if($pass_changed)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Passwod Changed successfully';
		}
			header('Location:change_password.php');
			exit;
	}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <title>Change Password - <?php echo APP_NAME; ?></title>
  <meta charset="utf-8">
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700">
  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,300,700">
  <link rel="stylesheet" href="./css/font-awesome.min.css">
  <link rel="stylesheet" href="./js/libs/css/ui-lightness/jquery-ui-1.9.2.custom.min.css">
  <link rel="stylesheet" href="./css/bootstrap.min.css">

  <!-- Plugin CSS -->

  <link rel="stylesheet" href="./js/plugins/icheck/skins/minimal/blue.css">
  <link rel="stylesheet" href="./js/plugins/datepicker/datepicker.css">
  <link rel="stylesheet" href="./js/plugins/select2/select2.css">
  <link rel="stylesheet" href="./js/plugins/simplecolorpicker/jquery.simplecolorpicker.css">
  <link rel="stylesheet" href="./js/plugins/timepicker/bootstrap-timepicker.css">
  <link rel="stylesheet" href="./js/plugins/fileupload/bootstrap-fileupload.css">

  <!-- App CSS -->

  <link rel="stylesheet" href="./css/target-admin.css">

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

  <!--[if lt IE 9]>

  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

  <![endif]-->

</head>
<body>

<?php 

	#INCLUDE HEADER

	include_once "includes/navbar.php";

?>
<div class="container">

  <div class="content">

    <div class="content-container">

      <div class="content-header">
        <h2 class="content-header-title">Change Password</h2>
        <ol class="breadcrumb">
          <li><a href="index.php">Home</a></li>
          <li class="active">Change Password</li>
        </ol>
      </div> <!-- /.content-header -->

	  	<?php

		if(isset($_SESSION['message']))

		{

			echo '<div class="alert alert-'.$_SESSION['message_type'].'">

			<a class="close" data-dismiss="alert" href="#" aria-hidden="true">×</a>

			'.$_SESSION['message'].'

			</div>';

		}

	  ?>

      <div class="row">



        <div class="col-sm-6">



          <div class="portlet">



            <div class="portlet-header">



              <h3>

                <i class="fa fa-tasks"></i>

             Chanage Password

              </h3>

            </div> <!-- /.portlet-header -->



            <div class="portlet-content">

              <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">

                <div class="form-group">

                  <label for="name">Current Password</label>

                  <input type="text" id="current_password" maxlength="10" name="current_password" class="form-control" data-required="true" >

                </div>

                <div class="form-group">

                  <label for="name">New Password</label>

                  <input type="text" id="new_password" maxlength="10" name="new_password" class="form-control" data-required="true" >

                </div>

				<div class="form-group">

                  <label for="name">Password Again</label>

                  <input type="text" id="new_password_again" maxlength="10" name="new_password_again" class="form-control" data-required="true" >

                </div>

                <div class="form-group">

                  <button type="submit" class="btn btn-primary">Change Password</button>

                </div>

              </form>

            </div> <!-- /.portlet-content -->



          </div> <!-- /.portlet -->

        </div> <!-- /.col -->

      </div> <!-- /.row -->

    </div> <!-- /.content-container -->

      

  </div> <!-- /.content -->



</div> <!-- /.container -->



<?php 

	include_once "includes/footer.php";

?>



  <script src="./js/libs/jquery-1.10.1.min.js"></script>

  <script src="./js/libs/jquery-ui-1.9.2.custom.min.js"></script>

  <script src="./js/libs/bootstrap.min.js"></script>



  <!--[if lt IE 9]>

  <script src="./js/libs/excanvas.compiled.js"></script>

  <![endif]-->

  

  <!-- Plugin JS -->

  <script src="./js/plugins/parsley/parsley.js"></script>

  <script src="./js/plugins/icheck/jquery.icheck.js"></script>

  <script src="./js/plugins/datepicker/bootstrap-datepicker.js"></script>

  <script src="./js/plugins/timepicker/bootstrap-timepicker.js"></script>

  <script src="./js/plugins/simplecolorpicker/jquery.simplecolorpicker.js"></script>

  <script src="./js/plugins/select2/select2.js"></script>



  <!-- App JS -->

  <script src="./js/target-admin.js"></script>

  <!-- Plugin JS -->

  <script src="./js/demos/form-validation.js"></script>

</body>

</html>