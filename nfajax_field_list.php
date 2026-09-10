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
	$results			= $db->get_fields($start, PER_PAGE, $searchval,$sort, $order);
	$paginationCount 	= $db->get_field_count( $searchval);
?>
<div style="overflow-x:auto;">
	<table class="md3-table">
		<thead>
			<tr>
				<th onclick="changeValue('sort','Field_Name');" class="<?php echo ($sort == 'Field_Name')? "sorting_".$order : "sorting"; ?>">Field Name</th>
				<th onclick="changeValue('sort','Field_Average_TD');" class="<?php echo ($sort == 'Field_Average_TD')? "sorting_".$order : "sorting"; ?>">Field Average TD</th>
				<th onclick="changeValue('sort','Field_Road_Cost');" class="<?php echo ($sort == 'Field_Road_Cost')? "sorting_".$order : "sorting"; ?>">Field Road Cost</th>
				<th onclick="changeValue('sort','Field_Accommodation_Cost');" class="<?php echo ($sort == 'Field_Accommodation_Cost')? "sorting_".$order : "sorting"; ?>">Field Accommodation Cost</th>
				<th style="width:140px;text-align:center;">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($results) {
				foreach($results as $row) {
					echo '<tr>
						<td>'.htmlspecialchars($row['Field_Name']).'</td>
						<td>'.htmlspecialchars($row['Field_Average_TD']).'</td>
						<td>'.htmlspecialchars($row['Field_Road_Cost']).'</td>
						<td>'.htmlspecialchars($row['Field_Accommodation_Cost']).'</td>
						<td style="text-align:center;white-space:nowrap;">
							<div style="display:flex;align-items:center;justify-content:center;gap:6px;">
								<button onclick="edit('.$row['fid'].');" class="md3-btn md3-btn--outline" style="padding:4px 10px;font-size:12px;" title="Edit field">
									<span class="material-symbols-outlined" style="font-size:16px;">edit</span> Edit
								</button>
								<button onclick="delete1('.$row['fid'].');" class="md3-btn md3-btn--text" style="color:var(--md-error,#ba1a1a);padding:4px 8px;" title="Delete field">
									<span class="material-symbols-outlined" style="font-size:18px;">delete</span>
								</button>
							</div>
						</td>
					</tr>';
				}
			} else {
				echo '<tr>
					<td colspan="5" style="text-align:center;padding:24px;">No Fields found</td>
				</tr>';
			}
		?>
		</tbody>
	</table>
</div>

<?php
	include "pagination.php"; 
?>