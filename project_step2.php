<?php
	include('includes/db.class.php');
	include('includes/user_common.php');

	if(!isset($_SESSION['user_id'])){
		header('Location:login.php');
		exit;
	}
	$session_id = $db->get_active_session($_SESSION['user_id']);
	if($_SESSION['session_id'] !== $session_id){
		header('Location:login.php');
		exit;
	}
	$db->set_access_time($_SESSION['user_id']);
	if(@$_SESSION['project']['steps_completed'] < 1){
		header('Location:project_step1.php');
		exit;
	}
	//Task 21
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'dataAcquisition' && @$_SESSION['project']['tasks_completed'] == 11 ){

/* 		1.	Project_Survey_Cost =Block_Surface *Block_Survey_cost   

		2.	Budget_spending = Budget_spending + Project_Survey_Cost .

		3.	Display Project_Survey_Cost  in top right box

		4.	The Project Block_Survey_Status   is set to True.

		5.	Project_Task21_Status set to done. */

		$_SESSION['project']['tasks_completed'] = 21;

		$_SESSION['project']['Project_Survey_Cost'] = $block['Block_Surface']*$block['Block_Survey_Cost'];

		//$_SESSION['project']['Project_Budget'] = $_SESSION['project']['Project_Budget'] + $_SESSION['project']['Project_Survey_Cost'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_Survey_Cost'];

		

		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', Project_Task21_Status = 'Done', Project_Survey_Cost = ".$_SESSION['project']['Project_Survey_Cost'].",  tasks_completed = 21 ";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);

		

		$block_str = "Block_Survey_Status = 'True' ";

		$updated1 = $db->update_block_str($block_str, $_SESSION['project']['block_id']);

		if($updated && $updated1){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Survey Data Acquisition done successfully';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Survey Data Acquisition. Please Try Again';
		}
		header('Location:project_step2.php');
		exit;
	}

	//Task 22
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'dataInterpretation' && @$_SESSION['project']['tasks_completed'] == 21 ) {
/* 		1.	Project_Interpretation_Cost = Block_Surface * Block_Survey_Interpretation_cost 
		2.	Budget_spending = Budget_spending + Project_Interpretation_Cost.
		3.	Display Project_Interpretation_Cost in top right box
		4.	Block_Survey_interpretation_Status changed to Yes
		5.	Task22 Status set to done.
 */
		$_SESSION['project']['tasks_completed'] = 22;

		$_SESSION['project']['steps_completed'] = 2;

		$Project_Interpretation_cost = $block['Block_Surface']*$block['Block_Survey_interpretation_cost'];

		$_SESSION['project']['Project_Interpretation_cost'] = $Project_Interpretation_cost;

		$_SESSION['project']['Project_Spending'] += $Project_Interpretation_cost;
    $project_year = $db->get_project_year_by_step($_SESSION['project']['steps_completed']);
		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', Project_Task22_Status = 'Done', Project_Interpretation_cost = $Project_Interpretation_cost, tasks_completed = 22, steps_completed = 2, Project_year = ".$project_year;

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
    
		//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['steps_completed'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
      $step_updated = $db->add_project_step($_SESSION['project']['project_id'], $project_year, $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);

		$block_str = "Block_Survey_Interpretation_Status = 'True' ";

		$updated1 = $db->update_block_str($block_str, $_SESSION['project']['block_id']);
		if($updated && $updated1){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Survey Data Interpretation. Done Succeessfully.';
		}

		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Survey Data Interpretation. Please Try Again';
		}
		header('Location:project_step2.php');
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step 2 SURVEY - <?php echo APP_NAME; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/md3-theme.css" rel="stylesheet">
  <link href="css/target-admin.css" rel="stylesheet">
<script src="js/graph/amcharts.js"></script>
<script src="js/graph/serial.js"></script>
<script src="js/graph/light.js"></script>
<style>
.md3-step-tabs{gap:1px;padding:20px 0;margin-bottom:32px}
.md3-step-tab{gap:8px;padding:18px 28px;height:80px;font-size:15px;font-weight:700;border-width:2px;border-radius:0;background:#67b7dc1f;border-color:rgba(103,183,220,0.35)}
.md3-step-tab:hover{border-color:#C1E977;color:#C1E977;background:rgba(193,233,119,0.12)}
.md3-step-tab.active{background:#C1E977;color:#1a2a1a;border-color:#C1E977;box-shadow:none}
.md3-step-tab.active .md3-step-tab__num{background:rgba(0,0,0,0.15);color:#1a2a1a}
.md3-step-tab__num{width:30px;height:30px;font-size:14px}
.md3-step-tab__label{font-size:15px;font-weight:600}
.md3-step-tab .material-symbols-outlined{font-size:20px}
.param_tab{color:#FEB76A!important;border-color:rgba(254,183,106,0.35)!important;background:rgba(254,183,106,0.1)!important}
.param_tab:hover{color:#FEB76A!important;border-color:#FEB76A!important;background:rgba(254,183,106,0.1)!important;border-style:solid!important}
.side-card__title,.task-status-title{font-size:13px}
.side-info-row__label{font-size:13px}
.side-info-row__value{font-size:24px}
.side-info-row__value--text{font-size:20px}
.task-item__label{font-size:17px;font-weight:600}
</style>
</head>
<body>
<?php include_once "includes/user_navbar.php"; ?>

<div class="md3-step-container">
  <?php include "includes/project_nav.php"; ?>

  <div class="step-layout">
    <div class="step-layout__main">
      <div class="md3-card">
        <div class="md3-card__header"><h3>Step 2 - SURVEY</h3></div>
        <div class="md3-card__body">
          <form class="form-horizontal" role="form" method="post">
            <img src="img/Picture2.png" style="width: 100%;"><br>
            <div class="clear"></div>
            <img src="img/Picture3.jpg" style="width: 100%;"><br>
            <input type="hidden" id="form_action" name="action" value="" />
          </form>
        </div>
      </div>
    </div>
    <div class="step-layout__side">
      <div class="side-card">
        <p class="side-card__title">Project summary
          <?php
          if($_SESSION['project']['tasks_completed'] >= 21)
            echo 'Project Survey Cost: $'.number_format((float)$_SESSION['project']['Project_Survey_Cost'], 0).'<br/>';
          if($_SESSION['project']['tasks_completed'] >= 22)
            echo '<br/>Project Interpretation Cost: $'.number_format((float)$_SESSION['project']['Project_Interpretation_cost'], 0).'<br/>';
          ?>
      </div>
      <div class="side-card">
        <p class="task-status-title">Task status
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 21): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task21: Survey Data Acquisition</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 21) echo 'disabled'; ?> onclick="dataAcquisition();" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 22): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task22: Survey Data Interpretation</span>
            </div>
            <button id="new_prj" <?php if(@$_SESSION['project']['tasks_completed'] >= 22) echo 'disabled'; ?> onclick="dataInterpretation();" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
      </div>
    </div>
      <div class="side-card">
        <?php if(@$_SESSION['project']['steps_completed'] < 2): ?>
          <a href="#" class="md3-btn md3-btn--filled" disabled style="width:100%;justify-content:center">Next Step</a>
        <?php else: ?>
          <a href="project_step3.php" class="md3-btn md3-btn--filled" style="width:100%;justify-content:center">Next Step</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="md3-step-footer">
    <a href="project_step1.php" class="md3-btn md3-btn--outline">Previous Step</a>
    <span class="md3-step-footer__center"><?php echo APP_NAME; ?> &copy; Ver 2.0</span>
  </div>
</div>

<?php include_once "includes/user_footer.php"; ?>
<script src="./js/libs/jquery-1.10.1.min.js"></script>
<script src="./js/libs/jquery-ui-1.9.2.custom.min.js"></script>
<script src="./js/libs/bootstrap.min.js"></script>
<script src="./js/target-admin.js"></script>
<script src="./js/demos/ui-notifications.js"></script>
<script>
function dataAcquisition(){
  md3_confirm("Are you sure? You want to do Survey Data Acquisition?", function() {
    $("#form_action").val("dataAcquisition");
    document.forms[0].submit();
  });
  return false;
}
function dataInterpretation(){
  md3_confirm("Are you sure? You want to do Survey Data Interpretation ?", function() {
    $("#form_action").val("dataInterpretation");
    document.forms[0].submit();
  });
  return false;
}
</script>
<?php include_once "includes/graphscript.php"; ?>
</body>
</html>
<?php
	unset($_SESSION['message']);
	unset($_SESSION['message_type']);
?>
