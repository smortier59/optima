<?
require_once dirname(__FILE__)."/../pdf.class.php";
/**  
* @package Optima
* @subpackage AbsysTech
*/
class pdf_absystech extends pdf {
	var $Rentete = 225;
	var $Gentete = 236;
	var $Bentete = 178;
	var $isLivraison = false;
	private $leftStyle = array(
		"size" => 8
		,"color" => 000000
		,"font" => "arial"
		,"border" => ""
		,"align" => "L"
	);
	
	private $leftStyleItalique = array(
		"size" => 8
		,"color" => 000000
		,"font" => "arial"
		,"decoration" => "I"
		,"border" => ""
		,"align" => "L"
	);
	

	private $rightStyle = array(
		 "size" => 8
		,"color" => 000000
		,"font" => "arial"
		,"border" => ""
		,"align" => "R"
	);
	
	var $repeatEntete = true;

	private $noPageNo = false;

	/**
	* Header des PDF absystech
	*
	*/
	public function Header() {
		if ($this->getHeader()) return false;
		if ($this->isLivraison) {
			$this->image(__PDF_PATH__.ATF::$codename."/bon_de_livraison_top.png",7,1,80);
		}
		if ($this->isPret) {
			$this->image(__PDF_PATH__.ATF::$codename."/bon_de_pret_top.png",7,1,80);
		}
	}
	
	public function Footer() {
		if($this->previsu || $this->forcePrevisu){
			$this->settextcolor('black');
			$this->setfont('arial','BU',18);
			$this->setxy(10,5);
			$this->multicell(0,5,"PREVISUALISATION",0,'C');
		}
		if ($this->getFooter()) return false;
		if (!$this->societe) return false;
		$this->setautopagebreak(false,'1');
		$this->SetXY(10,-10);
		$this->setfont('arial','I',8);
		$this->multicell(0,3,strtoupper($this->societe["societe"])." - ".$this->societe['structure'].($this->societe["capital"] ? " au capital de ".number_format($this->societe["capital"],2,',','.')." EUR" : NULL).($this->societe["siret"] ? " SIRET : ".$this->societe["siret"] : NULL),0,'C');
		$this->multicell(0,3,"Siège social : ".$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville']." - ".ATF::pays()->select($this->societe['id_pays'],"pays")." - ".$this->societe['email']." - Tél : ".$this->societe['tel'].($this->societe['fax'] ? " - Fax : ".$this->societe['fax'] : NULL),0,'C');
		if (!$this->noPageNo) {
			$this->ln(-3);
			$this->Cell(0,3,$this->noPageNo.'Page '.$this->PageNo(),0,0,'R');
		}
	}

	public function cgv() {

		$this->unsetHeader();		
		$this->AddPage();

		$pageCount = $this->setSourceFile(__PDF_PATH__."absystech/cgv.pdf");
		$tplIdx = $this->importPage(1);
		$r = $this->useTemplate($tplIdx, -5, -10, 220, 0, true);	
	}

	public function facture($id,&$s) {
		$id = ATF::facture()->decryptId($id);

		if(ATF::affaire()->select(ATF::facture()->select($id , "id_affaire"), "nature") == "consommable"){
			$this->copieur_facture($id);
		}else{

			$this->setFooter();
			// Couleurs entêtes
			$this->Rentete = 238;
			$this->Gentete = 241;
			$this->Bentete = 255;
			$infos_facture = ATF::facture()->select($id);
			$type_facture=$infos_facture["type_facture"];	
			ATF::facture_ligne()->q->reset()->addCondition('id_facture',$id,"AND")->addCondition('visible',"oui")->end();
			$infos_facture_produit = ATF::facture_ligne()->select_all();			//Si c'est une facture liée à une commande
			ATF::commande_facture()->q->reset()->addCondition('id_facture',$id)->end();
			if($id_commande=ATF::commande_facture()->select_all()){
				$commande=ATF::commande()->select($id_commande[0]["id_commande"]);

				if($commande["prix"]!=$infos_facture["prix"]){
					$infos_facture["frais_de_port"]=$infos_facture["frais_de_port"];
					//Si c'est un solde ou un acompte				
					$sum_anc_facture=ATF::facture()->facture_by_commande($commande["id_commande"],true);
					
				}
			}
			$infos_client = ATF::societe()->select($infos_facture['id_societe']);
			if ($infos_facture['id_affaire']) {
				ATF::devis()->q->reset()->addCondition('id_affaire',$infos_facture['id_affaire'])->setDimension("row")->end();
				$infos_devis = ATF::devis()->select_all(false,false,false,false,true);
				$infos_affaire = ATF::affaire()->select($infos_facture['id_affaire']);
			}
			
			$infos_user = ATF::user()->select($infos_facture["id_user"]);
			$infos_societe = ATF::societe()->select($infos_user['id_societe']);
			$this->societe = $infos_absystech = ATF::societe()->select(1);
			

			if ($infos_client['id_contact_facturation']) {
				$infos_contact = ATF::contact()->select($infos_client['id_contact_facturation']);
			}elseif ($infos_devis['id_contact']) {
				$infos_contact = ATF::contact()->select($infos_devis['id_contact']);
			}

			$this->Open();
			$this->Addpage();
			$this->setleftmargin(15);
			$this->setrightmargin(15);
			$this->setdrawcolor(0,184,255);
			
			$this->image(__PDF_PATH__.ATF::$codename."/facturePage1.jpg",5,0,200);
			
			$this->setfont('arial','',8);
			$this->setxy(85,10);
			$this->multicell(110,10,"Le ".ATF::$usr->date_trans($infos_facture['date'], "force", true).",",0,'R');
			

			$cadre[] = array("size"=>12,"bold"=>true,"txt"=>$infos_client['societe'],"h"=>7);
			$cadre[] = array("size"=>10,"bold"=>false,"txt"=>ATF::$usr->trans($infos_contact['civilite'])." ".$infos_contact['nom']." ".$infos_contact['prenom']);
	 
			if ($infos_client['facturation_adresse']) {
				$cadre[] = array("size"=>10,"txt"=>$infos_client['facturation_adresse']);
				if ($infos_client['facturation_adresse_2']) $cadre[] = array("size"=>10,"txt"=>$infos_client['facturation_adresse_2']);
				if ($infos_client['facturation_adresse_3']) $cadre[] = array("size"=>10,"txt"=>$infos_client['facturation_adresse_3']);
				$cadre[] = array("size"=>10,"txt"=>$infos_client['facturation_cp']." ".$infos_client['facturation_ville']." (".ATF::pays()->nom($infos_client['facturation_id_pays']).")");
			} else {
				$cadre[] =  array("size"=>10,"txt"=>$infos_client['adresse']);
				if ($infos_client['adresse_2']) $cadre[] =  array("size"=>10,"txt"=>$infos_client['adresse_2']);
				if ($infos_client['adresse_3']) $cadre[] =  array("size"=>10,"txt"=>$infos_client['adresse_3']);
				$cadre[] =  array("size"=>10,"txt"=>$infos_client['cp']." ".$infos_client['ville']." (".ATF::pays()->nom($infos_client['id_pays']).")");
			}
			
			if ($infos_client['reference_tva']) $cadre[] = array("size"=>12,"txt"=>"N° TVA : ".$infos_client['reference_tva']);
			
			
			$this->cadre(100,30,90,40,$cadre);

			
			$date = $infos_facture["date_debut_periode"];
			$date = explode("-", $date);
			
			$this->sety(35);
			switch ($type_facture) {
				case "avoir":
					$this->setfont('arial','B',22);	
					$this->multicell(50,8,strtoupper(ATF::$usr->trans($type_facture,"facture_type")),0,'C');
					$this->setfont('arial','',10);	
					$this->multicell(50,5,"N° ".$infos_facture['ref'],0,'C');
					if ($infos_facture['id_facture_parente']) {
						$this->multicell(50,5,"Sur Facture ".ATF::facture()->select($infos_facture['id_facture_parente'] , "ref"),0,'C');
					}
				break;			
				default:
					$this->setfont('arial','B',22);	
					$this->multicell(50,8,strtoupper(ATF::$usr->trans($type_facture,"facture_type")),0,'C');
					$this->setfont('arial','',10);	
					$this->multicell(50,5,"N° ".$infos_facture['ref'],0,'C');
				break;
			}
			
			if ($infos_facture['infosSup']) {	
				$this->setLeftMargin(5);
				$this->setfont('arial','',8);	
				$this->multicell(70,5,$infos_facture['infosSup'],0,'C');
				$this->setLeftMargin(15);
			}
			
			
			if(isset($infos_facture["periodicite"])){
					$this->setfont('arial','B',9);	
					switch ($infos_facture["periodicite"]) {
						case 'mensuelle': 	
								$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',mktime( 0, 0, 0, $date[1], 1, $date[0] ))."/".$date[1]."/".$date[0],0,'L');
						break;
						
						case 'trimestrielle'://4 Trimestres -> 							
								//01 Janvier - 31 Mars
								if(intval($date[1]) <= 3){
									$mois = mktime( 0, 0, 0, 3, 1, $date[0] );
									$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',$mois)."/03/".$date[0],0,'L');
								} elseif(intval($date[1]) <= 6) {
									//01 Avril - 30 Juin
									$mois = mktime( 0, 0, 0, 6, 1, $date[0] );
									$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',$mois)."/06/".$date[0],0,'L');
								} elseif(intval($date[1]) <= 9) {
									//01 Juillet - 30 Septembre
									$mois = mktime( 0, 0, 0, 9, 1, $date[0] );
									$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',$mois)."/09/".$date[0],0,'L');
								} elseif(intval($date[1]) <= 12) {
									//01 Octrobre - 31 Decembre
									$mois = mktime( 0, 0, 0, 12, 1, $date[0] );
									$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',$mois)."/12/".$date[0],0,'L');
								}
						break;
						
						case 'semestrielle' : 
							//01 Janvier au 30 Juin
							if(intval($date[1]) <= 6){
								$mois = mktime( 0, 0, 0, 6, 1, $date[0] );
								$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',$mois)."/06/".$date[0],0,'L');
							}//01 Juillet au 31 Decembre
							else{
								$mois = mktime( 0, 0, 0, 12, 1, $date[0] );
								$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',$mois)."/12/".$date[0],0,'L');
							}						
						break;
						
							
						case 'annuelle':
								$mois = mktime( 0, 0, 0, 12, 1, $date[0] );
								$this->multicell(70,5,ATF::$usr->trans("Période du ").$date[2]."/".$date[1]."/".$date[0].ATF::$usr->trans(" au ").date('t',$mois)."/12/".$date[0],0,'L');
						break;
					}				
			}
			
			

			if($infos_affaire["code_commande_client"]){
				$this->multicell(50,5,"Rf. Commande ".$infos_affaire["code_commande_client"],0,'C');
			}
			
			$this->setfont('arial','',8);	
			$this->setxy(5,75);
			$this->multicell(70,4,"Edité".($type_facture!="avoir"?"e":"")." par : ".ATF::user()->nom($infos_facture['id_user'])." (".ATF::user()->select($infos_facture['id_user'],"email").")",0,'L');
			
			$this->setLeftMargin(5);
			$this->sety(90);
			$head = array("Référence","Désignation","Qté","Prix unitaire","Montant");
			$width = array(25,107,18,25,25);
			
			if ($infos_facture_produit){
				foreach ($infos_facture_produit as $k => $i) {
					$data[$k][0] = $i['ref'];
					$data[$k][1] = $i['produit'];
					if($i["ref"] === "PRORATA") { $style[$k][1] = $this->leftStyleItalique; } else { $style[$k][1] = $this->leftStyle; }
					$data[$k][2] = number_format($i['quantite'],2,',',' ');
					$data[$k][3] = number_format($i['prix'],2,',',' ')." €";
					$style[$k][3] = $this->rightStyle;
					$data[$k][4] = number_format($i['quantite']*$i['prix'],2,',',' ')." €";
					$style[$k][4] = $this->rightStyle;
					$total +=  number_format($i['quantite']*$i['prix'],2,'.','');
				}
			}

			$this->tableau($head,$data,$width,5,$style,260);
			$this->setLeftMargin(24);
				
			$this->setx(24);
			$this->cell(131,4,"",0,0,'C');
			$this->cell(25,4,"Frais de port",1,0,'R');
			$this->cell(25,4,number_format($infos_facture['frais_de_port'],2,',',' ')." €",1,1,'R');

			if(!$infos_facture_produit){
				$this->cell(131,4,"",0,0,'C');
				$this->cell(25,4,"Montant",1,0,'R');
				if($type_facture=="acompte" || $type_facture=="solde")$this->cell(25,4,number_format(abs($commande[0]["prix"]),2,',',' ')." €",1,1,'R');
				else $this->cell(25,4,number_format(abs($infos_facture['prix']-$infos_facture['frais_de_port']),2,',',' ')." €",1,1,'R');
			}
			
			$this->cell(131,4,"",0,0,'C');
			
			$this->cell(25,4,"Total HT",1,0,'R');




			switch ($type_facture) {
				case "solde":
					$this->cell(25,4,number_format($infos_facture["prix"]+$sum_anc_facture["prix"],2,',',' ')." €",1,1,'R');
					$this->cell(131,4,"",0,0,'C');
					$y = $this->getY();
					$this->cell(25,4,"Acompte(s)" ,"LR",1,'R');
					$this->cell(131,4,"",0,0,'C');
					$this->cell(25,4, "facturé(s) HT","LRB",0,'R');
					$this->setY($y);
					$this->cell(156,4,"",0,0,'C');
					$this->cell(25,8,number_format($sum_anc_facture["prix"],2,',',' ')." €",1,1,'R');	

					$this->cell(131,4,"",0,0,'C');
					$this->cell(25,4,"Solde HT",1,0,'R');
					$this->cell(25,4,number_format($infos_facture["prix"],2,',',' ')." €",1,1,'R');	

					$solde = $infos_facture["prix"];	

					$this->cell(131,4,"En votre aimable règlement,",0,0,'L');
				break;
				case "acompte":
					$this->cell(25,4,number_format($commande["prix"],2,',',' ')." €",1,1,'R');
					// accompte sur le solde sinon total HT
					$accompte = $infos_facture["prix"]/$commande["prix"];
					$this->cell(131,4,"Facture d'acompte de ".round($accompte*100)."% du total hors taxe ",0,0,'L');
					$infos_facture["prix"] = $commande["prix"] * $accompte;
					$this->cell(25,4,"Acompte HT ".round(($accompte)*100)."%",1,0,'R');
					$this->cell(25,4,number_format($infos_facture["prix"],2,',',' ')." €",1,1,'R');		
					$this->cell(131,4,"En votre aimable règlement,",0,0,'L');	
				break;
				case "avoir":
					// SI on traite un avoir, celui ci doit avoir le même comportement que sa facture parente
					$parente = ATF::facture()->select($infos_facture['id_facture_parente']);

					if ($parente['type_facture']=="acompte") {
						ATF::commande_facture()->q->reset()->addCondition('id_facture',$parente['id_facture'])->end();
						if($id_commande=ATF::commande_facture()->select_all()){
							$commande=ATF::commande()->select($id_commande[0]["id_commande"]);

							$this->cell(25,4,number_format($commande["prix"],2,',',' ')." €",1,1,'R');

						    $accompte = $parente["prix"]/$commande["prix"];
							$this->cell(131,4,"Facture d'acompte de ".round(($accompte)*100)."%",0,0,'L');
							$this->cell(25,4,"Acompte HT ".round(($accompte)*100)."%",1,0,'R');
							$this->cell(25,4,number_format($parente["prix"],2,',',' ')." €",1,1,'R');		

							$this->cell(131,4,"En votre aimable règlement,",0,0,'L');		
						}					
					} else if ($parente['type_facture']=="solde") {

						ATF::commande_facture()->q->reset()->addCondition('id_facture',$infos_facture['id_facture_parente'])->end();
						if($id_commande=ATF::commande_facture()->select_all()){
							$commande=ATF::commande()->select($id_commande[0]["id_commande"]);

							if($commande["prix"]!=$infos_facture["prix"]){
								$infos_facture["frais_de_port"]=$infos_facture["frais_de_port"];
								//Si c'est un solde ou un acompte				
								$sum_anc_facture=ATF::facture()->facture_by_commande($commande["id_commande"],true);
								
							}
						}


						$this->cell(25,4,number_format($parente["prix"],2,',',' ')." €",1,1,'R');
						$this->cell(131,4,"",0,0,'C');
						$this->cell(25,4,"Déjà Payé HT",1,0,'R');
						$this->cell(25,4,number_format($sum_anc_facture["prix"],2,',',' ')." €",1,1,'R');	

						$this->cell(131,4,"",0,0,'C');
						$this->cell(25,4,"Solde HT",1,0,'R');						
						
						$infos_facture["prix"] = ($parente["prix"] + $sum_anc_facture["prix"])*-1;



						$this->cell(25,4,number_format($parente["prix"],2,',',' ')." €",1,1,'R');
						$this->cell(131,4,"",0,0,'C');

					} else {
						$infos_facture["prix"] = ($infos_facture["prix"] + $sum_anc_facture["prix"])*-1;



						$this->cell(25,4,number_format($infos_facture["prix"],2,',',' ')." €",1,1,'R');
						$this->cell(131,4,"",0,0,'C');
						
					}

					$infos_facture['prix'] = abs($infos_facture['prix']);



					//ATF::facture()->u(array("id_facture"=>$id , "prix"=>$infos_facture["prix"]));

				break;
				default:				
					if(($sum_anc_facture["prix"] - $infos_facture["prix"]>0)){
						$this->cell(131,4,"",0,0,'C');
						$this->cell(25,4,"Déjà Payé HT",1,0,'R');
						$this->cell(25,4,number_format($sum_anc_facture["prix"],2,',',' ')." €",1,1,'R');
						$infos_facture["prix"] = $infos_facture["prix"] - $sum_anc_facture["prix"];
					}

					$this->cell(25,4,number_format($infos_facture["prix"],2,',',' ')." €",1,1,'R');
					$this->cell(131,4,"En votre aimable règlement,",0,0,'L');
				break;
			}
		
			if((float)($infos_facture["tva"])>1){
				if($type_facture == "solde"){
					$TTC = $solde * $infos_facture["tva"];
					$tva = $solde * ($infos_facture["tva"]-1);
				}else{
					$TTC=$infos_facture['prix']*$infos_facture["tva"];
					$tva = $TTC-$infos_facture["prix"];
				}
				$this->cell(25,4,"TVA ".round($infos_facture["tva"]*100-100,2)."%",1,0,'R');	
				$this->cell(25,4,number_format(abs($tva),2,',',' ')." €",1,1,'R');		
				$this->cell(131,4,"",0,0,'C');
				$this->setfont('arial','B',8);
				$this->cell(25,4," Montant TTC",1,0,'R'); 
				if($type_facture == "avoir"){ $this->cell(25,4,number_format($TTC,2,',',' ')." €",1,1,'R'); }
				else{ $this->cell(25,4,number_format(abs($TTC),2,',',' ')." €",1,1,'R'); }
				
			}elseif (method_exists($this,"facture_exoneration_tva")){
				$this->facture_exoneration_tva($infos_facture,$infos_client);
			}
				
			
			if ($this->gety()>225) $this->AddPage();
			else $this->sety(225);
			$this->setleftmargin(15);
			$this->setrightmargin(15);
			//$this->sety(220);
			$this->setfont('arial','',8);
			$this->ln(5);
			$y = $this->gety();
			if($infos_facture['id_termes']){
				$cadre = array(
					array("txt"=>"\n".ATF::termes()->nom($infos_facture['id_termes']),"align"=>"C","h"=>5)
				);
			}else{
				$cadre = array(
					array("txt"=>"\n".ATF::termes()->nom($infos_affaire['id_termes']),"align"=>"C","h"=>5)
				);
			}
			
			
			$this->cadre(15,$y,50,15,$cadre,"Termes de paiement :");
			
			$cadre = array(
				array("txt"=>$infos_client['ref'],"align"=>"C")
			);
			$this->cadre(15,$y+20,50,15,$cadre,"Votre numéro client :");

			$cadre = array();
			if($type_facture=="factor"){
				$cadre[] = "Crédit agricole Nord de France - Eurofactor";
				$cadre[] = "RIB : ".util::formatRIB($infos_client["rib_affacturage"]);
				$cadre[] = "IBAN : ".$infos_client["iban_affacturage"];
				$cadre[] = "BIC : ".$infos_client["bic_affacturage"];
			}elseif ($infos_absystech["banque"]) {
				$cadre[] = $infos_absystech["banque"];
				if ($infos_absystech['rib']) {
					$cadre[] = "RIB : ".util::formatRIB($infos_absystech["rib"]);
				}
				if ($infos_absystech['iban']) {
					$cadre[] = "IBAN : ".$infos_absystech["iban"];
				}
				if ($infos_absystech['bic']) {
					$cadre[] = "BIC : ".$infos_absystech["bic"];
				}
				if ($infos_absystech['swift']) {
					$cadre[] = "SWIFT : ".$infos_absystech["swift"];
				}
				if ($infos_absystech['reference_tva']) {
					$cadre[] = "TVA : ".$infos_absystech['reference_tva'];
				}
			}
			
			$this->cadre(75,$y,60,35,$cadre,"Coordonnées bancaires");
			
			$cadre = array();
			if($type_facture=="factor"){
				$cadre[] = "Merci de bien vouloir régler cette facture directement à Eurofactor.";
				$cadre[] = "Coordonnées bancaires indiquées ci-contre.";
			}else{
				$cadre[] = "En cas de règlement par chèque, merci de l'adresser à :";
				$cadre[] = $infos_societe['societe'];
				if ($infos_absystech["adresse_2"]) $cadre[] = $infos_absystech["adresse_2"];
				$cadre[] = $infos_absystech["adresse"];
				$cadre[] = $infos_absystech["cp"]." ".$infos_absystech["ville"];
			}
			
			$this->cadre(145,$y,50,35,$cadre,"Règlement");
			
			$this->ln(-5);
			$this->setfont('arial','B',8);
			$this->multicell(0,5,"Tout paiement anticipé fera l'objet d'un escompte calculé au prorata au taux de 4,8% l'an",0,'C');
			$this->setfont('arial','',5);
			$this->multicell(0,2,"Tout paiement postérieur à la date d'échéance entraîne l'exigibilité d'intérêts de retard au taux de 12% par an. A compter du 1er janvier 2013, une indemnité de recouvrement de 40€, non soumise à la TVA, est applicable sur chaque facture impayée à date et s'ajoute aux intérêts de retard précédemment cités, en application des articles L441-3 et L441-6 du code de commerce.",0,'C');


			if (ATF::_r('dematerialisation')) {
				$this->noPageNo = true;
				$this->addpage();

				$this->image(__PDF_PATH__.'absystech/AT.jpg',10,10,50);
				
				$this->setfont('arial','',9);
				$this->sety(30);

				$this->multicell(0,5,"Madame, Monsieur, ");
				$this->ln(5);

				$this->multicell(0,5,"Pour vous offrir un meilleur service, et dans la continuité de sa démarche zéro papier, ABSYSTECH vous propose de dématérialiser l’envoi de ses factures par e-mail.");
				$this->ln(3);
				$this->multicell(0,5,"Ce nouveau mode d’envoi garantit une gestion optimale, économique et environnementale du traitement des factures et offre de multiples atouts : tranquillité et souplesse, réception instantanée de vos factures par le ou les interlocuteur(s) de votre choix.");
				$this->ln(3);
				$this->multicell(0,5,"Afin de ne pas bouleverser vos habitudes et méthodes de travail, pourriez-vous nous donner votre accord pour recevoir désormais vos factures par e-mail.");
				$this->ln(3);
				$this->multicell(0,5,"Il suffit de nous adresser le coupon ci-dessous par e-mail, à l’adresse suivante : facturation@absystech.fr ou par fax au 03 20 74 50 05.");
				$this->ln(3);

				$this->ln(15);

				$this->checkbox(false,4);
				$this->cell(0,6,"Je souhaite recevoir mes factures par e-mail",0,1);
				$this->ln(5);

				$head = array("Nom","Prénom","Email","Tél.","Signature + Cachet");
				$data = array(
					array("","","","",""),
					array("","","","",""),
					array("","","","","")
				);

	 
				$this->tableau($head,$data,array(30,30,45,20,50),30);
				$this->setfont('arial','',9);

				$this->ln(10);
				$this->checkbox(false,4);
				$this->cell(0,6,"Je ne souhaite PAS recevoir mes factures par e-mail",0,1);
				$this->ln(5);

				$this->multicell(0,5,"Nous vous remercions de votre confiance, et vous prions d’agréer, Madame, Monsieur, l’expression de nos salutations distinguées.");
				

				$this->ln(15);

				$this->cell(90,5,"Sébastien MORTIER",0,0,"C");
				$this->cell(90,5,"Emma GREMY",0,1,"C");
				$this->cell(90,5,"Gérant",0,0,"C");
				$this->cell(90,5,"Assistante",0,0,"C");

			}
		}


		//$this->pied($infos_societe);	
	}
	
	public function devis($id,&$s) {

		$infos_devis = ATF::devis()->select($id);

		if($infos_devis["type_devis"] == "consommable"){
			$this->copieur_contrat($id);
		}else{
			$this->devis_normal($id);
		}
	}


	public function devis_normal($id){
		$infos_devis = ATF::devis()->select($id);
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",ATF::devis()->decryptId($id),"AND")->addCondition('visible',"oui")->addOrder("id_devis_ligne","asc")->end();
		$infos_devis_produit = ATF::devis_ligne()->select_all();
		$infos_affaire = ATF::affaire()->select($infos_devis['id_affaire']);

		$infos_client = ATF::societe()->select($infos_devis['id_societe']);
		$infos_contact = ATF::contact()->select($infos_devis['id_contact']);
		$infos_user = ATF::user()->select($infos_devis["id_user"]);
		$infos_agence = ATF::agence()->select($infos_user['id_agence']);
		$infos_societe = ATF::societe()->select($infos_user['id_societe']);
			
		if ($infos_devis['etat']=="bloque") $this->forcePrevisu = true;


		//DEVIS PAGE 1

		$this->Open();
		$this->Addpage();
		$this->setleftmargin(10);
		$this->setrightmargin(15);
		$this->setfont('arial','',10);

		$this->image(__PDF_PATH__.ATF::$codename."/entete.jpg",5,3,200);

		$this->multicell(0,5,"A ".$infos_agence['agence'].", le ".ATF::$usr->date_trans($infos_devis['date'],true),0,'R');

		$this->ln(6);
	
		$this->setleftmargin(123);
		$this->setfont('arial','',12);
		$this->multicell(0,5,$infos_client['societe']);
		$this->multicell(0,5,$infos_client['adresse']);
		if ($infos_client['adresse_2']) $this->multicell(0,5,$infos_client['adresse_2']);
		if ($infos_client['adresse_3']) $this->multicell(0,5,$infos_client['adresse_3']);
		$this->multicell(0,5,$infos_client['cp']." ".$infos_client['ville']);
		$this->setfont('arial','',10);
		$this->setleftmargin(10);
		$this->ln(10);
		
		$this->multicell(0,7,"Devis n°".$infos_devis['ref']."-".$infos_devis['revision']." valable jusqu'au ".ATF::$usr->date_trans($infos_devis['validite'],"force"));
		$this->multicell(0,7,"Objet : ".$infos_devis['resume']);
		$this->multicell(0,5,"Termes de paiement : ".ATF::termes()->nom($infos_affaire['id_termes']));
		
		$termes = ATF::termes()->nom($infos_affaire['id_termes']);
		
		if($infos_devis["acompte"]){

			$acompteHT = $infos_devis["prix"] * $infos_devis["acompte"]/100;

			$acompteTTC = $acompteHT* $infos_devis["tva"];
			$this->setLeftMargin(45);
			$this->multicell(0,5,"Acompte de ".number_format($infos_devis["acompte"])."% = ".number_format($acompteHT,2, ","," ")." € HT soient ".number_format($acompteTTC, 2,",", " ")." € TTC");
			$this->setLeftMargin(10);
		}
		

		
		if(ATF::delai_de_realisation()->nom($infos_devis['id_delai_de_realisation'])){
			$this->multicell(0,5,"Délai de réalisation : ".ATF::delai_de_realisation()->nom($infos_devis['id_delai_de_realisation']));
		}	
		
		
		$this->ln(3);
	
		$this->multicell(0,5,ATF::politesse()->nom($infos_devis['id_politesse_pref']));
		
		$this->ln(3);

		$periodique = array();
		$non_periodique = array();
		foreach ($infos_devis_produit as $key => $value) {			
			if($value["periode"]){
				$periodique[] = $value;
			}else{
				$non_periodique[]=$value;
			}			
		}
		if(!empty($periodique)){
			$head = array("Référence","Désignation","Qté","Prix unitaire","Total / ".$periodique[0]["periode"]);
			$width = array(25,105,11,20,25);
			$totalPeriodique = 0;
			foreach ($periodique as $k => $i) {				
				$data[$k][0] = $i['ref'];
				$data[$k][1] = $i['produit'];
				$style[$k][1] = $this->leftStyle;
				$data[$k][2] = number_format($i['quantite'],2,',',' ');
				$data[$k][3] = number_format($i['prix'],2,',',' ')." €";
				$style[$k][3] = $this->rightStyle;
				$data[$k][4] = number_format($i['quantite']*$i['prix'],2,',',' ')." €";
				$style[$k][4] = $this->rightStyle;
				$totalPeriodique +=  number_format($i['quantite']*$i['prix'],2,'.','');
				$periode = $i["periode"];
			}
			$this->tableau($head,$data,$width,5,$style,270);
			$this->cell(141,5,"",0,0,'L');	
			$this->cell(20,5,"Total HT",1,0,'R');
			$this->cell(25,5,number_format($totalPeriodique,2,',',' ')." €",1,1,'R');			
			if (defined("__TVA__") && __TVA__>1) {
				$this->cell(141,5,"",0,0,'L');
				$this->cell(20,5,"Total TTC",1,0,'R');
				$this->cell(25,5,number_format($totalPeriodique * __TVA__,2,',',' ')." €",1,1,'R');
			}
			$this->ln(5);		
		}


		if(!empty($non_periodique)){
			$data = array();
			if($infos_devis["type_devis"] == "normal") { 
				$head = array("Référence","Désignation","Qté","Prix unitaire","Total"); 
				$width = array(25,105,11,20,25);
			}else{ 
				$head = array("Référence","Désignation","Qté","Total mensuel"); 
				$width = array(25,116,23,22);

			}
			
			if ($infos_devis_produit)  {
				$total = 0;
				foreach ($infos_devis_produit as $k => $i) {
					if(!$i["periode"]){
						$data[$k][0] = $i['ref'];
						$data[$k][1] = $i['produit'];
						$style[$k][1] = $this->leftStyle;
						$data[$k][2] = number_format($i['quantite'],2,',',' ');
						if($infos_devis["type_devis"] == "normal"){
							$data[$k][3] = number_format($i['prix'],2,',',' ')." €";
							$style[$k][3] = $this->rightStyle;
							$data[$k][4] = number_format($i['quantite']*$i['prix'],2,',',' ')." €";
							$style[$k][4] = $this->rightStyle;
							$total +=  number_format($i['quantite']*$i['prix'],2,'.','');
						}else{ $data[$k][3] = "-"; }
						
					}	
				}
			}
			$this->tableau($head,$data,$width,5,$style,270);
			$total += $infos_devis['frais_de_port'];
			
			

			if($infos_devis["type_devis"] == "normal"){
				$this->cell(141,5,"",0,0,'L');
				$this->cell(20,5,"Frais de port",1,0,'R');
				$this->cell(25,5,number_format($infos_devis['frais_de_port'],2,',',' ')." €",1,1,'R');
				$this->cell(141,5,"",0,0,'L');				
				$this->cell(20,5,"Total HT",1,0,'R');
				$this->cell(25,5,number_format($total,2,',',' ')." €",1,1,'R');
			}else{ 				
				$this->cell(141,5,"",0,0,'L');
				$this->cell(23,5,"Total HT /mois",1,0,'R');
				$this->cell(22,5,number_format($infos_devis["prix_location"],2,',',' ')." €",1,1,'R');
			}
			
			if(($infos_client["facturation_id_pays"]=="FR" && $infos_client["facturation_adresse"]) || (!$infos_client["facturation_adresse"] && $infos_client["id_pays"]=="FR") && (!$infos_client["reference_tva"] || substr($infos_client["reference_tva"],0,2)=="FR")){
				if (defined("__TVA__") && __TVA__>1) {
					$this->cell(141,5,"",0,0,'L');
					
					if($infos_devis["type_devis"] == "normal"){
						$this->cell(20,5,"Total TTC",1,0,'R');
						$this->cell(25,5,number_format($total * __TVA__,2,',',' ')." €",1,1,'R');
					}else{ 
						$this->cell(23,5,"Total TTC /mois",1,0,'R');
						$this->cell(22,5,number_format($infos_devis["prix_location"]* __TVA__,2,',',' ')." €",1,1,'R');
					}

				}
			}else{
				$this->cell(141,5,"",0,0,'L');
				$this->cell(20,5,"Total exonéré",1,0,'R');
				$this->cell(25,5,number_format($total,2,',',' ')." €",1,1,'R');
			}
		}
		
		$this->setfont('arial','',10);
		$this->ln(5);
		$this->setx(15);
		$this->multicell(0,5,ATF::politesse()->nom($infos_devis['id_politesse_post']));
		$this->multicell(0,5,$infos_user['civilite'].". ".$infos_user['nom']." ".$infos_user['prenom'],0,'R');

		$this->pied($infos_societe);

							
		//BON POUR ACCORD, PAGE 2 DEVIS

		$this->Addpage();
		$this->setleftmargin(15);
		$this->setrightmargin(15);
		
		$this->image(__PDF_PATH__.ATF::$codename."/suite.jpg",5,3,200);

		$this->setfont('arial','BU',18);
		$this->multicell(0,5,"Bon pour accord",0,'C');
		
		$this->ln(20);
		$this->setfont('arial','',10);
		$this->cell(50,5,"Offre",1,0,'C');
		$this->cell(70,5,"Client",1,0,'C');
		$this->cell(60,5,"Contact",1,1,'C');
		$this->cell(50,5,$infos_devis['ref']."-".$infos_devis['revision'],1,0,'C');
		$y = $this->getY();
		$this->multicell(70,5,$infos_client['societe'],1,'C');
		$this->setY($y);
		$this->setX(135);
		$this->cell(60,5,$infos_contact['civilite'].". ".$infos_contact['nom']." ".$infos_contact['prenom'],1,1,'C');

		$this->ln(5);
		$this->setfont('arial','U',10);
		$this->cell(60,5,"CONTACT COMMERCIAL :",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,ATF::user()->nom($infos_user["id_user"]),0,1);
		$this->ln(5);
		$this->setfont('arial','U',10);
		$this->cell(60,5,"CONTACT TECHNIQUE :",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,ATF::user()->nom($infos_devis['id_user_technique']),0,1);
		$this->ln(5);
		$this->setfont('arial','U',10);
		$this->cell(60,5,"CONTACT ADMINISTRATIF :",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,ATF::user()->nom($infos_devis['id_user_admin']),0,1);
		$this->ln(10);
		$this->setfont('arial','U',10);
		$this->cell(60,5,"NATURE DE LA PRESTATION :",0,1);
		$this->setfont('arial','',10);
		$this->multicell(0,5,$infos_devis['resume'],0,'L',1);
		$this->ln(10);
		$this->setfont('arial','U',10);
		$this->cell(60,5,"CONDITIONS FINANCIERES :",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,"Notre prix forfaitaire pour la prestation s'élève à : ",0,1);

		$this->ln(10);
		$this->setfont('arial','B',10);
		
		if(! empty($periodique)){
			if(!empty($non_periodique)){ $width = 90; } else { $width = 0; }
			$this->cell($width,5,number_format($totalPeriodique,2,',',' ')." € HT/".$periode,'TRL',0,'C');
		}else{
			$width = 0;
		}

		if(!empty($non_periodique)){
			if($infos_devis["type_devis"] == "normal"){
				if(!$infos_devis["duree_financement"]){
					$this->cell($width,5,number_format($total,2,',',' ')." € HT",'TRL',1,'C'); 
				}else{
					$total = ($infos_devis["cout_total_financement"] / $infos_devis["duree_financement"]) - $infos_devis["maintenance_financement"];
					$this->cell($width,5,number_format($total,2,',',' ')." € HT/mois sur ".$infos_devis["duree_financement"]." mois",'TRL',1,'C'); 
				}
			}else{ $this->cell($width,5,number_format($infos_devis["prix_location"],2,',',' ')." € HT/mois sur ".$infos_devis["duree_location"]." mois",'TRL',1,'C'); }

			
		}else{
			$this->cell($width,5,"",0,1,'C'); 
		}
		
		
		$this->setfont('arial','',10);	
		if(! empty($periodique)){
			if (strpos($totalPeriodique,'.') || strpos($totalPeriodique,',')) {
				$prix_en_lettrePeriodique = util::nb2texte(substr($totalPeriodique,0,strpos($totalPeriodique,'.')))."euros et ".util::nb2texte(($totalPeriodique-floor($totalPeriodique))*100)."centimes hors taxes par ".$periode;
			}else{
				$prix_en_lettrePeriodique = util::nb2texte(substr($totalPeriodique,0,strlen($totalPeriodique)))."euros hors taxes par ".$periode;
			}
			$flag = true;
			$y = $this->getY();
			$this->multicell($width,5,$prix_en_lettrePeriodique,'BRL','C');
		}


		if($infos_devis["type_devis"] == "normal"){
			if (strpos($total,'.') || strpos($total,',')) {
				$prix_en_lettre = util::nb2texte(substr($total,0,strpos($total,'.')))."euros et ".util::nb2texte(($total-floor($total))*100)."centimes hors taxes";
			}else{
				$prix_en_lettre = util::nb2texte(substr($total,0,strlen($total)))."euros hors taxes";
			}	
			
			if($infos_devis["duree_financement"]){
				$prix_en_lettre .= " par mois sur ".util::nb2texte(substr($infos_devis["duree_financement"],0,strlen($infos_devis["duree_financement"])))."mois";
			}
		}else{
			if( (strpos($infos_devis["prix_location"],'.') || strpos($infos_devis["prix_location"],',')) &&  (!strpos($infos_devis["prix_location"],'.00') && !strpos($infos_devis["prix_location"],',00')) ){
				$prix_en_lettre = util::nb2texte(substr($infos_devis["prix_location"],0,strpos($infos_devis["prix_location"],'.')))."euros et ".util::nb2texte(($infos_devis["prix_location"]-floor($infos_devis["prix_location"]))*100)."centimes hors taxes";
			}else{	$prix_en_lettre = util::nb2texte(substr($infos_devis["prix_location"],0,strlen($infos_devis["prix_location"])))."euros hors taxes";	}
			$prix_en_lettre .= " par mois sur ".$infos_devis["duree_location"]." mois";
		}
		
						
		if($flag){
			$this->setY($y);
			$x = $width+15;
			$this->setX($x);
			if(!empty($non_periodique)){
				$this->multicell($width,5,$prix_en_lettre,'BRL','C');
			}
			$y = $y + 15;
		}else{
			if(!empty($non_periodique)){
				$this->cell($width,5,$prix_en_lettre,'BRL',1,'C');
			}
			$y = 155;
		}
		

		if($infos_devis["type_devis"] != "location"){
			if($infos_devis["financement_cleodis"] == "oui"){
				if($total > 1500 && !$infos_devis["duree_financement"]){
					$this->ln(2);
					if($total <= 7500){
						$coeffCleodis = 0.03378;	
					}elseif($total <= 15000){
						$coeffCleodis = 0.03244;	
					}elseif($total <= 30000){
						$coeffCleodis = 0.03070;	
					}elseif($total > 30000){
						$coeffCleodis = 0.02985;	
					}	
					
					$total = ($total*$coeffCleodis);		

					$this->cell(0,30,"",1);
					$this->ln(5);
					$this->setx(15);
					$this->multicell(0,5,"Pensez «Location Evolutive» avec notre partenaire");
					$this->image(__PDF_PATH__.ATF::$codename."/cleodis.jpg",100,$this->gety()-9,20);
					$this->multicell(0,5,"Pour ce dossier, le loyer est estimé à ".number_format($total,2,',',' ')." € HT/mois sur 36 (+3) mois.*");			
					$this->ln(5);
					$this->multicell(0,5,"Si vous êtes intéressé  par ce mode de financement, merci de cocher la case");
					$this->setxy(140,$this->gety()-5);
					$this->checkbox(false,4);
				}
			}
		}	
		
		
		
		if($infos_devis["maintenance_financement"]){
			$this->setfont('arial','B',10);
			$this->ln(12);
			$this->cell(0,5,$infos_devis["maintenance_financement"]." € HT/mois sur ".$infos_devis["duree_financement"]." mois de maintenance",1,1,'C'); 
			$this->setfont('arial','',10);
			$this->ln(5);
		} else {
			$this->ln(20);		
		}		
		
		$this->setfont('arial','U',10);
		$this->cell(60,5,"TERMES DE PAIEMENT",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,ATF::termes()->nom($infos_affaire['id_termes']),0,1);
		if($infos_devis["acompte"]){
			$acompteHT = number_format((number_format($infos_devis["prix"],2,".","") * $infos_devis["acompte"])/100 , 2, ",", " ");

			$nacompteHT = str_replace(" ", "", $acompteHT);	
			$acompteTTC = number_format($nacompteHT* $infos_devis["tva"], 2,",", " ");
						
			$this->multicell(0,5,"Acompte de ".$infos_devis["acompte"]."% = ".$acompteHT." € HT soient ".$acompteTTC." € TTC");			
		}


		$this->setfont('arial','U',10);
		$this->cell(60,5,"DELAI DE REALISATION",0,1);
		$this->setfont('arial','',10);
		$this->cell(0,5,ATF::delai_de_realisation()->nom($infos_devis['id_delai_de_realisation']),0,1);
		
		$this->cadre(20,220,80,60,array(array('txt'=>"Date / Cachet / Visa",'align'=>"C"),"","","","","",""),"Partie réservée au client");
		$this->setfont('arial','',10);
		$this->cadre(110,220,80,60,array(array('txt'=>"Date / Cachet / Visa",'align'=>"C"),"","","","","",""),"Partie réservée à Absystech");

	
		$this->pied($infos_societe);
		if(ATF::$usr->get('id_societe')){
			$this->cgv($s);
		}
	}
	

	public function copieur_contrat($id) {
   		$this->setFooter();
   		$this->el = ATF::devis()->select($id);
   		$this->client = ATF::societe()->select($this->el['id_societe']);
		ATF::devis_ligne()->q->reset()->where('id_devis',$this->el['id_devis']);
		$this->lignes = ATF::devis_ligne()->sa();

		$this->addPage();
   		$y = $this->getY();
		$this->setfont('arial','B',10);   

		$this->image(__PDF_PATH__.ATF::$codename."/absystech_medaillon_cadre.jpg",10,10,30);

		$this->setLeftMargin(50);
		$this->cell(40,15,"LE PRESTATAIRE",1,0,"C");
		$this->setfont('arial','B',8);   
		$this->cell(0,5,ATF::societe()->maSociete['societe']." - ".ATF::societe()->getAddress(ATF::societe()->maSociete['id_societe'],true),"TRL",1);
		$this->setfont('arial','',8);   
		$this->setx(90);
		$this->cell(0,5,"Tél : ".ATF::societe()->maSociete['tel']." - Fax : ".ATF::societe()->maSociete['fax'],"RL",1);
		$this->setx(90);
		$this->setfont('arial','',6);   
		$this->cell(0,5,ATF::societe()->maSociete['structure']." au capital de ".ATF::societe()->maSociete['capital']." euros - RCS Roubaix Tourcoing B 444 804 066 - APE 6202A","BRL",1);

		$this->setfont('arial','B',10);   
		$this->cell(40,15,"LE CLIENT",1,0,"C");
		$this->cell(0,5,$this->client['societe'],"TRL",1);
		$this->setx(90);
		$this->cell(0,5,$this->client['adresse'],"RL",1);
		$this->setx(90);
		$this->cell(0,5,$this->client['cp']." ".$this->client['ville'],"BRL",1);

		$this->setfont('arial','B',10);   
		$this->cell(0,10,"Contrat de maintenance n°".$this->el['ref'],1,1,'C');

		$this->ln(10);
		$this->setLeftMargin(15);
		$this->setfont('arial','B',10);   
		$this->multicell(0,5,"ARTICLE 1 : DESCRIPTION DES EQUIPEMENTS MAINTENUS ET TARIFICATION");
		$this->ln(5);

		$this->setfont('arial','',10);   
		$this->multicell(0,5,"Le prix de la maintenance est tarifé aux conditions suivantes :");
		$this->ln(5);
		$this->setfont('arial','B',10);   

		$head = array("Quantité","Désignation","Coût de la page imprimée en noir et blanc","Coût de la page imprimée en couleur ");
		$w = array(20,80,40,40);

		$priceNB = $priceC = "";
		foreach ($this->lignes as $k=>$i) {
			$data[] = array($i['quantite'],$i['produit'], round($i['prix_nb'],5)." € HT", round($i['prix_couleur'],5)." € HT");					
		}

		$this->tableau($head,$data,$w);

		$this->ln(5);
		$this->setfont('arial','',10);   
		$this->setLeftMargin(15);		
		$this->multicell(0,5,"Par dérogation à l'article 7.2 des Conditions Générales, la facturation des pages ne porte pas sur un engagement de volume annuel, mais sur le réel consommé, selon le volume de pages qui sera indiqué par le relevé automatique. Le règlement s'effectuera mensuellement par prélèvement automatique terme à échoir.");


		$this->ln(10);
		$this->setLeftMargin(15);
		$this->setfont('arial','B',10);   
		$this->multicell(0,5,"ARTICLE 2 : DUREE DU CONTRAT");
		$this->ln(5);

		$this->setfont('arial','',10);   
		$this->multicell(0,5,"La durée de la maintenance est fixée à ".$this->el['duree_contrat_cout_copie']." mois.");

				
		$this->ln(10);
		$this->setLeftMargin(15);
		$this->setfont('arial','B',10);   
		$this->multicell(0,5,"ARTICLE 3 : VALIDITE");
		$this->ln(5);

		$this->setfont('arial','',10);   
		$this->multicell(0,5,"La présente proposition ne deviendra une offre ferme qu'après acceptation du Comité des Agréments d'AbsysTech.");

		$this->ln(5);
		$this->setfont('arial','BI',10);   
		$this->multicell(0,5,"Fait en double exemplaire");

		$cadre = array(" ","Fait à :","Le : ","Nom : ","Qualité : "," "," "," "," "," ",array("txt"=>"Signature et cachet commercial","align"=>"C","size"=>10));
		$y = $this->gety()+5;
		$this->cadre(15,$y,80,60,$cadre,"Le client");
		$this->cadre(115,$y,80,60,$cadre,"Le Prestataire");

		$this->cgvContratCopieur();

	}


	public function relance($id) {
		$this->facture = ATF::facture()->select($id);
		$this->user = ATF::user()->select($this->facture["id_user"]);
		$this->societe = ATF::societe()->select($this->user['id_societe']);
		$this->societe_client = ATF::societe()->select($this->facture['id_societe']);
		$this->relance = ATF::relance()->getEl($this->facture['id_facture']);
		if (!$this->interet) {
			ATF::facture()->q->reset()
								->where('facture.id_facture',$this->facture['id_facture'])
								->setDimension('row');
			$facture = ATF::facture()->select_all();
			$this->interet = round($facture['interet'],2);		
		}
		$this->noPageNo = true;
		$this->open();
		$this->addpage();
		$this->SetLeftMargin(15);
		$this->SetY(20);
		
		$date = ATF::_r('date')?ATF::_r('date'):date("d/m/Y");
		
		
		$this->SetFont('Arial','B',12);
		$this->Cell(40,5,$this->societe['societe'],0,0);
		$this->SetFont('Arial','',12);
		$this->MultiCell(0,5,$this->societe['ville'].", le ".$date,0,'R');
		$this->MultiCell(0,5,$this->societe['adresse']);
		if ($this->societe['adresse_2']) $this->MultiCell(0,5,$this->societe['adresse_2']);
		if ($this->societe['adresse_3']) $this->MultiCell(0,5,$this->societe['adresse_3']);
		$this->MultiCell(0,5,$this->societe['cp']." ".$this->societe['ville']);
		
		$this->SetLeftMargin(120);
		$this->Sety(40);
		$this->SetFont('Arial','B',12);
		$this->MultiCell(0,5,$this->societe_client['societe']);
		$this->SetFont('Arial','',12);
		$this->MultiCell(0,5,$this->societe_client['adresse']);
		if ($this->societe_client['adresse_2']) {
			$this->MultiCell(0,5,$this->societe_client['adresse_2']);
		}
		if ($this->societe_client['adresse_3']) {
			$this->MultiCell(0,5,$this->societe_client['adresse_3']);
		}
		$this->MultiCell(0,5,$this->societe_client['cp']." ".$this->societe_client['ville']);
		$this->Ln(10);
		$this->SetLeftMargin(15);
		
		$this->SetFont('Arial','U',10);
		$this->Cell($this->getStringWidth("Objet : "),5,"Objet : ",0,0);
		$this->SetFont('Arial','',10);
		if (!$this->relance['date_1']) {
			$this->premiereRelance();
		} elseif (!$this->relance['date_2']) {
			$this->deuxiemeRelance();
		} elseif (!$this->relance['date_demeurre']) {
			$this->troisiemeRelance(); 
		}
		
		$this->Ln(5);
		$this->MultiCell(0,5,"Nous vous prions d'agréer, Monsieur, l'expression de nos sentiments distingués.",0,"J",0,10);
		$this->Ln(15);
		$this->MultiCell(0,5,"Service Comptabilité ".$this->societe['societe'],0,'R');
		$this->MultiCell(0,5,ATF::contact()->nom($this->societe['id_contact_facturation']),0,'R');
		$this->MultiCell(0,5,"En cas de règlement par chèque, merci de l'adresser à :",0,'L');
		$this->MultiCell(0,5,"",0,'L');
		$this->SetFont('Arial','B',10);
		$this->MultiCell(0,5,$this->societe['societe'],0,'L');
		$this->SetFont('Arial','',10);
		$this->MultiCell(0,5,$this->societe['adresse'],0,'L');
		if ($this->societe['adresse_2']) $this->MultiCell(0,5,$this->societe['adresse_2']);
		if ($this->societe['adresse_3']) $this->MultiCell(0,5,$this->societe['adresse_3']);
		$this->MultiCell(0,5,$this->societe['cp']." ".$this->societe['ville'],0,'L');
		
	}
	
	private function premiereRelance() {
		$this->Cell(45,5,"1ère relance de facture", 0,0);
		$this->Ln(15);
		$this->Cell(45,5,"Madame, Monsieur,",0,0);
		$this->Ln(10);
		$this->MultiCell(0,5,"Sauf erreur ou omission de notre part, il apparaît que le règlement de notre facture dont la référence est ".$this->facture['ref']." du	".ATF::$usr->date_trans($this->facture['date'],true,true)." d'un montant de ".number_format($this->facture['prix'],2, '.', '')." EUR HT soit ".number_format($this->facture['prix'] * $this->facture['tva'],2, '.', '')." euros TTC n'a toujours pas été effectué par vos soins.",0,'J',0,10);
		$this->Ln(5);
		$this->MultiCell(0,5,"Nous sommes persuadés qu'il s'agit d'un simple oubli de votre part et vous invitons à nous faire parvenir votre règlement dans les meilleurs délais.",0,'J',0,10);
		$this->Ln(5);
		$this->MultiCell(0,5,"Si votre règlement nous est déjà parvenu, veuillez considérer ce courrier comme sans objet.",0,'J',0,10);
		$this->Ln(5);
		if ($this->interet) {
			$this->MultiCell(0,5,"Veuillez prendre en note que les intérêts exigibles par rapport à cette facture s'élèvent à ce jour à ".$this->interet."€.",0,'J',0,10);
			$this->Ln(5);
		}
		$this->MultiCell(0,5,"Dans le cas où vous connaîtriez des difficultés de trésorerie passagères, nous vous remercions de bien vouloir nous en informer.",0,'J',0,10);
	}
	
	private function deuxiemeRelance() {
		$this->Cell(45,5,"2ème relance de facture", 0,0);
		$this->Ln(10);
		$this->Cell(45,5,"Monsieur,",0,0);
		$this->Ln(10);
		$this->MultiCell(0,5,"Nous constatons avec regret que notre relance du ".ATF::$usr->date_trans($this->relance['date_1'],true,true).", relative au non-paiement de notre facture dont la référence est ".$this->facture['ref']." du ".ATF::$usr->date_trans($this->facture['date'],true,true)." d'un montant de ".number_format($this->facture['prix'],2, '.', '')." EUR HT soit ".number_format($this->facture['prix'] * $this->facture['tva'],2, '.', '')." euros TTC, est restée sans effet.",0,'J',0,10);
		$this->Ln(5);
		$this->MultiCell(0,5,"Nous sommes surpris de n'avoir toujours pas enregistré votre règlement et vous invitons à régulariser votre situation dans les plus brefs délais, par tout moyen à votre convenance.",0,'J',0,10);
		$this->Ln(5);
		if ($this->interet) {
			$this->MultiCell(0,5,"Veuillez prendre en note que les intérêts exigibles par rapport à cette facture s'élèvent à ce jour à ".$this->interet."€.",0,'J',0,10);
			$this->Ln(5);
		}
		$this->MultiCell(0,5,"Si votre situation financière actuelle ne vous permet pas de faire face à la totalité de votre créance, nous nous tenons à votre disposition pour étudier une solution de paiement convenable pour les parties.",0,'J',0,10);
		$this->Ln(5);
		$this->MultiCell(0,5,"En l'absence de réponse de votre part, nous vous informons que nous serons contraints de recouvrer cette créance par voie judiciaire.",0,'J',0,10);
	}
	
	private function troisiemeRelance() {
		$this->Cell(45,5,"MISE EN DEMEURE",0,0);
		$this->Ln(10);
		$this->SetFont('Arial','UB',10);
		$this->Cell(45,5,"LETTRE RECOMMANDEE AVEC A. R.",0,0);
		$this->SetFont('Arial','',10);
		$this->Ln(10);
		$this->Cell(45,5,"Monsieur,",0,0);
		$this->Ln(10);
		$this->MultiCell(0,5,"Malgré nos relances, nous constatons que vous nous devez la somme de ".number_format($this->facture['prix'],2, '.', '')." EUR HT soit ".number_format($this->facture['prix'] * $this->facture['tva'],2, '.', '')." euros TTC, correspondant à notre facture dont la référence est ".$this->facture['ref']." du ".ATF::$usr->date_trans($this->facture['date'],true,true).".",0,'J',0,10);
		$this->Ln(5);
		if ($this->interet) {
			$this->MultiCell(0,5,"En outre, les intérêts éxigibles suite à cet impayé s'élèvent à ".$this->interet."€.",0,'J',0,10);
			$this->Ln(5);
		}
		$this->MultiCell(0,5,"En conséquence, nous nous voyons dans l'obligation de vous METTRE EN DEMEURE de nous régler la somme susvisée dans un délai de 8 jours à compter de la réception des présentes.",0,'J',0,10);
		$this->Ln(5);
		$this->MultiCell(0,5,"Nous vous informons que, à defaut de régularisation de votre situation dans ce delai, nous serons contraints de recouvrer notre créance par voie judiciaire, en portant l'affaire devant le Tribunal de Commerce de Dunkerque. Conformément aux règles en vigueur, les frais de procédure et de recouvrement seront entièrement à votre charge.",0,'J',0,10);
	}
	
	
	
	public function ordre_de_mission($id,&$s){
		$this->Addpage();
		$id = ATF::ordre_de_mission()->decryptID($id);
		$odm = ATF::ordre_de_mission()->select($id);
		$user = ATF::user()->select($odm["id_user"]);
		$societe = ATF::societe()->select($odm['id_societe']);
		$agence = ATF::agence()->select($user['id_agence']);
		$contact= ATF::contact()->select($odm['id_contact']);
		$this->image(__PDF_PATH__.ATF::$codename."/entete.jpg",5,3,200);
		
		$this->setLeftMargin(15);

		// Société en entete : Absystech
		$this->setxy(60,3);
		$this->setfont('arial','B',10);
		$this->Cell(135,4,ATF::societe()->maSociete['societe'],0,1,'R');
		$this->setfont('arial','',9);
		$this->setx(60);
		$this->Cell(135,4,ATF::societe()->maSociete['adresse'],0,1,'R');
		$this->setx(60);
		$this->Cell(135,4,ATF::societe()->maSociete['cp']." ".ATF::societe()->maSociete['ville'],0,1,'R');
		$this->setx(60);
		$this->Cell(135,4,ATF::societe()->maSociete['email'],0,1,'R');
		$this->setx(60);
		$this->Cell(135,4,"Tel : ".ATF::societe()->maSociete['tel'],0,1,'R');
		$this->setx(60);
		$this->Cell(135,4,"Fax : ".ATF::societe()->maSociete['fax'],0,1,'R');
		
		$this->setfont('arial','B',11);
		$this->cadre(75,30,60,5,NULL,"ORDRE DE MISSION",2,true);
		
		$this->ln(5);
		$this->setfont('arial','B',9);
		$this->Cell(33,5,"Objet de la mission : ");
		$this->setfont('arial','',9);
		$lib = $odm['ordre_de_mission'];
		if ($odm['id_hotline']) {
			$lib .= " ( Ticket Hotline n°".$odm['id_hotline'].")";
		}
		$this->Cell(147,5,$lib);
		
		$y = $this->gety()+10;
		//Cadre intervenant
		$cadreIntervenant = array(
			"Nom : ".$user['nom'],
			"Prénom : ".$user['prenom'],
			"Agence : ".$agence['agence']
		);
		if ($odm['moyen_transport']) {
			$cadreIntervenant[] = "Moyen de transport : ".$odm['moyen_transport'];
		}
		$this->cadre(20,$y,80,25,$cadreIntervenant,"Intervenant");
		
		//Cadre client
		$cadreClient = array(
			"Société : ".$societe['societe'],
			"Nom : ".$contact['prenom']." ".$contact['nom'].($contact['tel']?" (Tel. ".$contact['tel'].")":""),
			"Adresse : ".$odm['adresse'],
		);
		if ($odm['adresse_2'])	$cadreClient[] = "                ".$odm['adresse_2'];
		if ($odm['adresse_3'])	$cadreClient[] = "                ".$odm['adresse_3'];
		$cadreClient[] = "                ".$odm['cp']." ".$odm['ville'];
		
		$this->cadre(110,$y,80,5+count($cadreClient)*5,$cadreClient,"Client");
		if (!ATF::isTestUnitaire() && $contact['id_contact']) {
			$fpQRCode = ATF::contact()->filepath($contact['id_contact'],"qrcode");
			if (!file_exists($fpQRCode)) ATF::contact()->vcardToQRcode($contact['id_contact'],false,false);
			copy($fpQRCode,str_replace("qrcode","png",$fpQRCode));
			$this->image(str_replace("qrcode","png",$fpQRCode),130,$this->gety(),35);
			util::rm(str_replace("qrcode","png",$fpQRCode));
		}
		
		$y = 80;
		$this->cadre(20,$y,80,20,false,"Signature ".ATF::societe()->maSociete['societe']);
		$this->cadre(20,$y+25,80,20,false,"Signature Intervenant");
		
		$this->setfont('arial','B',11);
		$this->cadre(75,$this->gety(),60,5,NULL,"Déroulement de la mission",2,true);
		
		$this->ln(5);
		$this->setfont('arial','B',9);
		$this->Cell(10,5,"Date : ");
		$this->setfont('arial','',9);
		$this->Cell(39,5,substr($odm['date'],8,10)."/".substr($odm['date'],5,2)."/".substr($odm['date'],0,4));
		$this->setfont('arial','B',9);
		$this->Cell(27,5,"Heure d'arrivée : ");
		$this->setfont('arial','',9);
		$this->Cell(38,5,"");
		$this->setfont('arial','B',9);
		$this->Cell(28,5,"Heure de départ : ",0,1);
		
		$this->ln(5);
		$this->setfont('arial','B',9);
		$this->Checkbox(false,4);
		$this->Cell(45,5,"A l'unité");
		$this->Checkbox(false,4);
		$this->Cell(45,5,"Bon de commande");
		$this->Checkbox(false,4);
		$this->Cell(45,5,"Contrat de maintenance",0,1);
		$this->multicell(0,5,"Nombre de tickets : ");
		
		$this->cadre(15,$this->gety()+5,180,50,NULL,"Description");

		$this->cadre(15,$this->gety(),180,25,NULL,"Commentaire Client");

		$y = $this->gety();
		$this->cadre(15,$y,87,25,NULL,"Signature Intervenant");

		$this->cadre(107,$y,88,25,NULL,"Signature Client");

		$this->pied(ATF::societe()->maSociete);
	}
	
	public function code_barreATT($date=false) {
		$this->code_barre($date,"ATT");
	}
	
	public function code_barre($date=false,$codename=false) {
	
		/* LIBS PR CODE A BARRE */
		require_once __ATF_PATH__.'libs/barcodegen.v2.0.0/class/BCGFont.php';
		require_once __ATF_PATH__.'libs/barcodegen.v2.0.0/class/BCGColor.php';
		require_once __ATF_PATH__.'libs/barcodegen.v2.0.0/class/BCGDrawing.php';
		require_once __ATF_PATH__.'libs/barcodegen.v2.0.0/class/BCGcode128.barcode.php';
		$font = new BCGFont(__ATF_PATH__.'libs/barcodegen.v2.0.0/class/font/Arial.ttf', 20);
		$color_black = new BCGColor(0, 0, 0);
		$color_white = new BCGColor(255, 255, 255);
		
		//CODE BAR : créer le code bar et le met dans images/bon_de_livraison/cb/$id.png
			/*Si le dossier n'existe pas on le crée*/
		$path = '/tmp/';
		
		parent::__construct('P','mm','A5');
		$this->Open();
		$this->Addpage();
		$this->setleftmargin(10);
		$this->setrightmargin(10);
		$this->SetAutoPageBreak(auto,1);
		$this->setfont('arial','B',10);
		
		if ($date==1) $date = date('ymd-His');
		//$this->image(__PDF_PATH__.ATF::$codename."/bgEtiquetteA5.jpg",0,0,149);
		for ($y=0;$y<2;$y++){
			for ($i=0;$i<12;$i++){
				
				// Forcer une ref
				$offset = $i+$y*12;
				if (is_array($date)) {
					if ($date["serial"][$offset]) {
						$ref = $date["serial"][$offset];
					} else {
						return;
					}
				} else {
					if ($offset<10) {
						$offset = "0".$offset;
					}
					$ref = (is_string($date)?$date:date('ymd-His')).'-'.$offset."-".($codename?$codename:"AT");
				}
			
				/*Si le png du code barre n'existe pas on le crée*/
				if (!file_exists($path.$ref.".png")) {
					/*Creation de l'objet code*/
					$code = new BCGcode128();
					$code->setScale(2);
					$code->setThickness(30);
					$code->setForegroundColor($color_black);
					$code->setBackgroundColor($color_white);
					$code->setStart('B');
					$code->setTilde(true);
					$code->parse($ref);
					
					/*Creation de l'image*/
					$drawing = new BCGDrawing($path.$ref.".png", $color_white);
					$drawing->setBarcode($code);
					$drawing->draw();
					$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
				}
				if($y == 0 && $i == 0){
					$this->image($path.$ref.".png",35,19,45,12);
				} else {
					//AVEC ESPACEMENT DE 12 ENTRE LES COLONNES ET 2 ENTRE LES LIGNES
					$this->image($path.$ref.".png",(35+57*$y),(19+15*$i),45,12);
				}
				unlink($path.$ref.".png");
			}
		}
	}
	
	public function etiquette_logo() {
				
		parent::__construct('P','mm','A5');
		$this->Open();
		$this->Addpage();
		$this->setleftmargin(10);
		$this->setrightmargin(10);
		$this->SetAutoPageBreak(auto,1);
		$this->setfont('arial','B',10);

		for ($i=0;$i<12;$i++){
			for ($y=0;$y<2;$y++){
				if($y == 0 && $i == 0){
					$this->image(__PDF_PATH__.ATF::$codename."/etiquette.jpg",35,19,45,12);
				} else {
					if ($y) {
						$img = __PDF_PATH__."/att/etiquette.jpg";
					} else {
						$img = __PDF_PATH__.ATF::$codename."/etiquette.jpg";
					}
					//AVEC ESPACEMENT DE 12 ENTRE LES COLONNES ET 2 ENTRE LES LIGNES
					$this->image($img,(35+57*$y),(19+15*$i),45,12);
				}
			}
		}
	}	
	
	public function factureInteret($id) {
		// Couleurs entêtes
		$this->Rentete = 238;
		$this->Gentete = 241;
		$this->Bentete = 255;
	
		$infos_facture_paiement = ATF::facture_paiement()->select($id);
		$infos_facture = ATF::facture()->select($infos_facture_paiement["id_facture"]);
		$type_facture=$infos_facture["type_facture"];	
		
		ATF::facture_ligne()->q->reset()->addCondition('id_facture',ATF::facture()->decryptId($id))->end();
		$infos_facture_produit = ATF::facture_ligne()->select_all();
				
		$infos_client = ATF::societe()->select($infos_facture['id_societe']);
		$infos_affaire = ATF::affaire()->select($infos_facture['id_affaire']);
		$infos_user = ATF::user()->select($infos_facture["id_user"]);
		$infos_societe = ATF::societe()->select($infos_user['id_societe']);
		$infos_absystech = ATF::societe()->select(1);
		if ($infos_client['id_contact_facturation']) {
			$infos_contact = ATF::contact()->select($infos_client['id_contact_facturation']);
		}
		
		$this->Open();
		$this->Addpage();
		$this->setleftmargin(15);
		$this->setrightmargin(15);
		$this->setdrawcolor(0,184,255);
		
		$this->image(__PDF_PATH__.ATF::$codename."/facture.png",15,5,180);
		
		$this->setfont('arial','B',16);
		
		$this->setxy(85,10);
		$this->multicell(110,5,$infos_client['societe'],0,'R');
		$this->setfont('arial','',8);
		if ($infos_client['facturation_adresse']) {
			$this->multicell(0,5,$infos_client['facturation_adresse'],0,'R');
			if ($infos_client['facturation_adresse_2']) {
				$this->multicell(0,5,$infos_client['facturation_adresse_2'],0,'R');
			}
			if ($infos_client['facturation_adresse_3']) {
				$this->multicell(0,5,$infos_client['facturation_adresse_3'],0,'R');
			}
			$this->multicell(0,5,$infos_client['facturation_cp']." ".$infos_client['facturation_ville'],0,'R');
			$this->multicell(0,5,ATF::pays()->nom($infos_client['facturation_id_pays']),0,'R');
		} else {
			$this->multicell(0,5,$infos_client['adresse'],0,'R');
			if ($infos_client['adresse_2']) {
				$this->multicell(0,5,$infos_client['adresse_2'],0,'R');
			}
			if ($infos_client['adresse_3']) {
				$this->multicell(0,5,$infos_client['adresse_3'],0,'R');
			}
			$this->multicell(0,5,$infos_client['cp']." ".$infos_client['ville'],0,'R');
			$this->multicell(0,5,ATF::pays()->nom($infos_client['id_pays']),0,'R');
		}
		
		$this->sety(25);
		$this->setleftmargin(85);
		$this->setfont('arial','B',8);	
		$this->multicell(0,5,ATF::$usr->trans($infos_contact['civilite']).". ".$infos_contact['nom']." ".$infos_contact['prenom']);
		$this->setfont('arial','',8);
		$this->multicell(0,5,$infos_client['tel'] ? "Tel : ".$infos_client['tel'] : "Tel : ".$infos_contact['tel']);
		$this->multicell(0,5,$infos_client['fax'] ? "Fax : ".$infos_client['fax'] : "Fax : ".$infos_contact['tel']);
		
		$this->setleftmargin(15);
		
		$jour = getdate($infos_facture['date']);
		$this->ln(-1);
		$this->setx(20);
		$this->cell(0,5,"Le ".date("j",mktime(0,0,0,0,substr($infos_facture_paiement['date'],8,9),0))." ".ATF::$usr->trans(date("F",mktime(0,0,0,substr($infos_facture_paiement['date'],5,2)+1,0,0)))." ".date("Y",mktime(0,0,0,1,1,substr($infos_facture_paiement['date'],0,4)))." - Rf. ".$infos_facture['ref']."-I",0,0);
		$this->ln(-5);
		$this->setx(25);
		$this->setfont('arial','B',10);	
	
		if($infos_affaire["code_commande_client"]){
			$this->setfont('arial','',8);	
			$this->ln(15);
			$this->cell(0,5,"Rf. Commande ".$infos_affaire["code_commande_client"],0,0);
			$this->ln(5);
		}else{
			$this->ln(20);
		}
		
		$head = array("Référence","Désignation","Qté","Prix unitaire","Montant");
		$width = array(25,95,11,25,25);
		
		$indemnite = "40.00";
		$txt = "Intérêts relatifs au retard de paiement de la facture ".$infos_facture['ref']." du ".ATF::$usr->date_trans($infos_facture['date'],true,true)." pour un montant de ".$infos_facture['prix']." € HT.";
		$txt .= "\nPaiement de ".$infos_facture_paiement['montant']." € TTC effectué le ".ATF::$usr->date_trans($infos_facture_paiement['date'],true,true);
		$data = array(
			array("INTERETS",$txt,"1",($infos_facture_paiement['montant_interet']-$indemnite)." €",($infos_facture_paiement['montant_interet']-$indemnite)." €"),
			array("INDEMNITE","Indemnité de recouvrement (articles L441-3 et L441-6 du code de commerce)","1",$indemnite." €",$indemnite." €")
		);
		$style = array(
			array("",$this->leftStyle,"",$this->rightStyle,$this->rightStyle),
			array("",$this->leftStyle,"",$this->rightStyle,$this->rightStyle)
		);

		$this->tableau($head,$data,$width,5,$style);
				
		$this->setx(15);
		$this->cell(131,4,"",0,0,'C');
		$this->cell(25,4,"Total",1,0,'R');
		$this->cell(25,4,($infos_facture_paiement['montant_interet'])." €",1,1,'R');
			
		$this->setleftmargin(15);
		$this->setrightmargin(15);
		//$this->sety(170);
		$this->setfont('arial','',8);
		$this->ln(20);
		$y = $this->gety();
		$cadre = array(
			array("txt"=>"\nA réception de facture","align"=>"C","h"=>5)
		);
		
		$this->cadre(15,$y,50,15,$cadre,"Termes de paiement :");
		
		$cadre = array(
			array("txt"=>$infos_client['ref'],"align"=>"C")
		);
		
		$this->cadre(15,$y+20,50,15,$cadre,"Votre numéro client :");
		
		$cadre = array();
		if ($infos_absystech["banque"]) {
			$cadre[] = $infos_absystech["banque"];
		}
		if ($infos_absystech['rib']) {
			$cadre[] = "RIB : ".util::formatRIB($infos_absystech["rib"]);
		}
		if ($infos_absystech['iban']) {
			$cadre[] = "IBAN : ".$infos_absystech["iban"];
		}
		if ($infos_absystech['bic']) {
			$cadre[] = "BIC : ".$infos_absystech["bic"];
		}
		if ($infos_absystech['swift']) {
			$cadre[] = "SWIFT : ".$infos_absystech["swift"];
		}
		if ($infos_absystech['reference_tva']) {
			$cadre[] = "TVA : ".$infos_absystech['reference_tva'];
		}
		
		$this->cadre(75,$y,60,35,$cadre,"Coordonnées bancaires");
		
		$cadre = array();
		$cadre[] = "En cas de règlement par chèque, merci de l'adresser à :";
		$cadre[] = $infos_absystech['societe'];
		if ($infos_absystech["adresse_2"]) $cadre[] = $infos_absystech["adresse_2"];
		$cadre[] = $infos_absystech["adresse"];
		$cadre[] = $infos_absystech["cp"]." ".$infos_absystech["ville"];
		
		$this->cadre(145,$y,50,35,$cadre,"Règlement");
		
		$this->pied($infos_absystech);	
	}

	/**
	* Portail des PDF livraison
	* @author Quentin JANON
	* @param string $id identifiant de livraison
	*/
	public function livraison($id) {
		$this->livraison = ATF::livraison()->select($id);
		$this->client = ATF::societe()->select($this->livraison["id_societe"]);
		$this->devis = ATF::devis()->select($this->livraison['id_devis']);
		$this->commande = ATF::commande()->select($this->livraison['id_commande']);
		$this->user = ATF::user()->select($this->livraison['id_expediteur']);
		
		$this->agence = ATF::agence()->select($this->user ['id_agence']);
		
		ATF::livraison_ligne()->q->reset()
			->addCondition("livraison_ligne.id_livraison",$this->livraison["id_livraison"])
			->from("livraison_ligne","id_stock","stock","id_stock")
			->addOrder('stock.ref');
		$this->lignes = ATF::livraison_ligne()->sa();
		
		$this->isLivraison = true;

		// Mode paysage
		$this->DefOrientation='L';
		$this->wPt=$this->fhPt;
		$this->hPt=$this->fwPt;

		$this->Open();
		$this->setleftmargin(10);
		$this->setrightmargin(10);
		$this->settopmargin(35);
		$this->AddPage();
		$this->setfont("arial",'',9);
		
		$y = 5;
		$cadre1 = array(
			"Expéditeur : ".ATF::user()->nom($this->livraison['id_expediteur'])
			,"Agence : ".$this->agence['ville']
			,"Date de livraison : "//.ATF::$usr->date_trans($this->livraison['date'],"force")
			,"Ref. Livraison : ".$this->livraison["ref"]
		);
		$this->cadre(90,$y,60,25,$cadre1,"Infos Livraison");
		
		$cadre2 = array(
			"Société : ".$this->client["societe"]
			,"Réf. client : ".$this->client["ref"]
		);
		if ($this->devis['ref']) $cadre2[] = "Réf. Devis : ".$this->devis['ref'];
		if ($this->commande['ref']) $cadre2[] = "Réf. Commande : ".$this->commande['ref'];
		$this->cadre(155,$y,60,25,$cadre2,"Vos Références");
		
		$cadre3 = array(
			$this->client["adresse"]
		);
		if ($this->client["adresse_2"]) $cadre3[] = $this->client["adresse_2"];
		if ($this->client["adresse_3"]) $cadre3[] = $this->client["adresse_3"];
		$cadre3[] = $this->client["cp"]." ".$this->client["ville"];
		$this->cadre(220,$y,60,25,$cadre3,"Adresse de livraison");
		
		$this->ln(5);

		$head = array("Ref constructeur","Désignation","N° de Serie","Qté");
		$width = array(25,105,130,10);
		
		//lignes de tableau
		if ($this->lignes){
			foreach ($this->lignes as $k => $i){
				if (!$data[$i['ref']]) {
					$data[$i['ref']][0] = $i['ref'];
					$styl[$i['ref']][0] = $this->leftStyle;
					$data[$i['ref']][1] = $i['libelle'];
					$styl[$i['ref']][2] = $this->leftStyle;
				}
				if ($data[$i['ref']][2]) $data[$i['ref']][2] .= " - ";
				$data[$i['ref']][2] .= $i['serial'];
				$data[$i['ref']][3]++;
			}
			foreach ($data as $k=>$i) {
				$data[$k][3] = number_format($data[$k][3],1,',',' ');
			}
			
		}
		
		$this->tableau($head,$data,$width,5,$styl,170);
		
		$cadre_1 = array("");
		$cadre_2 = array("");
		$cadre_3 = array("");
		$cadre = array($cadre_1,$cadre_1,$cadre_3);
		
		$cadre_y = 170;
		
		$this->cadre(10,$cadre_y,85,25,$cadre[0],'Nom/visa/cachet du client :');
		$this->cadre(100,$cadre_y,85,25,$cadre[1],'Visa Absystech :');		
		$this->cadre(190,$cadre_y,92,25,$cadre[2],'COMMENTAIRE :');
		$this->isLivraison = false;
	}
	
	
	/**
	* Génération du fichier PDF de bon de prêt
	* @author Quentin JANON
	*/
	public function bon_de_pret($id) {
		$this->bon_de_pret = ATF::bon_de_pret()->select($id);
		$this->client = ATF::societe()->select($this->bon_de_pret["id_societe"]);
		$this->user = ATF::user()->select($this->bon_de_pret['id_user']);
		
		$this->agence = ATF::agence()->select($this->user ['id_agence']);
		
		ATF::bon_de_pret_ligne()->q->reset()->addCondition("bon_de_pret_ligne.id_bon_de_pret",$this->bon_de_pret["id_bon_de_pret"]);
		$this->lignes = ATF::bon_de_pret_ligne()->sa();
		
		$this->isPret = true;

		// Mode paysage
		$this->DefOrientation='L';
		$this->wPt=$this->fhPt;
		$this->hPt=$this->fwPt;

		$this->Open();
		$this->setleftmargin(10);
		$this->setrightmargin(10);
		$this->settopmargin(35);
		$this->AddPage();
		$this->setFont('arial','I',9);
		$this->multicell(70,10,"(Pour une durée de ".ATF::bon_de_pret()->duree($this->bon_de_pret['id_bon_de_pret']).")",0,"C");

		$this->setfont("arial",'',9);
		
		$y = 5;
		$cadre1 = array(
			"Prêter par : AbsysTech"
			,"Agence : ".$this->agence['ville']
			,"Date de début : ".date("d-m-Y",strtotime($this->bon_de_pret['date_debut']))
			,"Date de Restitution : ".date("d-m-Y",strtotime($this->bon_de_pret['date_fin']))
		);
		$this->cadre(90,$y,60,25,$cadre1,"Infos Prêt");
		
		$cadre2 = array(
			"Société : ".$this->client["societe"]
			,"Réf. client : ".$this->client["ref"]
		);
		if ($this->devis['ref']) $cadre2[] = "Réf. Devis : ".$this->devis['ref'];
		if ($this->commande['ref']) $cadre2[] = "Réf. Commande : ".$this->commande['ref'];
		$this->cadre(155,$y,60,25,$cadre2,"Vos Références");
		
		$cadre3 = array(
			$this->client["adresse"]
		);
		if ($this->client["adresse_2"]) $cadre3[] = $this->client["adresse_2"];
		if ($this->client["adresse_3"]) $cadre3[] = $this->client["adresse_3"];
		$cadre3[] = $this->client["cp"]." ".$this->client["ville"];
		$this->cadre(220,$y,60,25,$cadre3,"Adresse du prêt");
		
		$this->ln(10);

		$head = array("Ref constructeur","Désignation","N° de Serie","N° de Serie AT","Qté");
		$width = array(25,105,65,65,10);
		
		//lignes de tableau
		if ($this->lignes){
			foreach ($this->lignes as $k => $i){
				if (!$data[$i['ref']]) {
					$data[$i['ref']][0] = $i['ref'];
					$styl[$i['ref']][0] = $this->leftStyle;
					$data[$i['ref']][1] = $i['stock'];
					$styl[$i['ref']][2] = $this->leftStyle;
				}
				if ($data[$i['ref']][2]) $data[$i['ref']][2] .= " - ";
				$data[$i['ref']][2] .= $i['serial'];
				if ($data[$i['ref']][3]) $data[$i['ref']][3] .= " - ";
				$data[$i['ref']][3] .= $i['serialAT'];
				$data[$i['ref']][4]++;
			}
			foreach ($data as $k=>$i) {
				$data[$k][4] = number_format($data[$k][4],1,',',' ');
			}
			
		}
		
		$this->tableau($head,$data,$width,5,$styl,170);
		
		$cadre_1 = array("");
		$cadre_2 = array("");
		$cadre_3 = array("");
		$cadre = array($cadre_1,$cadre_1,$cadre_3);
		
		$cadre_y = 170;
		
		$this->cadre(10,$cadre_y,85,25,$cadre[0],'Nom/visa/cachet du client :');
		$this->cadre(100,$cadre_y,85,25,$cadre[1],'Visa Absystech :');		
		$this->cadre(190,$cadre_y,92,25,$cadre[2],'COMMENTAIRE :');
		
		$this->isPret = false;
	}
	
	public function facture_exoneration_tva($infos_facture) {
		$this->setfont('arial','B',8);
		if($infos_facture['type_facture']=="avoir")$this->cell(25,4,"Avoir",1,0,'R');
		else $this->cell(25,4,"Total exonéré",1,0,'C');
		$this->cell(25,4,number_format(abs($infos_facture["prix"]),2,',','.')." €",1,1,'R');
	}
    
	
	public function lettre_de_change($infos /*$id_societe*/){
		
		$echeance = $infos["echeance"];
		$id_societe = $infos["id_societe"];
		$factures= $infos["factures"];
		if($infos["tu"]){
			$tu = true;
		}		
		$this->Open();
		$this->Addpage();
		$this->setleftmargin(15);
		$this->setrightmargin(15);
		
		$this->lettre_de_change_entete($id_societe);
		$this->cell(110,20,"",0,5);
		$this->setX(35);
		
		$this->multicell(125,5,"Veuillez trouver ci-dessous une traite à découper correspondant aux factures référencées ci-dessous. Nous vous remercions de bien vouloir nous retourner cet effet par retour de courrier.",0);
		
		/*
		 * TABLEAU
		*/
		
		$head = array("N° Facture","Date Facture","Montant");
		$width = array(50,50,25);
		
		$total = 0;
		$deja_paye = 0;
		$l=0;
		foreach ($factures as $k => $i) {
			$infos_facture = ATF::facture()->select($i);
			$data[$l][0] = $infos_facture["ref"];
			$data[$l][1] = date("d/m/Y", strtotime($infos_facture['date']));			
			$data[$l][2] = number_format($infos_facture["prix"]*$infos_facture["tva"],2,',',' ')." €";
			$style[$l][2] = $this->rightStyle;
			ATF::facture_paiement()->q->reset()->where("facture_paiement.id_facture", ATF::facture()->decryptId($i));
			$l++;
			$paiement = ATF::facture_paiement()->select_all();			
			if($paiement){
				foreach ($paiement as $key => $value) {
				$data[$l][0] = "Payé par ".ATF::$usr->trans($value['facture_paiement.mode_paiement']);
					$data[$l][1] = date("d/m/Y", strtotime($value['facture_paiement.date']));
					$data[$l][2] = "- ".number_format($value["facture_paiement.montant"],2,',',' ')." €";
					$style[$l][2] = $this->rightStyle;
					$deja_paye += $value["facture_paiement.montant"]*$value["facture_paiement.tva"];
					$l++;
				}
			}			
			$total += $infos_facture["prix"]*$infos_facture["tva"];
		}	
		$a_payer = $total - $deja_paye;
		
		$this->ln(5);
		$this->setleftmargin(35);		
		$this->tableau($head,$data,$width,5,$style,175);
		$this->cell(50,5,"",0);
		$this->cell(50,5,"Total du en (€)...","LB");
		$this->cell(25,5,number_format($a_payer,2,',',' ')." €","RB",false,"R");
		$this->setleftmargin(15);
			
		$this->setY(175);
		/*
		 * POINTILLE
		*/
		$this->setX(2);
		$this->setfont('arial','',6);
		$this->cell(20,5,"Découper selon le pointillé.",0,5);
		$this->setX(0);
		for($i=0;$i<30;$i++){
			$this->cell(5,2,"","B");
			$this->cell(2,2,"",0);
		}
		$this->ln(10);			
		
		/*
		 * ZONE DU BAS
		*/	
		$this->lettre_de_change_bas($id_societe,$a_payer,$echeance,$tu);	 		 
	}
	
   function lettre_de_change_entete($id){
   		$this->setfont('arial','B',24);
		$this->setxy(10,5);
		$this->multicell(110,10,"AbsysTech",0,'L');
		$this->setfont('arial','',12);
		$this->setX(10);
		$this->multicell(110,5,"139 rue des arts",0,'L');
		$this->setX(10);
		$this->multicell(110,5,"59100 ROUBAIX",0,'L');				
		$this->image(__PDF_PATH__.ATF::$codename."/AT.jpg",155,5,50);		
				
		$this->setY(25);
		$this->setX(110);
		$id = ATF::societe()->decryptId($id);
		$this->multicell(60,5,ATF::societe()->select($id , "societe"),0,'L');
		$this->setX(110);
		$this->multicell(80,5,ATF::societe()->select($id , "adresse"),0,'L');
		if(ATF::societe()->select($id , "adresse_2")){
			$this->setX(110);
			$this->multicell(80,5,ATF::societe()->select($id , "adresse_2"),0,'L');
			if(ATF::societe()->select($id , "adresse_3")){
				$this->setX(110);
				$this->multicell(80,5,ATF::societe()->select($id , "adresse_3"),0,'L');
			}
		}
		$this->setX(110);
		$this->multicell(80,5,ATF::societe()->select($id , "cp")." ".ATF::societe()->select($id , "ville"),0,'L');
   }

   function lettre_de_change_bas($id,$total,$echeance,$tu=false){
   		$total = number_format($total,2,',','');
		if(strlen($total) < 11){
			$etoile = "";
			for($i=strlen($total); $i<11;$i++){
				$etoile .="*";
			}
		}
		$total = $etoile.$total;
	
	
   		$y = $this->getY();
		$this->setX(0);		
		$this->setfont('Courier','B',10);
		
		$this->cell(120,4,"",0);
		$this->multicell(53,4,"AbsysTech SARL \n139, rue des Arts \n59100 ROUBAIX",0,"L");
		$this->setY($y);
		$this->cell(170,4,"",0);
		$this->setfont('arial','',6);
		$this->multicell(12,2,"mention L.C.R.\ns'il y a lieu","L","C");
		$this->setY($y);
		$this->setfont('arial','',9);
		$this->setX(60);
		$this->multicell(53,4,"Contre cette LETTRE DE CHANGE stipulée SANS FRAIS veuillez payer la somme indiquée ci-dessous à l'ordre de",0,"L");
		
		/*
		 * Ligne 2
		 */
		$this->ln(4);
		$y = $this->getY();
		$this->setfont('arial','',6);
		$this->SetLineWidth(0.1); 
		$this->setX(10); 
		$this->cell(4,2,"A",0);	
		$this->cell(32,1,"",0);	
		$this->cell(4,2,"LE",0);
		$this->setY($y-2);
		$this->setfont('Courier','B',10);
		$this->cell(30,4,"Roubaix","B");
		$this->cell(6,4,"",0);
		$this->cell(5,4,"","B");
		
		/*
		 * Ligne 3
		 */	
		 $this->setfont('arial','',6);	 
		 $this->ln(8);
		 $y = $this->getY();
		 $this->setX(10);
		 $this->cell(34,2,"MONTANT POUR CONTRÔLE",0,0,"C");
		 $this->cell(28,2,"DATE DE CRÉATION",0,0,"C");
		 $this->cell(30,1,"ÉCHÉANCE",0,0,"C");
		 $this->cell(64,1,"L.C.R. seulement",0,0,"C");
		 $this->setfont('arial','B',6);	 
		 $this->cell(34,1,"F.MONTANT",0,0,"C");
		 
		 $date = date("d/m/Y");		
		 $echeance = $echeance;
		 if($tu){
		 	$date = "19/08/2013";
		 }
		
		
		 $this->setX(10);
		 $this->setfont('Courier','B',10);
		 $this->cell(33,10,$total." €","LBR",0,"C");
		 $this->setX(45);
		 $this->cell(26,10,$date,"LBR",0,"C");
		 $this->setX(74);
		 $this->SetLineWidth(0.4);
		 $this->cell(26,10,$echeance ,"LBR",0,"C");
		 $this->SetLineWidth(0.1);
		 
		 $this->setY($y+2);
		 $this->setX(102);		 
		 $this->cell(25,9,"" ,1,0,"C");
		 $this->cell(2,9,"" ,0,0,"C");
		 $this->cell(5,9,"" ,1,0,"C");
		 $this->cell(2,9,"" ,0,0,"C");
		 $this->cell(5,9,"" ,1,0,"C");
		 $this->cell(2,9,"" ,0,0,"C");
		 $this->cell(20,9,"" ,1,0,"C");
		 $this->setY($y+12);
		 $this->setX(102);
		 $this->setfont('arial','',4);
		 $this->cell(25,1,"REF. TIRE" ,0,0,"C");
		 $this->setfont('Courier','B',10);
				 
		 $this->setY($y+1);
		 $this->setX(166);
		 $this->SetLineWidth(0.4);
		 $this->cell(33,10,$total." €","LBR",0,"C");
		 $this->SetLineWidth(0.1);
		 
		 
		/*
		 * Ligne 4
		 */	 
		 $this->ln(13);
		 $this->setX(12);
		 $this->cell(2,6,"","LBT",0);
		 $this->cell(70,6,"",0,0);
		 $this->cell(2,6,"","RBT",0);
		 $this->cell(2,6,"",0,0);		 
		 $this->cell(2,6,"","LBT",0);
		 $this->cell(50,6,"",0,0);
		 $this->cell(2,6,"","RBT",0);
		 $this->cell(2,6,"",0,0);		 
		 $this->cell(2,6,"","LBT",0);
		 $this->cell(20,6,"",0,0);
		 $this->cell(2,6,"","RBT",10);
		 
		/*
		 * Ligne 5 GAUCHE
		 */		 
		 $y = $this->getY();
		 $this->setX(12);
		 $this->setfont('arial','',6);
		 $this->cell(70,2," R.I.B. du TIRÉ","B",1,"C");
		 $this->setfont('Courier','B',10);
		 $this->setX(12);
		 $this->cell(16,6,substr(ATF::societe()->select($id , "rib"), 0,5 ),"LR",0,"C");
		 $this->cell(16,6,substr(ATF::societe()->select($id , "rib"), 5,5 ),"R",0,"C");
		 $this->cell(33,6,substr(ATF::societe()->select($id , "rib"), 10,11),"R",0,"C");
		 $this->cell(5,6,substr(ATF::societe()->select($id , "rib"), -2),"R",10,"C");
		 
		 $this->setfont('arial','',6);
		 $this->setX(12);
		 $this->cell(16,6,"code établ.",0,0,"C");
		 $this->cell(16,6,"code guichet",0,0,"C");
		 $this->cell(30,6,"N° de compte",0,0,"C");
		 $this->cell(10,6,"Clé R.I.B.",0,10,"C");
		 
		 $this->setX(12);
		 $this->cell(10,6,"Valeur en",0,0,"L");
		 $this->setfont('Courier','B',10);
		 $this->cell(45,6," €",0,0,"L");
		 $this->setfont('arial','',6);
		 $this->multicell(18,3,"NOM \net ADRESSE \ndu TIRÉ",0,'R');
		 /*
		 * Ligne 5 CENTRE
		 */	
		 $this->setY($y+2);
		 $this->setX(86);
		
		 $this->SetLineWidth(0.4);
		 $this->cell(2,30,"","LBT",0,"C");
		 $this->SetLineWidth(0.1);		 
		 $this->setfont('Courier','B',10);
		 $this->ln(2);
		 $this->setX(88);
		 $id = ATF::societe()->decryptId($id);
		 $this->multicell(70,5,ATF::societe()->select($id , "societe"),0,'L');
		 $this->setX(88);
		 $this->multicell(70,5,ATF::societe()->select($id , "adresse"),0,'L');
		 if(ATF::societe()->select($id , "adresse_2")){
		 	 $this->setX(88);
			 $this->multicell(70,5,ATF::societe()->select($id , "adresse_2"),0,'L');
			 if(ATF::societe()->select($id , "adresse_3")){
			 	 $this->setX(88);
				 $this->multicell(70,5,ATF::societe()->select($id , "adresse_3"),0,'L');
			 }
		 }
		 $this->setX(88);
		 $this->multicell(80,5,ATF::societe()->select($id , "cp")." ".ATF::societe()->select($id , "ville"),0,'L');
		 
		 /*
		 * Ligne 5 DROITE
		 */	 
		 $this->setY($y+1);		 
		 $this->setX(150);
		 $this->setfont('arial','',6);	 
		 $this->cell(50,2,"DOMICILIATION",0,0,"C");		 
		 $this->setY($y+3);
		 $this->setX(150);
		 $this->setfont('Courier','B',10);
		 $this->multicell(50,5,strtoupper(ATF::societe()->select($id , "banque")),1,2,"L");
		 $this->setfont('arial','',6);
		 $this->setX(150);
		 $this->cell(50,5,"Droit de Timbre et Signature",0,2,"C");
		 
		 /*
		 * BAS DE PAGE
		 */	
		 $this->setfont('arial','',6);	 
		 $this->setY(271);		
		 $this->cell(190,4,"ACCEPTATION OU AVAL                                                                            ne rien inscrire au-dessous de cette ligne","B",0);
	
   } 


   

	private function cgvContratCopieur() {
		$this->addPage();
		
		$this->setFont('arial','B',7);
		$this->multicell(0,3,"CONDITIONS GENERALES RELATIVES AUX CONTRATS COUT COPIE",0,"C");
		$this->ln(3);

		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 1. OBJET DU CONTRAT");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,2,"Le présent Contrat a pour objet de déterminer les conditions dans lesquelles la société ABSYSTECH. ci après dénommée ( le Prestataire ) garantit au Client les prestations de services présentées ci-dessous, assurant le bon fonctionnement du matériel, désigné aux conditions particulières. Les prestations de services comprennent l'assistance technique téléphonique, l'entretien du matériel, le dépannage du matériel et la livraison des consommables. Les consommables désignent pour les imprimantes et les multifonctions, les toners, les cartouches et les encres. À l'exclusion de tout autre consommable désigné par le terme de « fourniture » (agrafes, papier, sans que cette liste ne soit exhaustive). Les prestations de service couvrent le matériel et ses accessoires. Par accessoires, il faut entendre ceux prévus et référencés par les constructeurs pour s'intégrer de façon indissociable à la machine de base. Le Contrat comprend optionnellement la maintenance des éléments matériels et logiciels liés à la connexion, et la mise à jour des drivers et des firmwares. Cette maintenance fera l'objet d'une facturation forfaitaire additionnelle. La fourniture et le remplacement des kits de maintenance et pièces d'usure, pièces dont le remplacement est normal après une période régulière, sont inclus dans le Contrat. L'obligation du Prestataire, s'étend au remplacement des pièces détachées hors d'usage sous réserve des exclusions énoncées dans l'article 5 du présent Contrat. Ces prestations seront exécutées moyennant une redevance selon les modalités définies dans l'article 7 du présent Contrat. En aucun cas d'autres prestations de services comme la formation ou l'installation du matériel ou d'un de ses éléments, ne sont comprises dans le présent Contrat. Les engagements pris par nos représentants, agents ou employés, ne sauraient nous lier qu'en cas d'acceptation par notre Direction.");
		$this->ln(1);
		
		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 2. DATE D'EFFET ET DUREE DU CONTRAT");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,3,"Le présent Contrat entre en vigueur, après signature par les deux parties, à la date de prise d'effet indiquée en annexe du présent contrat. La durée du présent Contrat figure en annexe.");
		$this->ln(1);
		
		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 3. OBLIGATIONS DU CLIENT");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,2,"Le Client s'engage à n'utiliser que des produits fournis par le Prestataire. Le Client s'engage à ne pas déplacer les matériels sans autorisation préalable du Prestataire. Le Client s'engage à informer le Prestataire de tous changements de propriété. Les commandes de consommables et leur mise en place sont à la charge du Client. Un utilisateur/contact par site ou par machine devra être désigné et qualifié par le Client pour effectuer ces mises en place. Le Prestataire pourra solliciter téléphoniquement cet utilisateur pour obtenir des informations sur les éventuels incidents et lui faire procéder ainsi à des manipulations pilotées à distance par le Prestataire. Le Prestataire est seul habilité à déterminer si le problème rencontré nécessite un déplacement d'un technicien ou si le problème peut être résolu à distance. Le local dans lequel le matériel est installé doit répondre aux exigences communiquées par le constructeur dans la brochure de présentation du matériel, et aux instructions données par le Prestataire. Le Client s'interdit de s'adresser à un tiers pour assurer l'entretien, les dépannages de la machine et la fourniture des consommables. Le cas échéant le Contrat pourra être résilié de plein droit par le Prestataire sans que le Client puisse se prévaloir d'un remboursement d'une quelconque indemnité. Le Client devra mettre à disposition du Prestataire le stock de consommables restant en fin de contrat. Le Client ne peut en aucun cas opposer à son encontre ses propres conditions générales sauf dérogation expresse acceptée par écrit.");
		$this->ln(1);
		
		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 4. CARACTERISTIQUES DES INTERVENTIONS");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,2,"Les interventions n'ont lieu que durant les heures normales de travail du Prestataire, soit du lundi au vendredi de 8H30 à 18h30 Elles ne peuvent avoir lieu les samedis et jours fériés ou chômés, sauf accord particulier. Au cas où les interventions seraient exceptionnellement effectuées en dehors des heures normales de travail du Prestataire, elles seraient facturées au tarif des heures supplémentaires du Prestataire. Préalablement à toute demande d'intervention, le Client devra s'assurer que le Prestataire puisse librement intervenir sur ses équipements informatiques sur lesquels est connecté ou administré le matériel. Le remplacement des pièces détachées couvertes par le type du Contrat, est à l'initiative du Prestataire et assuré par le technicien. Les pièces défectueuses récupérées deviennent la propriété du Prestataire.");
		$this->ln(1);
		
		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 5. EXCLUSIONS");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,3,"Sont exclus du champ d'application du présent Contrat et feront l'objet d'un devis spécifique : 
- les livraisons, installations, mises en service, démonstrations, programmations, conditionnements, déménagements ou déplacements de matériel et reconnexion de l'appareil ;
- la mise en place d'accessoires supplémentaires ;
- toutes extensions ou modifications de la configuration initiale après la signature du présent Contrat, feront l'objet d'une révision tarifaire de celui-ci.
Faute d'acceptation par le Client de ces nouvelles conditions, le Contrat pourra être résilié de plein droit par le Prestataire. Sont également exclus du champ d'application du présent Contrat et seront facturables séparément pour un forfait de 120 euros HT (plus pièces détachées détériorées) : 
- l'intervention rendue nécessaire par suite d'utilisation de produits non agréés par Le constructeur (papier ou supports spéciaux inclus) ;
- l'intervention rendue nécessaire par suite de défaillance due au logiciel et aux matériels connectés à l'équipement du Contrat ;
- l'intervention liée aux dommages causés par le feu, l'eau, la foudre, les chocs, une installation électrique insuffisante ou défectueuse, les accidents, et plus généralement les détériorations qui ne sont pas directementimputables au fonctionnement du matériel ;
- les réparations suite à une intervention effectuée par un tiers ;
- les réparations consécutives à la négligence, la malveillance, les fausses manipulations répétées, les corps étrangers introduits accidentellement ou non dans le matériel ;
- l'intervention liée à l'inobservation des conditions d'utilisation préconisées par le constructeur figurant dans le mode d'emploi joint à l'appareil, que le Client déclare bien connaître");
		$this->ln(1);
		
		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 6. RESPONSABILITES");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,2,"Le Prestataire est dégagé de ses obligations lors d'événements indépendants de sa volonté (grève, incendie, etc.) l'empêchant de s'exécuter. Le Prestataire n'est pas responsable des préjudices éventuels directs ou indirects, financiers ou commerciaux subis par le Client et résultant de retard dans les opérations de maintenance, d'immobilisation due à un incident technique, de rupture momentanée ou définitive de pièces détachées du fait du constructeur. ll ne pourra être exigé aucune indemnité, pénalité ou prêt de matériel de remplacement, sauf aux dispositions contraires stipulées dans le Contrat. Il appartient au Client, sous sa seule responsabilité, par tous les moyens dont il peut disposer de sauvegarder les fichiers, programmes et bases de données stockées sur son serveur ou autre matériel informatique. Le Prestataire ne sera en aucun cas responsable d'une perte de données. En cas de redémarrage complet du matériel, le re-paramétrage éventuel sera limité à la configuration effectuée par le Prestataire lors de l'installation. Les autres re-paramétrages étant à la charge du Client. En aucun cas le Prestataire ne pourra être tenu responsable des conséquences dues aux mauvaises transmissions imputables au réseau de télécommunication utilisé, ou au système de connexion informatique propre au Client.");
		$this->ln(1);

		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 7. SERVICE DE FACTURATION A LA PAGE");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,3,"7.1 Remontée des compteurs. Le relevé des compteurs se fait automatiquement, sans intervention humaine, à l'aide de l'outil de supervision. Pour les équipements ne remontant pas automatiquement les compteurs, ABSYSTECH fournira une procédure de relevé en envoi par le client.");
		$this->multicell(0,3,"7.2 Facturation des pages. La facturation porte sur un engagement de volume annuel. Sauf stipulation contraire, sa périodicité sera trimestrielle terme à échoir. Une régularisation entre le volume estimé annuel et le volume réalisé est effectuée par ABSYSTECH au minimum à l'issue de chaque période de 3 mois, et à la fin du Contrat, par relevé des compteurs de la machine qui seuls font foi. Les prix des pages sont définis sur la base de 5% de taux de couverture par couleur pour un format 44. La facturation des copies A3 sera égale à 2 (deux) A4. Le prix des pages est fixe sur la durée du contrat sur la base de 5% de taux de couverture par couleur pour un format A4. Le Prestataire mesurera l'utilisation des toners régulièrement. Les calculs seront basés sur le rapport entre le nombre total de cartouches d'encre livrées, divisé par le nombre de pages imprimées, basé sur une capacité de pages par cartouche elle-même communiquée par les constructeurs. Les surconsommations calculées sur la base du taux de 5 % donneront lieu à facturation complémentaire selon le tarif par page. Fourniture et facturation des toners supplémentaires : Si le taux moyen de remplissage de la page par encre est supérieur à 5 % par couleur, la fourniture des toners supplémentaires sera facturée en appliquant le prix public des toners supplémentaires livrés. Le Prestataire se réserve le droit à tout moment de modifier la périodicité de relevé compteurs en fonction du volume copies réalisé mensuellement. En cas de manque d'information sur les relevés de compteurs, il sera établit. une semaine après l'échéance, une facturation basée sur votre consommation réelle de cartouches d'encre et selon le taux standard de couverture de 5 % par couleur.");
		$this->ln(1);


		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 8. REVISION DES PRIX");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,3,"Aucune sur la durée du contrat, dans les conditions standard de l'offre (durée, encrage 5% par couleur et engagement de volumétrie)");
		$this->ln(1);


		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 9. DENONCIATION ET RESILIATION");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,3,"Le présent Contrat peut être résilié de plein droit par le Prestataire sans formalité préalable, dans chacun des cas ci-dessous, le Prestataire sera alors autorisé à recouvrer le montant total de ses créances majoré de tous frais (gestion, avocat ou officier de justice) et des pénalités éventuelles de retard. 
- non respect par le Client de l'une de ses obligations, telles que définies dans l'article 3 ;
- défaut ou de retard de paiement pour des raisons qui sont imputables au Client ;
- redressement ou de liquidation judiciaire des biens du Client ;
- déplacement géographique du lieu d'installation de la machine ;
- utilisation non conforme aux préconisations du constructeur ;
- arrêt d'approvisionnement ;
- déménagement de l'équipement en dehors du territoire métropolitain - non respect d'une des clauses particulières.
La dénonciation devra être notifiée par lettre recommandée avec accusé de réception. Elle prendra effet à 90 jours à compter de l'émission de la lettre. Toute résiliation intervenant avant la fin de la durée du Contrat ou des ses renouvellements entraînera le règlement de la totalité du montant du Contrat, basé sur la moyenne de la facturation précédente ou de l'engagement de volume et des coûts copie/impression tels que signés dans l'annexe du présent contrat.");
		$this->ln(1);


		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 10. CONDITIONS DE PAIEMENT");
		$this->ln(1);
		
		$this->setFont('arial','',5);
		$this->multicell(0,3,"Le paiement s'entend net et sans escompte. Il est réalisé à compter de l'encaissement de la somme à payer. Ne constitue donc pas un paiement la remise de traite, de chèque ou tout autre titre de crédit, ou une obligation de payer. Dans le cas où est prévu un règlement à terme, il s'effectuera par traite acceptée ou par LCR sans acceptation. Toute contestation relative à une facturation, devra être formulée par le Client dans les 8 jours de sa réception : au delà de ce délai, la facture sera considérée comme acceptée, et plus aucune réclamation ne sera recevable, ni aucune contestation ne pourra être formulée, et la facture devra être réglée au terme prévu.");
		$this->ln(1);


		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 11. ELIMINATION DES CARTOUCHES VIDES");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,3,"Le Client s'engage expressément à traiter ces consommables selon les instructions du constructeur pour leur élimination. ");
		$this->ln(1);


		$this->setFont('arial','B',6);
		$this->multicell(0,3,"ARTICLE 12. ATTRIBUTION DE JURIDICTION");
		$this->ln(1);

		$this->setFont('arial','',5);
		$this->multicell(0,3,"Tout litige qui pourrait s'élever directement ou indirectement au sujet de l'interprétation ou de l'exécution du présent Contrat relèvera de la compétence exclusive du Tribunal de Roubaix - Tourcoing. Seul le droit français sera applicable.");
		$this->ln(5);

		$this->multicell(0,5,"CGCC AbsysTech, Avril 2014",0,"R");

	}

   public function copieur_facture($id) {
		$this->Rentete = 238;
		$this->Gentete = 241;
		$this->Bentete = 255;

   		$this->setFooter();
   		$this->el = ATF::facture()->select($id);
   		$this->affaire = ATF::affaire()->select($this->el['id_affaire']);
   		$this->client = ATF::societe()->select($this->el['id_societe']);
		ATF::facture_ligne()->q->reset()->where('id_facture',$this->el['id_facture']);
		$this->lignes = ATF::facture_ligne()->sa();
		if ($this->client['id_contact_facturation']) {
			$this->contact = ATF::contact()->select($this->client['id_contact_facturation']);
		}
		$infos_absystech = ATF::societe()->maSociete;
		$this->lastEl = ATF::facture()->getLastFacture($this->el['id_affaire'],$id,true);

		$this->Open();
		$this->Addpage();
		$this->setleftmargin(15);
		$this->setrightmargin(15);
		$this->setdrawcolor(0,184,255);
		
		$this->image(__PDF_PATH__.ATF::$codename."/facturePage1.jpg",5,0,200);
		
		$this->setfont('arial','',8);
		$this->setxy(85,10);
		$this->multicell(110,10,"Le ".ATF::$usr->date_trans($this->el['date'], "force", true).",",0,'R');
				
		$cadre[] = array("size"=>14,"bold"=>true,"txt"=>$this->client['societe'],"h"=>7);
		$cadre[] = array("size"=>12,"bold"=>false,"txt"=>ATF::$usr->trans($infos_contact['civilite'])." ".$infos_contact['nom']." ".$infos_contact['prenom']);
 
		if ($this->client['facturation_adresse']) {
			$cadre[] = array("size"=>12,"txt"=>$this->client['facturation_adresse']);
			if ($this->client['facturation_adresse_2']) $cadre[] = array("size"=>12,"txt"=>$this->client['facturation_adresse_2']);
			if ($this->client['facturation_adresse_3']) $cadre[] = array("size"=>12,"txt"=>$this->client['facturation_adresse_3']);
			$cadre[] = array("size"=>12,"txt"=>$this->client['facturation_cp']." ".$this->client['facturation_ville']." (".ATF::pays()->nom($this->client['facturation_id_pays']).")");
		} else {
			$cadre[] =  array("size"=>12,"txt"=>$this->client['adresse']);
			if ($this->client['adresse_2']) $cadre[] =  array("size"=>12,"txt"=>$this->client['adresse_2']);
			if ($this->client['adresse_3']) $cadre[] =  array("size"=>12,"txt"=>$this->client['adresse_3']);
			$cadre[] =  array("size"=>12,"txt"=>$this->client['cp']." ".$this->client['ville']." (".ATF::pays()->nom($this->client['id_pays']).")");
		}
		
		if ($this->client['reference_tva']) $cadre[] = array("size"=>12,"txt"=>"N° TVA : ".$this->client['reference_tva']);
		
		$this->cadre(100,30,90,40,$cadre);		
		$this->sety(35);

		$this->setfont('arial','B',22);	
		$this->multicell(50,8,strtoupper("facture"),0,'C');
		$this->setfont('arial','',10);	
		$this->multicell(50,5,"Réf. : ".$this->el['ref'],0,'C');
		$this->setfont('arial','B',12);
		$this->multicell(50,5,"\nPour la période du : ".date("d/m/Y", strtotime($this->el['date_debut_periode']))." au ".date("d/m/Y", strtotime($this->el['date_fin_periode'])),0,'C');

		$this->setfont('arial','',8);	
		$this->setxy(5,75);
		$this->multicell(70,4,"Editée par : ".ATF::user()->nom($this->el['id_user'])." (".ATF::user()->select($this->el['id_user'],"email").")",0,'L');
		
		$this->setLeftMargin(5);
		$this->sety(90);
		$head = array("Désignation","Index précédent","Index actuel","Qté","Prix unitaire","Montant");
		$width = array(90,23,22,15,25,25);
		
		if ($this->lignes){
			foreach ($this->lignes as $k => $i) {				

				if ($this->lastEl) {	
					ATF::facture_ligne()->q->reset()->from("facture_ligne","id_facture","facture","id_facture")
													->where("facture.id_facture", $this->lastEl["id_facture"])
													->where("produit",$i["produit"],"AND",false,"LIKE");

					$facture_ligne = ATF::facture_ligne()->select_row();


				}else{			
					ATF::devis_ligne()->q->reset()->from("devis_ligne","id_devis","devis","id_devis")
												  ->where("devis.id_affaire", $this->el["id_affaire"])
												  ->where("produit",$i["produit"],"AND",false,"LIKE");

					$devis_ligne = ATF::devis_ligne()->select_row();
				}
				


				if ($this->lastEl) {
					$qteNB = $i["index_nb"]-$facture_ligne['index_nb'];
					$precNB = $facture_ligne['index_nb'];
				} else {
					$qteNB = $i["index_nb"]-$devis_ligne['index_nb'];
					$precNB = $devis_ligne['index_nb'];
				}
				$data[] = array(					
					$i['produit']." - Page noir et blanc",
					$precNB,
					$i["index_nb"],					
					$qteNB,
					number_format($i['prix_nb'],5,',',' ')." €",
					number_format($qteNB*$i['prix_nb'],2,',',' ')." €",
				);

				$prixHT += $qteNB*$i['prix_nb'];

				$styles[] = array($this->leftStyle,"","","",$this->rightStyle,$this->rightStyle);

				if ($this->lastEl) {
					$qteC = $i["index_couleur"]-$facture_ligne['index_couleur'];
					$precC = $facture_ligne['index_couleur'];
				} else {
					$qteC = $i["index_couleur"]-$devis_ligne['index_couleur'];
					$precC = $devis_ligne['index_couleur'];
				}
				$data[] = array(					
					$i['produit']." - Page couleur",
					$precC,
					$i["index_couleur"],					
					$qteC,
					number_format($i['prix_couleur'],5,',',' ')." €",
					number_format($qteC*$i['prix_couleur'],2,',',' ')." €",
				);
				$prixHT += $qteC*$i['prix_couleur'];
				$styles[] = array($this->leftStyle,"","","",$this->rightStyle,$this->rightStyle);
			}
		}

		ATF::facture()->u(array('id_facture' =>$this->el["id_facture"] , "prix"=>$prixHT ));

		$this->tableau($head,$data,$width,5,$styles,260);
		$this->setLeftMargin(24);
		
		$this->cell(131,4,"",0,0,'C');
		
		$this->cell(25,4,"Total HT",1,0,'R');
		$this->cell(25,4,number_format($prixHT,2,',',' ')." €",1,1,'R');
		$this->cell(131,4,"En votre aimable règlement,",0,0,'L');
		
		if((float)($this->el["tva"])>1){
			$this->cell(25,4,"TVA ".round($this->el["tva"]*100-100,2)."%",1,0,'R');			
			
			$TTC=round($prixHT,2)*$this->el["tva"];
			
			

			$this->cell(25,4,number_format(abs($TTC-$prixHT),2,',',' ')." €",1,1,'R');		
			$this->cell(131,4,"",0,0,'C');
			$this->setfont('arial','B',8);
			$this->cell(25,4," Montant TTC",1,0,'R'); 
			$this->cell(25,4,number_format(round($TTC,2),2,',',' ')." €",1,1,'R');
		}	
		
		if ($this->gety()>225) $this->AddPage();
		else $this->sety(225);
		$this->setleftmargin(15);
		$this->setrightmargin(15);
		//$this->sety(220);
		$this->setfont('arial','',8);
		$this->ln(5);
		$y = $this->gety();
		$cadre = array();
		if($this->el['id_termes']){
			$cadre = array(
				array("txt"=>"\n".ATF::termes()->nom($this->el['id_termes']),"align"=>"C","h"=>5)
			);
		}
		
		
		$this->cadre(15,$y,50,15,$cadre,"Termes de paiement :");
		
		$cadre = array(
			array("txt"=>$this->client['ref'],"align"=>"C")
		);
		$this->cadre(15,$y+20,50,15,$cadre,"Votre numéro client :");

		$cadre = array();
		if ($infos_absystech["banque"]) {
			$cadre[] = $infos_absystech["banque"];
			if ($infos_absystech['rib']) {
				$cadre[] = "RIB : ".util::formatRIB($infos_absystech["rib"]);
			}
			if ($infos_absystech['iban']) {
				$cadre[] = "IBAN : ".$infos_absystech["iban"];
			}
			if ($infos_absystech['bic']) {
				$cadre[] = "BIC : ".$infos_absystech["bic"];
			}
			if ($infos_absystech['swift']) {
				$cadre[] = "SWIFT : ".$infos_absystech["swift"];
			}
			if ($infos_absystech['reference_tva']) {
				$cadre[] = "TVA : ".$infos_absystech['reference_tva'];
			}
		}
		
		$this->cadre(75,$y,60,35,$cadre,"Coordonnées bancaires");
		
		$cadre = array();
		$cadre[] = "En cas de règlement par chèque, merci de l'adresser à :";
		$cadre[] = $infos_societe['societe'];
		if ($infos_absystech["adresse_2"]) $cadre[] = $infos_absystech["adresse_2"];
		$cadre[] = $infos_absystech["adresse"];
		$cadre[] = $infos_absystech["cp"]." ".$infos_absystech["ville"];
		
		$this->cadre(145,$y,50,35,$cadre,"Règlement");
		
		$this->ln(-5);
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"Tout paiement anticipé fera l'objet d'un escompte calculé au prorata au taux de 4,8% l'an",0,'C');
		$this->setfont('arial','',5);
		$this->multicell(0,2,"Tout paiement postérieur à la date d'échéance entraîne l'exigibilité d'intérêts de retard au taux de 12% par an. A compter du 1er janvier 2013, une indemnité de recouvrement de 40€, non soumise à la TVA, est applicable sur chaque facture impayée à date et s'ajoute aux intérêts de retard précédemment cités, en application des articles L441-3 et L441-6 du code de commerce.",0,'C');

	}


















}

class pdf_att extends pdf_absystech { }
class pdf_aewd extends pdf_absystech {	
	public function facture_exoneration_tva($infos_facture,$infos_client) {
		$this->setfont('arial','B',8);
		$this->cell(25,4,"Total exonéré",1,0,'R');
		$this->cell(25,4,number_format(abs($infos_facture["prix"]),2,',',' ')." €",1,1,'R');
		$this->cell(131,4,"TVA non applicable, article 293 B du Code général des impôts, ",0,1,'L');
		if ($infos_client["reference_tva"]) {
			$this->cell(136,4,"Référence TVA : ".$infos_client["reference_tva"],0,1,'L');
		}
	}
}
class pdf_wapp6 extends pdf_absystech { }
?>