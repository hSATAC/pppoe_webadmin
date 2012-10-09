<?php if(isset($_SESSION['slim.flash']['ok_msg'])): ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong>
<?= $_SESSION['slim.flash']['ok_msg'] ?>
</div>
<?php endif; ?>
<?php if(isset($_SESSION['slim.flash']['err_msg'])): ?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Error!</strong>
<?= $_SESSION['slim.flash']['err_msg'] ?>
</div>
<?php endif; ?>
