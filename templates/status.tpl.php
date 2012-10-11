<?php require("header.tpl.php") ?>

<?php require("nav.tpl.php") ?>
    <div class="container">

      <h1>Status</h1>

      <h3>iptables</h3>
      <?php
      $iptables_status = exec("/etc/init.d/iptables status");
      if ($iptables_status != " * status: started"):
      ?>
      <span class="label label-important">Error</span> iptables is not running. Please run `/etc/init.d/iptables start` to launch the service.
      <?php else:?>
      <span class="label label-success">OK</span> iptables is running.
      <?php endif;?>

      <h3>sshd</h3>
      <?php
      $sshd_status = exec("/etc/init.d/sshd status");
      if ($sshd_status != " * status: started"):
      ?>
      <span class="label label-important">Error</span> sshd is not running. Please access lab vm console to launch sthe service.
      <?php else:?>
      <span class="label label-success">OK</span> sshd is running.
      <?php endif;?>

      <h3>dnsmasq</h3>
      <?php
      $dnsmasq_status = exec("/etc/init.d/dnsmasq status");
      if ($dnsmasq_status != " * status: started"):
      ?>
      <span class="label label-important">Error</span> dnsmasq is not running. Please run `/etc/init.d/dnsmasq start` to launch the service.
      <?php else:?>
      <span class="label label-success">OK</span> dnsmasq is running.
      <?php endif;?>

      <h3>postgresql</h3>
      <?php
      $post_status = exec("ps aux | grep postgresql | grep -v grep | wc -l");
      if ($post_status != "1"):
      ?>
      <span class="label label-important">Error</span> postgresql is not running. Please run `/etc/init.d/postgresql-9.1 start` to launch the service.
      <?php else:?>
      <span class="label label-success">OK</span> postgresql is running.
      <?php endif;?>

      <h3>radiusd</h3>
      <?php
      $radiusd_status = exec("/etc/init.d/radiusd status");
      if ($radiusd_status != " * status: started"):
      ?>
      <span class="label label-important">Error</span> radiusd is not running. Please run `/etc/init.d/radiusd start` to launch the service.
      <?php else:?>
      <span class="label label-success">OK</span> radiusd is running.
      <?php endif;?>

      <h3>pppoe-server</h3>
      <?php
      $pppoe_status = exec("ps aux | grep /usr/sbin/pppoe-server | grep -v grep | wc -l");
      if ($pppoe_status != "1"):
      ?>
      <span class="label label-important">Error</span> pppoe-server is not running. Please run `/root/bin/pppoe-server-192.168.79.61` to launch the service.
      <?php else:?>
      <span class="label label-success">OK</span> pppoe-server is running.
      <?php endif;?>

      <h3>devm1 vpn</h3>
      <?php
      $devm1_status = exec("ping -W 1 -c 1 10.22.254.9 | grep time= | wc -l");
      if ($devm1_status != "1"):
      ?>
      <span class="label label-important">Error</span> devm1 vpn is not connected.
      <?php else:?>
      <span class="label label-success">OK</span> devm1 vpn is connected.
      <?php endif;?>

      <h3>devm2 vpn</h3>
      <?php
      $devm2_status = exec("ping -W 1 -c 1 10.22.254.8 | grep time= | wc -l");
      if ($devm2_status != "1"):
      ?>
      <span class="label label-important">Error</span> devm2 vpn is not connected.
      <?php else:?>
      <span class="label label-success">OK</span> devm2 vpn is connected.
      <?php endif;?>

  </div> <!-- /container -->
<?php require("footer.tpl.php") ?>
<script>
$(document).ready(function(){
    $("#nav_status").addClass("active");
});
</script>
<?php require("eof.tpl.php") ?>
