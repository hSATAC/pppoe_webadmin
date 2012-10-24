<?php require("header.tpl.php") ?>

<?php require("nav.tpl.php") ?>
    <div class="container">

      <h1><?=$whoami?></h1>

      <h2>Current environment</h2>

      <p>
      <span class="label label-info">Info</span> <?=which_env($whoami)?>
      </p>

      <h2>Current rule</h2>

      <pre>
      <?php

    if(array_key_exists($whoami, $environments)) {
      $subnet = $environments[$whoami]['subnet'];
      system("sudo iptables -L -t nat -n | grep $subnet");
    }
	?>
      </pre>
      <?php

        if(array_key_exists($whoami, $environments)) {
          $rule_count = exec("sudo iptables -L -t nat -n | grep $subnet | wc -l");
        
          if ((int)$rule_count != 6){
      ?>
      <div class="alert">
	  <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>Warning!</strong> You have <?=$rule_count?> iptable rules, you should have 8. Please contact Ash.
      </div>

      <?php
          }
        }
      ?>

      <h2>Radius Account</h2>
      <p>
      <?php if(radius_sql_exists($whoami)): ?>
      <span class="label label-success">OK</span> Radius account exists.
      <?php else: ?>
      <span class="label label-important">ERROR</span> Radius account doesn't exist!
      <form method="POST" action="/pppoe/radius/add">
      <input type="hidden" name="name" value="<?=$whoami?>">
      <button type="submit" class="btn btn-success">Create</button>
      </form>
      <?php endif; ?>
      </p>

    </div> <!-- /container -->
<?php require("footer.tpl.php") ?>
<script>
$(document).ready(function(){
    $("#iptable_index").addClass("active");
});
</script>
<?php require("eof.tpl.php") ?>
