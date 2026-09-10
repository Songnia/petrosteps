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
	if(!isset($_SESSION['chartshow']))
		$_SESSION['chartshow'] = 'hide';
	$db->set_access_time($_SESSION['user_id']);
	if(!isset($_SESSION['project']['steps_completed'])){
		header('Location:index.php');
		exit;
	}
	//Task 11
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'acquire_license' && @$_SESSION['project']['tasks_completed'] == 0) {
		if($_POST['block']){
			$_SESSION['project']['block_id'] = $_POST['block'];
			$_SESSION['project']['steps_completed'] = 1;
			$_SESSION['project']['tasks_completed'] = 11;
			$block = $db->get_block($_POST['block']);
		//2-	Project_Spending=0
		//3-	Project_License_Cost=Block_Surface*Block_License_Cost_Exp +Block_ Signature_Bonus
		//4-	Project_spending= Project_spending + Project_License_Cost .
		//5-	Display Project_License_Cost in top right box
			$Project_License_Cost = $block['Block_Surface']*$block['Block_License_Cost_Exp']+$block['Signature_Bonus'];

			$_SESSION['project']['Project_Spending'] = $Project_License_Cost;
			$_SESSION['project']['Project_License_Cost'] = $Project_License_Cost;
			$project_year = $db->get_project_year_by_step($_SESSION['project']['steps_completed']);
			$update_str = "Project_Spending = '".$_SESSION['project']['Project_Spending']."', Project_Block = '".$_POST['block']."', Project_Task11_Status = 'Done', Project_License_Cost = $Project_License_Cost, steps_completed = 1,  tasks_completed = 11, Project_year =  ".$project_year;
			$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
			
			//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['steps_completed'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
			$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $project_year, $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
			$_SESSION['graph']['project_id'][] = $_SESSION['project']['project_id'];
			$_SESSION['graph']['Cumul_Production'][] = $_SESSION['project']['Cumul_Production'];
			$_SESSION['graph']['Project_Spending'][] = $_SESSION['project']['Project_Spending'];
			$update_block = " Block_License_Status = 'True' ";
			$updated1 = $db->update_block_str($update_block, $_POST['block']);
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Acquire License done succeessfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in acquiring license. Please Try Again.';
		}
		header('Location:project_step1.php');
		exit;
	}
	//$_SESSION['chartshow']
	//var_dump($_SESSION['chartshow']);
	$blocks = $db->get_blocks(0, 10, '', 'Block_Name', '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step1 LICENSE - <?php echo APP_NAME; ?></title>
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
        <div class="md3-card__header"><h3>Step 1 - LICENSE</h3></div>
        <div class="md3-card__body">
          <form class="form-horizontal" role="form" method="post">
			<?php
				if(isset($_SESSION['project']['block_id']) && $_SESSION['project']['block_id'] != ''){
					foreach($blocks as $block1){
						if(@$_SESSION['project']['block_id'] == $block1['bid'])
							echo '<div class="md3-field"><label>Selected Block: <strong>'.$block1['Block_Name'].'</strong></label></div>';
					}
				}
				else{
			?>
            <div class="md3-field">
              <select name="block" id="block" onchange="get_block_params(this.value)">
				<option value="">Select Block</option>
				<?php
				foreach($blocks as $block1){
					if(@$_SESSION['project']['block_id'] == $block1['bid'])
						echo '<option selected value="'.$block1['bid'].'">'.$block1['Block_Name'].'</option>';
					else
						echo '<option value="'.$block1['bid'].'">'.$block1['Block_Name'].'</option>';
				}
				?>
              </select>
            </div>
			<?php } ?>

			<div id="block_details">
			<?php
			if(@$_SESSION['project']['block_id']) {
				$block = $db->get_block($_SESSION['project']['block_id']);
			?>
			<div style="background:#F5F5F5; border-radius:8px; padding:16px; margin-top:16px;">
				<h5><strong>Block Name: <?php echo $block['Block_Name']; ?></strong></h5>
				<h5><strong>Block Surface (Ha): <?php echo $block['Block_Surface']; ?></strong></h5>
				<h5><strong>Block Description: <?php echo $block['Block_Description']; ?></strong></h5>
				<h5><strong>Block License Cost Exp (USD/Ha): <?php echo $block['Block_License_Cost_Exp']; ?></strong></h5>
				<h5><strong>Block License cost Prod (USD/Ha): <?php echo $block['Block_License_cost_Prod']; ?></strong></h5>
				<h5><strong>Block Survey Cost (USD/Ha): <?php echo $block['Block_Survey_Cost']; ?></strong></h5>
				<h5><strong>Block Survey interpretation cost (USD/Ha): <?php echo $block['Block_Survey_interpretation_cost']; ?></strong></h5>
				<h5><strong>Probability to find (%): <?php echo $block['Probability_to_find']; ?></strong></h5>
				<h5><strong>Signature Bonus ($): <?php echo $block['Signature_Bonus']; ?></strong></h5>
			</div>
			<?php } ?>
			</div>

			<img src="img/Picture1.jpg" style="max-width:100%; margin-top:16px;">

			<input type="hidden" id="form_action" name="action" value="acquire_license">
          </form>
        </div>
      </div>
    </div>
    <div class="step-layout__side">
      <div class="side-card">
        <p class="side-card__title">Project summary</p>
		<?php
			if(@$_SESSION['project']['steps_completed'] > 0) {
				echo '<strong>Project License Cost: </strong>$'.number_format((float)$_SESSION['project']['Project_License_Cost'], 0).'<br/>';
			}
			echo '<br/>';
			if(@$_SESSION['project']['block_id']) {
				echo '<strong>Selected Block:</strong><br/>'.@$block['Block_Name'];
			}
			else{
				echo 'Select blocks to see parameters and acquire a license.';
			}
		?>
      </div>
      <div class="side-card">
        <p class="task-status-title">Task status</p>
			      <div class="task-item">
			<div class="task-item__left">
			<?php if(@$_SESSION['project']['tasks_completed'] >= 11): ?>
				<span class="task-dot task-dot--done"></span>
			<?php else: ?>
				<span class="task-dot task-dot--pending"></span>
			<?php endif; ?>
				<span class="task-item__label">Task11: Acquire License</span>
			</div>
				<button id="new_prj" <?php if(@$_SESSION['project']['tasks_completed'] >= 11) echo 'disabled'; ?> onclick="acquire_license();" type="button" class="task-item__btn">
					<span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
				</button>
      </div>
    </div>
      <div class="side-card">
        <?php if(@$_SESSION['project']['steps_completed'] < 1): ?>
          <a href="#" class="md3-btn md3-btn--filled" disabled style="width:100%;justify-content:center">Next Step</a>
        <?php else: ?>
          <a href="project_step2.php" class="md3-btn md3-btn--filled" style="width:100%;justify-content:center">Next Step</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="md3-step-footer">
    <span></span>
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
function acquire_license(){
	if($("#block").val() == "") {
		md3_alert("Please Select Block");
		return false;
	}
	md3_confirm("Are you sure? You want to Acquire License for selected block?", function() {
		document.forms[0].submit();
	});
}
function get_block_params(blockId){
	if(blockId != "") {
		$.ajax({
			type: "POST",
			url: "ajax_block_parameters.php",
			data: "block_id="+blockId,
			success: function(msg) {
				$("#block_details").html(msg);
				$("#block_details").ajaxComplete(function(event, request, settings){
					$("#block_details").html(msg);
				});
			}
		});
	}
	else{
		$("#block_details").html("");
	}
}
</script>
<?php include_once "includes/graphscript.php"; ?>
</body>
</html>
<?php
	unset($_SESSION['message']);
	unset($_SESSION['message_type']);
?>
