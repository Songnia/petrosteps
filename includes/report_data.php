<?php

if (!function_exists('report_array_get')) {
	function report_array_get($array, $key, $default) {
		if (is_array($array) && isset($array[$key]) && $array[$key] !== '' && $array[$key] !== null) {
			return $array[$key];
		}

		return $default;
	}
}

if (!function_exists('report_number')) {
	function report_number($value, $default) {
		if ($value === '' || $value === null || !is_numeric($value)) {
			return $default;
		}

		return (float) $value;
	}
}

if (!function_exists('report_positive_count')) {
	function report_positive_count($values) {
		$count = 0;
		foreach ($values as $value) {
			if (is_numeric($value) && (float) $value > 0) {
				$count++;
			}
		}

		return $count;
	}
}

if (!function_exists('report_build_annual_profile')) {
	function report_build_annual_profile($project_cash_flow, $oil_price) {
		$oil_price = (float) $oil_price;
		$profile_by_year = array();
		$current_project_year = 0;
		$previous_absolute_year = 0;
		$previous_cumulative_flow = 0.0;
		$previous_spending = 0.0;
		$has_production_started = false;

		foreach ($project_cash_flow as $flow) {
			$raw_year = isset($flow['Project_year']) ? (int) $flow['Project_year'] : 0;
			$current_cumulative_flow = isset($flow['Project_Cumulative_Flow']) ? (float) $flow['Project_Cumulative_Flow'] : 0.0;
			$current_spending = isset($flow['Project_Spending']) ? (float) $flow['Project_Spending'] : 0.0;

			if (
				$previous_cumulative_flow > 0
				&& $oil_price > 0
				&& $raw_year === 1
				&& ($current_cumulative_flow / $previous_cumulative_flow) > max(5, $oil_price / 3)
			) {
				$current_cumulative_flow = $current_cumulative_flow / $oil_price;
			}

			if ($raw_year <= 0) {
				$previous_cumulative_flow = $current_cumulative_flow;
				$previous_spending = $current_spending;
				continue;
			}

			$step_production = $current_cumulative_flow - $previous_cumulative_flow;
			$production_increased = ($step_production > 0.5);

			if ($production_increased) {
				if ($raw_year > $previous_absolute_year) {
					$current_absolute_year = $raw_year;
				}
				else {
					$current_absolute_year = $previous_absolute_year + max($raw_year, 1);
				}
			}
			elseif (!$has_production_started) {
				$current_absolute_year = max($previous_absolute_year, $raw_year);
			}
			else {
				$current_absolute_year = $previous_absolute_year;
			}

			if ($current_absolute_year <= 0) {
				$previous_absolute_year = $current_absolute_year;
				$previous_cumulative_flow = $current_cumulative_flow;
				$previous_spending = $current_spending;
				continue;
			}

			$year_span = $current_absolute_year - $previous_absolute_year;
			if ($year_span < 1) {
				$year_span = 1;
			}

			$step_spending = $current_spending - $previous_spending;
			$step_revenue = $step_production * $oil_price;
			$step_cashflow = $step_revenue - $step_spending;
			$per_year_production = $step_production / $year_span;
			$per_year_revenue = $step_revenue / $year_span;
			$per_year_spending = $step_spending / $year_span;
			$per_year_cashflow = $step_cashflow / $year_span;
			$start_year = ($current_absolute_year > $previous_absolute_year) ? ($previous_absolute_year + 1) : $current_absolute_year;

			if ($start_year < 1) {
				$start_year = 1;
			}

			for ($year = $start_year; $year <= $current_absolute_year; $year++) {
				if (!isset($profile_by_year[$year])) {
					$profile_by_year[$year] = array(
						'project_year' => $year,
						'production' => 0.0,
						'revenue' => 0.0,
						'spending' => 0.0,
						'cashflow' => 0.0,
						'cumulative_cashflow' => 0.0,
					);
				}

				$profile_by_year[$year]['production'] += round($per_year_production, 2);
				$profile_by_year[$year]['revenue'] += round($per_year_revenue, 2);
				$profile_by_year[$year]['spending'] += round($per_year_spending, 2);
				$profile_by_year[$year]['cashflow'] += round($per_year_cashflow, 2);
			}

			$current_project_year = max($current_project_year, $current_absolute_year);
			if ($production_increased) {
				$has_production_started = true;
			}

			$previous_absolute_year = $current_absolute_year;
			$previous_cumulative_flow = $current_cumulative_flow;
			$previous_spending = $current_spending;
		}

		ksort($profile_by_year);

		$cumulative_cashflow = 0.0;
		foreach ($profile_by_year as $year => $row) {
			$cumulative_cashflow += (float) $row['cashflow'];
			$profile_by_year[$year]['cumulative_cashflow'] = round($cumulative_cashflow, 2);
		}

		// Compute payback year: first year where cumulative cash flow turns >= 0
		$payback_year = null;
		$cumulative = 0.0;
		foreach ($profile_by_year as $year => $row) {
			$cumulative += (float) $row['cashflow'];
			$profile_by_year[$year]['cumulative_cashflow'] = round($cumulative, 2);
			if ($payback_year === null && $cumulative >= 0) {
				$payback_year = $year;
			}
		}

		return array(array_values($profile_by_year), $current_project_year, $payback_year);
	}
}

if (!function_exists('report_build_appendix_rows')) {
	function report_build_appendix_rows($source_rows, $map) {
		$rows = array();

		foreach ($map as $item) {
			$key = $item['key'];
			$value = report_array_get($source_rows, $key, '');
			if ($value === '') {
				continue;
			}

			$rows[] = array(
				'label' => $item['label'],
				'unit' => $item['unit'],
				'value' => $value,
			);
		}

		return $rows;
	}
}

if (!function_exists('build_report_context')) {
	function build_report_context($source) {
		$db = isset($source['db']) ? $source['db'] : null;
		$project = isset($source['project']) && is_array($source['project']) ? $source['project'] : array();
		$parameters = isset($source['parameters']) && is_array($source['parameters']) ? $source['parameters'] : array();
		$block = isset($source['block']) && is_array($source['block']) ? $source['block'] : array();
		$field = isset($source['field']) && is_array($source['field']) ? $source['field'] : array();
		$pfacility = isset($source['pfacility']) && is_array($source['pfacility']) ? $source['pfacility'] : array();
		$session_project = isset($source['session_project']) && is_array($source['session_project']) ? $source['session_project'] : array();

		$project_id = 0;
		if (isset($session_project['project_id'])) {
			$project_id = (int) $session_project['project_id'];
		}
		elseif (isset($project['Pid'])) {
			$project_id = (int) $project['Pid'];
		}

		$oil_price = 0;
		if ($db && method_exists($db, 'get_parameter')) {
			$oil_price = report_number($db->get_parameter('Oil_Price'), 0);
		}
		if (!$oil_price && isset($parameters['Oil_Price']['value'])) {
			$oil_price = report_number($parameters['Oil_Price']['value'], 0);
		}

		$project_cash_flow = array();
		if ($db && method_exists($db, 'get_cash_flow') && $project_id > 0) {
			$project_cash_flow = $db->get_cash_flow($project_id);
		}

		list($annual_profile, $derived_project_year, $payback_year) = report_build_annual_profile($project_cash_flow, $oil_price);

		$step_labels = array(
			1 => 'License',
			2 => 'Survey',
			3 => 'Exploration',
			4 => 'Appraisal',
			5 => 'Development',
			6 => 'Production',
			7 => '2nd Recovery',
			8 => 'Abandonment',
		);

		$current_step = function_exists('project_get_current_step') ? project_get_current_step($project) : 1;
		$current_step_label = isset($step_labels[$current_step]) ? $step_labels[$current_step] : 'Project';
		$steps_completed = isset($project['steps_completed']) ? (int) $project['steps_completed'] : 0;

		$timeline = array();
		foreach ($step_labels as $step_number => $step_name) {
			$status = 'Upcoming';
			if ($steps_completed >= $step_number) {
				$status = 'Completed';
			}
			elseif ($current_step === $step_number) {
				$status = 'Current';
			}
			elseif ($current_step > $step_number) {
				$status = 'Completed';
			}

			$timeline[] = array(
				'number' => $step_number,
				'name' => $step_name,
				'status' => $status,
			);
		}

		$peak_production = array();
		$peak_cashflow = array();
		$total_profile_production = 0.0;
		$total_profile_cashflow = 0.0;
		$total_profile_spending = 0.0;
		$total_profile_revenue = 0.0;

		foreach ($annual_profile as $row) {
			$total_profile_production += (float) $row['production'];
			$total_profile_cashflow += (float) $row['cashflow'];
			$total_profile_spending += (float) $row['spending'];
			$total_profile_revenue += (float) $row['revenue'];

			if (!$peak_production || (float) $row['production'] > (float) $peak_production['production']) {
				$peak_production = $row;
			}

			if (!$peak_cashflow || (float) $row['cashflow'] > (float) $peak_cashflow['cashflow']) {
				$peak_cashflow = $row;
			}
		}

		$project_year = isset($project['Project_year']) ? (int) $project['Project_year'] : 0;
		if ($derived_project_year > $project_year) {
			$project_year = $derived_project_year;
		}

		$production_year = isset($project['Production_year']) ? (int) $project['Production_year'] : 0;
		$cumul_production = report_number(report_array_get($project, 'Cumul_Production', report_array_get($session_project, 'Cumul_Production', 0)), 0);
		$total_flowrate = report_number(report_array_get($project, 'Project_Total_Flowrate', report_array_get($session_project, 'Project_Total_Flowrate', 0)), 0);
		$projected_revenue = report_number(report_array_get($project, 'Project_Projected_Revenue', report_array_get($session_project, 'Project_Projected_Revenue', 0)), 0);
		$actual_revenue = report_number(report_array_get($project, 'Project_Actual_Revenue', report_array_get($session_project, 'Project_Actual_Revenue', 0)), 0);
		$project_budget = report_number(report_array_get($project, 'Project_Budget', report_array_get($session_project, 'Project_Budget', 0)), 0);
		if ($project_budget <= 0 && isset($parameters['Budget']['value'])) {
			$project_budget = report_number($parameters['Budget']['value'], 0) * 1000000;
		}
		$project_spending = report_number(report_array_get($project, 'Project_Spending', report_array_get($session_project, 'Project_Spending', 0)), 0);

		// Finding Cost per barrel: total capex / recoverable volume
		$recoverable_volume = report_number(report_array_get($project, 'Project_Recoverable_Volume', report_array_get($session_project, 'Project_Recoverable_Volume', 0)), 0);
		$finding_cost_per_barrel = ($recoverable_volume > 0) ? round($project_spending / $recoverable_volume, 2) : null;

		$wells = array();
		$well_names = array('Field_ExpWell1', 'Field_ExpWell2', 'Field_AppWell1', 'Field_AppWell2', 'Field_DevWell1', 'Field_DevWell2', 'Field_DevWell3');
		foreach ($well_names as $well_key) {
			if (isset($source[$well_key]) && is_array($source[$well_key]) && !empty($source[$well_key])) {
				$wells[$well_key] = $source[$well_key];
			}
		}

		$observations = array();
		$observations[] = 'This PDF is an internal Petrosteps project report aligned with project-based upstream reporting logic. It is not a reserves certification or a regulatory filing.';
		$observations[] = 'Current project stage: Step '.$current_step.' - '.$current_step_label.'.';

		if (!empty($pfacility['Prod_Facilities_Name'])) {
			$observations[] = 'Selected production facility: '.$pfacility['Prod_Facilities_Name'].'.';
		}
		else {
			$observations[] = 'No production facility is currently linked to the project record.';
		}

		if ($production_year > 0) {
			$observations[] = 'Production has started and the recorded production year is '.$production_year.'.';
		}
		else {
			$observations[] = 'Production has not started yet in the stored project data.';
		}

		if (!empty($peak_production)) {
			$observations[] = 'Peak annual production in the current profile occurs in project year '.$peak_production['project_year'].'.';
		}

		if (!empty($peak_cashflow)) {
			$observations[] = 'Peak annual cash flow in the current profile occurs in project year '.$peak_cashflow['project_year'].'.';
		}

		$next_action = 'Continue validating the next technical and economic gate before moving further in the project lifecycle.';
		if ($current_step <= 4) {
			$next_action = 'Advance appraisal quality and decision readiness before full development commitment.';
		}
		elseif ($current_step === 5) {
			$next_action = 'Complete development readiness and confirm production start conditions.';
		}
		elseif ($current_step === 6) {
			$next_action = 'Monitor production behaviour and annual cash generation before deciding on secondary recovery.';
		}
		elseif ($current_step >= 7) {
			$next_action = 'Review late-life recovery potential and end-of-life economics before closure.';
		}

		require_once __DIR__.'/project_step_helpers.php';
		$phase_costs = project_calculate_phase_costs($project, $pfacility);

		$context = array(
			'generated_at' => date('F j, Y'),
			'project' => $project,
			'parameters' => $parameters,
			'block' => $block,
			'field' => $field,
			'pfacility' => $pfacility,
			'wells' => $wells,
			'project_id' => $project_id,
			'project_name' => report_array_get($project, 'Project_Name', 'Untitled Project'),
			'project_year' => $project_year,
			'production_year' => $production_year,
			'current_step' => $current_step,
			'current_step_label' => $current_step_label,
			'steps_completed' => $steps_completed,
			'project_budget' => $project_budget,
			'project_spending' => $project_spending,
			'projected_revenue' => $projected_revenue,
			'actual_revenue' => $actual_revenue,
			'cumul_production' => $cumul_production,
			'total_flowrate' => $total_flowrate,
			'oil_price' => $oil_price,
			'annual_profile' => $annual_profile,
			'timeline' => $timeline,
			'peak_production' => $peak_production,
			'peak_cashflow' => $peak_cashflow,
			'total_profile_production' => round($total_profile_production, 2),
			'total_profile_cashflow' => round($total_profile_cashflow, 2),
			'total_profile_spending' => round($total_profile_spending, 2),
			'total_profile_revenue' => round($total_profile_revenue, 2),
			'observations' => $observations,
			'next_action' => $next_action,
			// New E&P financial metrics
			'payback_year' => $payback_year,
			'recoverable_volume' => $recoverable_volume,
			'finding_cost_per_barrel' => $finding_cost_per_barrel,
			'exploration_cost' => $phase_costs['exploration_cost'],
			'development_cost' => $phase_costs['development_cost'],
			'production_cost' => $phase_costs['production_cost'],
		);

		$context['appendices'] = array(

			'project_parameters' => report_build_appendix_rows($project, array(
				array('label' => 'Project budget', 'unit' => '$', 'key' => 'Project_Budget'),
				array('label' => 'Project year', 'unit' => 'Years', 'key' => 'Project_year'),
				array('label' => 'Project spending', 'unit' => 'USD', 'key' => 'Project_Spending'),
				array('label' => 'Project license cost', 'unit' => 'USD', 'key' => 'Project_License_Cost'),
				array('label' => 'Project survey cost', 'unit' => 'USD', 'key' => 'Project_Survey_Cost'),
				array('label' => 'Project interpretation cost', 'unit' => 'USD', 'key' => 'Project_Interpretation_cost'),
				array('label' => 'Project road cost', 'unit' => 'USD', 'key' => 'Project_road_Cost'),
				array('label' => 'Project accommodation cost', 'unit' => 'USD', 'key' => 'Project_Accomodation_cost'),
				array('label' => 'Project oil in place', 'unit' => 'B', 'key' => 'Project_Oil_in_Place'),
				array('label' => 'Projected revenue', 'unit' => 'USD', 'key' => 'Project_Projected_Revenue'),
				array('label' => 'Project total flowrate', 'unit' => 'B/D', 'key' => 'Project_Total_Flowrate'),
				array('label' => 'Production year', 'unit' => 'Years', 'key' => 'Production_year'),
				array('label' => 'Cumulative production', 'unit' => 'B', 'key' => 'Cumul_Production'),
			)),
			'block_parameters' => report_build_appendix_rows($block, array(
				array('label' => 'Block name', 'unit' => '', 'key' => 'Block_Name'),
				array('label' => 'Block surface', 'unit' => 'Ha', 'key' => 'Block_Surface'),
				array('label' => 'License cost exploration', 'unit' => 'USD/Ha', 'key' => 'Block_License_Cost_Exp'),
				array('label' => 'License cost production', 'unit' => 'USD/Ha', 'key' => 'Block_License_cost_Prod'),
				array('label' => 'Probability to find', 'unit' => '%', 'key' => 'Probability_to_find'),
				array('label' => 'Signature bonus', 'unit' => '$', 'key' => 'Signature_Bonus'),
				array('label' => 'Block survey cost', 'unit' => 'USD/Ha', 'key' => 'Block_Survey_Cost'),
				array('label' => 'Survey interpretation cost', 'unit' => 'USD/Ha', 'key' => 'Block_Survey_interpretation_cost'),
			)),
			'field_parameters' => report_build_appendix_rows($field, array(
				array('label' => 'Field name', 'unit' => '', 'key' => 'Field_Name'),
				array('label' => 'Average TD', 'unit' => 'm', 'key' => 'Field_Average_TD'),
				array('label' => 'Road cost', 'unit' => 'USD', 'key' => 'Field_Road_Cost'),
				array('label' => 'Accommodation cost', 'unit' => 'USD', 'key' => 'Field_Accommodation_Cost'),
				array('label' => 'Drilling cost', 'unit' => 'USD/m', 'key' => 'Field_Drilling_Cost'),
				array('label' => 'Formation eval cost exp', 'unit' => 'USD/m', 'key' => 'Field_Formation_Eval_cost_Exp'),
				array('label' => 'Formation eval cost dev', 'unit' => 'USD/m', 'key' => 'Field_Formation_Eval_cost_Dev'),
				array('label' => 'Casing and cement cost', 'unit' => 'USD/m', 'key' => 'Field_Casing_Cement_cost'),
				array('label' => 'Testing cost', 'unit' => 'USD/well', 'key' => 'Field_Testing_cost'),
				array('label' => 'Completion cost', 'unit' => 'USD/well', 'key' => 'Field_Completion_Cost'),
				array('label' => 'General support cost', 'unit' => 'USD', 'key' => 'Other_Gen_Sup_Cost'),
				array('label' => 'Technical support cost', 'unit' => 'USD', 'key' => 'Other_Tech_Sup_Cost'),
				array('label' => 'Abandonment cost', 'unit' => 'USD/well', 'key' => 'Field_Abandonment_cost'),
				array('label' => 'Water saturation', 'unit' => '%', 'key' => 'Field_Water_Saturation'),
				array('label' => 'Porosity', 'unit' => '%', 'key' => 'Field_Field_Porosity'),
				array('label' => 'BO', 'unit' => 'B/STB', 'key' => 'Field_BO'),
				array('label' => 'Reservoir volume', 'unit' => 'B', 'key' => 'Field_Reservoir_Volume'),
				array('label' => 'Recovery factor', 'unit' => '%', 'key' => 'Field_Recovery_Factor'),
			)),
			'facility_parameters' => report_build_appendix_rows($pfacility, array(
				array('label' => 'Facility name', 'unit' => '', 'key' => 'Prod_Facilities_Name'),
				array('label' => 'Facility capacity', 'unit' => 'B/D', 'key' => 'Prod_Facilities_Capacity'),
				array('label' => 'Facility cost', 'unit' => '$', 'key' => 'Prod_Facilities_Cost'),
				array('label' => 'Facility status', 'unit' => '', 'key' => 'Production_Facility_Satus'),
				array('label' => 'Decommissioning cost', 'unit' => 'USD', 'key' => 'Prod_Facilities_decommissioning_cost'),
			)),
		);

		$general_parameters = array();
		foreach ($parameters as $parameter) {
			if (!is_array($parameter)) {
				continue;
			}
			$parameter_name = report_array_get($parameter, 'name', '');
			$parameter_unit = report_array_get($parameter, 'unit', '');
			$parameter_value = report_array_get($parameter, 'value', '');
			if ($parameter_name === 'Budget') {
				$parameter_unit = '$';
				$parameter_value = report_number($parameter_value, 0) * 1000000;
			}

			$general_parameters[] = array(
				'label' => report_array_get($parameter, 'display_name', report_array_get($parameter, 'name', 'Parameter')),
				'unit' => $parameter_unit,
				'value' => $parameter_value,
			);
		}

		usort($general_parameters, function ($left, $right) {
			return strcasecmp($left['label'], $right['label']);
		});

		$context['appendices']['general_parameters'] = $general_parameters;

		$well_rows = array();
		foreach ($wells as $well_key => $well) {
			$well_rows[] = array(
				'label' => report_array_get($well, 'Well_Name', $well_key),
				'unit' => 'B/Day',
				'value' => report_array_get($well, 'Well_Flowrate', ''),
			);
		}
		$context['appendices']['wells'] = $well_rows;

		return $context;
	}
}

?>
