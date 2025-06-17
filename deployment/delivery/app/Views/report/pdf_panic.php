
<table style="border: 1px solid #ddd;width: 100%;max-width: 100%;margin-bottom: 11px;"  width="100%" cellspacing="0">
	<thead>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="4">SOS/Panic Report</th>
		</tr>
		<tr style="padding: 11px 10px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: 11px 10px;color: #fff; border: 0.5px solid #fff;" colspan="4">Report Generation Period - On : <?php echo $dtt;?></th>
		</tr>
		<tr style="padding: -1px 0px;background: #37759c;">
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">SL No</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Device No</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Device Name</th>
			<th align="center" class="hidden-phone" style="padding: -1px 0px;color: #fff; border: 0.5px solid #fff;">Time</th>
		</tr>
	</thead>
	<tbody>
		<?php if (is_array($alldata) && count($alldata) > 0) { 
			$i = 1;
			foreach($alldata as $irow){ ?>
			<tr>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $i; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->serial_no; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->device_name; ?></td>
				<td align="center" class="hidden-phone" style="padding: 11px 10px;border: 1px solid black;"><?php echo $irow->currentdate . ' ' . $irow->currenttime; ?></td>
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




