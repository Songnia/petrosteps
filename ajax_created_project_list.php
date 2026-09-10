<?php

  include('includes/db.class.php');

  if(!isset($_SESSION['user_id']) || !$_SESSION['user_id']){
    header('Location:index.php');
    exit;
  }

  $db = new DB();
  $cur_page			= $page	= $_POST['page'];
  $page				-= 1;
  $start				= $page * PER_PAGE;
  $searchval			=  isset($_POST['str'])? $_POST['str']: '';
  $sort				=  isset($_POST['sort'])? $_POST['sort']: 'Project_Name';
  $order				=  isset($_POST['order'])? $_POST['order']: 'desc';
  $page_start 		= $start +1;
  $projects			= $db->get_created_projects($start, PER_PAGE, $searchval,$sort, $order);
  $paginationCount 	= $db->get_created_project_count( $searchval);

?>

<div style="padding: 0 24px 16px;">
  <a href="project_add.php"><button class="md3-btn md3-btn--filled"><span class="material-symbols-outlined" style="font-size:18px;">add</span> Add Project</button></a>
</div>

<div style="overflow-x:auto;">
  <table class="md3-table">
    <thead>
      <tr>
        <th onclick="changeValue('sort','username');" class="<?php echo ($sort == 'username')? "sorting_".$order : "sorting"; ?>">Participant</th>
        <th>Trainer</th>
        <th onclick="changeValue('sort','Project_Name');" class="<?php echo ($sort == 'Project_Name')? "sorting_".$order : "sorting"; ?>">Project Name</th>
        <th onclick="changeValue('sort','Project_year');" class="<?php echo ($sort == 'Project_year')? "sorting_".$order : "sorting"; ?>">Project Year</th>
        <th onclick="changeValue('sort','Project_Spending');" class="<?php echo ($sort == 'Project_Spending')? "sorting_".$order : "sorting"; ?>">Spending</th>
        <th onclick="changeValue('sort','steps_completed');" class="<?php echo ($sort == 'steps_completed')? "sorting_".$order : "sorting"; ?>">Steps</th>
        <th style="width:160px;text-align:center;">Action</th>
      </tr>
    </thead>
    <tbody>
<?php
  if($projects) {
    foreach($projects as $project) {
      $user = $db->get_user($project['trainer_id']);
      if(!$user) {
        $user = ['username' => 'Admin'];
      }
      $spending = is_numeric($project['Project_Spending']) ? '$'.number_format($project['Project_Spending']) : '$0';
      echo '<tr>
        <td><strong>'.htmlspecialchars($project['username'] ?? 'N/A').'</strong></td>
        <td>'.htmlspecialchars($user['username'] ?? 'Admin').'</td>
        <td><span class="proj-name">'.htmlspecialchars($project['Project_Name']).'</span></td>
        <td>Year '.$project['Project_year'].'</td>
        <td><span class="proj-figure proj-figure--spending">'.$spending.'</span></td>
        <td><span class="proj-status-pill" style="background:rgba(46,125,50,0.12);color:#2E7D32;"><span class="proj-status-dot" style="background:#2E7D32;"></span>Completed</span></td>
        <td style="text-align:center;position:relative;">
          <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
            <button onclick="edit(\''.$project['Pid'].'\');" class="md3-btn md3-btn--filled" style="padding:6px 12px;font-size:12px;">
              <span class="material-symbols-outlined" style="font-size:16px;">visibility</span> Open
            </button>
            <button onclick="delete1(\''.$project['Pid'].'\');" class="md3-btn md3-btn--text" style="color:var(--md-error);padding:6px 8px;" title="Delete project">
              <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
            </button>
          </div>
        </td>
      </tr>';
    }
  } else {
    echo '<tr>
      <td colspan="7" style="text-align:center;padding:24px;">No completed projects found</td>
    </tr>';
  }
?>
    </tbody>
  </table>
</div>

<?php
  include "pagination.php"; 
?>
