<?php
/**
* Objet queue qui permet de placer des actions dans une queue (ex génération fichier, mail...) qui ne doivent se réaliser qu'à la fin d'une transaction sur la méthode generate
* @date 2010-04-28
* @package ATF
* @version 5
* @author  Mathieu Tribouillard <mtribouillard@absystech.fr>
*/ 
namespace ATF;
class queue{
	/**
	* Ordres dans la queue
	* @var array	
	*/
	private $orders; 
	
	/** 
	* Retourne la liste des ordres
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	public function getOrders(){
		return $this->orders;
	}
		
	/** 
	* Ajoute un déplacement de fichier à la queue
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $from Chemin de provenance (dans /temp/)
	* @param string $to Chemin de destination (dans /data/)
	*/
	public function moveFile($from,$to){
		$this->orders["moveFile"][$from]=$to;	
	}
	
	/** 
	* Ajoute une seppression de fichier à la queue
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id : id de l'élément (référence du fichier à delete)
	* @param string $table Table courante
	* @param string $key Seul et unique fichier à supprimer
	*/
	public function deleteFile($id,$table,$key=NULL){
		$a = array("id"=>$id,"table"=>$table);
		if ($key) {
			$a["field"]=$key;
		}
		$this->orders["deleteFile"][]=$a;	
	}
	
	/** 
	* Envoi de mail
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $email adresse destinataire
	* @param string $objet objet du mail
	*/
	public function sendEmail($objet,$email){
		$this->orders["sendEmail"][]=array("objet"=>$objet,"email"=>$email);	
	}
	
	/** 
	* Insertion dans le ldap
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $order Ordres
	* @param array $infos Informations structurés pour ldap
	*/
	public function ldap($order,$infos){
		$this->orders["ldap"][$order][]=$infos;	
	}
	
	/** 
	* Permet de générer l'ensemble des actions lockée
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	*/
	public function generate(){
		$result = true; // Toutes les actions se sont-elles correctement terminées ?
		
		$result = $this->generateLdap() 
				& $this->generateMoveFile()
				& $this->generateDeleteFile()
				& $this->generateSendEmail();
		
		//result renvoie un integer de merde... Comprends pas le php moi...
		if($result){
			return true;
		}else{
			return false;
		}
	}

	/** 
	* Permet de créer des actions de suppressions (ex fichier) sur un rollback
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function deGenerate(){
		$result = true; // Toutes les actions se sont-elles correctement terminées ?
		return $this->deGenerateMoveFile();
	}

	/** 
	* Permet de supprimer les fichiers du temp sur un rollback
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @return boolean True si tout se passe bien !
	*/
	public function deGenerateMoveFile(){
		$result=true;
		foreach($this->orders["moveFile"] as $from => $to){
			if (unlink($from)) {
				//si tout se passe bien on dépile l'élement du tableau
				$this->depilerElement("moveFile",$from);
			}
		}
		return $result;
	}

	/** 
	* Permet de générer l'ensemble des actions MoveFiles
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return boolean True si tout se passe bien !
	*/
	public function generateMoveFile(){
		$result=true;
		foreach($this->orders["moveFile"] as $from => $to){
			if(!file_exists(dirname($to)) && !\util::mkdir(dirname($to))){
				throw new \error("Le dossier ".dirname($to)." ne s'est pas créé !",401);
			}
			
			// Suppression des miniatures générées
			$cmd = "rm -f ".$to.".*-*";
			$erreur = `$cmd 2>&1`;
			

//				if(!file_exists($from)){
//					throw new errorATF("from_file_not_exists");
//				}
			if (rename($from,$to)) {
				//si tout se passe bien on dépile l'élement du tableau
				$this->depilerElement("moveFile",$from);
			}else{	
				\ATF::$msg->addWarning(\ATF::$usr->trans("queue_move_file_error"));
				$result=false;
			}
//			if(!file_exists($to)){
//				throw new errorATF("to_file_not_exists");
//			}
		}
		return $result;
	}
	
	/** 
	* Permet de générer l'ensemble des actions deleteFile
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return boolean True si tout se passe bien !
	* @todo Faire un système de corbeille pour l'unlink
	*/
	public function generateDeleteFile(){
		$result=true;
		$nb_delete_file=0;
		foreach($this->orders["deleteFile"] as $cle=>$elements){
			//if(!\ATF::getClass($elements['table'])->delete_files($elements['id'])){
			//nouvelle gestion de fichier avec corbeille
			if(\ATF::tracabilite()->filesToAttach(array("id_".$elements['table']=>$elements['id']),$elements['table'],true,$elements['field'])){	
				//si tout se passe bien on dépile l'élement du tableau
				$this->depilerElement("deleteFile",$cle);
				$nb_delete_file++;
				if($name) $name.=" - ";
				$name= $name.\ATF::$usr->trans($elements['field'])." (".\ATF::$usr->trans($elements['table']).")";
			}else{
				\ATF::$msg->addWarning(\ATF::$usr->trans("queue_delete_file_error"));
				$result=false;
			}
		}
		if($nb_delete_file>0){
			\ATF::$msg->addNotice(\loc::mt(\ATF::$usr->trans("fichiers_supprimes"),array("nb"=>$nb_delete_file,"name"=>$name)));
		}

		return $result;
	}
	
	/** 
	* Permet de générer l'ensemble des actions sendEmail
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return boolean True si tout se passe bien !
	*/
	public function generateSendEmail($orders=false){
		$result=true;
		foreach($this->orders["sendEmail"] as $cle=>$email){
			if (!is_a($email["objet"],"mail") || !$email["objet"]->send($email["email"],false,true)){
				\ATF::$msg->addWarning(\ATF::$usr->trans("queue_sendmail_error"));
				$result=false;
			}else{
				//si tout se passe bien on dépile l'élement du tableau
				$this->depilerElement("sendEmail",$cle);
			}
		}
		return $result;
	}
	
	/** 
	* Permet de générer l'ensemble des actions ldap
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return boolean
	*/
	public function generateLdap(){
		if(!$this->orders["ldap"]) return true;
		if(\ATF::ldapDefined()) {
			foreach ($this->orders["ldap"] as $action => $actions) {
				switch ($action) {
					case "insert":
					case "delete":
						foreach ($actions as $infos) {
							\ATF::ldap($action,$infos);
						}
				}
			}
			return true;
		}
		return false;
	}
	
	/** Permet d'enlever un élément de la queue
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param $type : sendEmail/deleteFile/moveFile
	* @param $element : dimension a unseter
	*/
	public function depilerElement($type,$element){
		if(isset($this->orders[$type][$element])){
			unset($this->orders[$type][$element]);	
		}
	}
	
};
?>