<?
/** 
* Classe formation_facture
* @package Optima
*/
require_once dirname(__FILE__)."/../formation_facture.class.php";
class formation_facture_cleodis extends formation_facture {
	/**
	* Constructeur
	*/
	function __construct() {
		$this->table = "formation_facture";
		parent::__construct();

		$this->colonnes['fields_column'] = array(			
			 'formation_facture.ref'
			,"formation_facture.id_formation_devis"
			,"formation_facture.id_societe"
			,"formation_facture.date"
			,"formation_facture.num_dossier"
			,"formation_facture.prix"=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","suffix"=>"€","type"=>"decimal","renderer"=>"money")
			,'formation_facture.date_regularisation'=>array("renderer"=>"updateDate","width"=>170)
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","width"=>70)			
		);
		
		// Panel prinicpal
		$this->colonnes['primary'] = array(
			 "ref"=> array("disable"=>true)
			,"id_formation_devis"
			,"id_societe"
			,"date"
			,"num_dossier"
			,"prix"
			,'type'=>array("data"=>array("normale","acompte"),"xtype"=>"combo","listeners"=>array("change"=>"ATF.formation_facture_type"))
			,"commentaire"
		);

		$this->fieldstructure();

		$this->field_nom = "%ref%";		

		$this->files["fichier_joint"] = array("type"=>"pdf","preview"=>true, "no_upload"=>true);

		$this->no_insert = true;


		$this->addPrivilege("export_special");
		$this->addPrivilege("export_special2");
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */   	
	public function default_value($field){	
		if(ATF::_r('id_formation_commande')){
			$commande = ATF::formation_commande()->select(ATF::formation_commande()->decryptId(ATF::_r('id_formation_commande')));
			$id_devis = $commande["id_formation_devis"];
			$devis = ATF::formation_devis()->select($id_devis);


			ATF::formation_priseEnCharge()->q->reset()->where("id_formation_devis", $id_devis);
			$formation_priseEnCharge = ATF::formation_priseEnCharge()->select_row();


			switch ($field) {				
				case "date" :
					return date("Y-m-d");
				case "id_formation_devis" :
					return $id_devis;
				case "id_societe" :
					if($formation_priseEnCharge && $formation_priseEnCharge["subro_client"] == "oui"){	return $formation_priseEnCharge["opca"]; }
					else{	return ATF::formation_devis()->select($id_devis , "id_societe");	}					
				case "prix" :
					return $devis["montantHT"];
			}
		}

		switch ($field) {				
			case "date" :
				return date("Y-m-d");
		}		
		
		return parent::default_value($field);
	}


	/** 
	* Surcharge de l'insert afin de créer le contrat 
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>    
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$this->infoCollapse($infos);
		
		if($infos["type"] == "normale"){
			if(!$infos["ref"]){ throw new errorATF("Le champs réference est obligatoire pour une facture normale");}
			if(!$infos["num_dossier"]){ throw new errorATF("Le champs N° de dossier est obligatoire pour une facture normale");}
		}

		unset($infos["id_formation_commande"]);
		ATF::db($this->db)->begin_transaction();

		$last_id=parent::insert($infos,$s,NULL,$var=NULL,NULL,true);	

		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){	ATF::formation_devis()->redirection("select",$infos["id_formation_devis"]);	}
		return $last_id;
	}


	/** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique 
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>              
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $infos : contient le nom de l'onglet 
     */     
	 public function export_special($infos){
	 	 if($infos["rejet"]){ $rejet = true; }

         $this->q->reset();

         $this->setQuerier(ATF::_s("pager")->create($infos['onglet'])); // Recuperer le querier actuel


         $this->q->setLimit(-1)->unsetCount();   
         if(!isset($infos["tu"])) $infos = $this->select_all();
         else $infos = $infos["data"]; 	
		 
		 if($rejet) $infos["rejet"] = "oui";
		 
		 $this->export_xls_special($infos);                   
     }      
            
     /** Surcharge pour avoir un export identique Ã  celui de Nebula    
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>              
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $infos : contient tous les enregistrements          
     */     
     public function export_xls_special(&$infos){
     		     
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php"; 
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";  
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());        
		$workbook = new PHPExcel;        
            
		//premier onglet  
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle('Autoporté'); 
		$sheets=array("auto"=>$worksheet_auto);         
		
		//mise en place des titres       
		$this->ajoutTitre($sheets);      
		
		//ajout des donnÃ©es             
		if($infos){			      
			 $this->ajoutDonnees($sheets,$infos);        
		}     
		
		$writer = new PHPExcel_Writer_Excel5($workbook);
		
		$writer->save($fname);           
		header('Content-type: application/vnd.ms-excel');              
		header('Content-Disposition:inline;filename=export_facture_formation.xls');            
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");         
		fpassthru($fh);   
		unlink($fname);   
		PHPExcel_Calculation::getInstance()->__destruct(); 
		// Pour remettre la prÃ©cision correcte ! sinon ini_set('precision',14)... sinon ca provoque un pb avec php > var_dump((1.196-1)*100); => float(19.59999999999999432) ( https://bugs.php.net/bug.php?id=55368 )
     }      
            
     /** Mise en place des titres         
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>              
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $sheets : contient les 5 onglets     
     */     
     public function ajoutTitre(&$sheets){
        $row_data = array(
        	"A"=>'Type'
        	,"B"=>'Date'
			,"C"=>'Journal'
			,"D"=>'Général'
			,"E"=>'Auxiliaire'
			,"F"=>'Sens'
			,"G"=>'Montant'
			,"H"=>'Libellé'
			,"I"=>'Référence'
			,"J"=>'Section A1'
		);           
            
         foreach($sheets as $nom=>$onglet){              
             foreach($row_data as $col=>$titre){         
				  $sheets[$nom]->write($col.'1',$titre);  
				  $sheets[$nom]->sheet->getColumnDimension($col)->setAutoSize(true);    
             }             
         }  
     }      
  	            
     /** Mise en place du contenu         
     * @author Nicolas BERTEMONT <nbertemont@absystech.fr>              
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
     * @param array $sheets : contient les 5 onglets     
     * @param array $infos : contient tous les enregistrements          
     */     
     public function ajoutDonnees(&$sheets,$infos){
		$row_auto=1;      
		$increment=0;   

		$rejet = false;
		if($infos["rejet"]) $rejet = true; unset($infos["rejet"]);

		foreach ($infos as $key => $item) {             
			$increment++; 
			if($item){		
				//initialisation des données
				$facture = ATF::formation_facture()->select($item["formation_facture.id_formation_facture"]);		
				
				$devis=ATF::formation_devis()->select($item["formation_facture.id_formation_devis_fk"]);        
				$societe = ATF::societe()->select($facture['id_societe']);
				

				
	 			$date=date("dmY",strtotime($facture['date']));        
				$affaire=ATF::affaire()->select($facture['id_affaire']);          
				if($increment>999){  $reference="F".date("ym",strtotime($facture['date'])).$increment;
				}elseif($increment>99){ $reference="F".date("ym",strtotime($facture['date']))."0".$increment;
				}elseif($increment>9){  $reference="F".date("ym",strtotime($facture['date']))."00".$increment;           
				}else{ $reference="F".date("ym",strtotime($facture['date']))."000".$increment;  }              
				
				$libelle = 'F'.$facture['ref'].'-'.$societe['code_client'].'/'.$societe['societe'];
				if($facture["prix"]<0) $libelle = 'A'.$facture['ref'].'-'.$societe['code_client'].'/'.$societe['societe'];
				
				$compte_2='706100';  
				$compte_3='445710';  
				$type="divers";               
				
				//insertion des donnÃ©es     
				for ($i = 1; $i <= 4; $i++) {
					$row_data=array();       
					if($i==1){
						$row_data["A"]='G';  
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';
						$row_data["D"]="411000";					     
						$row_data["E"]=$societe["code_client"];      
						  
						if($facture['prix']<0){  $row_data["F"]='C';             
						}else{	$row_data["F"]='D';  } 
						$row_data["G"]=round(abs($facture['prix']*__TVA__),2);
															    
						$row_data["H"]=$libelle;            
						$row_data["I"]=$reference;          
						        
					}elseif($i==2){          
						$row_data["A"]='G';  
						$row_data["B"]=" ".$date;
						$row_data["C"]='VEN';
						
						if($facture['prix']<0){ $row_data["F"]='D';             
						}else{ $row_data["F"]='C';  }
						$row_data["G"]=abs($facture['prix']); 
						$row_data["D"]=$compte_2; 						
						$row_data["H"]=$libelle;            
						$row_data["I"]=$reference;           
					}elseif($i==3){
						
						$row_data["A"]='A1';            
						$row_data["B"]=" ".$date;           
						$row_data["C"]='VEN';  
						$row_data["J"]= substr($devis["numero_dossier"],0,7).$societe["code_client"]."00";  						
						$row_data["D"]=$compte_2;       

						if($facture['prix']<0){ $row_data["F"]='D';         
						}else{ $row_data["F"]='C'; } 							
						$row_data["G"]=abs($facture['prix']); 							  
						$row_data["H"]=$libelle;        
						$row_data["I"]=$reference;          
					}elseif($i==4){ 			
						$row_data["A"]='G';             
						$row_data["B"]=" ".$date;           
						$row_data["C"]='VEN';           
						$row_data["D"]=$compte_3;   
							
						if($facture['prix']<0){ $row_data["F"]='D';         
						}else{  $row_data["F"]='C'; } 
						
						$row_data["G"]=abs(($facture['prix']*__TVA__-$facture['prix']));
						$row_data["H"]=$libelle;        
						$row_data["I"]=$reference; 
					}         
					
												
					if($row_data){
						if($rejet){						
							if($row_data["G"] != 0){
								$row_auto++; 
								foreach($row_data as $col=>$valeur){							
									$sheets['auto']->write($col.$row_auto, $valeur);              
								}
							}
						}else{
							$row_auto++; 
							foreach($row_data as $col=>$valeur){							
								$sheets['auto']->write($col.$row_auto, $valeur);              
							}
						}    
					}        
				}             
			}  
		}
	}   


};

class formation_facture_cleodisbe extends formation_facture_cleodis { };
class formation_facture_cap extends formation_facture_cleodis { };