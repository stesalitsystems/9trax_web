
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="12">Patrolling Report</th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="12">Report Generation Period - From : <?php echo $stdt;?> To : <?php echo $endt;?></th>
		</tr>
		<tr style="padding: -1px 0px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">SL No</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;" >Device No</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;" >Section</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Start Date Time</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Start Address</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">End Date Time</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">End Address</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">KM Run</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Overspeed</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Avg Speed</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Max Speed</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Start Low Battery</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			if(count($alldata) > 0) {
				// Grouping Data by Device ID
				$groupedData = [];
				foreach ($alldata as $row) {
					$deviceId = $row->imeino;
					
					if (!isset($groupedData[$deviceId])) {
						// Initialize the device entry with the first row values
						$groupedData[$deviceId] = $row;
						$groupedData[$deviceId]->actual_starttime = $row->actual_starttime; // First Start Time
						$groupedData[$deviceId]->actual_endtime = $row->actual_endtime; // Last End Time
						$groupedData[$deviceId]->startbattery = $row->startbattery; // Start battery
						$groupedData[$deviceId]->totaldistancetravel = $row->totaldistancetravel;
						$groupedData[$deviceId]->avg_speed = $row->avg_speed;
						$groupedData[$deviceId]->max_speed = $row->max_speed;
					} else {
						// Update the End Time with the latest occurrence
						$groupedData[$deviceId]->actual_endtime = $row->actual_endtime;
						$groupedData[$deviceId]->totaldistancetravel = $row->totaldistancetravel;
						$groupedData[$deviceId]->avg_speed += $row->avg_speed;
						$groupedData[$deviceId]->max_speed += $row->max_speed;
					}
				}

				$i=1; 
				foreach ($groupedData as $irow) {
					$avg_speed = number_format($irow->avg_speed,4);
					$startbattery = $irow->startbattery;

					if($avg_speed > 5) { $os = 'Yes';} else { $os = 'No';}
					if($startbattery > 50) { $os1 = 'No';} else { $os1 = 'No';}
			?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $i; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->imeino; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->organisation ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo date("Y-m-d H:i:s", strtotime($irow->actual_starttime)); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo ($irow->actual_stpole ?? '') ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo date("Y-m-d H:i:s", strtotime($irow->actual_endtime)); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo ($irow->actual_endpole ?? ''); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo number_format($irow->totaldistancetravel,4); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $os; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo number_format($irow->avg_speed,4); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo number_format($irow->max_speed,4); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $os1; ?></td>
			</tr>
			<?php
				$i++;
			} 
		} else { ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="4">No Records Found</td>
			</tr>
		<?php } ?>
	</tbody>           
</table>




