<?
/** Classe pdf_societe
* @package Optima
* @subpackage Absystech
*/

class pdf_societe extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct($table_or_id);
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			,'fichier_joint2'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>50)
			,'nom_document'
			,'pdf_societe.id_societe'
		);

		$this->colonnes['primary'] = array(
			'id_societe'
		);

		$this->colonnes["panel"]['fichier'] = array(
			"fichier"=>array("custom"=>true)
		);

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array("fichier");

		$this->fieldstructure();


		$this->foreign_key["id_societe"] = "societe";
		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>false,"no_upload"=>false,"no_generate"=>true);
		$this->files["fichier_joint2"] = array("preview"=>false,"no_upload"=>false,"no_generate"=>true);

		$this->addPrivilege('getUrlImagePDF');
		$this->addPrivilege('getAll');

	}


	public function dynamicPicture($id_pdf_societe){
		if (!$id_pdf_societe) return false;

		$id_pdf_societe_crypted = $id_pdf_societe;
		$id_pdf_societe = $this->decryptId($id_pdf_societe);

		$path = $this->filepath($id_pdf_societe,"fichier_joint");

		$name = $id_pdf_societe;

    	// Nom du document final
    	$filename = "dldoc";
    	$extension = "pdf";
    	// Filename du fichier a convertir
    	$fn = $path;
    	// Chemin vers la miniature
    	$pos = strrpos($path, '/' , -1);
    	$pathPDF = substr($path , 0 , $pos);

    	$previewFn = $pathPDF.'/'.$id_pdf_societe.".previewPDF";


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

   		//$this->u(array("id_pdf_societe"=> $id_pdf_societe , "nb_page"=> $page));

    	// On prépare nos URL de vignette et de DL
	    $array['URL'] = __MANUAL_WEB_PATH__.$this->table."-".$id_pdf_societe_crypted."-previewPDF";
    	$array['URLDL'] =__MANUAL_WEB_PATH__.$this->table."-".$id_pdf_societe_crypted."-previewPDF";

        // Ici on renomme les fichiers extrait avec leur vrai nom par le nom qu'on leur attribut
        //rename($path2extract.$name,$path2extract.$id_pdf_societe.".".$filename.$i);
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
			if($i["pdf_societe.id_pdf_societe"]) $id = $this->cryptId($i["pdf_societe.id_pdf_societe"]);
			else $id = $this->cryptId($i["id_pdf_societe"]);

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

	public function _documents($infos){

		ATF::pdf_societe()->q->reset()->where('pdf_societe.id_societe',$infos["id_societe"]);

		$pdf_societes = ATF::pdf_societe()->select_all();

		$response = "";

		$zipname = 'liasse_documentaire' . date("Ymd-Hi") . '.zip';
		$zip = new ZipArchive();
		if($zip->open(__TEMP_PATH__  .$zipname, ZipArchive::CREATE) === true){
			if($pdf_societes){

				foreach($pdf_societes["data"] as $item){

					// zippage des fichiers pdf
					if(file_exists($this->filepath($item['id_pdf_societe'],"fichier_joint"))){
						$name=$item['id_pdf_societe'].".pdf";
						try{
							$zip->addFile($this->filepath($item['id_pdf_societe'],"fichier_joint"),$name);
							}catch(errorATF $e){
								throw $e;
						}
					}

					//zippage des autres fichiers
					if(file_exists($this->filepath($item['id_pdf_societe'],"fichier_joint2"))){
						$name=$item['id_pdf_societe'].".zip";
						try{
							$zip->addFile($this->filepath($item['id_pdf_societe'],"fichier_joint2"),$name);
							}catch(errorATF $e){
								throw $e;
						}
					}
				}
			}

		}

		$zip->close();

		$response = $this->getBase64(__TEMP_PATH__  .$zipname);

		unlink(__TEMP_PATH__  .$zipname);

		return $response;


	}


	public function getBase64($path){
		$data = file_get_contents($path);
	  	return base64_encode($data);
	}
};