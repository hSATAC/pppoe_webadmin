<?php
function cidr_match($ip, $range)
{
  list ($subnet, $bits) = explode('/', $range);
  $ip = ip2long($ip);
  $subnet = ip2long($subnet);
  $mask = -1 << (32 - $bits);
  $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
  return ($ip & $mask) == $subnet;
}

function make_iptables_command($action, $subnet, $http_port, $https_port, $from, $to)
{
  $command = <<<COMMAND
sudo iptables -t nat -$action PREROUTING -s $subnet -d $from -p tcp -m tcp --dport $http_port -j DNAT --to-destination $to:$http_port
sudo iptables -t nat -$action PREROUTING -s $subnet -d $from -p tcp -m tcp --dport 80 -j DNAT --to-destination $to:$http_port
sudo iptables -t nat -$action PREROUTING -s $subnet -d 192.168.254.9 -p tcp -m tcp --dport 80 -j DNAT --to-destination $to:$http_port
sudo iptables -t nat -$action PREROUTING -s $subnet -d 192.168.254.9 -p tcp -m tcp --dport $http_port -j DNAT --to-destination $to:$http_port
sudo iptables -t nat -$action PREROUTING -s $subnet -d $from -p tcp -m tcp --dport $https_port -j DNAT --to-destination $to:$https_port
sudo iptables -t nat -$action PREROUTING -s $subnet -d $from -p tcp -m tcp --dport 443 -j DNAT --to-destination $to:$https_port
sudo iptables -t nat -$action PREROUTING -s $subnet -d 192.168.254.9 -p tcp -m tcp --dport 443 -j DNAT --to-destination $to:$https_port
sudo iptables -t nat -$action PREROUTING -s $subnet -d 192.168.254.9 -p tcp -m tcp --dport $https_port -j DNAT --to-destination $to:$https_port
COMMAND;
  return $command;
}

function which_env($name)
{
  $env = "unknown";
  global $environments;

  if(!array_key_exists($name, $environments)) return $env;

  $subnet = $environments[$name]['subnet'];
  $current_env = explode(":", exec("sudo iptables -L -t nat -n | grep $subnet | head -n 1"));
  if(count($current_env) == 0) return $env;
  switch ($current_env[2]) {
  case "10.22.254.9":
    $env = "devm1";
    break;
  case "10.22.254.8":
    $env = "devm2";
    break;
  }

  return $env;
}

# check if radius account exists
function radius_sql_exists($name)
{
  $exists = false;
  global $environments;
  
  if(!array_key_exists($name, $environments)) return $exists;

  $id = $environments[$name]['_id'] . "@miiicasa.com";

  $dbconn = pg_connect("host=localhost port=5432 dbname=radius user=postgres");
  $result = pg_query_params($dbconn, 'SELECT * FROM radcheck WHERE username = $1', array($id));
  if(pg_num_rows($result) > 0) $exists = true;
  
  pg_close($dbconn);

  return $exists;
}

function radius_insert_radcheck($name)
{
  global $environments;
  
  if(!array_key_exists($name, $environments)) return false;

  $id = $environments[$name]['_id'] . "@miiicasa.com";
  $id_static = $environments[$name]['_id'] . "@ip.miiicasa.com";

  $dbconn = pg_connect("host=localhost port=5432 dbname=radius user=postgres");
  pg_query_params($dbconn, 'INSERT INTO radcheck (username, attribute, op, value) VALUES ($1, \'Cleartext-Password\', \':=\', \'53345405\');', array($id));
  pg_query_params($dbconn, 'INSERT INTO radcheck (username, attribute, op, value) VALUES ($1, \'Cleartext-Password\', \':=\', \'53345405\');', array($id_static));
  
  pg_close($dbconn);

  return true;
}
function radius_insert_radgroupcheck($name)
{
  global $environments;
  
  if(!array_key_exists($name, $environments)) return false;

  $id = $environments[$name]['_id'];

  $dbconn = pg_connect("host=localhost port=5432 dbname=radius user=postgres");
  pg_query_params($dbconn, 'INSERT INTO radgroupcheck (groupname, attribute, op, value) VALUES ($1, \'Pool-Name\', \':=\', $1);', array($id));
  
  pg_close($dbconn);

  return true;
}

function radius_insert_radippool($name)
{

  global $environments;
  
  if(!array_key_exists($name, $environments)) return false;

  $id = $environments[$name]['_id'];
  $subnet = $environments[$name]['subnet'];

  $ary = explode('.', $subnet);
  unset($ary[3]);
  $subnet = implode('.', $ary);

  $dbconn = pg_connect("host=localhost port=5432 dbname=radius user=postgres");
  for($i = 2; $i <=16; $i++) {
    pg_query_params($dbconn, 'INSERT INTO radippool (pool_name, framedipaddress) VALUES ($1, $2);', array($id, $subnet . '.'.$i));
  }
  pg_close($dbconn);

  return true;
}

function radius_insert_radusergroup($name)
{

  global $environments;
  
  if(!array_key_exists($name, $environments)) return false;

  $id = $environments[$name]['_id'];
  $id_dynamic = $environments[$name]['_id'] . "@miiicasa.com";
  $id_static = $environments[$name]['_id'] . "@ip.miiicasa.com";

  $dbconn = pg_connect("host=localhost port=5432 dbname=radius user=postgres");
  pg_query_params($dbconn, 'INSERT INTO radusergroup (username, groupname, priority) VALUES ($1, $2, 0);', array($id_dynamic, $id));
  pg_query_params($dbconn, 'INSERT INTO radusergroup (username, groupname, priority) VALUES ($1, $2, 0);', array($id_static, 'RD'));
  pg_close($dbconn);

  return true;
}
