<?php
/**
* Classe upload
* @package ATF
* Cet objet gère l'upload de fichier de 3 manière différentes : basic HTML, flash et AJAX
*/
class upload extends classes_optima {
	/**
	* Colonnes SELECT ALL
	* @var mixed
	*/
	public $protocoleUploadXHR = 'php://input';

	/**
	* Constructeur
	* @todo Trouver plus propre pour le try catch de filedstructure...
	*/
	public function __construct($table_or_id=NULL) {
		//Appel du constructeur de classes
		parent::__construct($table_or_id);

	}

	/**
	* Upload AJAX d'un fichier
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 01-05-2011
	* @return JSON
	*/
	public function xhrupload(&$infos,$s) {
		$infos['display'] = true;
		$tmpFile = "/tmp/tempDLATF-".util::generateRandWord();

		util::transverseFile($tmpFile,$this->protocoleUploadXHR);

		$return = array("success"=>true);

		if (!$t = ATF::getClass(ATF::_r('table'))) {
			throw new errorATF("La classe pour la table '".ATF::_r('table')."' n'existe pas\n",222);
		}
		//Header 'X-File-Name' has the dashes converted to underscores by PHP:
		if(!$filename = ATF::_srv('HTTP_X_FILE_NAME')){
			$return = array("success"=>false,"error"=>"Impossible d'identifier le nom du fichier.","errorNo"=>510);
		} elseif (!$infos['field']){
			$return = array("success"=>false,"error"=>"Impossible d'identifier le nom du fichier.","errorNo"=>511);
		} else {

			$filename = util::removeAccents($filename,false,"iso-8859-1");

			$f = $t->filepath(ATF::$usr->getID(),$infos['field'].".".$filename,true);
			util::rename($tmpFile,$f);
			//$t->store($s,ATF::$usr->getID(),$infos['field'].".".$filename,$data,true);
		}
		echo json_encode($return);

	}

	/**
	* Upload FLASH d'un fichier
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 01-05-2011
	* @return JSON
	*/
	public function flashupload(&$infos,$s,$files=false) {
		$infos['display'] = true;

		$return = array("success"=>true);

		if ($infos['codename'] && !ATF::$codename) ATF::$codename = $infos['codename'];

		$t = ATF::getClass(ATF::_r('table'));
		foreach ($t->files as $k=>$i) {
			if (!$files[$k]) continue;
			$data = file_get_contents($files[$k]['tmp_name']);
			if(!$files[$k]['name']){
				$return = array("success"=>false,"error"=>"Impossible d'identifier le nom du fichier.","errorNo"=>510);
			} elseif($files[$k]['error']){
				$return = array("success"=>false,"error"=>"Une erreur est survenue pendant le trasfert du fichier.","errorFiles"=>$files[$k]['error'],"errorNo"=>511);
			} elseif(!$files[$k]['size']){
				$return = array("success"=>false,"error"=>"Le fichier uploadé fait 0 octets.","errorNo"=>512);
			} else {
				$t->store($s,$infos['id_user'],$k.".".$files[$k]['name'],$data,true);
			}
		}

		echo json_encode($return);
	}
	/**
	* Upload HTML d'un fichier
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 01-05-2011
	* @return JSON
	*/
	public function basicupload(&$infos,$s,$files=false,$log=false) {
		$infos['display'] = true;
		$return = array("success"=>true);

		$t = ATF::getClass(ATF::_r('table'));

		foreach ($t->files as $k=>$i) {
			if (!$files[$k]) continue;
			$data = file_get_contents($files[$k]['tmp_name']);
			if(!$files[$k]['name']){
				$return = array("success"=>false,"error"=>"Impossible d'identifier le nom du fichier.","errorNo"=>510);
			} elseif($files[$k]['error']){
				$return = array("success"=>false,"error"=>"Une erreur est survenue pendant le trasfert du fichier.","errorNo"=>511);
			} elseif(!$files[$k]['size']){
				$return = array("success"=>false,"error"=>"Le fichier uploadé fait 0 octets.","errorNo"=>512);
			} else {
				$t->store($s,ATF::$usr->getID(),$k.".".$files[$k]['name'],$data,true);
			}
		}

		echo json_encode($return);
	}

	/**
	* Récupération de tous les fichiers liés a l'élément, que ça soit dans le ZIP associé ou dans le dossier temporaire
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 01-05-2011
	* @return JSON
	*/
	public function getAllFiles($table,$id=false,$field=false) {
		$t = ATF::getClass($table);
		if ($id) $id = $t->decryptID($id);
		$return = array();
		foreach ($t->files as $k=>$i) {
			if ($field && $field!=$k) continue;
			if ($i["type"]=="pdf") continue;
			// J'échappe les document récupéré pour l'update des devis, car l'update des devis engendre un INSERT du fait des révisions
			if ($id && $t->table!="devis") {
				$zipName = $t->filepath($id,$k);
				if (file_exists($zipName) && class_exists('ZipArchive')) {
					$zip = new ZipArchive();
					$res = $zip->open($zipName);
					if ($res !== TRUE) {
						throw new errorATF("Ouverture du ZIP (".$zipName.") Impossible, res = ".$res,501);
					}

					for ($j=0; $j<$zip->numFiles;$j++) {
						$d = $zip->statIndex($j);

						$file = explode(".",$d['name']);
						$return[] = array(
							"field"=>$k,
							"action"=>$t->table,
							"id"=>$d['index'],
							"name"=>$file[0],
							"format"=>$file[1],
							"size"=>$d['size'],
							"status"=>ATF::$usr->trans("deja_present"),
							"progress"=>"100"
						);

					}
				}
			}
			$dirTemp = dirname($t->filepath(ATF::$usr->getID(),"",true));
			foreach (scandir($dirTemp) as $k_=>$i_) {
				$regex = "#^(".ATF::$usr->getID().")\.(".$k.")\.([-. a-zA-Z0-9\-\_\#]*)\.([a-zA-Z0-9]+)$#";
				if (preg_match_all($regex,$i_,$m)) {
					$return[] = array(
						"field"=>$k,
						"action"=>$t->table,
						"id"=>$k_,
						"name"=>$m[3][0],
						"format"=>$m[4][0],
						"size"=>filesize($dirTemp."/".$i_),
						"status"=>ATF::$usr->trans("fichier_temporaire_recupere"),
						"progress"=>"100"
					);
				}
			}
		}
		return json_encode($return);
	}

	/**
	* Supprime un des fichiers liés a la note de frais, que ça soit dans le ZIP associé ou dans le dossier temporaire
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 01-05-2011
	* @return int Index du fichier supprimé
	*/
	public function deleteFile($infos) {
		if (!is_numeric($infos['index'])) return false;
		if (!$infos['name']) return false;
		if (!$infos['field']) return false;
		if (!$infos['action']) return false;

		$t = ATF::getClass($infos['action']);
		// Avoir un ID ne signifie pas qu'il y a forcément le zip associé... du coup on test plus bas le zipname
		if ($infos['id'] && $infos['id']!="undefined") {
			$id = $t->decryptId($infos['id']);
			$zipName = $t->filepath($id,$infos['field']);
		}

		if ($zipName && file_exists($zipName)) {
			// suppression des fichiers dans le zip du /data
			$zip = new ZipArchive;
			if ($zip->open($zipName) === TRUE) {
				// Il est dans le ZIP
				if ($zip->getFromName($infos['name'].".".$infos['format'])) {
					$zip->deleteIndex($infos['index']);
				//Il n'est pas dans le ZIP
				} else {
					$fileName = $t->filepath(ATF::$usr->getID(),$infos['field'].".".$infos['name'].".".$infos['format'],true);
					util::rm($fileName);
				}
				$zip->close();
			} else {
				throw new errorATF("Ouverture du ZIP (".$zipName.") Impossible, res = ".$res,501);
			}
		} elseif ($infos['format'] && $infos['format']!="undefined") {
			// suppression des fichiers dans temp lorsqu'il ne sont pas encore enregistrés
			$fileName = $t->filepath(ATF::$usr->getID(),$infos['field'].".".$infos['name'].".".$infos['format'],true);
			util::rm($fileName);
		} else {
			// suppression des fichiers dans temp lorsqu'il ne sont pas encore enregistrés
			$fileName = $t->filepath(ATF::$usr->getID(),$infos['field'].".".$infos['name'],true);
			util::rm($fileName);
		}
		return $infos['index'];
	}
};
?>
