<div class="container-fluid">
    
    <!-- Example DataTables Card-->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-comment"></i> <?php echo $page_title;?>
        </div>

		
		<div class="search-panel">
			<div class="col-xs-12 col-md-12">
				<?php if(isset($errmsg) && !empty($errmsg)){?>
				<div class="error"><?php echo $errmsg?></div>
				<?php } ?>
			</div>
			
			<form method="post" action="<?= base_url('comment/marketingcomment')?>" autocomplete="off" id="marketingcomment" novalidate="true">
				<?= csrf_field() ?>
				<div class="form-row">					
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label for="exampleInputEmail1">Title *</label>
                        <input type="text" class="form-control" name="title" id="title" value="<?= old('title') ?>" />
					</div>				
				</div>
                <div class="clearfix"></div>
                <div class="form-row">					
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<label for="exampleInputEmail1">Message *</label>
						<textarea class="form-control" rows="2" maxlength="200" name="message" id="message"><?= old('message'); ?></textarea>
					</div>				
				</div>
				<div class="clearfix"></div>
				<div class="form-row">      
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<button type="submit" class="btn btn-primary pull-right" name="add" value="add">Submit</button>
					</div>                
				</div>
			</form>
			
		</div>
	
	</div>
</div>

<script>
$(document).ready(function(){
	$("#marketingcomment").validate({
		rules: {
			message: {
				required: true
			}
		}
	});
});
</script>