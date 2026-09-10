<?php

function project_get_current_step($project) {
	$steps_completed = isset($project['steps_completed']) ? (int) $project['steps_completed'] : 0;
	$tasks_completed = isset($project['tasks_completed']) ? (int) $project['tasks_completed'] : 0;

	if ($tasks_completed >= 81 || $steps_completed >= 8) {
		return 8;
	}
	if ($tasks_completed >= 71 || $steps_completed >= 7) {
		return 7;
	}
	if ($tasks_completed >= 61 || $steps_completed >= 6) {
		return 6;
	}
	if ($tasks_completed >= 42 || $steps_completed >= 5) {
		return 5;
	}
	if ($tasks_completed >= 37 || $steps_completed >= 4) {
		return 4;
	}
	if ($tasks_completed >= 22 || $steps_completed >= 3) {
		return 3;
	}
	if ($tasks_completed >= 11 || $steps_completed >= 2) {
		return 2;
	}

	return 1;
}

function project_get_step_url($step_number) {
	$step_number = (int) $step_number;
	if ($step_number < 1) {
		$step_number = 1;
	}
	if ($step_number > 8) {
		$step_number = 8;
	}

	return 'project_step'.$step_number.'.php';
}

if (!function_exists('project_calculate_phase_costs')) {
	function project_calculate_phase_costs($project_data, $pfacility = array()) {
		$project = is_array($project_data) ? $project_data : array();

		// 1. Exploration Cost (Steps 1 to 4: License, Survey, Road, Accom, Exp & App Wells)
		$license_cost = isset($project['Project_License_Cost']) ? (float)$project['Project_License_Cost'] : 0;
		$survey_cost = isset($project['Project_Survey_Cost']) ? (float)$project['Project_Survey_Cost'] : 0;
		$road_cost = isset($project['Project_road_Cost']) ? (float)$project['Project_road_Cost'] : 0;
		$accom_cost = isset($project['Project_Accomodation_cost']) ? (float)$project['Project_Accomodation_cost'] : 0;
		$exp_well_cost = isset($project['Project_ExpWell_Cost']) ? (float)$project['Project_ExpWell_Cost'] : 0;
		$app_well_cost = isset($project['Project_AppWell_Cost']) ? (float)$project['Project_AppWell_Cost'] : 0;

		$exploration_cost = $license_cost + $survey_cost + $road_cost + $accom_cost + $exp_well_cost + $app_well_cost;

		// 2. Development Cost (Step 5: Dev Wells & Facilities)
		$dev_well_cost = isset($project['Project_DevWell_Cost']) ? (float)$project['Project_DevWell_Cost'] : 0;
		$facility_cost = isset($pfacility['Prod_Facilities_Cost']) ? (float)$pfacility['Prod_Facilities_Cost'] : (isset($project['Project_ProdFacility_Cost']) ? (float)$project['Project_ProdFacility_Cost'] : 0);

		$development_cost = $dev_well_cost + $facility_cost;

		// 3. Production Cost (Steps 6 to 8: Decommissioning and Abandonment)
		$decom_cost = isset($project['Project_Decomissionning_Cost']) ? (float)$project['Project_Decomissionning_Cost'] : 0;
		$abandon_cost = isset($project['Project_Abandonment_Cost']) ? (float)$project['Project_Abandonment_Cost'] : 0;
		$production_cost = $decom_cost + $abandon_cost;

		$total_spending = isset($project['Project_Spending']) ? (float)$project['Project_Spending'] : 0;
		$sum_costs = $exploration_cost + $development_cost + $production_cost;

		// Fallback proportional allocation if granular breakdown sum is 0 but total spending > 0
		if ($sum_costs <= 0 && $total_spending > 0) {
			$current_step = project_get_current_step($project);
			if ($current_step <= 4) {
				$exploration_cost = $total_spending;
			} elseif ($current_step == 5) {
				$exploration_cost = round($total_spending * 0.35, 2);
				$development_cost = round($total_spending * 0.65, 2);
			} else {
				$exploration_cost = round($total_spending * 0.25, 2);
				$development_cost = round($total_spending * 0.55, 2);
				$production_cost = round($total_spending * 0.20, 2);
			}
		}

		return array(
			'exploration_cost' => round($exploration_cost, 2),
			'development_cost' => round($development_cost, 2),
			'production_cost' => round($production_cost, 2),
			'total_cost' => round($total_spending, 2)
		);
	}
}
