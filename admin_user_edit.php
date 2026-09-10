<?php
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] != 'SuperAdmin') {
		header('Location:index.php');
		exit;
	}
	if( $_SERVER['REQUEST_METHOD'] != "POST"){
		header('Location:index.php');
		exit;
	}
	$db = new DB();

	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update_user') {
		array_walk($_POST, 'cleanVar');
		extract($_POST);
		$update = $db->update_user($user_id, $username, $email, $firstname);
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in updaing Admin, Entry already exists';
		if($update)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Admin updated successfully';
		}
		header('Location:admin_user_list.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete_user') {
		extract($_POST);
		$delete = $db->delete_user($user_id);
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in deleting Admin please try again';
		if($delete)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Admin user deleted successfully';
		}
		header('Location:admin_user_list.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'edit_user'){
		extract($_POST);
		$user = $db->get_user($user_id);
		if(!$user_id)
		{
			header('Location:admin_user_list.php');
			exit;
		}
	}
 ?>
 <!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <title>Edit Admin - <?php echo APP_NAME; ?></title>
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
  <link rel="stylesheet" href="./css/custom.css">
  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<?php
  include_once "includes/navbar.php";
?>
<div class="container">

  <div class="content">

    <div class="content-container">
      <div class="content-header">
        <h2 class="content-header-title">Edit Admin User</h2>
      </div> <!-- /.content-header -->
      <div class="row">
        <div class="col-sm-6">

          <div class="portlet">
            <div class="portlet-header">

              <h3>
                <i class="fa fa-tasks"></i>
                Edit Admin
              </h3>
            </div> <!-- /.portlet-header -->

            <div class="portlet-content">
              <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" id="name" name="firstname" class="form-control" data-required="true" value="<?php echo $user['firstname'];?>" >
                </div>
                <div class="form-group">
                  <label for="name">Email Id</label>
                  <input type="text" id="name" name="email" class="form-control" data-required="true" value="<?php echo $user['email'];?>" >
                </div>
                <div class="form-group">
                  <label for="name">User Name</label>
                  <input type="text" id="name" name="username" class="form-control" data-required="true" value="<?php echo $user['username'];?>" >
                </div>
               <!-- <div class="form-group">
                  <label for="name">Password</label>
                  <input type="password" id="name" name="pwd" class="form-control" data-required="true" >
                </div>
                <div class="form-group">
                  <label for="name">Confirm Password</label>
                  <input type="password" id="name" name="confirm_pwd" class="form-control" data-required="true" >
                </div>-->
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Update User</button>
			<input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
				<input type="hidden" name="action" value="update_user">
                </div>
              </form>
            </div> <!-- /.portlet-content -->
          </div> <!-- /.portlet -->
          
        </div> <!-- /.col -->

      </div> <!-- /.row -->

    </div> <!-- /.content-container -->
      
  </div> <!-- /.content -->
</div> <!-- /.container -->
<?php include "includes/footer.php"; ?>

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