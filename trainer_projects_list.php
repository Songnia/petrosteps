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

?>
 <!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <title>Project List - <?php echo APP_NAME; ?></title>

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

        <h2 class="content-header-title">Project List</h2>

      </div> <!-- /.content-header -->

      <div class="row">

        <div class="col-md-12">

          <h4 class="heading">

           Project List

          </h4>

         <div class="table-responsive">

            <form action="project_edit.php" method="post" name="edit_form">

              <input type="hidden" name="Pid" id="Pid">

              <input type="hidden" id="form_action" name="action" value="">

            </form>

            <?php

              if(isset($_SESSION['message'])) {
                echo '<div class="alert alert-'.$_SESSION['message_type'].'">
                <a class="close" data-dismiss="alert" href="#" aria-hidden="true">×</a>
                '.$_SESSION['message'].'
                </div>';
              }
            ?>
              <div class="col-md-4">
                <div class="form-group">
					<div class="input-group">
						<input class="form-control" id="appendedInput" type="text" onkeyup="loadData(1);">

						<span class="input-group-addon" style="cursor:pointer;" onclick="loadData(1);">

						<i class="fa fa-search"></i> Search</span>

					</div>

                </div>

              </div>

              <input type="hidden" id="order" value="asc">
              <input type="hidden" id="sort" value="Project_Name">
              <div id="ajax_list_div" class="col-md-12">

              </div>

        </div> <!-- /.table-responsive -->

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

  <!-- App JS -->

  <script src="./js/target-admin.js"></script>

 

<script type="text/javascript">

/* function edit(id) {
  document.getElementById('bid').value = id;
  document.getElementById('form_action').value = "edit_block";
  if(!document.getElementById('bid').value)
  {
    alert("Error occured please try again");
    return false;
  }
  document.forms[0].submit();
} */



function delete1(id) {
  document.getElementById('Pid').value = id;
  document.getElementById('form_action').value = "delete_project";
  if(!document.getElementById('Pid').value)
    {
      alert("Error occured please try again");
      return false;
    }
  var con = confirm("Are You Sure ? ");
  if(con)
    document.forms[0].submit();
  return false;
}
$(document).ready(function(){ 
    loadData(1);

});

function changeValue(name, value) {
  if(order == 'asc')
    $('#order').val('desc');
  else
    $('#order').val('asc');
  $("#"+name).val(value);
  loadData(1);
}

function loadData(page){
  searchStr = $('#appendedInput').val();
  order = $('#order').val();
  sort = $('#sort').val();
  $.ajax
  ({
    type: "POST",
    url: "ajax_trainer_projects.php",
    data: "page="+page+"&str="+searchStr+"&sort="+sort+"&order="+order,
    success: function(msg)
    {
      var searchString = searchStr;
      $("#ajax_list_div").html(msg);
      $("#ajax_list_div").ajaxComplete(function(event, request, settings)
    {
      $("#ajax_list_div").html(msg);
    });
    }
  });
}
</script>
</body>
</html>