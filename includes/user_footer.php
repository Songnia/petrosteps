  <div id="basicModal" class="modal fade">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

        <h3 class="modal-title">Detailed Parameters</h3>

      </div>

      <div class="modal-body">

	<table class="table table-bordered table-condensed">

	 <caption></caption>

            <thead>

              <tr>

                <th>Parameter</th>

                <th>Unit</th>
                <th>Value</th>
              </tr>

            </thead>

            <tbody>

              <tr>

                <th colspan="3">General parameters</th>

              </tr>

<?php
	foreach($parameters as $key => $param){
		echo '
			<tr>
                <td>'.$param['display_name'].'</td>
                <td>'.$param['unit'].'</td>
                <td>'.$param['value'].'</td>
			</tr>';

	}
?>	

              <tr>

                <th colspan="3">Project parameters</th>

              </tr>

			<tr>

                <td>Project Budget</td> <td>($)</td> <td><?php echo number_format((float)$project['Project_Budget'], 0); ?></td>

			</tr>

			<tr>
                <td>Project Year</td> <td>(Years)</td> <td><?php echo $_SESSION['project']['current_project_year'];    //echo $project['Project_year']; ?></td>

			</tr>

			<tr>

                <td>Project Spending</td> <td>(USD)</td> <td><?php echo $project['Project_Spending']; ?></td>

			</tr>

			<tr>

                <td>Project License Cost</td> <td>(USD)</td> <td><?php echo $project['Project_License_Cost']; ?></td>

			</tr>

			<tr>

                <td>Project Survey Cost</td> <td>(USD)</td> <td><?php echo $project['Project_Survey_Cost']; ?></td>

			</tr>

			<tr>

                <td>Project Interpretation cost</td> <td>(USD)</td> <td><?php echo $project['Project_Interpretation_cost']; ?></td>

			</tr>

			<tr>

                <td>Project Road Cost</td> <td>(USD)</td> <td><?php echo $project['Project_road_Cost']; ?></td>

			</tr>

			<tr>

                <td>Project Accomodation cost</td> <td>(USD)</td> <td><?php echo $project['Project_Accomodation_cost']; ?></td>

			</tr>

			<tr>

                <td>Project Oil in Place</td> <td>(B)</td> <td><?php echo $project['Project_Oil_in_Place']; ?></td>

			</tr>

			<tr>

                <td>Project Projected Revenue</td> <td>(USD)</td> <td><?php echo $project['Project_Projected_Revenue']; ?></td>

			</tr>

			<tr>

                <td>Project Total Flowrate</td> <td>(B/D)</td> <td><?php echo $project['Project_Total_Flowrate']; ?></td>

			</tr>

			<?php

			if(isset($block))
			{
				?>

			<tr>

                <th colspan="3">Block parameters</th>

              </tr>

			<tr>

                <td>Block Name</td> <td> </td> <td><?php echo $block['Block_Name']; ?></td>

			</tr>

			<tr>

                <td>Block Surface</td> <td>(Ha) </td> <td><?php echo $block['Block_Surface']; ?></td>

			</tr>

			<tr>

                <td>License Cost Exp</td> <td>( USD/Ha)  </td> <td><?php echo $block['Block_License_Cost_Exp']; ?></td>

			</tr>

			<tr>

                <td>License cost Prod</td> <td>(USD/Ha)   </td> <td><?php echo $block['Block_License_cost_Prod']; ?></td>

			</tr>

			<tr>

                <td>Probability to find</td> <td> (%) </td> <td><?php echo $block['Probability_to_find']; ?></td>

			</tr>

			<tr>

                <td>Signature Bonus</td> <td> ($)</td> <td><?php echo $block['Signature_Bonus']; ?></td>

			</tr>

			<tr>

                <td>Block Survey Cost</td> <td> ( USD/Ha)</td> <td><?php echo $block['Block_Survey_Cost']; ?></td>

			</tr>

			<tr>

                <td>Block Survey interpretation cost</td> <td>( USD/Ha) </td> <td><?php echo $block['Block_Survey_interpretation_cost']; ?></td>

			</tr>



			<tr>

                <th colspan="3">Field parameters</th>

              </tr>

			<tr>

                <td>Field_Name</td> <td> UNIT </td> <td><?php echo $field['Field_Name']; ?></td>

			</tr>

			<tr>

                <td>Field_Average_TD</td> <td>  (Meters) </td> <td><?php echo $field['Field_Average_TD']; ?></td>

			</tr>

			<tr>

                <td>Field_Road_Cost</td> <td> (USD) </td> <td><?php echo $field['Field_Road_Cost']; ?></td>

			</tr>

			<tr>

                <td>Field_Accommodation_Cost</td> <td>  (USD) </td> <td><?php echo $field['Field_Accommodation_Cost']; ?></td>

			</tr>

			<tr>

                <td>Field_Drilling cost</td> <td> (USD/M) </td> <td><?php echo $field['Field_Drilling_Cost']; ?></td>

			</tr>

			<tr>

                <td>Field_Formation_Eval_cost_Exp</td> <td> (USD/M) </td> <td><?php echo $field['Field_Formation_Eval_cost_Exp']; ?></td>

			</tr>

			<tr>

                <td>Field_Formation_Eval_cost_Dev</td> <td> (USD/M) </td> <td><?php echo $field['Field_Formation_Eval_cost_Dev']; ?></td>

			</tr>

			<tr>

                <td>Field_Casing_Cement_cost</td> <td> (USD/M) </td> <td><?php echo $field['Field_Casing_Cement_cost']; ?></td>

			</tr>

			<tr>

                <td>Field_Testing_cost</td> <td> (USD/WELL) </td> <td><?php echo $field['Field_Testing_cost']; ?></td>

			</tr>

			<tr>

                <td>Field_Completion_Cost</td> <td> (USD/WELL) </td> <td><?php echo $field['Field_Completion_Cost']; ?></td>

			</tr>

			<tr>

                <td>Gen_Sup_Cost</td> <td>  (USD) </td> <td><?php echo $field['Other_Gen_Sup_Cost']; ?></td>

			</tr>

			<tr>

                <td>Tech_Sup_Cost</td> <td> (USD) </td> <td><?php echo $field['Other_Tech_Sup_Cost']; ?></td>

			</tr>

			<tr>

                <td>Field_Abandonment_cost</td> <td> (USD/WELL) </td> <td><?php echo $field['Field_Abandonment_cost']; ?></td>

			</tr>

			<tr>

                <td>Field_Water_Saturation</td> <td> (%) </td> <td><?php echo $field['Field_Water_Saturation']; ?></td>

			</tr>

			<tr>

                <td>Field_Porosity</td> <td>  (%)</td> <td><?php echo $field['Field_Field_Porosity']; ?></td>

			</tr>

			<tr>

                <td>Field_BO</td> <td> (B/STB) </td> <td><?php echo $field['Field_BO']; ?></td>

			</tr>

			<tr>

                <td>Field_Reservoir_Volume</td> <td> (B) </td> <td><?php echo $field['Field_Reservoir_Volume']; ?></td>

			</tr>

			<tr>

                <td>Field_Recovery_Factor</td> <td> (%) </td> <td><?php echo $field['Field_Recovery_Factor']; ?></td>

			</tr>

			<?php

			}
			if(isset($pfacility))
			{

		?>

			<tr>

                <th colspan="3">Production Facility parameters</th>

              </tr>

			<tr>

			<tr>

                <td>Prod_Facilities_Name</td> <td>   </td> <td><?php echo $pfacility['Prod_Facilities_Name']; ?></td>

			</tr>

			<tr>

                <td>Prod_Facilities_Capacity</td> <td> (B/D)	 </td> <td><?php echo $pfacility['Prod_Facilities_Capacity']; ?></td>

			</tr>

			<tr>

                <td>Prod_Facilities_Cost</td> <td>($)</td> <td><?php echo number_format((float)$pfacility['Prod_Facilities_Cost'], 0); ?></td>

			</tr>

			<tr>

                <td>Production_Facility_Status</td> <td>   </td> <td><?php echo $pfacility['Production_Facility_Satus']; ?></td>

			</tr>

			<tr>

                <td>Prod_Facilities_decommissioning_cost</td> <td>   </td> <td><?php echo $pfacility['Prod_Facilities_decommissioning_cost']; ?></td>

			</tr>

		<?php

			}

			if(isset($Field_ExpWell1))

			{

		?>

				

			<tr>

                <th colspan="3">WELLS </th>

              </tr>

			<tr>

			<tr>

                <td>ExpWell1_Name</td> <td>   </td> <td><?php echo $Field_ExpWell1['Well_Name']; ?></td>

			</tr>

			<tr>

                <td>ExpWell1_Flowrate</td> <td> ( B/Day)</td> <td><?php echo $Field_ExpWell1['Well_Flowrate']; ?></td>

			</tr>

			<tr>

                <td>ExpWell2_Name</td> <td>   </td> <td><?php echo $Field_ExpWell2['Well_Name']; ?></td>

			</tr>

			<tr>

                <td>ExpWell2_Flowrate</td> <td> ( B/Day)</td> <td><?php echo $Field_ExpWell2['Well_Flowrate']; ?></td>

			</tr>

			<tr>

                <td>AppWell1_Name</td> <td>   </td> <td><?php echo $Field_AppWell1['Well_Name']; ?></td>

			</tr>

			<tr>

                <td>AppWell1_Flowrate</td> <td> ( B/Day)</td> <td><?php echo $Field_AppWell1['Well_Flowrate']; ?></td>

			</tr>

			<tr>

                <td>AppWell2_Name</td> <td>   </td> <td><?php echo $Field_AppWell2['Well_Name']; ?></td>

			</tr>

			<tr>

                <td>AppWell2_Flowrate</td> <td> ( B/Day)</td> <td><?php echo $Field_AppWell2['Well_Flowrate']; ?></td>

			</tr>

			<tr>

                <td>DevWell1_Name</td> <td>   </td> <td><?php echo $Field_DevWell1['Well_Name']; ?></td>

			</tr>

			<tr>

                <td>DevWell1_Flowrate</td> <td> ( B/Day)</td> <td><?php echo $Field_DevWell1['Well_Flowrate']; ?></td>

			</tr>

			<tr>

                <td>DevWell2_Name</td> <td>   </td> <td><?php echo $Field_DevWell2['Well_Name']; ?></td>

			</tr>

			<tr>

                <td>DevWell2_Flowrate</td> <td> ( B/Day)</td> <td><?php echo $Field_DevWell2['Well_Flowrate']; ?></td>

			</tr>

			<tr>

                <td>DevWell3_Name</td> <td>   </td> <td><?php echo $Field_DevWell3['Well_Name']; ?></td>

			</tr>

			<tr>

                <td>DevWell3_Flowrate</td> <td> ( B/Day)</td> <td><?php echo $Field_DevWell3['Well_Flowrate']; ?></td>

			</tr>

			

		<?php

			}

		?>

			</tbody>

	</table>

	</div>

      <div class="modal-footer">

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

     <!--   <button type="button" class="btn btn-primary">Save changes</button>-->

      </div>

    </div><!-- /.modal-content -->

  </div><!-- /.modal-dialog -->

</div><!-- /.modal -->

<!-- MD3 DIALOG -->
<div id="md3-dialog-overlay" class="md3-dialog-overlay" style="display:none;"></div>
<div id="md3-dialog" class="md3-dialog" style="display:none;">
  <div class="md3-dialog__content">
    <div class="md3-dialog__icon">
      <span class="material-symbols-outlined" id="md3-dialog-icon">info</span>
    </div>
    <p class="md3-dialog__message" id="md3-dialog-message"></p>
    <div class="md3-dialog__actions" id="md3-dialog-actions"></div>
  </div>
</div>

<script>
var md3_callback = null;

function md3_alert(msg, icon) {
  icon = icon || 'info';
  document.getElementById('md3-dialog-message').textContent = msg;
  document.getElementById('md3-dialog-icon').textContent = icon;
  document.getElementById('md3-dialog-actions').innerHTML =
    '<button onclick="md3_closeDialog();" class="md3-btn md3-btn--filled">OK</button>';
  document.getElementById('md3-dialog').className = 'md3-dialog md3-dialog--alert';
  document.getElementById('md3-dialog-overlay').style.display = '';
  document.getElementById('md3-dialog').style.display = '';
}

function md3_confirm(msg, cb, icon) {
  icon = icon || 'help';
  md3_callback = cb;
  document.getElementById('md3-dialog-message').textContent = msg;
  document.getElementById('md3-dialog-icon').textContent = icon;
  document.getElementById('md3-dialog-actions').innerHTML =
    '<button onclick="md3_cancelDialog();" class="md3-btn md3-btn--outline">Cancel</button>' +
    '<button onclick="md3_execConfirm();" class="md3-btn md3-btn--filled">Confirm</button>';
  document.getElementById('md3-dialog').className = 'md3-dialog md3-dialog--confirm';
  document.getElementById('md3-dialog-overlay').style.display = '';
  document.getElementById('md3-dialog').style.display = '';
}

function md3_closeDialog() {
  document.getElementById('md3-dialog-overlay').style.display = 'none';
  document.getElementById('md3-dialog').style.display = 'none';
  md3_callback = null;
}

function md3_cancelDialog() {
  md3_callback = null;
  md3_closeDialog();
}

function md3_execConfirm() {
  var cb = md3_callback;
  md3_callback = null;
  md3_closeDialog();
  if (typeof cb === 'function') cb();
}
</script>
