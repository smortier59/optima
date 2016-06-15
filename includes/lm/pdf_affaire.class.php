<?
/** Classe pdf_affaire
* @package Optima
* @subpackage Absystech
*/

class pdf_affaire extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct($table_or_id);
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			 'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"scanner","width"=>200)
			,'fichier_joint2'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			,'pdf_affaire.id_affaire'
			,'pdf_affaire.provenance'	
			,"action"=>array("custom"=>true,"nosort"=>true,"renderer"=>"transfertPDFAffaire","width"=>80)

		);

		$this->colonnes['primary'] = array(
			'id_affaire',
			'provenance'
		);

		$this->colonnes["panel"]['fichier'] = array(
			"fichier"=>array("custom"=>true)
		);				
		
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array("fichier");
		
		$this->fieldstructure();


		$this->foreign_key["id_affaire"] = "affaire";
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>false,"no_upload"=>false,"no_generate"=>true);

		$this->addPrivilege('getUrlImagePDF');		
		$this->addPrivilege('getAll');	
		
	}
	

	public function dynamicPicture($id_pdf_affaire){
		if (!$id_pdf_affaire) return false;
		
		$id_pdf_affaire_crypted = $id_pdf_affaire;
		$id_pdf_affaire = $this->decryptId($id_pdf_affaire);

		$path = $this->filepath($id_pdf_affaire,"fichier_joint");	

		$name = $id_pdf_affaire;
	        
    	// Nom du document final
    	$filename = "dldoc";
    	$extension = "pdf";
    	// Filename du fichier a convertir
    	$fn = $path;
    	// Chemin vers la miniature
    	$pos = strrpos($path, '/' , -1);
    	$pathPDF = substr($path , 0 , $pos);

    	$previewFn = $pathPDF.'/'.$id_pdf_affaire.".previewPDF";

   		
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

   		//$this->u(array("id_pdf_affaire"=> $id_pdf_affaire , "nb_page"=> $page));

    	// On prépare nos URL de vignette et de DL
	    $array['URL'] = __MANUAL_WEB_PATH__.$this->table."-".$id_pdf_affaire_crypted."-previewPDF";
    	$array['URLDL'] =__MANUAL_WEB_PATH__.$this->table."-".$id_pdf_affaire_crypted."-previewPDF";	        

        // Ici on renomme les fichiers extrait avec leur vrai nom par le nom qu'on leur attribut
        //rename($path2extract.$name,$path2extract.$id_pdf_affaire.".".$filename.$i);     
        return $array;
   	}	

	public function imageExist($id){
		//$id = $this->decryptId($id);
		return (file_exists($this->filepath($id,"previewPDF_0")));	
	}

	public function getUrlImagePDF($id){	
		$id_decrypt = $this->decryptId($id);		
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
		
		if(!$return["data"]) $return["data"] = $return;

		foreach ($return['data'] as $k=>$i) {			
			if($i["pdf_affaire.id_pdf_affaire"]) $id = $this->cryptId($i["pdf_affaire.id_pdf_affaire"]);
			else $id = $this->cryptId($i["id_pdf_affaire"]);
			
			$url = NULL;
			if($this->getFichierJoint($id)){
				if($this->imageExist($id)){	$url = $this->getUrlImagePDF($id); }
				else{
					//On génére l'image
					if($this->getFichierJoint($id)){
						$url = $this->dynamicPicture($id);
					}					
				}
			}
			$return["data"][$k]["url"] = $url["URL"];
			$return["data"][$k]["urlBig"] = $url["URLDL"];
			$return["data"][$k]["urlPDF"] = $url["URL"]."_0-800-300.png";
		}
		return $return;
	}


	/**
	* Permet de transferer le pdf vers le bon module et champs
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/	
	public function transfert($infos){
		$infos["id_pdf_affaire"] = $this->decryptId($infos["id_pdf_affaire"]);
		$data = explode(".", $infos["comboDisplay"]);
					
		ATF::getClass($data[0])->q->reset()->addField($data[0].".id_".$data[0], "id")
										   ->where($data[0].".ref", $infos["reference"]);
		$classe = ATF::getClass($data[0])->select_row();		
		

		if($classe){									
			copy(ATF::pdf_affaire()->filepath($infos["id_pdf_affaire"],"fichier_joint") , ATF::getClass($data[0])->filepath($classe["id"],$data[1]));

			//$this->update(array("id_scanner" => $infos["id_scanner"] , "transfert" => $infos["transfert"]." (ref : ".$infos["reference"].")"));
		}else{
			$module = explode("-", $infos["transfert"]);
			throw new errorATF("Il n'y a pas de ".$module[0]." ayant la référence ".$infos["reference"]);
		}		
	}
};