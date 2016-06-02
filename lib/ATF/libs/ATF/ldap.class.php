<?php
/**
* @date 2010-09-03
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class ldap {
	/*
	* @var Ressource de connexion 
	*/
	private $ds;
	
	/*
	* @var Domaine de connexion
	*/
	private $dn;
	
	/*
	* @var Mot de passe
	*/
	private $password;
	
	/*
	* @var Adresse de connexion
	*/
	private $host;
	
	/*
	* @var Codename utilisé pour le cn
	*/
	private $codename;
	
	/**
	* Constructeur
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $host ldaps://ldap.example.com:389/
	* @param string $codename
	* @param string $dn Domaine
	* @param string $password
	*/
	public function __construct($host,$codename,$dn,$password) {
		$this->codename = $codename;
		$this->dn = $dn;
		$this->password = $password;
		$this->connect($host);
	}
	
	/**
	* Connexion
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $host ldaps://ldap.example.com:389/
	*/	
	public function connect($host) {
		$this->host = $host;
		if ($this->ds = ldap_connect($this->host)) {
			ldap_set_option($this->ds,LDAP_OPT_PROTOCOL_VERSION,3);				
			$r = ldap_bind($this->ds, $this->dn, $this->password);

			if (!$r) {
				throw new errorATF("connexion au domaine impossible ".ldap_error($this->ds),2);
			}
			
			// Vérifie toujours si le domaine du codename a bien été créé
			if (!$this->isExistsCodenameCN()) {
				$this->initCodenameCN();
			}
		} else {
			throw new errorATF("connexion impossible".ldap_error($this->ds),1);
		}
	}
	
	/**
	* Fermeture de la connexion
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function close() {
		ldap_close($this->ds);
	}
	
	/**
	* Retourne la dernière erreur Ldap
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function error() {
		return ldap_error($this->ds);
	}
	
	/**
	* Création du domaine sur ldap pour ce codename
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function initCodenameCN() {
		$info["objectClass"][] = "top";
		$info["objectClass"][] = "organizationalUnit";
		$info["ou"] = $this->codename;
		$r = ldap_add($this->ds, "ou=".$this->codename.",dc=absystech,dc=optima", $info);

		if (!$r) {
			throw new errorATF("creation du domaine ".$this->codename." impossible ".ldap_error($this->ds),3);
		}
	}
	
	/**
	* Vérifie si le domaine sur ldap pour ce codename existe
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
	*/
	public function isExistsCodenameCN() {
		$sr = ldap_search($this->ds, "dc=absystech,dc=optima", "ou=".$this->codename, array("ou"));
		$res = ldap_get_entries($this->ds, $sr);
		return is_numeric($res["count"]) && $res["count"]>0;
	}
	
	/**
	* Insertion
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*/	
	public function insert($infos) {
//log::logger($infos,ygautheron);		
		$query = "cn=".str_replace(",", "\,", $infos["cn"]).",ou=".$this->codename.",dc=absystech,dc=optima";
//log::logger($query,ygautheron);		
//log::logger($infos,ygautheron);		
		if ($r = ldap_add($this->ds, $query, $infos)) {
			return true;
		} else {
			ATF::$msg->addWarning(ATF::$usr->trans("ldap_fail_insert") . (ATF::getDebug() ?" : ". ldap_error($this->ds) : NULL));
			return false;
		}
	}
	
	/**
	* Suppression
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*/	
	public function delete($infos,$useCN=true) {
//log::logger("DELETE cn=".str_replace(",", "\\,", $infos["cn"]).",ou=".$this->codename.",dc=absystech,dc=optima",ygautheron);		
//log::logger($infos,ygautheron);
		if ($useCN) {
			$cn = "cn=".str_replace(",", "\,", $infos["cn"]).",";
		}
		if ($r = ldap_delete($this->ds,$cn."ou=".$this->codename.",dc=absystech,dc=optima")) {
			return true;
		} else {
			ATF::$msg->addWarning(ATF::$usr->trans("ldap_fail_delete") . (ATF::getDebug() ? " : ".ldap_error($this->ds) : NULL));
			return false;
		}
	}
};
?>