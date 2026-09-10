<?php 
/************************************************************************************
 * File Name	: pagination.php
 * Purpose		: For displaying pagination
 * Created On   : 03-06-2015
 * Created By	: Swapnil Wagh
 ***********************************************************************************/
 
	$pagination = getPaginValues($paginationCount,$cur_page);
	if($pagination['paginationCount'] != 0){

	$startrecordno	=	$start + 1;
	$endrecordno	=	$start	+ PER_PAGE;

	if($endrecordno > $paginationCount)
	    $endrecordno = $paginationCount;
	if($paginationCount ==  0)
		$startrecordno = 0;
	$showingRrdHst	= "Showing  $startrecordno To $endrecordno  Of $paginationCount entries";
?>
	<div class="row dt-rb">
		<div class="col-sm-6">
			<div class="dataTables_info" id="DataTables_Table_0_info">
			<?php
				echo $showingRrdHst; 
			?></div>
		</div>
<?php
	echo  '<div class="col-sm-6">
			<div class="dataTables_paginate paging_bootstrap">
			<ul class="pagination">';

// FOR ENABLING THE FIRST BUTTON
if ($pagination['first_btn'] && $pagination['cur_page'] > 1) {
	echo '<li class="next"><a href="#" onclick="loadData(1); return false;">First</a></li>';
} else if ($pagination['first_btn']) {
	echo "<li class='disabled'><a href='javascript:void();'>First </a></li>";
}

// FOR ENABLING THE PREVIOUS BUTTON
if ($pagination['previous_btn'] && $pagination['cur_page'] > 1) {
    $pre = $pagination['cur_page'] - 1;
	echo '<li class="prev"><a href="#" onclick="loadData('.$pre.'); return false;">← Previous</a></li>';
} else if ($pagination['previous_btn']) {
	echo '<li class="prev disabled">
				<a href="javascript:void();">← Previous</a>
			</li>';
}
for ($i = $pagination['start_loop']; $i <= $pagination['end_loop']; $i++) {
    if ($pagination['cur_page'] == $i)
		echo '<li class="active"><a href="#" onclick="loadData('.$i.'); return false;">'.$i.'</a></li>';
    else
		echo '<li><a href="#" onclick="loadData('.$i.'); return false;">'.$i.'</a></li>';
}

// TO ENABLE THE NEXT BUTTON
if ($pagination['next_btn'] && $pagination['cur_page'] < $pagination['no_of_paginations']) {
    $nex = $pagination['cur_page'] + 1;
    
	echo '<li><a href="#" onclick="loadData('.$nex.'); return false;">Next →</a></li>';
} else if ($pagination['next_btn']) {
	echo "<li class='disabled'><a href='javascript:void();'>Next → </a></li>";
}
echo "</ul></div></div></div>";
}
