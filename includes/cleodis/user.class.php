<?php
require_once dirname(__FILE__)."/../../libs/ATF/includes/user.class.php";

class user_cleodis extends user {

	public function __construct() {
		parent::__construct();
		$this->fieldstructure();

		$this->addPrivilege("export_user_infos");
		$this->addPrivilege("export_commercial_partenaire");
		$this->addPrivilege("export_prospection");

	}

	public function getFeuilles($infos){
		if($infos['id_user'] /*|| $infos['id_contact']*/){
			return array("Export Optima",
					  "RDV",
					  "Appels",
					  "Devis gagnés",
					  "Devis en attente",
					  "Devis perdus",
					  "MEL"
					 );
		}else{
			return array(
					  "Devis gagnés",
					  "Devis en attente",
					  "Devis perdus",
					  "MEL"
					 );
		}

	}


	public function export_prospection($infos){
		if($infos['id_contact']){
			$infos['id_contact'] = ATF::contact()->decryptId($infos['id_contact']);
			$this->export_user_infos($infos);
		}
	}


	public function export_commercial_partenaire($infos){
		if($infos['id_partenaire']){
			$infos['id_partenaire'] = ATF::societe()->decryptId($infos['id_partenaire']);
			$this->export_user_infos($infos);
		}

	}

	public function export_user_infos($infos){

        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		if($infos['id_user']){
			$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		}elseif($infos['id_contact']){
			$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.$infos['id_contact']);
		}else{
			$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__."_all");
		}

		$workbook = new PHPExcel;

		$feuilles = $this->getFeuilles($infos);

		$premfeuille = true;

		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);


		foreach ($feuilles as $key => $value) {
			if ($premfeuille){
				$workbook->setActiveSheetIndex($key);
			    $sheet = $workbook->getActiveSheet();
			    $sheet->setTitle($value);
			    $this->ajoutTitreExport($sheet);
			    $premfeuille = false;
			}else{
				$sheet = $workbook->createSheet($key);
				$workbook->setActiveSheetIndex($key);
				$sheet = $workbook->getActiveSheet();
				$sheet ->setTitle($value);
			}
			$this->ajoutTitreExport($sheet, $value);
			$this->ajoutDataExport($sheet, $value, $infos);
		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export suivis commerce.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();

	}





	/** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function ajoutTitreExport(&$sheet, $titre){
    	switch ($titre) {
    		case "Devis gagnés" :
			case "Devis en attente" :
			case "Devis perdus":
    			$row_data = array(
		        	 "A"=>array('ENTITE',30)
		        	,"B"=>array("RESPONSABLE", 30)
					,"C"=>array("REDACTEUR",30)
					,"D"=>array('DEVIS',30)
					,"E"=>array('DATE DEBUT',30)
					,"F"=>array('CODE CLIENT',30)
					,"G"=>array('ETAT',30)
					,"H"=>array('PREMIERE DATE D\'ACCORD',30)
					,"I"=>array('DERNIERE DATE D\'ACCORD',30)
					,"J"=>array('TYPE DE CONTRAT',30)
					,"K"=>array('DATE INSTALLATION PREVUE',30)
					,"L"=>array('LOYER 1',30)
					,"M"=>array('DUREE 1',30)
					,"N"=>array('FREQUENCE 1',30)
					,"O"=>array('LOYER 2',30)
					,"P"=>array('DUREE 2',30)
					,"Q"=>array('FREQUENCE 2',30)
					,"R"=>array('LOYER 3',30)
					,"S"=>array('DUREE 3',30)
					,"T"=>array('FREQUENCE 3',30)
					,"U"=>array('LOYER 4',30)
					,"V"=>array('DUREE 4',30)
					,"W"=>array('FREQUENCE 4',30)
					,"X"=>array('ACHAT',30)
					,"Y"=>array('LOYER x DUREE',30)
					,"Z"=>array('PROSPECTION',30)
					,"AA"=>array('PARTENAIRE (AFFAIRE)',30)
					,"AB"=>array('SITE ASSOCIE',30)
					,"AC"=>array('PROVENANCE',30)
				);

    		break;

    		case "MEL" :
    			$row_data = array(
		        	 "A"=>array('ENTITE',30)
		        	,"B"=>array("RESPONSABLE", 30)
					,"C"=>array("CODE CLIENT",15)
					,"D"=>array('CONTRAT',15)
					,"E"=>array('INSTALLATION REELLE',15)
					,"F"=>array('PRIX ACHAT',15)
					,"G"=>array('PRIX HT',15)
					,"H"=>array('DATE CREATION CONTRAT',15)
					,"I"=>array('DEBUT',15)
					,"J"=>array('LOYER',15)
					,"K"=>array('DUREE',15)
					,"L"=>array('ASSURANCE',15)
					,"M"=>array('FRAIS DE GESTION',15)
					,"N"=>array('FREQUENCE',15)
					,"O"=>array('TOTAL',15)
					,"P"=>array('REFINANCEUR',30)
					,"Q"=>array('PROSPECTION',30)
					,"R"=>array('PARTENAIRE (AFFAIRE)',30)
					,"S"=>array('SITE ASSOCIE',30)
					,"T"=>array('PROVENANCE',30));

    		break;


    		case "Export Optima" :
			case "RDV" :
			case "Appels":
    				$row_data = array(
    					 "A"=>array('DATE',30)
			        	,"B"=>array("TEXTE", 30)
						,"C"=>array("ENTITE",30)
						,"D"=>array('REDACTEUR',30)
						,"E"=>array('PROSPECTION',30)
						,"F"=>array('TYPE',30)
						,"G"=>array('TYPE',30));
    		break;
    	}
    	$i=0;
    	foreach($row_data as $col=>$titre){
			$sheet->setCellValueByColumnAndRow($i , 1, $titre[0]);
			$sheet->getColumnDimension($col)->setWidth($titre[1]);
			$i++;
        }
    }

    /** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function ajoutDataExport(&$sheet, $titre, $infos){

    	$row_data = array();
    	$loyers = array();

       	switch ($titre) {
    		case "Devis gagnés" :
			case "Devis en attente" :
			case "Devis perdus":
				ATF::devis()->q->reset()->from("devis","id_societe","societe","id_societe");

				if($infos['id_user']){
					ATF::devis()->q->where("societe.id_owner",ATF::user()->decryptId($infos["id_user"]),"OR","user_filtre","=")
								   ->where("devis.id_user",ATF::user()->decryptId($infos["id_user"]),"OR","user_filtre","=")->addOrder("devis.date");
				}elseif($infos['id_contact']){

					ATF::devis()->q->where("societe","id_prospection",$infos['id_contact'])
								   ->addOrder("devis.date");


				}else{
					if($infos['id_partenaire']){
						ATF::devis()->q->from("devis","id_affaire","affaire","id_affaire")
									->where("affaire.id_partenaire",$infos['id_partenaire'])->addOrder("devis.date");
					}else{
						ATF::devis()->q->whereIsNotNull("societe.id_owner","OR","user_filtre")
									   ->whereIsNotNull("devis.id_user","OR","user_filtre")->addOrder("devis.date");
					}

				}

				if($titre == "Devis gagnés"){ ATF::devis()->q->where("devis.etat","gagne"); }
				elseif($titre == "Devis en attente"){ ATF::devis()->q->where("devis.etat","attente"); }
				else{ ATF::devis()->q->where("devis.etat","perdu"); }





				if($infos["tu"]){
					ATF::devis()->q->where("devis.date", "2015-06-01","AND","user_filtre",">=")->where("devis.date", "2015-06-07","AND","user_filtre","<=");
				}

				$res = ATF::devis()->sa();
				foreach ($res as $k => $v) {

					$affaire =ATF::affaire()->select($v["id_affaire"]);



					ATF::loyer()->q->reset()->where("loyer.id_affaire",$v["id_affaire"]);
					$loyers = ATF::loyer()->sa();

					$row_data[$k] = array(
			        	 "A"=>array(ATF::societe()->nom($v["id_societe"]))
			        	,"B"=>array(ATF::user()->nom(ATF::societe()->select($v["id_societe"], "id_owner")))
						,"C"=>array(ATF::user()->nom($v["id_user"]))
						,"D"=>array($v["ref"])
						,"E"=>array($v["date"])
						,"F"=>array(ATF::societe()->select($v["id_societe"], "code_client"))
						,"G"=>array($v["etat"])
						,"H"=>array($v["first_date_accord"])
						,"I"=>array($v["date_accord"])
						,"J"=>array($v["type_contrat"])
						,"K"=>array(ATF::affaire()->select($v["id_affaire"], "date_installation_prevu"))
						,"L"=>array("")	,"M"=>array(""),"N"=>array(""),"O"=>array(""),"P"=>array(""),"Q"=>array(""),"R"=>array(""),"S"=>array(""),"T"=>array(""),"U"=>array("")
						,"V"=>array(""),"W"=>array(""),"X"=>array(""),"Y"=>array("")
						,"Z"=>array(ATF::contact()->nom(ATF::societe()->select($v["id_societe"], "id_prospection")))
						,'AA'=>array($affaire['id_partenaire']?ATF::societe()->select($affaire['id_partenaire'], "societe"):"")
						,"AB"=>array($affaire["site_associe"])
						,"AC"=>array($affaire["provenance"])
					);


					//A =65 Z=90
				    $lettre = 76;
				    $totalLoyer = 0;


					foreach ($loyers as $keyLoyer => $valueLoyer) {
						$totalLoyer = $totalLoyer + ($valueLoyer["loyer"]+$valueLoyer["assurance"]+$valueLoyer["frais_de_gestion"])*$valueLoyer["duree"];
						$char = chr($lettre);
						$row_data[$k][$char] = array($valueLoyer["loyer"]);
						$lettre++;
						$char = chr($lettre);
						$row_data[$k][$char] = array($valueLoyer["duree"]);
						$lettre++;
						$char = chr($lettre);
						$row_data[$k][$char] = array($valueLoyer["frequence_loyer"]);
						$lettre++;
						$char = chr($lettre);
					}
					$row_data[$k]["Y"]= array($totalLoyer);


					ATF::devis_ligne()->q->reset()->where("devis_ligne.id_devis",$v["id_devis"]);
					$devis_lignes = ATF::devis_ligne()->sa();
					$achat = 0;
					foreach ($devis_lignes as $dlk => $dlv) {	$achat = $achat + ($dlv["prix_achat"]*$dlv["quantite"] );	}
					$row_data[$k]["X"]= array($achat);


				}


    		break;


    		case "MEL" :
    			ATF::commande()->q->reset()->from("commande","id_societe","societe","id_societe")
											->whereIsNotNull("commande.date_debut","AND")
											->where("commande.etat", "non_loyer","OR")
											->where("commande.etat", "mis_loyer","OR");
				if($infos["id_user"]){
					ATF::commande()->q->where("societe.id_owner",ATF::user()->decryptId($infos["id_user"]));
				}elseif($infos['id_contact']){

					ATF::commande()->q->where("societe.id_prospection",$infos['id_contact']);
				}else{
					if($infos['id_partenaire']){
						ATF::commande()->q->whereIsNotNull("societe.id_owner")
									      ->from("commande","id_affaire","affaire","id_affaire")
									      ->where("affaire.id_partenaire",$infos['id_partenaire']);
					}else{
						ATF::commande()->q->whereIsNotNull("societe.id_owner");
					}
				}

				if($infos["tu"]){
					ATF::commande()->q->where("commande.date_debut", "2014-06-01","AND","user_filtre",">=")->where("commande.date_debut", "2015-05-15","AND","user_filtre","<=");
				}
				$res = ATF::commande()->sa();

				$row_auto=1;
				foreach ($res as $k => $v) {
					$loyer = $duree= $assurance = $frais = $frequence =  $total = $refi = "";

					ATF::loyer()->q->reset()->where("loyer.id_affaire",$v["id_affaire"]);
					$loyers = ATF::loyer()->sa();

					foreach ($loyers as $keyLoyer => $valueLoyer) {
						if($loyer == ""){
							$loyer = $valueLoyer["loyer"];
							$duree = $valueLoyer["duree"];
							$frequence = $valueLoyer["frequence_loyer"];
							$assurance = $valueLoyer["assurance"];
							$frais = $valueLoyer["frais_de_gestion"];
							$total = ($valueLoyer["loyer"]+$valueLoyer["assurance"]+$valueLoyer["frais_de_gestion"])*$valueLoyer["duree"];
						}else{
							$loyer .= "\n".$valueLoyer["loyer"];
							$duree .= "\n".$valueLoyer["duree"];
							$frequence .= "\n".$valueLoyer["frequence_loyer"];
							$assurance .= "\n".$valueLoyer["assurance"];
							$frais .= "\n".$valueLoyer["frais_de_gestion"];
							$total = $total + (($valueLoyer["loyer"]+$valueLoyer["assurance"]+$valueLoyer["frais_de_gestion"])*$valueLoyer["duree"]);
						}
					}

					ATF::demande_refi()->q->reset()->where("id_affaire",$v["id_affaire"],"AND")
										   ->where("etat", "valide");
					$refi = ATF::demande_refi()->select_row();
					if($refi){	$refi = ATF::refinanceur()->select($refi["id_refinanceur"] , "refinanceur"); }

					$affaire =ATF::affaire()->select($v["id_affaire"]);

					$row_data[$k] = array(
			        	 "A"=>array(ATF::societe()->nom($v["id_societe"]))
			        	,"B"=>array(ATF::user()->nom(ATF::societe()->select($v["id_societe"], "id_owner")))
						,"C"=>array(ATF::societe()->select($v["id_societe"], "code_client"))
						,"D"=>array($v["commande"])
						,"E"=>array(ATF::affaire()->select($v["id_affaire"], "date_installation_reel"))
						,"F"=>array($v["prix_achat"])
						,"G"=>array($v["prix"])
						,"H"=>array(ATF::commande()->select($v["id_commande"], "date"))
						,"I"=>array(ATF::commande()->select($v["id_commande"], "date_debut"))
						,"J"=>array($loyer)
						,"K"=>array($duree)
						,"L"=>array($assurance)
						,"M"=>array($frais)
						,"N"=>array($frequence)
						,"O"=>array($total)
						,"P"=>array($refi)
						,"Q"=>array(ATF::contact()->nom(ATF::societe()->select($v["id_societe"], "id_prospection")))
						,'R'=>array($affaire['id_partenaire']?ATF::societe()->select($affaire['id_partenaire'], "societe"):"")
						,"S"=>array($affaire["site_associe"])
						,"T"=>array($affaire["provenance"])
					);



				}

    		break;

    		case "Export Optima" :
			case  "RDV" :
			case  "Appels":

				if($infos["id_user"]){
					ATF::suivi()->q->reset()->where("id_user",ATF::user()->decryptId($infos["id_user"]));
				}else{
					ATF::suivi()->q->reset()->whereIsNotNull("id_user");
				}


			    if($infos["tu"]){
					ATF::suivi()->q->where("suivi.date", "2015-06-01","AND","user_filtre",">=")->where("suivi.date", "2015-06-07","AND","user_filtre","<=");
				}
			    if($titre == "RDV"){
			    	ATF::suivi()->q->where("type","RDV");
			    }elseif($titre == "Appels"){
					ATF::suivi()->q->where("type","appel");
				}

				$res = ATF::suivi()->sa();
				foreach ($res as $keySuivi => $valueSuivi) {
					$intervenant = $mail = "";



    				$row_data[$keySuivi] = array(
    					"A"=>array(date("Y-m-d", strtotime($valueSuivi["date"])))
			        	,"B"=>array($valueSuivi["texte"])
						,"C"=>array(ATF::societe()->nom($valueSuivi["id_societe"]))
						,"D"=>array(ATF::user()->nom($valueSuivi["id_user"]))
						,"E"=>array(ATF::contact()->nom(ATF::societe()->select($valueSuivi["id_societe"], "id_prospection")) )
						,"F"=>array($valueSuivi["type"])
						,"G"=>array($valueSuivi["type_suivi"]));
				}
    		break;

    	}
    	$i=0;
    	$j=2;
    	foreach ($row_data as $ligne => $value){
	    	foreach($value as $col=>$titre){
				$sheet->setCellValueByColumnAndRow($i , $j, $titre[0]);
				$i++;
	        }
	        $i=0;
	        $j++;
	    }

    }

};

class user_cleodisbe extends user_cleodis { };
class user_cap extends user_cleodis {

	public function getFeuilles(){
		return array("Export Optima",
					  "RDV",
					  "Appels"
					 );
	}

}


class user_bdomplus extends user_cleodis { };

class user_boulanger extends user_cleodis { };

class user_assets extends user_cleodis { };