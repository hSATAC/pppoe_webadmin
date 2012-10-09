<?php
session_start();
require 'vendor/autoload.php';

require 'config.php';
require 'function.php';
$app = new \Slim\Slim();
$app->get('/', function () use($app, $environments) {
    $whoami = 'unknown';
    foreach($environments as $name => $env) {
      if (isset($env['subnet']) && cidr_match($_SERVER['REMOTE_ADDR'], $env['subnet'])) {
	$whoami = $name;
      }
    }
 
    $app->render('index.tpl.php', array('whoami' => $whoami, 'environments' => $environments));
});

$app->get('/iptable', function () use($app, $environments) {
 
    $app->render('iptable.tpl.php', array("environments" => $environments));
});

$app->post('/iptable/switch', function () use($app, $environments) {
    $user = $app->request()->post('user');
    $env  = $app->request()->post('env');

    if(array_key_exists($user, $environments)) {
    
      // delete former rules first.
      system(make_iptables_command('D', $environments[$user]['subnet'], $environments[$user]['http'], $environments[$user]['https'], '10.22.254.9', '10.22.254.8'));
      system(make_iptables_command('D', $environments[$user]['subnet'], $environments[$user]['http'], $environments[$user]['https'], '10.22.254.9', '10.22.254.9'));
      
      // insert new rule
      switch($env) {
          case "devm1":
              system(make_iptables_command('I', $environments[$user]['subnet'], $environments[$user]['http'], $environments[$user]['https'], '10.22.254.9', '10.22.254.9'));
      break;
      case "devm2":
              system(make_iptables_command('I', $environments[$user]['subnet'], $environments[$user]['http'], $environments[$user]['https'], '10.22.254.9', '10.22.254.8'));
      break;
      }
    }
    $app->flash('ok_msg', "$user's environment has been switched to $env");
    $app->redirect("/pppoe/iptable");
});


$app->run();
