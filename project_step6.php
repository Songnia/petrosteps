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
		if(@$_SESSION['project']['steps_completed'] < 5 || (int)@$_SESSION['project']['tasks_completed'] < 61){
			header('Location:project_step5.php');
			exit;
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'Start production' && @$_SESSION['project']['tasks_completed'] == 61) {
		$_SESSION['project']['steps_completed'] = 6;
		$_SESSION['project']['tasks_completed'] = 62;
	/*	
2.11.2	Task62: Start production
Upon execution this task   
1.	 Project parameter Project_Prod_Year is set to  0 , Cumul_Production=0
2.	Task61_Status set to done.
3.	Turn Wells images to green (Refer to PPT V8 slide 12).
4.	Actual_Flowrate = (ExpWellflowrate+AppWeel1_Flowrate+ AppWeel2_Flowrate+DevWell1_flowrate+DevWell2_flowrate+DevWell3_flowrate+DevWell4_flowrate).

	1.	Project parameter Project_Prod_Year is set to  0 , Cumul_Production=0-----
		2.	Task61_Status set to done.----
		3.	Turn Wells images to green (Refer to PPT V8 slide 12).-----
		4.	Actual_Flowrate = (ExpWellflowrate+AppWeel1_Flowrate+ AppWeel2_Flowrate+DevWell1_flowrate+DevWell2_flowrate+DevWell3_flowrate+DevWell4_flowrate).-----


		Then every 15 Sec the following happens:
		5.	Production_year=Production_year+1
		6.	Cumul_Production = Cumul_Production+ Actual_flowrate*365

		-	When Prod_year =4
		-	Every 15 Sec
		1.	 Actual_Flowrate = Actual* 0.9
		2.	Cumul_Production = Cumul_Production+ Actual_flowrate*365
		3.	Production_year=Production_year+1
		4.	Until Production_year=15 or the participant move to step7
 */
		//$_SESSION['project']['Project_year'] = 0;
		$_SESSION['project']['Cumul_Production'] = 0;
		$_SESSION['project']['Project_Cumulative_Flow'] = 0;
		$_SESSION['project']['Project_Opex'] = 0;
		$_SESSION['project']['Project_Actual_Revenue'] = $_SESSION['project']['Cumul_Production']*$parameters['Oil_Price']['value'];
		//$_SESSION['project']['Production_year'] = 1;
		//$_SESSION['project']['Project_year'] = $_SESSION['project']['Project_year']+1;

	$Field_ExpWell1 = $db->get_well($field['Field_ExpWell1']);
	$Field_ExpWell2 = $db->get_well($field['Field_ExpWell2']);
	
	$Field_AppWell1 = $db->get_well($field['Field_AppWell1']);
	$Field_AppWell2 = $db->get_well($field['Field_AppWell2']);

	$Field_DevWell1 = $db->get_well($field['Field_DevWell1']);
	$Field_DevWell2 = $db->get_well($field['Field_DevWell2']);
	$Field_DevWell3 = $db->get_well($field['Field_DevWell3']);

	$Actual_Flowrate =  $Field_ExpWell1['Well_Flowrate'] + $Field_ExpWell2['Well_Flowrate']+  $Field_AppWell1['Well_Flowrate']+ $Field_AppWell2['Well_Flowrate']+ $Field_DevWell1['Well_Flowrate']+ $Field_DevWell2['Well_Flowrate']+ $Field_DevWell3['Well_Flowrate'];

	$_SESSION['project']['Project_Total_Flowrate'] = $Actual_Flowrate;
	//var_dump($Actual_Flowrate); 

	//$_SESSION['project']['Cumul_Production'] = $Actual_Flowrate*$_SESSION['project']['Production_Year'];
	//exit;
		$update_str = " Project_Total_Flowrate = '$Actual_Flowrate', Project_Opex = 0, Project_year = ".$_SESSION['project']['Project_year'].", Production_year = ".$_SESSION['project']['Production_year'].", Project_Task62_Status = 'Done', tasks_completed = 62, steps_completed = 6";

		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['steps_completed'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
		//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], 1, $_SESSION['project']['Cumul_Production'], $_SESSION['project']['Project_Spending']);
		if($updated){
			$_SESSION['message_type'] = 'success';
			$_SESSION['message'] = 'Start production done successfully.';
		}
		else{
			$_SESSION['message_type'] = 'danger';
			$_SESSION['message'] = 'Error in Start production. Please Try Again';
		}
		header('Location:project_step6.php');
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Step 6 PRODUCTION - <?php echo APP_NAME; ?></title>
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
.production-graph-card .md3-card__body{padding:24px}
.production-graph-bar{padding:0 0 18px;margin-bottom:0}
.production-graph-panel{min-height:420px}
.production-graph-panel #chartdiv{width:100%;height:420px;min-height:420px;display:block}
.production-graph-axis{
	display:flex;
	flex-wrap:wrap;
	gap:12px 24px;
	margin-top:16px;
	color:var(--md-on-surface-variant);
	font-size:13px;
	font-weight:600;
}
@media (max-width: 991px){
	.production-graph-card .md3-card__body{padding:20px}
}
</style>
</head>
<body>
<?php include_once "includes/user_navbar.php"; ?>

<div class="md3-step-container">
	<?php
		$project_nav_show_graph = false;
		include "includes/project_nav.php";
	?>

	<form id="production-actions-form" class="form-horizontal" role="form" method="post" style="margin:0;">
	  <input type="hidden" id="form_action" name="action" value="" />
	</form>

	<div class="step-layout">
	    <div class="step-layout__main">
	      <div class="md3-card production-graph-card">
	        <div class="md3-card__header"><h3>Step 6 - PRODUCTION</h3></div>
	        <div class="md3-card__body">
	          <div class="md3-graph-bar production-graph-bar">
	            <button type="button" class="md3-btn md3-btn--outline md3-btn--sm see_graph_btn">
	              <span class="material-symbols-outlined">bar_chart</span> Show Graph
	            </button>
	            <button type="button" class="md3-btn md3-btn--outline md3-btn--sm hide_graph_btn">
	              <span class="material-symbols-outlined">bar_chart</span> Hide Graph
	            </button>
	          </div>
	          <div id="mainChartDiv" class="production-graph-panel">
	            <div id="chartdiv"></div>
	            <div class="production-graph-axis">
	              <span><strong>X-axis :</strong> Project Year</span>
	              <span><strong>Y-axis left :</strong> Cash Flow</span>
	              <span><strong>Y-axis right :</strong> Production</span>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	    <div class="step-layout__side">
	      <div class="side-card">
	        <p class="task-status-title">Task status</p>
	<div class="task-item">
	  <div class="task-item__left">
<?php if($_SESSION['project']['tasks_completed'] >= 62): ?>
  <span class="task-dot task-dot--done"></span>
<?php else: ?>
  <span class="task-dot task-dot--pending"></span>
<?php endif; ?>
  <span class="task-item__label">Task62: Start production</span>
  </div>
  <button <?php if($_SESSION['project']['tasks_completed'] >= 62) echo 'disabled'; ?> onclick="do_task('Start production');" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>
<?php if($_SESSION['project']['tasks_completed'] >= 62): ?>
<div class="task-item">
  <div class="task-item__left">
<?php
  $prod_year = isset($_SESSION['project']['Production_year']) ? (int)$_SESSION['project']['Production_year'] : 0;
  $proj_year = isset($_SESSION['project']['Project_year']) ? (int)$_SESSION['project']['Project_year'] : 0;
  if ($prod_year >= 10): ?>
  <span class="task-dot task-dot--done"></span>
  <?php elseif ($prod_year > 0): ?>
  <span class="task-dot task-dot--pending" style="background-color:#f0ad4e;"></span>
  <?php else: ?>
  <span class="task-dot task-dot--pending" style="background-color:#777;"></span>
  <?php endif; ?>
  <span class="task-item__label">Task63: Increment Production and project year</span>
  </div>
  <button id="inc_button" <?php if($prod_year >= 10) echo 'disabled'; ?> onclick="increment_count();" type="button" class="task-item__btn">
    <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
  </button>
</div>

<!-- Espace sous le bouton pour afficher les années de production -->
<div class="production-year-card" style="margin-top: 12px; padding: 14px 16px; background: rgba(103, 183, 220, 0.08); border: 1px solid rgba(103, 183, 220, 0.25); border-radius: 12px;">
  <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--md-primary, #1e88e5);">Années de Production</span>
    <!--<span class="md3-badge md3-badge--accent" style="font-weight: 700;">Année <?php echo $prod_year; ?> / 10</span>-->
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
	        <?php if(@$_SESSION['project']['steps_completed'] < 6): ?>
	          <a href="#" class="md3-btn md3-btn--filled" disabled style="width:100%;justify-content:center">Next Step</a>
	        <?php else: ?>
	          <a href="project_step7.php" class="md3-btn md3-btn--filled" style="width:100%;justify-content:center">Next Step</a>
	        <?php endif; ?>
	      </div>
	    </div>
	</div>

	<div class="md3-step-footer">
	  <a href="project_step5.php" class="md3-btn md3-btn--outline">Previous Step</a>
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
	  message = "Are you sure? you want to Start production ?";
	  md3_confirm(message, function() {
	    $("#form_action").val(taskname);
	    document.getElementById('production-actions-form').submit();
	  });
	  return false;
	}
	function increment_count() {
  $.ajax
  ({
    type: "POST",
    url: "ajax_calculation.php",
    data: "action=increment1",
      success: function(msg)
    {
      console.log('res', msg);
      var res = msg.split("##");
      console.log('res', res);
      if(res[0] >= 10) {
        md3_alert('You can not increment count more than 10.');
        $("#inc_button").attr('disabled', 'disabled');
      }
      $("#PRODUCTION_YEAR").html(res[0]);
      $("#PROD_YEAR_DISPLAY").html(res[0] + " ans");
      $("#CUMUL_PROD").html(res[1]);
      $("#ACTUAL_REVENUE").html(res[2]);
      $("#ACTUAL_FLOWRATE").html(res[3]);
      $("#CASH_FLOW").html(res[4]);
      $("#CUMULATIVE_CASH_FLOW").html(res[5]);
      window.location.reload();
    }
  });
  setTimeout(function(){
    $.ajax
    ({
      type: "POST",
      url: "ajax_graph.php",
      data: "action=increment1",
        success: function(json_data) {
        /*console.log('json_data', json_data);
        var json_str = JSON.parse(json_data);
        $('#chartdiv').html('');
        var chart = AmCharts.makeChart( "chartdiv", {
          "type": "serial",
          "theme": "light",
          "titles": [ {
            "text": "Cash flow and production profile of upstream assets",
            "size": 15
          } ],
          "legend": {
            "align": "center",
            "equalWidths": false,
            "periodValueText": "total: [[value.sum]]",
            "valueAlign": "left",
            "valueText": "[[value]] ",
            "valueWidth": 100
          },
          "dataProvider": json_str,
          "valueAxes": [ {
            "id": "v1",
            "stackType": "regular",
            "gridAlpha": 0.07,
            "position": "left",
            "title": "Cash flow"
          }, {
            "id": "v2",
            "stackType": "regular",
            "gridAlpha": 0,
            "axisAlpha": 0,
            "labelsEnabled": false
          } ],
          "graphs": [ {
            "valueAxis": "v1",
            "fillAlphas": 0.5,
            "lineAlpha": 0.5,
            "title": "Cash Flow",
            "valueField": "cashflow"
          }, {
            "valueAxis": "v2",
            "fillAlphas": 0.5,
            "lineAlpha": 0.5,
            "title": "Production",
            "valueField": "production"
          } ],
          "plotAreaBorderAlpha": 0,
          "marginLeft": 0,
          "marginBottom": 0,
          "chartCursor": {
            "cursorAlpha": 0,
            "zoomable": false
          },
          "categoryField": "project_year",
          "categoryAxis": {
            "startOnAxis": true,
            "axisColor": "#DADADA",
            "gridAlpha": 0.07,
            "title": "Project year"
          }
        } );*/
      }
    });
  },100);
}
</script>
<?php include_once "includes/graphscript.php"; ?>
</body>
</html>
<?php
  unset($_SESSION['message']);
  unset($_SESSION['message_type']);
?>
