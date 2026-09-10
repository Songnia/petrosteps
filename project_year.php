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
		$update = $db->update_project_year();
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in updating Parameters, Entry already exists';
		if($update){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Parameters updated successfully';
		}
		header('Location:project_year.php');
		exit;
	}
	$parameters = $db->get_project_years();
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>

  <title>Project Year - <?php echo APP_NAME; ?></title>
  <meta charset="utf-8">
  <meta name="description" content="">

  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">

  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700">

  <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,300,700">

  <link rel="stylesheet" href="./css/font-awesome.min.css">

  <link rel="stylesheet" href="./js/libs/css/ui-lightness/jquery-ui-1.9.2.custom.min.css">

  <link rel="stylesheet" href="./css/bootstrap.min.css">

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
	include_once "includes/navbar.php";
?>
<div class="container">
  <div class="content">

    <div class="content-container">
      <div class="content-header">
        <h2 class="content-header-title">Edit Project Year</h2>
      </div> <!-- /.content-header -->
      <div class="portlet">
        <div class="portlet-header">
          <h3>
            <i class="fa fa-tasks"></i>
           Project Year for step
          </h3>
        </div> <!-- /.portlet-header -->

        <div class="portlet-content">

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
	foreach($parameters as $param){
	echo '<div class="form-group">
              <label class="col-md-4">Step '.$param['step'].'</label>
              <div class="col-md-4">
                <input onkeypress="return isNumber(event)" type="text" maxlength="3" class="form-control" placeholder="" name="'.$param['step_name'].'" data-required="true" value="'.$param['project_year'].'">
              </div>
		    </div>';
	}
?>
			<!--
            <div class="form-group">
              <label class="col-md-4">Budget ($)</label>
              <div class="col-md-6">
                <input type="text" class="form-control" placeholder="" name="Budget" data-required="true">
              </div>

            </div>

            <div class="form-group">

              <label class="col-md-4">Oil_Price (USD/B)</label>

              <div class="col-md-6">

                <input type="text" class="form-control" name="Oil_Price" placeholder="" data-required="true">

              </div>

            </div>-->

  <div class="form-group">
			<div class="col-md-4">
              <button type="submit" class="btn btn-primary">Update</button>

			       <input type="hidden" name="action" value="update_parameters">

            </div>

              <div class="col-md-6">

                &nbsp;

              </div>

            </div>

          </form>

        </div> <!-- /.portlet-content -->

      </div> <!-- /.portlet -->

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

  <script src="./js/plugins/select2/select2.js"></script>
  <!-- App JS -->
  <script src="./js/target-admin.js"></script>
  <!-- Plugin JS -->
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
