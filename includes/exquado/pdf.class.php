<?
require_once dirname(__FILE__)."/../pdf.class.php"; 
/**  
* @package Optima
* @subpackage Cleodis 
* @date 05-01-2011
*/
class pdf_exquado extends pdf {
	
	private $heightLimitTableDevisClassique = 80;
	private $heightLimitTableDevisVente = 65;
	private $heightLimitTableContratA4 = 80; 
	private $heightLimitTableContratPV = 100; 
	private $heightLimitTableContratA43 = 175;
	private $heightLimitTableBDC = 85;
	
	private $noPageNo = false;
	
	public $site_web = false;
	public $logo = 'cleodis/logo.jpg';
	public $logo_site = 'cleodis/formation.png';

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
		if ($this->A3) {
			if(ATF::$codename == "cleodisbe"){
				//Numéro de page centré
				$this->ATFSetStyle($style);
				$this->SetXY(10,-10);
				$this->multicell(200,3,$this->societe['societe']." ".$this->societe['structure']." au capital de ".number_format($this->societe["capital"],2,'.',' ')." € - N° de TVA BE.0811.464.584 - ".$this->societe['web'],0,'C');
				$this->SetXY(210,-10);
				$this->multicell(200,3,$this->societe['societe']." ".$this->societe['structure']." au capital de ".number_format($this->societe["capital"],2,'.',' ')." € - N° de TVA BE.0811.464.584 - ".$this->societe['web'],0,'C');
			}else{
				//Numéro de page centré
				$this->ATFSetStyle($style);
				$this->SetXY(10,-10);
				$this->multicell(200,3,$this->societe['societe']." ".$this->societe['structure']." au capital de ".number_format($this->societe["capital"],2,'.',' ')." € - SIREN ".$this->societe["siren"]." - ".$this->societe['web'],0,'C');
				$this->SetXY(210,-10);
				$this->multicell(200,3,$this->societe['societe']." ".$this->societe['structure']." au capital de ".number_format($this->societe["capital"],2,'.',' ')." € - SIREN ".$this->societe["siren"]." - ".$this->societe['web'],0,'C');
			}
			$this->SetX(10);
			$this->multicell(200,3,$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville']." - ".strtoupper(ATF::pays()->nom($this->societe['id_pays']))." - Tél : ".$this->societe['tel']." - Fax : ".$this->societe['fax'],0,'C');
			$this->SetXY(210,-7);
			$this->multicell(200,3,$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville']." - ".strtoupper(ATF::pays()->nom($this->societe['id_pays']))." - Tél : ".$this->societe['tel']." - Fax : ".$this->societe['fax'],0,'C');
			$this->ln(-3);
			$this->SetLeftMargin($savelMargin);
		} else {
			//Numéro de page centré
			$this->ATFSetStyle($style);
			$this->SetXY(10,-10);
			if(ATF::$codename == "cleodisbe"){
				$this->multicell(0,3,$this->societe['societe']." ".$this->societe['structure']." au capital de ".number_format($this->societe["capital"],2,'.',' ')." € - N° de TVA BE.0811.464.584 - ".$this->societe['web'],0,'C');
			
			}else{
				$this->multicell(0,3,$this->societe['societe']." ".$this->societe['structure']." au capital de ".number_format($this->societe["capital"],2,'.',' ')." € - SIREN ".$this->societe["siren"]." - ".$this->societe['web'],0,'C');
			}
			
			$this->SetX(10);
			$this->multicell(0,3,$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville']." - ".strtoupper(ATF::pays()->nom($this->societe['id_pays']))." - Tél : ".$this->societe['tel']." - Fax : ".$this->societe['fax'],0,'C');
			if (!$this->noPageNo) {
				$this->ln(-3);
				$this->Cell(0,3,$this->noPageNo.'Page '.$this->PageNo(),0,0,'R');
			}
			$this->SetLeftMargin($savelMargin);
		}

		if($this->previsu){
			$this->setfont('arial','BU',18);
			$this->sety(10);
			$this->multicell(0,5,"PREVISUALISATION",0,'C');
		}

	}
	
	/* Retourne une numérotation de page spécifique
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	public function PageNo() {
		if($this->A4 && parent::PageNo()>1) {
			// Pas de page numéro 2, ca rc'est lannexe pré-imprimée
			return parent::PageNo()+1;
		}
		return parent::PageNo();
	}
	*/
	
	/* Header spécifique aux documents cléodis
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	*/
	public function Header() {	
		$this->texteHT = "HT";
		$this->texteTTC = "TTC";
		if(ATF::$codename == "cleodisbe"){ 
			$this->texteHT = "HTVA";
			$this->texteTTC = "TVAC"; 
		}

		if ($this->getHeader()) return false;
		if ($this->A3) {
			$this->image(__PDF_PATH__.$this->logo,295,5,35);
			$this->sety(20);
		} elseif ($this->relance || $this->envoiContrat) {
            $this->image(__PDF_PATH__.$this->logo,75,10,60);
            $this->setfont('arial','',11);
            if ($this->client) {
                $cadre = array(
                    array("txt"=>$this->client['societe'],"size"=>12,"bold"=>true)
                    ,array("txt"=>($this->contact?"A l'attention de ".ATF::contact()->nom($this->contact['id_contact']):""),"italic"=>true,"size"=>8)
                    ,array("txt"=>$this->client["adresse"],"size"=>10)
                );  
                if ($this->client["adresse2"]) $cadre[] = array("txt"=>$this->client["adresse2"],"size"=>10); 
                if ($this->client["adresse3"]) $cadre[] = array("txt"=>$this->client["adresse3"],"size"=>10); 
                $cadre[] = array("txt"=>$this->client["cp"]." ".$this->client["ville"],"size"=>10); 
                $this->cadre(110,45,85,35,$cadre);
            }            
            $this->setfont('arial','',12);            	    
        } else {

        	if($this->site_web){ $this->unsetHeader(); }else{ $this->image(__PDF_PATH__.$this->logo,170,5,35); }
			
			$this->sety(20);
		}
	}

	/* Ecrit un titre d'article pour les CGV
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	* @param int $id Id du devis
	*/
	function article($x,$y,$num,$titre,$police,$h=3,$w=0){
		$this->setxy($x,$y);
		$this->setfont('arial','BI',$police);
		$this->multicell($w,$h,"Article ".$num." : ".$titre,0,'L');
		$this->ln($h);
		if ($police != 8)	$this->setfont('arial','',8);
	}

	/* Initialise les données pour la génération d'un devis et redirige vers la bonne fonction.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	* @param int $id Id du devis
	*/
	public function devis($id,$s) {

		$this->devis = ATF::devis()->select($id);
		$this->loyer = ATF::loyer()->ss('id_affaire',$this->devis['id_affaire']);
		
		ATF::devis_ligne()->q->reset()->where("visible","oui")->where("id_devis",$this->devis['id_devis']);
		$this->lignes = ATF::devis_ligne()->sa();
		
		$this->user = ATF::user()->select($this->devis['id_user']);
		$this->societe = ATF::societe()->select($this->user['id_societe']);
		$this->client = ATF::societe()->select($this->devis['id_societe']);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);
		$this->affaire = ATF::affaire()->select($this->devis['id_affaire']);
		$this->agence = ATF::agence()->select($this->user['id_agence']);

		if($this->devis["type_devis"] === "optic_2000"){
			$this->devisoptic_2000($id);			
		}else{
			/* PAGE 1 */
			$this->unsetHeader();
			$this->Addpage();
			$this->SetLeftMargin(15);
					
			$this->RoundedRect(15,50,180,70,5);		
			$this->image(__PDF_PATH__.$this->logo,90,55,35);
			$this->sety(90);
			$this->setfont('arial','B',12);
			$this->multicell(0,5,$this->societe['societe'],0,'C');
			$this->ln(10);
			$this->multicell(0,5,date("d/m/Y",strtotime($this->devis['date'])),0,'C');

			$this->setfont('arial','',8);
			$this->RoundedRect(15,140,85,70,5);		
			$this->setxy(15,145);
			$this->multicell(85,10,ATF::user()->nom($this->user['id_user']));
			$this->multicell(85,5,"Tél : ".($this->user['tel']?$this->user['tel']:$this->societe['tel']));
			$this->multicell(85,5,"GSM : ".($this->user['gsm']?$this->user['gsm']:$this->societe['gsm']));
			$this->multicell(85,5,"Fax : ".($this->user['fax']?$this->user['fax']:$this->societe['fax']));
			$this->ln(5);
			$this->multicell(85,5,($this->user['email']?$this->user['email']:$this->societe['email']));

			$this->RoundedRect(110,140,85,70,5);		
			$this->sety(145);
			$this->setleftmargin(110);
			$this->multicell(85,10,$this->client['societe']);
			$this->multicell(85,5,$this->client['adresse']);
			$this->multicell(85,5,$this->client['cp']." ".$this->client['ville']);
			$this->ln(5);
			$this->multicell(85,5,"A l'attention de ".ATF::contact()->nom($this->contact['id_contact']));
			$this->setleftmargin(15);

			if($this->devis['type_contrat'] =='vente'){
				$this->devisVente();
			}elseif($this->affaire['nature'] =='avenant'){
				$this->devisAvenant();
			}else{
				$this->devisClassique();
			}
		}
		return true;

	}
	
	/* Génère le PDF d'un devis de vente
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	*/
	private function devisVente(){
		if (!$this->devis) return false;
		/* PAGE 2 */
		$this->setHeader();
		$this->AddPage();
		$this->SetLeftMargin(15);
		$this->sety(30);
		$this->setfont('arial','BU',14);
		$this->multicell(0,5,"Le contrat de vente CLEODIS");
		$this->ln(5); 
		
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"Qui sommes nous ?");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Créée par des professionnels de la location évolutive, CLEODIS s'appuie sur un réseau d'établissements financiers partenaires - filiales spécialisées des grandes banques françaises et européennes - pour mettre en place ses contrats de location.");
		$this->ln(5);
		$this->setfont('arial','I',8);
		$this->multicell(0,5,"Réduire les coûts  de détention du parc informatique de ses clients, liés à l'utilisation de leur système d'information, tel est l'objectif de l'équipe de proximité, professionnelle et réactive de CLEODIS.",1,'C');
		
		$this->sety(80);
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"Domaines d'intervention");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Cléodis intervient sur l'ensemble des composants du système d'information de l'entreprise :");
		$this->SetLeftMargin(25);
		$this->multicell(0,5,"> Informatique : poste de travail, portable, serveur, réseau...");
		$this->multicell(0,5,"> Bureautique : imprimantes, copieurs, fax...");
		$this->multicell(0,5,"> Télécommunication : standards, PABX, réseau IP...");
		$this->multicell(0,5,"> Communication – Multimédia : borne interactive, écrans plats...");
		$this->SetLeftMargin(15);
		
		$this->multicell(0,5,"L'objectif de CLEODIS est de proposer à ses clients des solutions pertinentes et cohérentes en rapport avec leur envergure et leur budget.");
		$this->multicell(0,5,"Afin de toujours proposer les meilleures offres, CLEODIS travaille en partenariat avec des distributeurs, éditeurs et SSII locales et nationales, validés pour leurs compétences et leur professionnalisme,  capables d'appréhender toute problématique autour du système d'information.");
		
		$this->image(__PDF_PATH__.'cleodis/domaine.jpg',60,160,81);

	//BON POUR ACCORD, PAGE 4 DEVIS

		$this->Addpage();
		$this->setfont('arial','BU',18);
		$this->multicell(0,10,"Bon pour accord",0,'C');
		
		$this->sety(30);
		$this->setfont('arial','',10);
		$this->cell(50,5,"Offre",1,0,'C');
		$this->cell(70,5,"Client",1,0,'C');
		$this->cell(60,5,"Contact",1,1,'C');
		$this->cell(50,5,$this->devis['ref'],1,0,'C');
		$this->cell(70,5,$this->client['societe'],1,0,'C');
		$this->cell(60,5,ATF::contact()->nom($this->contact["id_contact"]),1,1,'C');

		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->multicell(0,10,"TABLEAU DE SYNTHESE DE L'OFFRE : MATERIEL / LOGICIEL / PRESTATION");

		$total=$this->devis["prix"];

		if ($this->lignes) {
			// Groupe les lignes par affaire
			$lignes=$this->groupByAffaire($this->lignes);
			// Flag pour savoir si le tableau part en annexe ou pas
			$flagOnlyEquipementNeuf = true;
			$flagOnlyPrixInvisible = true;
			foreach ($lignes as $k => $i) {
				if (!$k) {
					$title = "NOUVEAU(X) EQUIPEMENT(S)";
				} else {
					$flagOnlyEquipementNeuf = false;
					$affaire_provenance=ATF::affaire()->select($k);
					$title = "EQUIPEMENT(S) VENDU(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
				}

				$head = array("Référence","Désignation","Qté","Prix unitaire HT","Total HT");
				$w = array(40,93,12,20,20);
				unset($data,$st);
				foreach ($i as $k_ => $i_) {
					if ($i_['visibilite_prix']=="visible") {
						$flagOnlyPrixInvisible = false;
					}
					$etat = "";
					if($i_["neuf"] == "non"){
						$etat = "( OCCASION )";
					}
															
					//Si c'est une prestation, on affiche pas l'etat
					if(($produit["type"] == "sans_objet") || ($produit['id_sous_categorie'] == 16) || ($produit['id_sous_categorie'] == 114)){  $etat = ""; }	
					
					if(ATF::$codename == "cleodisbe"){ $etat = ""; }
					
					$produit = ATF::produit()->select($i_['id_produit']);
					$marque =  ATF::fabriquant()->nom($produit['id_fabriquant']);
					$sous_type =  ATF::sous_categorie()->nom($produit['id_sous_categorie']);
					$data[] = array(
						$i_['ref']
						,$i_['produit']." ".$etat
						,round($i_['quantite'],0)
						,($i_['visibilite_prix']=="visible")?number_format($i_['prix_achat'],2,","," ")." €":"NC"
						,($i_['visibilite_prix']=="visible")?number_format($i_['quantite']*$i_['prix_achat'],2,","," ")." €":"NC"
					);

					//$total+=($i_['quantite']*$i_['prix_achat']);
				}
				$tableau[$k] = array(
					"head"=>$head
					,"data"=>$data
					,"w"=>$w
					,"styles"=>$st
					,"title"=>$title
				);
			}

			$h = count($tableau)*5; //Ajout dans le calcul des titres de tableau mis a la main
			$h += 10; //Ajout dans le calcul des deus lgnes de total mis a la find u tableau
			foreach ($tableau as $k=>$i) {
				if ($i['head']) $h += 5;
				$h += $this->getHeightTableau($i['head'],$i['data'],$i['w'],5,$i['styles']);
			}
			foreach ($tableau as $k=>$i) {
				$this->setFillColor(239,239,239);
				$this->setfont('arial','B',10);
				$this->multicell(0,5,$i['title'],1,'C',1);
				$this->setfont('arial','',8);
				if (!$flagOnlyEquipementNeuf || $flagOnlyPrixInvisible) {
					array_pop($i['head']);
					array_pop($i['head']);
					$i['w'][1] += array_pop($i['w']);	
					$i['w'][1] += array_pop($i['w']);	
					array_pop($i['styles']);	
					array_pop($i['styles']);	
					foreach ($i['data'] as $k_=>$i_) {
						array_pop($i['data'][$k_]);	
						array_pop($i['data'][$k_]);	
					}
				}
				if ($h>$this->heightLimitTableDevisVente) {
					$this->multicellAnnexe();
					$annexes[$k] = $i;
				} else {
					$this->tableauBigHead($i['head'],$i['data'],$i['w'],5,$i['styles']);
				}
			}
			
			if ($flagOnlyEquipementNeuf) {
				$totalTTC = $total * $this->devis["tva"];
			} else {
				$total = $this->devis["prix"];
				$totalTTC = $this->devis["prix"] * $this->devis["tva"];
			}
			$totalTable = array(
				"data"=>array(
								array("","Total ".$this->texteHT,number_format($total,2,'.',' ')." €")
								,array("","Total ".$this->texteTTC,number_format($totalTTC,2,'.',' ')." €")
							)
				,"styles"=>array(
									array(array("border"=>" "),"","")
									,array(array("border"=>" "),"","")
								)
				,"w"=>array(135,25,25)
			);
			if (!$annexes) {
				$this->tableau(false,$totalTable['data'],$totalTable['w'],5,$totalTable['styles']);
			}

		}
		

		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->cell(60,5,"INTERLOCUTEUR CLEODIS :",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,ATF::user()->select($this->user['id_user'],"civilite").". ".ATF::user()->nom($this->user['id_user']),0,1);
		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->cell(60,5,"NATURE DE LA PRESTATION :",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,$this->devis['devis'],0,1);
		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->cell(60,5,"CLAUSE DE PROPRIETE",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,"Le transfert de propriété des travaux et marchandises n'est effectif qu'après le règlement complet ",0,1);
		$this->cell(0,5,"et constaté de l'ensemble des lots.",0,1);
		$this->cell(0,5,"Jusqu'à cette date, Cleodis conserve l'entière propriété des prestations et matériels acquis ou produits.",0,1);
		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->cell(60,5,"CONDITIONS FINANCIERES :",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,"Notre prix forfaitaire pour la prestation s'élève à : ",0,1);

		$this->ln(5);
		if (strpos($totalTTC,'.') || strpos($totalTTC,',')) {
			$prix_en_lettre = util::nb2texte(substr($totalTTC,0,strpos($totalTTC,'.')))."euros et ".util::nb2texte(($totalTTC-floor($totalTTC))*100)."centimes toutes taxes comprises";
		}else{
			$prix_en_lettre = util::nb2texte(substr($totalTTC,0,strlen($totalTTC)))."euros toutes taxes comprises";
		}		
		$this->RoundedRect(15,$this->gety(),180,10,3);		
		$this->setfont('arial','B',10);
		$this->multicell(0,5,number_format($totalTTC,2,'.',' ')." € ".$this->texteTTC."\n".$prix_en_lettre,0,"C");
		$this->setfont('arial','',10);

		$this->ln(3);
		$this->setfont('arial','BU',10);
		$this->cell(60,5,"TERMES DE PAIEMENT",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,"A réception de facture",0,1);
		
		$this->ln(3);
		$y = $this->gety();
		$this->cadre(15,$y,85,40,array("Date / Cachet / Visa","","",""),"Partie réservée au client");		
		$this->cadre(110,$y,85,40,array("Date / Cachet / Visa","","",""),"Partie réservée à Cléodis");		

		$this->setfont('arial','B',10);
		$this->multicell(0,4,"A faxer au : ".$this->agence['fax'],0,'C');
		$this->setfont('arial','',10);
		
		if ($annexes) {
			$this->annexes($annexes);
			$this->tableau(false,$totalTable['data'],$totalTable['w'],5,$totalTable['styles']);
		}
	}
	
	/* Génère le PDF d'un devis Classique
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	*/
	private function devisClassique() {
		if (!$this->devis) return false;
		
		/* PAGE 2 */
		$this->setHeader();
		$this->setTopMargin(30);
		$this->addpage();
		
		$this->setfont('arial','BU',14);
		$this->multicell(0,5,"1 – La ".ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." CLEODIS");
		$this->ln(5);
		
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"Un constat simple");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Nombre d'entreprises et de collectivités rencontrent des problèmes liés à l'évolution permanente des systèmes d'informations et donc de leur infrastructure :");
		$this->setleftmargin(25);
		$this->multicell(0,5,">Obsolescence rapide des matériels liés à la sortie régulière de nouveaux logiciels ou versions,");
		$this->multicell(0,5,">Problème d'homogénéité du parc compliquant les échanges entre utilisateurs internes et externes (clients, fournisseurs ...) et ne permettant pas l'utilisation d'un nombre réduit de « masters » qui contribuerait à une simplification des procédure d'installation et de réinstallation (suite à opération de maintenance) des postes de travail,");
		$this->multicell(0,5,">Coût de maintenance élevé passé la garantie constructeur : l'année de garantie supplémentaire ou le recours à une tierce maintenance grève sensiblement le budget informatique pour conserver des machines en fin de vie,");
		$this->multicell(0,5,">Difficulté à voir clair sur un marché technique et promotionnel : sur un marché en perpétuelle évolution où la dernière technologie coûte chère (30%) et où les promotions sont légion, le soutien et le conseil d'un professionnel indépendant vous assure des matériels parfaitement adaptés au meilleur prix.");
		$this->setleftmargin(15);
		
		$this->ln(10);
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"La réponse de CLEODIS");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"L'objectif de CLEODIS est la mise à disposition de matériels adaptés aux besoins de chaque utilisateur en recherchant la plus grande homogénéité du parc tout en optimisant les coûts liés à la gestion et l'évolution des systèmes d'information.");
		$this->multicell(0,5,"Pour ce faire CLEODIS s'appuie sur un contrat de location, support financier idéal par sa souplesse, sa capacité à évoluer à tout moment et à intégrer des services de gestion quotidienne du parc. Ainsi, il permet une mise à jour régulière de l'adéquation entre les besoins techniques et les ressources financières.");
		
		$this->multicell(0,5,"Schéma comparatif LLD CLEODIS / ACHAT ou CREDIT-BAIL :");
		$this->image(__PDF_PATH__.'cleodis/graph.jpg',50,155,100);
		
		$this->sety(240);
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"Qui sommes nous ?");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Créée par des professionnels de la location évolutive, CLEODIS s'appuie sur un réseau d'établissements financiers partenaires - filiales spécialisées des grandes banques françaises et européennes - pour mettre en place ses contrats de location.");
		$this->ln(5);
		$this->setfont('arial','I',8);
		$this->RoundedRect(15,$this->gety(),180,10,3);		
		$this->multicell(0,5,"Réduire les coûts  de détention du parc informatique de ses clients, liés à l'utilisation de leur système d'information, tel est l'objectif de l'équipe de proximité, professionnelle et réactive de CLEODIS.",0,'C');
		$this->setfont('arial','',8);
		
		/* PAGE 3 */
		$this->AddPage();

		$this->setfont('arial','U',10);
		$this->multicell(0,5,"Domaines d'intervention");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Cléodis intervient sur l'ensemble des composants du système d'information de l'entreprise :");
		$this->SetLeftMargin(25);
		$this->multicell(0,5,"> Informatique : poste de travail, portable, serveur, réseau...");
		$this->multicell(0,5,"> Bureautique : imprimantes, copieurs, fax...");
		$this->multicell(0,5,"> Télécommunication : standards, PABX, réseau IP...");
		$this->multicell(0,5,"> Communication – Multimédia : borne interactive, écrans plats...");
		$this->SetLeftMargin(15);
		
		$this->multicell(0,5,"L'objectif de CLEODIS est de proposer à ses clients des solutions pertinentes et cohérentes en rapport avec leur envergure et leur budget.");
		$this->multicell(0,5,"Afin de toujours proposer les meilleures offres, CLEODIS travaille en partenariat avec des distributeurs, éditeurs et SSII locales et nationales, validés pour leurs compétences et leur professionnalisme,  capables d'appréhender toute problématique autour du système d'information.");

		$this->image(__PDF_PATH__.'cleodis/domaine.jpg',60,85,81);

		$this->sety(160);
		$this->setfont('arial','BU',14);
		$this->multicell(0,5,"2 – La Location Longue Durée selon CLEODIS");
		$this->ln(5);
		
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"La location évolutive, un moyen plutôt qu'un objectif ");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Le contrat de location n'est pas une finalité en soi ; c'est simplement le meilleur outil permettant la mise à jour de votre système d'information au meilleur coût. Il suffit de le comparer aux autres modes de financement :");
		$this->ln(5);
		$this->setfont('arial','B',8);
		$this->multicell(0,5,">L'achat sur fonds propres");
		$this->setfont('arial','',8);
		$this->multicell(0,5,"Acheter ses matériels revient à penser qu'un investissement informatique est réalisé pour une période longue.");
		$this->multicell(0,5,"Hors vouloir prolonger la durée de vie de ses matériels coûte souvent plus cher que de les renouveler :");
		$this->SetLeftMargin(25);
		$this->multicell(0,5,"> fin des périodes de garantie et donc recours à des contrats de maintenance onéreux");
		$this->multicell(0,5,"> logiciels en retard sur les clients, fournisseurs d'où problèmes d'échanges de données");
		$this->multicell(0,5,"> baisse des performances des machines et donc de baisse de compétitivité");		
		$this->multicell(0,5,"> pas de valeur marché des équipements sortants....");
		$this->SetLeftMargin(15);
		
		$this->multicell(0,5,"De plus, l'achat sur fonds propres rend plus difficile les évolutions régulières et non programmées longtemps à l'avance : amortissement comptable non achevé, demande d'investissement souvent mal comprise par la direction financière, projet lourd à mener seul....");

		$this->ln(5);
		$this->setfont('arial','B',8);
		$this->multicell(0,5,">Crédit classique ou Crédit Bail");
		$this->setfont('arial','',8);
		$this->multicell(0,5,"La démarche de réaliser un crédit, quelle qu'en soit sa forme, n'apporte rien de plus que l'achat sur fonds propres. En effet, subsistent tous les problèmes liés à l'évolution avant le terme du financement, vous restez seul face aux choix à réaliser et vous continuez d'obérer votre capacité d'investissement sur des projets directement liés à votre coeur de métier.");

		/*	PAGE 4	*/
		$this->AddPage();
		
		$this->sety(25);
		$this->setFontDecoration('B');
		$this->multicell(0,5,"=> La location évolutive, un moyen efficace");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"En dehors des services CLEODIS qui facilitent la gestion quotidienne et optimisent les coûts de détention de votre parc, le contrat de location constitue intrinsèquement la solution à ces problématiques d'évolution :");
		$this->setFontDecoration('B');
		$this->setx(35);
		$this->multicell(0,5,"Une charge inscrite au compte de résultat n'obérant pas la capacité d'investissement ;");
		$this->setx(35);
		$this->multicell(0,5,"La possibilité d'évoluer à tout moment sur tout ou partie du contrat : modification des loyers, matériels ou logiciels ;");
		$this->setx(35);
		$this->multicell(0,5,"Un budget mensuel maintenu à périmètre équivalent.");
		$this->unsetFontDecoration();
		
		$this->ln(5);
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"La location selon CLEODIS, une valeur ajoutée clairement exprimée");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->setFontDecoration('B');
		$this->multicell(0,5,">Conseil");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"Forte de l'expérience de ses collaborateurs dans le monde informatique, CLEODIS est à même de vous conseiller sur les orientations stratégiques de votre système d'information : tant sur le plan Matériel que Logiciel.");
		$this->multicell(0,5,"CLEODIS intervient sur tout type de logiciel et de plate formes : INTEL, AS400, UNIX mais également Télécom et réseau. CLEODIS vous conseille et vous accompagne dans l'étude et la mise en place de vos projets informatiques.");
		$this->multicell(0,5,"Pour une refonte complète de votre système d'information, CLEODIS peut faire appel à des consultants externes qu'elle missionne pour votre compte.");
		$this->setFontDecoration('B');
		$this->multicell(0,5,">Mise en relation");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"CLEODIS n'a pas pour objectif de réaliser des prestations techniques ou même de vous distribuer des matériels, CLEODIS souhaite vous faciliter le montage du projet en vous mettant en relation avec des SSII, VAR ou éditeur après avoir qualifié ensemble votre projet.");
		$this->multicell(0,5,"Ces sociétés sont référencées par CLEODIS en fonction d'un certain nombre de critères :");
		$this->setx(40);
		$this->multicell(0,5,"- compétences spécifiques");
		$this->setx(40);
		$this->multicell(0,5,"- sérieux, professionnalisme et qualité des prestations");
		$this->setx(40);
		$this->multicell(0,5,"- proximité");
		$this->setFontDecoration('B');
		$this->multicell(0,5,">Suivi de projet, maîtrise d'ouvrage");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"Au-delà du montage du dossier locatif, CLEODIS vous accompagne dans la mise en oeuvre de votre projet jusqu'à sa complète réalisation.");
		$this->setFontDecoration('B');
		$this->multicell(0,5,">Mise en location");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"Conjointement à la remise des devis établis en fonction du cahier des charges défini ensemble, CLEODIS vous remettra une proposition locative intégrant :");
		$this->setx(40);
		$this->multicell(0,5,"- Montant de l'investissement avec répartition HW et SW");
		$this->setx(40);
		$this->multicell(0,5,"- Services associés souhaités");
		$this->setx(40);
		$this->multicell(0,5,"- Durée de la location");
		$this->setx(40);
		$this->multicell(0,5,"- Loyers linéaires, dégressifs, progressifs ou spécifiques");
		$this->setFontDecoration('B');
		$this->multicell(0,5,">Services associés");
		$this->unsetFontDecoration();
		$this->setFontDecoration('I');
		$this->multicell(0,5,"=>Gestion de parc");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"Pour vous faciliter la gestion quotidienne de votre parc, CLEODIS vous donne accès via son site Web à la base de donnée reprenant tous les équipements intégrés au contrat de location. Vous y trouverez toutes les informations concernant vos contrats, les données techniques des matériels et logiciels.");
		$this->ln(5);
		$this->setFontDecoration('I');
		$this->multicell(0,5,"=>Assurance remplacement");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"CLEODIS vous propose d'intégrer au contrat une assurance remplacement (et non remboursement à la valeur vénale) du matériel en cas de sinistre partiel ou total lors d'un vol avec effraction, un incendie ou un dégât des eaux.");
		$this->ln(5);
		$this->setFontDecoration('I');
		$this->multicell(0,5,"=>Brokerage – Matériel de seconde main");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"CLEODIS a la possibilité de vous fournir des matériels de seconde main, ou  de reprendre vos équipements en fin de vie en vue de les revendre sur le marché de l'occasion ou de les recycler dans le respect des normes environnementales.");
		$this->ln(4);
		$this->setFontDecoration('I');
		$this->multicell(0,4,"=>Formatage des disques durs / effacement des données utilisateurs");
		$this->unsetFontDecoration();
		$this->multicell(0,4,"Lors de la reprise de vos unités centrales à l'issue de votre contrat, CLEODIS se charge d'effacer les données utilisateurs présentes sur les disques durs. Un formatage bas niveau est systématiquement inclus dans nos offres avec la possibilité - en option - de procéder à un formatage niveau 7 qui vous garantit l'impossibilité de récupérer vos données.");
		
		/*	PAGE 5	*/
		$this->AddPage();
		
		$this->setfont('arial','I',8);
		$this->multicell(0,7,"=>L'évolution et le suivi du contrat de location");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"La possibilité d'évoluer à tout moment sur tout ou partie des matériels passe par la capacité de CLEODIS à savoir re-commercialiser l'ensemble des équipements nécessitant une évolution. Cette valorisation viendra en déduction du nouvel investissement.");
		
		$this->ln(5);
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"La location de logiciels");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Afin de toujours mieux répondre aux demandes du marché, CLEODIS propose le Contrat de Mise à Disposition de Logiciels.");
		$this->multicell(0,5,"Semblable à un contrat de location classique, celui-ci permet le financement d'offres composées à 100% de logiciels, et ce quel que soit le montant.");
		$this->multicell(0,5,"Le coût de la mise à disposition est comparable à celui d'une location classique d'équipements informatiques.");
		$this->multicell(0,5,"Ce contrat permet l'acquisition de licences logicielles en diminuant sensiblement les contraintes budgétaires puisque le coût des licences est étalé dans le temps.");
		$this->setFontDecoration('B');
		$this->multicell(0,5,"L'entreprise n'est alors plus contrainte d'utiliser ses fonds propres pour acquérir un logiciel et n'est plus tenue de l'amortir sur 1 an ; elle peut désormais répartir la charge sur la période d'utilisation et budgétiser ainsi le coût de ce logiciel ainsi que celui de ses mises à jours.");
		$this->unsetFontDecoration();
		$this->multicell(0,5,"En outre, le contrat étant évolutif, il permet d'incorporer au fur et à mesure des besoins, de nouveaux modules ou versions de logiciel au travers d'un budget mensuel. Le travail de CLEODIS consistant à vous conseiller pour gérer avec vous le contrat et proposer les évolutions aux meilleurs moments afin de vous maintenir un budget constant.");
		$this->ln(5);
		$this->RoundedRect(15,$this->gety(),180,15,3);		
		$this->multicell(0,5,"La solution locative de CLEODIS, avantageuse d'un point de vue comptable et opérationnel, vous assure la possibilité de mettre en adéquation vos besoins d'évolution technique avec  vos contraintes budgétaires. L'équipe CLEODIS est également présente tout au long du contrat pour vous accompagner dans vos choix d'évolution.");
		
		$this->ln(5);
		$this->setfont('arial','BU',14);
		$this->multicell(0,5,"3 – Proposition commerciale CLEODIS pour ".$this->client['societe']);
		$this->ln(5);
		
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"3.1 Synthèse de la proposition suivant éléments transmis :");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"TABLEAU DE SYNTHESE DE L'OFFRE : MATERIEL / LOGICIEL / PRESTATION",0,'C');
		$this->ln(5);
		
		if ($this->lignes) {
			// Groupe les lignes par affaire
			$lignes=$this->groupByAffaire($this->lignes);
			// Flag pour savoir si le tableau part en annexe ou pas
			$flagOnlyPrixInvisible = true;
			foreach ($lignes as $k => $i) {
				if (!$k) {
					$title = "NOUVEAU(X) EQUIPEMENT(S)";
				} else {
					$affaire_provenance=ATF::affaire()->select($k);
					if($this->affaire["nature"]=="AR"){
						$title = "EQUIPEMENT(S) PROVENANT(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}
				}

				$head = array("Qté","Fournisseur","Désignation","Prix HT");
				$w = array(12,40,111,22);
				unset($data,$st);
				foreach ($i as $k_ => $i_) {
					if ($i_['visibilite_prix']=="visible") {
						$flagOnlyPrixInvisible = false;
					}
					$produit = ATF::produit()->select($i_['id_produit']);
					//On prépare le détail de la ligne
					$details=$this->detailsProduit($i_['id_produit'],$k,$i_['commentaire']);
					//Ligne 1 "type","processeur","puissance" OU Infos UC ,  j'avoue que je capte pas bien
					
					if ($details == "") unset($details);
					
					$etat = "";
					if($i_["neuf"] == "non"){
						$etat = "( OCCASION )";
					}									
					
					//Si c'est une prestation, on affiche pas l'etat
					if($produit["type"] == "sans_objet" || ($produit['id_sous_categorie'] == 16) || ($produit['id_sous_categorie'] == 114)){	$etat = "";		}	
					
					if(ATF::$codename == "cleodisbe"){ $etat = ""; }					
					
					$data[] = array(
						round($i_['quantite'])
						,$i_['id_fournisseur'] ?ATF::societe()->nom($i_['id_fournisseur']) : "-"
						,$i_['produit']." - ".ATF::fabriquant()->nom($produit['id_fabriquant'])." ".$etat
						,($i_['visibilite_prix']=="visible")?number_format($i_['quantite']*$i_['prix_achat'],2,","," ")." €":"NC"
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
				if ($flagOnlyPrixInvisible) {
					array_pop($i['head']);
					$i['w'][1] += array_pop($i['w']);	
					array_pop($i['styles']);	
					foreach ($i['data'] as $k_=>$i_) {
						array_pop($i['data'][$k_]);	
					}
				}
				if ($h>$this->heightLimitTableDevisClassique) {
					$this->multicellAnnexe();
					$annexes[$k] = $i;
				} else {
					$this->tableauBigHead($i['head'],$i['data'],$i['w'],5,$i['styles']);
				}
			}
			
			unset($data,$st);
		}
	
		/*	PAGE 6	*/
		$this->AddPage();
		
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"3.2 Valorisation des matériels sortants pour ".$this->client['societe']);
		$this->ln(5);
		$this->setfont('arial','',8);

		$this->multicell(0,5,"L'intérêt de la location vient en grande partie de la faculté du loueur à ne vous faire payer que l'utilisation des matériels. Cela est rendu possible par notre capacité à valoriser les matériels à l'issu de la location et/ou lors de la mise en place de la solution locative.");
		$this->multicell(0,5,"La valeur marché vient alors en déduction du nouvel investissement et participe à la baisse de budget pour ".$this->client['societe'].".");
		$this->setFontDecoration('B');
		$this->multicell(0,5,"Si vous le souhaitez, transmettez nous la configuration de vos équipements actuels ; nous analyserons leur valeur marché et, le cas échéant, vous en ferons bénéficier sous forme de baisse de loyer.");
		$this->unsetFontDecoration();
		$this->ln(5);
		
		$this->setfont('arial','U',10);
		$this->multicell(0,5,"3.3 Formule Locative dédiée MATERIELS : Location à Renouvellement Planifié");
		$this->ln(5);
		$this->setfont('arial','',8);
		
		$this->multicell(0,5,"Destinée aux entreprises soucieuses de bénéficier d'un contrat sur mesure, la Location à Renouvellement Planifié CLEODIS est un contrat de location dont les évolutions sont prévues lors de la signature pour faire de cette formule une véritable solution évolutive.");
		$this->Setfont('arial','BUI',8);
		$this->multicell(0,5,"Principes :");
		$this->SetFont('arial','',8);
		$this->setx(30);
		$this->multicell(0,5,"1.Une redevance mensuelle attractive tenant compte de la valorisation à terme des matériels");
		$this->setx(30);
		$this->multicell(0,5,"2.La facturation de frais de gestion intégrant l'ensemble des prestations réalisées par CLEODIS pour le compte de son client : suivi, mise en place, gestion de parc, évolution, reprise...");
		$this->setx(30);
		$this->multicell(0,5,"3.Des matériels renouvelés tous les 36 mois avec l'engagement de CLEODIS de transmettre un contrat d'évolution 3 mois avant cette échéance ;");
		$this->setx(30);
		$this->multicell(0,5,"4.La gestion de parc technique des matériels loués avec fourniture d'un fichier Excel pour incrémenter votre  propre gestion de parc.");
		$this->setx(30);
		$this->multicell(0,5,"5.l'assurance en cas de besoin et de volonté du client de pouvoir conserver les matériels au delà de la période prévue");
		$this->ln(5);
		$this->setFontDecoration('B');
		$this->multicell(0,5,"La Location à Renouvellement Planifié CLEODIS, c'est l'assurance d'un service de qualité  assuré par des professionnels de la location prenant en charge l'ensemble du processus de renouvellement.");
		$this->unsetFontDecoration();
		
		/*	PAGE 7	*/
		$this->AddPage();
		
		$this->sety(10);
		$this->setfont('arial','B',14);
		$this->RoundedRect(15,10,140,25,5);
		$this->multicell(140,6,"Proposition locative CLEODIS",0,'C');
		$this->multicell(140,6,"pour ".$this->client['societe'],0,'C');
		$this->multicell(140,6,($this->affaire['nature']=="avenant"?"Avenant au contrat ".ATF::affaire()->select($this->affaire['id_parent'],'ref'):""),0,'C');
		$this->multicell(140,6," Le ".date("d/m/Y",strtotime($this->devis['date'])),0,'C');
		
		$this->sety(35);
		$this->setfont('arial','',8);

		$this->cell(0,5,"N° d'affaire : ".$this->affaire["ref"],0,1);
		$societe = ATF::societe()->select($this->devis['id_societe']);
		if($societe["code_client"]){$this->cell(0,5,"Code client : ".$societe["code_client"],0,1); }
		
		$duree = ATF::loyer()->dureeTotal($this->devis['id_affaire']);
		$frequence=ATF::$usr->trans($this->loyer[0]["frequence_loyer"],"loyer_frequence_loyer");
		if($this->devis['loyer_unique']=='oui'){
			$this->setfont('arial','B',12);
			$this->multicell(0,10,"La durée de la location est identique à celle du contrat principal.",0,'L');
		}elseif($this->devis['type_contrat'] == 'lrp') {
			if ($duree==39 || $duree==27 || $duree==51) {
				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree-3)." (+3) ".$frequence,0,'L');
			} else {
				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree)."  ".$frequence,0,'L');
			}
//			if($this->loyer[0]['frequence_loyer']=='mois'){
//				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree-3)." (+3) ".$frequence,0,'L');
//			}elseif($this->loyer[0]['frequence_loyer']=='trimestre'){
//				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree-1)." (+1) ".$frequence,0,'L');
//			}else{
//				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree)."  ".$frequence,0,'L');
//			}
		}
		$this->setfont('arial','',10);
		
		$this->multicell(0,5,"TABLEAU DE SYNTHESE DE L'OFFRE : MATERIEL / LOGICIEL / PRESTATION",0,'C');

		foreach ($tableau as $k=>$i) {
			$this->setFillColor(239,239,239);
			$this->setfont('arial','B',10);
			$this->multicell(0,5,$i['title'],1,'C',1);
			$this->setfont('arial','',8);
			if ($flagOnlyPrixInvisible) {
				array_pop($i['head']);
				$i['w'][1] += array_pop($i['w']);	
				array_pop($i['styles']);	
				foreach ($i['data'] as $k_=>$i_) {
					array_pop($i['data'][$k_]);	
				}
			}
			if ($h>$this->heightLimitTableDevisClassique) {
				$this->multicellAnnexe();
				$annexes[$k] = $i;
			} else {
				$this->tableauBigHead($i['head'],$i['data'],$i['w'],5,$i['styles']);
			}
		}
		
		$this->sety(130);
		if($this->devis['loyer_unique']=='non'){
			$this->tableauLoyer();
		}

		$this->setfont('arial','',8);
		$this->cell(0,5,"",0,1,'C');
		
		$this->setfont('arial','B',10);
		$this->multicell(0,5,"Les engagements Cléodis : ");
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"Nous nous engageons à vous fournir :");
		$this->setfont('arial','',8);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>Un conseil indépendant en renouvellement de matériels",0,1);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>La possibilité d'évoluer à tout moment, et d'incorporer des budgets non prévus tout en lissant la charge budgétaire",0,1);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>Un service de reprise des équipements au terme ou en cours de contrat",0,1);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>La gestion de parc des matériels loués",0,1);
		
		$this->sety(235);
		$this->cell(0,40,"",1,1);
		$this->sety(235);
		$this->setFontDecoration('B');
		if ($this->totalAssurance) {
			$this->multicell(0,5,"Option assurance remplacement : oui ou non");
		}
		$this->unsetFontDecoration();
		$this->multicell(0,5,"« Bon pour accord »");
		$this->multicell(0,5,"Cachet commercial+ Signature");
		
		$this->setfont('arial','I',6);
		$this->sety(270);
		$this->multicell(0,5,"Cette offre, valable jusqu'au ".ATF::$usr->trans($this->devis['validite']).", reste soumise à notre comité des engagements.",0,'C');
		if ($annexes) {
			$this->annexes($annexes);
		}
	}
	
	/* Génère le PDF d'un devis Avenant
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	*/
	private function devisAvenant() {
		if (!$this->devis) return false;

		/* PAGE 2 */
		$this->setHeader();
		$this->setTopMargin(30);
		
		if ($this->lignes) {
			// Groupe les lignes par affaire
			$lignes=$this->groupByAffaire($this->lignes);
			// Flag pour savoir si le tableau part en annexe ou pas
			$flagOnlyPrixInvisible = true;
			foreach ($lignes as $k => $i) {
				if (!$k) {
					$title = "NOUVEAU(X) EQUIPEMENT(S)";
				} else {
					$affaire_provenance=ATF::affaire()->select($k);
					$title = "EQUIPEMENT(S) RETIRE(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
				}

				$head = array("Qté","Fournisseur","Désignation","Prix HT");
				$w = array(12,40,111,22);
				unset($data,$st);
				foreach ($i as $k_ => $i_) {
					$etat = "";
					if($i_["neuf"] == "non"){
						$etat = "( OCCASION )";
					}				
					
					//Si c'est une prestation, on affiche pas l'etat
					if($produit["type"] == "sans_objet" || ($produit['id_sous_categorie'] == 16) || ($produit['id_sous_categorie'] == 114)){	$etat = "";		}	
					
					if(ATF::$codename == "cleodisbe"){ $etat = ""; }					
					
					if ($i_['visibilite_prix']=="visible") {
						$flagOnlyPrixInvisible = false;
					}
					$produit = ATF::produit()->select($i_['id_produit']);
					//On prépare le détail de la ligne
					$details=$this->detailsProduit($i_['id_produit'],$k,$i_['commentaire']);
					//Ligne 1 "type","processeur","puissance" OU Infos UC ,  j'avoue que je capte pas bien
					
					if ($details == "") unset($details);
					
					$data[] = array(
						round($i_['quantite'])
						,$i_['id_fournisseur'] ?ATF::societe()->nom($i_['id_fournisseur']) : "-"
						,$i_['produit']." - ".ATF::fabriquant()->nom($produit['id_fabriquant'])." ".$etat
						,($i_['visibilite_prix']=="visible")?number_format($i_['quantite']*$i_['prix_achat'],2,","," ")." €":"NC"
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
			
			$h = count($tableau)*5; //Ajout dans le calcul des titres de tableau mis a la main
			foreach ($tableau as $k=>$i) {
				if ($i['head']) $h += 5;
				$h += $this->getHeightTableau($i['head'],$i['data'],$i['w'],5,$i['styles']);
			}

			unset($data,$st);
		}
	
		$this->AddPage();
		
		$this->sety(10);
		$this->setfont('arial','B',14);
		$this->RoundedRect(15,10,140,25,5);
		$this->multicell(140,6,"Proposition locative CLEODIS",0,'C');
		$this->multicell(140,6,"pour ".$this->client['societe'],0,'C');
		$this->multicell(140,6,($this->affaire['nature']=="avenant"?"Avenant au contrat ".ATF::affaire()->select($this->affaire['id_parent'],'ref'):""),0,'C');
		$this->multicell(140,6," Le ".date("d/m/Y",strtotime($this->devis['date'])),0,'C');
		
		$this->sety(35);
		$this->setfont('arial','',8);
		$duree = ATF::loyer()->dureeTotal($this->devis['id_affaire']);
		$frequence=ATF::$usr->trans($this->loyer[0]["frequence_loyer"],"loyer_frequence_loyer");
		if($this->devis['loyer_unique']=='oui'){
			$this->setfont('arial','B',12);
			$this->multicell(0,10,"La durée de la location est identique à celle du contrat principal.",0,'L');
		}elseif($this->devis['type_contrat'] == 'lrp') {
			if ($duree==39) {
				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree-3)." (+3) ".$frequence,0,'L');
			} else {
				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree)."  ".$frequence,0,'L');
			}
//			if($this->loyer[0]['frequence_loyer']=='mois'){
//				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree-3)." (+3) ".$frequence,0,'L');
//			}elseif($this->loyer[0]['frequence_loyer']=='trimestre'){
//				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree-1)." (+1) ".$frequence,0,'L');
//			}else{
//				$this->multicell(0,5,ATF::$usr->trans($this->devis['type_contrat'],'devis_type_contrat')." sur ".($duree)."  ".$frequence,0,'L');
//			}
		}
		$this->setfont('arial','',10);
		
		$this->multicell(0,5,"TABLEAU DE SYNTHESE DE L'OFFRE : MATERIEL / LOGICIEL / PRESTATION",0,'C');

		foreach ($tableau as $k=>$i) {
			$this->setFillColor(239,239,239);
			$this->setfont('arial','B',10);
			$this->multicell(0,5,$i['title'],1,'C',1);
			$this->setfont('arial','',8);
			if ($flagOnlyPrixInvisible) {
				array_pop($i['head']);
				$i['w'][1] += array_pop($i['w']);	
				array_pop($i['styles']);	
				foreach ($i['data'] as $k_=>$i_) {
					array_pop($i['data'][$k_]);	
				}
			}
			if ($h>$this->heightLimitTableDevisClassique) {
				$this->multicellAnnexe();
				$annexes[$k] = $i;
			} else {
				$this->tableauBigHead($i['head'],$i['data'],$i['w'],5,$i['styles']);
			}
		}
		
		$this->sety(120);
		if($this->devis['loyer_unique']=='non'){
			$this->tableauLoyer();
		}

		$this->setfont('arial','',8);
		$this->cell(0,5,"",0,1,'C');
		
		$this->setfont('arial','B',10);
		$this->multicell(0,5,"Les engagements Cléodis : ");
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"Nous nous engageons à vous fournir :");
		$this->setfont('arial','',8);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>Un conseil indépendant en renouvellement de matériels",0,1);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>La possibilité d'évoluer à tout moment, et d'incorporer des budgets non prévus tout en lissant la charge budgétaire",0,1);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>Un service de reprise des équipements au terme ou en cours de contrat",0,1);
		$this->cell(30,5,"",0,0);
		$this->cell(0,5,"=>La gestion de parc des matériels loués",0,1);
		
		$this->sety(235);
		$this->cell(0,40,"",1,1);
		$this->sety(235);
		$this->setFontDecoration('B');
		if ($this->totalAssurance) {
			$this->multicell(0,5,"Option assurance remplacement : oui ou non");
		}
		$this->unsetFontDecoration();
		$this->multicell(0,5,"« Bon pour accord »");
		$this->multicell(0,5,"Cachet commercial+ Signature");
		
		$this->setfont('arial','I',6);
		$this->sety(270);
		$this->multicell(0,5,"Cette offre, valable jusqu'au ".ATF::$usr->trans($this->devis['validite']).", reste soumise à notre comité des engagements.",0,'C');
		if ($annexes) {
			$this->annexes($annexes);
		}
	}
	
	/* Génère le tableau récapitulatif des loyers pour le devis
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 13-01-2011
	*/
	public function tableauLoyer() {
		if (!$this->loyer || !$this->devis) return false;
		$saveHeadStyle = $this->headStyle;
		$nbLoyer = count($this->loyer);
		
		$style = array(
			"col1"=>array("size"=>9,"decoration"=>"IB","border"=>"T","align"=>"L")
			,"col1bis"=>array("size"=>8,"decoration"=>"","border"=>" ","align"=>"L","bgcolor"=>"efefef") 
			,"loyer"=>array("size"=>8,"border"=>"T","align"=>"C")
			,"head1"=>array("size"=>8,"border"=>" ","align"=>"C","bgcolor"=>"ffffff") 
			,"head2"=>array("size"=>8,"border"=>1,"align"=>"C","bgcolor"=>"ffffff") 
		);
		
		//Largeur des cellules
		$plus = (5-count($this->loyer))*21;
		$width = array(80+$plus);
		
		foreach ($this->loyer as $k=>$i) {
			$width[] = 21;
		}
		//Création des entêtes
		if ($this->affaire['nature']=="avenant") {
			$head[] = "AVENANT";
		} else {
			$head[] = "";
		}
		$this->headStyle[0] = $style["head1"];

		foreach ($this->loyer as $k=>$i) {
			if (!$k) $prefix = "Sur ";
			else $prefix = "Puis sur ";
			
			if ($k==(count($this->loyer)-1)) {
				if ($i['duree']==39 || $i['duree']==27 || $i['duree']==51) $head[] = $prefix.($i['duree']-3)." (+3) "." ".ATF::$usr->trans($i['frequence_loyer'],"loyer_frequence_loyer");
				else $head[] = $prefix.$i['duree']." ".ATF::$usr->trans($i['frequence_loyer'],"loyer_frequence_loyer");
			} else {
				$head[] = $prefix.$i['duree']." ".ATF::$usr->trans($i['frequence_loyer'],"loyer_frequence_loyer");
			}
			
			$this->headStyle[] = $style["head2"];
		}
		//Création des données
		//Ligne 1
		$data[0][] = "> Mise a Disposition ".ATF::$usr->trans($this->loyer[0]["frequence_loyer"],"loyer_frequence_loyer_feminin")." ".$this->texteHT;
		$s[0][] = $style["col1"];
		foreach ($this->loyer as $k=>$i) {
			$data[0][] = number_format($i['loyer'],2,"."," ")." €/".substr($i['frequence_loyer'],0,1);
			$s[0][] = $style["loyer"];
		}
		//Ligne 2
		$data[1][] = "Mise a disposition des Equipements";
		$s[1][] = $style["col1bis"];
		foreach ($this->loyer as $k=>$i) {
			$data[1][] = "";
			$s[1][] = $style["col1bis"];
		}
		//Ligne 3
		$data[2][] = "> Frais de Gestion";
		$s[2][] = $style["col1"];
		foreach ($this->loyer as $k=>$i) {
			$data[2][] = $i['frais_de_gestion']?$i['frais_de_gestion']." €":"Inclus";
			$s[2][] = $style["loyer"];
		}
		//Ligne 4
		$data[3][] = "Incluant : \nSuivi de la facturation, Gestion de parc, Evolution à tout moment, reprise des materiel, Valorisation ou mise au rebut des matériels sortants et simulation d'evolution de contrat";
		$s[3][] = $style["col1bis"];
		foreach ($this->loyer as $k=>$i) {
			$data[3][] = "";
			$s[3][] = $style["col1bis"];
		}
		//Ligne 5
		$data[4][] = "> Redevance ".ATF::$usr->trans($this->loyer[0]["frequence_loyer"],"loyer_frequence_loyer_feminin")." ".$this->texteHT;
		$s[4][] = $style["col1"];
		foreach ($this->loyer as $k=>$i) {
			$data[4][] = number_format($i['frais_de_gestion']+$i['loyer'],2,"."," ")." €/".substr($i['frequence_loyer'],0,1);
			$s[4][] = $style["loyer"];
		}
		$this->totalAssurance = 0;
		foreach ($this->loyer as $k=>$i) {
			$this->totalAssurance += $i['assurance'];
		}
		if ($this->totalAssurance) {
			// Ligne 6 Assurance
			$data[5][] = "> Option Assurance Remplacement *";
			$s[5][] = $style["col1"];
			foreach ($this->loyer as $k=>$i) {
				$data[5][] = $i['assurance']?$i['assurance']." €/".substr($i['frequence_loyer'],0,1):"-";
				$s[5][] = $style["loyer"];
			}
			//Ligne 7
			$data[6][] = "En cas de vol avec effraction, bris de machine, incendie ou dégâts des eaux : Remplacement à l'équivalent, voire au modèle supérieur";
			$s[6][] = $style["col1bis"];
			foreach ($this->loyer as $k=>$i) {
				$data[6][] = "";
				$s[6][] = $style["col1bis"];
			}
			//Ligne 8
			$data[7][] = "Redevance ".ATF::$usr->trans($this->loyer[0]["frequence_loyer"],"loyer_frequence_loyer_feminin")." ".$this->texteHT." avec Assurance :";
			$s[7][] = $style["col1"];
			foreach ($this->loyer as $k=>$i) {
				$data[7][] = number_format($i['frais_de_gestion']+$i['loyer']+$i['assurance'],2,"."," ")." €/".substr($i['frequence_loyer'],0,1);
				$s[7][] = $style["loyer"];
			}
		}
		$this->tableau($head,$data,$width,5,$s);
		$this->headStyle = $saveHeadStyle;
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

	/** Renvoi le detail d'un produit par rapport a ses informations
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param int $id Identifiant produit
	*/
	public function detailsProduit($id_produit,$provenance=NULL,$commentaire=NULL){
		$produit=ATF::produit()->select($id_produit);
		if ($produit['id_produit_type']) $d1 .= ATF::produit_type()->nom($produit['id_produit_type']).", ";
		if ($produit['id_processeur']) $d1 .= ATF::processeur()->nom($produit['id_processeur']).", ";
		if ($produit['id_produit_puissance']) $d1 .= ATF::produit_puissance()->nom($produit['id_produit_puissance']).", ";
		if ($produit['id_produit_garantie_uc'] && ($this->affaire['nature']!="AR" || !$provenance)) $d1 .= ATF::produit_garantie()->nom($produit['id_produit_garantie_uc']).", ";
		if ($produit['id_produit_ram']) $d1 .= ATF::produit_ram()->nom($produit['id_produit_ram']).", ";
		if ($produit['id_produit_OS']) $d1 .= ATF::produit_OS()->nom($produit['id_produit_OS']).", ";
		if ($produit['id_produit_lecteur']) $d1 .= ATF::produit_lecteur()->nom($produit['id_produit_lecteur']).", ";
		if ($produit['id_produit_lan']) $d1 .= ATF::produit_lan()->nom($produit['id_produit_lan']).", ";
		if ($produit['id_produit_dd']) $d1 .= ATF::produit_dd()->nom($produit['id_produit_dd']).", ";
		if ($d1 && $d1 !="") $d1 .="\n";
		//Ligne 2 "marque","frequence","ram","dd","lecteur","lan","OS","garantie" OU infos Ecran
		if ($produit['id_produit_typeecran']) $d2 .= ATF::produit_typeecran()->nom($produit['id_produit_typeecran']).", ";
		if ($produit['id_produit_viewable']) $d2 .= ATF::produit_viewable()->nom($produit['id_produit_viewable']).", ";
		if ($produit['id_produit_garantie_ecran'] && ($this->affaire['nature']!="AR" || !$provenance)) $d2 .= ATF::produit_garantie()->nom($produit['id_produit_garantie_ecran']).", ";
		if ($d2 && $d2 !="") $d2 .="\n";
		//Ligne 3 "marque2","typeecran","viewable","garantie2","marque3","technique","format","garantie3" OU Infos Imprimante
		if ($produit['id_produit_technique']) $d3 .= ATF::produit_technique()->nom($produit['id_produit_technique']).", ";
		if ($produit['id_produit_format']) $d3 .= ATF::produit_format()->nom($produit['id_produit_format']).", ";
		if ($produit['id_produit_garantie_imprimante'] && ($this->affaire['nature']!="AR" || !$provenance)) $d3 .= ATF::produit_garantie()->nom($produit['id_produit_garantie_imprimante']).", ";
		if ($d3 && $d3 !="") $d3 .="\n";


		if ($commentaire) $d4 .= "Commentaire : ".$commentaire;
		if ($produit['commentaire']){
			if (!$commentaire) $d4 .= "Commentaire : ";
			else $d4 .= " - ";
			$d4 .= $produit['commentaire'];
		} 
		
		$details = $d1.$d2.$d3.$d4;
		
		return $details;
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
	
	/** PDF d'un contrat en A3
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param int $id Identifiant commande
	*/
	public function contratA3($id) {
		/* Passage en A3 */
		$format=array(841.89,1190.55);
		$this->fwPt=$format[0];
		$this->fhPt=$format[1];
		
		/* Passage en LandScape */
		
		$this->DefOrientation='L';
		$this->wPt=$this->fhPt;
		$this->hPt=$this->fwPt;
		
		/* Formattage de la taille */
		$this->CurOrientation=$this->DefOrientation;
		$this->w=$this->wPt/$this->k;
		$this->h=$this->hPt/$this->k;

		$this->commandeInit($id);
		$this->A3 = true;

		$this->Open();		
		$this->AddPage();

		$this->contratA3Left();
		$this->contratA3Right();

		if($this->devis["type_contrat"] == "presta"){ }
		else{	$this->conditionsGeneralesDeLocationA3();	}

		
	}
	
	/** Partie de gauche d'un PDF d'un contrat en A3
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	*/
	private function contratA3Left() {
		$this->SetLeftMargin(15);

		$this->setfont('arial','',8);
		$this->setxy(10,10);
		if($this->devis["type_contrat"]=="vente"){
			$this->multicell(190,5,"CONDITIONS PARTICULIERES DU CONTRAT DE VENTE",1,'C');
			$this->article(10,20,'1',"OBJET DU CONTRAT",10);
			$texte = "L'objet du contrat est la vente d'équipements dont le détail figure en première page.";
			
			$this->multicell(190,4,$texte);
			
			$this->article(10,35,'2',"MONTANT",10);
			$this->multicell(190,5,"Le montant est fixé à ".number_format($this->commande['prix'],2,"."," ")." € ".$this->texteHT);
			$this->multicell(190,5,"Il est payable par ".ATF::$usr->trans($this->commande['type'],'commande'));
			
			$this->article(10,55,'3',"VALIDITE",10);
			$location="vente";		
			$locataire="L'acheteur";	
			$loueur="Vendeur";		
		}else{
			if($this->devis["type_contrat"] == "presta"){
				$this->multicell(190,5,"CONDITIONS PARTICULIERES DU CONTRAT DE PRESTATION",1,'C');
				$this->article(10,20,'1',"OBJET DU CONTRAT",10);
				$texte = "L'objet du contrat concerne les prestations dont le détail figure en première page. ";
			}else{
				$this->multicell(190,5,"CONDITIONS PARTICULIERES DU CONTRAT DE LOCATION",1,'C');
				$this->article(10,20,'1',"OBJET DU CONTRAT",10);
				$texte = "L'objet du contrat est la mise en location d'équipements dont le détail figure en première page. ";
			}			
			
			if ($this->affaire['nature']=="AR" && $this->AR) {
				$texte .= "Ce contrat annule et remplace le(s) contrat(s) suivant(s) : ";
				foreach ($this->AR as $k=>$i) {
					$texte .= ATF::affaire()->nom($i['id_affaire']).", ";
				}
			}
			
			$this->multicell(190,4,$texte);
			if($this->devis["type_contrat"] == "presta"){
				$this->article(10,35,'2',"DUREE",10);
			}else{
				$this->article(10,35,'2',"DUREE DE LA LOCATION",10);
			}
			$duree = ATF::loyer()->dureeTotal($this->devis['id_affaire']);

			$texte_duree = "La durée de la location";
			if($this->devis["type_contrat"] == "presta"){ $texte_duree = "La durée"; }
			
			if($this->affaire["nature"]=="avenant"){
				if($this->devis['loyer_unique']=='oui'){
					$texte = $texte_duree." est identique à celle du contrat principal. ";
				}else{
					$texte = $texte_duree." est fixée à ".$duree." mois"." à compter du ";
					if($this->commande['date_debut']){
						$texte .= date("d/m/Y",strtotime($this->commande['date_debut'])).".";
					}
				}
			}else{
				$texte = $texte_duree." est fixée à ".$duree." mois".". ";
			}
			$this->multicell(190,5,$texte);
			
			$this->article(10,50,'3',"LOYERS",10);

			if($this->devis['loyer_unique']=='oui'){
				$loyers[] = "1 loyer de ".number_format($this->loyer["loyer"]+$this->loyer["assurance"]+$this->loyer["frais_de_gestion"],2,"."," ")." € ".$this->texteHT;
			}else{
				foreach ($this->loyer as $k=>$i) {
					if ($k) $prefix = " suivi de ";
					$loyers[] = $i["duree"]." loyers de ".number_format($i["loyer"]+$i["assurance"]+$i["frais_de_gestion"],2,"."," ")." € ".$this->texteHT;
				}
			}
			$this->multicell(190,5,"Les loyers ".ATF::$usr->trans($this->loyer[0]["frequence_loyer"],"loyer_frequence_loyer_masculin")."s sont fixés ainsi  : ".implode(" suivis de ",$loyers).".");
			$this->multicell(190,5,"Ils sont payables terme à échoir par ".ATF::$usr->trans($this->commande['type'],'commande').".");
			$this->multicell(190,5,"Ils sont fixes et non révisables pendant toute la durée de la location.");

			$this->article(10,$this->gety()+5,'4',"VALIDITE",10);
			$location="location";		
			$locataire="Locataire";	
			$loueur="Loueur";		
		}
		

		$this->multicell(190,5,"La présente proposition ne deviendra une offre ferme qu'après acceptation du Comité des Agréments de CLEODIS.");
		if ($this->commande["clause_logicielle"]=="oui") {
			$this->article(10,$this->gety()+5,'5',"MISE A DISPOSITION DES LOGICIELS",10);
			$this->multicell(190,5,"ETANT PREALABLEMENT EXPOSE :");
			$this->multicell(190,5,"Pour les besoins de son activité, le Locataire a souhaité la mise à disposition d'une configuration informatique composée de matériels et de logiciels [ci-après désignés les «Logiciels »] objet du contrat ci-dessus référencé.");
			$this->multicell(190,5,"Le Locataire a obtenu du Fournisseur de pouvoir utiliser les Logiciels dans le cadre d'une licence dont il a approuvé les termes.");
			$this->multicell(190,5,"Le mode de souscription de ce droit d'utilisation s'effectue dans le cadre d'une mise à disposition temporaire convenue dans le cadre du Contrat en référence.");
			$this->multicell(190,5,"LE LOCATAIRE DECLARE :");
			$this->multicell(190,5,"=>reconnaître que le Contrat lui permet de bénéficier d'une mise à disposition des Logiciels et donc de l'utilisation de ceux-ci conformément à ses besoins;");
			$this->multicell(190,5,"=>qu'en cas de contradiction, les clauses du contrat ci-dessus référencé prévalent, dans ses relations avec le Loueur, sur celles qui régissent ou constituent la licence;");
			$this->multicell(190,5,"=>que les configurations informatiques seront livrées et installées, les prestations réalisées conformément à la commande qu'il a passé aux fournisseurs et selon les modalités convenues directement avec l'éditeur des Logiciels ou les prestataires et/ou les fournisseurs des matériels;");
			$this->multicell(190,5,"=>prendre livraison des configurations informatiques à ses frais et risques, et reconnaît avoir choisi seul sans que le Loueur et/ou l'Etablissement Cessionnaire du contrat n'interviennent en quoi que ce soit dans ce choix;");
			$this->ln(5);
			$this->multicell(190,5,"En conséquence, ce choix relevant de la responsabilité exclusive du Locataire, ce dernier s'engage à régler ponctuellement l'ensemble des sommes dues au titre du Contrat et ce, même en cas de défaillance des éditeurs ou de leurs logiciels ainsi que des Prestataires.");
			$this->ln(5);
		}
		$this->setfont('arial','BI',8);

		//les deux cadres
		$cadre = array(
			"Fait à : ______________________"
			,"Le : ______________________"
			,"Nom : ______________________"
			,"Qualité : ______________________"
		);
		
		$this->cadre(25,195,70,80,$cadre,$locataire);
		$this->setEnteteBGColor("white");
		$this->cadre(115,195,70,80,$cadre,$loueur);
		$this->setEnteteBGColor("base");
		$this->setFillColor(255,255,0);
		$this->setxy(25,270);
		$this->cell(10,4,"",0,0,'C',false);
		$this->cell(50,4,"Signature et cachet du ".$locataire,0,0,'C',1);
		$this->cell(10,4,"",0,0,'C',false);
		$this->setxy(115,270);
		$this->multicell(70,5,"Signature et cachet du ".$loueur,0,'C');
		
	}

	/** Partie de droite d'un PDF d'un contrat en A3
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	*/
	private function contratA3Right() {
		if($this->devis["type_contrat"]=="vente"){
			$locationmaj="VENTE";
			$location="vente";		
			$locataire="L'acheteur";	
			$loueur="Vendeur";		
		}else{
			if($this->devis["type_contrat"] == "presta"){
				$locationmaj="PRESTATION";
				$location="prestation";		
				$locataire="Locataire";	
				$loueur="Loueur";	
			}else{
				$locationmaj="LOCATION";
				$location="location";		
				$locataire="Locataire";	
				$loueur="Loueur";	
			}	
		}
		$this->setleftmargin(210);
		
		$this->sety(20);
		$this->setfont('arial','B',15);
		$this->multicell(70,5,"Contrat de ".$location,0,'R');
		$this->setxy(345,20);
		$this->cell(0,5,"n°".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),0,1,'L');
	
		$this->setfont('arial','',8);
		$cadreLocataire = array(
			array("txt"=>$this->client['societe'],"align"=>"C","h"=>8)
			,array("txt"=>$this->client['adresse'],"align"=>"C","h"=>4)
		);
		if ($this->client['adresse_2']) {
			$cadreLocataire[] = array("txt"=>$this->client['adresse_2'],"align"=>"C","h"=>4);
		}
		if ($this->client['adresse_3']) {
			$cadreLocataire[] = array("txt"=>$this->client['adresse_3'],"align"=>"C","h"=>4);
		}
		$cadreLocataire[] = array("txt"=>$this->client['cp']." ".$this->client['ville'],"align"=>"C","h"=>4);
		if(ATF::$codename == "cleodisbe"){
			$cadreLocataire[] = array("txt"=>"NUMERO DE TVA ".$this->societe['reference_tva'],"align"=>"C");			
		}else{
			$siren = $this->client['siren']?"SIREN ".$this->client['siren']:NULL;
			if ($siren) {
				$cadreLocataire[] = array("txt"=>$siren,"align"=>"C","h"=>4);
			}
		}


		$this->cadre(235,35,70,35,$cadreLocataire,$locataire);
	
		$cadreLoueur = array(
			array("txt"=>$this->societe['societe'],"align"=>"C","h"=>8)
			,array("txt"=>$this->societe['adresse']." ".$this->societe['cp']." ".$this->societe['ville'],"align"=>"C","h"=>3)
			,array("txt"=>"Tél : ".$this->societe['tel']." – Fax : ".$this->societe['fax'],"align"=>"C","h"=>3)
		);

		
		if ($this->societe['id_pays']=='FR'){
			$cadreLoueur[] = array("txt"=>$this->societe["structure"]." AU CAPITAL DE ".number_format($this->societe["capital"],2,'.',' ')." €","align"=>"C","h"=>3);
			$cadreLoueur[] = array("txt"=>"SIREN ".$this->societe['siren']." – APE 7739Z","align"=>"C","h"=>3);
			$cadreLoueur[] = array("txt"=>"N° de TVA intracommunautaire :","align"=>"C","h"=>3);
			$cadreLoueur[] = array("txt"=>"FR 91 ".$this->societe["siren"],"align"=>"C","h"=>3);
		}else {
			$cadreLoueur[] = array("txt"=>"NUMERO DE TVA ".$this->societe['siret'],"align"=>"C");
		}
		

		
		$this->setEnteteBGColor("white");
		$this->cadre(320,35,70,35,$cadreLoueur,$loueur);
		$this->setleftmargin(220);
		$this->setEnteteBGColor("base");
		//A refactorisé quand l'AR et l'avenant seront fonctionnels
		$this->setfont('arial','BU',10);
		$this->multicell(0,10,"DESCRIPTION DES EQUIPEMENTS ET PRESTATIONS OBJET DU CONTRAT DE ".$locationmaj." ".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),0,'C');
		$this->setfont('arial','',8);
		$w = array(10,30,30,120);

		
		$eq = "EQUIPEMENT(S)";	
		if($this->devis["type_contrat"] == "presta") $eq = "PRESTATION(S)";	

		if ($this->lignes) {
			$lignes=$this->groupByAffaire($this->lignes);
			foreach ($lignes as $k => $i) {
			$this->setFillColor(239,239,239);
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
				$this->setfont('arial','',8);
				
				unset($data,$st);
				foreach ($i as $k_ => $i_) {
					$produit = ATF::produit()->select($i_['id_produit']);
					$ssCat = ATF::sous_categorie()->nom($produit['id_sous_categorie'])?ATF::sous_categorie()->nom($produit['id_sous_categorie']):"-";
					$fab = ATF::fabriquant()->nom($produit['id_fabriquant'])?ATF::fabriquant()->nom($produit['id_fabriquant']):"-";
					//On prépare le détail de la ligne
					$details=$this->detailsProduit($i_['id_produit'],$k,$i_['commentaire']);
					//Ligne 1 "type","processeur","puissance" OU Infos UC ,  j'avoue que je capte pas bien
					
					$etat = "( NEUF )";
					if($i_["id_affaire_provenance"] || $i_["neuf"]== "non" ){
						if($i_["neuf"] == "non"){
								$etat = "( OCCASION )";
						}
					}
					
					//Si c'est une prestation, on affiche pas l'etat
					if($produit["type"] == "sans_objet" || ($produit['id_sous_categorie'] == 16) || ($produit['id_sous_categorie'] == 114)){	$etat = "";		}	
					
					if(ATF::$codename == "cleodisbe"){ $etat = ""; }

					if ($details == "") unset($details);
					$data[] = array(
						round($i_['quantite'])
						,$ssCat
						,$fab
						,$i_['produit'].$etat
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
				$this->unsetHeader();
				
				$tableau[$k] = array(
					"head"=>$head
					,"data"=>$data
					,"w"=>$w
					,"styles"=>$st
					,"title"=>$title
				);				
				
			}
			
			$h = count($tableau)*5; //Ajout dans le calcul des titres de tableau mis a la main
			foreach ($tableau as $k=>$i) {
				if ($i['head']) $h += 5;
				$h += $this->getHeightTableau($i['head'],$i['data'],$i['w'],5,$i['styles']);
			}

			foreach ($tableau as $k=>$i) {
				$this->setFillColor(239,239,239);
				$this->setfont('arial','B',10);
				$this->multicell(0,10,$i['title'],1,'C',1);
				$this->setfont('arial','',8);
				if ($h+ 10>$this->heightLimitTableContratA43) {
					$this->multicellAnnexe();
					$annexes[$k] = $i;
				} else {
					$this->tableauBigHead($i['head'],$i['data'],$i['w'],5,$i['styles']);
				}
			}
			unset($data,$st);
		}
		if ($annexes) {
			$this->setLeftMargin(15);
			$this->annexes($annexes);
		}

	}
	
	/** CGL d'un PDF d'un contrat en A3
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	*/
	public function conditionsGeneralesDeLocationA3()  {
		$this->unsetHeader();
		$this->AddPage();
		$this->SetLeftMargin(10);
		
		//page gauche
		$this->setfont('arial','BI',8);		
		$this->sety(5);
		$this->multicell(190,5,"CONDITIONS GENERALES DE LOCATION",1,'C');
		
		$this->SetLeftMargin(10);
		$this->article(15,15,'1','DEFINITIONS',8);
		$this->setfont('arial','I',6);		
		$this->multicell(90,2,"Le présent contrat utilise les termes suivants ayant le sens qui leur est donné ci-dessous :",0,'L');
		$this->multicell(90,2,"Locataire : entité juridique au bénéfice de laquelle les Equipements sont loués et les services assurés.");
		$this->multicell(90,2,"Loueur : CLEODIS ou Cessionnaire");
		$this->multicell(90,2,"Fournisseur : entité juridique autre que CLEODIS désigné par le Locataire pour fournir les Equipements et/ou réaliser certains services associés à la location desdits équipements. Cessionnaire : établissement financier ou de crédit agréé en qualité de société financière ou société de location");
		$this->multicell(90,2,"Cessionnaire : établissement financier ou de crédit agréé en qualité de société financière ou société de location.");
		$this->multicell(90,2,"Services : prestation de service fournie par le Loueur et/ou le Fournisseur tel que prévu dans le présent contrat");
		$this->multicell(90,2,"Equipements : tout équipement et notamment équipements informatiques, bureautiques et de télécommunications y compris l’ensemble des droits d’utilisations des logiciels d’exploitation et d’application qui y sont associés.");
		
		$this->article(15,52,'2','OBJET & VALIDITE',8);
		$this->setfont('arial','I',6);		
		$this->multicell(90,2,"2.1 L’objet du présent contrat consiste en la location d’Equipements à laquelle des Services sont associés, l’ensemble étant détaillé dans les Conditions Particulières."); 		
		$this->multicell(90,2,"2.2 La signature du Contrat constitue un engagement ferme et définitif de la part du Locataire et annule et remplace tous accords antérieurs, écrits et verbaux, se rapportant aux dits Equipements");
		$this->multicell(90,2,"2.3 Les parties reconnaissent que les Equipements loués ayant un rapport direct avec l’activité professionnelle du Locataire, le code de la consommation ne s’applique pas");		
		$this->multicell(90,2,"2.4 Le Loueur dispose d’un mois à compter de la réception par lui du présent contrat pour signifier son accord au Locataire. Passé ce délai, le Locataire pourra se rétracter sans aucune indemnité due de part et d’autre");
		$this->multicell(90,2,"2.5 Au cas où le Loueur prendrait connaissance, après la conclusion du contrat mais avant la livraison des Equipements, de faits concernant la solvabilité du Locataire pouvant laisser craindre de sa part une incapacité à exécuter tout ou partie de ses obligations contractuelles, le Contrat serait alors résolu de plein droit à l’initiative du Loueur sans qu’aucune indemnité ne soit due de part et d’autre");
		$this->multicell(90,2,"2.6 Toute modification des clauses et conditions du présent contrat sera réputée nulle et non avenue sauf à résulter d’un avenant écrit et signé par CLEODIS et validé par le Cessionnaire.");
		
		$this->article(15,100,'3','CHOIX DES EQUIPEMENTS',8);
		$this->setfont('arial','I',6);		
		$this->multicell(90,2,"3.1 Le Locataire reconnaît avoir choisi librement, en toute indépendance et sous sa seule responsabilité, les Equipements loués, ainsi que les Fournisseurs, constructeurs et éditeurs qui participent à la fabrication, l’assemblage, la livraison et l’installation des Equipements et de leurs composants. Il reconnaît avoir pris connaissance des spécifications techniques et des modalités d’exploitation préalablement à la location. En conséquence, le Loueur ne saurait en aucun cas être recherché par le Locataire à raison de dommages causés par, ou à ces Equipements et résultant d’un vice de construction. Le Loueur ne saurait être tenu ni à une obligation de résultat, ni pour responsable de toute inadaptation des Equipements aux besoins du Locataire, de toute insuffisance de performance ou de tout manque de compatibilité des Equipements entre eux. Il en sera également ainsi si des mises au point sont rendues nécessaires pour leur fonctionnement ou si des évolutions techniques modifient leur compatibilité."); 
		$this->multicell(90,2,"3.2 Le Locataire reconnaît avoir été mis en garde par le Loueur du fait que certains Equipements peuvent présenter des disfonctionnements. Il incombe au Locataire de vérifier auprès de ses Fournisseurs de la qualité de ses Equipements, y compris lorsque ceux-ci sont incorporés dans un système informatique préexistant. N’étant ni fabricant de matériels ni concepteur de logiciels, Le loueur ne saurait être tenu pour responsable de tout disfonctionnement des Equipements; elle ne saurait donc être recherchée par le Locataire à raison de surcoûts ou dommages consécutifs à ces disfonctionnements");
		$this->multicell(90,2,"3.3 Les logiciels sont livrés selon les modalités directement convenues par le Locataire aveccl'éditeur. Le Locataire reconnaît avoir régularisé avec l'éditeur, en tant que mandataire ducLoueur, la licence d'utilisation des logiciels et faire son affaire directement avec l'éditeur ducrespect des clauses y figurant. La présente location étant conclue \"intuitu personae\" avec lecLocataire, les licences ne pourront être ni cédées, ni faire l'objet d'une sous-licence au profitcd'un tiers quel qu'il soit, sans accord préalable.");
		
		
		$this->article(15,164,'4','LIVRAISON DES EQUIPEMENTS',8);
		$this->setfont('arial','I',6);		
		$this->multicell(90,2,"4.1 Le Locataire prendra livraison des Equipements sous son unique responsabilité, à ses frais et risques, sans que la présence du Loueur ne soit requise. Lors de la réception des Equipements, le Locataire remettra au Loueur un procès-verbal de livraison signé constatant la conformité et le bon fonctionnement. Il s’interdit de refuser les Equipements pour tout motif autre qu’une non-conformité ou un mauvais fonctionnement, auxquels cas, il garantit le Loueur de toutes les condamnations qui pourraient être prononcées contre lui à raison des droits et recours du Fournisseur. S’il y a lieu il devra notifier au transporteur toutes les réserves utiles, les confirmer dans les délais légaux et informer immédiatement le Loueur par lettre recommandée avec accusé de réception.");
		
		$this->multicell(90,2,"4.2 Le procès verbal de livraison vaut autorisation de paiement du Loueur au Fournisseur. Si le Locataire transmet ce procès verbal sans avoir reçu les Equipements ou sans vérifier leur conformité et l’absence de vices ou défauts, il engage sa responsabilité et devra au Loueur réparation du préjudice subi par ce dernier.");
		$this->multicell(90,2,"4.3 Le Loueur transmet au Locataire l’ensemble des recours contre le Fournisseur y compris l’action en résolution de la vente pour vices rédhibitoires pour laquelle le Loueur lui donne en tant que de besoin mandat d’ester, sous réserve d’être mis en cause. Le Locataire renonce ainsi à tout recours contre le Loueur en cas de défaillance ou de vices cachés affectant les Equipements ou dans l’exécution des prestations et garanties. Si la résolution judiciaire de la vente est prononcée, le Contrat est résilié à la date du prononcé. Le Locataire s’engage alors à restituer les Equipements à ses frais au Fournisseur et se porte garant solidaire de ce dernier pour rembourser les sommes versées par le Loueur.4.4 Le Locataire dispose d’un délai de six mois à compter de la signature du Contrat pour faire procéder à la livraison des Equipements. Passé ce délai, le Contrat sera résilié aux torts du Locataire et ce dernier sera redevable d’une indemnité égale à la totalité des sommes réglées par le Loueur au titre du Contrat augmentée d’une pénalité équivalente à douze loyers.");
		$this->multicell(90,2,"4.5 Le contrat ne peut être interprété comme transférant un quelconque droit de propriété ou tout autre droit du locataire sur les éventuels produits sous licence. Le locataire s’engage à considérer les produits sous licence comme des informations confidentielles du titulaire des droits de propriété intellectuelle, à observer les restrictions de droits d’auteur et à ne pas reproduire ni vendre les produits sous licence. Tout litige lié au fonctionnement ou à l’utilisation du produit sous licence devra être réglé entre le titulaire des droits et le locataire. À cet effet, le Locataire dégage le Loueur de toute obligation de garantie d’éventuels vices ou défauts relatifs à la conformité, au fonctionnement et aux performances du produit sous licence, même si ces vices et défauts sont découverts au cours de la location. Le Locataire ne pourra invoquer un tel litige pour ne pas honorer ses engagements résultant du Contrat. De manière générale, le Locataire s’engage à respecter l’intégralité des droits du titulaire des droits sur le produit sous licence fourni pendant la durée de la location. Le Locataire renonce expressément à se prévaloir à l’encontre du Loueur de quelque exception que ce soit qu’il pourrait faire valoir contre le titulaire des droits du produit sous licence. Le Locataire garantit le Loueur et ses ayants droit contre tous recours du titulaire des droits ou de tout autre tiers et l’indemnisera, le cas échéant, de toutes conséquences dommageables de pareils recours.");
		$this->setxy(105,15);
		$this->setleftmargin(105);
		$this->article(105,15,'5',"DATE D’EFFET & DUREE DE LOCATION",8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"5.1 La période initiale de location prévue aux Conditions Particulières prend effet au plus tard le premier jour du mois ou du trimestre civils suivant celui au cours duquel s’effectue la livraison de la totalité des Equipements dans les locaux désignés par le Locataire constatée par le procès verbal de livraison. Cette disposition ne fait pas obstacle à l’application de l’article 6.3 alinéa 2. ");
		$this->multicell(95,2,"5.2 La durée de la location est fixée par les Conditions Particulières, en nombre entier de mois ou de trimestres, ceci sans préjudice de l’application des dispositions de l’art. 6. Elle ne peut en aucun cas être réduite par la seule volonté du locataire.");
		
		$this->article(105,42,'6',"REDEVANCES & PAIEMENT",8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"6.1 Le montant des loyers est fixé dans les Conditions Particulières. Si le prix des Equipements à payer au Fournisseur ou le taux de référence venait à augmenter entre la date de signature et la date de livraison, le montant du loyer serait ajusté proportionnellement. Il est rappelé que le taux de référence est la moyenne des derniers taux connus et publiés au jour du contrat de l'EURIBOR 12 mois et du TEC 5. (EURIBOR 12 mois : taux interbancaire offert en euro publié chaque jour par la Fédération Bancaire de l'Union Européenne et TEC 5 : Taux des échéances constantes à 5 ans, publié chaque jour par la Caisse des Dépôts et Consignations).");
		$this->multicell(95,2,"6.2 Les modalités de règlements des loyers sont précisées aux Conditions Particulières. Ils sont portables et non quérables, comme le sont les redevances éventuelles de mise à disposition prévues ci-après."); 
		$this->multicell(95,2,"6.3 En cas de livraisons partielles, une redevance de mise à disposition sera facturée au fur et à mesure de la livraison sur la base de la valeur des loyers proportionnellement au prix d’achat figurant sur le devis du Fournisseur au jour de la signature du contrat. Si la prise d’effet telle que définie à l’article 5 intervient après le premier jour du mois ou du trimestre civils, le Locataire payera au Loueur, pour lesdits mois ou trimestre en cours, une redevance de mise à disposition calculée prorata temporis au trentième ou au quatre-vingt dixième, sur la base du montant du loyer");
		$this->multicell(95,2,"6.4 Le premier loyer est exigible à la date prévue à l’article 5.1 ; il ne doit pas être confondu avec les redevances de mise à disposition."); 		
		$this->multicell(95,2,"6.5 Les prix mentionnés aux Conditions Particulières sont hors taxes. Tous droits, impôts et taxes liés aux Equipements sont à la charge du Locataire et lui sont facturés. Toute modification légale de ces droits, impôts et taxes s’applique de plein droit et sans avis.");
		$this->multicell(95,2,"6.6 Les loyers (TTC) et les redevances de mise à disposition (TTC) non payés à leur échéance porteront intérêt au profit du Loueur, de plein droit et sans qu’il soit besoin d’une quelconque mise en demeure, au taux refi de la Banque Centrale Européenne majoré de 10 points, conformément à l’article L 441-6 du Code de commerce.");
		$this->multicell(95,2,"6.7 Le Locataire autorise expressément le Loueur à recouvrer le montant des loyers et les redevances de mise à disposition, par l’intermédiaire de l’établissement bancaire de son choix, par prélèvements SEPA sur le compte bancaire indiqué par le Locataire. A cette fin, le Locataire remettra à CLEODIS un mandat de prélèvement SEPA au profit de CLEODIS et du Cessionnaire dans les conditions prévues à l’article 9.");
		$this->multicell(95,2,"6.8 Déchéance du terme : toute facture non payée à l’échéance entraîne immédiatement et de plein droit l’exigibilité des sommes facturées non échues"); 		
		$this->multicell(95,2,"6.9 A titre de clause pénale, toute somme impayée à l’échéance entraînera l’exigibilité d’une pénalité fixée à 15 % du montant des factures impayées, avec un minimum de 80 euros.");
		$this->multicell(95,2,"6.10 Conformément à l’article L 441-6 du Code de commerce, toute facture impayée entraîne aussi de plein droit l’exigibilité d’une indemnité forfaitaire de recouvrement de 40 euros.");
		$this->article(105,130,'7',"AUTRES PRESTATIONS – MANDATS DONNES AU LOUEUR",8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"Si le Locataire a conclu avec un Fournisseur un contrat de prestations autre que la location, le Loueur peut intervenir pour le compte du prestataire après avoir reçu mandat d’encaisser les redevances du contrat de service en même temps que les loyers. Le Loueur procède à la facturation pour compte du prestataire et reverse les redevances audit prestataire. Le Loueur n’assume aucune responsabilité quant à l’exécution desdites prestations et ne garantit donc pas les obligations des contractants à cet égard. Le Locataire s’interdit donc de refuser le paiement des loyers du contrat pour quelque motif que ce soit. La révocation du mandat peut être opéré à tout moment par un prestataire ou par le Loueur, à sa convenance et notamment en cas de contestation quelconque ou d’incident de paiement. Toute prestation non prévue dans le contrat et facturée directement par le prestataire n’est pas incluse dans le mandat précité ; il en est de même pour tout droit à remboursement pour le Locataire au titre de prestations non effectuées ou non satisfaisantes. Le Locataire reconnaît que le contrat de location qu'il a signé est indépendant du contrat de prestation ou de service qu'il a signé avec le prestataire");

		$this->article(105,166,'8','ENTRETIEN – REPARATION – EXPLOITATION',8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"8.1 Le Locataire étant responsable des Equipements, il s’engage à les utiliser suivant les spécifications du constructeur, dans un local permettant leur bon fonctionnement et leurentretien, ce afin de les maintenir en parfait état pendant toute la durée de la location. Par dérogation aux articles 1719 et suivants du code civil, le Locataire prend à sa charge l’ensemble des frais relatifs à l’utilisation, l’entretien et la réparation des Equipements. Par dérogation aux articles 1722 et 1724 du Code Civil, le Locataire ne pourra prétendre à aucune indemnité, aucun différé ni diminution de loyer s’il devait être privé de la jouissance des Equipements. En cas d’indisponibilité des Equipements et ce quelque ’en soit la raison, le Loueur aura la faculté de proposer au Locataire des équipements aux caractéristiques équivalentes afin de pallier cette indisponibilité. Le locataire renonce à son droit de résilier le contrat en cas d’indisponibilité des biens si la durée d’indisponibilité n’excède pas 6 mois et s’il n’a pas bénéficié d’équipements de remplacement tel qu’évoqué précédemment",0,'L');

		$this->multicell(95,2,"8.2 Le Locataire s’interdit toute modification des Equipements loués sans l’accord préalable du Loueur. La propriété de toute pièce remplacée, de tout accessoire incorporé ou de toute adjonction dans les Equipements pendant la location sera acquise aussitôt et sans récompense au Loueur.");
		$this->multicell(95,2,"8.3 Le Loueur ne pourra être tenu pour responsable en cas de détérioration, de mauvais fonctionnement ou de dommages causés par les Equipements"); 		
		$this->multicell(95,2,"8.4 Le déplacement des Equipements s’effectue sous l’entière responsabilité du Locataire, notamment pour les matériels dits portables. En cas de déménagement, les loyers restent dus quelle qu’en soit la durée.");

		$this->article(105,220,'9','SOUS LOCATION – CESSION – DELEGATION – NANTISSEMENT',8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"9.1 Le Locataire ne pourra ni sous-louer, ni prêter, mettre à disposition de quiconque à quelque titre et sous quelque forme que ce soit, tout ou partie des Equipements sans l’accord écrit du Loueur.");
		$this->multicell(95,2,"9.2 Le Locataire reconnaît que le Loueur l’a tenu informé de l’éventualité d’une cession, d’un nantissement ou d’une délégation, des Equipements ou des créances, au profit du Cessionnaire de son choix, pour une durée n’excédant pas la période initiale de location. Le Cessionnaire sera alors lié par les termes et conditions du contrat, ce que le Locataire accepte dès à présent et sans réserve. En cas d’acceptation par le Cessionnaire, celui-ci se substitue alors à CLEODIS sachant que l’obligation du Cessionnaire se limite à laisser au Locataire la libre disposition des Equipements, les autres obligations restant à la charge de CLEODIS. Le Locataire a alors l’obligation de payer au Cessionnaire les loyers ainsi que toute somme éventuellement due au titre du contrat, sans pouvoir opposer au Cessionnaire aucune compensation ou exception qu’il pourrait faire valoir vis à vis de CLEODIS.");
		

//		$this->setxy(15,273);
//		$this->setleftmargin(15);
//		$this->setfont('arial','I',7);
//		$this->multicell(200,3,"CLEODIS - 91 rue Nationale – 59000 LILLE -RCS Lille B 453 079 816",0,'C');
		
		//page droite
		$this->setautopagebreak('disabled',10);
		$this->SetLeftMargin(215);
		
		$this->setfont('arial','BI',8);		
		$this->setxy(215,5);
		$this->multicell(195,5,"CONDITIONS GENERALES DE LOCATION",1,'C');
		
		$this->setY(15);		
		$this->setfont('arial','I',6);						
		$this->multicell(95,2,"9.3 Le Locataire s’interdit de céder et/ou de se dessaisir de tout ou partie des Equipements, à quelque titre que ce soit et pour quelque motif que ce soit, même au profit du Loueur sans l’accord écrit du Cessionnaire. La cession des Equipements et des créances de loyer n’emporte pas novation du Contrat et CLEODIS se substituera au Cessionnaire au terme de la période initiale de location. Tout autre accord contractuel intervenu entre CLEODIS et le Locataire n’est pas opposable au Cessionnaire. Le Locataire sera informé de la cession par tout moyen et notamment par le libellé de l'avis de prélèvement, de la facture de loyer ou de l’échéancier qui seront émis. Le locataire dispense le Cessionnaire de la signification prévue par l'article 1690 du Code Civil.");
						
		$this->article(215,36,'10','ASSURANCE – SINISTRES',8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"Le Locataire est gardien responsable du matériel qu'il détient. Dès sa mise à disposition et jusqu'à la restitution effective de celui-ci et tant que le matériel reste sous sa garde, le Locataire assume tous les risques de détérioration et de perte, même en cas fortuit. Il est responsable de tout dommage causé par le matériel dans toutes circonstances. Il s'oblige en conséquence à souscrire une assurance couvrant sa responsabilité civile ainsi que celle du Loueur, et couvrant tous les risques de dommages ou de vol subis par les matériels loués avec une clause de délégation d'indemnités au profit du Loueur et une clause renonciation aux recours contre ce dernier. Le Locataire doit informer sans délai le Loueur de tout sinistre en précisant ses circonstances et ses conséquences. En cas de sinistre total ou de vol, couvert ou non par l'assurance, le contrat est résilié. Le Locataire doit au Loueur une indemnisation pour la perte du matériel et pour l'interruption prématurée du contrat calculée et exigible à la date de résiliation. Le montant global de cette indemnisation est égal aux loyers restant à échoir jusqu'à l'issue de la période de location, augmentés de la valeur estimée du matériel détruit ou volé au terme de cette période ou si une expertise est nécessaire, de sa valeur à dire d'expert au jour du sinistre. Les indemnités d'assurances, éventuellement perçues par le Loueur s'imputent en premier lieu sur l'indemnisation de la perte du matériel et ensuite sur l'indemnisation de l'interruption prématurée. Pour un sinistre partiel, en cas d'insuffisance de l'indemnité reçue de la Compagnie d'assurance, le Locataire est tenu de parfaire la remise en état complète des Equipements à ses frais");
		
		
		$this->article(215,86,'11','EVOLUTION DES EQUIPEMENTS',8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"Le Locataire pourra demander à CLEODIS, au cours de la période de validité du présentccontrat, la modification des Equipements loués, sous réserve de l’accord du LOUEUR ; lescmodifications éventuelles du contrat seront déterminées par l’accord écrit des parties.");

		$this->article(215,106,'12',"ANNULATION & RESILIATION",8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"12.1 En cas d’annulation de son engagement avant l’expiration du délai d’un mois donné au Loueur pour faire connaître son accord, comme il est dit à l’article 2.4 ci-dessus, le Locataire sera redevable envers le Loueur d’une indemnité d’annulation égale aux six premiers mois de loyers prévus au contrat. L’annulation ne sera reconnue effective qu’à la date de règlement de l’indemnité définie ci-dessus.");
		
		
		$this->multicell(95,2,"Le contrat est résilié de plein droit dès restitution du matériel loué. Le contrat est également résilié de plein droit si les deux conditions ci-après se trouvent réunies :");
		$this->multicell(95,2,"1er condition : Etre dans un des cas suivants :");
		$this->multicell(95,2,"1/ non respect de l'un des engagements pris au présent contrat et notamment le défaut de paiement d'une échéance ou de toute somme due en vertu du contrat, dans les 8 jours qui suivent une mise en demeure restée infructueuse;");
		$this->multicell(95,2,"2/ modification de la situation du Locataire et notamment décès, redressement judiciaire, liquidation amiable ou judiciaire, cessation d'activité, cession du fonds de commerce, de parts ou d'actions du Locataire, changement de forme sociale;");
		$this->multicell(95,2,"3/ modification concernant le matériel loué et notamment détérioration, destruction ou aliénation du matériel loué (apport en société, fusion absorption, scission, ...), ou perte ou diminution des garanties fournies.");
		
		$this->multicell(95,2,"2ème condition :");
		$this->multicell(95,2,"mise en demeure de restituer le matériel loué restée infructueuse dans les 8 jours de son envoi par lettre recommandée avec accusé de réception. Après mise en demeure de restituer, le Locataire ou ses ayants droits sont tenus de remettre immédiatement le matériel à disposition du Loueur dans les conditions prévues à l'article 14 traitant de la restitution du matériel. La résiliation entraîne de plein droit, au profit du Loueur, le paiement par le Locataire ou ses ayants droit, en réparation du préjudice subi en sus des loyers impayés et de leurs accessoires, d'une indemnité égale aux loyers restant à échoir au jour de la résiliation. Cette indemnité sera majorée d'une somme forfaitaire égale à 10 % de ladite indemnité à titre de clause pénale. Si le contrat est résilié pour l'un des motifs visés au présent article, tous les autres contrats qui auraient pu être conclus entre le Locataire aux présentes, le Loueur ou l'une des Sociétés de son Groupe (art. 145 du C.G.I.) sont, si le Loueur y a convenance, résiliés de plein droit."); 		
		
		$this->multicell(95,2,"12.2 La durée du contrat est ferme : il ne sera toléré aucune résiliation anticipée.");
		$this->multicell(95,2,"12.3 Le contrat pourra être résilié de plein droit par le LOUEUR, aux torts exclusifs du LOCATAIRE, si ce dernier ne respecte pas une obligation contractuelle. LE LOUEUR qui souhaite résilier en cas de faute du LOCATAIRE, telle par exemple le non-paiement des factures de location, devra préalablement mettre en demeure par LRAR ce dernier d’exécuter l’obligation concernée. Ce n’est qu’après cette mise en demeure restée infructueuse pendant une période de huit jours que la résiliation pourra être constatée aux torts exclusifs du LOCATAIRE. Le LOUEUR aura l’opportunité de solliciter le paiement de l’intégralité des loyers restants à courir jusque le terme du contrat, sans préjudice des indemnités évoqués à l’article 12.5.");
		$this->multicell(95,2,"12.4 Après mise en demeure de restituer, le Locataire ou ses ayants droits sont tenus de remettre immédiatement le matériel à disposition du Loueur dans les conditions prévues à l'article 14 traitant de la restitution du matériel.");
		
		$this->multicell(95,2,"12.5 La résiliation entraîne de plein droit, au profit du Loueur, le paiement par le Locataire ou ses ayants droit, en réparation du préjudice subi en sus des loyers impayés et de leurs accessoires, d'une indemnité égale aux loyers restant à échoir au jour de la résiliation. Cette indemnité sera majorée d'une somme forfaitaire égale à 10 % de ladite indemnité à titre de clause pénale. Si le contrat est résilié pour l'un des motifs visés au présent article, tous les autres contrats qui auraient pu être conclus entre le Locataire aux présentes et le Loueur sont, si le Loueur y a convenance, résiliés de plein droit. Celle-ci sera effective dès restitution du matériel loué.");

		$this->multicell(95,2,"12.6 Si après la résiliation le Locataire conserve pendant un certain temps la jouissance des Equipements, le Loueur est autorisé à mettre en recouvrement des redevances de mise à disposition de même montant que les loyers conventionnels, sans que le paiement de ces redevances puissent diminuer l’indemnité de résiliation telle que définie à l’article 12.5.");

		$this->multicell(95,2,"12.7 Les dispositions de l’article 12.6 sont applicables dans leur intégralité auxdites redevances de mise à disposition de l’article 6.");
		$this->multicell(95,2,"12.8 Les clauses ci-dessus relatives à une résiliation de plein droit, ne privent pas le LOUEUR de sa faculté d’exiger l’exécution pure et simple du contrat jusqu’à son terme, conformément à l’article 1184 du Code Civil.");
		$this->multicell(95,2,"12.9 Ce contrat est intuitu personae. En cas de cession de fonds de commerce, le LOCATAIRE devra informer le LOUEUR au moins 1 mois à l’avance, ce afin que le LOUEUR puisse statuer sur la poursuite ou l’arrêt du contrat en cours. Le transfert de contrat n'est jamais automatique et ne constitue pas un droit du Locataire. Il est soumis à l'accord du Loueur qui peut le refuser et exiger l'exécution du contrat jusqu'à son terme ou le paiement de la totalité des loyers restants dus.« La résiliation peut également intervenir, à la demande du Bailleur, en cas de décès du Locataire, de cessation de son activité pendant plus de trois (3) mois, de dissolution ou de cession de la société Locataire, de changement d’actionnariat, ainsi que dans les cas prévus par la réglementation en vigueur applicable aux entreprises en difficultés. »");
		$this->sety(15);
		$this->setleftmargin(315);

		$this->article(315,15,'13',"PROPRIETE",8);
		$this->ln(-2);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"13.1 CLEODIS conserve la propriété des Equipements loués sauf en cas d’application de l’article 9.2. Dans tous les cas CLEODIS conserve les relations commerciales avec le Locataire.");
		$this->multicell(95,2,"13.2 Le Locataire s’engage à apposer sur les Equipements pour toute la durée de la location, une étiquette de propriété. "); 		
		$this->multicell(95,2,"113.3 Le Locataire est tenu d’aviser immédiatement le Loueur par lettre recommandé avec accusé de réception en cas de tentative de saisie ou de toute autre intervention sur les Equipements et il devra élever toute protestation et prendre toute mesure pour faire reconnaître les droits du Loueur. Si la saisie a eu lieu, le Locataire devra faire diligence, à ses frais, pour en obtenir la mainlevée.");
		$this->multicell(95,2,"13.4 Le Locataire ne bénéficie en vertu du contrat d’aucun droit d’acquisition des Equipements pendant ou au terme de la location.");

		$this->article(315,157,'14',"RESTITUTION DES EQUIPEMENTS",8);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"14.1 Au delà de la durée initiale prévue aux Conditions Particulières, sauf pour l’une des
parties à notifier à l’autre par lettre recommandée avec accusé de réception en respectant un
préavis de six mois, son intention de ne pas reconduire le contrat, ce dernier est prolongé par
tacite reconduction par période d’un an minimum aux mêmes conditions et sur la base du
dernier loyer, sous réserve qu’il ait respecté l’intégralité de ses obligations contractuelles");
		$this->multicell(95,2,"14.2 Le Locataire doit, en fin de période de location, restituer au Loueur au lieu désigné par
celui-ci, les Equipements en parfait état d’entretien et de fonctionnement, les frais de
transport et de déconnexion incombant au Locataire. Le Locataire doit aussi restituer tout
matériel endommagé ou hors d’état de fonctionnement, à ses frais et au lieu désigné par le
Loueur. Tout frais éventuel de remise en état, destruction ou recyclage, sera à la charge du
Locataire et les Equipements manquants lui seront facturés selon la valeur de marché à la
date de la reprise");
		$this->multicell(95,2,"14.3 Si le Locataire ne restitue pas immédiatement et de son propre chef les Equipements au Loueur à l'expiration du contrat, il est redevable d'une indemnité égale aux loyers jusqu'à leur restitution effective.");
		
		$this->article(315,203,'15',"ATTRIBUTION DE JURIDICTION",8);
		$this->ln(-2);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"Le Loueur et le Locataire contractant en qualité de commerçant attribuent compétence, même en cas de pluralité de défendeurs ou d'appel en garantie, au tribunal de commerce de CLEODIS ou du Cessionnaire. Pour les Locataires non commerçants, tout litige auquel peut donner lieu l'exécution des présentes est de la compétence du Tribunal de Commerce du domicile du défendeur, conformément aux conditions de l'article 42 du Nouveau Code de Procédure Civile. La loi française est applicable à tout litige né du présent contrat ou de ses suites.");
		
		$this->article(315,230,'16',"INFORMATIQUE ET LIBERTE",8);
		$this->ln(-2);
		$this->setfont('arial','I',6);		
		$this->multicell(95,2,"Les informations figurant dans les présentes ou celles recueillies ultérieurement ne seront utilisées et ne feront l'objet de communication aux destinataires déclarés à la commission nationale  de l'informatique et des libertés que pour les seules nécessités de gestion, ou actions commerciales. Ces informations pourront toutefois être communiquées aux établissements de crédit soumis au secret professionnel bancaire, en vertu des dispositions de l'article L.511-33 et suivants du Code Monétaire et Financier, liés au Loueur en vue de la gestion des financements. Elles pourront donner lieu à l'exercice du droit d'accès et de rectification dans les conditions prévues par la loi du 6 janvier 1978, en particulier après paiement de la redevance légale sauf rectification justifiée. En outre, en cas d'incident de paiement caractérisé, elles pourront être communiquées au Fichier National des incidents de remboursement des crédits géré par la Banque de France.");

		$this->setxy(15,280);
		$this->setfont('arial','I',8);
		$this->multicell(0,3,"CGL Cléodis V 2008-03");
		
	}

	/** CGL d'un PDF d'un contrat en A3
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 10-08-2012
	*/
	public function conditionsGeneralesDeLocationA4($type)  {
		$this->unsetHeader();
		$this->AddPage();
		$this->SetLeftMargin(10);
		$this->setAutoPageBreak(false);
		//page gauche
		$this->setfont('arial','BI',8);		
		$this->setxy(5,5);
		$this->multicell(200,5,"CONDITIONS GENERALES DE ".($type=="vente"?"VENTE":"LOCATION"),1,'C');
		
		$this->SetLeftMargin(5);
		$this->article(5,10,'1','DEFINITIONS',6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"Le présent contrat utilise les termes suivants ayant le sens qui leur est donné ci-dessous :",0,'L');
		$this->multicell(50,2,"Locataire : entité juridique au bénéfice de laquelle les Equipements sont loués et les services assurés",0,'L');
		$this->multicell(50,2,"Loueur : CLEODIS ou Cessionnaire",0,'L');
		$this->multicell(50,2,"Fournisseur : entité juridique autre que CLEODIS désigné par le Locataire pour fournir les Equipements et/ou réaliser certains services associés à la location desdits équipements.",0,'L');
		$this->multicell(50,2,"Cessionnaire : établissement financier ou de crédit agréé en qualité de société financière ou société de location.",0,'L');
		$this->multicell(50,2,"Services : prestation de service fournie par le Loueur et/ou le Fournisseur tel que prévu dans le présent contrat.",0,'L');
		$this->multicell(50,2,"Equipements : tout équipement et notamment équipements informatiques, bureautiques et de télécommunications y compris l’ensemble des droits d’utilisations des logiciels d’exploitation et d’application qui y sont associés.",0,'L');
		
		$this->article(5,51,'2','OBJET & VALIDITE',6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"2.1 L’objet du présent contrat consiste en la location d’Equipements à laquelle des Services sont associés, l’ensemble étant détaillé dans les Conditions Particulières.",0,'L');
		$this->multicell(50,2,"2.2 La signature du Contrat constitue un engagement ferme et définitif de la part du Locataire et annule et remplace tous accords antérieurs, écrits et verbaux, se rapportant aux dits Equipements.",0,'L'); 		
		$this->multicell(50,2,"2.3 Les parties reconnaissent que les Equipements loués ayant un rapport direct avec l’activité professionnelle du Locataire, le code de la consommation ne s’applique pas.",0,'L');
		$this->multicell(50,2,"2.4 Le Loueur dispose d’un mois à compter de la réception par lui du présent contrat pour signifier son accord au Locataire. Passé ce délai, le Locataire pourra se rétracter sans aucune indemnité due de part et d’autre.",0,'L');
		$this->multicell(50,2,"2.5 Au cas où le Loueur prendrait connaissance, après la conclusion du contrat mais avant la livraison des Equipements, de faits concernant la solvabilité du Locataire pouvant laisser craindre de sa part une incapacité à exécuter tout ou partie de ses obligations contractuelles, le Contrat serait alors résolu de plein droit à l’initiative du Loueur sans qu’aucune indemnité ne soit due de part et d’autre.",0,'L');
		$this->multicell(50,2,"2.6 Toute modification des clauses et conditions du présent contrat sera réputée nulle et non avenue sauf à résulter d’un avenant écrit et signé par CLEODIS et validé par le Cessionnaire.",0,'L');
		
		$this->article(5,112,'3','CHOIX DES EQUIPEMENTS',6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"3.1 Le Locataire reconnaît avoir choisi librement, en toute indépendance et sous sa seule responsabilité, les Equipements loués, ainsi que les Fournisseurs, constructeurs et éditeurs qui participent à la fabrication, l’assemblage, la livraison et l’installation des Equipements et de leurs composants. Il reconnaît avoir pris connaissance des spécifications techniques et des modalités d’exploitation préalablement à la location. En conséquence, le Loueur ne saurait en aucun cas être recherché par le Locataire à raison de dommages causés par, ou à ces Equipements et résultant d’un vice de construction. Le Loueur ne saurait être tenu ni à une obligation de résultat, ni pour responsable de toute inadaptation des Equipements aux besoins du Locataire, de toute insuffisance de performance ou de tout manque de compatibilité des Equipements entre eux. Il en sera également ainsi si des mises au point sont rendues nécessaires pour leur fonctionnement ou si des évolutions techniques modifient leur compatibilité.",0,'L'); 
		$this->multicell(50,2,"3.2 Le Locataire reconnaît avoir été mis en garde par le Loueur du fait que certains Equipements peuvent présenter des disfonctionnements. Il incombe au Locataire de vérifier auprès de ses Fournisseurs de la qualité de ses Equipements, y compris lorsque ceux-ci sont incorporés dans un système informatique préexistant. N’étant ni fabricant de matériels ni concepteur de logiciels, Le loueur ne saurait être tenu pour responsable de tout disfonctionnement des Equipements; elle ne saurait donc être recherchée par le Locataire à raison de surcoûts ou dommages consécutifs à ces disfonctionnements.",0,'L'); 		
		$this->multicell(50,2,"3.3 Les logiciels sont livrés selon les modalités directement convenues par le Locataire avec l'éditeur. Le Locataire reconnaît avoir régularisé avec l'éditeur, en tant que mandataire du Loueur, la licence d'utilisation des logiciels et faire son affaire directement avec l'éditeur du respect des clauses y figurant. La présente location étant conclue \"intuitu personae\" avec le Locataire, les licences ne pourront être ni cédées, ni faire l'objet d'une sous-licence au profit d'un tiers quel qu'il soit, sans accord préalable.",0,'L');
		
		
		$this->article(5,196,'4','LIVRAISON DES EQUIPEMENTS',6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"4.1 Le Locataire prendra livraison des Equipements sous son unique responsabilité, à ses frais et risques, sans que la présence du Loueur ne soit requise. Lors de la réception des Equipements, le Locataire remettra au Loueur un procès-verbal de livraison signé constatant la conformité et le bon fonctionnement. Il s’interdit de refuser les Equipements pour tout motif autre qu’une non-conformité ou un mauvais fonctionnement, auxquels cas, il garantit le Loueur de toutes les condamnations qui pourraient être prononcées contre lui à raison des droits et recours du Fournisseur. S’il y a lieu il devra notifier au transporteur toutes les réserves utiles, les confirmer dans les délais légaux et informer immédiatement le Loueur par lettre recommandée avec accusé de réception.",0,'L');
		$this->multicell(50,2,"4.2 Le procès verbal de livraison vaut autorisation de paiement du Loueur au Fournisseur. Si le Locataire transmet ce procès verbal sans avoir reçu les Equipements ou sans vérifier leur conformité et l’absence de vices ou défauts, il engage sa responsabilité et devra au Loueur réparation du préjudice subi par ce dernier.",0,'L'); 		
		
		$this->multicell(50,2,"4.3 Le Loueur transmet au Locataire l’ensemble des recours contre le Fournisseur y compris l’action en résolution de la vente pour vices rédhibitoires pour laquelle le Loueur lui donne en tant que de besoin mandat d’ester, sous réserve d’être mis en cause. Le Locataire renonce ainsi à tout recours contre le Loueur en cas de défaillance ou de vices cachés affectant les Equipements ou dans l’exécution des prestations et garanties. Si la résolution judiciaire de la vente est prononcée, le Contrat est résilié à la date du prononcé. Le Locataire s’engage alors à restituer les Equipements à ses frais au Fournisseur et se porte garant solidaire de ce dernier pour rembourser les sommes versées par le Loueur.",0,'L');
		$this->multicell(50,2,"Le Locataire dispose d’un délai de six mois à compter de la signature du Contrat pour faire procéder à la livraison des Equipements. Passé ce délai, le Contrat sera résilié aux torts du Locataire et ce dernier sera redevable d’une indemnité égale à la totalité des sommes réglées par le Loueur au titre du Contrat augmentée d’une pénalité équivalente à douze loyers.",0,'L');
		$this->setleftmargin(55);
		$this->sety(10);
		$this->multicell(50,2,"4.5 Le contrat ne peut être interprété comme transférant un quelconque droit de propriété ou tout autre droit du locataire sur les éventuels produits sous licence. Le locataire s’engage à considérer les produits sous licence comme des informations confidentielles du titulaire des droits de propriété intellectuelle, à observer les restrictions de droits d’auteur et à ne pas reproduire ni vendre les produits sous licence. Tout litige lié au fonctionnement ou à l’utilisation du produit sous licence devra être réglé entre le titulaire des droits et le locataire. À cet effet, le Locataire dégage le Loueur de toute obligation de garantie d’éventuels vices ou défauts relatifs à la conformité, au fonctionnement et aux performances du produit sous licence, même si ces vices et défauts sont découverts au cours de la location. Le Locataire ne pourra invoquer un tel litige pour ne pas honorer ses engagements résultant du Contrat. De manière générale, le Locataire s’engage à respecter l’intégralité des droits du titulaire des droits sur le produit sous licence fourni pendant la durée de la location. Le Locataire renonce expressément à se prévaloir à l’encontre du Loueur de quelque exception que ce soit qu’il pourrait faire valoir contre le titulaire des droits du produit sous licence. Le Locataire garantit le Loueur et ses ayants droit contre tous recours du titulaire des droits ou de tout autre tiers et l’indemnisera, le cas échéant, de toutes conséquences dommageables de pareils recours.",0,'L');


		$this->article(55,64,'5',"DATE D'EFFET & DUREE DE \nLOCATION",6,2);
		$this->ln(-2);
		$this->setfont('arial','I',5);
		$this->multicell(50,2,"5.1 La période initiale de location prévue aux Conditions Particulières prend effet au plus tard le premier jour du mois ou du trimestre civils suivant celui au cours duquel s’effectue la livraison de la totalité des Equipements dans les locaux désignés par le Locataire constatée par le procès verbal de livraison. Cette disposition ne fait pas obstacle à l’application de l’article 6.3 alinéa 2",0,'L');
		$this->multicell(50,2,"5.2 La durée de la location est fixée par les Conditions Particulières, en nombre entier de mois ou de trimestres, ceci sans préjudice de l’application des dispositions de l’art. 6. Elle ne peut en aucun cas être réduite par la seule volonté du locataire.",0,'L');
		
		$this->article(55,92,'6',"REDEVANCES",6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"6.1 Le montant des loyers est fixé dans les Conditions Particulières. Si le prix des Equipements à payer au Fournisseur ou le taux de référence venait à augmenter entre la date de signature et la date de livraison, le montant du loyer serait ajusté proportionnellement. Il est rappelé que le taux de référence est la moyenne des derniers taux connus et publiés au jour du contrat de l'EURIBOR 12 mois et du TEC 5. (EURIBOR 12 mois : taux interbancaire offert en euro publié chaque jour par la Fédération Bancaire de l'Union Européenne et TEC 5 : Taux des échéances constantes à 5 ans, publié chaque jour par la Caisse des Dépôts et Consignations).",0,'L');
		$this->multicell(50,2,"6.2 Les modalités de règlements des loyers sont précisées aux Conditions Particulières. Ils sont portables et non quérables, comme le sont les redevances éventuelles de mise à disposition prévues ci-après.",0,'L');  
		$this->multicell(50,2,"6.3 En cas de livraisons partielles, une redevance de mise à disposition sera facturée au fur et à mesure de la livraison sur la base de la valeur des loyers proportionnellement au prix d’achat figurant sur le devis du Fournisseur au jour de la signature du contrat. Si la prise d’effet telle que définie à l’article 5 intervient après le premier jour du mois ou du trimestre civils, le Locataire payera au Loueur, pour lesdits mois ou trimestre en cours, une redevance de mise à disposition calculée prorata temporis au trentième ou au quatre-vingt dixième, sur la base du montant du loyer.",0,'L');
		$this->multicell(50,2,"6.4 Le premier loyer est exigible à la date prévue à l’article 5.1 ; il ne doit pas être confondu avec les redevances de mise à disposition.",0,'L'); 		
		$this->multicell(50,2,"6.5 Les prix mentionnés aux Conditions Particulières sont hors taxes. Tous droits, impôts et taxes liés aux Equipements sont à la charge du Locataire et lui sont facturés. Toute modification légale de ces droits, impôts et taxes s’applique de plein droit et sans avis.",0,'L'); 		
		$this->multicell(50,2,"6.6 Les loyers (TTC) et les redevances de mise à disposition (TTC) non payés à leur échéance porteront intérêt au profit du Loueur, de plein droit et sans qu’il soit besoin d’une quelconque mise en demeure, au taux refi de la Banque Centrale Européenne majoré de 10 points, conformément à l’article L 441-6 du Code de commerce.",0,'L'); 		
		$this->multicell(50,2,"6.7 Le Locataire autorise expressément le Loueur à recouvrer le montant des loyers et les redevances de mise à disposition, par l’intermédiaire de l’établissement bancaire de son choix, par prélèvements SEPA sur le compte bancaire indiqué par le Locataire. A cette fin, le Locataire remettra à CLEODIS un mandat de prélèvement SEPA au profit de CLEODIS et du Cessionnaire dans les conditions prévues à l’article 9.",0,'L');
		$this->multicell(50,2,"6.8 Déchéance du terme : toute facture non payée à l’échéance entraîne immédiatement et de plein droit l’exigibilité des sommes facturées non échues.",0,'L'); 		
		$this->multicell(50,2,"6.9 A titre de clause pénale, toute somme impayée à l’échéance entraînera l’exigibilité d’une pénalité fixée à 15 % du montant des factures impayées, avec un minimum de 80 euros.",0,'L'); 		
		$this->multicell(50,2,"6.10 Conformément à l’article L 441-6 du Code de commerce, toute facture impayée entraîne aussi de plein droit l’exigibilité d’une indemnité forfaitaire de recouvrement de 40 euros.",0,'L');
		
		$this->article(55,217,'7',"AUTRES PRESTATIONS - MANDATS\nDONNES AU LOUEUR",6,2);
		$this->ln(-2);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"Si le Locataire a conclu avec un Fournisseur un contrat de prestations autre que la location, le Loueur peut intervenir pour le compte du prestataire après avoir reçu mandat d’encaisser les redevances du contrat de service en même temps que les loyers. Le Loueur procède à la facturation pour compte du prestataire et reverse les redevances audit prestataire. Le Loueur n’assume aucune responsabilité quant à l’exécution desdites prestations et ne garantit donc pas les obligations des contractants à cet égard. Le Locataire s’interdit donc de refuser le paiement des loyers du contrat pour quelque motif que ce soit. La révocation du mandat peut être opéré à tout moment par un prestataire ou par le Loueur, à sa convenance et notamment en cas de contestation quelconque ou d’incident de paiement. Toute prestation non prévue dans le contrat et facturée directement par le prestataire n’est pas incluse dans le mandat précité ; il en est de même pour tout droit à remboursement pour le Locataire au titre de prestations non effectuées ou non satisfaisantes. Le Locataire reconnaît que le contrat de location qu'il a signé est indépendant du contrat de prestation ou de service qu'il a signé avec le prestataire",0,'L');
				
		$this->article(55,267,'8',"ENTRETIEN – REPARATION – \nEXPLOITATION",6,2);
		$this->ln(-2);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"8.1 Le Locataire étant responsable des Equipements, il s’engage à les utiliser suivant les spécifications du constructeur, dans un local permettant leur bon fonctionnement et leur entretien, ce afin de les maintenir en parfait état pendant toute la durée de la location. Par dérogation aux articles 1719 et suivants du code",0,'L');
		
		$this->setleftmargin(105);
		$this->sety(10);
		
		$this->multicell(50,2,"civil, le Locataire prend à sa charge l’ensemble des frais relatifs à l’utilisation, l’entretien et la réparation des Equipements. Par dérogation aux articles 1722 et 1724 du Code Civil, le Locataire ne pourra prétendre à aucune indemnité, aucun différé ni diminution de loyer s’il devait être privé de la jouissance des Equipements. En cas d’indisponibilité des Equipements et ce quelque ’en soit la raison, le Loueur aura la faculté de proposer au Locataire des équipements aux caractéristiques équivalentes afin de pallier cette indisponibilité. Le locataire renonce à son droit de résilier le contrat en cas d’indisponibilité des biens si la durée d’indisponibilité n’excède pas 6 mois et s’il n’a pas bénéficié d’équipements de remplacement tel qu’évoqué précédemment.",0,'L');
		$this->multicell(50,2,"8.2 Le Locataire s’interdit toute modification des Equipements loués sans l’accord préalable du Loueur. La propriété de toute pièce remplacée, de tout accessoire incorporé ou de toute adjonction dans les Equipements pendant la location sera acquise aussitôt et sans récompense au Loueur.",0,'L'); 		
		$this->multicell(50,2,"8.3 Le Loueur ne pourra être tenu pour responsable en cas de détérioration, de mauvais fonctionnement ou de dommages causés par les Equipements.",0,'L'); 		
		$this->multicell(50,2,"8.4 Le déplacement des Equipements s’effectue sous l’entière responsabilité du Locataire, notamment pour les matériels dits portables. En cas de déménagement, les loyers restent dus quelle qu’en soit la durée.",0,'L');
		
						
		$this->article(105,66,'9',"SOUS LOCATION – CESSION – \nDELEGATION – NANTISSEMENT",6,2);
		$this->ln(-2);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"9.1 Le Locataire ne pourra ni sous-louer, ni prêter, mettre à disposition de quiconque à quelque titre et sous quelque forme que ce soit, tout ou partie des Equipements sans l’accord écrit du Loueur.",0,'L'); 		
		$this->multicell(50,2,"9.2 Le Locataire reconnaît que le Loueur l’a tenu informé de l’éventualité d’une cession, d’un nantissement ou d’une délégation, des Equipements ou des créances, au profit du Cessionnaire de son choix, pour une durée n’excédant pas la période initiale de location. Le Cessionnaire sera alors lié par les termes et conditions du contrat, ce que le Locataire accepte dès à présent et sans réserve. En cas d’acceptation par le Cessionnaire, celui-ci se substitue alors à CLEODIS sachant que l’obligation du Cessionnaire se limite à laisser au Locataire la libre disposition des Equipements, les autres obligations restant à la charge de CLEODIS. Le Locataire a alors l’obligation de payer au Cessionnaire les loyers ainsi que toute somme éventuellement due au titre du contrat, sans pouvoir opposer au Cessionnaire aucune compensation ou exception qu’il pourrait faire valoir vis à vis de CLEODIS",0,'L'); 		
		$this->multicell(50,2,"dessaisir de tout ou partie des Equipements, à quelque titre que ce soit et pour quelque motif que ce soit, même au profit du Loueur sans l’accord écrit du Cessionnaire. La cession des Equipements et des créances de loyer n’emporte pas novation du Contrat et CLEODIS se substituera au Cessionnaire au terme de la période initiale de location. Tout autre accord contractuel intervenu entre CLEODIS et le Locataire n’est pas opposable au Cessionnaire. Le Locataire sera informé de la cession par tout moyen et notamment par le libellé de l'avis de prélèvement, de la facture de loyer ou de l’échéancier qui seront émis. Le locataire dispense le Cessionnaire de la signification prévue par l'article 1690 du Code Civil.",0,'L'); 						
		$this->article(105,140,'10','ASSURANCES – SINISTRES',6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"Le Locataire est gardien responsable du matériel qu'il détient. Dès sa mise à disposition et jusqu'à la restitution effective de celui-ci et tant que le matériel reste sous sa garde, le Locataire assume tous les risques de détérioration et de perte, même en cas fortuit. Il est responsable de tout dommage causé par le matériel dans toutes circonstances. Il s'oblige en conséquence à souscrire une assurance couvrant sa responsabilité civile ainsi que celle du Loueur, et couvrant tous les risques de dommages ou de vol subis par les matériels loués avec une clause de délégation d'indemnités au profit du Loueur et une clause renonciation aux recours contre ce dernier. Le Locataire doit informer sans délai le Loueur de tout sinistre en précisant ses circonstances et ses conséquences. En cas de sinistre total ou de vol, couvert ou non par l'assurance, le contrat est résilié. Le Locataire doit au Loueur une indemnisation pour la perte du matériel et pour l'interruption prématurée du contrat calculée et exigible à la date de résiliation. Le montant global de cette indemnisation est égal aux loyers restant à échoir jusqu'à l'issue de la période de location, augmentés de la valeur estimée du matériel détruit ou volé au terme de cette période ou si une expertise est nécessaire, de sa valeur à dire d'expert au jour du sinistre. Les indemnités d'assurances, éventuellement perçues par le Loueur s'imputent en premier lieu sur l'indemnisation de la perte du matériel et ensuite sur l'indemnisation de l'interruption prématurée. Pour un sinistre partiel, en cas d'insuffisance de l'indemnité reçue de la Compagnie d'assurance, le Locataire est tenu de parfaire la remise en état complète des Equipements à ses frais.",0,'L'); 		 		 		
		$this->article(105,205,'11','EVOLUTION DES EQUIPEMENTS',6); 		
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"Le Locataire pourra demander à CLEODIS, au cours de la période de validité du présent contrat, la modification des Equipements loués, sous réserve de l’accord du LOUEUR ; les modifications éventuelles du contrat seront déterminées par l’accord écrit des parties.",0,'L');

		$this->article(105,218,'12',"ANNULATION & RESILIATION",6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"12.1 En cas d’annulation de son engagement avant l’expiration du délai d’un mois donné au Loueur pour faire connaître son accord, comme il est dit à l’article 2.4 ci-dessus, le Locataire sera redevable envers le Loueur d’une indemnité d’annulation égale aux six premiers mois de loyers prévus au contrat. L’annulation ne sera reconnue effective qu’à la date de règlement de l’indemnité définie ci-dessus. ",0,'L');
		$this->multicell(50,2,"12.2 La durée du contrat est ferme : il ne sera toléré aucune résiliation anticipée.",0,'L');
		$this->multicell(50,2,"12.3 Le contrat pourra être résilié de plein droit par le LOUEUR, aux torts exclusifs du LOCATAIRE, si ce dernier ne respecte pas une obligation contractuelle. LE LOUEUR qui souhaite résilier en cas de faute du LOCATAIRE, telle par exemple le non-paiement des factures de location, devra préalablement mettre en demeure par LRAR ce dernier d’exécuter l’obligation concernée. Ce n’est qu’après cette mise en demeure restée infructueuse pendant une période de huit jours que la résiliation pourra être constatée aux torts exclusifs du LOCATAIRE. Le LOUEUR aura l’opportunité de solliciter le paiement de l’intégralité des loyers restants à courir jusque le terme du contrat, sans préjudice des indemnités évoqués à l’article 12.5.",0,'L');
		$this->multicell(50,2,"12.4 Après mise en demeure de restituer, le Locataire 1/ non respect de l'un des engagements pris au présent contrat et notamment le défaut de paiement d'une échéance ou de toute somme due en vertu du contrat, dans les 8 jours qui suivent une mise en demeure restée infructueuse;",0,'L');
		$this->sety(10);
		$this->setleftmargin(155);
		
		$this->multicell(50,2,"12.5 La résiliation entraîne de plein droit, au profit du Loueur, le paiement par le Locataire ou ses ayants droit, en réparation du préjudice subi en sus des loyers impayés et de leurs accessoires, d'une indemnité égale aux loyers restant à échoir au jour de la résiliation. Cette indemnité sera majorée d'une somme forfaitaire égale à 10 % de ladite indemnité à titre de clause pénale. Si le contrat est résilié pour l'un des motifs visés au présent article, tous les autres contrats qui auraient pu être conclus entre le Locataire aux présentes et le Loueur sont, si le Loueur y a convenance, résiliés de plein droit. Celle-ci sera effective dès restitution du matériel loué",0,'L');
		$this->multicell(50,2,"12.6 Si après la résiliation le Locataire conserve pendant un certain temps la jouissance des Equipements, le Loueur est autorisé à mettre en recouvrement des redevances de mise à disposition de même montant que les loyers conventionnels, sans que le paiement de ces redevances puissent diminuer l’indemnité de résiliation telle que définie à l’article 12.5.",0,'L');
		
		$this->multicell(50,2,"12.7 Les dispositions de l’article 12.6 sont applicables dans leur intégralité auxdites redevances de mise à disposition de l’article 6.",0,'L'); 		
		
		$this->multicell(50,2,"12.8 Les clauses ci-dessus relatives à une résiliation de plein droit, ne privent pas le LOUEUR de sa faculté d’exiger l’exécution pure et simple du contrat jusqu’à son terme, conformément à l’article 1184 du Code Civil.",0,'L');
		$this->multicell(50,2,"12.9 Ce contrat est intuitu personae. En cas de cession de fonds de commerce, le LOCATAIRE devra informer le LOUEUR au moins 1 mois à l’avance, ce afin que le LOUEUR puisse statuer sur la poursuite ou l’arrêt du contrat en cours. Le transfert de contrat n'est jamais automatique et ne constitue pas un droit du Locataire. Il est soumis à l'accord du Loueur qui peut le refuser et exiger l'exécution du contrat jusqu'à son terme ou le paiement de la totalité des loyers restants dus.« La résiliation peut également intervenir, à la demande du Bailleur, en cas de décès du Locataire, de cessation de son activité pendant plus de trois (3) mois, de dissolution ou de cession de la société Locataire, de changement d’actionnariat, ainsi que dans les cas prévus par la réglementation en vigueur applicable aux entreprises en difficultés. »",0,'L');
		
		
		$this->article(155,96,'13',"PROPRIETE",6);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"13.1 CLEODIS conserve la propriété des Equipements loués sauf en cas d’application de l’article 9.2. Dans tous les cas CLEODIS conserve les relations commerciales avec le Locataire.",0,'L');
		$this->multicell(50,2,"13.2 Le Locataire s’engage à apposer sur les Equipements pour toute la durée de la location, une étiquette de propriété.",0,'L');
		$this->multicell(50,2,"13.3 Le Locataire est tenu d’aviser immédiatement le Loueur par lettre recommandé avec accusé de réception en cas de tentative de saisie ou de toute autre intervention sur les Equipements et il devra élever toute protestation et prendre toute mesure pour faire reconnaître les droits du Loueur. Si la saisie a eu lieu, le Locataire devra faire diligence, à ses frais, pour en obtenir la mainlevée.",0,'L');
		$this->multicell(50,2,"13.4 Le Locataire ne bénéficie en vertu du contrat d’aucun droit d’acquisition des Equipements pendant ou au terme de la location.",0,'L');

		$this->article(155,134,'14',"RESTITUTION DES EQUIPEMENTS",6,3,50);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"14.1 Au delà de la durée initiale prévue aux Conditions Particulières, sauf pour l’une des parties à notifier à l’autre par lettre recommandée avec accusé de réception en respectant un préavis de six mois, son intention de ne pas reconduire le contrat, ce dernier est prolongé par tacite reconduction par période d’un an minimum aux mêmes conditions et sur la base du dernier loyer, sous réserve qu’il ait respecté l’intégralité de ses obligations contractuelles.",0,'L');
		$this->multicell(50,2,"14.2 Le Locataire doit, en fin de période de location, restituer au Loueur au lieu désigné par celui-ci, les Equipements en parfait état d’entretien et de fonctionnement, les frais de transport et de déconnexion incombant au Locataire. Le Locataire doit aussi restituer tout matériel endommagé ou hors d’état de fonctionnement, à ses frais et au lieu désigné par le Loueur. Tout frais éventuel de remise en état, destruction ou recyclage, sera à la charge du Locataire et les Equipements manquants lui seront facturés selon la valeur de marché à la date de la reprise.",0,'L');
		$this->multicell(50,2,"14.3 Si le Locataire ne restitue pas immédiatement et de son propre chef les Equipements au Loueur à l’expiration du contrat, il est redevable d’une indemnité égale aux loyers jusqu’à leur restitution effective.",0,'L');
		
		$this->article(155,188,'15',"ATTRIBUTION DE JURIDICTION",6,3,50);
		$this->ln(-3);
		$this->setfont('arial','I',5);		
		$this->multicell(52,2,"Le Loueur et le Locataire contractant en qualité de commerçant attribuent compétence, même en cas de pluralité de défendeurs ou d'appel en garantie, au Tribunal de commerce de CLEODIS ou du Cessionnaire. Pour les Locataires non commerçants, tout litige auquel peut donner lieu l'exécution des présentes est de la compétence du Tribunal de Commerce du domicile du défendeur, conformément aux conditions de l'article 42 du Nouveau Code de Procédure Civile. La loi française est applicable à tout litige né du présent contrat ou de ses suites.",0,'L');
		
		$this->article(155,215,'16',"INFORMATIQUE & LIBERTE",6);
		$this->ln(-3.5);
		$this->setfont('arial','I',5);		
		$this->multicell(50,2,"Les informations nominatives recueillies dans le cadre du Contrat sont obligatoires pour le traitement de votre demande. Elles sont destinées, de même que celles qui seront recueillies ultérieurement, au LOUEUR pour les besoins de la gestion des opérations de location consenties aux entreprises. Elles pourront, de convention expresse, être communiquées par le LOUEUR à ses sous-traitants, partenaires, courtiers et assureurs, ainsi qu’aux personnes morales du groupe du LOUEUR, à des fins de gestion ou de prospection commerciale. Vous pouvez, pour des motifs légitimes, vous opposer à ce que ces données fassent l’objet d’un traitement. Vous pouvez également vous opposer, sans frais, à ce qu’elles soient utilisées à des fins de prospection, notamment commerciale. Vos droits d’accès, de rectification et d’opposition peuvent être exercés auprès de la Direction de la communication du LOUEUR. Le responsable du traitement est le Directeur de Communication.",0,'L');

		$this->sety(280);
		$this->setleftmargin(15);
		$this->setfont('arial','BI',8);
		$this->multicell(0,3,"CGL CLEDOIS V09-14",0,"R");
		$this->setAutoPageBreak(true);
	}

	/** PDF d'un contrat en A4
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param int $id Identifiant commande
	*/
	public function contratA4($id) {
		$this->noPageNo = true;
		$this->unsetHeader();
		$this->commandeInit($id);
		$this->Open();
		$this->AddPage();
		$this->A3 = false;
		$this->A4 = true;

		$this->setfont('arial','B',10);
		$this->image(__PDF_PATH__."/cleodis/logo.jpg",5,18,55);

		
		$this->sety(10);
		$this->multicell(0,5,$this->affaire['nature']=="vente"?"LE VENDEUR":"LE LOUEUR",0,'C');
		$this->setLeftMargin(65);
		$this->setfont('arial','B',7);
		$this->multicell(0,3,$this->societe['societe']." - ".$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville'],0);
		$this->multicell(0,3,"Tél :".$this->societe['tel']." - Fax :".$this->societe['fax'],0);
		if($this->societe['id_pays'] =='FR'){
			$this->multicell(0,3,"RCS LILLE B ".$this->societe['siren']." – APE 7739Z N° de TVA intracommunautaire : FR 91 ".$this->societe["siren"],0);
		}else{
			$this->multicell(0,3,"Numéro de TVA  ".$this->societe['siret'],0);
		}
		$this->setLeftMargin(15);
		$this->ln(5);
		$this->setfont('arial','B',10);
		$this->multicell(0,6,$this->affaire['nature']=="vente"?"L'ACHETEUR":"LE LOCATAIRE",0,'C');
		$this->setLeftMargin(65);
		$this->setfont('arial','B',7);
		$this->multicell(0,3,"Raison sociale : ".$this->client['societe'],0);
		$this->multicell(0,3,"Adresse : ".$this->client['adresse'],0);
		$this->multicell(0,3,"Code Postal : ".$this->client['cp']." Ville : ".$this->client['ville'],0);
			
		if(ATF::$codename == "cleodisbe"){
			$this->multicell(0,3,"NUMERO DE TVA : ".$this->client['reference_tva']." Tél : ".$this->client['tel'],0);
		}else{
			if($this->client['id_pays'] =='FR'){
				$this->multicell(0,3,"SIRET : ".$this->client['siret']." Tél : ".$this->client['tel'],0);
			}else{
				$this->multicell(0,3,"NUMERO DE TVA : ".($this->client['siret']?$this->client['siret']:"-")." Tél : ".$this->client['tel'],0);
			}
		}
		
		
		$this->SetLineWidth(0.35);
		$this->SetDrawColor(64,192,0);
		$this->line(0,60,220,60);
		$this->setLeftMargin(15);
		$this->setfont('arial','B',10);
		$this->setY(62);

		if($this->affaire["nature"]=="avenant"){
			if($this->devis["type_contrat"] == "presta"){ $this->multicell(0,3,"AVENANT N°".ATF::affaire()->num_avenant($this->affaire["ref"])." AU CONTRAT DE PRESTATION N°".ATF::affaire()->select($this->affaire["id_parent"],"ref").($this->client["code_client"]?"-".$this->client["code_client"]:NULL)); 
			}else{ 	$this->multicell(0,3,"AVENANT N°".ATF::affaire()->num_avenant($this->affaire["ref"])." AU CONTRAT DE ".($this->affaire['nature']=="vente"?"VENTE":"LOCATION")." N°".ATF::affaire()->select($this->affaire["id_parent"],"ref").($this->client["code_client"]?"-".$this->client["code_client"]:NULL)); }
			$this->ln(5);
		}else{
			if($this->devis["type_contrat"] == "presta"){
				$this->multicell(0,3,"CONDITIONS PARTICULIERES du Contrat de PRESTATION n° : ".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL));
			}else{
				$this->multicell(0,3,"CONDITIONS PARTICULIERES du Contrat de ".($this->affaire['nature']=="vente"?"vente":"location")." n° : ".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL));
			
			}
			if($this->lignes && $this->affaire["nature"]=="AR"){
				foreach($this->AR as $k=>$i){
					$affaire=ATF::affaire()->select($i['id_affaire']);
					$ref_ar .=" ".$affaire["ref"]." (".$affaire["affaire"]."), ";
				}
				$this->ln(2);
				$this->setfont('arial','BI',7.5);				
				$this->multicell(0,2,"Annule et remplace le(s) contrat(s) n° ".$ref_ar." (cf. tableau descriptif des équipements) et reprend tout ou partie des matériels ainsi que tous leurs encours.",0,'L');
			} else {
				$this->ln(5);
			}
		}
		
		$this->SetLineWidth(0.35);
		$this->SetDrawColor(64,192,0);
		$this->line(0,73,220,73);

		$this->setxy(15,75);
		$this->SetDrawColor(0,0,0);
		$this->SetLineWidth(0.2);

		if($this->affaire["nature"]=="avenant"){
			$titre = "ARTICLE 1 : OBJET DE L'AVENANT";
			$texte = "L'objet de cet avenant concerne l'ajout et le retrait d'équipements au contrat de base cité en référence.";
		}else{
			$titre = "ARTICLE 1 : OBJET DU CONTRAT";
			//$texte = "L'objet du contrat est la ".($this->affaire['nature']=="vente"?"vente":"mise en location")." d'équipements dont le détail figure ci-après. ";
			
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
			
		$w = array(20,30,30,105);
		
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
					//Ligne 1 "type","processeur","puissance" OU Infos UC ,  j'avoue que je capte pas bien
					

					$etat = "( NEUF )";
					if($i_["id_affaire_provenance"] || $i_["neuf"]== "non" ){
						if($i_["neuf"] == "non"){
								$etat = "( OCCASION )";
						}
					}

					if(ATF::$codename == "cleodisbe"){ $etat = ""; }
					
					//Si c'est une prestation, on affiche pas l'etat
					if($produit["type"] == "sans_objet" || ($produit['id_sous_categorie'] == 16) || ($produit['id_sous_categorie'] == 114)) {	$etat = "";		}	
					
					if ($details == "") unset($details);
					$data[] = array(
						round($i_['quantite'])
						,$ssCat
						,$fab
						,$i_['produit'].$etat
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
//					if (count($i['data'])<5 && count($tableau)==1) {
//						for ($c = count($i['data']); $c<6; $c++) {
//							$i['data'][] = array("","","","");
//							$i['styles'][] = array(
//								($details?$this->colsProduitAvecDetailFirst:$this->colsProduitFirst)
//								,($details?$this->colsProduitAvecDetail:$this->colsProduit)
//								,($details?$this->colsProduitAvecDetail:$this->colsProduit)
//								,($details?$this->colsProduitAvecDetailLast:$this->colsProduitLast)
//								,"details"=>$this->styleDetailsProduit
//							);
//						}
//					}
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
				else{ $this->multicell(0,3,"La durée de la location est identique à celle du contrat principal."); }
				
			}elseif($this->affaire["nature"]=="avenant"){
				if($this->devis["type_contrat"] == "presta"){	$texte = "La durée est fixée à ".$duree." mois"." à compter du "; }
				else{ $texte = "La durée de la location est fixée à ".$duree." mois"." à compter du "; }
				if($this->commande['date_debut']){
					$texte .= date("d/m/Y",strtotime($this->commande['date_debut'])).".";
				}
				$this->multicell(0,3,$texte);
			}else{
				if($this->devis["type_contrat"] == "presta"){ $this->multicell(0,3,"La durée est fixée à ".$duree." mois."); }
				else{ $this->multicell(0,3,"La durée de la location est fixée à ".$duree." mois."); }
				
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
						$data[] = array(
							$i['duree']
							,strtoupper($i['frequence_loyer'])
							,number_format($i["loyer"]+$i["frais_de_gestion"]+$i["assurance"],2,"."," ")." €"
							,number_format((($i['loyer']+$i["frais_de_gestion"]+$i["assurance"])*$this->commande["tva"]),2,"."," ")." €"
						);	
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

		$cadre = array(
			"Fait à : "
			,"Le : "
			,"Nom : "
			,"Qualité : "
			,array("txt"=>"Signature et cachet commercial : ","fill"=>1,"w"=>$this->GetStringWidth("Signature et cachet commercial : ")+10,"bgColor"=>"ffff00")
		);
		$y = $this->gety()+2;
		if ($this->affaire['nature']=="vente") {
			$t = "L'acheteur";
		} else {
			$t = "Le Locataire";
		}
		$this->cadre(20,$y,80,48,$cadre,$t);
		$cadre = array(
			"Fait à : "
			,"Le : "
			,"Nom : "
			,"Qualité : "
			,"Signature et cachet commercial : "
		);
		if ($this->affaire['nature']=="vente") {
			$t = "Le Vendeur";
		} else {
			$t = "Le Loueur";
		}
		$this->cadre(110,$y,80,48,$cadre,$t);
		
		$this->Annot(110,$y,"SignatureDebtor");
		
		$this->setfont('arial','B',9);
		$this->setY(275.9);
		$this->multicell(0,1,"POUR ACCEPTATION DES CONDITIONS GENERALES AU VERSO",0,'C');
		if($this->devis["type_contrat"] == "presta"){	}
		else{ $this->conditionsGeneralesDeLocationA4($this->affaire['nature']); }
		
		if ($annexes) {
			$this->annexes($annexes);
		}
	}

	/** Génère un Procès verbal
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 11-02-2011
	*/
	public function contratPV($id,$s,$previsu) {
		$this->commandeInit($id,$s,$previsu);
		$this->unsetHeader();
		$this->Open();		
		$this->AddPage();

		$this->image(__PDF_PATH__."/cleodis/logo.jpg",4,5,35);

		$this->setfont('arial','I',8);
		//Cadre du haut avec Loueur et Locataire
		$this->sety(5);
		$this->setleftMargin(40);
		$this->cell(30,12,"LE LOUEUR",1,0,'C');
		$this->multicell(
			0,4,
			$this->societe['societe']." - ".$this->societe['adresse']." – ".$this->societe['cp']." ".$this->societe['ville']." - Tél : ".$this->societe['tel']." – Fax : ".$this->societe['fax']."\n".
			($this->societe['id_pays']=='FR'?$this->societe['structure']." AU CAPITAL DE ".number_format($this->societe["capital"],2,'.',' ')." € - RCS LILLE B ".$this->societe['siren']." – APE ".$this->societe['naf']."\n":"\n").
			($this->societe['id_pays']=='FR'?"N° de TVA intracommunautaire : FR 91 ".$this->societe["siren"]."\n":$this->societe['structure']."NUMERO DE TVA ".$this->societe['siret']."\n")
			,1
		);
		$this->cell(30,12,"LE LOCATAIRE",1,0,'C');
		if(ATF::$codename == "cleodisbe"){
			$this->multicell(
				0,4,
				$this->client['societe']." - ".($this->client["tel"]?"Tél : ".$this->client["tel"]:"")." – ".($this->client["fax"]?"Fax : ".$this->client["fax"]:"")."\n".
				$this->client["adresse"]." - ".$this->client["cp"]." ".$this->client["ville"]."\n".
				($this->client['id_pays']=='FR'?"SIREN : ".$this->client['siren'].($this->client['structure']?" - ".$this->client['structure']:"").($this->client['capital']?" au capital de ".number_format($this->client["capital"],2,'.',' ')." €":"")."\n":"NUMERO DE TVA : ".$this->client['reference_tva'].($this->client['structure']?" - ".$this->client['structure']:"").($this->client['capital']?" au capital de ".number_format($this->client["capital"],2,'.',' ')." €":"")."\n")
				,1
			);
		}else{

			$this->multicell(
				0,4,
				$this->client['societe']." - ".($this->client["tel"]?"Tél : ".$this->client["tel"]:"")." – ".($this->client["fax"]?"Fax : ".$this->client["fax"]:"")."\n".
				$this->client["adresse"]." - ".$this->client["cp"]." ".$this->client["ville"]."\n".
				($this->client['id_pays']=='FR'?"SIREN : ".$this->client['siren'].($this->client['structure']?" - ".$this->client['structure']:"").($this->client['capital']?" au capital de ".number_format($this->client["capital"],2,'.',' ')." €":"")."\n":"NUMERO DE TVA : ".$this->client['siret'].($this->client['structure']?" - ".$this->client['structure']:"").($this->client['capital']?" au capital de ".number_format($this->client["capital"],2,'.',' ')." €":"")."\n")
				,1
			);
		}
		
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"PROCES-VERBAL DE LIVRAISON AVEC CESSION DU MATERIEL ET DU CONTRAT DE LOCATION N°".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:""),1,'C');
		$this->setleftMargin(15);
		
		$this->ln(5);
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"IL EST EXPOSE ET CONVENU CE QUI SUIT :");
		$this->setfont('arial','',8);
		$this->multicell(0,5,"Suivant le contrat sous-seing privé N° ".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL)." en date du _____________________, le Loueur a loué au Locataire ci-dessus désigné les équipements suivants :");

		$this->setfont('arial','',8);
		//$this->ln(5);
		if ($this->lignes) {
			$w = array(20,35,35,95);
			
			$lignesVides = 15-count($this->lignes);
			
			$lignes=$this->groupByAffaire($this->lignes);
			
//			if ($lignesVides>1) $lignesVides = round($lignesVides/count($lignes));
//			else $lignesVides = false;
			
			$this->setFillColor(255,255,255);
	
			$head = array("Quantité","Type","Marque","Désignation");
			foreach ($lignes as $k => $i) {
				$this->setfont('arial','B',10);
				if (!$k) {
					$title = "NOUVEAU(X) EQUIPEMENT(S)";
				} else {
					$affaire_provenance=ATF::affaire()->select($k);
					if($this->affaire["nature"]=="avenant"){
						$title = "EQUIPEMENT(S) RETIRE(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}elseif($this->affaire["nature"]=="AR"){
						$title = "EQUIPEMENT(S) PROVENANT(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}
				}
				$this->setfont('arial','',8);
							
				
				unset($data);
				foreach ($i as $k_ => $i_) {					
					$etat = "( NEUF )";
					if($i_["id_affaire_provenance"] || $i_["neuf"]== "non" ){
						if($i_["neuf"] == "non"){
								$etat = "( OCCASION )";
						}
					}
					$produit = ATF::produit()->select($i_['id_produit']);
					//Si c'est une prestation, on affiche pas l'etat
					if($produit["type"] == "sans_objet" || ($produit['id_sous_categorie'] == 16) || ($produit['id_sous_categorie'] == 114)){	$etat = "";		}
					
					if(ATF::$codename == "cleodisbe"){ $etat = ""; }
					
					$data[] = array(
						round($i_['quantite'])
						,ATF::sous_categorie()->nom($produit['id_sous_categorie'])
						,ATF::fabriquant()->nom($produit['id_fabriquant'])
						,$i_['produit'].$etat
					);											
				}
				
//
//				if($lignesVides) {
//					for ($c=0; $c<$lignesVides; $c++) {
//						$data[] = array("","","","");	
//					}
//				}
				$tableau[$k] = array(
					"head"=>$head
					,"data"=>$data
					,"w"=>$w
					,"styles"=>$st
					,"title"=>$title
				);				
//				$this->tableau($head,$data,$w,4);
			}
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
				if ($h>$this->heightLimitTableContratPV) {
					$this->multicellAnnexe();
					$annexes[$k] = $i;
				} else {
					$this->tableau($i['head'],$i['data'],$i['w'],5,$i['styles']);
				}
			}
		

		}
				
		$this->ln(3);
		$this->setfont('arial','B',9);
		$this->multicell(0,5,"LIVRAISON :");
		$this->setfont('arial','',8);
		$this->multicell(0,4,"La livraison est à ce jour complète et définitivement acceptée par Le Locataire sans restriction ni réserve. Le Locataire reconnaît que : ");
		$this->multicell(0,4,"=> le matériel est bien installé, mis en ordre de marche, qu'il est réglementaire, conforme notamment aux lois, règlements, prescriptions administratives, normes françaises et qu'il est muni de tous les justificatifs nécessaires notamment l'Attestation de Conformité sur la sécurité et l'hygiène des travailleurs.");
		$this->multicell(0,4,"=> les logiciels décrits dans les annexes du contrat lui ont été entièrement livrés et apparaissent parfaitement conformes aux spécifications des fournisseurs, que l'ensemble de la documentation relative à ces logiciels lui a été remise, que la formation de son personnel relative à ces logiciels a été correctement effectuée ou planifiée, que les licences et/ou les modules de déploiement ont été recettés selon des modalités directement convenues avec les éditeurs des licences et le cas échéant les prestataires assurant leurs déploiements et qu'ainsi leur réception définitive a été prononcée à la date de signature de la présente, que rien ne s'oppose à la cession des droits d'exploitations liés aux logiciels et, le cas échéant, liés à leur développement qui est facturé par le(s) fournisseurs au Loueur.");
		$this->multicell(0,4,"=> Qu'en conséquence la location est devenue effective en totale conformité avec le Contrat de location.");

		$this->ln(5);
		$this->setfont('arial','',8);
		$cadre = array(
			"Fait à : ______________________"
			,"Le : ______________________"
			,"Nom : ______________________"
			,"Qualité : ______________________"
		);

		$this->cadre(25,215,70,60,$cadre,"Locataire");
		$this->cadre(115,215,70,60,$cadre,"Loueur");
		$this->setxy(25,269);
		$this->setFillColor(255,255,0);
		$this->cell(10,5,"");
		$this->cell(50,5,"Signature et cachet du Locataire",0,0,'C',1);
		$this->cell(10,5,"",0,1);
		$this->setxy(115,270);
		$this->multicell(70,5,"Signature et cachet du Loueur",0,'C');
		
		$this->setautopagebreak(false,'1');
		$this->sety(280);
		$this->setfont('arial','B',8);
		$this->cell(40,4,"",0,0);
		$this->cell(100,4,"POUR ACCEPTATION DES CONDITIONS DE CESSION AU VERSO",1,0,'C');
		$this->cell(50,4,"",0,1);


		$this->addpage();
		$this->setfont('arial','B',12);
		$this->multicell(0,5,"CESSION DU MATERIEL ET DU CONTRAT DE LOCATION N°".$this->commande['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),0,'C');
		$this->ln(5); 
		$this->setfont('arial','B',9);
		$this->cell(100,5,"Nombre de loyers cédés : ________");
		$this->cell(60,5,"Le Cessionnaire : ",0,1);
		$this->setx(115);
		$this->cell(60,20,"",1,1);
		$this->ln(-5); 
		$this->multicell(0,5,"La présente cession prend effet le : __________");
		$this->ln(4); 
		$this->setfont('arial','',8);
		$this->multicell(0,5,"CESSION DU MATERIEL ET DELEGATION DU CONTRAT DE LOCATION : Par la présente, le Loueur déclare transférer au Cessionnaire, qui l'accepte, la propriété du matériel et céder les droits résultant du contrat de location conclu avec le Locataire, avec toutes les garanties de fait et de droit.");
		$this->ln(2);
		$this->multicell(0,5,"A la date de cession, le Loueur subroge le Cessionnaire dans tous les droits et actions qu'elle détient contre le Locataire en vertu dudit contrat.");
		$this->ln(2);
		$this->multicell(0,5,"Conformément aux Conditions Générales du contrat de location, le Locataire ayant pris connaissance de la cession y consent, sans restriction ni réserve. Le Locataire reconnaît donc comme Bailleur le Cessionnaire et s'engage notamment à lui verser directement ou à son ordre la totalité des loyers en principal, intérêts et accessoires prévus aux Conditions Particulières.");
		$this->ln(2);
		$this->multicell(0,5,"Le Locataire réitère les engagements et renonciation qu'il a pris au contrat de location, et déclare qu'elles resteront valables même si, pour une raison indépendante de sa volonté, le Loueur ne serait pas en mesure d'assurer ses obligations. ");
		$this->ln(2);
		$this->multicell(0,5,"Au titre de cet article, le Locataire a renoncé notamment à effectuer toute compensation, déduction, demande reconventionnelle en raison des droits qu'il pourrait faire valoir contre le Loueur, à tous recours contre le Cessionnaire du fait de la construction, la livraison, l'installation et les assurances mais conserve sur ce point tous ces recours contre le Loueur.");
		$this->ln(2);
		$this->multicell(0,5,"A ce tire, pendant toute la durée du contrat de location, le Locataire exercera, en vertu d'une stipulation pour autrui expresse, tous ses droits et action en garantie vis-à-vis du Loueur en sa qualité de fournisseur et plus généralement à l'encontre de tout constructeur ou  fournisseur du bien loué. ");
		$this->ln(2);
		$this->multicell(0,5,"Dans une telle éventualité, le locataire restera tenu d'exécuter toutes les obligations contractuelles pendant toute la durée de la procédure. Si cette action aboutie à une résolution judiciaire de la vente, objet du contrat de location, celui-ci sera résilié à compter du jour où cette résolution sera devenue définitive et le Bailleur pourra réclamer une indemnité qui ne pourra être inférieure au montant total des coûts de revient du bien pour le Bailleur, déduction faite des loyers déjà payés. Le Locataire reste garant solidaire du Loueur, du fournisseur pour toutes les sommes que ceux ci devraient au Cessionnaire. ");
		$this->ln(2);
		$this->multicell(0,5,"Le Loueur et le Locataire déclarent, sous leur responsabilité, que le contrat de location et ses annexes ou avenants sus visés, ci-annexés, forment l'intégralité de leur convention pour la location du matériel cédé, et sont indépendants de toute autre convention conclue entre eux.");
		$this->ln(2);
		$this->multicell(0,5,"En conséquence, toute autre convention ou document quelconque qui empêcherait l'application d'une des clauses dudit contrat sera inopposable au Cessionnaire.");
		$this->ln(2);
		$this->multicell(0,5,"Le contrat de location objet de la présente cession est formé des originaux joints au présent acte, à savoir :");
		$this->setx(40);
		$this->multicell(0,5,"- 1 pages de Conditions Générales,");
		$this->setx(40);
		$this->multicell(0,5,"- 1 page de Conditions Particulières");
		$this->ln(2);
		
		$this->setfillColor(211,211,211);
		$this->multicell(0,4,"PARTIE RESERVEE A CLEODIS","TB","C",1);
		
		$this->Rotate(14);
		$this->setxy(15,255);
		$this->setLineWidth(0.5);
		$this->setfont('arial',"B",18);
		$this->setDrawColor(211,211,211);
		$this->setTextColor(211,211,211);
		$this->multicell(165,10,"PARTIE RESERVEE A CLEODIS","TB","C");
		$this->setTextColor("black");
		$this->setfont('arial',"",8);
		$this->Rotate(0);
		
		$this->cadre(25,215,70,50,$cadre,"Loueur");
		$this->cadre(115,215,70,50,$cadre,"Cessionnaire");
		$this->setxy(25,259);
		$this->cell(10,5,"");
		$this->cell(50,5,"Signature et cachet",0,0,'C');
		$this->cell(10,5,"",0,1);
		$this->setxy(125,259);
		$this->multicell(50,5,"Signature et cachet",0,'C');
		$this->ln(2);
		$this->setfillColor(211,211,211);
		$this->multicell(0,4,"PARTIE RESERVEE A CLEODIS","TB","C",1);
		

		if ($annexes) {
			$this->setLineWidth(0.2);
			$this->setFillColor(239,239,239);
			$this->setDrawColor(0,0,0);
			$this->annexes($annexes);
			$this->unsetHeader();
			$this->settopmargin(10);
		}
		return true;		
	}
	
	/** Génère une autorisation de prélèvement
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 16-02-2011
	*/
	public function contratAP($id,$s) {
		$this->Open();
		$this->unsetHeader();
		$this->commandeInit($id,$s);

		//print_r($infos_refi);
		$this->AddPage();
		$this->SetLeftMargin(15);
		//1er cadre
		$this->setxy(15,20);
		
		$this->setfont('arial','B',15);
		$this->setFillColor(239,239,239);
		$this->multicell(0,5,'AUTORISATION DE PRELEVEMENT',1,'C',1);

		$this->setfont('arial','',8);
		$this->setxy(15,30);
		$this->multicell(0,5,"Je soussigné, autorise l’établissement teneur de mon compte à prélever sur ce dernier, si la situation le permet, tous les prélèvements ordonnés par le créancier désigné ci-dessous. Je reconnais que l’établissement teneur du compte à débiter ne sera pas tenu de m’aviser de l’exécution de ces opérations. En cas de litige sur un prélèvement, je pourrai en faire suspendre l’exécution sur simple demande à l’établissement teneur de mon compte. Je règlerai le différend directement avec le créancier.",0,'L');
		
		$this->setxy(35,55);
		$this->setfont('arial','B',10);
		$this->cell(100,5,"N° NATIONAL D’EMETTEUR : ",0,0,'L');
		$this->cell(0,5,"492 537",0,1,'L');
		
		$this->setxy(15,60);
		$this->setfont('arial','B',8);
		$this->cell(85,5,"NOM, PRENOMS ET ADRESSE DU DEBITEUR",0,0,'C');
		$this->cell(15,5,"",0,0);
		$this->cell(85,5,"NOM ET ADRESSE DU CREANCIER",0,1,'C');
		
		$this->cell(85,25,"",1,0);
		$this->cell(15,5,"",0,0);
		$this->cell(85,25,"",1,1);
		
		$filiale=ATF::societe()->select($this->affaire['id_filiale']);
		
		$this->sety(70);
		$this->cell(100,5,"",0,0);
		$this->cell(85,5,$filiale['societe'],0,1,'C');
		$this->cell(100,5,"",0,0);
		$this->cell(85,5,$filiale['adresse'],0,1,'C');
		$this->cell(100,5,"",0,0);
		$this->cell(85,5,$filiale['cp']." ".$filiale['ville'],0,1,'C');

		
		$this->setxy(110,90);
		$this->cell(85,5,"AU NOM ET ADRESSE POSTALE DE",0,1,'C');
		$this->cell(85,5,"COMPTE A DEBITER",0,0,'C');
		$this->cell(15,5,"",0,0);
		$this->cell(85,5,"L’ETABLISSEMENT TENEUR DU COMPTE A DEBITER",0,1,'C');
		
		$this->cell(85,20,"",1,0);
		$this->cell(15,5,"",0,0);
		$this->cell(85,25,"",1,1);
		
		$this->setxy(15,110);
		$this->setfont('arial','BU',8);
		$this->cell(25,5,"Etablissement",0,0,'L');
		$this->cell(20,5,"Guichet",0,0,'L');
		$this->cell(25,5,"N° de compte",0,0,'L');
		$this->cell(15,5,"Clé",0,1,'R');
		$this->setxy(15,120);
		$this->cell(45,5,"Date : ",0,0,'L');
		$this->setFillColor(255,255,0);
		$this->setxy(60,120.5);
		$this->cell(27,4,"Signature + cachet",0,1,'L',1);
				
		//2eme cadre
		$this->setxy(15,145);
		
		$this->setfont('arial','B',15);
		$this->setFillColor(239,239,239);
		$this->multicell(0,5,'AUTORISATION DE PRELEVEMENT',1,'C',1);
		
		$this->setfont('arial','',8);
		$this->setxy(15,155);
		$this->multicell(0,5,"Je soussigné, autorise l’établissement teneur de mon compte à prélever sur ce dernier, si la situation le permet, tous les prélèvements ordonnés par le créancier désigné ci-dessous. Je reconnais que l’établissement teneur du compte à débiter ne sera pas tenu de m’aviser de l’exécution de ces opérations. En cas de litige sur un prélèvement, je pourrai en faire suspendre l’exécution sur simple demande à l’établissement teneur de mon compte. Je règlerai le différend directement avec le créancier.",0,'L');
		
		$this->setxy(35,180);
		$this->setfont('arial','B',10);
		$this->cell(100,5,"N° NATIONAL D’EMETTEUR : ",0,0,'L');
		$this->cell(0,5,$this->refinanceur['numero_emetteur'],0,1,'L');
		
		$this->setxy(15,185);
		$this->setfont('arial','B',8);
		$this->cell(85,5,"NOM, PRENOMS ET ADRESSE DU DEBITEUR",0,0,'C');
		$this->cell(15,5,"",0,0);
		$this->cell(85,5,"NOM ET ADRESSE DU CREANCIER",0,1,'C');
		
		$this->cell(85,25,"",1,0);
		$this->cell(15,5,"",0,0);
		$this->cell(85,25,"",1,1);
		
		$this->sety(190);
		$this->cell(100,5,"",0,0);
		$this->cell(85,5,$this->refinanceur['refinanceur'],0,1,'C');
		$this->cell(100,5,"",0,0);
		$this->cell(85,5,$this->refinanceur['adresse'],0,1,'C');
		$this->cell(100,5,"",0,0);
		$this->cell(85,5,$this->refinanceur['adresse_2'],0,1,'C');
		$this->cell(100,5,"",0,0);
		$this->cell(85,5,$this->refinanceur['cp']." ".$this->refinanceur['ville'],0,1,'C');
		
		$this->setxy(110,215);
		$this->cell(85,5,"AU NOM ET ADRESSE POSTALE DE",0,1,'C');
		$this->cell(85,5,"COMPTE A DEBITER",0,0,'C');
		$this->cell(15,5,"",0,0);
		$this->cell(85,5,"L’ETABLISSEMENT TENEUR DU COMPTE A DEBITER",0,1,'C');
		
		$this->cell(85,20,"",1,0);
		$this->cell(15,5,"",0,0);
		$this->cell(85,25,"",1,1);
		
		$this->setxy(15,225);
		$this->setfont('arial','BU',8);
		$this->cell(25,5,"Etablissement",0,0,'L');
		$this->cell(20,5,"Guichet",0,0,'L');
		$this->cell(25,5,"N° de compte",0,0,'L');
		$this->cell(15,5,"Clé",0,1,'R');
		$this->setxy(15,245);
		$this->cell(45,5,"Date : ",0,0,'L');
		$this->setxy(60,245.5);
		$this->setFillColor(255,255,0);
		$this->cell(27,4,"Signature + cachet",0,1,'L',1);
		
		$this->sety(266);
		$this->setfont('arial','BI',8);
		$this->multicell(0,5,"Prière de joindre un Relevé d’Identité Bancaire");
		$this->setfont('arial','I',7);
		$this->multicell(0,5,"Contrat n°".$this->commande['ref']);
		
	}
	
	/** Génère les annexes des PDF
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 16-02-2011
	*/
	public function annexes($tableau) {
		if (!$tableau) return false;
		$this->setHeader();
		$this->setTopMargin(30);
		$this->addpage();
		$this->setfont('arial','B',18);
		$this->multicell(0,5,"ANNEXES DE DESCRIPTION DES EQUIPEMENTS",0,'C');
		$this->ln(5);
		foreach ($tableau as $k=>$i) {
			$this->setFillColor(239,239,239);
			if ($i['title']) {
				$this->setfont('arial','B',10);
				$this->multicell($this->A3?190:0,5,$i['title'],1,'C',1);	
				$this->setfont('arial','',8);
			}
			$this->tableau($i['head']?$i['head']:false,$i['data'],$i['w']?$i['w']:180,$i['h']?$i['h']:5,$i['styles']?$i['styles']:false);	
		}
	}
	/** Génère le multicell qui indique que le tableau est en annexe
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 17-02-2011
	*/
	public function multicellAnnexe() {
		$this->setfont('arial','BU',8);
		$this->multicell(0,5,"Voir détails dans l'annexe de description des équipements",0,'C');
		$this->setfont('arial','',8);
	}

	/** Initialise les variables pour générer un BDC
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-02-2011
	* @param int $id Identifiant bon de commande
	*/
	private function initBDC($id,$s) {
		$this->bdc = ATF::bon_de_commande()->select($id);
		ATF::bon_de_commande_ligne()->q->reset()->where("id_bon_de_commande",ATF::bon_de_commande_ligne()->decryptID($id));
		$this->lignes = ATF::bon_de_commande_ligne()->sa();

		$this->client = ATF::societe()->select($this->bdc['id_societe']);
		$this->user = ATF::user()->select($this->bdc['id_user']);
		$this->affaire = ATF::affaire()->select($this->bdc['id_affaire']);
		$this->societe = ATF::societe()->select($this->affaire['id_filiale']);
		$this->fournisseur = ATF::societe()->select($this->bdc['id_fournisseur']);
		$this->bpa = ATF::affaire()->getCommande($this->affaire['id_affaire'])->infos;
				
		// Les styles utiles :
		$this->styleLibAffaire = array(
			array("border"=>"LBT","bgcolor"=>"efefef","decoration"=>"B")
			,array("border"=>"TB","bgcolor"=>"efefef","decoration"=>"B")
			,array("border"=>"TRB","bgcolor"=>"efefef","decoration"=>"B")
		);
		$this->styleLibTotaux = array("decoration"=>"B","align"=>"R");
		$this->styleTotaux = array("decoration"=>"B");
		$this->styleNotice = array("decoration"=>"I","size"=>6);
	}
	
	/** PDF d'un bon de commande
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-02-2011
	* @param int $id Identifiant bon de commande
	*/
	public function bon_de_commande($id,$s) {
		
		$this->initBDC($id,$s,$previsu);
		$this->unsetHeader();
		$this->open();
		$this->addpage();
		
		
		$this->image(__PDF_PATH__."/cleodis/logo.jpg",80,20,40);
		
		$this->setfont('arial','',10);
		
		$this->sety(10);
		/*$this->multicell(0,5,$this->societe['societe']);
		if ($this->societe['adresse']) $this->multicell(0,5,$this->societe['adresse']);
		if ($this->societe['adresse_2']) $this->multicell(0,5,$this->societe['adresse_2']);
		if ($this->societe['adresse_3']) $this->multicell(0,5,$this->societe['adresse_3']);
		$this->multicell(0,5,$this->societe['cp']." ".$this->societe['ville']);*/
		
		//CADRE REFERENCE
		$cadre = array($this->bdc["ref"]);
		$this->cadre(10,10,60,15,$cadre,"REFERENCES A RAPPELER");
		//CADRE COMMANDE LE
		$cadre = array(
			$this->user["prenom"]." ".$this->user["nom"]
			,"Téléphone : ".$this->societe['tel']
			,"Fax : ".$this->societe['fax']
			,$this->user["email"]
		);

		$this->cadre(10,28,60,25,$cadre,"Commandé le : ".date("d M Y",strtotime($this->bdc['date'])));
		
		
		//CADRE DELAIS SOUHAITES
		if($this->bdc['date_livraison_demande'] || $this->bdc['date_installation_demande']){
			$cadre = array(
				 "Livraison souhaitée ".date("d/m/Y",strtotime($this->bdc['date_livraison_demande']))
				,"Installation souhaitée ".date("d/m/Y",strtotime($this->bdc['date_installation_demande']))
				,"RAV ".date("d/m/Y",strtotime($this->affaire['date_ouverture']))
			);
			$this->cadre(10,60,60,20,$cadre,"Délais souhaités : ");	
		}
		
		
		//CADRE A L'ATTENTION DE
		$cadre = array(
			 ATF::contact()->nom($this->fournisseur["id_contact_facturation"])
			,$this->fournisseur['societe']
			,$this->fournisseur['adresse']
			,$this->fournisseur['adresse_2']?$this->fournisseur['adresse_2']:""
			,$this->fournisseur['adresse_3']?$this->fournisseur['adresse_3']:""
			,$this->fournisseur['cp']." ".$this->fournisseur['ville']
		);
		$this->cadre(130,10,70,30,$cadre,"A l'attention de");
		//CADRE ADRESSE DE LIVRAISON
		$cadre = array(
			$this->bdc['destinataire']
			,$this->bdc['adresse']
			,$this->bdc['adresse_2']?$this->bdc['adresse_2']:""
			,$this->bdc['adresse_3']?$this->bdc['adresse_3']:""
			,$this->bdc['cp']." ".$this->bdc['ville']
		);
		$this->cadre(130,42,70,35,$cadre,"Adresse de livraison finale");


		//CADRE Prestataire intermédiaire
		if($this->bdc["livraison_destinataire"] || $this->bdc['livraison_adresse']){
			$cadre = array(
				$this->bdc['livraison_destinataire']
				,$this->bdc['livraison_adresse']
				,$this->bdc['livraison_cp']." ".$this->bdc['livraison_ville']
			);
			$this->cadre(130,80,70,30,$cadre,"Livraison prestataire intermédiaire");
		}


		$this->setFont('arial','IB',6);
		$this->setleftmargin(10);
		$this->setrightmargin(5);
		$this->multicell(0,5,"Toute clause de réserve de propriété insérée par nos fournisseurs dans leur documents (conditions générales de ventes, factures, etc...) sera réputée non écrite");
		
		$this->ln(5);
		$this->setfont('arial','BI',12);
		$this->multicell(0,10,"BON DE COMMANDE ".$this->bdc["bon_de_commande"],0,'C');
		$this->setfont('arial','',10);
		
		if ($this->lignes) {
			$head = array("Référence","Désignation","Quantité","P.U","Total");
			$w = array(30,100,20,20,20);
			foreach($this->lignes as $k=>$i) {
				if ($i['id_produit']) $produit = ATF::produit()->select($i['id_produit']);
				$data[] = array(
					$i['ref']
					,$produit['produit']?$produit['produit']:$i['produit']
					,round($i['quantite'])
					,number_format($i['prix'],2,'.',' ')
					,number_format($i['quantite']*$i['prix'],2,'.',' ')
				); 
				$styles[] = array();
				$total += $i['quantite']*$i['prix'];
			}
			
			$h = 20 + $this->getHeightTableau($head,$data,$w,5,$styles); 
			
			if ($h>$this->heightLimitTableBDC) {
				$this->multicellAnnexe();
				$annexes[] = array(
					"head"=>$head
					,"data"=>$data
					,"w"=>$w
					,"styles"=>$styles
				);
			} else {
				$this->tableauBigHead($head,$data,$w,5,$styles);
			}
			$totalTable = array(
				"data"=>array(
								array("TOTAL HT",number_format($this->bdc["prix"],2,"."," ")." €")
								,array("TVA (".(($this->bdc['tva']-1)*100)."%)",number_format(($this->bdc["prix"]*($this->bdc['tva']-1)),2,"."," ")." €")
								,array("TOTAL TTC",number_format(($this->bdc["prix"]*$this->bdc['tva']),2,"."," ")." €")
							)
				,"styles"=>array(
									array($this->styleLibTotaux,$this->styleTotaux)
									,array($this->styleLibTotaux,$this->styleTotaux)
									,array($this->styleLibTotaux,$this->styleTotaux)
								)
				,"w"=>array(170,20)
			);
			if (!$annexes) {
				$this->tableau(false,$totalTable['data'],$totalTable['w'],5,$totalTable['styles']);
			}
		}
		if($this->bdc['commentaire']){			
			$this->Ln(5);
			$this->multicell(190,5,"Commentaire : ".$this->bdc['commentaire'],1,"L");
		}		
		
		$this->sety(200);		
		
		$this->setx(65);
		$this->cell(0,5,"SIGNATAIRES",1,1,'C');
		
		$head = array("Adresse de facturation","VISA EMETTEUR","VISA ACCUSE DE RECEPTION");
		$w = array(55,65,75);
		$data1 = array(
			array(
				$this->societe['societe']."\n"
				.($this->societe['facturation_adresse']?$this->societe['facturation_adresse']:$this->societe['adresse'])."\n"
				.($this->societe['facturation_adresse_2']?$this->societe['facturation_adresse_2']:$this->societe['adresse_2'])."\n"
				.($this->societe['facturation_adresse_3']?$this->societe['facturation_adresse_3']:$this->societe['adresse_3'])."\n"
				.($this->societe['facturation_cp']?$this->societe['facturation_cp']:$this->societe['cp'])." ".($this->societe['facturation_ville']?$this->societe['facturation_ville']:$this->societe['ville'])."\n"
				,"",""
			)
		);
		/*
		if($this->user["id_user"]==$this->idUserPierreCaminel){
			//$this->image("images/signature/Pierre_Caminel.PNG",$this->getx()+2,$this->gety()+2,45);
		} elseif($this->user["id_user"]==$this->idUserJeromeLoison) {
			//$this->image("images/signature/Jerome_Loison.PNG",$this->getx()+2,$this->gety()+2,40);
		}
		*/
		$data2 = array(
			array(
				"Facture à établir en 2 exemplaires.\n Joindre 1 exemplaire de commande à la facture"
				,$this->societe['societe']
				,$this->fournisseur['societe']
			)
		);
		$styles = array();
		$this->tableau($head,$data1,$w,25,$styles);
		$styles[0][0] = $this->styleNotice;
		$this->tableau(false,$data2,$w,5,$styles);
		
		$this->setfont('arial','',8);
		$this->multicell(0,5,"L'acceptation de vos factures est subordonnée à :");
		$this->multicell(0,5,"N° de commande & identification du client");
		$this->multicell(0,5,"· La signature par notre client du procès verbal de livraison ne comportant aucune réserve");
		$this->multicell(0,5,"· L'indication par vos soins des n° de série des matériels sur les factures");
		$this->multicell(0,5,"· Description et détail des modèles, marque, type dispositifs, prix unitaire, matériels, logiciels et prestation objets.");
	
		if ($annexes) {
			$this->annexes($annexes);
			$this->tableau(false,$totalTable['data'],$totalTable['w'],5,$totalTable['styles']);
		}
	}
	
	/** Initialise les variables pour générer une demande de refi
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param int $id Identifiant demande de refi
	*/
	private function initDemandeRefi($id,$s) {
		$this->demandeRefi = ATF::demande_refi()->select($id);
		$this->affaire = ATF::affaire()->select($this->demandeRefi['id_affaire']);
		$this->contrat = ATF::affaire()->getCommande($this->affaire['id_affaire'])->infos;
		$this->refinanceur = ATF::refinanceur()->select($this->demandeRefi['id_refinanceur']);
		$this->client = ATF::societe()->select($this->affaire['id_societe']);
		$this->devis = ATF::affaire()->getDevis($this->affaire['id_affaire'])->infos;
		$this->ligneDevis = ATF::devis_ligne()->ss("id_devis",$this->devis['id_devis']);
		$this->user = ATF::user()->select($this->contrat['id_user']);
		$this->societe = ATF::societe()->select($this->user['id_societe']);
		$this->loyer = ATF::loyer()->ss('id_affaire',$this->devis['id_affaire']);

//		include_once __ABSOLUTE_PATH__.'libs/finance.class.php';
//		$f = new Financial;
//		foreach ($this->loyer as $k=>$i) {
//			$this->loyerActualise += -$f->PV($this->affaire["taux_refi_reel"]/($i['frequence'])/100, $i["duree"], $i["loyer"], 0, 1);
//			$this->totalLoyer += $i["duree"]*$i["loyer"];
//		}


	}
	
	/** PDF d'un comite
	* @author Moragn FLEURQUIN <mfleurquin@absystech.fr>
	* @date 20-04-2015
	* @param int $id Identifiant comite
	*/
	public function comite($id,$s) {
		$this->comite = ATF::comite()->select($id);
		$this->affaire = ATF::affaire()->select($this->comite['id_affaire']);
		ATF::devis()->q->reset()->where("id_affaire", $this->comite['id_affaire']);
		$this->devis = ATF::devis()->select_row();
		$this->client = ATF::societe()->select($this->affaire['id_societe']);


		$this->unsetHeader();
		
		$this->open();
		$this->addpage();
		$this->setleftmargin(10);
		$this->sety(10);

		$this->setfont('arial','BU',14);
		$this->cell(0,5,ATF::societe()->nom($this->client["id_societe"]),0,1,'C');
		$this->setfont('arial','',12);
		$this->cell(0,5,"SIREN : ".$this->client["siren"],0,1,'C');

		$this->ln(15);
		$this->setfont('arial','',12);
		$this->multicell(0,5,"ACTIVITE : ".$this->comite["activite"],0,'L');
		$this->ln(10);


		$this->setleftmargin(30);
		$y = $this->getY();
		$date = split("/", $this->comite["date_creation"]);

		if( (date("Y") - $date[1] ) >= 2 ){
			$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5); }
		$this->cell(0,5,"Ancienneté > à 2 ans ( ".$date[1]." )",0,1,"L");

		$this->ln(5);
		$y = $this->getY();
		if( floatval(str_replace(" ", "", $this->comite["capital_social"])) > 10000 ) { $this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5); }
		$this->cell(0,5,"Capital social > 10 000 € ( ".number_format(floatval($this->comite["capital_social"]),2,',',' ')." )",0,1,"L");
		
		$this->ln(5);
		if(!$this->comite["dettes_financieres"]) $this->comite["dettes_financieres"] = 0;

		$y = $this->getY();
		if( floatval(str_replace(" ", "", $this->comite["capitaux_propres"])) > floatval(str_replace(" ", "", $this->comite["dettes_financieres"])) ) {	$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5); }
		$this->cell(0,5,"Capitaux propres > Dettes financières ( ".number_format(floatval($this->comite["capitaux_propres"]),2,',',' ').">".number_format(floatval($this->comite["dettes_financieres"]),2,',',' ')." )",0,1,"L");


		$this->ln(5);
		$y = $this->getY();
		$investissement = 2*$this->devis["prix_achat"];
		if( floatval(str_replace(" ", "", $this->comite["capitaux_propres"])) > $investissement ) {	$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5); }
		$this->cell(0,5,"Capitaux propres > 2 x investissement ( ".number_format(floatval($this->comite["capitaux_propres"]),2,',',' ')." > ".$investissement." )",0,1,"L");


		$this->ln(5);
		$y = $this->getY();
		if( floatval(str_replace(" ", "", $this->comite["ca"])) > 150000 ) { $this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5);	}
		$this->cell(0,5,"CA > 150 000 € ( ".number_format(floatval($this->comite["ca"]),2,',',' ')." )",0,1,"L");

		$this->ln(5);
		$y = $this->getY();
		if( floatval(str_replace(" ", "", $this->comite["resultat_exploitation"])) > 0 ) {	$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5);	}
		$this->cell(0,5,"Résultat d'exploitation > 0 ( ".number_format(floatval($this->comite["resultat_exploitation"]),2,',',' ')." )",0,1,"L");

		$this->ln(5);
		$y = $this->getY();
		if( $this->comite["pourcentage_materiel"] > 50 ) {	$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5); }
		$this->cell(0,5,"% Matériel > 50 % ",0,1,"L");

		$this->ln(5);
		$y = $this->getY();
		if( $this->comite["note"] > 50 ) {	$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);
		}else{	$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5); }
		$this->cell(0,5,"Note CréditSafe > 50 ( ".$this->comite["note"]." )",0,1,"L");


		$this->ln(5);
		$y = $this->getY();
		ATF::demande_refi()->q->reset()->addCondition("etat","perdu","AND")
									   ->addCondition("id_societe",$this->comite["id_societe"],"AND")
									   ->addCondition("date",date("Y-m-d",strtotime(date("Y-m-d")." - 6 month")),"AND",false,">");
									   

		if(! ATF::demande_refi()->select_all() ) {	$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);			
		}else{		$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5);	}
		$this->cell(0,5,"Pas de refus refi récent ",0,1,"L");

		$this->ln(5);		
		$y = $this->getY();	

		/*if( ) {
			$this->image(__PDF_PATH__.'cleodis/check.png',20,$y,5);			
		}else{
			$this->image(__PDF_PATH__.'cleodis/uncheck.png',20,$y,5);			
		}*/
		$this->cell(0,5,"Maison mère",0,1,"L");


		$this->devis($this->devis["id_devis"]);


	}



	/** PDF d'une demande de refi
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 25-01-2011
	* @param int $id Identifiant demande de refi
	*/
	public function demande_refi($id,$s) {
		$this->initDemandeRefi($id,$s);
		
		$this->unsetHeader();
		
		$this->open();
		$this->addpage();
		$this->setleftmargin(15);
		$this->sety(10);

		$this->setfont('arial','B',12);
		$this->multicell(0,10,"DEMANDE DE REFINANCEMENT",1,'C');
		
		$this->ln(5);
		$this->setfont('arial','',8);
		$this->cell(50,5,"Date :",0,0,'L');
		$this->cell(0,5,date("d/m/Y",strtotime($this->demandeRefi["date"])),0,1);
		//$this->ln(5);
		$this->setfont('arial','I',8);
		$this->cell(50,5,"NOS REFERENCES :",0,0,'L');
		$this->setfont('arial','',8);
		$this->cell(0,5,$this->societe['societe']." - ".$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville'],0,1,'L');
		$this->cell(50,5,"",0,0,'L');
		$this->cell(0,5,"Tél : ".$this->societe['tel']." - Fax : ".$this->societe['fax'],0,1,'L');
		
		$this->setfont('arial','B',8); 
		$this->cell(50,5,"BAILLEUR :",0,0,'L');
		$this->setfont('arial','',8);
		$this->cell(0,5,$this->refinanceur['refinanceur']." - Fax : ".$this->refinanceur['fax'],0,1,'L');
		
		$this->setlinewidth(2);
		$this->setdrawcolor(192,192,192);
		$this->line(15,$this->gety()+5,200,$this->gety()+5);
		$this->ln(7);

		$this->setfont('arial','B',8);
		$this->cell(50,5,"LOCATAIRE :",0,0);

		if (ATF::refinanceur()->isKBC($this->refinanceur['id_refinanceur'])) {
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Raison sociale :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['societe'],0,1);
				
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"N° SIREN :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['siren'],0,1);
	
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Adresse :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['adresse'],0,1);
			if ($this->client['adresse_2']) {
				$this->cell(100,5,"",0,0);
				$this->cell(0,5,$this->client['adresse_2'],0,1);			
			}
			if ($this->client['adresse_3']) {
				$this->cell(100,5,"",0,0);
				$this->cell(0,5,$this->client['adresse_3'],0,1);
			}
			$this->cell(100,5,"",0,0);
			$this->cell(0,5,$this->client['cp']." ".$this->client['ville'],0,1);
			
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Activité:",0,0);
			$this->setfont('arial','',8);
			$this->multicell(0,5,$this->client['activite']);

			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Téléphone :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['tel'],0,1);

			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Nom du dirigeant :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,ATF::contact()->nom($this->demandeRefi['id_contact']),0,1);
			
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Date de création de la société :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['date_creation']?date("d/m/Y",strtotime($this->client['date_creation'])):"-",0,1);
			
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Date de naissance :",0,0);
			$this->setfont('arial','',8);
			$birthday = ATF::contact()->select($this->demandeRefi['id_contact'],"anniversaire");
			$this->cell(0,5,$birthday ? date("d/m/Y",strtotime($birthday)):"-",0,1);

			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(70,5,"Score :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,ATF::$usr->trans($this->client['score'], "societe_score"),0,1);

			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(70,5,"Avis de crédit :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,ATF::$usr->trans($this->client['avis_credit'], "societe_score") ,0,1);
			

		} else {
		
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Raison sociale :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['societe'],0,1);
				
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"N° SIREN :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['siren'],0,1);
	
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Code NAF :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['naf'],0,1);
			
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(50,5,"Adresse :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,$this->client['adresse'],0,1);
			if ($this->client['adresse_2']) {
				$this->cell(100,5,"",0,0);
				$this->cell(0,5,$this->client['adresse_2'],0,1);			
			}
			if ($this->client['adresse_3']) {
				$this->cell(100,5,"",0,0);
				$this->cell(0,5,$this->client['adresse_3'],0,1);
			}
			$this->cell(100,5,"",0,0);
			$this->cell(0,5,$this->client['cp']." ".$this->client['ville'],0,1);
			
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(70,5,"Activité du locataire ou de son groupe si holding :",0,0);
			$this->setfont('arial','',8);
			$this->multicell(0,4,$this->client['activite']);
	
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(70,5,"Nom du dirigeant :",0,0);
			$this->setfont('arial','',8);
			$this->cell(0,5,ATF::contact()->nom($this->demandeRefi['id_contact']),0,1);
	
			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(70,5,"Date de naissance :",0,0);
			$this->setfont('arial','',8);
			$birthday = ATF::contact()->select($this->demandeRefi['id_contact'],"anniversaire");
			$this->cell(0,5,$birthday ? date("d/m/Y",strtotime($birthday)):"-",0,1);

			$this->cell(50,5,"",0,0);
			$this->setfont('arial','U',8);
			$this->cell(40,5,"Score :",0,0);
			$this->setfont('arial','',8);
			$this->cell(40,5,ATF::$usr->trans($this->client['score'], "societe_score"),0);
			
			$this->setfont('arial','U',8);
			$this->cell(40,5,"Avis de crédit :",0,0);
			$this->setfont('arial','',8);
			$this->cell(40,5,ATF::$usr->trans($this->client['avis_credit'], "societe_score") ,0,1);
	
		}
		$this->line(15,$this->gety()+5,200,$this->gety()+5);
		$this->ln(7);
		
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"LE PROJET :");
		$this->setfont('arial','',8);
		
		$this->setlinewidth(0);
		$this->setdrawcolor(0,0,0);
		$y = $this->gety();
		foreach ($this->loyer as $k=>$i) {
			if (!$k) {
				$this->cell(30,5,"",0,0);
				$this->setxy(50,$y);
				$this->cell(30,5,$i['duree']." premier(s) loyer(s)",'LR',1,'C');
				$this->cell(30,5,"Loyer en € HT : ",0,0);
				$this->setxy(50,$y+5);
				$this->cell(30,5,number_format($i['loyer']+$i['assurance']+$i['frais_de_gestion'],2,"."," ")." €/".$i['frequence_loyer'],'LR',1,'C');
				$this->cell(30,5,"Durée de financement : ",0,0);
				$this->setxy(50,$y+10);
				$this->cell(30,5,$i['duree'],'LR',1,'C');
				$this->cell(30,5,"Périodicité : ",0,0);
				$this->setxy(50,$y+15);
				$this->cell(30,5,ATF::$usr->trans($i['frequence_loyer'],"loyer_frequence_loyer_feminin"),'LR',1,'C');
			} else {
				$this->setxy(50+$k*30,$y);
				$this->cell(30,5,$i['duree']." loyer(s) suivant(s)",'LR',1,'C');
				$this->setxy(50+$k*30,$y+5);
				$this->cell(30,5,number_format($i['loyer']+$i['assurance']+$i['frais_de_gestion'],2,"."," ")." €/".$i['frequence_loyer'],'LR',1,'C');
				$this->setxy(50+$k*30,$y+10);
				$this->cell(30,5,$i['duree'],'LR',1,'C');
				$this->setxy(50+$k*30,$y+15);
				$this->cell(30,5,ATF::$usr->trans($i['frequence_loyer'],"loyer_frequence_loyer_feminin"),'LR',1,'C');
			}
		}
		$this->ln(5);
			
		if (ATF::refinanceur()->isKBC($this->refinanceur['id_refinanceur'])) {
			$this->multicell(0,5,"COEFFICIENT : ".$this->demandeRefi["coefficient"]);
			$this->multicell(0,5,"TOTAL : ".number_format($this->demandeRefi['loyer_actualise'],2,'.',' ')." €");
		} else {
			$this->multicell(0,5,"TOTAL : ".number_format($this->demandeRefi['loyer_actualise'],2,'.',' ')." €");
			$this->multicell(0,5,"Valeur Résiduelle :".number_format($this->demandeRefi['valeur_residuelle'],2,'.',' ')." € HT");
			$this->ln(5);
			$this->multicell(0,5,"Règlement des loyer :".ATF::$usr->trans($this->contrat["type"],'commande'));
			$this->cell(40,5,"Pourcentage : ",0,0);
			$this->cell(0,5,"MATERIELS : ".$this->demandeRefi["pourcentage_materiel"]." %",0,1);
			$this->cell(40,5,"",0,0);
			$this->cell(0,5,"LOGICIELS : ".$this->demandeRefi["pourcentage_logiciel"]." %",0,1);
			$this->cell(40,5,"",0,0);
			$this->cell(0,5,"AUTRES : ".(100-($this->demandeRefi["pourcentage_materiel"]+$this->demandeRefi["pourcentage_logiciel"]))." %",0,1);
			$this->multicell(0,5,"NB : Pas de services à réalisations successives.");
		}
		
		$this->setlinewidth(2);
		$this->setdrawcolor(192,192,192);
		$this->line(15,$this->gety()+5,200,$this->gety()+5);
		$this->ln(7);
		
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"DETAIL DU PROJET :");
		$this->setfont('arial','',8);
		//$this->ln(5);
		$this->multicell(0,5,"Description du matériel : ".$this->demandeRefi["description"],0,1);
		$this->multicell(0,5,"Marque du matériel : ".$this->demandeRefi["marque_materiel"],0,1);
		if (ATF::refinanceur()->isBNP($this->refinanceur['id_refinanceur'])) {
			$this->multicell(0,5,"Nom et N° SIREN du fournisseur ou de l'apporteur d'affaire :".ATF::devis()->getFournisseurs($this->devis["id_devis"],true));
			$this->multicell(0,5,"Raison sociale :",0,1);
			$this->multicell(0,5,"Siren :",0,1);
		}

		if (!ATF::refinanceur()->isKBC($this->refinanceur['id_refinanceur'])) {
			$this->line(15,$this->gety()+5,200,$this->gety()+5);
			$this->ln(7);
			
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"COUPON REPONSE DE :".$this->refinanceur['refinanceur']);
			$this->setfont('arial','',8);
			//$this->ln(5);
			
			$this->cell(80,5,"Date d'accord : _____________",0,0);
			$this->cell(80,5,"Date de refus : _____________",0,1);
			
			$this->multicell(0,5,"Taux : _____________");
			$this->cell(50,5,"Si A/R Contrat n°",0,0);
			$this->cell(30,5,"Encours HT : ____________");
			$this->cell(20,5,"",0,0);
			$this->cell(0,5,"Révision possible avec : _____________",0,1);
			
			$this->multicell(0,5,"Echéance  du (payée) : ____________");
			$this->multicell(0,5,"Frais de gestion : ____________€");
			$this->multicell(0,5,"Validité accord : ____________");
			$this->multicell(0,5,"Observations et / ou conditions :");
			$this->multicell(0,5,"Signature :");
		}

	}
			
	/** Initialise les variables pour générer une Facture
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-02-2011
	* @param int $id Identifiant Facture
	*/
	public function facture($id,$s,$global=false) {
		$this->facture = ATF::facture()->select($id);
		ATF::facture_ligne()->q->reset()->where("visible","oui")->where("afficher","oui")->where("id_facture",$this->facture['id_facture']);
		$this->lignes = ATF::facture_ligne()->sa();

		$this->client = ATF::societe()->select($this->facture['id_societe']);
		$this->affaire = ATF::affaire()->select($this->facture['id_affaire']);
		$this->devis = ATF::affaire()->getDevis($this->affaire['id_affaire'])->infos;
		$this->user = ATF::user()->select($this->facture['id_user']);
		$this->agence = ATF::agence()->select($this->user['id_agence']);
		$this->societe = ATF::societe()->select($this->affaire['id_filiale']);
		$this->contrat = ATF::affaire()->getCommande($this->affaire['id_affaire'])->infos;

		//Styles utilisés

		$this->colsProduit = array("border"=>1,"size"=>9);
		$this->colsProduitAlignLeft = array("border"=>1,"size"=>9,"align"=>"L");
		$this->styleDetailsProduit = array("border"=>1,"bgcolor"=>"efefef","decoration"=>"I","size"=>8,"align"=>"L");

		if ($this->facture['type_facture']=="refi") {
			$this->demandeRefi = ATF::demande_refi()->select($this->facture['id_demande_refi']);
			$this->refinanceur = ATF::refinanceur()->select($this->facture['id_refinanceur']);
			$this->factureRefi($global);
		} elseif ($this->facture['type_facture']=="facture" || $this->facture['type_facture']=="libre") {
			$this->factureClassique($global);
		}elseif($this->facture['type_facture']=="midas"){
			$this->factureMidas($global);
		}

		$this->SetXY(10,-30);
		$this->setfont('arial','',7);	
		$this->multicell(200,2,"Conformément à l’article L 441-6 du code de commerce, une indemnité forfaitaire de 40,00 EUR sera due de plein droit pour tout retard de paiement à l'échéance.\nCette indemnité compensatoire sera complétée d’une indemnité moratoire correspondant au Taux BCE à sa dernière opération de refinancement majorée de 10 points, sans qu’une mise en demeure ne soit nécessaire, et ce sous toute réserve d’actions complémentaires en réparation du préjudice financier subit.");
	}
	
	/** PDF d'une facture refinanceur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 21-02-2011
	*/
	private function factureRefi($global=false) {
		if(!$global){
			$this->open(); 
		}
		$this->addpage();
		$this->setMargins(15,30);
		$this->sety(10);
		
		$this->setfont('arial','B',22);
		if($this->facture["prix"]>0){
			$this->multicell(0,15,'FACTURE',0,'C');
		}else{
			$this->multicell(0,15,'AVOIR',0,'C');
		}
		$this->setfont('arial','',8);
		
		if(ATF::$codename == "cleodisbe"){
			$cadre = array(
				$this->societe['adresse']
				,$this->societe['adresse_2']
				,$this->societe['cp']." ".$this->societe['ville']
				,"Tel : ".$this->agence['tel']
				,"TVA : BE 0 ".$this->societe["siren"]
				," "
			);
			$this->cadre(20,30,80,35,$cadre,$this->societe['societe']);

		}else{
			//CADRE Societe
			$cadre = array(
				$this->societe['adresse']
				,$this->societe['adresse_2']
				,$this->societe['cp']." ".$this->societe['ville']
				,"Tel : ".$this->agence['tel']
				,"N° TVA intra : FR 91 ".$this->societe["siren"]
				,"RCS ".$this->societe['ville']." ".$this->societe['siren']
			);
			$this->cadre(20,30,80,35,$cadre,$this->societe['societe']);
		}

		
		
		//CADRE Refi
		$cadre = array(
			$this->refinanceur['adresse']
			,$this->refinanceur['adresse_2']
			,$this->refinanceur['cp']." ".$this->refinanceur['ville']
		);
		if(ATF::$codename == "cleodisbe"){ $cadre[] = "TVA : BE 0 ".$this->refinanceur["siren"]; }
		$this->cadre(110,30,80,35,$cadre,$this->refinanceur['refinanceur']);
		
		$this->multicell(0,5,"A l'attention du service comptabilité,");
		$this->ln(5);
		$y = $this->gety();
		
		//CADRE Date
		$cadre = array(array("txt"=>"Date : ".date("d/m/Y",strtotime($this->facture['date'])),"align"=>"C"));
		$this->cadre(10,$y,60,10,$cadre);

		//CADRE Client
		$cadre = array(array("txt"=>$this->client['societe'].($this->client['code_client']?"(".$this->client['code_client'].")":NULL),"align"=>"C"));
		$this->cadre(75,$y,60,10,$cadre);

		//CADRE Facture
		$cadre = array(array("txt"=>"N° de facture : ".$this->facture['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),"align"=>"C"));
		$this->cadre(140,$y,60,10,$cadre);

		if ($this->lignes) {
			$this->repeatEntete = true;
			$this->ln(5);
			$this->multicell(0,5,"DESCRIPTION DES EQUIPEMENTS ET PRESTATIONS OBJET DU CONTRAT DE LOCATION ".$this->devis['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),0,'C');
			
			// Groupe les lignes par affaire
			$lignes=$this->groupByAffaire($this->lignes);
			$head = array("Qté","Type","Marque","Désignation");
			$w = array(16,30,30,109);
			foreach ($lignes as $k => $i) {
				if (!$k) {
					$title = "EQUIPEMENT(S) NEUF(S)";
				} else {
					$affaire_provenance=ATF::affaire()->select($k);
					if($this->affaire["nature"]=="avenant"){
						$title = "EQUIPEMENT(S) RETIRE(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}elseif($this->affaire["nature"]=="AR"){
						$title = "EQUIPEMENT(S) PROVENANT(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}elseif($this->affaire["nature"]=="vente"){
						$title = "EQUIPEMENT(S) VENDU(S) DE L'AFFAIRE ".$affaire_provenance["ref"]." - ".ATF::societe()->select($affaire_provenance['id_societe'],'code_client');
					}
				}
				$this->setFillColor(239,239,239);
				$this->setfont('arial','B',10);
				$this->multicell(185,5,$title,1,'C',1);
				$this->setfont('arial','',8);

				unset($data,$styles);
				foreach ($i as $k_ => $i_) {
					$produit = ATF::produit()->select($i_['id_produit']);
					$data[] = array(
						round($i_['quantite'])
						,ATF::sous_categorie()->nom($produit['id_sous_categorie'])
						,ATF::fabriquant()->nom($produit['id_fabriquant'])
						,$i_['produit']
						,"details"=>$i_['serial']?"Serial : ".$i_['serial']:""
					);
					$styles[] = array(
						$this->colsProduit
						,$this->colsProduit
						,$this->colsProduit
						,$this->colsProduit
						,"details"=>$this->styleDetailsProduit
					);
					$total+=$i_['quantite']*$i_['prix_achat'];
				}
				
				$this->tableauBigHead($head,$data,$w,5,$styles,265);
			
				if ($this->facture['commentaire']) {
					$com = array(array("Commentaire : ".$this->facture['commentaire']));
					$sCom = array(array($this->styleDetailsProduit));
					$this->tableau(false,$com,185,5,$sCom);
				}
			}
//			$total = ATF::devis()->valeur_residuelle_pv($this->devis["id_affaire"]);
			$this->ln(5);
			$total = $this->facture['prix'];
			$totalTTC = $total*$this->facture['tva'];

			
			$head = array("Montant Total HT","Taux","Montant TVA (".abs(($this->facture['tva']-1)*100)."%)","Total T.T.C.");
			$data = array(
				array(
					number_format(round($total,2),2,"."," ")." €"
					,number_format(($this->facture['tva']-1)*100,2,"."," ")."%"
					,number_format(round(($totalTTC-$total),2),2,"."," ")." €"
					,number_format(round($totalTTC,2),2,"."," ")." €"
				)
			);
			$this->tableau($head,$data,185);
		}
		
		$this->ln(10);
		$this->setfont('arial','I',8);
		$this->multicell(0,5,"Echéance de la facture : ".date("d/m/Y",strtotime($this->facture['date_previsionnelle'])));

	}

	/** PDF d'une facture Midas 
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 17-11-2014
	*/
	private function factureMidas($global=false){

		if(!$global){
			$this->open(); 
		}
		$this->addpage();
		$this->setMargins(15,30);
		$this->sety(10);

		$this->setfont('arial','B',22);
		$this->multicell(0,15,'FACTURE',0,'C');
		
		$this->setfont('arial','',8);

		$cadre = array(
			$this->societe['adresse']
			,$this->societe['adresse_2']
			,$this->societe['cp']." ".$this->societe['ville']
			,"Tel : ".$this->agence['tel']
			,"N° TVA intra : FR 91 ".$this->societe["siren"]
			,"RCS ".$this->societe['ville']." ".$this->societe['siren']
		);
		$this->cadre(20,30,80,35,$cadre,$this->societe['societe']);
		
		//CADRE Client
		$cadre = array(
			($this->client['facturation_adresse']?$this->client['facturation_adresse']:$this->client['adresse'])
			,($this->client['facturation_adresse_2']?$this->client['facturation_adresse_2']:$this->client['adresse_2'])
			,($this->client['facturation_adresse_3']?$this->client['facturation_adresse_3']:$this->client['adresse_3'])
			,($this->client['facturation_cp']?$this->client['facturation_cp']:$this->client['cp'])." ".($this->client['facturation_ville']?$this->client['facturation_ville']:$this->client['ville'])
			,"Tel : ".$this->client['tel']
		);
		$this->cadre(110,30,80,35,$cadre,$this->client['societe']);
		
		$this->cell(35,10,'Date : '.date("d/m/Y", strtotime($this->facture["date"])),1,0,'C');
		$this->cell(40,10,'MIDAS France',1,0,'C');
		$this->cell(60,10,'N° de facture : '.$this->facture["ref"],1,0,'C');

		$echeance = date("d/m/Y", strtotime($this->facture["date"]."+1 month"));
		if($echeance != $this->facture["date_previsionnelle"]){$echeance = date("d/m/Y", strtotime($this->facture["date_previsionnelle"])); }
		$this->cell(45,10,'Echéance : '.$echeance,1,1,'C');

		$this->ln(5);

		
		$head = array("Quantité","Libellé","Montant total € HT");
		$w = array(20,120,40);
		$data = $styles = array();
		
		$data[] = array(
				"1"
				,"Convention n° C201211-1 du 28/12/2012"
				,$this->facture["prix"]				
			);
		$styles[] = array(
				""
				,$this->colsProduitAlignLeft
				,""
			);

		$data[] = array(
				""
				,"Objet : \n\nGestion totale des factures de flotte automobile pour le compte de MIDAS France\nPériode concernée : ".$this->facture["commentaire"]
				,""						
			);
		$styles[] = array(
				""
				,$this->colsProduitAlignLeft
				,""
			);


		$this->tableau($head,$data,$w,5,$styles);

		$this->ln(5);
		$total = $this->facture['prix'];
		$totalTTC = $total*$this->facture['tva'];
		
		$head = array("Montant Total HT","Taux","Montant TVA (".(($this->facture['tva']-1)*100)."%)","Total T.T.C.");
		$w = array(46,47,47,40);
		$data = array(
			array(
				number_format(abs(round($this->facture["prix"],2)),2,'.',' ')." €"
				,number_format(abs(($this->facture['tva']-1)*100),2,'.',' ')."%"
				,number_format(abs(round(($this->facture["prix"]*($this->facture['tva']-1)),2)),2,'.',' ')." €"
				,number_format(abs(round($this->facture["prix"]*$this->facture['tva'],2)),2,'.',' ')." €"
			)
		);
		$this->tableau($head,$data,$w);
		

	}
		
	/** PDF d'une facture Classique aussi dit 'Autre Facture'
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 22-02-2011
	*/
	private function factureClassique($global=false){
		if(!$global){
			$this->open(); 
		}
		$this->addpage();
		$this->setMargins(15,30);
		$this->sety(10);

		$this->setfont('arial','B',22);
		if($this->facture["prix"]>0){
			$this->multicell(0,15,'FACTURE',0,'C');
		}else{
			$this->multicell(0,15,'AVOIR',0,'C');
		}
		$this->setfont('arial','',8);
		
		if(ATF::$codename == "cleodisbe"){
			$cadre = array(
				$this->societe['adresse']
				,$this->societe['adresse_2']
				,$this->societe['cp']." ".$this->societe['ville']
				,"Tel : ".$this->agence['tel']
				,"TVA : BE 0 ".$this->societe["siren"]
				," "
			);
			$this->cadre(20,30,80,35,$cadre,$this->societe['societe']);

		}else{
			//CADRE Societe
			$cadre = array(
				$this->societe['adresse']
				,$this->societe['adresse_2']
				,$this->societe['cp']." ".$this->societe['ville']
				,"Tel : ".$this->agence['tel']
				,"N° TVA intra : FR 91 ".$this->societe["siren"]
				,"RCS ".$this->societe['ville']." ".$this->societe['siren']
			);
			$this->cadre(20,30,80,35,$cadre,$this->societe['societe']);
		}
		
		//CADRE Client
		if($this->client['facturation_adresse']){
			$cadre = array(
				 $this->client['facturation_adresse']
				,$this->client['facturation_adresse_2']
				,$this->client['facturation_adresse_3']
				,$this->client['facturation_cp']." ".$this->client['facturation_ville']
				,"Tel : ".$this->client['tel']
			);
		}else{
			$cadre = array(
				 $this->client['adresse']
				,$this->client['adresse_2']
				,$this->client['adresse_3']
				,$this->client['cp']." ".$this->client['ville']
				,"Tel : ".$this->client['tel']
			);
		}		

		if(ATF::$codename == "cleodisbe"){ $cadre[] = "TVA : BE 0 ".$this->client["siren"]; }
		
		$this->cadre(110,30,80,35,$cadre,$this->client['societe']);
		
		$this->multicell(0,5,"A l'attention du service comptabilité,");
		$this->ln(5);
		$y = $this->gety();
		
//		//CADRE Date
//		$cadre = array(array("txt"=>"Date : ".date("d/m/Y",strtotime($this->facture['date'])),"align"=>"C"));
//		$this->cadre(10,$y,35,10,$cadre);
//
//		//CADRE Client
//		$cadre = array(array("txt"=>$this->client['societe'].($this->client['code_client']?"(".$this->client['code_client'].")":NULL),"align"=>"C"));
//		$this->cadre(50,$y,85,10,$cadre);
//
//		//CADRE Facture
//		$cadre = array(array("txt"=>"N° de facture : ".$this->facture['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),"align"=>"C"));
//		$this->cadre(140,$y,60,10,$cadre);

		//CADRE Date
		$cadre = array(array("txt"=>"Date : ".date("d/m/Y",strtotime($this->facture['date'])),"align"=>"C"));
		$this->cadre(10,$y,60,10,$cadre);

		//CADRE Client
		$cadre = array(array("txt"=>util::truncate($this->client['societe'],25).($this->client['code_client']?"(".$this->client['code_client'].")":NULL),"align"=>"C"));
		$this->cadre(75,$y,60,10,$cadre);

		//CADRE Facture
		$cadre = array(array("txt"=>"N° de facture : ".$this->facture['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),"align"=>"C"));
		$this->cadre(140,$y,60,10,$cadre);

		if ($this->lignes) {
			$head = array("Quantité","Désignation","Montant");
			$w = array(20,120,40);
			$data = $styles = array();
			//Quantite
			$data[0][0] = "1";
			if($this->facture['type_facture'] !== "libre") {
				//Désignation L1
				if($this->affaire['nature']=="vente"){
					$data[0][1] = "Vente pour le contrat n°".$this->affaire['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL);
				}else{	
					if($this->devis['type_contrat']=="presta"){ $data[0][1] = "Redevance du contrat de prestation n°".$this->affaire['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL);
					}else{$data[0][1] = "Redevance de mise à disposition du contrat n°".$this->affaire['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL); }
				}
				//Désignation L2
				if($this->affaire['ref'] && $this->affaire['nature']!="vente"){
					$data[0][1] .= "\nPour la période allant du ".date("d/m/Y",strtotime($this->facture['date_periode_debut']))." au ".date("d/m/Y",strtotime($this->facture['date_periode_fin']));
				}
			}else{
				if($this->facture['type_libre'] === "normale"){
					//Désignation L1
					if($this->affaire['nature']=="vente"){
						$data[0][1] = "Vente pour le contrat n°".$this->affaire['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL);
					}else{
						if($this->facture["redevance"] === "oui"){
							if($this->devis['type_contrat']=="presta"){ $data[0][1] = "Redevance du contrat n°".$this->affaire['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL);
							}else{	$data[0][1] = "Redevance de mise à disposition du contrat n°".$this->affaire['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL); }
						}						
					}
					//Désignation L2
					if($this->facture["redevance"] === "oui"){
						if($this->affaire['ref'] && $this->affaire['nature']!="vente"){
							$data[0][1] .= "\nPour la période allant du ".date("d/m/Y",strtotime($this->facture['date_periode_debut']))." au ".date("d/m/Y",strtotime($this->facture['date_periode_fin']));
						}
					}
				}
			}		
			//Désignation L3
			$data[0][1] .= "\nPar ".ATF::$usr->trans($this->facture['mode_paiement'],'facture');
			//Désignation L4
			list($annee,$mois,$jour)= explode("-",$this->facture['date']);
			//$data[0][1] .= "\nDate de facture le ".date("d/m/Y",strtotime($this->facture['date']));
			// Montant Facture
			$data[0][2] = number_format(abs($this->facture["prix"]),2,'.',' ')." €";
			
			if($this->facture['type_facture'] !== "libre"){
				//Préparation du détail
				if($this->affaire['nature']=="vente"){
					$data[0]['details'] = "Equipements objets de la vente";
				}elseif($this->devis['type_contrat']=="presta"){ $data[0]['details'] = "";
				}else{	$data[0]['details'] = "Matériels objets de la location"; }
				foreach ($this->lignes as $k => $i) {
					$data[0]['details'] .= "\n".round($i['quantite'])." ".$i['produit'].($i['serial']?" Numéro(s) de série : ".$i['serial']:"");
				}
				$styles[0] = array(
					""
					,$this->colsProduitAlignLeft
					,""
					,"details"=>$this->styleDetailsProduit
				);
			}else{
				if($this->facture['type_libre'] === "normale"){
					//Préparation du détail
					if($this->affaire['nature']=="vente"){
						$data[0]['details'] = "Equipements objets de la vente";
					}else{
						$data[0]['details'] = "Matériels objets de la location";
					}
					foreach ($this->lignes as $k => $i) {
						$data[0]['details'] .= "\n".round($i['quantite'])." ".$i['produit'].($i['serial']?" Numéro(s) de série : ".$i['serial']:"");
					}
					$styles[0] = array(
						""
						,$this->colsProduitAlignLeft
						,""
						,"details"=>$this->styleDetailsProduit
					);
				}
			}
							
			$this->tableauBigHead($head,$data,$w,5,$styles);
			
			if ($this->facture['commentaire']) {
				$com = array(array("Commentaire : ".$this->facture['commentaire']));
				$sCom = array(array($this->styleDetailsProduit));
				$this->tableau(false,$com,180,5,$sCom);
			}
			
			if($this->facture['type_facture'] === "libre"){
				if($this->facture['type_libre'] !== "normale"){
					$InfosTVA = array(array("\n\nTVA non applicable - Article 4632b du CGI"));
					$sInfosTVA = array(array($this->styleDetailsProduit));
					$this->tableau(false,$InfosTVA,180,5,$sInfosTVA);
				}
			}
			
			$this->ln(5);
			$total = $this->facture['prix'];
			$totalTTC = $total*$this->facture['tva'];
			if($this->facture['type_facture'] === "libre"){
				if($this->facture['type_libre'] === "normale"){
					$head = array("Montant Total HT","Taux","Montant TVA (".(($this->facture['tva']-1)*100)."%)","Total T.T.C.");
					$data = array(
						array(
							number_format(abs(round($this->facture["prix"],2)),2,'.',' ')." €"
							,number_format(abs(($this->facture['tva']-1)*100),2,'.',' ')."%"
							,number_format(abs(round(($this->facture["prix"]*($this->facture['tva']-1)),2)),2,'.',' ')." €"
							,number_format(abs(round($this->facture["prix"]*$this->facture['tva'],2)),2,'.',' ')." €"
						)
					);
				}else{
					$head = array("Montant Total HT","Taux","Montant TVA","Total T.T.C.");
					$data = array(
						array(
							number_format(abs(round($this->facture["prix"],2)),2,'.',' ')." €"
							,number_format(abs((1-1)*100),2,'.',' ')."%"
							,number_format(abs(round(($this->facture["prix"]*0),2)),2,'.',' ')." €"
							,number_format(abs(round($this->facture["prix"],2)),2,'.',' ')." €"
						)
					);
				}
			}else{
				$head = array("Montant Total HT","Taux","Montant TVA (".(($this->facture['tva']-1)*100)."%)","Total T.T.C.");
					$data = array(
						array(
							number_format(abs(round($this->facture["prix"],2)),2,'.',' ')." €"
							,number_format(abs(($this->facture['tva']-1)*100),2,'.',' ')."%"
							,number_format(abs(round(($this->facture["prix"]*($this->facture['tva']-1)),2)),2,'.',' ')." €"
							,number_format(abs(round($this->facture["prix"]*$this->facture['tva'],2)),2,'.',' ')." €"
						)
					);
			}
			
			
			
			$this->tableau($head,$data);
			
		}



		$this->ln(10);
		$y = $this->getY();
		$this->setfont('arial','U',8);
		$this->cell(60,5,"TERMES DE PAIEMENT",0,1);
		$this->setfont('arial','',8);
		if($this->facture["prix"]>0){
			if($this->facture['mode_paiement']){
				if ($this->facture['mode_paiement']=="cheque") {
					$this->cell(0,5,"A réception de facture",0,1);
				} elseif ($this->facture['mode_paiement']=="virement") {
					$this->cell(0,5,"Par virement en date du ".date("d/m/Y",strtotime($this->facture['date_previsionnelle'])),0,1);
				} else {
					$this->cell(0,5,"Le ".date("d/m/Y",strtotime($this->facture['date_previsionnelle']))." vous serez débité sur le compte : ".$this->affaire['IBAN']." - ".$this->affaire['BIC'],0,1);
				}
			}			
		}else{
			$this->cell(0,5,"Par remboursement ou compensation",0,1);
		}		
		$this->cell(0,5,"RUM ".$this->affaire["RUM"],0,1);
		$this->cell(0,5,"ICS ".__ICS__ ,0,1);

		if($this->facture["mode_paiement"] == "virement"){
			$cadre = array();
			$cadre[] = $this->societe["nom_banque"];
			$cadre[] = "RIB : ".util::formatRIB($this->societe["RIB"]);
			$cadre[] = "IBAN : ".$this->societe["IBAN"];
			$cadre[] = "BIC : ".$this->societe["BIC"];
			$this->cadre(85,$y,80,35,$cadre,"Coordonnées bancaires");
		}
	}

	
	/** PDF de l'échéancier d'une prolongation
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	public function prolongation($id) {
		
		$this->prolongation = ATF::prolongation()->select($id);
		$this->initEcheancier($this->prolongation['id_affaire']);
		
		$this->duree = ATF::loyer_prolongation()->dureeTotal($this->affaire['id_affaire']);
		
		
		$this->dateExpiration = date("d/m/Y",strtotime($this->prolongation['date_fin']));
		$this->dateDebut = date("d/m/Y",strtotime($this->prolongation['date_debut']));
		
		$this->type = "prolongation";
		
		$this->echeancierFacturation();
	}
	
	/** PDF de l'échéancier d'une affaire
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @date 28-02-2011
	*/
	public function affaire($id) {
		
		$this->initEcheancier($id);

		$this->duree = ATF::loyer()->dureeTotal($this->affaire['id_affaire']);
		
		$this->dateExpiration = date("d/m/Y",strtotime($this->commande['date_evolution']));
		$this->dateDebut = date("d/m/Y",strtotime($this->commande['date_debut']));
		
		$this->type = "contrat";
		
		$this->echeancierFacturation();
	}
	
	/** PDF de l'échéancier d'une affaire
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 30-05-2011
	*/
	private function initEcheancier($id) {
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
	
	/** PDF de l'échéancier d'une affaire
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 28-02-2011
	*/
	private function echeancierFacturation($id) {
		$this->open();
		$this->unsetHeader();
		$this->addpage();
		
		$this->setfont('arial','B',10);
		$this->image(__PDF_PATH__."/cleodis/logo.jpg",5,18,55);

		$this->setxy(100,10);
		$this->cell(0,5,"LE LOUEUR",0,1,'L');
		$this->setLeftMargin(65);
		$this->setfont('arial','B',7);
		$this->cell(0,3,$this->societe['societe']." - ".$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville'],0,1);
		$this->cell(0,3,"Tél :".$this->societe['tel']." - Fax :".$this->societe['fax'],0,1);
		if($this->societe['id_pays'] =='FR'){
			$this->cell(0,3,"RCS LILLE B ".$this->societe['siren']." – APE 7739Z N° de TVA intracommunautaire : FR 91 ".$this->societe["siren"],0,1);
		}else{
			$this->cell(0,3,"Numéro de TVA  ".$this->societe['siret'],0,1);
		}
	
		$this->setfont('arial','B',10);
		$this->setxy(100,28);
		$this->cell(0,6,"LE LOCATAIRE",0,1,'L');
		$this->setLeftMargin(65);
		$this->setfont('arial','B',10);
		$this->cell(30,5,"Raison sociale : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(0,5,$this->client['societe'],0,1);
		$this->setfont('arial','B',10);
		$this->cell(20,5,"Adresse : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(0,5,$this->client['adresse'],0,1);
		$this->setfont('arial','B',10);
		$this->cell(25,5,"Code Postal : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(15,5,$this->client['cp'],0,0);
		$this->setfont('arial','B',10);
		$this->cell(15,5,"Ville : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(40,5,$this->client['ville'],0,1);
		$this->setfont('arial','B',10);
		if($this->client['id_pays'] =='FR'){
			$this->cell(15,5,"SIRET : ",0,0);
		}else{
			$this->cell(35,5,"NUMERO DE TVA : ",0,0);
		}
		$this->setfont('arial','',10);
		$this->cell(30,5,$this->client['siret'],0,0);
		$this->setfont('arial','B',10);
		$this->cell(10,5,"Tél. : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(15,5,$this->client['tel'],0,1);


		$this->SetLineWidth(0.35);
		$this->SetDrawColor(64,192,0);
		$this->line(0,60,220,60);
		$this->setLeftMargin(10);
		$this->sety(62);

		$this->setfont('arial','B',10);
		$this->cell(45,5,"Contrat de ".($this->devis['type_contrat']=="vente"?"vente":"location")." : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(80,5,$this->devis['ref'].($this->client["code_client"]?"-".$this->client["code_client"]:NULL),0,0);
		$this->setfont('arial','B',10);

		$this->cell(45,5,"Date départ : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(80,5,$this->dateDebut,0,1);
		$this->setfont('arial','B',10);
		$this->cell(45,5,"Durée : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(80,5,$this->duree." Mois",0,0);
		$this->setfont('arial','B',10);
		$this->cell(45,5,"Date d'expiration : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(80,5,$this->dateExpiration,0,1);
		
		$this->setfont('arial','B',10);

		$this->setfont('arial','B',10);
		$this->cell(45,5,"Terme : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(80,5,"Terme à échoir",0,1);

		$this->setfont('arial','B',10);
		$this->cell(45,5,"Mode de paiement : ",0,0);
		$this->setfont('arial','',10);
		$this->cell(80,5,ATF::$usr->trans($this->commande['type'],'commande'),0,1);


		
		$this->SetLineWidth(0.35);
		$this->SetDrawColor(64,192,0);

		$this->line(0,60,220,60);
		$this->setLeftMargin(10);
		$this->sety(62);

		$this->sety(95);
		
		$this->setdrawcolor(0,0,0);
		$this->SetFillColor(200,200,200);
		$this->SetLineWidth(0.2);

		$this->cell(190,10,"CET ÉCHÉANCIER VAUT FACTURE (MONTANTS EN EUROS)",1,0,'C',true);

		$this->sety(110);
		
	
		$this->setfont('arial','',8);
		
		$totaux=ATF::facturation()->montant_total($this->affaire['id_affaire'],$this->type);
		if ($this->lignes) {
			$this->setTopMargin(30);
			$head = array("Date échéance","Loyer HT","Prestations","Assurances","TVA (".(($this->commande['tva']-1)*100)."%) (1)","Total TTC");
			foreach ($this->lignes as $k=>$i) {
				//Si le montant est différent c'est qu'on a changé de loyer, on le signale par une ligne
				if($montant!=$i["montant"]){
//					$this->line($this->getX(),$this->getY(),($this->getX()+192),$this->getY());
				}
				
				if($i["type"]==$this->type) {
					$loyer_ht=$i["montant"];
					$tva=($i["montant"]+$i["frais_de_gestion"]+$i["assurance"])*($this->commande['tva']-1);
					$total=$tva+($i["montant"]+$i["frais_de_gestion"]+$i["assurance"]);
					
					$data[] = array(
						date("d/m/Y",strtotime($i["date_periode_debut"]))
						,number_format($loyer_ht,2,","," ")
						,number_format($i["frais_de_gestion"],2,","," ")
						,number_format($i["assurance"],2,","," ")
						,number_format($tva,2,","," ")
						,number_format($total,2,","," ")
					);
					
					//Conserver le montant pour vérifier si on a changé de loyer
					$montant=$i["montant"];
				}
			}
			
			$data[] = array("TOTAL"	,number_format($totaux["loyer"],2, ',', ' '),number_format($totaux["total_frais_de_gestion"],2, ',', ' '),number_format($totaux["total_assurance"],2, ',', ' '),number_format($totaux["tva"],2, ',', ' '),number_format($totaux["total"],2, ',', ' '));
			
			$this->tableauBigHead($head,$data,190,5,false,270);

		}

		$this->ln(5);
		$this->setfont('arial','',10);
		$this->cell(45,5,"(1) Taux de TVA (loyers) :",0,0);
		$this->cell(10,5,(($this->commande['tva']-1)*100)." %",0,1);
		$this->cell(10,5,"(2) Exonération de TVA article 261 C2 du CGI",0,1);

		$this->ln(5);
		$this->cell(192,5,"Sans escompte, règlement comptant. En cas de retard de paiement, des intérêts de retard seront calculés aux taux de 1 %",0,1);
		$this->cell(192,5,"par mois de retard sans préjudice des conditions générales du contrat.",0,1);

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
		
		$head = array("SOCIETE","AFFAIRE","DATE DEBUT","DATE FIN","MONTANT HT","TYPE");
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
				"(".ATF::societe()->select($item["id_societe"],"code_client").") ".ATF::societe()->nom($item["id_societe"])
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
	/*
	function facturation_grille_client_non_facture($non_facturer) {
	
		$this->open();
		$this->addpage();
		
		$this->setfont('arial','B',10);
		$this->image(__PDF_PATH__."/cleodis/logo.jpg",5,18,55);
		

		$this->setxy(100,10);
		$this->cell(0,5,"GRILLE DE FACTURATION DES FACTURES NON ENVOYÉES",0,1,'L');


		$this->SetLineWidth(0.35);
		$this->SetDrawColor(64,192,0);
		$this->line(0,60,220,60);
		$this->setLeftMargin(10);
		$this->sety(62);
		$this->setfont('arial','B',10);
		
		$date_debut = date("Y-m-d",mktime(0,0,0,date("m"),01,date("Y")));
		$date_debut=date("Y-m-d",strtotime($date_debut."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_debut."+1 month"));
		$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));

		$this->cell(0,3,"Grille de facturation pour la période du ".utf8_decode($GLOBALS['classes']['main']->date_translator($date_debut,true))." au ".utf8_decode($GLOBALS['classes']['main']->date_translator($date_fin,true)));
	
		$this->sety(70);

		$this->setfont('arial','',8);
		$this->cell(0,3,"Liste des factures n'ayant pas été envoyées (".count($non_facturer).") dont ".$non_facturer[0]['nfc']." contrats et ".$non_facturer[0]['nfp']." prolongations.",0,1);

		$this->sety(75);
		$this->setdrawcolor(0,0,0);
		$this->SetLineWidth(0.2);
		
		$this->cell(50,10,"SOCIETE",1,0,'C');
		$this->cell(15,10,"AFFAIRE",1,0,'C');
		
		$x = $this->getx();
		$this->cell(15,5,"DATE",'TRL',0,'C');
		$this->setxy($x,$this->gety()+5);		
		$this->cell(15,5,"DEBUT",'BRL',0,'C');
		$this->setxy($this->getx(),$this->gety()-5);		
	
		$x = $this->getx();
		$this->cell(15,5,"DATE",'TRL',0,'C');
		$this->setxy($x,$this->gety()+5);		
		$this->cell(15,5,"FIN",'BRL',0,'C');
		$this->setxy($this->getx(),$this->gety()-5);		
		
		$x = $this->getx();
		$this->cell(15,5,"MONTANT",'TRL',0,'C');
		$this->setxy($x,$this->gety()+5);		
		$this->cell(15,5,"HT",'BRL',0,'C');
		$this->setxy($this->getx(),$this->gety()-5);		

		$this->cell(15,10,"TYPE",1,0,'C');
		$this->cell(65,10,"RAISON",1,1,'C');

		$this->setfont('arial','',6);

		$montant_total=0;

		foreach ($non_facturer as $key => $item) {
			$this->cell(50,5,ATF::societe()->nom($item["id_societe"]),1,0,'C');
			$this->cell(15,5,$GLOBALS['classes']['affaire']->select($item["id_affaire"],"ref"),1,0,'C');
			$this->cell(15,5,date("d/m/Y",strtotime($item["date"])),1,0,'C');
			$this->cell(15,5,date("d/m/Y",strtotime($item["date_fin"])),1,0,'C');
			$this->cell(15,5,number_format($item["montant"],2, ',', ' ')." €",1,0,'C');
			$this->cell(15,5,$item["type"],1,0,'C');

			if($item["cause"]=="an"){
				$societe=ATF::societe()->select($item["id_societe"]);
				$this->cell(65,5,"Pas de mail pour le contact ".ATF::contact()->nom($societe["id_contact_facturation"]),1,1,'C');
			}elseif($item["cause"]=="pc"){
				$this->cell(65,5,"Pas de contact de facturation pour cette société",1,1,'C');
			}elseif($item["cause"]=="pi"){
				$this->cell(65,5,"Problème lors de l'insertion de la facture",1,1,'C');
			}
			$montant_total+=$item["montant"];
		}
		
		$this->ln(5);

		$this->setfont('arial','',8);
		$this->cell(0,3,"Montant total des factures non envoyées : ".number_format($montant_total,2, ',', ' ')." €",0,1);
	}
	*/

	private function tableauBigHead($head,$data,$width=false,$c_height=5,$style=false,$limitBottomMargin=270) {
		$save = $this->headStyle;
		$newStyleHead = array(
			"size" => 10
			,"color" => 000000
			,"font" => "arial"
			,"border" => 1
			,"align" => "J"
			,"bgcolor" => "c7ebb5"
		);
		
		$this->headStyle[0] = $newStyleHead;
		foreach ($head as $k=>$i) {
			$this->headStyle[] = $newStyleHead;
		}
		$this->tableau($head,$data,$width,$c_height,$style,$limitBottomMargin);
		$this->headStyle = $save;
	}
	
	/**
	*  Génère les relances et mise en demeure
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la facture
	* @date 06-02-2013
	*/
	public function relance($id){
	    if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }

		$this->relance = ATF::relance()->select($id);
        $this->client = ATF::societe()->select($this->relance['id_societe']);
        $this->contact = ATF::contact()->select($this->relance['id_contact']);
        
        ATF::relance_facture()->q->reset()->where('id_relance',$this->relance['id_relance'])->setCount();
        $this->factures = ATF::relance_facture()->sa();
        
		$this->open();
		$this->addpage();
		$this->sety(80); 
        if ($this->relance['type']=="premiere") {
            self::premiereRelance($id);
        } elseif ($this->relance['type']=="seconde") {
            self::deuxiemeRelance($id);
        } elseif ($this->relance['type']=="mise_en_demeure") {
            self::miseDemeure($id);    
        }
		      
		      
		$this->multicell(0,10,"Nous vous prions d'agréer, Madame, Monsieur, l'expression de nos salutations distinguées.",0);        
		$this->multicell(0,10,"Le service comptable,");
        $this->setfont("arial","I",12);
		$this->multicell(0,10,"Pièce jointe (copie) facture(s) impayée(s)");		
			
	}
	
	/**
	* Contenu de la 1ere relance
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la facture	
	* @date 06-02-2013
	*/
	private function premiereRelance(){
		$this->multicell(0,10,"Objet : Relance facture impayée ");
        $this->ln(5);
		
		$this->multicell(0,5,"Madame, Monsieur, ");
        $this->ln(5);
		
        if ($this->factures['count']>1) {
            $phrase = "Vos factures n°";
            foreach ($this->factures['data'] as $k=>$i) {
                $phrase .= ATF::facture()->select($i['id_facture'],"ref").", ";
            }
            $phrase .= "ayant pour montants respectifs : ";
            foreach ($this->factures['data'] as $k=>$i) {
                $phrase .= ATF::facture()->select($i['id_facture'],"prix")."€ HT, ";
            }
            $phrase .= " ont été rejetées pour le(s) motif(s) suivant(s) : ";
                   
            $phrase .= "\n\n".$this->relance['texte']."\n\n";
                        
            $this->multicell(0,5,$phrase);
        } else {
            $phrase = "Votre facture n°".
                            ATF::facture()->select($this->factures['data'][0]['id_facture'],"ref").
                            " du ".
                            ATF::facture()->select($this->factures['data'][0]['id_facture'],"date").
                            " d'un montant de ".
                            ATF::facture()->select($this->factures['data'][0]['id_facture'],"prix").
                            "€ HT a été rejetée pour le motif suivant :";
            
            $phrase .= "\n\n".$this->relance['texte']."\n\n";
                        
            $this->multicell(0,5,$phrase);
        }
        $this->ln(5);
        
		$this->multicell(0,5,"Nous vous rapellons que selon l'article 6.6, du contrat de location que vous avez signé avec CLEODIS : Les loyers (TTC) et les redevances de mise à disposition (TTC) non payés à leur échéance porteront interêt au profit du Loueur, de plein droit et sans qu'il soit besoin de quelconque mise en demeure, au taux conventionnel de 1.5% par mois à compter de leur date d'exigibilité.");		
        $this->ln(5);
	}
		
	/**
	* Contenu de la 2e relance
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la facture	
	* @date 06-02-2013
	*/
	private function deuxiemeRelance(){
		$this->multicell(0,5,"Objet : 2ème Relance facture impayée ");
        $this->ln(5);
        
        $this->multicell(0,5,"Madame, Monsieur, ");
        $this->ln(5);
		
        if ($this->factures['count']>1) {
            $phrase = "Malgré notre première relance, vos factures n°";
            foreach ($this->factures['data'] as $k=>$i) {
                $phrase .= ATF::facture()->select($i['id_facture'],"ref").", ";
            }
            $phrase .= "ayant pour montants respectifs : ";
            foreach ($this->factures['data'] as $k=>$i) {
                $phrase .= ATF::facture()->select($i['id_facture'],"prix")."€ HT, ";
            }
            $phrase .= " n'ont, à ce jour, pas été reglées";
                   
            $phrase .= "\n\n".$this->relance['texte']."\n\n";
                       
            $this->multicell(0,5,$phrase);
        } else {
            $phrase = "Malgré notre première relance, votre facture n°".
                            ATF::facture()->select($this->factures['data'][0]['id_facture'],"ref").
                            " du ".
                            ATF::facture()->select($this->factures['data'][0]['id_facture'],"date").
                            " d'un montant de ".
                            ATF::facture()->select($this->factures['data'][0]['id_facture'],"prix").
                            "€ HT n'a, à ce jour, pas été reglée.";
            
            $phrase .= "\n\n".$this->relance['texte']."\n\n";
                    
            $this->multicell(0,5,$phrase);
        }
        $this->ln(5);
        		
		$this->multicell(0,5,"Sans retour de votre part sous huitaine, nous engagerons une procédure de recouvrement. Vous vous exposez dès lors du calcul des intérêts de retard.");
		$this->ln(5);
		$this->multicell(0,5,"Si vous nous avez adressé votre règlement entre-temps, nous vous remercions de ne pas tenir compte de la présente.");
		$this->ln(5);
		$this->multicell(0,5,"Espérant que vous comprendrez l'intérêt rapide de ce dossier,");
		$this->ln(5);
	}
	
	/**
	* Contenu de la mise en demeure
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la facture	
	* @date 06-02-2013
	*/
	private function miseDemeure(){		
        $this->ln(5);
        $this->multicell(0,5,"Objet : Mise en demeure - Lettre recommandée avec accusé de reception");
        $this->ln(5);
        
        $this->multicell(0,5,"Madame, Monsieur, ");
        $this->ln(5);
        
        if ($this->factures['count']>1) {
            foreach ($this->factures['data'] as $k=>$i) {
                $refs .= ATF::facture()->select($i['id_facture'],"ref").", ";
                $prix .= ATF::facture()->select($i['id_facture'],"prix")." € HT, ";
                $total += $prix;
                $totalTTC += $prix*ATF::facture()->select($i['id_facture'],"tva");
            }
            $p = "Malgré nos multiples relances, le réglement des factures ".$refs." pour des montants respectifs de ".$prix.", ne nous est toujours pas parvenu.";
        } else {
            $refs = ATF::facture()->select($this->factures['data'][0]['id_facture'],"ref");
            $prix = ATF::facture()->select($this->factures['data'][0]['id_facture'],"prix");
            $total += $prix;
            $totalTTC += $prix*ATF::facture()->select($this->factures['data'][0]['id_facture'],"tva");
            $p = "Malgré nos multiples relances, le réglement de la facture ".$refs." pour un montant de ".$prix." € HT, ne nous est toujours pas parvenu.";
            
        }
        
 		$this->multicell(0,5,$p,0);
        $this->ln(5);
		
		$this->multicell(0,5,"Aussi, par la présente, nous vous mettons en demeure de nous verser, à titre principal, la somme de ".$total." € HT, soit ".$totalTTC." € TTC. Conformément à l'article 6.6 des conditions générales du contrat de location, cette somme sera majorée des intérêts au taux conventionnel de 1.5%.");
        $this->ln(5);
		
		$this->multicell(0,5,"Nous vous informons que ces pénalités courent dès réception de la présente.",0);
        $this->ln(5);
		
		$this->multicell(0,5,"Si dans un délai de 15 jours à compter de cette date vous ne vous êtes pas acquitté de cette somme, nous saisirons la juridiction compétente afin d'obtenir le paiement des sommes dues.");
        $this->ln(5);
		
		$this->multicell(0,5,"Si vous nous avez adressé votre réglement entre-temps, nous vous prions de ne pas tenir compte de la présente");
        $this->ln(5);		
	}
	
	
	/**
	* Génère le Contrat avec Bilan
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param $id id de la commande	
	* @date 03-03-2013
	*/
	public function envoiContratEtBilan($id,$s){
				
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		$this->envoiContrat = true;
		$this->noPageNo = true;
		
		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);
		
		$this->open();
		$this->addpage();
		$this->sety(80);   
		
		$this->multicell(0,5,"Objet : contrat de location ".$this->societe["societe"]."");
		$this->setFont("arial","I",10);
		$this->multicell(0,5,"Lille, le ".ATF::$usr->date_trans($s['date']?$s['date']:date("Y-m-d"),"force",true));
        $this->setFont("arial","",12);
				
		$this->ln(5);
        $this->multicell(0,5,ATF::$usr->trans($this->contact['civilite']).", ");
        $this->ln(5);
				
		if($this->affaire["nature"] == "AR"){
			$this->multicell(0,5,"Pour faire suite à la réception du devis ".$s['type_devis']." signé, j'ai le plaisir de vous transmettre votre contrat de location ".$this->societe["societe"]." qui annule et remplace le précédent.");
		}else{
			$this->multicell(0,5,"Pour faire suite à la réception du devis ".$s['type_devis']." signé, j'ai le plaisir de vous transmettre votre contrat de location ".$this->societe["societe"].".");	
		}
        
		$this->ln(5);
		$this->setfont("arial","U",12);
		$this->multicell(0,10,"Vous trouverez ci-joints les éléments suivants :");
		$this->setfont('arial','',11);	
		$this->setx(15);		
		$phrase = "- 3 exemplaires du contrat de location\n";  
		$phrase .= "- 3 exemplaires du procès verbal de livraison\n";  
		$phrase .= "- 2 exemplaires de mandats SEPA ";
		$this->multicell(0,5,$phrase);
		$this->ln(5);
		$this->setx(10);
		$this->setfont("arial","U",12);
		$this->multicell(0,10,"Je vous remercie de bien vouloir nous retourner :");
		$this->setfont('arial','',11);
		
		$this->setx(15);		
		$phrase = "- l'ensemble des documents signés et paraphés avec le cachet de la société \n";
		$phrase .= "- RIB mentionnant le nom de la banque\n"; 
		$phrase .= "- la copie recto/verso d'une pièce d'identité\n";
		$phrase .= "- le dernier bilan disponible";   
		$this->multicell(0,5,$phrase);
		$this->setx(25);
		$this->setFontDecoration("I");
		$phrase = "=> Si vous le souhaitez, nous pouvons contacter votre comptable pour vous. Dans ce cas, merci de renseigner les éléments suivants :";
		$this->multicell(0,5,$phrase);		
        $this->ln(3);
		$this->setx(40);
		$phrase  = "Cabinet : ................................................................ \n";
		$phrase .= "Nom : ....................................................................\n";
		$phrase .= "Téléphone : ..............................................................";
		$this->multicell(0,5,$phrase);		
        $this->unsetFontDecoration();
        
        $this->ln(5);
        $this->multicell(0,5,"Vous en souhaitant une bonne réception, je vous remercie d'avoir choisi ".$this->societe["societe"]." et demeure a votre entière disposition pour tout complément d'information.");
        $this->ln(5);
        $this->multicell(0,5,"Veuillez agréer, ".$this->contact["civilite"]." ".$this->contact["nom"].", l'expression de mes salutations distinguées.");
        
        $this->ln(15);  
        $this->setfont('arial','I',12);                 
        $this->multicell(0,5,ATF::user()->select(ATF::usr()->getId() , "prenom")." ".ATF::user()->select(ATF::usr()->getId() , "nom"));
	}

	/**
    * Génère le Contrat Sans Bilan
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param $id id de la commande	
	* @date 03-03-2013
	*/	
	public function envoiContratSsBilan($id,$s){
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		$this->envoiContrat = true;
        $this->noPageNo = true;
		
		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);
		
		$this->open();
		$this->addpage();
		$this->sety(80);   
		
        $this->multicell(0,5,"Objet : contrat de location ".$this->societe["societe"]."");
        $this->setFont("arial","I",10);
        $this->multicell(0,5,"Lille, le ".ATF::$usr->date_trans($s['date']?$s['date']:date("Y-m-d"),"force",true));
        $this->setFont("arial","",12);
                
        $this->ln(5);
        $this->multicell(0,5,ATF::$usr->trans($this->contact['civilite']).", ");
        $this->ln(5);				
		
		if($this->affaire["nature"] == "AR"){
			$this->multicell(0,5,"Pour faire suite à la réception du devis ".$s['type_devis']." signé, j'ai le plaisir de vous transmettre votre contrat de location ".$this->societe["societe"]." qui annule et remplace le précédent.");
		}else{
			$this->multicell(0,5,"Pour faire suite à la réception du devis ".$s['type_devis']." signé, j'ai le plaisir de vous transmettre votre contrat de location ".$this->societe["societe"].".");
		}
		$this->ln(5);
		$this->setfont("arial","U",12);
		$this->multicell(0,5,"Vous trouverez ci-joints les éléments suivants :");
        $this->ln(5);
		$this->setfont('arial','',11);	
		$this->setx(15);		
		$phrase = "- 3 exemplaires du contrat de location\n";  
		$phrase .= "- 3 exemplaires du procès verbal de livraison\n";  
		$phrase .= "- 2 exemplaires de mandats SEPA ";
		$this->multicell(0,5,$phrase);
		$this->ln(5);
		$this->setx(10);
		$this->setfont("arial","U",12);
		$this->multicell(0,5,"Je vous remercie de bien vouloir nous retourner :");
		$this->setfont('arial','',11); 
        $this->ln(5);
		$this->setx(15);			
		$phrase  = "- l'ensemble des documents signés et paraphés avec le cachet de la société\n"; 		   
		$phrase .= "- RIB mentionnant le nom de la banque \n";
		$phrase .= "- la copie recto/verso d'une pièce d'identité";   
		$this->multicell(0,5,$phrase);
		
		$this->setx(0);
		$this->ln(5);		
		$foot = "Vous en souhaitant bonne réception, je vous remercie d'avoir choisi ".$this->societe["societe"]." et demeure à votre entière disposition pour tout complément d'information.
		\nVeuillez agréer, ".$this->contact["civilite"]." ".$this->contact["nom"].", l'expression de mes salutations distinguées.";	
		$this->multicell(0,5,$foot);
		
		$this->ln(15);	
		$this->setfont('arial','I',12);					
		$this->multicell(0,5,ATF::user()->select(ATF::usr()->getId() , "prenom")." ".ATF::user()->select(ATF::usr()->getId() , "nom"));
		
	}

	/**
    * Génère l'avenant courrier type
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param $id id de la commande	
	* @date 03-03-2013
	*/
	public function envoiAvenant($id,$s){

		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		$this->envoiContrat = true;	
        $this->noPageNo = true;
				
		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);
		
		$this->open();
		$this->addpage();
		$this->sety(80);   
		
        $this->multicell(0,5,"Objet : contrat de location ".$this->societe["societe"]."");
        $this->setFont("arial","I",10);
        $this->multicell(0,5,"Lille, le ".ATF::$usr->date_trans($s['date']?$s['date']:date("Y-m-d"),"force",true));
        $this->setFont("arial","",12);
                
        $this->ln(5);
        $this->multicell(0,5,ATF::$usr->trans($this->contact['civilite']).", ");
        $this->ln(5);               
		
		$this->multicell(0,5,"Suite à la réception du bon de commande ".$s["bdc"].", j'ai le plaisir de vous transmettre l'avenant à votre contrat de location ".$this->societe["societe"]."");
		$this->ln(5);
        $t = "Je vous remercie de bien vouloir retourner ";
		$this->cell(81    ,5,$t);
        $this->setFontDecoration("B");
        $this->cell(50,5,"les 2 exemplaires signés avec le cachet de la société.",0,1);
        $this->unsetFontDecoration();
		$this->ln(5);
		
		$this->setx(0);
		$this->ln(5);		
		$foot = "Vous en souhaitant bonne réception, je vous remercie d'avoir choisi ".$this->societe["societe"]." et demeure à votre entière disposition pour tout complément d'information.
		\nVeuillez agréer, ".$this->contact["civilite"]." ".$this->contact["nom"].", l'expression de mes salutations distinguées.";	
		$this->multicell(0,5,$foot);
		
		$this->ln(15);	
		$this->setfont('arial','I',12);					
		$this->multicell(0,5,ATF::user()->select(ATF::usr()->getId() , "prenom")." ".ATF::user()->select(ATF::usr()->getId() , "nom"));
		
	}

	/**
    * Génère le transfert de contrat courrier type
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param $id id de la commande	
	* @date 03-03-2013
	*/
	public function contratTransfert($id,$s){
		if(ATF::$codename == "cleodis"){ $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		$this->envoiContrat = true;
        $this->noPageNo = true;
		
		
        $this->commande = ATF::commande()->select($id); 
        $this->client = ATF::societe()->select($this->commande['id_societe']);          
        $this->devis = ATF::devis()->select($this->commande["id_devis"]);
        $this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
        $this->contact = ATF::contact()->select($this->devis['id_contact']);
		
		$this->open();
		$this->addpage();
		$this->sety(80);   
		
        $this->multicell(0,5,"Objet : contrat de location ".$this->societe["societe"]."");
        $this->setFont("arial","I",10);
        $this->multicell(0,5,"Lille, le ".ATF::$usr->date_trans($s['date']?$s['date']:date("Y-m-d"),"force",true));
        $this->setFont("arial","",12);
                
        $this->ln(5);
        $this->multicell(0,5,ATF::$usr->trans($this->contact['civilite']).", ");
        $this->ln(5);               
			
		
		$this->multicell(0,5,"Pour faire suite à la reprise ".$s["reprise_magasin"].", j'ai le plaisir de vous transmettre votre contrat de location ".$this->societe["societe"]."");
		$this->ln(5);
		$this->setfont("arial","U",12);
		$this->multicell(0,5,"Vous trouverez ci-joints les éléments suivants :");
		$this->setfont('arial','',11);
		$this->setx(15);
		$phrase = "- 3 exemplaires du contrat de location\n";
		$phrase .= "- 3 exemplaires du procès verbal de livraison\n";
	    $phrase .= "- 2 exemplaires de mandats SEPA\n";
		$this->multicell(0,5,$phrase);
		$this->ln(5);
		$this->setx(10);
		$this->setfont("arial","U",12);
		$this->multicell(0,5,"Je vous remercie de bien vouloir nous retourner :");
		$this->setfont('arial','',11);
		$this->setleftMargin(15);
		$this->multicell(0,5,"- l'ensemble des documents signés et paraphés avec le cachet de la société");
	    $this->multicell(0,5,"- RIB mentionnant le nom de la banque");
		$this->multicell(0,5,"- la copie recto/verso d'une pièce d'identité");
        if ($s['docSupAretourner']) {
            $this->multicell(0,5,"- ".$s['docSupAretourner']);
        }
            
        $this->setleftMargin(10);
		$this->ln(5);

        $this->ln(5);       
        $foot = "Lors de la mise en place de votre contrat, des frais de transfert d'un montant de 195 euros HT seront prélevés sur votre compte.\n\nVous en souhaitant bonne réception, je vous remercie d'avoir choisi ".$this->societe["societe"]." et demeure à votre entière disposition pour tout complément d'information.
        \nVeuillez agréer, ".$this->contact["civilite"]." ".$this->contact["nom"].", l'expression de mes salutations distinguées.";    
        $this->multicell(0,5,$foot);
        
        $this->ln(15);  
        $this->setfont('arial','I',12);                 
        $this->multicell(0,5,ATF::user()->select(ATF::usr()->getId() , "prenom")." ".ATF::user()->select(ATF::usr()->getId() , "nom"));
			
	}

	/**
    * Génère le contrat signécourrier type
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param $id id de la commande	
	* @date 03-03-2013
	*/
	public function ctSigne($id,$s){
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		$this->envoiContrat = true;
        $this->noPageNo = true;
				
		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);
		
		$this->open();
		$this->addpage();
		$this->sety(80);   
		
        $this->multicell(0,5,"Objet : contrat de location ".$this->societe["societe"]."");
        $this->setFont("arial","I",10);
        $this->multicell(0,5,"Lille, le ".ATF::$usr->date_trans($s['date']?$s['date']:date("Y-m-d"),"force",true));
        $this->setFont("arial","",12);
                
        $this->ln(5);
        $this->multicell(0,5,ATF::$usr->trans($this->contact['civilite']).", ");
        $this->ln(15);               

		$this->multicell(0,5,"J'ai le plaisir de vous retourner votre original du contrat de location ".$this->societe["societe"]." signé par nos soins, ainsi que la ou les facture(s) correspondante(s).");	
		$this->ln(15);
		$this->multicell(0,5,"Vous en souhaitant bonne réception, je vous remercie d'avoir choisi ".$this->societe["societe"]." et demeure à votre entière disposition pour tout complément d'information.");
		$this->ln(10);
		$this->multicell(0,5,"Veuillez agréer, ".$this->contact["civilite"]." ".$this->contact["nom"].", l'expression de mes salutations distinguées.");	
		$this->multicell(0,5,$foot);
		
		$this->ln(15);	
		$this->setfont('arial','I',12);					
		$this->multicell(0,5,ATF::user()->select(ATF::usr()->getId() , "prenom")." ".ATF::user()->select(ATF::usr()->getId() , "nom"));
		
	}

	/**
    * Génère le courrier de restitution
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la commande	
	* @date 20-08-2015
	*/
	public function CourrierRestitution($id,$s){
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }

		$this->envoiContrat = true;
        $this->noPageNo = true;
				
		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		
		$this->open();
		$this->addpage();
		$this->sety(80);   
		
		$this->setFont("arial","BIU",12);
        $this->multicell(0,5,"Objet : Restitution de matériel ");
        $this->setFont("arial","B",12);
        $this->cell(0,5,"RAR : ".$s["rar"],0,1);
        $this->setFont("arial","I",10);
        $this->multicell(0,5,"Lille, le ".ATF::$usr->date_trans($s['date']?$s['date']:date("Y-m-d"),"force",true));
        $this->setFont("arial","",10);
                
        $this->ln(5);
        $this->multicell(0,5,ATF::$usr->trans($this->contact['civilite']).", ");           
        $this->multicell(0,5,"En réponse à votre courrier du ".ATF::$usr->date_trans($this->commande['date_demande_resiliation']?$this->commande['date_demande_resiliation']:date("Y-m-d"),"force",true).", nous vous confirmons par la présente prendre acte de la résiliation de votre contrat ".ATF::affaire()->select($this->affaire['id_affaire'], "ref")."-".ATF::societe()->select($this->client["id_societe"], "code_client").".");	
		$this->ln(5);
		$this->multicell(0,5,"Conformément à l’article 14.2 des Conditions Générales, nous vous invitons à nous restituer le matériel du contrat ".ATF::affaire()->select($this->affaire['id_affaire'], "ref")."-".ATF::societe()->select($this->client["id_societe"], "code_client")." au plus tard le ".ATF::$usr->date_trans(implode('-', array_reverse(explode('/', $s['date_echeance']))),"force",true).", date d’échéance de votre contrat à :");	
		$this->ln(5);

		$this->setFont("arial","B",12);
		$this->cell(0,5,"CLEODIS BROKE SYSTEMES ",0,1,'C');	
		$this->setFont("arial","",10);
		$this->cell(0,5,"11 Rue du Général Mocquery ",0,1,'C');
		$this->cell(0,5,"37550 SAINT AVERTIN",0,1,'C');
		$this->ln(10);

		$this->multicell(0,5,"A défaut de restitution à la date indiquée ci-dessus et conformément à l’article 14.3 des Conditions Générales, vous seriez redevable d’une indemnité égale aux loyers jusqu’à la restitution effective des Equipements.");	
		$this->ln(5);

		$this->multicell(0,5,"Dans un souci de qualité et de suivi logistique, nous vous remercions de noter sur chacun des colis l'adresse complète de votre société et de confirmer par mail la date de dépôt des équipements par le transporteur aux adresses suivantes :");	
		$this->ln(5);

		$this->setFont("arial","U",10);
		$this->cell(0,5,"- cleodis@brokesystemes.com",0,1,'L');
		$this->cell(0,5,"- ".ATF::user()->select(ATF::usr()->getId() , "email"),0,1,'L');
		$this->setFont("arial","",10);
		$this->ln(5);

		$this->multicell(0,5,"Nous vous informons que  le déchargement se fait sur un quai.\nLes équipements ne peuvent être déchargés que d’un camion avec hayon ou camion de marchandises et ce afin de protéger le matériel restitué.");	
		$this->ln(5);

		$this->setFont("arial","B",10);
		$this->multicell(0,5,"Nous vous rappelons que jusqu’à la date d’échéance incluse vous pouvez demander l’évolution de votre contrat actuel. Vous bénéficierez ainsi de nouveaux équipements plus performants et à nouveau sous garantie.");	
		$this->setFont("arial","",10);
		$this->ln(5);
		

		$this->multicell(0,5,"Veuillez agréer, Monsieur, l’expression de mes salutations distinguées.");				
		$this->ln(10);	
		$this->setfont('arial','I',12);					
		$this->multicell(0,5,ATF::user()->select(ATF::usr()->getId() , "prenom")." ".ATF::user()->select(ATF::usr()->getId() , "nom"));
		
	}

	/**
    * Génère la lettre de banque
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la commande	
	* @date 05-11-2015
	*/
	public function lettreSGEF($id,$s){
		$this->unsetHeader();
		$this->unsetFooter();		
		
		$this->open();	

		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);

		ATF::loyer()->q->reset()->where("id_affaire", $this->devis['id_affaire']);
		$this->loyer = ATF::loyer()->select_all();

		ATF::demande_refi()->q->reset()->where("id_affaire", $this->devis["id_affaire"])
									   ->where("etat", "valide");
		$this->demande_refi = ATF::demande_refi()->select_row();
		foreach ($this->loyer as $key => $value) {	$duree += $value["duree"]; }

		ATF::facturation()->q->reset()->where("id_affaire", $this->devis["id_affaire"])->addOrder("date_periode_debut");
		if($this->demande_refi["date_cession"]) ATF::facturation()->q->where("date_periode_debut", $this->demande_refi["date_cession"],"AND",false, ">=");
		$facturations = ATF::facturation()->select_all();

		$cleodis = ATF::societe()->select(246);

		$this->addpage();
		$this->setfont('arial',"B",10);
		$this->cell(0,15," CONTRAT DE VENTE ",0,1,"C");

		$this->setfont('arial',"",9);
		$this->cell(0,5,"Entre les soussignées :",0,1);

		$this->setfont('arial',"B",8);
		$this->cell(0,5,"(3)          CLEODIS,",0,1);
		$this->setleftMargin(25);
		$this->setfont('arial',"",8);
		$this->multicell(0,4,"Société CLEODIS au capital de ".$cleodis["capital"]." euros, dont le siège social est situé au ".$cleodis["adresse"]." ".$cleodis["cp"]." ".$cleodis["ville"].", ayant pour numéro unique d’identification ".$cleodis["siren"]." R.C.S. LILLE, représentée par la ou les personnes mentionnées en page de signature des présentes, dûment habilitée(s) à l’effet des présentes,\n\nCi-après dénommée « CLEODIS»,");
		$this->setfont('arial',"B",8);
		$this->cell(0,5,"D’une part,",0,1);


		$this->setleftMargin(10);
		$this->setfont('arial',"B",8);
		$this->cell(0,5,"(4)           FRANFINANCE LOCATION,",0,1);
		$this->setleftMargin(25);
		$this->setfont('arial',"",8);
		$this->multicell(0,4,"Société par actions simplifiée unipersonnelle au capital de 23 088 000 euros, dont le siège social est situé 59, avenue de Chatou – 92853 Rueil-Malmaison Cedex, ayant pour le numéro unique d’identification 314 975 806 RCS Nanterre, intermédiaire en assurance inscrit à l’ORIAS n°07 032 526 – www.orias.fr, représentée par la ou les personnes mentionnées en page de signature, dûment habilitée(s) à l’effet des présentes,\n\nCi-après dénommée « SGEF France »,");
		$this->setfont('arial',"B",8);
		$this->cell(0,5,"D’autre part,",0,1);

		$this->ln(3);
		$this->setleftMargin(10);
		$this->setfont('arial',"",8);
		$this->multicell(0,4,"Conformément à la convention de coopération en date du 18.09.2014, CLEODIS vend à SGEF France qui l’accepte l’Equipement grevé du Contrat de Financement décrit ci-après, aux conditions financières suivantes :",0);

		$this->setfont('arial',"B",8);
		$this->setleftMargin(25);
		$this->ln(5);
		$this->multicell(0,5,"1. CONTRAT DE FINANCEMENT");
		$this->ln(5);
		$this->setleftMargin(10);
		$this->setfont('arial',"",8);
		$this->cell(0,5,"Numéro du Contrat de Financement ",0,1);
		$this->multicell(0,4,"Désignation du Client : ".$this->client["societe"].", ".$this->client["structure"]." au capital de ".$this->client["capital"]." Euros – ".$this->client["siren"]." R.C.S. LILLE.",0);
		

		$siege = $this->client["adresse_siege_social"];
		if(!$this->client["adresse_siege_social"]){
			$siege = $this->client["adresse"];
			if($this->client["adresse_2"]) $siege .= " ".$this->client["adresse_2"];
			if($this->client["adresse_3"]) $siege .= " ".$this->client["adresse_3"];
			$siege .= " - ".$this->client["cp"]." ".$this->client["ville"];
		}

		$this->cell(0,5,"Siège social : ".$siege,0,1);
		$this->ln(5);
		$this->cell(0,5,"Date de la signature du Contrat de Financement : ",0,1);
		$this->cell(0,5,"Durée du Contrat de Financement : ".$duree." ".$this->loyer[0]["frequence_loyer"]." du ".date("d / m / Y", strtotime($facturations[0]["date_periode_debut"]))." au ".date("d / m / Y", strtotime($facturations[count($facturations)-1]["date_periode_fin"])) ,0,1);

		$this->ln(2);
		$this->cell(0,5,"Echéancier des loyers H.T. :",0,1);
		foreach ($this->loyer as $key => $value) {
			$ht = $value["loyer"]+$value["assurance"]+$value["frais_de_gestion"];
			$this->cell(0,4,$value["duree"]." Loyers H.T : ".$ht." euros H.T.",0,1);
		}
		$this->cell(0,4,"à majorer de la TVA au taux en vigueur.",0,1);
		$this->ln(2);
		$periode = $this->loyer[0]["frequence_loyer"];

		$y = $this->getY();

		if($periode === "mois"){ $this->image(__PDF_PATH__."cap/caseCheck.jpg",41,$this->getY(),4);
		}else{ $this->image(__PDF_PATH__."cap/case.jpg",41,$this->getY(),4); }	
		if($periode === "semestre"){ $this->image(__PDF_PATH__."cap/caseCheck.jpg",60,$this->getY(),4);
		}else{ $this->image(__PDF_PATH__."cap/case.jpg",60,$this->getY(),4); }		
		if($periode === "trimestre"){ $this->image(__PDF_PATH__."cap/caseCheck.jpg",81,$this->getY(),4);
		}else{ $this->image(__PDF_PATH__."cap/case.jpg",81,$this->getY(),4); }			
		if($periode === "an"){ $this->image(__PDF_PATH__."cap/caseCheck.jpg",102,$this->getY(),4);
		}else{ $this->image(__PDF_PATH__."cap/case.jpg",102,$this->getY(),4); }			

		$this->image(__PDF_PATH__."cap/caseCheck.jpg",139,$this->getY(),4);
		$this->image(__PDF_PATH__."cap/case.jpg",156,$this->getY(),4);
		$this->cell(0,4,"Périodicité des loyers :         Mensuels        Semestriels        Trimestriels        Annuels, payables terme        A Echoir        Echu",0,1);
		$this->ln(2);
		$this->cell(0,4,"Lieu d'installation (Si différent du siège social) : ",0,1);
		$this->ln(5);

		$this->setfont('arial',"B",8);
		$this->setleftMargin(25);
		$this->multicell(0,5,"2. VENTE DE L’EQUIPEMENT EN APPLICATION DE LA PARTIE 3 (CESSION DES EQUIPEMENTS) DE LA CONVENTION");
		$this->ln(5);
		$this->setleftMargin(10);
		$this->setfont('arial',"",8);
		$this->multicell(0,5,"Désignation de l’Equipement : ".$this->demande_refi["description"]."\nPrix de Vente : ".number_format($this->demande_refi["loyer_actualise"], 2, " ", "," )." euros H.T. à majorer de la TVA au taux en vigueur.");
		$this->ln(5);

		$this->setfont('arial',"B",8);
		$this->setleftMargin(25);
		$this->multicell(0,5,"3. REVENTE DE L’EQUIPEMENT EN APPLICATION DE L’ARTICLE 21 (REVENTE DES EQUIPEMENTS EN FIN DE CONTRAT) DE LA CONVENTION");
		$this->ln(5);
		$this->setleftMargin(10);
		$this->setfont('arial',"",8);
		$this->cell(0,4,"Date de la revente : ".date("d / m / Y", strtotime($this->commande["date_evolution"])),0,1);
		$this->cell(0,4,"Prix de Revente ".$this->demande_refi["valeur_residuelle"]." euros H.T. à majorer de la TVA en vigueur.",0,1);
		$this->ln(5);
		$this->cell(0,4,"Fait en deux (2) exemplaires, à Rueil-Malmaison, le ".date("d / m / Y", strtotime($this->demande_refi["date"])),0,1);
		$this->ln(5);
		$this->setfont('arial',"B",8);
		$this->cell(90,6,"Pour FRANFINANCE LOCATION",0,0,"C");
		$this->cell(90,6,"Pour CLEODIS",0,1,"C");
		$this->setfont('arial',"",8);
		$this->cell(90,6,"Nom, Prénom du signataire :",0,0);
		$this->cell(90,6,"Nom, Prénom du signataire :",0,1);

		$this->cell(90,6,"____________________________________________",0,0);
		$this->cell(90,6,ATF::user()->nom(ATF::$usr->get('id_user')),0,1);

		$this->cell(90,6,"Qualité :",0,0);
		$this->cell(90,6,"Qualité :",0,1);

		$this->cell(90,6,"____________________________________________",0,0);
		$this->cell(90,6,"____________________________________________",0,1);
	}


	/**
    * Génère la lettre de banque Belfius pour Cleodis BE
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la commande	
	* @date 05-11-2015
	*/
	public function lettreBelfius($id,$s,$tu=false){
		$this->unsetHeader();
		$this->unsetFooter();		
		
		$this->open();	

		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);

		ATF::loyer()->q->reset()->where("id_affaire", $this->devis['id_affaire']);
		$this->loyer = ATF::loyer()->select_all();

		ATF::demande_refi()->q->reset()->where("id_affaire", $this->devis["id_affaire"])
									   ->where("etat", "valide");
		$this->demande_refi = ATF::demande_refi()->select_row();
		foreach ($this->loyer as $key => $value) {	$duree += $value["duree"]; }

		ATF::facturation()->q->reset()->where("id_affaire", $this->devis["id_affaire"])->addOrder("date_periode_debut");
		if($this->demande_refi["date_cession"]) ATF::facturation()->q->where("date_periode_debut", $this->demande_refi["date_cession"],"AND",false, ">=");
		$this->facturations = ATF::facturation()->select_all();

		ATF::facture()->q->reset()->where("facture.id_affaire", $this->devis["id_affaire"])->where("type_facture","refi");
		$this->facture_refi = ATF::facture()->select_row();


		$this->cleodis = ATF::societe()->select(4225);


		$this->cleodissiege = $this->cleodis["adresse_siege_social"];
		if(!$this->cleodis["adresse_siege_social"]){
			$this->cleodissiege  = $this->cleodis["adresse"];
			if($this->cleodis["adresse_2"]) $this->cleodissiege  .= " ".$this->cleodis["adresse_2"];
			if($this->cleodis["adresse_3"]) $this->cleodissiege  .= " ".$this->cleodis["adresse_3"];
			$this->cleodissiege  .= " - ".$this->cleodis["cp"]." ".$this->cleodis["ville"];
		}

		$this->date = date("d/m/Y");

		if(ATF::_r("tu") == "OK") $this->date = "08/12/2015";

		$this->lettreBelfius1();
		$this->cell(0,4,"Fait en deux exemplaires à Bruxelles, le ".$date ,0,1);

		$this->ln(10);
		$this->cell(90,3,"CLEODIS  s.a.",0,0,"C");
		$this->cell(90,3,"BELFIUS LEASE SERVICES s.a.",0,1,"C");

		$this->lettreBelfius2();
		$this->cell(0,4,"Fait en deux exemplaires à Bruxelles, le ".$date ,0,1);

		$this->ln(10);
		$this->cell(90,3,"CLEODIS  s.a.",0,0,"C");
		$this->cell(90,3,"BELFIUS LEASE SERVICES s.a.",0,1,"C");
	}

	public function lettreBelfius1(){
		$this->addpage();
		$this->setfont('times',"BU",12);
		$this->multicell(0,5,"CONVENTION D’ACHAT DE MATERIEL INFORMATIQUE FAISANT L’OBJET DU CONTRAT DE LOCATION CLEODIS N°".$this->affaire["ref"],0);
		$this->ln(5);
		$this->setfont('times',"U",10);
		$this->cell(0,5,"I. EXPOSE",0,1);
		$this->ln(2);
		$this->setfont('times',"",9);
		$this->multicell(0,4,"Le ".$this->date.", la s.a. CLEODIS, ayant son siège social ".$this->cleodissiege.", ci-après dénommé 'CLEODIS', a conclu le contrat de location précité portant sur le matériel informatique plus amplement décrit dans l’article des conditions particulières dudit contrat, avec la société ".$this->client["societe"].", ci-après 'le locataire'.\nLe matériel objet de ce contrat est la propriété de CLEODIS." ,0);
		$this->multicell(0,4,"L’intention de CLEODIS est de vendre à BELFIUS LEASE SERVICES s.a., ayant sons siège social Place Rogier 11 à 1210 Bruxelles, ci-après dénommé 'BELFIUS LEASE SERVICES', le matériel objet de ce contrat moyennant la cession à cette dernière des droits résultant du contrat de location n°".$this->affaire["ref"]." et des loyers y relatifs conformément à l’article 10.2 des conditions générales de ce contrat au moyen d’une convention de cession de contrat séparée." ,0);
		$this->multicell(0,4, "Cet achat et cession ont lieu conformément à la convention cadre portant sur la vente de matériel et cession de loyers signée par les parties dénommées ci-dessus en date du 08 juin 2015 et sous réserve des conditions particulières suivantes : ",0);
		$this->ln(5);
		$this->setfont('times',"U",9);
		$this->cell(0,5,"II CONVENTION D’ACHAT",0,1);
		$this->ln(2);
		$this->setfont('times',"",9);
		$date_facture = ".........";
		if($this->facture_refi["date_previsionnelle"]) $date_facture = date("d/m/Y", strtotime($this->facture_refi["date_previsionnelle"]));
		$this->multicell(0,4,"1. Par la présente, CLEODIS vend à BELFIUS LEASE SERVICES le matériel décrit dans l’article des conditions particulières du contrat de location CLEODIS précité, pour le prix de ".$this->demande_refi["loyer_actualise"]."EUR hors TVA, payable le ".$date_facture,0);
		$this->cell(0,4,"Ce prix a été calculé sur base des éléments suivants :",0,1);
		$this->ln(5);
		$prix = $this->loyer[0]["loyer"]+$this->loyer[0]["assurance"]+$this->loyer[0]["frais_de_gestion"];
		$freq = "mensuels";
		if($this->loyer[0]["frequence_loyer"] == "trimestre") $freq = "trimestriels";
		if($this->loyer[0]["frequence_loyer"] == "semestre") $freq = "semestriels";
		if($this->loyer[0]["frequence_loyer"] == "an") $freq = "annuels";

		$this->setLeftMargin(15);
		$this->image(__PDF_PATH__.'cleodisbe/puce.png',12,$this->getY()+1,3);
		$this->cell(0,4,$this->loyer[0]["duree"]." loyers ".$freq." et anticipatifs de ".$prix." EUR hors TVA",0,1);
		$this->image(__PDF_PATH__.'cleodisbe/puce.png',12,$this->getY()+1,3);
		$this->cell(0,4,"une valeur résiduelle de 15 EUR hors TVA",0,1);
		$this->setLeftMargin(10);
		$this->cell(0,4,"La cession prend effet au ".date("d/m/Y", strtotime($this->demande_refi["date_cession"])),0,1);
		$this->setLeftMargin(10);
		
		$this->ln(3);
		$this->multicell(0,4,"2.  CLEODIS garantit à BELFIUS LEASE SERVICES qu’elle lui cède la pleine et entière propriété de ce matériel informatique lequel n’est grevé d’aucun privilège, nantissement ou autre sûreté.",0);
		$this->ln(5);
		$this->multicell(0,4,"3.  Ce prix sera payé par BELFIUS LEASE SERVICES à CLEODIS le jour de prise d’effet de la cession, tel  que mentionné ci-dessus, si les documents suivants ont bien été remis à BELFIUS LEASE SERVICES :",0);
		$this->ln(2);

		$liste = array("convention de cession du contrat de location et des ".$this->loyer[0]["duree"]." loyers ".$freq." de ".$prix." EUR, dûment signée par CLEODIS;",
						"un exemplaire original du contrat de location n° ".$this->affaire["ref"]." conclu entre CLEODIS et son client, ainsi que de toutes ses parties et annexes;",
						"copie de la présente convention d’achat dûment signée par les personnes habilitées;",
						"la facture de vente de l’équipement, établie par CLEODIS au nom de BELFIUS LEASE SERVICES pour un montant de ".number_format($this->demande_refi["prix"],2,","," ")." EUR hors TVA; ",
						"copie de la notification de la cession au locataire;",
						"l’avis de domiciliation, dûment signé par le locataire.",
						"copie de la notification au propriétaire de l’immeuble"			
				   );
		$this->setLeftMargin(15);
		foreach ($liste as $key => $value) {
			$this->image(__PDF_PATH__.'cleodisbe/puce.png',12,$this->getY()+1,3);
			$this->multicell(0,4,$value,0);
			$this->ln(1);
		}
		$this->ln(5);
		$this->setLeftMargin(10);
		$this->multicell(0,4,"4.  Dès à présent et dans la mesure où le contrat de location a connu un déroulement normal, Belfius Lease Services s’engage à revendre, et CLEODIS à racheter, le matériel au terme de la période de location initiale, ainsi que de céder le contrat de location à CLEODIS pour le prix global de  15 EUR hors  TVA, le tout sans préjudice des obligations souscrites dans la convention cadre en cas de résiliation anticipée. Tous les frais de démontage et d’enlèvement seront à charge de CLEODIS.",0);
		$this->ln(3);
		$this->multicell(0,4,"La propriété de cet équipement informatique sera transférée à CLEODIS après paiement complet du prix de vente majoré des taxes en vigueur au moment de la vente. Ce paiement devra se faire le premier jour ouvrable suivant la fin de la période de location initiale.",0);
		$this->ln(5);
		
	}

	public function lettreBelfius2(){
		$this->addpage();
		$this->setfont('times',"BU",12);
		$this->multicell(0,5,"CONVENTION DE CESSION D’UN CONTRAT DE LOCATION ",0,"C");
		$this->ln(5);
		$this->setfont('times',"U",10);
		$this->cell(0,5,"I. EXPOSE DES FAITS",0,1);
		$this->ln(2);
		$this->setfont('times',"",9);
		$this->multicell(0,4,"La s.a. CLEODIS, ayant son siège social ".$cleodis["adresse_siege_social"].", a conclu le ".date("d/m/Y", strtotime($this->commande["date"]))." avec la société ".$this->client["societe"].", le contrat de location n° ".$this->affaire["ref"]." portant sur des équipements informatiques, plus amplements décrits dans les conditions particulières  dudit contrat et dont une copie est jointe en annexe pour faire partie intégrante de cette cession.",0);
		$this->ln(5);
		$this->multicell(0,4,"La s.a. CLEODIS vend à la s.a. BELFIUS LEASE SERVICES, ayant son siège social Place Rogier 11 1210 Bruxelles, ces équipements et désire céder à cette dernière le contrat de location y relatif, conformément aux stipulations de l’article 10.2 des conditions générales de ce contrat.",0);
		$this->ln(5);
		$this->multicell(0,4,"La s.a. BELFIUS LEASE SERVICES accepte cette cession.",0);

		$this->ln(5);
		$this->setfont('times',"U",10);
		$this->cell(0,5,"II. CONVENTION",0,1);
		$this->setfont('times',"",9);


		$this->ln(5);
		$this->multicell(0,4,"1.  La s.a. CLEODIS cède à la s.a. BELFIUS LEASE SERVICES, qui accepte, tous les droits découlant du contrat de location n° ".$this->affaire["ref"]." conclu avec ".$this->client["societe"].", qui reprend exhaustivement l’ensemble des accords intervenus entre CLEODIS et le locataire et portant sur le matériel décrit aux conditions particulières.",0);
	
		$this->ln(2);
		$this->multicell(0,4,"La cession prendra effet le ".date("d/m/Y", strtotime($this->demande_refi["date_cession"]))." et emporte cession des loyers relatifs à la période du ".date("d/m/Y", strtotime($this->facturations[0]["date_periode_debut"]))." au ".date("d/m/Y", strtotime($this->facturations[count($this->facturations)-1]["date_periode_fin"])),0);
		$this->ln(5);
		$this->multicell(0,4,"Cette cession a lieu conformément à la convention cadre portant sur la vente de matériel et cession de loyers conclue entre nos sociétés et date du 08 juin 2015.",0);
		$this->ln(5);
		$this->multicell(0,4,"2. La s.a. CLEODIS certifie que le matériel a bien été livré au lieu indiqué dans le contrat de location et a été déclaré conforme par le locataire. Faute de quoi, BELFIUS LEASE SERVICES conserve un recours auprès de CLEODIS pour la récupération du montant des loyers non perçus.  ",0);
		$this->ln(5);
		$this->multicell(0,4,"3. Compte tenu des termes du contrat de location, et plus particulièrement de l’article   , il est entendu que la s.a. CLEODIS reste tenue envers le locataire à l’égard de toute obligation pouvant découler de la construction, de la livraison et de l’installation de l’équipement, ainsi que de l’exécution de la clause d’évolution (article 12 des conditions générales). CLEODIS est tenue d’informer BELFIUS LEASE SERVICES endéans la semaine de toutes les correspondances qui lui parviendront suite à l’application de l’article 6.3  des conditions générales des contrats de location cédés. ",0);
		$this->ln(5);
		$this->multicell(0,4,"4. La s.a. CLEODIS marque dès lors son entier accord quant au versement -par exclusivité et par privilège- de toutes sommes pouvant lui revenir du chef de ce contrat de location sur le compte n° 552-2961101-30 de BELFIUS LEASE SERVICES.",0);

		$this->multicell(0,4,"Pour lui assurer l’encaissement du montant des créances, objet de la présente cession, CLEODIS autorise BELFIUS LEASE SERVICES -pour autant que de besoin- à effectuer toutes démarches de vérification et, le cas échéant, à entamer toute procédure qui pourrait être utile à cette fin. CLEODIS apportera son support total et autorise le débiteur cédé à donner à BELFIUS LEASE SERVICES toutes informations sur les créances qu’elle possède contre ledit débiteur et, en général, tous renseignements quelconques que jugerait utile de connaître BELFIUS LEASE SERVICES. ",0);
		$this->ln(5);
		$this->multicell(0,4,"5. Tous frais ou droits quelconques dus en vertu de cette cession sont à charge de CLEODIS.",0);
		
		$this->ln(15);

	}

	/**
    * Génère le mandat SEPA
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la commande	
	* @date 18-12-2013
	*/
	public function mandatSepa($id,$s){
		
		$this->unsetHeader();
		$this->unsetFooter();		
		
		$this->open();	
		$this-> datamandatSepa($id,$s);		
		$this-> datamandatSepa($id,$s);

	}	

	public function datamandatSepa($id,$s){
		
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }



		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);

		$this->addpage();
		$this->setfont('arial',"",8);
		$this->multicell(0,15, "REFERENCE UNIQUE DU MANDAT ....");


		$this->setfont('arial',"I",7);
		$textLeft = "En signant ce formulaire de mandat, vous autorisez (A) le créancier à envoyer des instructions à votre banque pour débiter votre compte, et (B) votre banque à débiter votre compte conformément aux instructions du créancier.
Vous bénéficiez du droit d’être remboursé par votre banque selon les conditions décrites dans la convention que vous avez passée avec elle. Une demande de remboursement doit être présentée dans les 8 semaines suivant la date de débit de votre compte pour un prélèvement autorisé. Vos droits concernant le présent mandat sont expliqués dans un document que vous pouvez obtenir auprès de votre banque.

Le présent mandat est donné pour le débiteur en référence, il sera utilisable pour les contrats conclus avec celui-ci et aux termes desquels le débiteur donne autorisation de paiement en utilisant le présent mandat.";

		$textRight = "Les informations contenues dans le présent mandat, qui doit être complété, sont destinées à n'être utilisées par le créancier que pour la gestion de sa relation avec son client. Elles pourront donner lieu à l'exercice, par ce dernier, de ses droits d'opposition, d’accès et de rectification tels que prévus aux articles 38 et suivants de la Loi n° 78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés.

En signant ce mandat le débiteur, par dérogation à la règle de pré-notification de 14 jours, déclare que le délai de pré notification des prélèvements par le créancier est fixé à 2 jours avant la date d’échéance du prélèvement.

Nonobstant toute indication contraire, le mandat de prélèvement signé par le client est constitutif d'une autorisation
de prélèvement nationale jusqu'au 1er février 2014 sauf à ce que le créancier ait informé le client du traitement du mandat sous la norme SEPA.

Les champs marqués sont obligatoires (*) _ Ne compléter que les champs incorrects ou manquants.";

		$y = $this->getY();
		$this->multicell(90,3, $textLeft ,0, "J");

		$this->setfont('arial',"B",7);
		$this->Ln(10);
		$this->multicell(90,3, "Référence contrat/convention/client :" ,0, "J");
		$this->setfont('arial',"I",7);

		$this->setY($y);
		$this->setX(106);
		$this->multicell(95,3, $textRight ,0 , "L");

		$point = ".....................................................................................................";

		$this->setfont('arial',"",8);
		$this->Ln(5);
		$this->multicell(0,5, "1- Données débiteur" ,1, "C");
		$this->Ln(2);
		$this->multicell(0,5, "NOM_PRENOM/ RAISON SOCIALE*       ".$this->client["structure"]."  ".$this->client["societe"] ,0, "L");
		$this->multicell(0,5, "ADRESSE*       ".$this->client["adresse"]."  ".$this->client["adresse1"]."  ".$this->client["adresse2"] ,0, "L");
		$this->multicell(100,5, "CODE POSTAL*       ".$this->client["cp"] ,0, "L");
		$this->Ln(-5);

		$this->setX(110);
		$this->multicell(100,5, "VILLE *       ".$this->client["ville"] ,0, "L");
		$this->multicell(0,5, "PAYS*       ".strtoupper(ATF::pays()->select($this->client["id_pays"], "pays")) ,0, "L");
		$this->multicell(0,5, "E-mail       ".$this->client["email"] ,0, "L");

		$this->Ln(5);
		$this->multicell(0,5, "2 - Informations coordonnées bancaires" ,1, "C");
		$this->Ln(2);
		$this->multicell(0,5, "COORDONNEES DE VOTRE COMPTE- IBAN*       ".$point ,0, "L");
		$this->multicell(0,5, "BIC - SWIFT - CODE INTERNATIONAL D'IDENTIFICATIONS DE VOTRE BANQUE*  ".$point ,0, "L");
		$this->multicell(0,5, "SIREN / SIRET       ".$point ,0, "L");


		$this->Ln(5);
		$this->multicell(0,5, "3 - Information Créancier" ,1, "C");
		$this->Ln(5);
	
		$this->setfont('arial',"BI",10);
		$this->multicell(80,5, $this->societe["societe"]." \nICS/SCI: ".__ICS__." \n".$this->societe["adresse"]." \n".$this->societe["cp"]." ".$this->societe["ville"]." \n".strtoupper(ATF::pays()->select($this->societe["id_pays"], "pays")) ,0, "L");
		$this->setfont('arial',"B",8);

		$this->Ln(-25);
		$this->setX(90);
		$this->multicell(90,5 , "Ou pour tout établissement financier ou loueur secondaire : 
			".$point."
			".$point."
			".$point."
			".$point);

		$this->setfont('arial',"",8);
		$this->Ln(5);
		$this->multicell(0,5, "4 - Information type de paiement" ,1, "C");
		$this->Ln(5);
		$y = $this->getY();
		$this->Cell(50,5, "Type de paiement" ,0);
		$this->Cell(70,5, "Paiement récurrent / répétitif " ,0);
		
		$this->Cell(70,5, "Paiement ponctuel " ,0);


		$this->image(__PDF_PATH__.'cleodis/caseCheck.jpg',98,$y,5);
		$this->image(__PDF_PATH__.'cleodis/case.jpg',155,$y,5);

		$this->Ln(10);

		$this->multicell(0,5, "5 - Signature(s)" ,1, "C");
		$this->Ln(2);
		$this->multicell(80,5, "Signé à" ,0, "L");
		$this->multicell(80,5, "Date *" ,0, "L");
		$this->Ln(-10);
		$this->setX(90);
		$this->Cell(10,5, "Signature (s)*" ,0);
		$this->setX(110);
		$this->Cell(60,30, "" ,1,1);

		$this->Ln(10);
		$this->setfont('arial',"",12); 
		if(ATF::$codename == "cleodisbe"){ $this->multicell(0,5, "MANDAT DE PRELEVEMENT SEPA" ,1, "C"); }
		else{ $this->multicell(0,5, "MANDAT DE PRELEVEMENT SEPA (Joindre un RIB)" ,1, "C"); }
		
	}

	/**
	* Génère le Courrier classique
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $id id de la commande	
	* @date 06-03-2014
	*/
	public function envoiCourrierClassique($id,$s){
				
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		$this->envoiContrat = true;
        $this->noPageNo = true;
		
		$this->commande = ATF::commande()->select($id);	
		$this->client = ATF::societe()->select($this->commande['id_societe']);        	
		$this->devis = ATF::devis()->select($this->commande["id_devis"]);
		$this->affaire = ATF::affaire()->select($this->devis["id_affaire"]);
		$this->contact = ATF::contact()->select($this->devis['id_contact']);
		
		$this->open();
		$this->addpage();
		$this->sety(80);   
		
        $this->multicell(0,5,"Objet : contrat de location ".$this->societe["societe"]."");
        $this->setFont("arial","I",10);
        $this->multicell(0,5,"Lille, le ".ATF::$usr->date_trans($s['date']?$s['date']:date("Y-m-d"),"force",true));
        $this->setFont("arial","",12);
                
        $this->ln(5);
        $this->multicell(0,5,ATF::$usr->trans($this->contact['civilite']).", ");
        $this->ln(5);				
		
		if($this->affaire["nature"] == "AR"){
			$this->multicell(0,5,"J'ai le plaisir de vous transmettre votre contrat de location ".$this->societe["societe"]." qui annule et remplace le précédent.");
		}else{
			$this->multicell(0,5,"J'ai le plaisir de vous transmettre votre contrat de location ".$this->societe["societe"].".");
		}
		$this->ln(5);
		$this->setfont("arial","U",12);
		$this->multicell(0,5,"Vous trouverez ci-joints les éléments suivants :");
        $this->ln(5);
		$this->setfont('arial','',11);	
		$this->setx(15);		
		$phrase = "- 3 exemplaires du contrat de location\n";  
		$phrase .= "- 3 exemplaires du procès verbal de livraison\n";  
		$phrase .= "- 2 exemplaires de mandats SEPA ";
		$this->multicell(0,5,$phrase);
		$this->ln(5);
		$this->setx(10);
		$this->setfont("arial","U",12);
		$this->multicell(0,5,"Je vous remercie de bien vouloir nous retourner :");
		$this->setfont('arial','',11); 
        $this->ln(5);
		$this->setx(15);			
		$phrase  = "- l'ensemble des documents signés et paraphés avec le cachet de la société\n"; 		   
		$phrase .= "- RIB mentionnant le nom de la banque \n";
		$phrase .= "- la copie recto/verso d'une pièce d'identité";   
		$this->multicell(0,5,$phrase);
		
		$this->setx(0);
		$this->ln(5);		
		$foot = "Vous en souhaitant bonne réception, je vous remercie d'avoir choisi ".$this->societe["societe"]." et demeure à votre entière disposition pour tout complément d'information.
		\nVeuillez agréer, ".$this->contact["civilite"]." ".$this->contact["nom"].", l'expression de mes salutations distinguées.";	
		$this->multicell(0,5,$foot);
		
		$this->ln(15);	
		$this->setfont('arial','I',12);					
		$this->multicell(0,5,ATF::user()->select(ATF::usr()->getId() , "prenom")." ".ATF::user()->select(ATF::usr()->getId() , "nom"));
	}


	public function devisoptic_2000($id){	

		$this->Addpage();
		$this->sety(10);
		$this->image(__PDF_PATH__.'cleodis/audioptic.jpg',5,10,35);
		$this->setfont('arial','B',12);
		$this->RoundedRect(50,10,110,30,5);
		$this->setleftMargin(50);
		$this->multicell(110,6,"Proposition locative CLEODIS N° ".$this->affaire['ref'],0,'C');
		$this->multicell(110,6,"pour ".$this->client['societe'],0,'C');
		$text = '';
		$affaires = "";
		if($this->affaire['nature']=="avenant"){
			$text = "Avenant au contrat ";
			$affaires = ATF::affaire()->select($this->affaire['id_parent'],'ref')."-".ATF::societe()->select($this->affaire['id_societe'],"code_client");
		}elseif($this->affaire['nature']=="AR"){
			$text = "Annule et remplace le contrat ";
			$this->AR = ATF::affaire()->getParentAR($this->affaire['id_affaire']);
			foreach ($this->AR as $key => $value) {
				$societe = ATF::affaire()->select($value["id_affaire"], "id_societe");
				$affaires .= $value["ref"]."-".ATF::societe()->select($societe,"code_client")." / ";
			}				
			$affaires = substr($affaires, 0, -3);
		}
		$this->multicell(110,6,$text.$affaires,0,'C');
		$this->multicell(110,6," Le ".date("d/m/Y",strtotime($this->devis['date'])),0,'C');
		$this->setleftMargin(10);
		$this->sety(40);
		$this->setfont('arial','BU',10);
		$this->cell(0,5,"1.     Contrat actuel",0,1,'L');
		$this->setfont('arial','',9);
		$parc = array();
		
		if($this->affaire['nature']=="avenant"){
			$this->setX(30);
			$this->cell(90,5,"=> Contrat n° ".ATF::affaire()->select($this->affaire['id_parent'],'ref')."-".ATF::societe()->select($this->affaire['id_societe'],"code_client"),0,0,'L');
			
			ATF::loyer()->q->reset()->where("id_affaire", $this->affaire['id_parent']);
			$loyer = ATF::loyer()->select_all();			
			foreach ($loyer as $k => $v) {
				if($k == 0){ $l = $loyer[$k]["loyer"]." Euros HT "; }else{ $l .= "puis ".$loyer[$k]["loyer"]." Euros HT "; }
			}
			$this->cell(90,5,"Loyer(s) HT : ".$l,0,1,'L');	
			

			ATF::parc()->q->reset()->addCondition("parc.id_affaire",$this->affaire['id_parent'])
						   ->addCondition("parc.existence","actif","AND")
						   ->addCondition("parc.etat","loue","AND",1)
						   ->addCondition("parc.etat","reloue","OR",1)
						   ->addOrder("parc.id_parc","asc");
			$parc = ATF::parc()->sa();
		}elseif($this->affaire['nature']=="AR"){
			ATF::parc()->q->reset();
			foreach ($this->AR as $key => $value) {
				$societe = ATF::affaire()->select($value["id_affaire"], "id_societe");
				$this->setX(30);
				$this->cell(70,5,"=> Contrat n° ".$value["ref"]."-".ATF::societe()->select($societe,"code_client"),0,0,'L');								
				ATF::loyer()->q->reset()->where("id_affaire", $value["id_affaire"]);
				$loyer = ATF::loyer()->select_all();				
				foreach ($loyer as $k => $v) {
					if($k == 0){ $l = $loyer[$k]["loyer"]." Euros HT "; }else{ $l .= "puis ".$loyer[$k]["loyer"]." Euros HT "; }
				}
				$this->cell(70,5,"Loyer(s) HT : ".$l,0,1,'L');	
				
			    ATF::parc()->q->addCondition("parc.id_affaire",$value['id_affaire'],"OR",2);    			    
			}
			ATF::parc()->q->addCondition("parc.existence","actif","AND")
						   ->addCondition("parc.etat","loue","AND",1)
						   ->addCondition("parc.etat","reloue","OR",1)
						   ->addOrder("parc.id_parc","asc");
			$parc = ATF::parc()->sa();
		}

		$repris = array();
		foreach ($parc as $key => $value) {
			$espion = false;			
			foreach ($this->lignes as $k => $v) {
				if($v["serial"] === $value["serial"]){	$espion = true;	}
			}
			if($espion === false){	$repris[] = $value; }
		}
		
		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->cell(0,5,"2.     Nouveaux équipements",0,1,'L');
		$this->setfont('arial','',9);			
		$nouveauParc = array();	
		foreach ($this->lignes as $key => $value) {
			$produit = ATF::produit()->select($value["id_produit"]);			
			$fabriquant = ATF::fabriquant()->select($produit["id_fabriquant"], "fabriquant");
			$sous_categorie = ATF::sous_categorie()->select($produit["id_sous_categorie"] , "sous_categorie");
			$produit["fabriquant"] = $fabriquant;
			$produit["sous_categorie"] = $sous_categorie;

			$this->lignes[$key]["select_produit"] = $produit;
			$value["select_produit"] = $produit;

			if($value["id_affaire_provenance"] == NULL){
				if($value["produit"] == "Migration SIMAG"){
					$nouveauParc[2][]["parc"] = "Prestation de DEPLOIEMENT SIMAG (facturée ".$value["prix_achat"]." € par ATS) comprenant :\n        - accompagnement du référent magasin et fourniture des guides\n        - paramétrage de l'espace magasin et reprise des données\n        - supports à J-1 mois lors de la bascule du module de gestion\n        - préparation du paramétrage du matériel\n        - le jour J : déplacement de l’accompagnateur et bascule SIMAG ";
				}elseif(!$value["ref_simag"]){					
					$nouveauParc[2][]["parc"] = $value["quantite"]." ".$value["produit"]." (".$value["select_produit"]["sous_categorie"]." ".$value["select_produit"]["fabriquant"].")";
				}else{
					$nouveauParc[0][0]["ref_simag"][] = $value["ref_simag"];
					$nouveauParc[0][]["parc"] = $value["quantite"]." ".$value["produit"];
				}				
			}
		}		
		
		$parc = $nouveauParc;

		$nouveauParc = array();
		$ref_simag = "";		
		for($i=0;$i<3;$i++){
			if($parc[$i]){				
				$nouveauParc[$i] = $parc[$i];				
			}			
		}
		$simag = $parc[0][0]["ref_simag"];
		if($simag){			
			$simag = array_map("unserialize", array_unique(array_map("serialize", $simag)));
			foreach ($simag as $key => $value) {
				if($key == 0){ $ref_simag = $value; }
				else{ $ref_simag .= "+ ".$value; }				
			}
		}

		foreach ($nouveauParc as $k => $v){
			$this->setX(30);			
			if($k == 0){
				$this->multicell(0,5,"- Selon devis ATS n° ".$ref_simag,0,1,'L');				
			}
			foreach ($v as $key => $value){
				$this->setX(30);	
				if($value["parc"]){
					if($k==0){ $this->setX(37);	}						
					$this->multicell(0,5,"- ".$value["parc"],0,1,'L');
				}	
			}
		}


		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->cell(0,5,"3.     Equipements que CLEODIS reprend",0,1,'L');
		$this->setfont('arial','',9);
		$ref = $repris[0]["ref"];
		$qte = 0;


		if(!empty($repris)){
			foreach ($repris as $key => $value) {				
				$this->setX(30);						
				if($value["ref"] == $ref){
					$qte ++;
				}else{				
					$produit = ATF::produit()->select($repris[$key-1]["id_produit"]);			
					$fabriquant = ATF::fabriquant()->select($produit["id_fabriquant"], "fabriquant");
					$sous_categorie = ATF::sous_categorie()->select($produit["id_sous_categorie"] , "sous_categorie");
					$text = 
					$this->cell(0,5,"- ".$qte." ".$repris[$key-1]["libelle"]." (".$sous_categorie." ".$fabriquant.")",0,1,'L');
					$qte = 1;
					$ref = $value["ref"];
				}
			}		
			$this->setX(30);	
			$produit = ATF::produit()->select($value["id_produit"]);	
			$fabriquant = ATF::fabriquant()->select($produit["id_fabriquant"], "fabriquant");
			$sous_categorie = ATF::sous_categorie()->select($produit["id_sous_categorie"] , "sous_categorie");
			$this->cell(0,5,"- ".$qte." ".$value["libelle"]." (".$sous_categorie." ".$fabriquant.")",0,1,'L');
		}
		$this->ln(5);
		$this->setfont('arial','BU',10);
		$this->cell(0,5,"4.     Equipements que vous conservez",0,1,'L');
		$this->setfont('arial','',9);
		if($affaires)	$this->cell(0,5,"Ensemble des autres équipements issus des contrats et avenants n° ".$affaires ,0,1,'L');

		$prod = array();
		foreach ($this->lignes as $key => $value) {
			if($value["id_affaire_provenance"] !== NULL){				
				$prod[] = $value["produit"]." (".$value["select_produit"]["sous_categorie"]." ".$value["select_produit"]["fabriquant"].")";	
			}
		}
		if(!empty($prod)){
			$prod = array_count_values ($prod);
			foreach ($prod as $key => $value) {
				$this->setX(30);
				$this->cell(0,5,"- ".$value." ".$key,0,1,'L');
			}
		}

		$frequence = "";	
		if($this->loyer[0]["frequence_loyer"] == "mois"){
            $frequence = "Mensuel";
        }elseif($this->loyer[0]["frequence_loyer"] == "trimestre"){
            $frequence = "Trimestriel";
        }elseif($this->loyer[0]["frequence_loyer"] == "semestre"){
            $frequence = "Semestriel";
        }else{
            $frequence = "Annuel";
        }

		$this->ln(5);
		if($this->getY() >= 250){ 	$this->Addpage(); 	$this->sety(20); }

		$this->setfont('arial','BU',10);
		$this->cell(0,5,"5.     Nouveau Loyer ".$frequence." qui remplace le loyer du contrat actuel",0,1,'L');
		$this->ln(3);
		$this->setfont('arial','B',10);	
		$y= $this->gety();	
		$this->cell(40,8,"Durée",1,0,'L');
		foreach ($this->loyer as $key => $value) {
			$text = $value["duree"]." ".$value["frequence_loyer"];
			if($value["frequence_loyer"] == "trimestre"  || $value["frequence_loyer"] == "semestre") $text = $text."(s)";
			if($key > 0) $text = "Puis ".$text;
			if($key == count($this->loyer)-1){ $this->cell(40,8,$text,1,1,'C');	}
			else{  $this->cell(40,8,$text,1,0,'C'); }			
		}	
		$this->cell(40,8,"Loyer ".$frequence." *",1,0,'L');
		foreach ($this->loyer as $key => $value) {		
			if($key == count($this->loyer)-1){ $this->cell(40,8, $value["loyer"]." € HT",1,1,'C');	}
			else{  $this->cell(40,8, $value["loyer"]." € HT",1,0,'C'); }
		}
		

		$this->setfont('arial','',7);	
		$this->SetY($y);
		$this->setleftMargin(130);
		$this->multicell(60,3,"* Ce loyer s'entend avec :\n       Assurance Remplacement en cas de sinistre\n       Frais de gestion inclus\n       Possibilité d'évoluer à tout moment\n       Reprise et Valorisation des matériels sortants ",0,'L');

		$this->setfont('arial','B',8);	
		$this->setleftMargin(10);
		$this->ln(5);

		$validite = $this->devis["validite"];
		$this->multicell(180,3,"\n\n\"Bon pour accord\"\n\n\nCachet commercial + signature\n\n\n               Cette proposition valable jusqu'au ".ATF::$usr->date_trans($validite)." ".date("Y", strtotime($validite)).", reste soumise à l'accord de notre comité des engagements ",1,'L');

			
	}


	/** PDF d'un devis formation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 09-12-2014
	* @param int $id du devis
	*/
	public function formation_devis($id) {
		$devis = ATF::formation_devis()->select($id);

		ATF::formation_devis_ligne()->q->reset()->where("id_formation_devis", $devis["id_formation_devis"]);
		$formation_devis_ligne = ATF::formation_devis_ligne()->select_all();
		sort($formation_devis_ligne);
		$this->client = ATF::societe()->select($devis["id_societe"]);
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }



		$this->noPageNo = true;
		$this->unsetHeader();
		$this->open();
		$this->addpage();
		$this->SetLeftMargin(10);		
		$this->image(__PDF_PATH__.$this->logo,10,10,40);
		$this->sety(30);
		
		$this->setfont('arial','',10);
		$this->cell(100,5,$this->societe["adresse"]." - ".$this->societe["cp"]." ".$this->societe["ville"],0,1);
		$this->cell(100,5,"SIREN : ".$this->societe["siren"],0,1);
		$this->cell(100,5,"N° d'organisme : 31 59 084 48 59",0,1);
		$this->cell(100,5,"Contact : ".ATF::user()->nom($devis["id_owner"]),0,1);
		$this->cell(100,5,"Mail : ".ATF::user()->select($devis["id_owner"], "email"),0,1);		
		

		$this->formation_devis_light($devis, $formation_devis_ligne);

		/*if($devis["type"] == "light"){
			$this->formation_devis_light($devis, $formation_devis_ligne);
		}else{
			$this->formation_devis_normal($devis, $formation_devis_ligne);
		}*/	
	}

	public function formation_devis_light($devis, $formation_devis_ligne){
		$cadre = array(
                     array("txt"=>$this->client['societe'],"size"=>10)                    
                    ,array("txt"=>$this->client["adresse"],"size"=>10)
                );
		if ($this->client["adresse2"]) $cadre[] = array("txt"=>$this->client["adresse2"],"size"=>10); 
        if ($this->client["adresse3"]) $cadre[] = array("txt"=>$this->client["adresse3"],"size"=>10); 
        $cadre[] = array("txt"=>$this->client["cp"]." ".$this->client["ville"],"size"=>10); 

        $this->cadre(110,20,85,35,$cadre);
        

        $this->ln(20);
        $this->cell(80,5,"Lille, le ".date("d/m/Y", strtotime($devis["date"])),0,1,'L');


        $this->setfont('arial','B',18);
		$this->cell(0,5,"DEVIS N° ".$devis["numero_dossier"],0,1,'C');
		$this->ln(10);
		
		$this->setfont('arial','B',12);
		$this->cell(0,5,ATF::contact()->nom($devis["id_contact"]).",",0,1,'L');

		$this->setfont('arial','',10);
		$this->multicell(0,5,"Comme convenu, nous avons le plaisir de vous présenter le devis de formation selon la thématique suivante :" ,0,'L');
		$this->cell(0,5,"                  - ".$devis["thematique"],0,1);
		
		$this->ln(5);
		$this->cell(0,5,"Pour la période :",0,1);	
		$this->cell(0,10,"                  - Du ".date("d.m.Y", strtotime($formation_devis_ligne[0]["date"]))." AU ".date("d.m.Y", strtotime($formation_devis_ligne[count($formation_devis_ligne)-1]["date"]))." soit ".$devis["nb_heure"]." heures sur ".ATF::formation_devis()->get_nb_jours($formation_devis_ligne)." jours",0,1,'L');

		$this->ln(5);
		$this->cell(0,5,"Concernant la/les personne(s) suivante(s):",0,1);
		ATF::formation_participant()->q->reset()->where("id_formation_devis",$devis["id_formation_devis"]);
		$participants = ATF::formation_participant()->select_all();		
		foreach ($participants as $key => $value) {
			$this->cell(0,5,"                  - ".ATF::contact()->nom($value["id_contact"]) ,0,1,'L');
		}

		$this->ln(5);
		$this->cell(0,5,"Pour un montant de :",0,1);
		$this->cell(0,5,"                  - ".$devis["montantHT"]." euros HT soit ".round($devis["montantHT"]*__TVA__,2)." euros TTC" ,0,1,'L');

		$this->ln(5);
		if($devis["remuneration_of"]){	
			$this->multicell(0,5,"L'organisme de formation conservera ".$devis["remuneration_of"]."% du budget obtenu au titre des frais de montage et de gestion administrative du dossier.",0);
		}

		$this->ln(5);
		if($devis["acompte"]){	
			$acompte = $devis["acompte"]. " euros HT soit ".round($devis["acompte"]*__TVA__,2)." euros TTC "; 
			$this->multicell(0,5,"Un acompte de ".$acompte." vous sera demandé lors de la signature de la convention de formation.",0);
		}
		$this->ln(5);
		$this->setfont('arial','',10);
		$this->cell(10,5,"",0,0);
		$this->multicell(160,5,"BON POUR ACCORD, le\nCachet commercial + signature\n\n\n\n\n\n\nCette offre, valable jusqu'au ".date("d/m/Y" ,strtotime($devis["date_validite"])).", reste soumise à l'obtention de prise en charge OPCA." ,1);


	}

	/*public function formation_devis_normal($devis, $formation_devis_ligne){
		$this->setY(15);

		$this->setfont('arial','B',16);
		$this->setLeftMargin(100);
		$this->cell(90,5,"Formation et accompagnement",0,1,'C');
		

		$this->setY(60);
		$this->setfont('arial','B',10);
		$this->cell(0,5,"DEVIS N° ".$devis["numero_dossier"],0,1,'L');
		$this->cell(0,5,"Date ".date("d/m/Y", strtotime($devis["date"])),0,1,'L');
		$this->setLeftMargin(10);

		$this->setfont('arial','',10);

		$this->setY(90);

		$adresse = $this->client["adresse"];
		if ($this->client["adresse2"]) $cadre[] = $adresse .= " ".$this->client["adresse2"]; 
        if ($this->client["adresse3"]) $cadre[] = $adresse .= " ".$this->client["adresse3"];

		$cadre = array(
                     array("txt"=>"Nom de l’entreprise : ".$this->client['societe'],"size"=>10)
                    ,array("txt"=>"Nom de l’interlocuteur : ".ATF::contact()->nom($this->client['id_owner']) ,"size"=>10)
                    ,array("txt"=>"Fonction : ".ATF::contact()->select($this->client['id_owner'], "fonction"),"size"=>10)
                    ,array("txt"=>"Adresse de l’entreprise : ".$adresse,"size"=>10)
                    ,array("txt"=>"Code postal : ".$this->client["cp"],"size"=>10)
                    ,array("txt"=>"Ville : ".$this->client["ville"],"size"=>10)
                    ,array("txt"=>"Code Naf : ".$this->client["naf"],"size"=>10)
                    ,array("txt"=>"Téléphone : ".$this->client["tel"],"size"=>10)          
                );		
        $this->cadre(10,$this->getY(),180,55,$cadre);

        $this->setfont('arial','B',12);
        $this->cell(0,5,"Identification des besoins de formations de l’entreprise",0,1,'L');
        $this->setfont('arial','',10);     
        $this->ln(10);

        $size = 30;

        $cadre = array(array("txt"=>"Thématiques des formations souhaitées, par ordre de priorité et par bénéficiaire : ","size"=>10, "bold"=>true));

        $str = explode(";", $devis["thematique"]);

        foreach ($str as $key => $value) {
        	$cadre[] = array("txt"=>"                  - ".$value,"size"=>10);
        	$size = $size+5;
        }

        $cadre[] = array("txt"=>"OPCAS de l’entreprise : ","size"=>10);
        foreach (explode("|", $devis["opca"]) as $k => $v) {
        	$cadre[] = array("txt"=>"                  - ".ATF::societe()->select($v,"societe"),"size"=>10);
        	$size = $size+5;
        }

        $nb_participants = $devis["nb_participants"];
        if(!$nb_participants){
        	ATF::formation_participant()->q->reset()->where("id_formation_devis",$devis["id_formation_devis"]);
			$participants = ATF::formation_participant()->select_all();
			$nb_participants = count($participants);
        } 


        $cadre[] = array("txt"=>"Nombre de participants : ".$nb_participants,"size"=>10);
        
        $lieu_formation = ATF::societe()->select($devis["id_lieu_formation"]);
		$lieu_formation["adresse"] = $lieu_formation["adresse"];
		if($lieu_formation["adresse_2"]) $lieu_formation["adresse"] .= " ".$lieu_formation["adresse_2"];
		if($lieu_formation["adresse_3"]) $lieu_formation["adresse"] .= " ".$lieu_formation["adresse_3"];
		$lieu_formation["adresse"] .= ", ".$lieu_formation["cp"]." ".$lieu_formation["ville"];
        $cadre[] = array("txt"=>"Lieu de la formation : ".$lieu_formation["societe"]." - ".$lieu_formation["adresse"],"size"=>10);

        $this->cadre(10,$this->getY(),180,$size,$cadre);


        $this->addpage();

        $cadre = array(  array("txt"=>"Notre offre d’accompagnement :","size"=>10, "bold"=>true)
        				,array("txt"=>" ","size"=>5)
        				,array("txt"=>$this->societe["societe"]." vous propose un accompagnement sur mesure dans l’ensemble de votre démarche de formation en :","size"=>10)
        				,array("txt"=>"     -  Identifiant  des besoins en formation, aide aux montages financiers et recherche des financements correspondants","size"=>10)
        				,array("txt"=>"     -  Animation de formations ","size"=>10)
        				,array("txt"=>"     -  Suivi qualitatif de la formation : une évaluation à chaud de la formation par les participants, une  évaluation à froid de l’atteinte des objectifs visés par la formation et des propositions d’actions correctives.","size"=>10)
        			);

        $this->cadre(10,20,180,60,$cadre);

        $this->ln(15);


        $cadre = array(  array("txt"=>"Coût de la formation et de l’accompagnement :","size"=>10, "bold"=>true)
        				,array("txt"=>" ","size"=>5)
        				,array("txt"=>"Le coût payé par l’entreprise sera fonction de la prestation fournie par ".$this->societe["societe"].".","size"=>10)
        				,array("txt"=>" ","size"=>2)
        				,array("txt"=>"Dans l’hypothèse où  ".$this->societe["societe"]." fournit l’accompagnement et les formateurs :","size"=>10, "bold"=>true)
        				,array("txt"=>" ","size"=>5)
        				,array("txt"=>"L’entreprise devra verser un acompte de  ".$devis["acompte"]."%  du budget obtenu. La somme correspondant à ce montant lui sera reversée dans le cas où l’organisme financeur règle directement la prestation d’Exactitude par subrogation.","size"=>10)
        				,array("txt"=>" ","size"=>5)
        				,array("txt"=>"Dans l’hypothèse où l’entreprise souhaite faire appel à ses propres formateurs : ","size"=>10, "bold"=>true)
        				,array("txt"=>" ","size"=>5)
        				,array("txt"=>"L’entreprise devra verser un montant égal à ".$devis["remuneration_of"]."% du budget obtenu, au titre de l’appui-conseil et de l’accompagnement.","size"=>10)
        			);

        $this->cadre(10,$this->getY(),180,80,$cadre);

        $this->ln(10);

        $this->setfont('arial','',10);
		$this->multicell(0,5,"Ce devis est valable 30 jours. Pour toute confirmation, merci de nous retourner ce devis signé, avec la mention Bon pour accord et le cachet de l’entreprise.",0);

		$this->ln(5);

		$this->multicell(0,5,"Cachet commercial + signature\n\n\n\n\n\n\n\n\n" ,1);




	}*/

	public function cleanHtml($text){
		$tag = "span";
		$tags = array("span","font","ul");

		foreach ($tags as $key => $value) {
			$text = preg_replace('#</?'.$value.'[^>]*>#is', "", $text);
		}	
		$text = preg_replace('/ style=\\"[^\\"]*\\"/', '', $text);	
		return $text;

	}


	/** PDF d'un devis formation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 09-12-2014
	* @param int $id du devis
	*/
	public function formation_commande($id) {	

		$commande = ATF::formation_commande()->select($id);
		$devis = ATF::formation_devis()->select($commande["id_formation_devis"]);
		ATF::formation_devis_ligne()->q->reset()->where("id_formation_devis", $commande["id_formation_devis"]);
		$formation_devis_ligne = ATF::formation_devis_ligne()->select_all();
		sort($formation_devis_ligne);
		$this->client = ATF::societe()->select($devis["id_societe"]);
		$this->formateur = ATF::societe()->select($devis["id_formateur"]);
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		ATF::formation_participant()->q->reset()->where("id_formation_devis",$devis["id_formation_devis"]);
		$participants = ATF::formation_participant()->select_all();

		$this->open();
		$this->noPageNo = true;
		$this->unsetHeader();
	
		$this->AddPage();
		$this->SetLeftMargin(15);		
		$this->image(__PDF_PATH__.$this->logo,10,10,40);
		$this->sety(30);

		$this->setfont('arial','',16);
		$this->cell(0,5,"CONVENTION DE FORMATION PROFESSIONNELLE",0,1,"C");
		$this->setfont('arial','',10);
		$this->cell(0,5,"(Article L. 6353-1 et L.6353-2 du Code du Travail)",0,1,"C");

		$this->ln(5);
		$this->cell(0,10,"Entre les soussignés :",0,1,"L");

		$this->setfont('arial','',9);

		$adresse = "";
		$adresse = $this->client["adresse"];
		if($this->client["adresse1"]){ $adresse .= " ".$this->client["adresse1"]; }
		if($this->client["adresse2"]){ $adresse .= " ".$this->client["adresse2"]; }
		$adresse .= " - ".$this->client["cp"]." ".$this->client["ville"];
		
		$this->multicell(0,5,"La société :\n\n".strtoupper($this->client["societe"])." - ".$adresse."\nSIRET : ".$this->client["siret"],0,'L');

		$this->ln(2);

		$adresse = $this->formateur["adresse"];
		if($this->formateur["adresse1"]){ $adresse .= " ".$this->formateur["adresse1"]; }
		if($this->formateur["adresse2"]){ $adresse .= " ".$this->formateur["adresse2"]; }
		$adresse .= " - ".$this->formateur["cp"]." ".$this->formateur["ville"];

		$this->multicell(0,5,"Et l'organisme de formation : \n\n".strtoupper($this->societe["societe"])." situé au ".$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville']."\nSIRET : ".$this->societe["siret"] ,0,'L');
		
		$this->multicell(0,5,"Numéro de déclaration d'activité : " ,0,'L');
		$this->ln(2);

		$this->cell(0,5,"Est conclue la convention suivante.",0);
		

		$this->ln(10);
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"I - OBJET, NATURE, DUREE ET EFFECTIF DE LA FORMATION.",0,1,"L");
		$this->setfont('arial','',10);
		$this->multicell(0,5," Le bénéficiaire entend faire participer une partie de son personnel à la session de formation professionnelle organisée par ".strtoupper($this->societe["societe"])." intitulée :",0,'L');
		$this->ln(5);
		$this->setfont('arial','B',14);
		$this->cell("0",5,$devis["thematique"],0,1,'C');
		$this->ln(5);	
		$this->setfont('arial','B',10);
		$this->multicell("0",5,"Le programme détaillé de l’action de formation est explicité en Annexe de la présente convention. L’entreprise bénéficiaire s’engage à le transmettre aux stagiaires avant le début de la formation",0,'L');
		$this->ln(2);
		
		$this->setfont('arial','BU',10);
		$this->cell("0",5,"Les objectifs :",0,1,'L');
		$this->setfont('arial','B',10);
		$this->cell("0",5,"A l’issue de la formation, l’apprenant sera capable : ",0,1,'L');
		$this->setfont('arial','',10);

		$objectif = str_replace("<br>", "\n", $commande["objectif"]);

		$objectif = $this->cleanHtml($objectif);

		if(strpos($objectif, "<li>")){			
			$pattern = '~<li[^>]*>\K.*(?=</li>)~Uis';
			preg_match_all($pattern, $objectif, $out);			
			foreach ($out[0] as $key => $value) {	
				$html = "<li>".$value."</li>";			
				$str = "               - ".$value ."\n";
				$objectif = str_replace($html, $str, $objectif);
			}

		}

		$this->multicell(0,5,str_replace("<br>", "\n", $objectif),0,'L');

		$this->ln(2);
		$this->setfont('arial','B',10);
		$this->multicell(0,5,"Cette action entre dans le champ de l’article L6313-1 du code du travail comme « action d’adaptation et de développement de compétence ».",0,'L');
		$this->ln(2);

		$this->setfont('arial','',10);
		$this->cell("0",5,"Le NOMBRE total des PARTICIPANTS à cette session est fixé à : ".count($participants),0,1,'L');
		$this->ln(2);

		$this->setfont('arial','B',10);
		$this->cell("50",5,"Date de la session",0,0,'L');
		$this->setfont('arial','',10);
		$this->cell("100",5,"DU ".date("d/m/Y", strtotime($formation_devis_ligne[0]["date"]))." AU ".date("d/m/Y", strtotime($formation_devis_ligne[count($formation_devis_ligne)-1]["date"])),0,1,'L');
		$this->ln(2);

		$this->setfont('arial','B',10);
		$this->cell("55",5,"Nombre d'heure par stagiaire : ",0,0,'L');
		$this->setfont('arial','',10);		
		$this->cell("100",5,$devis["nb_heure"]." heures",0,1,'L');

		$this->ln(2);
		$horaires = "";
		if($formation_devis_ligne[0]["date_deb_matin"] && $formation_devis_ligne[0]["date_fin_matin"]){ $horaires .= $formation_devis_ligne[0]["date_deb_matin"]."/".$formation_devis_ligne[0]["date_fin_matin"]." "; }
		if($formation_devis_ligne[0]["date_deb_am"] && $formation_devis_ligne[0]["date_fin_am"]){ $horaires .= $formation_devis_ligne[0]["date_deb_am"]."/".$formation_devis_ligne[0]["date_fin_am"]; }
		
		$this->setfont('arial','B',10);
		$this->cell("55",5,"Horaires de formation : ",0,0,'L');
		$this->setfont('arial','',10);
		$this->cell(0,5,$horaires,0,1,'L');
		$this->ln(2);

		$this->setfont('arial','B',10);
		$this->cell("50",5,"Lieu de formation :",0,0,'L');
		$this->setfont('arial','',10);
		$lieu_formation = ATF::societe()->select($devis["id_lieu_formation"]);
		$lieu_formation["adresse"] = $lieu_formation["adresse"];
		if($lieu_formation["adresse_2"]) $lieu_formation["adresse"] .= " ".$lieu_formation["adresse_2"];
		if($lieu_formation["adresse_3"]) $lieu_formation["adresse"] .= " ".$lieu_formation["adresse_3"];
		$lieu_formation["adresse"] .= ", ".$lieu_formation["cp"]." ".$lieu_formation["ville"];

		$this->multicell(0,5,$lieu_formation["societe"]." - ".$lieu_formation["adresse"],0,'L');



		

		
		/**********************************************************************************************************************
														PAGE 2
		**********************************************************************************************************************/

		$this->AddPage();
		$this->image(__PDF_PATH__.$this->logo,10,10,40);
		$this->sety(30);

		$this->setfont('arial','BU',14);
		$this->cell(0,10,"II - ENGAGEMENT DE PARTICIPATION",0,1,"L");
		$this->setfont('arial','',10);
		$this->multicell(0,5,"Le bénéficiaire s’engage à assurer la présence des participants à la date, lieux et heures prévus ci-dessus. Liste des participants en Annexe de la présente convention.",0,'J');
		$this->ln(2);

		$this->setfont('arial','BU',14);
		$this->cell(0,10,"III - PRIX DE LA FORMATION :",0,1,"L");
		$this->setfont('arial','',10);
		$this->multicell(0,5,"Le coût de la formation, objet de la présente, s'élève",0,'L');
		$this->ln(2);

		$this->cell(0,5,"à ".round($devis["montantHT"])." € HT" ,0,1);
		$this->cell(0,5,"Soit ".round($devis["montantHT"]*__TVA__)." € TTC",0,1);
		
		$this->cell(0,5,"Payable à 30 jours à partir de la réception de la facture.",0,1);

		$this->ln(2);
		$phrase = "Cette somme couvre l'intégralité des frais engagés de l'organisme de formation pour cette session. ";
		if($devis["acompte"]){	$phrase .= "Un acompte de ".$devis["acompte"]." € HT soit ".round($devis["acompte"]*__TVA__,2)." € TTC sera dû lors de la signature de cette convention et le solde à la réception de facture."; }
		$this->multicell(0,5,$phrase,0,'J');
							
		$this->ln(5);
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"IV - MOYENS PEDAGOGIQUES ET TECHNIQUES MIS EN OEUVRE",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"Trois moyens pédagogiques permettent d’y parvenir :\n     - Des apports théoriques présentant les connaissances actuelles sur le sujet traité,\n     - Des auto-questionnements amenant à une optimisation des pratiques\n     - Un atelier sur l’élaboration des documents types et/ou validation des documents existants\n\n\nLes moyens techniques sont assurés par l’entreprise bénéficiaire.",0,'J');

		$this->ln(5);
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"V - MOYENS PERMETTANT D'APPRECIER LES RESULTATS DE L'ACTION",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"L’appréciation des résultats se fait par l’évaluation des connaissances acquises au cours de la formation. Cette évaluation se fait dans le cadre d’ateliers faisant appel aux différents sujets abordés durant la formation et d’un QCM de validation finale.",0,'J');

		$this->ln(5);
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"VI - SANCTION DE LA FORMATION",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"Une attestation mentionnant les objectifs, la nature et la durée de l'action et les résultats de l'évaluation des acquis de la formation sera remise au stagiaire à l'issue de la formation conformément à l’article L.6353-1 du Code du travail.",0,'J');

		$this->ln(5);
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"VII - MOYENS PERMETTANT DE SUIVRE L'EXECUTION DE L'ACTION",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"Des feuilles de présence seront signées par le(s) stagiaire(s) et le formateur par demi-journée de formation.",0,'J');

		$this->ln(5);
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"VIII -REGLEMENT INTERIEUR",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"Une copie du règlement intérieur est transmise en Annexe 3 de la présente convention. L’entreprise bénéficiaire s’engage à le transmettre aux stagiaires avant le début de la formation.",0,'J');


	
		/**********************************************************************************************************************
														PAGE 3
		**********************************************************************************************************************/

		$this->AddPage();
		$this->image(__PDF_PATH__.$this->logo,10,10,40);
		$this->sety(40);	
		
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"IX -NON RÉALISATION DE LA PRESTATION DE FORMATION",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"En application de l’article L. 6354-1 du Code du travail, il est convenu entre les signataires de la présente convention, que faute de réalisation totale ou partielle de la prestation de formation, l’organisme prestataire doit rembourser au cocontractant les sommes indûment perçues de ce fait.",0,'J');

		$this->ln(5);

		$this->setfont('arial','BU',14);
		$this->cell(0,10,"X - DEDOMMAGEMENT, REPARATION OU DEDIT",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"En cas de renoncement par l’entreprise bénéficiaire à l’exécution de la présente convention dans un délai de 7 jours avant la date de démarrage de la prestation de formation, objet de la présente convention, l’entreprise bénéficiaire s’engage au versement de la somme de ".$devis["remuneration_of"]." % à titre de dédommagement. La somme correspondante n’est pas imputable sur l’obligation de participation au titre de la formation professionnelle continue de l’entreprise bénéficiaire et ne peut pas faire l’objet d’une demande de remboursement ou de prise en charge par l’OPCA. Celle-ci est spécifiée sur la facture et ne doit pas être confondue avec les sommes dues au titre de la formation.\n\nToute session de formation  commencée est due dans son intégralité. La somme correspondant à la partie de formation non effectuée n’est pas imputable sur l’obligation de participation au titre de la formation professionnelle continue de l’entreprise bénéficiaire et ne peut pas faire l’objet d’une demande de remboursement ou de prise en charge par l’OPCA. Celle-ci est spécifiée sur la facture et ne doit pas être confondue avec les sommes dues au titre de la formation.",0,'J');

		$this->ln(5);
		$this->setfont('arial','BU',14);
		$this->cell(0,10,"XI - LITIGES",0,1,"L");
		$this->setfont('arial','',10);
		$this->ln(2);		
		$this->multicell(0,5,"Si une contestation ou un différend ne peuvent-être réglés à l’amiable, celui-ci sera porté auprès du tribunal compétent pour gérer ce litige.",0,'J');

		$this->ln(2);		
		$this->multicell(0,5,"Fait en double éxemplaire à ............................ le .............................",0,'J');

		$this->ln(20);
		$this->cell(80,5, "Pour l'entreprise,",0,0,"C");
		$this->cell(80,5, "Pour l'organisme de formation,",0,1,"C");

		$this->cell(80,5, "(Nom et qualité du signataire) ",0,0,"C");		
		$this->cell(80,5, "(Nom et qualité du signataire)",0,1,"C");
	
	}

	/** PDF d'une fiche de présence de formation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 09-12-2014
	* @param int $id de la fiche
	*/
	public function formation_attestation_presence($id) {

		$attestation = ATF::formation_attestation_presence()->select($id);
		$commande = ATF::formation_commande()->select($attestation["id_formation_commande"]);
		$devis = ATF::formation_devis()->select($commande["id_formation_devis"]);
		ATF::formation_devis_ligne()->q->reset()->where("id_formation_devis", $commande["id_formation_devis"]);
		$formation_devis_ligne = ATF::formation_devis_ligne()->select_all();
		sort($formation_devis_ligne);
		$this->client = ATF::societe()->select($devis["id_societe"]);
		$this->contact = ATF::contact()->select($attestation["id_contact"]);		
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }




		$this->noPageNo = true;
		$this->unsetHeader();

		$this->open();
		$this->addpage();
		$this->SetLeftMargin(15);		
		$this->image(__PDF_PATH__.$this->logo,10,10,40);
		$this->sety(30);

		$this->setfont('arial','',16);
		$this->cell(0,5,"ATTESTATION DE PRESENCE",0,1,"C");
		$this->ln(15);

		$this->setfont('arial','B',10);
		$this->cell(40,15,"Bénéficiaire",1,0,"C");
		$this->cell(140,15,$this->client["societe"],1,1,"C");

		$this->cell(40,15,"Formateur",1,0,"C");
		$this->setfont('arial','',10);
		$this->cell(80,15,ATF::contact()->nom($devis["id_formateur"]),1,0,"L");
		$this->cell(60,15,"Signature",1,1,"L");

		$this->setfont('arial','B',10);
		$this->cell(40,15,"Date",1,0,"C");
		$this->setfont('arial','',10);
		$this->cell(140,15,"Du ".date("d/m/Y", strtotime($formation_devis_ligne[0]["date"]))." au ".date("d/m/Y", strtotime($formation_devis_ligne[count($formation_devis_ligne)-1]["date"]))." (".$devis['nb_heure']." heures)",1,1,"C");

		$this->setfont('arial','B',10);	
		$i = 0;	
		$lieu_formation = ATF::societe()->select($devis["id_lieu_formation"]); $i++;
		$lieu_formation["adresse"] = $lieu_formation["adresse"]; $i++;
		if($lieu_formation["adresse_2"]){ $lieu_formation["adresse"] .= "\n ".$lieu_formation["adresse_2"]; $i++;}
		if($lieu_formation["adresse_3"]){ $lieu_formation["adresse"] .= "\n ".$lieu_formation["adresse_3"]; $i++;}
		$lieu_formation["adresse"] .= "\n ".$lieu_formation["cp"]." ".$lieu_formation["ville"];	$i++;	

		$this->cell(40,$i*7,"Lieu",1,0,"C");
		$this->setfont('arial','',10);
		$this->multicell(140,7,$lieu_formation["societe"]."\n".$lieu_formation["adresse"],1,"C");

		$this->setfont('arial','B',10);
		$this->cell(40,15,"Intitulé de l'action",1,0,"C");
		$this->setfont('arial','',10);
		$this->cell(140,15,$devis["thematique"],1,1,"C");


		$this->ln(15);
		$this->setfont('arial','U',10);
		$this->cell(0,10,"Nom du stagiaire :",0,1,"L");
		$this->setfont('arial','',10);
		$this->cell(0,10,strtoupper($this->contact["nom"]." ".$this->contact["prenom"]),0,1,"L");

		$this->setfont('arial','U',10);
		$this->cell(0,10,"Qualité :",0,1,"L");
		$this->setfont('arial','',10);
		$this->cell(0,10,$this->contact["fonction"],0,1,"L");

		$this->ln(5);
		$this->setfont('arial','',10);
		$this->cell(40,7,"Date et signature",1,0,"C");
		$this->cell(70,7,"Matin",1,0,"C");
		$this->cell(70,7,"Après-midi",1,1,"C");
		$j = 1;

		foreach ($formation_devis_ligne as $key => $value) {
			$this->cell(40,15,"Jour ".$j.": le ".date("d/m/Y", strtotime($value["date"])),1,0,"C");
			if($value["date_deb_matin"] && $value["date_deb_matin"]){ 
				$this->cell(70,15,$value["date_deb_matin"]."/".$value["date_fin_matin"],1,0,"L");
			}else{ 
				$this->setfillcolor(96,96,96);
				$this->cell(70,15,"",1,0,"L",1); 
			}
			
			if($value["date_deb_am"] && $value["date_deb_am"]){ 
				$this->cell(70,15,$value["date_deb_am"]."/".$value["date_fin_am"],1,1,"L");
			}else{ 
				$this->setfillcolor(96,96,96);
				$this->cell(70,15,"",1,1,"L",1); 
			}
			$j++;
		}		
		$this->setfont('arial','',10);
		$this->cell(40,7,"Total heure(s)",1,0,"C");
		$this->cell(140,7,$devis["nb_heure"],1,1,"C");		
	}
	

	public function formation_commande_fournisseur($id){
		$bdc = ATF::formation_commande_fournisseur()->select($id);
		$commande = ATF::formation_commande()->select($bdc["id_formation_commande"]);
		$devis = ATF::formation_devis()->select($bdc["id_formation_devis"]);
		ATF::formation_devis_ligne()->q->reset()->where("id_formation_devis", $bdc["id_formation_devis"]);
		$formation_devis_ligne = ATF::formation_devis_ligne()->select_all();
		sort($formation_devis_ligne);
		ATF::formation_devis_fournisseur()->q->reset()->where("id_formation_devis",$bdc["id_formation_devis"])
										   ->where("type","formateur");
		$formateur = ATF::formation_devis_fournisseur()->select_row();
		$formateur = ATF::societe()->select($formateur["id_societe"]);
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		$this->noPageNo = true;
		$this->unsetHeader();

		$this->open();
		$this->addpage();
		$this->SetLeftMargin(15);		
		$this->image(__PDF_PATH__.$this->logo,10,10,40);
		$this->sety(30);

		$this->setfont('arial','',16);
		$this->cell(0,5,"CONTRAT DE SOUS-TRAITANCE FORMATION",0,1,"C");
		$this->ln(10);

		$this->setfont('arial','B',10);
		$this->cell("50",5,"Entre les soussignés :",0,1,'L');
		$this->ln(2);
		$this->setfont('arial','',10);
		$this->societe['siret'] = str_replace(" ", "", $this->societe['siret']);
		$this->societe['siret'] = substr($this->societe['siret'], 0, 3)." ".substr($this->societe['siret'], 3, 3)." ".substr($this->societe['siret'], 6, 3)." ".substr($this->societe['siret'], -5);

		$this->multicell(0,5,"1 - ".strtoupper($this->societe["societe"]).", ".$this->societe["adresse"]." ".$this->societe["cp"]." ".$this->societe["ville"].", SIRET ".$this->societe["siret"].", organisme de formation enregistré sous le numéro ....... auprès du Préfet de la région d’Île-de-France,\nci-après « le donneur d’ordre »",0);
		$this->ln(2);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Et",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);

		$formateur['siret'] = str_replace(" ", "", $formateur['siret']);
		$formateur['siret'] = substr($formateur['siret'], 0, 3)." ".substr($formateur['siret'], 3, 3)." ".substr($formateur['siret'], 6, 3)." ".substr($formateur['siret'], -5);


		$this->multicell(0,5,"2 - ".strtoupper($formateur["societe"]).", ".$formateur["adresse"]." ".$formateur["cp"]." ".$formateur["ville"].", SIRET ".$formateur["siret"].", organisme de formation enregistré sous le numéro ....... auprès du Préfet de la région d’Île-de-France,\nci-après « le sous-traitant »",0);

		$this->ln(3);

		$this->cell("100",5,"Il a été convenu ce qui suit :",0,1,'L');


		$this->ln(3);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Article premier : Nature du contrat",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);
		$this->multicell(0,5,"Le présent contrat est conclu dans le cadre d’une prestation de formation ponctuelle réalisée par le sous-traitant au bénéfice du donneur d’ordre.",0);

		$this->ln(3);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Article 2 : Objet du contrat",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);
		$this->multicell(0,5,"La formation, objet du contrat, est la suivante : ".$devis["thematique"] ,0);

		$this->cell(50,5,"Date(s) :",0,1,'L');
		$this->setfont('arial','',10);
		$this->setleftMargin(30);
		foreach ($formation_devis_ligne as $key => $value) {
			$horaires = "";
			if($value["date_deb_matin"] && $value["date_fin_matin"]) $horaires .= " ".$value["date_deb_matin"]."/".$value["date_fin_matin"];
			if($value["date_deb_am"] && $value["date_fin_am"]) $horaires .= " ".$value["date_deb_am"]."/".$value["date_fin_am"];
			$this->cell(0,7,"- ".ATF::$usr->date_trans(date($value["date"]))." : ".$horaires,0,1,"L");
		}	
		$this->setleftMargin(10);


		$this->ln(3);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Article 3 : Durée du contrat",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);
		$this->multicell(0,5,"Le présent contrat est strictement limité à la prestation de formation visée à l’article 2.\nIl cesse de plein droit à son terme." ,0);


		$this->ln(3);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Article 4 : Obligations du sous-traitant",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);
		$this->multicell(0,5,"Le sous-traitant s’engage à :" ,0);

		$engagements_sstratitant = array("Communiquer au donneur d’ordre une copie de son extrait K-bis / de son immatriculation avant le début de la formation ;"
										,"Animer la formation dans le respect des objectifs fixés par le donneur d’ordre ;"
										,"Animer personnellement la formation, sauf en cas de situation exceptionnelle, et uniquement après accord du donneur d’ordre ;"
										,"Communiquer au donneur d’ordre ses besoins en matériel (projecteur, tableau, photocopies de supports ...) au moins .... jours avant le début de la formation ;"
										,"Assurer l’évaluation des stagiaires à l’issue de l’action de formation, afin de permettre au donneur d’ordre d’établir les attestations de fin de formation prévues à l’article L.6353-1 du Code du travail ;"
										,"Participer, en tant que de besoin, aux réunions de préparation."
									);

		$this->setleftMargin(20);
		foreach ($engagements_sstratitant as $key => $value) {
			$this->image(__PDF_PATH__.'exactitude/puce.png',17,$this->getY()+1,2);				
			$this->multicell(0,5,$value,0,1,"J");
		}	
		$this->setleftMargin(10);

		$this->AddPage();

		$this->ln(3);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Article 5 : Obligations du donneur d’ordre",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);
		$this->multicell(0,5,"Le donneur d’ordre s’engage à :" ,0);

		$engagements_dooneurordre = array("Confier au sous-traitant la formation prévue à l’article 2 ;"
										,"Prendre en charge la gestion administrative et logistique de la formation ;"
										,"Transmettre au sous-traitant une copie des feuilles de présence signées par les stagiaires ;"
										,"Transmettre au sous-traitant une copie des questionnaires de satisfaction remplis par les stagiaires à l’issue de la formation"
										,"Assurer l’évaluation des stagiaires à l’issue de l’action de formation, afin de permettre au donneur d’ordre d’établir les attestations de fin de formation prévues à l’article L.6353-1 du Code du travail ;"
										,"Prévenir le sous-traitant au moins ... jours à l’avance en cas d’annulation ou de report de la formation ;"
									);

		$this->setleftMargin(20);
		foreach ($engagements_dooneurordre as $key => $value) {
			$this->image(__PDF_PATH__.'exactitude/puce.png',17,$this->getY()+1,2);					
			$this->multicell(0,5,$value,0,1,"J");
		}	
		$this->setleftMargin(10);



		$this->ln(3);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Article 6 : Modalités financières",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);
		$this->multicell(0,5,"Le sous-traitant percevra une rémunération de ... euros HT par journée / par session / par stagiaire.\nLe paiement sera effectué à réception de la facture." ,0);


		$this->ln(3);
		$this->setfont('arial','B',10);
		$this->cell("50",5,"Article 7 : Dispositions diverses",0,1,'L');
		$this->setfont('arial','',10);
		$this->ln(2);
		
		$dispositions = array("Le présent contrat ne crée entre les parties aucun lien de subordination, le sous-traitant demeurant libre et responsable du contenu de la formation ;"
							 ,"Le sous-traitant déclare avoir souscrit une police d’assurance responsabilité civile professionnelle (RCP) auprès de la compagnie ...."
							 ,"Le sous-traitant dispose d’une propriété intellectuelle et/ou artistique sur le contenu de sa formation. Le donneur d’ordre s’engage à ne pas reproduire ni diffuser ce contenu sans l’accord du sous-traitant."
						);

		$this->setleftMargin(20);
		foreach ($dispositions as $key => $value) {
			$this->image(__PDF_PATH__.'exactitude/puce.png',17,$this->getY()+1,2);				
			$this->multicell(0,5,$value,0,1,"J");
		}	
		$this->setleftMargin(10);



		$this->ln(20);
		$this->cell(0,5,"Fait à LILLE, le ".date("d/m/Y", strtotime($bdc["date_envoi"])),0,1);

		$this->setfont('arial','',10);
		$this->setLeftMargin(30);
		$y = $this->getY();
		$this->multicell(80,5, "Le donneur d’ordre,\n[Nom, prénom, qualité,\nsignature, tampon]",0);
		$this->setY($y);
		$this->setX(120);
		$this->multicell(80,5, "Le sous-traitant,\n[Nom, prénom, qualité,\nsignature, tampon]",0);

	}

	/** PDF d'une fiche de présence de formation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 09-12-2014
	* @param int $id de la fiche
	*/
	public function formation_facture($id) {

		$this->facture = ATF::formation_facture()->select($id);		
		$devis = ATF::formation_devis()->select($this->facture["id_formation_devis"]);
		ATF::formation_devis_ligne()->q->reset()->where("id_formation_devis", $this->facture["id_formation_devis"]);
		$formation_devis_ligne = ATF::formation_devis_ligne()->select_all();
		sort($formation_devis_ligne);
		
		if($this->facture["type"] == "acompte"){
			$this->client = ATF::societe()->select($devis["id_societe"]);
		}else{
			$this->client = ATF::societe()->select($this->facture["id_societe"]);
		}
		
		if(ATF::$codename == "cleodis") { $this->societe = ATF::societe()->select(246); }elseif(ATF::$codename == "cleodisbe"){ $this->societe = ATF::societe()->select(4225); }elseif(ATF::$codename == "cap"){ $this->societe = ATF::societe()->select(1); } elseif(ATF::$codename == "exactitude"){ $this->societe = ATF::societe()->select(1); }


		
		ATF::formation_participant()->q->reset()->where("id_formation_devis",$devis["id_formation_devis"]);
		$participants = ATF::formation_participant()->select_all();
		
		$this->open();
		$this->addpage();
		$this->noPageNo = true;
		$this->setMargins(15,30);
		$this->setY(10);
		$this->setfont('arial','',8);

		$this->multicell(0,15,'FACTURE',0,'C');	

		//CADRE Societe
		$cadre = array(
			$this->societe['adresse']
			,$this->societe['adresse_2']
			,$this->societe['cp']." ".$this->societe['ville']
			,"Tel : ".$this->agence['tel']
			,"N° TVA intra : FR 91 ".$this->societe["siren"]
			,"RCS ".$this->societe['ville']." ".$this->societe['siren']
		);
		$this->cadre(20,30,80,35,$cadre,$this->societe['societe']);
		
		//CADRE Client
		$cadre = array(
			 $this->client['adresse']
			,$this->client['adresse2']
			,$this->client['adresse3']
			,$this->client['cp']." ".$this->client['ville']			
		);
		$this->cadre(110,30,80,35,$cadre,$this->client['societe']);


		$this->multicell(0,5,"A l'attention du service comptabilité,");
		$this->ln(5);
		$y = $this->getY();

		//CADRE Date
		$cadre = array(array("txt"=>"Date : ".date("d/m/Y",strtotime($this->facture['date'])),"align"=>"C"));
		$this->cadre(10,$y,60,10,$cadre);

		//CADRE Client
		$cadre = array(array("txt"=>util::truncate($this->client['societe'],25),"align"=>"C"));
		$this->cadre(75,$y,60,10,$cadre);

		//CADRE Facture
		$cadre = array(array("txt"=>"N° de facture : ".$this->facture['ref'],"align"=>"C"));
		$this->cadre(140,$y,60,10,$cadre);

		$this->cell(0,5,"Organisme de formation : ".$this->societe["societe"],0,1,"L");
		$this->cell(0,5,"N° d'activité : 31590845159",0,1,"L");
		
		if($this->facture["type"] == "acompte"){ $this->cell(0,5,"Acompte : ".$this->facture["num_dossier"],0,1,"L");	}
		else{ $this->cell(0,5,"Dossier ".ATF::societe()->nom($this->client["id_societe"])." : ".$this->facture["num_dossier"],0,1,"L"); }
		

		$this->ln(5);
		

		$head = array("Désignation","Montant");
		$w = array(120,60);
		$data = $styles = array();	
		if($this->facture["type"] == "acompte"){ $text = "Facture d'acompte pour formation pour :\n"; }
		else{ $text = "Formation pour :\n"; }
		foreach ($participants as $key => $value) {					
			$text .= "          ".strtoupper(ATF::contact()->select($value["id_contact"], "nom")." ".ATF::contact()->select($value["id_contact"], "prenom"));
			$text .= " (adhérent : ".ATF::contact()->select($value["id_contact"], "code_adherent")." ) de la société ".ATF::societe()->nom(ATF::contact()->select($value["id_contact"], "id_societe"));
			if($key-1 != count($participants)){
				$text .= ",\n";
			}
		}
			$text .= "pour l'action : ".$devis["thematique"]."\ndu ".date("d/m/Y", strtotime($formation_devis_ligne[0]["date"]))." au ".date("d/m/Y", strtotime($formation_devis_ligne[count($formation_devis_ligne)-1]["date"]))." sur ".$devis["nb_heure"]." heures";

			$data[] = array(
						$text
						,number_format(abs($this->facture["prix"]),2,'.',' ')." €"
					);
			$styles[][0] = array("align" => "L","size"=>9);
				

		$this->tableauBigHead($head,$data,$w,5,$styles);				
		$this->ln(5);
		$total = $this->facture['prix'];
		$totalTTC = $total*1.20;
		
		$head = array("Montant Total HT","Taux","Montant TVA (20%)","Total T.T.C.");
		$data = array(
			array(
				number_format(abs(round($this->facture["prix"],2)),2,'.',' ')." €"
				,number_format(abs(20),2,'.',' ')."%"
				,number_format(abs(round(($this->facture["prix"]*(1.20-1)),2)),2,'.',' ')." €"
				,number_format(abs(round($this->facture["prix"]*1.20,2)),2,'.',' ')." €"
			)
		);			
		$this->tableau($head,$data);		

			
		$this->ln(20);

		$this->multicell(0,5,"Le paiement se fera au comptant sans escompte à reception de facture par virement bancaire aux coordonnées suivantes :");

		$this->setfont('arial','B',8);

		$this->cell(0,10,"Domiciliation ".$this->societe["nom_banque"],0,1,"C");

		$this->cell(40,5,"Code établissement",0,0,"C");
		$this->cell(40,5,"Code Guichet",0,0,"C");
		$this->cell(40,5,"Numéro de compte",0,0,"C");
		$this->cell(40,5,"Clé de RIB",0,1,"C");

		$rib= str_replace(" ", "", $this->societe["RIB"]);

		$this->cell(40,5,substr($rib, 0, 5),0,0,"C");
		$this->cell(40,5,substr($rib, 5, 5),0,0,"C");
		$this->cell(40,5,substr($rib, 10, 11),0,0,"C");
		$this->cell(40,5,substr($rib, -2),0,1,"C");

		$this->cell(0,5,"N° de TVA : ".$this->societe["reference_tva"],0,1,"L");
		$this->cell(0,5,"IBAN : ".$this->societe["IBAN"],0,1,"L");
		$this->cell(0,5,"BIC : ".$this->societe["BIC"],0,1,"L");
		
		
	}



	/** Initialise les variables pour générer un BDC Fournisseur formation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 12-03-2015
	* @param int $id Identifiant bon de commande
	*/
	private function initFormationBDC($id,$s) {
		$this->bdc = ATF::formation_bon_de_commande_fournisseur()->select($id);;
		$this->devis = ATF::formation_devis()->select($this->bdc["id_formation_devis"]);

		ATF::formation_priseEnCharge()->q->reset()->where("id_formation_devis", $this->bdc["id_formation_devis"]);
		$this->priseEnCharge = ATF::formation_priseEnCharge()->select_row();

		ATF::formation_participant()->q->reset()->where("id_formation_devis", $this->bdc["id_formation_devis"]);
		$this->participants = ATF::formation_participant()->select_all();
		$this->client = ATF::societe()->select($this->bdc['id_societe']);
		$this->user = ATF::user()->select($this->devis['id_owner']);
		$this->fournisseur = ATF::societe()->select($this->bdc['id_fournisseur']);
				
		$this->societe =  ATF::societe()->select($this->user['id_societe']);	
		// Les styles utiles :
		$this->styleLibAffaire = array(
			array("border"=>"LBT","bgcolor"=>"efefef","decoration"=>"B")
			,array("border"=>"TB","bgcolor"=>"efefef","decoration"=>"B")
			,array("border"=>"TRB","bgcolor"=>"efefef","decoration"=>"B")
		);
		$this->styleLibTotaux = array("decoration"=>"B","align"=>"R");
		$this->styleTotaux = array("decoration"=>"B");
		$this->styleNotice = array("decoration"=>"I","size"=>6);
	}


	/** PDF d'un bon de commande fournisseur de formation
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @date 12-03-2015
	* @param int $id Identifiant bon de commande fournisseur
	*/
	public function formation_bon_de_commande_fournisseur($id,$s) {
		
		$this->initFormationBDC($id,$s,$previsu);
		$this->unsetHeader();
		$this->open();
		$this->addpage();
		
		
		$this->image(__PDF_PATH__."/cleodis/logo.jpg",80,20,40);
		
		$this->setfont('arial','',10);
		
		$this->sety(10);
		$this->multicell(0,5,$this->societe['societe']);
		if ($this->societe['adresse']) $this->multicell(0,5,$this->societe['adresse']);
		if ($this->societe['adresse_2']) $this->multicell(0,5,$this->societe['adresse_2']);
		if ($this->societe['adresse_3']) $this->multicell(0,5,$this->societe['adresse_3']);
		$this->multicell(0,5,$this->societe['cp']." ".$this->societe['ville']);
		
		//CADRE REFERENCE
		$cadre = array(
			"Commande n°".$this->bdc["ref"]
			,"Code client ".$this->client['code_client']
		);
		$this->cadre(130,10,70,15,$cadre,"REFERENCES A RAPPELER");
		//CADRE COMMANDE LE
		$cadre = array(
			$this->user["prenom"]." ".$this->user["nom"]
			,"Téléphone : ".$this->societe['tel']
			,"Fax : ".$this->societe['fax']
			,$this->user["email"]
		);

		$this->cadre(10,28,60,25,$cadre,"Commandé le : ".date("d M Y",strtotime($this->bdc['date'])));
			
		

		//CADRE A L'ATTENTION DE
		$cadre = array(
			$this->fournisseur['societe']
			,ATF::contact()->nom($this->bdc["id_contact"])
			,$this->fournisseur['adresse']
			,$this->fournisseur['adresse_2']?$this->fournisseur['adresse_2']:""
			,$this->fournisseur['adresse_3']?$this->fournisseur['adresse_3']:""
			,$this->fournisseur['cp']." ".$this->fournisseur['ville']
		);
		$this->cadre(130,28,70,30,$cadre,"A l'attention de");
		

		$this->setFont('arial','IB',6);
		$this->setleftmargin(10);
		$this->setrightmargin(5);
		$this->multicell(0,5,"Toute clause de réserve de propriété insérée par nos fournisseurs dans leur documents (conditions générales de ventes, factures, etc...) sera réputée non écrite");
		
		$this->ln(5);
		$this->setfont('arial','BI',12);
		$this->multicell(0,10,"BON DE COMMANDE ".$this->bdc["ref"],0,'C');
		$this->multicell(0,10,$this->bdc["thematique"],0,'C');
		$this->setfont('arial','',10);
		
		if ($this->participants) {
			$head = array("Désignation","Nombre de jours","Total");
			$w = array(150,20,20);

			ATF::formation_devis_ligne()->q->reset()->where("id_formation_devis", $this->devis["id_formation_devis"]);
			$formation_devis_ligne = ATF::formation_devis_ligne()->select_all();


			$nbJours = ATF::formation_devis()->get_nb_jours($formation_devis_ligne);

			$data[] = array(					
				"Formation pour ".count($this->participants)." personne(s)"					
				,$nbJours
				,$this->bdc["montant"]
			); 
			$styles[] = array();

			if($this->bdc["commentaire"]){
				$data[] = array($this->bdc["commentaire"],"" ,""); 
				$styles[] = array();
			}

			$total = $this->bdc["montant"];
			
			
			$h = 20 + $this->getHeightTableau($head,$data,$w,5,$styles); 
			
			$this->tableauBigHead($head,$data,$w,5,$styles);
			
			$totalTable = array(
				"data"=>array(
								array("TOTAL HT",number_format($this->bdc["montant"],2,"."," ")." €")
								,array("TVA (".(($this->fournisseur['tva']-1)*100)."%)",number_format($this->bdc["montant"]*($this->fournisseur['tva']-1),2,"."," ")." €")
								,array("TOTAL TTC",number_format(($this->bdc["montant"]*$this->fournisseur['tva']),2,"."," ")." €")
							)
				,"styles"=>array(
									array($this->styleLibTotaux,$this->styleTotaux)
									,array($this->styleLibTotaux,$this->styleTotaux)
									,array($this->styleLibTotaux,$this->styleTotaux)
								)
				,"w"=>array(170,20)
			);
			
			$this->tableau(false,$totalTable['data'],$totalTable['w'],5,$totalTable['styles']);
			
		}
		$this->sety(200);		
		
		$this->setx(65);
		$this->cell(0,5,"SIGNATAIRES",1,1,'C');
		
		$head = array("Adresse de facturation","VISA EMETTEUR","VISA ACCUSE DE RECEPTION");
		$w = array(55,65,75);
		$data1 = array(
			array(
				$this->societe['societe']."\n"
				.($this->societe['adresse'])."\n"
				.($this->societe['adresse_2'])."\n"
				.($this->societe['adresse_3'])."\n"
				.($this->societe['cp'])." ".$this->societe['ville']."\n"
				,"",""
			)
		);		
		$data2 = array(
			array(
				"Facture à établir en 2 exemplaires.\n Joindre 1 exemplaire de commande à la facture"
				,$this->user["prenom"]." ".$this->user["nom"]
				,$this->fournisseur['societe']
			)
		);
		$styles = array();
		$this->tableau($head,$data1,$w,25,$styles);
		$styles[0][0] = $this->styleNotice;
		$this->tableau(false,$data2,$w,5,$styles);
		
		$this->setfont('arial','',8);

		if($this->priseEnCharge["subro_client"] == "oui"){
			$subro = "de l'OPCA.";
		}else{
			$subro = "du client.";
		}

		$this->multicell(0,10,"Condition de reglement : A reception du reglement ".$subro,0,'C');
	}
};
?>