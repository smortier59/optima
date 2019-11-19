<?
/**
* Gestion du pointage
* @package Optima
*/
require_once dirname(__FILE__)."/../pointage.class.php";
/**
 * @codeCoverageIgnore
 */
class pointage_absystech extends pointage {
	/**
	* Contructeur
	*/
	public function __construct() {
		parent::__construct();

		$this->type = array('production'=>'Production', 'rd'=>'Recherche & Developpement');
		$this->no_update = true;
		$this->no_insert = true;
		ATF::tracabilite()->no_trace[$this->table]=1;

		$this->addPrivilege("pointages");
		$this->addPrivilege("getCategorieScheduler");
	}

	/** EXPERIMENTAL : Retourne des infos pour le scheduler
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $infos : Arguments postés en ajax
	*/
	public function pointages(&$infos){
		// On récupère tous le pointage de l'utilisateur
		$this->q->reset()->where('id_user',ATF::$usr->getId())->addOrder("date","desc");
		$sa = $this->sa();
		foreach ($sa as $k=>$i) {
			if (!$i['id_gep_projet']) continue;
			$startDate = $i['date']." "+$i['temps'];
			$endDate = $i['date']." 18:00";
			$time = $this->selectHoraire($i['date'],$i['sujet'],ATF::$usr->getId());

			$return[] = array(
				"Group"=>"default"
				,"Id"=>"e".$k
				,"ResourceId"=>$i['id_gep_projet']
				,"StartDate"=>$startDate
				,"EndDate"=>$endDate
				,"Time"=>$time['temps']
			);
		}

		ATF::$json->add('totalCount',count($return));
		return $return;
	}

	/** EXPERIMENTAL : Retourne des infos pour le scheduler
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $infos : Arguments postés en ajax
	*/
	public function getCategorieScheduler($infos){
		$this->q->reset()
					->addField("id_gep_projet")
					->addField("type")
					->setDistinct()
					->addGroup('id_gep_projet')
					->where('id_user',ATF::$usr->getId());
		foreach ($this->sa() as $k=>$i) {
			if (!$i['id_gep_projet']) continue;
			$projet = ATF::gep_projet()->nom($i['id_gep_projet']);
			$projetInfos = ATF::gep_projet()->select($i['id_gep_projet']);

			/* rangement par projet
			if (strstr($projet,"Optima")) {
				$cat = "Optima";
			} elseif (strstr($projet,"Nebula")) {
				$cat = "Nebula";
			} elseif (strstr($projet,"Ginger")) {
				$cat = "Ginger";
			} else {
				$cat = $projet;
			}*/

			/* Rangement par société */
			$cat = ATF::societe()->nom($projetInfos['id_societe']);

			/* Rangement par type
			$cat = $i['type'];*/


			$return[] = array(
				"Id"=>$i['id_gep_projet']
				,"Name"=>$projet
				,"Type"=>$i['type']
				,"Category"=>$cat
			);

		}
		ATF::$json->add('totalCount',count($return));

		return $return;
	}
	/**
	* Compte le nombre d'heures passees sur un mois et sur un projet
	* @param : date date
	* @param : int id_user
	* @return float le nombre d'heures
	*/
	public function totalHeure($date,$id_user,$type=NULL,$sujet=NULL){
		$this->q->reset()
			->addField(array(
			"REPLACE(
				SUBSTRING_INDEX(
					SEC_TO_TIME(
						SUM(
							TIME_TO_SEC(temps)
						)
					)
				 ,':', 2 )
			,':','h')"
			))
			->addCondition("`date`",$date."%","AND",false,"LIKE")
			->addCondition("id_user",$this->decryptId($id_user),"AND");

		if ($type) {
			$this->q->addCondition("type",$type,"AND");
		}
		if ($sujet) {
			$this->q->addCondition("sujet",$sujet,"AND");
		}

		$this->q->setDimension("cell");

		return $this->sa();
	}

	/**
	* Donne les requêtes de la journée par utilisateur AVEC LES RESULTATS HORAIRES DU POINTAGE (et non de la hotline)
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $date La date au format Y-m-d
	* @param int $id_user l'utilisateur désiré !!
	* @return array Un joli tableau contenant le num de la requête, le nom de L'entité, le résumé et le temps consacré
	*/
	public function getRequetesByDays($date,$id_user){
		$this->q->reset()
			->addField("pointage.id_hotline")
			->addField("pointage.id_societe")
			->addField("societe")
			->addField("hotline")
			->addField(
			"REPLACE(
				SUBSTRING_INDEX(
					SEC_TO_TIME(
						SUM(
							TIME_TO_SEC(pointage.temps)
						)
					)
				 ,':', 2 )
			,':','h')"
			,"temps_calcule")
			->addCondition("pointage.date",$date)
			->addCondition("pointage.id_user",$this->decryptId($id_user),"AND")
			->addJointure("pointage","id_hotline","hotline","id_hotline")
			->addJointure("pointage","id_societe","societe","id_societe")
			->addGroup("pointage.id_hotline")
			->setStrict()
			->setToString();

		$subQuery=$this->sa();
		$this->q->reset()
            ->setSubQuery($subQuery,"a")
			->addCondition("a.temps_calcule","00h00","AND","false","!=");

		return ATF::db($this->db)->select_all($this);
	}


	/**
	* Selection des projets pour un user et une date donnée
	* @author : Maïté Glinkowski
	* @param : string date
	* @param : int id_user
	* @param : string type
	* @return array
	*/
	public function selectSujet($date,$id_user,$type=NULL) {
		$this->q->reset()
			->addField('sujet,id_gep_projet,type')
			->addCondition("pointage.id_user",$this->decryptId($id_user))
			->addCondition("pointage.date",$date."%","AND",false,"LIKE")
			->addGroup("sujet")
			->addOrder("type,id_gep_projet,sujet");

		if ($type) {
			$this->q
				->addCondition("type",$type,"AND")
				->setDistinct();

		} else {
			$this->q

				// Désativer les hotline (temporaire)
				->addCondition("type","hotline","AND",false,"!=")

			->addCondition("type","conge_sans_solde","AND",false,"!=")
			->addCondition("type","arret","AND",false,"!=")
			->addCondition("type","conge_annuel","AND",false,"!=")
			->addCondition("type","conge_legaux","AND",false,"!=");

		}

		return $this->sa();
	}

	/**
	* Sélection du temps et de l'id du pointage pour un sujet, une date et un id_user donnés
	* @param : string date
	* @param : string sujet
	* @param : int id_user
	* @return array
	*/
	public function selectHoraire($date,$sujet,$id_user){
		$this->q->reset()
			->addField('id_pointage')
			->addField(array("SUBSTR(`temps`,'1','5')"=>array("alias"=>"temps")))
			->addCondition("pointage.date",$date)
			->addCondition("pointage.sujet",$sujet)
			->addCondition("pointage.id_user",$this->decryptId($id_user))
			->setStrict()
			->setDimension("row");

		return $this->sa();
	}

	/**
	* Supprime toutes les entrées dans la base où = $sujet & $date & $id_user
	* @todo querier de suppression
	* @param : string sujet
	* @param : date date
	* @param : int id_user
	* @return boolean
	*/
	public function deleteHoraire($sujet,$date,$id_user) {
		$query = "DELETE FROM `pointage`
					WHERE `date` LIKE '".$date."%'
					AND `sujet`='".addslashes($sujet)."'
					AND `id_user`=".$this->decryptId($id_user)."
					AND type!='conge'";
		return ATF::db()->query($query);
	}

	/**
	* Ajoute un nouveau pointage
	* @param int $id_hotline L'identifiant de la hotline
	* @param int $id_hotline_interaction L'identifiant de l'interaction (utilisé pour une meilleure traçabilité du pointage !)
	* @param int $id_user L'utilisateur
	* @param int $date La date
	* @param string $tps le temps (H:i exemple 1:30 correspond à une heure et trente minutes)
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	*/
	public function addPointage($id_hotline,$id_hotline_interaction,$id_user,$date,$temps){
		$dateYmd = substr($date,0,10);
		//Pointage actuel ?
		$pointage=$this->select_special("id_hotline_interaction",$id_hotline_interaction);
		if(is_array($pointage) && isset($pointage[0]["id_pointage"])){
			$update = array(
				"id_pointage"=>$pointage[0]["id_pointage"]
				,"id_user"=>$id_user
				,"date"=>$dateYmd
				,"temps"=>$temps
			);
			parent::update($update);
		}else{
			$this->insert(
				"hotline"
				,$id_hotline
				,$id_hotline_interaction
				,$id_user
				,$dateYmd
				,$temps
			);
		}
	}

	/**
	* Formatage des données pour le insert de classes
	* @author : Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @param string $choix gep, cours, personnalisé ou hotline
	* @param int $id_hotline L'identifiant de la hotline
	* @param int $id_hotline_interaction L'identifiant de l'interaction (utilisé pour une meilleure traçabilité du pointage !)
	* @param int $id_user L'utilisateur
	* @param int $date La date
	* @param string $tps le temps (H:i exemple 1:30 correspond à une heure et trente minutes)
	* @param string $personnalise Le nom du sujet personnalise
	* @param string $type Un type de pointage person
	* @param : array infos
	*/
	public function insert($choix,$id_hotline,$id_hotline_interaction,$id_user,$date,$tps,$personnalise=NULL,$type=NULL){
		$insert=array();
		switch($choix){
			case "hotline":
				if(is_numeric($id_hotline)){
					$hotline = ATF::hotline()->select($id_hotline);
				}
				$insert = array(
					"sujet"=>"Requete ".$hotline['id_hotline']
					,"id_societe"=>$hotline['id_societe']
					,"type"=>'hotline'
					,"id_hotline"=>$hotline['id_hotline']
					,"id_gep_projet"=>$hotline['id_gep_projet']
					,"id_hotline_interaction"=>$id_hotline_interaction
				);
			break;
			default:
				$insert['sujet'] = $personnalise;
				$insert['type'] = "production";
			break;
		}
		//Type personnalisé
		if ($type) {
			$insert['type'] = $type;
		}
		//Utilisateur
		$insert["id_user"] = $id_user;
		//Date du jour (Y-m-d)
		$insert['date'] = $date;
		//Temps
		$insert['temps'] = $tps;

		return parent::insert($insert);
	}

	/**
	* Supprime toutes les entrées d'un projet dans la table de pointage pour un user et une date donnés
	* @atuhor : Maïté Glinkowski
	* @param : array infos
	* @return boolean
	*/
	public function delete($infos,&$s,$files=NULL,&$cadre_refreshed){
		if(!$this->isMySheet($infos["id_user"])) return false;

		ATF::db($this->db)->begin_transaction();
		$id = $this->decryptId($infos["id_pointage"]);

		$this->q->reset()
			->addField("date")
			->addField("sujet")
			->addField("id_user")
			->addCondition("id_pointage",$id);
		$res=$this->sa();
		if(!$res[0]) throw new errorATF("pointage_not_found");

		$res=$res[0];
		$id_user = $res["id_user"];
		$date = explode('-',$res["date"]);
		$mois = $date[1];
		$annee = $date[0];
		$date = $annee.'-'.$mois;
		$sujet = $res["sujet"];

		$this->deleteHoraire($sujet,$date,$id_user);
		ATF::db()->commit_transaction();
		ATF::$cr->add("main","pointage-select.tpl.htm",array('id_user'=>$id_user,'id_pointage'=>$id));
		return true;

	}

	/**
	* Mise à jour le temps passé pour une journée sur un pointage
	* @author : Maïté Glinkowski
	* @param : array $post : informations passées en post
	* @return boolean
	*/
	public function maj($post,&$s,$files=NULL,&$cadre_refreshed){
		ATF::$cr->rm("top");//Optimisation
		if(!$this->isMySheet($post["id_user"])) return false;

		$update['pointage'] = $post;
		return $this->update($update,$s,$files);

	}

};

class pointage_att extends pointage_absystech {
};
class pointage_demo extends pointage_absystech { };
class pointage_atoutcoms extends pointage_absystech { };

?>