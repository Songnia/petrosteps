<?php
	include('includes/db.class.php');
	include('includes/user_common.php');

	if(!isset($_SESSION['user_id'])){
		header('Location:login.php');
		exit;
	}

    $project_cash_flow = $db->get_cash_flow($_SESSION['project']['project_id']);
    $oil_price = $db->get_parameter('Oil_Price');
    include_once 'includes/graph_data.php';
    list($graph_array, $current_project_year) = build_project_graph_data($project_cash_flow, $oil_price);
    $_SESSION['project']['current_project_year'] = $current_project_year;
    echo json_encode($graph_array);
?>
<?php // include_once "includes/graphscript.php"; ?>
