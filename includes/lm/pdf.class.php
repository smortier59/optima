<?
require_once dirname(__FILE__)."/../pdf.class.php"; 
require_once dirname(__FILE__)."/../cleodis/pdf.class.php"; 
/**  
* @package Optima
* @subpackage Cleodis 
* @date 05-01-2011
*/
class pdf_lm extends pdf_cleodis {	
	public $logo = 'lm/lm.png';
	private $heightLimitTableContratA4 = 100; 

	private $leftStyle = array(
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
		$this->multicell(0,3,"S.A.S LEROY MERLIN ABONNEMENTS - Capital de 10 000 EUR - 820 472 009 RCS LILLE - N° C.E.E. FR 08820472009
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
			
		$this->sety(20);
		
	}


	/** Initialise les données pour générer une commande
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param int $id Identifiant commande
	*/
	private function commandeInit($id) {
		$this->commande = ATF::commande()->select($id);
			
		$this->colsProduit = array("border"=>"TB","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduit");
		$this->colsProduitFirst = array("border"=>"TLB","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitFirst");
		$this->colsProduitLast = array("border"=>"TBR","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitLast");
		$this->colsProduitAvecDetail = array("border"=>"T","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitAvecDetail");
		$this->colsProduitAvecDetailFirst = array("border"=>"TL","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitAvecDetailFirst");
		$this->colsProduitAvecDetailLast = array("border"=>"TR","bgcolor"=>"efefef","size"=>9,"flag"=>"colsProduitAvecDetailLast");
		$this->styleDetailsProduit = array("border"=>"LRB","decoration"=>"I","size"=>8,"flag"=>"styleDetailsProduit");
		ATF::commande_ligne()->q->reset()->where("visible","oui")->where("id_commande",$this->commande['id_commande']);
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

		if($this->affaire["nature"]=="avenant"){
			if($this->devis["type_contrat"] == "presta"){ $this->multicell(134,5,"AVENANT N°".ATF::affaire()->num_avenant($this->affaire["ref"])." AU CONTRAT DE PRESTATION N°".ATF::affaire()->select($this->affaire["id_parent"],"ref").($this->client["code_client"]?"-".$this->client["code_client"]:NULL)); 
			}else{ 	$this->multicell(134,5,"AVENANT N°".ATF::affaire()->num_avenant($this->affaire["ref"])." AU CONTRAT DE ".($this->affaire['nature']=="vente"?"VENTE":"LOCATION")." N°".ATF::affaire()->select($this->affaire["id_parent"],"ref").($this->client["code_client"]?"-".$this->client["code_client"]:NULL)); }
			$this->ln(5);
		}else{
			if($this->devis["type_contrat"] == "presta"){
				$this->multicell(134,5,"CONDITIONS PARTICULIERES du Contrat de PRESTATION n° : ".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL));
			}else{
				$this->multicell(134,5,"CONDITIONS PARTICULIERES du Contrat de ".($this->affaire['nature']=="vente"?"vente":"location")." n° : ".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL));
			
			}
			if($this->lignes && $this->affaire["nature"]=="AR"){
				foreach($this->AR as $k=>$i){
					$affaire=ATF::affaire()->select($i['id_affaire']);
					$ref_ar .=" ".$affaire["ref"]." (".$affaire["affaire"]."), ";
				}
				$this->ln(2);
				$this->setfont('arial','BI',7.5);				
				$this->multicell(134,5,"Annule et remplace le(s) contrat(s) n° ".$ref_ar." (cf. tableau descriptif des équipements) et reprend tout ou partie des matériels ainsi que tous leurs encours.",0,'L');
			} else {
				$this->ln(5);
			}
		}
		
		$this->setleftmargin(7);
		$this->setrightmargin(7);
		$this->setY(30);

		$this->setfont('arial','',8);		
		$this->setFillColor(200,200,200);
		$adresse = $this->societe["adresse"];
		if($this->societe["adresse_2"]) $adresse .= "\n".$this->societe["adresse_2"];
		if($this->societe["adresse_3"]) $adresse .= "\n".$this->societe["adresse_3"];
		$adresse .= "\n".$this->societe["cp"]." ".$this->societe["ville"];

		if($this->societe["tel"]) $adresse .= "\n"."Téléphone : ".$this->societe["tel"];
		if($this->societe["fax"]) $adresse .= "\n"."Télécopie : ".$this->societe["fax"];

		$y = $this->getY();		

		$this->multicell(80,4,$this->societe["societe"]."\n".$adresse,0,"L",1);

		
		$cadre2[] = "          ".strtoupper($this->client["civilite"]." ".$this->client["prenom"]." ".$this->client["nom"]);
		$cadre2[] = "          ".$this->client["adresse"];
		if($this->client["adresse_2"]) $cadre2[] = "          ".$this->client["adresse_2"];
		if($this->client["adresse_3"]) $cadre2[] = "          ".$this->client["adresse_3"];
		$cadre2[] = "          ".$this->client["cp"]." ".$this->client["ville"];
		
		$this->cadre(110,$y,90,35,$cadre2,false,0);
	
		$this->setxy(15,70);
		$this->SetDrawColor(0,0,0);
		$this->SetLineWidth(0.2);

		if($this->affaire["nature"]=="avenant"){
			$titre = "ARTICLE 1 : OBJET DE L'AVENANT";
			$texte = "L'objet de cet avenant concerne l'ajout et le retrait d'équipements au contrat de base cité en référence.";
		}else{
			$titre = "ARTICLE 1 : OBJET DU CONTRAT";			
			
			if($this->devis["type_contrat"] == "presta"){				
				$texte = "L'objet du contrat concerne les prestations dont le détail figure ci-après. ";
			}else{
				$texte = "L'objet du contrat est la ".($this->affaire['nature']=="vente"?"vente":"mise en location")." d'équipements dont le détail figure ci-après. ";
			}


			if ($this->affaire['nature']=="AR" && $this->AR) {
				$texte .= "Ce contrat annule et remplace le(s) contrat(s) suivant(s) : ";
				foreach ($this->AR as $k=>$i) {
					$texte .= ATF::affaire()->nom($i['id_affaire']).", ";
				}
			}
		}
		$this->setfont('arial','B',8);
		$this->cell(0,5,$titre,0,1);
		$this->setfont('arial','',8);
		$this->multicell(0,4,$texte,0,1);
			
		$w = array(20,35,35,106);
		
		$eq = "EQUIPEMENT(S)";	

		if($this->devis["type_contrat"] == "presta") $eq = "PRESTATION(S)";	


		if ($this->lignes) {
			$this->setFillColor(239,239,239);
			// Groupe les lignes par affaire
			$lignes=$this->groupByAffaire($this->lignes);
			// Flag pour savoir si le tableau part en annexe ou pas
			foreach ($lignes as $k => $i) {
				$this->setfont('arial','B',10);
				if (!$k) {
					if($this->devis["type_contrat"] == "presta"){ $title = "NOUVELLE(S) PRESTATION(S)"; }
					else{ $title = "NOUVEAU(X) EQUIPEMENT(S)"; }
					
				} else {
					$affaire_provenance=ATF::affaire()->select($k);
					if($this->affaire["nature"]=="avenant"){
						$title = $eq." RETIRE(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}elseif($this->affaire["nature"]=="AR"){
						$title = $eq." PROVENANT(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}elseif($this->affaire["nature"]=="vente"){
						$title = $eq." VENDU(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}
				}
				unset($data,$st);
				foreach ($i as $k_ => $i_) {
					$produit = ATF::produit()->select($i_['id_produit']);
					$ssCat = ATF::sous_categorie()->nom($produit['id_sous_categorie'])?ATF::sous_categorie()->nom($produit['id_sous_categorie']):"-";
					$fab = ATF::fabriquant()->nom($produit['id_fabriquant'])?ATF::fabriquant()->nom($produit['id_fabriquant']):"-";
					//On prépare le détail de la ligne
					$details=$this->detailsProduit($i_['id_produit'],$k,$i_['commentaire']);
					
					$etat = "";
										
					
					//Si c'est une prestation, on affiche pas l'etat
					if($produit["type"] == "sans_objet" || ($produit['id_sous_categorie'] == 16) || ($produit['id_sous_categorie'] == 114)) {	$etat = "";		}	
					
					if ($details == "") unset($details);
					$data[] = array(
						round($i_['quantite'])
						,$ssCat
						,$fab
						,str_replace("&nbsp;","",str_replace("&nbsp;>", "", $i_['produit'])).$etat
						,"details"=>$details
					);
						
					$st[] = array(
						($details?$this->colsProduitAvecDetailFirst:$this->colsProduitFirst)
						,($details?$this->colsProduitAvecDetail:$this->colsProduit)
						,($details?$this->colsProduitAvecDetail:$this->colsProduit)
						
						,($details?$this->colsProduitAvecDetailLast:$this->colsProduitLast)
						,"details"=>$this->styleDetailsProduit
					);
						
				}
				$tableau[$k] = array(
					"head"=>$head
					,"data"=>$data
					,"w"=>$w
					,"styles"=>$st
					,"title"=>$title
				);
			}
			unset($data,$st);
			$h = count($tableau)*5; //Ajout dans le calcul des titres de tableau mis a la main
			foreach ($tableau as $k=>$i) {
				if ($i['head']) $h += 5;
				$h += $this->getHeightTableau($i['head'],$i['data'],$i['w'],5,$i['styles']);
			}

			foreach ($tableau as $k=>$i) {
				$this->setFillColor(239,239,239);
				$this->setfont('arial','B',10);
				$this->multicell(0,5,$i['title'],1,'C',1);
				$this->setfont('arial','',8);
				if ($h>$this->heightLimitTableContratA4 || $this->commande["clause_logicielle"]=="oui") {
					$this->multicellAnnexe();
					$annexes[$k] = $i;
				} else {

					$this->tableau($i['head'],$i['data'],$i['w'],5,$i['styles']);
				}
			}
		}
		$this->ln(3);

				
		if ($this->affaire['nature']=="vente") {
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"ARTICLE 2 : PRIX DE VENTE");
			$this->setfont('arial','',8);
			$prix = $this->loyer[0]["loyer"]+$this->loyer[0]["assurance"]+$this->loyer[0]["frais_de_gestion"];
			$this->multicell(0,5,"Le prix de vente est fixé à ".number_format($prix,2,"."," ")." € ".$this->texteHT." soit ".number_format((($prix)*$this->commande["tva"]),2,"."," ")." € ".$this->texteTTC);
			$numArticle = 3;
			
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"ARTICLE ".$numArticle." : CONDITION DE PAIEMENT ET ECHEANCE");
			$numArticle++;
			$this->setfont('arial','',8);
			$this->multicell(0,5,"La facture est payable par chèque à 30 jours date de facture.");
			
			
		} else {
			//$this->sety(167);
			$this->setfont('arial','B',8);
			if($this->devis["type_contrat"] == "presta"){ $this->multicell(0,5,"ARTICLE 2 : DUREE"); }
			else{ $this->multicell(0,5,"ARTICLE 2 : DUREE DE LA LOCATION"); }
			
			$this->setfont('arial','B',10);
			$duree = ATF::loyer()->dureeTotal($this->devis['id_affaire']);
			$this->setfont('arial','',8);
			if($this->devis['loyer_unique']=='oui'){
				if($this->devis["type_contrat"] == "presta"){ $this->multicell(0,3,"La durée est identique à celle du contrat principal."); }
				else{ $this->multicell(0,3,"La durée d'engagement est identique à celle du contrat principal."); }
				
			}elseif($this->affaire["nature"]=="avenant"){
				if($this->devis["type_contrat"] == "presta"){	$texte = "La durée est fixée à ".$duree." mois"." à compter du "; }
				else{ $texte = "La durée d'engagement est fixée à ".$duree." mois"." à compter du "; }
				if($this->commande['date_debut']){
					$texte .= date("d/m/Y",strtotime($this->commande['date_debut'])).".";
				}
				$this->multicell(0,3,$texte);
			}else{
				if($this->devis["type_contrat"] == "presta"){ $this->multicell(0,3,"La durée est fixée à ".$duree." mois."); }
				else{ $this->multicell(0,3,"La durée d'engagement est fixée à ".$duree." mois."); }
				
			}
			$this->ln(2);
			
			if($this->devis['loyer_unique']=='oui'){
				$this->setfont('arial','B',8);
				$this->multicell(0,5,"ARTICLE 3 : LOYER UNIQUE");
				$this->setfont('arial','',8);
				$this->multicell(0,3,"Il est payable terme à échoir par ".ATF::$usr->trans($this->commande['type'],'commande')." et est fixe et non révisable pendant toute la durée de la location.");
				if(($this->loyer["loyer"]+$this->loyer["assurance"]+$this->loyer["frais_de_gestion"])>0){
					$this->multicell(0,3,"Le montant du loyer unique est fixé à ".number_format($this->loyer["loyer"]+$this->loyer["assurance"]+$this->loyer["frais_de_gestion"],2,"."," ")." € HT.");
				}else{
					$this->multicell(0,3,"Les loyers restent inchangés.");
				}
			}else{
				$this->setfont('arial','B',8);
				$this->multicell(0,5,"ARTICLE 3 : LOYERS");
				$this->setfont('arial','',7);
				$this->setfont('arial','',8);
				if ($this->affaire['nature']=="avenant"){
					$this->multicell(0,3,"Les loyers de l'avenant sont définis ainsi : ");
				}else{
					$this->multicell(0,3,"Ils sont payables termes à échoir par ".ATF::$usr->trans($this->commande['type'],'commande')." et sont fixes et non révisables pendant toute la durée de la location.");
				}
				if($duree){
					$donnee = array();
					$head = array("Nombre de Loyers","Périodicité : Mois (M) Trimestre (T) Semestre (S) ou Année (A)","Loyer ".$this->texteHT,"Loyer ".$this->texteTTC);
					foreach ($this->loyer as $k=>$i) {
						if($i["nature"] !== "prolongation"){
								$data[] = array(
								$i['duree']
								,strtoupper($i['frequence_loyer'])
								,number_format((($i['loyer']+$i["frais_de_gestion"]+$i["assurance"])/$this->commande["tva"]),2,"."," ")." €"
								,number_format($i["loyer"]+$i["frais_de_gestion"]+$i["assurance"],2,"."," ")." €"
								
							);
						}
					}
					$this->SetLineWidth(0.20);
					$this->ln(3);
					$this->tableau($head,$data,180,3);
				}
			}
			$numArticle = 4;
		}
		
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"ARTICLE ".$numArticle." : VALIDITE");
		$numArticle++;
		$this->setfont('arial','',8);
		$this->cell(0,6,"La présente proposition ne deviendra une offre ferme qu'après acceptation du Comité des Agréments de CLEODIS.",0,1);
		
		if ($this->commande["clause_logicielle"]=="oui") {
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"ARTICLE ".$numArticle." : MISE A DISPOSITION DES LOGICIELS");
			$this->ln(1);
			$this->setfont('arial','',8);
			$this->multicell(0,3,"ETANT PREALABLEMENT EXPOSE :");
			$this->multicell(0,3,"Pour les besoins de son activité, le Locataire a souhaité la mise à disposition d'une configuration informatique composée de matériels et de logiciels [ci-après désignés les «Logiciels »] objet du contrat ci-dessus référencé.");
			$this->multicell(0,3,"Le Locataire a obtenu du Fournisseur de pouvoir utiliser les Logiciels dans le cadre d'une licence dont il a approuvé les termes.");
			$this->multicell(0,3,"Le mode de souscription de ce droit d'utilisation s'effectue dans le cadre d'une mise à disposition temporaire convenue dans le cadre du Contrat en référence.");
			$this->multicell(0,3,"LE LOCATAIRE DECLARE :");
			$this->multicell(0,3,"=>reconnaître que le Contrat lui permet de bénéficier d'une mise à disposition des Logiciels et donc de l'utilisation de ceux-ci conformément à ses besoins;");
			$this->multicell(0,3,"=>qu'en cas de contradiction, les clauses du contrat ci-dessus référencé prévalent, dans ses relations avec le Loueur, sur celles qui régissent ou constituent la licence;");
			$this->multicell(0,3,"=>que les configurations informatiques seront livrées et installées, les prestations réalisées conformément à la commande qu'il a passé aux fournisseurs et selon les modalités convenues directement avec l'éditeur des Logiciels ou les prestataires et/ou les fournisseurs des matériels;");
			$this->multicell(0,3,"=>prendre livraison des configurations informatiques à ses frais et risques, et reconnaît avoir choisi seul sans que le Loueur et/ou l'Etablissement Cessionnaire du contrat n'interviennent en quoi que ce soit dans ce choix;");
			$this->multicell(0,3,"En conséquence, ce choix relevant de la responsabilité exclusive du Locataire, ce dernier s'engage à régler ponctuellement l'ensemble des sommes dues au titre du Contrat et ce, même en cas de défaillance des éditeurs ou de leurs logiciels ainsi que des Prestataires.");
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
		if ($this->affaire['nature']=="vente") {
			$t = "L'acheteur";
		} else {
			$t = "Le Locataire";
		}
		$this->cadre(20,$y,80,48,array(),$t);
		$cadre = array(
			"Fait à : "
			,"Le : "
			,"Nom : "
			,"Qualité : "
			,"Signature : "
		);
		if ($this->affaire['nature']=="vente") {
			$t = "Le Vendeur";
		} else {
			$t = "Le Loueur";
		}
		$this->cadre(110,$y,80,48,$cadre,$t);
			
		
		$this->setfont('arial','B',9);
		$this->setY(275.9);
		$this->multicell(0,1,"POUR ACCEPTATION DES CONDITIONS GENERALES AU VERSO",0,'C');
		if($this->devis["type_contrat"] == "presta"){	}
		else{ $this->conditionsGeneralesDeLocationA4($this->affaire['nature']); }
		
		if ($annexes) {
			$this->annexes($annexes);
		}
	}



	/** Groupe les produits par affaire de provenance
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param array tableau contenant toutes les lignes de produit
	*/
	private function groupByAffaire($lignes){
		$prov_encours="";
		//On fait en sorte d'avoir autant de tableau que de provenance
		foreach ($lignes as $k => $i) {
			if($i["id_affaire_provenance"]==$prov_encours){
				$tab_lignes_provenance[$prov_encours][]=$i;
			}else{
				$tab_lignes_provenance[$i["id_affaire_provenance"]][]=$i;
				$prov_encours=$i["id_affaire_provenance"];
			}
		}
		return $tab_lignes_provenance;	
	}

	private function tableauBigHead($head,$data,$width=false,$c_height=5,$style=false,$limitBottomMargin=270) {
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

	public function facture($id){
		$this->noPageNo = true;
		$this->setFooter();
		
		$this->A3 = false;
		$this->A4 = true;	

		$this->Open();
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
		$this->cell(134,5,"FACTURE N° ".$facture["ref"],0,1,"C");

		$this->setfont('arial','',8);
		$this->cell(134,5,"Date de facture ".date("d/m/Y", strtotime($facture["date"]))." Exemplaire client ",0,1,"C");
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

			
		if($facture["nature"]){
			$this->lignes_facture($facture_lignes,$facture,"loyer");
		}else{
			$this->lignes_facture($facture_lignes,$facture,"prestation");
		}	
		

		

		$this->ln(5);
		$y = $this->getY();
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
		$this->multicell(0,5,"TERMES DE PAIEMENTS \nLe ".date("d/m/Y", strtotime($facture["date_previsionnelle"])).", vous serez débité sur le compte : FRXX XXXX XXXX XXXX XXXX XXXX XXX – XXXXXXXX\nRUM XXXXXXXXXX\nICS XXXXXX");

		
		$this->setY($y);
		$this->setX(130);

		$this->setfont('arial','B',10);
		$this->cell(50,10,"Total TTC",1,0);
		$this->cell(20,10,$facture["prix"]." €",1,1,"C");
		

	}

	public function lignes_facture($facture_lignes,$facture, $nature){
		

		if($nature == "loyer"){
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
					
					if($ligne_produits[($prod["tva_loyer"]*100)-100]["produits"]) $ligne_produits[($prod["tva_loyer"]*100)-100]["produits"] .= "\n";
					$ligne_produits[($prod["tva_loyer"]*100)-100]["produits"] .= $i['quantite']." x ".str_replace("&nbsp;","",str_replace("&nbsp;>", "", $prod['produit']));

					if($prod["ref_lm"]){
						$ligne_produits[($prod["tva_loyer"]*100)-100]["produits"] .= " ( Ref: ".$prod["ref_lm"]." )";
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
					$data[$key][0] = $value["produits"];
					$style[$key][0] = $this->leftStyle;
					$data[$key][1] = number_format($value["HT"],2)." €" ;
					$data[$key][2] = $key."%";
					$data[$key][3] = number_format($value["TTC"],2)." €" ;
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

					$data[$k][0] = $k+1;
					$data[$k][1] = $prod["ref_lm"];	
					$style[$k][1] = $this->leftStyle;
					$data[$k][2] = str_replace("&nbsp;","",str_replace("&nbsp;>", "", $prod['produit']));
					$style[$k][2] = $this->leftStyle;			
					if($loyer){
						$data[$k][3] = number_format($loyer["loyer"],2)." €";
						$data[$k][4] = (($prod["tva_loyer"]-1)*100)." %";
						$data[$k][5] = $i['quantite'];
						$data[$k][6] = number_format(($loyer["loyer"]*$prod["tva_loyer"])*$i["quantite"],2)." €";
							
						$this->ttc = ($loyer["loyer"]*$prod["tva_loyer"]);
						$this->ttva = $this->ttc - $loyer["loyer"];

						$this->tva[($prod["tva_loyer"]*100)-100]["TVA"] += number_format($i['quantite']* ($this->ttva),2);
						$this->tva[($prod["tva_loyer"]*100)-100]["total"] += number_format($i['quantite']* ($this->ttc-$this->ttva),2);
					}else{
						$data[$k][3] = "-";
						$data[$k][4] = "-";
						$data[$k][5] = $i['quantite'];
						$data[$k][6] = "-";
					}					
				}
			}
			$this->tableauBigHead($head,$data,$width,7,$style,260);
		}
	}


	public function conditionsGeneralesDeLocationA4($type){
		$this->unsetHeader();
		$this->AddPage();
		$this->SetLeftMargin(10);

		$articles = ATF::cgl_article()->sa();
		
		foreach ($articles as $key => $value) {
			$this->setfont('arial','BI',10);	
			$this->cell(0,5,"Article ".$value["numero"]." - ".$value["titre"],0,1);

			$this->setfont('arial','',8);
			$texte = NULL;
			ATF::cgl_texte()->q->reset()->where("id_cgl_article",$value["id_cgl_article"])
										->addOrder("cgl_texte.numero");
			$texte = ATF::cgl_texte()->select_all();
			if($texte){
				if(count($texte)>1){
					foreach ($texte as $k => $v) {
						$v["texte"] = $this->formateTextPDF($v["texte"]);

						if($v["numero"]) $this->multicell(0,4,$value["numero"].".".$v["numero"].". ".$v["texte"]);
						else $this->multicell(0,4,$v["texte"]);
					}
				}else{
					$texte[0]["texte"] = $this->formateTextPDF($texte[0]["texte"]);					
					$this->multicell(0,4,$texte[0]["texte"]);
				}
			}
			$this->ln(3);
			
		}
	}

	public function formateTextPDF($texte){
		$texte = str_replace("\n", "", $texte);
		$texte = str_replace("<br>", "\n", $texte);
		$texte = str_replace("&nbsp;", " ", $texte);
		$texte = strip_tags($texte);

		return $texte;
	}
}
