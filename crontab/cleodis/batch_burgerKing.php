<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";



$sql = "CREATE TABLE IF NOT EXISTS `bk` (
  `COL 1` varchar(21) DEFAULT NULL,
  `COL 2` varchar(5) DEFAULT NULL,
  `COL 3` varchar(84) DEFAULT NULL,
  `COL 4` varchar(189) DEFAULT NULL,
  `COL 5` varchar(12) DEFAULT NULL,
  `COL 6` varchar(10) DEFAULT NULL,
  `COL 7` varchar(11) DEFAULT NULL,
  `COL 8` varchar(10) DEFAULT NULL,
  `COL 9` varchar(11) DEFAULT NULL,
  `COL 10` varchar(14) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


$sql2 = "INSERT INTO `bk` (`COL 1`, `COL 2`, `COL 3`, `COL 4`, `COL 5`, `COL 6`, `COL 7`, `COL 8`, `COL 9`, `COL 10`) VALUES
('REFERENCE', 'ETAT', 'DESIGNATION', 'COMMENTAIRE', 'PRIX D''ACHAT', 'TYPE', 'FOURNISSEUR', 'FABRIQUANT', 'CATEGORIE', 'SOUS CATEGORIE'),
('BK- RES-MX64', 'Actif', 'RESEAU : Firewall MX64', '', '6158', 'fixe', '6728', '124', 'RESEAU', 'FIREWALL'),
('BK- RES-MS220-48LP', 'Actif', 'RESEAU : Switch 48 ports GE MS220-48LP', '', '0', 'fixe', '6728', '124', 'RESEAU', 'SWITCH'),
('BK- RES-MS220-48', 'Actif', 'RESEAU : Switch 48 ports GE MS220-48', '', '0', 'fixe', '6728', '124', 'RESEAU', 'SWITCH'),
('BK- RES-MR18', 'Actif', 'RESEAU : 2 bornes Wifi MR18 avec kit de fixation', '', '0', 'fixe', '6728', '124', 'RESEAU', 'BORNE WIFI'),
('BK- RES-JARR', 'Actif', 'RESEAU : Lot de 48 jarretières cat6 avec codes couleurs (1m et 2 m)', '', '0', 'sans_objet', '6728', '281', 'RESEAU', 'ACCESSOIRE'),
('BK- RES-PASSEC', 'Actif', 'RESEAU : 2 passe-câbles vertical', '', '0', 'sans_objet', '6728', '281', 'RESEAU', 'ACCESSOIRE'),
('BK- RES-LOGMX64', 'Actif', 'RESEAU : Licence MX64 advancedsecurity 3 ans', '', '2086', 'sans_objet', '6728', '124', 'RESEAU', 'LOGICIEL'),
('BK- RES-LOGMS220-48LP', 'Actif', 'RESEAU : Licence MS220-48LP 3 ans', '', '0', 'sans_objet', '6728', '124', 'RESEAU', 'LOGICIEL'),
('BK- RES-LOGMS228-48', 'Actif', 'RESEAU : Licence MS220-48 3 ans', '', '0', 'sans_objet', '6728', '124', 'RESEAU', 'LOGICIEL'),
('BK- RES-LOGWIFI', 'Actif', 'RESEAU : 2 licences Wifi enterprise 3 ans', '', '0', 'sans_objet', '6728', '124', 'RESEAU', 'LOGICIEL'),
('BK- RES-ZENCLOUD', 'Actif', 'RESEAU : 2 licences ZenCloud Premium portail interactif 3 ans', '', '0', 'sans_objet', '6728', 'ZENCONECT', 'RESEAU', 'LOGICIEL'),
('BK- RES-PREST', 'Actif', 'RESEAU : Préparation, installation et paramétrage', '', '0', 'sans_objet', '6728', '281', 'RESEAU', 'PRESTATION'),
('BK- RES-INFOG', 'Actif', 'RESEAU : Infogérance et support standard', '', '245', 'sans_objet', '6728', '281', 'RESEAU', 'PRESTATION'),
('BK- RES-INFOG+', 'Actif', 'RESEAU : Infogérance et support étendu', '', '995', 'sans_objet', '6728', '281', 'RESEAU', 'PRESTATION'),
('BK-POS-AIO', 'Actif', 'POS : PC All-in-One SL20 écran tactile + caisse serveur ', '', '1475', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'CAISSE'),
('BK-POS-IMP', 'Actif', 'POS : Imprimante pour reçu et factures TM', '', '500', 'fixe', 'MYTECH', '76', 'CAISSE', 'IMPRIMANTE'),
('BK-POS-CAISS', 'Actif', 'POS : Tiroir caisse avec clés de sécurité', '', '150', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'TIROIR CAISSE'),
('BK-POS-CAISS2', 'Actif', 'POS : Tiroir interne (fond de caisse) supplémentaire', '', '30', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'TIROIR CAISSE'),
('BK-POS-KSC', 'Actif', 'POS : Kitchen Station controller', '', '1000', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'ACCESSOIRE'),
('BK-POS-ECR22', 'Actif', 'POS : Moniteur LCD 22 pouces pour Kitchen Station', '', '200', 'fixe', 'MYTECH', '163', 'CAISSE', 'ECRAN'),
('BK-POS-CLAV', 'Actif', 'POS : Clavier Bump Bar', '', '250', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'ACCESSOIRE'),
('BK-POS-SOFTSL19-1', 'Actif', 'POS : Licence Software SL 19 - EMS 5/Pos sl', '', '2850', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-POS-SOFTSL19-2', 'Actif', 'POS : Licence Software SL 19 - EMS +1/Pos sl, pour plus 5', '', '200', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-POS-MODCARTE', 'Actif', 'POS : Module Carte serveur dispositif de sauvegarde', '', '500', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-POS-MODSL19', 'Actif', 'POS : Module SL 19 Kit de Installation', '', '500', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-POS-PREST', 'Actif', 'POS : Livraison, installation et configuration', '', '0', 'sans_objet', 'MYTECH', '281', 'CAISSE', 'PRESTATION'),
('BK-POS-SUPP5', 'Actif', 'POS : Support pour 5/pos', '', '2950', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-POS-SUPP1', 'Actif', 'POS : Support pour +1/pos SL, plus 5 - chaque', '', '200', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-POS-LOGSEMS', 'Actif', 'POS : Paquet de licences Web software SEMS', '', '950', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-DMB-ECR46', 'Actif', 'DMB : Moniteur 46 pouces', 'avec support mural - garantie sur site', '953', 'fixe', 'MYTECH', '102', 'CAISSE', 'ECRAN'),
('BK-DMB-DMBC', 'Actif', 'DMB : Digital Menu Board Controller', '', '534', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'ACCESSOIRE'),
('BK-DMB-LOGDMB5', 'Actif', 'DMB : Licence software Digital Menu Board 5/écrans pour point de vente', '', '513', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-DMB-LOGDMB1', 'Actif', 'DMB : Licence software Digital Menu Board +1/ écran- plus 5 moniteur', '', '84', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-DMB-PREST', 'Actif', 'DMB : Livraison et installation', '', '0', 'sans_objet', 'MYTECH', '281', 'CAISSE', 'PRESTATION'),
('BK-DMB-SUPP5', 'Actif', 'DMB : Support pour 5/Moniteur DMB pour point de vente - Annuel', '', '700', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-DMB-SUPP1', 'Actif', 'DMB : Support pour +1/Moniteur DMB pour plus 5 - Annuel', '', '95', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-ORS-ECR32', 'Actif', 'ORS : Moniteur 32 pouces fullColor Professionnel haute qualité', 'avec support mural - garantie sur site', '672', 'fixe', 'MYTECH', '102', 'CAISSE', 'ECRAN'),
('BK-ORS-LOGORSC', 'Actif', 'ORS : Licence Order Ready System Controller connexion ethernet', '', '534', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-ORS-KSC', 'Actif', 'ORS : Kitchen Station controller', '', '1050', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'ACCESSOIRE'),
('BK-ORS-ECR22', 'Actif', 'ORS : Moniteur LCD 22 pouces pour Kitchen Station avec support mural', '', '150', 'fixe', 'MYTECH', '163', 'CAISSE', 'ECRAN'),
('BK-ORS-IMP', 'Actif', 'ORS : Epson Imprimante pour les ordres de la zone kiosk ', '', '500', 'fixe', 'MYTECH', '76', 'CAISSE', 'IMPRIMANTE'),
('BK-ORS-LOGORS', 'Actif', 'ORS : Licence software Order Ready Screen POS', '', '100', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-ORS-PREST', 'Actif', 'ORS : Livraison et installation', '', '0', 'sans_objet', 'MYTECH', '281', 'CAISSE', 'PRESTATION'),
('BK-ORS-SUPP', 'Actif', 'ORS : Support pour 1/ORS pour Point de vente - Annuel', '', '110', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-OCU-ECR15', 'Actif', 'OCU + SOS : Moniteur PC 15 pouces fullColor professionnel pour espace extérieur', '', '3750', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'ECRAN'),
('BK-OCU-LOG', 'Actif', 'OCU + SOS : Speed of Service hardware et software', '', '850', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'LOGICIEL'),
('BK-OCU-PREST', 'Actif', 'OCU + SOS : Livraison et installation', '', '0', 'sans_objet', 'MYTECH', '281', 'CAISSE', 'PRESTATION'),
('BK-OCU-SUPP1OCU', 'Actif', 'OCU + SOS : Support Sicom-pour 1/OCU pour Point of Sale - Annuel', '', '300', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-OCU-SUPP1SOS', 'Actif', 'OCU + SOS : Support Sicom-pour 1/SOS pour Point de vente - Annuel', '', '85', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-ICC-MOD5', 'Actif', 'ICC + EO INTEGRATION : Module Sicom/Ingenico ICC- pour 5/pos paiements', '', '1100', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'PRESTATION'),
('BK-ICC-MOD1', 'Actif', 'ICC + EO INTEGRATION : Module Sicom/Ingenico ICC- for +1/pos paiement plus 5', '', '150', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'PRESTATION'),
('BK-ICC-MODQSL', 'Actif', 'ICC + EO INTEGRATION : Module Sicom/QSL interface pour ordre electronique', '', '175', 'sans_objet', 'MYTECH', 'SICOM', 'CAISSE', 'PRESTATION'),
('BK-ICC-PREST', 'Actif', 'ICC + EO INTEGRATION : Installation system ICC 1/icc inclus configuration à distance', '', '150', 'sans_objet', 'MYTECH', '281', 'CAISSE', 'PRESTATION'),
('BK-ICC-SUPP1ICC', 'Actif', 'ICC + EO INTEGRATION : Support Sicom-pour 1/ICC pour Point de vente - Annuel', '', '350', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-ICC-SUPP1EO', 'Actif', 'ICC + EO INTEGRATION : Support Sicom-pour 1/EO pour Point de vente - Annuel', '', '225', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-GURU-PC21', 'Actif', 'PC GURU : PC avec écran 21 pouces', 'avec support mural + clavier - Switch + UPS - Switch/UPS/Bracket shipping', '1101', 'fixe', 'MYTECH', 'SICOM', 'CAISSE', 'PC'),
('BK-GURU-PREST', 'Actif', 'PC GURU : Configuration', '', '135', 'sans_objet', 'MYTECH', '281', 'CAISSE', 'PRESTATION'),
('BK-GURU-SUPP', 'Actif', 'PC GURU : Support', '', '216', 'sans_objet', 'MYTECH', 'MYTECH', 'CAISSE', 'PRESTATION'),
('BK-DMB-ECR48', 'Actif', 'DMB : Ecran plat LCD 48 pouces DM48E à rétroéclairage LED - full HD', '', '745', 'fixe', '6737', '44', 'ECRAN', 'ECRAN'),
('BK-DMB-FIX', 'Actif', 'DMB : Support mural', '', '50', 'sans_objet', '6737', '44', 'ECRAN', 'ECRAN'),
('BK-DMB-PREST', 'Actif', 'DMB : Livraison et installation', '', '0', 'sans_objet', '6737', '281', 'ECRAN', 'PRESTATION'),
('BK-KIOS-KIOSQ', 'Actif', 'KIOSQUES : Kiosque', '', '5199', 'fixe', 'TILLSTER', 'TILLSTER', 'KIOSQUE', 'CAISSE'),
('BK-KIOS-FIX1', 'Actif', 'KIOSQUES : Fixation 1 - Poteau acier inox', '', '1145', 'sans_objet', 'TILLSTER', 'TILLSTER', 'KIOSQUE', 'ACCESSOIRE'),
('BK-KIOS-FIX2', 'Actif', 'KIOSQUES : Fixation 2 - Stand personnalisé BK', '', '860', 'sans_objet', 'TILLSTER', 'TILLSTER', 'KIOSQUE', 'ACCESSOIRE'),
('BK-KIOS-FIX3', 'Actif', 'KIOSQUES : Fixation 3 - Fixation murale', '', '145', 'sans_objet', 'TILLSTER', 'TILLSTER', 'KIOSQUE', 'ACCESSOIRE'),
('BK-KIOS-TPE', 'Actif', 'KIOSQUES : Terminal de paiement', '', '1050', 'fixe', 'TILLSTER', '103', 'KIOSQUE', 'TPE'),
('BK-KIOS-PREST', 'Actif', 'KIOSQUES : Livraison et installation', '', '1800', 'sans_objet', 'TILLSTER', '281', 'KIOSQUE', 'PRESTATION'),
('BK-KIOS-GAR', 'Actif', 'KIOSQUES : Garantie matériel 3 ans', '', '0', 'sans_objet', 'TILLSTER', 'TILLSTER', 'KIOSQUE', 'PRESTATION'),
('BK-KIOS-SUPP', 'Actif', 'KIOSQUES : Support éditeur 1 an', '', '985', 'sans_objet', 'TILLSTER', 'TILLSTER', 'KIOSQUE', 'PRESTATION'),
('BK-KIOS-TAXE', 'Actif', 'KIOSQUES : Taxe', '', '0', 'sans_objet', 'TILLSTER', '281', 'KIOSQUE', 'PRESTATION'),
('BK-DRIV-HME6000', 'Actif', 'COM DRIVE : Centrale de communication HME 6000 ION', 'livrée avec 1 carte de détection, 2 transformateurs d''alimentation 220/12V,\n1 chargeur rapide de batterie, 5 batteries grande capacité, 5 casques sans fil, 1 hp + 1 micro à la borne, câbles', '7829,52', 'fixe', '6730', 'HME', 'COM DRIVE', 'RESEAU'),
('BK-DRIV-OPT1', 'Actif', 'COM DRIVE : Option 1 - 1 haut-parleur de contrôle avec étrier', '', '215,73', 'fixe', '6730', 'HME', 'COM DRIVE', 'ACCESSOIRE'),
('BK-DRIV-OPT2', 'Actif', 'COM DRIVE : Option 2 - Extension d''antenne', '', '220', 'sans_objet', '6730', 'HME', 'COM DRIVE', 'ACCESSOIRE'),
('BK-BUR-ELITE800', 'Actif', 'BUREAUTIQUE : Elite One 800', 'Garantie 3 ans', '1099', 'fixe', 'CALIPAGE', '63', 'BUREAUTIQUE', 'PC'),
('BK-BUR-Z30', 'Actif', 'BUREAUTIQUE : Z30-A-18H', '13.3 pouces - Core I3-4030U - 8 Gb - 128 SSD - Windows 7 Pro / 8 Pro', '935', 'portable', '6733', '45', 'BUREAUTIQUE', 'PC'),
('BK-BUR-R50', 'Actif', 'BUREAUTIQUE : R50-B-109', '15.6 pouces - Core I3 - 4 Gb - 500 Gb', '449', 'portable', '6733', '45', 'BUREAUTIQUE', 'PC'),
('BK-BUR-GAR', 'Actif', 'BUREAUTIQUE : Extension de garantie 3 ans', '', '84,58', 'sans_objet', '6733', '45', 'BUREAUTIQUE', 'PRESTATION'),
('BK-BUR-M476DW', 'Actif', 'BUREAUTIQUE : Multifonction Color LaserJet Pro MFP M476dw', '', '469', 'fixe', '6733', '63', 'BUREAUTIQUE', 'IMPRIMANTE'),
('BK-DLC-TH2', 'Actif', 'IMP DLC : Imprimante étiquettes TH2', 'livrée avec 3 protections clavier, câble réseau, carte SD 2Go, kit de nettoyage, étiquettes', '889', 'fixe', 'SATO', '299', 'IMP DLC', 'IMPRIMANTE'),
('BK-TR-PACKSCAN', 'Actif', 'TR : Pack scanner titre (CRT, CAP, ANCV) + logiciel', '', '2535', 'fixe', '6732', '76', 'TR', 'DIVERS'),
('BK-TR-MASSICOT', 'Actif', 'TR : Massicot invalidateur de titres CRT', '', '150', 'sans_objet', '6732', '76', 'TR', 'ACCESSOIRE'),
('BK-TR-PREST', 'Actif', 'TR : Livraison, installation et paramétrage à distance', '', '1115', 'sans_objet', '6732', '281', 'TR', 'PRESTATION'),
('BK-TR-MAINT', 'Actif', 'TR : Maintenance 3 ans', '', '750', 'sans_objet', '6732', 'GLOBAL POS', 'TR', 'PRESTATION');";

ATF::db()->sql2array($sql);
ATF::db()->sql2array($sql2);


$i = "SELECT * FROM bk;";
$liste = ATF::db()->sql2array($i);

	
foreach ($liste as $key => $value) {	
	$produit = array();
	$id_fournisseur = $id_fournisseur = "";
	if($key > 0){
		if(intval($value["COL 7"])){
			$id_fournisseur = $value["COL 7"];
		}else{
			if($value["COL 7"] == 'MYTECH'){ $id_fournisseur = 6729; }
			elseif($value["COL 7"] == 'TILLSTER'){ $id_fournisseur = 6731; }
			elseif($value["COL 7"] == 'CALIPAGE'){ $id_fournisseur = 6733; }
			elseif($value["COL 7"] == 'SATO'){ $id_fournisseur = 6733; }					
		}		
		
		if(intval($value["COL 8"])){
			$id_fabricant = $value["COL 8"];
		}else{
			ATF::fabriquant()->q->reset()->where("fabriquant",$value["COL 8"]);
			if($fabriquant = ATF::fabriquant()->select_row()){
				$id_fabricant = $fabriquant["id_fabriquant"];
			}else{
				$id_fabricant = ATF::fabriquant()->i(array("fabriquant"=>$value["COL 8"]));
			}			
		}


		ATF::sous_categorie()->q->reset()->from("sous_categorie","id_categorie","categorie","id_categorie")
										 ->where("sous_categorie",$value["COL 10"], "AND")
										 ->where("categorie",$value["COL 9"]);
		$id_sous_categorie = ATF::sous_categorie()->select_row();

		if(!$id_sous_categorie){
			ATF::categorie()->q->reset()->where("categorie", $value["COL 9"]);
			$id_categorie = ATF::categorie()->select_row();
			$id_categorie = $id_categorie["id_categorie"];
			$id_sous_categorie = ATF::sous_categorie()->i(array("id_categorie"=>$id_categorie, "sous_categorie"=> $value["COL 10"]));			
		}else{
			$id_sous_categorie = $id_sous_categorie["id_sous_categorie"];
		}

		$ref = $value["COL 1"];

		if($ref == "BK-DMB-PREST"){
			$ref = "BK-DMB-PREST-".$value["COL 9"];		}


		$produit["ref"] = $ref;
		$produit["produit"] = $value["COL 3"];
		$produit["prix_achat"] = $value["COL 5"];
		$produit["id_fabriquant"] = $id_fabricant;
		$produit["id_sous_categorie"] = $id_sous_categorie;
		$produit["type"] = $value["COL 6"];
		$produit["id_fournisseur"] = $id_fournisseur;
		$produit["etat"] = strtolower($value["COL 2"]);
		$produit["commentaire"] = $value["COL 4"];

		log::logger($produit , "mfleurquin");

		ATF::produit()->i($produit);


	}
}


$sup = "DROP TABLE bk";
ATF::db()->sql2array($sup);


?>