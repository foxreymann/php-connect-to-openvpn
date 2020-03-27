<?php
// namespace Clu;

/* DOCUMENTATION

$openVpn = new OpenVpn();
$openVpn->connect();

echo 'scraping when vpn connect is in the background';
sleep(20);
$openVpn->disconnect();

https://nordvpn.com/tutorials/linux/openvpn/
https://help.vpntunnel.com/support/solutions/articles/5000613671-how-do-i-save-my-username-password-in-openvpn-for-automatic-login-

*/

class OpenVpn {
  private $isWindows;
  private $pid = null;


  function __construct() {
    $this->isWindows = (stripos(PHP_OS, 'WIN') === 0);
  }


  function connect() {
      $directory = $this->isWindows ? 'C:\\Program Files\\OpenVPN\\config\\ovpn_tcp\\' : '/etc/openvpn/ovpn_tcp/';
      $configs = array_diff(scandir($directory), array('..', '.'));
      $numberOfConfigs = count($configs);
      $configurationFile = $configs[rand(2,$numberOfConfigs+1)];

      if($this->isWindows) {
        $command = 'openvpn "'.$directory.$configurationFile.'"';
        $this->pid = proc_open($cmd, [STDIN, STDOUT, STDOUT], $pipes); 
      } else {
        $command = 'sudo bash -c "exec nohup openvpn '.$directory.$configurationFile.' &> /dev/null &"';
        echo $command;
        exec($command);
      }

  }

  function disconnect() {
      if ($this->pid) {
        proc_terminate($this->pid);
      }

      $command = 'sudo killall openvpn';
      shell_exec($command);
  }

}

echo 'original ip:'.file_get_contents("http://ipecho.net/plain").PHP_EOL;

$openVpn = new OpenVpn();
$openVpn->connect();

sleep(20);
echo 'vpn ip:'.file_get_contents("http://ipecho.net/plain").PHP_EOL;
echo 'do some scraping now'.PHO_EOL;

$openVpn->disconnect();

echo 'back to original ip:'.file_get_contents("http://ipecho.net/plain").PHP_EOL;
