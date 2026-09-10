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
		if(@$_SESSION['project']['steps_completed'] < 3){
			header('Location:project_step3.php');
			exit;
		}
		$task41_done = (@$_SESSION['project']['Project_Task41_Status'] === 'Done');
		$task42_done = (@$_SESSION['project']['Project_Task42_Status'] === 'Done');
		$step4_completed = ((int)@$_SESSION['project']['steps_completed'] >= 4);
		$step5_started = ((int)@$_SESSION['project']['tasks_completed'] >= 51);
		$app_well_cost = !empty($_SESSION['project']['Project_ExpWell_Cost'])
			? $_SESSION['project']['Project_ExpWell_Cost']
			: (($field['Field_Drilling_Cost'] + $field['Field_Formation_Eval_cost_Exp'] + $field['Field_Casing_Cement_cost']) * $field['Field_Average_TD']) + $field['Field_Testing_cost'];
		if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'CONSTRUCT APPWELL1' && !$task41_done && !$step5_started && (int)@$_SESSION['project']['tasks_completed'] >= 37) {
	/* 	1.	Project_AppWell_Cost= Project_ExpWell_Cost
		2.	AppWELL1_flowrate is set from selected Block_field_AppWELL1_Flowrate 
		3.	Budget_spending = Budget_spending +  Project_AppWell_Cost
		4.	Displayed Project_AppWell_Cost and AppWELL1_flowrate
		5.	Appwell1_Status is set to done
		6.	Task41_Status set to done
	 */
			$was_step4_completed = $step4_completed;
			$_SESSION['project']['Project_AppWell_Cost'] = $app_well_cost;
			$_SESSION['project']['Project_Task41_Status'] = 'Done';
			$_SESSION['project']['tasks_completed'] = max((int)@$_SESSION['project']['tasks_completed'], 42);
			if(!$was_step4_completed){
				$_SESSION['project']['steps_completed'] = 4;
				$_SESSION['project']['Project_year'] = $db->get_project_year_by_step($_SESSION['project']['steps_completed']);
			}

			$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_AppWell_Cost'];

			$update_parts = array(
				"Project_Spending = '".$_SESSION['project']['Project_Spending']."'",
				"Project_AppWell_Cost = '".$_SESSION['project']['Project_AppWell_Cost']."'",
				"Project_Task41_Status = 'Done'",
				"tasks_completed = ".$_SESSION['project']['tasks_completed']
			);
			if(!$was_step4_completed){
				$update_parts[] = "steps_completed = 4";
				$update_parts[] = "Project_year = ".$_SESSION['project']['Project_year'];
			}
			$update_str = implode(",\n\n\t\t\t", $update_parts);

			$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
			if($updated && !$was_step4_completed){
				$db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['Project_year'], $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
			}

			if($updated){
				$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'CONSTRUCT APPWELL1 done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in CONSTRUCT APPWELL1. Please Try Again';
		}
		header('Location:project_step4.php');
		exit;
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'CONSTRUCT APPWELL2' && !$task42_done && !$step5_started && (int)@$_SESSION['project']['tasks_completed'] >= 37) {
	/* 	1.	Budget_spending = Budget_spending +  Project_AppWell_Cost
		2.	Displayed   AppWELL2_flowrate
		3.	AppWell2_Status is set to true
	4.	Display AppWELL2_Flowrate 
	5.	Project_Oil_In_Place = Field_Reservoir_Volume 
	6.	Project_Recoverable_Volume=Project_Field_Oil_In_place*Field_Recovery_Factor

	7.	Project_Finding_Cost=Budget_Spending/Project_Recoverable_Volume.

	8.	Display Oil_In_Place,  Project_Recoverable_Volume, Project_Finding cost  in the top right parameter box.

	9.	Projected_Revenue= Project_Recoverable_Volume*Oil_Price

	10.	Task42_Status set to done

	 */

			$was_step4_completed = $step4_completed;
			$_SESSION['project']['Project_AppWell_Cost'] = $app_well_cost;
			$_SESSION['project']['Project_Task42_Status'] = 'Done';
			$_SESSION['project']['tasks_completed'] = max((int)@$_SESSION['project']['tasks_completed'], 42);
			if(!$was_step4_completed){
				$_SESSION['project']['steps_completed'] = 4;
				$_SESSION['project']['Project_year'] = $db->get_project_year_by_step($_SESSION['project']['steps_completed']);
			}

			$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_AppWell_Cost'];

		//$_SESSION['project']['Project_Oil_in_Place'] = $field['Field_Reservoir_Volume']* $field['Field_Field_Porosity']*(1- $field['Field_Water_Saturation']);

		$_SESSION['project']['Project_Oil_in_Place'] = $field['Field_Reservoir_Volume'];

			$_SESSION['project']['Project_Recoverable_Volume'] = $_SESSION['project']['Project_Oil_in_Place']*$field['Field_Recovery_Factor'];

			$_SESSION['project']['Project_Finding_Cost'] = $_SESSION['project']['Project_Spending']/$_SESSION['project']['Project_Recoverable_Volume'];

			$_SESSION['project']['Project_Projected_Revenue'] = $_SESSION['project']['Project_Recoverable_Volume']*$parameters['Oil_Price']['value'];
			$update_parts = array(
				"Project_Spending = '".$_SESSION['project']['Project_Spending']."'",
				"Project_AppWell_Cost = '".$_SESSION['project']['Project_AppWell_Cost']."'",
				"Project_Oil_in_Place = '".$_SESSION['project']['Project_Oil_in_Place']."'",
				"Project_Recoverable_Volume = '".$_SESSION['project']['Project_Recoverable_Volume']."'",
				"Project_Finding_Cost = '".$_SESSION['project']['Project_Finding_Cost']."'",
				"Project_Projected_Revenue = '".$_SESSION['project']['Project_Projected_Revenue']."'",
				"Project_Task42_Status = 'Done'",
				"tasks_completed = ".$_SESSION['project']['tasks_completed']
			);
			if(!$was_step4_completed){
				$update_parts[] = "steps_completed = 4";
				$update_parts[] = "Project_year = ".$_SESSION['project']['Project_year'];
			}
			$update_str = implode(",\n\n\t\t\t", $update_parts);

			$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
	      
			if($updated && !$was_step4_completed){
				$db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['Project_year'], $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
			}

			if($updated){

			$_SESSION['message_type'] = 'success';

			$_SESSION['message'] = 'CONSTRUCT APPWELL2 done successfully.';

		}

		else{

			$_SESSION['message_type'] = 'danger';

			$_SESSION['message'] = 'Error in CONSTRUCT APPWELL2. Please Try Again';

		}

		header('Location:project_step4.php');

		exit;

	}

	$Field_AppWell1 = $db->get_well($field['Field_AppWell1']);

	$Field_AppWell2 = $db->get_well($field['Field_AppWell2']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step 4 APPRAISAL - <?php echo APP_NAME; ?></title>
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
        <div class="md3-card__header"><h3>Step 4 - APPRAISAL</h3></div>
        <div class="md3-card__body">
          <form class="form-horizontal" role="form" method="post">
            <img src="img/Picture4.png" style="width:100%;">
            <input type="hidden" id="form_action" name="action" value="" />
          </form>
        </div>
      </div>
    </div>
    <div class="step-layout__side">
	      <div class="side-card">
	        <p class="side-card__title">Project summary</p>
	          <strong>APPRAISAL WELLS CONSTRUCTION</strong><br/>
	          <br/>
	          <?php
	          if($task41_done){
	            echo "Project AppWell Cost: $".number_format((float)$_SESSION['project']['Project_AppWell_Cost'], 0)." <br/><br/>";
	            echo "APPWELL1 Flowrate (B/D): ".$Field_AppWell1['Well_Flowrate']." <br/>";
	          }
	          if($task42_done)
	          {
	            echo "APPWELL2 Flowrate ( B/D): ".$Field_AppWell2['Well_Flowrate']." <br/><br/>";
	            echo "Project Oil in Place: ".$_SESSION['project']['Project_Oil_in_Place']." <br/>";
            echo "Project Recoverable Volume: ".$_SESSION['project']['Project_Recoverable_Volume']." <br/>";
            echo "Project Finding Cost: $".number_format((float)$_SESSION['project']['Project_Finding_Cost'], 2)."/B <br/>";
          }
          ?>
      </div>
      <div class="side-card">
	        <p class="task-status-title">Task status</p>
	          <div class="task-item">
	            <div class="task-item__left">
	            <?php if($task41_done): ?>
	              <span class="task-dot task-dot--done"></span>
	            <?php else: ?>
	              <span class="task-dot task-dot--pending"></span>
	            <?php endif; ?>
	            <span class="task-item__label">Task41: CONSTRUCT APPWELL1</span>
	            </div>
	            <button <?php if($task41_done || $step5_started) echo 'disabled'; ?> onclick="do_task('CONSTRUCT APPWELL1');" type="button" class="task-item__btn">
	              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
	            </button>
	          </div>
	          <div class="task-item">
	            <div class="task-item__left">
	            <?php if($task42_done): ?>
	              <span class="task-dot task-dot--done"></span>
	            <?php else: ?>
	              <span class="task-dot task-dot--pending"></span>
	            <?php endif; ?>
	            <span class="task-item__label">Task42: CONSTRUCT APPWELL2</span>
	            </div>
	            <button <?php if($task42_done || $step5_started) echo 'disabled'; ?> onclick="do_task('CONSTRUCT APPWELL2');" type="button" class="task-item__btn">
	              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
	            </button>
      </div>
    </div>
      <div class="side-card">
        <?php if(@$_SESSION['project']['steps_completed'] < 4): ?>
          <a href="#" class="md3-btn md3-btn--filled" disabled style="width:100%;justify-content:center">Next Step</a>
        <?php else: ?>
          <a href="project_step5.php" class="md3-btn md3-btn--filled" style="width:100%;justify-content:center">Next Step</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="md3-step-footer">
    <a href="project_step3.php" class="md3-btn md3-btn--outline">Previous Step</a>
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
function do_task(taskname){
	var message = "Are you sure?";
if(taskname ==  "CONSTRUCT APPWELL1")
	message = "Are you sure? you want to CONSTRUCT APPWELL1 ?" ;
if(taskname ==  "CONSTRUCT APPWELL2")
	message = "Are you sure? you want to CONSTRUCT APPWELL2 ?" ;
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
