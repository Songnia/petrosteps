<?php
	$participant_count =  $db->get_participant_count_of_trainer($_SESSION['user_id']);
	$parameter_count =  $db->get_parameter_count();

?>
<?php $active = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Dashboard — <?php echo APP_NAME; ?></title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- MD3 Tokens -->
  <link href="css/md3-theme.css" rel="stylesheet">

  <!-- App CSS -->
  <link href="css/target-admin.css" rel="stylesheet">
  <link href="css/custom.css" rel="stylesheet">
</head>
<body>
<?php include_once "includes/navbar.php"; ?>

  <!-- Page Header -->
  <div class="md3-page-header">
    <div>
      <h1 class="md3-page-title">Dashboard</h1>
      <p class="md3-page-subtitle">Trainer — Petrosteps training platform</p>
    </div>
  </div>

  <!-- KPI Grid -->
  <div class="kpi-grid">

    <div class="kpi-card kpi-card--primary">
      <div class="kpi-card__icon">
        <span class="material-symbols-outlined">group</span>
      </div>
      <div class="kpi-card__content">
        <span class="kpi-card__label">Participants</span>
        <span class="kpi-card__value"><?php echo $participant_count; ?></span>
        <span class="kpi-card__meta">Assigned trainees</span>
      </div>
      <a href="trainer_participant_list.php" class="kpi-card__action">
        <span class="material-symbols-outlined">arrow_forward</span>
      </a>
    </div>

    <div class="kpi-card kpi-card--secondary">
      <div class="kpi-card__icon">
        <span class="material-symbols-outlined">tune</span>
      </div>
      <div class="kpi-card__content">
        <span class="kpi-card__label">Parameters</span>
        <span class="kpi-card__value"><?php echo $parameter_count; ?></span>
        <span class="kpi-card__meta">Configurations</span>
      </div>
      <a href="parameter_list.php" class="kpi-card__action">
        <span class="material-symbols-outlined">arrow_forward</span>
      </a>
    </div>

  </div>

<?php include "includes/footer.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
</body>
</html>
