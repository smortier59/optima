<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cap";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);
/*
ATF::$usr->set('id_user',15);
ATF::$usr->set('id_agence',1);*/

echo "========= DEBUT DE SCRIPT IMPORT CAMPAGNE GIGAFIT =========\n";

// Début de transaction SQL
ATF::db()->begin_transaction();

$data = array(
	array("LE PLESSIS-BELLEVILLE","PROV","SUSPECT","S3","XL","18/08/2015","HALICHE SAID","49789339600018"," 5 Avenue George Bataille, 60330 Le Plessis-Belleville","Said HALICHE","06 98 48 71 20","ks_forme@yahoo.fr"),
	array("MANOSQUE","PROV","SUSPECT","S3","XL","01/09/2016","SAS BC FIT XL","81883765000014","Les Bastides Blanches, 04220 Sainte-Tulle","Thierry MATIAS DOS REIS","06 62 09 19 27","thierry.matias@gmail.com"),
	array("BRETIGNY-SUR-ORGE","IDF","SUSPECT","S3","XL","05/09/2016","SARL FUN SPORT ET FITNESS","43321747800026","32 Avenue de la Commune de Paris, 91220 Brétigny-sur-Orge","Refik Cevik","07 77 96 67 32","refik.cevik@reteks.fr"),
	array("CHAMBLY","PROV","SUSPECT","S3","XL","22/10/2016","SARL EXTREM FORCE","82092639200011","1 rue Henri Becquerel, 60230 Chambly","Francois CROS","06 58 69 59 54","mediaplus57@gmail.com"),
	array("BLAYE","PROV","SUSPECT","S3","DESIGN","04/12/2016","SARL BODY'S CULTE","82223451400013","32 La Gruppe, 33390 Cars","Jérémy Juzwiak","06 58 69 59 54-06 61 53 90 77","bodyscultfrance@gmail.com"),/*
	array("SAINT-MAXIMIN","PROV","SUSPECT","S3","XL","26/01/2017","SARL NJ FORME","38326861200017","403 Rue Louis St Just, 60740 Saint-Maximin","Noureddine Nachite","06 25 90 04 41","nn.nachite@gmail.com"),
	array("CREIL / VERNEUIL","PROV","SUSPECT","S3","XL","26/01/2017","SARL NJ FORME","38326861200017","1024 Avenue du Tremblay, 60100 Creil","Noureddine Nachite","06 25 90 04 41","nn.nachite@gmail.com"),*/
	array("BEAUVAIS","PROV","SUSPECT","S3","XL","30/01/2017","SARL BEAUVAIS FORCE","82264410000015","14 Rue Ferdinand Lesseps, 60000 Beauvais","Francois CROS","06 58 69 59 54","mediaplus57@gmail.com"),
	array("CHAMPIGNY / MARNE","IDF","SUSPECT","S3","XL","13/05/2017","SARL PLANETE FITNESS","82172829200011","101 Avenue Roger Salengro, 94500 Champigny-sur-Marne","Nathalie Vigeant","06 37 11 80 44-06 81 93 26 24","natazur@hotmail.com"),
	array("MEAUX","IDF","SUSPECT","S3","XL","01/06/2017","SARL LES JEUX DE MEAUX","79863238600024","5 Place de Beauval, 77100 Meaux","Stéphane Abehassera","06 60 61 49 47-06 11 11 43 11","labbe555@yahoo.fr"),
	array("MORANGIS","IDF","SUSPECT","S3","XL","02/09/2017","SARL FLHDD","82954559900013","80 avenue Charles de Gaulle, 91420 Morangis","Frederic L'Hostis","06 83 79 72 44-06 89 99 48 52","flhostis@gmail.com"),
	array("DRAVEIL VIGNEUX","IDF","SUSPECT","S3","EXPRESS","12/09/2017","SASU US FIT","83005057100017","2 avenue Henri Barbusse, 91270 Vigneux sur Seine","Didier Cesarus","06 20 30 51 89","opcpacha@gmail.com"),
	array("VILLEMOMBLE","IDF","SUSPECT","S3","XL","01/10/2017","SAS EAFORM","82793645100018","27 allée du plateau 93250 Villemomble ","Alexandre Allix","06 25 70 28 91-06 61 19 89 69","alexallix@live.fr"),
	array("NICE PORT","PROV","SUSPECT","S3","EXPRESS","02/10/2017","SAS T.A (TRAINING ATHLETIC)","83052714900025","1 bis rue de Maeyer, 06300 Nice","Eric Castagnet","06 65 75 91 91  ","castagneteric@orange.fr"),
	array("PIERRELAYE","IDF","SUSPECT","S3","EXPRESS","06/11/2017","SARL NFFIT","83140576600013","7 bis avenue du Général Leclerc, 95480 Pierrelaye","Nélio GOUVEIA","06 20 10 59 49-06 78 61 93 37","nelio7@hotmail.fr"),
	array("SAVIGNY-LE-TEMPLE","IDF","SUSPECT","S3","XL","01/12/2017","SASU LIVIGYM","83144812100010"," 22 Impasse de l'Orée du Bois, 77176 Savigny-le-Temple","Houssine NEGHIZ","07 84 27 56 96","drhouss92@gmail.com"),
	array("SAINT-ORENS","PROV","SUSPECT","S3","XL","22/02/2018","SASU SASQUASHFIT","83102668700010","20  allée des Champs Pinsons, 31650 Saint-Orens-de-Gameville","Rodolphe Vingerder","06 16 41 01 32","rodolphe.vingerder@gmail.com"),
	array("FIGEAC","PROV","SUSPECT","S3","EXPRESS","28/02/2018","SARL BCL SPORT","50109319900021","Corgnet et Fontaubar, 46100 Camboulit","Laurent  Bocquet","06 14 30 35 41","clarabocquet2@gmail.com"),
	array("PONT-STE-MAXENCE","PROV","SUSPECT","S3","EXPRESS","24/03/2018","SAS GIGAFIT PSM","83436345900019","C-Cial Val D'Halatte, 60700 Pont-Ste-Maxence","Michel Nantin","06 36 36 11 86","nantin.michel@hotmail.fr"),
	array("BERGERAC","PROV","SUSPECT","S3","DESIGN","18/04/2018","SAS GIGAFIT BERGERAC","83377310400017","ZAC Saint Lizier, Avenue de la Roque, 24100 Creysse","Guillaume Flores","06 33 85 00 23","guillaume.flores@orange.fr"),
	array("AGEN BON-ENCONTRE","PROV","SUSPECT","S3","DESIGN","14/06/2018","SASU SARLEA FITNESS","83782542100014","ZAC de Redon, Rue André Tissidre, 47240 Bon-Encontre","Jean Michel BUISSON","06 20 60 36 25","jm.buisson.ortec@gmail.com"),
	array("FREJUS","PROV","SUSPECT","S3","DESIGN","25/06/2018","SAS JMLP","83898114000018","19 Via Nova, Pole Excellence Jean Louis, 83600 Fréjus","Jérémie Lorrain","07 81 46 95 52-07 78 66 60 24","jeremie.lorrain@gmail.com"),
	array("CONFLANS-STE-HONORINE","IDF","SUSPECT","S3","DESIGN","04/09/2018","SAS BP FITNESS","84086785700012","47 Rue Maurice Berteaux, 78700 Conflans-Sainte-Honorine","Benjamin FIQUET","07 82 64 36 28","benjamin.fiquet@gmail.com"),
	array("COURCELLES / SEINE","PROV","SUSPECT","S3","EXPRESS","05/09/2018","SARL BODYFIT","84060039900017","19 Bis Route des Andelys, 27940 Courcelles-sur-Seine","Stéphane Vuillaume","07 66 08 13 18","stefcbr@free.fr"),
	array("BELFORT","PROV","SUSPECT","S3","XL","29/09/2018","SARL BODY ENERGIZED","83202302200011","27 Avenue Capitaine de la Laurencie, 90000 Belfort","Fabien ORTEGA","06 22 00 11 29","fabien.ortega1@gmail.com"),
	array("NANCY-LANEUVEVILLE","PROV","SUSPECT","S3","EXPRESS","01/10/2018","SAS GIGAFIT NANCY","83939543100019","71 rue Lucien Galtier, 54410 Laneuveville-devant-Nancy","Alexandre SAMPAIO","06 31 70 90 16","sampaioalexandre@hotmail.fr"),
	array("ANTONY VERRIERES","IDF","SUSPECT","S3","DESIGN","03/12/2018","SAS BLM","84233411200013","19 Rue des Petits Ruisseaux, 91370 Verrières-le-Buisson","Wilfried Liroy","06 20 11 24 36-06 66 09 10 76","liroy.w@gmail.com"),
	array("DUNKERQUE","PROV","SUSPECT","S3","XL","15/12/2018","SASU DKFIT","83948440900017","99 Quai Wilson, 59430 Saint-Pol-sur-Mer","Karim BENSABEUR","06 23 80 65 13-06 72 61 67 35","bensabeur.karim@gmail.com"),
	array("FERRIERES-EN-GATINAIS","PROV","SUSPECT","S3","FLASH","07/01/2019","SAS EDEN FITNESS","84335074500017","2 Rue du Bois Planté, 45210 Ferrières-en-Gâtinais","Pierre NAUDASCHER","06 42 28 42 86","gestion45210@yahoo.fr"),
	array("TERRASSON-LAVILLEDIEU","PROV","SUSPECT","S3","EXPRESS","09/01/2019","SAS LOGOFIT 24","84007229200013","ZI du Coutal, Rue Pierre Proudhon, 24120 Terrasson-Lavilledieu","Georges GODINHO","06 11 49 32 32-06 14 54 83 67","logofit24@orange.fr"),
	array("SOUILLAC","PROV","SUSPECT","S3","EXPRESS","14/01/2019","SASU SPORT PLAISIR","84373895600013","44 Avenue du Général de Gaulle, 46200 Souillac","Pierre Many","06 27 76 35 85","pfmany19@gmail.com"),
	array("AVRAINVILLE","IDF","SUSPECT","S3","DESIGN","16/01/2019","SASU WIKA FIT","84276126400014","10 Rue Louise de Vilmorin, 91630 Avrainville","","06 63 06 00 06-06 62 18 12 18","wikafit@outlook.com"),
	array("PERIGUEUX","PROV","SUSPECT","S3","XL","02/03/2019","SARL JM PERIGUEUX","53048667900018","1 Rue des Commerces, 24430 Marsac-sur-L'Isle","Jacques Michaud","06 24 03 90 43-06 67 88 05 00","gigagym.marsac@gmail.com"),
	array("SAINT-GRATIEN","IDF","SUSPECT","S3","DESIGN","04/03/2019","SARL GIGAFIT SAINT GRATIEN","84403242500010","4 Rue du Général Leclerc, 95210 Saint-Gratien","Arnaud Pugin-Bron","06 87 05 87 69","arnaud.puginbron@gmail.com"),
	array("GIEN","PROV","SUSPECT","S3","DESIGN","31/03/2019","SARL BEAUTE FITNESS GIEN","84523353500010","ZA de la Bosserie Nord, 45500 Gien","Marc PERRET","07 67 41 91 82-06 58 74 06 42","commun.gf@gmail.com"),
	array("CAVIGNAC","PROV.","SUSPECT","S3","EXPRESS","01/04/2019","SARL CRAOU","84482158700012","4 Rillac, 33620 Cavignac","Fabien FERRE","06 58 69 59 54-06 61 53 90 77","bodyscultfrance@gmail.com"),
	array("CALAIS-COQUELLES","PROV","SUSPECT","S3","XL ","22/05/2019","SARL SAFARI PARC","79071570000031","Boulevard du Kent, Place de Cantorbery , 62231 Coquelles","Martine BOSSAERT","06 80 85 96 44","the_john_bull_pub@hotmail.fr"),
	array("TOULON BON-RENCONTRE","PROV","SUSPECT","S3","FLASH","21/06/2019","SAS BENSSI (GIGAFIT FLASH)","84860750300010","85 Avenue Aristide Briand, 83200 Toulon","Abdelali BENAISSI","06 98 42 43 10","benaissiabdel163@gmail.com"),
	array("ITTEVILLE","IDF","SUSPECT","S3","DESIGN","09/09/2019","SAS FITDEV","84894842800018","22 Route de la Ferté Alais, 91760 Itteville","Julien Colas","06 10 09 47 46-06 28 42 68 89","juliencolas@hotmail.com"),
	array("NOGENT-SUR-OISE","PROV","SUSPECT","S3","EXPRESS","09/09/2019","SAS GIGAFIT NSO","85099318900016","86 Rue Jean Monnet, 60180 Nogent-sur-Oise","Michel NANTIN","06 36 36 11 86","nantin.michel@hotmail.fr"),
	array("ST MALO","PROV.","SUSPECT","S3","XL","15/10/2019","SARL BREIZH FITNESS","85254929400019","Zac de la Madeleine, Impasse de la Peupleraie, 35400 Saint-Malo","Manuel Seror","06 11 60 14 04-06 43 48 31 08","manuel.seror@mma.fr"),
	array("BRUZ","PROV","SUSPECT","S3","XL","17/10/2019","SARL AMJ FITNESS","85160143500015","Place de Bretagne, 35170 Bruz","JEAN-MARIE JAOUANNET","06 20 34 42 96","jm.jaouannet@gmail.com"),
	array("COMPIEGNE","PROV","SUSPECT","S3","DESIGN","07/11/2019","SASU SJM FITNESS","85181095200016","1 Rue Ferdinand de Lesseps, 60200 Compiègne","MICHEL MACHECLER","07 82 38 94 62","michelmachecler@hotmail.com"),
	array("PONTARLIER","PROV","SUSPECT","S3","Design","09/11/2019","SARL J FIT TEAM","85168518000016","18 Rue des Remparts, 25300 Pontarlier","Julien Coquet","06 80 26 02 22","julien-coquet@orange.fr"),
	array("SOLLIES-LA FARLEDE","PROV","SUSPECT","S3","FLASH","07/12/2019","SAS JOYFIT","84923044600015","RN97, Quartier de la Roumiouve, 83210 Solliès-Ville","Valentin SAVA","06 23 36 28 93‬","bizza84_bz@hotmail.com"),
	array("SOISY-SUR-SEINE","IDF","SUSPECT","S3","FLASH","06/01/2020","SARL NICO-COACHING-SOISY","85300932200019","1 Rue de la Forêt de Sénart, 91450 Soisy-sur-Seine","Nicolas FAUCHEREAU","06 09 67 22 61","nico-coaching-soisy@outlook.fr "),
	array("ARGELES-SUR-MER","PROV","SUSPECT","S3","FLASH","13/01/2020","SASU G.I.F","85273052200012","25 Avenue de Hurts, 66700 Argelès-sur-Mer","GIMENEZ adrien","06 31 32 52 93","gimenez.ag66@gmail.com"),
	array("VILLEPARISIS","IDF","SUSPECT","S3","DESIGN","24/01/2020","EURL EA PARISIS","84943863500013","17 Avenue Roger Salengros, 77270 Villeparisis","Alexandre Allix","06 25 70 28 91-06 61 19 89 69","alexallix@live.fr"),
	array("AUBERGENVILLE","IDF","SUSPECT","S3","DESIGN","","SAS GLOBAL FITNESS DEVELOPPEMENT","84936941800017","Family Village, Route de Quarante Sous, 78410 Aubergenville","Elvis ZERAH","06 62 05 82 18","elviszerah@me.com"),
	array("DIJON","PROV","SUSPECT","S3","XL","","SAS GFXL DIJON","87833467100012","12 Rue de Bourgogne, 21800 Neuilly-Crimolois","Alan VILLEGAS","06 78 35 01 82","villegas.alan@live.fr"),
	/*array("BRIVE LA GAILLARDE","PROV","SUSPECT","S3","DESIGN","","SARL PARCOURS BRIVE","81359191400011","11 Impasse de la Sarretie, 19100 Brive-la-Gaillarde","Geordie PARSIS","06 48 56 81 08","geordie.parsis@gmail.com"),*/
	array("VICHY","PROV","SUSPECT","S3","XL","","SAS FITNESS & SPORTS","85062195400013","3ter Avenue du Général de Gaulle, 03700 Bellerive-sur-Allier","William Martin","07 80 32 04 69-06 86 12 46 69","martinwilliam885@gmail.com"),
	array("VILLABE","IDF","SUSPECT","S3","XL","","SARL GIGA VILLABE","87786157500018","3 Rue de la Plaine, ZAC des Brateaux, 91100 Villabé","Dylan BRUXELLES","06 16 81 16 83-06 62 35 47 72","dylan.bruxelles@hotmail.com"),
	array("MILLY-LA-FORÊT","IDF","SUSPECT","S3","FLASH","","SAS FMH 1","88184852700019 ","ZA du Chenet, Rue du Chenet, 91490 Milly-la-Forêt","Djouzarhoussen AMIDJEE","06 21 75 85 45","djouzarhoussen@gmail.com")
);
$societes = $campagnes = $secteurs = array();


$err = 0;


$id_campagne = ATF::campagne()->insert(array("campagne"=> "GIGAFIT"));

foreach ($data as $key => $v) {

	try{
		if(!$secteurs[$v[1]]){
				ATF::secteur_geographique()->q->reset()->where("secteur_geographique", $v[1]);

				if($secteur_geographique = ATF::secteur_geographique()->select_row()){
					$secteurs[$v[1]] = $secteur_geographique["id_secteur_geographique"];
				}else{
					$secteurs[$v[1]] = ATF::secteur_geographique()->insert(array("secteur_geographique"=> $v[1]));
				}
			}

			$soc = ATF::societe()->getInfosFromCREDITSAFE(array("siret"=> $v[7]));

			log::logger($soc , "mfleurquin");


			$gerants = $soc["gerant"];

			unset($soc["gerant"],
					  $soc["nb_employe"],
					  $soc["ville_rcs"],
					  $soc["resultat_exploitation"],
	    			$soc["capital_social"],
	    			$soc["capitaux_propres"],
	    			$soc["dettes_financieres"]
					);

			if($soc["cs_avis_credit"] == "Limite de crédit non applicable") unset($soc["cs_avis_credit"]);

			$societe = $soc;

			$societe["id_secteur_geographique"] = $secteurs[$v[1]];
			$societe["siret"] = $v[7];
			$societe["nom_commercial"] = $soc["societe"];
			$societe["societe"] = $v[6];
			$societe["relation"] = "suspect";
			$societe["code_groupe"] = $v[3];
			$societe["code_fournisseur"] = $v[4];
			$societe["code_regroupement"] = "GIGAFIT";
			$societe["id_campagne"] = $id_campagne;


			log::logger($societe , "mfleurquin");


			$id_soc = ATF::societe()->insert(array("societe"=>$societe));

			foreach ($gerants as $kg => $vg) {
				ATF::contact()->insert(array(
					"nom"=> $vg["nom"],
					"prenom"=> $vg["prenom"],
					"fonction"=> $vg["fonction"],
					"id_societe" => $id_soc
				));
			}




			if($v[9]){

				$nomprenom = explode(" ", $v[9]);
				$tels = explode("-", $v[10]);

				log::logger($nomprenom , "mfleurquin");


				$contact = array(
					"nom"=> $nomprenom[0],
					"prenom"=> $nomprenom[1],
					"tel" => $tel[0],
					"email" => $v[11],
					"id_societe" => $id_soc
				);
				if($tels[1]) $contact["gsm"] = $tels[1];

				log::logger($contact , "mfleurquin");

				ATF::contact()->insert($contact);

			}
	}catch(errorATF $e){
		echo $e->getMessage()."\n\n";
	}

}






echo "Erreur --> ".$err;


// Rollback la transaction
ATF::db()->rollback_transaction();
// Valide la trnasaction
//ATF::db()->commit_transaction();
echo "========= FIN DE SCRIPT =========\n";