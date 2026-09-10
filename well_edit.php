<?php
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
		header('Location:index.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] != "POST"){
		header('Location:index.php');
		exit;
	}
	$db = new DB();
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'update_well'){
		array_walk($_POST, 'cleanVar');
		$update = $db->update_well();
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in updaing Well, Entry already exists';
		if($update){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Well updated successfully';
		}
		header('Location:well_list.php');
		exit;
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'delete_well') {
		extract($_POST);
		$delete = $db->delete_well($Wid);
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in deleting Well please try again';
		if($delete)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Well deleted successfully';
		}
		header('Location:well_list.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'edit_well'){
		extract($_POST);
		$well = $db->get_well($Wid);
		
		$Well_Production_Start = date("m/d/Y", strtotime($well['Well_Production_Start']));
		$Well_Production_Stop = date("m/d/Y", strtotime($well['Well_Production_Stop']));
		if(!$Wid)
		{
			header('Location:well_list.php');
			exit;
		}
	}
?>
<?php $active = 'masters'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Edit Well — <?php echo APP_NAME; ?></title>

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
    <h1 class="md3-page-title">Edit Well</h1>
    <p class="md3-page-subtitle">Update well master data</p>
  </div>
</div>

<div class="md3-form-card">
  <div class="md3-form-card__header">
    <span class="material-symbols-outlined">oil_barrel</span>
    <h2>Well Information</h2>
  </div>
  <div class="md3-form-card__body">
    <form id="validate-basic" action="" data-validate="parsley" class="form parsley-form" method="post">

      <div class="md3-field">
        <input type="text" id="Well_Name" name="Well_Name" required placeholder=" " data-required="true" value="<?php echo $well['Well_Name']; ?>">
        <label for="Well_Name">Well Name</label>
      </div>

      <div class="md3-field">
        <input type="text" id="Well_Depth" name="Well_Depth" required placeholder=" " data-required="true" value="<?php echo $well['Well_Depth']; ?>">
        <label for="Well_Depth">Well Depth (M)</label>
      </div>

      <div class="md3-field">
        <input type="text" id="Well_Flowrate" name="Well_Flowrate" required placeholder=" " data-required="true" value="<?php echo $well['Well_Flowrate']; ?>">
        <label for="Well_Flowrate">Well Flow rate (B/Day)</label>
      </div>

      <div class="md3-field">
        <input type="text" id="Well_Production_Start" name="Well_Production_Start" required placeholder=" " class="datepicker" data-required="true" value="<?php echo $Well_Production_Start; ?>">
        <label for="Well_Production_Start">Well Production Start</label>
      </div>

      <div class="md3-field">
        <input type="text" id="Well_Production_Stop" name="Well_Production_Stop" required placeholder=" " class="datepicker" data-required="true" value="<?php echo $Well_Production_Stop; ?>">
        <label for="Well_Production_Stop">Well Production Stop</label>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
          <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Drilling Status</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Drilling_Status" <?php if($well['Well_Drilling_Status'] == 'False') echo 'checked'; ?> value="False" style="accent-color:var(--md-primary);"> False
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Drilling_Status" <?php if($well['Well_Drilling_Status'] == 'True') echo 'checked'; ?> value="True" style="accent-color:var(--md-primary);"> True
            </label>
          </div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well FE Status</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_FE_Status" <?php if($well['Well_FE_Status'] == 'False') echo 'checked'; ?> value="False" style="accent-color:var(--md-primary);"> False
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_FE_Status" <?php if($well['Well_FE_Status'] == 'True') echo 'checked'; ?> value="True" style="accent-color:var(--md-primary);"> True
            </label>
          </div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Casing Cement Status</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Casing_Cement_Status" <?php if($well['Well_Casing_Cement_Status'] == 'False') echo 'checked'; ?> value="False" style="accent-color:var(--md-primary);"> False
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Casing_Cement_Status" <?php if($well['Well_Casing_Cement_Status'] == 'True') echo 'checked'; ?> value="True" style="accent-color:var(--md-primary);"> True
            </label>
          </div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Testing Status</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Testing_Status" <?php if($well['Well_Testing_Status'] == 'False') echo 'checked'; ?> value="False" style="accent-color:var(--md-primary);"> False
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Testing_Status" <?php if($well['Well_Testing_Status'] == 'True') echo 'checked'; ?> value="True" style="accent-color:var(--md-primary);"> True
            </label>
          </div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Completion Status</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Completion_Status" <?php if($well['Well_Completion_Status'] == 'False') echo 'checked'; ?> value="False" style="accent-color:var(--md-primary);"> False
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Completion_Status" <?php if($well['Well_Completion_Status'] == 'True') echo 'checked'; ?> value="True" style="accent-color:var(--md-primary);"> True
            </label>
          </div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Production Status</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Production_Status" <?php if($well['Well_Production_Status'] == 'Closed') echo 'checked'; ?> value="Closed" style="accent-color:var(--md-primary);"> Closed
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Production_Status" <?php if($well['Well_Production_Status'] == 'Open') echo 'checked'; ?> value="Open" style="accent-color:var(--md-primary);"> Open
            </label>
          </div>
        </div>
        <div>
          <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Abandonment Status</label>
          <div style="display:flex;gap:16px;">
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Abandonment_Status" <?php if($well['Well_Abandonment_Status'] == 'False') echo 'checked'; ?> value="False" style="accent-color:var(--md-primary);"> False
            </label>
            <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
              <input type="radio" name="Well_Abandonment_Status" <?php if($well['Well_Abandonment_Status'] == 'True') echo 'checked'; ?> value="True" style="accent-color:var(--md-primary);"> True
            </label>
          </div>
        </div>
      </div>

      <div class="md3-form-card__actions">
        <a href="well_list.php" class="md3-btn md3-btn--outlined">Cancel</a>
        <button type="submit" class="md3-btn md3-btn--filled">
          <span class="material-symbols-outlined">save</span>
          Update
        </button>
      </div>

      <input type="hidden" name="action" value="update_well">
      <input type="hidden" name="Wid" value="<?php echo $well['Wid']; ?>" />

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
</body>
</html>
