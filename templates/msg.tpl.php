<?php if(isset($_SESSION['slim.flash']['ok_msg'])): ?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Success!</strong>
<?= $_SESSION['slim.flash']['ok_msg'] ?>
</div>
<?php endif; ?>
