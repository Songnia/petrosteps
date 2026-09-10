<?php
if (!function_exists('build_project_graph_data')) {
	function build_project_graph_data($project_cash_flow, $oil_price) {
		$oil_price = (float)$oil_price;
		$graph_by_year = array();
		$current_project_year = 0;
		$previous_absolute_year = 0;
		$previous_cumulative_flow = 0.0;
		$previous_spending = 0.0;
		$has_production_started = false;

		foreach ($project_cash_flow as $key => $flow) {
			$raw_year = isset($flow['Project_year']) ? (int)$flow['Project_year'] : 0;

			$current_cumulative_flow = isset($flow['Project_Cumulative_Flow']) ? (float)$flow['Project_Cumulative_Flow'] : 0.0;
			if (
				$previous_cumulative_flow > 0
				&& $oil_price > 0
				&& $raw_year === 1
				&& ($current_cumulative_flow / $previous_cumulative_flow) > max(5, $oil_price / 3)
			) {
				// Old step 7 rows stored revenue where cumulative production was expected.
				$current_cumulative_flow = $current_cumulative_flow / $oil_price;
			}

			$current_spending = isset($flow['Project_Spending']) ? (float)$flow['Project_Spending'] : 0.0;

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
					// Legacy yearly rows used +1 increments instead of absolute project years.
					$current_absolute_year = $previous_absolute_year + max($raw_year, 1);
				}
			}
			elseif (!$has_production_started) {
				// Pre-production rows may reuse short step durations (1, 2, ...).
				$current_absolute_year = max($previous_absolute_year, $raw_year);
			}
			else {
				// Post-production admin rows should affect the last visible year, not extend the scale.
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

			$step_spendings = $current_spending - $previous_spending;
			$cashflow = ($step_production * $oil_price) - $step_spendings;
			$per_year_production = $step_production / $year_span;
			$per_year_cashflow = $cashflow / $year_span;
			$start_year = ($current_absolute_year > $previous_absolute_year) ? ($previous_absolute_year + 1) : $current_absolute_year;

			if ($start_year < 1) {
				$start_year = 1;
			}

			for ($year = $start_year; $year <= $current_absolute_year; $year++) {
				if (!isset($graph_by_year[$year])) {
					$graph_by_year[$year] = array(
						'project_year' => $year,
						'cashflow' => 0.0,
						'production' => 0.0,
					);
				}

				$graph_by_year[$year]['cashflow'] += round($per_year_cashflow, 2);
				$graph_by_year[$year]['production'] += round($per_year_production, 2);
			}

			$current_project_year = max($current_project_year, $current_absolute_year);
			if ($production_increased) {
				$has_production_started = true;
			}
			$previous_absolute_year = $current_absolute_year;
			$previous_cumulative_flow = $current_cumulative_flow;
			$previous_spending = $current_spending;
		}

		ksort($graph_by_year);
		return array(array_values($graph_by_year), $current_project_year);
	}
}
