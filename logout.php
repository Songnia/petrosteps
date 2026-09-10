<?php
/************************************************************************************
* File Name		: logout.php
* Purpose		: Logout page
* Created On	: 02-08-2016
***********************************************************************************/
  include('includes/db.class.php');
  $db = new DB();
  $session_id = $db->set_active_session($_SESSION['user_id'], '');
	session_unset();
	$_SESSION['message_type'] = 'success';
	$_SESSION['message'] = 'Logout Successful';
	header('Location:login.php');
	exit;
?>