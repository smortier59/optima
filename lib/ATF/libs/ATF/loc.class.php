<?php
/**
* La classe de localisation linguistique permet d'appeler une traduction contextuelle
*
* @date 2008-02-01
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class loc {
	/* Méthode de localisation textuelle */
	public static function ation($word,$prefix=NULL,$suffix=NULL,$strict=false,$id_language="fr",$suffixInPrefix=NULL,$force_codename=false) {
		if (!$id_language) {
			$id_language="fr";
		}

//		if (ATF::$project && is_dir(__INCLUDE_PATH__."language/".ATF::$project) && ATF::$project!=ATF::$codename) {
//			$language_path = __INCLUDE_PATH__."language/".ATF::$project."/";
//		} else
//		if (ATF::$codename && is_dir(__INCLUDE_PATH__."language/".ATF::$codename)) {
//			$language_path = __INCLUDE_PATH__."language/".ATF::$codename."/";
//		} else {
			$language_path = __INCLUDE_PATH__."language/";
//		}
		if(!is_array($GLOBALS["language"][$id_language])){	
			$GLOBALS["language"][$id_language]=array();
		}
		if (empty($GLOBALS["language"][$id_language])) { // Lecture du fichier de langue nécessaire à cette traduction
			if (file_exists($language_path.$id_language.".inc.php")) {
				include_once $language_path.$id_language.".inc.php";
			}
            $codename = ATF::$codenameForTraduction?ATF::$codenameForTraduction:ATF::$codename;

			if ($codename && file_exists($language_path.$codename."/".$id_language.".inc.php")) {
				include_once $language_path.$codename."/".$id_language.".inc.php";
			}
		}
//log::logger($GLOBALS["language"][$id_language]["id_language"]." loc::ation(".$word.",".$prefix.",".$suffix.",".$strict.",".$id_language.",".$suffixInPrefix.") ".count($GLOBALS["language"][$id_language]),ygautheron);		
		/* Champ prefixé par la table (ex: societe.id_famille) */
		if (is_string($word) && strpos($word,".")!==false && !preg_match("/^[0-9]*\.[0-9]*$/",$word)  && preg_match("/^[a-zA-Z0-9_]*\.[a-zA-Z0-9_]*$/",$word) ) {
			$explode_word = explode(".",$word);
			if (count($explode_word)==2) {
				$prefix = array_shift($explode_word);
				$word = array_shift($explode_word);
			}
		}
		
		if (is_array($word)) {
			foreach ($word as $key => $item) {
				$w[$key] = self::ation($item,$prefix,$suffix,$strict,$id_language);
				//si le mot n'est pas traduit, on vérifie si la donnée est dans la base sinon on l'insert automatiquement
				if (ATF::$autoAddNewTranslations) {
					ATF::localisation_traduction()->insertDefaultTrans($w[$key],$item,$prefix,$suffix,$strict,$id_language);
				}
			}

			return $w;
		}
		
		if ($prefix && $suffix && $GLOBALS["language"][$id_language][$prefix."_".$word."_".$suffix]) {
			return stripslashes($GLOBALS["language"][$id_language][$prefix."_".$word."_".$suffix]);
		}
		if ($prefix && $GLOBALS["language"][$id_language][$prefix."_".$word] && (!$suffix || !$strict)) {
			return stripslashes($GLOBALS["language"][$id_language][$prefix."_".$word]);
		}
		if($GLOBALS["language"][$id_language][$word] && (!$suffix && !$prefix || !$strict)) {
			return stripslashes($GLOBALS["language"][$id_language][$word]);
		}
//		if($GLOBALS["language"][$id_language]["module_".$word] && (!$suffix && !$prefix || !$strict)) {
//			return stripslashes($GLOBALS["language"][$id_language]["module_".$word]);
//		}
		
		/* Si c'est strict on a rien trouvé, on ne retourne RIEN */
		if ($strict) {
			// Avec un suffixe dans le préfixe, on cible les valeurs des ENUM ou SET qui ont deux préfixe : la table + le nom du champ
			if ($suffixInPrefix) {
				return self::ation($word,$prefix."_".$suffixInPrefix,$suffix,$strict,$id_language);
			} elseif ($id_language!=ATF::$default_language) {
				/* Si non trouvé dans cette langue, on retourne en français */
				return self::ation($word,$prefix,$suffix,$strict,ATF::$default_language,$suffixInPrefix);
			} else {
				return;
			}
		} elseif ($id_language!=ATF::$default_language) {
			/* Si non trouvé dans cette langue, on retourne en français */
			return self::ation($word,$prefix,$suffix,$strict,ATF::$default_language,$suffixInPrefix);
		}
		
		return $word;		
	}
	
	/**
	* Remplace les balises entre accolades d'une traduction par les équivalents passés en paramètre $remp
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $string
	* @param array $remp
	* @return string
	*/	
	static function mt($string,$remp) {
		$pattern = '/\{(.[^\}]*)\}/';
		$matches = array();
		preg_match_all($pattern, $string, $matches);
		
		foreach ($matches[0] as $k => $i) {
			$string = str_replace($i,$remp[$matches[1][$k]],$string);
		}
		return $string;
	} 
	
	/**
	* Retourne VRAI si le fichier de langue existe
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $id_language
	* @return boolean
	*/	
	static function exists($id_language) {
		$language_path = __INCLUDE_PATH__."language/";
		return file_exists($language_path.$id_language.".inc.php") 
			|| ATF::$codename && file_exists($language_path.ATF::$codename."/".$id_language.".inc.php");
	} 
};
?>