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

	private function beginTransaction($codename, $begin, $commit){
		if($begin){
			ATF::db()->select_db("extranet_v3_".$codename);
	    	ATF::$codename = $codename;
	    	ATF::db()->begin_transaction(true);
		}
		
		if($commit){
			ATF::db()->rollback_transaction(true);
	        ATF::$codename = "cleodis";
	        ATF::db()->select_db("extranet_v3_cleodis");
		}
		
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
        $this->assertEquals("7fd505021a1fcdcc409b7c13de6ad1a4",$md5,"Erreur de génération du mandatSellAndSign");
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

        $this->assertEquals("032f29428bce02ea090ecdf113c39b91",$md5,"Erreur de génération de la facture");

       
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

        $this->assertEquals("26bd2149332f4dcf02e6c81bbab62686",$md5,"Erreur de génération de la facture");
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
        $this->assertEquals("910c4303cac05264856a4ada027ae223",$md5,"Erreur de génération du Footer A4 Cléodis, auraient-ils été modifié ?");
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

        $this->assertEquals("58aea7ae306dbe0df7095cd8d2447581",$md5,"Erreur de génération du Footer A4 Cléodis BE, auraient-ils été modifié ?");
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
        $this->assertEquals("de1b3591d911952a02454442705ef28b",$md5,"Erreur de génération du Footer A3 Cléodis, auraient-ils été modifié ?");        
        
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

        $this->assertEquals("138aa00134a341462c4a1cac5d34e9e9",$md5,"Erreur de génération du Footer A3 Cléodis BE, auraient-ils été modifié ?");
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
        $this->assertEquals("b5a8643e8335366a51d2ab6c25f4f772",$md5,"Erreur de génération du Header A4 Cléodis, auraient-ils été modifié ?");

     
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

        $this->assertEquals("bebe5930d930e6179fe28b5fd0ef0c8a",$md5,"Erreur de génération du Header A4 Cléodis BE, auraient-ils été modifié ?");


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
        $this->assertEquals("2c446a4dd25eb9687be0a27c8c1b203c",$md5,"Erreur de génération du Header A3 Cléodis, auraient-ils été modifié ?");
        
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
        $this->assertEquals("2a0349bed0f33a7d289d0c226694cb59",$md5,"Erreur de génération du Footer PREVISU Cléodis");
        
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
        $this->assertEquals("5afdd8100608b57e33bb64f1bf309f25",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("dd4ce822bdd70aa54db5e7619f62f4ae",$md5,"Erreur de génération du devis optic 2000 Avenant");        
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
        $this->assertEquals("d1a942b8076b63c483a57686c1f85b7d",$md5,"Erreur de génération du devis optic 2000 AR");
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
        $this->assertEquals("c0e69656caafedf16562221a264cbe28",$md5,"Erreur de génération du devis optic 2000 AR");
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
        $this->assertEquals("8ff7bfc8d32d95fb4b318ebe89038e8b",$md5,"Erreur de génération du devis optic 2000 AR");
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
        $this->assertEquals("5a1b6e07f6e1cc50476cd68e4e36fe8a",$md5,"Erreur de génération du devis optic 2000 AR");
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
        $this->assertEquals("7582b8e6e2e700d3f416aea53b0e629a",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("eff3abd3c2544440bb10111fd6abc78b",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("f96eb7e0bc4b928b6df6be62b8faf5b5",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("cb155dc0708b27c6a30ef6f17856380b",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("850e572a2811e2e9d9d93b6b9628455c",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("859f2b18c5be7878fc7cc3affe552a6f",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("b9016540171faa6e9f69d5ec949b9b72",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("f476c28e0feba11c2fdac8632d9faf72",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("f5905d346c389711940d708798d26066",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("a7f66fd7e6a1c5dbdcb66e1ea777e622",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("2ed2a1129699eef86d2b31753b33da9e",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("4a1d6cec8d1a2f71d0779a6d6ad229de",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("eb7381a1b9ebc4d2fdded31b30cf35e8",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("ff6a0afa0bf9e58c82f5d8fcff03b7d4",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("dc3b11a524d30d4c2f214f8b9103942d",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("03fa9afc5ef7e22effd1dd0538e62d4c",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("22ff70d089eafc8404a8f1ce3d6f2ff1",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("497d126e7338e831a2752c825c25a174",$md5,"Erreur de génération du devis");
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
        $this->assertEquals("2c2caf02e47bc1791b015d87a47f87a0",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("5cf38a8ee61b64e4bcfcf9059a5649c0",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("c37aa05a865d0a7e4872498a23123ea2",$md5,"Erreur de génération de la contratA3AvecAnnexes");
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
        $this->assertEquals("1ae12c8ae2e1b02714a7efb789c0eca1",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("df0569b2f5b8c1c51a75fc107b998049",$md5,"Erreur de génération de la commande");

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
        $this->assertEquals("384bddea5cea51e1fe19ca5934953d11",$md5,"Erreur de génération de la commande");

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
        $this->assertEquals("19bd30f1b899cf150321b6149faf306d",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("0043bd3a1cee680dda47d55a5f76bb4b",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("5c95b3f9fdbbb6aba6861c0a30b0ab3d",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("e818656ad4cbf582aac934374ada04fb",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("0064c6b2b53eb17f8aa998e31ce5136f",$md5,"Erreur de génération de la contratA4ClauseVente");
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
        $this->assertEquals("1a994ceb27ffb8a3b59008afe9eb87f7",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("cc304b73fb2c957a0a5178f08076b1c5",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("86633efcfa911b7871cb64b247f03142",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("fa2975579ddab221f4080700e6f8513c",$md5,"Erreur de génération de la commande");

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
        $this->assertEquals("110f9b541a5c02a6fd0e58c84f821aff",$md5,"Erreur de génération de la commande 2" );

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
        $this->assertEquals("42a33992434f016028b642d6b7c8ead8",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("517864a8b8722b1b2945b683bf31f7be",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("32e84b4c732f80d29e7706faecadf3ad",$md5,"Erreur de génération de la contratPVARANNEXES");
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
        $this->assertEquals("46a10b0866d25005d794bf454b915eb6",$md5,"Erreur de génération de la commande");
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
        $this->assertEquals("0efed40aeeebb1c0c980340b27c0b615",$md5,"Erreur de génération de la commande");
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
    	
    	$this->assertEquals("b93b66e87bd732dc19073091458f8d04",$md5,"Erreur de génération de la commande");
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
    	$this->assertEquals("31fb156e09755c5a3191a69d90d048f7",$md5,"Erreur de génération de la lettre belfius");
    	$this->assertEquals("31fb156e09755c5a3191a69d90d048f7",$md52,"Erreur de génération de la lettre belfius sans siege");
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
        $this->assertEquals("0164ef09cf5cb271c7d0912e5986f597",$md5,"Erreur de génération de la BDC");
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
        $this->assertEquals("d25aa0ba72cbb47494907df3d9325ac8",$md5,"Erreur de génération de la BDCAvecAnnexe");
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
        $this->assertEquals("5299d2c0951ea30f8acfcc75dbb8b348",$md5,"Erreur de génération de la demande_refiKBC");
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
        $this->assertEquals("2c5e5877d730845438667e72ff02c483",$md5,"Erreur de génération de la demande_refiBNP");
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
//      $this->assertNotNull($this->obj->contact,"Erreur : le contact n'est pas initialisé");
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
        $this->assertEquals("8957b74ba563a801762493d89da2a600",$md5,"Erreur de génération de la facture");


        $this->facture['prix'] = 0;
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
        $this->assertEquals("fba0572740abc3c66660983f1acb8b77",$md5,"Erreur de génération de la facture AVOIR");

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
        $this->assertEquals("5ca60806ed29c37ba6c21aac7901a25a",$md5,"Erreur de génération de la facture VENTE");

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
        $this->assertEquals("dde969e57c2e0c27896d1ee69edc3b05",$md5,"Erreur de génération de la facture AVOIR cheque");

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
        $this->assertEquals("94a12a84f6501d9773c7643bd9333cd5",$md5,"Erreur de génération de la facture AVOIR Virement");
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
        $this->assertEquals("81441a787a872a9e3bd0a163960a62f7",$md5,"Erreur de génération de la facture");
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
        $this->assertEquals("ebfadbb54fc42502cfbf5526774f1ab5",$md5,"Erreur de génération de la facture");
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
        $this->assertEquals("e1cf4a36e4095b84247839ea2dacf29f",$md5,"Erreur de génération de la facture");
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
        $this->assertEquals("439c57d575907008ed0515c0b8f30b6d",$md5,"Erreur de génération de la facture");
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
        $this->assertEquals("314787e8cfc4122cd50539b7441a0092",$md5,"Erreur de génération de la facture Refi d'Avoir");
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
        $this->assertEquals("c8d64a87ac477fac757316d2f67a1005",$md5,"Erreur de génération de la echeancierFacturationProlongation");
        
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
        $this->assertEquals("93022bbf08912f61a0cd0ec6c93b7c96",$md5,"Erreur de génération de la echeancierFacturationContrat");
        
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
        $this->assertEquals("34d9e93d071413d49980a467ab2b05a6",$md5,"Erreur de génération de la global_prolongation_et_facture");

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
        $this->assertEquals("5beff989f9391f4ddc7a31254a71534f",$md5,"Erreur de génération de la grille_client");

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
                $this->assertEquals("5beff989f9391f4ddc7a31254a71534f",$md51,"Erreur de génération de la grille_contratclientSociete");
                
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
                $this->assertEquals("6e575fe63befeb393fd24ff1a94121e4",$md52,"Erreur de génération de la grille_prolongationclientSociete");

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
                $this->assertEquals("480b88906911f6e9de9be184f3d26129",$md53,"Erreur de génération de la grille_contratclient_non_envoyeSociete");
                
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
                $this->assertEquals("0c963a904f7f6bcbfe0c84b6a2c4376c",$md54,"Erreur de génération de la grille_prolongationclient_non_envoyeSociete");


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
        $this->assertEquals("bc4102c9ddbda905341cff0eb1bdbff4",$md5,"Erreur de génération de la facture libre Retard");
        
                
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
        $this->assertEquals("bc4102c9ddbda905341cff0eb1bdbff4",$md5,"Erreur de génération de la facture libre Contentieux");
        
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
        $this->assertEquals("b1dff7ec7a582cd18b7344783b448355",$md5,"Erreur de génération de la facture libre Normale Affaire");
        
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
        $this->assertEquals("5c9b67b1638a0f6db814c682d3c02ddf",$md5,"Erreur de génération de la facture libre Normale Vente");
        
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
        $this->assertEquals("7c6895ae65d6321cfeab1d8f1fc8151b",$md5,"Erreur de génération de la facture Midas");
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
        $this->assertEquals("2d84ba6f07493f9a1665df62351a32b8",$md5,"Erreur de génération de la relance 1");
                
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
        $this->assertEquals("3b0cdc4698fdccb615cc1803a3963a6a",$md5,"Erreur de génération de la relance 1 BIS");
        
        
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
        $this->assertEquals("aaa78e095bb82396c128b409ef0b4af1",$md5,"Erreur de génération de la relance 2");
        
        
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
        $this->assertEquals("deef12ebcde221cf4f29762b08cd1d08",$md5,"Erreur de génération de la relance 2 BIS");
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
        $this->assertEquals("58951addcfa3d7068aacb28f276e18b9",$md5,"Erreur de génération de la relance 3");
    
    
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
        $this->assertEquals("15a2d3f4e7f3e8e4f7032213df971172",$md5,"Erreur de génération de la relance 3 BIS");
    
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
        $this->assertEquals("976d3a12622aa8f18179af8f10c0d975",$md5,"Erreur de génération de la envoiContratEtBilan");
    
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
        $this->assertEquals("e63d50e84d41e8b963952749ae73696f",$md5,"Erreur de génération de la envoiContratEtBilanAR");
    
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
        $this->assertEquals("bdf43d8b2b64089ff33a64eec57b5288",$md5,"Erreur de génération de la envoiContratSsBilan");
    
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
        $this->assertEquals("792d40f6f29b68b393508f6df34ff6ad",$md5,"Erreur de génération de la envoiContratSsBilanAR");
    
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
        $this->assertEquals("ca1a849569477a9de37523c0fa11cc76",$md5,"Erreur de génération de la envoiAvenant");
    
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
        $this->obj->Output($this->dirSavedPDF."-CRcontratTransfert-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6af77a3045907358b0043aa10c70e24d",$md5,"Erreur de génération de la contratTransfert");
    
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
        $this->obj->Output($this->dirSavedPDF."-CRctSigne -".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("a65ce5151594de06e38aadcced7b6ff7",$md5,"Erreur de génération de la ctSigne ");
    
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
        $this->obj->Output($this->dirSavedPDF."-CRCourrierRestitution -".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("4eb86ea01ccf6f4169d71cc6fde94715",$md5,"Erreur de génération de la CourrierRestitution ");
    
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
        $this->obj->Output($this->dirSavedPDF."-CRenvoiCourrierClassique-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("6d46b9c7fd9a7f2c58b0f3f18b6669e8",$md5,"Erreur de génération de la envoiCourrierClassique");
    
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
        $this->obj->Output($this->dirSavedPDF."-CRenvoiCourrierClassiqueAR-".$this->dateSave.".pdf");
        ob_start();
        // Commande SHELL pour générer le fichier
        system($this->GScmd);
        $md5 = system($this->MD5cmd);
        $md5 = substr($md5,0,32);
        ob_get_clean();
        $this->assertEquals("957b2e2b335016d5192c9960e4285e91",$md5,"Erreur de génération de la envoiCourrierClassiqueAR");
    
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
        $this->assertEquals("f07590e8e54aa886e41a684beef6218c",$md5,"Erreur de génération du Mandat Sepa");   
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
        $this->assertEquals("3a69179a2fe52e7736ece13f130a9694",$md5,"Erreur de génération du test_formation_devis");   


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
        $this->assertEquals("aa5df55fa0d479daf0dd3dc50edb353e",$md5,"Erreur de génération du test_formation_devis_light");   


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
		$this->id_commande_formation = ATF::formation_commande()->i(array(	"id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13","objectif"=>"Les objectifs sont les suivants :<br><ul><li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li><li>Etiam dignissim massa vel arcu fermentum, ac ornare libero tempor.</li><li>Aenean id tortor nec erat viverra iaculis.</li><li>Pellentesque vehicula nulla eu tortor vehicula, et placerat magna posuere.</li><li>Phasellus tincidunt urna venenatis, egestas massa at, varius quam.</li><li>Suspendisse mollis ex eu mi fermentum, ac posuere enim cursus.</li></ul>"
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
        $this->assertEquals("ebed85c6178cd7f0d3af04298800fa45",$md5,"Erreur de génération du test_formation_commande"); 


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
        $this->assertEquals("b65a6894e59c2ef8fe1304ed9dee8fb1",$md5,"Erreur de génération du test_formation_commande avec acompte"); 

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
		$this->id_commande_formation = ATF::formation_commande()->i(array(	"id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13" ));
		
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
        $this->assertEquals("52fb2acfb395004f15b8d60ca3ef7994",$md5,"Erreur de génération du test_formation_commande_fournisseur"); 
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
        $this->assertEquals("1375b8ea2761be9109c2684aa659ea20",$md5,"Erreur de génération du test_formation_attestation_presence"); 
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
        $this->assertEquals("a3a5d7035a9d484657431d9953d61b53",$md5,"Erreur de génération du formation_bon_de_commande_fournisseur");



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
        $this->assertEquals("4b2e1e336de1f9ecf923acd67f594695",$md5,"Erreur de génération du formation_bon_de_commande_fournisseur avec acompte");

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
		
		$this->id_commande_formation = ATF::formation_commande()->insert(array(	"id_formation_devis" => $this->id_devis_formation, "ref"=>"FCL20150001", "date" => "2015-03-13" ));
		
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
        $this->assertEquals("1746d05022b4a4fa305e9186a83e8a9f",$md5,"Erreur de génération du formation_facture"); 
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
        $this->assertEquals("383a4cf44f5bce763a40dad39c3fe90d",$md5,"Erreur de génération de formation_facture_acompte"); 
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

        $this->assertEquals("ef47dfa1102164d1c82fdacc3317365f",$md5,"Erreur de génération de la commande BE");       

       
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

        $this->assertEquals("bf17351aec75618a6122d8be48ce9dcb",$md5,"Erreur de génération de la commande BE");       

        
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

        $this->assertEquals("c6734fcdff53426d0202f3543c28ebdd",$md5,"Erreur de génération de l'audit CAP");       
	}

    //@author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    public function test_mandat_modifiable_cap(){
        ATF::unsetSingleton("pdf");
        ATF::db()->select_db("extranet_v3_cap");
        
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
        $this->assertEquals("3e322a5eeb69a30b47d01ec488fccfe4",$md5,"Erreur de génération du mandat CAP");       




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
        ATF::db()->select_db("extranet_v3_cleodis");
    }

    

};
?>