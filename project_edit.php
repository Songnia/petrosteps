<?php
	include('includes/db.class.php');
	require_once 'includes/project_step_helpers.php';
	
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
		header('Location:index.php');
		exit;
	}

	$db = new DB();

	// Handle Delete Project
	if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['action'] == 'delete_project') {
		$Pid = $_POST['Pid'] ?? $_POST['project_id'] ?? 0;
		if($Pid) {
			$delete = $db->delete_project($Pid);
			if($delete) {
				$_SESSION['message_type'] = 'success';
				$_SESSION['message'] = 'Project deleted successfully';
			} else {
				$_SESSION['message_type'] = 'danger';
				$_SESSION['message'] = 'Error in deleting Project please try again';
			}
		}
		header('Location:ongoing_project_list.php');
		exit;
	}

	// Handle Open / Edit / Select Project
	$projectId = $_POST['Pid'] ?? $_POST['project_id'] ?? $_GET['id'] ?? 0;
	if($projectId) {
		$project = $db->get_project($projectId);
		if($project) {
			$_SESSION['project'] = $project;
			$_SESSION['project']['project_id'] = $project['Pid'];
			$_SESSION['project']['block_id'] = $project['Project_Block'];
			$_SESSION['project']['Cumul_Production'] = $project['Project_Cumulative_Flow'];
			$_SESSION['project']['production_facility'] = $project['Project_Production_Facility'];
			$_SESSION['project']['current_project_year'] = $project['Project_year'];
			
			header('Location:'.project_get_step_url(project_get_current_step($_SESSION['project'])));
			exit;
		}
	}

	header('Location:ongoing_project_list.php');
	exit;
?>