<?php
    require_once __DIR__.'/project_step_helpers.php';
    // Graph calculation — runs on all pages (sets session vars)
    $project_cash_flow = $db->get_cash_flow($_SESSION['project']['project_id']);
    $oil_price = $db->get_parameter('Oil_Price');
    include_once 'includes/graph_data.php';
    list($graph_array, $current_project_year) = build_project_graph_data($project_cash_flow, $oil_price);
    $project_cashflow = 0.0;
    $project_cumulative_cashflow = 0.0;
    foreach ($graph_array as $graph_point) {
      $project_cashflow = isset($graph_point['cashflow']) ? (float)$graph_point['cashflow'] : 0.0;
      $project_cumulative_cashflow += $project_cashflow;
    }
    $_SESSION['project']['current_project_year'] = $current_project_year;
    $project_current_step = project_get_current_step($_SESSION['project']);
    $project_step_tabs = array(
      1 => 'LICENSE',
      2 => 'SURVEY',
      3 => 'EXPLORATION',
      4 => 'APPRAISAL',
      5 => 'DEVELOPMENT',
      6 => 'PRODUCTION',
      7 => '2<sup>nd</sup> RECOVERY',
      8 => 'ABANDON'
    );

    if(!isset($project_nav_show_graph)) {
      $project_nav_show_graph = true;
    }
    if(!isset($project_nav_show_params)) {
      $project_nav_show_params = true;
    }
    if(!isset($project_nav_show_tabs)) {
      $project_nav_show_tabs = true;
    }
    if(!isset($project_nav_show_message)) {
      $project_nav_show_message = true;
    }
?>

<?php if($project_nav_show_graph): ?>
<div class="md3-graph-bar">
  <button type="button" class="md3-btn md3-btn--outline md3-btn--sm see_graph_btn">
    <span class="material-symbols-outlined">bar_chart</span> Show Graph
  </button>
  <button type="button" class="md3-btn md3-btn--outline md3-btn--sm hide_graph_btn">
    <span class="material-symbols-outlined">bar_chart</span> Hide Graph
  </button>
</div>
<div class="graph-card">
<div id="mainChartDiv">
  <div id="chartdiv" style="width:100%; height:420px; min-height:420px; display:block;"></div>
    <b>X-axis : Project Year</b><br/>
    <b>Y-axis left : Cash Flow</b><br/>
    <b>Y-axis right : Production</b>
  </div>
      <br/>
      <hr/>
</div>
<?php endif; ?>
<?php if($project_nav_show_params): ?>
<!-- Parameters -->
<div class="md3-param-strip">
  <div class="md3-param-item">
    <span class="md3-param-label">Oil PRICE</span>
    <span class="md3-param-value"><?php echo $parameters['Oil_Price']['value']; ?> $/B</span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">BUDGET</span>
    <span class="md3-param-value">$<?php echo number_format((float)$parameters['Budget']['value'] * 1000000, 0); ?></span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">PRODUCTION</span>
    <span class="md3-param-value" id="PRODUCTION_YEAR"><?php echo $_SESSION['project']['Production_year']; ?></span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">PROJECT REVENUE</span>
    <span class="md3-param-value">$<?php echo number_format((float)$_SESSION['project']['Project_Projected_Revenue'], 0); ?></span>
  </div>
</div>
<div class="md3-param-strip md3-param-strip--dark">
  <div class="md3-param-item">
    <span class="md3-param-label">ACTUAL FLOWRATE</span>
    <span class="md3-param-value" id="ACTUAL_FLOWRATE"><?php echo $_SESSION['project']['Project_Total_Flowrate']; ?> B/D</span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">CUMUL - PROD</span>
    <span class="md3-param-value" id="CUMUL_PROD"><?php echo $_SESSION['project']['Cumul_Production']; ?> B</span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">ACTUAL REVENUE</span>
    <span class="md3-param-value" id="ACTUAL_REVENUE">$<?php echo number_format((float)$_SESSION['project']['Project_Actual_Revenue'], 0); ?></span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">CUMUL - CASH FLOW</span>
    <span class="md3-param-value" id="CUMULATIVE_CASH_FLOW">$<?php echo number_format($project_cumulative_cashflow, 0); ?></span>
  </div>
</div>
<?php
  $nav_phase_costs = project_calculate_phase_costs($_SESSION['project']);
?>
<div class="md3-param-strip md3-param-strip--costs" style="margin-top: 8px;">
  <div class="md3-param-item">
    <span class="md3-param-label">EXPLORATION COST</span>
    <span class="md3-param-value">$<?php echo number_format($nav_phase_costs['exploration_cost'], 0); ?></span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">DEVELOPMENT COST</span>
    <span class="md3-param-value">$<?php echo number_format($nav_phase_costs['development_cost'], 0); ?></span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">PRODUCTION COST</span>
    <span class="md3-param-value">$<?php echo number_format($nav_phase_costs['production_cost'], 0); ?></span>
  </div>
  <div class="md3-param-item">
    <span class="md3-param-label">SPENDINGS</span>
    <span class="md3-param-value">$<?php echo number_format((float)$_SESSION['project']['Project_Spending'], 0); ?></span>
  </div>
</div>
<br/>
<?php endif; ?>
<?php if($project_nav_show_tabs): ?>
<div class="md3-step-nav-wrapper">
  <div class="md3-step-tabs">
    <?php foreach($project_step_tabs as $step_number => $step_label): ?>
      <?php
        $step_is_active = (strpos($_SERVER["PHP_SELF"], 'project_step'.$step_number) > -1);
        $step_is_unlocked = ($step_number <= $project_current_step);
        $step_classes = 'md3-step-tab';
        if($step_is_active) {
          $step_classes .= ' active';
        }
        if(!$step_is_unlocked) {
          $step_classes .= ' md3-step-tab--locked';
        }
      ?>
      <?php if($step_is_unlocked): ?>
        <a href="<?php echo project_get_step_url($step_number); ?>" class="<?php echo $step_classes; ?>">
          <span class="md3-step-tab__num"><?php echo $step_number; ?></span>
          <span class="md3-step-tab__label"><?php echo $step_label; ?></span>
        </a>
      <?php else: ?>
        <span class="<?php echo $step_classes; ?>" style="opacity:.45; cursor:not-allowed;" aria-disabled="true" title="This step will unlock later in the project.">
          <span class="md3-step-tab__num"><?php echo $step_number; ?></span>
          <span class="md3-step-tab__label"><?php echo $step_label; ?></span>
        </span>
      <?php endif; ?>
    <?php endforeach; ?>

    <a data-toggle="modal" href="#basicModal" class="md3-step-tab md3-step-tab--utility param_tab">
      <span class="md3-step-tab__num material-symbols-outlined" style="font-size:18px;">tune</span>
      <span class="md3-step-tab__label">PARAMETERS</span>
    </a>
    <a href="report.php" target="_blank" class="md3-step-tab md3-step-tab--utility">
      <span class="md3-step-tab__num material-symbols-outlined" style="font-size:18px;">description</span>
      <span class="md3-step-tab__label">REPORT</span>
    </a>
  </div>
</div>
<?php endif; ?>
<?php
	if($project_nav_show_message && isset($_SESSION['message'])) {
		echo '<div class="md3-alert md3-alert--'.$_SESSION['message_type'].'">
		<span class="material-symbols-outlined">info</span>
		'.$_SESSION['message'].'
		</div>';
	}
?>
