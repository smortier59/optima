<?
/** Classe devis
* @package Optima
* @subpackage Absystech
*/
require_once dirname(__FILE__)."/../produit.class.php";
class produit_absystech extends produit {
	// Mapping prévu pour un autocomplete sur produit
	public static $autocompleteMapping = array(
		array("name"=>'nom', "mapping"=>0),
		array("name"=>'detail', "mapping"=>1),
		array("name"=>'prix', "mapping"=>2),
		array("name"=>'id_devis_ligne', "mapping"=>3),
		array("name"=>'id_produit', "mapping"=>4),
		array("name"=>'ref', "mapping"=>5)
	);

	function __construct() {
		parent::__construct();
		$this->table = "produit";

		$this->colonnes['fields_column'] = array(
			'produit.produit'
			,'produit.ref'=>array("width"=>100,"align"=>"center")
			,'produit.prix_achat'=>array("width"=>100,"align"=>"right","renderer"=>"money")
			,'produit.prix'=>array("width"=>100,"align"=>"right","renderer"=>"money")
		);

		$this->colonnes['panel']['caracteristiques']=array('description','prix_achat','prix','id_fabriquant');

		$this->fk_ligne =  'ref,prix_achat,id_fournisseur,id_produit,id_compte_absystech,prix,produit';

		$this->fieldstructure();

		$this->addPrivilege("autocompleteConsommable");

	}

	/**
    * Surcharge de la méthode autocomplete pour faire apparaître les produits déjà insérés par l'utilisateur
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos
    * @return int  id si enregistrement ok
    */
	function autocomplete($infos) {
		//if ($infos["limit"]>25) return; // Protection nombre d'enregistrements par page

		if (strlen($infos["query"])>0) {
			$data = array();
			$searchKeywords = stripslashes(urldecode($infos["query"]));

			// Récupérer les lignes devis
			ATF::devis_ligne()->q->reset()
				->setStrict()
				->addJointure("devis_ligne","id_devis","devis","id_devis")
				->addCondition("devis_ligne.produit","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				->addCondition("devis_ligne.ref","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				//->addCondition("devis.id_user",ATF::$usr->getID(),'AND')
				->addCondition("devis_ligne.id_produit",NULL,'AND',false,'IS NULL')
				->addField("devis_ligne.produit","produit")
				->addField("devis_ligne.ref","ref")
				->addField("devis_ligne.prix","prix")
				->addField("devis_ligne.id_devis_ligne","id_devis_ligne")
				->addField('""',"id_produit")
				->addGroup("produit")
				->addOrder("devis.date","DESC")
				->setStrict(1)
				->setToString();
			$queries[] = ATF::devis_ligne()->sa();

			// Récupérer les produits
			$this->q->reset()
				->setStrict()
				->addCondition("produit.produit","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				->addCondition("produit.ref","%".ATF::db($this->db)->real_escape_string($infos["query"])."%","OR","cle",'LIKE')
				->addField("produit.produit","produit")
				->addField("produit.ref","ref")
				->addField("produit.prix","prix")
				->addField('""',"id_devis_ligne")
				->addField('produit.id_produit',"id_produit")
				->setStrict(1)
				->setToString();
			$queries[] = $this->sa();
			$q = new querier();
			$q->setLimit($infos["limit"])->setPage($infos["start"]/$infos["limit"]);
			if ($result = ATF::db($this->db)->union($queries,$q)) {
				// On met en valeur la chaîne recherchée dans les réponses
				$replacement = ATF::$html->fetch("search_replacement.tpl.htm","sr");
				foreach ($result["data"] as $k => $i) {
					foreach ($result["data"][$k] as $k_ => $i_) { // Mettre en valeur
						$result_final["data"][$k][$k_] = preg_replace("/".$infos["query"]."/i", $replacement, $i_);
					}
					$result_final["data"][$k][$k_+1] = $result["data"][$k][1];
				}
			}
			ATF::$json->add("totalCount",$result_final["count"]);
		}
		ATF::$cr->rm("top");
		return $result_final["data"];
	}

	/**
	* Autocomplete retournant les consommables
	* @author Morgan FLEURQUIN
	* @param array $infos ($_POST habituellement attendu)
	*	string $infos[recherche]
	* @param boolean $reset VRAI si on reset lme querier, FAUX si on a initialisé qqch de précis avant...
	* @return string HTML de retour
	*/
	public function autocompleteConsommable($infos,$reset=true) {
		if ($reset) {
               $this->q->reset();
       }
       //$this->q->where("produit.fournisseur","oui");

       return $this->autocomplete($infos,false);
	}


	public function _ac($get,$post){
		$return = NULL;
		ATF::devis_ligne()->q->reset()
				->setStrict()
				->addJointure("devis_ligne","id_devis","devis","id_devis")
				->addCondition("devis_ligne.produit","%".ATF::db($this->db)->real_escape_string($get["q"])."%","OR","cle",'LIKE')
				->addCondition("devis_ligne.ref","%".ATF::db($this->db)->real_escape_string($get["q"])."%","OR","cle",'LIKE')
				->addCondition("devis.id_user",ATF::$usr->getID(),'AND')
				->addCondition("devis_ligne.id_produit",NULL,'AND',false,'IS NULL')
				->addField("devis_ligne.produit","produit")
				->addField("devis_ligne.ref","ref")
				->addField("devis_ligne.prix","prix")
				->addField("devis_ligne.id_devis_ligne","id_devis_ligne")
				->addField('""',"id_produit")
				->addGroup("produit")
				->addOrder("devis.date","DESC")
				->setStrict(1)
				->setToString();
			$queries[] = ATF::devis_ligne()->sa();

			// Récupérer les produits
			$this->q->reset()
				->setStrict()
				->addCondition("produit.produit","%".ATF::db($this->db)->real_escape_string($get["q"])."%","OR","cle",'LIKE')
				->addCondition("produit.ref","%".ATF::db($this->db)->real_escape_string($get["q"])."%","OR","cle",'LIKE')
				->addField("produit.produit","produit")
				->addField("produit.ref","ref")
				->addField("produit.prix","prix")
				->addField('""',"id_devis_ligne")
				->addField('produit.id_produit',"id_produit")
				->setStrict(1)
				->setToString();
			$queries[] = $this->sa();
			$q = new querier();
			if ($result = ATF::db($this->db)->union($queries,$q)) {
				foreach ($result["data"] as $key => $value) {
					if($value[4]){
						$produit = $this->select($value[4]);
						$return[] = array("ref"=>$produit["ref"],
										  "produit"=>$produit["produit"],
										  "prix"=>$produit["prix"],
										  "prix_achat"=>$produit["prix_achat"],
										  "id_fournisseur"=>$produit["id_fournisseur"],
										  "id_fournisseur_fk"=>ATF::societe()->select($produit["id_fournisseur"],"societe"),
										  "id_compte_absystech"=>$produit["id_compte_absystech"]);
					}else{
						$return[] = array("ref"=>$value[1],
										  "produit"=>$value[0],
										  "prix"=>$value[2],
										  "prix_achat"=>NULL,
										  "id_fournisseur"=>NULL,
										  "id_compte_absystech"=>NULL);
					}
				}
			}
			return $return;

	}
};

class produit_att extends produit_absystech { };
class produit_wapp6 extends produit_absystech { };
class produit_atoutcoms extends produit_absystech { };
class produit_demo extends produit_absystech { };

class produit_nco extends produit_absystech { };

?>