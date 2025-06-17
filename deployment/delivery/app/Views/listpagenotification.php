<div class="alert  <?php echo (isset($alertclass))?$alertclass:'alert-warning'?> alert-dismissible fade show" role="alert">
  <strong style="font-style:italic;"><?php echo $msg?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>