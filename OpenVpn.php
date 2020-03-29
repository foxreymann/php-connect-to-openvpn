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

    $command = 'openvpn "'.$directory.$configurationFile.'"';
    $this->pid = proc_open($command, [STDIN, STDOUT, STDOUT], $pipes);
  }

  function disconnect() {
    if ($this->pid) {
      proc_terminate($this->pid);
    }

    // as proc_terminate usually doens't work
    if ($this->isWindows && $this->pid) {
      $status = proc_get_status($this->pid);
      return exec('taskkill /F /T /PID '.$status['pid']);
    }

    // Linux
    $command = 'sudo killall openvpn';
    shell_exec($command);
  }

}

echo 'original ip:'.file_get_contents("http://ipecho.net/plain").PHP_EOL;

$openVpn = new OpenVpn();

$openVpn->connect();

sleep(10);
echo 'vpn ip:'.file_get_contents("http://ipecho.net/plain").PHP_EOL;
echo 'do some scraping now'.PHP_EOL;
sleep(10);

$openVpn->disconnect();

echo 'back to original ip:'.file_get_contents("http://ipecho.net/plain").PHP_EOL;
