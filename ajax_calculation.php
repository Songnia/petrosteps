<?php
	include('includes/db.class.php');
	include('includes/user_common.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id']){
		header('Location:index.php');
		exit;
	}
	$db = new DB();
	require_once 'includes/graph_data.php';
	function get_cashflow_kpis($db, $project_id, $oil_price) {
		$cashflow_rows = $db->get_cash_flow($project_id);
		list($cashflow_graph) = build_project_graph_data($cashflow_rows, $oil_price);
		$current_cashflow = 0.0;
		$cumulative_cashflow = 0.0;
		foreach ($cashflow_graph as $cashflow_point) {
			$current_cashflow = isset($cashflow_point['cashflow']) ? (float)$cashflow_point['cashflow'] : 0.0;
			$cumulative_cashflow += $current_cashflow;
		}
		return array($current_cashflow, $cumulative_cashflow);
	}
	function apply_annual_production_opex($annual_production, $parameters) {
		$opex_per_barrel = isset($parameters['Production_Opex_Per_Barrel']['value'])
			? max(0, (float)$parameters['Production_Opex_Per_Barrel']['value'])
			: 0;
		$annual_opex = round(max(0, (float)$annual_production) * $opex_per_barrel, 2);

		if (!isset($_SESSION['project']['Project_Opex'])) {
			$_SESSION['project']['Project_Opex'] = 0;
		}
		$_SESSION['project']['Project_Opex'] += $annual_opex;
		$_SESSION['project']['Project_Spending'] += $annual_opex;

		return $annual_opex;
	}
		/*	1.	Project parameter Project_Prod_Year is set to  0 , Cumul_Production=0-----
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

		$_SESSION['project']['Project_year'] = 0;
		$_SESSION['project']['Cumul_Production'] = 0;
		$_SESSION['project']['Project_Cumulative_Flow'] = 0;
 */
 //$_SESSION['project']['Project_year'] =0;
//chartshowhide
if(@$_POST['action'] == 'chartshowhide') {
	$type = isset($_POST['type'])? $_POST['type'] : 'hide';
	$_SESSION['chartshow'] = $type;
	exit;
}
 if(@$_POST['action'] == 'increment1') {
	$step_should_be_saved = false;
	if($_SESSION['project']['Production_year'] < 10){
		if($_SESSION['project']['Production_year'] >= 4){
			$_SESSION['project']['Project_Total_Flowrate'] = $_SESSION['project']['Project_Total_Flowrate']*0.9;
		}
		$_SESSION['project']['Production_year'] = $_SESSION['project']['Production_year']+1;
		$_SESSION['project']['Project_year'] = $_SESSION['project']['Project_year']+1;
		$annual_production = $_SESSION['project']['Project_Total_Flowrate'] * 365;
		$_SESSION['project']['Cumul_Production'] += $annual_production;
		apply_annual_production_opex($annual_production, $parameters);
		$_SESSION['project']['Project_Actual_Revenue'] = $_SESSION['project']['Cumul_Production']*$parameters['Oil_Price']['value'];
		$step_should_be_saved = true;
	}

	$_SESSION['project']['Cumul_Production'] = round($_SESSION['project']['Cumul_Production'], 2);
	$_SESSION['project']['Project_Actual_Revenue'] = round($_SESSION['project']['Project_Actual_Revenue'], 2);
	if($step_should_be_saved){
		$db->add_project_step(
			$_SESSION['project']['project_id'],
			$_SESSION['project']['Project_year'],
			$_SESSION['project']['Cumul_Production'],
			$_SESSION['project']['Project_Spending']
		);
	}

	$update_str = "Project_Total_Flowrate = '".$_SESSION['project']['Project_Total_Flowrate']."', Production_year = '".$_SESSION['project']['Production_year']."', Project_year = '".$_SESSION['project']['Project_year']."', Project_Cumulative_Flow = ".$_SESSION['project']['Cumul_Production'].", Project_Actual_Revenue = '".$_SESSION['project']['Project_Actual_Revenue']."', Project_Opex = '".$_SESSION['project']['Project_Opex']."', Project_Spending = '".$_SESSION['project']['Project_Spending']."' ";
	$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
	//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['Project_year'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
	list($current_cashflow, $cumulative_cashflow) = get_cashflow_kpis($db, $_SESSION['project']['project_id'], $parameters['Oil_Price']['value']);
	echo $_SESSION['project']['Production_year']."##".$_SESSION['project']['Cumul_Production']." B##$".number_format((float)$_SESSION['project']['Project_Actual_Revenue'], 0, '.', ',')."##".$_SESSION['project']['Project_Total_Flowrate']." B/D##$".number_format($current_cashflow, 0, '.', ',')."##$".number_format($cumulative_cashflow, 0, '.', ',');
 }
 if(@$_POST['action'] == 'increment2') {
	 	/*	Then Every one minute the following happens.
		2.	Production_year=Production_year+1
		3.	Cumul_Production = Cumul_Production+ Actual_flowrate*365
		Until the following happens:
		4.	Prod_Year = 20  or Participant moves to Step7.
		5.	If any of the above happens Actual_Flowrate =0 and displayed.
		6.	Task71 status set to done */
	$step_should_be_saved = false;

	if($_SESSION['project']['Production_year'] < 100){
		if($_SESSION['project']['Production_year'] >= 4){
			$_SESSION['project']['Project_Total_Flowrate'] = $_SESSION['project']['Project_Total_Flowrate']*0.9;
		}
		$_SESSION['project']['Production_year'] = $_SESSION['project']['Production_year']+1;
		$_SESSION['project']['Project_year'] = $_SESSION['project']['Project_year']+1;
		$annual_production = $_SESSION['project']['Project_Total_Flowrate'] * 365;
		$_SESSION['project']['Cumul_Production'] += $annual_production;
		apply_annual_production_opex($annual_production, $parameters);
		$_SESSION['project']['Project_Actual_Revenue'] = $_SESSION['project']['Cumul_Production']*$parameters['Oil_Price']['value'];
		$step_should_be_saved = true;
	}
	else{
		$_SESSION['project']['Project_Total_Flowrate'] = 0;
	}
	$_SESSION['project']['Cumul_Production'] = round($_SESSION['project']['Cumul_Production'], 2);

	$_SESSION['project']['Project_Actual_Revenue'] = round($_SESSION['project']['Project_Actual_Revenue'], 2);
	if($step_should_be_saved){
		$db->add_project_step(
			$_SESSION['project']['project_id'],
			$_SESSION['project']['Project_year'],
			$_SESSION['project']['Cumul_Production'],
			$_SESSION['project']['Project_Spending']
		);
	}
		$update_str = "Project_Total_Flowrate = '".$_SESSION['project']['Project_Total_Flowrate']."', Production_year = '".$_SESSION['project']['Production_year']."', Project_year = '".$_SESSION['project']['Project_year']."', Project_Cumulative_Flow = ".$_SESSION['project']['Cumul_Production'].", Project_Actual_Revenue = '".$_SESSION['project']['Project_Actual_Revenue']."', Project_Opex = '".$_SESSION['project']['Project_Opex']."', Project_Spending = '".$_SESSION['project']['Project_Spending']."' ";
		$updated = $db->update_project($update_str, $_SESSION['project']['project_id']);
		//$step_updated = $db->add_project_step($_SESSION['project']['project_id'], $_SESSION['project']['Project_year'], $_SESSION['project']['Project_Actual_Revenue'], $_SESSION['project']['Project_Spending']);
	list($current_cashflow, $cumulative_cashflow) = get_cashflow_kpis($db, $_SESSION['project']['project_id'], $parameters['Oil_Price']['value']);
	echo $_SESSION['project']['Production_year']."##".$_SESSION['project']['Cumul_Production']." B##$".number_format((float)$_SESSION['project']['Project_Actual_Revenue'], 0, '.', ',')."##".$_SESSION['project']['Project_Total_Flowrate']." B/D##$".number_format($current_cashflow, 0, '.', ',')."##$".number_format($cumulative_cashflow, 0, '.', ',');
 }
?>
