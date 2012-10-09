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
