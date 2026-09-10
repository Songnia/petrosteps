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
	$projects			= $db->get_projects($start, PER_PAGE, $searchval,$sort, $order);
	$paginationCount 	= $db->get_project_count( $searchval);
?>
			<table class="table table-striped table-bordered table-hover" 
                data-provide="datatable"  >
                  <thead>
                    <tr>
                      <th onclick="changeValue('sort','Project_Name');" class="<?php
					  echo ($sort == 'Project_Name')? 
					   "sorting_".$order : "sorting"; ?>">Project Name</th>

                      <th onclick="changeValue('sort','Project_year');" class="<?php

					  echo ($sort == 'Project_year')? 

					   "sorting_".$order : "sorting"; ?>">Project year</th>

					   <th onclick="changeValue('sort','Project_Spending');" class="<?php

					  echo ($sort == 'Project_Spending')? 

					   "sorting_".$order : "sorting"; ?>">Project Spending </th>

                      <th onclick="changeValue('sort','Project_License_Cost');" class="<?php

					  echo ($sort == 'Project_License_Cost')? 

					   "sorting_".$order : "sorting"; ?>">Project License Cost</th>

					   <th onclick="changeValue('sort','Project_Survey_Cost');" class="<?php

					  echo ($sort == 'Project_Survey_Cost')? 

					   "sorting_".$order : "sorting"; ?>">Project Survey Cost </th>

					   <th onclick="changeValue('sort','Project_Oil_in_Place');" class="<?php

					  echo ($sort == 'Project_Oil_in_Place')? 

					   "sorting_".$order : "sorting"; ?>">Project Oil in Place </th>

					   <th onclick="changeValue('sort','steps_completed');" class="<?php

					  echo ($sort == 'steps_completed')? 

					   "sorting_".$order : "sorting"; ?>">Steps Completed </th>

                      <th>Action</th>

                    </tr>

                  </thead>

				  <tbody>

		<?php

			if($projects)

			foreach($projects as $project)	{

				echo '<tr>

                    <td>'.$project['Project_Name'].'</td>

                    <td>'.$project['Project_year'].'</td>

					<td>'.$project['Project_Spending'].'</td>

                    <td>'.$project['Project_License_Cost'].'</td>

                    <td>'.$project['Project_Survey_Cost'].'</td>

                    <td>'.$project['Project_Oil_in_Place'].'</td>

                    <td>'.$project['steps_completed'].'</td>

 

                    <td style="width: 12%;">

						<!--<button type="button" onclick="edit('.$project['Pid'].');" class="btn btn-secondary btn-xs">Edit</button></a>-->

						<button type="button" onclick="delete1('.$project['Pid'].');"  class="btn btn-primary btn-xs">Delete</button>

					</td>

                    </tr>';

			}

			else{

				echo '<tr>

                    <td colspan="8"> No Projects found</td>

				</tr>';

			}

		?>

			</tbody>

		</table>			

	<?php

		include "pagination.php"; 

	?>