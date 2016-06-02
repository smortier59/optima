<?
/** Classe de Cryptage
* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
* @package réseaux
*/
class Crypto{
	/*---------Attributs-----------*/
	private $cle;//La clée utilisée
	
	/*---------Constructeurs------------*/
	
	/** Crée un nouvel objet Crypto
	* @param cle string le clée que l'on désire
	*/
	public function __construct($cle){
		$this->cle=$cle;
	}
	
	/*---------Méthodes------------*/
	
	/** Genère une nouvelle clée
	* @param string $message le message à crypter
	* @param string $cle la clée utilisée
	*/
	private function genereCle($message,$cle){
		$cle = md5($cle);
		$Compteur=0;
		$VariableTemp = "";
		for ($Ctr=0;$Ctr<strlen($message);$Ctr++){
			if ($Compteur==strlen($cle)){
				$Compteur=0;
			}
			$VariableTemp.= substr($message,$Ctr,1) ^ substr($cle,$Compteur,1);
			$Compteur++;
		}
		return $VariableTemp; 
	}
	
	function crypte($Texte){
		srand((double)microtime()*1000000);
		$CleDEncryptage = md5(rand(0,32000) );
		$Compteur=0;
		$VariableTemp = "";
		for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++){
			if ($Compteur==strlen($CleDEncryptage))
			$Compteur=0;
			$VariableTemp.= substr($CleDEncryptage,$Compteur,1).(substr($Texte,$Ctr,1) ^ substr($CleDEncryptage,$Compteur,1) );
			$Compteur++;
		}
		return base64_encode($this->genereCle($VariableTemp,$this->cle));
	}

	function decrypte($Texte){
		$Texte = $this->genereCle(base64_decode($Texte),$this->cle);
		$VariableTemp = "";
		for ($Ctr=0;$Ctr<strlen($Texte);$Ctr++){
			$md5 = substr($Texte,$Ctr,1);
			$Ctr++;
			$VariableTemp.= (substr($Texte,$Ctr,1) ^ $md5);
		}
		return $VariableTemp;
	} 
};
?>