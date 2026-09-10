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
		if(@$_SESSION['project']['steps_completed'] < 4){
			header('Location:project_step4.php');
			exit;
		}
		$appraisal_ready = (@$_SESSION['project']['Project_Task41_Status'] === 'Done' || @$_SESSION['project']['Project_Task42_Status'] === 'Done');
		$production_facilitys = $db->get_production_facilitys(0, 10, '', 'Prod_Facilities_Name', '');
		if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'CONSTRUCT DevWell1' && $appraisal_ready && (int)@$_SESSION['project']['tasks_completed'] < 51) {

/* 	1.	Project_DevWell_Cost=(Field_Drilling_cost + Field_Formation_Eval_cost_Dev+ Field_Casing_Cement_cost )* Field_Average_TD  + Field_Testing_cost  
	2.	Project_Spendings= Project_Spendings+ Project_DevWell_Cost.
	3.	Display Project_DevWell_Cost in top right box
	4.	Project Block Field DevWell1_Status is set to true
	5.	Project Block Field DevWell1__flowrate is set from selected Block_Field_DevWell1_Flowrate and Displayed. 
	6.	Task51_Status set to done
 */

		$_SESSION['project']['tasks_completed'] = 51;
		$_SESSION['project']['Project_DevWell_Cost'] = ($field['Field_Drilling_Cost'] + $field['Field_Formation_Eval_cost_Dev']+ $field['Field_Casing_Cement_cost']) *$field['Field_Average_TD']+ $field['Field_Testing_cost'];
		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_DevWell_Cost'];
		$update_str = "Project_Spending = ".$_SESSION['project']['Project_Spending']." ,

		Project_DevWell_Cost = ".$_SESSION['project']['Project_DevWell_Cost']." ,

		Project_Task51_Status = 'Done', tasks_completed = 51";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);

		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'CONSTRUCT DevWell1 done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in CONSTRUCT DevWell1. Please Try Again';
		}
		header('Location:project_step5.php');
		exit;
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'CONSTRUCT DevWell2' && @$_SESSION['project']['tasks_completed'] == 51) {
	/* 	1.	Project_Spendings= Project_Spendings+ Project_DevWell_Cost.
		2.	Project Block Field DevWell2_Status is set to true
		3.	Project Block Field DevWell2__flowrate is set from selected Block_Field_DevWell2_Flowrate and Displayed. 
		4.	Task52_Status set to done
	*/
		$_SESSION['project']['tasks_completed'] = 52;
		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_DevWell_Cost'];
		$update_str = "Project_Spending = ".$_SESSION['project']['Project_Spending']." , Project_Task52_Status = 'Done', tasks_completed = 52";
		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'CONSTRUCT DevWell2 done successfully.';
		}
		else{

			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in CONSTRUCT DevWell2. Please Try Again';
		}
		header('Location:project_step5.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'CONSTRUCT DevWell3' && @$_SESSION['project']['tasks_completed'] == 52) {
/* 		1.	Project_Spendings= Project_Spendings+ Project_DevWell_Cost.

		2.	Project Block Field DevWell3_Status is set to true

		3.	Project Block Field DevWell3__flowrate is set from selected Block_Field_DevWell3_Flowrate and Displayed. 

		4.	Task53_Status set to done

 */

		$_SESSION['project']['tasks_completed'] = 53;

 		$_SESSION['project']['steps_completed'] = 5;

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_DevWell_Cost'];

		$project_year = $db->get_project_year_by_step($_SESSION['project']['steps_completed']);
		$_SESSION['project']['Project_year'] = $project_year;
		$update_str = "Project_Spending = ".$_SESSION['project']['Project_Spending']." , Project_Task53_Status = 'Done' , tasks_completed = 53, steps_completed = 5 , Project_year = ".$project_year;
		
		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		
    	//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['steps_completed'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
      	$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $project_year, $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'CONSTRUCT DevWell3 done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in CONSTRUCT DevWell3. Please Try Again';
		}
			header('Location:project_step5.php');
			exit;
		}
		if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Build Production Facilities' && @$_SESSION['project']['tasks_completed'] == 53) {

	/* 		1.	The production Facility image appears on the screen.  ( Refer to Powerpoint V8 , Slide 11)
			2.	Project_Production_Capacity parameters = User Selected Production Facility parameters 
			3.	Other production facilities are hidden.
			4.	Corresponding Prod_Facilities_Cost is added to Project_Spendings.
			5.	Project Prod_Facilities_Status is set to true
			6.	Task61_Status set to done
	 */

			$_SESSION['project']['tasks_completed'] = 61;
			$production_facility = $db->get_production_facility($_POST['production_facility']);

			$_SESSION['project']['Project_Spending'] += $production_facility['Prod_Facilities_Cost'];
			$_SESSION['project']['production_facility'] = $_POST['production_facility'];

			$update_str = "Project_Spending = ".$_SESSION['project']['Project_Spending']." , Project_Task61_Status = 'Done' , Project_Production_Facility = '".$_POST['production_facility']."', tasks_completed = 61";
			$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
			$pf_str = "Production_Facility_Satus = 'On' ";
			$updated1 = $db->update_production_facility_str($pf_str, $_POST['production_facility']);

			if($updated && $updated1){
				$_SESSION['message_type'] = 'success';
				$_SESSION['message'] = 'Build Production Facilities done successfully.';
			}
			else{
				$_SESSION['message_type'] = 'danger';
				$_SESSION['message'] = 'Error in Build Production Facilities. Please Try Again';
			}
			header('Location:project_step5.php');
			exit;
		}
	/* 	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'CONSTRUCT DevWell4' && @$_SESSION['project']['tasks_completed'] == 53) {

		$_SESSION['project']['tasks_completed'] = 54;
 		$_SESSION['project']['steps_completed'] = 5;
		$_SESSION['project']['Project_Spending'] += ($field['Field_Drilling_Cost'] + $field['Field_Formation_Eval_cost_Dev']+ $field['Field_Casing_Cement_cost']) *$field['Field_Average_TD']+ $field['Field_Testing_cost'];
		$update_str = "Project_Spending = ".$_SESSION['project']['Project_Spending']." , Project_Task54_Status = 'Done', tasks_completed = 54, steps_completed = 5 ";
		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		if($updated){
			$_SESSION['message_type'] = 'success';

			$_SESSION['message'] = 'CONSTRUCT DevWell4 done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in CONSTRUCT DevWell4. Please Try Again';
		}
		header('Location:project_step5.php');
		exit;
	} */
	$Field_DevWell1 = $db->get_well($field['Field_DevWell1']);
	$Field_DevWell2 = $db->get_well($field['Field_DevWell2']);
	$Field_DevWell3 = $db->get_well($field['Field_DevWell3']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step 5 DEVELOPMENT - <?php echo APP_NAME; ?></title>
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
	        <div class="md3-card__header"><h3>Step 5 - DEVELOPMENT</h3></div>
	        <div class="md3-card__body">
	          <form class="form-horizontal" role="form" method="post">
	            <div class="form-group">
	              <div class="col-md-12">
<?php
if($_SESSION['project']['tasks_completed'] >= 53)
{
  echo '<img src="img/Picture61.png" style="max-width:100%;">';
}
else{
  echo '<img src="img/Picture5.jpg" style="width:100%;">';
}
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
<?php
if($_SESSION['project']['tasks_completed'] >= 51)
  echo "Project DevWell Cost: $".number_format((float)$_SESSION['project']['Project_DevWell_Cost'], 0)." <br/><br/>";
?>
<strong>DEVELOPMENT  WELLS CONSTRUCTION</strong><br/>
<?php  
if($_SESSION['project']['tasks_completed'] >= 51)
  echo "DEVWELL1 Flowrate ( B/D): ".$Field_DevWell1['Well_Flowrate']." <br/>";

if($_SESSION['project']['tasks_completed'] >= 52)
  echo "DEVWELL2 Flowrate ( B/D): ".$Field_DevWell2['Well_Flowrate']." <br/>";

if($_SESSION['project']['tasks_completed'] >= 53)
  echo "DEVWELL3 Flowrate ( B/D): ".$Field_DevWell3['Well_Flowrate']." <br/>";
?>
<!--DEVWELL4 Flowrate ( B/D): 423<br/>-->
	      </div>
<?php if($_SESSION['project']['tasks_completed'] >= 53): ?>
	      <div class="side-card">
	        <p class="side-card__title">Production Facilities</p>
<?php
if(@$_SESSION['project']['production_facility']) {
  foreach($production_facilitys as $key => $facility){
    if(@$_SESSION['project']['production_facility'] == $facility['pfid'])
      echo '<div class="radio prod_radio_div" id="prod_faci'.$key.'">
          <label>
            <input name="production_faci" id="facility_" class="parsley-validated" value="'.$facility['pfid'].'" type="radio" checked >
           '.$facility['Prod_Facilities_Capacity'].' B/D - Cost $'.number_format((float)$facility['Prod_Facilities_Cost'], 0).'
          </label>
          </div>';
  }
}
else{
  $i = 0;
  foreach($production_facilitys as $key => $facility){
    echo '<div class="radio prod_radio_div" id="prod_faci'.$key.'">
        <label>
          <input name="production_faci" id="facility_'.$i.'" class="parsley-validated" value="'.$facility['pfid'].'" type="radio" onclick="confirm_selection(\'prod_faci'.$key.'\');">
         '.$facility['Prod_Facilities_Capacity'].' B/D - Cost $'.number_format((float)$facility['Prod_Facilities_Cost'], 0).'
        </label>
        </div>';
    $i++;
  }
}
?>
	      </div>
<?php endif; ?>
	      <div class="side-card">
	        <p class="task-status-title">Task status</p>
<div class="task-item">
  <div class="task-item__left">
<?php if(@$_SESSION['project']['tasks_completed'] >= 51): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task51: CONSTRUCT DevWell1</span>
  </div>
  <button <?php if(@$_SESSION['project']['tasks_completed'] >= 51) echo 'disabled'; ?> onclick="do_task('CONSTRUCT DevWell1');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>

<div class="task-item">
  <div class="task-item__left">
<?php if(@$_SESSION['project']['tasks_completed'] >= 52): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task52: CONSTRUCT DevWell2</span>
  </div>
  <button <?php if(@$_SESSION['project']['tasks_completed'] >= 52) echo 'disabled'; ?> onclick="do_task('CONSTRUCT DevWell2');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>
<div class="task-item">
  <div class="task-item__left">
<?php if(@$_SESSION['project']['tasks_completed'] >= 53): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task53: CONSTRUCT DevWell3</span>
  </div>
  <button <?php if(@$_SESSION['project']['tasks_completed'] >= 53) echo 'disabled'; ?> onclick="do_task('CONSTRUCT DevWell3');" type="button" class="task-item__btn">
	    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
	  </button>
</div>
<?php if($_SESSION['project']['tasks_completed'] >= 53): ?>
<div class="task-item">
  <div class="task-item__left">
<?php if(@$_SESSION['project']['tasks_completed'] >= 61): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task61: Build Production Facilities</span>
  </div>
  <button <?php if(@$_SESSION['project']['tasks_completed'] >= 61) echo 'disabled'; ?> onclick="do_task('Build Production Facilities');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>
<?php endif; ?>
<!--
<div class="task-item">
  <div class="task-item__left">
<?php if(@$_SESSION['project']['tasks_completed'] >= 54): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task54: CONSTRUCT DevWell4</span>
  </div>
  <button <?php if(@$_SESSION['project']['tasks_completed'] >= 54) echo 'disabled'; ?> onclick="do_task('CONSTRUCT DevWell4');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>
-->
        </div>
			<div class="side-card">
	        <?php if(@$_SESSION['project']['tasks_completed'] < 61): ?>
	          <a href="#" class="md3-btn md3-btn--filled" disabled style="width:100%;justify-content:center">Next Step</a>
	        <?php else: ?>
	          <a href="project_step6.php" class="md3-btn md3-btn--filled" style="width:100%;justify-content:center">Next Step</a>
        <?php endif; ?>
</div>
      </div>
      
    </div>

  <div class="md3-step-footer">
    <a href="project_step4.php" class="md3-btn md3-btn--outline">Previous Step</a>
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
  if(taskname ==  "CONSTRUCT DevWell1")
    message = "Are you sure? you want to CONSTRUCT DevWell1 ?" ;

  if(taskname ==  "CONSTRUCT DevWell2")
    message = "Are you sure? you want to CONSTRUCT DevWell2 ?" ;

	  if(taskname ==  "CONSTRUCT DevWell3")
	    message = "Are you sure? you want to CONSTRUCT DevWell3 ?" ;

	  if(taskname == 'Build Production Facilities') {
	    message = "Are you sure? you want to Build Production Facilities?";
	    if(document.getElementById('facility_0') && (document.getElementById('facility_0').checked || document.getElementById('facility_1').checked || document.getElementById('facility_2').checked)){
	      if ($('#facility_0').is(":checked")) {
	        $('#production_facility').val($('#facility_0').val());
	      }
	      if ($('#facility_1').is(":checked")) {
	        $('#production_facility').val($('#facility_1').val());
	      }
	      if ($('#facility_2').is(":checked")) {
	        $('#production_facility').val($('#facility_2').val());
	      }
	    }
	    else if(!document.getElementById('facility_0') && !document.getElementById('facility_1') && !document.getElementById('facility_2')) {
	      message = "Build Production Facilities is already configured.";
	    }
	    else{
	      md3_alert('Please Select Production Facility');
	      return false;
	    }
	  }

	  md3_confirm(message, function() {
	    $("#form_action").val(taskname);
    document.forms[0].submit();
	  });
	  return false;
	}

	function confirm_selection(sele_id)
	{
	  md3_confirm("Are you sure? you want to select this production facility? ", function() {
	    $(".prod_radio_div").each(function(){
	      if(sele_id != this.id)
	        $(this).hide();
	    });
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
