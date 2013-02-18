<?php
session_start();
require 'vendor/autoload.php';

require 'config.php';
require 'function.php';
$app = new \Slim\Slim();

# index
$app->get('/', function () use($app, $environments) {
  $whoami = 'unknown';
  foreach($environments as $name => $env) {
    if (isset($env['subnet']) && cidr_match($_SERVER['REMOTE_ADDR'], $env['subnet'])) {
      $whoami = $name;
    }
  }

  $app->render('index.tpl.php', array('whoami' => $whoami, 'environments' => $environments));
});

# iptable list
$app->get('/iptable', function () use($app, $environments) {

  $app->render('iptable.tpl.php', array("environments" => $environments));
});

# stauts check 
$app->get('/status', function () use($app, $environments) {

  $app->render('status.tpl.php', array("environments" => $environments));
});

# switch iptables env
$app->post('/iptable/switch', function () use($app, $environments) {
  $user = $app->request()->post('user');
  $env  = $app->request()->post('env');

  if(!array_key_exists($user, $environments)) {
    $app->flash('err_msg', "No such user ". $name);
    $app->redirect("/pppoe/iptable");
    exit;
  }

  // delete former rules first.
  delete_iptables($environments[$user]['subnet']);

  // insert new rule
  switch($env) {
  case "devm1":
    system(make_iptables_command('I', $environments[$user]['subnet'], $environments[$user]['http'], $environments[$user]['https'], '10.22.254.8', '10.22.254.9'));
    break;
  case "devm2":
    system(make_iptables_command('I', $environments[$user]['subnet'], $environments[$user]['http'], $environments[$user]['https'], '10.22.254.8', '10.22.254.8'));
    break;
  }

  $app->flash('ok_msg', "$user's environment has been switched to $env");
  $app->redirect("/pppoe/iptable");
});


# user profile
$app->get('/user/:name', function($name) use($app, $environments) {
  $app->render('user.tpl.php', array('whoami' => $name, 'environments' => $environments));
});

# radius add account
$app->post('/radius/add', function() use($app, $environments) {
  $name = $app->request()->post('name');

  if(!array_key_exists($name, $environments)) {
    $app->flash('err_msg', "No such user ". $name);
    $app->redirect("/pppoe/iptable");
    exit;
  }

  if(radius_sql_exists($name)) {
    $app->flash('err_msg', "Radius account ". $name . " exists!");
    $app->redirect("/pppoe/iptable");
    exit;
  }

  radius_insert_radcheck($name);
  radius_insert_radgroupcheck($name);
  radius_insert_radippool($name);
  radius_insert_radusergroup($name);

  $app->flash('ok_msg', $name . "'s Radius account has been created.");
  $app->redirect("/pppoe/iptable");
});

$app->run();
