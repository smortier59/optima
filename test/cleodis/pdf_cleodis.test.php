<?
/*
* @author Quentin JANON <qjanon@absystech.fr>
* @package Optima
* @subpackage Cléodis
* @date 21-01-11
*/
class pdf_cleodis_test extends ATF_PHPUnit_Framework_TestCase {

	/*
	* Chemin où est stocké le fichier temporaire pour les tests
	* @access private
	* @var string
	*/
	private $dirSavedPDF = "/home/www/SauvegardePDFTU/";
	private $dateSave = "";
	private $tmpFile = "/tmp/TUPDFTEMPORAIRE_PDF_CLEODIS_CLASS.pdf";
	/*
	* Commande Ghost Script permettant la conversion du PDF en image
	* @access private
	* @var string
	*/
	private $GScmd = "";
	/*
	* Commande SHELL pour avoir la résultante d'un fichier en md5
	* @access private
	* @var string
	*/
	private $MD5cmd = "";

	// Ne pas toucher cette fonction ! */
	public function __construct() {
		parent::__construct();
		$this->GScmd = "gs -dQUIET -dNOPAUSE -dBATCH -sDEVICE=jpeg -sOutputFile=".str_replace(".pdf",".jpg",$this->tmpFile)." ".$this->tmpFile." 2>&1";
		$this->MD5cmd = "md5sum ".str_replace(".pdf",".jpg",$this->tmpFile);
		$this->dateSave = date('Ymd');
	}

	/*
	* SetUp : créer un user/societe/droit, une facture, un devis, une affaire et un ODM !
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-01-11r
	*/
	public function setUp(){
		ATF::db()->select_db("optima_cleodis");
	    ATF::$codename = "cleodis";

		//Suppression des fichiers qui sont daté de plus de 7 jours
		$this->dirSavedPDF .= "cleodis/";
		$list = scandir($this->dirSavedPDF);
		$dateRef = mktime(0, 0, 0, date("m"), date("d")-7, date("Y"));
		foreach ($list as $k=>$i) {
			if ($i=="." || $i=="..") continue;
			$a = preg_match("/[0-9]{8}/",$i,$m);
			$dateFile = strtotime($m[0]);
			if ($dateRef>$dateFile) {
				ob_start();
				system("rm ".$this->dirSavedPDF.$i." 2>&1");
				ob_end_clean();
			}
		}
		$this->dirSavedPDF .= "PDF_CL";

		ATF::db()->begin_transaction(true);

		$this->assertFileNotExists($this->tmpFile,"Erreur : Les fichiers temporaires sont déjà présents :/");
		$this->insertSociete();
		$this->societe = ATF::societe()->select($this->id_societe);
		$this->insertContact();
		$this->contact = ATF::contact()->select($this->id_contact);

		$this->create("user");
		$this->create("affaire");
		$this->create("devis");
		$this->create("loyer");

		//SUppression du fichier devis généré automatiquement et stocké
		util::rm(__ABSOLUTE_PATH__."../data/absystech/devis/".$this->id_devis.".fichier_joint");
	}

	/*
	* Méthode qui supprime les fichiers générés a la fin du TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-01-11
	*/
	public function tearDown(){
		ATF::$msg->getNotices();
		ATF::db()->rollback_transaction(true);
		ob_start();
		system("rm ".str_replace(".pdf","",$this->tmpFile).".* 2>&1");
		ob_end_clean();
	}


	/*
	* Méthode qui permet de créer les enregistrements utiles aux TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-01-11
	*/
	private function create($el) {
		$this->id_profil = 1;
		switch ($el) {
			case "contact":
				$this->contact = array(
					"id_societe"=>$this->id_societe
					,"nom"=>"NOMTU"
					,"prenom"=>"PRENOMTU"
				);
				$this->contact['id_contact'] = ATF::contact()->i($this->contact);
			break;
			case "affaire":
				$this->affaire = array(
					"id_societe"=>$this->id_societe
					,"affaire"=>"Affaire pour les TU"
					,"ref"=>"AFFAIRETU1"
					,"IBAN"=>"IBAN REF"
					,"BIC"=>"BIC REF"
					,"RUM"=>"RUM numero"
					,"nom_banque"=>"banque REF"
					,"ville_banque"=>"ville_banque REF"
				);
				$this->affaire['id_affaire'] = ATF::affaire()->i($this->affaire);
			break;
			case "user":
				//Création d'un utilisateur
				$this->user=array(
					"login"=>"tutulForPDF"
					,"password"=>"tu"
					,"civilite"=>"M"
					,"prenom"=>"Tutul pour les PDF"
					,"nom"=>"unitaire"
					,"id_agence"=>1
					,"id_societe"=>$this->id_societe
					,"id_profil"=>$this->id_profil
					,"email"=>"debug@absystech.fr"
					,'custom'=>serialize(array('preference'=>array('langue'=>'fr')))
				);
				$this->user['id_user'] = ATF::user()->i($this->user);
			break;
			case "devis":
				$this->devis = array(
					"id_affaire"=>$this->affaire['id_affaire']
					,"ref"=>"REFDEVISCLEODIS"
					,"id_societe"=>$this->id_societe
					,"id_user"=>$this->user['id_user']
					,"devis"=>"Devis pour les TU"
					,"type_contrat"=>"lrp"
					,"tva"=>"1.196"
					,"validite"=>"2050-08-01"
					,"date"=>"2011-02-24"
					,"id_contact"=>$this->id_contact
				);

				$this->devis['id_devis'] = ATF::devis()->i($this->devis,ATF::_s());
				//Quelques lignes produit
				for ($a=0; $a<3; $a++) {
					$neuf = "oui";
					if($a == 0){
						$neuf = "non";
					}
					$this->ligneDevis[$a] = array(
						"ref"=>"REF".$a
						,"produit"=>"Produit TU n°".$a
						,"quantite"=>2*$a+1
						,"id_devis"=>$this->devis['id_devis']
						,"id_fournisseur"=>$this->id_societe
						,"prix_achat"=>11*$a+1
						,"neuf"=>$neuf
					);

					$this->ligneDevis[$a]['id_devis_ligne'] = ATF::devis_ligne()->i($this->ligneDevis[$a]);
				}
			break;
			case "devis_optic2000_avenant":
				$affaire = $this->affaire;
				ATF::affaire()->u(array("id_affaire"=> $affaire["id_affaire"], "nature"=> "avenant", "id_parent"=> 26));

				$this->devis_optic2000_avenant = array(
					"id_affaire"=>$affaire['id_affaire']
					,"ref"=>"REFDEVISOPTIC"
					,"id_societe"=>$this->id_societe
					,"id_user"=>$this->user['id_user']
					,"devis"=>"Devis pour les TU optic"
					,"type_contrat"=>"lrp"
					,"type_devis"=>"optic_2000"
					,"tva"=>"1.196"
					,"validite"=>"2050-08-01"
					,"date"=>"2011-02-24"
					,"id_contact"=>$this->id_contact
				);

				$this->devis_optic2000_avenant['id_devis'] = ATF::devis()->i($this->devis_optic2000_avenant,ATF::_s());
				//Quelques lignes produit
				for ($a=0; $a<3; $a++) {
					$neuf = "oui";
					if($a == 0){
						$neuf = "non";
					}
					$this->devis_optic2000_avenant[$a] = array(
						"ref"=>"REF".$a
						,"produit"=>"Produit TU n°".$a
						,"quantite"=>2*$a+1
						,"id_devis"=>$this->devis_optic2000_avenant['id_devis']
						,"id_fournisseur"=>$this->id_societe
						,"prix_achat"=>11*$a+1
						,"neuf"=>$neuf
					);

					$this->devis_optic2000_avenant[$a]['id_devis_ligne'] = ATF::devis_ligne()->i($this->devis_optic2000_avenant[$a]);
				}
			break;

			case "devis_optic2000_AR":
				$affaire = $this->affaire;
				ATF::affaire()->u(array("id_affaire"=> $affaire["id_affaire"], "nature"=> "AR"));
				ATF::affaire()->u(array("id_affaire"=> 26, "id_fille"=> $affaire["id_affaire"]));
				ATF::affaire()->u(array("id_affaire"=> 81, "id_fille"=> $affaire["id_affaire"]));
				ATF::affaire()->u(array("id_affaire"=> 3035, "id_fille"=> $affaire["id_affaire"]));

				ATF::parc()->q->reset()->where("parc.id_affaire", 26);
				foreach (ATF::parc()->select_all() as $key => $value) {
					ATF::parc()->u(array("id_parc"=>$value["id_parc"] , "existence"=>"actif"));
				}


				$this->devis_optic2000_AR = array(
					"id_affaire"=>$affaire['id_affaire']
					,"ref"=>"REFOPTICAR"
					,"id_societe"=>$this->id_societe
					,"id_user"=>$this->user['id_user']
					,"devis"=>"Devis pour les TU optic"
					,"type_contrat"=>"lrp"
					,"type_devis"=>"optic_2000"
					,"tva"=>"1.196"
					,"validite"=>"2050-08-01"
					,"date"=>"2011-02-24"
					,"id_contact"=>$this->id_contact
				);

				$this->devis_optic2000_AR['id_devis'] = ATF::devis()->i($this->devis_optic2000_AR,ATF::_s());
				//Quelques lignes produit
				for ($a=0; $a<3; $a++) {
					$neuf = "oui";
					$ref_simag = "";
					if($a == 0){
						$neuf = "non";
						$ref_simag = "25652486";
					}elseif($a == 1){
						$neuf = "non";
						$ref_simag = "25652487";
					}
					$this->devis_optic2000_AR[$a] = array(
						"ref"=>"REF".$a
						,"produit"=>"Produit TU n°".$a
						,"quantite"=>2*$a+1
						,"id_devis"=>$this->devis_optic2000_AR['id_devis']
						,"id_fournisseur"=>$this->id_societe
						,"prix_achat"=>11*$a+1
						,"neuf"=>$neuf
						,"ref_simag"=> 	$ref_simag
					);
					$this->devis_optic2000_AR[$a]['id_devis_ligne'] = ATF::devis_ligne()->i($this->devis_optic2000_AR[$a]);
				}
				$this->devis_optic2000_AR[3] = array(
						"ref"=>"REF 4"
						,"produit"=>"Produit TU n° 4"
						,"quantite"=>"10"
						,"id_devis"=>$this->devis_optic2000_AR['id_devis']
						,"id_fournisseur"=>$this->id_societe
						,"prix_achat"=>"200"
						,"neuf"=>"non"
						,"id_affaire_provenance"=>81
					);
				$this->devis_optic2000_AR[3]['id_devis_ligne'] = ATF::devis_ligne()->i($this->devis_optic2000_AR[3]);
				$this->devis_optic2000_AR[4] = array(
						"ref"=>"SIMAG"
						,"id_produit" => 8292
						,"produit"=>"Migration SIMAG"
						,"quantite"=>"1"
						,"id_devis"=>$this->devis_optic2000_AR['id_devis']
						,"id_fournisseur"=>$this->id_societe
						,"prix_achat"=>"200"
						,"neuf"=>"oui"
					);
				$this->devis_optic2000_AR[4]['id_devis_ligne'] = ATF::devis_ligne()->i($this->devis_optic2000_AR[4]);
				$this->devis_optic2000_AR[5] = array(
						"ref"=>"REF 5"
						,"serial" => "PCKINEAMD01"
						,"produit"=>"Produit TU n° 5"
						,"quantite"=>"10"
						,"id_devis"=>$this->devis_optic2000_AR['id_devis']
						,"id_fournisseur"=>$this->id_societe
						,"prix_achat"=>"200"
						,"neuf"=>"non"
						,"id_affaire_provenance"=>81
					);
				$this->devis_optic2000_AR[5]['id_devis_ligne'] = ATF::devis_ligne()->i($this->devis_optic2000_AR[5]);
			break;

			case "commande":
				$this->commande = array(
					"id_affaire"=>$this->affaire['id_affaire']
					,"ref"=>"REFCMDCLEODIS"
					,"id_societe"=>$this->id_societe
					,"id_user"=>$this->user['id_user']
					,"commande"=>"CMD pour les TU"
					,"type"=>"prelevement"
					,"tva"=>"1.196"
					,"date"=>"2011-02-24"
					,"id_devis"=>$this->devis['id_devis']
					,"clause_logicielle"=>$this->id_devis
					,"date_debut"=>"2011-02-24"
				);

				$this->commande['id_commande'] = ATF::commande()->i($this->commande,ATF::_s());
				//Quelques lignes produit
				for ($a=0; $a<3; $a++) {
					$neuf = "oui";
					if($a == 0){
						$neuf = "non";
					}
					$this->ligneCommande[$a] = array(
						"ref"=>"REF".$a
						,"id_produit"=>5
						,"produit"=>"Produit TU n°".$a
						,"quantite"=>2*$a+1
						,"id_commande"=>$this->commande['id_commande']
						,"prix_achat"=>11*$a+1
						,"neuf"=>$neuf
					);
					$this->ligneCommande[$a]['id_commande_ligne'] = ATF::commande_ligne()->i($this->ligneCommande[$a]);
				}
			break;
			case "produitsCommande":
				if ($this->commande) {
					for ($a=0; $a<3; $a++) {
						$this->produit[$a] = array(
							"ref"=>"REFTUDETAIL".$a
							,"produit"=>"Produit ".$a." pour les TU avec du détails a foison"
							,"prix_achat"=>11*$a+1
							,"id_fabriquant"=>68 // 3COM
							,"id_sous_categorie"=>16 // PRESTATION
						);
						$this->produit[$a]['id_produit'] = ATF::produit()->i($this->produit[$a]);
					}
					foreach ($this->ligneCommande as $k=>$i) {
						$i['id_produit'] = $this->produit[$k]['id_produit'];
						ATF::commande_ligne()->u($i);
					}
				}
			break;
			case "loyer":
				if ($this->affaire) {
					for ($a=0; $a<5; $a++) {
						$this->loyer[$a] = array(
							"id_affaire"=>$this->affaire['id_affaire']
							,"loyer"=>50*$a+0.5
							,"duree"=>2*$a+1
							,"assurance"=>10*$a+1
							,"frais_de_gestion"=>10*$a+1
							,"frequence_loyer"=>($a%3?"mois":($a%2?"trimestre":"an"))
						);
						if($a == 4){
							$this->loyer[4]["frequence_loyer"] = "semestre";
							$this->loyer[4]["duree"] = 10;
						}
						$this->loyer[$a]['id_loyer'] = ATF::loyer()->i($this->loyer[$a]);
					}

				}
			break;
			case "produitAvecDetail":
				$this->produitDetail = array(
					"ref"=>"REFTUDETAIL"
					,"produit"=>"Produit pour les TU avec du détails a foison"
					,"prix_achat"=>2000
					,"id_fabriquant"=>68 // 3COM
					,"id_sous_categorie"=>16 // PRESTATION
					,"id_produit_dd"=>1 // 1 x 20Go
					,"id_produit_dotpitch"=>1 // 0.2
					,"id_produit_format"=>1 // A0
					,"id_produit_garantie_uc"=>1 // Garantie 1 an
					,"id_produit_garantie_ecran"=>1 // Garantie 1 an
					,"id_produit_garantie_imprimante"=>1 // Garantie 1 an1
					,"id_produit_lan"=>1 //10 Mbit
					,"id_produit_lecteur"=>1 // Lecteur de CD
					,"id_produit_OS"=>1 // Windows Vista Basic
					,"id_produit_puissance"=>1 // 1 x 1.5gHz
					,"id_produit_ram"=>1 //64 Mo
					,"id_produit_technique"=>1 //Imprimante laser couleur
					,"id_produit_type"=>1 //Station
					,"id_produit_typeecran"=>1 //Ecran TFT
					,"id_produit_viewable"=>1 //12 pouces
					,"id_processeur"=>1 // Intel Pentium 4
				);
				$this->produitDetail['id_produit'] = ATF::produit()->i($this->produitDetail);
				if ($this->commande) {
					$a = count($this->ligneCommande);
					$this->ligneCommande[$a] = array(
						"ref"=>"REF".$a
						,"produit"=>"Produit TU n°".$a
						,"quantite"=>2*$a+1
						,"id_commande"=>$this->commande['id_commande']
						,"prix_achat"=>11*$a+1
						,"id_produit"=>$this->produitDetail['id_produit']
					);
					$this->ligneCommande[$a]['id_commande_ligne'] = ATF::commande_ligne()->i($this->ligneCommande[$a]);
				}
			break;
			case "bdc":
				if ($this->commande) {
					$this->bdc = array(
						"id_affaire"=>$this->affaire['id_affaire']
						,"ref"=>"REFBDCCLEODIS"
						,"id_societe"=>$this->id_societe
						,"id_fournisseur"=>$this->id_societe
						,"id_contact"=>$this->id_contact
						,"id_user"=>$this->user['id_user']
						,"bon_de_commande"=>"BDC pour les TU"
						,"prix"=>20000
						,"tva"=>"1.196"
						,"date"=>"2011-02-24"
						,"id_commande"=>$this->commande['id_commande']
						,"destinataire"=>"Destinataire pour les BDC TU"
						,"adresse"=>"adresse"
						,"cp"=>"cp"
						,"ville"=>"ville"
						,"livraison_destinataire" => "Livraison destinataire"
						,"livraison_adresse" => "Adresse de livraison"
						,"livraison_cp" => "59100"
						,"livraison_ville" => "Ville destinataire"
					);
					$this->bdc['id_bdc'] = ATF::bon_de_commande()->i($this->bdc,ATF::_s());
					//Quelques lignes produit
					for ($a=0; $a<3; $a++) {
						$this->ligneBDC[$a] = array(
							"ref"=>"REF".$a
							,"produit"=>"Produit TU n°".$a
							,"quantite"=>2*$a+1
							,"id_bon_de_commande"=>$this->bdc['id_bdc']
							,"id_commande_ligne"=>$this->ligneCommande[$a]["id_commande_ligne"]
							,"prix"=>11*$a+1
						);
						$this->ligneBDC[$a]['id_bon_de_commande_ligne'] = ATF::bon_de_commande_ligne()->i($this->ligneBDC[$a]);
					}
				}
			break;
			case "demande_refi":
				$this->demande_refi = array(
					"id_affaire"=>$this->affaire['id_affaire']
					,"id_societe"=>$this->id_societe
					,"id_contact"=>$this->id_contact
					,"id_refinanceur"=>2 // KBC
					,"prix"=>20000
					,"date"=>"2011-02-24"
					,"valeur_residuelle"=>2
					,"pourcentage_materiel"=>20
					,"pourcentage_logiciel"=>65
					,"description"=>"Demande refi pour les TU"
					,"etat"=>"accepte"
					,"loyer_actualise"=>20000
				);
				$this->demande_refi['id_demande_refi'] = ATF::demande_refi()->i($this->demande_refi,ATF::_s());

			break;
			case "factureNormale":
				if ($this->commande) {
					$this->facture = array(
						"id_affaire"=>$this->affaire['id_affaire']
						,"id_societe"=>$this->id_societe
						,"prix"=>20000
						,"date"=>"2011-02-24"
						,"id_commande"=>$this->commande['id_commande']
						,"ref"=>"FACTURE CLEODIS"
						,"tva"=>"1.196"
						,"date_periode_debut"=>"2011-02-24"
						,"date_periode_fin"=>"2011-09-24"
						,"date_previsionnelle"=>"2050-01-01"
						,"commentaire"=>"Commentaire"
						,"mode_paiement"=>"virement"
					);
					$this->facture['id_facture'] = ATF::facture()->i($this->facture,ATF::_s());
					for ($a=0; $a<3; $a++) {
						$this->ligneFacture[$a] = array(
							"ref"=>"REF".$a
							,"produit"=>"Produit TU n°".$a
							,"quantite"=>2*$a+1
							,"id_facture"=>$this->facture['id_facture']
							,"prix_achat"=>11*$a+1
						);
						$this->ligneFacture[$a]['id_facture_ligne'] = ATF::facture_ligne()->i($this->ligneFacture[$a]);
					}
				}

			break;
			case "factureRefi":
				if ($this->commande && $this->demande_refi) {
					$this->facture = array(
						"id_affaire"=>$this->affaire['id_affaire']
						,"type_facture"=>"refi"
						,"id_societe"=>$this->id_societe
						,"prix"=>20000
						,"date"=>"2011-02-24"
						,"id_commande"=>$this->commande['id_commande']
						,"ref"=>"REFFACCLEODIS"
						,"tva"=>"1.196"
						,"date_previsionnelle"=>"2011-02-24"
						,"date_periode_debut"=>"2011-02-24"
						,"date_periode_fin"=>"2011-09-24"
						,"id_demande_refi"=>$this->demande_refi['id_demande_refi']
						,"id_refinanceur"=>$this->demande_refi['id_refinanceur']
						,"commentaire"=>"Commentaire"
						,"mode_paiement"=>"virement"
					);
					$this->facture['id_facture'] = ATF::facture()->i($this->facture,ATF::_s());
					for ($a=0; $a<3; $a++) {
						$this->ligneFacture[$a] = array(
							"ref"=>"REF".$a
							,"produit"=>"Produit TU n°".$a
							,"quantite"=>2*$a+1
							,"id_facture"=>$this->facture['id_facture']
							,"prix_achat"=>11*$a+1
						);
						$this->ligneFacture[$a]['id_facture_ligne'] = ATF::facture_ligne()->i($this->ligneFacture[$a]);
					}
				}

			break;
			case "prolongation":
				$this->prolongation = array(
					"id_affaire"=>$this->affaire['id_affaire']
					,"ref"=>"REFPROLON"
					,"id_refinanceur"=>2 // KBC
					,"date_debut"=>"2011-02-24"
					,"date_fin"=>"2011-09-24"
					,"id_societe"=>$this->id_societe
				);
				$this->prolongation['id_prolongation'] = ATF::prolongation()->i($this->prolongation,ATF::_s());

				if ($this->affaire) {
					for ($a=0; $a<5; $a++) {
						$this->loyerProlongation[$a] = array(
							"id_affaire"=>$this->affaire['id_affaire']
							,"id_prolongation"=>$this->prolongation['id_prolongation']
							,"loyer"=>50*$a+0.5
							,"duree"=>2*$a+1
							,"assurance"=>10*$a+1
							,"frais_de_gestion"=>10*$a+1
							,"frequence_loyer"=>($a%3?"mois":($a%2?"trimestre":"an"))
							,"date_debut"=>"2011-02-24"
							,"date_fin"=>"2011-09-24"
						);
						$this->loyerProlongation[$a]['id_loyer'] = ATF::loyer_prolongation()->i($this->loyerProlongation[$a]);
					}
				}

			break;
			case "lignesFacturation":
				// Lignes de Facturation
					$date="2011-09-24";
					for ($a=1; $a<11; $a++) {
						$id_societe=ATF::societe()->i(array("societe"=>"TU-".$a,"code_client"=>"TU-".(100-$a)));
						$this->lignesFacturation[$a] = array(
							"id_affaire"=>$this->affaire['id_affaire']
							,"id_societe"=>$id_societe
							,"id_facture"=>$this->facture['id_facture']
							,"montant"=>$a*20.2
							,"frais_de_gestion"=>$a*2.3
							,"assurance"=>$a*4.7
							,"date_periode_debut"=>"2011-02-24"
							,"type"=>$a%2?"contrat":"prolongation"
							,"date_periode_fin"=>$date
						);
						$date=(date("Y-m-d",strtotime($date." + 1 day")));
						$this->lignesFacturation[$a]['id_facturation'] = ATF::facturation()->i($this->lignesFacturation[$a]);

						if($a<4){
							$cause="an";
						}elseif($a<6){
							$cause="pc";
						}else{
							$cause="pi";
						}
						$this->lignesFacturation[$a]["cause"]=$cause;
					}
			break;
			case "factureLibre":
				if ($this->commande) {
					$this->facture = array(
						"id_affaire"=>$this->affaire['id_affaire']
						,"id_societe"=>$this->id_societe
						,"prix"=>20000
						,"type_facture" => "libre"
						,"type_libre" => "retard"
						,"date"=>"2011-02-24"
						,"id_commande"=>$this->commande['id_commande']
						,"ref"=>"REFFACCLEODIS"
						,"tva"=>"1.196"
						,"date_periode_debut"=>"2011-02-24"
						,"date_periode_fin"=>"2011-09-24"
						,"date_previsionnelle"=>"2050-01-01"
						,"commentaire"=>"Commentaire"
						,"mode_paiement"=>"virement"
					);
					$this->facture['id_facture'] = ATF::facture()->i($this->facture,ATF::_s());
					for ($a=0; $a<3; $a++) {
						$this->ligneFacture[$a] = array(
							"ref"=>"REF".$a
							,"produit"=>"Produit TU n°".$a
							,"quantite"=>2*$a+1
							,"id_facture"=>$this->facture['id_facture']
							,"prix_achat"=>11*$a+1
						);
						$this->ligneFacture[$a]['id_facture_ligne'] = ATF::facture_ligne()->i($this->ligneFacture[$a]);
					}
				}

			break;


			case "factureMidas":
				if ($this->commande) {
					$this->facture = array(
						"id_affaire"=>$this->affaire['id_affaire']
						,"id_societe"=>$this->id_societe
						,"prix"=>20000
						,"type_facture" => "midas"
						,"date"=>"2011-02-24"
						,"id_commande"=>$this->commande['id_commande']
						,"ref"=>"REFFACCLEODIS"
						,"tva"=>"1.196"
						,"date_periode_debut"=>"2011-02-24"
						,"date_periode_fin"=>"2011-09-24"
						,"date_previsionnelle"=>"2050-01-01"
						,"commentaire"=>"Commentaire"
						,"mode_paiement"=>"virement"
					);
					$this->facture['id_facture'] = ATF::facture()->i($this->facture,ATF::_s());
				}

			break;

		}

	}

	private function setSocieteParticulier(){

        ATF::societe()->u(array("id_societe"=> $this->id_societe,
        				        "id_famille"=>9,
        				        "societe" => "Nom particulier Prénom particulier",
        				        "particulier_civilite"=>"M",
        				        "particulier_nom" => "Nom particulier",
        				        "particulier_prenom" => "Préom particulier",
        				        "particulier_portable" => "06 01 02 03 04",
        				        "particulier_fixe" => "03 01 02 03 04",
        				        "particulier_fax" => "04 01 02 03 04",
        				        "particulier_email" => "email@particulier.fr"
    					));
	}

	private function beginTransaction($codename, $begin, $commit){
		if($begin){
			ATF::db()->select_db("optima_".$codename);
	    	ATF::$codename = $codename;
	    	ATF::db()->begin_transaction(true);
		}

		if($commit){
			ATF::db()->rollback_transaction(true);
	        ATF::$codename = "cleodis";
	        ATF::db()->select_db("optima_cleodis");
		}

	}


	// @author Quentin JANON <qjanon@absystech.fr>
    // @author Yann GAUTHERON <ygautheron@absystech.fr>
    public function test_factureSimple() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureNormale");

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->facture ,"Erreur : le facture n'est pas initialisé");
        $this->assertNotNull($this->obj->lignes,"Erreur : le lignes n'est pas initialisé");
        $this->assertNotNull($this->obj->client ,"Erreur : le client n'est pas initialisé");
        $this->assertNotNull($this->obj->affaire ,"Erreur : le affaire n'est pas initialisé");
        $this->assertNotNull($this->obj->devis,"Erreur : le devis n'est pas initialisé");
        $this->assertNotNull($this->obj->user,"Erreur : le user n'est pas initialisé");
        $this->assertNotNull($this->obj->agence ,"Erreur : le agence n'est pas initialisé");
        $this->assertNotNull($this->obj->societe,"Erreur : le societe n'est pas initialisé");
        $this->assertNotNull($this->obj->contrat  ,"Erreur : le contrat n'est pas initialisé");

        $this->assertNotNull($this->obj->colsProduit  ,"Erreur : le colsProduit n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduitAlignLeft ,"Erreur : le colsProduitAlignLeft n'est pas initialisé");
        $this->assertNotNull($this->obj->styleDetailsProduit ,"Erreur : le styleDetailsProduit n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureSimple-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("1a814260bfd2b550fdceea287c728ed2",$md5,"Erreur de génération de la facture");


        $this->facture['prix'] = -10;
        ATF::facture()->u($this->facture);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureAvoir-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("0e0224582c3418d3c492d0e89773a500",$md5,"Erreur de génération de la facture AVOIR");

        $this->affaire['nature'] = "vente";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("f7edcec480addae6ab2dfc2981806a1c",$md5,"Erreur de génération de la facture VENTE");


    	$this->facture["type_libre"] = "normale";
        $this->facture['mode_paiement'] = "cheque";
        $this->facture['prix'] = 100;
        ATF::facture()->u($this->facture);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureVenteCheque-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("3ddaedb2f82777c825bbcca1e1454c73",$md5,"Erreur de génération de la facture AVOIR cheque");

        $this->facture['mode_paiement'] = "virement";
        $this->facture['prix'] = 100;
        ATF::facture()->u($this->facture);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureVenteVirement-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6175b026db08a9524b77e30d2fb3e1fe",$md5,"Erreur de génération de la facture AVOIR Virement");




        ATF::refinanceur()->u(array("id_refinanceur"=>2, "code_refi"=>"REFACTURATION"));
        $this->create("demande_refi");
        $this->demande_refi["etat"] = "valide";
        ATF::demande_refi()->u($this->demande_refi);
        $this->affaire["commentaire_facture"] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureAvecCommentaire-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("b9d849fb4421664ecf35e05045ea16ac",$md5,"Erreur de génération de la factureAvecCommentaire");
    }


    public function factureSimpleModePaiement(){
    	$this->create("commande");
        $this->create("demande_refi");
        $this->create("factureNormale");

    	$this->facture['mode_paiement'] = "pre-paiement";
        $this->facture['prix'] = 100;
        ATF::facture()->u($this->facture);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureVentePrePaiement-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("54f955f46c026fa218fdb4e68e7e418b",$md5,"Erreur de génération de la factureVentePrePaiement");


        $this->facture["type_libre"] = "liberatoire";
        $this->facture['mode_paiement'] = "cb";
        $this->facture['prix'] = 100;
        ATF::facture()->u($this->facture);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureLiberatoireCB-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ed58c9b4b00e07d05c847aecaf1a91b4",$md5,"Erreur de génération de la factureLiberatoireCB");
    }

	public function test_contratA4Particulier() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->setSocieteParticulier();

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4Particulier-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("bfb683fee118ca3e6a8a755767e2aaf5",$md5,"Erreur de génération de la commande");
    }

    public function test_contratA4Particulier2SI() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $id_affaire = ATF::commande()->select($this->commande['id_commande'], "id_affaire");
        ATF::affaire()->u(array("id_affaire"=> $id_affaire, "type_affaire"=> "2SI"));

        $id_societe = ATF::commande()->select($this->commande['id_commande'], "id_societe");
        ATF::societe()->u(array("id_societe"=> $id_societe,
                                "id_famille"=>9,
                                "societe" => "Nom particulier Prénom particulier",
                                "particulier_civilite"=>"M",
                                "particulier_nom" => "Nom particulier",
                                "particulier_prenom" => "Prénom particulier",
                                "particulier_portable" => "06 01 02 03 04",
                                "particulier_fixe" => "03 01 02 03 04",
                                "particulier_fax" => "04 01 02 03 04",
                                "particulier_email" => "email@particulier.fr"
                        ));

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4Particulier2SI-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a68048f2a7b7e8451da11ea12aa972a9",$md5,"Erreur de génération de la commande");
    }


     /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA4ParticulierPresta() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");
        ATF::devis()->u(array("id_devis"=>$this->devis['id_devis'], "type_contrat"=>"presta"));

        $societe = ATF::societe()->select($this->id_societe);

        $id_societe = ATF::commande()->select($this->commande['id_commande'], "id_societe");
        ATF::societe()->u(array("id_societe"=> $id_societe,
                                "id_famille"=>9,
                                "societe" => "Nom particulier Prénom particulier",
                                "particulier_civilite"=>"M",
                                "particulier_nom" => "Nom particulier",
                                "particulier_prenom" => "Préom particulier",
                                "particulier_portable" => "06 01 02 03 04",
                                "particulier_fixe" => "03 01 02 03 04",
                                "particulier_fax" => "04 01 02 03 04",
                                "particulier_email" => "email@particulier.fr",
                                'siren' => "SIRENTU"
                        ));

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ParticulierPresta-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("5da5af60af354b2458705b8497ddf5da",$md5,"Erreur de génération de la commande");
    }
 /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4ParticulierAR() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->societe['id_pays'] = "BE";
        ATF::societe()->u($this->societe);

        $this->setSocieteParticulier();

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ParticulierAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("5acb04598d93b2f3712622264ac186bf",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4ParticulierAvenant() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->setSocieteParticulier();

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ParticulierAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("140d1e7f2dc9658522e0f79d88e344b2",$md5,"Erreur de génération de la commande");
    }

    public function test_contratA42SI() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $id_affaire = ATF::commande()->select($this->commande['id_commande'], "id_affaire");
        ATF::affaire()->u(array("id_affaire"=> $id_affaire, "type_affaire"=> "2SI"));

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA42SI-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2b93920e47d280d84f8c6d43ac610bde",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 29/11/2016
    */
    public function test_mandatSellAndSign(){
        $this->create("commande");



        $this->obj->generic("mandatSellAndSign",$this->affaire['id_affaire'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-mandatSellAndSign-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("f5f6a0905659ef3b4fcbd8ccb9b4e2c7",$md5,"Erreur de génération du mandatSellAndSign");
    }



    public function test_noticeAssurance(){
    	$this->obj->generic("noticeAssurance",null,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-noticeAssurance-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("f095533731958f1e4b3a1d30109116d4",$md5,"PDF de notice assurance a changé ?");
    }





     /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_FooterA4() {
        $this->obj->unsetHeader();
        $this->obj->unsetFooter();
        $this->assertFalse($this->obj->Footer(),"Erreur, le footer est unsetté donc ca doit retourner FALSE");
        $this->obj->setFooter();
        $this->assertFalse($this->obj->Footer(),"Erreur, Pas de société dans l'objet donc ca doit retourner FALSE");

        $this->obj->societe = $this->societe;
        $this->obj->addpage();
        $this->obj->setFont('arial','',10);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."FooterA4-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("c4f81b1d6d0a64d27c6225b557b37640",$md5,"Erreur de génération du Footer A4 Cléodis, auraient-ils été modifié ?");
    }

    public function test_FooterA4_cleodisBE(){
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();
        $c->societe = $this->societe;
        $c->addpage();
        $c->setFont('arial','',10);
        $c->Close();
        $c->Output($this->dirSavedPDF."FooterA4-CLEODISBE-".$this->dateSave.".pdf");
        $c->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("bb866c64f72d997e1e3b1155aef24b4f",$md5,"Erreur de génération du Footer A4 Cléodis BE, auraient-ils été modifié ?");
    }




    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_FooterA3() {
        $format=array(841.89,1190.55);
        $this->obj->fwPt=$format[0];
        $this->obj->fhPt=$format[1];
        $this->obj->DefOrientation='L';
        $this->obj->wPt=$this->obj->fhPt;
        $this->obj->hPt=$this->obj->fwPt;
        $this->obj->CurOrientation=$this->obj->DefOrientation;
        $this->obj->w=$this->obj->wPt/$this->obj->k;
        $this->obj->h=$this->obj->hPt/$this->obj->k;
        $this->obj->societe = $this->societe;
        $this->obj->A3 = true;
        $this->obj->unsetHeader();
        $this->obj->setFooter();
        $this->obj->addpage();
        $this->obj->setFont('arial','',10);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."FooterA3-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("34a1b676147f040c78260cad7bea9e93",$md5,"Erreur de génération du Footer A3 Cléodis, auraient-ils été modifié ?");

    }

    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 17-11-2016
    */
    public function test_footerA3_cleodisBE(){
        $this->beginTransaction("cleodisbe", true, false);
        $format=array(841.89,1190.55);
        $c = new pdf_cleodisbe();
        $c->fwPt=$format[0];
        $c->fhPt=$format[1];
        $c->DefOrientation='L';
        $c->wPt=$c->fhPt;
        $c->hPt=$c->fwPt;
        $c->CurOrientation=$c->DefOrientation;
        $c->w=$c->wPt/$c->k;
        $c->h=$c->hPt/$c->k;
        $c->societe = $this->societe;
        $c->A3 = true;
        $c->unsetHeader();
        $c->setFooter();
        $c->addpage();
        $c->setFont('arial','',10);
        $c->Close();
        $c->Output($this->dirSavedPDF."FooterA3-CLEODISBE-".$this->dateSave.".pdf");
        $c->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("55aaa62cd0678cc046bbf68294500692",$md5,"Erreur de génération du Footer A3 Cléodis BE, auraient-ils été modifié ?");
    }


   /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_HeaderA4() {
        $this->obj->unsetFooter();
        $this->obj->unsetHeader();
        $this->assertFalse($this->obj->Header(),"Erreur, le Header est unsetté donc ca doit retourner FALSE");
        $this->obj->setHeader();

        $this->obj->addpage();
        $this->obj->setFont('arial','',10);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."HeaderA4-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("5f07ac3f95ef60482880815cbe57b04e",$md5,"Erreur de génération du Header A4 Cléodis, auraient-ils été modifié ?");



       $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();
        $c->unsetFooter();
        $c->unsetHeader();
        $this->assertFalse($c->Header(),"Erreur, le Header est unsetté donc ca doit retourner FALSE");
        $c->setHeader();

        $c->addpage();
        $c->setFont('arial','',10);
        $c->Close();
        $c->Output($this->dirSavedPDF."HeaderA4-CLEODISBE-".$this->dateSave.".pdf");
        $c->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("a64b4c70406c7a439421ddbe9317566f",$md5,"Erreur de génération du Header A4 Cléodis BE, auraient-ils été modifié ?");


    }

    public function test_HeaderA42Si(){

        $this->obj->relance = true;
        $this->obj->logo = "cleodis/2SI_CLEODIS.jpg";

        $this->obj->unsetFooter();
        $this->obj->unsetHeader();
        $this->assertFalse($this->obj->Header(),"Erreur, le Header est unsetté donc ca doit retourner FALSE");
        $this->obj->setHeader();

        $this->obj->addpage();



        $this->obj->setFont('arial','',10);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."HeaderA42SI-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2596a9fa8c39d39b24a82a91125ad622",$md5,"Erreur de génération du Header A4 Cléodis 2SI?");
    }


     public function test_HeaderA4Devis() {

        $this->obj->pdf_devis = true;

        $this->obj->unsetFooter();
        $this->obj->unsetHeader();
        $this->assertFalse($this->obj->Header(),"Erreur, le Header est unsetté donc ca doit retourner FALSE");
        $this->obj->setHeader();

        $this->obj->addpage();
        $this->obj->setFont('arial','',10);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."HeaderA4Devis-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d2ae636262e04761765cd4e8950408ee",$md5,"Erreur de génération du Header A4 Cléodis, auraient-ils été modifié ?");

    }




    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_HeaderA3() {
        $format=array(841.89,1190.55);
        $this->obj->fwPt=$format[0];
        $this->obj->fhPt=$format[1];
        $this->obj->DefOrientation='L';
        $this->obj->wPt=$this->obj->fhPt;
        $this->obj->hPt=$this->obj->fwPt;
        $this->obj->CurOrientation=$this->obj->DefOrientation;
        $this->obj->w=$this->obj->wPt/$this->obj->k;
        $this->obj->h=$this->obj->hPt/$this->obj->k;
        $this->obj->A3 = true;
        $this->obj->unsetFooter();
        $this->obj->setHeader();
        $this->obj->addpage();
        $this->obj->setFont('arial','',10);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."HeaderA3-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("00b3147ab9e30c2e0d449dc0417e4b57",$md5,"Erreur de génération du Header A3 Cléodis, auraient-ils été modifié ?");

    }



    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
     public function test_FooterPrevisu() {
        $this->obj->setFooter();
        $this->obj->previsu = true;
        $this->obj->societe = true;
        $this->obj->addpage();
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."FooterPrevisu-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2cd9e58683e39d3898fe95ef6cf1af41",$md5,"Erreur de génération du Footer PREVISU Cléodis");

    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_article() {
        $this->obj->unsetFooter();
        $this->obj->unsetHeader();

        $this->obj->open();
        $this->obj->addpage();
        $this->obj->setFont('arial','',10);
        $this->obj->article(10,10,"3.2.1","Titre number One",12);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."article-".$this->dateSave.".pdf");
        $this->obj->Output($this->tmpFile);
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("acf5157031db928bf8bc8e6cd9b50c36",$md5,"Erreur de génération des articles Cléodis");
    }


   /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisInit() {
        $this->obj->devis($this->devis['id_devis']);
        $this->devis['type_contrat'] = "vente";
        ATF::devis()->u($this->devis);
        $this->obj->devis($this->devis['id_devis']);

        $this->assertNotNull($this->obj->devis,"Erreur : le devis n'est pas initialisé");
        $this->assertNotNull($this->obj->loyer ,"Erreur : le loyer n'est pas initialisé");
        $this->assertNotNull($this->obj->lignes ,"Erreur : le lignes n'est pas initialisé");
        $this->assertNotNull($this->obj->user,"Erreur : le user n'est pas initialisé");
        $this->assertNotNull($this->obj->societe ,"Erreur : le societe n'est pas initialisé");
        $this->assertNotNull($this->obj->client ,"Erreur : le client n'est pas initialisé");
        $this->assertNotNull($this->obj->contact ,"Erreur : le devcontactis n'est pas initialisé");
        $this->assertNotNull($this->obj->affaire  ,"Erreur : le affaire n'est pas initialisé");
        $this->assertNotNull($this->obj->agence  ,"Erreur : le agence n'est pas initialisé");

    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisSimple() {
        ATF::loyer()->q->reset()->where("id_affaire", $this->devis['id_affaire']);
        $loyers = ATF::loyer()->select_all();

        ATF::loyer()->d($loyers[4]["id_loyer"]);
        ATF::loyer()->d($loyers[3]["id_loyer"]);
        ATF::loyer()->u(array("id_loyer"=>$loyers[2]["id_loyer"] , "duree"=> 24));

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisSimple-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d30114cf091ba9a60bbb7f1ad17590e3",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 29-04-2014
    */
   public function test_devis_optic2000_avenant() {
        $this->create("devis_optic2000_avenant");
        $this->obj->generic("devis",$this->devis_optic2000_avenant['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devis_optic2000_avenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2c8a7f4d2721288f7bc151bf6ee888db",$md5,"Erreur de génération du devis optic 2000 Avenant");
    }

     /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 29-04-2014
    */
    public function test_devis_optic2000_AR() {
        $this->create("devis_optic2000_AR");
        $this->obj->generic("devis",$this->devis_optic2000_AR['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devis_optic2000_AR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ed084c06cf447969d15b2a04d5f7fe09",$md5,"Erreur de génération du devis optic 2000 AR");
    }
    public function test_devis_optic2000_AR_mensuel() {
        $this->create("devis_optic2000_AR");
        //Loyer mensuel
        ATF::loyer()->u(array("id_loyer"=>$this->loyer[0]['id_loyer'], "frequence_loyer" => "mois"));
        $this->obj->generic("devis",$this->devis_optic2000_AR['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devis_optic2000_AR_Mensuel-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("29d5deed86918082106a4cbb773d9ac6",$md5,"Erreur de génération du devis optic 2000 AR");
       }
    public function test_devis_optic2000_AR_trimestriel() {
        $this->create("devis_optic2000_AR");
        //Loyer trimestriel
        ATF::loyer()->u(array("id_loyer"=>$this->loyer[0]['id_loyer'], "frequence_loyer" => "trimestre"));
        $this->obj->generic("devis",$this->devis_optic2000_AR['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devis_optic2000_AR_Trimestre-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("49d07ea9cd99c7aed7ee990d0811565d",$md5,"Erreur de génération du devis optic 2000 AR");
    }

    public function test_devis_optic2000_AR_semestriel() {
        $this->create("devis_optic2000_AR");
        //Loyer trimestriel
        ATF::loyer()->u(array("id_loyer"=>$this->loyer[0]['id_loyer'], "frequence_loyer" => "semestre"));
        $this->obj->generic("devis",$this->devis_optic2000_AR['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devis_optic2000_AR_Semestre-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("383cb10c47af2f90e3f86381043659b7",$md5,"Erreur de génération du devis optic 2000 AR");
    }


    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisSimplePrixInvisible() {
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            ATF::devis_ligne()->u($i);
        }
        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisSimplePrixInvisible-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("c7f5cd0bcdba789c5455f20874439abd",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisAvenant() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::devis_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("875984ae75ef37ad65293a0f592de7a2",$md5,"Erreur de génération du devis");
    }








  /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4ParticulierAffaireVente() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->setSocieteParticulier();

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "vente";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ParticulierAffaireVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("b96cd2f67235d1c3acb5ec8a3a97ff29",$md5,"Erreur de génération de la commande");
    }
  /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4ParticulierAvecAnnexe() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->setSocieteParticulier();

        for ($c=0;$c<10;$c++) {
            foreach ($this->ligneCommande as $k=>$i) {
                unset($i['id_commande_ligne']);
                ATF::commande_ligne()->i($i);
            }
        }

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ParticulierAvecAnnexe-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("3a200331904d05de6619567850bbc285",$md5,"Erreur de génération de la commande");
    }

      /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4ParticulierLoyerUnique() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->devis['loyer_unique'] = "oui";
        ATF::devis()->u($this->devis);

        $this->setSocieteParticulier();

        foreach ($this->loyer as $k=>$i) {
            if (!$k) continue;
            ATF::loyer()->delete($i['id_loyer']);
        }

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ParticulierLoyerUnique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("bd452d9eb78b6480ff7ccf8cc23e326f",$md5,"Erreur de génération de la commande");

        foreach ($this->loyer as $k=>$i) {
            if (!$k){
                $i["loyer"]="0";
                $i["assurance"]=NULL;
                $i["frais_de_gestion"]=NULL;
                ATF::loyer()->u($i);
            }
        }
        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ParticulierLoyerUniqueZ-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("3cd643a439337463633ebc406f64ebc6",$md5,"Erreur de génération de la commande 2" );

    }


    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratParticulierPVAR() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->setSocieteParticulier();

        $this->obj->generic("contratPV",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratParticulierPVAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a6adce88de3b5dfec197ecbc9d800240",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratParticulierPVARANNEXES() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");
        $this->setSocieteParticulier();

        for ($c=0;$c<40;$c++) {
            foreach ($this->ligneCommande as $k=>$i) {
                unset($i['id_commande_ligne']);
                ATF::commande_ligne()->i($i);
            }
        }

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratPV",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratParticulierPVARANNEXES-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("df31bc3ddcc068df17da66452edcf32d",$md5,"Erreur de génération de la contratPVARANNEXES");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratParticulierPVAvenant() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");
        $this->setSocieteParticulier();

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratPV",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratParticulierPVAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d5d76293e6bf81a95c4e8ecaa3bdfe38",$md5,"Erreur de génération de la commande");


        $this->obj->generic("contratPVSignature",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratParticulierPVSignatureAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("7fd8e8fbfdea796ef61628eb0c2b19a5",$md5,"Erreur de génération de la commande");


    }


 /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisAvenant39Mois() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");

        $loyer = ATF::loyer()->ss('id_affaire',$this->devis['id_affaire']);
        foreach ($loyer as $key => $value) {
            if($key == 0){
                ATF::loyer()->u(array("id_loyer"=>$value["id_loyer"] , "duree"=> 39, "frequence_loyer"=>"mois"));
            }else{
                ATF::loyer()->d($value["id_loyer"]);
            }
        }

        foreach ($this->ligneDevis as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::devis_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvenant39Mois-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("7ba77fa0a1773457a2142fc939b52a2e",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisAvenantPrixInvisible() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
            }
            ATF::devis_ligne()->u($i);
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvenantPrixInvisible-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("32d904a0c96221c4c6c7f216fd1abee2",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisAvenantLoyerUnique() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
            }
            ATF::devis_ligne()->u($i);
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        $this->devis['loyer_unique'] = "oui";
        ATF::devis()->u($this->devis);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvenantLoyerUnique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("0d1d8d80a34908ac9102cf4c0fbc849a",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisAvenantFrequenceM() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
            }
            ATF::devis_ligne()->u($i);
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        $this->loyer[0]['frequence_loyer'] = "mois";
        ATF::loyer()->u($this->loyer[0]);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvenantFrequenceM-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("dd9f35e7f050d8ac3cf75d572e104d1f",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisAvenantFrequenceT() {
        ATF::affaire()->q->reset()
            ->addField("id_affaire")
            ->setLimit(1)
            ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
            }
            ATF::devis_ligne()->u($i);
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        $this->loyer[0]['frequence_loyer'] = "trimestre";
        ATF::loyer()->u($this->loyer[0]);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvenantFrequenceT-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("5372de84a24b71c583c7b8a3fbbb34f2",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisAvenantAnnexes() {
        ATF::affaire()->q->reset()
            ->addField("id_affaire")
            ->setLimit(1)
            ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
            }
            ATF::devis_ligne()->u($i);
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        for ($c=0;$c<10;$c++) {
            foreach ($this->ligneDevis as $k=>$i) {
                if (!$c) {
                    $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                }
                unset($i['id_devis_ligne']);
                ATF::devis_ligne()->i($i);
            }
        }

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvenantAnnexes-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ef06e2db8f90262346893b7c099ad3d7",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisAR() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::devis_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("0e78e9643f10b69efb5e4519e8f60a74",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisARPrixInvisible() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
            }
            ATF::devis_ligne()->u($i);
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisARPrixInvisible-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("94187024b1bc8e8914d4055704c7d070",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisAffaireVente() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::devis_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "vente";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAffaireVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("78fc0bd2a9a049786e6e6e0b42d69175",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisAffaireVentePrixInvisible() {
        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneDevis as $k=>$i) {
            $i['visibilite_prix'] = "invisible";
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
            }
            ATF::devis_ligne()->u($i);
        }

        $this->affaire['nature'] = "vente";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAffaireVentePrixInvisible-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("29a54f19db7921576189033360d246ae",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisLoyerUnique() {
        $this->devis['loyer_unique'] = "oui";
        ATF::devis()->u($this->devis);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisLoyerUnique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("74894ce8effec3c6754505a434e1b22f",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisFrequenceM() {
        $this->loyer[0]['frequence_loyer'] = "mois";
        ATF::loyer()->u($this->loyer[0]);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisFrequenceM-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("1987fbb2d71605d2028b4dacd4761b03",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisFrequenceT() {
        $this->loyer[0]['frequence_loyer'] = "trimestre";
        ATF::loyer()->u($this->loyer[0]);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisFrequenceT-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d1171b1afde1b8d0b720d8ecb3087a73",$md5,"Erreur de génération du devis");
    }

/*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 24-02-2011
    */
    public function test_devisAvecAnnexe() {
        ATF::affaire()->q->reset()->addField("id_affaire")->setLimit(1)->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        for ($c=0;$c<10;$c++) {
            foreach ($this->ligneDevis as $k=>$i) {
                if (!$c) {
                    $i['id_affaire_provenance'] = $id_affaire;
                    $i["neuf"] = "non";
                }
                unset($i['id_devis_ligne']);
                ATF::devis_ligne()->i($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisAvecAnnexe-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("82d2a5507e4b2cef77cbb2b79c2a34a6",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 08-10-2015
    */
    public function test_detailsProduit(){
        $this->assertEquals("Commentaire : Commentaire sup - Deuxième, troisième ou quatrième bac supérieur à 530 feuilles - Meuble cabinet roulette",$this->obj->detailsProduit(10055,NULL,"Commentaire sup"),"Detail Produit Incorrect");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisVente() {
        $this->devis['type_contrat'] = "vente";
        ATF::devis()->u($this->devis);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("0f57950969a98514b3c2b618499f8450",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_devisVenteAvecAnnexe() {
        ATF::affaire()->q->reset()->addField("id_affaire")->setLimit(1)->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        for ($c=0;$c<10;$c++) {
            foreach ($this->ligneDevis as $k=>$i) {
                if (!$c) {
                    $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                }
                unset($i['id_devis_ligne']);
                ATF::devis_ligne()->i($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->devis['prix'] = 1800.45;
        $this->devis['type_contrat'] = "vente";
        $this->devis['tva'] = 1;
        ATF::devis()->u($this->devis);

        $this->obj->generic("devis",$this->devis['id_devis'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-devisVenteAvecAnnexe-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2826893027bd7f8c911ff8f73cc2be39",$md5,"Erreur de génération du devis");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_commandeInit() {
        $this->create("commande");
        ATF::affaire()->q->reset()->addField("id_affaire")->setLimit(1)->setDimension('cell');
        $affaire = array(
            "id_affaire"=>ATF::affaire()->sa()
            ,"id_fille"=>$this->affaire['id_affaire']
        );

        ATF::affaire()->u($affaire);
        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->devis['loyer_unique'] = "oui";
        ATF::devis()->u($this->devis);

        foreach ($this->loyer as $k=>$i) {
            if (!$k) continue;
            ATF::loyer()->delete($i['id_loyer']);
        }

        $this->obj->contratA3($this->commande['id_commande']);

        //Vérifie l'existence des styles
        $this->assertNotNull($this->obj->colsProduit ,"Erreur : le colsProduit n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduitFirst  ,"Erreur : le colsProduitFirst n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduitLast  ,"Erreur : le colsProduitLast n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduitAvecDetail ,"Erreur : le colsProduitAvecDetail n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduitAvecDetailFirst  ,"Erreur : le colsProduitAvecDetailFirst n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduitAvecDetailLast  ,"Erreur : le colsProduitAvecDetailLast n'est pas initialisé");
        $this->assertNotNull($this->obj->styleDetailsProduit  ,"Erreur : le styleDetailsProduit n'est pas initialisé");

        //Vérifie l'existence des variables
        $this->assertNotNull($this->obj->commande,"Erreur : le commande n'est pas initialisé");
        $this->assertNotNull($this->obj->devis,"Erreur : le devis n'est pas initialisé");
        $this->assertNotNull($this->obj->loyer ,"Erreur : le loyer n'est pas initialisé");
        $this->assertNotNull($this->obj->loyer['loyer'] ,"Erreur : le loyer n'est pas initialisé");
        $this->assertNotNull($this->obj->lignes ,"Erreur : le lignes n'est pas initialisé");
        $this->assertNotNull($this->obj->user,"Erreur : le user n'est pas initialisé");
        $this->assertNotNull($this->obj->societe ,"Erreur : le societe n'est pas initialisé");
        $this->assertNotNull($this->obj->client ,"Erreur : le client n'est pas initialisé");
        $this->assertNotNull($this->obj->affaire  ,"Erreur : le affaire n'est pas initialisé");
        $this->assertNotNull($this->obj->AR  ,"Erreur : le AR n'est pas initialisé");

    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA3Simple() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $societe = ATF::societe()->select($this->id_societe);
        $societe['siren'] = "SIRENTU";
        ATF::societe()->u($societe);

        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3Simple-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("630f8e8715bdea3b815e3ed718ac19e2",$md5,"Erreur de génération de la commande");
    }

     /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA3Presta() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");
        ATF::devis()->u(array("id_devis"=>$this->devis['id_devis'], "type_contrat"=>"presta"));

        $societe = ATF::societe()->select($this->id_societe);
        $societe['siren'] = "SIRENTU";
        ATF::societe()->u($societe);

        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3Presta-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("4a5c49a81fd06b3ae74bed202e8946a7",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA3AvecAnnexes() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()->addField("id_affaire")->setLimit(1)->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        for ($c=0;$c<30;$c++) {
            foreach ($this->ligneCommande as $k=>$i) {
                if (!$c) {
                    $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                }
                unset($i['id_commande_ligne']);
                ATF::commande_ligne()->i($i);
            }
        }


        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3AvecAnnexes-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("64cb978b2ecb668ccac99ed885e1a772",$md5,"Erreur de génération de la contratA3AvecAnnexes");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA3ClauseVente() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        // Ajout adresse 2 et 3 a la societe et on la met autre que FR
        $this->societe['adresse_2'] = "Adresse 2";
        $this->societe['adresse_3'] = "Adresse 3";
        $this->societe['id_pays'] = "BE";
        ATF::societe()->u($this->societe);
        // Ajout de la clause logicielle
        $this->commande['clause_logicielle'] = "oui";
        ATF::commande()->u($this->commande);
        // Devis de vente
        $this->devis['type_contrat'] = "vente";
        ATF::devis()->u($this->devis);

        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3ClauseVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("cd66c62fe60b63707476b1a83c729f00",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA3Avenant() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");

        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3Avenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2c54ffd61b9f00bc728715408927f17f",$md5,"Erreur de génération de la commande");

        $this->devis['loyer_unique'] = "oui";
        ATF::devis()->u($this->devis);

        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3AvenantLoyerUnique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ddc58fff285ae1589ba1f539d697a498",$md5,"Erreur de génération de la commande");

    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA3AR() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3AR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("5f2493628689eb7bf6489390e71ed3e2",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA3AffaireVente() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "vente";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3AffaireVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("7c4a45bf332110e0f6f4bf6f51072a97",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4Simple() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");


        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4Simple-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d50bea1daf7b937c28597094bfd7b1fb",$md5,"Erreur de génération de la commande");
    }


     /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA4Presta() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");
        ATF::devis()->u(array("id_devis"=>$this->devis['id_devis'], "type_contrat"=>"presta"));

        $societe = ATF::societe()->select($this->id_societe);
        $societe['siren'] = "SIRENTU";
        ATF::societe()->u($societe);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA3Presta-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("3cb4b400489bf6c19eb3ac8f8b487de2",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA4ClauseVente() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        // Ajout de la clause logicielle
        $this->commande['clause_logicielle'] = "oui";
        ATF::commande()->u($this->commande);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ClauseVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("41c15403b5d770452b68f302bdbda7e7",$md5,"Erreur de génération de la contratA4ClauseVente");
    }


    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4AR() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->societe['id_pays'] = "BE";
        ATF::societe()->u($this->societe);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4AR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("47ae20aa5dc7bc9454c051afffaab5d4",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4Avenant() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4Avenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("4616392fa071f6bf19a081cbe2a887e8",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4AffaireVente() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "vente";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4AffaireVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("c2fbe8192059621d367dcb613b8869fa",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4LoyerUnique() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->devis['loyer_unique'] = "oui";
        ATF::devis()->u($this->devis);

        foreach ($this->loyer as $k=>$i) {
            if (!$k) continue;
            ATF::loyer()->delete($i['id_loyer']);
        }

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4LoyerUnique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("175e0a4e444e0aed4608d0d108669bc9",$md5,"Erreur de génération de la commande");

        foreach ($this->loyer as $k=>$i) {
            if (!$k){
                $i["loyer"]="0";
                $i["assurance"]=NULL;
                $i["frais_de_gestion"]=NULL;
                ATF::loyer()->u($i);
            }
        }
        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4LoyerUniqueZ-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6901c361e69d23d380a36267e2d1e8af",$md5,"Erreur de génération de la commande 2" );

    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4AvecAnnexe() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        for ($c=0;$c<10;$c++) {
            foreach ($this->ligneCommande as $k=>$i) {
                unset($i['id_commande_ligne']);
                ATF::commande_ligne()->i($i);
            }
        }

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4AvecAnnexe-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("750db78430519f454435be1955674e46",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratPVAR() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratPV",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratPVAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("e69eae4afa49d86b363e02a4adeb403d",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratPVARANNEXES() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        for ($c=0;$c<40;$c++) {
            foreach ($this->ligneCommande as $k=>$i) {
                unset($i['id_commande_ligne']);
                ATF::commande_ligne()->i($i);
            }
        }

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratPV",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratPVARANNEXES-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("783d90d81be09a35575b7e4aa3ce7e95",$md5,"Erreur de génération de la contratPVARANNEXES");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratPVAvenant() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("contratPV",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratPVAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("316d60099a45083504310ed6f0bdc9d0",$md5,"Erreur de génération de la commande");


        $this->obj->generic("contratPVSignature",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratPVSignatureAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("316d60099a45083504310ed6f0bdc9d0",$md5,"Erreur de génération de la commande");


        $this->affaire['type_affaire'] = "2SI";
        ATF::affaire()->u($this->affaire);
        $this->obj->generic("contratPV",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratPV2SIAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("2f31edaf5721f8133db18f05f69325c1",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratAP() {
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->obj->generic("contratAP",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratAP-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("0dae519d12aebbc4fe7e1b6e441164f4",$md5,"Erreur de génération de la commande");
    }


    public function test_lettreSGEF(){
        $this->obj->generic("lettreSGEF",320,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-lettreSGEF-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->assertEquals("4876f0b5e39680f990fb82b9a176654f",$md5,"Erreur de génération de la commande");

        $id_affaire = ATF::commande()->select(320, "id_affaire");
        ATF::loyer()->q->reset()->where("id_affaire", $id_affaire);
        $loyer = ATF::loyer()->select_all();
        ATF::loyer()->u(array("id_loyer"=> $loyer[0]["id_loyer"], "frequence_loyer" => "semestre"));

        $this->obj->generic("lettreSGEF",320,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-lettreSGEF2-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_3 = system($this->MD5cmd);
        $md5_3 = substr($md5_3,0,32);
        ob_get_clean();
        $this->assertEquals("90bf9a97bbf4e62a536cedbf779d0854",$md5_3,"Erreur de génération de la lettreSGEF 2 CLEODIS");


    }

    public function test_lettreBelfius(){
        $this->beginTransaction("cleodisbe",true,false);

        ATF::_r("tu", "OK");

        $this->obj->generic("lettreBelfius",2208,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-lettreBelfius-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        ATF::societe()->u(array("id_societe"=>4225, "adresse_siege_social"=>NULL));

        $this->obj->generic("lettreBelfius",2208,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-lettreBelfius_sanssiege-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md52 = system($this->MD5cmd);
        $md52 = substr($md52,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodis",false,true);
        $this->assertEquals("fe5d054baa00beaf9baa105be20d85f9",$md5,"Erreur de génération de la lettre belfius");
        $this->assertEquals("fe5d054baa00beaf9baa105be20d85f9",$md52,"Erreur de génération de la lettre belfius sans siege");
    }



    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_initBDC() {
        $this->create("commande");
        $this->create("bdc");

        $this->obj->bon_de_commande($this->bdc['id_bdc']);

        //Vérifie l'existence des styles
        $this->assertNotNull($this->obj->styleLibAffaire  ,"Erreur : le styleLibAffaire n'est pas initialisé");
        $this->assertNotNull($this->obj->styleLibTotaux   ,"Erreur : le styleLibTotaux n'est pas initialisé");
        $this->assertNotNull($this->obj->styleTotaux   ,"Erreur : le styleTotaux n'est pas initialisé");
        $this->assertNotNull($this->obj->styleNotice  ,"Erreur : le styleNotice n'est pas initialisé");

        //Vérifie l'existence des variables
        $this->assertNotNull($this->obj->bdc ,"Erreur : le bdc n'est pas initialisé");
        $this->assertNotNull($this->obj->lignes ,"Erreur : le lignes n'est pas initialisé");
        $this->assertNotNull($this->obj->client  ,"Erreur : le client n'est pas initialisé");
        $this->assertNotNull($this->obj->user  ,"Erreur : le user n'est pas initialisé");
        $this->assertNotNull($this->obj->affaire  ,"Erreur : le affaire n'est pas initialisé");
        $this->assertNotNull($this->obj->societe ,"Erreur : le societe n'est pas initialisé");
        $this->assertNotNull($this->obj->fournisseur ,"Erreur : le fournisseur n'est pas initialisé");
//      $this->assertNotNull($this->obj->contact  ,"Erreur : le contact n'est pas initialisé");
        $this->assertNotNull($this->obj->bpa   ,"Erreur : le bpa n'est pas initialisé");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_BDC() {
        $this->create("commande");
        $this->create("bdc");
        ATF::bon_de_commande()->u(array("id_bon_de_commande"=> $this->bdc['id_bdc'] ,
                                        "commentaire" => "Zone de commentaire pour le bon de commande",
                                        'date_livraison_demande' => "2015-02-02",
                                        "date_installation_demande"=>"2015-02-10"));

        $this->obj->generic("bon_de_commande",$this->bdc['id_bdc'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-BDC-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("17e592a7095bc337d96588cc1d43c6e5",$md5,"Erreur de génération de la BDC");



        ATF::affaire()->u(array("id_affaire"=>$this->affaire["id_affaire"], "type_affaire"=> "2SI"));
        $this->obj->generic("bon_de_commande",$this->bdc['id_bdc'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-BDC2SI-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5_2,0,32);
        ob_get_clean();
        $this->assertEquals("ff9b1fa2651f0f8bd4d3542c9a0f7347",$md5_2,"Erreur de génération de la BDC");


    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_BDCAvecAnnexe() {
        $this->create("commande");
        $this->create("bdc");

        for ($c=0;$c<10;$c++) {
            foreach ($this->ligneBDC as $k=>$i) {
                unset($i['id_bon_de_commande_ligne']);
                ATF::bon_de_commande_ligne()->i($i);
            }
        }

        $this->obj->generic("bon_de_commande",$this->bdc['id_bdc'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-BDCAvecAnnexe-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("3c053696e7a54cf81dfd9317a51ba00c",$md5,"Erreur de génération de la BDCAvecAnnexe");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_demande_refi() {
        $this->create("commande");
        $this->create("demande_refi");

        $this->obj->demande_refi($this->demande_refi['id_demande_refi']);

        //Vérifie l'existence des variables
        $this->assertNotNull($this->obj->demandeRefi,"Erreur : le demandeRefi n'est pas initialisé");
        $this->assertNotNull($this->obj->affaire,"Erreur : le affaire n'est pas initialisé");
        $this->assertNotNull($this->obj->contrat,"Erreur : le contrat n'est pas initialisé");
        $this->assertNotNull($this->obj->refinanceur,"Erreur : le refinanceur n'est pas initialisé");
        $this->assertNotNull($this->obj->client,"Erreur : le client n'est pas initialisé");
        $this->assertNotNull($this->obj->devis,"Erreur : le devis n'est pas initialisé");
        $this->assertNotNull($this->obj->ligneDevis,"Erreur : le ligneDevis n'est pas initialisé");
        $this->assertNotNull($this->obj->user,"Erreur : le user n'est pas initialisé");
        $this->assertNotNull($this->obj->societe,"Erreur : le societe n'est pas initialisé");
        $this->assertNotNull($this->obj->loyer,"Erreur : le loyer n'est pas initialisé");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_demande_refiKBC() {
        $this->create("commande");
        $this->create("demande_refi");

        $this->societe['adresse_2'] = "adresse2";
        $this->societe['adresse_3'] = "adresse3";
        ATF::societe()->u($this->societe);

        $this->obj->generic("demande_refi",$this->demande_refi['id_demande_refi'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-demande_refiKBC-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6243109b43949258cebcd57f05eaeaad",$md5,"Erreur de génération de la demande_refiKBC");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_demande_refiBNP() {
        $this->create("commande");
        $this->create("demande_refi");

        $this->societe['adresse_2'] = "adresse2";
        $this->societe['adresse_3'] = "adresse3";
        ATF::societe()->u($this->societe);

        $this->demande_refi['id_refinanceur'] = 8;
        ATF::demande_refi()->u($this->demande_refi);

        $this->obj->generic("demande_refi",$this->demande_refi['id_demande_refi'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-demande_refiBNP-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a9777503784c7101fb7165430cc933b5",$md5,"Erreur de génération de la demande_refiBNP");
    }





    // @author Quentin JANON <qjanon@absystech.fr>
    // @author Yann GAUTHERON <ygautheron@absystech.fr>
    public function test_factureSimpleCleodisBE() {
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->insertSociete();
        $this->societe = ATF::societe()->select($this->id_societe);
        $this->insertContact();
        $this->contact = ATF::contact()->select($this->id_contact);

        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureNormale");

        $c->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);


        $c->Close();
        $c->Output($this->dirSavedPDF."-factureSimpleCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("7a3ce1828d38edab47e235c7a89f9185",$md5,"Erreur de génération de la facture");
    }

    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 28-02-2011
    */
    public function test_factureRefiCleodisBE() {
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->insertSociete();
        $this->societe = ATF::societe()->select($this->id_societe);
        $this->insertContact();
        $this->contact = ATF::contact()->select($this->id_contact);

        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureRefi");

        $c->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($c->demandeRefi ,"Erreur : le demandeRefi n'est pas initialisé");
        $this->assertNotNull($c->refinanceur ,"Erreur : le refinanceur n'est pas initialisé");

        $c->Close();
        $c->Output($this->dirSavedPDF."-factureRefiCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("b46ae132000b0bbdb0a5a1dc953198dd",$md5,"Erreur de génération de la facture");


    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_factureRefi() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureRefi");

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->demandeRefi ,"Erreur : le demandeRefi n'est pas initialisé");
        $this->assertNotNull($this->obj->refinanceur ,"Erreur : le refinanceur n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureRefi-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("1f3a497cc5918edfeae9c0ed48f5007f",$md5,"Erreur de génération de la facture");


        $this->affaire["type_affaire"] = "2SI";
        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureRefi2SI-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("1f3a497cc5918edfeae9c0ed48f5007f",$md5,"Erreur de génération de la factureRefi2SI");

        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureRefi");

        $c->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-factureRefiCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5_2,0,32);
        ob_get_clean();


        ATF::affaire()->u(array("id_affaire"=>$this->affaire["id_affaire"], "type_affaire"=>"2SI"));
        $c->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-factureRefi2SICleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_3 = system($this->MD5cmd);
        $md5_3 = substr($md5_3,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("3decb22c8a8ab505127651569c983d79",$md5_2,"Erreur de génération de la factureRefi CLEODIS BE");
        $this->assertEquals("3decb22c8a8ab505127651569c983d79",$md5_3,"Erreur de génération de la factureRefi2SICleodisBE CLEODIS BE");
    }




    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_factureRefiAR() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureRefi");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneFacture as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                ATF::facture_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->demandeRefi ,"Erreur : le demandeRefi n'est pas initialisé");
        $this->assertNotNull($this->obj->refinanceur ,"Erreur : le refinanceur n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureRefiAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a62222ef2eb84012d431deeb15102edd",$md5,"Erreur de génération de la facture");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_factureRefiAvenant() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureRefi");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneFacture as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                ATF::facture_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->demandeRefi ,"Erreur : le demandeRefi n'est pas initialisé");
        $this->assertNotNull($this->obj->refinanceur ,"Erreur : le refinanceur n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureRefiAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6dcf25cec8a752692c049e0a9f4b459a",$md5,"Erreur de génération de la facture");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_factureRefiVente() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureRefi");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneFacture as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                ATF::facture_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "vente";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->demandeRefi ,"Erreur : le demandeRefi n'est pas initialisé");
        $this->assertNotNull($this->obj->refinanceur ,"Erreur : le refinanceur n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureRefiVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a445ebaf8c87d9ee74a3f4c0296eb830",$md5,"Erreur de génération de la facture");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_factureRefiAvoir() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureRefi");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneFacture as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                ATF::facture_ligne()->u($i);
            }
        }

        $this->facture['prix'] = 0;
        ATF::facture()->u($this->facture);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->demandeRefi ,"Erreur : le demandeRefi n'est pas initialisé");
        $this->assertNotNull($this->obj->refinanceur ,"Erreur : le refinanceur n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureRefiAvoir-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("009fcac65ee8eb71aa95e828a8da69cd",$md5,"Erreur de génération de la facture Refi d'Avoir");
    }

//  /*
//  * @author Quentin JANON <qjanon@absystech.fr>
//  * @date 28-02-2011
//  */
//  public function test_factureAP() {
//      $this->create("commande");
//      $this->create("factureAP");
//
//      $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);
//      $this->obj->Close();
//      $this->obj->Output($this->dirSavedPDF."-factureAP-".$this->dateSave.".pdf");
//      ob_start();
//      // Commande SHELL pour générer le fichier
//      system($this->GScmd);
//      $md5 = system($this->MD5cmd);
//      $md5 = substr($md5,0,32);
//      ob_get_clean();
//      $this->assertEquals("98c5bfb7afca93a782a72348f6e18b8a",$md5,"Erreur de génération de la facture");
//  }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_prolongation() {
        $this->create("commande");
        $this->create("facture");
        $this->create("prolongation");
        $this->create("lignesFacturation");

        $this->obj->prolongation($this->prolongation['id_prolongation']);

        // Init Echeancier
        $this->assertNotNull($this->obj->affaire,"Erreur : Affaire non initialisée");
        $this->assertNotNull($this->obj->commande,"Erreur : commande non initialisée");
        $this->assertNotNull($this->obj->devis,"Erreur : devis non initialisée");
        $this->assertNotNull($this->obj->client,"Erreur : client non initialisée");
        $this->assertNotNull($this->obj->societe,"Erreur : societe non initialisée");
        $this->assertNotNull($this->obj->lignes,"Erreur : lignes non initialisée");

        // Special prolongation Init
        $this->assertNotNull($this->obj->duree,"Erreur : duree non initialisée");
        $this->assertNotNull($this->obj->prolongation,"Erreur : prolongation non initialisée");
        $this->assertNotNull($this->obj->dateExpiration,"Erreur : dateExpiration non initialisée");
        $this->assertNotNull($this->obj->dateDebut,"Erreur : dateExpiration non initialisée");
        $this->assertEquals("24/09/2011",$this->obj->dateExpiration,"Erreur : dateExpiration non initialisée");
        $this->assertEquals("24/02/2011",$this->obj->dateDebut,"Erreur : dateDebut non initialisée");
        $this->assertEquals("prolongation",$this->obj->type,"Erreur : type non initialisée en prolongation");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_affaire() {
        $this->create("commande");

        $this->commande['date_evolution'] = "2050-01-01";
        $this->commande['date_debut'] = "2049-01-01";
        ATF::commande()->u($this->commande);

        $this->create("facture");
        $this->create("lignesFacturation");

        $this->obj->affaire($this->affaire['id_affaire']);

        // Init Echeancier
        $this->assertNotNull($this->obj->affaire,"Erreur : Affaire non initialisée");
        $this->assertNotNull($this->obj->commande,"Erreur : commande non initialisée");
        $this->assertNotNull($this->obj->devis,"Erreur : devis non initialisée");
        $this->assertNotNull($this->obj->client,"Erreur : client non initialisée");
        $this->assertNotNull($this->obj->societe,"Erreur : societe non initialisée");
        $this->assertNotNull($this->obj->lignes,"Erreur : lignes non initialisée");

        // Special prolongation Init
        $this->assertNotNull($this->obj->duree,"Erreur : duree non initialisée");
        $this->assertNotNull($this->obj->dateExpiration,"Erreur : dateExpiration non initialisée");
        $this->assertNotNull($this->obj->dateDebut,"Erreur : dateExpiration non initialisée");
        $this->assertEquals("01/01/2050",$this->obj->dateExpiration,"Erreur : dateExpiration non initialisée");
        $this->assertEquals("01/01/2049",$this->obj->dateDebut,"Erreur : dateDebut non initialisée");
        $this->assertEquals("contrat",$this->obj->type,"Erreur : type non initialisée en prolongation");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_echeancierFacturationProlongation() {
        $this->create("commande");
        $this->create("facture");
        $this->create("prolongation");
        $this->create("lignesFacturation");

        $this->obj->generic("prolongation",$this->prolongation['id_prolongation'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-echeancierFacturationProlongation-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("f4726fef0d1790e28cd509a92ad190c3",$md5,"Erreur de génération de la echeancierFacturationProlongation");

    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 30-05-2011
    */
    public function test_echeancierFacturationContrat() {
        $this->create("commande");
        $this->create("facture");
        $this->create("prolongation");
        $this->create("lignesFacturation");

        // Modification du pays de la société
        $soc = $this->societe['id_pays'] = "BE";
        $soc = $this->societe['siret'] = "SIRETBE";
        ATF::societe()->u($this->societe);
        $this->affaire["id_filiale"]=$this->id_societe;
        ATF::affaire()->u($this->affaire);

        $this->obj->generic("prolongation",$this->prolongation['id_prolongation'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-echeancierFacturationContrat-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a6ffa84a8779148668ff7000cddabde0",$md5,"Erreur de génération de la echeancierFacturationContrat");

    }


    public function test_global_prolongation_et_facture() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureNormale");
        $ids[] = $this->facture['id_facture'];
        $this->facture = array(
            "id_affaire"=>$this->affaire['id_affaire']
            ,"type_facture"=>"refi"
            ,"id_societe"=>$this->id_societe
            ,"prix"=>20000
            ,"date"=>"2011-02-24"
            ,"id_commande"=>$this->commande['id_commande']
            ,"ref"=>"REFFACCLEODIS2"
            ,"tva"=>"1.196"
            ,"date_previsionnelle"=>"2011-02-24"
            ,"date_periode_debut"=>"2011-02-24"
            ,"date_periode_fin"=>"2011-09-24"
            ,"id_demande_refi"=>$this->demande_refi['id_demande_refi']
            ,"id_refinanceur"=>$this->demande_refi['id_refinanceur']
        );
        $this->facture['id_facture'] = ATF::facture()->i($this->facture,ATF::_s());
        for ($a=0; $a<3; $a++) {
            $this->ligneFacture[$a] = array(
                "ref"=>"REF".$a
                ,"produit"=>"Produit TU n°".$a
                ,"quantite"=>2*$a+1
                ,"id_facture"=>$this->facture['id_facture']
                ,"prix_achat"=>11*$a+1
            );
            $this->ligneFacture[$a]['id_facture_ligne'] = ATF::facture_ligne()->i($this->ligneFacture[$a]);
        }
        $ids[] = $this->facture['id_facture'];


        //***********Normal**************/
        $this->obj->generic("global_prolongation",$ids,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-global_prolongation_et_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d2ae70c01908ebe578b4407a6c7fb7d2",$md5,"Erreur de génération de la global_prolongation_et_facture");

        //***********Societe**************/
        $this->obj->generic("global_prolongationSociete",$ids,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-global_prolongation_et_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md51 = system($this->MD5cmd);
        $md51 = substr($md51,0,32);
        ob_get_clean();
        $this->assertEquals($md5,$md51,"Erreur de génération de la global_prolongationSociete");

        //***********Code**************/
        $this->obj->generic("global_prolongationCode",$ids,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-global_prolongation_et_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md52 = system($this->MD5cmd);
        $md52 = substr($md52,0,32);
        ob_get_clean();
        $this->assertEquals($md5,$md52,"Erreur de génération de la global_prolongationCode");

        //***********Date**************/
        $this->obj->generic("global_prolongationDate",$ids,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-global_prolongation_et_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md53 = system($this->MD5cmd);
        $md53 = substr($md53,0,32);
        ob_get_clean();
        $this->assertEquals($md5,$md53,"Erreur de génération de la global_prolongationDate");

        //***********Societe**************/
        $this->obj->generic("global_factureSociete",$ids,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-global_prolongation_et_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md51 = system($this->MD5cmd);
        $md51 = substr($md51,0,32);
        ob_get_clean();
        $this->assertEquals($md5,$md51,"Erreur de génération de la global_factureSociete");

        //***********Code**************/
        $this->obj->generic("global_factureCode",$ids,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-global_prolongation_et_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md52 = system($this->MD5cmd);
        $md52 = substr($md52,0,32);
        ob_get_clean();
        $this->assertEquals($md5,$md52,"Erreur de génération de la global_factureCode");

        //***********Date**************/
        $this->obj->generic("global_factureDate",$ids,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-global_prolongation_et_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md53 = system($this->MD5cmd);
        $md53 = substr($md53,0,32);
        ob_get_clean();
        $this->assertEquals($md5,$md53,"Erreur de génération de la global_factureDate");

    }

    public function test_grille_client() {
        $this->create("commande");
        $this->create("facture");
        $this->create("prolongation");
        $this->create("lignesFacturation");

        $this->lignesFacturation["reserve"]["date_debut"]="2011-01-01";
        $this->lignesFacturation["reserve"]["date_fin"]="2011-02-01";
        $this->lignesFacturation["reserve"]["fp"]=count($this->lignesFacturation)-1;
        $this->lignesFacturation["reserve"]["fc"]=count($this->lignesFacturation)-1;

        //***********Normal**************/
        $this->obj->generic("grille_client",$this->lignesFacturation,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-grille_client-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("7346d1ce8abb19fcf29990412286ca3a",$md5,"Erreur de génération de la grille_client");

        //***********Societe**************/
            //Envoye*/

                //Contrat*/
                $this->obj->generic("grille_contratclientSociete",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_contratclientSociete-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md51 = system($this->MD5cmd);
                $md51 = substr($md51,0,32);
                ob_get_clean();
                $this->assertEquals("7346d1ce8abb19fcf29990412286ca3a",$md51,"Erreur de génération de la grille_contratclientSociete");

                //Prolongation*/
                $this->obj->generic("grille_prolongationclientSociete",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_prolongationclientSociete-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md52 = system($this->MD5cmd);
                $md52 = substr($md52,0,32);
                ob_get_clean();
                $this->assertEquals("d547996f3191cbb9f059a166118b3bd6",$md52,"Erreur de génération de la grille_prolongationclientSociete");

            //Non envoye*/

                //Contrat*/
                $this->obj->generic("grille_contratclient_non_envoyeSociete",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_contratclient_non_envoyeSociete-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md53 = system($this->MD5cmd);
                $md53 = substr($md53,0,32);
                ob_get_clean();
                $this->assertEquals("58a5adc6143a23d02718c8af9ee25764",$md53,"Erreur de génération de la grille_contratclient_non_envoyeSociete");

                //Prolongation*/
                $this->obj->generic("grille_prolongationclient_non_envoyeSociete",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_prolongationclient_non_envoyeSociete-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md54 = system($this->MD5cmd);
                $md54 = substr($md54,0,32);
                ob_get_clean();
                $this->assertEquals("044fdb0b99abb1a58bb7eea95aad0c20",$md54,"Erreur de génération de la grille_prolongationclient_non_envoyeSociete");


        //***********Code**************/
            //Envoye*/

                //Contrat*/
                $this->obj->generic("grille_contratclientCode",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_contratclientCode-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md55 = system($this->MD5cmd);
                $md55 = substr($md55,0,32);
                ob_get_clean();
                $this->assertEquals($md51,$md55,"Erreur de génération de la grille_contratclientCode");

                //Prolongation*/
                $this->obj->generic("grille_prolongationclientCode",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_prolongationclientCode-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md56 = system($this->MD5cmd);
                $md56 = substr($md56,0,32);
                ob_get_clean();
                $this->assertEquals($md52,$md56,"Erreur de génération de la grille_prolongationclientCode");

            //Non envoye*/

                //Contrat*/
                $this->obj->generic("grille_contratclient_non_envoyeCode",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_contratclient_non_envoyeCode-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md57 = system($this->MD5cmd);
                $md57 = substr($md57,0,32);
                ob_get_clean();
                $this->assertEquals($md53,$md57,"Erreur de génération de la grille_contratclient_non_envoyeCode");

                //Prolongation*/
                $this->obj->generic("grille_prolongationclient_non_envoyeCode",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_prolongationclient_non_envoyeCode-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md58 = system($this->MD5cmd);
                $md58 = substr($md58,0,32);
                ob_get_clean();
                $this->assertEquals($md54,$md58,"Erreur de génération de la grille_prolongationclient_non_envoyeCode");

        //***********Date**************/
            //Envoye*/

                //Contrat*/
                $this->obj->generic("grille_contratclientDate",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_contratclientDate-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md59 = system($this->MD5cmd);
                $md59 = substr($md59,0,32);
                ob_get_clean();
                $this->assertEquals($md51,$md59,"Erreur de génération de la grille_contratclientDate");

                //Prolongation*/
                $this->obj->generic("grille_prolongationclientDate",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_prolongationclientDate-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md510 = system($this->MD5cmd);
                $md510 = substr($md510,0,32);
                ob_get_clean();
                $this->assertEquals($md52,$md510,"Erreur de génération de la grille_prolongationclientDate");

            //Non envoye*/

                //Contrat*/
                $this->obj->generic("grille_contratclient_non_envoyeDate",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_contratclient_non_envoyeDate-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md511 = system($this->MD5cmd);
                $md511 = substr($md511,0,32);
                ob_get_clean();
                $this->assertEquals($md53,$md511,"Erreur de génération de la grille_contratclient_non_envoyeDate");

                //Prolongation///
                $this->obj->generic("grille_prolongationclient_non_envoyeDate",$this->lignesFacturation,$this->tmpFile,$s);
                $this->obj->Close();
                $this->obj->Output($this->dirSavedPDF."-grille_prolongationclient_non_envoyeDate-".$this->dateSave.".pdf");
                ob_start();
                // Commande SHELL pour générer le fichier
                system($this->GScmd);
                $md512 = system($this->MD5cmd);
                $md512 = substr($md512,0,32);
                ob_get_clean();
                $this->assertEquals($md54,$md512,"Erreur de génération de la grille_prolongationclient_non_envoyeDate");
    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_factureLibre() {
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureLibre");

        ATF::societe()->u(array("id_societe"=>$this->id_societe,
                                "facturation_adresse"=>ATF::societe()->select($this->id_societe, "adresse"),
                                "facturation_adresse_2"=>ATF::societe()->select($this->id_societe, "adresse_2"),
                                "facturation_adresse_3"=>ATF::societe()->select($this->id_societe, "adresse_3"),
                                "facturation_cp"=>ATF::societe()->select($this->id_societe, "cp"),
                                "facturation_ville"=>ATF::societe()->select($this->id_societe, "ville")
                                )
                        );


        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->facture ,"Erreur : le facture n'est pas initialisé");
        $this->assertNotNull($this->obj->lignes,"Erreur : le lignes n'est pas initialisé");
        $this->assertNotNull($this->obj->client ,"Erreur : le client n'est pas initialisé");
        $this->assertNotNull($this->obj->affaire ,"Erreur : le affaire n'est pas initialisé");
        $this->assertNotNull($this->obj->devis,"Erreur : le devis n'est pas initialisé");
        $this->assertNotNull($this->obj->user,"Erreur : le user n'est pas initialisé");
        $this->assertNotNull($this->obj->agence ,"Erreur : le agence n'est pas initialisé");
        $this->assertNotNull($this->obj->societe,"Erreur : le societe n'est pas initialisé");
        $this->assertNotNull($this->obj->contrat  ,"Erreur : le contrat n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduit  ,"Erreur : le colsProduit n'est pas initialisé");
        $this->assertNotNull($this->obj->colsProduitAlignLeft ,"Erreur : le colsProduitAlignLeft n'est pas initialisé");
        $this->assertNotNull($this->obj->styleDetailsProduit ,"Erreur : le styleDetailsProduit n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureLibreRetard-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6ad5c54fe7b72ad96813be4693d606c0",$md5,"Erreur de génération de la facture libre Retard");


        //--------------------------------------------------------------------------------------------------
        //          Facture Libre Contentieux
        //--------------------------------------------------------------------------------------------------

        $this->facture['type_libre'] = "contentieux";
        ATF::facture()->u($this->facture);
        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);


        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureLibreContentieux-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6ad5c54fe7b72ad96813be4693d606c0",$md5,"Erreur de génération de la facture libre Contentieux");

        //--------------------------------------------------------------------------------------------------
        //          Facture Libre Normale
        //--------------------------------------------------------------------------------------------------


        $this->facture['type_libre'] = "normale";
        $this->facture['tva'] = "1.196";
        ATF::facture()->u($this->facture);

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureLibreNormaleAffaire-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("71ecad4a849c2ff533167475b41ecd92",$md5,"Erreur de génération de la facture libre Normale Affaire");

        //Affaire Vente
        $this->affaire['nature'] = "vente";
        ATF::affaire()->u($this->affaire);
        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureLibreNormaleVente-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("47d2f8052e76fc1a1120bd0c8104a53c",$md5,"Erreur de génération de la facture libre Normale Vente");

    }


    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_factureMidas() {
        $this->create("commande");
        $this->create("factureMidas");

        $this->obj->generic("facture",$this->facture['id_facture'],$this->tmpFile,$s);

        $this->assertNotNull($this->obj->facture ,"Erreur : le facture n'est pas initialisé");

        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-factureMidas-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("10071903f1a38ba2b2b308f6ab6fc648",$md5,"Erreur de génération de la facture Midas");
    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_relance1(){
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureLibre");
        $relance1 = ATF::relance()->i(array("id_societe" => $this->societe["id_societe"] , "id_contact" => $this->contact["id_contact"], "date" => "2012-01-01", "type" => "premiere", "texte"=> "Motif du rejet !!!!"));
        ATF::relance_facture()->insert(array("id_facture" => $this->facture['id_facture'] , "id_relance"=> $relance1));

        $this->obj->generic("relance",$relance1,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-Relance1-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("db043784581795141c476eacbfaa66db",$md5,"Erreur de génération de la relance 1");

        $this->create("factureNormale");
        ATF::relance_facture()->insert(array("id_facture" => $this->facture['id_facture'] , "id_relance"=> $relance1));
        $this->obj->generic("relance",$relance1,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-Relance1Bis-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a05ebe7b7bd8632a2330dfecf8cc4514",$md5,"Erreur de génération de la relance 1 BIS");


    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_relance2(){
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureLibre");
        $relance1 = ATF::relance()->i(array("id_societe" => $this->societe["id_societe"] , "id_contact" => $this->contact["id_contact"], "date" => "2012-01-01", "type" => "seconde", "texte"=> "Motif du rejet !!!!"));
        ATF::relance_facture()->insert(array("id_facture" => $this->facture['id_facture'] , "id_relance"=> $relance1));

        $this->obj->generic("relance",$relance1,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-Relance2-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("cc9c61fa32da8ca0b97e5807343485b9",$md5,"Erreur de génération de la relance 2");


        $this->create("factureNormale");
        ATF::relance_facture()->insert(array("id_facture" => $this->facture['id_facture'] , "id_relance"=> $relance1));
        $this->obj->generic("relance",$relance1,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-Relance2Bis-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("582ff5571a9f0640628566718fc711c6",$md5,"Erreur de génération de la relance 2 BIS");
    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_relance3(){
        $this->create("commande");
        $this->create("demande_refi");
        $this->create("factureLibre");
        $relance1 = ATF::relance()->i(array("id_societe" => $this->societe["id_societe"] , "id_contact" => $this->contact["id_contact"], "date" => "2012-01-01", "type" => "mise_en_demeure", "texte"=> "Motif du rejet !!!!"));
        ATF::relance_facture()->insert(array("id_facture" => $this->facture['id_facture'] , "id_relance"=> $relance1));

        $this->obj->generic("relance",$relance1,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-Relance3-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ae0539ffe1687fc6e2252680bfe4a77d",$md5,"Erreur de génération de la relance 3");


        $this->create("factureNormale");
        ATF::relance_facture()->insert(array("id_facture" => $this->facture['id_facture'] , "id_relance"=> $relance1));
        $this->obj->generic("relance",$relance1,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-Relance3Bis-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ba0a636d91bdaa353e1d70a6a6f6b141",$md5,"Erreur de génération de la relance 3 BIS");

    }

    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_envoiContratEtBilan(){
        $this->initUserOnly(false);
        $s['date'] = "2013-01-01";
        $s['type_devis'] = "de renouvellement";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $this->obj->generic("envoiContratEtBilan",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-CRenvoiContratEtBilan-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("92ec4bb37278e7002aabb15aceb3c105",$md5,"Erreur de génération de la envoiContratEtBilan");

    }

    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_envoiContratEtBilanAR(){
        $s['date'] = "2013-01-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");
        $affaire = array("id_affaire"=>$this->affaire['id_affaire'],"nature"=>"AR");
        ATF::affaire()->u($affaire);

        $this->obj->generic("envoiContratEtBilan",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-CRenvoiContratEtBilanAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("c4217558a4d173441a4a201387d22bb3",$md5,"Erreur de génération de la envoiContratEtBilanAR");

    }

    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_envoiContratSsBilan(){
        $s['date'] = "2013-01-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $this->obj->generic("envoiContratSsBilan",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-CRenvoiContratSsBilan-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("84cdbef0b595dd55253afd661d3ebf05",$md5,"Erreur de génération de la envoiContratSsBilan");

    }

    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_envoiContratSsBilanAR(){
        $s['date'] = "2013-01-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");
        $affaire = array("id_affaire"=>$this->affaire['id_affaire'],"nature"=>"AR");
        ATF::affaire()->u($affaire);

        $this->obj->generic("envoiContratSsBilan",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-CRenvoiContratSsBilanAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("1f2b61d813c2bbd08f86d821ae1fe502",$md5,"Erreur de génération de la envoiContratSsBilanAR");


    }

    public function test_documents() {
    	$this->create("contact");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");
        $this->create("bdc");

        ATF::$usr->set('contact', $this->contact);


        ATF::affaire()->u(array("id_affaire"=>$this->affaire['id_affaire'] , "id_partenaire"=> $this->contact["id_societe"]));

        $res = $this->obj->_documents(array("id_affaire"=> $this->affaire['id_affaire'] , "document"=>"bon_de_commande"));
        $this->assertNotNull($res , "retour 2 vide?");

        $res = $this->obj->_documents(array("id_affaire"=> $this->affaire['id_affaire'] , "document"=>"contratA3"));
        $this->assertEquals($res, false , "CONTRAT A3 	autorisé?");

        $res = $this->obj->_documents(array("id_affaire"=> $this->affaire['id_affaire'] , "document"=>"contratA4"));
        $this->assertNotNull($res , "retour vide?");



    }


    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_envoiAvenant(){
        $s['date'] = "2013-01-01";
        $s['bdc'] = "REFBDC!!";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $this->obj->generic("envoiAvenant",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-CRenvoiAvenant-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("58e795e329ae5aa43def61e13d6c3c62",$md5,"Erreur de génération de la envoiAvenant");


        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");

        $s['date'] = "2013-01-01";
        $s['bdc'] = "REFBDC!!";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("envoiAvenant",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-cCRenvoiAvenantCleodisBE".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("58e795e329ae5aa43def61e13d6c3c62",$md5_2,"Erreur de génération de la envoiAvenant CLEODIS BE");


    }

    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_contratTransfert(){
        $s['date'] = "2013-01-01";
        $s['reprise_magasin'] = "Reprise de magasin";
        $s['docSupAretourner'] = "docSupAretourner";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $this->obj->generic("contratTransfert",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratTransfert-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("617da5a5a43b18af2d2fee1de9f709a5",$md5,"Erreur de génération de la contratTransfert");

        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");

        $s['date'] = "2013-01-01";
        $s['bdc'] = "REFBDC!!";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("contratTransfert",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratTransfertCleodisBE".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("617da5a5a43b18af2d2fee1de9f709a5",$md5_2,"Erreur de génération de la contratTransfert CLEODIS BE");

    }

    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_ctSigne(){
        $s['date'] = "2013-01-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $this->obj->generic("ctSigne",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-ctSigne -".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("1ebe352b831ce23ae5e805cbe312d1c8",$md5,"Erreur de génération de la ctSigne ");


        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("ctSigne",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-ctSigneCleodisBE".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("1ebe352b831ce23ae5e805cbe312d1c8",$md5_2,"Erreur de génération de la ctSigne CLEODIS BE");


    }

    /*
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function test_CourrierRestitution(){
        $s['date'] = "2013-01-01";
        $s['date_echeance'] = "2013-02-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        ATF::commande()->u(array("id_commande"=>$this->commande['id_commande'], "date_demande_resiliation"=>"2015-10-07"));

        $this->obj->generic("CourrierRestitution",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-courrierRestitution -".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("b0ed719ee4ac007a826c54d63623a766",$md5,"Erreur de génération de la CourrierRestitution ");


        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("CourrierRestitution",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-CourrierRestitutionCleodisBE".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("b0ed719ee4ac007a826c54d63623a766",$md5_2,"Erreur de génération de la CourrierRestitution CLEODIS BE");

    }

     /*
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function test_envoiCourrierClassique(){
        $this->initUserOnly(false);
        $s['date'] = "2013-01-01";
        $s['type_devis'] = "de renouvellement";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $this->obj->generic("envoiCourrierClassique",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-envoiCourrierClassique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ce74fff0292131e31a7ac8696058360a",$md5,"Erreur de génération de la envoiCourrierClassique");

        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);
        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("envoiCourrierClassique",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-envoiCourrierClassiqueCleodisBE".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("ce74fff0292131e31a7ac8696058360a",$md5_2,"Erreur de génération de la envoiCourrierClassique CLEODIS BE");

    }

    /*
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function test_envoiCourrierClassiqueAR(){
        $s['date'] = "2013-01-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");
        $affaire = array("id_affaire"=>$this->affaire['id_affaire'],"nature"=>"AR");
        ATF::affaire()->u($affaire);

        $this->obj->generic("envoiCourrierClassique",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-envoiCourrierClassiqueAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("bf15b860a83e3aa37db83b27d0374042",$md5,"Erreur de génération de la envoiCourrierClassiqueAR");

    }


    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_mandatSepa(){
        $this->create("commande");

        $this->obj->generic("mandatSepa",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-MandatSepa-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("b8a93ba234cc5ad41affe9e706ebe57e",$md5,"Erreur de génération du Mandat Sepa");


        ATF::affaire()->u(array("id_affaire"=>$this->affaire["id_affaire"], "RUM"=>null));

        $this->obj->generic("mandatSepa",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-MandatSepa-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("539414f21102307b4a041366c7cfb7ee",$md5,"Erreur de génération du Mandat Sepa Sans RUL");


        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);
        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("mandatSepa",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-mandatSepaCleodisBE".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("539414f21102307b4a041366c7cfb7ee",$md5_2,"Erreur de génération de la mandatSepa CLEODIS BE");
    }




    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_formation_devis(){
        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"acompte"=>200
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"remuneration_of"=>"20"
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"normal"
                 );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));
        ATF::formation_devis()->u(array("id_formation_devis"=> $this->id_devis_formation, "numero_dossier"=>"FCL20150002"));

        $this->obj->generic("formation_devis",$this->id_devis_formation,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_devis-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("82f83eb1fa9ae71fcee3c90ad4623a08",$md5,"Erreur de génération du test_formation_devis");


    }


    public function test_formation_devis_light(){
        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation; La thématique 2 de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"acompte"=>200
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"remuneration_of"=>"20"
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"light"
                 );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));
        ATF::formation_devis()->u(array("id_formation_devis"=> $this->id_devis_formation, "opca"=>"246|253|254" , "numero_dossier"=>"FCL20150002"));

        $this->obj->generic("formation_devis",$this->id_devis_formation,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_devis_light-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("e123130aa5421545fd9f17e91e8b9cc5",$md5,"Erreur de génération du test_formation_devis_light");


    }


    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_formation_commande(){
        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"normal"
                        );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));
        $this->id_commande_formation = ATF::formation_commande()->i(array(  "id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13","objectif"=>"Les objectifs sont les suivants :<br><ul><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li><li>Etiam dignissim massa vel arcu fermentum, ac ornare libero tempor.</li><li>Aenean id tortor nec erat viverra iaculis.</li><li>Pellentesque vehicula nulla eu tortor vehicula, et placerat magna posuere.</li><li>Phasellus tincidunt urna venenatis, egestas massa at, varius quam.</li><li>Suspendisse mollis ex eu mi fermentum, ac posuere enim cursus.</li></ul>"
                  ));



        $this->obj->generic("formation_commande",$this->id_commande_formation,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_commande-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("8f9ba46080d9cacfa162864f4a7c17b2",$md5,"Erreur de génération du test_formation_commande");


        /*
        * PDF FORMATION COMMANDE AVEC DEVIS AYANT UN ACOMPTE
        */

        ATF::formation_devis()->u(array("id_formation_devis" => $this->id_devis_formation, "acompte"=> 5555));

        $this->obj->generic("formation_commande",$this->id_commande_formation,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_commande-avec-acompte-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("451489ad0cdec9e36502c3da2e31f805",$md5,"Erreur de génération du test_formation_commande avec acompte");

    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_formation_commande_fournisseur(){
        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"normal"
                 );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));
        $this->id_commande_formation = ATF::formation_commande()->i(array(  "id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13" ));

        $this->formation_commande_fournisseur = ATF::formation_commande_fournisseur()->i(array("id_formation_devis" => $this->id_devis_formation , "id_formation_commande" => $this->id_commande_formation ,"objectif" => "L'objectif de la formation", "id_user"=>97 ));

        $this->obj->generic("formation_commande_fournisseur",$this->formation_commande_fournisseur,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_commande_fournisseur-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("38b6f992c1091d7ae6273ba64944afdf",$md5,"Erreur de génération du test_formation_commande_fournisseur");
    }




    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_formation_attestation_presence(){
        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"normal"
                 );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));

        $this->id_commande_formation = ATF::formation_commande()->insert(array( "id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13" ));

        ATF::formation_attestation_presence()->q->reset()->where("id_formation_devis", $this->id_devis_formation);
        $res = ATF::formation_attestation_presence()->select_all();

        $this->obj->generic("formation_attestation_presence",$res[0]["id_formation_attestation_presence"],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_attestation_presence-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("4c9a857f2a01f1fe1e290df05b1c364b",$md5,"Erreur de génération du test_formation_attestation_presence");
    }


    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_formation_bon_de_commande_fournisseur(){

        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"normal"
                 );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));

        $this->id_commande_formation = ATF::formation_commande()->insert(array( "id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13" ));
        $infos = array("id_formation_devis" => $this->id_devis_formation , "id_formation_commande" => $this->id_commande_formation ,"objectif" => "L'objectif de la formation", "id_user"=>97 );
        $this->id_formation_commande_fournisseur = ATF::formation_commande_fournisseur()->insert(array("formation_commande_fournisseur" => $infos));

        $infos =  array("id_formation_devis" => $this->id_devis_formation ,
                       "id_societe" => $this->id_societe ,
                       "id_contact" => $this->id_contact ,
                       "id_fournisseur" => 1606 ,
                       "montant"=> 500,
                       "thematique"=> "Thematique",
                       "date"=>"2015-03-18",
                       "ref"=> "ref TU",
                       "commentaire"=> "commentaire");

        $id_formation_bon_de_commande_fournisseur = ATF::formation_bon_de_commande_fournisseur()->insert(array( "formation_bon_de_commande_fournisseur" => $infos));
        ATF::formation_bon_de_commande_fournisseur()->u(array("id_formation_bon_de_commande_fournisseur" => ATF::formation_bon_de_commande_fournisseur()->decryptId($id_formation_bon_de_commande_fournisseur), "ref"=> "FCL20150004"));

        $this->obj->generic("formation_bon_de_commande_fournisseur",$id_formation_bon_de_commande_fournisseur,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_bon_de_commande_fournisseur-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("7bc504bc578ca05ae365d9fffa1c60cd",$md5,"Erreur de génération du formation_bon_de_commande_fournisseur");



        ATF::formation_priseEnCharge()->insert(array("id_formation_devis"=>$this->id_devis_formation,
                                                "opca"=>6491,
                                                "etat"=>"attente_element",
                                                "montant_demande"=>3500,
                                                "date_envoi"=>date("Y-m-d"),
                                                "subro_client"=>"oui"
                                            ));

        $this->obj->generic("formation_bon_de_commande_fournisseur",$id_formation_bon_de_commande_fournisseur,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_bon_de_commande_fournisseur-avec-subro-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("9ed6ede0ea23616a75ee9827ee938c52",$md5,"Erreur de génération du formation_bon_de_commande_fournisseur avec acompte");

    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_formation_facture(){
        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"normal"
                 );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));

        $this->id_commande_formation = ATF::formation_commande()->insert(array( "id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13" ));

        $infos = array("id_formation_devis" => $this->id_devis_formation ,
                      "id_societe" => $this->id_societe ,
                      "type"=>"normale",
                      "ref" => "La ref de facture",
                      "prix"=> "2500",
                      "num_dossier"=> "Le num de dossier",
                      "date"=>"2015-03-13");


        $this->id_formation_facture = ATF::formation_facture()->insert(array("formation_facture" => $infos));

        $this->obj->generic("formation_facture",$this->id_formation_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_facture-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("4557d92826aa943d71a828822f4b5a2b",$md5,"Erreur de génération du formation_facture");
    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_formation_facture_acompte(){
        $this->environnementFormation();
        $infos = array(  "numero_dossier" => "123456789"
                        ,"thematique" => "La thématique de la formation"
                        ,"date" => "2015-03-13"
                        ,"nb_heure" => "20"
                        ,"prix" => "50"
                        ,"id_owner"=>97
                        ,"id_contact"=>81
                        ,"id_societe" => $this->id_societe
                        ,"contact" => array($this->id_contact1, $this->id_contact2, $this->id_contact3)
                        ,"type"=>"normal"
                 );
        $dates = array("formation_devis_ligne" => json_encode(array(array("formation_devis_ligne__dot__date"=>"2015-01-31T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_am"=> "14h",
                                                                                "formation_devis_ligne__dot__date_fin_am"=> "18h"),
                                                                          array("formation_devis_ligne__dot__date"=>"2015-01-29T00:00:00",
                                                                                "formation_devis_ligne__dot__date_deb_matin"=> "8h",
                                                                                "formation_devis_ligne__dot__date_fin_matin"=> "12h30")
                                                                        )
                                                                ),
                        "formation_devis_fournisseur" => json_encode(array(array("formation_devis_fournisseur__dot__id_societe_fk"=>"1606",
                                                                                 "formation_devis_fournisseur__dot__type"=>"lieu_formation"),
                                                                                array("formation_devis_fournisseur__dot__id_societe_fk"=>"246",
                                                                                      "formation_devis_fournisseur__dot__type"=>"apporteur_affaire")
                                                                                )
                                                                        )
                    );
        $this->id_devis_formation = ATF::formation_devis()->insert(array("formation_devis" => $infos , "values_formation_devis" => $dates));

        $this->id_commande_formation = ATF::formation_commande()->insert(array( "id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13" ));

        $infos = array("id_formation_devis" => $this->id_devis_formation ,
                      "id_societe" => $this->id_societe ,
                      "type"=>"acompte",
                      "ref" => "La ref de facture",
                      "prix"=> "2500",
                      "num_dossier"=> "Le num de dossier",
                      "date"=>"2015-03-13" );


        $this->id_formation_facture = ATF::formation_facture()->insert(array("formation_facture" => $infos));
        $this->obj->generic("formation_facture",$this->id_formation_facture,$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-formation_facture_acompte-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("9fa688930cef8bb09c8188f7935ce532",$md5,"Erreur de génération de formation_facture_acompte");
    }

    public function test_comite() {

    	$this->comite = array(
    			'date' => '2019-03-25',
		    	'id_refinanceur' => 4,
		    	'id_contact' => $this->contact["id_contact"],
		    	'id_affaire' => $this->affaire["id_affaire"],
		    	'id_societe' => $this->societe["id_societe"],
		    	'activite' => "Commerce de gros (commerce interentreprises) de bois et de matériaux de construction",
		    	'prix' => 0.00,
		    	'valeur_residuelle' => 0.00,
		    	'pourcentage_materiel' => 0.00,
		    	'pourcentage_logiciel' => 0.00,
		    	'description' => 'Comité CLEODIS',
		    	'marque_materiel' => NULL,
		    	'reponse' => NULL,
		    	'etat' => 'en_attente',
		    	'taux' => NULL ,
		    	'coefficient' => NULL ,
		    	'encours' => NULL ,
		    	'frais_de_gestion' => NULL ,
		    	'validite_accord' => NULL ,
		    	'observations' => NULL ,
		    	'loyer_actualise' => NULL ,
		    	'date_cession' => NULL ,
		    	'duree_refinancement' => NULL ,
		    	'note' => NULL ,
		    	'limite' => NULL ,
		    	'ca' => NULL ,
		    	'resultat_exploitation' => NULL ,
		    	'capital_social' => NULL ,
		    	'capitaux_propres' => NULL ,
		    	'dettes_financieres' => NULL ,
		    	'maison_mere1' => NULL ,
		    	'maison_mere2' => NULL ,
		    	'maison_mere3' => NULL ,
		    	'maison_mere4' => NULL ,
		    	'date_compte' => '30/04/2018' ,
		    	'commentaire' => NULL ,
		    	'date_creation' => date('Y-m-d'),
		    	'decisionComite' => NULL ,
		    	'notifie_utilisateur' => NULL ,
		    	'destinataire' => 'jerome.loison@cleodis.com' );

    	$this->comite["id_comite"] = ATF::comite()->i( $this->comite );

    	$this->obj->generic("comite",$this->comite["id_comite"],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-comiteAllUncheck-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("359c761f446a805b525eb6b6baf39024",$md5,"Erreur de génération de comite ALL UNCHECK");


        $this->comite["date_creation"] = "2002-01-01";
        $this->comite["capital_social"] = 12000;
        $this->comite["dettes_financieres"] = 5000;
        $this->comite["capitaux_propres"] = 15000;
        $this->comite["ca"] = 200000;
        $this->comite["note"] = 60;
        ATF::comite()->u($this->comite);
        $this->obj->generic("comite",$this->comite["id_comite"],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-comiteAllCheck-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("d150a979cdb1d0445e6a176fd5266051",$md5,"Erreur de génération de comite AL CHECK");






    }



    public function test_contratA4CleodisBE() {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");


        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4CleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();


        ATF::commande()->u(array("id_commande"=> $this->commande['id_commande'], "clause_logicielle"=> "oui"));
        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4ClauseLogicielleCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_3 = system($this->MD5cmd);
        $md5_3 = substr($md5,0,32);
        ob_get_clean();



        $c->generic("contratA4NL",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4NLCleodisBEAvecFiligramme-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();




        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("05d135f89cd5f9ebfc56f285e1be2bd9",$md5,"Erreur de génération de la commande");
        $this->assertEquals("05d135f89cd5f9ebfc56f285e1be2bd9",$md5_3,"Erreur de génération de la commande 2");
        $this->assertEquals("05d135f89cd5f9ebfc56f285e1be2bd9",$md5_2,"Erreur de génération de la commande 3");
    }


    /*
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA4PrestaCleodisBE () {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::devis()->u(array("id_devis"=>$this->devis['id_devis'], "type_contrat"=>"presta"));

        $societe = ATF::societe()->select($this->id_societe);
        $societe['siren'] = "SIRENTU";
        ATF::societe()->u($societe);

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4PrestaCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("e3f339a0d8270c7863bd91bc6d98c594",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 25-02-2011
    */
    public function test_contratA4ClauseVenteCleodisBE () {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        // Ajout de la clause logicielle
        $this->commande['clause_logicielle'] = "oui";
        ATF::commande()->u($this->commande);

        $this->obj->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $this->obj->Close();
        $this->obj->Output($this->dirSavedPDF."-contratA4ClauseVenteCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("e2353f57b997cb508a650318f59dfac3",$md5,"Erreur de génération de la contratA4ClauseVente");
    }


    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4ARCleodisBE () {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        $affaire = array(
            "id_affaire"=>$id_affaire
            ,"id_fille"=>$this->affaire['id_affaire']
        );
        ATF::affaire()->u($affaire);
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "AR";
        ATF::affaire()->u($this->affaire);


        ATF::societe()->u(array("id_societe"=>$this->societe['id_societe'] , "id_pays"=>"BE"));

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4ARCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("38ef744c307c6016af8f769550828e0e",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4AvenantCleodisBE () {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "avenant";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4AvenantCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();



        ATF::devis()->u(array("id_devis"=>$this->devis['id_devis'], "type_contrat"=>"presta"));
        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4AvenantPrestaCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5_2,0,32);
        ob_get_clean();


        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("b384573d1904a6a4e9eb68c3e558c0ba",$md5,"Erreur de génération de la commande");
        $this->assertEquals("c73178d00c23a8520f6dec2c13aed26d",$md5_2,"Erreur de génération de la commande 2");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4AffaireVenteCleodisBE () {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->q->reset()
                                ->addField("id_affaire")
                                ->setLimit(1)
                                ->setDimension('cell');
        $id_affaire = ATF::affaire()->sa();
        $this->assertNotNull($id_affaire,"Pas d'ID affaire pour changer la provenance...");
        foreach ($this->ligneCommande as $k=>$i) {
            if ($k) {
                $i['id_affaire_provenance'] = $id_affaire;
                $i["neuf"] = "non";
                ATF::commande_ligne()->u($i);
            }
        }

        $this->affaire['nature'] = "vente";
        $this->affaire['id_parent'] = $id_affaire;
        ATF::affaire()->u($this->affaire);

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4AffaireVenteCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("8c76763557558231b38b56c3f11f7830",$md5,"Erreur de génération de la commande");
    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4LoyerUniqueCleodisBE () {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $this->devis['loyer_unique'] = "oui";
        ATF::devis()->u($this->devis);

        foreach ($this->loyer as $k=>$i) {
            if (!$k) continue;
            ATF::loyer()->delete($i['id_loyer']);
        }

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4LoyerUniqueCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        foreach ($this->loyer as $k=>$i) {
            if (!$k){
                $i["loyer"]="0";
                $i["assurance"]=NULL;
                $i["frais_de_gestion"]=NULL;
                ATF::loyer()->u($i);
            }
        }
        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4LoyerUniqueCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5_2,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("08654a9ded583c2ca7c420e88f519bb2",$md5,"Erreur de génération de la commande");
        $this->assertEquals("2205c7db68dc5b14ebc7b2c37d415450",$md5_2,"Erreur de génération de la commande 2" );

    }

    /*
    * @author Quentin JANON <qjanon@absystech.fr>
    * @date 28-02-2011
    */
    public function test_contratA4AvecAnnexeCleodisBE () {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        for ($a=0;$a<10;$a++) {
            foreach ($this->ligneCommande as $k=>$i) {
                unset($i['id_commande_ligne']);
                ATF::commande_ligne()->i($i);
            }
        }

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4AvecAnnexeCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->beginTransaction("cleodisbe", false, true);
        $this->assertEquals("d7983779eb54ed285bf0afe7f4bcbd88",$md5,"Erreur de génération de la commande");
    }






























    public function test_contratA4NLCleodisBE() {
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        ATF::affaire()->u(array("id_affaire"=>$this->affaire["id_affaire"], "langue"=>"NL"));

        $c->generic("contratA4NL",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4NLCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4CleodisBEAvecFiligramme-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5_2 = system($this->MD5cmd);
        $md5_2 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);


        $this->assertEquals("26e4d68e349dbb34ec8617939220ac85",$md5,"Erreur de génération de la commande");
        $this->assertEquals("26e4d68e349dbb34ec8617939220ac85",$md5_2,"Erreur de génération de la commande 2 ");
    }


    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_conditionsGeneralesDeLocationA3_cleodisbe(){
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $societe = ATF::societe()->select($this->id_societe);
        $societe['siren'] = "SIRENTU";
        ATF::societe()->u($societe);

        $c->generic("contratA3",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA3SimpleBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("84af45bc64b8514f42c41b127d2c76f3",$md5,"Erreur de génération de la commande BE");


    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_conditionsGeneralesDeLocationA4_cleodisbe(){
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();

        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");
        $this->create("commande");
        $this->create("produitsCommande");
        $this->create("produitAvecDetail");

        $societe = ATF::societe()->select($this->id_societe);
        $societe['siren'] = "SIRENTU";
        ATF::societe()->u($societe);

        $c->generic("contratA4",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-contratA4SimpleBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("05d135f89cd5f9ebfc56f285e1be2bd9",$md5,"Erreur de génération de la commande BE");


    }


    /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_envoiContratEtBilanCleodisBE(){

        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();
        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");

        $s['date'] = "2013-01-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("envoiContratEtBilan",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-CRenvoiContratEtBilanCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("139086a9b2f7d08f5c44a71fb19d6148",$md5,"Erreur de génération de la envoiContratEtBilanCleodisBE");

    }


     /*
     * @author Quentin JANON <qjanon@absystech.fr>
     */
    public function test_envoiContratSsBilanCleodisBE(){

        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cleodisbe", true, false);

        $c = new pdf_cleodisbe();
        $this->id_societe = 4225;
        $this->id_contact = 3878;
        $this->create("user");
        $this->create("affaire");
        $this->create("devis");
        $this->create("loyer");

        $s['date'] = "2013-01-01";
        $this->create("contact");
        $devis = array("id_devis"=>$this->devis['id_devis'],"id_contact"=>$this->contact['id_contact']);
        ATF::devis()->u($devis);
        $this->create("commande");

        $c->generic("envoiContratSsBilan",$this->commande['id_commande'],$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-CRenvoiContratSsBilanCleodisBE-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cleodisbe", false, true);

        $this->assertEquals("72e5dbf381540187d4badc995ea28026",$md5,"Erreur de génération de la envoiContratSsBilanCleodisBE");

    }



    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_audit_cap(){
        ATF::unsetSingleton("pdf");
        $this->beginTransaction("cap", true, false);

        $c = new pdf_cap();

        ATF::$usr->set('id_user', 14);
        $audit = array("id_societe"=>1,
                       "id_user"=>14,
                       "date"=>"2015-10-24",
                       "ref"=>"123456789",
                       "type"=>"gestion_poste");

        $id_audit = ATF::audit()->i($audit);



        $c->generic("audit",$id_audit,$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-auditA4-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();

        $this->beginTransaction("cap", false, true);

        $this->assertEquals("536ce67d4e50217a5907991213930124",$md5,"Erreur de génération de l'audit CAP");
    }

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_mandat_modifiable_cap(){
        ATF::unsetSingleton("pdf");
        ATF::db()->select_db("optima_cap");

        $c = new pdf_cap();

        ATF::$usr->set('id_user', 14);



        $id_societe = 2;

        $c->generic("mandat",$id_societe,$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-mandat-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("0998f465cac6e9e0ea24f7ec01af45e6",$md5,"Erreur de génération du mandat CAP");




       /* $id_societe = 8843;

        $c->generic("mandat",$id_societe,$this->tmpFile,$s);
        $c->Close();
        $c->Output($this->dirSavedPDF."-mandat-SansSiren-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("ddf66b29bce860534df66f6690b41b73",$md5,"Erreur de génération du mandat CAP SANS SIREN");

        */
        ATF::db()->select_db("optima_cleodis");
    }





};
?>