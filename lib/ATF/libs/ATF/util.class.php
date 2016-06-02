<?php
/**
* La classe util fournit de nombreux outils de formatage,
* de conversion ou de manipulation de fichiers.
* C'est elle qui gère également la traduction.
*
* @date 2008-11-03
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
class util {

	/**
	* Supprime tous les fichiers avec syntaxe *
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $fileglob Adresse du fichier ou syntaxe à supprimer ou nom du dossier a vider
	* @example rm("thumbs_675_*") supprime tous les fichiers commençant par "thumbs_675_"
	* @return boolean Vrai si cela s'est correctement passé, Faux sinon
	*/
	public static function rm($fileglob) {
		if (is_string($fileglob)) {
		   if (is_file($fileglob)) {
			   return unlink($fileglob);
		   } else if (is_dir($fileglob)) {
			   $ok = self::rm("$fileglob/*");
			   rmdir($fileglob);
			   return $ok;
		   } else {
			   $matching = glob($fileglob);
			   if ($matching === false || !count($matching)) {
				   trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
				   return false;
			   }
			   $rcs = array_map('self::rm', $matching);
			   if (in_array(false, $rcs)) return false;
		   }
	   } else if (is_array($fileglob)) {
			$rcs = array_map('self::rm', $fileglob);
			if (in_array(false, $rcs)) return false;
	   } else {
		   trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
		   return false;
	   }

	   return true;
	}

	/**
	* Permet d'obtenir la classe d'un id pas forcément nommé comme le nom de la classe relative : id_owner vers id_user par exemple...
	* @author AbsysTech DEV <dev@absystech.fr>
	* @param string $s Nom de champs
	* @example class_from_id('id_societe') => 'societe'
	* @version 1.0
	* @return string Singleton de la classe, FALSE s'il est inexistant
	*/
	public static function class_from_id($s) {
		if (substr($s,0,3)=="id_") {
			switch (substr($s,3)) {
				case "owner":
					$return = ATF::user();
			}
			if (ATF::getClass(substr($s,3))) {
				$return = ATF::getClass(substr($s,3));
			}
			return $return;
		}
		return false;
	}

	/**
	* Copie le fichier a l'emplacement spécifié
	* @author AbsysTech DEV <dev@absystech.fr>
	* @param string $source Fichier source
	* @param string $target Emplacement pour la copie
	* @version 1.0
	* @return bollean VRAI si la copie est ok, sinon FALSE
	*/
	public static function rename($source,$target) {
		if (!is_dir(dirname($target))) {
			self::mkdir(dirname($target));
		}
		return rename($source,$target);
	}

	/**
	* Copie le fichier a l'emplacement spécifié
	* @author AbsysTech DEV <dev@absystech.fr>
	* @param string $source Fichier source
	* @param string $target Emplacement pour la copie
	* @version 1.0
	* @return bollean VRAI si la copie est ok, sinon FALSE
	*/
	public static function copy($source,$target) {
		if (!is_dir(dirname($target))) {
			self::mkdir(dirname($target));
		}
		return copy($source,$target);
	}

	/**
	* Écrit un contenu dans un fichier
	* @author AbsysTech DEV <dev@absystech.fr>
	* @param string $f Chemin vers le fichier dans lequel on doit écrire les données.
	* @param string $data Les données à écrire. Peut être soit une chaîne de caractères, un tableau ou une ressource de flux (explication plus bas).
	* @version 1.0
	* @return boolean/int Retourne le nombre d'octets qui ont été écrits au fichier, ou FALSE  si une erreur survient.
	*/
	public static function file_put_contents($f,$data) {
		if (!is_dir(dirname($f))) {
			self::mkdir(dirname($f));
		}
		return file_put_contents($f,$data);
	}

	/**
	* Crée un dossier
	* @author AbsysTech DEV <dev@absystech.fr>
	* @param string $pathname Le chemin du dossier.
	* @param string $data Les données à écrire. Peut être soit une chaîne de caractères, un tableau ou une ressource de flux (explication plus bas).
	* @version 1.0
	* @return boolean/int Retourne le nombre d'octets qui ont été écrits au fichier, ou FALSE  si une erreur survient.
	*/
	public static function mkdir($pathname,$droits=0755) {
		if (!is_dir($pathname)) {
			if (!is_dir(dirname($pathname))) {
				self::mkdir(dirname($pathname),$droits);
			}
			return mkdir($pathname,$droits);
		}
		return true;
	}

	/**
    * Retourne le hash d'un nom de ressource : règle cryptage des noms de variables stockées en session
	* La règle peut etre changée pour chaque différent portails
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $value
	* @version 1.0
    * @return string value
    */
	public static function res($value) {
   		return md5($value.strlen($value));
	}


	/**
	* Convertir un nombre en lettre ( francais )
	* @param integer nombre
	* @return string
	* @version 1.0
	*/
	public static function n2t_triade($number, $and, $preceding) {
		$small = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf');
		$medium = array(2=>'vingt', 3=>'trente', 4=>'quarante', 5=>'cinquante', 6=>'soixante', 7=>'soixante-dix', 8=>'quatre-vingt', 9=>'quatre-vingt-dix');

		$text = "";

		if ($hundreds = floor($number / 100)) {
			$text .= ($small[$hundreds]!="un"?$small[$hundreds]." cent ":" cent ");
		}
		$tens = $number % 100;
		if ($tens) {
			if ($and && ($hundreds || $preceding)) {
				if (substr($text,strlen($text)-5)!="cent ")
					$text .= " et ";
			}
			if ($tens < 20) {
				$text .= $small[$tens];
			}
			else {
				if ($medium[floor($tens/10)]=='quatre-vingt-dix') {
					if ($ones = $tens % 10) {
						$text .= 'quatre-vingt-'.$small[$ones+10];
					}
					else $text .= 'quatre-vingt-dix';
				}
				elseif ($medium[floor($tens/10)]=='soixante-dix') {
					if ($ones = $tens % 10) {
						$text .= 'soixante-'.$small[$ones+10];
					}
					else $text .= 'soixante-dix';
				}
				else {
					$text .= $medium[floor($tens/10)];
					if ($ones = $tens % 10) {
						$text .= "-".$small[$ones];
					}
				}
			}
		}

		return $text;
	}

	/**
	* Convertir un nombre en lettre ( francais )
	* @param integer nombre
	* @return string
	* @version 1.0
	*/
	public static function nb2texte($int){
		$big = array('mille', 'million', 'milliard');
		$small = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf');

		$text = "";

		$int = intval($int, 10);
		$ok = true;
		$section = 0;
		do {
				if ($section <= count($big)) {
						if ($int < 1000) {
								$convert = $int;
						} else {
								$convert = substr($int, -3);
						}
						if (substr($int, 0 , -1 *strlen($convert)) == false) $ok = false;
						$int = substr($int, 0, -1 * strlen($convert));
						if ($convert > 0) {
								$tmp = util::n2t_triade($convert, false, ($int > 0));
								if ($tmp == "un" && $big[$section-1]=="mille")
										$text = "mille ".$text;
								else $text = $tmp.' '.$big[$section-1]." ".$text;
				   }
				} else { $ok = false; }
				$section++;
		} while ($int > 0 && $ok);

		return $text;
	}

	public static function nb2TextLanguage($chiffre, $prix=false, $langue='fr') {
		$fmt = new NumberFormatter($langue, NumberFormatter::SPELLOUT);
		if (gettype($chiffre) == "string") $chiffre =  floatval(str_replace(" ", "", $chiffre));
		$retour = '';

		if (gettype($chiffre) == "double") {
			$chiffre = number_format($chiffre,2, ',', '');
			$chiffre = explode(',', $chiffre);
			$retour = $fmt->format($chiffre[0]);
			if ($prix) $retour .= " euros";
			if ($chiffre[1] > 0) {
				if ($prix) $retour .= " et".
				$retour .= " ".$fmt->format($chiffre[0]);
				if ($prix) $retour .=" centimes";
			}

		}

		if (gettype($chiffre) == "integer") {
			$retour = $fmt->format($chiffre);
			if ($prix) $retour .= " euros";
		}

		return $retour;
	}

	public static function nb2texteespanol($chiffre, $prix=false) {
		$fmt = new NumberFormatter('es', NumberFormatter::SPELLOUT);
		if (gettype($chiffre) == "string") $chiffre =  floatval(str_replace(" ", "", $chiffre));
		$retour = '';

		if (gettype($chiffre) == "double") {
			$chiffre = number_format($chiffre,2, ',', '');
			$chiffre = explode(',', $chiffre);
			$retour = $fmt->format($chiffre[0]);
			if ($prix) $retour .= " euros";
			if ($chiffre[1] > 0) {
				if ($prix) $retour .= " y".
				$retour .= " ".$fmt->format($chiffre[0]);
				if ($prix) $retour .=" c  ntimos";
			}

		}

		if (gettype($chiffre) == "integer") {
			$retour = $fmt->format($chiffre);
			if ($prix) $retour .= " euros";
		}

		return $retour;
	}

	/**
	* Renvoi l'offset d'un element dans un tableau
	* @param array $array
	* @param string $key
	* @param string $item
	* @version 1.0
	*/
	public static function getOffset($array, $key, $item=false) {
		$flag=0;
		foreach ($array as $k=>$i) {
			if ($k==$key) {
				if ($item) {
					if ($i==$item) {
						return $flag;
					}
				} else {
					return $flag;
				}
			}
			$flag++;
		}
		return false;
	}

	/**
	* Insert un element dans un tableau a l'offset choisi, conserve la clé de l'$insert_array
	* @param array $array
	* @param int $position
	* @param array $insert_array
	* @version 1.0
	*/
	public static function array_insert(&$array, $position, $insert_array) {
		$first_array = array_splice($array, 0, $position);
		$array = array_merge ($first_array, $insert_array, $array);
	}

	/**
	* Retourne un array normalisé
	* @author Y.GAUTHERON <ygautheron@absystech.fr>
	* @param array $content Template ou Contenu texte
	* @param array $title Titre
	* @param array $other_params Autres paramètres modalbox
	* @param array $data les données transférées au template .dialog
	* @version 2.0
	*/
	public static function mbox($content, $title=NULL, $other_params=NULL ,$data=NULL) {
		if (strpos($content," ")===false && ATF::$html->template_exists($content.".tpl.dialog")) { // Aucun espace dans la string, c'est forcément un template (j'espère !)
			ATF::$html->array_assign($data);
			$array["text"] = ATF::$html->fetch($content.".tpl.dialog");
		} else {
			$array["text"] = $content;
		}
		if ($other_params) {
			$array["params"]=$other_params;
		}
		if ($title) {
			$array["params"]["title"] = $title;
		}
		return $array;
	}

	/**
	* Retourne VRAI si la string est un email
	* @author Y.GAUTHERON <ygautheron@absystech.fr>
	* @param string $string
	* @version 1.0
	*/
	public static function isEmail($string) {
		return filter_var(trim($string),FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	* Retourne VRAI si la string est une url
	* @author Y.GAUTHERON <ygautheron@absystech.fr>
	* @param string $string
	* @version 1.0
	*/
	public static function isURL($string) {
		$pattern = '/^((https|http|ftp)\:\/\/|www\.)([a-zA-Z0-9.\-\_\/]*)/i';
		return preg_match($pattern,trim($string));
	}

	/**
	* Normalise une URL
	* @author Y.GAUTHERON <ygautheron@absystech.fr>
	* @param string $string on suppose que c'est un email en entrée
	* @version 1.0
	*/
	public static function fixURL($string) {
		if (!preg_match('/^(https|http|ftp)(.*)/i',trim($string))) {
			$string = "http://".$string;
		}
		return $string;
	}

	/**
	* Renvoi un tableau contenant la liste des mois
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
    * @return array Conteneur de tous les mois
	*/
	public static function month(){
		$mois=array("01"=>"January"
					,"02"=>"February"
					,"03"=>"March"
					,"04"=>"April"
					,"05"=>"May"
					,"06"=>"June"
					,"07"=>"July"
					,"08"=>"August"
					,"09"=>"September"
					,"10"=>"October"
					,"11"=>"November"
					,"12"=>"December"
					);
		$mois_final=array();
		foreach($mois as $index=>$m){
			$mois_final[$index]=ATF::$usr->trans($m);
		}
		return $mois_final;
	}
	/**
	* Permet de voir le type d'une variable dans un log
	* @author M.TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $string
    * @return array de la variable avec toutes les infos
	* @version 1.0

	public function var_dump($string){
		ob_start();
		var_dump($string);
		$s=ob_get_contents();
		ob_clean();
		return $s;
	}
	*/

	/**
	* Permet de voir le type d'une variable dans un log
	* @author Anonymous
	* @url http://fr.wikipedia.org/wiki/Relev%C3%A9_d%27Identit%C3%A9_Bancaire
	* @param string $string
    * @return boolean
	*/
	public static function check_rib($cbanque, $cguichet, $nocompte, $clerib) {
		$tabcompte = "";
		$len = strlen($nocompte);
		if ($len != 11) {
			return false;
		}
		for ($i = 0; $i < $len; $i++) {
			$car = substr($nocompte, $i, 1);
			if (!is_numeric($car)) {
				$c = ord($car) - 64;
				$b = ($c < 10) ? $c : (($c < 19) ? $c - 9 : $c - 17);
				$tabcompte .= $b;
			}
			else {
				$tabcompte .= $car;
			}
		}
		$int = $cbanque . $cguichet . $tabcompte . $clerib;
		return (strlen($int) >= 21 && bcmod($int, 97) == 0);
	}

	/**
	* Formatte le RIB pour l'affichage (Ajoute les espaces)
	* @author JANON Quentin <qjanon@absystech.fr>
	* @param string $string
    * @return string $string
	*/
	public static function formatRIB($rib) {
		$rib = str_replace(" ","",$rib);
		$cbanque = substr($rib,0,5);
		$cguichet = substr($rib,5,5);
		$nocompte = substr($rib,10,11);
		$clerib = substr($rib,-2);

		return $cbanque." ".$cguichet." ".$nocompte." ".$clerib;
	}

	/**
	* Retourne VRAI si le fichier existe dans le répertoire du projet
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $file Chemin relatif d'un fichier contenu dans le projet courant
	* @return boolean
	*/
	public static function file_exists($file) {
		return file_exists(__ABSOLUTE_PATH__.$file);
	}

	/**
	* Retourne VRAI si le repertoire existe dans le répertoire du projet
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $file Chemin relatif d'un répertoire contenu dans le projet courant
	* @return boolean
	*/
	public static function is_dir($dir) {
		return is_dir(__ABSOLUTE_PATH__.$dir);
	}

	/**
	* Supprime le fichier dans le répertoire du projet
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param $file Chemin relatif d'un fichier contenu dans le projet courant
	* @return boolean
	*/
	public static function unlink($file) {
		return unlink(__ABSOLUTE_PATH__.$file);
	}

	/**
	* Retrouve le type d'un fichier a partir de son contenu TEXT (file_get_contents)
	* @author QJ <qjanon@absystech.fr>
	* @param $data Contenu TEXT du fichier
	* @return string extension
	*/
	public static function foundFileType($data) {
		if (preg_match("/^%PDF/",$data)) {
			return ".pdf";
		} else {
		   throw new errorATF("Extension de fichier inconnu. Enregistrement Impossible.");
		}
	}

	/**
	* Generation d'un mot aléatoire (utilisable pour générer un mot de passe par exemple)
	* Basé sur l'algorithme très simple utilisé dans le js de phpmyadmin
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param int $length La longueur du mot de passe désiré
	* @param string $alphabet la liste de l'alphabet à utiliser
	* @return string $passwd le mot de passe
	*/
	public static function generateRandWord($length=16,$alphabet="abcdefhjmnpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWYXZ"){
		 $passwd = '';
		 for ($i = 0; $i < $length ;$i++ ) {
			$passwd .= $alphabet[rand(0,strlen($alphabet)-1)];
		 }

		 return $passwd;
	}

	/**
	* Conversion heures en string sexadecimale formatée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $hours En attendant un chiffre arrondi à 2 décimales
	* @param bool $workDays Une journée=7h
	* @return string $hours
	*/
	public static function hoursToString($h,$workDays=true){
		 $m = round(60 * ( ($h*100) % 100 ) / 100);
		 if ($m == 0) {
			$m = "00";
		 } elseif ($m < 10) {
			$m = "0".$m;
		 }
		 $h = floor($h);
		 if ($workDays) {
			 if ($h>=7) { // Calcul des jours
			 	$j = floor($h/7);
				$h = $h % 7;
				 $return = $j."j ".$h."h".$m;
			 }
		 }
		return $return?$return:$h."h".$m;
	}

	/**
	* Analyse syntaxique du moteur de recherche pour transofmer en mots clés distincts
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $subject
	* @return array Tableau des mots clés trouvés
	* @todo Analyse syntaxique de la recherche demandée, on peut imaginer des mots clés comme google (Exemple : "etat:termine priorite:10")
	*/
	public static function searchEngineParser($subject) {
		$m = array();
		preg_match_all("/ ?\"(.[^\"]+)\" ?| ?(.[^\" ]+) ?/",$subject,$m); // Trouver les guillemets doubles, sinon les mots clés séparés par espaces
		for ($k=1;$k<=2;$k++) {
			foreach ($m[$k] as $i) {
				if ($i) {
					$keywords[]=$i;
				}
			}
		}
		if (!$keywords) {
			$keywords = array($subject);
		}
		return $keywords;
	}

	/**
	* Procédure de mise en valeur des termes recherchés
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $subject
	* @param string|array $findme
	* @param string $replacewith
	* @return string
	*/
	public static function searchHighlight($subject, $findme, $replacewith) {
		if (!$findme) return $subject;
		if (!is_array($findme)) {
			$findme = self::searchEngineParser($findme);
		}
		$findme = implode("|",$findme);
		return preg_replace("/".str_replace("/","\/",$findme)."/i", $replacewith, $subject);
	}

	/** EXPERIMENTAL : champs pour Grid ExtJS : destiné à un objet Store.fields
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $view
	* @param array $plusThoseFields Champs supplémentaires
	* @param classes $current_class Champs supplémentaires
	* @return string $a tableau encodé en JSON
	*/
	public static function getExtJSGridMappingFields($view,$plusThoseFields=NULL){
		foreach ($view["order"] as $i) {
			$a[] = array(
				'convert' => $i
				,'name' => util::extJSEscapeDot($i)
			);
			if (strpos($i,".id")>-1) {
				// On ajoute la foreignkey
				$a[] = array(
					'convert' => $i."_fk"
					,'name' => util::extJSEscapeDot($i."_fk")
				);
			}
		}
		if ($view["body"]) {
			foreach ($view["body"] as $i) {
				if (!in_array($i,$view["order"])) {
					$a[] = array(
						'convert' => $i
						,'name' => util::extJSEscapeDot($i)
					);
				}
			}
		}
		if ($plusThoseFields) {
			foreach ($plusThoseFields as $i) {
				$a[] = array(
					'convert' => $i
					,'name' => util::extJSEscapeDot($i)
				);
			}
		}

		return json_encode($a);
	}

	/** EXPERIMENTAL : colonnes pour Grid ExtJS : destiné à un objet GridPanel.columns
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $view
	* @param classes $current_class
	* @return string $a tableau encodé en JSON
	*/
	public static function getExtJSGridMappingColumns($view,$current_class){
//log::logger($current_class->table,ygautheron);
//log::logger($view,qjanon);
		foreach ($view["order"] as $k => $i) {
			if (in_array($i,$view["body"])) {
				continue; // Si flag BODY alors pas de colonne
			}
			$x = array(
				'header' => ATF::$usr->trans($i,$current_class->table)
				,'dataIndex' => util::extJSEscapeDot($i)
				,'sortable' => !$current_class->colonnes['fields_column'][$i]['nosort']
				,'hideable' => true
				,'resizable' => true
//				,'width' => 100
				,'align' => 'left'
			);
			if ($current_class) {

				$colonne = $current_class->getColonne($i);
				$f = explode('.',$i); // Séparation table/champ
				$f_certain = $f[1] ? $f[1] : $f[0];

				// Se base sur la classe du champ pour trouver la colonne
				if ($f[1]) {
					$class = ATF::getClass($f[0]);
				} else {
					$class = $current_class;
				}

				if (!$colonne && $f_certain!=$i && $class) {
					$colonne = $class->getColonne($f_certain);
				}
				if ($colonne && $class) {
					// Rendu spécial pour la cellule
					if(!$colonne["norender"]){
						$r = $colonne["renderer"];
						if (substr($f[1],0,3)=="id_") { // Si clé étrangère
							$r="foreignKey";
						}
						if (!$r && $colonne["xtype"]) { // Si xtype défini
							$r=$colonne["xtype"];
						}
						if (!$r && $colonne["type"]) { // Sinon on prend le type mysql défini
							$r=$colonne["type"];
						}

						if ($r) {
							if ($r==="foreignKey") {
								// Clé éntrangère
								$x["renderer"] = "%%ATF.render('".$r."','".($f[1] ? $f[0] : $class->table)."','".($f_certain)."','".$class->fk_from($i)."')%%";
							} else {
								// Champs normal avec renderer demandé
								$x["renderer"] = "%%ATF.render('".$r."','".($f[1] ? $f[0] : $class->table)."','".($f_certain)."')%%";
							}
						}
					}

					// Cacher le header
					if ($colonne["hideLabel"]) {
						unset($x["header"]);
					}

					// Editable dans le Grid
					if ($colonne["rowEditor"]) {
						$x["editor"] = "%%ATF.rowEditor('".$colonne["rowEditor"]."','".($f[1] ? $f[0] : $class->table)."','".($f_certain)."')%%";
					}

					// Largeur définie
					if ($colonne["width"]) {
						$x["width"] = $colonne["width"];
						// Ces colonnes ne peuvent pas être redimensionné car leur largeur a été défini dans le constructeur
						$x["fixed"] = true;
					}
					if($taille=$view["width"][$i]){
						$x["width"] = intval($taille);
					}

					//Alignement
					if ($colonne["align"]) {
						$x["align"] = $colonne["align"];
					}
				}
			}
			$a[] = $x;

			if (strpos($i,".id")>-1) {
				// On ajoute la foreignkey
				$a[] = array(
					'dataIndex' => util::extJSEscapeDot($i."_fk")
					,'hidden' => true
					,'hideable' => false
				);
			}
		}
		return self::removeJSONFunctionFlags(json_encode($a));
	}

	/** Permet d'échapper les clés de début et fin d'une spécification de fonction ou nom de fonction pour qu'elle na soit pas convertie en string en JSON "%%function () {} %%"
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $json
	* @return string
	*/
	public static function removeJSONFunctionFlags($json){
		return str_replace(array('"%%','%%"'),array('',''),$json);
	}

	/** EXPERIMENTAL : rendu du body, retourne les colonnes ayant l'attribut body à true
	* Les colonnes avec body à true ne doivent pas apparaitre comme colonne
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $view
	* @return string $a tableau encodé en JSON
	*/
	public static function getExtJSGridRowBody($fields_columns){
		foreach ($fields_columns["body"] as $k => $i) {
			if ($i!="body") {  // Exception du champ appelé "body"
				$a[]=util::extJSEscapeDot($i);
			}
		}
		return $a;
	}
	public static function extJSEscapeDot($s){
		return str_replace(".","__dot__",$s);
	}
	public static function extJSUnescapeDot($s){
		return str_replace("__dot__",".",$s);
	}

	/**
	* Formatte une date pour pouvoir être utilisée avec le format en mysql (Y-m-d)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string la date !
	*/
	public static function formatDate($date){
		return date('Y-m-d H:i:s',strtotime($date));
	}

 	/**
     * Url_exists ! Chopé sur php.net
     * @param string $url
	 * @author Jérémie Gwiazdowski <jgw@absystech.fr>
     * @return bool True si l'url existe
     * @throws error
     */
    public static function url_exists($url){
		$hdrs = @get_headers($url);
   		return !empty($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
	}

	/**
	* Donne le path vers un élément Statique (JS, CSS, .swf)
	* Cette méthode permet l'utilisation du serveur static.absystech.net en environnement de production ou tout autre serveur défini dans ATF::$staticserver
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $relativePath le path (images/icones/toto.png , js/toto.js, flash/toto.swf)
	* @param bool|string $forceStatic Permet de forcer l'utilisation du serveur static

	public function gurl($relativePath,$forceStatic=false){
		return $relativePath;

		if(__DEV__ && !$forceStatic){
			return $relativePath;
		}else{
			if(is_string($forceStatic)){
				return $forceStatic."/".$relativePath;
			}elseif(ATF::$staticserver){
				return ATF::$staticserver."/".$relativePath;
			}else{
				return "http://static.absystech.net:8080/".$relativePath;
			}
		}
	}
	*/

	/**
	* Renvoi un tableau contenant les 29 prochaines années
	* @author Q.JANON <qjanon@absystech.fr>
	* @param string Année de départ
    * @return array Conteneur de toutes les années
    * @example
	*	year(2005) retourne :
	*	$array(	2005=>2005
	*				[...]
	*				2035=>2035);
	* @version 1.0
	*/
	public static function year($start=false){
		if (!$start) $start = date("Y") ;
		for ($i=$start-1;$i<$start+29;$i++){
			$annee[$i] = $i;
		}
		return $annee;
	}

	/**
	*Fonction permettant de n'afficher que les Gpoint dans un rayon de X Km
	*Utiisation :
	*		{assign var=start value=$globals.classes.produit->LatLong($lat,$lng)}
	*		{assign var=end value=$globals.classes.produit->LatLong($lat2,$lng2)}
	*		{assign var=km value=$globals.classes.produit->distance($start,$end)}
	*La fonction LatLong utilise la fonction Lat2Rad poour convertir les valeurs
	*Renvoi une distance entre 2 Gpoint
	*@author Quentin JANON <qjanon@absystech.fr>
	*@copyright Copyright (c) 2007, AbsysTech
	*@param  array $p1  coordonnées WGS84 du point de départ
	*@param  array $p2  coordonnées WGS84 du point d'arrivé
	*@return  int Distance en KM.
	*/
	public static function GPSdistance($p1,$p2) {
		$R = 6371;
		$dLat  = $p2['lat'] - $p1['lat'];
		$dLong = $p2['lon'] - $p1['lon'];

		$a = sin($dLat/2) * sin($dLat/2) + cos($p1['lat']) * cos($p2['lat']) * sin($dLong/2) * sin($dLong/2);
		$c = 2 * atan2(sqrt($a), sqrt(1-$a));
		return $R * $c;
	}

	/**
	*Formatte un tableau avec les coordonnées en radian
	*@author Quentin JANON <qjanon@absystech.fr>
	*@copyright Copyright (c) 2007, AbsysTech
	*@param  int $degLat  Latitude
	*@param  int $degLong  Longitude
	*@return  array Tableau de coordonnées en radian !
	*/
	public static function GPSLatLong($degLat, $degLong) {
		$return['lat'] = $degLat * M_PI/180;
		$return['lon'] = $degLong * M_PI/180;
		return $return;
	}

	/**
	* Renvoie la valeur décimal d'une string
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $number
	* @return float $number
	*/
	public static function stringToNumber($number){
		$number=str_replace(" ","",$number);
		return floatval($number);

	}

	/**
	*/
	public static function is_array($var){
		return is_array($var);
	}

	/**
	* Renvoie vrai lorsque la date correspond au weekend ou à un jour férié
	* @author Quentin Janon <qjanon@absystech.fr>
	* @param : date date
	* @return boolean True si la date correspond à un weekend ou à un jour férié
	*/
	public static function testJour($date){
		$date = getdate(strtotime($date));
		//si on est samedi ou dimanche
		if($date['wday']==0 || $date['wday']==6)
			return true;
		else if($date['mday']==1 && $date['mon']==1) //Nouvel an
			return true;
		else if($date['mon']==5 &&($date['mday']==1 || $date['mday']==8)) //Fête de travail ou armistice
			return true;
		else if($date['mday']==14 && $date['mon']==7) //Fête nationnale
			return true;
		else if($date['mday']==15 && $date['mon']==8) //Assomption
			return true;
		else if($date['mon']==11 && ($date['mday']==1 || $date['mday']==11)) //Toussaint ou Armistice
			return true;
		else if($date['mday']==25 && $date['mon']==12) //Noel
			return true;
		else{
			$d_paques = easter_date($date['year']);
			$j_paques = date("d", $d_paques)+1;
			$m_paques = date("m", $d_paques);

			$d_ascension = mktime(date("H", $d_paques), date("i", $d_paques), date("s", $d_paques),
									date("m", $d_paques), date("d", $d_paques) + 39, date("Y", $d_paques));
			$j_ascension = date("d", $d_ascension);
			$m_ascension = date("m", $d_ascension);

			$d_pentecote = mktime(date("H", $d_ascension), date("i", $d_ascension), date("s", $d_ascension),
									 date("m", $d_ascension), date("d", $d_ascension) + 11, date("Y", $d_ascension));
			$j_pentecote = date("d", $d_pentecote);
			$m_pentecote = date("m", $d_pentecote);

			if($date['mday']==$j_paques && $date['mon']==$m_paques) //paques
				return true;
			else if($date['mday']==$j_ascension && $date['mon']==$m_ascension) //ascension
				return true;
			else if($date['mday']==$j_pentecote && $date['mon']==$m_pentecote) //pentecote
				return true;
			else
				return false; //ni weekend ni jour férié
		}
	}

	/**
	* Convertit un time retourné par mysql en secondes
	* Provient de php.net http://php.net/manual/en/book.datetime.php
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string tim un time mysql HH:MM:SS
	* @return int un nombre de secondes
	*/
	public static function time_to_sec($time){
		$hours = substr($time, 0, -6);
		$minutes = substr($time, -5, 2);
		$seconds = substr($time, -2);

		return $hours * 3600 + $minutes * 60 + $seconds;
	}

	/* Projection et conversion de coordonnées géographiques
	+<LAMB1> +title=Lambert I +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=49.500000000 +lon_0=0.000000000 +k_0=0.99987734 +lat_1=49.500000000 +x_0=600000.000 +y_0=200000.000 +units=m +no_defs <>
	+<LAMB1C> +title=Lambert I Carto +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=49.500000000 +lon_0=0.000000000 +k_0=0.99987734 +lat_1=49.500000000 +x_0=600000.000 +y_0=1200000.000 +units=m +no_defs <>
	+<LAMB2> +title=Lambert II +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=46.800000000 +lon_0=0.000000000 +k_0=0.99987742 +lat_1=46.800000000 +x_0=600000.000 +y_0=200000.000 +units=m +no_defs <>
	+<LAMB2C> +title=Lambert II Carto +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=46.800000000 +lon_0=0.000000000 +k_0=0.99987742 +lat_1=46.800000000 +x_0=600000.000 +y_0=2200000.000 +units=m +no_defs <>
	+<LAMB3> +title=Lambert III +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=44.100000000 +lon_0=0.000000000 +k_0=0.99987750 +lat_1=44.100000000 +x_0=600000.000 +y_0=200000.000 +units=m +no_defs <>
	+<LAMB3C> +title=Lambert III Carto +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=44.100000000 +lon_0=0.000000000 +k_0=0.99987750 +lat_1=44.100000000 +x_0=600000.000 +y_0=3200000.000 +units=m +no_defs <>
	+<LAMB4> +title=Lambert IV +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=42.165000000 +lon_0=0.000000000 +k_0=0.99994471 +lat_1=42.165000000 +x_0=234.358 +y_0=185861.369 +units=m +no_defs <>
	+<LAMB4C> +title=Lambert IV Carto +proj=lcc +nadgrids=ntf_r93.gsb,null +towgs84=-168.0000,-60.0000,320.0000 +a=6378249.2000 +rf=293.4660210000000 +pm=2.337229167 +lat_0=42.165000000 +lon_0=0.000000000 +k_0=0.99994471 +lat_1=42.165000000 +x_0=234.358 +y_0=4185861.369 +units=m +no_defs <>
	+<LAMB93> +title=Lambert 93 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=46.500000000 +lon_0=3.000000000 +lat_1=44.000000000 +lat_2=49.000000000 +x_0=700000.000 +y_0=6600000.000 +units=m +no_defs <>
	+<RGF93CC42> +title=Lambert conique conforme Zone 1 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=42.000000000 +lon_0=3.000000000 +lat_1=41.200000000 +lat_2=42.800000000 +x_0=1700000.000 +y_0=1200000.000 +units=m +no_defs <>
	+<RGF93CC43> +title=Lambert conique conforme Zone 2 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=43.000000000 +lon_0=3.000000000 +lat_1=42.200000000 +lat_2=43.800000000 +x_0=1700000.000 +y_0=2200000.000 +units=m +no_defs <>
	+<RGF93CC44> +title=Lambert conique conforme Zone 3 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=44.000000000 +lon_0=3.000000000 +lat_1=43.200000000 +lat_2=44.800000000 +x_0=1700000.000 +y_0=3200000.000 +units=m +no_defs <>
	+<RGF93CC45> +title=Lambert conique conforme Zone 4 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=45.000000000 +lon_0=3.000000000 +lat_1=44.200000000 +lat_2=45.800000000 +x_0=1700000.000 +y_0=4200000.000 +units=m +no_defs <>
	+<RGF93CC46> +title=Lambert conique conforme Zone 5 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=46.000000000 +lon_0=3.000000000 +lat_1=45.200000000 +lat_2=46.800000000 +x_0=1700000.000 +y_0=5200000.000 +units=m +no_defs <>
	+<RGF93CC47> +title=Lambert conique conforme Zone 6 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=47.000000000 +lon_0=3.000000000 +lat_1=46.200000000 +lat_2=47.800000000 +x_0=1700000.000 +y_0=6200000.000 +units=m +no_defs <>
	+<RGF93CC48> +title=Lambert conique conforme Zone 7 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=48.000000000 +lon_0=3.000000000 +lat_1=47.200000000 +lat_2=48.800000000 +x_0=1700000.000 +y_0=7200000.000 +units=m +no_defs <>
	+<RGF93CC49> +title=Lambert conique conforme Zone 8 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=49.000000000 +lon_0=3.000000000 +lat_1=48.200000000 +lat_2=49.800000000 +x_0=1700000.000 +y_0=8200000.000 +units=m +no_defs <>
	+<RGF93CC50> +title=Lambert conique conforme Zone 9 +proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=50.000000000 +lon_0=3.000000000 +lat_1=49.200000000 +lat_2=50.800000000 +x_0=1700000.000 +y_0=9200000.000 +units=m +no_defs <>
	*/
	/* gentoo : "emerge proj" */
	public static function L2toWGS84($x,$y,$projection="default") {
		$projections = array(
			"RGF93CC42" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=42.000000000 +lon_0=3.000000000 +lat_1=41.200000000 +lat_2=42.800000000 +x_0=1700000.000 +y_0=1200000.000 +units=m +no_defs"
			,"RGF93CC43" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=43.000000000 +lon_0=3.000000000 +lat_1=42.200000000 +lat_2=43.800000000 +x_0=1700000.000 +y_0=2200000.000 +units=m +no_defs"
			,"RGF93CC44" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=44.000000000 +lon_0=3.000000000 +lat_1=43.200000000 +lat_2=44.800000000 +x_0=1700000.000 +y_0=3200000.000 +units=m +no_defs"
			,"RGF93CC45" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=45.000000000 +lon_0=3.000000000 +lat_1=44.200000000 +lat_2=45.800000000 +x_0=1700000.000 +y_0=4200000.000 +units=m +no_defs"
			,"RGF93CC46" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=46.000000000 +lon_0=3.000000000 +lat_1=45.200000000 +lat_2=46.800000000 +x_0=1700000.000 +y_0=5200000.000 +units=m +no_defs"
			,"RGF93CC47" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=47.000000000 +lon_0=3.000000000 +lat_1=46.200000000 +lat_2=47.800000000 +x_0=1700000.000 +y_0=6200000.000 +units=m +no_defs"
			,"RGF93CC48" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=48.000000000 +lon_0=3.000000000 +lat_1=47.200000000 +lat_2=48.800000000 +x_0=1700000.000 +y_0=7200000.000 +units=m +no_defs"
			,"RGF93CC49" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=49.000000000 +lon_0=3.000000000 +lat_1=48.200000000 +lat_2=49.800000000 +x_0=1700000.000 +y_0=8200000.000 +units=m +no_defs"
			,"RGF93CC50" => "+proj=lcc +towgs84=0.0000,0.0000,0.0000 +a=6378137.0000 +rf=298.2572221010000 +lat_0=50.000000000 +lon_0=3.000000000 +lat_1=49.200000000 +lat_2=50.800000000 +x_0=1700000.000 +y_0=9200000.000 +units=m +no_defs"
			,"default" => "+proj=lcc +lat_0=46.8 +lat_1=45.898918 +lat_2=47.696014 +lon_0=2.3372291 +k_0=0.99987742 +x_0=600000 +y_0=2200000 +a=6378249.2 +b=6356515 +ellps=clrk80 +towgs84=-168,-60,320,0,0,0,0 +to +proj=latlong +datum=WGS84 +ellps=WGS84 +no_defs"
		);

		if ($projection = $projections[$projection]) {
			$return = `echo "$x $y" | cs2cs -f '%.10f' $projection -s`;
			$return = explode(" ",$return);
			return explode("\t",$return[0]);
		}
	}

	/**
	* Permet de récupérer le nombre de jour ouvré
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @link: http://www.phpcs.com/codes/CALCUL-SIMPLE-NOMBRE-JOURS-OUVRES-ENTRE-DEUX-DATES_47518.aspx
	* @param string $date_start : strtotime de la date de début
	* @param string $date_stop : strtotime de la date de fin
	* @return integer $nb_days_open : nombre de jour ouvré trouvé
	*/
	public static function get_nb_open_days($date_start, $date_stop) {
		$arr_bank_holidays = array(); // Tableau des jours feriés

		// On boucle dans le cas où l'année de départ serait différente de l'année d'arrivée
		$diff_year = date('Y', $date_stop) - date('Y', $date_start);
		for ($i = 0; $i <= $diff_year; $i++) {
			$year = (int)date('Y', $date_start) + $i;
			// Liste des jours feriés
			$arr_bank_holidays[] = '1_1_'.$year; // Jour de l'an
			$arr_bank_holidays[] = '1_5_'.$year; // Fete du travail
			$arr_bank_holidays[] = '8_5_'.$year; // Victoire 1945
			$arr_bank_holidays[] = '14_7_'.$year; // Fete nationale
			$arr_bank_holidays[] = '15_8_'.$year; // Assomption
			$arr_bank_holidays[] = '1_11_'.$year; // Toussaint
			$arr_bank_holidays[] = '11_11_'.$year; // Armistice 1918
			$arr_bank_holidays[] = '25_12_'.$year; // Noel

			// Récupération de paques. Permet ensuite d'obtenir le jour de l'ascension et celui de la pentecote
			$easter = easter_date($year);
			$arr_bank_holidays[] = date('j_n_'.$year, $easter + 86400); // Paques
			$arr_bank_holidays[] = date('j_n_'.$year, $easter + (86400*39)); // Ascension
			$arr_bank_holidays[] = date('j_n_'.$year, $easter + (86400*50)); // Pentecote
		}
		//print_r($arr_bank_holidays);
		$nb_days_open = 0;
		// Mettre <= si on souhaite prendre en compte le dernier jour dans le décompte
		while ($date_start <= $date_stop) {
			// Si le jour suivant n'est ni un dimanche (0) ou un samedi (6), ni un jour férié, on incrémente les jours ouvrés
			if (!in_array(date('w', $date_start), array(0, 6)) && !in_array(date('j_n_'.date('Y', $date_start), $date_start), $arr_bank_holidays)) {
				$nb_days_open++;
			}
			$date_start = mktime(date('H', $date_start), date('i', $date_start), date('s', $date_start), date('m', $date_start), date('d', $date_start) + 1, date('Y', $date_start));
		}
		return $nb_days_open;
	}

	/**
	* Analyse une string en loggant toutes les occurences et leur code ASCII
	* @author Quentin JANON <qjanon@absystech.fr>
	* @link: http://fr.php.net/manual/fr/function.count-chars.php
	* @param string $string chaîne en entrée
	* @param string $log nom du fichier ou logger les infos
	*/
	public static function analyseString($string,$log=NULL) {
		if (!$log) {
			$log = ATF::$usr->get('login');
		}
		log::logger("===========================================",$log);
		log::logger($string,$log);
		$c = count_chars($string, 1);
		log::logger($c,$log);
		foreach ($c as $i_ => $val) {
			log::logger("Il y a $val occurence(s) de ".$i_." = ".chr($i_)." dans la phrase.",$log);
		}
	}

	/**
    * Supprime les préfixes de tables sur les clés d'un tableau
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array
	* @return array
    */
	public static function removeTableInKeys($a) {
		foreach ($a as $ak => $ai) {
			foreach ($ai as $aak => $aai) {
				$b[$ak][substr($aak,strpos($aak,".")+1)]=$aai;
			}
		}
		return $b;
	}


	/**
    * Supprime tous les accents d'une stringe tremplace les espace par des '_'
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param str
	* @return str
    */
	public static function removeAccents($str, $whiteSpace=true, $charset='utf-8') {
	    $str = htmlentities($str, ENT_NOQUOTES, $charset);

	    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

		if ($whiteSpace) {
			return preg_replace('#[^a-zA-Z0-9\-\._]#', '_', $str); // Pour des noms de fichiers par exemple
		} else {
			return $str;
		}
	}

	/**
    * Permet de push dans un array
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array
    */
	public static function push(&$a,$push) {
		$a[]=$push;
	}

	/**
	* Nettoyage du format de sortie au cas ou HTML ou caractères bizarres
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* @return array
	*/
	public static function cleanForMobile($infos){
		foreach ($infos as $k => $i) {
			$infos[$k] = array_map("strip_tags",$infos[$k]);
			$infos[$k] = array_map("html_entity_decode",$infos[$k]);

			foreach ($i as $k_=>$i_) {
				if (!$infos[$k][$k_]) {
					$infos[$k][$k_]=""; // Ne pas avoir de valeur NULL, ca fait chier iOS
				}
			}
		}
		return $infos;
	}

	/**
	* Retourne un array à clé numérique en prenant les values ou key si de type string
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* @return array
	*/
	public static function keysOrValues($infos){
		foreach ($infos as $k => $i) {
			if (is_string($i)) {
				$a[]=$i;
			} else {
				$a[]=$k;
			}
		}
		return $a;
	}

  	/**
	* Transforme les noms des cellules pour simuler une provenance d'autre table à structure similaire (exemple commande_ligne=>facture_ligne
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $from
	* @param string $to
	* @return array
	*/
  	public static function finalAliasesTranslator(&$data,$from,$to) {
		foreach ($data["data"] as $kRow => $row) {
			foreach ($row as $kCol => $value) {
				$return[$kRow][str_replace($from,$to,$kCol)]=$value;
			}
		}
		$data["data"] = $return;
	}

  	/**
	* Tronque une string
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $string
	* @param int $length
	* @param string $sub
	* @return string
	*/
	public static function truncate($string, $length,$sub="..."){
		settype($string, 'string');
		settype($length, 'integer');
		if (strlen($string)<$length) {
			return $string;
		}
		for($a = 0; $a < $length && $a < strlen($string); $a++){
			$output .= $string[$a];
		}
		return $output.$sub;
	}

  	/**
	* Enlève les caractères spéciaux
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $string
	* @param int $length
	* @return string
	*/
	public static function mod_rewrite($string,$minLength=1,$lowercase=true) {
		$search  = utf8_decode("çñÄÂÀÁäâàáËÊÈÉéèëêÏÎÌÍïîìíÖÔÒÓöôòóÜÛÙÚüûùúµ@µ$£Ãš");
		$replace = "cnAAAAaaaaEEEEeeeeIIIIiiiiOOOOooooUUUUuuuuuauSLas";
		$string = utf8_encode(strtr(utf8_decode($string), $search, $replace));

		if ($array = str_split($string)) {
			foreach ($array as $s) {
				if (!preg_match("/[a-zA-Z0-9]/",$s)) {
					$string = str_replace($s,"-",$string);
				}
			}
		}
		do {
			$old_length = strlen($string);
			$string = str_replace("--","-",$string);
		} while ($old_length != strlen($string));

		if (substr($string,0,1)=="-") {
			$string = substr($string,1);
		}
		if (substr($string,-1)=="-") {
			$string = substr($string,0,strlen($string)-1);
		}
		if ($minLength>1) {
			$a = explode("-",$string);
			foreach ($a as $k => $w) {
				if (strlen($w)<$minLength) {
					unset($a[$k]);
				}
				$string = implode("-",$a);
			}
		}
		if ($lowercase) $string=strtolower($string);
		return $string;
	}

	/**
	* Récupère le type de fichier
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param string $target
	* @return string type fichier
	*/
	public static function detect_utf_encoding($target) {
		$text = file_get_contents($target);
		$first2 = substr($text, 0, 2);
		$first3 = substr($text, 0, 3);
		$first4 = substr($text, 0, 3);

//		$uTF32_BIG_ENDIAN_BOM = chr(0x00).chr(0x00).chr(0xFE).chr(0xFF);
//		$uTF32_LITTLE_ENDIAN_BOM = chr(0xFF).chr(0xFE).chr(0x00).chr(0x00);
		$uTF16_BIG_ENDIAN_BOM = chr(0xFE).chr(0xFF);
		$uTF16_LITTLE_ENDIAN_BOM = chr(0xFF).chr(0xFE);
		$uTF8_BOM = chr(0xEF).chr(0xBB).chr(0xBF);

		if($first3 == $uTF8_BOM){
			 return 'UTF-8';
//		}elseif($first4 == $uTF32_BIG_ENDIAN_BOM){
//			 return 'UTF-32BE';
//		}elseif($first4 == $uTF32_LITTLE_ENDIAN_BOM){
//			 return 'UTF-32LE';
		}elseif($first2 == $uTF16_BIG_ENDIAN_BOM){
			 return 'UTF-16BE';
		}elseif($first2 == $uTF16_LITTLE_ENDIAN_BOM){
			 return 'UTF-16LE';
		}else{
			 return false;
		}
	}

	/**
	* Récupère le type de fichier
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $target
	* @return string $source
	*/
	public static function transverseFile($target,$source="php://input") {
		$sp = fopen($source, "r");
		$f = "/tmp/".self::generateRandWord();
		$op = fopen($f, 'w');
		while (!feof($sp)) {
			$buffer = fread($sp, 512);  // use a buffer of 512 bytes
			fwrite($op, $buffer);
		}

		// append new data
		fwrite($op, $new_data);

		// close handles
		fclose($op);
		fclose($sp);

		// make temporary file the target
		return rename($f, $target);

	}

	/**
	* Créer un QRCode
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param int $path Le chemin de sauvegarde du QRcode généré
	* @param text $text Le texte du QRcode
	*/
	public function QRcode($path,$text) {
		include_once __ABSOLUTE_PATH__."libs/ATF/libs/phpqrcode/qrlib.php";
		util::mkdir(dirname($path));
		QRcode::png($text, $path, 'L', 4, 2);
	}

	/**
	* Retourne la différence entre deux dates
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param date $date1
	* @param date $date2
	* @return array Contient le nomre de jours, heure, minute et seconde.
	* @return string Contient la traduction de la différence entre les deux dates
	*/
	public function date_diff($date1, $date2,$trad=false) {
		$s = strtotime($date2)-strtotime($date1);
		$d = intval($s/86400);
		$s -= $d*86400;
		$h = intval($s/3600);
		$s -= $h*3600;
		$m = intval($s/60);
		$s -= $m*60;
		if ($trad) {
			$r = $d." ".ATF::$usr->trans("jours");
			if ($h) $r .= " ".$h." ".ATF::$usr->trans("heures").$m." ".ATF::$usr->trans("minutes");
			return $r;
		} else {
			return array("d"=>$d,"h"=>$h,"m"=>$m,"s"=>$s);
		}
	}

	/*
	* Convertit du HTML en texte brut
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param html $html
	* @return string $text
    */
	function toPlainText($html,$xhtml=false) {
		require_once(__ABSOLUTE_PATH__."libs/ATF/libs/ATF/html2text.php");
		return html2text($html,$xhtml);
	}


    /**
    * Convertit un nombre de bytes en B, KB, MB, GB, TB
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param int $bytes
    * @param int $precision
    * @return string Taille formatté
    */
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    function age($date) {
	  $dna = strtotime($date);
	  $now = time();

	  $age = date('Y',$now)-date('Y',$dna);
	  if(strcmp(date('md', $dna),date('md', $now))>0) $age--;

	  return $age;
	}


	function myStrtotime($date_string) {
		$date_string = strtolower($date_string);
		$pattern = array('janvier'=>'jan','février'=>'feb','mars'=>'march','avril'=>'apr','mai'=>'may','juin'=>'jun','juillet'=>'jul','août'=>'aug','septembre'=>'sep','octobre'=>'oct','novembre'=>'nov','décembre'=>'dec');

		return strtotime(strtr($date_string, $pattern));
	}

    /**
    * Renvoi l'extension selon le content type d'un fichier
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param int $ct Content Type (ex: application/msword)
    * @return string Extension avec son point (ex: .doc)
    */
   	public function  getExtensionByContentType($ct) {
		switch ($ct) {
			case "video/x-msvideo":
				return ".avi";
			break;
			case "image/bmp":
				return ".bmp";
			break;
			case "text/css":
				return ".css";
			break;
			case "application/octet-stream":
				return ".exe";
			break;
			case "text/html":
				return ".html";
			break;
			case "image/jpeg":
				return ".jpg";
			break;
			case "application/x-javascript":
				return ".js";
			break;
			case "audio/mpeg":
				return ".mp3";
			break;
			case "application/vnd.ms-powerpoint":
				return ".ppt";
			break;
			case "image/tiff":
				return ".tiff";
			break;
			case "application/vnd.ms-excel":
			case "application/vnd.ms-office":
				return ".xls";
			break;
			case "application/zip":
				return ".zip";
			break;
			case "application/msword":
				return ".doc";
			break;
			case "application/pdf":
				return ".pdf";
			break;
		}


	}


    /**
    * Permet de covnertir un nombre en heure-minute, format HH:MM par défaut.
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param int $time l'int a ceonvertir
    * @return string résultat formatté comme prévu par le format.
    */
    public function convertToHoursMinute($time, $format = '%02d:%02d') {
	    if ($time < 1) {
	        return;
	    }
	    $hours = floor($time / 60);
	    $minutes = ($time % 60);
	    return sprintf($format, $hours, $minutes);
	}
}