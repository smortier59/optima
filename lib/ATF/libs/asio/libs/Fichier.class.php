<?
/** Gestion de fichier - Lecture/Ecriture d'un fichier sur le disque
* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
* @package réseaux
*/
class Fichier{
	/*---------Attributs-----------*/
	private $path;//Le path du fichier
	
	/*---------Constructeurs------------*/
	
	/** Crée un nouvel objet "Network" qui sera soit un client ou un serveur
	* @logPath string l'emplacement du fichier de log
	*/
	public function __construct($path){
		$this->path=$path;
	}
	
	/*---------Méthodes------------*/
	
	/** Lit un fichier et renvoie le résultat sous forme de string
	* @param $path le path du fichier
	* @param $
	* @return string le fichier lu, false sinon
	*/
	function read($utf8_encode=false){
		//ouverture du fichier
		$descripteur=fopen($this->path,'r');
		//Erreur si l'ouverture a échouée
		if(empty($descripteur)){ return false;}
		//Lecture du fichier
		$contenu=fread($descripteur,filesize($this->path));
		//Fermeture du fichier
		fclose($descripteur);
		if($utf8_encode){
			return utf8_encode($contenu);
		}else{
			return $contenu;}
	}
	
	/** Ecrit dans un fichier ce qui est passé en paramètre
	* @param $input string les données à écire
	* @param $append boolean true si on veut juste rajouter à la suite des données au fichier
	* @true si l'écriture a réussi
	*/
	function write($input,$append=true){
		//ouverture du fichier
		$descripteur=fopen($this->path,(($append)?'a':'w'));
		//Erreur si l'ouverture a échouée
		if(empty($descripteur)){return false;}
		//Ecriture des données
		fwrite($descripteur,$input,strlen($input));
		//Fermeture du fichier
		fclose($descripteur);
		return true;
	}
};
?>