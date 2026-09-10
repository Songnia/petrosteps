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
	if(@$_SESSION['project']['steps_completed'] < 6){
		header('Location:project_step6.php');
		exit;
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Assessment' && @$_SESSION['project']['tasks_completed'] == 62) {

/* 	1.	Upon executing this task the following message appears. "FORMATION PRESSURE HAS DECREASED – CHECK WITH TRAINER FOR ASSESSMENT"
	2.	Task71 status set to done
 */
		$_SESSION['project']['tasks_completed'] = 71;
		$update_str = " Project_Task71_Status = 'Done', tasks_completed = 71 ";
		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Assessment done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Assessment. Please Try Again';

		}
		header('Location:project_step7.php');
		exit;
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Remedial Work' && @$_SESSION['project']['tasks_completed'] == 71) {

/* 		Upon executing this task 

		1.	Actual_Flowrate =Actual_Flowrate*2------Done.	//Project_Total_Flowrate
		Then Every one minute the following happens.

		2.	Production_year=Production_year+1------Done.

		3.	Cumul_Production = Cumul_Production+ Actual_flowrate*365------Done.
		Until the following happens:
		4.	Prod_Year = 20  or Participant moves to Step7.------Done.
		5.	If any of the above happens Actual_Flowrate =0 and displayed.------Done.
		6.	Task71 status set to done ------Done.
		Upon executing this task 
1.	Actual_Flowrate =Actual_Flowrate*1.5
2.	Task72 status set to done
*/
		$_SESSION['project']['Project_Total_Flowrate']*= 1.5;	

        // Handle Remedial Work cost
        $remedial_cost = isset($_POST['remedial_cost']) ? (float)$_POST['remedial_cost'] : 0;
        $remedial_type = isset($_POST['remedial_type']) ? $_POST['remedial_type'] : '';
        $_SESSION['project']['Project_Spending'] += $remedial_cost; // Add to spending
        $_SESSION['project']['remedial_work_type'] = $remedial_type;
        $_SESSION['project']['remedial_work_cost'] = $remedial_cost;

		$_SESSION['project']['tasks_completed'] = 72;

 		$_SESSION['project']['steps_completed'] = 7;

		$update_str = "Project_Total_Flowrate = '".$_SESSION['project']['Project_Total_Flowrate']."', Project_Spending = '".$_SESSION['project']['Project_Spending']."',  Project_Task72_Status = 'Done' , tasks_completed = 72, steps_completed = 7 ";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['Project_year'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
    //$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['Project_year'], $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
    //$step_updated = $db->add_project_step($_SESSION['project']['project_id'], 1, $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Remedial Work done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Remedial Work. Please Try Again';
		}
		header('Location:project_step7.php');
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step 7 RECOVERY - <?php echo APP_NAME; ?></title>
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
        <div class="md3-card__header"><h3>Step 7 - RECOVERY</h3></div>
        <div class="md3-card__body">
          <form class="form-horizontal" role="form" method="post">
            <div class="form-group">
              <div class="col-md-12">
                <img src="img/Picture71.png" style="width:100%;">
              </div>
            </div>
            <input type="hidden" id="form_action" name="action" value="" />
            <input type="hidden" id="production_facility" name="production_facility" value="" />
            <input type="hidden" id="remedial_cost_input" name="remedial_cost" value="" />
            <input type="hidden" id="remedial_type_input" name="remedial_type" value="" />
          </form>
        </div>
      </div>
    </div>
    <div class="step-layout__side">
      <div class="side-card">
        <p class="side-card__title">Project summary</p>
    <div class="form-group">
<?php
if($_SESSION['project']['tasks_completed'] == 71)
{
?>
    <strong>
      <span style="color:red;">
      FORMATION PRESSURE HAS DECREASED – CHECK WITH TRAINER 

FOR ASSESSMENT
    </span>
    </strong>
    <div style="margin-top: 20px;">
        <p class="side-card__title" style="margin-bottom: 12px; font-size: 11px; color: var(--md-primary);">REMEDIAL WORK OPTIONS</p>
        <div class="radio" style="margin-bottom: 8px;">
            <label style="font-size: 14px; font-weight: 500;"><input type="radio" name="remedial_cost_radio" value="10000000" checked> Basic Remedial Work ($10,000,000)</label>
        </div>
        <div class="radio">
            <label style="font-size: 14px; font-weight: 500;"><input type="radio" name="remedial_cost_radio" value="25000000"> Advanced Remedial Work ($25,000,000)</label>
        </div>
    </div>
<?php
} elseif($_SESSION['project']['tasks_completed'] >= 72 && isset($_SESSION['project']['remedial_work_type'])) {
?>
    <div style="margin-top: 10px;">
        <p class="side-card__title" style="margin-bottom: 10px;">REMEDIAL WORK PERFORMED</p>
        <div class="side-info-row">
            <span class="side-info-row__label">Operation</span>
            <span class="side-info-row__value side-info-row__value--text"><?php echo $_SESSION['project']['remedial_work_type']; ?></span>
        </div>
        <div class="side-info-row">
            <span class="side-info-row__label">Cost Incurred</span>
            <span class="side-info-row__value side-info-row__value--text">$<?php echo number_format((float)$_SESSION['project']['remedial_work_cost'], 0); ?></span>
        </div>
    </div>
<?php
}
?>
    </div>
      </div>
      <div class="side-card">
        <p class="task-status-title">Task status</p>
<div class="task-item">
  <div class="task-item__left">
<?php if($_SESSION['project']['tasks_completed'] >= 71): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task71: Assessment</span>
  </div>
  <button <?php if($_SESSION['project']['tasks_completed'] >= 71) echo 'disabled'; ?> onclick="do_task('Assessment');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>
<div class="task-item">
  <div class="task-item__left">
<?php if($_SESSION['project']['tasks_completed'] >= 72): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task72: Remedial Work</span>
  </div>
  <button <?php if($_SESSION['project']['tasks_completed'] >= 72) echo 'disabled'; ?> onclick="do_task('Remedial Work');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>
<?php if($_SESSION['project']['tasks_completed'] >= 72): ?>
<div class="task-item">
  <div class="task-item__left">
<?php
  $prod_year = isset($_SESSION['project']['Production_year']) ? (int)$_SESSION['project']['Production_year'] : 0;
  $proj_year = isset($_SESSION['project']['Project_year']) ? (int)$_SESSION['project']['Project_year'] : 0;
  if ($prod_year >= 100): ?>
  <span class="task-dot task-dot--done"></span>
  <?php elseif ($prod_year > 0): ?>
  <span class="task-dot task-dot--pending" style="background-color:#f0ad4e;"></span>
  <?php else: ?>
  <span class="task-dot task-dot--pending" style="background-color:#777;"></span>
  <?php endif; ?>
  <span class="task-item__label">Task73: Increment production year</span>
  </div>
  <button id="inc_button" <?php if($prod_year >= 100) echo 'disabled'; ?> onclick="increment_count();" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>

<!-- Espace sous le bouton pour afficher les années de production -->
<div class="production-year-card" style="margin-top: 12px; padding: 14px 16px; background: rgba(103, 183, 220, 0.08); border: 1px solid rgba(103, 183, 220, 0.25); border-radius: 12px;">
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--md-primary, #1e88e5);">Années de Production</span>
    <!--<span class="md3-badge md3-badge--accent" style="font-weight: 700;">Année <?php echo $prod_year; ?></span>-->
  </div>
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 12px; font-size: 13px;">
    <div>
      <span style="color: var(--md-on-surface-variant, #8e8e93); font-size: 11px; display: block;">Production Year</span>
      <strong id="PROD_YEAR_DISPLAY" style="font-size: 16px; color: var(--md-on-surface, #1c1b1f);"><?php echo $prod_year; ?> ans</strong>
    </div>
    <div>
      <span style="color: var(--md-on-surface-variant, #8e8e93); font-size: 11px; display: block;">Project Year</span>
      <strong id="PROJ_YEAR_DISPLAY" style="font-size: 16px; color: var(--md-on-surface, #1c1b1f);"><?php echo $proj_year; ?> ans</strong>
    </div>
  </div>
</div>
<?php endif; ?>
        </div>
        <div class="side-card">
        <?php if(@$_SESSION['project']['steps_completed'] < 7): ?>
          <a href="#" class="md3-btn md3-btn--filled" disabled style="width:100%;justify-content:center">Next Step</a>
        <?php else: ?>
          <a href="project_step8.php" class="md3-btn md3-btn--filled" style="width:100%;justify-content:center">Next Step</a>
        <?php endif; ?>
      </div>
      </div>
      
    </div>

  <div class="md3-step-footer">
    <a href="project_step6.php" class="md3-btn md3-btn--outline">Previous Step</a>
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
  if(taskname ==  "Assessment")
    message = "Are you sure? you want to do Assessment ?" ;

  if(taskname ==  "Remedial Work") {
    var selected_radio = $("input[name='remedial_cost_radio']:checked");
    if(selected_radio.length == 0) {
       alert('Please select a Remedial Work option first.');
       return false;
    }
    $("#remedial_cost_input").val(selected_radio.val());
    var text_label = selected_radio.parent().text().trim();
    $("#remedial_type_input").val(text_label);
    message = "Are you sure? you want to do " + text_label + " ?" ;
  }

  md3_confirm(message, function() {
    $("#form_action").val(taskname);
    document.forms[0].submit();
  });
  return false;
}

function increment_count()
{
  $.ajax
  ({
    type: "POST",
    url: "ajax_calculation.php",

    data: "action=increment2",

    success: function(msg)
    {
      //alert(msg);
      var res = msg.split("##");
      if(res[0] >= 100) {
        md3_alert('You can not increment count more than 100.');
        $("#inc_button").attr('disabled', 'disabled');
      }
      else{
        window.location.reload();
      }
      $("#PRODUCTION_YEAR").html(res[0]);
      $("#PROD_YEAR_DISPLAY").html(res[0] + " ans");
      $("#CUMUL_PROD").html(res[1]);
      $("#ACTUAL_REVENUE").html(res[2]);
      $("#ACTUAL_FLOWRATE").html(res[3]);
      $("#CASH_FLOW").html(res[4]);
      $("#CUMULATIVE_CASH_FLOW").html(res[5]);
    }
  });	
}
</script>
<?php include_once "includes/graphscript.php"; ?>
</body>
</html>
<?php
  unset($_SESSION['message']);
  unset($_SESSION['message_type']);
?>
