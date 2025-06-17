
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="16">Device Status Report For <?php echo $pwi_name; ?></th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="16">Report Generation Period - From : <?php echo $date_from;?> To : <?php echo $date_to;?></th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Date</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Device Name</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Device ID</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">PWI</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Type</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Trip No.</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Start Time(HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">End Time(HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Travelled Distance</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Travelled Time(HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Stop Duration(HH:MM:SS)</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">No. of SOS</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">No. of Call</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">No. of Alert</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Start Pole</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">End Pole</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($alldata) && count($alldata) > 0) { ?>
			<?php foreach($alldata as $irow){ 
			$mdddevicename_arr = explode("/",$irow->mdddevicename);
			if(count($mdddevicename_arr) > 1) {
				if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
					$type = 'Stock';
				}
				else if (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
					$type = 'Keyman';
				}
				else if (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
					$type = 'Patrolman';
				}
				$pwi_arr = explode("(",$mdddevicename_arr[3]);
			} else {
				$type = 'NA';
				$pwi_arr[0] = 'NA';
			}
			
			if($irow->acting_trip != ''){
			?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo date("d-m-Y", strtotime($irow->result_date)); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mdddevicename; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mddserialno; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $pwi_arr[0]; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $type; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->acting_trip; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->start_time; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->end_time; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo round($irow->distance_cover/1000,2).' km'; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->duration; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->totalstoptime; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->sos_no; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->call_no; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->alert_no; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->polename; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->polenameend; ?></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mdddevicename; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mddserialno; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $pwi_arr[0]; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $type; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
			</tr>
			<?php } ?>
			<?php } ?>
		<?php }else{ ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="16">No Records Found</td>
			</tr>
		<?php } ?>
	</tbody>           
</table>




