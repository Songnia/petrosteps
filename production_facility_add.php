<?php
  include('includes/db.class.php');
  if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
    header('Location:index.php');
    exit;
  }
  $db = new DB();
  if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'add_production_facility'){
    array_walk($_POST, 'cleanVar');
    $count = $db->get_production_facility_count('');
    if($count < 3){
      $added = $db->add_production_facility();
      $_SESSION['message_type'] = 'danger';
      $_SESSION['message'] = 'Error in adding production facility please try again';
      if($added)
      {
        $_SESSION['message_type'] = 'success';
        $_SESSION['message'] = 'production facility information added successfully';
      }
    }
    else{
      $_SESSION['message_type'] = 'danger';
      $_SESSION['message'] = 'Can not add more than 3 Production facility';
    }
    header('Location:production_facility_list.php');
    exit;
  }
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Production Facility Add — <?php echo APP_NAME; ?></title>

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
    <h1 class="md3-page-title">Add Production Facility</h1>
    <p class="md3-page-subtitle">Add a new production facility</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">factory</span>
    <h2>Add new Production Facility</h2>
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
        <input type="text" id="Prod_Facilities_Name" name="Prod_Facilities_Name" data-required="true" placeholder=" ">
        <label for="Prod_Facilities_Name">Production Facilities Name</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Prod_Facilities_Capacity" name="Prod_Facilities_Capacity" data-required="true" placeholder=" ">
        <label for="Prod_Facilities_Capacity">Production Facilities Capacity (B/D)</label>
      </div>
      <div class="md3-field">
        <input type="text" id="Prod_Facilities_Cost" name="Prod_Facilities_Cost" data-required="true" placeholder=" ">
        <label for="Prod_Facilities_Cost">Production Facilities Cost (USD)</label>
      </div>
      <div class="form-group">
        <label>Productionuction Facility Status</label>
        <div class="md3-radio-group">
          <label class="md3-radio-item">
            <input type="radio" name="Production_Facility_Satus" data-required="true" value="On"> On
          </label>
          <label class="md3-radio-item">
            <input type="radio" name="Production_Facility_Satus" data-required="true" value="Off"> Off
          </label>
        </div>
      </div>
      <div class="md3-field">
        <input type="text" id="Prod_Facilities_decommissioning_cost" name="Prod_Facilities_decommissioning_cost" data-required="true" placeholder=" ">
        <label for="Prod_Facilities_decommissioning_cost">Production Facilities decommissioning cost</label>
      </div>
      <div class="md3-form-card__actions">
        <button type="submit" class="md3-btn md3-btn--filled">Save</button>
        <input type="hidden" name="action" value="add_production_facility">
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
  <script src="./js/demos/form-validation.js"></script>
</body>
</html>
