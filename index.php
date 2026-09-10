<?php

	include('includes/db.class.php');
 	$db = new DB();
	if(!isset($_SESSION['user_id'])){
		header('Location:landing.php');
		exit;
	}
	$session_id = $db->get_active_session($_SESSION['user_id']);
	if($_SESSION['session_id'] !== $session_id){
		header('Location:login.php');
		exit;
	}
	
	if($_SESSION['user_type'] == "SuperAdmin") {
		include "dashboard_superadmin.php";
	}
	else if($_SESSION['user_type'] == "Admin") {
		include "dashboard_admin.php";
	}
	else if($_SESSION['user_type'] == "Trainer") {
		include "dashboard_trainer.php";
	}
	else  {
		include "dashboard_participant.php";
	}
?>
