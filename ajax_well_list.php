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
	$sort				=  isset($_POST['sort'])? $_POST['sort']: 'Field_Name';
	$order				=  isset($_POST['order'])? $_POST['order']: 'desc';

	$page_start 		= $start +1;
	$users				= $db->get_wells($start, PER_PAGE, $searchval,$sort, $order);
	$paginationCount 	= $db->get_well_count( $searchval);
?>
<div style="overflow-x:auto;">
	<table class="md3-table">
		<thead>
			<tr>
				<th onclick="changeValue('sort','Well_Name');" class="<?php echo ($sort == 'Well_Name')? "sorting_".$order : "sorting"; ?>">Well Name</th>
				<th onclick="changeValue('sort','Well_Depth');" class="<?php echo ($sort == 'Well_Depth')? "sorting_".$order : "sorting"; ?>">Well Depth</th>
				<th onclick="changeValue('sort','Well_Flowrate');" class="<?php echo ($sort == 'Well_Flowrate')? "sorting_".$order : "sorting"; ?>">Well Flowrate</th>
				<th onclick="changeValue('sort','Well_Production_Status');" class="<?php echo ($sort == 'Well_Production_Status')? "sorting_".$order : "sorting"; ?>">Well Production Status</th>
				<th style="width:140px;text-align:center;">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($users) {
				foreach($users as $user) {
					echo '<tr>
						<td>'.htmlspecialchars($user['Well_Name']).'</td>
						<td>'.htmlspecialchars($user['Well_Depth']).'</td>
						<td>'.htmlspecialchars($user['Well_Flowrate']).'</td>
						<td>'.htmlspecialchars($user['Well_Production_Status']).'</td>
						<td style="text-align:center;white-space:nowrap;">
							<div style="display:flex;align-items:center;justify-content:center;gap:6px;">
								<button onclick="edit('.$user['Wid'].');" class="md3-btn md3-btn--outline" style="padding:4px 10px;font-size:12px;" title="Edit well">
									<span class="material-symbols-outlined" style="font-size:16px;">edit</span> Edit
								</button>
								<button onclick="delete1('.$user['Wid'].');" class="md3-btn md3-btn--text" style="color:var(--md-error,#ba1a1a);padding:4px 8px;" title="Delete well">
									<span class="material-symbols-outlined" style="font-size:18px;">delete</span>
								</button>
							</div>
						</td>
					</tr>';
				}
			} else {
				echo '<tr>
					<td colspan="5" style="text-align:center;padding:24px;">No Wells found</td>
				</tr>';
			}
		?>
		</tbody>
	</table>
</div>

<?php
	include "pagination.php"; 
?>