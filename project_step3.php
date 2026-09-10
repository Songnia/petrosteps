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
	if(@$_SESSION['project']['steps_completed'] < 2){
		header('Location:project_step2.php');
		exit;
	}
	//Task 31
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'RoadConstruction' && @$_SESSION['project']['tasks_completed'] == 22) {

/* 		1.	Project_Road_Cost = Block_ Field_Road_Cost . 

		2.	Budget_spending = Budget_spending + Project_Road_Cost

		3.	Display Project_Road_Cost in top right Box

		4.	The parameter  Project_Field_Road_Status is set to True.

		5.	Task31_Status set to done

 */

		$_SESSION['project']['tasks_completed'] = 31;

		$_SESSION['project']['Project_road_Cost'] = $field['Field_Road_Cost'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_road_Cost'];

		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', Project_road_Cost = '".$_SESSION['project']['Project_road_Cost']."', Project_Task31_Status = 'Done', Project_Road_Status = 'True' , tasks_completed = 31";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);

		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Road construction done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Road construction. Please Try Again';
		}
		header('Location:project_step3.php');
		exit;
	}

	//Task 32
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Accommodation' && 

	@$_SESSION['project']['tasks_completed'] == 31) {

		

/* 		1.	Project_Accomodation_cost = Project_Block_ Field_Accommodation_Cost 

		2.	Budget_spending = Budget_spending + Project_Accomodation_cost

		3.	Display Project_Accommodation_Cost  in top right  box

		4.	Parameter Project_Accommodation_Status is set to True.

		5.	Task32_Status set to done

 */

		$_SESSION['project']['tasks_completed'] = 32;

		$_SESSION['project']['Project_Accomodation_cost'] = $field['Field_Accommodation_Cost'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_Accomodation_cost'];

		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', Project_Accomodation_cost = '".$_SESSION['project']['Project_Accomodation_cost']."', Project_Task32_Status = 'Done', Project_Accommodation_Status = 'True', tasks_completed = 32 ";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);

		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Accommodation done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Accommodation. Please Try Again';
		}
		header('Location:project_step3.php');
		exit;
	}

	//Task 33

	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Expwell1 Drilling' && @$_SESSION['project']['tasks_completed'] == 32) {

/* 		1.	ExpWell_Drilling_Cost(USD) = Project_ Field_Drilling_cost* Field_Average_TD 

		2.	Budget_spending = Budget_spending + ExpWell_Drilling_Cost

		3.	Display  ExpWell_Drilling_Cost  in the top right parameter box  .

		4.	The Project parameter Expwell1_Drilling_Status is set to true

		5.	Project_Task33_Status set to done

 */

		$_SESSION['project']['tasks_completed'] = 33;

		$_SESSION['project']['Project_ExpWell_Drilling_Cost'] = $field['Field_Drilling_Cost']*$field['Field_Average_TD'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_ExpWell_Drilling_Cost'];

		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', Project_ExpWell_Drilling_Cost = '".$_SESSION['project']['Project_ExpWell_Drilling_Cost']."',Project_Task33_Status = 'Done' , tasks_completed = 33";

		$well_str = "Well_Drilling_Status = 'True' ";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);

		$updated1 = $db->update_well_str($well_str, $field['Field_ExpWell1']);

		if($updated){

			$_SESSION['message_type'] = 'success';

			$_SESSION['message'] = 'Expwell1 Drilling done successfully.';

		}

		else{

			$_SESSION['message_type'] = 'danger';

			$_SESSION['message'] = 'Error in Expwell1 Drilling. Please Try Again';

		}

		header('Location:project_step3.php');
		exit;
	}

	//Task 34
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'ExpWell1 FE' && @$_SESSION['project']['tasks_completed'] == 33) {

/* 		1.	Project_ExpWell_FE_Cost= Field_Formation_Eval_cost_Exp *Field_Average_TD 

		2.	Budget_spending = Budget_spending + Project_ExpWell_FE_Cost

		3.	Display Parameter  ExpFE_Drilling_Cost  in the top right parameter box  .

		4.	Expwell1_Logging_Status set to true

		5.	Task34_Status  set to done

 */

		$_SESSION['project']['tasks_completed'] = 34;

		$_SESSION['project']['Project_ExpWell_FE_Cost'] = $field['Field_Formation_Eval_cost_Exp']*$field['Field_Average_TD'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_ExpWell_FE_Cost'];

		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', 

		Project_ExpWell_FE_Cost = '".$_SESSION['project']['Project_ExpWell_FE_Cost']."', 

		Project_Task34_Status = 'Done', tasks_completed = 34 ";

		$well_str = "Well_FE_Status = 'True' ";

		$updated1 = $db->update_well_str($well_str, $field['Field_ExpWell1']);

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);

		if($updated && $updated1){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'ExpWell1 FE done successfully.';
		}
		else{

			$_SESSION['message_type'] = 'danger';

			$_SESSION['message'] = 'Error in ExpWell1 FE. Please Try Again';

		}

		header('Location:project_step3.php');
		exit;
	}
	//Task 35

	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'ExploWell1 Cement' && @$_SESSION['project']['tasks_completed'] == 34) {


/* 		1.	ExpWell_Casing_Cement_Cost = Project Block Field_Casing_Cement_costt*Field_Average_TD

		2.	Budget_spending = Budget_spending + ExpWell_Casing_Cement_Cost

		3.	Display Parameter   ExpFE_Drilling_Cost  in the top parameter box  

		4.	The parameter Well_Casing_Cement_Status set to true

		5.	Task35_Status  set to done

 */

		$_SESSION['project']['tasks_completed'] = 35;

		$_SESSION['project']['Project_ExpWell_Casing_Cement_Cost'] = $field['Field_Casing_Cement_cost']*$field['Field_Average_TD'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_ExpWell_Casing_Cement_Cost'];

		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', 

		Project_ExpWell_Casing_Cement_Cost = '".$_SESSION['project']['Project_ExpWell_Casing_Cement_Cost']."', 

		Project_Task35_Status = 'Done' , tasks_completed = 35";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);



		$well_str = "Well_Casing_Cement_Status = 'True' ";

		$updated1 = $db->update_well_str($well_str, $field['Field_ExpWell1']);

		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'ExploWell1 Cement done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in ExploWell1 Cement. Please Try Again';
		}
		header('Location:project_step3.php');
		exit;
	}

	//Task 36

	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'ExploWell1 testing' && @$_SESSION['project']['tasks_completed'] == 35) {
/* 		1.	Project_ExpWell_Testing_Cost =Field_Testing_cost 
		2.	Budget_spending = Budget_spending + Project_ExpWell_Testing_Cost
		3.	Display ExpWell1_testing_Cost  in top right parameters Box
		4.	Project_ExpWell_Cost= Project_ExpWell_Drilling_Cost(USD) + Project_ExpWell_FE_Cost + Project_ExpWell_Casing_Cement_Cost+ Project_ExpWeel_Testing_Cost
		5.	Display Project_ExpWell_Testing_Cost.
		6.	Project Block Field _Expwell1_Flowrate is set to the selected Block Field Expwell1_Flowrate and displayed.

		7.	The parameter Expwell1_ Well_Testing_Status set to true.

		8.	Task36_Status set to done

		

		4.	Project_ExpWell_Cost= Project_ExpWell_Drilling_Cost(USD) + 



Project_ExpWell_FE_Cost + Project_ExpWell_Casing_Cement_Cost+ 



Project_ExpWeel_Testing_Cost

5.	Display Project_ExpWeel_Cost.
 */

		$_SESSION['project']['tasks_completed'] = 36;

		$_SESSION['project']['Project_ExpWell_Testing_Cost'] = $field['Field_Testing_cost'];

		$_SESSION['project']['Project_Spending'] += $_SESSION['project']['Project_ExpWell_Testing_Cost'];

		//$_SESSION['project']['Project_Total_Flowrate'] = $ExpWell1['Well_Flowrate'];

		$_SESSION['project']['Project_ExpWell_Cost'] = $_SESSION['project']['Project_ExpWell_Drilling_Cost'] + $_SESSION['project']['Project_ExpWell_FE_Cost'] + $_SESSION['project']['Project_ExpWell_Casing_Cement_Cost']+ $_SESSION['project']['Project_ExpWell_Testing_Cost'];

		

		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', 

		Project_ExpWell_Testing_Cost = '".$_SESSION['project']['Project_ExpWell_Testing_Cost']."', 

		Project_ExpWell_Cost = '".$_SESSION['project']['Project_ExpWell_Cost']."',

		Project_Task36_Status = 'Done', tasks_completed = 36";

		//, Project_Total_Flowrate =  '".$ExpWell1['Well_Flowrate']."'

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);

		$well_str = "Well_Testing_Status = 'True' ";

		$updated1 = $db->update_well_str($well_str, $field['Field_ExpWell1']);
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Explowell1 testing done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Explowell1 testing. Please Try Again';
		}
		header('Location:project_step3.php');
		exit;
	}

	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'CONSTRUCT WELL2' && @$_SESSION['project']['tasks_completed'] == 36){
/* 	1.	Project Block Field _Expwell2_Flowrate is set to the selected Block Field Expwell2_Flowrate and displayed.
	2.	The parameter Expwell2_Logging_Status set to true
	3.	Task37_Status set to done
 */
		$_SESSION['project']['tasks_completed'] = 37;
		$_SESSION['project']['steps_completed'] = 3;
		// Tasks 33 to 36 have already added every exploration-well cost component.
		$project_year = $db->get_project_year_by_step($_SESSION['project']['steps_completed']);
		$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', Project_Task37_Status = 'Done', tasks_completed = 37, steps_completed = 3 , Project_year = ".$project_year;

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['steps_completed'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
      	$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $project_year, $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
		$well_str = "Well_FE_Status = 'True' ";

		$updated1 = $db->update_well_str($well_str, $field['Field_ExpWell2']);

		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Construct EXPWELL2 done successfully.';

		}

		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Construct EXPWELL2. Please Try Again';
		}
		header('Location:project_step3.php');
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step 3 EXPLORATION - <?php echo APP_NAME; ?></title>
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
        <div class="md3-card__header"><h3>Step 3 - EXPLORATION</h3></div>
        <div class="md3-card__body">
          <form class="form-horizontal" role="form" method="post">
            <img src="img/Picture31.jpg" style="width:100%;">
            <input type="hidden" id="form_action" name="action" value="" />
          </form>
        </div>
      </div>
    </div>
    <div class="step-layout__side">
      <div class="side-card">
        <p class="side-card__title">Project summary</p>
          <?php
          if($_SESSION['project']['tasks_completed'] >= 31)
            echo 'Project Road Cost: $'.number_format((float)$_SESSION['project']['Project_road_Cost'], 0).'<br/>';
          if($_SESSION['project']['tasks_completed'] >= 32)
            echo 'Project Accomodation Cost: $'.number_format((float)$_SESSION['project']['Project_Accomodation_cost'], 0).'<br/>';
          if($_SESSION['project']['tasks_completed'] >= 33)
            echo 'Project ExpWell Drilling Cost: $'.number_format((float)$_SESSION['project']['Project_ExpWell_Drilling_Cost'], 0).'<br/>';
          if($_SESSION['project']['tasks_completed'] >= 34)
            echo 'Project ExpWell ExpWell FE Cost: $'.number_format((float)$_SESSION['project']['Project_ExpWell_FE_Cost'], 0).'<br/>';
          if($_SESSION['project']['tasks_completed'] >= 35)
            echo 'Project ExpWell Casing Cement Cost: $'.number_format((float)$_SESSION['project']['Project_ExpWell_Casing_Cement_Cost'], 0).'<br/>';
          if($_SESSION['project']['tasks_completed'] >= 36)
          {
            echo 'Project ExpWell Testing Cost: $'.number_format((float)$_SESSION['project']['Project_ExpWell_Testing_Cost'], 0).'<br/>';
            echo '<br/>Project ExpWell Cost: $'.number_format((float)$_SESSION['project']['Project_ExpWell_Cost'], 0).'<br/>';
          }
          ?>
          <br/>
          <strong>EXPLORATION WELLS CONSTRUCTION</strong><br/>
          <?php
          if($_SESSION['project']['tasks_completed'] >= 36)
            echo "ExploWELL1 Flowrate ( B/D): ".$Field_ExpWell1['Well_Flowrate']." <br/>";
          if($_SESSION['project']['tasks_completed'] >= 37)
            echo "ExploWELL2 Flowrate ( B/D): ".$Field_ExpWell2['Well_Flowrate']." <br/>";
          ?>
      </div>
      <div class="side-card">
        <p class="task-status-title">Task status</p>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 31): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task31: Road construction</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 31) echo 'disabled'; ?> onclick="do_task('RoadConstruction');" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 32): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task32: Site construction</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 32) echo 'disabled'; ?> onclick="do_task('Accommodation');" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 33): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task33: Expwell1 Drilling</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 33) echo 'disabled'; ?> onclick="do_task('Expwell1 Drilling');" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 34): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task34: ExpWell1 FE</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 34) echo 'disabled'; ?> onclick="do_task('ExpWell1 FE');" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 35): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task35: ExploWell1 Cement</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 35) echo 'disabled'; ?> onclick="do_task('ExploWell1 Cement');" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 36): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task36: Explowell1 testing</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 36) echo 'disabled'; ?> onclick="do_task('ExploWell1 testing');" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
          <div class="task-item">
            <div class="task-item__left">
            <?php if(@$_SESSION['project']['tasks_completed'] >= 37): ?>
              <span class="task-dot task-dot--done"></span>
            <?php else: ?>
              <span class="task-dot task-dot--pending"></span>
            <?php endif; ?>
            <span class="task-item__label">Task37: Construct EXPWELL2</span>
            </div>
            <button <?php if(@$_SESSION['project']['tasks_completed'] >= 37) echo 'disabled'; ?> onclick="do_task('CONSTRUCT WELL2');" type="button" class="task-item__btn">
              <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
            </button>
          </div>
      </div>
      <div class="side-card">
        <?php if(@$_SESSION['project']['steps_completed'] < 3): ?>
          <a href="#" class="md3-btn md3-btn--filled" disabled style="width:100%;justify-content:center">Next Step</a>
        <?php else: ?>
          <a href="project_step4.php" class="md3-btn md3-btn--filled" style="width:100%;justify-content:center">Next Step</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="md3-step-footer">
    <a href="project_step2.php" class="md3-btn md3-btn--outline">Previous Step</a>
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
if(taskname ==  "RoadConstruction")
	message = "Are you sure? you want to do Road Construction?" ;
if(taskname ==  "Accommodation")
	message = "Are you sure? you want to do Accommodation?" ;
if(taskname ==  "Expwell1 Drilling")
	message = "Are you sure? you want to do Expwell1 Drilling?" ;
if(taskname ==  "ExpWell1 FE")
	message = "Are you sure? you want to do ExpWell1 FE?" ;
if(taskname ==  "ExploWell1 Cement")
	message = "Are you sure? you want to do ExploWell1 Cement?" ;
if(taskname ==  "ExploWell1 testing")
	message = "Are you sure? you want to do ExploWell1 testing?" ;
if(taskname ==  "CONSTRUCT WELL2")
	message = "Are you sure? you want to CONSTRUCT WELL2?" ;
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
