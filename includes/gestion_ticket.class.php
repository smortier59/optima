<?
/**
* Classe gestion_ticket
* Cet objet permet de gérer les tickets sur les entités
* @package Optima
*/
class gestion_ticket extends classes_optima {
	/**
	* Constructeur par défaut
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;

		/*-----------Colonnes Select all par défaut------------------------------------------------*/
		$this->colonnes['fields_column'] = array(
			'gestion_ticket.operation'=>array("width"=>50,"align"=>"center")
			,'gestion_ticket.date'=>array("width"=>100,"align"=>"center")
			,'gestion_ticket.type'=>array("width"=>100,"align"=>"center")
			,'gestion_ticket.libelle'
			,'gestion_ticket.nbre_tickets'=>array("width"=>50,"align"=>"center")
			,'gestion_ticket.solde'=>array("width"=>50,"align"=>"center")
			,'gestion_ticket.id_hotline'
			,'gestion_ticket.id_facture'
		);

		//Colonnes principales
		$this->colonnes['primary'] = array(
											"date"
											,"type"
											,"nbre_tickets"
											,"id_societe"
											,"id_hotline"
										);

		$this->fieldstructure();
		$this->no_delete = $this->no_update  = true;
		$this->field_nom = " %gestion_ticket.type% - %gestion_ticket.date%";
	}

	/**
    * Méthode d'ajout des tickets !
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param array $infos $infos['id_societe'] et $infos['credits']
	* @param array $s
	* @param file $files
	* @param array $cadre_refreshed
    * @return boolean true
    */
	public function add_ticket($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		//Test du paramètre crédits
		if(!is_numeric($infos["credits"])){
			throw new errorATF(ATF::$usr->trans("gestion_ticket_not_numeric",$this->table));
		}
		if($infos["credits"]<=0){
			throw new errorATF(ATF::$usr->trans("gestion_ticket_not_positif",$this->table));
		}

		// Test de la présence de la facture ou du libellé
		if(!$infos["id_facture"] && !$infos["libelle"]){
			throw new errorATF(ATF::$usr->trans("libelle_or_id_facture",$this->table));
		}

		//Recherche du crédit précédent
		$credits=ATF::societe()->getSolde($infos['id_societe']);

		ATF::db($this->db)->begin_transaction();

		//Insertion d'un nouveau ticket
		$nouveau_solde=$credits+$infos["credits"];
		$infos_ticket["operation"]=$this->getLastOp($infos['id_societe'])+1;
		$operation=$this->getLastOp($infos['id_societe'])+1;
		$id_gestion_ticket=$this->insert(array("gestion_ticket"=>
										array(
											"id_societe"=>$infos["id_societe"]
											,"date"=>date("Y-m-d H:i:s")
											,"nbre_tickets"=>$infos["credits"]
											,"type"=>"ajout"
											,"solde"=>$nouveau_solde
											,"id_facture"=>$infos["id_facture"]
											,"libelle"=>$infos["libelle"]
											,"operation"=>$operation
											))
										,$s);

		ATF::db()->commit_transaction();

		// Cadre refresh
		if(is_array($cadre_refreshed)){
			ATF::societe()->redirection("select",$infos["id_societe"]);
		}

		//Notice
		ATF::$msg->addNotice(ATF::$usr->trans("gestion_ticket_add",$this->table));

		return $id_gestion_ticket;
	}

    /**
    * Débite les tickets en fonction des requêtes hotline ! Chaque débit correspond à une requête hotline
    * @author QJ <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
    * @param int $id_societe
    * @param int $id_hotline
    * @param boolean $envoi_mail
	* @param array $s
    * @return float le nombre de tickets
    */
	public function remove_ticket($infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {
		// Recherche du crédit précédent
		$credits=ATF::societe()->getSolde($infos['id_societe']);

		// Recherche du nombre d'heures facturés (en base 10)
		$duree=ATF::hotline()->getCreditUtilises($infos['id_hotline']);

		// Transactionnel
		ATF::db($this->db)->begin_transaction();

		// historique du retrait de ticket pr facturation
		$infos_ticket['id_societe']=$infos['id_societe'];
		$infos_ticket['type']='retrait';
		$infos_ticket['date']=date('Y-m-d H:i:s');
		$infos_ticket['id_hotline']=$infos['id_hotline'];
		$infos_ticket['nbre_tickets']="-".$duree;
		$infos_ticket['solde']=$credits-$duree;
		$infos_ticket["operation"]=$this->getLastOp($infos['id_societe'])+1;
		$this->insert($infos_ticket,$s,NULL,$cadre_refreshed);

		// Commit !
		ATF::db()->commit_transaction();

		return $duree;
	}

//	/**
//	* Traitement du solde de chaque enregistrement de gestion ticket
//	* @param array $s La session
//	*/
//	public function traitement_solde($s){
//		//Liste des sociétés
//		$this->q->reset()->addField("id_societe")->addGroup("id_societe")->addOrder("id_societe");
//		$data=$this->sa();
//		$solde=0;
//		$somme=0;
//		$operation=2;
//		foreach($data as $societe){
//			$id_societe=$societe["id_societe"];
//			//if($id_societe!=666) continue;
//
//			echo "------------------------------------------------------\n";
//			echo "Traitement societe ".ATF::societe()->select($id_societe,"societe")." - ".$id_societe."\n";
//
//			//Calcul du solde avant réajustement
//			echo "----------Calcul des soldes avant reajustement------------\n";
//			//Suppression du ticket d'init éventuel
//			$this->q->reset()->addField("id_gestion_ticket")->addCondition("id_societe",$id_societe)->addCondition("libelle","Solde initial","AND")->setDimension("cell");
//			$ticket_init=$this->sa();
//			if($ticket_init){
//				$this->delete($ticket_init);
//			}
//			$this->ss_traitement_solde($id_societe);
//
//			//Vérification du solde société
//			$solde_societe=ATF::societe()->select($id_societe,"credits");
//			echo "Ancien Solde : ".$solde_societe."\n";
//			//Solde avant la dernière mise en prod
//			$solde=ATF::societe()->getSolde($id_societe,"2010-06-01");
//			//$solde=ATF::societe()->getSolde($id_societe,"2009-09-30");
//			echo "Nouveau Solde avt mep : ".$solde."\n";
//			//Solde actuel
//			$soldenv=ATF::societe()->getSolde($id_societe);
//			echo "Nouveau Solde actuel : ".$soldenv."\n";
//
//			//Calcul de la différence
//			$diff=0;
//			if($solde!=$solde_societe){
//				$diff=$solde_societe-$solde;
//				$somme+=$diff;
//				echo "Différence = ".$diff."\n";
//			}
//
//			// Solde initial
//			$solde=0;
//			$gestion_ticket=array();
//			$gestion_ticket["operation"]=1;
//			$gestion_ticket["libelle"]="Solde initial";
//			$gestion_ticket["date"]="2007-01-01";
//			$gestion_ticket["type"]=($diff>=0?"ajout":"retrait");
//			$gestion_ticket["nbre_tickets"]=$diff;
//			$gestion_ticket["solde"]=$diff;
//			$gestion_ticket["id_societe"]=$id_societe;
//			$solde=$diff;
//			echo "Creation solde initial\n";
//			$this->insert($gestion_ticket);
//			$operation=2;
//
//
//			//Calcul du solde avant réajustement
//			echo "----------Calcul des soldes après reajustement------------\n";
//			$total=0;
//			$total=$this->ss_traitement_solde($id_societe);
//			echo "SOLDE FINAL = ".$total."\n";
//
//		}
//		echo "------------------------------------------------------\n";
//		echo "Somme des diff : ".$somme."\n";
//	}
//
//	/**
//	* Sous traitement solde
//	* @param int $id_societe
//	* @return float $solde
//	*/
//	private function ss_traitement_solde($id_societe){
//		$this->q->reset()->addOrder("date,id_gestion_ticket")->addCondition("id_societe",$id_societe);
//		$data=$this->sa();
//		//print_r($data);
//		$solde=0;
//		$operation=2;
//		foreach($data as $enregistrement){
//			//Calcul du solde
//			$solde+=$enregistrement["nbre_tickets"];
//
//			//On saute le ticket d'init
//			if($enregistrement["libelle"]=="Solde initial"){
//				continue;
//			}
//
//			//maj du solde
//			echo "Date : ".$enregistrement["date"]." Op : ".$operation." - Societe : ".$id_societe." - Mv Tickets : ".$enregistrement["nbre_tickets"]." - Solde : ".$solde."\n";
//			$this->update(array("id_gestion_ticket"=>$enregistrement["id_gestion_ticket"],"solde"=>$solde,"operation"=>$operation));
//
//			//Incrémentation du numéro d'opération
//			$operation++;
//		}
//		return $solde;
//	}

	/**
	* Donne Le numéro de la dernière opération effectuée
	* @param int $id_societe L'identifiant de la société
	* @return int le numéro de l'opération
	*/
	public function getLastOp($id_societe){
		$id_societe=$this->decryptId($id_societe);
		$this->q->reset()->addField("MAX(operation)")->addCondition("id_societe",$id_societe);
		$retour=$this->sa();
		return $retour[0]["MAX(operation)"];
	}

	/**
	 * Retourne un tableau pour les graphes d'affaires, dans statistique
	 * @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	 * @param array $stats
	 * @param string $type : type de graphe, par CA /marge ou nbre de création
	 * @param boolean $widget : si c'est un graphe en page d'accueil ou non
	 * @param integer $id_societe : la société concernée par le graphe
	 * @return array
	 */
	public function stats($stats=false,$type=false,$widget=false,$id_societe=NULL,$month=NULL,$year=NULL) {
		if(!$month)$month=date('m');
		if(!$year)$year=date('Y');
		$id_societe=ATF::societe()->decryptId($id_societe);
		if($widget){
			$mois=util::month();
			for($m=$month+1;$m<=12;$m++){
				$stats['categories']["category"][$m]=array("label"=>substr($mois[(strlen($m)<2?"0".$m:$m)],0,1).substr($year-1,-2));
			}
			for($m=1;$m<=$month;$m++){
				$stats['categories']["category"][$m]=array("label"=>substr($mois[(strlen($m)<2?"0".$m:$m)],0,1).substr($year,-2));
			}

			//si il s'agit du graphe d'une société
			if($id_societe){
				$this->ajoutDonneesStats($stats,$id_societe,$month,$year);
				//si différent de 12 c'est qu'on a pas de données pour chaque mois
				if(count($stats['DATA'])!=12){
					$this->ajoutComplement($stats['DATA'],$stats['categories']["category"],$id_societe,$year);
				}
			}else{
				//si il s'agit du graphe de toutes les sociétés, vu qu'il y a pas mal de mois non renseigné, on fait un système spécial
				$this->ajoutDonneesStatsSansSoc($stats['DATA'],$stats['categories']["category"]);
			}

			return parent::stats($stats,$type,$widget,false);
		}else{
			return parent::stats($stats,$type,$widget,false);
		}
	}

	/** Récupération des données dans un intervalle d'un an glissant
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $stats : contient les données du graphe
	* @param integer $id_societe : id de la société à vérifier
	*/
	public function ajoutDonneesStats(&$stats,$id_societe,$month=NULL,$year=NULL){
		$this->q->reset()->addField('MAX(id_gestion_ticket)','id_gestion_ticket')
							->setStrict()
							->addCondition("DATE_ADD(gestion_ticket.date, INTERVAL 11 MONTH)","'".$year."-".$month."-01'",NULL,false,">=",false,false,true)
							->addGroup("MONTH(date)")
							->addCondition("id_societe",$id_societe)
							->setToString();
		$subQuery = $this->sa();

		$this->q->reset()->addJointure("gestion_ticket","id_gestion_ticket","gt2","id_gestion_ticket","gt2",NULL,NULL,NULL,"inner",NULL,$subQuery)
							->addField("gestion_ticket.id_gestion_ticket","id_gestion_ticket")
							->addField("MONTH(gestion_ticket.date)","month")
							->addField("gestion_ticket.solde","nb")
							->addField("societe.societe","label")
							->addJointure("gestion_ticket","id_societe","societe","id_societe")
							->setStrict();
		$this->q->setArrayKeyIndex("month",false);
		$stats['DATA'] = $this->sa("no_order");
	}

	/** Récupération des données dans un intervalle d'un an glissant pour chaques sociétés présentes dans la table et étant active
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $data : contient les données du graphe
	* @param array $cat : liste des catégories
	*/
	public function ajoutDonneesStatsSansSoc(&$data,$cat){
		//récupération de toutes les sociétés présentes dans la table
		$this->q->reset()->addField("DISTINCT(".$this->table.".id_societe)","id_societe")->setStrict()
						->addJointure($this->table,"id_societe","societe","id_societe")
						->addCondition("societe.etat","actif")
						->addConditionNotNull($this->table.".id_societe");
		//récupération des données de chacune d'elle
		foreach($this->sa() as $key=>$item){
			$this->ajoutDonneesStats($stats,$item['id_societe']);
			//si différent de 12 c'est qu'on a pas de données pour chaque mois
			if(count($stats['DATA'])!=12){
				$this->ajoutComplement($stats['DATA'],$cat,$item['id_societe']);
			}
			//on additionne les résultats
			foreach($cat as $mois=>$els){
				$data[$mois]['nb']+=$stats['DATA'][$mois]['nb'];
			}
		}

		//ajout du complément de données pour permettre l'affichage du graphe
		foreach($data as $mois=>$infos){
			$data[$mois]['label']="societes";
			$data[$mois]['month']=$mois;
		}
	}

	/** Récupération des données dans un intervalle d'un an glissant pour chaques sociétés présentes dans la table et étant active
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array $data : contient les données du graphe
	* @param array $cat : liste des catégories
	* @param integer $id_societe : id de la société à vérifier
	*/
	public function ajoutComplement(&$data,$cat,$id_societe,$year=NULL){
		if(!$year)$year=date('Y');
		$this->q->reset()->addField('MAX(id_gestion_ticket)','id_gestion_ticket')
							->setStrict()
							->addCondition("id_societe",$id_societe)
							->addGroup("MONTH(date)")
							->setToString()
							->addOrder("id_gestion_ticket","desc");
		$subQuery = $this->sa("no_order");

		foreach($cat as $mois=>$els){
			if(!isset($data[$mois])){
				$ancienne_date=date("Y-m",strtotime("-1 month",strtotime(substr($year,0,2).substr($els['label'],-2)."-".($mois<=9?"0".$mois:$mois)."-01 00:00:00")));
				$recup_el=explode("-",$ancienne_date);
				if($recup_el[1]<10)$nom_el=str_replace("0","",$recup_el[1]);
				else $nom_el=$recup_el[1];
				//si deja present dans le dataset, on le récupère
				if(isset($data[$nom_el]) && substr($recup_el[0],-2)==substr($cat[$nom_el]['label'],-2)){
					$data[$mois]=array("nb"=>$data[$nom_el]["nb"],"month"=>$mois,"label"=>$data[$nom_el]["label"]);
				}else{
					//sinon on va le chercher (sql)
					$this->q->reset()->addJointure("gestion_ticket","id_gestion_ticket","gt2","id_gestion_ticket","gt2",NULL,NULL,NULL,"inner",NULL,$subQuery)
										->addCondition("DATE_FORMAT(gestion_ticket.date,'%Y-%m')",$ancienne_date)
										->addField("gestion_ticket.id_gestion_ticket","id_gestion_ticket")
										->setStrict()
										->addField("gestion_ticket.solde","nb")
										->addField("societe.societe","label")
										->addJointure("gestion_ticket","id_societe","societe","id_societe");

					//si la donnée n'est pas présente, on récupère le dernier solde présent dans la base, dont la date est juste antérieure à l'actuelle
					if($solde=$this->sa("no_order")){
						$data[$mois]=array("nb"=>$solde[0]['nb'],"month"=>$mois,"label"=>$solde[0]["label"]);
					}else{
						//sinon on prends le dernier solde connu, le plus proche de la date utilisée
						$this->q->reset()->addJointure("gestion_ticket","id_gestion_ticket","gt2","id_gestion_ticket","gt2",NULL,NULL,NULL,"inner",NULL,$subQuery)
											->addCondition("gestion_ticket.date",$ancienne_date,"OR",false,"<")
											->addField("gestion_ticket.id_gestion_ticket","id_gestion_ticket")
											->setStrict()->setLimit(1)
											->addField("gestion_ticket.solde","nb")
											->addField("societe.societe","label")
											->addJointure("gestion_ticket","id_societe","societe","id_societe");

						if($solde=$this->sa("no_order")) $data[$mois]=array("nb"=>$solde[0]['nb'],"month"=>$mois,"label"=>$solde[0]["label"]);
					}
				}
			}
		}
	}

	/** Initialisation des abscisses du graphe (commence par le mois+1 (annee courante-1) et fini par le mois courant (annee courante)
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param array graph : contient les données concernant le graphe
	* @param string intitule : nom de l'abscisse
	*/
	public function initGraphe(&$graph,$intitule,$month=NULL){
		if(!$month)$month=date('m');
		for($m=$month+1;$m<=12;$m++){
			$graph['dataset'][$intitule]['set'][strlen($m)<2?"0".$m:$m] = array("value"=>0,"alpha"=>100,"titre"=>$intitule." : 0");
		}
		for($m=1;$m<=$month;$m++){
			$graph['dataset'][$intitule]['set'][strlen($m)<2?"0".$m:$m] = array("value"=>0,"alpha"=>100,"titre"=>$intitule." : 0");
		}
	}

	/** Retourne la liste des sociétés formatés pour une liste déroulante
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @return array
	*/
	public function options_soc(){
		$this->q->reset()
				->addField("societe.id_societe","id")
				->addField("societe.societe","societe")
				->addJointure("gestion_ticket","id_societe","societe","id_societe",NULL,NULL,NULL,NULL,"inner")
				->addCondition("YEAR( gestion_ticket.date )",date('Y'))
				->addGroup("id")
				->addOrder("societe")
				->addOrder("id");

		foreach(parent::select_all() as $tab){
			$r[$tab['id']] = $tab['societe'];
		}
		return $r;
	}

};
?>