<?php
/**
* La classe de journalisation est utile pour debugger les programmes.
*
* @date 2009-04-16
* @package ATF
* @version 6
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class log {
	/**
	* Bloque la génération des log
	* @var bool
	*/
	private static $block = false;
	
	/**
	* Niveau de debug (0=rien, 1=date, 2=+IP+template+user, 3=+backtrace réduit, 4=+backtrace complet)
	* @var int
	*/
	private static $debugLevel = 1;
	
	/** 
	* Génère un fichier de journalisation
	* @param string $log le message à insérer
	* @param string $filename le nom du fichier, avec chemin relatif ou absolu
	*			NULL prendra le nom du login automatiquement
	* @param boolean $filtre Si FALSE on log tous les utilisateurs loggués, sinon uniquement celui du nom du filename
	* @param boolean $relative FALSE signifie un $filename en chemin absolu, sinon relatif au projet
	* @param int $level Niveau de debug
	* @param boolean $withTime Préfixe par le timestamp humain
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return boolean FALSE si le fichier de log n'a pu être écrit, rien sinon
	*/
	public static function logger($log,$filename="logger",$filtre=false,$relative=true,$level=NULL,$withTime=true) {
		if (self::$block) return false;
		
		if (is_a(ATF::$usr,"usr"))
			$login = ATF::$usr->getLogin(true);

		if ($filtre && $login && $login!=$filename) { // Ne pas loguer si utilisateur loggué non admis
			return false;
		}
		
		// Level défini
		if ($level===NULL) {
			$level = self::$debugLevel;
		}
		
		// Si level < 1, on abandonne
		if ($level<1) {
			return false;
		}
		
		// Chemin relatif
		if ($relative) { 
			$path = __ABSOLUTE_PATH__.'log/';
		}
		$path .= $filename;
		//die($path);
		
		// Ouverture du fichier
		if ($handle = fopen($path, 'a')) {
			$log = self::arrayToString($log);
		} else {
			// L'ouverture a échouée
			return false;
		}
		
		// Date
		if ($withTime) {
			$s = date("Y-m-d H:i:s",time());
		}
		
		if ($level>1) {
			// IP
			$s .= " [".$_SERVER['REMOTE_ADDR']."]";
			
			// Backtrace : Quelle fonction a appelé le log ?
			if ($level>2) {
				$bt = debug_backtrace();
				if ($level>3) {
					// Ajout en fin de chaîne
				} else {
					$s .= " ".$bt[0]["file"].":".$bt[0]["line"].":".$bt[1]["function"]."()";
				}
			}
			
			// Template et User
			$s .= " ".__TEMPLATE__;
			if ($_GET["method"]) {
				$s .= "/".$_GET["method"];
			}
			if ($_GET["event"]) {
				$s .= "/".$_GET["event"];
			}
		}
		
		if (ATF::$usr && $login && $withTime) {
			$s .= " ".$login;
		}
		
		// Log
		$s .= " ".$log;
		
		// Backtrace complet
		if ($level>3) {
			$s .= "\n".self::array2string($bt[0]);
			//$s .= "\n".self::array2string($bt[1]);
		}

		// Ecriture
		fwrite($handle,$s."\n");

		// Fermeture du fichier
		fclose($handle);
	}
	
	/** 
	* Applique un nouveau niveau de debug
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $l
	*/
	public static function setLevel($l) {
		self::$debugLevel = $l;
	}
	
	/**
	* Donne le niveau de debug actuel
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return int
	*/
	public static function getLevel(){
		return self::$debugLevel;
	}
	
	/**  
	* Retourne un tableau sous la forme d'une string
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array|string $s Soit un array, soit une string
	* @param string $method print_r|var_dump
	* @return string
	*/
	public static function arrayToString($s,$method="print_r") {
		if (is_array($s) || is_object($s)) {
			ob_start();
			$method($s);
			$s = ob_get_contents();
			ob_end_clean();
		}
		return $s;
	}
	
	/**
	* Retourne un tableau sous la forme d'une string (Alias de arrayToString)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array|string $s Soit un array, soit une string
	* @param string $method print_r|var_dump
	* @return string
	*/
	public static function array2string(&$s,$method="print_r") {
		return self::arrayToString($s,$method);
	}
		
	/**
	* Active le Log. Par défaut il est activé, il est donc nécessaire d'utiliser cette méthode après avoir fait un disableLog()
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public static function enableLog(){
		self::$block = false;
	}
	
	/**
	* Désactive le log
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public static function disableLog(){
		self::$block = true;
	}
	
	/**
	* Indique si le log est désactivé
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return bool True si le log est désactivé
	*/	
	public static function isDisabled(){
		return self::$block;
	}
	
	/**
	* Insère les logs d'erreurs texte en base
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $path le chemin du fichier à analyser
	* @param string $table le nom de la table à mettre à jour
	*/
//	public function insert_log_error_type($path,$table){
//		//Ouverture du fichier
//		$ressource=fopen($path,'r');
//		if($ressource===false) throw new errorATF('Le fichier de log ne peut pas être lu, vérifier le path (path='.$path.')');
//		
//		//Lock du fichier de log
//		if(!flock($ressource,LOCK_SH)) throw new errorATF('Impossible de verouiller le fichier (path='.$path.')');
//		
//		$logs=array();
//		//Lecture du fichier
//		while(!feof($ressource)){
//			//Lecture d'un enrgistrement
//			$buffer=stream_get_line($ressource,4096,"\n");
//			//echo "[Lecture]".$buffer."<br />";
//			//Découpage de la ligne
//			$tmp=explode(chr(124),$buffer);
//			array_push($logs,array(
//				$table=>substr($tmp[0],20),
//				'date'=>substr($tmp[0],0,20),
//				'code'=>$tmp[1],
//				'file'=>$tmp[2],
//				'line'=>$tmp[3],
//				'trace'=>$tmp[4],
//				'application'=>$tmp[5],
//				'user'=>$tmp[6],
//			));
//		}
//		//Deblocage du fichier
//		flock($ressource,LOCK_SH);
//		
//		//Fermeture du fichier
//		fclose($ressource,LOCK_UN);
//		
//		//copie du fichier de log
//		rename($path,__ABSOLUTE_PATH__.'log/old_log/'.$table.'-'.date('d-m-Y'));
//		
//		//Recréation du fichier avec les bons droits
//		touch($path);
//		chown($path,'apache');
//		chgrp($path,'apache');
//		chmod($path,0775);
//		
//		//Insertion en base grâce au super multi_insert
//		//Bidouillage de %¨P% !
//		$tmp=NULL;
//		return ATF::$table('classes','main')->multi_insert($logs,$tmp,NULL,$tmp,true);
//	}
		
	/**
	* Insère des logs de type SQL texte en base
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $path le chemin du fichier à analyser
	* @param string $table le nom de la table à mettre à jour
	*/
//	public function insert_log_sql_type($path,$table){
//		//Ouverture du fichier
//		$ressource=fopen($path,'r');
//		if($ressource===false) throw new errorATF('Le fichier de log ne peut pas être lu, vérifier le path (path='.$path.')');
//		
//		//Lock du fichier de log
//		if(!flock($ressource,LOCK_SH)) throw new errorATF('Impossible de verouiller le fichier (path='.$path.')');
//		
//		$logs=array();
//		//Lecture du fichier
//		while(!feof($ressource)){
//			//Lecture d'un enrgistrement
//			$buffer=stream_get_line($ressource,4096,"\n");
//			//echo "[Lecture]".$buffer."<br />";
//			//Découpage de la ligne
//			$tmp=explode(chr(124),$buffer);
//			array_push($logs,array(
//				$table=>$tmp[6],
//				'date'=>$tmp[0],
//				'code'=>__SQL_ERROR__,
//				'module'=>$tmp[4],
//				'application'=>$tmp[2],
//				'user'=>$tmp[1],
//			));
//		}
//
//		//Deblocage du fichier
//		flock($ressource,LOCK_SH);
//		
//		//Fermeture du fichier
//		fclose($ressource,LOCK_UN);
//		
//		//copie du fichier de log
//		rename($path,__ABSOLUTE_PATH__.'log/old_log/'.$table.'-'.date('d-m-Y'));
//		
//		//Recréation du fichier avec les bons droits
//		touch($path);
//		chown($path,'apache');
//		chgrp($path,'apache');
//		chmod($path,0775);
//		
//		//Insertion en base grâce au super multi_insert
//		//Bidouillage de %¨P% !
//		$tmp=NULL;
//		return ATF::$table('classes','main')->multi_insert($logs,$tmp,NULL,$tmp,true);
//	}
	
	/**
	* Ajout des journaux d'erreurs en Base
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
//	public function insert_error_log(){
//		$this->insert_log_error_type(__ABSOLUTE_PATH__.'log/error.log','error_log');
//	}
	
	/**
	* Ajout des journaux d'erreurs en Base
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
//	public function insert_insert_log(){
//		$this->insert_log_sql_type(__ABSOLUTE_PATH__.'log/insert.log','insert_log');
//	}

	/**
	* Ajout des journaux d'erreurs en Base
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
//	public function insert_update_log(){
//		$this->insert_log_sql_type(__ABSOLUTE_PATH__.'log/update.log','update_log');
//	}

	/**
	* Ajout des journaux d'erreurs en Base
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
//	public function insert_delete_log(){
//		$this->insert_log_sql_type(__ABSOLUTE_PATH__.'log/delete.log','delete_log');
//	}

};
?>