<?
/** Classe facture fournisseur
* @package Optima
* @subpackage Absystech
*/

class facture_fournisseur extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct($table_or_id);
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"scanner","width"=>200),
			'facture_fournisseur.id_societe',
			'facture_fournisseur.date',
			'facture_fournisseur.achat'=>array("width"=>150,"rowEditor"=>"setInfos"),
			'facture_fournisseur.fourniture'=>array("width"=>150,"rowEditor"=>"setInfos"),
			'facture_fournisseur.immo'=>array("width"=>150,"rowEditor"=>"setInfos"),
			'facture_fournisseur.frais_generaux'=>array("width"=>150,"rowEditor"=>"setInfos"),
			'facture_fournisseur.montant_ht'=>array("width"=>150,"rowEditor"=>"setInfos"),
			'facture_fournisseur.ocr',
			'facture_fournisseur.statut'=>array("width"=>150,"rowEditor"=>"setInfos"),
			'facture_fournisseur.date_paiement'=>array("renderer"=>"updateDate","width"=>170)

		);

		$this->colonnes['primary'] = array(
			'id_societe',
			'date',
			'achat',
			'fourniture',
			'immo',
			'frais_generaux',
			'montant_ht',
			'statut',
			'date_paiement',
			'nb_page'
		);

		$this->colonnes["panel"]['fichier'] = array(
			"fichier"=>array("custom"=>true)
		);

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array("ocr", "fichier");



		$this->fieldstructure();
		$this->foreign_key["id_societe"] = "societe";
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>false,"no_upload"=>false,"no_generate"=>true);

		$this->onglets = array('facture_fournisseur_affaire');

		$this->addPrivilege('getUrlImagePDF');
		$this->addPrivilege('setInfos');
		$this->addPrivilege('getAll');

		$this->field_nom = "[%id_societe%] %date%";

	}

	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$this->infoCollapse($infos);

		$last_id = parent::insert($infos,$s);

		ATF::facture_fournisseur()->redirection("select",$last_id);
	}

	/** Analyse l'OCR pour déterminen un montant hors-taxe
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return void
	*/
	public function getNextAfterOCR(){
		ATF::facture_fournisseur()->q->reset()->select('id_facture_fournisseur')->select('ocr')->whereIsNull("montant_ht")->whereIsNotNull("ocr");
		if($result = ATF::facture_fournisseur()->select_all()){
			foreach ($result as $key => $value) {

				//echo $value["ocr"]."\n\n";
				$s = str_replace("\n","",$value["ocr"]);
				$s = str_replace("\r","",$s);
				$s = str_replace("\t","",$s);
				$s = preg_match_all("/([0-9]*[\.\,][0-9][0-9])/", $s, $matches);
				$nb = count($matches[1]);
				$matches[1] = str_replace(",",".",$matches[1]);

				$montant_ht = 0;
				for($x=0;$x<$nb;$x++) {
					for($y=0;$y<$nb;$y++) {
						//echo "\n".round($matches[1][$x] * 1.2,2)." == ". $matches[1][$y];
						if ($x!=$y && round($matches[1][$x] * 1.2,2) == $matches[1][$y]) {
							if ($matches[1][$x]>$montant_ht)
								$montant_ht=$matches[1][$x];
						}
					}
				}

				if ($montant_ht) {
					log::logger("Montant pour facture fournisseur ".$value["id_facture_fournisseur"]." probable de : ".$montant_ht,"ocr");
				} else {
					log::logger("Aucun montant probable pour facture fournisseur ".$value["id_facture_fournisseur"].".","ocr");
				}

				$this->update(array(
					"id_facture_fournisseur"=>$value["id_facture_fournisseur"],
					"montant_ht"=>$montant_ht
				));
			}
		}
		return;
	}

	//Retourne les id des facture_fournisseur n'ayant pas encore le champs OCR rempli
	public function getNextToOCR(){
		log::disableLog();
		ATF::facture_fournisseur()->q->reset()->whereIsNull("ocr");
		$result = ATF::facture_fournisseur()->select_all();
		if($result){
			$res = array();
			foreach ($result as $key => $value) {
				$filename = ATF::facture_fournisseur()->filepath($value["id_facture_fournisseur"],"fichier_joint");
				if(file_exists($filename)){
					$res[] = $value["id_facture_fournisseur"];
				}
			}
			if(!empty($res)) return $res;
		}
		return;
	}

	public function dynamicPicture($id_facture){
		if (!$id_facture) {
			return false;
		}
		$id_facture_crypted = $id_facture;
		$id_facture = $this->decryptId($id_facture);

		$path = $this->filepath($id_facture,"fichier_joint");

		$name = $id_facture;

		$array['URL'] = "";
	    $array['URLDL'] = "";

	    if(file_exists($path)){
	    	// Nom du document final
	    	$filename = "dldoc";
	    	$extension = "pdf";
	    	// Filename du fichier a convertir
	    	$fn = $path;
	    	// Chemin vers la miniature
	    	$pos = strrpos($path, '/' , -1);
	    	$pathPDF = substr($path , 0 , $pos);

	    	$previewFn = $pathPDF.'/'.$id_facture.".previewPDF";


	   		//On recupere le nombre de page
	   		$page = array();
	   		$handle = fopen($path, "r");
			$contents = fread($handle, filesize($path));
			fclose($handle);

	   		preg_match_all("#/Count ([0-9]*)#" , $contents, $page);
	   		if(!empty($page[1])){ $page = $page[1][count($page[1])-1];	}
	   		else{	$page = 1;	}

	   		for($i=0; $i<$page; $i++){
	   			 //execute imageMagick's 'convert', setting the color space to RGB
			    $cmd = "convert \"{$fn}[".$i."]\" -colorspace RGB -geometry 900 -quality 100 -flatten ".$previewFn."_".$i.".png";
			    exec($cmd);

			    // Renommer l'image créée par le convert pour lui soustraire son extension
	    		util::rename($previewFn."_".$i.".png",$previewFn."_".$i);
	   		}

	   		$this->u(array("id_facture_fournisseur"=> $id_facture , "nb_page"=> $page));

	    	// On prépare nos URL de vignette et de DL
		    $array['URL'] = __MANUAL_WEB_PATH__.$this->table."-".$id_facture_crypted."-previewPDF";
	    	$array['URLDL'] =__MANUAL_WEB_PATH__.$this->table."-".$id_facture_crypted."-previewPDF";
	    }


        // Ici on renomme les fichiers extrait avec leur vrai nom par le nom qu'on leur attribut
        //rename($path2extract.$name,$path2extract.$id_facture.".".$filename.$i);
        return $array;
   	}

	public function imageExist($id){
		//$id = $this->decryptId($id);
		return (file_exists($this->filepath($id,"previewPDF_0")));
	}

	public function getUrlImagePDF($id){
		$id_decrypt = $this->decryptId($id);
		$page = $this->select($id_decrypt , "nb_page");
		$page = $page-1;
		$array['page'] = $page;
		$id= $this->cryptId($id);
		$array['URL'] = __MANUAL_WEB_PATH__.$this->table."-".$id."-previewPDF";
		$array['URLDL'] = __MANUAL_WEB_PATH__.$this->table."-".$id."-previewPDF";;
		return $array;
	}

	public function getFichierJoint($id){
		$id = $this->decryptId($id);
		return file_exists($this->filepath($id,"fichier_joint"));
	}

	/**
	* Surcharge du select-All
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$return = parent::select_all($order_by,$asc,$page,$count);

		foreach ($return['data'] as $k=>$i) {
			if($i["facture_fournisseur.id_facture_fournisseur"]) $id = $this->cryptId($i["facture_fournisseur.id_facture_fournisseur"]);
			else $id = $this->cryptId($i["id_facture_fournisseur"]);

			$url = NULL;
			if($this->getFichierJoint($id)){
				if($this->imageExist($id)){	$url = $this->getUrlImagePDF($id); }
				else{
					$url = $this->dynamicPicture($id);
				}
			}
			$return["data"][$k]["url"] = $url["URL"];
			$return["data"][$k]["urlBig"] = $url["URLDL"];
			$return["data"][$k]["page"] = $url["page"];
			$return["data"][$k]["urlPDF"] = $url["URL"]."_0-800-300.png";
		}
		return $return;
	}
	public function setInfos($infos){
		$res = $this->update(array("id_facture_fournisseur"=> $this->decryptId($infos["id_facture_fournisseur"]),
						  $infos["field"] => $infos["value"])
					);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}
}
?>