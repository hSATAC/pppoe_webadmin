<?php require("header.tpl.php") ?>

<?php require("nav.tpl.php") ?>
    <div class="container">

      <h1>Home</h1>

      <h2>Who Are You?</h2>

      <p>Your ip is <?=$_SERVER["REMOTE_ADDR"]?>, you're  <?= $whoami ?></p>
      
      <h2>Your current environment</h2>

      <p>
      <span class="label label-info">Info</span> <?=which_env($whoami)?>
      </p>

      <h2>Your current rule</h2>

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
        
          if ((int)$rule_count != 8){
      ?>
      <div class="alert">
	  <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>Warning!</strong> You have <?=$rule_count?> iptable rules, you should have 6. Please contact Ash.
      </div>

      <?php
          }
        }
      ?>


    </div> <!-- /container -->
<?php require("footer.tpl.php") ?>
<script>
$(document).ready(function(){
    $("#nav_index").addClass("active");
});
</script>
<?php require("eof.tpl.php") ?>
