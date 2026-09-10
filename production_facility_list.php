<?php
  include('includes/db.class.php');
  if(!isset($_SESSION['user_id']) || !$_SESSION['user_id'] || $_SESSION['user_type'] == 'Participant'){
    header('Location:index.php');
    exit;
  }
?>
<?php $active = 'masters'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Production Facility List — <?php echo APP_NAME; ?></title>

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
</head>

<body>
<?php
	include_once "includes/navbar.php";
?>

<div class="md3-page-header">
  <div>
    <h1 class="md3-page-title">Production Facility List</h1>
    <p class="md3-page-subtitle">Manage production facility master data</p>
  </div>
</div>

<div class="md3-table-container">
  <div class="md3-table-toolbar">
    <h2 class="md3-table-title">Production Facility List</h2>
    <div class="md3-table-actions">
      <a href="production_facility_add.php"><button class="md3-btn md3-btn--filled"><span class="material-symbols-outlined">add</span> Add Production Facility</button></a>
    </div>
  </div>

  <form action="production_facility_edit.php" method="post" name="edit_form">
    <input type="hidden" name="pfid" id="pfid">
    <input type="hidden" id="form_action" name="action" value="">
  </form>

  <?php
    if(isset($_SESSION['message'])) {
      echo '<div class="alert alert-'.$_SESSION['message_type'].'">
      <a class="close" data-dismiss="alert" href="#" aria-hidden="true">×</a>
      '.$_SESSION['message'].'
      </div>';
    }
  ?>

  <div style="padding: 12px 24px;">
    <div class="md3-search-field">
      <span class="material-symbols-outlined">search</span>
      <input type="text" id="appendedInput" onkeyup="loadData(1);" placeholder="Search facilities...">
    </div>
  </div>

  <input type="hidden" id="order" value="asc">
  <input type="hidden" id="sort" value="Prod_Facilities_Name">

  <div id="ajax_list_div" style="padding: 0;">
  </div>
</div>

<?php include "includes/footer.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/libs/jquery-1.10.1.min.js"></script>
  <script src="./js/target-admin.js"></script>

<script type="text/javascript">
function edit(id) {
  document.getElementById('pfid').value = id;
  document.getElementById('form_action').value = "edit_production_facility";
  if(!document.getElementById('pfid').value)
  {
    alert("Error occured please try again");
    return false;
  }
  document.forms[0].submit();
}

function delete1(id)
{
  document.getElementById('pfid').value = id;
  document.getElementById('form_action').value = "delete_production_facility";
  if(!document.getElementById('pfid').value)
    {
      alert("Error occured please try again");
      return false;
    }
  var con = confirm("Are You Sure ? ");
  if(con)
    document.forms[0].submit();
  return false;
}

$(document).ready(function(){ 
    loadData(1);
});

function changeValue(name, value) {
  if(order == 'asc')
    $('#order').val('desc');
  else
    $('#order').val('asc');
  $("#"+name).val(value);
  loadData(1);
}

function loadData(page){
  searchStr = $('#appendedInput').val();
  order = $('#order').val();
  sort = $('#sort').val();
  $.ajax
  ({
    type: "POST",
    url: "ajax_production_facility_list.php",
   data: "page="+page+"&str="+searchStr+"&sort="+sort+"&order="+order,
    success: function(msg)
    {
      var searchString = searchStr;
      $("#ajax_list_div").html(msg);
      $("#ajax_list_div").ajaxComplete(function(event, request, settings)
    {
      $("#ajax_list_div").html(msg);
    });
    }
  });
}
</script>

</body>
</html>
