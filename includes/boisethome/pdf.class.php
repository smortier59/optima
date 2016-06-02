<?
require_once dirname(__FILE__)."/../pdf.class.php";
/**
* @package Optima
* @subpackage AbsysTech
*/
class pdf_boisethome extends pdf {
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
	}

	public function Footer() {
		if($this->previsu){
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
		$this->multicell(0,3,strtoupper($this->societe["societe"])." - ".$this->societe['structure'].($this->societe["capital"] ? " au capital de ".number_format($this->societe["capital"],2,'.',' ')." EUR" : NULL).($this->societe["siret"] ? " SIRET : ".$this->societe["siret"] : NULL),0,'C');
		$this->multicell(0,3,"Siège social : ".$this->societe['adresse']." - ".$this->societe['cp']." ".$this->societe['ville']." - ".ATF::pays()->select($this->societe['id_pays'],"pays")." - ".$this->societe['email']." - Tél : ".$this->societe['tel'].($this->societe['fax'] ? " - Fax : ".$this->societe['fax'] : NULL),0,'C');
		if (!$this->noPageNo) {
			$this->ln(-3);
			$this->Cell(0,3,$this->noPageNo.'Page '.$this->PageNo(),0,0,'R');
		}
	}

	public function devis_reduit($id,&$s) {
		return $this->devis_detaille($id,$s,false);
	}

	public function devis_detaille($id,&$s,$details=true) {
try {
		$infos_devis = ATF::devis()->select($id);
		ATF::devis_lot()->q->reset()->addCondition("devis_lot.id_devis",ATF::devis()->decryptId($id))->addOrder("devis_lot.id_devis_lot","desc");
		$lots = ATF::devis_lot()->sa();

		$infos_affaire = ATF::affaire()->select($infos_devis['id_affaire']);
		$infos_client = ATF::societe()->select($infos_devis['id_societe']);
		$infos_contact = ATF::contact()->select($infos_devis['id_contact']);
		$infos_user = ATF::user()->select($infos_devis["id_user"]);
		$infos_agence = ATF::agence()->select($infos_user['id_agence']);
		$infos_societe = ATF::societe()->select($infos_user['id_societe']);
		if ($lots) {
			foreach ($lots as $kl => $lot) {
				ATF::devis_lot_produit()->q->reset()->where("devis_lot_produit.id_devis_lot",$lot["id_devis_lot"])->addOrder("devis_lot_produit.id_devis_lot_produit","desc");
				if ($lots[$kl]["produits"] = ATF::devis_lot_produit()->sa()) {
					foreach ($lots[$kl]["produits"] as $kp => $produit) {
						ATF::devis_lot_produit_article()->q->reset()->where("devis_lot_produit_article.id_devis_lot_produit",$produit["id_devis_lot_produit"])->addOrder("devis_lot_produit_article.id_devis_lot_produit_article","desc");
						if ($lots[$kl]["produits"][$kp]["articles"] = ATF::devis_lot_produit_article()->select_all()) {
							foreach ($lots[$kl]["produits"][$kp]["articles"] as $ka => $article) {
								$taux = ATF::marge()->select($article['id_marge'],'taux');
								$lots[$kl]["produits"][$kp]["articles"][$ka]["prix"] = $article['prix_achat'] * $taux;
								$lots[$kl]["produits"][$kp]["articles"][$ka]["total"] = $lots[$kl]["produits"][$kp]["articles"][$ka]["prix"] * $article['quantite'];
								$lots[$kl]["produits"][$kp]["total"] += $lots[$kl]["produits"][$kp]["articles"][$ka]["total"];
							}
						}
						$lots[$kl]["produits"][$kp]["prix"] = $lots[$kl]["produits"][$kp]["total"];
						$lots[$kl]["produits"][$kp]["total"] = $lots[$kl]["produits"][$kp]["quantite"] * $lots[$kl]["produits"][$kp]["total"];
						$lots[$kl]["total"] += $lots[$kl]["produits"][$kp]["total"];
					}
				}
				$total_devis += $lots[$kl]["total"];
			}
		}

		if ($infos_devis['nature']=="avenant") {
			// On supprime l'affichage des articles si on est en mode avenant
			$details = false;
		}

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
		$this->multicell(0,5,$infos_client['cp']." ".$infos_client['ville']);
		$this->setfont('arial','',10);
		$this->setleftmargin(10);
		$this->ln(10);

		$this->multicell(0,7,ucfirst($infos_devis['nature'])." n°".$infos_devis['ref']."-".$infos_devis['revision']." valable jusqu'au ".ATF::$usr->date_trans($infos_devis['validite'],"force"));
		$this->multicell(0,7,"Objet : ".$infos_devis['devis']);

		$this->ln(3);
		$this->ln(5);

		$head = array("Désignation","Uté","Qté","Prix unitaire","Total");
		$width = array(110,10,15,25,26);

		$s_produit = array("size" => 9,"color" => 000000,"font" => "arial","border" => "1","align" => "L", "decoration" => "B", "bgcolor"=> "EFEFEF");
		$s_produit_prix = array("size" => 9,"color" => 000000,"font" => "arial","border" => "1","align" => "R", "decoration" => "B", "bgcolor"=> "EFEFEF");
		$s_produit_unite = array("size" => 7,"color" => 000000,"font" => "arial","border" => "1","align" => "C", "decoration" => "B", "bgcolor"=> "EFEFEF");
		$s_produit_details = array("size" => 7,"color" => 000000,"font" => "arial","border" => "1","align" => "L", "decoration" => "I", "bgcolor"=> "EFEFEF");

		$s_article = array("size" => 7,"color" => 000000,"font" => "arial","border" => "1","align" => "L", "decoration" => "I");
		$s_article_prix = array("size" => 7,"color" => 000000,"font" => "arial","border" => "1","align" => "R", "decoration" => "I");
		$s_article_unite = array("size" => 6,"color" => 000000,"font" => "arial","border" => "1","align" => "C", "decoration" => "I");
		$s_details = array("size" => 6,"color" => 000000,"font" => "arial","border" => "1","align" => "L", "decoration" => "I");
		foreach ($lots as $lot) {
			if ($lot["produits"]) {
				$total_lot = 0;

				$styles = $data = array();

				foreach ($lot["produits"] as $produit) {
					$data[] = array($produit["produit"],ATF::$usr->trans($produit["unite"]),$produit['quantite'],number_format($produit["prix"],2,'.',' '),number_format($produit["total"],2,'.',' '),"details"=>$produit["description"] && $details?"Description : ".$produit["description"]:"");
					$styles[] = array($s_produit,$s_produit_unite,$s_produit_prix,$s_produit_prix,$s_produit_prix,"details"=>$s_produit_details);
					foreach ($produit["articles"] as $article) {
						if ($article["visible"]=="actif" && $details) {
							$data[] = array("   ".$article["article"],ATF::$usr->trans($article["unite"]),$article['quantite'],/*number_format($article['prix'],2,'.',*/' '/*),number_format($article['total'],2,'.'*/,' '/*)*/,"details"=>$article["description"]?"Description : ".$article["description"]:"");
							$styles[] = array($s_article,$s_article_unite,$s_article_prix,$s_article_prix,$s_article_prix,"details"=>$s_details);
						}
					}
				}

				$this->setfont('arial','BU',14);
				$this->multicell(0,15,$lot["libelle"]);
				$this->tableau($head,$data,$width,5,$styles,270);
				$this->setfont('arial','B',9);
				$this->cell($width[0]+$width[1]+$width[2],5,"",0,0,'L');
				$this->cell($width[3],5,"Total du lot",1,0,'R');
				$this->cell($width[4],5,number_format($lot["total"],2,'.',' '),1,1,'R');
			}
		}

		$this->setfont('arial','B',14);
		$this->multicell(0,15,"Total : ".number_format($total_devis,2,'.',' ')." EUR",0,0,'R');

		if ($infos_devis["remise"]) {
			$remise = $total_devis * $infos_devis["remise"] / 100;
			$this->setfont('arial','B',14);
			$this->multicell(0,15,"Remise (".$infos_devis["remise"]."%) : -".number_format($remise,2,'.',' ')." EUR",0,0,'R');
			$total_devis -= $remise;

			$this->setfont('arial','B',14);
			$this->multicell(0,15,"Total après remise : ".number_format($total_devis,2,'.',' ')." EUR",0,0,'R');
		}

		if ($infos_devis['nature']=="avenant") {
			ATF::devis()->q->reset()->setDimension("row")
				->where("revision",chr(ord($infos_devis["revision"])-1))
				->where("ref",$infos_devis["ref"]);
			$infos_devis_precedent = ATF::devis()->sa();

			$this->setfont('arial','B',14);
			$this->multicell(0,15,"Solde déjà engagé sur ".$infos_devis_precedent["nature"]." ".$infos_devis["ref"]."-".chr(ord($infos_devis["revision"])-1)." : ".number_format($infos_devis_precedent["prix"],2,'.',' ')." EUR",0,0,'R');

			$total_restant = $total_devis - $infos_devis_precedent["prix"];

			if ($total_restant>=0) {
				$this->setfont('arial','B',14);
				$this->multicell(0,15,"Total à facturer : ".number_format($total_restant,2,'.',' ')." EUR",0,0,'R');
			} else {
				$this->setfont('arial','B',14);
				$this->multicell(0,15,"AVOIR résultant : ".number_format(abs($total_restant),2,'.',' ')." EUR",0,0,'R');
			}
		}

		if ($infos_devis['nature']=="devis") {
			// Echeancier
			$styles = $data = array();
			$head = array("","Pourcentage du total","Montant à payer");
			$width = array(100,50,36);

			foreach ($lots as $lot) {
				if ($total_devis*$lot["payer_pourcentage"]>0) {
					$data[] = array("Payer lorsque le lot '".$lot["libelle"]."' est livré ",$lot["payer_pourcentage"]." %",number_format($total_devis*$lot["payer_pourcentage"]/100,2,'.',' ')." EUR");
					$styles[] = array($s_produit,$s_produit,$s_produit_prix);
				}
			}

			$this->setfont('arial','BU',14);
			$this->multicell(0,15,"Echeanchier prévu");
			$this->tableau($head,$data,$width,5,$styles,270);
		}

		$this->setfont('arial','',10);
		$this->ln(5);
		$this->setx(15);
		$this->multicell(0,5,ATF::politesse()->nom($infos_devis['id_politesse_post']));
		$this->multicell(0,5,$infos_user['civilite'].". ".$infos_user['nom']." ".$infos_user['prenom'],0,'R');

		$this->pied($infos_societe);
} catch (Exception $e) {
	echo $e->getmessage();
}
	}


	public function facture($id,&$s) {
		$id = ATF::facture()->decryptId($id);
		$this->setFooter();
		// Couleurs entêtes
		$this->Rentete = 238;
		$this->Gentete = 241;
		$this->Bentete = 255;
		$infos_facture = ATF::facture()->select($id);
		$type_facture=$infos_facture["type_facture"];

		$infos_client = ATF::societe()->select($infos_facture['id_societe']);
		if ($infos_facture['id_affaire']) {
			ATF::devis()->q->reset()->addCondition('id_affaire',$infos_facture['id_affaire'])->setDimension("row")->end();
			$infos_devis = ATF::devis()->select_all(false,false,false,false,true);
			$infos_affaire = ATF::affaire()->select($infos_facture['id_affaire']);
			$infos_devis_lot = ATF::devis_lot()->select($infos_facture['id_devis_lot']);
		}
		$infos_user = ATF::user()->select($infos_facture["id_user"]);
		$infos_societe = ATF::societe()->select($infos_user['id_societe']);
		$this->societe = $infos_boisethome = ATF::societe()->select(1);
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

		$cadre[] = array("size"=>14,"bold"=>true,"txt"=>$infos_client['societe'],"h"=>7);
		$cadre[] = array("size"=>12,"bold"=>false,"txt"=>ATF::$usr->trans($infos_contact['civilite'])." ".$infos_contact['nom']." ".$infos_contact['prenom']);

		if ($infos_client['facturation_adresse']) {
			$cadre[] = array("size"=>12,"txt"=>$infos_client['facturation_adresse']);
			if ($infos_client['facturation_adresse_2']) $cadre[] = array("size"=>12,"txt"=>$infos_client['facturation_adresse_2']);
			if ($infos_client['facturation_adresse_3']) $cadre[] = array("size"=>12,"txt"=>$infos_client['facturation_adresse_3']);
			$cadre[] = array("size"=>12,"txt"=>$infos_client['facturation_cp']." ".$infos_client['facturation_ville']." (".ATF::pays()->nom($infos_client['facturation_id_pays']).")");
		} else {
			$cadre[] =  array("size"=>12,"txt"=>$infos_client['adresse']);
			if ($infos_client['adresse_2']) $cadre[] =  array("size"=>12,"txt"=>$infos_client['adresse_2']);
			if ($infos_client['adresse_3']) $cadre[] =  array("size"=>12,"txt"=>$infos_client['adresse_3']);
			$cadre[] =  array("size"=>12,"txt"=>$infos_client['cp']." ".$infos_client['ville']." (".ATF::pays()->nom($infos_client['id_pays']).")");
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


		$this->setfont('arial','',8);
		$this->setxy(5,75);
		$this->multicell(70,4,"Edité".($type_facture!="avoir"?"e":"")." par : ".ATF::user()->nom($infos_facture['id_user'])." (".ATF::user()->select($infos_facture['id_user'],"email").")",0,'L');


		$this->ln(5);
		$this->setfont('arial','',15);
		$this->multicell(121,7,$infos_affaire["affaire"]." / ".$infos_devis_lot["libelle"],0,1,'L');
		$this->ln(5);

		$this->setfont('arial','',12);
		$this->cell(121,7,"",0,0,'C');
		$this->cell(35,7,"Total HT",1,0,'R');

		switch ($type_facture) {
			case "avoir":
				$this->cell(35,7,number_format(-$infos_facture["prix"],2,',',' ')." €",1,1,'R');
				$this->cell(121,7,"",0,0,'C');
			break;
			default:
				$this->cell(35,7,number_format($infos_facture["prix"],2,',',' ')." €",1,1,'R');
				$this->cell(121,7,"En votre aimable règlement,",0,0,'L');
			break;
		}

		if((float)($infos_facture["tva"])>1){
			$this->cell(35,7,"TVA ".round($infos_facture["tva"]*100-100,2)."%",1,0,'R');
			$TTC=$infos_facture['prix']*$infos_facture["tva"];
			$this->cell(35,7,number_format(abs($TTC-$infos_facture["prix"]),2,',',' ')." €",1,1,'R');
			$this->cell(121,7,"",0,0,'C');
			$this->setfont('arial','B',8);
			$this->cell(35,7," Montant TTC",1,0,'R');
			$this->cell(35,7,number_format(abs($TTC),2,',',' ')." €",1,1,'R');
		}

		if ($this->gety()>225) $this->AddPage();
		else $this->sety(225);
		$this->setleftmargin(15);
		$this->setrightmargin(15);

		$this->setfont('arial','',8);
		$this->ln(5);
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
		if ($infos_boisethome["banque"]) {
			$cadre[] = $infos_boisethome["banque"];
			if ($infos_boisethome['rib']) {
				$cadre[] = "RIB : ".util::formatRIB($infos_boisethome["rib"]);
			}
			if ($infos_boisethome['iban']) {
				$cadre[] = "IBAN : ".$infos_boisethome["iban"];
			}
			if ($infos_boisethome['bic']) {
				$cadre[] = "BIC : ".$infos_boisethome["bic"];
			}
			if ($infos_boisethome['swift']) {
				$cadre[] = "SWIFT : ".$infos_boisethome["swift"];
			}
			if ($infos_boisethome['reference_tva']) {
				$cadre[] = "TVA : ".$infos_boisethome['reference_tva'];
			}
		}

		$this->cadre(75,$y,60,35,$cadre,"Coordonnées bancaires");

		$cadre = array();
		$cadre[] = "En cas de règlement par chèque, merci de l'adresser à :";
		$cadre[] = $infos_societe['societe'];
		if ($infos_boisethome["adresse_2"]) $cadre[] = $infos_boisethome["adresse_2"];
		$cadre[] = $infos_boisethome["adresse"];
		$cadre[] = $infos_boisethome["cp"]." ".$infos_boisethome["ville"];

		$this->cadre(145,$y,50,35,$cadre,"Règlement");

		$this->ln(-5);
		$this->setfont('arial','B',8);
		$this->multicell(0,5,"Tout paiement anticipé fera l'objet d'un escompte calculé au prorata au taux de 4,8% l'an",0,'C');
		$this->setfont('arial','',5);
		$this->multicell(0,2,"Tout paiement postérieur à la date d'échéance entraîne l'exigibilité d'intérêts de retard au taux de 12% par an avec un montant minimum de 65 €. A compter du 1er janvier 2013, une indemnité de recouvrement de 40€, non soumise à la TVA, est applicable sur chaque facture impayée à date et s'ajoute aux intérêts de retard précédemment cités, en application des articles L441-3 et L441-6 du code de commerce.",0,'C');
	}

	public function barcode($articles,$s) {
		require_once __ATF_PATH__.'libs/barcodegen.1d-php5.v5.2.1/class/BCGFont.php';
		require_once __ATF_PATH__.'libs/barcodegen.1d-php5.v5.2.1/class/BCGColor.php';
		require_once __ATF_PATH__.'libs/barcodegen.1d-php5.v5.2.1/class/BCGDrawing.php';
		require_once __ATF_PATH__.'libs/barcodegen.1d-php5.v5.2.1/class/BCGcode39.barcode.php';
		$color_black = new BCGColor(0, 0, 0);
		$color_white = new BCGColor(255, 255, 255);
		parent::__construct('P','mm','A4');
		$this->Open();
		$this->Addpage();
		$this->setleftmargin(10);
		$this->setrightmargin(10);
		$this->SetAutoPageBreak(auto,1);
		$this->setfont('arial','B',10);

		$path = tempnam('/tmp/','boisethome_pdf_barcode_');
		foreach ($articles as $k => $a) {
			$ref = $a["article.id_article"];
			if (!file_exists($path.$ref.".png")) {
				$code = new BCGcode39();
				$code->setScale(2);
				$code->setThickness(30);
				$code->setForegroundColor($color_black);
				$code->setBackgroundColor($color_white);
				$code->parse($ref);

				$drawing = new BCGDrawing($path.$ref.".png", $color_white);
				$drawing->setBarcode($code);
				$drawing->draw();
				$drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
			}

			$x=40+80*($k%2);
			$y=19+20*(floor($k/2)%12);
			$this->image($path.$ref.".png",$x,$y,45,12);
			$this->setxy($x-13,$y-4);
			$this->multicell(70,4,substr($a["article.article"],0,30) . " (".$a["article.unite"].")",0,'C');
			unlink($path.$ref.".png");

			$page++;
			if ($page % 24 == 0) $this->Addpage();
		}
		$this->close();

		header('Content-disposition: attachment; filename=barcode.pdf');
		header("Content-type: application/pdf");
		echo $this->buffer;
		die;
	}
}