
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="14">Trip Report For <?php echo $pwi_name; ?></th>
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
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Start(DD-MM-YYYY HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">End(DD-MM-YYYY HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Within Pole Distance</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Total Distance</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Travelled Time(HH:MM:SS)</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($alldata) && count($alldata) > 0) { ?>
			<?php foreach($alldata as $irow){ 
				if($irow->typeofuser == 'PatrolMan'){
					$start = date("d-m-Y", strtotime($irow->sessiondate)).' '.$irow->starttime;
					$end = date("d-m-Y", strtotime($irow->sessiondate . ' +1 day')).' '.$irow->endtime;
				}
				else if($irow->typeofuser == 'Key Man'){
					$start = date("d-m-Y", strtotime($irow->sessiondate)).' '.$irow->starttime;
					$end = date("d-m-Y", strtotime($irow->sessiondate)).' '.$irow->endtime;
				}
				else{
					$start = 'NA';
					$end = 'NA';
				}
				if($irow->orginallength < $irow->distance){
					$irow->orginallength = $irow->distance;
				}
			?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->typeofuser; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->devicealiasname; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->devicename; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $start; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $end; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo round($irow->distance/1000,2).' km'; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo round($irow->orginallength/1000,2).' km'; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->duration; ?></td>
			</tr>
			<?php } ?>
		<?php }else{ ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="8">No Records Found</td>
			</tr>
		<?php } ?>
	</tbody>           
</table>




