<?
/** Classe personnel
* @package Optima MANALA
*/
class personnel extends classes_optima {
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		
		$this->colonnes["fields_column"] = array(
			'personnel.civilite'=>array("EnumTranslate"=>true)
			,'personnel.nom'
			,'personnel.prenom'
			,'personnel.ville'
			,'personnel.email'
			,'personnel.tel'
			,'personnel.date_ajout'
			,'detail'=>array("width"=>80,"nosort"=>true,"renderer"=>"detailRapide","custom"=>true)
			,'personnel.etat'=>array("width"=>50,"renderer"=>"etat")
			,'cv'=>array("width"=>50,"nosort"=>true,"type"=>"file","custom"=>true)
		);
		$this->colonnes['primary'] = array(
			"civilite"
			,"nom"
			,"prenom"
			,"etat"
			,'date_ajout'

		);	
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>4);

		// Adresse
		$this->colonnes['panel']['coordonnees'] = array(
			"adresse"
			,"cp_ville"=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"cp"
				,"ville"
			))
			,"adresse_2"
			,"id_pays"
			,"adresse_3"
			,"contact_direct"=>array("custom"=>true,'xtype'=>'compositefield','fields'=>array(
				"tel"
				,"email"
			)),
			'province',
			'nationalite'
		);
		$this->panels['coordonnees'] = array("visible"=>true,'nbCols'=>2);

		$this->colonnes['panel']["caracteristiques"] = array(
		 	'naissance'=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"date_naissance"
				,"lieu_naissance"
			)),
			'num_secu',
			'mensuration'=>array("custom"=>true,'null'=>true,'xtype'=>'compositefield','fields'=>array(
				"taille"
				,"mensuration_haut"
				,"mensuration_bas"
			)),
			'permis',
			'voiture',
			'anglais',
			'langues',
			'type_mission',
		);
		$this->panels['caracteristiques'] = array("visible"=>true,'nbCols'=>2);

		$this->colonnes['bloquees']['insert'] =array("latitude","longitude");
		
		$this->fieldstructure();

		$this->field_nom = "%civilite% %prenom% %nom%";

		$this->files["cv"] = array("obligatoire"=>true);
		$this->files["photo_identite"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);
		$this->files["photo_pleine"] = array("type"=>"png","convert_from"=>array("jpg","png","gif"),"select"=>true);

		$this->onglets = array('mission_ligne');

		$this->addPrivilege('generateFicheCasting');
		$this->addPrivilege('detailPersonnel');

	}

	public function generateFicheCasting($infos) {

		$fn = "ficheCasting";
		$fp = ATF::personnel()->filepath(ATF::$usr->getId(),$fn,true);
       	ATF::pdf()->generic("ficheCasting",$infos,$fp);     

       	if (!file_exists($fp)) {
       		throw new errorATF("Fichier impossible a générer"); 
       	}
       	$url = __ABSOLUTE_WEB_PATH__."personnel-select-ficheCasting-".ATF::user()->cryptId(ATF::$usr->getId())."-pdf.temp";

       	return $url;
	}

	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);

		if ($infos['photo_identite']) {
			$pi = $infos['photo_identite'];
			unset($infos['photo_identite']);
		}
		if ($infos['photo_pleine']) {
			$pp = $infos['photo_pleine'];
			unset($infos['photo_pleine']);
		}
		if ($infos['cv']) {
			$cv = $infos['cv'];
			unset($infos['cv']);
		}

		if (substr($infos['num_secu'],0,1)=="2") {
			$infos['civilite'] = "mme";
		}

		$last_id = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		$id = $this->decryptId($last_id);

		// Ce sont des conditions qui ne sont vérifié que lors de l'execution du script d'import des fiches
		if ($pi) {
			$fp = ATF::personnel()->filepath($id,"photo_identite");
			file_put_contents($fp,file_get_contents($pi));
		}
		if ($pp) {
			$fp = ATF::personnel()->filepath($id,"photo_pleine");
			file_put_contents($fp,file_get_contents($pp));
		}
		if ($cv) {
			// Extension de base
	        $ext = pathinfo($cv, PATHINFO_EXTENSION);
	        // Fichier temporaire qu'on zipera
			$tmp = "/home/qjanon/".rand(0,10000);
			// On créer le fichier temporaire
			file_put_contents($tmp,file_get_contents($cv));
			// Param du store + envoi dans Optima
			$d = array('tmp_name'=>$tmp,'name'=>"CV-".$infos['prenom']."-".$infos['nom'].".".$ext);
			$this->store(ATF::_s(),$id,"cv",$d);
			// Suppression du document temporaire
			unlink($tmp);
		}

		return $last_id;
	}

	/** 
	* Génére la latitude et longitude d'une donnée d'un module
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $table : nom de la table concernée
	* @param integer $id : id de la table dont on recherche les coordonnées
	*/
	public function genLatLong($id){
		$el = $this->select($id); 			
		if ($el['latitude']!="-1.00") {
			if ($el['ville']){
				$adress = "";
				if ($el['adresse']) $adress = $el['adresse'].", ";
				//if ($el['adresse_2']) $adress .= $el['adresse_2'];
				//if ($el['adresse_3']) $adress .= $el['adresse_3'].", ";
				if ($el['cp']) $adress .= $el['cp']." ";
				$adress .= $el['ville'].", ";
				$adress .= $el['id_pays'];
			    ATF::curl()->curlInit("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($adress)."&sensor=false");
                ATF::curl()->curlSetopt(CURLOPT_RETURNTRANSFER, true);
                $result = ATF::curl()->curlExec();
                ATF::curl()->curlClose();
				//$result=file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($el['adresse']).",".urlencode($el['ville']).",".$el['id_pays']."&sensor=false");
				$coor=json_decode($result);								
				if($coor->status == "OK"){					
					$this->update(array("id_".$this->table=>$id,"latitude"=>$coor->results[0]->geometry->location->lat,"longitude"=>$coor->results[0]->geometry->location->lng));
					return array('lat'=>$coor->results[0]->geometry->location->lat,'long'=>$coor->results[0]->geometry->location->lng);
				}
			}
			$this->update(array("id_".$this->table=>$id,"latitude"=>"-1.00","longitude"=>"-1.00"));
		}
		return false;
	}
	/* Autocomplete sur les personnes
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocomplete($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q->addOrder("personnel.nom","asc");

		if (!$infos["query"]) {
			$infos["query"] = "%";
		}

			// On ne doit pas écraser une limite particulière demandée...
			if (!$this->q->getLimit()) {
				$this->q->setLimit(false);
			}

			$this->q
				->setCount()
				->setStrict(1)
				->setDimension('row_arro')
				->setSearch(stripslashes(urldecode($infos["query"])))
				->addField(array("CONCAT(".$this->table.".id_".$this->table.")"=>array("alias"=>"id","nosearch"=>true))); // Clé primaire brute

			/* On défini les champs sur lesquels effectuer la recherche */
			if ($this->autocomplete["view"]) {
				$this->q->addField($this->autocomplete["view"]);
			} else {
				$this->q->addField($this->table.".id_".$this->table);
			}
			if (count($this->autocomplete["view"])<2) {
				$this->q->addField('""',"detail");
			}

			// Clée étrangère
			if($infos["condition_field"] && $infos["condition_value"]){

				/* Lorsqu'on a une string pour condition_field */
				if (!is_array($infos["condition_field"])) {
					$infos["condition_field"] = array($infos["condition_field"]);
					$infos["condition_value"] = array($infos["condition_value"]);
				}

				foreach ($infos["condition_value"] as $k => $v) {
					$this->q->addCondition($infos["condition_field"][$k],$this->decryptId($infos["condition_value"][$k]));
				}
			}

			// Ordre particulier
			if($infos["order_field"] && $infos["order_sens"]){
				$this->q->addOrder($infos["order_field"],$infos["order_sens"]);
			}
			if ($result = $this->select_data()) {
				// On met en valeur la chaîne recherchée dans les réponses
				$replacement = ATF::$html->fetch("search_replacement.tpl.htm","sr");
				foreach ($result["data"] as $k => $i) {
					foreach ($result["data"][$k] as $k_ => $i_) { // Mettre en valeur
						$result["data"][$k]["raw_".$k_] = $i_;
						if ($k_>0 || !is_numeric($i_)) {
							$result["data"][$k][$k_] = util::searchHighlight($i_, $infos["query"], $replacement);
						} else {
							$result["data"][$k][$k_] = classes::cryptId($i_);
						}
					}
				}
			}
			ATF::$json->add("totalCount",$result["count"]);
		//}
		ATF::$cr->rm("top");
		return $result["data"];
	}
	public function detailPersonnel($infos,$reset=true) {
		if ($reset) {
			$this->q->reset();
		}
		$this->q->addField("personnel.mensuration_haut")
				->addField('personnel.mensuration_bas')
				->addField('personnel.taille')

				->where("personnel.id_personnel",$this->decryptId($infos['id']))
				->setDimension('row');
		$data = $this->select_all();
		return $data;
	}
};
?>