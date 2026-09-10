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
	if(@$_SESSION['project']['steps_completed'] < 7){
		header('Location:project_step7.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Abandon'&& @$_SESSION['project']['tasks_completed'] == 72) {
/* 2.13.1
1.	All well colors are turned to red (Refer to PPT V8 Slide 16)------
2.	Project_Abandonment_Cost =7*Wells_abandonment_Cost-----
3.	Project_Spending= Project_Spending  + Project_Abandonment_Cost------
4.	Display Project_Abandonment_Cost in the top right parameter box------
5.	All 7 Project Well_Abandonment_Status are set to True.
6.	Task81 status set to done--------

 */
		$_SESSION['project']['tasks_completed'] = 81;

		$_SESSION['project']['Project_Abandonment_Cost'] = 7*$field['Field_Abandonment_cost'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_Abandonment_Cost'];
		$update_str = "Project_Spending = ".$_SESSION['project']['Project_Spending']." ,
		Project_Abandonment_Cost = '".$_SESSION['project']['Project_Abandonment_Cost']."',
		Project_Task81_Status = 'Done' , tasks_completed = 81 ";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
    //$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['Project_year'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
		$step_updated = $db->add_project_step($_SESSION['project']['project_id'], 1, $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);

/* 		$pf_str = "Production_Facility_Satus = 'On' ";
		$updated1 = $db->update_production_facility_str($pf_str, $_POST['production_facility']); */

		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Abandon done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Abandon. Please Try Again';
		}
		header('Location:project_step8.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Decommissioning' && @$_SESSION['project']['tasks_completed'] == 81) {
 		$_SESSION['project']['steps_completed'] = 8;
/* Upon executing this task
Production facilities are turned to red ( Refer PPT V8 Slide 17)------

1.	Project_Decomissionning_Cost =500000------

2.	Project_Spendings=Project_Spending+ Project_Decomissionning_Cost------

3.	Display Decomissionning_Cost  and  Project_Spendings

4.	Equipment_decommissioning_Status is set to true.

5.	Task81 status set to done

When all tasks are done, the user logs out.

 */
		$_SESSION['project']['tasks_completed'] = 82;

		$_SESSION['project']['Project_Decomissionning_Cost'] = 500000;

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_Decomissionning_Cost'];

		

		$update_str = "Project_Spending = ".$_SESSION['project']['Project_Spending']." ,

		Project_Decomissionning_Cost = '".$_SESSION['project']['Project_Decomissionning_Cost']."',
		Project_Task82_Status = 'Done', tasks_completed = 82, steps_completed = 8";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['steps_completed'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
    $step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['steps_completed'], $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Decommissioning done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Start Decommissioning. Please Try Again';
		}
		header('Location:project_step8.php');
		exit;
	}
	if(!isset($_SESSION['chartshow']))
		$_SESSION['chartshow'] = 'show';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step 8 ABANDON - <?php echo APP_NAME; ?></title>
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
        <div class="md3-card__header"><h3>Step 8 - ABANDON</h3></div>
        <div class="md3-card__body">
          <form class="form-horizontal" role="form" method="post">
            <div class="form-group">
              <div class="col-md-12">
<?php  
if($_SESSION['project']['tasks_completed'] == 81)
  echo '<img src="img/Picture81.png" style="width:100%;">';
else if($_SESSION['project']['tasks_completed'] >= 82)
  echo '<img src="img/Picture82.png" style="width:100%;">';
else
  echo '<img src="img/Picture71.png" style="width:100%;">';
?>
              </div>
            </div>
            <input type="hidden" id="form_action" name="action" value="" />
            <input type="hidden" id="production_facility" name="production_facility" value="" />
          </form>
        </div>
      </div>
    </div>
    <div class="step-layout__side">
      <div class="side-card">
        <p class="side-card__title">Project summary</p>
      <div class="form-group">
<?php  
if($_SESSION['project']['tasks_completed'] >= 81)
  echo "Project Abandonment Cost: $".number_format((float)$_SESSION['project']['Project_Abandonment_Cost'], 0)." <br/>";

if($_SESSION['project']['tasks_completed'] >= 82){
  echo "Project Decomissionning Cost: $".number_format((float)$_SESSION['project']['Project_Decomissionning_Cost'], 0)." <br/>";
  echo "Project Spending: $".number_format((float)$_SESSION['project']['Project_Spending'], 0)." <br/>";
}		
?>
      </div>
      </div>
      <div class="side-card">
        <p class="task-status-title">Task status</p>
<div class="task-item">
  <div class="task-item__left">
<?php if($_SESSION['project']['tasks_completed'] >= 81): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task81: Abandon</span>
  </div>
  <button <?php if($_SESSION['project']['tasks_completed'] >= 81) echo 'disabled'; ?> onclick="do_task('Abandon');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>
<div class="task-item">
  <div class="task-item__left">
<?php if($_SESSION['project']['tasks_completed'] >= 82): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task82: Decommissioning</span>
  </div>
  <button <?php if($_SESSION['project']['tasks_completed'] >= 82) echo 'disabled'; ?> onclick="do_task('Decommissioning');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
      </div>
    </div>
    </div>
  </div>

  <div class="md3-step-footer">
    <a href="project_step7.php" class="md3-btn md3-btn--outline">Previous Step</a>
    <span class="md3-step-footer__center"><?php echo APP_NAME; ?> &copy; Ver 2.0</span>
    <a href="logout.php" class="md3-btn md3-btn--filled">Logout</a>
  </div>
</div>

<?php include_once "includes/user_footer.php"; ?>
<script src="./js/libs/jquery-1.10.1.min.js"></script>
<script src="./js/libs/jquery-ui-1.9.2.custom.min.js"></script>
<script src="./js/libs/bootstrap.min.js"></script>
<script src="./js/target-admin.js"></script>
<script src="./js/demos/ui-notifications.js"></script>
<script>
function do_task(taskname){
  var message = "Are you sure?";
  if(taskname ==  "Abandon")
    message = "Are you sure? you want to Abandon ?" ;

  if(taskname ==  "Decommissioning")
    message = "Are you sure? you want to do Decommissioning ?" ;

  md3_confirm(message, function() {
    $("#form_action").val(taskname);
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
