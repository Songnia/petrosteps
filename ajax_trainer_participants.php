<?php
	include('includes/db.class.php');
	if(
		!isset($_SESSION['user_id'], $_SESSION['session_id'], $_SESSION['user_type']) ||
		$_SESSION['user_type'] !== 'Trainer'
	){
		http_response_code(403);
		exit;
	}
	$db = new DB();
	$active_session = $db->get_active_session((int) $_SESSION['user_id']);
	if($active_session === false || $_SESSION['session_id'] !== $active_session) {
		http_response_code(403);
		exit;
	}
	$cur_page			= $page	= $_POST['page'];
	$page				-= 1;
	$start				= $page * PER_PAGE;
	$searchval			=  isset($_POST['str'])? $_POST['str']: '';
	$sort				=  isset($_POST['sort'])? $_POST['sort']: 'firstname';
	$order				=  isset($_POST['order'])? $_POST['order']: 'desc';
	$user_type			= 'Participant';
	$trainer_id			= (int) $_SESSION['user_id'];
	$page_start 		= $start +1;
	$users				= $db->get_participants_by_trainer($start, PER_PAGE, $trainer_id, $searchval,$user_type,$sort, $order);
	$paginationCount 	= $db->get_participants_by_trainer_count( $trainer_id, $searchval, $user_type);
?>
<div style="overflow-x:auto;">
	<table class="md3-table">
		<thead>
			<tr>
				<th onclick="changeValue('sort','firstname');" class="<?php echo ($sort == 'firstname')? "sorting_".$order : "sorting"; ?>">Name</th>
				<th onclick="changeValue('sort','username');" class="<?php echo ($sort == 'username')? "sorting_".$order : "sorting"; ?>">Username</th>
				<th onclick="changeValue('sort','email');" class="<?php echo ($sort == 'email')? "sorting_".$order : "sorting"; ?>">Email ID</th>
				<th style="width:140px;text-align:center;">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($users) {
				foreach($users as $user){
					echo '<tr>
						<td>'.htmlspecialchars($user['firstname']).'</td>
						<td>'.htmlspecialchars($user['username']).'</td>
						<td>'.htmlspecialchars($user['email']).'</td>
						<td style="text-align:center;white-space:nowrap;">
							<div style="display:flex;align-items:center;justify-content:center;gap:6px;">
								<button onclick="edit(\''.$user['id'].'\');" class="md3-btn md3-btn--outline" style="padding:4px 10px;font-size:12px;" title="Edit participant">
									<span class="material-symbols-outlined" style="font-size:16px;">edit</span> Edit
								</button>
								<button onclick="delete1(\''.$user['id'].'\');" class="md3-btn md3-btn--text" style="color:var(--md-error,#ba1a1a);padding:4px 8px;" title="Delete participant">
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
