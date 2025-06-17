
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="14">Trip Deviation Report For <?php echo $pwi_name; ?></th>
		</tr>
		<?php 
		if($schema == 'seas'){
		?>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="14">Chakradharpur Railway Division -- South Eastern Railway Zone</th>
		</tr>
		<?php } ?>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="14">Report Generation Period - From : <?php echo $date_from;?> To : <?php echo $date_to;?></th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">User Type</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Device Name</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Device ID</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Start Pole</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">End Pole</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Scheduled Start(DD-MM-YYYY HH:MM:SS</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Scheduled End(DD-MM-YYYY HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Actual Start(DD-MM-YYYY HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Actual End(DD-MM-YYYY HH:MM:SS)</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($alldata) && count($alldata) > 0) { ?>
			<?php foreach($alldata as $irow){ 
				if($irow->typeofuser == 'PatrolMan'){
					$schedulestarttime = $irow->schedulestarttime;
					$scheduleendtime = $irow->scheduleendtime;
					$startpoletime = $irow->startpoletime;
					$endpointtime = $irow->endpointtime;
					$startpole = $irow->startpole;
					$endpole = $irow->endpole;
				}
				else if($irow->typeofuser == 'Key Man'){
					$schedulestarttime = $irow->schedulestarttime;
					$scheduleendtime = $irow->scheduleendtime;
					$startpoletime = $irow->startpoletime;
					$endpointtime = $irow->endpointtime;
					$startpole = $irow->startpole;
					$endpole = $irow->endpole;
				}
				else{
					$schedulestarttime = 'NA';
					$scheduleendtime = 'NA';
					$startpoletime = 'NA';
					$endpointtime = 'NA';
					$startpole = 'NA';
					$endpole = 'NA';
				}
			?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->typeofuser; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->devicealiasname; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->devicename; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $startpole; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $endpole; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $schedulestarttime; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $scheduleendtime; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $startpoletime; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $endpointtime; ?></td>
			</tr>
			<?php } ?>
		<?php }else{ ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="9">No Records Found</td>
			</tr>
		<?php } ?>
	</tbody>           
</table>




