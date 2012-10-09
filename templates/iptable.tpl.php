<?php require("header.tpl.php") ?>

<?php require("nav.tpl.php") ?>
    <div class="container">

<?php require("msg.tpl.php") ?>

      <h1>iptable</h1>

      <h2>Switch Env</h2>

      <p>
      <form method="POST" action="/pppoe/iptable/switch">
      <label>User</label>
      <select name="user">
      <?php foreach($environments as $name => $env):?>
	<option><?= $name ?></option>
      <?php endforeach; ?>
      </select>

      <label>Env</label>
      <select name="env">
        <option>devm1</option>
        <option>devm2</option>
      </select>
      <button type="input" class="btn btn-primary">Submit</button>
      </form>
      </p>
      
      <h2>All User Env</h2>
        <div>
          <table class="table">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Env</th>
                <th>Subnet</th>
                <th>Port</th>
                <th>HTTPS Port</th>
              </tr>
            </thead>
      <?php
          $env_index = 1;
          foreach($environments as $name => $env):
          $user_env = which_env($name);  
      ?> 
            <tbody>
              <?php if($user_env == 'devm2'): ?>
              <tr class="success">
              <?php else: ?>
              <tr>
              <?php endif; ?>
                <td><?=$env_index?></td>
                <td><?=$name?></td>
                <td><?=$user_env?></td>
                <td><?=$env['subnet']?></td>
                <td><?=$env['http']?></td>
                <td><?=$env['https']?></td>
              </tr>
            </tbody>
      <?php $env_index++; ?>
      <?php endforeach; ?>
          </table>
        </div>
    </div> <!-- /container -->
<?php require("footer.tpl.php") ?>
<script>
$(document).ready(function(){
    $("#nav_iptable").addClass("active");
});
</script>
<?php require("eof.tpl.php") ?>
