<?php

/**
 * Class for reading BIP config
 *
 * @copyright	http://www.mekdrop.name
 * @license		http://www.opensource.org/licenses/lgpl-3.0.html
 * @package     main
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 * @version     $Id$
 */
class BIPConfig {

    public $vars = array(
        'ip' => '0.0.0.0',
        'port' => 7778,
        'client_side_ssl' => false,
        'client_side_ssl_pem' => '/path/to/pemfile',
        'pid_file' => '/opt/var/run/bip.pid',
        'log' => true,
        'log_level' => 3,
        'log_root' => '/opt/var/log/proxy',
        'log_system' => true,
        'log_format' => '%u/%n/%Y-%m/%c.%d.log',
        'log_sync_interval' => 5
    );
    public $vars_description = array(
        'ip' => 'Listening IP address. This is the IP address bip will listen for incoming
							 client connections.',
        'port' => 'To connect a client to bip, try the port below, and
							   be sure to set the password to the value
							   specified in the network you want to connect to. 
							   Port is 6667 by default.',
        'client_side_ssl' => 'If you set this to true, you\'ll only be able to connect to bip
										  with a SSL capable IRC client. Be sure to generate a certificate
										  for bip using scripts/bipgenconfig.',
        'client_side_ssl_pem' => 'This is the file containing the SSL cert/key pair bip\'ll use to
											  serve SSL clients. If unset, it defaults to <biphome>/bip.pem',
        'pid_file' => 'Define where the pidfile should be stored. Defaults to <biphome>/bip.pid',
        'log' => 'Uncomment this line to disable logging and backlogging.',
        'log_level' => 'Define bip\'s log level :
									0 : only fatal errors
									1 : add others errors
									2 : add warnings
									3 : add info messages
									4 : add debug messages',
        'log_root' => 'This is where logs go. Channel and private messages will use that
								   configuration value as a prefix, and then log_format to determine
								   full log filename.',
        'log_system' => 'Uncomment this line to disable bip\'s internal messages logging.
									This is not recommended, a better option is to reduce log_level.',
        'log_format' => 'Log format allows you to make log filenames depend on the log line\'s
									 attributes. Here\'s a list :
									 %u -> user name
									 %n -> network name
									 %Y -> 4 digit year
									 %m -> 2 digit month
									 %d -> 2 digit day
									 %c -> destination (#chan, privates, ...)',
        'log_sync_interval' => 'Sets the frequency (in seconds) of log syncing (real write to kernel)',
    );
    private $filename;

    function BIPConfig($filename) {
        $this->filename = $filename;
        if (isset($_SESSION['bip_config'][$filename]) && isset($_SESSION['bip_config_ftime']) && $_SESSION['bip_config_ftime'] > filemtime($filename)) {
            $this->vars = &$_SESSION['bip_config'][$filename];
        } else {
            $this->read();
            $_SESSION['bip_config'] = array($filename => &$this->vars);
            $_SESSION['bip_config_ftime'] = filemtime($filename);
        }
    }

    function &getNetworksNames() {
        $rez = array();
        foreach ($this->vars['network'] as $network) {
            $rez[] = $network['name'];
        }
        return $rez;
    }

    function &getUsersNames() {
        $rez = array();
        foreach ($this->vars['user'] as $user) {
            if (!isset($user['name'])) {
                continue;
            }
            $rez[] = $user['name'];
        }
        return $rez;
    }

    function getVarDesc($var, $for_save = false) {
        if (!isset($this->vars_description[$var])) {
            return '';
        }
        $data = explode("\n", $this->vars_description[$var]);
        $rez = '';
        foreach ($data as $line) {
            $line = trim($line);
            if ($for_save) {
                $rez .= '# ' . $line . "\n";
            } else {
                $rez .= ' ' . $line;
            }
        }
        return trim($rez);
    }

    function &getnetworkconfigtypes() {
        static $server = array(
            'name' => 'other',
            'ssl' => 'bool'
        );
        return $server;
    }

    function &getserverconfigtypes() {
        static $server = array(
            'host' => 'other',
            'port' => 'other'
        );
        return $server;
    }

    function &getuserconfigtypes() {
        static $server = array(
            'name' => 'other',
            'password' => 'password_sys',
            'admin' => 'bool',
            'bip_use_notice' => 'bool',
            'ssl_check_mode' => array('none' => 'accept anything', 'basic' => 'accept if the certificate is contained in the store'),
            'ssl_check_store' => 'other',
            'ssl_client_certfile' => 'other',
            'default_nick' => 'other',
            'default_user' => 'other',
            'default_realname' => 'other',
            'backlog' => 'bool',
            'backlog_lines' => 'other',
            'backlog_always' => 'bool',
            'backlog_no_timestamp' => 'bool',
            'backlog_reset_on_talk' => 'bool',
            'backlog_msg_only' => 'bool'
        );
        return $server;
    }

    function magic_create_value(&$array, $data) {
        $cdata = array();
        $count = 0;
        foreach ($data as $key => $value) {
            if ($value != '') {
                $cdata[$key] = $value;
                $count++;
            } elseif ($key == 'name' || $key == 'host') {
                $cdata[$key] = $value;
            }
        }
        if ($count > 0) {
            $array[] = $cdata;
            return true;
        }
        return false;
    }

    function &getconnectionconfigtypes() {
        $networks = array();
        $nnames = $this->getNetworksNames();
        foreach ($nnames as $network) {
            $networks[$network] = $network;
        }
        unset($nnames, $network);
        $server = array(
            'name' => 'other',
            'network' => $networks,
            'ssl_check_mode' => array('none' => 'accept anything', 'basic' => 'accept if the certificate is contained in the store'),
            'vhost' => 'other',
            'source_port' => 'other',
            'password' => 'password',
            'away_nick' => 'other',
            'no_client_away_msg' => 'other',
            'follow_nick' => 'bool',
            'ignore_first_nick' => 'bool',
            'nick' => 'other',
            'user' => 'other',
            'realname' => 'other',
        );
        return $server;
    }

    function fill_if_needed_channel(&$array) {
        $this->dnins($array, 'name', '');
        $this->dnins($array, 'key', '');
        $this->dnins($array, 'backlog', true);
    }

    function &getchannelconfigtypes() {
        static $rez = array(
            'name' => 'other',
            'key' => 'other',
            'backlog' => 'bool'
        );
        return $rez;
    }

    function fill_if_needed_connection(&$array) {
        $networks = $this->getNetworksNames();
        $network = current($networks);
        unset($networks);
        $this->dnins($array, 'name', '');
        $this->dnins($array, 'network', $network);
        $this->dnins($array, 'ssl_check_mode', 'none');
        $this->dnins($array, 'vhost', '');
        $this->dnins($array, 'source_port', '');
        $this->dnins($array, 'password', '');
        $this->dnins($array, 'away_nick', '');
        $this->dnins($array, 'no_client_away_msg', '');
        $this->dnins($array, 'follow_nick', false);
        $this->dnins($array, 'ignore_first_nick', false);
        $this->dnins($array, 'nick', '');
        $this->dnins($array, 'user', '');
        $this->dnins($array, 'realname', '');
    }

    private function dnins(&$array, $key, $value) {
        if (!isset($array[$key])) {
            $array[$key] = $value;
        }
    }

    function getConnectionsNames($user_id) {
        $rez = array();
        $user_id = intval($user_id);
        if (!isset($this->vars['user'][$user_id]['connection'])) {
            $this->vars['user'][$user_id]['connection'] = array();
        }
        foreach ($this->vars['user'][$user_id]['connection'] as $connection) {
            $rez[] = $connection['name'];
        }
        return $rez;
    }

    function getChannelsNames($user_id, $connection_id) {
        $rez = array();
        $user_id = intval($user_id);
        if (!isset($this->vars['user'][$user_id]['connection'][$connection_id]['channel'])) {
            $this->vars['user'][$user_id]['connection'][$connection_id]['channel'] = array();
        }
        foreach ($this->vars['user'][$user_id]['connection'][$connection_id]['channel'] as $channel) {
            $rez[] = $channel['name'];
        }
        return $rez;
    }

    function fill_if_needed_user(&$array) {
        $this->dnins($array, 'bip_use_notice', false);
        $this->dnins($array, 'admin', false);
        $this->dnins($array, 'name', '');
        $this->dnins($array, 'password', '');
        $this->dnins($array, 'ssl_check_mode', 'none');
        $this->dnins($array, 'ssl_check_store', './');
        $this->dnins($array, 'ssl_client_certfile', '');
        $this->dnins($array, 'default_nick', '');
        $this->dnins($array, 'default_user', '');
        $this->dnins($array, 'default_realname', '');
        $this->dnins($array, 'backlog', false);
        $this->dnins($array, 'backlog_lines', '10');
        $this->dnins($array, 'backlog_always', true);
        $this->dnins($array, 'backlog_no_timestamp', true);
        $this->dnins($array, 'backlog_reset_on_talk', true);
        $this->dnins($array, 'backlog_msg_only', true);
    }

    function fill_if_needed_server(&$array) {
        $this->dnins($array, 'host', '');
        $this->dnins($array, 'port', 6667);
    }

    function fill_if_needed_network(&$array) {
        $this->dnins($array, 'name', '');
        $this->dnins($array, 'ssl', false);
    }

    function &getServersNames($network_id) {
        $rez = array();
        $network_id = intval($network_id);
        foreach ($this->vars['network'][$network_id]['server'] as $user) {
            $rez[] = $user['host'];
        }
        return $rez;
    }

    function &getsystemconfigtypes() {
        $rez = array();
        foreach ($this->vars as $key => $value) {
            if (is_array($value))
                continue;
            if ($key == 'log_level') {
                $rez[$key] = array('only fatal errors', 'add others errors', 'add warnings', 'add info messages', 'add debug messages');
            } elseif (is_bool($value)) {
                $rez[$key] = 'bool';
            } else {
                $rez[$key] = 'other';
            }
        }
        return $rez;
    }

    private function doPregReplaceForAll($search, $replace, $where) {
        $data = $where;
        while (true) {
            $ndata = preg_replace($search, $replace, $data);
            if ($ndata == $data) {
                unset($ndata);
                break;
            }
            $data = $ndata;
        }
        return $data;
    }

    private function array_move(&$data, $search_key, $new_key) {
        $sl = strlen($search_key);
        $data[$new_key] = array();
        $keys = array();
        foreach ($data as $key => $value) {
            if (substr($key, 0, $sl) == $search_key) {
                $keys[] = $key;
                $data[$new_key][] = $value;
            }
        }
        foreach ($keys as $key) {
            unset($data[$key]);
        }
    }

    function read() {
        $data = file($this->filename);
        $lines = count($data);
        for ($i = 0; $i < $lines; $i++) {
            $data[$i] = trim($data[$i]);
            if ($k = strpos($data[$i], '#') === false)
                continue;
            if (substr($data[$i], 0, 1) == '#') {
                unset($data[$i]);
                continue;
            }
            $m1 = strrpos($data[$i], '"');
            $m2 = strrpos($data[$i], '\'');
            if ($m2 === false && $m1 === false) {
                $data[$i] = substr($data[$i], 0, $k);
                if (trim($data[$i]) == '') {
                    unset($data[$i]);
                }
                continue;
            }
//			if ($m1 < $k || $m2 < $k) continue;
//			$data[$i] = '|' . substr($data[$i], 0, $k) . $data[$i] . $k;
        }
        $data = implode("\n", $data);
        $data = $this->doPregReplaceForAll('/(.*){(.*)};/m', "\\1{\n\\2\n};", $data);
        $data = $this->doPregReplaceForAll('/(.*);(.*);/', "\\1;\n\\2;\n", $data);
        $data = preg_replace('/(.*)=(.*);/e', '"\"".trim("\1")."\" => \2,"', $data);
        $i = 0;
        $data = preg_replace('/(.*){/e', '"\"".trim("\1")."_".($i++)."\" => array("', $data);
        $data = preg_replace('/"#(.*)"(.*)=(.*)/', '', $data);
        $data = preg_replace('/};/', '),', $data);
        $data = 'return array(' . $data . ');';
        $data = str_replace("\'", "'", $data);
        $data = eval($data);
        $this->array_move($data, 'network_', 'network');
        foreach ($data['network'] as $key => $network) {
            $this->array_move($data['network'][$key], 'server_', 'server');
        }
        $this->array_move($data, 'user_', 'user');
        foreach ($data['user'] as $key => $user) {
            $this->array_move($data['user'][$key], 'connection_', 'connection');
            foreach ($data['user'][$key]['connection'] as $ukey => $connection) {
                $this->array_move($data['user'][$key]['connection'][$ukey], 'channel_', 'channel');
            }
        }
        unset($network, $user, $key, $ukey, $connection);

        $this->vars = array_merge($this->vars, $data);
    }

    function save() {
        $fp = fopen($this->filename, 'w');
        foreach ($this->vars as $k => $v) {
            $tw = $this->getVarDesc($k, true);
            if (is_array($v)) {
                $tw .= "\n" . $this->arrayToConfigString($k, $v) . "\n\n";
            } else {
                $tw .= "\n" . $this->outputConfigString($k, $v) . "\n\n";
            }
            fwrite($fp, $tw);
        }
        fclose($fp);
    }

    private function outputConfigString($name, $value) {
        $tw = "$name = ";
        if (is_bool($value)) {
            $tw .= $value ? 'true' : 'false';
        } elseif (intval($value) . '' == $value) {
            $tw .= $value;
        } elseif ("$value" == "") {
            return '';
        } else {
            $value = var_export($value, true);
            if (is_string($value)) {
                $value = str_replace("'", '"', $value);
            }
            $tw .= $value;
        }
        return $tw . ';';
    }

    private function arrayToConfigString($name, &$array) {
        static $tabs = 0;
        $rez = '';
        $ctabs = str_repeat("\t", $tabs);
        foreach ($array as $v9) {
            $rez .= "\n$ctabs$name {\n";
            $tabs++;
            $ctabs2 = str_repeat("\t", $tabs);
            foreach ($v9 as $k => $v) {
                if (is_array($v)) {
                    $rez .= $this->arrayToConfigString($k, $v) . "\n";
                } else {
                    $rez .= $ctabs2 . $this->outputConfigString($k, $v) . "\n";
                }
            }
            $tabs--;
            $rez .= "$ctabs};\n";
        }
        return $rez;
    }

}

//$array = array( 'namas' => 52, 'tb'[] => array('aaa'),  'tb'[] => array('aaa',333));

$objBipConfigManager = new BIPConfig($config['config_file']);
