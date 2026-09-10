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
	$sort				=  isset($_POST['sort'])? $_POST['sort']: 'firstname';
	$order				=  isset($_POST['order'])? $_POST['order']: 'desc';
	$user_type			=  isset($_POST['user_type'])? $_POST['user_type']: 'Admin';
	$page_start 		= $start +1;
	$users				= $db->get_users($start, PER_PAGE, $searchval,$user_type,$sort, $order);
	$paginationCount 	= $db->get_user_count( $searchval, $user_type);
?>
<div style="overflow-x:auto;">
	<table class="md3-table">
		<thead>
			<tr>
				<th onclick="changeValue('sort','firstname');" class="<?php echo ($sort == 'firstname')? "sorting_".$order : "sorting"; ?>">Name</th>
				<th onclick="changeValue('sort','username');" class="<?php echo ($sort == 'username')? "sorting_".$order : "sorting"; ?>">Username</th>
			<?php if($user_type == 'Participant'){ ?>
				<th>Ongoing Projects</th>
			<?php } else { ?>
				<th onclick="changeValue('sort','email');" class="<?php echo ($sort == 'email')? "sorting_".$order : "sorting"; ?>">Email ID</th>
			<?php } ?>
				<th style="width:140px;text-align:center;">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($users) {
				foreach($users as $user){
					$datetime1 = new DateTime($user['last_access_time']);
					$datetime2 = new DateTime("now");

					$td_class = 'class="offline_user"';
					$interval = $datetime1->diff($datetime2);
					$intervalmn = $interval->format('%i');
					$intervalh = $interval->format('%h');
					$intervald = $interval->format('%d');
					$intervalm = $interval->format('%m');
					$intervaly = $interval->format('%y');

					if($user_type == 'Participant'){
						if($intervalmn < 20 && $intervalh == 0 && $intervald == 0 && $intervalm == 0 && $intervaly == 0)
							$td_class = 'class="online_user"';
						$ongonig_projects = $db->get_ongoing_project_count_by_user($user['id']);
					}
					echo '<tr>
						<td '.$td_class.'>'.htmlspecialchars($user['firstname']).'</td>
						<td>'.htmlspecialchars($user['username']).'</td>';
						
					if($user_type == 'Participant'){
						echo '<td>'.$ongonig_projects.'</td>';
					} else {
						echo '<td>'.htmlspecialchars($user['email']).'</td>';
					}
					echo '<td style="text-align:center;white-space:nowrap;">
						<div style="display:flex;align-items:center;justify-content:center;gap:6px;">
							<button onclick="edit('.$user['id'].');" class="md3-btn md3-btn--outline" style="padding:4px 10px;font-size:12px;" title="Edit user">
								<span class="material-symbols-outlined" style="font-size:16px;">edit</span> Edit
							</button>
							<button onclick="delete1('.$user['id'].');" class="md3-btn md3-btn--text" style="color:var(--md-error,#ba1a1a);padding:4px 8px;" title="Delete user">
								<span class="material-symbols-outlined" style="font-size:18px;">delete</span>
							</button>
						</div>
					</td>
					</tr>';
				}
			} else {
				echo '<tr>
					<td colspan="4" style="text-align:center;padding:24px;">No '.$user_type.' Users found</td>
				</tr>';
			}
		?>
		</tbody>
	</table>
</div>

<?php
	include "pagination.php"; 
?>