<?php
/**
* La classe d'analyse des goulots d'étrangelements
*
* @date 2008-12-30
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class analyzer {
	public $flags = array(); // Drapeaux spécifiant les étapes dans le code
	public $current_flag = NULL; // Dernier drapeau posé
	public $start_flag = "Analyzer";
//	
//	/**
//	* Constructeur par défaut
//	*/
//	public function __construct() {
//	}
	
	/**
	* Pose un premier drapeau
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function start() {
		$this->flag($this->start_flag,false);
	}
	
	/**
	* Pose un nouveau drapeau
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $flag Nom du flag, code
	* @param boolean $auto_end Terminer le chronomètre dès le prochain flag posé (TRUE par défaut)
	*/	
	public function flag($flag,$auto_end=true) {
		if (isset($this->current_flag) && $this->current_flag["auto_end"]===true) {
			// Si un drapeau existe déjà, on marque l'ancien drapeau comme ayant duré jusqu'à maintenant
			$this->end_($this->current_flag);
		}
		
		if (isset($this->flags[$flag]) && is_array($this->flags[$flag])) {
			// Le flag existe deja, le but doit être de compter a nouveau du temps pour ce flag (généralement pour des boucles ou récursivité...)
			$this->flags[$flag]["start"] = microtime(true);
			if (isset($this->flags[$flag]["occurence"])) {
				$this->flags[$flag]["occurence"]++;
			} else {
				$this->flags[$flag]["occurence"]=2;
			}
		} else {
			$this->flags[$flag] = array(
				"start" => microtime(true)
				,"total" => 0 // Temps total d'execution de ce flag
				,"auto_end" => $auto_end
				,"name" => $flag
			);
		}
		
		// Flag actuel
		$this->current_flag =& $this->flags[$flag];
	}
	
	/**
	* Termine la dernière étape
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/
	public function end_last() {
		if (is_array($this->current_flag)) {
			$this->end_($this->current_flag);
		}
	}
	
	/**
	* Pose un dernier drapeau ou termine une étape
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string Nom du flag à terminer, ni NULL alors on ajoute un flag de fin générique
	*/
	public function end($flag=NULL) {
		if ($flag===NULL) {
			// Fin standard
			if (isset($this->flags[$this->start_flag]["end"])) {
				// end() déjà appelé
				return;
			} else {
				ATF::$analyzer->end_last();
				$flag = $this->start_flag;
			}
		}
		// Fin d'un flag non terminé
		$this->end_($this->flags[$flag]);
		if (isset($this->current_flag) && $this->flags[$flag] === $this->current_flag) {
			unset($this->current_flag);	
		}
	}
	
	/**
	* Ajoute une info supplémentaire à un drapeau
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $flag Nom du flag à terminer, ni NULL alors on ajoute un flag de fin générique
	* @param string $info
	*/
	public function info($flag,$info) {
		if (isset($this->flags[$flag])) {
			$this->flags[$flag]["infos"][] = $info;
		}
	}
	
	/**
	* Défini le nombre d'occurences a utiliser poue le calcule du temps unitaire de traitement
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $flag Nom du flag à terminer, ni NULL alors on ajoute un flag de fin générique
	* @param string $nb	Nombre d'occurences
	*/
	public function setOccurence($flag,$nb) {
		if ($this->flags[$flag]) {
			$this->flags[$flag]["occurence"] = $nb;
		}
	}
	
	/**
	* Termine une étape
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param array Référence au flag à terminer
	*/
	public function end_(&$flag) {
//if  ($flag["name"]==="SQL") {
//log::logger($flag["start"],ygautheron,true);
//}
		$flag["end"] = microtime(true);
		$flag["total"] += $flag["end"] - $flag["start"];
//if  ($flag["name"]==="SQL") {
//log::logger("+".($flag["end"] - $flag["start"]),ygautheron,true);
//}
		if (isset($flag["occurence"]) && is_int($flag["occurence"]) || $flag["total"]<0.001) {
			if ($flag["occurence"]==0) {
				$flag["occurence"]=1;
			}
			$flag["average"] = (1000 * $flag["total"] / $flag["occurence"])."ms";
		}
	}
	
	/**
	* Récupère un formatage texte du parametre flags
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return string 
	*/	
	public function output() {
		/*ob_start();
		print_r($this->flags);
		$s = ob_get_contents();
		ob_clean();*/
		$s .= "Total = ".$this->total();

		//ksort($this->flags);
		foreach ($this->flags as $key => $item) {
			//if ($key!==$this->start_flag) {
				$padding++;
				if ($padding%2) { // Lisibilité
					$pad = " ";	
				} else {
					$pad = "_";	
				}
				$s .= "\n".str_pad($key,24, $pad)."|";
				$s .= str_pad(number_format($item["total"],3,"."," "),8, $pad, STR_PAD_LEFT)."s"."|";
				if (isset($item["occurence"])) {
					$s .= str_pad(number_format($item["occurence"],0,"."," "),6, $pad, STR_PAD_LEFT)."x"."|";
					$s .= str_pad(number_format($item["average"],3,"."," "),8, $pad, STR_PAD_LEFT)."ms"."|";
				}
				if (is_array($item["infos"])) {
					foreach ($item["infos"] as $info) {
						$s .= "\n     ".$info;
					}
				}
			//}
		}
		return $s;
	}
	
	/**
	* Affiche les infos des drapeaux
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	*/	
	public function display() {
		echo $this->output();
	}
	
	/**
	* Retourne le temps total analysé
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @return float
	*/	
	public function total() {
		$this->end();
		return $this->flags[$this->start_flag]["total"];
	}
	
	/**
	* Affiche les infos des drapeaux
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $recipient Destinataire(s)
	*/	
	public function mail($recipient) {
		$output = $this->output();
		$time = $this->total();
		mail($recipient,"[Bottleneck Analyzer] ".$time." s.",$output,"From: ATF 5 Bottleneck Analyzer <ygautheron@absystech.fr>");
	}
	
//	/**
//	* Destructeur par défaut
//	*/
//	public function __destruct() {
//	}
};
?>