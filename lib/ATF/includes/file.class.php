<?php
/**
* Classe file pour Optima, gère les fichiers pour telescope en utilisant ATF
* @package ATF
*/
class file {
	private static $prefixKey = "telescope-";

	public static function cached($key=NULL,$value=NULL, $ttl=300) {
		$path = __TEMP_PATH__."/filecached/";
		if (!is_dir($path)) {
			mkdir($path);
		}
		if ($value !== NULL) {
			if (file_put_contents($path.self::$prefixKey.$key, $value)) {
				return touch($path.self::$prefixKey.$key, time() + $ttl);
			} else {
				throw new errorATF("Impossible d'écrire le fichier ".$path.self::$prefixKey.$key);
			}
		} elseif ($key !== NULL) {
			if (filemtime($path.self::$prefixKey.$key)>time()) {
				return file_get_contents($path.self::$prefixKey.$key);
			}
		}
	}

	public function _GET($get) {
		if (!$get['mod']) throw new Exception("MODULE_MISSING",1000);
		if (!$get['id']) throw new Exception("ID_MISSING",1001);
		if (!$get['field']) throw new Exception("FIELD_MISSING",1001);
		$mod = $get['mod'];
		$class = ATF::getClass($mod);
		$id = $get['id'];
		$id_c = $class->cryptId($id);

		if ($get['temp']) {
			$return = $this->getTemporaryFiles($get);
		} else {
			if ($class->files[$get['field']]['multiUpload']) {
				$return = $this->getFilesFromZipArchive($get);
			} else {
				$return = $this->getFileAlone($get);
			}
		}

		if ($get['dl']) {
			header("Pragma: public");
			header("Expires: 0");
			//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: no-cache");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment; filename=\"".addslashes($filename).'"');
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".$size);
			readfile($target);
		} else {
			return $return;
		}

	}

	public function _dl($get, $post) {
		if (!$get['mod']) throw new Exception("MODULE_MISSING",1000);
		if (!$get['id']) throw new Exception("ID_MISSING",1001);
		if (!$get['field']) throw new Exception("FIELD_MISSING",1001);
		$mod = $get['mod'];
		$class = ATF::getClass($mod);
		$id = $get['id'];
		$id_c = $class->cryptId($id);

		$target = $class->filepath($id,$get["field"],$get["temp"]);

		if ($class->files[$post["field"]]["type"]) {
			$return["strMimeType"] = "Content-Type: application/".$class->files[$post["field"]]["type"];
		} else {
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // Retourne le type mime à la extension mimetype
			$ct = finfo_file($finfo, $target);
			$return["strMimeType"] = $ct;
			header("Content-Type: ".$ct);
			if ($ext = util::getExtensionByContentType($ct)) {
				$filename = str_replace(".zip",$ext,$filename);
			}

			finfo_close($finfo);
		}

		$return["strFileName"] = $get['mod']."-".$get['id']."-".$get['field'].".".($class->files[$get['field']]['type']?$class->files[$get['field']]['type']:"zip");
		$return["data"] = base64_encode(file_get_contents($target));

		return $return;
	}
	/*!
	 * [_POST permet d'uploader des fichiers]
	 * @param  [type] $get   [description]
	 * @param  [type] $post[mod]  [description]
	 * @param  [type] $post[field] Noms de fichiers uploadés séparés par virgule
	 * @param  [type] $files [description]
	 * @return [type]        [description]
	 */
	public function _POST($get, $post, $files) {
		if (!$post['mod']) throw new Exception("MODULE_MISSING",1000);
		if (!$post['field']) throw new Exception("FIELD_MISSING",1001);

		if (empty($files)) throw new Exception("FILE_MISSING",1002);


		$prefix_error = utf8_decode("Impossible de traiter le(s) document(s), détail erreur : ");

		$fields = explode(",",$post['field']);
		//log::logger($files, __CLASS__.".".__FUNCTION__);
		foreach ($fields as $k=>$i) {
			$id = NULL; // Important
			if (strpos($i,'.')!==false) {
				// Classe spécifiée avec un prefixe "." (par exemple commande.retourPV)
				$field = explode(".",$i);
				$class = ATF::getClass($field[0]);
				$id = $class->decryptId($field[1]);
				$field = $field[2];
			} else {
				// Classe du module mod
				$field = $i;
				$id = $post["id"];
				$class = ATF::getClass($post['mod']);
			}
			log::logger($field." => ".$class->table, __CLASS__.".".__FUNCTION__);

			if (!$class->files[$field]) throw new Exception($prefix_error."FILE_CONFIG_NULL",1005);
			if ($files[$field]['error']) throw new Exception($prefix_error."FILE_ERROR, code: ".$files[$field]['error'],1003);
			if (!$files[$field]['size']) continue;
			// if (!$files[$field]['size']) throw new Exception("FILE_SIZE_ERROR",1004);

			if ($class->files[$field]['multiUpload']) {
				$filename = $post['filename'] ? $post['filename'] : $files[$field]['name'];
				$filename = util::removeAccents($filename,false,"iso-8859-1");
				$f = $class->filepath(ATF::$usr->getID(),$field.".".$filename,true);
			} else if ($id) {
				$f = $class->filepath($id,$field);
			} else {
				$f = $class->filepath(ATF::$usr->getID(),$field,true);
			}

			try {
				log::logger($field." => ".$class->table." move_uploaded_file(".$files[$field]['tmp_name'].",".$f.")", __CLASS__.".".__FUNCTION__);
				move_uploaded_file($files[$field]['tmp_name'],$f);
			} catch (Exception $e) {
				throw $e;
			}
		}

		return true;
	}

	private function getTemporaryFiles($get) {
		$class = ATF::getClass($get['mod']);
		$dirTemp = dirname($class->filepath(ATF::$usr->getID(),"",true));
		foreach (scandir($dirTemp) as $k=>$i) {
			$regex = "#^(".ATF::$usr->getID().")\.(".$get['field'].")\.([-. a-zA-Z0-9\-\_\#]*)\.([a-zA-Z0-9]+)$#";
			if (preg_match_all($regex,$i,$m)) {
				$return[] = array(
					"name"=>utf8_encode($m[3][0]),
					"size"=>filesize($dirTemp."/".$i),
					"ext"=>$m[4][0],
					"thumb"=>$this->getThumbPath($m[4][0]),
					"mod"=>$class->table,
					"id"=>ATF::$usr->getID(),
					"temp"=>true,
					"field"=>$get['field']
				);
			}
		}

		return $return;
	}

	private function getThumbPath($ext) {
		switch ($ext) {
			case 'json':
				$iconename = 'js';
			break;
			default:
				$iconename = $ext;
			break;
		}

		return '/icone/extensions/'.$iconename.'.png';
	}

	private function getFilesFromZipArchive($get) {
		$class = ATF::getClass($get['mod']);

		$path = $class->filepath($get['id'], $get['field']);
		// $path2extract = dirname($path)."/";

		//Dézippage
		$zip = new ZipArchive();
		$zip->open($path);

		for($i = 0; $i < $zip->numFiles; $i++){

			$infos_fichier = $zip->statIndex($i);

			//$name = utf8_decode($infos_fichier['name']);
			$name = $infos_fichier['name'];

			$extension=strtolower(substr(strrchr($name,".") ,1));
			$size=$infos_fichier['size'];
			$array[$i] = array(
				"name"=>utf8_encode($name),
				"size"=>$size,
				"ext"=>$extension,
				"thumb"=>$this->getThumbPath($extension),
				"mod"=>$class->table,
				"id"=>$get['id'],
				"field"=>$get['field']
			);

			// $zip->extractTo($path2extract,$name);

			// Ici on renomme les fichiers extrait avec leur vrai nom par le nom qu'on leur attribut
			// rename($path2extract.$name,$path2extract.$id.".".$filename.$i);

		}

		$zip->close();
		return $array;
	}

	private function getFileAlone($get) {
		$class = ATF::getClass($post['mod']);


	}

	public function _DELETE($get,$post) {
		$input = file_get_contents('php://input');
		if (!empty($input)) parse_str($input,$post);
		if (!$post['mod']) throw new Exception("MODULE_MISSING",1000);
		if (!$post['field']) throw new Exception("FIELD_MISSING",1001);
		$class = ATF::postClass($post['mod']);
		$field = $post['field'].($post['temp'] ? ".".$post['name'].".".$post['ext'] : '');

		if ($class->files[$post['field']]["multiUpload"] && !$post['temp']) {
			$path = $class->filepath($post['id'], $field);
			// suppression des fichiers dans le zip du /data
			$zip = new ZipArchive;
			if ($zip->open($path) === TRUE) {
				// Il est dans le ZIP
				if ($zip->getFromName($post['name'])) {
					$zip->deleteIndex($post['index']);
				}
				// Si plus de fichier, alors on nique le ZIP
				if ($zip->numFiles) {
					util::rm($path);
				}
				$zip->close();
			} else {
				throw new errorATF("Ouverture du ZIP (".$path.") Impossible, res = ".$res,1002);
			}
			return true;
		} else {
			$path = $class->filepath($post['id'], $field, $post['temp'] ? true : false);
			return util::rm($path);

		}
	}
}
