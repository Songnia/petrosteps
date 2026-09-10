<?php
	include('includes/db.class.php');
	if(!isset($_SESSION['user_id']) || !$_SESSION['user_id']){
		header('Location:index.php');
		exit;
	}
	$db = new DB();
	$block_id	= $_POST['block_id'];
	$block = $db->get_block($block_id);
?>
		<div class="form-group">
			<div class="col-md-10">
			<div class="well">
            <h5><strong>Block Name: <?php echo $block['Block_Name']; ?></strong></h5>

			<h5><strong>Block Surface (Ha): <?php echo $block['Block_Surface']; ?></strong></h5>

			<h5><strong>Block Description: <?php echo $block['Block_Description']; ?></strong></h5>

			<h5><strong>Block License Cost Exp (USD/Ha): <?php echo $block['Block_License_Cost_Exp']; ?></strong></h5>

			<h5><strong>Block License cost Prod (USD/Ha): <?php echo $block['Block_License_cost_Prod']; ?></strong></h5>

			<h5><strong>Block Survey Cost (USD/Ha): <?php echo $block['Block_Survey_Cost']; ?></strong></h5>

			<h5><strong>Block Survey interpretation cost (USD/Ha): <?php echo $block['Block_Survey_interpretation_cost']; ?></strong></h5>

			<h5><strong>Probability to find (%): <?php echo $block['Probability_to_find']; ?></strong></h5>

			<h5><strong>Signature Bonus ($): <?php echo $block['Signature_Bonus']; ?></strong></h5>

          </div>

		  </div>

		</div>