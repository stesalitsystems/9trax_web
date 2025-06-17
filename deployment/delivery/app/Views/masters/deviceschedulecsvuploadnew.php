
<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">

			<div class="card-header">
				<span class="card-header-title"><i class="fa fa-mobile"></i>  <?php echo $page_title;?></span>
				<span class="pull-right" style="margin-right: 0.5em;"><a class="btn btn-primary btn-sm moduleaddbtn"  href="<?php echo base_url();?>downloads/FORMAT EXCEPTION REPORT Details of GPS Tracker 2023-24 in ALL PWAY MERGED.csv" title="Download Exception data" dat_ifrheight="270">Download Exception Data</a></span>
			</div>
			<div class="search-panel">
				<div class="col-xs-12 col-md-12">
				<?php if (session()->has('msg')): ?>
					<div class="alert alert-info">
						<ul>
							<li><?= session()->getFlashdata('msg') ?></li>
						</ul>
					</div>
				<?php endif; ?>
				<?php if (session()->has('errors')): ?>
					<div class="alert alert-danger">
						<ul>
							<?php foreach (session('errors') as $error): ?>
								<li><?= esc($error) ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				</div>

				<form action='<?= base_url("masters/scheduleupdatecsv") ?>' method="post" autocomplete="off" id="deviceupload" novalidate="true" class="form-actions" enctype="multipart/form-data">
					<div class="form-row"> 
						<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
							<label for="exampleInputEmail1">CSV<span class="text-danger">*</span></label>
							<input type="file" class="form-control" name="csvfile" id="csvfile"  align="center" required/>
						</div>				
					</div>
					<div class="clearfix"></div>
					<div class="form-row">      
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="submit" class="btn btn-primary pull-right" name="upload">Upload</button>
							<a href="<?php echo site_url('masters/scheduleupdatecsv/')?>" class="btn btn-danger">Back</a>
						</div>                
					</div>
				</form>

			</div>

	</div>
</div>

