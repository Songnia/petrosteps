<?php
include('includes/db.class.php');

if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || !in_array($_SESSION['user_type'], array('Admin', 'SuperAdmin'))){
  header('Location:index.php');
  exit;
}

$db = new DB();
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_country') {
  $deleted = $db->delete_country(isset($_POST['country_id']) ? $_POST['country_id'] : 0);
  $_SESSION['message_type'] = $deleted ? 'success' : 'danger';
  $_SESSION['message'] = $deleted ? 'Country deleted successfully.' : ($db->last_error ?: 'The country could not be deleted.');
  header('Location:country_list.php');
  exit;
}

$countries = $db->get_countries();
?>
<?php $active = 'countries'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Countries - <?php echo APP_NAME; ?></title>
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
      <h1 class="md3-page-title">Countries</h1>
      <p class="md3-page-subtitle">Manage the countries available when creating trainer accounts.</p>
    </div>
  </div>

  <div class="md3-table-container">
    <div class="md3-table-toolbar">
      <h2 class="md3-table-title">Country directory</h2>
      <a href="country_add.php" class="md3-btn md3-btn--filled">
        <span class="material-symbols-outlined" style="font-size:18px;">add</span>
        Add country
      </a>
    </div>

    <?php if(isset($_SESSION['message'])) { ?>
      <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?>" style="margin:16px 24px 0;">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
      </div>
    <?php } ?>

    <div style="overflow-x:auto;">
      <table class="md3-table">
        <thead>
          <tr>
            <th>Country</th>
            <th>Accounts using this country</th>
            <th style="width:150px;text-align:center;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if($countries) { ?>
          <?php foreach($countries as $country) { ?>
            <tr>
              <td><?php echo htmlspecialchars($country['cname']); ?></td>
              <td><?php echo (int) $country['account_count']; ?></td>
              <td style="text-align:center;white-space:nowrap;">
                <a href="country_edit.php?id=<?php echo (int) $country['cid']; ?>" class="md3-btn md3-btn--outline" style="padding:4px 10px;font-size:12px;">Edit</a>
                <form action="country_list.php" method="post" style="display:inline;" onsubmit="return confirmCountryDeletion(this);">
                  <input type="hidden" name="action" value="delete_country">
                  <input type="hidden" name="country_id" value="<?php echo (int) $country['cid']; ?>">
                  <button type="submit" class="md3-btn md3-btn--text" style="color:var(--md-error,#ba1a1a);padding:4px 8px;" aria-label="Delete <?php echo htmlspecialchars($country['cname']); ?>">
                    <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
                  </button>
                </form>
              </td>
            </tr>
          <?php } ?>
        <?php } else { ?>
          <tr><td colspan="3" style="text-align:center;padding:28px;">No countries have been added yet.</td></tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include "includes/footer.php"; ?>
<script>
function confirmCountryDeletion(form) {
  md3_confirm('Delete this country? This action cannot be undone.', function() {
    form.submit();
  }, 'delete');
  return false;
}
</script>
</body>
</html>
