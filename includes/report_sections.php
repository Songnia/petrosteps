<?php

if (!function_exists('report_escape')) {
	function report_escape($value) {
		return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
	}
}

if (!function_exists('report_format_number')) {
	function report_format_number($value, $decimals) {
		if ($value === '' || $value === null || !is_numeric($value)) {
			return 'N/A';
		}

		return number_format((float) $value, (int) $decimals, '.', ',');
	}
}

if (!function_exists('report_format_metric')) {
	function report_format_metric($value, $unit, $decimals) {
		$formatted = report_format_number($value, $decimals);
		if ($formatted === 'N/A') {
			return $formatted;
		}

		if ($unit === '') {
			return $formatted;
		}
		if ($unit === '$') {
			return '$'.$formatted;
		}

		return $formatted.' '.$unit;
	}
}

if (!function_exists('report_has_rows')) {
	function report_has_rows($rows) {
		return is_array($rows) && !empty($rows);
	}
}

if (!function_exists('report_render_section_header')) {
	function report_render_section_header($eyebrow, $title, $intro) {
		return '
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="section-head">
  <tr>
    <td bgcolor="#fff1e3" style="padding: 10px 12px; border-bottom: 1px solid #edd7c8;">
      <div><font color="#cf6b20"><strong>'.report_escape(strtoupper($eyebrow)).'</strong></font></div>
      <div style="margin-top: 4px;"><font color="#5a2a05" style="font-size: 16pt;"><strong>'.report_escape($title).'</strong></font></div>
      <div style="margin-top: 6px;"><font color="#7a6555">'.report_escape($intro).'</font></div>
    </td>
  </tr>
</table>';
	}
}

if (!function_exists('report_render_metric_grid')) {
	function report_render_metric_grid($items, $columns) {
		$columns = max(1, (int) $columns);
		$html = '<table width="100%" border="0" cellspacing="8" cellpadding="0" class="metric-grid">';
		$rows = array_chunk($items, $columns);

		foreach ($rows as $row) {
			$html .= '<tr>';
			for ($index = 0; $index < $columns; $index++) {
				if (isset($row[$index])) {
					$item = $row[$index];
					$html .= '
    <td valign="top">
      <table width="100%" border="1" cellspacing="0" cellpadding="10">
        <tr>
          <td bgcolor="#fff8f1">
            <div><font color="#9b7557"><strong>'.report_escape(strtoupper($item['label'])).'</strong></font></div>
            <div style="margin-top: 4px;"><font color="#4e2400" style="font-size: 13pt;"><strong>'.report_escape($item['value']).'</strong></font></div>
          </td>
        </tr>
      </table>
    </td>';
				}
				else {
					$html .= '<td>&nbsp;</td>';
				}
			}
			$html .= '</tr>';
		}

		$html .= '</table>';
		return $html;
	}
}

if (!function_exists('report_render_styles')) {
	function report_render_styles() {
		return '
<style>
@page { margin: 18mm 14mm 16mm 14mm; }
body { font-family: dejavusans, sans-serif; color: #0f172a; font-size: 9.5pt; line-height: 1.45; background: #ffffff; }
h1, h2, h3, h4, p { margin: 0; padding: 0; }
.cover-topbar { background: #0f172a; padding: 9px 20px; }
.cover-body { background: #f8fafc; padding: 22px 20px 18px; border-bottom: 3px solid #0284c7; }
.cover-eyebrow { color: #0284c7; font-size: 7.5pt; font-weight: bold; letter-spacing: 1.2px; text-transform: uppercase; }
.cover-title { color: #0f172a; font-size: 22pt; font-weight: bold; line-height: 1.1; margin-top: 6px; }
.cover-subtitle { color: #475569; font-size: 10pt; margin-top: 8px; }
.cover-badge { display: inline-block; background: #0284c7; color: #ffffff; font-size: 7.5pt; font-weight: bold; letter-spacing: 0.8px; text-transform: uppercase; padding: 4px 12px; margin-top: 12px; }
.cover-meta-table { width: 100%; border-collapse: collapse; }
.cover-meta-table td { padding: 5px 14px; font-size: 9pt; border-bottom: 1px solid #e2e8f0; }
.cover-meta-label { color: #64748b; width: 42%; }
.cover-meta-value { color: #0f172a; font-weight: bold; }
.kpi-row { width: 100%; border-collapse: separate; border-spacing: 8px; margin: 14px 0 6px; }
.kpi-cell { width: 25%; background: #ffffff; border: 1px solid #e2e8f0; border-top: 3px solid #0284c7; padding: 10px 12px; vertical-align: top; }
.kpi-cell--green { border-top-color: #059669; }
.kpi-cell--amber { border-top-color: #d97706; }
.kpi-cell--violet { border-top-color: #7c3aed; }
.kpi-label { color: #64748b; font-size: 7pt; font-weight: bold; letter-spacing: 0.8px; text-transform: uppercase; }
.kpi-value { color: #0f172a; font-size: 13pt; font-weight: bold; margin-top: 4px; line-height: 1.2; }
.kpi-unit { color: #94a3b8; font-size: 8pt; font-weight: normal; }
.sec-eyebrow { color: #0284c7; font-size: 7pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
.sec-title { color: #0f172a; font-size: 13pt; font-weight: bold; margin-top: 2px; }
.sec-intro { color: #64748b; font-size: 9pt; margin-top: 4px; }
.sec-divider { height: 2px; background: #0284c7; margin: 6px 0 12px; }
.section-wrap { margin-bottom: 16px; border: 1px solid #e2e8f0; background: #ffffff; padding: 14px 16px; }
.section-wrap--alt { background: #f8fafc; }
.data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
.data-table th { background: #0f172a; color: #ffffff; font-size: 8pt; font-weight: bold; text-align: left; padding: 7px 9px; letter-spacing: 0.4px; }
.data-table td { padding: 6px 9px; border-bottom: 1px solid #f1f5f9; font-size: 9pt; }
.data-table tr:nth-child(even) td { background: #f8fafc; }
.data-table .lbl { color: #64748b; font-weight: bold; width: 38%; }
.data-table .unit { color: #94a3b8; font-size: 8pt; width: 16%; }
.annual-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 8.5pt; }
.annual-table th { background: #1e293b; color: #fff; padding: 6px 8px; text-align: right; font-size: 8pt; }
.annual-table th:first-child { text-align: center; }
.annual-table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; text-align: right; }
.annual-table td:first-child { text-align: center; font-weight: bold; color: #0284c7; }
.annual-table tr:nth-child(even) td { background: #f0f9ff; }
.annual-table .neg { color: #dc2626; }
.annual-table .pos { color: #059669; }
.tl-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9pt; }
.tl-table td { padding: 6px 10px; border-bottom: 1px solid #f1f5f9; }
.tl-num { color: #0284c7; font-weight: bold; width: 14%; }
.tl-name { width: 54%; }
.badge-done { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 2px 8px; font-size: 7.5pt; font-weight: bold; }
.badge-cur  { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 2px 8px; font-size: 7.5pt; font-weight: bold; }
.badge-up   { background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; padding: 2px 8px; font-size: 7.5pt; font-weight: bold; }
.well-table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 8.5pt; }
.well-table th { background: #334155; color: #fff; padding: 6px 8px; text-align: left; font-size: 8pt; }
.well-table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
.well-type { color: #7c3aed; font-weight: bold; font-size: 7.5pt; }
.open-dot  { color: #059669; font-weight: bold; }
.closed-dot { color: #dc2626; font-weight: bold; }
.app-table { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 8.5pt; }
.app-table th { background: #334155; color: #fff; padding: 5px 8px; text-align: left; font-size: 7.5pt; }
.app-table td { padding: 4px 8px; border-bottom: 1px solid #f1f5f9; }
.app-table tr:nth-child(even) td { background: #f8fafc; }
.note-box { background: #eff6ff; border-left: 3px solid #0284c7; padding: 9px 12px; color: #1e40af; font-size: 8.5pt; margin-top: 10px; }
.warn-box { background: #fffbeb; border-left: 3px solid #d97706; padding: 9px 12px; color: #92400e; font-size: 8.5pt; margin-top: 8px; }
.page-break { page-break-before: always; }
.muted { color: #94a3b8; }
.empty-state { padding: 12px; border: 1px dashed #e2e8f0; color: #94a3b8; background: #f8fafc; font-size: 9pt; }
</style>';
	}
}







if (!function_exists('report_render_key_value_rows')) {
	function report_render_key_value_rows($rows) {
		$html = '';
		foreach ($rows as $row) {
			$label = isset($row['label']) ? $row['label'] : '';
			$unit = isset($row['unit']) ? $row['unit'] : '';
			$value = isset($row['value']) ? $row['value'] : '';

			$html .= '
<tr>
	<td class="lbl">'.report_escape($label).'</td>
	<td class="unit">'.report_escape($unit).'</td>
	<td><strong>'.report_escape($value).'</strong></td>
</tr>';
		}
		return $html;
	}
}

if (!function_exists('report_chart_value_to_y')) {
	function report_chart_value_to_y($value, $min_value, $max_value, $plot_top, $plot_height) {
		if ($max_value <= $min_value) {
			return $plot_top + ($plot_height / 2);
		}
		$ratio = ((float) $value - $min_value) / ($max_value - $min_value);
		return $plot_top + $plot_height - ($ratio * $plot_height);
	}
}

if (!function_exists('report_chart_format_tick')) {
	function report_chart_format_tick($value) {
		$abs = abs((float) $value);
		if ($abs >= 1000000) {
			return report_format_number($value / 1000000, 1).'M';
		}
		if ($abs >= 1000) {
			return report_format_number($value / 1000, 1).'k';
		}
		return report_format_number($value, 0);
	}
}

if (!function_exists('report_render_header')) {
	function report_render_header($ctx) {
		return '<div style="font-family:dejavusans,sans-serif;font-size:8.5pt;color:#334155;border-bottom:1px solid #e2e8f0;padding-bottom:5px;">
  <span style="font-weight:bold;color:#0f172a;">PETROSTEPS</span>
  <span style="color:#0284c7;margin-left:6px;">Project Report</span>
  <span style="float:right;color:#64748b;">'.report_escape($ctx['project_name']).'</span>
</div>';
	}
}

if (!function_exists('report_render_footer')) {
	function report_render_footer($ctx) {
		return '<div style="font-family:dejavusans,sans-serif;font-size:8pt;color:#94a3b8;border-top:1px solid #e2e8f0;padding-top:5px;">
  <span>Petrosteps — Internal E&amp;P Project Dossier</span>
  <span style="float:right;">Page {PAGENO}</span>
</div>';
	}
}

if (!function_exists('report_render_cover_section')) {
	function report_render_cover_section($ctx) {
		$finding_cost_str = ($ctx['finding_cost_per_barrel'] !== null) ? report_format_metric($ctx['finding_cost_per_barrel'], '$/B', 2) : 'N/A';
		$payback_str = ($ctx['payback_year'] !== null) ? 'Year '.$ctx['payback_year'] : 'TBD';

		return '
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 16px;">
  <tr>
    <td class="cover-topbar">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style="color: #ffffff; font-size: 8pt; font-weight: bold; letter-spacing: 1px;">PETROSTEPS UPSTREAM EXECUTIVE DOSSIER</td>
          <td align="right" style="color: #94a3b8; font-size: 8pt;">CONFIDENTIAL / INTERNAL REVIEW</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class="cover-body">
      <div class="cover-eyebrow">UPSTREAM PROJECT EVALUATION &amp; DECISION REPORT</div>
      <div class="cover-title">'.report_escape($ctx['project_name']).'</div>
      <div class="cover-subtitle">Generated on '.report_escape($ctx['generated_at']).' &bull; Prepared for Technical &amp; Executive Portfolio Review</div>
      <div class="cover-badge">STEP '.$ctx['current_step'].' — '.report_escape(strtoupper($ctx['current_step_label'])).'</div>
    </td>
  </tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="section-wrap section-wrap--alt" style="margin-bottom: 14px;">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="0" class="cover-meta-table">
        <tr>
          <td class="cover-meta-label">Project Identifier</td>
          <td class="cover-meta-value">PRJ-'.report_escape($ctx['project_id']).'</td>
          <td class="cover-meta-label">Oil Benchmark Price</td>
          <td class="cover-meta-value">'.report_format_metric($ctx['oil_price'], '$/B', 2).'</td>
        </tr>
        <tr>
          <td class="cover-meta-label">Assigned Block / Field</td>
          <td class="cover-meta-value">'.report_escape(isset($ctx['block']['Block_Name']) ? $ctx['block']['Block_Name'] : 'N/A').' / '.report_escape(isset($ctx['field']['Field_Name']) ? $ctx['field']['Field_Name'] : 'N/A').'</td>
          <td class="cover-meta-label">Recoverable Volume</td>
          <td class="cover-meta-value">'.report_format_metric($ctx['recoverable_volume'], 'Mstb', 2).'</td>
        </tr>
        <tr>
          <td class="cover-meta-label">Project Budget (CAPEX)</td>
          <td class="cover-meta-value">'.report_format_metric($ctx['project_budget'], '$', 0).'</td>
          <td class="cover-meta-label">Est. Finding Cost</td>
          <td class="cover-meta-value">'.$finding_cost_str.'</td>
        </tr>
      </table>
    </td>
  </tr>
</table>';
	}
}

if (!function_exists('report_render_executive_summary_section')) {
	function report_render_executive_summary_section($ctx) {
		$payback_val = ($ctx['payback_year'] !== null) ? 'Year '.$ctx['payback_year'] : 'TBD';
		$finding_cost_val = ($ctx['finding_cost_per_barrel'] !== null) ? report_format_metric($ctx['finding_cost_per_barrel'], '$/B', 2) : 'N/A';

		return '
<div style="margin-bottom: 14px;">
  <div class="sec-eyebrow">Decision Snapshot</div>
  <div class="sec-title">Executive Key Performance Indicators</div>
  <div class="sec-intro">High-level physical and economic metrics reflecting the current project maturity.</div>
  <div class="sec-divider"></div>

  <table width="100%" border="0" cellspacing="8" cellpadding="0" class="kpi-row">
    <tr>
      <td class="kpi-cell">
        <div class="kpi-label">Project Budget</div>
        <div class="kpi-value">'.report_format_metric($ctx['project_budget'], '$', 0).'</div>
      </td>
      <td class="kpi-cell kpi-cell--green">
        <div class="kpi-label">Cumul. Production</div>
        <div class="kpi-value">'.report_format_metric($ctx['cumul_production'], 'B', 0).'</div>
      </td>
      <td class="kpi-cell kpi-cell--amber">
        <div class="kpi-label">Current Flowrate</div>
        <div class="kpi-value">'.report_format_metric($ctx['total_flowrate'], 'B/D', 1).'</div>
      </td>
      <td class="kpi-cell kpi-cell--violet">
        <div class="kpi-label">Payback / Finding Cost</div>
        <div class="kpi-value">'.$payback_val.' <span class="kpi-unit">('.$finding_cost_val.')</span></div>
      </td>
    </tr>
  </table>

  <table width="100%" border="0" cellspacing="8" cellpadding="0" class="kpi-row" style="margin-top: 4px;">
    <tr>
      <td class="kpi-cell kpi-cell--green">
        <div class="kpi-label">Exploration Cost</div>
        <div class="kpi-value">'.report_format_metric($ctx['exploration_cost'], '$', 0).'</div>
      </td>
      <td class="kpi-cell kpi-cell--amber">
        <div class="kpi-label">Development Cost</div>
        <div class="kpi-value">'.report_format_metric($ctx['development_cost'], '$', 0).'</div>
      </td>
      <td class="kpi-cell kpi-cell--violet">
        <div class="kpi-label">Production Cost</div>
        <div class="kpi-value">'.report_format_metric($ctx['production_cost'], '$', 0).'</div>
      </td>
      <td class="kpi-cell">
        <div class="kpi-label">Total Spendings</div>
        <div class="kpi-value">'.report_format_metric($ctx['project_spending'], '$', 0).'</div>
      </td>
    </tr>
  </table>
</div>';
	}
}

if (!function_exists('report_render_combined_ep_chart')) {
	function report_render_combined_ep_chart($rows, $payback_year = null) {
		if (!report_has_rows($rows)) {
			return '<div class="empty-state">No annual profile data available to render the combined E&amp;P chart.</div>';
		}

		$width = 680;
		$height = 230;
		$plot_left = 58;
		$plot_right = 58;
		$plot_top = 22;
		$plot_bottom = 36;
		$plot_width = $width - $plot_left - $plot_right;
		$plot_height = $height - $plot_top - $plot_bottom;

		$cashflows = array();
		$productions = array();
		foreach ($rows as $r) {
			$cashflows[] = isset($r['cashflow']) ? (float)$r['cashflow'] : 0.0;
			$productions[] = isset($r['production']) ? (float)$r['production'] : 0.0;
		}

		$min_cf = min($cashflows);
		$max_cf = max($cashflows);
		if ($min_cf > 0) $min_cf = 0;
		if ($max_cf < 0) $max_cf = 0;
		if ($min_cf === $max_cf) $max_cf = 1.0;
		$pad_cf = ($max_cf - $min_cf) * 0.1;
		$max_cf += $pad_cf; $min_cf -= $pad_cf;

		$min_prod = 0;
		$max_prod = max($productions);
		if ($max_prod <= 0) $max_prod = 1.0;
		$max_prod *= 1.15;

		$baseline_y = report_chart_value_to_y(0, $min_cf, $max_cf, $plot_top, $plot_height);

		$count = count($rows);
		$x_step = ($count > 1) ? ($plot_width / ($count - 1)) : 0;

		$prod_points = array();
		$cf_bars = '';
		$x_labels = '';

		for ($i = 0; $i < $count; $i++) {
			$r = $rows[$i];
			$x = $plot_left + (($count > 1) ? ($i * $x_step) : ($plot_width / 2));
			$cf_val = $cashflows[$i];
			$prod_val = $productions[$i];

			// Cash flow bar
			$cf_y = report_chart_value_to_y($cf_val, $min_cf, $max_cf, $plot_top, $plot_height);
			$bar_w = max(4, min(18, ($plot_width / $count) * 0.5));
			$bar_x = $x - ($bar_w / 2);
			$bar_top = min($cf_y, $baseline_y);
			$bar_h = max(2, abs($cf_y - $baseline_y));
			$bar_color = ($cf_val >= 0) ? '#059669' : '#dc2626';
			$cf_bars .= '<rect x="'.round($bar_x, 2).'" y="'.round($bar_top, 2).'" width="'.round($bar_w, 2).'" height="'.round($bar_h, 2).'" fill="'.$bar_color.'" opacity="0.78" rx="2" />';

			// Production line point
			$prod_y = report_chart_value_to_y($prod_val, $min_prod, $max_prod, $plot_top, $plot_height);
			$prod_points[] = round($x, 2).','.round($prod_y, 2);

			$yr = isset($r['project_year']) ? $r['project_year'] : ($i + 1);
			if ($count <= 12 || $i % ceil($count / 10) === 0) {
				$x_labels .= '<text x="'.round($x, 2).'" y="'.($height - 10).'" text-anchor="middle" font-size="8.5" fill="#64748b">Y'.$yr.'</text>';
			}
		}

		// Y-axis grid & labels (Left: Cashflow)
		$grid = '';
		$cf_axis_labels = '';
		for ($t = 0; $t <= 4; $t++) {
			$val = $max_cf - (($max_cf - $min_cf) * ($t / 4));
			$y = report_chart_value_to_y($val, $min_cf, $max_cf, $plot_top, $plot_height);
			$grid .= '<line x1="'.$plot_left.'" y1="'.round($y, 2).'" x2="'.($plot_left + $plot_width).'" y2="'.round($y, 2).'" stroke="#f1f5f9" stroke-width="1" />';
			$cf_axis_labels .= '<text x="'.($plot_left - 6).'" y="'.(round($y, 2) + 3).'" text-anchor="end" font-size="8" fill="#64748b">'.report_chart_format_tick($val).'</text>';
		}

		// Y-axis labels (Right: Production)
		$prod_axis_labels = '';
		for ($t = 0; $t <= 4; $t++) {
			$val = $max_prod - (($max_prod - $min_prod) * ($t / 4));
			$y = report_chart_value_to_y($val, $min_prod, $max_prod, $plot_top, $plot_height);
			$prod_axis_labels .= '<text x="'.($plot_left + $plot_width + 6).'" y="'.(round($y, 2) + 3).'" text-anchor="start" font-size="8" fill="#0284c7">'.report_chart_format_tick($val).'</text>';
		}

		return '
<div style="margin: 10px 0 16px;">
  <svg width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'" xmlns="http://www.w3.org/2000/svg">
    <rect x="0" y="0" width="'.$width.'" height="'.$height.'" fill="#ffffff" rx="6" stroke="#e2e8f0" stroke-width="1" />
    '.$grid.'
    <line x1="'.$plot_left.'" y1="'.round($baseline_y, 2).'" x2="'.($plot_left + $plot_width).'" y2="'.round($baseline_y, 2).'" stroke="#cbd5e1" stroke-width="1.5" />
    '.$cf_bars.'
    <polyline points="'.implode(' ', $prod_points).'" fill="none" stroke="#0284c7" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
    '.$cf_axis_labels.'
    '.$prod_axis_labels.'
    '.$x_labels.'
    <text x="16" y="'.($plot_top + ($plot_height / 2)).'" text-anchor="middle" font-size="8.5" font-weight="bold" fill="#64748b" transform="rotate(-90 16 '.($plot_top + ($plot_height / 2)).')">Net Cash Flow (USD)</text>
    <text x="'.($width - 14).'" y="'.($plot_top + ($plot_height / 2)).'" text-anchor="middle" font-size="8.5" font-weight="bold" fill="#0284c7" transform="rotate(90 '.($width - 14).' '.($plot_top + ($plot_height / 2)).')">Production (B/D)</text>
  </svg>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 4px;">
    <tr>
      <td style="font-size: 8pt; color: #64748b;">
        <span style="display:inline-block; width:10px; height:10px; background:#059669; border-radius:2px; margin-right:4px;"></span> Positive Cash Flow &nbsp;&nbsp;
        <span style="display:inline-block; width:10px; height:10px; background:#dc2626; border-radius:2px; margin-right:4px;"></span> Negative Cash Flow &nbsp;&nbsp;
        <span style="display:inline-block; width:14px; height:3px; background:#0284c7; margin-right:4px; vertical-align:middle;"></span> Production Rate (B/D)
      </td>
    </tr>
  </table>
</div>';
	}
}

if (!function_exists('report_render_cumulative_cashflow_chart')) {
	function report_render_cumulative_cashflow_chart($rows, $payback_year = null) {
		if (!report_has_rows($rows)) {
			return '<div class="empty-state">No annual profile data available to render the cumulative cash flow chart.</div>';
		}

		$width = 680;
		$height = 220;
		$plot_left = 65;
		$plot_right = 35;
		$plot_top = 24;
		$plot_bottom = 36;
		$plot_width = $width - $plot_left - $plot_right;
		$plot_height = $height - $plot_top - $plot_bottom;

		$cum_cashflows = array();
		foreach ($rows as $r) {
			$cum_cashflows[] = isset($r['cumulative_cashflow']) ? (float)$r['cumulative_cashflow'] : 0.0;
		}

		$min_cum = min($cum_cashflows);
		$max_cum = max($cum_cashflows);
		if ($min_cum > 0) $min_cum = 0;
		if ($max_cum < 0) $max_cum = 0;
		if ($min_cum === $max_cum) $max_cum = 1.0;

		$pad = ($max_cum - $min_cum) * 0.12;
		$max_cum += $pad;
		$min_cum -= $pad;

		$baseline_y = report_chart_value_to_y(0, $min_cum, $max_cum, $plot_top, $plot_height);

		$count = count($rows);
		$x_step = ($count > 1) ? ($plot_width / ($count - 1)) : 0;

		$cum_points = array();
		$area_points = array();
		$circles = '';
		$x_labels = '';
		$payback_x = null;

		$first_x = $plot_left;
		$area_points[] = round($first_x, 2).','.round($baseline_y, 2);

		for ($i = 0; $i < $count; $i++) {
			$r = $rows[$i];
			$x = $plot_left + (($count > 1) ? ($i * $x_step) : ($plot_width / 2));
			$val = $cum_cashflows[$i];
			$yr = isset($r['project_year']) ? (int)$r['project_year'] : ($i + 1);

			$y = report_chart_value_to_y($val, $min_cum, $max_cum, $plot_top, $plot_height);
			$pt = round($x, 2).','.round($y, 2);
			$cum_points[] = $pt;
			$area_points[] = $pt;

			if ($payback_year !== null && $yr === (int)$payback_year) {
				$payback_x = $x;
			}

			$circle_color = ($val >= 0) ? '#059669' : '#dc2626';
			$circles .= '<circle cx="'.round($x, 2).'" cy="'.round($y, 2).'" r="3.5" fill="'.$circle_color.'" stroke="#ffffff" stroke-width="1.5" />';

			if ($count <= 12 || $i % ceil($count / 10) === 0) {
				$x_labels .= '<text x="'.round($x, 2).'" y="'.($height - 10).'" text-anchor="middle" font-size="8.5" fill="#64748b">Y'.$yr.'</text>';
			}
		}

		$last_x = $plot_left + (($count > 1) ? (($count - 1) * $x_step) : ($plot_width / 2));
		$area_points[] = round($last_x, 2).','.round($baseline_y, 2);

		$grid = '';
		$y_axis_labels = '';
		for ($t = 0; $t <= 4; $t++) {
			$val = $max_cum - (($max_cum - $min_cum) * ($t / 4));
			$y = report_chart_value_to_y($val, $min_cum, $max_cum, $plot_top, $plot_height);
			$grid .= '<line x1="'.$plot_left.'" y1="'.round($y, 2).'" x2="'.($plot_left + $plot_width).'" y2="'.round($y, 2).'" stroke="#f1f5f9" stroke-width="1" />';
			$y_axis_labels .= '<text x="'.($plot_left - 6).'" y="'.(round($y, 2) + 3).'" text-anchor="end" font-size="8" fill="#64748b">'.report_chart_format_tick($val).'</text>';
		}

		$payback_line = '';
		if ($payback_x !== null) {
			$payback_line = '
				<line x1="'.round($payback_x, 2).'" y1="'.$plot_top.'" x2="'.round($payback_x, 2).'" y2="'.($plot_top + $plot_height).'" stroke="#d97706" stroke-width="1.5" stroke-dasharray="4 3" />
				<rect x="'.(round($payback_x, 2) - 45).'" y="'.($plot_top + 2).'" width="90" height="16" fill="#fffbeb" stroke="#fde68a" rx="3" />
				<text x="'.round($payback_x, 2).'" y="'.($plot_top + 13).'" text-anchor="middle" font-size="7.5" font-weight="bold" fill="#d97706">Payback Year '.$payback_year.'</text>';
		}

		return '
<div style="margin: 14px 0 16px;">
  <div style="font-size: 9.5pt; font-weight: bold; color: #1e293b; margin-bottom: 6px;">2. Cumulative Cash Flow Profile &amp; Payback Horizon</div>
  <svg width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'" xmlns="http://www.w3.org/2000/svg">
    <rect x="0" y="0" width="'.$width.'" height="'.$height.'" fill="#ffffff" rx="6" stroke="#e2e8f0" stroke-width="1" />
    '.$grid.'
    <line x1="'.$plot_left.'" y1="'.round($baseline_y, 2).'" x2="'.($plot_left + $plot_width).'" y2="'.round($baseline_y, 2).'" stroke="#cbd5e1" stroke-width="1.5" />
    <polygon points="'.implode(' ', $area_points).'" fill="#0284c7" opacity="0.12" />
    <polyline points="'.implode(' ', $cum_points).'" fill="none" stroke="#0284c7" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
    '.$circles.'
    '.$payback_line.'
    '.$y_axis_labels.'
    '.$x_labels.'
    <text x="16" y="'.($plot_top + ($plot_height / 2)).'" text-anchor="middle" font-size="8.5" font-weight="bold" fill="#64748b" transform="rotate(-90 16 '.($plot_top + ($plot_height / 2)).')">Cumulative Cash Flow (USD)</text>
  </svg>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 4px;">
    <tr>
      <td style="font-size: 8pt; color: #64748b;">
        <span style="display:inline-block; width:14px; height:3px; background:#0284c7; margin-right:4px; vertical-align:middle;"></span> Cumulative Cash Flow (USD) &nbsp;&nbsp;
        <span style="display:inline-block; width:10px; height:10px; background:#059669; border-radius:50%; margin-right:4px;"></span> Positive Cumulative Balance &nbsp;&nbsp;
        <span style="display:inline-block; width:10px; height:10px; background:#dc2626; border-radius:50%; margin-right:4px;"></span> Negative Cumulative Balance
      </td>
    </tr>
  </table>
</div>';
	}
}

if (!function_exists('report_render_profile_section')) {
	function report_render_profile_section($ctx) {
		$html = '
<div class="section-wrap page-break">
  <div class="sec-eyebrow">Financial &amp; Production Profile</div>
  <div class="sec-title">Financial Dynamics &amp; Cash Flow Analysis</div>
  <div class="sec-intro">Annual net cash flow, cumulative cash flow profile, and production rate evolution.</div>
  <div class="sec-divider"></div>

  <div style="font-size: 9.5pt; font-weight: bold; color: #1e293b; margin-bottom: 4px;">1. Combined Production &amp; Net Annual Cash Flow</div>
  '.report_render_combined_ep_chart($ctx['annual_profile'], $ctx['payback_year']).'

  '.report_render_cumulative_cashflow_chart($ctx['annual_profile'], $ctx['payback_year']);

		if (report_has_rows($ctx['annual_profile'])) {
			$html .= '
  <div class="sec-title" style="font-size: 11pt; margin-top: 14px;">Annual Financial &amp; Operational Breakdown</div>
  <table class="annual-table">
    <thead>
      <tr>
        <th>Year</th>
        <th>Production (B)</th>
        <th>Revenue (USD)</th>
        <th>Spending (USD)</th>
        <th>Cash Flow (USD)</th>
        <th>Cumul. Cash Flow</th>
      </tr>
    </thead>
    <tbody>';
			foreach ($ctx['annual_profile'] as $row) {
				$cf = (float) $row['cashflow'];
				$cf_class = ($cf >= 0) ? 'pos' : 'neg';
				$cum_cf = (float) $row['cumulative_cashflow'];
				$cum_class = ($cum_cf >= 0) ? 'pos' : 'neg';

				$html .= '
      <tr>
        <td>Y'.report_escape($row['project_year']).'</td>
        <td>'.report_format_number($row['production'], 0).'</td>
        <td>'.report_format_number($row['revenue'], 0).'</td>
        <td>'.report_format_number($row['spending'], 0).'</td>
        <td class="'.$cf_class.'">'.report_format_number($row['cashflow'], 0).'</td>
        <td class="'.$cum_class.'">'.report_format_number($row['cumulative_cashflow'], 0).'</td>
      </tr>';
			}
			$html .= '
    </tbody>
  </table>';
		}

		$html .= '</div>';
		return $html;
	}
}

if (!function_exists('report_render_context_section')) {
	function report_render_context_section($ctx) {
		$rows = array(
			array('label' => 'Project Name', 'unit' => '', 'value' => $ctx['project_name']),
			array('label' => 'Current Lifecycle Step', 'unit' => '', 'value' => 'Step '.$ctx['current_step'].' — '.$ctx['current_step_label']),
			array('label' => 'Steps Completed', 'unit' => '', 'value' => $ctx['steps_completed'].' / 8'),
			array('label' => 'Target Block', 'unit' => '', 'value' => isset($ctx['block']['Block_Name']) ? $ctx['block']['Block_Name'] : 'N/A'),
			array('label' => 'Target Field', 'unit' => '', 'value' => isset($ctx['field']['Field_Name']) ? $ctx['field']['Field_Name'] : 'N/A'),
			array('label' => 'Production Facility (CPF)', 'unit' => '', 'value' => isset($ctx['pfacility']['Prod_Facilities_Name']) ? $ctx['pfacility']['Prod_Facilities_Name'] : 'Not Configured'),
			array('label' => 'Production Start Year', 'unit' => 'Year', 'value' => ($ctx['production_year'] > 0) ? $ctx['production_year'] : 'TBD'),
			array('label' => 'Evaluation Horizon', 'unit' => 'Years', 'value' => $ctx['project_year']),
		);

		return '
<div class="section-wrap">
  <div class="sec-eyebrow">Project Frame</div>
  <div class="sec-title">Project Context &amp; Operational Scope</div>
  <div class="sec-intro">General parameters and administrative container linked to the simulation model.</div>
  <div class="sec-divider"></div>

  <table class="data-table">
    '.report_render_key_value_rows($rows).'
  </table>
</div>';
	}
}

if (!function_exists('report_render_timeline_section')) {
	function report_render_timeline_section($ctx) {
		$html = '
<div class="section-wrap">
  <div class="sec-eyebrow">Lifecycle Governance</div>
  <div class="sec-title">Workflow Step Progress</div>
  <div class="sec-intro">Status tracking across the 8 standard Petrosteps upstream decision gates.</div>
  <div class="sec-divider"></div>

  <table class="tl-table">
    <tbody>';

		foreach ($ctx['timeline'] as $item) {
			$st = strtolower((string)$item['status']);
			$badge_cls = 'badge-up';
			if ($st === 'completed') $badge_cls = 'badge-done';
			elseif ($st === 'current') $badge_cls = 'badge-cur';

			$html .= '
      <tr>
        <td class="tl-num">STEP '.report_escape($item['number']).'</td>
        <td class="tl-name"><strong>'.report_escape($item['name']).'</strong></td>
        <td><span class="'.$badge_cls.'">'.report_escape(strtoupper($item['status'])).'</span></td>
      </tr>';
		}

		$exp_share = ($ctx['project_spending'] > 0) ? round(($ctx['exploration_cost'] / $ctx['project_spending']) * 100, 1) : 0;
		$dev_share = ($ctx['project_spending'] > 0) ? round(($ctx['development_cost'] / $ctx['project_spending']) * 100, 1) : 0;
		$prod_share = ($ctx['project_spending'] > 0) ? round(($ctx['production_cost'] / $ctx['project_spending']) * 100, 1) : 0;

		$html .= '
    </tbody>
  </table>

  <div style="margin-top: 16px;">
    <div class="sec-eyebrow">Cost Distribution</div>
    <div class="sec-title">Lifecycle Cost Structure</div>
    <div class="sec-intro">CAPEX and OPEX allocation per development stage.</div>
    <div class="sec-divider"></div>

    <table class="data-table">
      <thead>
        <tr>
          <th>Lifecycle Phase</th>
          <th>Expense Category</th>
          <th>Cost Value ($)</th>
          <th>Share (%)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="lbl">Exploration &amp; Appraisal (Steps 1–4)</td>
          <td>CAPEX (Exploration)</td>
          <td><strong>'.report_format_metric($ctx['exploration_cost'], '$', 0).'</strong></td>
          <td>'.$exp_share.'%</td>
        </tr>
        <tr>
          <td class="lbl">Field Development (Step 5)</td>
          <td>CAPEX (Development)</td>
          <td><strong>'.report_format_metric($ctx['development_cost'], '$', 0).'</strong></td>
          <td>'.$dev_share.'%</td>
        </tr>
        <tr>
          <td class="lbl">Production &amp; Abandonment (Steps 6–8)</td>
          <td>OPEX &amp; Abandonment</td>
          <td><strong>'.report_format_metric($ctx['production_cost'], '$', 0).'</strong></td>
          <td>'.$prod_share.'%</td>
        </tr>
        <tr style="background:#f8fafc; font-weight:bold;">
          <td class="lbl">Total Cumulative Spendings</td>
          <td>Total Investment</td>
          <td><strong>'.report_format_metric($ctx['project_spending'], '$', 0).'</strong></td>
          <td>100%</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>';
		return $html;
	}
}

if (!function_exists('report_render_wells_matrix_section')) {
	function report_render_wells_matrix_section($ctx) {
		if (empty($ctx['wells'])) {
			return '
<div class="section-wrap">
  <div class="sec-eyebrow">Subsurface Assets</div>
  <div class="sec-title">Wells &amp; Subsurface Infrastructure</div>
  <div class="empty-state">No wells have been drilled or logged for this project context yet.</div>
</div>';
		}

		$html = '
<div class="section-wrap">
  <div class="sec-eyebrow">Subsurface Assets</div>
  <div class="sec-title">Wells Infrastructure &amp; Status</div>
  <div class="sec-intro">Logged exploration, appraisal, and development wells.</div>
  <div class="sec-divider"></div>

  <table class="well-table">
    <thead>
      <tr>
        <th>Well Name</th>
        <th>Category</th>
        <th>Depth</th>
        <th>Flowrate</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>';

		foreach ($ctx['wells'] as $well) {
			$status = report_array_get($well, 'Well_Status', report_array_get($well, 'Status', 'Active'));
			$dot = (strtolower($status) === 'open' || strtolower($status) === 'active') ? '<span class="open-dot">&bull; OPEN</span>' : '<span class="closed-dot">&bull; CLOSED</span>';
			$type = report_array_get($well, 'Well_Type', 'Standard Well');
			$depth = report_format_metric(report_array_get($well, 'Well_Depth', 0), 'm', 0);
			$flow = report_format_metric(report_array_get($well, 'Well_Flowrate', 0), 'B/D', 1);

			$html .= '
      <tr>
        <td><strong>'.report_escape(report_array_get($well, 'Well_Name', 'Well')).'</strong></td>
        <td><span class="well-type">'.report_escape($type).'</span></td>
        <td>'.$depth.'</td>
        <td>'.$flow.'</td>
        <td>'.$dot.'</td>
      </tr>';
		}

		$html .= '
    </tbody>
  </table>
</div>';
		return $html;
	}
}

if (!function_exists('report_render_observations_section')) {
	function report_render_observations_section($ctx) {
		$html = '
<div class="section-wrap">
  <div class="sec-eyebrow">Executive Synthesis</div>
  <div class="sec-title">Key Observations &amp; Action Plan</div>
  <div class="sec-divider"></div>

  <ul style="padding-left: 18px; margin: 0 0 10px 0; font-size: 9pt; color: #334155;">';
		foreach ($ctx['observations'] as $obs) {
			$html .= '<li style="margin-bottom: 4px;">'.report_escape($obs).'</li>';
		}
		$html .= '</ul>

  <div class="note-box">
    <strong>Recommended Next Action:</strong> '.report_escape($ctx['next_action']).'
  </div>
</div>';
		return $html;
	}
}

if (!function_exists('report_render_appendix_section')) {
	function report_render_appendix_section($title, $rows) {
		if (!report_has_rows($rows)) return '';

		$html = '
<div style="margin-bottom: 12px;">
  <div style="font-size: 9.5pt; font-weight: bold; color: #1e293b; margin-bottom: 4px;">'.report_escape($title).'</div>
  <table class="app-table">
    <thead>
      <tr>
        <th width="45%">Parameter</th>
        <th width="18%">Unit</th>
        <th width="37%">Value</th>
      </tr>
    </thead>
    <tbody>';
		foreach ($rows as $r) {
			$html .= '
      <tr>
        <td><strong>'.report_escape(isset($r['label'])?$r['label']:'').'</strong></td>
        <td class="muted">'.report_escape(isset($r['unit'])?$r['unit']:'').'</td>
        <td>'.report_escape(isset($r['value'])?$r['value']:'').'</td>
      </tr>';
		}
		$html .= '
    </tbody>
  </table>
</div>';
		return $html;
	}
}

if (!function_exists('report_render_appendices_section')) {
	function report_render_appendices_section($ctx) {
		$html = '
<div class="section-wrap page-break">
  <div class="sec-eyebrow">Data Archive</div>
  <div class="sec-title">Technical Appendices</div>
  <div class="sec-intro">Granular subsurface, block, field, and facility record parameters.</div>
  <div class="sec-divider"></div>';

		$html .= report_render_appendix_section('General Simulation Parameters', $ctx['appendices']['general_parameters']);
		$html .= report_render_appendix_section('Project Model Data', $ctx['appendices']['project_parameters']);
		$html .= report_render_appendix_section('Block Technical Summary', $ctx['appendices']['block_parameters']);
		$html .= report_render_appendix_section('Field &amp; Reservoir Parameters', $ctx['appendices']['field_parameters']);
		$html .= report_render_appendix_section('Production Facility Parameters', $ctx['appendices']['facility_parameters']);

		$html .= '</div>';
		return $html;
	}
}

if (!function_exists('report_render_document_body')) {
	function report_render_document_body($ctx) {
		return '
<div>
  '.report_render_cover_section($ctx).'
  '.report_render_executive_summary_section($ctx).'
  '.report_render_context_section($ctx).'
  '.report_render_timeline_section($ctx).'
  '.report_render_profile_section($ctx).'
  '.report_render_wells_matrix_section($ctx).'
  '.report_render_observations_section($ctx).'
  '.report_render_appendices_section($ctx).'
</div>';
	}
}

if (!function_exists('report_render_document')) {
	function report_render_document($ctx) {
		return '
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  '.report_render_styles().'
</head>
<body>
  '.report_render_document_body($ctx).'
</body>
</html>';
	}
}
