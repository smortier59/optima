<?php
/**
* Système d'encryptage symétrique AES
* ATTENTION IL EST NECESSAIRE D'AVOIR LA BIBLIOTHEQUE MCRYPT
* @date 2010-01-12
* @package ATF
* @version 5
* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
*/ 
class aes{
	/**
	* Cipher object
	* @var mixed
	*/
	private $cipher;
	
	/**
	* La clé
	* @var string
	*/ 	
	private $key;
	
	/**
	* Vecteur d'initialisation
	* @var string
	*/  	
	private $IV;
	
	/**
	* Affichage des résultats sur la sortie standard (debug)
	* @var bool
	*/
	//private $output=false;
		
	/** 
	* Création d'une erreur ATF
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param boolean $init Initialise la clé et le vecteur d'initialisation automatiquement
	*/
	public function __construct($init=true){
		if($init){
			$this->initCrypt();
		}
	}	
	
	/** 
	* Génération de la clé de façon aléatoire
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function generateKey($key256=true){
		if(!$this->key){
			if($key256){
				//Clé de 256bits utilisé
				$this->key=util::generateRandWord(32,'0123456789');
			}else{
				//Clé de 128bits
				$this->key=util::generateRandWord(16,'0123456789');
			}
		}
	}
	
	/** 
	* Génération du vecteur d'initialisation (Initialisation vector : IV)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function generateIV(){
		if(!$this->IV){
			$iv_size = mcrypt_enc_get_iv_size($this->cipher);
			$this->IV=util::generateRandWord($iv_size,'0123456789');}
	}
	
	/** 
	* Initialise le moteur de chiffrement s'il ne l'est pas déjà
	* @param boolean $key256 vrai cryptage 256 bits (32 caractères) faux cryptage 128 bits (16 caractères)
	*/
	public function initCrypt($key256=true){
		if(!$this->cipher || !$this->key || !$this->IV){
			//Création du module
			$this->cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			// Création de la clé
			$this->generateKey();
			// Création du vecteur d'initialisation
			$this->generateIV();
		}
	}
	
	/** 
	* Ferme le moteur de chiffrement
	*/
	public function endCrypt(){
		if($this->cipher){
			mcrypt_module_close($this->cipher);
		}	
	}
	
	/** 
	* Crypte
	* @param string $text Le texte à crypter
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @return string le texte crypté en hexadécimal (par défaut il est en binaire (et non en string binaire !))
	*/
	public function crypt($text){
		// Initialisation
		$this->initCrypt();
		
		//test des clées
		if(!$this->cipher) throw new errorATF('cipher_not_initialised');
		if(!$this->key) throw new errorATF('key_not_initialised');
		if(!$this->IV) throw new errorATF('IV_not_initialised');
		
		//Cryptage
		$cryptText="";
		if (mcrypt_generic_init($this->cipher, $this->key, $this->IV) >= 0){
			$cryptText = mcrypt_generic($this->cipher,$text);
			mcrypt_generic_deinit($this->cipher);
			
/*			if($this->output){
				printf("256-bit encrypted result:\n%s\n\n",bin2hex($cryptText));}
*/					
		}
		return bin2hex($cryptText);
	}
	
	/** 
	* Decrypte
	* @param string $cryptText Le texte à décrypter (en hexadécimal)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string le texte crypté
	*/
	public function decrypt($cryptText){
		// Initialisation
		$this->initCrypt();
		
		// Test des clées
		if(!$this->cipher) throw new errorATF('cipher_not_initialised');
		if(!$this->key) throw new errorATF('key_not_initialised');
		if(!$this->IV) throw new errorATF('IV_not_initialised');
		
		// Décryptage
		$text="";
		if (mcrypt_generic_init($this->cipher, $this->key, $this->IV) >= 0){
			// PHP pads with NULL bytes if $cleartext is not a multiple of the block size..
			$text = mdecrypt_generic($this->cipher,pack('H*',$cryptText));
			mcrypt_generic_deinit($this->cipher);
			
/*			if($this->output){
				printf("256-bit decrypted result:\n%s\n\n",$text);
			}
*/					
		}
		return rtrim($text, "\0");
	}
	
	/** 
	* Renvoie la clé utilisée
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function getKey(){
		return $this->key;
	}
	
	/** 
	* Renvoie le vecteur d'initialisation utilisé
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function getIV(){
		return $this->IV;
	}
	
	/** 
	* Initialise la clée
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function setKey($key){
		$this->key=$key;
	}
	
	/** 
	* Initialise le vecteur d'initialisation
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function setIV($IV){
		$this->IV=$IV;
	}

	/** 
	* Initialise le cryptage avec une seed contenant la clée et le vecteur d'initialisation
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function setSeed($seed){
		$this->initCrypt();
		$this->setIV(substr($seed,0,16));
		$this->setKey(substr($seed,16));
		return true;
	}
};
?>