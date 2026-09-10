<?php
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant')
	{
		header('Location:index.php');
		exit;
	}
 	$db = new DB();
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'add_well'){
		array_walk($_POST, 'cleanVar');
		//var_dump($_POST);
		
		$added = $db->add_well();
		$_SESSION['message_type'] = 'danger';
		$_SESSION['message'] = 'Error in adding well please try again';
		if($added)
		{
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Well information added successfully';
		}
			header('Location:well_list.php');
			exit;
	}
?>
<?php $active = 'masters'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Add Well — <?php echo APP_NAME; ?></title>

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
  <link href="css/custom.css" rel="stylesheet">
</head>
<body>
<?php include_once "includes/navbar.php"; ?>

  <div class="md3-page-header">
    <div>
      <h1 class="md3-page-title">Add New Well</h1>
      <p class="md3-page-subtitle">Create a new well record</p>
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
          <input type="text" id="Well_Name" name="Well_Name" required placeholder=" ">
          <label for="Well_Name">Well Name</label>
        </div>

        <div class="md3-field">
          <input type="text" id="Well_Depth" name="Well_Depth" required placeholder=" ">
          <label for="Well_Depth">Well Depth (M)</label>
        </div>

        <div class="md3-field">
          <input type="text" id="Well_Flowrate" name="Well_Flowrate" required placeholder=" ">
          <label for="Well_Flowrate">Well Flow rate (B/Day)</label>
        </div>

        <div class="md3-field">
          <input type="text" id="Well_Production_Start" name="Well_Production_Start" required placeholder=" " class="datepicker">
          <label for="Well_Production_Start">Well Production Start</label>
        </div>

        <div class="md3-field">
          <input type="text" id="Well_Production_Stop" name="Well_Production_Stop" required placeholder=" " class="datepicker">
          <label for="Well_Production_Stop">Well Production Stop</label>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
          <div>
            <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Drilling Status</label>
            <div style="display:flex;gap:16px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Drilling_Status" checked value="False" style="accent-color:var(--md-primary);"> False
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Drilling_Status" value="True" style="accent-color:var(--md-primary);"> True
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well FE Status</label>
            <div style="display:flex;gap:16px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_FE_Status" checked value="False" style="accent-color:var(--md-primary);"> False
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_FE_Status" value="True" style="accent-color:var(--md-primary);"> True
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Casing Cement Status</label>
            <div style="display:flex;gap:16px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Casing_Cement_Status" checked value="False" style="accent-color:var(--md-primary);"> False
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Casing_Cement_Status" value="True" style="accent-color:var(--md-primary);"> True
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Testing Status</label>
            <div style="display:flex;gap:16px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Testing_Status" checked value="False" style="accent-color:var(--md-primary);"> False
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Testing_Status" value="True" style="accent-color:var(--md-primary);"> True
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Completion Status</label>
            <div style="display:flex;gap:16px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Completion_Status" checked value="False" style="accent-color:var(--md-primary);"> False
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Completion_Status" value="True" style="accent-color:var(--md-primary);"> True
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Production Status</label>
            <div style="display:flex;gap:16px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Production_Status" checked value="Closed" style="accent-color:var(--md-primary);"> Closed
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Production_Status" value="Open" style="accent-color:var(--md-primary);"> Open
              </label>
            </div>
          </div>
          <div>
            <label style="font-size:13px;font-weight:500;color:var(--md-on-surface-variant);margin-bottom:8px;display:block;">Well Abandonment Status</label>
            <div style="display:flex;gap:16px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Abandonment_Status" checked value="False" style="accent-color:var(--md-primary);"> False
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                <input type="radio" name="Well_Abandonment_Status" value="True" style="accent-color:var(--md-primary);"> True
              </label>
            </div>
          </div>
        </div>

        <div class="md3-form-card__actions">
          <a href="well_list.php" class="md3-btn md3-btn--outlined">Cancel</a>
          <button type="submit" class="md3-btn md3-btn--filled">
            <span class="material-symbols-outlined">save</span>
            Save
          </button>
          <input type="hidden" name="action" value="add_well">
        </div>

      </form>
    </div>
  </div>

<?php include "includes/footer.php"; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
</body>
</html>
