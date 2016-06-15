<?php

/** 
* Classe scanner
* @package Optima
*/

class scanner extends classes_optima {
	/**
	* Constructeur
	*/
	function __construct($table_or_id=NULL) {
		parent::__construct();
		$this->table="scanner";		
		
		$this->colonnes['primary'] = array(	
			"date"
			,"nbpages"
			,"provenance"
			,'scanner'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>100)		
		);
						
		$this->fieldstructure();
		
		$this->files["scanner"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"no_generate"=>true);
		
		$this->noTruncateSA = true;
		$this->no_insert = $this->no_update = true;		
		$this->selectAllExtjs=true;
		$this->addPrivilege("transfert");
		$this->addPrivilege("getNoTransfered");
		$this->addPrivilege("transfertTo");
		
	}	
	
	/**
	 * Permet de checker la boite mail zimbra, recuperer les mails et recuperer les fichiers scanné
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * 
	*/
	public function checkMailBox($mail, $host, $port, $password, $class="scanner"){		
		ATF::imap()->init($host, $port, $mail, $password);		
		if (ATF::imap()->error) {			
			throw new errorATF(ATF::imap()->error);
		}
		$mails = ATF::imap()->imap_fetch_overview('1:*');
						
		if (is_array($mails)){				
			foreach ($mails as $val) {

				$piece_jointe = ATF::imap()->get_attachments($val->uid);
				
				if(!empty($piece_jointe)){
					$pdf = ATF::imap()->get_attachments($val->uid, $piece_jointe[0]['filename']);
					if($piece_jointe[0]['filename'] && strstr($piece_jointe[0]['filename'], ".pdf")){
						preg_match_all("#/Count ([0-9]*)#" , $pdf, $page);
										
						//Insere dans BDD
						$from = $val->from;
						if($class == "scanner"){
							$id = ATF::getClass($class)->insert(array("nbpages" => count($page[0]), "provenance"=> $from));
							$this->store(ATF::_s(), $id, "scanner", $pdf);	
						}else{
							$id = ATF::getClass($class)->i(array("date" => date("Y-m-d")));
							ATF::getClass($class)->store(ATF::_s(), $id, "fichier_joint", $pdf);
						}
					}
				}	
				ATF::imap()->imap_delete($val->uid);		
			}
		}
		ATF::imap()->imap_expunge();
		return true;
	}	
	
	/**
	 * Permet de transferer le pdf vers le bon module et champs
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function transfert($infos){
		$infos["id_scanner"] = $this->decryptId($infos["id_scanner"]);
		$data = explode(".", $infos["comboDisplay"]);
					
		ATF::getClass($data[0])->q->reset()->addField($data[0].".id_".$data[0], "id")
										   ->where($data[0].".ref", $infos["reference"]);
		$classe = ATF::getClass($data[0])->select_row();		
		
		if($classe){			
			$this->deplace($infos["id_scanner"] , $data[0], $data[1], $classe["id"]);
						
			$this->update(array("id_scanner" => $infos["id_scanner"] , "transfert" => $infos["transfert"]." (ref : ".$infos["reference"].")"));
		}else{
			$module = explode("-", $infos["transfert"]);
			throw new errorATF("Il n'y a pas de ".$module[0]." ayant la référence ".$infos["reference"]);
		}		
	}	
	
	/**
	 * Permet de transferer un pdf de scanner depuis un module
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function transfertTo($infos){
		ATF::getClass($infos["module"])->getRefForScanner($infos["id_to"]);
					
		if($infos["id_scanner"]){
			$id_to = ATF::getClass($infos["module"])->decryptId($infos["id_to"]);			
			$this->deplace($infos["id_scanner"] , $infos["module"], $infos["champs"], $id_to);
			$this->update(array("id_scanner" => $infos["id_scanner"] , "transfert" => ATF::getClass($infos["module"])->getRefForScanner($infos["id_to"], $infos["champs"] )));
		}else{
			throw new errorATF("Il faut séléctionner un fichier du scanner !");
		}
	}
	
	/**
	 * Permet de déplace un pdf de scanner vers le bon module et champs
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function deplace($id_scanner , $module, $champs, $id_to){
		//Recuperer le document		
		$source = $this->filepath($id_scanner, "scanner");	
		$target = str_replace("scanner/".$id_scanner.".scanner" , $module."/".$id_to.".".$champs, $source);
		
		//Deplacer le document
		util::copy($source , $target);
	}
	
	/**
	 * Recupere les fichiers de scanner qui n'ont pas encore étaient transfert
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function getNoTransfered(){
		$this->q->reset()->whereIsNull("transfert");
		return $this->select_all();
	}	
}