
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-fw fa-bar-chart"></i> <?php echo $page_title;?>
        </div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="lists" width="100%" cellspacing="0">
					<thead>
						<tr>
						<th>From</th>
						<th>To</th>
						<th>Report Type</th>
						<th>PWI</th>
						<th>Action</th>
						</tr>
					</thead>          
					<tbody class="reportlists-body">
						<?php 
						if(is_array($alldata) && count($alldata) > 0) {
						foreach($alldata as $irow){
						?>
						<tr>							
							<td><?php echo date("d-m-Y H:i:s", strtotime($irow->date_from))?></td>
							<td><?php echo date("d-m-Y H:i:s", strtotime($irow->date_to))?></td>
							<td><?php echo $irow->report_type_name?></td>
							<td><?php echo $irow->pwi_name?></td>
							<td>
								<div class="dropdown">
								<button class="btn ddaction dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-gear"></i><span class="caret"></span></button>
								<ul class="dropdown-menu action-dd">
									<li><a href="<?php echo site_url('/').'traxreport/savedreportpdf/'.$irow->id;?>"><i class="fa fa-download" aria-hidden="true"></i> PDF</a></li>
									<li><a href="<?php echo site_url('/').'traxreport/savedreportexcel/'.$irow->id;?>"><i class="fa fa-download" aria-hidden="true"></i> Excel</a></li>
									<?php $del_url = site_url('/').'traxreport/delstatuschange/'.$irow->id.'/2'; ?>
									<li><a href="<?php echo $del_url;?>" class="delstatuschange"><i class="fa fa-remove" aria-hidden="true"></i> Delete</a></li>
								</ul>
								</div>
							</td>
						</tr>
						<?php } } else { ?>
						<tr>
							<td colspan="5">No Records Found</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
    </div>
	
    <script>
        $(document).ready(function () {
			var dtble;
			function buildDataTable(){
				dtble = $('#lists').DataTable({
					searching: false,
					bSort: false,
					lengthChange: false
				});
			}
			<?php if(!empty($alldata)){ ?>
				buildDataTable();
			<?php } ?>
			
			$(document).on("click", ".delstatuschange", function(e){
				e.preventDefault();
				var r = confirm("Are you sure you want to delete?");
				if (r == true) {					
					var link = $(this).attr('href');
					//alert(link);
					$.ajax({
						url: link,
						type: "POST",
						data: {},
						success: function (data)
						{
							var recvdJson = JSON.parse(data);
							openAlert('alert-success', "Deleted Successfully...");
							location.reload();
							
						},
						error: function (data){
							openAlert('alert-danger', "Failed to delete....");
						}
					});
				}
			});
        });
    </script>