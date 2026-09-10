<?php

include('includes/db.class.php');

if (!isset($_SESSION['project']['project_id']) || (int) $_SESSION['project']['project_id'] <= 0) {
	header('Content-Type: text/html; charset=utf-8');
	echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Petrosteps Report</title></head><body style="font-family: Arial, sans-serif; padding: 32px;"><h2>No active project selected</h2><p>Please open a project first, then generate the report again.</p></body></html>';
	exit;
}

include('includes/user_common.php');
include('includes/project_step_helpers.php');
include('includes/report_data.php');

$report_context = build_report_context(array(
	'db' => $db,
	'project' => $project,
	'parameters' => $parameters,
	'block' => isset($block) && is_array($block) ? $block : array(),
	'field' => isset($field) && is_array($field) ? $field : array(),
	'pfacility' => isset($pfacility) && is_array($pfacility) ? $pfacility : array(),
	'session_project' => isset($_SESSION['project']) && is_array($_SESSION['project']) ? $_SESSION['project'] : array(),
	'Field_ExpWell1' => isset($Field_ExpWell1) && is_array($Field_ExpWell1) ? $Field_ExpWell1 : array(),
	'Field_ExpWell2' => isset($Field_ExpWell2) && is_array($Field_ExpWell2) ? $Field_ExpWell2 : array(),
	'Field_AppWell1' => isset($Field_AppWell1) && is_array($Field_AppWell1) ? $Field_AppWell1 : array(),
	'Field_AppWell2' => isset($Field_AppWell2) && is_array($Field_AppWell2) ? $Field_AppWell2 : array(),
	'Field_DevWell1' => isset($Field_DevWell1) && is_array($Field_DevWell1) ? $Field_DevWell1 : array(),
	'Field_DevWell2' => isset($Field_DevWell2) && is_array($Field_DevWell2) ? $Field_DevWell2 : array(),
	'Field_DevWell3' => isset($Field_DevWell3) && is_array($Field_DevWell3) ? $Field_DevWell3 : array(),
));

// Prepare Chart Data
$chart_labels = array();
$chart_cashflow = array();
$chart_cumul_cashflow = array();
$chart_production = array();
$chart_spending = array();
$chart_revenue = array();

if (isset($report_context['annual_profile']) && is_array($report_context['annual_profile'])) {
	foreach ($report_context['annual_profile'] as $row) {
		$chart_labels[] = 'Year '.$row['project_year'];
		$chart_cashflow[] = (float) $row['cashflow'];
		$chart_cumul_cashflow[] = (float) $row['cumulative_cashflow'];
		$chart_production[] = (float) $row['production'];
		$chart_spending[] = (float) $row['spending'];
		$chart_revenue[] = (float) $row['revenue'];
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Executive Report - <?php echo htmlspecialchars($report_context['project_name']); ?> - PetroSteps</title>
  
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
  
  <!-- Chart.js -->
  <script src="js/libs/chart.umd.js"></script>

  <style>
    :root {
      --primary: #0f172a;
      --primary-accent: #0284c7;
      --secondary-accent: #6366f1;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --bg-body: #f8fafc;
      --bg-card: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
      --shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: var(--bg-body);
      color: var(--text-main);
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
      padding-bottom: 60px;
    }

    /* Screen Top Action Toolbar */
    /* Screen Top Action Toolbar */
    .screen-toolbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: rgba(15, 23, 42, 0.95);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      color: #ffffff;
      padding: 12px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid rgba(255,255,255,0.1);
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      gap: 12px;
    }

    .toolbar-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      min-width: 0;
    }
    .toolbar-brand__logo {
      background: linear-gradient(135deg, #0284c7, #6366f1);
      width: 38px;
      height: 38px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 800;
      font-size: 20px;
      flex-shrink: 0;
    }
    .toolbar-brand__text {
      min-width: 0;
    }
    .toolbar-brand__text h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 16px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: -0.3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .toolbar-brand__text p {
      font-size: 11px;
      color: #94a3b8;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .toolbar-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    .btn-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 9px 18px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      text-decoration: none;
      white-space: nowrap;
      min-height: 40px;
    }

    .btn-print {
      background: linear-gradient(135deg, #0284c7, #2563eb);
      color: #ffffff;
      box-shadow: 0 2px 10px rgba(2, 132, 199, 0.4);
    }
    .btn-print:hover {
      background: linear-gradient(135deg, #0369a1, #1d4ed8);
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(2, 132, 199, 0.5);
    }

    .btn-close {
      background: rgba(255, 255, 255, 0.1);
      color: #e2e8f0;
      border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .btn-close:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }

    .btn-label-short {
      display: none;
    }

    /* Report Paper Layout Container */
    .report-container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 0 20px;
    }

    /* Hero Header Banner */
    .report-header-card {
      background: var(--bg-card);
      border-radius: 16px;
      border: 1px solid var(--border-color);
      box-shadow: var(--shadow-sm);
      padding: 32px;
      margin-bottom: 24px;
      position: relative;
      overflow: hidden;
    }
    .report-header-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 6px;
      background: linear-gradient(90deg, #0284c7, #6366f1, #10b981);
    }

    .report-meta-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      flex-wrap: wrap;
      gap: 12px;
    }
    .badge-confidential {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fecaca;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 6px;
    }
    .report-date {
      font-size: 12px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .report-title-main {
      font-family: 'Outfit', sans-serif;
      font-size: 28px;
      font-weight: 800;
      color: var(--primary);
      letter-spacing: -0.5px;
      margin-bottom: 6px;
    }
    .report-subtitle {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 24px;
    }

    .report-tags-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
      padding-top: 18px;
      border-top: 1px dashed var(--border-color);
    }
    .tag-item {
      background: var(--bg-body);
      padding: 10px 14px;
      border-radius: 10px;
      border: 1px solid #f1f5f9;
    }
    .tag-item__label {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      color: var(--text-muted);
      letter-spacing: 0.5px;
    }
    .tag-item__value {
      font-size: 14px;
      font-weight: 700;
      color: var(--primary);
      margin-top: 2px;
    }

    /* KPI Grid */
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .kpi-card {
      background: var(--bg-card);
      border-radius: 14px;
      border: 1px solid var(--border-color);
      padding: 20px;
      box-shadow: var(--shadow-sm);
      position: relative;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .kpi-card::after {
      content: '';
      position: absolute;
      top: 0; left: 16px; right: 16px;
      height: 3px;
      border-radius: 3px 3px 0 0;
    }
    .kpi-card--blue::after { background: var(--primary-accent); }
    .kpi-card--emerald::after { background: var(--success); }
    .kpi-card--purple::after { background: var(--secondary-accent); }
    .kpi-card--amber::after { background: var(--warning); }

    .kpi-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .kpi-title {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      color: var(--text-muted);
      letter-spacing: 0.5px;
    }
    .kpi-icon {
      font-size: 20px;
      color: var(--text-muted);
    }

    .kpi-value-main {
      font-family: 'Outfit', sans-serif;
      font-size: 24px;
      font-weight: 800;
      color: var(--primary);
      line-height: 1.2;
    }
    .kpi-unit {
      font-size: 13px;
      font-weight: 500;
      color: var(--text-muted);
      margin-left: 4px;
    }
    .kpi-subtext {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 6px;
    }
    .kpi-subtext strong {
      color: var(--primary);
    }

    /* Section Cards */
    .section-card {
      background: var(--bg-card);
      border-radius: 16px;
      border: 1px solid var(--border-color);
      box-shadow: var(--shadow-sm);
      padding: 24px;
      margin-bottom: 24px;
      page-break-inside: avoid;
    }

    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border-color);
    }
    .section-title-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .section-icon {
      width: 34px;
      height: 34px;
      border-radius: 8px;
      background: #e0f2fe;
      color: var(--primary-accent);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }
    .section-title {
      font-family: 'Outfit', sans-serif;
      font-size: 18px;
      font-weight: 700;
      color: var(--primary);
    }

    /* Chart Layout */
    .charts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(480px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    .chart-container-full {
      position: relative;
      height: 340px;
      width: 100%;
    }
    .chart-container-half {
      position: relative;
      height: 280px;
      width: 100%;
    }

    /* Custom Data Tables */
    .data-table-wrapper {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: 8px;
    }
    .custom-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12px;
      text-align: left;
    }
    .custom-table th {
      background: #f1f5f9;
      color: var(--primary);
      font-weight: 700;
      padding: 8px 10px;
      border-bottom: 2px solid var(--border-color);
      white-space: normal;
      vertical-align: bottom;
      line-height: 1.25;
      word-break: normal;
      overflow-wrap: break-word;
    }
    .custom-table td {
      padding: 8px 10px;
      border-bottom: 1px solid var(--border-color);
      white-space: nowrap;
    }
    .custom-table tr:nth-child(even) td {
      background-color: #f8fafc;
    }
    .custom-table tr:hover td {
      background-color: #f1f5f9;
    }

    .num-col { text-align: right; font-family: 'JetBrains Mono', monospace; font-size: 11.5px; }
    .center-col { text-align: center; }

    .val-positive { color: var(--success); font-weight: 600; }
    .val-negative { color: var(--danger); font-weight: 600; }

    /* Timeline Stepper */
    .timeline-stepper {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
      gap: 8px;
    }
    .timeline-step {
      background: var(--bg-body);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 12px 8px;
      text-align: center;
      position: relative;
    }
    .timeline-step--done {
      background: #ecfdf5;
      border-color: #a7f3d0;
      color: #065f46;
    }
    .timeline-step--current {
      background: #f0f9ff;
      border-color: #7dd3fc;
      color: #0369a1;
      font-weight: 700;
    }
    .step-num {
      font-size: 11px;
      font-weight: 800;
      opacity: 0.7;
    }
    .step-name {
      font-size: 12px;
      font-weight: 600;
      margin-top: 4px;
    }
    .step-badge {
      display: inline-block;
      font-size: 9.5px;
      padding: 2px 6px;
      border-radius: 4px;
      margin-top: 6px;
      text-transform: uppercase;
      font-weight: 700;
    }
    .timeline-step--done .step-badge { background: #d1fae5; color: #047857; }
    .timeline-step--current .step-badge { background: #bae6fd; color: #0284c7; }
    .timeline-step--upcoming .step-badge { background: #e2e8f0; color: #64748b; }

    /* Key Observations & Recommendations */
    .observations-list {
      list-style: none;
      padding: 0;
    }
    .observations-list li {
      position: relative;
      padding-left: 28px;
      margin-bottom: 10px;
      font-size: 13.5px;
      color: var(--text-main);
    }
    .observations-list li::before {
      content: 'check_circle';
      font-family: 'Material Symbols Outlined';
      position: absolute;
      left: 0; top: 0;
      color: var(--primary-accent);
      font-size: 18px;
    }

    .recommendation-box {
      background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
      border: 1px solid #bae6fd;
      border-radius: 12px;
      padding: 16px 20px;
      display: flex;
      align-items: flex-start;
      gap: 14px;
      margin-top: 16px;
    }
    .recommendation-box .material-symbols-outlined {
      color: var(--primary-accent);
      font-size: 24px;
    }
    .recommendation-text h4 {
      font-size: 14px;
      font-weight: 700;
      color: #0369a1;
      margin-bottom: 2px;
    }
    .recommendation-text p {
      font-size: 13px;
      color: #0c4a6e;
    }

    /* Mobile Screen Responsive Adaptations */
    @media (max-width: 768px) {
      .screen-toolbar {
        padding: 10px 14px;
      }
      .toolbar-brand__text p {
        display: none;
      }
      .toolbar-brand__text h1 {
        font-size: 14px;
      }
      .btn-action {
        padding: 8px 12px;
        font-size: 12px;
      }
      .report-container {
        padding: 0 12px;
        margin: 16px auto;
      }
      .report-header-card {
        padding: 20px 16px;
      }
      .report-title-main {
        font-size: 22px;
      }
    }

    @media (max-width: 576px) {
      .screen-toolbar {
        padding: 8px 10px;
        gap: 6px;
      }
      .toolbar-brand__logo {
        width: 32px;
        height: 32px;
        font-size: 16px;
        border-radius: 6px;
      }
      .toolbar-brand__text h1 {
        font-size: 13px;
        max-width: 110px;
      }
      .btn-action {
        padding: 7px 10px;
        font-size: 11.5px;
        gap: 4px;
      }
      .btn-print .btn-label-full {
        display: none;
      }
      .btn-print .btn-label-short {
        display: inline;
      }
    }

    @media (max-width: 380px) {
      .toolbar-brand__text {
        display: none;
      }
    }

    /* Print Specific Styling */
    @media print {
      @page {
        size: A4 portrait;
        margin: 10mm;
      }

      body {
        background-color: #ffffff !important;
        color: #000000 !important;
        padding-bottom: 0 !important;
      }

      .screen-toolbar {
        display: none !important;
      }

      .report-container {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
      }

      .section-card {
        padding: 16px 14px !important;
        box-shadow: none !important;
        border: 1px solid #cbd5e1 !important;
        page-break-inside: avoid;
        margin-bottom: 16px !important;
      }

      .report-header-card, .kpi-card {
        box-shadow: none !important;
        border: 1px solid #cbd5e1 !important;
        page-break-inside: avoid;
      }

      .data-table-wrapper {
        overflow: visible !important;
        width: 100% !important;
      }

      .custom-table {
        width: 100% !important;
        font-size: 8.5pt !important;
        table-layout: auto !important;
      }

      .custom-table th {
        white-space: normal !important;
        word-wrap: break-word !important;
        padding: 5px 4px !important;
        font-size: 8pt !important;
        line-height: 1.15 !important;
      }

      .custom-table td {
        white-space: nowrap !important;
        padding: 4px 4px !important;
        font-size: 8pt !important;
      }

      .num-col {
        font-size: 8pt !important;
      }

      .charts-grid {
        display: block !important;
      }
      .charts-grid > div {
        margin-bottom: 20px !important;
        page-break-inside: avoid;
      }

      .chart-container-full, .chart-container-half {
        height: 240px !important;
      }

      canvas {
        max-width: 100% !important;
        height: auto !important;
      }

      .btn-action { display: none !important; }
      
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Top Action Toolbar -->
  <div class="screen-toolbar no-print">
    <div class="toolbar-brand">
      <div class="toolbar-brand__logo">P</div>
      <div class="toolbar-brand__text">
        <h1>PetroSteps Upstream Intelligence</h1>
        <p>Executive Field Development & Financial Dossier</p>
      </div>
    </div>
    <div class="toolbar-actions">
      <button onclick="window.print()" class="btn-action btn-print" title="Imprimer ou Exporter PDF">
        <span class="material-symbols-outlined">print</span>
        <span class="btn-label-full">Imprimer / Exporter PDF</span>
        <span class="btn-label-short">Imprimer</span>
      </button>
      <button onclick="window.close()" class="btn-action btn-close" title="Fermer le rapport">
        <span class="material-symbols-outlined">close</span>
        <span class="btn-label">Fermer</span>
      </button>
    </div>
  </div>

  <div class="report-container">

    <!-- Header Card -->
    <div class="report-header-card">
      <div class="report-meta-bar">
        <span class="badge-confidential">Confidential &amp; Proprietary</span>
        <span class="report-date">Generated on: <?php echo date('F j, Y'); ?></span>
      </div>
      <h1 class="report-title-main"><?php echo htmlspecialchars($report_context['project_name']); ?></h1>
      <p class="report-subtitle">Executive Upstream Evaluation Dossier &bull; Step <?php echo $report_context['current_step']; ?> (<?php echo htmlspecialchars($report_context['current_step_label']); ?> Stage)</p>
      
      <div class="report-tags-grid">
        <div class="tag-item">
          <div class="tag-item__label">Oil Price Baseline</div>
          <div class="tag-item__value">$<?php echo number_format($report_context['oil_price'], 2); ?> / bbl</div>
        </div>
        <div class="tag-item">
          <div class="tag-item__label">Block Name</div>
          <div class="tag-item__value"><?php echo htmlspecialchars(report_array_get($report_context['block'], 'Block_Name', 'N/A')); ?></div>
        </div>
        <div class="tag-item">
          <div class="tag-item__label">Field Name</div>
          <div class="tag-item__value"><?php echo htmlspecialchars(report_array_get($report_context['field'], 'Field_Name', 'N/A')); ?></div>
        </div>
        <div class="tag-item">
          <div class="tag-item__label">Production Facility</div>
          <div class="tag-item__value"><?php echo htmlspecialchars(report_array_get($report_context['pfacility'], 'Prod_Facilities_Name', 'None Selected')); ?></div>
        </div>
      </div>
    </div>

    <!-- Executive KPI Grid -->
    <div class="kpi-grid">
      <div class="kpi-card kpi-card--emerald">
        <div class="kpi-header">
          <span class="kpi-title">Cumulative Oil Production</span>
          <span class="material-symbols-outlined kpi-icon">oil_barrel</span>
        </div>
        <div class="kpi-value-main">
          <?php echo number_format($report_context['cumul_production'], 0); ?>
          <span class="kpi-unit">BBL</span>
        </div>
        <div class="kpi-subtext">Peak Rate: <strong><?php echo isset($report_context['peak_production']['production']) ? number_format($report_context['peak_production']['production'], 0) : 0; ?> BBL/yr</strong></div>
      </div>

      <div class="kpi-card kpi-card--blue">
        <div class="kpi-header">
          <span class="kpi-title">Net Cumulative Cashflow</span>
          <span class="material-symbols-outlined kpi-icon">payments</span>
        </div>
        <div class="kpi-value-main <?php echo ($report_context['total_profile_cashflow'] >= 0) ? 'val-positive' : 'val-negative'; ?>">
          $<?php echo number_format($report_context['total_profile_cashflow'], 0); ?>
        </div>
        <div class="kpi-subtext">Payback: <strong><?php echo $report_context['payback_year'] ? 'Year '.$report_context['payback_year'] : 'N/A'; ?></strong></div>
      </div>

      <div class="kpi-card kpi-card--purple">
        <div class="kpi-header">
          <span class="kpi-title">Total Project Revenue</span>
          <span class="material-symbols-outlined kpi-icon">trending_up</span>
        </div>
        <div class="kpi-value-main">
          $<?php echo number_format($report_context['total_profile_revenue'], 0); ?>
        </div>
        <div class="kpi-subtext">Total Spending: <strong>$<?php echo number_format($report_context['project_spending'], 0); ?></strong></div>
      </div>

      <div class="kpi-card kpi-card--amber">
        <div class="kpi-header">
          <span class="kpi-title">Unit Development Cost</span>
          <span class="material-symbols-outlined kpi-icon">analytics</span>
        </div>
        <div class="kpi-value-main">
          <?php echo $report_context['finding_cost_per_barrel'] !== null ? '$'.number_format($report_context['finding_cost_per_barrel'], 2) : 'N/A'; ?>
          <span class="kpi-unit">/ bbl</span>
        </div>
        <div class="kpi-subtext">Budget: <strong>$<?php echo number_format($report_context['project_budget'], 0); ?></strong></div>
      </div>
    </div>

    <!-- Phase Cost Breakdown Grid -->
    <div class="kpi-grid" style="margin-top: 16px;">
      <div class="kpi-card kpi-card--emerald">
        <div class="kpi-header">
          <span class="kpi-title">Exploration Cost</span>
          <span class="material-symbols-outlined kpi-icon">explore</span>
        </div>
        <div class="kpi-value-main">
          $<?php echo number_format($report_context['exploration_cost'], 0); ?>
        </div>
        <div class="kpi-subtext">Steps 1–4 (License, Survey, Exp/App Wells)</div>
      </div>

      <div class="kpi-card kpi-card--amber">
        <div class="kpi-header">
          <span class="kpi-title">Development Cost</span>
          <span class="material-symbols-outlined kpi-icon">engineering</span>
        </div>
        <div class="kpi-value-main">
          $<?php echo number_format($report_context['development_cost'], 0); ?>
        </div>
        <div class="kpi-subtext">Step 5 (Dev Wells &amp; Facilities)</div>
      </div>

      <div class="kpi-card kpi-card--purple">
        <div class="kpi-header">
          <span class="kpi-title">Production Cost</span>
          <span class="material-symbols-outlined kpi-icon">factory</span>
        </div>
        <div class="kpi-value-main">
          $<?php echo number_format($report_context['production_cost'], 0); ?>
        </div>
        <div class="kpi-subtext">Steps 6–8 (OPEX, Decom. &amp; Abandonment)</div>
      </div>
    </div>

    <!-- Chart 1: Cashflow Profile -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-title-group">
          <div class="section-icon">
            <span class="material-symbols-outlined">ssid_chart</span>
          </div>
          <h2 class="section-title">Annual &amp; Cumulative Cashflow Profile</h2>
        </div>
      </div>
      <div class="chart-container-full">
        <canvas id="cashflowChart"></canvas>
      </div>
    </div>

    <!-- Charts Grid (Production & Economics) -->
    <div class="charts-grid">
      <div class="section-card">
        <div class="section-header">
          <div class="section-title-group">
            <div class="section-icon">
              <span class="material-symbols-outlined">show_chart</span>
            </div>
            <h2 class="section-title">Annual Production Profile</h2>
          </div>
        </div>
        <div class="chart-container-half">
          <canvas id="productionChart"></canvas>
        </div>
      </div>

      <div class="section-card">
        <div class="section-header">
          <div class="section-title-group">
            <div class="section-icon">
              <span class="material-symbols-outlined">pie_chart</span>
            </div>
            <h2 class="section-title">Project Financial Breakdown</h2>
          </div>
        </div>
        <div class="chart-container-half">
          <canvas id="breakdownChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Annual Financial Table -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-title-group">
          <div class="section-icon">
            <span class="material-symbols-outlined">table_chart</span>
          </div>
          <h2 class="section-title">Annual Economic &amp; Financial Performance</h2>
        </div>
      </div>

      <div class="data-table-wrapper">
        <table class="custom-table">
          <thead>
            <tr>
              <th class="center-col" style="min-width: 55px;">Year</th>
              <th class="num-col" style="min-width: 95px;">Oil Production<br><span style="font-size:10.5px; font-weight:500; opacity:0.85;">(bbl)</span></th>
              <th class="num-col" style="min-width: 95px;">Gross Revenue<br><span style="font-size:10.5px; font-weight:500; opacity:0.85;">($)</span></th>
              <th class="num-col" style="min-width: 95px;">Total Spending<br><span style="font-size:10.5px; font-weight:500; opacity:0.85;">($)</span></th>
              <th class="num-col" style="min-width: 105px;">Annual Net<br><span style="font-size:10.5px; font-weight:500; opacity:0.85;">Cashflow ($)</span></th>
              <th class="num-col" style="min-width: 110px;">Cumulative<br><span style="font-size:10.5px; font-weight:500; opacity:0.85;">Cashflow ($)</span></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($report_context['annual_profile'])): ?>
              <?php foreach ($report_context['annual_profile'] as $row): ?>
                <tr>
                  <td class="center-col"><strong>Year <?php echo $row['project_year']; ?></strong></td>
                  <td class="num-col"><?php echo number_format($row['production'], 0); ?></td>
                  <td class="num-col">$<?php echo number_format($row['revenue'], 0); ?></td>
                  <td class="num-col">$<?php echo number_format($row['spending'], 0); ?></td>
                  <td class="num-col <?php echo ($row['cashflow'] >= 0) ? 'val-positive' : 'val-negative'; ?>">
                    $<?php echo number_format($row['cashflow'], 0); ?>
                  </td>
                  <td class="num-col <?php echo ($row['cumulative_cashflow'] >= 0) ? 'val-positive' : 'val-negative'; ?>">
                    $<?php echo number_format($row['cumulative_cashflow'], 0); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="center-col" style="padding: 20px; color: var(--text-muted);">No annual financial data accumulated yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Project Timeline -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-title-group">
          <div class="section-icon">
            <span class="material-symbols-outlined">route</span>
          </div>
          <h2 class="section-title">Project Lifecycle &amp; Development Stage</h2>
        </div>
      </div>

      <div class="timeline-stepper">
        <?php foreach ($report_context['timeline'] as $step): ?>
          <?php
            $class = 'timeline-step--upcoming';
            if ($step['status'] === 'Completed') $class = 'timeline-step--done';
            if ($step['status'] === 'Current') $class = 'timeline-step--current';
          ?>
          <div class="timeline-step <?php echo $class; ?>">
            <div class="step-num">STEP <?php echo $step['number']; ?></div>
            <div class="step-name"><?php echo htmlspecialchars($step['name']); ?></div>
            <span class="step-badge"><?php echo $step['status']; ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Technical & Reservoir Appendix -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-title-group">
          <div class="section-icon">
            <span class="material-symbols-outlined">tune</span>
          </div>
          <h2 class="section-title">Field &amp; Reservoir Parameters</h2>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <!-- Field Specs -->
        <div>
          <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 10px; color: var(--primary);">Reservoir Specifications</h3>
          <table class="custom-table">
            <tbody>
              <?php foreach ($report_context['appendices']['field_parameters'] as $item): ?>
                <tr>
                  <td style="font-weight: 600; color: var(--text-muted);"><?php echo htmlspecialchars($item['label']); ?></td>
                  <td class="num-col"><strong><?php echo htmlspecialchars($item['value']); ?></strong> <?php echo htmlspecialchars($item['unit']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Facility Specs -->
        <div>
          <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 10px; color: var(--primary);">Facility &amp; Well Inventory</h3>
          <table class="custom-table">
            <tbody>
              <?php foreach ($report_context['appendices']['facility_parameters'] as $item): ?>
                <tr>
                  <td style="font-weight: 600; color: var(--text-muted);"><?php echo htmlspecialchars($item['label']); ?></td>
                  <td class="num-col"><strong><?php echo htmlspecialchars($item['value']); ?></strong> <?php echo htmlspecialchars($item['unit']); ?></td>
                </tr>
              <?php endforeach; ?>
              <?php foreach ($report_context['appendices']['wells'] as $item): ?>
                <tr>
                  <td style="font-weight: 600; color: var(--text-muted);"><?php echo htmlspecialchars($item['label']); ?></td>
                  <td class="num-col"><strong><?php echo htmlspecialchars($item['value']); ?></strong> <?php echo htmlspecialchars($item['unit']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Observations & Recommendations -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-title-group">
          <div class="section-icon">
            <span class="material-symbols-outlined">analytics</span>
          </div>
          <h2 class="section-title">Executive Remarks &amp; Strategic Action</h2>
        </div>
      </div>

      <ul class="observations-list">
        <?php foreach ($report_context['observations'] as $obs): ?>
          <li><?php echo htmlspecialchars($obs); ?></li>
        <?php endforeach; ?>
      </ul>

      <div class="recommendation-box">
        <span class="material-symbols-outlined">lightbulb</span>
        <div class="recommendation-text">
          <h4>Recommended Stage Gate Action</h4>
          <p><?php echo htmlspecialchars($report_context['next_action']); ?></p>
        </div>
      </div>
    </div>

  </div>

  <!-- Chart.js Scripts Initialization -->
  <script>
    const labels = <?php echo json_encode($chart_labels); ?>;
    const cashflowData = <?php echo json_encode($chart_cashflow); ?>;
    const cumulCashflowData = <?php echo json_encode($chart_cumul_cashflow); ?>;
    const productionData = <?php echo json_encode($chart_production); ?>;
    const spendingData = <?php echo json_encode($chart_spending); ?>;
    const revenueData = <?php echo json_encode($chart_revenue); ?>;

    // 1. Cashflow Dual Axis Chart
    const ctxCashflow = document.getElementById('cashflowChart').getContext('2d');
    new Chart(ctxCashflow, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Cumulative Cashflow ($)',
            data: cumulCashflowData,
            type: 'line',
            borderColor: '#0284c7',
            backgroundColor: 'rgba(2, 132, 199, 0.08)',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            yAxisID: 'y1',
            pointRadius: 4,
            pointBackgroundColor: '#0284c7'
          },
          {
            label: 'Annual Net Cashflow ($)',
            data: cashflowData,
            backgroundColor: cashflowData.map(v => v >= 0 ? 'rgba(16, 185, 129, 0.85)' : 'rgba(239, 68, 68, 0.85)'),
            borderRadius: 6,
            yAxisID: 'y'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { position: 'top', labels: { font: { family: 'Inter', size: 12, weight: '600' } } },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.dataset.label + ': $' + Number(context.raw).toLocaleString();
              }
            }
          }
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            type: 'linear',
            display: true,
            position: 'left',
            title: { display: true, text: 'Annual Cashflow ($)' },
            ticks: { callback: v => '$' + Number(v).toLocaleString() }
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            title: { display: true, text: 'Cumulative Cashflow ($)' },
            grid: { drawOnChartArea: false },
            ticks: { callback: v => '$' + Number(v).toLocaleString() }
          }
        }
      }
    });

    // 2. Production Chart
    const ctxProduction = document.getElementById('productionChart').getContext('2d');
    new Chart(ctxProduction, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Annual Production (BBL)',
          data: productionData,
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.15)',
          borderWidth: 3,
          fill: true,
          tension: 0.35,
          pointRadius: 5,
          pointBackgroundColor: '#10b981'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Production: ' + Number(context.raw).toLocaleString() + ' BBL';
              }
            }
          }
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Production (BBL)' },
            ticks: { callback: v => Number(v).toLocaleString() }
          }
        }
      }
    });

    // 3. Financial Breakdown Doughnut
    const totalRevenue = revenueData.reduce((a, b) => a + b, 0);
    const totalSpending = spendingData.reduce((a, b) => a + b, 0);
    const netProfit = Math.max(0, totalRevenue - totalSpending);

    const ctxBreakdown = document.getElementById('breakdownChart').getContext('2d');
    new Chart(ctxBreakdown, {
      type: 'doughnut',
      data: {
        labels: ['Total Spending ($)', 'Net Profit ($)'],
        datasets: [{
          data: [totalSpending, netProfit],
          backgroundColor: ['#ef4444', '#10b981'],
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 12, weight: '600' } } },
          tooltip: {
            callbacks: {
              label: function(context) {
                return context.label + ': $' + Number(context.raw).toLocaleString();
              }
            }
          }
        }
      }
    });
  </script>
</body>
</html>
