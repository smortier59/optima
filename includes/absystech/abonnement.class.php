<?
/**
* Classe abonnement
* @package Optima
* @subpackage AbsysTech
*/
class abonnement extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct();

		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array(
			'abonnement.codename',
			'abonnement.nbre_user_actif',
			'abonnement.espace_utilise',
			'abonnement.liste_user',
			'marge'=>array('custom'=>true),
			'marge12month'=>array('custom'=>true)
		);
		$this->view=array("suffix"=>array("abonnement.espace_utilise"=>"Mo"));
		$this->fieldstructure();

	}

	/**
	 * @author Quentin JANON <qjanon@absystech.fr>
	 */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){

		$this->q
			->addJointure("abonnement","id_affaire","facture","id_affaire","f")
			->addField("SUM(f.prix)","marge")
			->addField("SUM(IF(`f`.`date` >= '2014-06-04 17:25:24',`f`.`prix`,0))","marge12month")
			->addGroup("codename");

		$return = parent::select_all($order_by,$asc,$page,$count);
		return $return;
	}

	/* Remplissage de la table
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insertMassif(){
		$dbold = ATF::db($this->db)->getDatabase();

		//récupère les users actifs depuis moins de 3 mois
		ATF::db($this->db)->select_db("optima");
		ATF::activity()->q->reset()->addField("website_codename")
									->addField("login")
									->addField("activity")
									->setStrict();
		$r = ATF::activity()->select_all();
		if($r){
			foreach($r as $cle=>$donnees){
				if ($donnees['login'] == "absystech") continue;
				$dbToConnect = $donnees['website_codename'];
				if ($donnees['website_codename'] == "midas") {
					$dbToConnect = "cleodis";
				}
				//pour compter le nombre d'utilisateur, on regarde deux choses
				//soit la date d'activité est inférieure à 3 mois
				if(strtotime("+3 months",strtotime($donnees['activity']))>strtotime(date("Y-m-d H:i:s"))){
					$a_inserer[$donnees['website_codename']]["nbre_user_actif"]++;
					if(ATF::db($this->db)->select_db(ATF::getSchema().$dbToConnect)){
						ATF::user()->q->reset()->addCondition("login",$donnees['login']);
						if($u = ATF::user()->select_row()){
							$a_inserer[$donnees['website_codename']]["liste_user"] .= ATF::user()->nom($u['id_user'])."\n";
						}
					}
				}else{
					//$a_checker[$donnees['website_codename']][]=$donnees['login'];
					//soit on vérifie si son état est normal ou non
					if(ATF::db($this->db)->select_db(ATF::getSchema().$dbToConnect)){
						ATF::user()->q->reset()->addCondition("etat","normal")->addCondition("login",$donnees['login']);
						if($u = ATF::user()->select_row()){
							$a_inserer[$donnees['website_codename']]["nbre_user_actif"]++;
							$a_inserer[$donnees['website_codename']]["liste_user"] .= ATF::user()->nom($u['id_user'])."\n";
						}
					}
				}
			}

			foreach($a_inserer as $codename=>$nbre_usr){
				$size=NULL;
				if(file_exists(__DATA_PATH__.$codename)){
					$deb_chemin=__DATA_PATH__.$codename;
					$taille=explode("/",`du -bs $deb_chemin`);
					$size=round(trim($taille[0])/1048576);
				}
				$tab_insert[]=array(
					"codename"=>$codename,
					"nbre_user_actif"=>$nbre_usr['nbre_user_actif'],
					"liste_user"=>$nbre_usr['liste_user'],
					"espace_utilise"=>($size?$size:NULL)
				);

			}
		}

		//insertion des données dans la bonne base
		ATF::db($this->db)->select_db($dbold);
		//vu le peu de donnée je supprime le contenu et remplace par le nouveau
		ATF::db($this->db)->query("TRUNCATE TABLE `abonnement`");
		ATF::abonnement()->multi_insert($tab_insert);
	}

};
?>