
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="8">Trip Summary Details Report</th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="8">Report Generation Period - From : <?php echo $stdt;?> To : <?php echo $endt;?></th>
		</tr>
		<tr style="padding: -1px 0px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">SL No</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">PWI</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Section Name</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Off Device</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Beat Covered</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Beat Not Covered</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Not Allocated</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Total</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($alldata) && count($alldata) > 0) { 
			$i= 1;
			$device_off_count = 0;
			$beats_covered_count = 0;
			$beats_not_covered_count = 0;
			$not_allocated_count = 0;
			foreach($alldata as $irow){ ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $i; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->parent_organisation; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->organisation; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->device_off_count; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->beats_covered_count; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->beats_not_covered_count; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->not_allocated_count; ?></td>

				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo ($irow->beats_not_covered_count+$irow->device_off_count+$irow->beats_covered_count+$irow->not_allocated_count ); ?></td>

			</tr>
			<?php 
				$i++;
				    $device_off_count = $irow->device_off_count + $device_off_count;
					$beats_covered_count = $irow->beats_covered_count + $beats_covered_count;
					$beats_not_covered_count = $irow->beats_not_covered_count + $beats_not_covered_count;
					$not_allocated_count = $irow->not_allocated_count + $not_allocated_count;

			} ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="3" align="right">Total</td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $device_off_count; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $beats_covered_count; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $beats_not_covered_count; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $not_allocated_count; ?></td>

				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo ($not_allocated_count+$device_off_count+$beats_covered_count+$beats_not_covered_count); ?></td>

			</tr>
		<?php }else{ ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;" colspan="5">No Records Found</td>
			</tr>
		<?php } ?>
	</tbody>           
</table>




