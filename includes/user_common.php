<?php
	array_walk($_POST, 'cleanVar');
	$db = new DB();
	$params = $db->get_parameters();

	$parameters = array();

	foreach($params as $key => $value) {
		$parameters[$value['name']] = $value; 
	}

	$project = $db->get_project($_SESSION['project']['project_id']);
	if (!$project) $project = array();

	if(isset($_SESSION['project']['block_id'])) {

		$block = $db->get_block($_SESSION['project']['block_id']);
		$field = ($block && isset($block['Field'])) ? $db->get_field($block['Field']) : false;

		$Field_ExpWell1 = ($field && isset($field['Field_ExpWell1'])) ? $db->get_well($field['Field_ExpWell1']) : array();
		$Field_ExpWell2 = ($field && isset($field['Field_ExpWell2'])) ? $db->get_well($field['Field_ExpWell2']) : array();

		$Field_AppWell1 = ($field && isset($field['Field_AppWell1'])) ? $db->get_well($field['Field_AppWell1']) : array();
		$Field_AppWell2 = ($field && isset($field['Field_AppWell2'])) ? $db->get_well($field['Field_AppWell2']) : array();

		$Field_DevWell1 = ($field && isset($field['Field_DevWell1'])) ? $db->get_well($field['Field_DevWell1']) : array();
		$Field_DevWell2 = ($field && isset($field['Field_DevWell2'])) ? $db->get_well($field['Field_DevWell2']) : array();
		$Field_DevWell3 = ($field && isset($field['Field_DevWell3'])) ? $db->get_well($field['Field_DevWell3']) : array();

		if($project && !empty($project['Project_Production_Facility']))
			$pfacility = $db->get_production_facility($project['Project_Production_Facility']);

	}

?>