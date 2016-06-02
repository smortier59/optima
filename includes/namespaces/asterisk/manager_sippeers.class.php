<?php
/**
* @package Asterisk
*/
namespace asterisk;

require_once "manager.class.php";

class manager_sippeers extends manager {
	function __construct() {
		parent::__construct();
		
		// Gestion des namespace
		if (__NAMESPACE__) {
			$this->namespace = __NAMESPACE__;
			$class = explode("\\",__CLASS__);
			$this->db = $class[0];
			$this->table = $class[1];
		} else {
			$this->table = __CLASS__;
		}


		$this->controlled_by = "accueil";
		$this->colonnes['fields_column']  = array('Channeltype'
												  ,'ObjectName'
												  ,'ChanObjectType'
												  ,'IPaddress'
												  ,'IPport'
												  ,'Dynamic'
												  ,'Natsupport'
												  ,'ACL'
												  ,'Status');
		$this->fieldstructure();
	}
	
	public function select_all() {
		$asterisk_command["Action"] = "sippeers";
		//$asterisk_command["Command"] = "help";
		$asterisk_response_event = "Status";

//ATF::$analyzer->flag('connection asterisk');
		$this->connect();
//ATF::$analyzer->flag('login asterisk');
		$this->login();
//ATF::$analyzer->flag('commande asterisk');
		foreach ($asterisk_command as $param => $value) {
			$command[] = $param.": ".$value;
		}
		$command = implode("\r\n",$command)."\r\n\r\n";
//echo "|".$command."|";		
		if ($s = $this->_sendCommand($command)) {
//echo $s;	
//return false;
		//if ($s = $this->connect()->login()->_sendCommand("Action: sippeers\r\n\r\n")) {
			$s = str_replace("\r","",$s);
//ATF::$analyzer->flag('paser de reponse');
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
//ATF::$analyzer->flag('injection base de données');
			if (!\ATF::db($this->db)->fetch_array_once("SHOW TABLE STATUS LIKE '".$this->table."'")) {
				$query .= "CREATE TABLE `".$this->table."` (
				`id_".$this->table."` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,";
				foreach ($infos["PeerEntry"][0] as $k => $i) {
					$fields[] = "`".$k."` VARCHAR(64) NULL";
				}
				$query .= implode(",",$fields);
				$query .= ", PRIMARY KEY  (`id_".$this->table."`)
				) TYPE = MEMORY ;";
				\ATF::db()->query($query);
			} else {
				\ATF::db($this->db)->query("DELETE FROM ".$this->table.";");
			}
			//print_r(ATF::db()->report());
			$this->multi_insert($infos["PeerEntry"]);
			//print_r(ATF::db()->report());
		}

//ATF::$analyzer->mail("ygautheron@absystech.fr");
		return parent::select_all();
	}
}

?>