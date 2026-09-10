<?php
include('includes/db.class.php');

if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || !in_array($_SESSION['user_type'], array('Admin', 'SuperAdmin'))){
  header('Location:index.php');
  exit;
}

$db = new DB();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  $added = $db->add_country(isset($_POST['country_name']) ? $_POST['country_name'] : '');
  $_SESSION['message_type'] = $added ? 'success' : 'danger';
  $_SESSION['message'] = $added ? 'Country added successfully.' : ($db->last_error ?: 'The country could not be saved.');
  header('Location:country_list.php');
  exit;
}
?>
<?php $active = 'countries'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Add country - <?php echo APP_NAME; ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/md3-theme.css" rel="stylesheet">
  <link href="css/target-admin.css" rel="stylesheet">
</head>
<body>
<?php include_once "includes/navbar.php"; ?>

<main>
  <div class="md3-page-header">
    <div>
      <h1 class="md3-page-title">Add country</h1>
      <p class="md3-page-subtitle">Add a country to the trainer registration list.</p>
    </div>
  </div>

  <div class="md3-form-card">
    <div class="md3-form-card__header">
      <span class="material-symbols-outlined">public</span>
      <h2>Country details</h2>
    </div>
    <div class="md3-form-card__body">
      <form method="post">
        <div class="md3-field">
          <input type="text" id="country_name" name="country_name" maxlength="255" required placeholder=" ">
          <label for="country_name">Country name</label>
        </div>
        <div class="md3-form-card__actions">
          <a href="country_list.php" class="md3-btn md3-btn--outlined">Cancel</a>
          <button type="submit" class="md3-btn md3-btn--filled">Save country</button>
        </div>
      </form>
    </div>
  </div>
</main>

<?php include "includes/footer.php"; ?>
</body>
</html>
