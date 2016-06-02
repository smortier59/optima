<?
/** 
* Classe Commande fournisseur (bon de commmande)
* @package Optima
*/
class bon_de_pret extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'bon_de_pret.ref'
			,'bon_de_pret.bon_de_pret'
			,'bon_de_pret.id_affaire'
			,'bon_de_pret.etat'
			,'bon_de_pret.date_debut'
			,'bon_de_pret.date_fin'
		);
		$this->colonnes['primary'] = array(
			'id_societe'
			,'id_contact'
			,'id_affaire'
			,'bon_de_pret'
			,'date_debut'
			,'date_fin'
		);
	    $this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1 ,"collapsible"=>false);
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>3 ,"collapsible"=>false);
		
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('date','ref','etat');
		//$this->colonnes['bloquees']['select'] = array('email','emailCopie','emailTexte');
		
		$this->fieldstructure();	
		$this->onglets = array('bon_de_pret_ligne');
		$this->files = array(
			"pdf"=>array("type"=>"pdf","preview"=>true)
		);
	}
	
	public function insert($infos,&$s,$files=NULL,&$cr=NULL) {
		
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$lignes = json_decode($infos["values_".$this->table]["produits"],true);
		$this->infoCollapse($infos);

		// Pour regénérer le fichier à chaque fois ?
		foreach($this->files as $key=>$item){
			if($infos["filestoattach"][$key]==="true"){
				$infos["filestoattach"][$key]="";
			}
		}

		//Si c'est une insertion et non pas un update
		if(!$infos["ref"]){
			$infos["ref"] = $this->getRef($infos["date"]);
		}

		//Vérification du formulaire
		$this->check_field($infos);
		
		ATF::db($this->db)->begin_transaction();
		$id = parent::insert($infos,$s,$files,$cr);
		
		//Lignes
		foreach($lignes as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("bon_de_pret_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}
			unset($item["id_stock_fk"]);
			$item["id_bon_de_pret"]=$id;
			ATF::bon_de_pret_ligne()->insert($item,$s);
		}
		
		
		if($preview){
			$this->move_files($id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			unset($cr);
			return $this->cryptId($id);
		}else{
			$this->move_files($id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			ATF::db($this->db)->commit_transaction();
		}

		return $this->cryptId($id);
	}

	/**
    * Retourne la ref
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param int $id_parent
	* @return string ref
    */
	function getRef($date){
		$prefix="BP";
		$prefix .= strtoupper(substr(ATF::agence()->nom(ATF::$usr->get('id_agence')),0,2)).date("ym",strtotime($date));
		
		$this->q->reset()
					   ->addCondition("ref",$prefix."%","AND",false,"LIKE")
					   ->addField('SUBSTRING(`ref`,9)+1',"max_ref")
					   ->addOrder('ref',"DESC")
					   ->setDimension("row")
					   ->setLimit(1);
		
		$nb=$this->sa();
		
		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="00".$nb["max_ref"];
			}else{
				$suffix="0".$nb["max_ref"];
			}
		}else{
			$suffix="001";
		}
		return $prefix.$suffix;
	}
	
	public function duree($id) {
		$bp = $this->select($id);
		return util::date_diff($bp['date_debut'],$bp['date_fin'],true);
	}

};
?>