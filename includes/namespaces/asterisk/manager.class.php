<? 
/**
* Classe mère pour les modules asterisk utilisant la connection via le manager
* @package Asterisk
*/
namespace asterisk;

class manager extends \classes_optima {	
	function __construct($table=NULL) {
		parent::__construct($table);
	}
	
    private $login = "admin";
    private $pass = "huhu";
	
    /**
     * The Asterisk server which will recieve the manager commands 
     * @access public
     * @var string
     */
    public $server = "roubaix.absystech.net";

    /**
     * The port to use when connecting to the Asterisk server
     * @access public
     * @var integer
     */
    public $port = 5038;

    /**
     * The opened socket to the Asterisk server
     * @access private 
     * @var object
     */
    protected $_socket;

    /**
     * Private method for checking there is a socket open to the Asterisk
     * server.
     * 
     * @return null
     */
    private function _checkSocket()
    {
        if (!$this->_socket) {
            throw new \error("no socket");
        }
    }

    /**
     * Consolidated method for sending the given command to the server and returning
     * its reponse. Any failure in writing or reading will raise an exception.
     * 
     * @param string $command The command to send
     *
     * @return string
     */
    protected function _sendCommand($command)
    {
		
        if (!fwrite($this->_socket, $command)) {
            throw new \error("CMDSENDERR");
        }

        $response = stream_get_contents($this->_socket);

        if ($response === false) {
            throw new \error("RESPERR");
        }

        return $response;
    }

    /**
     * If not already connected then connect to the Asterisk server
     * otherwise close active connection and reconnect
     * 
     * @param array $params Array of the parameters used to connect to the server
     * <code>
     * array(
     *       'server' => '127.0.0.1'    // The server to connect to
     *       'port' => '5038',          // Port of manager API
     *       'auto_connect' => true     // Autoconnect on construction?
     *      );
     * </code>
     * 
     * @uses AsteriskManager::$server
     * @uses AsteriskManager::$port
     * @uses AsteriskManager::$_socket
     * @return bool
     */
    public function connect()
    {
        if ($this->_socket) {
            $this->close();
        }
        
        if ($this->_socket = fsockopen($this->server, $this->port)) {
            stream_set_timeout($this->_socket, 0, 100000);
            return $this;
        }
        
        throw new \error ("connect failed");
    }


    /**
     * Login into Asterisk Manager interface given the user credentials
     *
     * @param string $username The username to access the interface
     * @param string $password The password defined in manager interface of server
     * @param string $authtype Enabling the ability to handle encrypted connections
     * 
     * @return bool
     */
    public function login($username, $password, $authtype = null)
    {
        $this->_checkSocket();
        if (!$username) $username = $this->login;
        if (!$password) $password = $this->pass;
        if (strtolower($authtype) == 'md5') {
            $response = $this->_sendCommand("Action: Challenge\r\n"
                ."AuthType: MD5\r\n\r\n");
            if (strpos($response, "Response: Success") !== false) {    
                $challenge = trim(substr($response, 
                    strpos($response, "Challenge: ")));

                $md5_key  = md5($challenge . $password);
                $response = $this->_sendCommand("Action: Login\r\nAuthType: MD5\r\n"
                    ."Username: {$username}\r\n"
                    ."Key: {$md5_key}\r\n\r\n");
            } else {
                throw new \error(
                    error::AUTHFAIL
                );
            }
        } else {
            $response = $this->_sendCommand("Action: login\r\n"
                ."Username: {$username}\r\n"
                ."Secret: {$password}\r\n\r\n");
        }


        if (strpos($response, "Message: Authentication accepted") != false) {
            return $this;
        }
        throw new \error("AUTHFAIL : ".$response);
    }

    /**
     * Logout of the current manager session attached to $this::socket
     * 
     * @return bool
     */
    public function logout()
    {
        $this->_checkSocket();
        
        $this->_sendCommand("Action: Logoff\r\n\r\n");

        return true;
    }

    /**
     * Close the connection
     *
     * @return bool
     */
    public function close()
    {
        $this->_checkSocket();

        return fclose($this->_socket);
    }

    /**
     * Send a command to the Asterisk CLI interface. Acceptable commands 
     * are dependent on the Asterisk installation.
     *
     * @param string $command Command to execute on server
     *
     * @return string|bool
     */
    public function command($command)
    {
        $this->_checkSocket();
    
        $response = $this->_sendCommand("Action: Command\r\n"
            ."Command: $command\r\n\r\n");

        if (strpos($response, 'No such command') !== false) {
            throw new \error(
                error::NOCOMMAND
            );
        }
        return $response;
    }

    /**
     * A simple 'ping' command which the server responds with 'pong'
     *
     * @return bool
     */
    public function ping()
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: Ping\r\n\r\n");
        if (strpos($response, "Pong") === false) {
            return false;
        }
        return true;
    }

    /**
     * Make a call to an extension with a given channel acting as the originator
     *
     * @param string  $extension The number to dial
     * @param string  $channel   The channel where you wish to originate the call
     * @param string  $context   The context that the call will be dropped into 
     * @param string  $cid       The caller ID to use
     * @param integer $priority  The priority of this command
     * @param integer $timeout   Timeout in milliseconds before attempt dropped
     * @param array   $variables An array of variables to pass to Asterisk
     * @param string  $action_id A unique identifier for this command
     *
     * @return bool
     */
    public function originateCall($extension, 
                           $channel, 
                           $context, 
                           $cid, 
                           $priority = 1, 
                           $timeout = 30000, 
                           $variables = null, 
                           $action_id = null)
    {
        $this->_checkSocket();
        
        $command = "Action: Originate\r\nChannel: $channel\r\n"
            ."Context: $context\r\nExten: $extension\r\nPriority: $priority\r\n"
            ."Callerid: $cid\r\nTimeout: $timeout\r\n";

        if (count($variables) > 0) {
            $chunked_vars = array();
            foreach ($variables as $key => $val) {
                $chunked_vars[] = "$key=$val";
            }
            $chunked_vars = implode('|', $chunked_vars);
            $command     .= "Variable: $chunked_vars\r\n";
        }

        if ($action_id) {
            $command .= "ActionID: $action_id\r\n";
        }
        $this->_sendCommand($command."\r\n");
        return true;
    }

    /**
     * Returns a list of queues and their status
     *
     * @return string|bool
     */
    public function getQueues()
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: Queues\r\n\r\n");
        return $response;
    }

    /**
     * Add a handset to a queue on the server
     * 
     * @param string  $queue   The name of the queue you wish to add the handset too
     * @param string  $handset The handset to add, e.g. SIP/234
     * @param integer $penalty Penalty
     * 
     * @return bool
     */
    public function queueAdd($queue, $handset, $penalty = null)
    {
        $this->_checkSocket();
        
        $command = "Action: QueueAdd\r\nQueue: $queue\r\n"
                    ."Interface: $handset\r\n";

        if ($penalty) {
            $this->_sendCommand($command."Penalty: $penalty\r\n\r\n");
            return true;
        }

        $this->_sendCommand($command."\r\n");
        return true;
    }

    /**
     * Remove a handset from the given queue
     * 
     * @param string $queue   The queue you wish to perform this action on
     * @param string $handset The handset you wish to remove (e.g. SIP/200)
     *
     * @return bool
     */
    public function queueRemove($queue, $handset) 
    {
        $this->_checkSocket();
        
        $this->_sendCommand("Action: QueueRemove\r\nQueue: $queue\r\n"
            ."Interface: $handset\r\n\r\n");

        return true;
    }

    /**
     * Monitor(record) a channel to given file in given format
     *
     * @param string  $channel  Channel to monitor (e.g. SIP/234, ZAP/1)
     * @param string  $filename The filename to save to
     * @param string  $format   The format of the file (e.g. gsm, wav)
     * @param integer $mix      Boolean 1 or 0 on whether to mix
     *
     * @return bool
     */
    public function startMonitor($channel, $filename, $format, $mix = null)
    {
        
        $this->_checkSocket();
        
        $response = $this->_sendCommand("Action: Monitor\r\nChannel: $channel\r\n"
                               ."File: $filename\r\nFormat: $format\r\n"
                               ."Mix: $mix\r\n\r\n");
        
        if (strpos($response, "Success") === false) {
            throw new \error(
                error::MONITORFAIL
            );
        } else {
            return true;
        }
    }

    /**
     * Stop monitoring a channel
     * 
     * @param string $channel The channel you wish to stop monitoring
     *
     * @return bool
     */
    public function stopMonitor($channel)
    {
        $this->_checkSocket();
        
        $this->_sendCommand("Action: StopMonitor\r\n"
                            ."Channel: $channel\r\n\r\n");
        return true;
    }

    /**
     * Get the status information for a channel
     *
     * @param string $channel The channel to query
     * 
     * @return string|string
     */
    public function getChannelStatus($channel = null)
    {
        $this->_checkSocket();
        
        $response = $this->_sendCommand("Action: Status\r\nChannel: "
            ."$channel\r\n\r\n");
        
        return $response;
    }

    /**
     * Get a list of SIP peers and their status
     *
     * @return string|bool
     */
    public function getSipPeers()
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: Sippeers\r\n\r\n");
        return $response;
    }

    /**
     * Returns a list of all parked calls on the server.
     *
     * @return string
     */
    public function parkedCalls()
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: ParkedCalls\r\n"
            ."Parameters: ActionID\r\n\r\n");
        return $response;
    }

    /**
     * Return a list of IAX peers and their status
     *
     * @return string|bool
     */
    public function getIaxPeers() 
    {
        $this->_checkSocket();

        $response = $this->_sendCommand("Action: IAXPeers\r\n\r\n");
        return $response;
    }
	
	public function test() {
		$this->connect()->login("queuemanager","secret420");
		//$response = $this->getIaxPeers();
        $s = $this->SIPPeers();
		//return htmlentities($s);
//		echo "essai 46";
//		echo $this->mettre_en_relation("46", "Local/421");
//		echo "essai 41";
//		echo $this->mettre_en_relation("41", "Local/421");
	}
	
	public function mettre_en_relation($sip, $target_channel) {
		return $this->originateCall($sip,$target_channel,"internal","421");
	}
	
	public function SIPPeers() {
		if ($s = $this->_sendCommand("Action: sippeers\r\n\r\n")) {
			$s = str_replace("\r","",$s);
//			$infos = array();
//			if (preg_match_all("/(.[^\:]*)\: (.[^\n]*)/",$s,$infos)>0) {
//				foreach ($infos[1] as $k => $i) {
//					switch ($i) {
//						case "Event":
//						case "Channeltype":
//						case "ObjectName":
//						case "ChanObjectType":
//						case "IPaddress":
//						case "IPaddress":
//						case "IPport":
//						case "Dynamic":
//						case "Natsupport":
//						case "ACL":
//						case "Status":
//							$array[$i][] = $infos[2][$k];
//							break;
//						
//						default:
//							// Ignoring
//					}					
//				}
//				foreach ($array as $key => $values) {
//					foreach ($values as $k => $value) {
//						$return[$k][$key]=$value;
//					}
//				}
//				print_r($return);
//			}		
			if ($r = explode("\n",$s)) {
				$infos = array();
				foreach ($r as $row) {
					$info = explode(": ",$row);
					if ($info[0]) {
						if ($info[0]=="Event") {
							// Début d'un event
							$last_event = $info[1];
							$infos[$last_event][] = array();
						} elseif ($last_event) {
							$infos[$last_event][count($infos[$last_event])-1][$info[0]]=$info[1];
						}
					}
				}
				//print_r($infos);
			}
			
			// On va mettre ces infos dans une table mémoire, pour faciliter la manipulation en listing
			if (!ATF::db()->fetch_array_once("SHOW TABLE STATUS LIKE '___testor'")) {
				$query .= "CREATE TABLE `___testor` (
				`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,";
				foreach ($infos["PeerEntry"][0] as $k => $i) {
					$fields[] = "`".$k."` VARCHAR(64) NULL";
				}
				$query .= implode(",",$fields);
				$query .= ", PRIMARY KEY  (`id`)
				) TYPE = MEMORY ;";
				ATF::db()->query($query);
			} else {
				ATF::db()->query("DELETE FROM ___testor;");				
			}
			//print_r(ATF::db()->report());
			ATF::___testor()->multi_insert($infos["PeerEntry"]);
			//print_r(ATF::db()->report());
		}
		
	}
}
?>