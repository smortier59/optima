<?
require_once dirname(__FILE__)."/../pdf.class.php";
/**
* @package Optima
* @subpackage Manala
*/
class pdf_manala extends pdf {
	var $Rentete = 255;
	var $Gentete = 92;
	var $Bentete = 156;
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

	private $boldStyle = array(
		 "size" => 8
		,"color" => 000000
		,"font" => "arial"
		,"border" => ""
		,"align" => "L"
		,"decoration" => "B"
	);

	var $repeatEntete = true;

	private $noPageNo = true;

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
		if (!ATF::societe()->maSociete) return false;
		$this->setautopagebreak(false,'1');
		$this->SetXY(10,-10);
		$this->setfont('arial','I',8);
		$this->multicell(0,3,strtoupper(ATF::societe()->maSociete["societe"])." - ".ATF::societe()->maSociete['structure'].(ATF::societe()->maSociete["capital"] ? " au capital de ".number_format(ATF::societe()->maSociete["capital"],2,',','.')." EUR -" : NULL).(ATF::societe()->maSociete["siret"] ? " SIRET : ".ATF::societe()->maSociete["siret"] : NULL),0,'C');
		$this->multicell(0,3,"Siège social : ".ATF::societe()->maSociete['adresse']." - ".ATF::societe()->maSociete['cp']." ".ATF::societe()->maSociete['ville']." - ".ATF::pays()->select(ATF::societe()->maSociete['id_pays'],"pays")." - ".ATF::societe()->maSociete['email'].(ATF::societe()->maSociete['fax'] ? " - Tél : ".ATF::societe()->maSociete['tel'] : NULL).(ATF::societe()->maSociete['fax'] ? " - Fax : ".ATF::societe()->maSociete['fax'] : NULL),0,'C');
		if (!$this->noPageNo) {
			$this->ln(-3);
			$this->Cell(0,3,$this->noPageNo.'Page '.$this->PageNo(),0,0,'R');
		}
	}

	public function ficheCasting($data) {
		$ids = explode(",",$data['ids']);
		$f = explode(",",$data['fields']);

		$this->DefOrientation='L';
		$this->wPt=$this->fhPt;
		$this->hPt=$this->fwPt;

		$this->addPage();
		$this->setLeftMargin(15);
		$this->setFont('arial','B',30);

		$this->settextcolor("ff5c9c");
		$this->multicell(0,15,"Prestation",0,"R");
		if ($data['client']) $this->multicell(0,15,$data['client'],0,"R");
		if ($data['date']) $this->multicell(0,15,date('d/m/Y',strtotime($data['date'])),0,"R");
		if ($data['lieu']) $this->multicell(0,15,$data['lieu'],0,"R");
		$this->settextcolor("000000");


		// LOGO !
		$this->image(ATF::$staticserver."images/logos/manala.jpg",15,180,75);


		foreach ($ids as $k=>$i) {
			if (!$i) continue;
			$p = ATF::personnel()->select($i);

			$this->addPage();
			$this->setLeftMargin(15);
			$this->setFont('arial','B',24);

			$this->setxy(15,15);
			$this->settextcolor("ff5c9c");
			$this->multicell(0,5,ATF::$usr->trans($p['civilite'])." ".$p['prenom']." ".$p['nom']);
			$this->settextcolor("000000");

			// photo 1
			$fp1 = ATF::personnel()->filepath($p['id_personnel'],"photo_identite");
			if (file_exists($fp1)) {
				$x = 15;
				$y = 50;
				$w = 80;
				$h = false;
				$type = "jpg";

				$thumbPath1 = ATF::gd()->createThumb("personnel",$p['id_personnel'],"photo_identite",500,500,$type);
				parent::photo($thumbPath1,$x,$y,$w,$h,$type);
			}

			// Photo 2
			$fp2 = ATF::personnel()->filepath($p['id_personnel'],"photo_pleine");
			if (file_exists($fp2)) {
				$x = 200;
				$y = 20;
				$w = false;
				$h = 160;
				$type = "jpg";

				$thumbPath2 = ATF::gd()->createThumb("personnel",$p['id_personnel'],"photo_pleine",500,500,$type);
				parent::photo($thumbPath2,$x,$y,$w,$h,$type);
			}

			// Texte entre deux
			if (count($f)>7) {
				$this->setFont('arial','B',14);
				$h = 8;
			} else {
				$this->setFont('arial','B',18);
				$h = 13;
			}
			$this->setLeftMargin(100);
			$this->sety(50);
			if (in_array("age",$f)) {
				$this->multicell(95,$h,util::age($p['date_naissance'])." ans");
			}
			if (in_array("taille",$f)) {
				$this->multicell(95,$h,$p['taille']);
			}
			if (in_array("mensurations",$f)) {
				$this->multicell(95,$h,"Taille : ".$p['mensuration_haut']." - ".$p['mensuration_bas']);
			}
			if (in_array("adresse_full",$f)) {
				$this->multicell(95,$h,"Adresse : ".$p['adresse'].", ".$p['cp']." ".$p['ville']);
			}
			if (in_array("ville",$f)) {
				$this->multicell(95,$h,"Ville : ".$p['ville']);
			}
			if (in_array("cp",$f)) {
				$this->multicell(95,$h,"CP : ".$p['cp']);
			}
			if (in_array("lieu_naisance",$f)) {
				$this->multicell(95,$h,'Né(e) à : '.$p['lieu_naissance']);
			}
			if (in_array("date_naisance",$f)) {
				$this->multicell(95,$h,'Né(e) le : '.date("d-m-Y",strtotime($p['date_naissance'])));
			}
			if (in_array("num_secu",$f)) {
				$this->multicell(95,$h,"Numéro de sécu : ".$p['num_secu']);
			}
			if (in_array("nationalite",$f)) {
				$this->multicell(95,$h,"Nationalité : ".$p['nationalite']);
			}
			if (in_array("email",$f)) {
				$this->multicell(95,$h,"Email : ".$p['email']);
			}
			if (in_array("tel",$f)) {
				$this->multicell(95,$h,"Tél. : ".$p['tel']);
			}
			if (in_array("permis",$f)) {
				$this->multicell(95,$h,"Permis : ".$p['permis']);
			}
			if (in_array("voiture",$f)) {
				$this->multicell(95,$h,"Voiture : ".$p['voiture']);
			}
			if (in_array("anglais",$f)) {
				$this->multicell(95,$h,"Anglais : ".$p['anglais']);
			}
			if (in_array("langues",$f)) {
				$this->multicell(95,$h,"Autres langues : ".$p['langues']);
			}

			if (in_array("last_client",$f)) {
				ATF::mission_ligne()->q->reset()
										->addField("id_societe")
										->where("id_personnel",$p['id_personnel'])
										->from("mission_ligne","id_mission","mission","id_mission")
										->setDistinct()
										->setStrict()
										->setLimit(3)
										->addOrder("date","desc");
				$lm = ATF::mission_ligne()->sa();

				$clients = "";
				foreach ($lm as $i) {
					$clients .= ATF::societe()->nom($i['id_societe']);
					if (end($lm)!=$i) $clients .= ", ";
				}
				if ($clients) {
					$this->multicell(95,$h,"Derniers clients : ".$clients);
				}
			}

			// LOGO !
			$this->image(ATF::$staticserver."images/logos/manala.jpg",15,180,75);

		}


		$this->addPage();
		$this->setLeftMargin(25);
		$this->setFont('arial','B',45);

		$this->settextcolor("ff5c9c");
		$this->multicell(0,15,"Manala ... contact");
		$this->settextcolor("000000");
		$this->ln(20);

		$this->setFont('arial','',25);
		$this->multicell(0,10,strtoupper(ATF::societe()->maSociete['societe']));
		$this->multicell(0,10,ATF::societe()->maSociete['adresse']);
		$this->multicell(0,10,ATF::societe()->maSociete['adresse_2']);
		$this->multicell(0,10,ATF::societe()->maSociete['cp'].' '.ATF::societe()->maSociete['ville']);

		$this->ln(5);

		$this->multicell(0,10,"Votre contact : Emmanuelle OLLIVIER");
		$this->multicell(0,10,"Tel : 06.23.20.57.10");

		$this->ln(5);
		$this->multicell(0,10,ATF::societe()->maSociete['web']);

		$this->ln(5);
		$this->multicell(0,10,ATF::societe()->maSociete['email']);

		// PHOTO !
		$this->image(ATF::$staticserver."images/manala/emmanuelle.jpg",210,60,60);


		// LOGO !
		$this->image(ATF::$staticserver."images/logos/manala.jpg",15,180,75);


	}


	public function contratTravail($id) {
		$el = ATF::mission_ligne()->select($id);
		$mission = ATF::mission()->select($el['id_mission']);
		$personnel = ATF::personnel()->select($el['id_personnel']);
		$client = ATF::societe()->select($mission['id_societe']);

		$this->addPage();
		$this->setLeftMargin(15);

		// LOGO !
		$this->image(ATF::$staticserver."images/logos/manala.jpg",15,10,50);

		$this->setFont('arial','BU',14);
		$this->sety(15);
		$this->setx(70);
		$this->multicell(0,5,"CONTRAT A DUREE DETERMINEE",0,"C");

		$this->ln(5);
		$this->setFont('arial','',8);
		$this->multicell(0,5,"Entre les soussignés,");

		$lasociete = array(
			strtoupper(ATF::societe()->maSociete['societe'])." ".strtoupper(ATF::societe()->maSociete['structure']),
			ATF::societe()->getAddress(ATF::societe()->maSociete['id_societe'],true),
			"N° de SIRET : ".ATF::societe()->maSociete['siret'],
			"Code NAF : ".ATF::societe()->maSociete['naf'],
			"Représentée par Mme Emmanuelle OLLIVIER"
		);

		$this->cadre(15,30,85,30,$lasociete,"La société");

		$lepersonnel = array(
			$personnel['nom']." ".$personnel['prenom'].", né(e) le ".$personnel['date_naissance']." à ".$personnel['lieu_naissance'],
			"Num. sécu : ".$personnel['num_secu'],
			"Demeurant : ".$personnel['adresse'],
			"                    ".$personnel['cp']." ".$personnel['ville'],
			"Nationalité : ".$personnel['nationalite'],
		);

		$this->cadre(110,30,85,30,$lepersonnel,"Le salarié");



		$y = $this->getY();

		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article 1 : Objet / Fonction");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"Le salarié est engagé le ".ATF::$usr->date_trans($mission['date_debut'],true,true)." en tant que ".$el['poste'].". Le présent contrat est conclu dans le cadre d’un plan d’animation pour la société ".$client['societe']." en raison d’un surcroît de travail lié avec notre client.",0,"FJ");

		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article 2 : Durée / horaires");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"Le présent contrat est conclu pour commencer le ".ATF::$usr->date_trans($mission['date_debut'],true,true)." et finir le ".ATF::$usr->date_trans($mission['date_fin'],true,true)." pour un nombre total d’heures travaillées de : ".$el['heure_totale']." heure(s). Les horaires journaliers sont définis dans le dossier de travail remis au salarié. En cas de nécessité, le salarié devra assurer d’éventuels dépassements d’horaires à la demande de son supérieur hiérarchique",0,"FJ");

		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article 3 : Fonctions et obligations professionnelles");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"Les attributions du salarié sont définies dans le dossier de travail remis au salarié. Des tâches annexes pourront être confiées au salarié lorsque la bonne marche de l’entreprise l’exigera. Le salarié devra impérativement respecter les consignes que lui signifiera son supérieur, de même que les consignes générales de travail comme le respect des horaires, itinéraires, délais et autres informations. A défaut, il s’exposera à des sanctions pouvant aller jusqu’à la rupture du présent contrat. Compte tenu de la nature de l’activité et des fonctions du salarié, celui-ci devra scrupuleusement observer les règles de politesse et de bienséance. Toute attitude de nature à nuire à l’image de la société pourra entraîner la rupture immédiate du contrat sans rémunération. Étant donné la nature de ses fonctions, le salarié devra respecter la plus grande discrétion et observer en toute circonstance une obligation de réserve et de confidentialité.",0,"FJ");


		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article 4 : Lieu de travail");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"Le salarié exercera ses fonctions à l’adresse indiquée dans le dossier de travail remis au salarié et à tout autre endroit où les nécessités du service l’exigeront.",0,"FJ");


		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article 5 : Rémunération / congés");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"Le salarié percevra une rémunération brute de ".$el['prix']." € de l’heure. A l’issue de ce contrat, le salarié percevra des indemnités de fin de mission : une indemnité de congés payés de 10% ainsi qu’une indemnité de précarité de 10% également.",0,'FJ');


		$this->setLeftMargin(110);
		$this->setY($y);



		$numarticle = 6;

		if ($el['panier_repas']=="oui") {
			$this->setFont('arial','B',10);
			$this->multicell(90,5,"Article ".$numarticle." : Indemnités repas");
			$this->setFont('arial','',8);
			$this->multicell(90,4,"Le salarié percevra une indemnité forfaitaire de panier-repas de ",0,'FJ');
			$this->setFont('arial','B',8);
			$this->multicell(90,4,$el["prix_panier_repas"]." € par jour.");
			$this->setFont('arial','',8);
			$numarticle++;
		}

		if ($el['defraiement']=="oui") {
			$this->setFont('arial','B',10);
			$this->multicell(90,5,"Article ".$numarticle." : Indemnités kilométriques");
			$this->setFont('arial','',8);
			/*$this->multicell(90,4,"Dans le cas où l’utilisation de son véhicule personnel est constatée, le salarié percevra des indemnités kilométriques depuis son domicile jusqu’à son lieu de travail aller et retour : les indemnités kilométriques sont calculées à partir du site Mappy.fr. Cette indemnité est plafonné à 13€.");*/
			$this->multicell(90,4,"Le salarié percevra des indemnités kilométriques à hauteur de ",0,'FJ');
			$this->setFont('arial','B',8);
			$this->multicell(90,4,$el['indemnite_defraiement']."€/km.");
			$this->setFont('arial','',8);
			$numarticle++;
		} else if ($el['defraiement']=="forfait") {
			$this->setFont('arial','B',10);
			$this->multicell(90,5,"Article ".$numarticle." : Indemnités kilométriques");
			$this->setFont('arial','',8);
			$this->multicell(90,4,"Dans le cas où l’utilisation de son véhicule personnel est constatée, le salarié percevra des indemnités kilométriques depuis son domicile jusqu’à son lieu de travail aller et retour : les indemnités kilométriques sont calculées au forfait pour un montant de ".$el['indemnite_defraiement']."€. ");
			$numarticle++;
		}

		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article ".$numarticle." : Annulation");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"En cas d’annulation moins de 24 heures avant la date de l’animation, le salarié percevra une indemnité égale à 20% de sa rémunération brute hors primes et hors indemnités repas. En cas d’annulation plus de 24 heures avant la date de l’animation, le salarié ne percevra aucune rémunération ni dédommagement.",0,'FJ');
		$numarticle++;

		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article ".$numarticle." : Cession de droit à l’image");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"Le salarié autorise la société à utiliser des photos du salarié prises lors de son animation. Ces photos sont réservées à un usage très limité de reporting pour les besoins du client de la société. Ces photos pourront également être utilisées par la société pour son site internet ou des présentations de son activité à des prospects commerciaux. Cette cession de droit à l’image n’a pas de limite de durée et ne donne droit à aucune contrepartie financière au profit du salarié.",0,"FJ");
		$numarticle++;

		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article ".$numarticle." : Avantages sociaux");
		$this->setFont('arial','',8);

		$text = ucfirst($personnel['civilite'])." ".$personnel['nom']." ".$personnel['prenom']." bénéficiera des lois sociales instituées en faveur des salariés notamment en matière de sécurité sociale et en ce qui concerne le régime de : retraite complémentaire et santé";

		$this->multicell(90,4,$text,0,'FJ');
		$this->multicell(90,4,ucfirst($personnel['civilite'])." ".$personnel['nom']." ".$personnel['prenom']." relève de la catégorie Employé, et sera affiliée dès son entrée de la Société aux caisses correspondantes à sa qualification:",0,"FJ");

		$this->multicell(90,4,"       - Pour la retraite au Groupe KLESIA : 4-22 Rue Marie-Georges Piquart, 75017 Paris\n       - Pour le régime de frais de santé a QUATREM : 59-61 rue de la Fayette, 75009 Paris");
		$numarticle++;

		$this->setFont('arial','B',10);
		$this->multicell(90,5,"Article ".$numarticle." : Tenue de travail");
		$this->setFont('arial','',8);
		$this->multicell(90,4,"Dans le cas où la société Manala est amenée à prêter au salarié une tenue de travail et/ou accessoire et/ou matériel informatique type tablette ou ordinateur, le salarié s'engage à en prendre l'entière responsabilité. Il tiendra au salarié d'en prendre soin et de restituer l'ensemble dans son intégralité à son employeur dans un délai de deux semaines maximum, sous peine de retenue sur salaire.",0,'FJ');


		$this->setY(224);
		$this->setLeftMargin(15);

		$this->ln(5);
		$this->multicell(0,5,"Fait à Tourcoing, le ".date("d/m/Y"));



		$this->ln(5);
		$this->cell(85,5,"Pour la société",0,0,"C");
		$this->cell(10,5,"");
		$this->cell(85,5,"Pour le salarié",0,1,"C");
		$this->setFont('arial','B',8);
		$this->cell(85,5,strtoupper(ATF::societe()->maSociete['societe'])." représentée par Mme Emmanuelle Ollivier.",0,0,"C");
		$this->cell(10,5,"");
		$this->cell(85,5,ATF::personnel()->nom($el['id_personnel']),0,1,"C");
		$this->setFont('arial','',8);


		$y = $this->gety();
		$this->image(__PDF_PATH__.ATF::$codename."/signature.jpg",25,$y,70);
		$this->cadre(15,$y,85,35,false,"Signature");
		$this->cadre(110,$y,85,35,array(array("txt"=>"(précédée de la mention lu et approuvé, bon pour accord)","italic"=>true,"size"=>6,"align"=>"C"),"","","","",""),"Signature");

	}

	public function textToSpace($tbold){
		$return = "";
		for($i=0; $i<strlen($tbold); $i++){
			$return = $return." ";
		}
		return $return;
	}

	public function facture($id) {
		$el = ATF::facture()->select($id);
		$client = ATF::societe()->select($el['id_societe']);

		ATF::facture_mission()->q->reset()->where('id_facture',$el['id_facture']);
		$mf = ATF::facture_mission()->sa();
		foreach ($mf as $k=>$i) {
			$missions .= ATF::mission()->select($i['id_mission'],"mission");
			$missions .= ", ";
		}

		$this->setFooter();

		$this->addpage();
		$this->setLeftMargin(15);
		$this->setFont('arial','',8);
		// LOGO !
		$this->image(ATF::$staticserver."images/logos/manala.jpg",82,15,50);

		$this->sety(30);

		$this->ln(10);
		$this->setFont('arial','BU',16);
		$this->multicell(0,10,"FACTURE",0,"C");
		$this->setFont('arial','',8);

		$y = $this->gety()+5;
		$d1 = array(
			"Numéro de commande : ".$el['num_commande'],
			"Numéro de fournisseur : ".$el['num_num_fournisseur'],
			"Référence de facture : ".$el['ref'],
			"Date : ".$el['date'],
		);

		$this->cadre(25,$y,70,30,$d1,"Informations facture");

		$d2 = array(
			array("txt"=>$client['societe'],"align"=>"C","bold"=>true,"size"=>10),
			array("txt"=>$client['adresse'],"align"=>"C"),
			array("txt"=>$client['adresse_2'],"align"=>"C"),
			array("txt"=>$client['cp']." ".$client['ville'],"align"=>"C"),
			array("txt"=>ATF::pays()->nom($client['id_pays']),"align"=>"C")
		);

		$this->cadre(115,$y,70,30,$d2,"Informations client");

		$head = array("Désignation","Base","Prix unitaire HT","Prix HT");
		$w = array(120,20,20,20);
		$data = array(
			array(
				$el['designation'],
				$el['base'],
				$el['prix_unitaire']." €",
				$el['prix']." €"
			)
		);
		$style = array(array($this->leftStyle,$this->rightStyle,$this->rightStyle,$this->rightStyle));

		$this->ln(30);
		$this->tableau($head,$data,$w,5,$style);
		$data = array(
			array(
				"Mission concernées : ".$missions
			)
		);
		$style = array(array($this->boldStyle));
		$this->tableau(false,$data,false,5,$style);

		$head = array("Base HT","Taux de TVA","Montant TVA","Total HT","Total TTC");
		$data = array(
			array($el['base']." €",number_format(($el['tva']-1)*100,2)." %",$el['prix']*($el['tva']-1)." €",$el['prix']." €",$el['prix']." €")
		);
		$this->ln(5);
		$this->tableau($head,$data);

		$this->ln(10);

		$this->multicell(0,5,"Intérêt de retard : 300% de l'intérêt légal.");
		$this->setFont('arial','I',7);
		$this->multicell(0,5,"Passé le délai de règlement indiqué ci-dessous, les pénalités de retard sont immédiatement exigibles (article L.441-6 du code du commerce) et des sanctions civiles peuvent être réclamées dans l'hypothèse où des abus seraient constatés");
		$this->setFont('arial','',8);
		$this->ln(5);
		$this->multicell(0,5,"Facture à payer comptant à réception par chèque ou virement");
		$this->multicell(0,5,"Tout paiement anticipé partiel ou total ne donne pas lieu à escompte");
	}

	public function attestationEmploi($id) {
		$el = ATF::mission_ligne()->select($id);
		$mission = ATF::mission()->select($el['id_mission']);
		$personnel = ATF::personnel()->select($el['id_personnel']);
		$client = ATF::societe()->select($mission['id_societe']);

		$this->addPage();
		$this->setLeftMargin(15);

		// LOGO !
		$this->image(ATF::$staticserver."images/logos/manala.jpg",15,10,50);

		$this->setFont('arial','BU',14);
		$this->sety(50);
		$this->multicell(0,5,"ATTESTATION D’EMPLOI",0,"C");
		$this->setFont('arial','BI',8);
		$this->multicell(0,5,"(à remettre aux salariés intervenants dans les magasins pour remise au pointeau sécurité)",0,"C");
		$this->ln(5);
		$this->setFont('arial','',10);
		$this->multicell(0,5,"      Je soussignée Emmanuelle OLLIVIER, Gérante de MANALA SARL (siret : 53748734000037) situé 200 rue de Roubaix BP 32000 59203 TOURCOING, certifie que le porteur de la présente : ".ATF::personnel()->nom($personnel['id_personnel']).", né(e) le ".$personnel['date_naissance']." et résidant ".$personnel['adresse'].", ".$personnel['cp']." ".$personnel['ville']." est inscrit à l'effectif depuis le ".date("d/m/Y",strtotime($mission['date_debut'])).", sous contrat à durée déterminée en qualité de : ".$el['poste']);

		$this->ln(10);
		$this->multicell(0,5,"Et que conformément aux dispositions de la loi du 31 décembre 1991 relative au travail clandestin, il est fait application des dispositions des articles L 324-9 et suivants concernant « le travail dissimulé », des articles L L 3243-1, L3243-2, L L1221-10, L1221-13, L1221-15  du code du travail relatifs au bulletin de paie et au Registre unique du Personnel, et des articles L 5221-1 et suivants relatifs au « travail des étrangers ».");

		$this->ln(10);
		$this->multicell(0,5,"De plus, ne satisfait pas en fait des conditions prévues par les articles L7311-3, L7313-1, L7313-2, L7313-18 et suivants du livre III du Code du travail et cependant est appelé à exercer des opérations de ventes en dehors des locaux de l'entreprise ci-dessus désignée.");

		$this->ln(10);
		$this->multicell(0,5,"Dans le cadre de son contrat de travail, il est appelé à intervenir dans les hypermarchés et supermarchés.");

		$this->ln(10);
		$this->multicell(0,5,"Attestation établie pour valoir ce que de droit.",0,"R");
		$this->multicell(0,5,"A Tourcoing, le ".date("d/m/Y").".",0,"R");

		$this->image(__PDF_PATH__.ATF::$codename."/signature.jpg",140,$this->gety(),70);

		$this->addPage();

		$this->setFont('arial','BU',14);

		$this->multicell(0,10,"Mesures générales d’hygiène et de sécurité","TLR","C");
		$this->setFont('arial','BI',10);
		$this->multicell(0,5,"Applicables au sein de l’établissement","BRL","C");
		$this->ln(5);

		$this->setFont('arial','B',10);
		$this->multicell(0,5,"En cas d'accident ou d'incendie : appeler la SECURITE au 218","","C");
		$this->ln(5);

		$this->setFont('arial','',10);
		$this->multicell(0,5,"Je soussigné(e) ".ATF::personnel()->nom($personnel['id_personnel']).", salarié(e) de la société MANALA SARL, reconnais avoir été informé des mesures générales d’hygiène et de sécurité qui sont en vigueur au sein de l’établissement ".$mission['lieu']." , et notamment de :");
		$this->ln(5);

		$this->setFont('arial','BU',11);
		$this->multicell(0,5,"1.	Mesures générales concernant l’hygiène :");
		$this->setFont('arial','',10);
		$this->ln(5);
		$this->multicell(0,5,"Toute personne présente dans l’établissement doit, par son comportement, contribuer au respect des règles d’hygiène.");
		$this->multicell(0,5,"Elle exécute  un travail au sein de l’établissement et doit donc veiller au respect de l’ensemble des règles d’hygiène  et en particulier la propreté. ");
		$this->ln(5);


		$this->setFont('arial','BU',11);
		$this->multicell(0,5,"2.	Mesures générales concernant la sécurité :");
		$this->setFont('arial','',10);
		$this->ln(5);
		$this->multicell(0,5,"2.1.	Pour assurer la protection des personnes présentes au sein de l’établissement, des dispositifs de sécurité ont été installés. Seules les personnes habilitées par la Direction de l’établissement peuvent apporter des modifications à ces dispositifs de sécurité.");
		$this->multicell(0,5,"2.2.	Toute personne présente dans l’établissement doit, par son comportement, contribuer à assurer sa propre sécurité et celle d’autrui, notamment en :");
		$this->setLeftMargin(30);
		$this->multicell(0,5,"-	respectant les dispositions réglementaires qui interdisent de fumer dans tous les lieux fermés et couverts qui accueillent du public ;");
		$this->multicell(0,5,"-	circulant dans les couloirs réservés aux piétons ;");
		$this->multicell(0,5,"-	utilisant les sorties destinées à la clientèle ;");
		$this->multicell(0,5,"-	ne pénétrant pas dans les locaux, réserves, etc. destinés aux seuls salariés de la société cliente.");
		$this->setLeftMargin(15);

		$this->multicell(0,5,"2.3.	Toute personne exécutant un travail au sein de l’établissement doit :");
		$this->setLeftMargin(30);
		$this->multicell(0,5,"-	disposer des autorisations nécessaires à l’utilisation des machines indispensables à son travail ;");
		$this->multicell(0,5,"-	utiliser les appareils ou dispositifs de protection individuelle mis à sa disposition par son employeur.");
		$this->setLeftMargin(15);


		$this->multicell(0,5,"2.4.	Par ailleurs  il est rappelé  pour des raisons de sécurité que toute personne doit veiller :");
		$this->setLeftMargin(30);
		$this->multicell(0,5,"-	à ne pas stationner à proximité des portes battantes ;");
		$this->multicell(0,5,"-	à ne pas sortir du champ de vision des conducteurs d'engins ;");
		$this->multicell(0,5,"-	à ne pas passer sous une charge.");
		$this->multicell(0,5,"- à laisser libre d’accès  en permanence les issues de secours");
		$this->multicell(0,5,"- à laisser les moyens de lutte contre l’incendie , libre d’accès en permanence");
		$this->setLeftMargin(15);
		$this->ln(10);

		$this->multicell(0,5,"L’utilisation des moyens de levage et de manutention qui sont dans les réserves est interdite à toute personne étrangère à la société ".$client['societe'].".");

		$this->ln(10);

		$this->multicell(0,5,"Fait en double exemplaire à Tourcoing");
		$this->multicell(0,5,"Le  ".date("d/m/Y"));

	}

	public function attestationSecurite($id) {
		$el = ATF::mission_ligne()->select($id);
		$mission = ATF::mission()->select($el['id_mission']);
		$personnel = ATF::personnel()->select($el['id_personnel']);
		$client = ATF::societe()->select($mission['id_societe']);

		$this->addPage();
		$this->setLeftMargin(15);

		// LOGO !
		$this->image(ATF::$staticserver."images/logos/manala.jpg",80,15,50);

		$this->setFont('arial','BU',14);
		$this->sety(50);
		$this->multicell(0,10,"A L’ATTENTION DU SERVICE SECURITE",0,"C");
		$this->setFont('arial','BU',12);
		$this->multicell(0,5,"Concerne : ".$client['societe'],0,"C");
		$this->ln(5);
		$this->setFont('arial','',10);
		$this->multicell(0,5,"Madame, Monsieur, ");
		$this->ln(5);
		$this->multicell(0,5,"En accord avec les termes de vos conditions d’accès en magasin, nous vous informons que nous, société MANALA, avons été mandatés par la société ".$client['societe']." afin de réaliser une animation le ".ATF::$usr->date_trans($mission['date_debut'],true,true));

		$this->ln(5);
		$this->multicell(0,5,"Nous embauchons ".ATF::personnel()->nom($personnel['id_personnel'])." afin de réaliser ce jour d’animation.");

		$this->ln(5);
		$this->multicell(0,5,"Vous souhaitant bonne réception de la présente,");

		$this->ln(5);
		$this->multicell(0,5,"Nous vous prions d’agréer, Madame, Monsieur, l’expression de nos salutations distinguées.");

		$this->ln(5);
		$this->multicell(0,5,"Fait à Tourcoing, le ".date("d/m/Y"));

		$this->ln(5);
		$this->multicell(0,5,"Pour la société MANALA représentée par ");

		$this->ln(5);
		$this->multicell(0,5,"Melle Emmanuelle Ollivier\n06.23.20.57.10");

		$this->image(__PDF_PATH__.ATF::$codename."/signature.jpg",15,$this->gety(),70);

	}

}
