<?
require_once dirname(__FILE__)."/../pdf.class.php"; 
require_once dirname(__FILE__)."/../cleodis/pdf.class.php"; 
/**  
* @package Optima
* @subpackage Cleodis 
* @date 05-01-2011
*/
class pdf_lm extends pdf_cleodis {	
	public $logo = 'lm/lm.jpg';
	public $heightLimitTableContratA4 = 100; 

	public $leftStyle = array(
		"size" => 8
		,"color" => 000000
		,"font" => "arial"
		,"border" => ""
		,"align" => "L"
	);

	/* Génère le pied de page des PDF Cléodis
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	*/
	public function Footer() {




		if ($this->getFooter()) return false;
		if (!$this->societe) return false;
		//Police Arial italique 8
		$style = array("decoration"=>"I","size"=>8,"color"=>"000000");
		$style2 = array("decoration"=>"BI","size"=>10,"color"=>"000000");
		
		$savelMargin=$this->lMargin;
		
		//Numéro de page centré
		$this->ATFSetStyle($style);
		$this->SetXY(10,-20);
		$this->multicell(0,3,"Conformément à l'article 27 de la loi Informatique et Libertés, vous disposez d'un droit d'accès et de rectification des données vous concernant et dont nous sommes les seuls utilisateurs",0,"C");
		$this->multicell(0,3,"S.A.S LEROY MERLIN ABONNEMENTS - Capital de 10.000 € - 820 472 009 RCS LILLE - N° C.E.E. FR 08820472009
SIEGE SOCIAL - rue Chanzy - LEZENNES - 59712 LILLE Cedex 9 - Tel : 03 28 80 80 80",0,'C');
		
		
		$this->SetX(10);		
		if (!$this->noPageNo) {
			$this->ln(-3);
			$this->Cell(0,3,$this->noPageNo.'Page '.$this->PageNo(),0,0,'R');
		}
		$this->SetLeftMargin($savelMargin);
		

		if($this->previsu){
			$this->setfont('arial','BU',18);
			$this->sety(10);
			$this->multicell(0,5,"PREVISUALISATION",0,'C');
		}
					
	}


		
	/* Header spécifique aux documents cléodis
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	*/
	public function Header() {	
		$this->texteHT = "HT";
		$this->texteTTC = "TTC";

		if ($this->getHeader()) return false;		

        $this->sety(5);
		$this->setleftmargin(2);
		$this->setrightmargin(2);
		$this->setFillColor(102,170,51);
		$this->cell(30,5,"",0,0,"L",1);

		$this->cell(6,5,"",0,0,"L");

		$this->setfont('arial','B',9);
		$this->settextcolor(255,255,255);
		$this->setFillColor(102,170,51);
		$this->cell(134,5,"BRICOLAGE - CONSTRUCTION - DECORATION - JARDINAGE",0,0,"C",1);

		$this->cell(6,5,"",0,0,"L");
		$this->setFillColor(102,170,51);
		$this->cell(30,5,"",0,1,"L",1);

		$this->image(__PDF_PATH__.$this->logo,7,13,20);


		$this->setX(178);
		$this->settextcolor(0,0,0);
		$this->setfont('arial','B',9);
		$this->cell(40,5,"www.leroymerlin.fr",0,1,"L");
		
		if(__PRE__ === true || __DEV__ === true){
			$this->setX(40);
			$this->setY(20);

			$this->Rotate(-55);
			$this->setLineWidth(0.5);
			$this->setfont('arial',"B",160);
			$this->setTextColor(211,211,211);
			if(__PRE__ === true){$this->multicell(300,10,"RECETTE",0,"C");}
			if(__DEV__ === true){$this->multicell(300,10,"ESP DEV",0,"C");}
			
			$this->setTextColor("black");
			$this->setfont('arial',"",8);
			$this->Rotate(0);
		}
		

		$this->sety(20);
		
	}


	/** Initialise les données pour générer une commande
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param int $id Identifiant commande
	*/
	public function commandeInit($id) {
		$this->commande = ATF::commande()->select($id);
			
		$this->colsProduit = array("border"=>"TB","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduit");
		$this->colsProduitFirst = array("border"=>"TLB","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitFirst");
		$this->colsProduitLast = array("border"=>"TBR","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitLast");
		$this->colsProduitAvecDetail = array("border"=>"T","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitAvecDetail");
		$this->colsProduitAvecDetailFirst = array("border"=>"TL","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitAvecDetailFirst");
		$this->colsProduitAvecDetailLast = array("border"=>"TR","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitAvecDetailLast");
		$this->styleDetailsProduit = array("border"=>"LRB","decoration"=>"I","size"=>8,"flag"=>"styleDetailsProduit");
		ATF::commande_ligne()->q->reset()->where("visible","oui")
									     ->where("id_commande",$this->commande['id_commande'])
									     ->addGroup("id_produit");
		$this->lignes = ATF::commande_ligne()->sa();
			
		$this->devis = ATF::devis()->select($this->commande['id_devis']);
		$this->loyer = ATF::loyer()->ss('id_affaire',$this->devis['id_affaire']);
		if (count($this->loyer)==1 && $this->devis['loyer_unique']=="oui") {
			$this->loyer = $this->loyer[0];
		}
		$this->user = ATF::user()->select($this->commande['id_user']);
		$this->societe = ATF::societe()->select($this->user['id_societe']);
		$this->client = ATF::societe()->select($this->commande['id_societe']);
		$this->affaire = ATF::affaire()->select($this->commande['id_affaire']);
		if ($this->affaire['nature']=="AR") {
			$this->AR = ATF::affaire()->getParentAR($this->affaire['id_affaire']);
		}
	}



	/** PDF d'un contrat en A4
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 02-06-2016
	* @param int $id Identifiant commande
	*/
	public function contratA4($id) {
		$this->noPageNo = true;
		$this->commandeInit($id);
		$this->Open();
		$this->AddPage();
		$this->A3 = false;
		$this->A4 = true;

		
		$this->setfont('arial','B',10);
				
		$this->sety(10);
		$this->setLeftMargin(37);


		if($this->affaire["type_affaire"] == "LP"){
			$this->multicell(134,5,"CONDITIONS PARTICULIERES DE LOCATION \nContrat N° : ".$this->commande['ref'],0,"C");
		}else{
			$this->multicell(134,5,"CONDITIONS PARTICULIERES D'ABONNEMENT \nContrat N° : ".$this->commande['ref'],0,"C");
		}		
		$this->setleftmargin(7);
		$this->setrightmargin(7);
		$this->setY(30);


		$this->setfont('arial','B',8);		
		$this->setFillColor(200,200,200);
		if($this->affaire["type_affaire"] == "LP"){
			$this->multicell(0,5,"1.1. Le Client :",1,"L",1);
		}else{
			$this->multicell(0,5,"1.1. L'Abonné :",1,"L",1);
		}

		$this->setfont('arial','',8);
		$adresseFacturation = $this->affaire["adresse_facturation"]; 				
		$adresseFacturation .= "\n".$this->affaire["cp_adresse_facturation"]." ".$this->affaire["ville_adresse_facturation"]; 

		$y = $this->getY();
		$this->multicell(100,4,"\nNom : ".$this->client["nom"]."\nPrénom : ".$this->client["prenom"]."\nAdresse : ".$adresseFacturation."\n",1,"L");	
		
		$this->setXY(107,$y);
		
	
		$this->multicell(96,4,"\nTéléphone : ".$this->client["tel"]."\nE-mail: ".$this->client["email"],"LR","L");
		$this->setXY(107,$this->getY());		
		$this->SetTextColor(255,255,255);
		$this->multicell(96,4,$adresseFacturation."\n","LRB","L");
		$this->SetTextColor(0,0,0);
				
		$this->ln(5);

		$this->setfont('arial','B',8);		
		$this->setFillColor(200,200,200);		
		if($this->affaire["type_affaire"] == "LP"){
			$this->multicell(0,5,"1.2. Le Loueur/Leroy Merlin Abonnements",1,"L",1);
		}else{
			$this->multicell(0,5,"1.2. Leroy Merlin Abonnements",1,"L",1);
		}
		$this->setfont('arial','',8);
		$this->multicell(0,4,"\nLa société Leroy Merlin Abonnements, SAS au capital de 10.000€ dont le siège social est situé Rue de Chanzy Lezennes 59712 Lille Cedex 9, immatriculée au RCS Lille Métropole sous le numéro 820 472 009\n\n",1,"L");
		
		$this->ln(5);


		if($this->affaire["type_affaire"] == "LP"){
			$this->listingLP();
			$chapSuivant = 4;

		}elseif($this->affaire["type_affaire"] == "SP"){
			$this->listingSP();
			$chapSuivant = 4;
		}else{
			$this->listingLS();
			$chapSuivant = 5;
		}
		
		$this->AddPage();
		$this->setleftmargin(7);
		$this->setrightmargin(7);
		$this->setY(30);

		$this->setfont('arial','B',8);		
		$this->setFillColor(200,200,200);	
		$this->multicell(0,5,"1.".$chapSuivant.". Durée de l'abonnement",1,"L",1);

		$this->setfont('arial','',8);	
		$texteDuree = "";

		$dureeEngagement = 0;
		$FrequenceEngagement = $this->loyer[0]["frequence_loyer"];

		$head = array("Nombre de loyers","Périodicité","Loyer HT","Loyer TTC");
		$width = array(49,49,49,49);		
		$data = array();
		$style = array();

		foreach ($this->loyer as $key => $value) {
			if($value["nature"] != "prolongation" && $value["nature"] != "prolongation_probable"){
				$dureeEngagement += $value["duree"];

				$data[$key][0] = $value["duree"];		
				switch ($value["frequence_loyer"]) {
					case 'jour' : $data[$key][1] = "Hebdomadaire"; break;
					case 'mois' : $data[$key][1] = "Mensuel"; break;
					case 'trimestre' : $data[$key][1] = "Trimestriel"; break;
					case 'semestre' : $data[$key][1] = "Semestriel"; break;
					case 'an' : $data[$key][1] = "Annuel"; break;
				}			
				$data[$key][2] = number_format(round(($value["loyer"]/__TVA__),2),2,"."," ")." €";
				$data[$key][3] = $value["loyer"]." €";
				//$style[$key][1] = $this->leftStyle;
			}
							
		}



		if($this->affaire["type_affaire"] == "SP"){
			$texteDuree = "Durée : ".$dureeEngagement." ".$FrequenceEngagement." à compter de la souscription du Contrat d'Abonnement";
		}else{
			$texteDuree ="Durée : ".$dureeEngagement." ".$FrequenceEngagement." à compter de la réception du Produit";
		}

		$this->multicell(0,4,"\n".$texteDuree."\n\nEchéancier :\n\n",1,"L");

		
		$this->tableau($head,$data,$width,7,$style,260);


		$dureeProl = 0;
		$FrequenceProl = "";
		$head = array("Nombre de loyers","Périodicité","Loyer HT","Loyer TTC");
		$width = array(49,49,49,49);		
		$data = array();
		$style = array();
		
		foreach ($this->loyer as $key => $value) {
			if($value["nature"] == "prolongation" || $value["nature"] == "prolongation_probable"){
				$dureeProl += $value["duree"];
				$FrequenceProl = $value["frequence_loyer"];
				$data[$key][0] = $value["duree"];		
				switch ($value["frequence_loyer"]) {
					case 'jour' : $data[$key][1] = "Hebdomadaire"; break;
					case 'mois' : $data[$key][1] = "Mensuel"; break;
					case 'trimestre' : $data[$key][1] = "Trimestriel"; break;
					case 'semestre' : $data[$key][1] = "Semestriel"; break;
					case 'an' : $data[$key][1] = "Annuel"; break;
				}			
				$data[$key][2] = number_format(round(($value["loyer"]/__TVA__),2),2,"."," ")." €";
				$data[$key][3] = $value["loyer"]." €";
				//$style[$key][1] = $this->leftStyle;
			}
							
		}


		$this->multicell(0,4,"\nProlongation possible de ".$dureeProl." ".$FrequenceProl." par tacite reconduction sans engagement :\n\nEchéancier de prolongation (indicatif) :\n\n",1,"L");
		

		
		$this->tableau($head,$data,$width,7,$style,260);
		

		$this->ln(5);		
		$this->setFillColor(200,200,200);
		if($this->affaire["type_affaire"] == "SP"){
			$chapSuivant++;
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"1.".$chapSuivant.". Déclaration de l'Abonné",1,"L",1);
			$this->setfont('arial','',8);
			$this->multicell(0,4,"\nLe présent Contrat d'Abonnement prendra effet à compter de l'acceptation, par Leroy Merlin Abonnements, de la demande d'abonnement. L'Abonné reconnait être engagé par toutes les stipulations du Contrat d'Abonnement, y compris les Conditions Générales d'Abonnement, dont l'Abonné reconnait les avoir reçues, lues, comprises et acceptées. L'Abonné confirme également que toutes les informations contenues dans le présent Contrat sont correctes.\nL'Abonné déclare et garantit à Leroy Merlin Abonnements, à la date de signature et à tout moment pendant l'exécution du Contrat, qu'il est valablement constitué, qu'il a le pouvoir et la faculté de conclure et exécuter la présente convention.\n\n",1,"L");
			$chapSuivant++;
			$this->ln(5);$this->setfont('arial','B',8);
			$this->multicell(0,5,"1.".$chapSuivant.". Utilisation des données personnelles ",1,"L",1);
			$this->setfont('arial','',8);
			$this->multicell(0,4,"\nL'Abonné reconnait en signant les présentes Conditions Particulières avoir accepté les termes des Conditions Générales d'Abonnement, et consentir à l'utilisation et à la communication des données personnelles qu'il aura fournies ou qui auront été collectées, après avoir été informé qu'elles pourront être transférées en dehors de l'Union Européenne et qu'il pourra exercer son droit d'accès et de rectification à l'adresse indiquée dans les conditions générales.\n\n",1,"L");

		}elseif($this->affaire["type_affaire"] == "LP"){	
			$chapSuivant++;
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"1.".$chapSuivant.". Déclaration du Client",1,"L",1);
			$this->setfont('arial','',8);			
			$this->multicell(0,4,"\nLe présent Contrat de location prendra effet à compter de la réception par le Client du ou des Produit(s). Le Client reconnait être engagé par toutes les stipulations du Contrat de location, y compris les Conditions Générales de Location, dont le Client reconnait les avoir reçues, lues, comprises et acceptées. Le Client confirme également que toutes les informations contenues dans le présent Contrat sont correctes.\nLe Client déclare et garantit à Leroy Merlin Abonnements, à la date de signature et à tout moment pendant l’exécution du Contrat, qu’il est valablement constitué, qu’il a le pouvoir et la faculté de conclure et exécuter la présente convention.\n\n",1,"L");
			$chapSuivant++;
			$this->ln(5);
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"1.".$chapSuivant.". Utilisation des données personnelles ",1,"L",1);
			$this->setfont('arial','',8);
			$this->multicell(0,4,"\nLe Client reconnait en signant les présentes Conditions Particulières avoir accepté les termes des conditions générales de location, et consentir à l’utilisation et à la communication des données personnelles qu’il aura fournies ou qui auront été collectées, après avoir été informé qu’elles pourront être transférées en dehors de l’Union Européenne et qu’il pourra exercer son droit d’accès et de rectification à l’adresse indiquée dans les conditions générales.\n\n",1,"L");
		}else{
				$chapSuivant++;
				$this->setfont('arial','B',8);
				$this->multicell(0,5,"1.".$chapSuivant.". Déclaration de l'Abonné",1,"L",1);
				$this->setfont('arial','',8);
				$this->multicell(0,4,"\nLe présent Contrat d'Abonnement prendra effet à compter de la réception par l'Abonné du ou des Produit(s). L'Abonné reconnait être engagé par toutes les stipulations du Contrat d’Abonnement, y compris les Conditions Générales d’Abonnement, dont l’Abonné reconnait les avoir reçues, lues, comprises et acceptées. L'Abonné confirme également que toutes les informations contenues dans le présent Contrat sont correctes.\nL'Abonné déclare et garantit à Leroy Merlin Abonnements, à la date de signature et à tout moment pendant l’exécution du Contrat, qu’il est valablement constitué, qu’il a le pouvoir et la faculté de conclure et exécuter la présente convention.\n\n",1,"L");
				$chapSuivant++;
				$this->ln(5);
				$this->setfont('arial','B',8);
				$this->multicell(0,5,"1.".$chapSuivant.". Utilisation des données personnelles ",1,"L",1);
				$this->setfont('arial','',8);
				$this->multicell(0,4,"\nL'Abonné reconnait en signant les présentes Conditions Particulières avoir accepté les termes des Conditions Générales d’Abonnement, et consentir à l’utilisation et à la communication des données personnelles qu’il aura fournies ou qui auront été collectées, après avoir été informé qu’elles pourront être transférées en dehors de l’Union Européenne et qu’il pourra exercer son droit d’accès et de rectification à l’adresse indiquée dans les conditions générales.\n\n",1,"L");
		}	
		

			





		$this->setY(219);
		$this->line(0,$this->gety(),238,$this->gety());
		$this->SetTextColor(64,192,0);
		$this->setfont('arial','B',10);
		$this->multicell(0,5,"Fait en trois exemplaires",0,'C');
		$this->SetDrawColor(0,0,0);
		$this->SetTextColor(0,0,0);
		
		$this->setfont('arial','',9);

		$this->setFillColor(255,255,0);

		
		$y = $this->gety()+2;
		if($this->affaire["type_affaire"] == "LP"){
			$t = "Pour le client";
		} else {
			$t = "Pour l'abonné";
		}
		$this->cadre(20,$y,80,48,array(),$t);
		$cadre = array(
			"Fait à : "
			,"Le : "
			,"Nom : "
			,"Qualité : "
			,"Signature : "
		);
		
		$t = "Pour Leroy Merlin Abonnements";
		
		$this->cadre(110,$y,80,48,$cadre,$t);
			
		
		$this->setfont('arial','B',9);
		$this->setY(275.9);		
	}


	public function listingLP(){

		$this->setfont('arial','B',8);		
		$this->setFillColor(200,200,200);	
		$this->multicell(0,5,"1.3. Le(s) Produits loué(s)",1,"L",1);
		$this->listingProduitsService("produit");

		$this->ln(5);
		if($this->affaire["adresse_livraison"] == $this->affaire["adresse_facturation"]){
			$texteAdresse = "......................................................................................................................................................................................................................................................";
		}else{
			$texteAdresse = $this->affaire["adresse_livraison"]." ".$this->affaire["cp_adresse_livraison"]." ".$this->affaire["ville_adresse_livraison"];
		}
		$this->multicell(0,4,"Adresse de livraison du Produit (si différente de celle indiquée au 1.1 ci-dessus) : \n".$texteAdresse,1,"L");


	}

	public function listingSP(){
		$this->setfont('arial','B',8);		
		$this->setFillColor(200,200,200);	
		$this->multicell(0,5,"1.3. Le(s) Service(s)",1,"L",1);
		$this->listingProduitsService("service");
	}

	public function listingLS(){
		$this->setfont('arial','B',8);		
		$this->setFillColor(200,200,200);	
		$this->multicell(0,5,"1.3. Le(s) Produit(s) loué(s)",1,"L",1);
		$this->listingProduitsService("produit");

		$this->ln(5);
		if($this->affaire["adresse_livraison"] == $this->affaire["adresse_facturation"]){
			$texteAdresse = "......................................................................................................................................................................................................................................................";
		}else{
			$texteAdresse = $this->affaire["adresse_livraison"]." ".$this->affaire["cp_adresse_livraison"]." ".$this->affaire["ville_adresse_livraison"];
		}
		$this->multicell(0,4,"Adresse de livraison du Produit (si différente de celle indiquée au 1.1 ci-dessus) : \n".$texteAdresse,1,"L");


		$this->ln(5);
		$this->setfont('arial','B',8);		
		$this->setFillColor(200,200,200);	
		$this->multicell(0,5,"1.4. Le(s) Service(s)",1,"L",1);
		$this->listingProduitsService("service");


	}

	public function listingProduitsService($type){
		$lignes = array();
		if ($this->lignes) {			
			foreach ($this->lignes as $key => $value) {
				if(ATF::produit()->select($value["id_produit"], "nature") == $type){					
					if(strpos($value["produit"], "&nbsp;>") === false){						
						$lignes[] = $value;
					}
				}
			}		
		}
		if($lignes){
			if($type == "produit"){
				$head = array("Quantité","Description des produits");
				$width = array(30,166);		
				$data = array();
				$style = array();

				foreach ($lignes as $key => $value) {
					$data[$key][0] = $value["quantite"];					
					$data[$key][1] = $value["produit"];
					$style[$key][1] = $this->leftStyle;					
				}
				$this->tableau($head,$data,$width,7,$style,260);
			}

			if($type == "service"){
				$head = array("","Description des services");
				$width = array(30,166);		
				$data = array();
				$style = array();

				foreach ($lignes as $key => $value) {
					$data[$key][0] = "";					
					$data[$key][1] = $value["produit"];
					$style[$key][1] = $this->leftStyle;					
				}
				$this->tableau($head,$data,$width,7,$style,260);
			}			

		}
		
	}
	
	

	public function tableauBigHead($head,$data,$width=false,$c_height=5,$style=false,$limitBottomMargin=270) {
		$save = $this->headStyle;
		$newStyleHead = array(
			"size" => 9
			,"color" => 000000
			,"font" => "arial"
			,"border" => 1
			,"align" => "J"
			,"bgcolor" => "ffffff"
		);
		
		$this->headStyle[0] = $newStyleHead;
		foreach ($head as $k=>$i) {
			$this->headStyle[] = $newStyleHead;
		}
		$this->tableau($head,$data,$width,$c_height,$style,$limitBottomMargin);
		$this->headStyle = $save;
	}

	public function facture($id,$s,$global=false){
		
		$this->noPageNo = true;
		$this->setFooter();
		
		$this->A3 = false;
		$this->A4 = true;	

		if(!$global){
			$this->open(); 
		}
		$this->Addpage();

		$id = ATF::facture()->decryptId($id);

		$facture = ATF::facture()->select($id);
		ATF::facture_ligne()->q->reset()->where("id_facture",$id);
		$facture_lignes = ATF::facture_ligne()->select_all();
		$affaire = ATF::affaire()->select($facture["id_affaire"]);

		$client = ATF::societe()->select($facture["id_societe"]);
		$societe = ATF::societe()->select(ATF::user()->select($facture["id_user"], "id_societe"));
		$this->societe = $societe;

		$this->setY(13);
		$this->setleftmargin(40);
		
		$this->setfont('arial','',16);
		if($facture["prix"]>0){
			$this->cell(134,5,"FACTURE N° ".$facture["ref"],0,1,"C");
		}else{
			$this->cell(134,5,"AVOIR N° ".$facture["ref"],0,1,"C");
		}
		

		$this->setfont('arial','',8);
		$this->cell(134,5,"Date ".($facture["prix"]>0?"de facture":"de l'avoir")." ".date("d/m/Y", strtotime($facture["date"]))." Exemplaire client ",0,1,"C");
		$this->cell(134,5,"Date d'émission ".date("d/m/Y", strtotime($facture["date"])),0,1,"C");


		$this->setleftmargin(7);
		$this->setrightmargin(7);
		$this->setY(40);

		$this->setfont('arial','',8);		
		$this->setFillColor(200,200,200);
		$adresse = $societe["adresse"];
		if($societe["adresse_2"]) $adresse .= "\n".$societe["adresse_2"];
		if($societe["adresse_3"]) $adresse .= "\n".$societe["adresse_3"];
		$adresse .= "\n".$societe["cp"]." ".$societe["ville"];
		$adresse .= "\n"."FRANCE";
		$adresse .= "\n"."Téléphone : Se référer à votre contrat";
		$adresse .= "\n"."Site internet : https://abonnement.leroymerlin.fr";

		$y = $this->getY();		

		$this->multicell(80,4,$societe["societe"]."\n".$adresse,0,"L",1);

		$this->ln(5);

		$this->setfont('arial','',7);
		$this->multicell(80,4,"N° de client :".$client["ref"],0,"L",0);
		$this->multicell(80,4,"N° de contrat :".$affaire["ref"],0,"L",0);
		$this->multicell(110,3,"Conditions de règlement : prix comptant sans escompte , paiement à réception de la facture",0,"L");

		$this->multicell(110,3,"Pénalité retard : En cas de non-paiement à l’échéance, des pénalités de retard égales à trois fois le taux d’intérêt légal pourront être appliquées, outre l’indemnité forfaitaire d’un montant de 40 euros prévue par la loi sauf frais de recouvrement plus important");

		$this->setfont('arial','',8);
			
		$cadre2[] = "          ".strtoupper($client["civilite"]." ".$client["prenom"]." ".$client["nom"]);
		$cadre2[] = "          ".$client["adresse"];
		if($client["adresse_2"]) $cadre2[] = "          ".$client["adresse_2"];
		if($client["adresse_3"]) $cadre2[] = "          ".$client["adresse_3"];
		$cadre2[] = "          ".$client["cp"]." ".$client["ville"];
		
		$this->cadre(110,$y,90,30,$cadre2,false,0);

		$this->setY(95);

		$this->setLeftMargin(7);

			
		/*if($facture["nature"]){
			$this->lignes_facture($facture_lignes,$facture,"loyer");
		}else{
			$this->lignes_facture($facture_lignes,$facture,"prestation");
		}	*/
		$this->lignes_facture($facture_lignes,$facture,NULL);

			

		$this->ln(5);
		$y = $this->getY();
		/*
		$head = array("","Total HT","Montant TVA");
		$width = array(20,30,30);		
		$data = array();
		$style = array();

		foreach ($this->tva as $key => $value) {
			$data[0][0] = "TVA ".$key."%";
			$style[0][0] = $this->leftStyle;
			$data[0][1] = $value["total"]." €";
			$data[0][2] = $value["TVA"]." €";

			$montantTVA += $value["TVA"];
			$montantHT += $value["total"];
		}

		

		$data[1][0] = "Total";
		$style[1][0] = $this->leftStyle;
		$data[1][1] = $montantHT." €";
		$data[1][2] = $montantTVA." €";

		
		$this->tableau($head,$data,$width,7,$style,260);
		*/

		if($facture["prix"]>0){
			$this->ln(5);
			$head = array("Date","Réglement","Montant");
			$width = array(20,30,30);		
			$data = array();
			$style = array();
			$data[0][0] =  date("d/m/Y", strtotime($facture["date"]));
			$data[0][1] = "PRELEVEMENT AUTOMATIQUE";
			$data[0][2] = $facture["prix"]." €";
			
			$this->tableau($head,$data,$width,7,$style,260);
		
			$this->ln(10);
			$this->multicell(0,5,"TERMES DE PAIEMENTS \nLe ".date("d/m/Y", strtotime($facture["date_previsionnelle"])).", vous serez débité sur le compte bancaire");
		}
		
		$this->setY($y);
		$this->setX(130);

		$this->setfont('arial','B',10);
		$this->cell(50,10,"Total TTC",1,0);
		$this->cell(20,10,$facture["prix"]." €",1,1,"C");
		

	}

	public function lignes_facture($facture_lignes,$facture, $nature){
		
		$head = array("Désignation ( Référence )","Prix unit. HT","Taux de TVA","Total TTC");
		$width = array(132,20,20,20);

		$pack = ATF::produit()->select($facture_lignes[0]['id_produit'] , "id_pack_produit");


		$data[0][0] = "Abonnement à l'offre ".ATF::pack_produit()->select($pack , "libelle");
		$style[0][0] = $this->leftStyle;
		$data[0][1] = number_format(($facture["prix"] - ($facture["prix"]*0.2)) ,2)." €" ;
		$data[0][2] = "20%";
		$data[0][3] = number_format($facture["prix"],2)." €" ;

		$this->tableauBigHead($head,$data,$width,7,$style,260);

		/*if($nature == "loyer"){
			$head = array("Désignation ( Référence )","Prix unit. HT","Taux de TVA","Total TTC");
			$width = array(132,20,20,20);

			if ($facture_lignes){
				foreach ($facture_lignes as $k => $i) {
					$prod = ATF::produit()->select($i['id_produit']);
									
					
					if($facture["nature"]){
						ATF::produit_loyer()->q->reset()->where("id_produit",$i["id_produit"])
														->where("nature", $facture["nature"]);
						$loyer = ATF::produit_loyer()->select_row();
					}
					if(strpos($prod["produit"], "&nbsp;>") === false){
						if($ligne_produits[($prod["tva_loyer"]*100)-100]["produits"]) $ligne_produits[($prod["tva_loyer"]*100)-100]["produits"] .= "\n";
					

						$ligne_produits[($prod["tva_loyer"]*100)-100]["produits"] .= $i['quantite']." x ".$prod['produit'];

						if($prod["ref_lm"]){
							$ligne_produits[($prod["tva_loyer"]*100)-100]["produits"] .= " ( Ref: ".$prod["ref_lm"]." )";
						}

					}
					
				
					
					if($loyer){	
						$this->ttc = ($loyer["loyer"]*$prod["tva_loyer"]);
						$this->ttva = $this->ttc - $loyer["loyer"];

						$this->tva[($prod["tva_loyer"]*100)-100]["TVA"] += number_format($i['quantite']* ($this->ttva),2);
						$this->tva[($prod["tva_loyer"]*100)-100]["total"] += number_format($i['quantite']* ($this->ttc-$this->ttva),2);
						
						$ligne_produits[($prod["tva_loyer"]*100)-100]["HT"] += ($loyer["loyer"]*$i['quantite']);
						$ligne_produits[($prod["tva_loyer"]*100)-100]["TTC"] += (($loyer["loyer"]*$i['quantite'])*$prod["tva_loyer"]);

					}				
				}

				foreach ($ligne_produits as $key => $value) {
					if(strpos($value["produit"], "&nbsp;>") === false){						
						$data[$key][0] = $value["produits"];
						$style[$key][0] = $this->leftStyle;
						$data[$key][1] = number_format($value["HT"],2)." €" ;
						$data[$key][2] = $key."%";
						$data[$key][3] = number_format($value["TTC"],2)." €" ;
					}
					
				}
				

			}
			$this->tableauBigHead($head,$data,$width,7,$style,260);
		}

		if($nature == "prestation"){
			$head = array("N","Référence","Désignation","Prix unit. HT","Taux de TVA","Quantité","Total TTC");
			$width = array(8,24,80,20,20,20,20);
			if ($facture_lignes){
				foreach ($facture_lignes as $k => $i) {
					$prod = ATF::produit()->select($i['id_produit']);
									
					
					if($facture["nature"]){
						ATF::produit_loyer()->q->reset()->where("id_produit",$i["id_produit"])
														->where("nature", $facture["nature"]);
						$loyer = ATF::produit_loyer()->select_row();
					}

					if(strpos($value["produit"], "&nbsp;>") === false){	
						$data[$k][0] = $k+1;
						$data[$k][1] = $prod["ref_lm"];	
						$style[$k][1] = $this->leftStyle;
						$data[$k][2] = str_replace("&nbsp;","",str_replace("&nbsp;>", "", $prod['produit']));
						$style[$k][2] = $this->leftStyle;			
					}	
						if($loyer){
							if(strpos($value["produit"], "&nbsp;>") === false){	
								$data[$k][3] = number_format($loyer["loyer"],2)." €";
								$data[$k][4] = (($prod["tva_loyer"]-1)*100)." %";
								$data[$k][5] = $i['quantite'];
								$data[$k][6] = number_format(($loyer["loyer"]*$prod["tva_loyer"])*$i["quantite"],2)." €";
							}

							$this->ttc = ($loyer["loyer"]*$prod["tva_loyer"]);
							$this->ttva = $this->ttc - $loyer["loyer"];

							$this->tva[($prod["tva_loyer"]*100)-100]["TVA"] += number_format($i['quantite']* ($this->ttva),2);
							$this->tva[($prod["tva_loyer"]*100)-100]["total"] += number_format($i['quantite']* ($this->ttc-$this->ttva),2);
						}else{
							if(strpos($value["produit"], "&nbsp;>") === false){	
								$data[$k][3] = "-";
								$data[$k][4] = "-";
								$data[$k][5] = $i['quantite'];
								$data[$k][6] = "-";
							}
						}					
				}
			}
			$this->tableauBigHead($head,$data,$width,7,$style,260);
		}*/
	}

	public function formateTextPDF($texte){
		$texte = str_replace("\n", "", $texte);
		$texte = str_replace("<br>", "\n", $texte);
		$texte = str_replace("&nbsp;", " ", $texte);
		$texte = strip_tags($texte);

		return $texte;
	}



	/** PDF de l'échéancier d'une affaire
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 30-05-2011
	*/
	public function initEcheancier($id) {
		$this->affaire = ATF::affaire()->select($id);
		$this->commande = ATF::affaire()->getCommande($this->affaire['id_affaire'])->infos;
		$this->devis = ATF::devis()->select($this->commande['id_devis']);
		$this->client = ATF::societe()->select($this->devis['id_societe']);
		$this->societe = ATF::societe()->select($this->affaire['id_filiale']);
		ATF::facturation()->q->reset()->addCondition("id_affaire",$this->affaire['id_affaire'])
									  ->addOrder("date_periode_debut","ASC");
		$this->lignes = ATF::facturation()->sa();
	}
	
	function global_prolongation ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_prolongationSociete ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_prolongationCode ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_prolongationDate ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_facture_contrat_envoye($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_facture_contrat_envoyeSociete ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_facture_contrat_envoyeCode ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_facture_contrat_envoyeDate ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_factureSociete ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_factureCode ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_factureDate ($facture,$s){
		$this->global_facture($facture,$s);
	}

	function global_facture ($facture,$s){
		$this->open();
		foreach ($facture as $key => $item) {
			$this->facture($item,$s,true) ;
		}
	}


	function grille_prolongationclient_non_envoyeSociete($non_facturerSociete,$s) {
		$this->grille_client($non_facturerSociete,$s,true,true);
	}

	function grille_prolongationclient_non_envoyeCode($non_facturerCode,$s) {
		$this->grille_client($non_facturerCode,$s,true,true);
	}

	function grille_prolongationclient_non_envoyeDate($non_facturerDate,$s) {
		$this->grille_client($non_facturerDate,$s,true,true);
	}

	function grille_contratclient_non_envoyeSociete($non_facturerSociete,$s) {
		$this->grille_client($non_facturerSociete,$s,true);
	}

	function grille_contratclient_non_envoyeCode($non_facturerCode,$s) {
		$this->grille_client($non_facturerCode,$s,true);
	}

	function grille_contratclient_non_envoyeDate($non_facturerDate,$s) {
		$this->grille_client($non_facturerDate,$s,true);
	}

	function grille_prolongationclientSociete($facturerSociete,$s) {
		$this->grille_client($facturerSociete,$s,false,true);
	}

	function grille_prolongationclientCode($facturerCode,$s) {
		$this->grille_client($facturerCode,$s,false,true);
	}

	function grille_prolongationclientDate($facturerDate,$s) {
		$this->grille_client($facturerDate,$s,false,true);
	}

	function grille_contratclientSociete($facturerSociete,$s) {
		$this->grille_client($facturerSociete,$s,false);
	}

	function grille_contratclientCode($facturerCode,$s) {
		$this->grille_client($facturerCode,$s,false);
	}

	function grille_contratclientDate($facturerDate,$s) {
		$this->grille_client($facturerDate,$s,false);
	}


	function grille_client($facturer,$s,$nf=false,$prol=false) {
		$this->open();
		$this->addpage();
		$this->setfont('arial','B',15);

		$this->sety(10);
		
		$titre="GRILLE DE FACTURATION";

		if($prol){
			$titre.=" DE PROLONGATION";
		}

		if($nf){
			$titre.=" NON ENVOYÉES";
		}
		
		$this->multicell(0,10,$titre,0,'C');

		$this->SetLineWidth(0.35);
		$this->SetDrawColor(64,192,0);
		$this->line(0,35,220,35);
		$this->setLeftMargin(10);
		$this->sety(37);
		$this->setfont('arial','B',10);
		
		$date_debut=$facturer["reserve"]['date_debut'];
		$date_fin=$facturer["reserve"]['date_fin'];
		
		$this->multicell(0,3,"Grille de facturation pour la période du ".date("d/m/Y",strtotime($date_debut))." au ".date("d/m/Y",strtotime($date_fin)));
		$this->setfont('arial','',8);
		
		if($prol){
			$nbFac=$facturer["reserve"]["fp"];
		}else{
			$nbFac=$facturer["reserve"]["fc"];
		}
		
		if($nf){
			$liste="Liste des factures n'ayant pas été envoyées (".$nbFac.").";
		}else{
			$liste="Liste des factures ayant été envoyées (".$nbFac.").";
		}

		unset($facturer["reserve"]);

		$this->multicell(0,3,$liste);

		$this->setdrawcolor(0,0,0);
		$this->SetLineWidth(0.2);
		
		$head = array("SOCIETE","AFFAIRE","DATE DEBUT","DATE FIN","MONTANT ".$this->texteHT,"TYPE");
		if($nf){
			$head[]="RAISON";
		}else{
			$head[]="CONTACT FACTURATION";
		}
		$w = array(35,39,21,20,20,20,35);
		
		$this->setfont('arial','',6);
		
		$montant=0;	
		foreach ($facturer as $key => $item) {
			$txt = "";
			if($nf){
				if($item["cause"]=="an"){
					$societe=ATF::societe()->select($item["id_societe"]);
					$txt = "Pas de mail pour le contact ".ATF::contact()->nom($societe["id_contact_facturation"]);
				}elseif($item["cause"]=="pc"){
					$txt = "Pas de contact de facturation pour cette société";
				}elseif($item["cause"]=="pi"){
					$txt = "Problème lors de l'insertion de la facture";
				}
			}else{
				$txt = $item["email"];
			}
			$data[] = array(
				"(".ATF::societe()->select($item["id_societe"],"ref").") ".ATF::societe()->nom($item["id_societe"])
				,ATF::affaire()->select($item["id_affaire"],"ref")
				,date("d/m/Y",strtotime($item["date_periode_debut"]))
				,date("d/m/Y",strtotime($item["date_periode_fin"]))
				,number_format(($item["montant"]+$item["frais_de_gestion"]+$item["assurance"]),2,'.',' ')." €"
				,$item["type"]
				,$txt
			);
			$total+=($item["montant"]+$item["frais_de_gestion"]+$item["assurance"]);
		}
		$this->ln(5);
		
		$this->tableau($head,$data,$w);
		
		$this->ln(5);

		$this->setfont('arial','',8);
		$this->cell(0,3,"Montant total des factures envoyées : ".number_format($total,2, ',', ' ')." €",0,1);
	}
}
