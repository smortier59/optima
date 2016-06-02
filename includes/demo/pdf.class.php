<?
require_once dirname(__FILE__)."/../absystech/pdf.class.php";
/**  
* @package Optima
* @subpackage AbsysTech
*/
class pdf_demo extends pdf_absystech {
	
	//Largeur de l'espace Enoncé
	public $widthEnonce = 120;
	//Largeur de l'espace Réponse
	public $widthReponse = 75;
	public $widthConstitutionTable = 40;
	
	public $ToTable = array();
	
	//Largeur de l'espace Enoncé
	public $dateFormat = "d-m-Y";
	
	public $ADesordre = "3505";
	public $StyleStandard = 1;
	
	//Seuil maximal pour les bookmark
	public $seuilLvlBookmark = 5;
	//Nombres de  celulles des réponses d'unaire a aligner sur la même ligne
	public $CNumberSameLine=3;
	//Largeur des celulles des réponses d'unaire quand elles sont alignés sur la même ligne
	public $CWidthSameLine=27;
	
	//Couleur des réponses
	public $colorReponse = "1a029a";
	// Sauvegarde des styles, pour ne pas aller les chercher X fois dans la base
	private $styles = array();
	
	// Style utilisé par défaut
	private $default_style = array();
	
	private $numerotationLVL1 = 65;
	private $prefixNumerotation = "";
	private $numerotation = array(1);
	private $lvl = 0;

	//ID attr des Locaux de travail 
	public $Locaux = array("ERP"=>3786,"LT"=>3785,"M"=>3716);
	
	//Traduction des types de projet
	public $trad = array("gros_entretien"=>"Gros Entretien","accessibilite"=>"Accessibilité","energie"=>"Energie");

	/* Initialise les variables et lance la fonction demandé
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2009
	* @param string $function Fonction FPDF a executé pour générer le PDF
	* @param array $params Paramètres de la fonction
	* @param bool $output Permet d'activer ou non la sortie du PDF
	* @param array $s Session
	* @param bool $temp PDF temporaire ou non
	*/
	function generic($function,$params,$output=false,&$s=NULL,$temp=false) {
		//Vider les infos de visite et de projet.
		unset($this->visite,$this->projet,$this->ToTable,$this->id_ppa);
		parent::generic($function,$params,$output,$s,$temp);
	}	
	
	/* Verification et anticipation d'un saut de page automaique qui coupe les cellule en deux.
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2009
	* @param string $txt Texte
	* @param int $Wcell Largeur de la celulle
	* @param int $lineH Hauteur de la ligne
	*/
	function verifyPageBreak($txt,$Wcell,$lineH=4) {
		// Taille de la cellule enoncé
		$Hcell = $lineH*(number_format($this->getstringwidth($txt)/$Wcell,0)+1)+$lineH;
		//On vérifie si l'on doit changer de page... c'est a dire qi l'enoncé qu'on va ecrire enclenche un saut de page, dans ce cas la on le fait de suite sinon ya le bordel dans les coordonnées.
		if ($this->gety()+$Hcell > $this->h-$this->bMargin) {
			$this->addpage(true);
		}
	}
	
	function Footer() {
		if ($this->noFooter) return;
		//Police Arial italique 8
		$this->SetXY(10,-10);
		$style = array("decoration"=>"I","size"=>8,"color"=>"000000");
		$style2 = array("decoration"=>"BI","size"=>10,"color"=>"000000");
		$savelMargin=$this->lMargin;
		//Numéro de page centré
		if ($this->PageNo()===1) {
			//Police Arial italique 8
			$this->ATFsetStyle($style2);
			$this->Cell(50,8,'Version du '.date($this->dateFormat),1,0,'C');
		} else {
			$this->ATFsetStyle($style);
			$this->Cell(100,10,'Généré le '.date($this->dateFormat). " à ".date("H:i:s"),0,0,'L');
		}
			$this->ATFsetStyle($style);
		$this->dataOnPage--;
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'R');
		$this->dataOnPage--;
		$this->SetLeftMargin($savelMargin);
	}
	
	function Header() {
		if ($this->Header) {
			$savelMargin=$this->lMargin;
			//Positionnement à 5mm du haut
			$this->SetY(5);
			$style = array("decoration"=>"I","size"=>8,"color"=>"000000");
			//Police Arial italique 8
			$this->ATFsetStyle($style);
			//Numéro de page centré
			$this->Cell(0,5,$this->Header,0,0,'R');
			$this->dataOnPage--;
			$this->SetLeftMargin($savelMargin);
			$this->sety(15);
		}
	}
		
	function visiteReduit($id_visite) {
		$this->visite($id_visite,true,false);
	}	
		
	function visite($id_visite,$reduit=false,$intermediaire=false) {
		if ($reduit) {
			$this->onlyAnswerd = true;
		}
		
		if (ATF::_g('date')) {
			$this->dateReference = ATF::vi_pa()->formatDateReference(ATF::_g('date'));
			//substr(ATF::_g('date'),0,10)." ".substr(ATF::_g('date'),10);
		} else {
			$this->dateReference = date("Y-m-d H:i:s");
		}
		
		//Re init des bookmarks
		$this->outlines=array();
		// Infos de la visite
		$this->visite = ATF::visite()->select($id_visite);
		// Génération du projet
		$this->projet($this->visite['id_gep_projet']);
	}
	
	function projet($id_gp) {
		ini_set("max_execution_time",128);
	
		//On active par défaut la recherche d'enfant
		$this->searchChild = true;
	
		//Re init des bookmarks
		$this->outlines=array();
		// Variables du rapport
		$this->projet = ATF::gep_projet()->select($id_gp);
		$this->client = ATF::societe()->select($this->projet['id_societe']);
		
		// On défini le style par défaut
		$this->default_style = ATF::style()->select($this->projet['id_style']); // Style 1 par défaut
		
		// On active le paramètre VIERGE
		$this->vierge = true;
	
		// Page de garde
		$this->pageDeGarde();
		
		// Récap Visite
		if ($this->visite) {
			$this->recapVisite();
		}
		
		// Formulaire
		$this->formulaire();
		
		// Synthèse
		// $this->synthese();
		
		// Sommaire
		unset($this->Header);
		$this->sommaire();
		ini_set("max_execution_time",0);
	}
	
	function pageDeGarde($titre=false) {
		$this->Addpage(true);
		$this->SetAutoPageBreak(true,15);
		$this->setmargins(15,15);
		$this->setfont("arial","B",14);
		
		$this->image(__PDF_PATH__."demo/logo.jpg",20,20,40);
		$x = 170;
		$y = 20;
		$this->photo(ATF::gd()->createThumb("societe",$this->client['id_societe'],"logo",200,NULL,"jpg"),$x,$y,30);

		$this->sety(60);
		$this->multicell(0,10,$this->client['societe'],0,"C");
		
		if ($titre) {
			$this->multicell(0,10,$titre,"TRL","C");
		} else {
			$this->multicell(0,10,"Rapport de Diagnostic ".$this->trad[$this->projet['rapport']],"TRL","C");
		}
		$this->multicell(0,7,$this->visite['visite'],"RL","C");
		$this->multicell(0,5,$this->visite['reference_site'],"RL","C");
		$this->multicell(0,5,$this->visite['chorus']?"N° ".$this->visite['chorus']:"","BRL","C");
		
		$x = 65;
		$y = 100;
		if ($this->visite) {
			$this->photo(ATF::gd()->createThumb("visite",$this->visite['id_visite'],"photo",500,NULL,"jpg"),$x,$y,80,60);
		} else {
			$this->photo("",$x,$y,80,60);
		}
		
		$this->ln(15);
				
	}
	
	function recapVisite() {
		$this->setfont("arial","BU",12);
		$this->multicell(0,5,"Présentation du bien");
		$this->setleftmargin(65);
		$this->ln(5);
		$this->setfont("arial","",10);
		$this->multicell(0,5,"Adresse: ".$this->visite['adresse']);
		$this->multicell(0,5,"Code Postal: ".$this->visite['cp']);
		$this->multicell(0,5,"Ville: ".$this->visite['ville']);
			
		$this->setfont("arial","BU",12);
		$this->setleftmargin(15);
		$this->ln(5);
		$this->multicell(0,5,"Informations sur la visite");
		$this->setleftmargin(35);
		$this->ln(5);
		$this->setfont("arial","",10);
		$this->multicell(0,5,"Date de la visite : ".($this->visite['date_visite']?date($this->dateFormat,strtotime($this->visite['date_visite'])):""));
		$this->multicell(0,5,"Personne accompagnante : ".$this->visite['personne_accompagnante']);
		$this->multicell(0,5,"Fonction: ".$this->visite['fonction']);
		$this->multicell(0,5,"N° de téléphone : ".$this->visite['tel_pa']);
		$this->multicell(0,5,"Intervenant : ".$this->visite['intervenantGinger']);
		$this->multicell(0,5,"N° de téléphone : ".$this->visite['tel_ig']);		
	}
	
	function formulaire() {
		ini_set("max_execution_time",0);
		// PA root
		$counter = 0;
		foreach (ATF::pa()->selectRootFromProjet($this->projet['id_gep_projet'],"pa.offset") as $k=>$i) {
			$this->attr($i);
		}
	}
	/**
    * Un attribut, fonction récursive
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $infos
    * @param int $lvl
    * @param array $numerotation
    * @param int $lvlBookmark
    */   
	function attr($infos,$forceShow=false) {	
//echo "Attr ".$infos['id_attr']." - ".$i['id_pa']."\n";	
//		if ($this->PageNo()>30) return false;
		$this->forceShow = $forceShow;
//		echo "\n======================[".$infos['id_attr']."]".$infos['attr']."============================\n";
		// L'attribut a un style
		if($infos)$this->defineStyle($infos);
//		print_r($infos);
		
		//Bipassement du LVL via le style
		if ($infos['style']['retrait']!==false && $infos['style']['retrait']!==NULL) {
			$this->lvl = $infos['style']['retrait'];
		}
		
		//Stockage dans un tableau de tous les attr de style TABLE
		if ($infos['style']['format']=="table" && !ATF::pa()->getFonction($infos)) {
			if (isset($infos['id_pa']) && $infos['id_pa']) {
				$this->ToTable[$infos['id_parent']][$infos['id_attr']][] = $infos['id_pa'].($infos['id_vi_pa_multi']?"-".$infos['id_vi_pa_multi']:"");
			} else {
				$this->ToTable[$infos['id_parent']][$infos['id_attr']][] = $this->id_ppa.($infos['id_vi_pa_multi']?"-".$infos['id_vi_pa_multi']:"");
			}
			return false;
		}
		
		//Initialisation pour les photos et plans
		if ($infos['type']!='photo') {
			if ($this->lastType=='photo') $this->ln(5);
			$this->nbPhoto=$this->nbPhotoPrint=0;
			unset($this->ySavedForPhoto);
		}
		if ($infos['type']!='plan') {
			if ($this->lastType=='plan') $this->ln(5);
			$this->nbPlan=$this->nbPlanPrint=0;
			unset($this->ySavedForPlan);
		}
				
		// Passage de page
		$this->setleftmargin(10+$this->lvl*5+$this->specialDecalage);
		
		// Applique un nouveau style et applique les styles "non-élémentaires" (Qui ne sont pas des paramètres d'autres méthode, comme MultiCell ou Cell par exemple).
		$this->ATFsetStyle($infos["style"]);
		if ($infos['id_pa']) {
			$this->id_ppa = $infos['id_pa'];
		}
		if ($this->visite['id_visite']) {
			$this->searchChild = false;
		} else {
			$this->searchChild = true;		
		}
//		print_r($multi);

		// Numérotation que sur les élements ayant des enfants et récupération du libelle a afficher dans le PDF
		$infos['libelleATTR'] = ($infos['pa']?$infos['pa']:$infos['attr']);
//		echo $infos['libelleATTR']."\n";
		if ($infos['multi'] && $this->visite['id_visite']) {
			$multi = ATF::vi_pa()->getDistinct($this->visite['id_visite'],$infos['id_attr'],$infos['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
			if (!$multi && $this->onlyAnswerd && !$forceShow) return;
		}

		//Enfants dans les PA
		$enfants = ATF::pa()->selectChilds($infos['id_pa']);
		if (!$enfants) {
			//Enfants dans les ATTR
			$enfants = ATF::attr()->selectChilds($infos['id_attr']);
		}

		if ($infos && ATF::pa()->getClass($infos)) {//preg_match("/class:'/",$infos['option'])) {
			$this->attrWithStyle($infos);
			unset($infos);
			$this->searchChild=false;
		} elseif ($enfants[0] && ATF::pa()->getClass($enfants[0])=='constitution') {//preg_match("/class:'constitution'/",$enfants[0]['option'])) {
			if (is_array($multi)) {
				foreach ($multi as $Mk=>$Mi) {
					$lib = $infos['pa']?$infos['pa']:$infos['attr'];
					$infos['libelleATTR'] = $lib;
					$infos['id_vi_pa_multi'] = $Mi['id_vi_pa_multi'];
					$this->attrWithStyle($enfants[0],$infos,($Mk?false:true));
					$ch = $enfants;
					unset($ch[0]);
					if ($ch) {
						foreach ($ch as $k=>$i) {
							if ($infos['id_pa']) {
								$this->id_ppa = $infos['id_pa'];
							}
							if ($infos['id_vi_pa_multi']) {
								$i['id_vi_pa_multi']=$infos['id_vi_pa_multi'];
							}
							$this->attr($i,$forceShow);
						}
					}

				}
				return false;
			} else {
//			print_r($infos);print_r($enfants[0]);die();
				$enfants[0]['id_vi_pa_multi'] = $infos['id_vi_pa_multi'];
				$enfants[0]['libelleATTR'] = ($enfants[0]['pa']?$enfants[0]['pa']:$enfants[0]['attr']);
				$this->defineStyle($enfants[0]);
				//Gestion des options attr sur les enfants Pour éviter que le titre du tableau de constitution soit bien sur la meme page que le tableau.
				$r = $this->attrWithStyle($enfants[0],$infos);
				// Si return false, ca veut dire que le bloc constitution n'a pas été renseigné
				// Mais attention si ce bloc a des frères/soeurs, il faut quand meme afficher le titre de son parent.
				if (!$r && count($enfants)>1) {
					$this->defineStyle($infos,true);
					if ($infos['type']=="sans_reponse") {
						$this->titre($infos);

					} elseif($infos['type']=="unary") {
						$this->unary($infos);
					}
				}
			}
			if (count($multi)>1) {
				unset($enfants[0],$infos);
			} else {
				$id_vi_pa_multi = $infos['id_vi_pa_multi'];
				unset($enfants[0],$infos);
				$infos['id_vi_pa_multi'] = $id_vi_pa_multi;
			}
			$this->searchChild=true;
		}
		switch ($infos['type']) { 
			case "photo":
				unset($var);
				$this->searchChild = true;
				$ln = false;
				if (is_array($multi)) {
					$this->ATFsetStyle($infos['style']);
					$this->multicell(0,5,$infos['libelleATTR']);
					foreach ($multi as $k=>$i) {
						$infos['id_vi_pa_multi'] = $i['id_vi_pa_multi'];
						$var = $this->initPhotoVarByStyle($infos['style']['id_style']);
						if ($var && $this->attrPhoto($infos,$var['x'],$var['y'],$var['w'],$var['h'],"jpg",false)) {
							$this->nbPhotoPrint++;						
							if ($var['ln']) {
								$this->ln(5);
								unset($this->nbPhotoPrint,$this->ySavedForPhoto);
							}
						}
					}
				} else {
					try {
						$testVIPA=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$infos['id_attr'],$this->id_ppa,$infos['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
					} catch(error $e) {	}
					if (!$testVIPA) continue;
					$var = $this->initPhotoVarByStyle($infos['style']['id_style']);
					if ($var && $this->attrPhoto($infos,$var['x'],$var['y'],$var['w'],$var['h'],"jpg")) {
						$this->nbPhotoPrint++;						
						if ($var['ln']) {
							$this->ln(5);
							unset($this->nbPhotoPrint,$this->ySavedForPhoto);
						}
					} else {
						unset($this->nbPhotoPrint,$this->ySavedForPhoto);
					}
				}
			break;
			
			case "plan":
				unset($var);
				$this->searchChild = true;
				$ln = false;
				if (is_array($multi)) {
					$this->multicell(0,5,$infos['libelleATTR']);
					foreach ($multi as $k=>$i) {
						$infos['id_vi_pa_multi'] = $i['id_vi_pa_multi'];
						$var = $this->initPlanVarByStyle($infos['style']['id_style']);
						if ($var && $this->attrPhoto($infos,$var['x'],$var['y'],$var['w'],$var['h'],"jpg",false)) {
							$this->nbPlanPrint++;					
							if ($var['ln']) {
								$this->ln(5);
								unset($this->nbPlanPrint,$this->ySavedForPlan);
							}
						}
					}
				} else {
					$var = $this->initPlanVarByStyle($infos['style']['id_style']);
					if ($var && $this->attrPhoto($infos,$var['x'],$var['y'],$var['w'],$var['h'],"jpg")) {
						$this->nbPlanPrint++;
						if ($var['ln']) {
							$this->ln(5);
							unset($this->nbPlanPrint,$this->ySavedForPlan);
						}
					}
				}
			break;
			
			case "text":
			case "date":
			case "num":
				if (is_array($multi)) {
					foreach ($multi as $k=>$i) {
						unset($infos['multi']);
						$infos['id_vi_pa_multi'] = $i['id_vi_pa_multi'];
						self::attr($infos,$forceShow);
					}
					$this->searchChild = false;
				} else {
					if (!$this->text($infos)) unset($infos['style']);
				}
			break;
			
			case "enum":
			case "set":
				$this->enum = $infos;
				$this->searchChild = true;
				//Affichage spécifique si il n'y a que deux unaires en réponse.
//				$enfants = ATF::pa()->selectChilds($infos['id_pa']);
//				if (!$enfants) {
//					$enfants = ATF::attr()->selectChilds($infos['id_attr']);
//				}
				// Si il n'y a que deux enfants a l'enum/set, il faut checker que ses enfants n'ont pas eux même d'enfants
				// Si c'est le cas on les affiches dans l'espace Réponse côte à côte, 
				// Sinon on les affiche dans l'espace enoncé les uns en dessous des autres
			
				if (count($enfants)<=$this->CNumberSameLine && !ATF::attr()->getChild($enfants) && !ATF::pa()->getChild($enfants)) {
					//On ne recherche pas les enfants dans ce cas la car on les affiche de façon particulière
					$this->searchChild = false;
					//Pour chacun d'eux on appelle directement la fonction unaire avec des paramètre d'affichage différents
					foreach ($enfants as $k=>$i) {
						$i['libelleATTR'] = ($i['pa']?$i['pa']:$i['attr']);
						if ($this->getstringwidth($i['libelleATTR'])>$this->CWidthSameLine) {
							unset($dataChild);
							$this->searchChild = true;
							break;
						}
						
						
						if ($infos['id_vi_pa_multi']) {
							$i['id_vi_pa_multi']=$infos['id_vi_pa_multi'];
						}
						$dataChild[$k+1] = $i;
					}
					if ($dataChild) {
						if ($this->enumORset($infos,false,$dataChild)) {
							foreach ($dataChild as $k=>$i) {
								if ($infos['id_pa'] || $i['id_pa']) {
									$this->id_ppa = $i['id_pa']?$i['id_pa']:$infos['id_pa'];
								}
								// L'attribut a un style
								if ($i['id_style']) {
									if (!isset($this->styles[$i['id_style']])) {
										if ($style = ATF::style()->select($i['id_style'])) {
											$this->styles[$i['id_style']] = $style;
										}
									}
									$i["style"] =& $this->styles[$i['id_style']];
								} else {
									//Si le style est le style STD, on vérifie qu'il n'y ai pas de style sur le A si oui, on le prend en compte
									$i["style"] = ATF::style()->select(ATF::attr()->select($i['id_attr'],'id_style'));
									if (!$i["style"]) {
										// Style par défaut
										$i["style"] = $this->default_style;
									}
								}
								$this->ATFsetStyle($i["style"]);
								$this->unary($i,$k,true);
							}
						}
					}
				} 
				
				if (!$dataChild) {
					$this->searchChild = $this->enumORset($infos,true,$enfants);
				}
				
			break;
			case "unary":

				if ($function = ATF::pa()->getFonction($infos)) {//preg_match("/function:/",$infos['option'])) {
					//$function = str_replace("'","",substr($infos['option'],10));
					if (method_exists($this,$function)) $this->$function($infos);
					else die("Fonction : ".$function." non définie");//
				} else {
					if (is_array($multi)) {
						foreach ($multi as $k=>$i) {
							unset($infos['multi']);
							$infos['id_vi_pa_multi'] = $i['id_vi_pa_multi'];
							self::attr($infos,$forceShow);
						}
					} else {
						$this->unary($infos);
					}
				}
			break;
			
			case "sans_reponse":
//				if ($infos['id_pa']==20798) {
//					print_r($infos);
//					print_r($enfants);
//					die();
//				}
				$this->searchChild = true;
				//Si le PA est multi, s'il n'y a pas déjà de vi_pa_multi qui circule dans la recursivité 
				// et si bien sûr si on récupère le tableau avec tous les id_vi_pa_multi
				if (is_array($multi)) {
//					echo "\n======MULTI SANS REPONSE======\n";
					foreach ($multi as $k=>$i) {
						unset($infos['multi']);
						$infos['id_vi_pa_multi'] = $i['id_vi_pa_multi'];
						self::attr($infos,$forceShow);
					}
					//Pour ne pas afficher l'attr qui ne contient pas les id vi pa multi sinon il y a doublon.
					$this->searchChild = false;
				} else {
					if (!$this->titre($infos)) unset($infos['style']);
				}
				if ($function = ATF::pa()->getFonction($infos,true)) {//preg_match("/function:/",$infos['option'])) {
					//$function = str_replace("'","",substr($infos['option'],10));
					if (method_exists($this,$function)) $this->$function($infos);
					else die("Fonction : ".$function." non définie");//
				}
			break;
			case "textarea":
				$this->searchChild = false;
				$this->titre($infos);
				try{
					$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$infos['id_attr'],$this->id_ppa,$infos['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
					$this->verifyPageBreak($vi_pa['reponse'] ? $vi_pa['reponse'] : $vide,$this->widthReponse);
				} catch(error $e) { }
				$this->defineStyle(ATF::style()->select($this->StyleStandard),true);
				$this->multicell(0,$this->height,$vi_pa['reponse'],$this->border,$this->align,$this->fill);
			break;
			default:
				// Attribut les police, taille et marge du texte a suivre
				//$this->multicell(0,5,"A GERER - ".$infos['type'],$this->border,$this->align);
			break;
		}
		
		//Saut de ligne ??
		if ($infos["style"]['ln']) {
			$this->ln($infos["style"]['ln']);
		}
		
		if ($infos['type']) {
			$this->lastType = $infos['type'];
		}
		if ($this->searchChild) {
			$lvlSaved = $this->lvl;
			if (method_exists($this,"casParticulierA".$infos['id_attr'])) {
				$function = "casParticulierA".$infos['id_attr'];
				$this->$function($infos,$enfants);
			} elseif ($enfants) {
//				echo "================".$infos['libelleATTR']." / Lvl ".$this->lvl."================\n";
				$this->lvl++;
				foreach ($enfants as $k=>$i) {
//					echo "================Enfants n°".$k." - ".$i['attr']."-".$i['pa']." / Lvl ".$this->lvl."================\n";
					if ($infos['id_pa']) {
						$this->id_ppa = $infos['id_pa'];
					}
					if ($infos['id_vi_pa_multi']) {
						$i['id_vi_pa_multi']=$infos['id_vi_pa_multi'];
					}
					$this->attr($i,$forceShow);
				}
			}
			$this->lvl = $lvlSaved;
		}
		$this->TableStyle($infos);

	}
	
	function TableStyle($infos) {
		if (is_array($this->ToTable[$infos['id_pa']])) {
			// Affichage des TABLE du PA si il y a un ID PA
			$v = $this->ToTable[$infos['id_pa']];
			unset($this->ToTable[$infos['id_pa']]);
		} elseif ($this->ToTable[$infos['id_attr']]) {
			//Sinon On essaye avec seulement l'ID ATTR
			$v = $this->ToTable[$infos['id_attr']];
			unset($this->ToTable[$infos['id_attr']]);
		}
		if ($infos['id_pa']) {
			$this->id_ppa = $infos['id_pa'];
		}
		if ($v) {
			$cHeight = 5;
			$cHeight = 5;
			$counter = 0;
			$data = array();
			$head = array();
			foreach ($v as $k_=>$i_) {
				foreach ($i_ as $key=>$id_pa) {
					if (preg_match("/-/",$id_pa)) {
						$id_vi_pa_multi = substr($id_pa,strpos($id_pa,"-")+1);
						$id_pa = substr($id_pa,0,strpos($id_pa,"-"));
					}
								
					$TableAnswerd[$k_] = false;
					// afficher au moins une ligne d'un tableau si on affiche le pdf complet
//					if (!$this->onlyAnswerd && !$this->visite['id_visite']) {
//						$TableAnswerd[$k_] = true;
//						
//					}
					
					//Pour les infos double, c'est le PA qui l'emporte
					$current = ATF::attr()->select($k_);
					$pa = ATF::pa()->select($id_pa);
					//Récupération du PA PARENT LE PLUS PROCHE
					if ($pa['id_pa']) {
						$this->id_ppa = $pa['id_pa'];
					}
					if ($pa['id_attr']==$k_) {
						$current = array_merge($current,$pa);
					}
					$enfants = ATF::pa()->selectChilds($id_pa);
					if (!$enfants) {
						$enfants = ATF::attr()->selectChilds($k_);
					}
					// on récupère les enfants du PA ou de l'ATTR
					$this->defineStyle($current);
					$this->ATFsetStyle($current["style"]);
					//Bipassement du LVL via le style
					$this->setleftmargin(15+$current['style']['retrait']*5+$this->specialDecalage);
					
					//Gestion des ATTR/PA multis
					if ($current['multi'] && $this->visite['id_visite']) {
						$multi = ATF::vi_pa()->getDistinct($this->visite['id_visite'],$current['id_attr'],$current['id_pa'],$id_vi_pa_multi,$this->dateReference);
						if (!$multi && $this->onlyAnswerd) continue;
					}
					
					//S'il y a des enfants on rempli le tableau
					if ($enfants) {
						// Si on est dans la configuration suivante : PARENT > ENFANT1 (en mode tableau, sans reponse) > X ENFANTS
						// Alors on ajoute l'enfant1 en première colonne et ces enfants à la sutie.
						// CE CAS NE MARCHE QUE SI TOUS LES ENFANTS DU SANS REPONSE SONT DES TEXTES OU DES UNAIRES !
						if ($enfants[0]['type']=="sans_reponse") {
							$current['libelleATTR'] = ($current['pa']?$current['pa']:$current['attr']);
							$TableAnswerd[$k_] = true;
							$this->searchChild = false;
							foreach ($enfants as $k=>$i) {
								//Init des entêtes
								$data[$k_][$k][] = $i['pa']?$i['pa']:$i['attr'];
								$style[$k_][$k][] = "";
								$head[$k_][0] = "Titre";
								$width[$k_][0] =100;
								
								if ($i['id_pa']) {
									$this->id_ppa = $i['id_pa'];
								}
								// on récupère les enfants du PA ou de l'ATTR
								$enfantsST = ATF::pa()->selectChilds($i['id_pa']);
								if (!$enfantsST) {
									$enfantsST = ATF::attr()->selectChilds($i['id_attr']);
								}
								$Cwidth = number_format(80/count($enfantsST));
								foreach ($enfantsST as $c=>$o) {
									unset($vi_pa);
									//Init des entêtes
									if (!$k) {
										$width[$k_][] = $Cwidth;
										$head[$k_][] = $o['pa']?$o['pa']:$o['attr'];
									}
									try {
										$vi_pa = ATF::vi_pa()->isAnswered($this->visite['id_visite'],$o['id_attr'],$o['id_pa']?$o['id_pa']:$i['id_pa'],$o['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
										if ($o['type']=="unary") {
											if ($vi_pa['reponse']) {
												$data[$k_][$k][] = "X";
												$style[$k_][$k][] = ATF::pa()->getStyle($vi_pa);
											} else {
												$data[$k_][$k][] = "";
												$style[$k_][$k][] = "";
											}
										} elseif ($o['type']=="enum") {
											$cnt = count($data[$k_]);
											if (!$o['id_pa']) $o['id_pa'] = $this->id_ppa;
											$vi_pa = ATF::vi_pa()->getEnumReponse($o,$this->visite['id_visite']);
											$reponse = ATF::pa()->getLibelle($vi_pa);
											if ($reponse) {
												$style[$k_][$k][] = ATF::pa()->getStyle($reponse);
												$data[$k_][$k][] = $reponse;
											} else {
												$style[$k_][$k][] = "";
												$data[$k_][$k][] = "-";
											}
											
										} else {
											$data[$k_][$k][] =$vi_pa['reponse'];
											$style[$k_][$k][] = ATF::pa()->getStyle($vi_pa);
										}
									} catch(error $e) { 
										$data[$k_][$k][] = "";
										$style[$k_][$k][] = "";
									}
								}
							}
						} else {
							$current['libelleATTR'] = ($current['pa']?$current['pa']:$current['attr']);
							if (is_array($multi) && $current['multi']) {
								// Init des données
								foreach ($multi as $c=>$o) {
									try{
										$currentVIPA = ATF::vi_pa()->isAnswered($this->visite['id_visite'],$current['id_attr'],$current['id_pa'],$current['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
									} catch(error $e) { }
									// Première Case : Plus l'id Multi
									//On récupère les réponses du multi qu'on exploitera plus bas
									foreach ($enfants as $k=>$i) {
										unset($reponses);
										try {
											if ($i['type']=="enum") {
												$enfantsENUM = ATF::pa()->selectChilds($i['id_pa']);
												if (!$enfantsENUM) {
													$enfantsENUM = ATF::attr()->selectChilds($i['id_attr']);
												}
												foreach ($enfantsENUM as $cle=>$obj) {
													try {
														$answerd = ATF::vi_pa()->isAnswered($this->visite['id_visite'],$obj['id_attr'],$obj['id_pa']?$obj['id_pa']:$i['id_pa'],$obj['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
													} catch (error $e) { }
													if ($answerd) {
														if ($answerd['id_attr']==$obj['id_attr'] && $answerd['reponse']) {
															$reponses = $answerd;
															$reponses['reponse'] = $answerd['id_pa']?ATF::pa()->select($answerd['id_pa'],"pa"):ATF::attr()->select($answerd['id_attr'],"attr");
															break;
														}
													}
												}
											} else {
												try {
													$reponses = ATF::vi_pa()->isAnswered($this->visite['id_visite'],$i['id_attr'],$i['id_pa']?$i['id_pa']:$this->id_ppa,$i['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
												} catch (error $e) { }
											}
										} catch(error $e) { 
										}
										//Init des entêtes
										if (!$c) {
											$head[$k_][] = $i['pa']?$i['pa']:$i['attr'];
										}
										
										if ($this->visite['id_visite']) {
											//Formattage photo
											if ($i['type'] == 'photo') {
												$style[$k_][$counter][] = "";
												$cHeight = 30;
												$data[$k_][$counter][] = "IMAGE:".ATF::gd()->createThumb("vi_pa",$reponses['id_vi_pa'],"photo",150,NULL,"jpg")."::::".$cHeight;
											} elseif ($i['type'] == 'date' && $reponses['reponse']) {
												$style[$k_][$counter][] = ATF::pa()->getStyle($reponses);
												$TableAnswerd[$k_] = true;
												$data[$k_][$counter][] = date($this->dateFormat,strtotime($reponses['reponse']));
											} else {
												if ($reponses['reponse']) {
													$TableAnswerd[$k_] = true;
												}
												$style[$k_][$counter][] = ATF::pa()->getStyle($reponses);
				
												// Traitement spéciale pourl es projet 59 - 62 conformément au ticket 3297
												if (($this->projet['id_gep_projet']==49 || $this->projet['id_gep_projet']==31) && $i['id_attr']==3506) {
													switch($reponses['reponse']) {
														case 1:
															$reponses['reponse'] = "TS";
														break;
														case 2:
															$reponses['reponse'] = "S";
														break;
														case 3:
															$reponses['reponse'] = "PS";
														break;
														case 4:
															$reponses['reponse'] = "M";
														break;
													}
												} elseif ($i['id_attr']==3511 || $i['id_attr']==3867) {
													if ($tempCost = ATF::vi_pa_cout()->getCosts($currentVIPA['id_vi_pa'],$this->dateReference)) {
														$tC = array_pop($tempCost);
														$reponses['reponse'] .= " ".$tC['cout_catalogue']['unite'];
													}
												}
				
												$data[$k_][$counter][] = $reponses['reponse'];
											}
										} else {
											$style[$k_][$counter][] = "";
											$data[$k_][$counter][] = "";
										}
//										if ($current['id_attr']==3505) {
//											print_r($data);
//											print_r($style);
//										}
									}
									$counter++;
								}
//								if ($current['id_attr']==3505) {
//									die();
//								}
							} else {
								foreach ($enfants as $k=>$i) {
									unset($vi_pa);
									if ($i['type'] == 'photo') {
										$cHeight = 30;
									}
									//Init des entêtes
									if (!$key) {
										$head[$k_][] = $i['pa']?$i['pa']:$i['attr'];
									}
									//Init des données
									$currentIDPA = $i['id_pa']?$i['id_pa']:$id_pa;
									try {
										if ($i['type']=="enum") {
											$enfantsENUM = ATF::pa()->selectChilds($i['id_pa']);
											if (!$enfantsENUM) {
												$enfantsENUM = ATF::attr()->selectChilds($i['id_attr']);
											}
											foreach ($enfantsENUM as $cle=>$obj) {
												try {
													$answerd = ATF::vi_pa()->isAnswered($this->visite['id_visite'],$obj['id_attr'],$obj['id_pa']?$obj['id_pa']:$this->id_ppa,$obj['id_pa'],$id_vi_pa_multi,$this->dateReference);
												} catch (error $e) { }
												if ($answerd) {
													if ($answerd['id_attr']==$obj['id_attr'] && $answerd['reponse']) {
														$vi_pa = $answerd;
														$vi_pa['reponse'] = $answerd['id_pa']?ATF::pa()->select($answerd['id_pa'],"pa"):ATF::attr()->select($answerd['id_attr'],"attr");
														break;
													}
												}
											}
										} else {
											try {
												$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$i['id_attr'],$currentIDPA,$i['id_pa'],$id_vi_pa_multi,$this->dateReference);
											} catch (error $e) { }
										}
//										$style[$k_][$key][] = ATF::pa()->getStyle($vi_pa);
									} catch(error $e) { 
									}
									if ($i['type'] == 'photo') {
										$style[$k_][$key][] = "";
										$data[$k_][$key][] = "IMAGE:".ATF::gd()->createThumb("vi_pa",$vi_pa['id_vi_pa'],"photo",150,NULL,"jpg")."::::".$cHeight;
									} elseif ($i['type'] == 'date' && $vi_pa['reponse']) {
										$style[$k_][$key][] = ATF::pa()->getStyle($vi_pa);
										$TableAnswerd[$k_] = true;
										$data[$k_][$key][] = date($this->dateFormat,strtotime($vi_pa['reponse']));
									} elseif ($vi_pa['reponse']) {
										$style[$k_][$key][] = ATF::pa()->getStyle($vi_pa);
										$TableAnswerd[$k_] = true;
										$data[$k_][$key][] = $vi_pa['reponse'];
									} else {
										$style[$k_][$key][] = "";
										$data[$k_][$key][] = "";
									}
								}
							}
						}
					}
				}
			}
		}
		if (is_array($head)) {
			foreach ($head as $id_attr=>$cols) {
				if (count($cols)>1) {
					foreach ($data[$id_attr] as $c=>$o) {
						$count = max($count,count($o));
					}
					if ($count-count($cols)==2 || (count($data[$id_attr][0])-count($cols))==2) {
						$cols[] = "Coût Unitaire (EUR)";
						$cols[] = "Coût Total (EUR)";
					}
					if ($TableAnswerd[$id_attr] || !$this->visite['id_visite']) {

						$attr = ATF::attr()->select($id_attr);
						$pa = ATF::pa()->isPA($id_attr,$this->visite['id_gep_projet'],$infos['id_pa']);
						$current['libelleATTR'] = $pa?ATF::pa()->getLibelle($pa):ATF::pa()->getLibelle($attr);
						$this->ATFsetStyle($current["style"]);
						$this->lvl++;
						$this->titre($current);
						$this->lvl--;
						if ($current['style']['ln']) $this->ln($current['style']['ln']);
						
						if (!$this->visite['id_visite']) {
							foreach ($cols as $value) {
								$data[$id_attr][] = "";
							}
						}
						
						$w = $width[$id_attr]?$width[$id_attr]:ATF::pa()->getWidth($pa?$pa:$attr);
						if ($id_attr==3533) {
							$cHeight = 10;
						}
						$this->tableau($cols,$data[$id_attr],$w,$cHeight,$style[$id_attr]);
						//$this->ATFsetStyle($newStyle);
						if ($current['style']['ln']) $this->ln($current['style']['ln']);
					}
				}
			}
			$this->ln(5);
		}
	}


	/**
    * Appliquer la numérotation à ce libellé
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param string $titre
    * @param array $numerotation
    */   
	function setNumerotation(&$lib,$bm=false) {
//		echo "================".$lib." / Lvl ".$this->lvl."================\n";
		if ($this->lvl>4) return;
//		print_r($this->numerotation);

		if ((int)$this->lvl===0/* || $this->lvl==='0'*/) {
			$this->numerotation = array();
			if ($this->prefixNumerotation) {
				$lib = chr($this->prefixNumerotation).".".chr($this->numerotationLVL1).". ".$lib;
			} else {
				$lib = chr($this->numerotationLVL1).". ".$lib;
			}
			$this->numerotationLVL1++;
			if ($this->numerotationLVL1==91) {
				$this->prefixNumerotation?$this->prefixNumerotation++:$this->prefixNumerotation=65;
				$this->numerotationLVL1 = 65;
			}
		} else {
			if (!$this->numerotation[$this->lvl]) {
//				echo "PAS D'ENTREE\n";
				$this->numerotation[$this->lvl] = 1;
			} else {
//				echo "\nDEJA ENTREE ".$this->lvl."\n";
				$this->numerotation[$this->lvl]++;
//				print_r($this->numerotation);
				for ($i=1; $i<=$this->lvl;$i++) {
					$num[$i] = $this->numerotation[$i];
				}
				$this->numerotation = $num;
			}
//			print_r($this->numerotation);
			
			$num = $this->numerotation;
			$lib = str_replace("..",".",implode(".",$num)).". ".$lib;
		}
		
//		echo "\n================================\n";
		
		if ($bm!==false && $this->lvl<$this->seuilLvlBookmark) {
			$this->bookmark($lib,$this->lvl); // Raccourci dans le PDF
		}
		//$this->Header = $titre;
	}
	
	function titre($infos) {
//		echo "=========".$infos['libelleATTR']."(".$infos['id_pa'].")===========>";
		if ($this->onlyAnswerd && !$this->forceShow && !$this->childsAnswerd($infos) && !ATF::pa()->getOption($infos)) {
//			echo "JE PASSE\n<br>";
//			var_dump(!$this->childsAnswerd($infos));
//			var_dump(!ATF::pa()->getOption($infos));
			return false;
		}
//		echo "REPONDU\n<br>";
		if ($infos['style']['numerotation']=='oui') $this->setNumerotation($infos['libelleATTR'],$infos['style']['bookmark']=='oui'?true:false);
		$this->multicell(0,$this->height,$infos['libelleATTR'],$this->border,$this->align,$this->fill);
		return true;
	}
	
	function enumORset($infos,$allTheLine=false,$childs=false) {
		//On enregistre ici la réponse unique de l'enum afin de pouvoir le ré utiliser dans la fonction unary
		if ($infos['type']=="enum") {
			$reinit = false;
			if (!$infos['id_pa']) {
				$infos['id_pa'] = $this->id_ppa;
				$reinit = true;
			}
			$answer = $this->reponseEnum[$infos['id_pa']."-".$infos['id_attr']."-".$infos['id_vi_pa_multi']] = ATF::vi_pa()->getEnumReponse($infos,$this->visite['id_visite'],$this->dateReference);
//			if ($infos['id_attr']==3356) { print_r($infos);print_r($this->reponseEnum);die(); }
			if ($reinit) unset($infos['id_pa']);
//			if (ATF::vi_pa()->getEnumReponse($infos,$this->visite['id_visite'],$this->dateReference)) {
//				print_r($this->reponseEnum);die();
//			}
		}
		// Si les enfants sont passé, on vérifie qu'ils ont des réponses, si non, on n'affiche pas le enum/set

		if (is_array($childs) && $this->onlyAnswerd && !$this->forceShow && !$answer) {
			unset($this->enumReponse[$infos['id_attr']]);
			try {
				$Show = false;
				foreach ($childs as $k=>$i) {
					try{
						$vi_pa = ATF::vi_pa()->isAnswered($this->visite['id_visite'],$i['id_attr'],$i['id_pa']?$i['id_pa']:$this->id_ppa,$i['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
					} catch (error $e) { }
					if ($vi_pa['reponse']) {
						$Show = true;
					}
				}
			} catch (error $e) { }
			if (!$Show && !$this->forceShow) {
				return false;
			}
		
		}
	
		//On sauvegarde la marge pour le modifier sans incidence
		$lMarginSave = $this->lMargin;
		
		//Marge a 0
		$this->setleftmargin(0);
		
		//On vérifie que l'enoncé n'enclenche pas un saut de page
		if ($allTheLine) {
			$WcellEnonce = 0;
		} else {
			$WcellEnonce = $this->widthEnonce-$lMarginSave;
		}
		$this->verifyPageBreak($infos['libelleATTR'],$WcellEnonce,$this->height+3);
		
		//Cellule invisible qui fait office de marge
		$this->cell($lMarginSave,$this->height,"",0);
		
		$this->multicell($WcellEnonce,$this->height,$infos['libelleATTR'],$this->border,$this->align,$this->fill);
		//On initialise la dernière hauteur pour éviter des problèmes d'affichages côté unary
		$this->lastHeight = $this->height;
		return true;
	}
	
	function text($infos) {
		// Variable de réponse vide
		for ($i=0; $i<$this->widthReponse; $i++){
			$vide .= ".";
		}
		
		//On sauvegarde la marge pour le modifier sans incidence
		$lMarginSave = $this->lMargin;
		
		//Marge a 0
		$this->setleftmargin(0);
		
		//On vérifie que l'enoncé n'enclenche pas un saut de page
		$WcellEnonce = $this->widthEnonce-$lMarginSave;
		$this->verifyPageBreak($infos['libelleATTR'],$WcellEnonce);
		
		//On vérifie que la réponse n'enclenche pas un saut de page
		//Pour ça on récupère la réponse.
		if ($this->id_ppa && $this->visite['id_visite']) {
			try{
				$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$infos['id_attr'],$this->id_ppa,$infos['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
				$this->verifyPageBreak($vi_pa['reponse'] ? $vi_pa['reponse'] : $vide,$this->widthReponse);
				$this->searchChild = true;

				//Récupération du coût associé s'il existe
				if (ATF::pa()->hasCout($infos['id_pa'])) {
					$cout = " [".ATF::vi_pa()->getCost($vi_pa['id_vi_pa'],false,$this->dateReference)."EUR]";
				}
			} catch(error $e) { }
		}
		
		if ($this->onlyAnswerd && !$this->forceShow && !$vi_pa['reponse']) {
			$this->setleftmargin($lMarginSave);
			return false;
		}
		
		//On sauvegarde le Y
		$yStart = $this->gety();
		
		//Cellule invisible qui fait office de marge
		$this->cell($lMarginSave,$this->height,"",0);
		
		//L'enoncé de la question
		if ($infos['style']['numerotation']=='oui') $this->setNumerotation($infos['libelleATTR'],false);
		if ($infos['style']['bookmark']=='oui') $this->bookmark($infos['libelleATTR']." ".$vi_pa['reponse'],$this->lvl); // Raccourci dans le PDF
		$this->multicell($WcellEnonce,$this->height,$infos['libelleATTR'],$this->border,$this->align,$this->fill);
		$H = $this->height;
		//On stock le nombre de ligne ocupé par l'enoncé
		$nbLigneEnonce = $this->gety()-$yStart;
		
		//On reviens au meme Y mais on bouge le X pour mettre a la suite de l'enoncé
		$this->setxy($this->widthEnonce,$yStart);
		
		if ($vi_pa['reponse']) {
			//Le style des réponses
			$newStyle['color']=$this->colorReponse;
			
			$newStyle['size']=$infos['style']['size'];//9;
			$newStyle['decoration']="";
			$this->ATFsetStyle($newStyle);
			$this->multicell($this->widthReponse,$H,$vi_pa['reponse'].$cout,$this->border,$this->align,$this->fill);
			$this->unsetStyle();
		} else {
			$style['size']=9;
			$style['color']="000000";
			$this->ATFsetStyle($style);
			$this->multicell($this->widthReponse,$H, $vide,$this->border,$this->align,$this->fill);
			$this->unsetStyle();
		}

		//On stock le nombre de ligne ocupé par l'enoncé
		$nbLigneReponse = $this->gety()-$yStart;
		
		//On remet la marge en place
		$this->setleftmargin($lMarginSave);
		//Ainsi que le Y
		$this->sety($yStart+max($nbLigneEnonce,$nbLigneReponse));
		return true;	
	}
	
	function unary($infos,$num=false,$force_searchChild=false) {
		if($num) {
			// Initialisation de la marge
			$marge=$this->widthEnonce+($num-1)*$this->CWidthSameLine;
			// Initialisation de la taille de la cellule
			$CWidth = $this->CWidthSameLine-2;
			// On positionne tous les unaires du côté des réponses
			$this->setxy($marge,$this->gety()-$this->lastHeight);
		} else {
			$this->lastHeight = $this->height;
			$CWidth = $this->getstringwidth($infos['libelleATTR'])+20;
			if ($CWidth>180) $CWidth = 180;
		}
		// Affectation du style en fonction de la présence de réponse
		unset($style);
		
		//On sauvegarde le Y courant
		$yStart = $this->gety();
		
		//On vérifie ce qu'il reste dans la page, pour sauter si on est a la limite.
		if ($yStart>270) {
			$this->addpage();
			$yStart = 15;
		}
		
		if ((ATF::pa()->typeParent($infos['id_pa'])=="enum") || (!$infos['id_pa'] && ATF::attr()->typeParent($infos['id_attr'])=="enum")) {
		
			if ($this->enum['id_pa']) {
				$this->id_ppa = $this->enum['id_pa'];
			}
//			if ($infos['id_pa']==30853) {
////				print_r($this->enum);
//				var_dump($this->searchChild);
//				print_r($infos);die('phoque');
//				//die('fock'.$this->enum['id_pa']."-".$this->enum['id_attr']."-".$infos['id_vi_pa_multi']);
////				die('toto');
//			}
			// On vérifie la correspondance au niveau des id_attr
			if ($this->reponseEnum[$this->id_ppa."-".$this->enum['id_attr']."-".$infos['id_vi_pa_multi']]["id_attr"]==$infos['id_attr']) {
				// On vérifie la correspondance au niveau des id_pa s'il y en a
				if (!$infos['id_pa'] || $this->reponseEnum[$this->id_ppa."-".$this->enum['id_attr']."-".$infos['id_vi_pa_multi']]["id_pa"]==$infos['id_pa']) {
					$style="F";
					if (!$num) {
						$this->searchChild=true;
					}
				} else {
					unset($style);
					$this->searchChild=false;
				}
			} else {
				unset($style);
				if ($this->onlyAnswerd && !$this->forceShow) $this->searchChild=false;
			}
//			if (!$style && $this->onlyAnswerd) {
//				return;
//			}
			if ($infos['style']['id_style']!=27) {
				$this->Circle($this->getx()+2,$this->gety()+(($this->height)/2),1,$style);
			}
			$this->setx($this->getx()+3);
		} else {
			if ($this->onlyAnswerd && !$this->forceShow) {
				$this->searchChild=false;
			}
			try {
				$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$infos['id_attr'],$this->id_ppa,$infos['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
				if ($this->onlyAnswerd && !$this->forceShow && !$vi_pa['reponse']) {
					if($num) {
						$this->setxy($marge,$this->gety()+$this->lastHeight);
					}
					return false;
				}
				if ($vi_pa['reponse']) {
					$style = "F";
					if (!$force_searchChild) {
						$this->searchChild=true;
					}
				}
			} catch(error $e) { }
			$this->Checkbox($style,$this->height);
		}
		$this->ATFsetStyle($infos['style']);
		if ($infos['style']['numerotation']=='oui') $this->setNumerotation($infos['libelleATTR'],$infos['style']['bookmark']=='oui'?true:false);
		$this->multicell($CWidth,$this->height,"  ".($infos['libelleATTR']),$this->border,$this->align,$this->fill);
		//On calcul la hauteur que l'unaire a pris sur le PDF pour gérer l'affichage du suivant.
		$this->lastHeight = $this->gety()-$yStart;
		
	}
		
	function attrPhoto($infos,$x,$y,$w,$h,$type="jpg",$libelle=true,$res=500) {
		// Pour la visite
		if ($this->visite['id_visite']) {
			try {
				$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$infos['id_attr'],$this->id_ppa,$infos['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
				$this->searchChild=true;
				if (!$vi_pa['reponse'] || !ATF::gd()->createThumb("vi_pa",$vi_pa['id_vi_pa'],"photo",$this->mm2px($w),$this->mm2px($h),"jpg")/* && ($this->projet['rapport']=="accessibilite" || $this->onlyAnswerd)*/) {
					return false;
				}
				
				$wSaved = $w;
				$hSaved = $h;
								
				// Si la qualité n'est pas suffisante, il faut multiplié par deux les valeurs d'entrée du creteThumb et divisé par deux la résultante du px2mm
				if ($thumbPath = ATF::gd()->createThumb("vi_pa",$vi_pa['id_vi_pa'],"photo",$this->mm2px($w),$this->mm2px($h),"jpg")) {
					$thumb = getimagesize($thumbPath);
					$w = $this->px2mm($thumb[0]);
					$h = $this->px2mm($thumb[1]);
				}
								
				$w = parent::photo($thumbPath,$x,$y,$w,$h,$type);
				if ($this->nbPlanPrint || $this->nbPhotoPrint) {
					$this->setxy($x,$y);
					$this->cell($wSaved,$hSaved,"",0,1);
				}
				$this->setx($x);
				if ($libelle) {
					$ySaved = $this->gety();
					$this->multicell($w,4,$infos['libelleATTR'],$this->border,"C");
					$this->sety($ySaved+4);
				}
			} catch(error $e) { }
		//Pour le projet
		} elseif ($infos['attr'] || $infos['pa']) {
			$this->cell($this->getStringWidth($infos['attr']),4,$infos['libelleATTR'],$this->border,1,"L");
			$this->image(__PDF_PATH__."new_photo.jpg",$this->lMargin+$this->getStringWidth($infos['libelleATTR'])+4,$this->gety()-4,5,4,"jpg");
		//Pour les autre images affichées avec cette méthode
		} else {
			parent::photo(false,$x,$y,$w,$h,$type);
		}
		return true;
	}
	
	function defineStyle(&$infos,$set=false) {
		if ($infos['id_style'])  {
			if (!isset($this->styles[$infos['id_style']])) {
				if ($style = ATF::style()->select($infos['id_style'])) {
					$this->styles[$infos['id_style']] = $style;
				}
			}
			$infos["style"] =& $this->styles[$infos['id_style']];
		} else {
			//Si le style est le style STD, on vérifie qu'il n'y ai pas de style sur le A si oui, on le prend en compte
			$id_style=ATF::attr()->select($infos['id_attr'],'id_style');
			if($id_style)$infos["style"] = ATF::style()->select($id_style);
			if (!$infos["style"]) {
				// Style par défaut
				$infos["style"] = $this->default_style;
			}
		}
		if ($set) {
			$this->ATFsetStyle($infos["style"]);
		}
		
	}
		
	/* Génère le tableau de Niveau d'accessibilité par handicap
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2010
	* @param array $infos Infos de l'attribut
	*/
	function niveauAccessibiliteHandicap($infos) {
	
		$style = array("color"=>"000000","size"=>8,"font"=>"arial","bgcolor"=>"c7ebb5");
		$this->ATFsetStyle($style);
		$this->cell(50,10,"Categories de Handicap",1,0,'C',1);
		$this->cell(30,10,"Niveau d'accessibilité",1,0,'C',1);
		$this->cell(0,10,"Commentaires",1,1,'C',1);
		foreach (ATF::pa()->selectChilds($infos['id_pa']) as $k=>$i) {
			
			$style = array("color"=>"000000","size"=>6,"font"=>"arial","decoration"=>"I");
			$this->ATFsetStyle($style);
			
			//Sélectiond el 'image
			switch ($i['pa']) {
				case "Handicap moteur : Fauteuil roulant...":
					$image = __PDF_PATH__."mobilite_chaise.jpg";
				break;
				case "Handicap moteur : Fauteuil roulant avec assistance, poussettes...":
					$image = __PDF_PATH__."mobilite_chaise_accompagne.jpg";
				break;
				case "Handicap moteur : Marches difficiles, femmes enceint...":
				case "Handicap moteur : Marches difficiles, femmes enceintes...":
					$image = __PDF_PATH__."mobilite.jpg";
				break;
				case "Déficience visuelle":
					$image = __PDF_PATH__."vue.jpg";
				break;
				case "Déficience auditive":
					$image = __PDF_PATH__."ouie.jpg";
				break;
				case "Déficience cognitive":
					$image = __PDF_PATH__."mental.jpg";
				break;
				default:
					$image = __PDF_PATH__."mobilite_chaise.jpg";
				break;
			}
			
			
			$this->image($image,$this->getx()+20,$this->gety()+8,10,10);
			$ySave = $this->gety();
			$this->multicell(50,5,$i['pa'],0,'C');
			$this->sety($ySave);
			$this->cell(50,20,"",1,0,'C');
			$this->unsetStyle();
			
			$Ienfants = ATF::pa()->selectChilds($i['id_pa']);
			if (count($Ienfants)!=2) {
				continue;
				print_r($infos);
				print_r($Ienfants);
				die($this->visite['id_visite'].",".$Ienfants[0]['id_attr'].",".$Ienfants[0]['id_parent'].",".$Ienfants[0]['id_pa'].",".$infos['id_vi_pa_multi']);
			}
			if ($Ienfants[0]['type']=="enum") {
				try{
					$vi_pa=ATF::vi_pa()->getEnumReponse($Ienfants[0],$this->visite['id_visite'],$this->dateReference);
					$vi_pa['reponse'] = ATF::pa()->getLibelle($vi_pa);
					$this->ATFsetStyle(ATF::pa()->getStyle($vi_pa));
					$xSave = $this->getx();
					$ySave = $this->gety();
					$this->cell(30,20,"",1,1,'C',$this->fill);
					$this->setxy($xSave,$ySave);
					$this->multicell(30,5,$vi_pa['reponse'],0,'C');
					$this->setxy($xSave+30,$ySave);
				} catch(error $e) {
					$this->cell(30,20,"-",1,0,'C');
				}
			} else {
				try{
					$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$Ienfants[0]['id_attr'],$Ienfants[0]['id_pa'],$Ienfants[0]['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
					$xSave = $this->getx();
					$ySave = $this->gety();
					$this->cell(30,20,"",1,1,'C');
					$this->setxy($xSave,$ySave);
					$this->multicell(30,5,$vi_pa['reponse'],0,'C');
					$this->setxy($xSave+30,$ySave);
				} catch(error $e) {
					$this->cell(30,20,"-",1,0,'C');
				}
			}
			try{
				$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$Ienfants[1]['id_attr'],$Ienfants[1]['id_pa'],$Ienfants[1]['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
				$xSave = $this->getx();
				$ySave = $this->gety();
				$this->cell(0,20,"",1,1,'C');
				$yEnd = $this->gety();
				$this->setxy($xSave,$ySave);
				$this->multicell(0,5,$vi_pa['reponse'],0,'C');
				$this->sety($yEnd);
			} catch(error $e) {
				$this->cell(0,20,"-",1,1,'C');
			}
		}
		$this->searchChild = false;
	}
		
	/* Génère un listing des désordres trié par urgence AVCE LES COUTS
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 10-05-2010
	* @param array $infos Infos de l'attribut
	*/
	function listeDesordresCouts($infos) {
		$this->listeDesordres($infos,true);
	}
	
	/* Génère un listing des désordres trié par urgence
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2010
	* @param array $infos Infos de l'attribut
	*/
	function listeDesordres($id_visite,$cout=false) {

		if (!$id_visite) return false;
		if (ATF::_g('date')) {
			$this->dateReference = substr(ATF::_g('date'),0,4)."-".substr(ATF::_g('date'),4,2)."-".substr(ATF::_g('date'),6,2)." ".substr(ATF::_g('date'),8,2).":".substr(ATF::_g('date'),10,2).":".substr(ATF::_g('date'),12,2);
			//substr(ATF::_g('date'),0,10)." ".substr(ATF::_g('date'),10);
		} else {
			$this->dateReference = date("Y-m-d H:i:s");
		}
		$visite = ATF::visite()->select($id_visite);
		$projet = ATF::gep_projet()->select($visite['id_gep_projet']);
		$desordres = ATF::vi_pa()->getDesordres($visite['id_gep_projet'],$id_visite,false,$this->dateReference);
		$this->Addpage(true);
		$this->Header = ATF::societe()->nom($projet['id_societe'])." - ".$visite['visite'];
		$this->SetAutoPageBreak(true,15);
		$this->setfont("arial","BU",14);
		$this->setmargins(35,35);
		$v = $this->getVersion($id_visite);
		if ($cout) {
			$this->multicell(0,7,"LISTE DES DESORDRES CÔTÉS".($v?" - ".$v:""),"TRL",'C');
		} else {
			$this->multicell(0,7,"LISTE DES DESORDRES".($v?" - ".$v:""),"TRL",'C');
		}
		$this->multicell(0,5,$visite['chorus']?"N° ".$visite['chorus']:"","BRL",'C');
		
		$this->setfont("arial","BI",8);
		$this->multicell(0,5,ATF::societe()->nom($projet['id_societe'])." - ".$visite['visite'].($visite['reference_site']?" (".$visite['reference_site'].")":""),0,'C');
		$this->ln(5);
		$this->setmargins(15,15);
		$this->setfont("arial","",8);
		
		// Récapitulatif visite.
		$this->image(__PDF_PATH__."demo/logo.jpg",10,10,20);
		$x = 180;
		$y = 10;
		$this->photo(ATF::gd()->createThumb("societe",$projet['id_societe'],"logo",200,NULL,"jpg"),$x,$y,20);
		
		$this->sety(35);
		// Rangement par bâtiment
		foreach ($desordres as $k=>$i) {
			$b = ATF::vi_pa()->getBatiment($i);
			//Protection, le désordres doit avoir son propre multi... sinon on l'echappe
			// Pour vérifier on réupère l'id_vi_pa_multi via l'id_vi_pa et on vérifie qu'il est bien raccroché a un A3505
			$idDesordre = ATF::vi_pa()->select(ATF::vi_pa_multi()->select($i["id_vi_pa_multi"],"id_vi_pa"),"id_attr");
//			echo "====>".$b."  <=====> ".$i["id_vi_pa_multi"]." == ".$idDesordre."<======\n";
//			if ($idDesordre!=3505 && $idDesordre!=3831 && $idDesordre!=3862) continue;
//			if (isset($b) && ATF::vi_pa()->select($b,"id_vi_pa_multi")==$i["id_vi_pa_multi"]) {
////				echo "FOCK ====>".$b."  <=====> ".ATF::vi_pa()->select($b,"id_vi_pa_multi")."-".$i["id_vi_pa_multi"]."<======\n";
//				continue;
//			}
//			print_r($i);
			$d[$b][] = $i;
		}
		$ct=0;
//		print_r($d);die();

		// Traitement pour les libellé différents de Batiments
		if ($projet['id_gep_projet']==69) {
			$intitule = "Jardin";	
		} else {
			$intitule = "Bâtiment";	
		}
		ksort($d);

		foreach ($d as $bat=>$des) {
			// On unset les variables pour éviter le bordel dans le mapping
			unset($head,$dataTrieFinal,$dataFinal,$data,$stylesToRange);
			$this->setfont("arial","B",11);
			if ($bat) {
				if ($ct) $this->addpage(true);
				else $ct++;
				$this->multicell(187,5,$intitule." : ".ATF::vi_pa()->select($bat,'reponse')/*."-".$bat."-".ATF::vi_pa()->select($bat,'id_vi_pa_multi')*/,1,'C');
			} else {
				$this->multicell(187,5,"Equipements communs au site",1,'C');
				$ct++;
			}
			if ($visite['id_gep_projet']==26) {
				$this->ln();
			}
			foreach ($des as $c=>$o) {	
	//			if ($o["id_pa"]!=31565) continue;
				if ($visite['id_gep_projet']==26 || $visite['id_gep_projet']==100) {
					if ($cout) {
						$w = array(38,30,28,20,10,12,30,10,10);
						$entetesCout = array("Type","P.U.","Total");
						$x3833 = 111;
						$x3851 = 131;
					} else {
						$w = array(38,50,48,20,10,12);
						$x3833 = 151;
						$x3851 = 171;
					}
					
					
					$enfants = ATF::attr()->selectChilds($o['id_attr']);
					$this->setLeftMargin(15);
					$this->setfont('arial','BU',10);
					$title = ATF::pa()->getLibelle(ATF::pa()->select($o['id_pa'],'id_parent'));
					if (!$flag_title || $title!=$flag_title) {
						$this->multicell(0,10,$title);
						$flag_title = $title;
					}
					$this->setLeftMargin(15);
					$this->setfont('arial','',8);
					$this->setfillcolor(199,235,181);
					//Hauteur des ligne, doit être un multiple de 4
					$c_height = 32;
					if (($this->gety() + $c_height) < 260 && ($title!=$flag_title || !$c)) {
						foreach ($enfants as $k=>$i) {
							$this->setfont('arial','',7);
							$this->cell($w[$k],5,$i['attr'],1,0,'C',1);
						}
						if ($cout && is_array($entetesCout)) {
							$plus = count($enfants);
							foreach ($entetesCout as $k=>$i) {
								$this->setfont('arial','',7);
								$this->cell($w[$k+$plus],5,$i,1,0,'C',1);
							}
						}
						$this->cell(0,5,"",0,1);
					}
		
					if (($this->gety() + $c_height) > 260) {
						$this->addpage();
						$this->setfillcolor(199,235,181);
						foreach ($enfants as $k=>$i) {
							$this->setfont('arial','',7);
							$this->cell($w[$k],5,$i['attr'],1,0,'C',1);
						}
						if ($cout && is_array($entetesCout)) {
							$plus = count($enfants);
							foreach ($entetesCout as $k=>$i) {
								$this->setfont('arial','',7);
								$this->cell($w[$k+$plus],5,$i,1,0,'C',1);
							}
						}
						$this->cell(0,5,"",0,1);
					}
					foreach ($enfants as $k=>$i) {
						$ln = 0;
						switch ($i['id_attr']) {
							case 3832:
//								echo $visite['id_visite'].",".$i['id_attr'].",".$o['id_pa'].",".$i['id_pa'].",".$o['id_vi_pa_multi'].",".$this->dateReference."\n";
								try {
									$vi_pa=ATF::vi_pa()->isAnswered($visite['id_visite'],$i['id_attr'],$o['id_pa'],$i['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
								} catch (error $e) { }
								if ($vi_pa) {
									$this->cell($w[$k],$c_height,"",1,$ln);
									$y = $this->gety();
									$x = 16;
									$y2 = $y+1;
									parent::photo(ATF::gd()->createThumb("vi_pa",$vi_pa['id_vi_pa'],"photo",300,NULL,"jpg"),$x,$y2,36,30);
									$this->setxy(53,$y);
								} else {
									$this->cell($w[$k],$c_height,"",1,$ln);
								}
							break;
							
							case 3833:
								$y = $this->gety();
								$enfants3833 = ATF::attr()->selectChilds($o['id_attr']);
								foreach (ATF::attr()->selectChilds($i['id_attr']) as $kC=>$iC) {
									unset($fill,$reponse);
									$iC['id_vi_pa_multi'] = $o['id_vi_pa_multi'];
									$this->setx($x3833);
									$vi_pa = ATF::vi_pa()->getEnumReponse($iC,$visite['id_visite'],$this->dateReference);
									$reponse = ATF::attr()->select($vi_pa['id_attr'],'attr');
									if ($reponse) {
										switch ($reponse) {
											case "+ de 5 ans":
												$fill = 1;
												$this->setFillColor(16,183,0);
											break;
											case "2 à 4 ans":
												$fill = 1;
												$this->setFillColor(253,255,15);
											break;
											case "1 an":
												$fill = 1;
												$this->setFillColor(255,182,27);
											break;
											case "Immédiat":
												$fill = 1;
												$this->setFillColor(255,86,53);
											break;
											default:
												unset($fill);
											break;
										}
									}
									
									$this->cell($w[$k],$c_height/4,"  ".substr($iC['attr'],0,1)." : ".$reponse,1,1,'L',$fill);
								}
								$this->setxy($x3833+$w[$k],$y);
							break;
							
							case 3851:
								$y = $this->gety();
								unset($fill,$reponse);
								$i['id_vi_pa_multi'] = $o['id_vi_pa_multi'];
								$this->setx($x3851);
								$vi_pa = ATF::vi_pa()->getEnumReponse($i,$visite['id_visite'],$this->dateReference);
								$reponse = ATF::attr()->select($vi_pa['id_attr'],'attr');
									switch ($reponse) {
										case 1:
											$fill = 1;
											$this->setFillColor(16,183,0);
										break;
										case 2:
											$fill = 1;
											$this->setFillColor(253,255,15);
										break;
										case 3:
											$fill = 1;
											$this->setFillColor(255,182,27);
										break;
										case 4:
											$fill = 1;
											$this->setFillColor(255,86,53);
										break;
										default;
											unset($fill);
										break;
									}
								$this->cell($w[$k],$c_height,$reponse,1,1,'C',$fill);
								$this->setxy($x3851+$w[$k],$y);
								unset($fill);
							break;
							
							case 3852:
								if (!$cout) $ln = 1;
								try {
									$vi_pa=ATF::vi_pa()->isAnswered($visite['id_visite'],$i['id_attr'],$o['id_pa'],$i['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
									if ($tempCost = ATF::vi_pa_cout()->getCosts($o['id_vi_pa'],$this->dateReference)) {
										$tC = array_pop($tempCost);
										$vi_pa['reponse'] .= " ".$tC['cout_catalogue']['unite'];
									}
								} catch (error $e) { }
								
								$xSaved = $this->getx();
								$ySaved = $this->gety();
								
								if ($this->getstringwidth($vi_pa['reponse'])+5>$w[$k]) {
									$this->cell($w[$k],$c_height,"",1,0,"C",$fill);
									$this->setxy($xSaved,$ySaved+5);
									$this->multicell($w[$k],5,$vi_pa['reponse'],0,"C");
									if ($ln) {
										$this->sety($ySaved+$c_height);
									}
								} else {
									$this->multicell($w[$k],$c_height,$vi_pa?$vi_pa['reponse']:"-",1,"C",$fill);
								}
								if (!$ln) {
									$this->setxy($xSaved+$w[$k],$ySaved);
								}
							break;
							case 3850:
							case 3911:
								try {
									$vi_pa=ATF::vi_pa()->isAnswered($visite['id_visite'],$i['id_attr'],$o['id_pa'],$i['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
								} catch (error $e) { }
								
								$xSaved = $this->getx();
								$ySaved = $this->gety();
								
								if ($this->getstringwidth($vi_pa['reponse'])+5>$w[$k]) {
									$this->cell($w[$k],$c_height,"",1,0,"C",$fill);
									$this->setxy($xSaved,$ySaved+5);
									$this->multicell($w[$k],5,$vi_pa['reponse'],0,"C");
									if ($ln) {
										$this->sety($ySaved+$c_height);
									}
								} else {
									$this->multicell($w[$k],$c_height,$vi_pa?$vi_pa['reponse']:"-",1,"C",$fill);
								}
								if (!$ln) {
									$this->setxy($xSaved+$w[$k],$ySaved);
								}
								//$this->cell($w[$k],$c_height,$vi_pa?$vi_pa['reponse']:"-",1,$ln,"C",$fill);
							break;
												
						}
					}
					
					if ($cout) {
						//Récupération du coût associé s'il existe
						$cleCout = count($enfants);
						if (ATF::pa()->hasCout($o['id_pa']) || ATF::pa()->getRegle($o['id_pa']) && $o['id_vi_pa']) {
							$temp = "";
							foreach(ATF::vi_pa_cout()->getCosts($o['id_vi_pa'],false,$this->dateReference) as $kCout=>$iCout) {
								if ($kCout) $temp .= " - ";
								$temp .= $iCout['cout_catalogue']['cout_catalogue'];
							}
							$xSaved = $this->getx();
							$ySaved = $this->gety();
							
							$this->cell($w[$cleCout],$c_height,"",1,0,"C",$fill);
							$this->setxy($xSaved,$ySaved+5);
							$this->multicell($w[$cleCout],5,$temp,0,"C");
							
							
							$xSaved = $xSaved+$w[$cleCout];
							$cleCout++;
							$this->setxy($xSaved,$ySaved);
							$this->cell($w[$cleCout],$c_height,"",1,0,"C",$fill);
							$this->setxy($xSaved,$ySaved+5);
							$this->multicell($w[$cleCout],5,ATF::vi_pa()->getCost($o['id_vi_pa'],true,$this->dateReference),0,"C");
							
							$xSaved = $xSaved+$w[$cleCout];
							$cleCout++;
							$this->setxy($xSaved,$ySaved);
							$this->cell($w[$cleCout],$c_height,"",1,0,"C",$fill);
							$this->setxy($xSaved,$ySaved+5);
							$this->multicell($w[$cleCout],5,ATF::vi_pa()->getCost($o['id_vi_pa'],false,$this->dateReference),0,"C");
							
							$this->sety($ySaved+$c_height);
	
						} else {
							$this->cell($w[$cleCout],$c_height,"",1,0,"C",$fill);
							$cleCout++;
							$this->cell($w[$cleCout],$c_height,"",1,0,"C",$fill);
							$cleCout++;
							$this->cell($w[$cleCout],$c_height,"",1,1,"C",$fill);
						}
					}
	



//					if (in_array($o['id_pa'],$alreadyPut)) {
//						continue;
//					}
//					$this->visite['id_visite'] = $visite['id_visite'];
//					$pa = $attr = array();
//					$pa = ATF::pa()->select($o['id_pa']);
//					$attr = ATF::attr()->select($o['id_attr']);
//					$dS = array_merge($attr,$pa);
//					$this->desordre3831($dS,($c?false:true),$cout);
//					$alreadyPut[] = $dS['id_pa'];
					continue;
				}
			
				$data[$c]["id_pa"] = $o["id_pa"];
//				$data[$c]["id_vi_pa"] = $o["id_vi_pa"];
				$Answerd[$c] = false;
				$current = ATF::pa()->select($o['id_pa']);
				// Enfants dans les PA
				$enfants = ATF::pa()->selectChilds($o['id_pa']);
				if (!$enfants) {
					// Enfants dans les ATTR
					$enfants = ATF::attr()->selectChilds($o['id_attr']);
				}
				try{
					$currentVIPA = ATF::vi_pa()->isAnswered($id_visite,$current['id_attr'],$current['id_pa'],$current['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
				} catch(error $e) { }
				foreach ($enfants as $k=>$i) {
					unset($vi_pa);
					//Init des entêtes
					$head[$k] = $i['pa']?$i['pa']:$i['attr'];
					//Init des données
					$currentIDPA = $i['id_pa']?$i['id_pa']:$o['id_pa'];
					try {
						if ($i['type']=="enum") {
							$enfantsENUM = ATF::pa()->selectChilds($i['id_pa']);
							if (!$enfantsENUM) {
								$enfantsENUM = ATF::attr()->selectChilds($i['id_attr']);
							}
							foreach ($enfantsENUM as $cle=>$obj) {
								unset($answerd);
								if ($answerd = ATF::vi_pa()->isAnswered($id_visite,$obj['id_attr'],$obj['id_pa']?$obj['id_pa']:$o['id_pa'],$obj['id_pa'],$o['id_vi_pa_multi'],$this->dateReference)) {
									if ($answerd['reponse']==1 && strtotime($vi_pa['date'])<strtotime($answerd['date'])) {
										$vi_pa = $answerd;
										$vi_pa['reponse'] = $answerd['id_pa']?ATF::pa()->select($answerd['id_pa'],"pa"):ATF::attr()->select($answerd['id_attr'],"attr");
									}
								}
							}
														
						} else {
							$vi_pa=ATF::vi_pa()->isAnswered($id_visite,$i['id_attr'],$currentIDPA,$i['id_pa'],$o['id_vi_pa_multi'],$this->dateReference);
							if (ATF::$usr->get('login')=="qjanon") {
								$vi_pa['reponse'].= " - ".$currentVIPA['id_vi_pa'];
							}
						}
						// Traitement spéciale pourl es projet 59 - 62 conformément au ticket 3297
						if (($projet['id_gep_projet']==49 || $projet['id_gep_projet']==31) && $i['id_attr']==3506) {
							switch($vi_pa['reponse']) {
								case 1:
									$vi_pa['reponse'] = "TS";
								break;
								case 2:
									$vi_pa['reponse'] = "S";
								break;
								case 3:
									$vi_pa['reponse'] = "PS";
								break;
								case 4:
									$vi_pa['reponse'] = "M";
								break;
							}
						}
						if ($i['id_attr']==3511 || $i['id_attr']==3867) {
							if (preg_match("/./",$vi_pa['reponse'])) {
								$vi_pa['reponse'] = number_format($vi_pa['reponse'],0,"."," ");
							} elseif ($vi_pa['reponse']) {
								$vi_pa['reponse'] = number_format($vi_pa['reponse'],1,"."," ");
							}
							if ($tempCost = ATF::vi_pa_cout()->getCosts($currentVIPA['id_vi_pa'],$this->dateReference)) {
								$tC = array_pop($tempCost);
								$vi_pa['reponse'] .= " ".$tC['cout_catalogue']['unite'];
							}
							
						}
					} catch(error $e) { 
					}
					if ($i['type'] == 'photo' && $vi_pa['reponse']) {
						$Answerd[$c] = true;
						$data[$c][] = "IMAGE:".ATF::gd()->createThumb("vi_pa",$vi_pa['id_vi_pa'],"photo",150,NULL,"jpg")."::::30";
						$stylesToRange[$c][] = "";
					} elseif ($i['type'] == 'date' && $vi_pa['reponse']) {
						$Answerd[$c] = true;
						$data[$c][] = date($this->dateFormat,strtotime($vi_pa['reponse']));
						$stylesToRange[$c][] = ATF::style()->select(ATF::attr()->select($vi_pa['id_attr']));
					} elseif ($vi_pa['reponse']) {
						$Answerd[$c] = true;
						$data[$c][] = $vi_pa['reponse'];
						$stylesToRange[$c][] = ATF::pa()->getStyle($vi_pa);						
					} else {
						$data[$c][] = "";
						$stylesToRange[$c][] = "";
					}
				}
				if (!$c && $cout) {
					$head[] = "Type";
					$head[] = "P.U.";
					$head[] = "Total";
				}
				if ($Answerd[$c]) {
					if ($cout) {
						//Récupération du coût associé s'il existe
						if (ATF::pa()->hasCout($current['id_pa']) || ATF::pa()->getRegle($current['id_pa']) && $currentVIPA['id_vi_pa']) {
							$temp = "";
							foreach(ATF::vi_pa_cout()->getCosts($currentVIPA['id_vi_pa'],$this->dateReference) as $kCout=>$iCout) {
								if ($kCout) $temp .= " - ";
								$temp .= $iCout['cout_catalogue']['cout_catalogue'];
							}
							$data[$c][] = $temp;
							$data[$c][] = number_format(ATF::vi_pa()->getCost($currentVIPA['id_vi_pa'],true,$this->dateReference),0,","," ");
							$data[$c][] = number_format(ATF::vi_pa()->getCost($currentVIPA['id_vi_pa'],false,$this->dateReference),0,","," ");
						} else {
							$data[$c][] = "-";
							$data[$c][] = "-";
							$data[$c][] = "-";
						}
					}
				 } else {
					unset($data[$c],$stylesToRange[$c]);
				 }
	
			}
			
			
			switch ($visite['id_gep_projet']) {
				case 34: // 09.114 - SNCF Région Est
					if ($cout) {
						$w = array(40,30,17,30,11,9,30,10,10);
					} else {
						$w = array(40,38,17,50,20,15);
					}
					//Tri par urgence !
					foreach ($data as $k=>$i) {
						$dataTrie["Urgent"] = $dataTrie["2 à 3 ans"] = $dataTrie["3 à 5 ans"] = $dataTrie["NA"] = array();
						// Rangement par éléments de références.
						if ($ref = ATF::pa()->getReferenceElement($i['id_pa'])) {
							$keyOnTabs = ATF::pa()->getLibelle($ref);
						} else {
							$keyOnTabs = 0;
						}
						unset($i['id_pa']);
						//$i[1] = utf8_decode($i[1]);
						$index = ATF::pa()->getIndexUrgence($visite['id_gep_projet']);
						if ($i[$index]!="3 à 5 ans" && $i[$index]!="2 à 3 ans" && $i[$index]!="Urgent" && $i[$index]!="") {
							die("1. problème dans l'identification de l'urgence. ".$i[$index]);
						}
						if (isset($i['id_pa'])) unset($i['id_pa']);
						if (!$i[$index]) {
							$dataTrie["NA"] = $i;			
						} else {
							$dataTrie[$i[$index]] = $i;
							$dataTrie[$i[$index]]['style'] = $stylesToRange[$k];
						}
						$dataTrieFinal[$keyOnTabs][] = array_merge($dataTrie["Urgent"],$dataTrie["2 à 3 ans"],$dataTrie["3 à 5 ans"],$dataTrie["NA"]);
					}
					
				break;
				default:
					if ($cout) {
						$w = array(40,29,26,16,9,11,26,15,15);
					} else {
						$w = array(40,55,50,17,10,15);
					}
					foreach ($data as $k=>$i) {
						//Tri par urgence !
						$dataTrie["0 à 2 ans"] = $dataTrie["2 à 5 ans"] = $dataTrie["5 à 10 ans"] = $dataTrie["plus de 10 ans"] = $dataTrie["NA"] = array();
						// Rangement par éléments de références.
						if ($ref = ATF::pa()->getReferenceElement($i['id_pa'])) {
							$keyOnTabs = ATF::pa()->getLibelle($ref);
						} else {
							$keyOnTabs = 0;
						}
						unset($i['id_pa']);
						$index = ATF::pa()->getIndexUrgence($visite['id_gep_projet']);
						if ($i[$index]!="5 à 10 ans" && $i[$index]!="2 à 5 ans" && $i[$index]!="0 à 2 ans" && $i[$index]!="" && $i[$index]!="plus de 10 ans") {
							print_r($data);
							print_r($i);
							die("2. problème dans l'identification de l'urgence. ".$i[$index]);
						}
						if (isset($i['id_pa'])) unset($i['id_pa']);
						if (!$i[$index]) {
							$dataTrie["NA"] = $i;			
						} else {
							$dataTrie[$i[$index]] = $i;
							$dataTrie[$i[$index]]['style'] = $stylesToRange[$k];
						}
						$dataTrieFinal[$keyOnTabs][] = array_merge($dataTrie["0 à 2 ans"],$dataTrie["2 à 5 ans"],$dataTrie["5 à 10 ans"],$dataTrie["plus de 10 ans"],$dataTrie["NA"]);
						
					}
				break;
			}
			
			if ($cout && $visite['id_gep_projet']==26) {
				$this->ln(5);
				$this->setfont('arial','BI',8);
				$this->multicell(0,5,"* Tous les coûts sont exprimés en Euros Hors Taxes");
			}
			if (!$dataTrieFinal || $visite['id_gep_projet']==26) continue;
			//Re attribution des clé dans le tableau des styles
			if (is_array($dataTrieFinal)) {
				foreach ($dataTrieFinal as $k_=>$i_) {
					if (!is_array($i_)) continue;
					$this->setfont("arial","BU",11);
					$this->multicell(0,10,$k_?$k_:"Sans Eléments de référence");
					$this->setfont("arial","",8);
					if (isset($dataFinal)) {
						unset($dataFinal,$styles);
					}
					foreach ($i_ as $k=>$i) {
						$styles[$k] = $i['style'];
						if (is_array($i['style'])) {
							unset($i['style']);
						}
						$dataFinal[$k] = $i;
					}
					$this->tableau($head,$dataFinal,$w,30,$styles);
				}
			}
			
			if ($cout) {
				$this->ln(5);
				$this->setfont('arial','BI',8);
				$this->multicell(0,5,"* Tous les coûts sont exprimés en Euros Hors Taxes");
			}
			
			$ct++;
		}
//			print_r($dataTrieFinal);die("PHOQUE");
		
		
//		print_r($head);
//		print_r($data);
//		print_r($w);
//		print_r($styles);
//		die();
		
		return true;
	}
	
	/* Permet de détecter s'il y a des réponse parmis les enfants d'un attribut sans prendre en compte les sans réponses
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2010-04-21
	* @param array $infos Infos de l'attribut
	* @return bool TRUE or FALSE
	*/
	function childsAnswerd($infos) {
		if (!$infos['style']) {
			$infos['style'] = ATF::style()->select($infos['id_style']);
		}
		// Si tableau on envoi true
		if ($infos['style']['format']=='table') {
			return true;
		}
		$enfants = ATF::pa()->selectChilds($infos['id_pa']);
		if (!$enfants) {
			//Enfants dans les ATTR
			$enfants = ATF::attr()->selectChilds($infos['id_attr']);
		}
		$onlySR = true;
		foreach ($enfants as $k=>$i) {
			//Sans reponse a gérer de facon recursive...
			if ($i['type']!="sans_reponse") $onlySR=false;
			if ($i['option']) return true;
		}
		
		
		if ($onlySR) {
			if (!$enfants) return true;
			foreach ($enfants as $k=>$i) {
				$return = self::childsAnswerd($i);
				if ($return) return $return;
			}
		}
		
//		if ($infos['id_pa']==20798) {
//			echo "================HOP================\n<br>";
//			print_r($infos);
//		}
		foreach ($enfants as $k=>$i) {
			$i['id_vi_pa_multi'] = $infos['id_vi_pa_multi'];
//			if ($infos['id_pa']==20798) {
//				print_r($i);
//			}
			//Sans reponse géré de facon recursive...
			if ($i['type']=="sans_reponse") {
				$return = self::childsAnswerd($i);
				if ($return) return $return;
			}
			//if ($i['type']=="sans_reponse") return true;	
			//Multi a gérer en regardant chacunes des occurences
			if ($i['multi']) {
				$multi = ATF::vi_pa()->getDistinct($this->visite['id_visite'],$i['id_attr'],$i['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
//				if ($infos['id_pa']==20798) {
//					echo "==>MULTI DANS LA PLACE<==\n<br>";
//				}
				if (!$multi) continue;
				else return true;
			}
			
			//if (!$i['id_pa']) $i['id_pa'] = $this->id_ppa;
			if ($i['type']=="enum") {
				try {
					$vi_pa = ATF::vi_pa()->getEnumReponse($i,$this->visite['id_visite'],$this->dateReference);
				} catch (error $e) { }
//				if ($infos['id_pa']==20798) {
//					echo "==>ENUM DANS LA PLACE<==\n<br>";
//				}
			} else {
				try {
					$vi_pa=ATF::vi_pa()->isAnswered($this->visite['id_visite'],$i['id_attr'],$i['id_pa']?$i['id_pa']:$infos['id_pa'],$i['id_pa'],$infos['id_vi_pa_multi'],$this->dateReference);
				} catch (error $e) { }
//				if ($infos['id_pa']==20798) {
//					echo "==>".$this->visite['id_visite'].",".$i['id_attr'].",".($i['id_pa']?$i['id_pa']:$infos['id_pa']).",".$i['id_pa'].",".$infos['id_vi_pa_multi'].",".$this->dateReference."<==\n<br>";
//				}
			}
//			if ($infos['id_pa']==20798) {
//				print_r($vi_pa);
//			}
			if ($vi_pa['reponse']) {
//				if ($infos['id_pa']==20798) {
//					print_r($vi_pa);
//					die("hop".$vi_pa['reponse']."hop");
//				}
				return true;							
			}
//			if ($infos['id_pa']==20798) {
//				echo "======================================\n";
//			}
		}
//		if ($infos['id_pa']==20798) {
//			print_r($infos);
//			die("NON REPONDU\n");
//		}
		return false;
	}
		
	/* Initialise les styles des photos
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2010
	* @param int $id_style
	*/
	function initPhotoVarByStyle($id_style) {
		if (!$id_style) return;
		$fullPage = ATF::style()->select($id_style,"fullPage");
		if ($fullPage) {
			$this->addpage(true);
			$x = 15;
			$y = 10;
			$w = 180;
			$h = 260;
			unset($this->nbPhoto);
			$ln = true;
		} else {
			$nbPerLines = ATF::style()->select($id_style,"nbPerLines");
			switch ($nbPerLines) {
				case 1: // 1 photo par ligne
					if ($this->gety() > 245 && !$this->ySavedForPhoto) {
						$this->addpage(true);
					}
					$x = 70;
					$y = $this->gety();
					$w = 65;
					$h = 40;
					unset($this->nbPhoto);
					$ln = true;
				break;
				
				case 2: // 2 photo par ligne
//					echo "===>".$this->gety()." ====>".$this->ySavedForPhoto."\n";
					
					if ($this->gety() > 240 && !$this->ySavedForPhoto) {
						$this->addpage(true);
					}
					$this->nbPhoto++;
					if ($this->nbPhoto==2) {
						$ln = true;
						unset($this->nbPhoto);
					}
					$w = 65;
					$h = 40;
//							echo "hop ".$infos['pa']." -> ".$this->nbPhotoPrint." -> ".$this->nbPhoto."\n";
					if (!$this->nbPhotoPrint) {
						$this->ySavedForPhoto = $this->gety();
						$x = 40;
						$y = $this->ySavedForPhoto;
					} elseif ($this->nbPhotoPrint==1) {
						$x = 45+$w;
						$y = $this->ySavedForPhoto;
					}
				break;
				
				case 3: // 3 photo par ligne
					if ($this->gety() > 245 && !$this->ySavedForPhoto) {
						$this->addpage(true);
					}
					$this->nbPhoto++;
					if ($this->nbPhoto==3) {
						unset($this->nbPhoto);
						$ln = true;
					}
					$w = 65;
					$h = 40;

					if (!$this->nbPhotoPrint) {
						$this->ySavedForPhoto = $this->gety();
						$x = 15;
						$y = $this->ySavedForPhoto;
					} elseif ($this->nbPhotoPrint==1) {
						$x = 15+$w;
						$y = $this->ySavedForPhoto;
					} elseif ($this->nbPhotoPrint==2) {
						$x = 15+(2*$w);
						$y = $this->ySavedForPhoto;
					}
					
				break;
				
				case 4: // 4 photo par ligne
				default: // Par défaut 4 par ligne
					if ($this->gety() > 245 && !$this->ySavedForPhoto) {
						$this->addpage(true);
					}
					$this->nbPhoto++;
					if ($this->nbPhoto==4) {
						unset($this->nbPhoto);
						$ln = true;
					}
					$w = 45;
					$h = 30;
		
					if (!$this->nbPhotoPrint) {
						$this->ySavedForPhoto = $this->gety();
						$x = 15;
						$y = $this->ySavedForPhoto;
					} elseif ($this->nbPhotoPrint==1) {
						$x = 15+$w;
						$y = $this->ySavedForPhoto;
					} elseif ($this->nbPhotoPrint==2) {
						$x = 15+(2*$w);
						$y = $this->ySavedForPhoto;
					} elseif ($this->nbPhotoPrint==3) {
						$x = 15+(3*$w);
						$y = $this->ySavedForPhoto;
					}
				break;
			}
		}
		return array("x"=>$x,"y"=>$y,"ln"=>$ln,"w"=>$w,"h"=>$h);
	}
	
	/* Initialise les styles des plans
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 2010
	* @param int $id_style
	*/
	function initPlanVarByStyle($id_style) {
		if (!$id_style) return;
		$fullPage = ATF::style()->select($id_style,"fullPage");
		if ($fullPage) {
			$this->addpage(true);
			$x = 15;
			$y = 10;
			$w = 180;
			$h = 260;
			unset($this->nbPlan);
			$ln = true;
		} else {
			$nbPerLines = ATF::style()->select($id_style,"nbPerLines");
			switch ($nbPerLines) {
				case 2: // 2 Plan par ligne
					if ($this->gety() > 200 && !$this->ySavedForPlan) {
						$this->addpage(true);
					}
					$this->nbPlan++;
					if ($this->nbPlan==2) {
						$ln = true;
						unset($this->nbPlan);
					}
					$w = 90;
					$h = 70;
					if (!$this->nbPlanPrint) {
						$this->ySavedForPlan = $this->gety();
						$x = 15;
						$y = $this->ySavedForPlan;
					} elseif ($this->nbPlanPrint==1) {
						$x = 16+$w;
						$y = $this->ySavedForPlan;
					}
				break;
				default: // Par défaut 1 par ligne
//					echo "\nhop ".$this->gety()." -> ".$this->nbPlanPrint." -> ".$this->nbPlan."->".$this->ySavedForPlan."\n";
					if ($this->gety() > 140) {
						$this->addpage(true);
//						echo "ADDPAGE HERE\n";
					}
					$x = 15;
					$y = $this->gety();
					$w = 140;
					$h = 180;
//					echo "hip ".$x." -> ".$y."\n";
					unset($this->nbPlan);
					$ln = true;
				break;
			}
		}
		
		return array("x"=>$x,"y"=>$y,"ln"=>$ln,"w"=>$w,"h"=>$h);
		
	}
		
	/** Bipasse le addpage pour gérer les pages blanches non desirés
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	function addpage($force=false) {		
		if ($this->dataOnPage || $force) {
			parent::addpage();
		}
		$this->dataOnPage=0;
	}

	/** Bipasse le cell pour gérer un compteur d'élément affiché sur la page, histoire de manager les pages blanches
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='') {
		parent::cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
		if ($txt) {
			$this->dataOnPage++;
		}
		
	}
	
	public function CheckBox($fill) {
		
		return parent::CheckBox($fill,2);	
	}
}
?>