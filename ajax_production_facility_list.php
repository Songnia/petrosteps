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
	$sort				=  isset($_POST['sort'])? $_POST['sort']: 'Prod_Facilities_Name';
	$order				=  isset($_POST['order'])? $_POST['order']: 'desc';

	$page_start 		= $start +1;
	$users				= $db->get_production_facilitys($start, PER_PAGE, $searchval,$sort, $order);
	$paginationCount 	= $db->get_production_facility_count($searchval);
?>
<div style="overflow-x:auto;">
	<table class="md3-table">
		<thead>
			<tr>
				<th onclick="changeValue('sort','Prod_Facilities_Name');" class="<?php echo ($sort == 'Prod_Facilities_Name')? "sorting_".$order : "sorting"; ?>">Prod Facilities Name</th>
				<th onclick="changeValue('sort','Prod_Facilities_Capacity');" class="<?php echo ($sort == 'Prod_Facilities_Capacity')? "sorting_".$order : "sorting"; ?>">Prod Facilities Capacity</th>
				<th onclick="changeValue('sort','Prod_Facilities_Cost');" class="<?php echo ($sort == 'Prod_Facilities_Cost')? "sorting_".$order : "sorting"; ?>">Prod Facilities Cost</th>
				<th onclick="changeValue('sort','Production_Facility_Satus');" class="<?php echo ($sort == 'Production_Facility_Satus')? "sorting_".$order : "sorting"; ?>">Facility Status</th>
				<th onclick="changeValue('sort','Prod_Facilities_decommissioning_cost');" class="<?php echo ($sort == 'Prod_Facilities_decommissioning_cost')? "sorting_".$order : "sorting"; ?>">Decommissioning Cost</th>
				<th style="width:140px;text-align:center;">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($users) {
				foreach($users as $user) {
					echo '<tr>
						<td>'.htmlspecialchars($user['Prod_Facilities_Name']).'</td>
						<td>'.htmlspecialchars($user['Prod_Facilities_Capacity']).'</td>
						<td>'.htmlspecialchars($user['Prod_Facilities_Cost']).'</td>
						<td>'.htmlspecialchars($user['Production_Facility_Satus']).'</td>
						<td>'.htmlspecialchars($user['Prod_Facilities_decommissioning_cost']).'</td>
						<td style="text-align:center;white-space:nowrap;">
							<div style="display:flex;align-items:center;justify-content:center;gap:6px;">
								<button onclick="edit('.$user['pfid'].');" class="md3-btn md3-btn--outline" style="padding:4px 10px;font-size:12px;" title="Edit facility">
									<span class="material-symbols-outlined" style="font-size:16px;">edit</span> Edit
								</button>
								<button onclick="delete1('.$user['pfid'].');" class="md3-btn md3-btn--text" style="color:var(--md-error,#ba1a1a);padding:4px 8px;" title="Delete facility">
									<span class="material-symbols-outlined" style="font-size:18px;">delete</span>
								</button>
							</div>
						</td>
					</tr>';
				}
			} else {
				echo '<tr>
					<td colspan="6" style="text-align:center;padding:24px;">No Production Facility found</td>
				</tr>';
			}
		?>
		</tbody>
	</table>
</div>

<?php
	include "pagination.php"; 
?>