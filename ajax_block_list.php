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
	$users				= $db->get_blocks($start, PER_PAGE, $searchval,$sort, $order);
	$paginationCount 	= $db->get_block_count( $searchval);
?>
<div style="overflow-x:auto;">
	<table class="md3-table">
		<thead>
			<tr>
				<th onclick="changeValue('sort','Block_Name');" class="<?php echo ($sort == 'Block_Name')? "sorting_".$order : "sorting"; ?>">Block Name</th>
				<th onclick="changeValue('sort','Block_Surface');" class="<?php echo ($sort == 'Block_Surface')? "sorting_".$order : "sorting"; ?>">Block Surface</th>
				<th onclick="changeValue('sort','Block_Description');" class="<?php echo ($sort == 'Block_Description')? "sorting_".$order : "sorting"; ?>">Block Description</th>
				<th onclick="changeValue('sort','Block_License_Cost_Exp');" class="<?php echo ($sort == 'Block_License_Cost_Exp')? "sorting_".$order : "sorting"; ?>">License Cost Exp</th>
				<th onclick="changeValue('sort','Block_License_cost_Prod');" class="<?php echo ($sort == 'Block_License_cost_Prod')? "sorting_".$order : "sorting"; ?>">License Cost Prod</th>
				<th onclick="changeValue('sort','Probability_to_find');" class="<?php echo ($sort == 'Probability_to_find')? "sorting_".$order : "sorting"; ?>">Probability</th>
				<th onclick="changeValue('sort','Signature_Bonus');" class="<?php echo ($sort == 'Signature_Bonus')? "sorting_".$order : "sorting"; ?>">Signature Bonus</th>
				<th onclick="changeValue('sort','Block_Survey_Cost');" class="<?php echo ($sort == 'Block_Survey_Cost')? "sorting_".$order : "sorting"; ?>">Survey Cost</th>
				<th onclick="changeValue('sort','Block_Survey_interpretation_cost');" class="<?php echo ($sort == 'Block_Survey_interpretation_cost')? "sorting_".$order : "sorting"; ?>">Survey Interp Cost</th>
				<th style="width:140px;text-align:center;">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($users) {
				foreach($users as $user) {
					echo '<tr>
						<td>'.htmlspecialchars($user['Block_Name']).'</td>
						<td>'.htmlspecialchars($user['Block_Surface']).'</td>
						<td>'.htmlspecialchars($user['Block_Description']).'</td>
						<td>'.htmlspecialchars($user['Block_License_Cost_Exp']).'</td>
						<td>'.htmlspecialchars($user['Block_License_cost_Prod']).'</td>
						<td>'.htmlspecialchars($user['Probability_to_find']).'</td>
						<td>'.htmlspecialchars($user['Signature_Bonus']).'</td>
						<td>'.htmlspecialchars($user['Block_Survey_Cost']).'</td>
						<td>'.htmlspecialchars($user['Block_Survey_interpretation_cost']).'</td>
						<td style="text-align:center;white-space:nowrap;">
							<div style="display:flex;align-items:center;justify-content:center;gap:6px;">
								<button onclick="edit('.$user['bid'].');" class="md3-btn md3-btn--outline" style="padding:4px 10px;font-size:12px;" title="Edit block">
									<span class="material-symbols-outlined" style="font-size:16px;">edit</span> Edit
								</button>
								<button onclick="delete1('.$user['bid'].');" class="md3-btn md3-btn--text" style="color:var(--md-error,#ba1a1a);padding:4px 8px;" title="Delete block">
									<span class="material-symbols-outlined" style="font-size:18px;">delete</span>
								</button>
							</div>
						</td>
					</tr>';
				}
			} else {
				echo '<tr>
					<td colspan="10" style="text-align:center;padding:24px;">No Blocks found</td>
				</tr>';
			}
		?>
		</tbody>
	</table>
</div>

<?php
	include "pagination.php"; 
?>