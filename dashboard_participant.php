<?php
	require_once 'includes/project_step_helpers.php';
	$projects =  $db->get_all_user_projects($_SESSION['user_id']);
	$parameters = $db->get_parameters();
  $db->set_access_time($_SESSION['user_id']);
	unset($_SESSION['project']);
if(!isset($_SESSION['project']['Project_Spending']))
	$_SESSION['project']['Project_Spending'] = 0;
	//var_dump($_POST); exit;
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'create_project') {
		array_walk($_POST, 'cleanVar');
		$projectId = $db->add_project($_POST['new_project'], $_SESSION['user_id']);
		if($projectId){
    $step_updated = $db->add_project_step($projectId, 0, 0, 0);

			$_SESSION['project']['steps_completed'] = 0;
			$_SESSION['project']['tasks_completed'] = 0;
			$_SESSION['project']['project_id'] = $projectId;
			$_SESSION['project']['Project_Name'] = $_POST['new_project'];
			$_SESSION['project']['Project_Spending'] = 0;
			$_SESSION['project']['Project_Total_Flowrate'] = 0;
			$_SESSION['project']['Project_License_Cost'] = 0;
			$_SESSION['project']['Project_Budget'] = 0;
			$_SESSION['project']['Project_Projected_Revenue'] = 0;
			
      $_SESSION['project']['Project_year'] = 0;
			$_SESSION['project']['Production_year'] = 0;
			$_SESSION['project']['Actual_Flowrate'] = 0;
			$_SESSION['project']['Cumul_Production'] = 0;
			$_SESSION['project']['Project_Actual_Revenue'] = 0;
      $_SESSION['project']['current_project_year'] = 0;
      $_SESSION['graph'] = array();
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Project Created Successfully';
			header('Location:project_step1.php');
			exit;
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in adding project. Project with same name already exists';
			header('Location:index.php');
			exit;
		}
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'select_project') {
		$_SESSION['project']['project_id'] = $_POST['project_id'];
		$project = $db->get_project($_POST['project_id']);
		
/* 		$_SESSION['project']['steps_completed'] = $project['steps_completed'];
		$_SESSION['project']['tasks_completed'] = $project['tasks_completed'];
		$_SESSION['project']['block_id'] = $project['Project_Block'];
		$_SESSION['project']['Project_Name'] = $project['Project_Name'];
		$_SESSION['project']['Project_Spending'] = $project['Project_Spending'];
		
		$_SESSION['project']['Project_Total_Flowrate'] = $project['Project_Total_Flowrate'];
		$_SESSION['project']['Project_Oil_In_Place'] = $project['Project_Oil_In_Place'];
		$_SESSION['project']['Project_License_Cost'] = $project['Project_License_Cost']; */
		$_SESSION['project'] = $project;
		$_SESSION['project']['project_id'] = $project['Pid'];
    $_SESSION['project']['block_id'] = $project['Project_Block'];
		$_SESSION['project']['Cumul_Production'] = $project['Project_Cumulative_Flow'];
		$_SESSION['project']['production_facility'] = $project['Project_Production_Facility'];
    $_SESSION['project']['current_project_year'] = $project['Project_year'];
		//$_SESSION['project']['Cumul_Production'] = 0;
		header('Location:'.project_get_step_url(project_get_current_step($_SESSION['project'])));
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Project Dashboard — <?php echo APP_NAME; ?></title>

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
<body class="participant-page">
<?php
	include_once "includes/user_navbar.php";
?>

<div class="user-content-wrapper">

  <!-- PAGE HEADER -->
  <div class="md3-page-header" style="margin-bottom:32px;">
    <div>
      <h1 class="md3-page-title">My Projects</h1>
      <p class="md3-page-subtitle">
        Welcome back, <strong><?php echo $_SESSION['username']; ?></strong>
        — select a project to continue your simulation
      </p>
    </div>
  </div>

<?php
	if(isset($_SESSION['message'])) {
		echo '<div class="md3-alert md3-alert--error" style="margin-bottom:20px;">
		<span class="material-symbols-outlined">info</span>
		'.$_SESSION['message'].'
		</div>';
	}
?>
  <!-- SECTION : NOUVEAU PROJET -->
  <div class="user-section user-section--new-project">

    <div class="user-section__header">
      <div class="user-section__title-group">
        <span class="material-symbols-outlined">add_circle</span>
        <div>
          <h2>Start a new project</h2>
          <p>Launch a fresh oilfield simulation from scratch</p>
        </div>
      </div>
    </div>

    <div class="new-project-card">
      <div class="new-project-card__icon">
        <span class="material-symbols-outlined">science</span>
      </div>
      <div class="new-project-card__content">
        <h3>New Project</h3>
        <!--<p>Name your project and begin the full oilfield lifecycle — from budget planning to abandonment.</p>-->
      </div>
      <form class="new-project-card__form" role="form" action="" method="post" id="createForm">
        <div class="md3-field new-project-field">
          <input type="text" id="new_project" name="new_project" required placeholder=" " value="">
          <label for="new_project">Project name</label>
        </div>
        <button id="new_prj" onclick="create_project(this);" type="button" class="md3-btn md3-btn--filled">
          <span class="material-symbols-outlined">rocket_launch</span>
          Start
        </button>
        <input type="hidden" id="form_action1" name="action" value="">
      </form>
    </div>

  </div>
  <!-- SECTION : PROJETS EXISTANTS -->
  <?php if(!empty($projects)): ?>
  <div class="user-section">

    <div class="user-section__header">
      <div class="user-section__title-group">
        <span class="material-symbols-outlined">history</span>
        <div>
          <h2>Continue a project</h2>
          <p>Pick up where you left off</p>
        </div>
      </div>
    </div>

    <div class="md3-table-container">

      <table class="md3-table">
        <thead>
          <tr>
            <th style="width:48px;">#</th>
            <th>Project name</th>
            <th>Budget</th>
            <th>Spending</th>
            <th>License cost</th>
            <th style="width:120px;text-align:center;">Status</th>
            <th style="width:180px;text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; ?>
          <?php foreach($projects as $project): ?>
          <tr>

            <td>
              <span class="proj-index"><?php echo $i; ?></span>
            </td>

            <td>
              <span class="proj-name">
                <?php echo htmlspecialchars($project['Project_Name']); ?>
              </span>
            </td>

            <td>
              <span class="proj-figure">
                <?php echo $project['Project_Budget'] > 0
                    ? '$'.number_format($project['Project_Budget'])
                    : '&mdash;'; ?>
              </span>
            </td>

            <td>
              <span class="proj-figure proj-figure--spending">
                $<?php echo number_format($project['Project_Spending']); ?>
              </span>
            </td>

            <td>
              <span class="proj-figure">
                $<?php echo number_format($project['Project_License_Cost']); ?>
              </span>
            </td>

            <td style="text-align:center;">
              <span class="proj-status-pill">
                <span class="proj-status-dot"></span>
                In progress
              </span>
            </td>

            <td style="text-align:center;">
              <a href="javascript:select_project(<?php echo $project['Pid']; ?>);" class="md3-btn md3-btn--filled">
                <span class="material-symbols-outlined">play_arrow</span>
                Resume
              </a>
            </td>

          </tr>
          <?php $i++; ?>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>

  </div>
  <?php endif; ?>

  

</div> <!-- /.user-content-wrapper -->

  <form class="form-horizontal" role="form" action="" method="post" style="display:none;" id="selectForm">
    <input type="hidden" id="form_action" name="action" value="select_project">
    <input type="hidden" id="project_id" name="project_id" value="">
  </form>

<?php
	unset($_SESSION['message']);
	unset($_SESSION['message_type']);
?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
<script>
function select_project(project_id) {
	document.getElementById('project_id').value = project_id;
	document.getElementById('selectForm').submit();
}

function create_project(btn) {
	if(btn.id == 'new_prj'){
		if(!document.getElementById('new_project').value){
			md3_alert("Please enter project name");
			return false;
		}
		document.getElementById('form_action1').value = "create_project";
	}
	else{
		document.getElementById('form_action1').value = "update_project";
	}
	document.getElementById('createForm').submit();
}
</script>
</body>
</html>
