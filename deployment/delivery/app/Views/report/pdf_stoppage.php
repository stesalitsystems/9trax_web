
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="6">Stoppage Report</th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="6">Report Generation Period - From : <?php echo $stdt;?> To : <?php echo $endt;?></th>
		</tr>
		<tr style="padding: -1px 0px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">SL No</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;" >Device No</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;" >Device Name</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Stoppage Start Time</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Stoppage Address</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Halt Time</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($alldata) && count($alldata) > 0) { 
			$i = 1;
			$imeino = '';
			$groupOpen = false;
			foreach($alldata as $irow){
				if($irow->stoppage_duration != '00:00:00') { 
					$stoppage_duration = strtotime($irow->stoppage_duration);

					$seconds = (int) $stoppage_duration; // Ensure it's an integer

					$days = floor($seconds / 86400); 
					$seconds %= 86400; 

					$hours = floor($seconds / 3600);
					$seconds %= 3600;

					$minutes = floor($seconds / 60);
					$seconds %= 60;

					$formatted = "{$hours} hours {$minutes} mins {$seconds} sec";
			?>
			<tr>
				<?php if ($irow->imeino != $imeino) { ?>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $i; ?></td>
				<?php 
					$i++;
				} else { ?>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"></td>
				<?php } ?>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->imeino; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->device_name ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo date("Y-m-d H:i:s", strtotime($irow->stoppage_start)); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->pole ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $formatted; ?></td>
			</tr>
			<?php
				 
				$imeino = $irow->imeino;
			} 
		}} else { ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="4">No Records Found</td>
			</tr>
		<?php } ?>
	</tbody>           
</table>




