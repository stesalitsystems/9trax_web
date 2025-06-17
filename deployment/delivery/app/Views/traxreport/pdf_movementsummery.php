
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="5">Movement Summery Report For <?php echo $pwi_name; ?></th>
		</tr>
		<?php 
		if($schema == 'seas'){
		?>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="5">Chakradharpur Railway Division -- South Eastern Railway Zone</th>
		</tr>
		<?php } ?>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="5">Report Generation Period - From : <?php echo $date_from;?> To : <?php echo $date_to;?></th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Date</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Trip No.</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Device Name</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Device ID</th>
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;">Travelled Distance</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($alldata) && count($alldata) > 0) { ?>
			<?php foreach($alldata as $irow){ 
			if($irow->acting_trip != ''){
			?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo date("d-m-Y", strtotime($irow->result_date)); ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->acting_trip; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mdddevicename; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mddserialno; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo round($irow->distance_cover/1000,2).' km'; ?></td>
			</tr>
			<?php } else { ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mdddevicename; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->mddserialno; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;">NA</td>
			</tr>
			<?php } ?>
			<?php } ?>
		<?php }else{ ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="5">No Records Found</td>
			</tr>
		<?php } ?>
	</tbody>           
</table>




