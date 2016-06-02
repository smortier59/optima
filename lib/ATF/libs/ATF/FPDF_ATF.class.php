<?php
include_once __ATF_PATH__.'libs/fpdf/fpdf.php';
include_once __ATF_PATH__.'libs/fpdfi/fpdi.php';

/**
* @author Quentin JANON <qjanon@absystech.fr>
* @package ATF
* @date 08-07-2010
*/ 
class FPDF_ATF extends FPDI {
	//internal attributes
	var $HREF; //! string
	var $pgwidth; //! float
	var $fontlist; //! array 
	var $issetfont; //! bool
	var $issetcolor; //! bool
	var $titulo; //! string
	var $oldx; //! float
	var $oldy; //! float
	var $B; //! int
	var $U; //! int
	var $I; //! int
	
	var $Rentete = 199;
	var $Gentete = 235;
	var $Bentete = 181;
	
	var $REnteteTextColor = 0;
	var $GEnteteTextColor = 0;
	var $BEnteteTextColor = 0;
	
	var $tmpFiles = array();
	var $repeatEntete = false;
	
	/**
	* Style par défaut
	* @var array
	*/
	private $defaultStyle = array(
		"style" =>"[TEXTE] Standard"
		,"format" =>"standard"
		,"decoration" => ""
		,"size" => 8
		,"color" => 000000
		,"font" => "arial"
		,"border" => ""
		,"align" => "J"
		,"width" => ""
		,"height" => 4
		,"bgcolor" => ""
		,"numerotation" => "non"
		,"bookmark" => "non"
		,"retrait" => ""
		,"addpage" => "non"
		,"ln" => ""
		,"fullPage" => ""
		,"nbPerLines" => 1
	);

	private $defaultHeadStyle = array(
		"style" =>"[TEXTE] Standard"
		,"format" =>"standard"
		,"decoration" => ""
		,"size" => 8
		,"color" => 000000
		,"font" => "arial"
		,"border" => 1
		,"height" => 4
		,"align" => "J"
		,"width" => ""
		,"bgcolor" => ""
		,"numerotation" => "non"
		,"bookmark" => "non"
		,"retrait" => ""
		,"addpage" => "non"
		,"ln" => ""
		,"fullPage" => ""
		,"nbPerLines" => 1
	);
	/**
	* Infos diverses concernant le PDF (style, etc...)
	* @var array
	*/
	private $infos = array();
	
	function generic($function,$id,$output=false,&$s=NULL,$temp=false) {
		//Ré-instancier l'objet afin que PDF se ré-initialise
		self::__construct();
//log::logger("=============================","pdf");
//log::logger("Debut de PDF","pdf");
//log::logger("Fonction : ".$function,"pdf");
//log::logger("Params : ","pdf");
//log::logger($params,"pdf");
		$this->setAuthor("AbsysTech (Quentin JANON <qjanon@absystech.fr>)");
		$this->setCreator("Optima");
		
		$this->previsu = $temp;
		try {
		  $this->$function($id,$s);
        } catch (errorATF $e) {
            echo "ERREUR DE GENERATION DU PDF, VEUILLEZ VOUS RENDRE SUR LE PORTAIL HOTLINE POUR OUVRIR UN TICKET.";
            
            $trace = $e->getTrace();
            $body = "ERREUR DE GENERATION DU PDF : ".$e->getCode()." -> ".$e->getMessage();
            ob_start();
            print_r($trace[0]);
            print_r($trace[1]);
            print_r($trace[2]);
            $body .= ob_get_clean();
         
            // Envoi du mail
            $mail = new mail(array(
                    "objet"=>"[IMPORTANT] PDF CORE PANIC - ".ATF::$project." ".ATF::$codename
                    ,"body"=>$body
                    ,"from"=>"ATF 5 <atf@absystech.fr>"));
            $mail->send(ATF::$debugMailbox,true);            
            
            return false;
        }
		
		//log::logger("Fin d'execution de la fonction, lancement du Output","pdf");
		if ($output === true) {
			$this->close();
			return $this->buffer;
		} elseif ($output) $this->Output($output);
		else {
			ob_clean();
			$this->Output();
		}
//log::logger("FIN du PDF","pdf");
//log::logger("=============================","pdf");

	}	
	
	function html2rgb($color) {
		if ($color[0] == '#') $color = substr($color, 1);
		
		if (strlen($color) == 6)	list($r, $g, $b) = array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
		elseif (strlen($color) == 3) list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else return false;
		
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		
		return array("R"=>$r, "G"=>$g, "B"=>$b);
	}

	//conversion pixel -> millimeter in 150 dpi
	function px2mm($px){
		return $px*25.4/150;
	}
			
	//conversion pixel -> millimeter in 150 dpi
	function mm2px($mm){
		return $mm*5.9;
	}
				
	/** Génère un tableau en PDF.
	* Gère les images en écrivant le code suivant dans la cellule :
		"IMAGE:".[PHOTO PATH]:[COORDONNEE X]:[COORDONNE Y]:[WIDTH]:[HEIGHT]  (Sans les crochets)
	* Gère le passage à la ligne dans les cellules si le texte est trop long.
	* Gère la pagination, sil e tableau est plus grand qu'une page il reprend sur la page suivante en raffichant les entêtes
	* Gère une batterie d'option qui permet notamment de mettre une couleur de fond a une cellule
	* @author Quentin JANON
	* @param array $head Tableau contenant les entêtes (en item)
	* @param array $data Tableau contenant les données : Chaque entrée va être considéré comme une ligne du tableau
	* @param int/array $width INT : Largeur du tableau (par défaut 188); Si ARRAY : détails de la largeur de chaque cellule
	* @param int $c_height Hauteur des lignes dans le tableau
	* @return boolean false si ça s'est mal passé
	*/
	function tableau($head,$data,$width=false,$c_height=5,$style=false,$limitBottomMargin=270,$hMIN=false) {
		$flagA3BreakPage = false;
        if (!$limitBottomMargin) $limitBottomMargin = 270;
		if (!$width) $width=180;
		//$this->setfont('arial','',6);
		// verfication pour voir si on doit changer de page pour éviter un tableau couper en deux. //
		$height_max = count($data)*$c_height + 5;
//		if (($this->gety() + $height_max) > 260) {
//			$this->addpage();
//		}
		// On s'occupe d'initialiser les largeurs de colonnes
		if (is_array($width)) {
			foreach ($width as $k=>$i) {
				$w[$k] = $i;
			}
		} else {
			for ($z=0;$z<count($head);$z++) {
				$w[$z] = $width/count($head);
			}
		}
		
		foreach ($w as $k=>$i) {
			$totalWidth += $i;
		}
		
		// On affiche les entêtes
		// Il faut vérifier que les entête + la première ligne n'entraine pas de saut de page, si oui il faut l'anticiper.
		
		if (is_array($head)) {
			self::head($head,$w,$c_height,$limitBottomMargin);
		}
		
		if (is_array($data)) {
			foreach ($data as $k=>$i) {
				unset($details);
				if ($i['details'] && $i['details']!="") {
					$details = $i['details'];
					unset($i['details']);	
				} else {
					if (isset($i['details'])) unset($i['details']);	
					if (isset($style[$k]['details'])) unset($style[$k]['details']);	
				}
				
				$H = $this->maxHeightLine($i,$w,$style[$k]);
				if ($c_height>5*$H['max']) {
					$flagH = $this->gety()+$c_height;
				} else {
					$flagH = $this->gety()+5*$H['max'];
				}

				if ($flagH >= $limitBottomMargin) {
					if ($this->A3) {
							if (!$flagA3BreakPage) {
								$this->SetLeftMargin(220);
								$this->sety($this->tMargin+10);
								$flagA3BreakPage = true;
							} else {
								$this->addpage();
								$this->setLeftMargin(15);
								$this->sety($this->tMargin+10);
								$flagA3BreakPage = false;
							}
					} else {
						$this->addpage();
					}
					if ($this->repeatEntete && is_array($head)) {
						self::head($head,$w,$c_height,$limitBottomMargin);
					}
				}

				$xSaved = $this->getx();
				$ySaved = $this->gety();
				
				foreach ($i as $k_=>$i_) {
					// Y a t-il un style pour la donnée ?
					if ($style[$k][$k_]) {
						$this->ATFsetStyle($style[$k][$k_]);
					} else {
						$this->ATFsetStyle($this->defaultStyle);
					}
					$this->height = $c_height;
					if (!$style[$k][$k_]['border'] && $style[$k][$k_]['border']!==0) {
						$this->border = 1;
					}
					if (!$style[$k][$k_]['align']) {
						$this->align = "C";
					}
					// Décalage en X et repositionnement en Y sauf pour la premiere colonne
					if ($k_ && $k_!="details") {
						$xSaved += $w[$k_-1];
						$this->setxy($xSaved,$ySaved);
					}
                    
                    if ($c_height<=5) {
                        $multiplicateur = $c_height;  
                    } else {
                        $multiplicateur = 5;  
                    }
                    
					if (preg_match("/IMAGE\:/",$i_)) {
						if (preg_match("/http/",$i_)) {
							$i_ = str_replace("http://","[HTTP]",$i_);
						}
						$image = explode(":",$i_);
						// Récupération du format de fichier
						if (preg_match("/.jpg/",$image[1]) || preg_match("/.jpeg/",$image[1])) {
							$format = "jpg";
// C'est bien de prévoir les png mais dans tous les cas le FPDF ne gère pas la transparence donc on est de la baise...
// Ni les GIF.
						} elseif (preg_match("/.png/",$image[1])) {
							$format = "png";
//						} elseif (preg_match("/.gif/",$image[1])) {
//							$format = "gif";
						}
						
						$x = $image[2]?$image[2]:$xSaved;
						// Calcul rapide de la marge
						if (!$image[4]) $image[4]=$w[$k_];
						$wR = $w[$k_]-$image[4];
						$x += $wR/2;
						$y = $image[3]?$image[3]:$ySaved;
						// Calcul rapide de la marge
						$yR = $c_height-$image[5];
						$y += $yR/2;
						$this->height = $c_height;
						$this->photo(str_replace("[HTTP]","http://",$image[1]),$x,$y,$image[4]?$image[4]:$w[$k_],$image[5],$format,0);
						$this->setxy($xSaved,$ySaved);
						if ($this->height<($multiplicateur*$H['max'])) $this->height = $multiplicateur*$H['max'];
						$this->multicell($w[$k_],$this->height,"",$this->border);
					} elseif (is_int($k_)) {
						// Gestion des sauts de lignes
						$i_ = str_replace("[nl]","\n",$i_);

						// Gestion multiligne
						// Multiligne sur plusieurs cellule maisp	as au max
						if ($H['max']>1 && $H['max']>$H[$k_] && $H[$k_]!=1) {
                            
							if ($c_height > $multiplicateur*$H['max']) {

								$this->multicell($w[$k_],$c_height,"",$this->border,'C',$this->fill);
								$this->setxy($xSaved,$ySaved);
								$this->multicell($w[$k_],$multiplicateur,$i_,0,$this->align);
								$this->sety($ySaved+$c_height);
							} else {
								$this->multicell($w[$k_],$multiplicateur*$H['max'],"",$this->border,'C',$this->fill);
								$this->setxy($xSaved,$ySaved);
								$this->multicell($w[$k_],$multiplicateur,$i_,0,$this->align);
								$this->sety($ySaved+$multiplicateur*$H['max']);
							}
						
						// Multiligne : Cellule  concerné par le chagement de ligne
						} elseif ($H['max']>1 && $H['max']==$H[$k_]) {
							if ($c_height > $multiplicateur*$H['max']) {
								$this->multicell($w[$k_],$c_height,"",$this->border,'C',$this->fill);
								$this->setxy($xSaved,$ySaved);
								$this->multicell($w[$k_],$multiplicateur,$i_,0,$this->align);
								$this->sety($ySaved+$c_height);
							} else {
								$this->multicell($w[$k_],$H[$k_]*$multiplicateur,"",$this->border,'C',$this->fill);
								$this->setxy($xSaved,$ySaved);
								$this->multicell($w[$k_],$multiplicateur,$i_,0,$this->align);
								$this->sety($ySaved+($this->gety()-$ySaved));
							}
						// Multiligne : Cellule impacté par sa cellule soeur.
						} elseif($H['max']>1 && $H['max']!=$H[$k_]) {
							if ($c_height > $multiplicateur*$H['max']) {
								$this->multicell($w[$k_],$c_height,"",$this->border,'C',$this->fill);
								$this->setxy($xSaved,$ySaved);
								$this->multicell($w[$k_],$multiplicateur,$i_,0,$this->align);
								$this->sety($ySaved+$c_height);
							} else {
								$this->multicell($w[$k_],$multiplicateur*$H['max'],$i_,$this->border,$this->align,$this->fill);
							}
						// Pas de multiligne
						} elseif ($H['max']==1) {
							$this->multicell($w[$k_],$this->height,$i_,$this->border,$this->align,$this->fill);
						}
					}
				}
				
				if ($details) {
					$this->ATFsetStyle($style[$k]['details']);
					$this->multicell($totalWidth,5,$details,$this->border,$this->align,$this->fill);	
				}
			}
		}//die();
		
		if ($hMIN && $hMIN>$this->gety()) {
			$c=0;
			while ($hMIN>$this->gety()) {
				$xSaved = $this->getx();
				$ySaved = $this->gety();
				foreach ($w as $k=>$i) {
					if ($k) {
						$xSaved += $w[$k-1];
						$this->setxy($xSaved,$ySaved);
					}
					$this->multicell($w[$k],$c_height,"",$this->border,'',$this->fill);
				}
				$c++;
			}
		}
	}
	
	function head($head,$w,$c_height,$limitBottomMargin) {
			if (isset($headStyle)) unset($headStyle);
			if ($this->headStyle) {
				if (is_array($this->headStyle)) {
					$headStyle = $this->headStyle;
					$this->ATFsetStyle($headStyle[0]);
				}/* else {
					$this->ATFsetStyle($this->headStyle);
				}*/
			} else {
				$this->ATFsetStyle($this->defaultHeadStyle);
			}
			// Couleur fond entête
			$this->setfillcolor($this->Rentete,$this->Gentete,$this->Bentete);
			$this->settextcolor($this->REnteteTextColor,$this->GEnteteTextColor,$this->BEnteteTextColor);
			$H = $this->maxHeightLine($head,$w);
//			if ($c_height>$c_height*$H['max']) {
//				$flagH = $this->gety()+$c_height;
//			} else {
//			}
			$flagH = $this->gety()+$c_height/**$H['max']*/;
			if ($flagH+$c_height > $limitBottomMargin) {
				$this->addpage();
				if ($this->headStyle) {
					if (is_array($this->headStyle)) {
						$headStyle = $this->headStyle;
						$this->ATFsetStyle($headStyle[0]);
					}/* else {
						$this->ATFsetStyle($this->headStyle);
					}*/
				} else {
					$this->ATFsetStyle($this->defaultHeadStyle);
				}
				$this->setfillcolor($this->Rentete,$this->Gentete,$this->Bentete);
				$this->settextcolor(0,0,0);
			}
			$xSavedForTheEnd = $this->getx();
			$xSaved = $this->getx();
			$ySaved = $this->gety();
			foreach ($head as $k=>$i) {
				if ($headStyle) {
					$this->ATFsetStyle($headStyle[$k]);
				}
				// Décalage en X et repositionnement en Y sauf pour la premiere colonne
				if ($k) {
					$xSaved += $w[$k-1];
					$this->setxy($xSaved,$ySaved);
				}
				// Gestion multiligne
				// Multiligne sur plusieurs cellule mais pas au max
				if ($H['max']>1 && $H['max']>$H[$k] && $H[$k]!=1) {
					$this->cell($w[$k],$this->height*$H['max'],"",$this->border,0,'C',1);
					$this->setxy($xSaved,$ySaved);
					$this->multicell($w[$k],$this->height,$i,0,'C');
				// Multiligne : Cellule  concerné par le chagement de ligne
				} elseif ($H['max']>1 && $H['max']==$H[$k]) {
					$this->multicell($w[$k],$this->height,$i,$this->border,'C',1);
				// Multiligne : Cellule impacté par sa cellule soeur.
				} elseif($H['max']>1 && $H['max']!=$H[$k]) {
					$this->multicell($w[$k],$this->height*$H['max'],$i,$this->border,'C',1);
				// Pas de multiligne
				} elseif ($H['max']==1) {
					$this->multicell($w[$k],$this->height,$i,$this->border,'C',1);
				}
			}
			$this->setxy($xSavedForTheEnd,$ySaved+$this->height*$H['max']);

	}

	/** Renvoi le nombre de ligne qu'un multicell va prendre par rapport a sa largeur.
	* @author Quentin JANON
	* @param int $w Largeur du multicell
	* @param string $txt Texte a écrire dans le multicell
	*/
	function NbLines($w, $txt) {
		//Computes the number of lines a MultiCell of width w will take
        //log::logger("=========================================","qjanon");
        //log::logger($txt,"qjanon");
        //log::logger($this->CurrentFont['cw'],"qjanon");
        //log::logger("FontSize = ".$this->FontSize,"qjanon");
        //log::logger("rMargin = ".$this->rMargin,"qjanon");
        //log::logger("cMargin = ".$this->cMargin,"qjanon");
		$cw=&$this->CurrentFont['cw'];
		if($w==0) {
			$w=$this->w-$this->rMargin-$this->x;
		}
        // Quand la fonction déconne niveau compte de lignes, il faut jouer avec la variable '960'. La réduire, renvoi plus de lignes, la monter renvoi moins de ligne.
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        // log::logger("w = ".$w,"qjanon");
        // log::logger("wmax = ".$wmax,"qjanon");
		$s=str_replace("\r", '', $txt);
		$nb=strlen($s);
		if ($nb>0 and $s[$nb-1]=="\n") {
			$nb--;
		}
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb) {
			$c=$s[$i];
			if($c=="\n") {
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ') {
				$sep=$i;
			}
			$l+=$cw[$c];
			if($l>$wmax) {
				if($sep==-1) {
					if($i==$j) $i++;
				} else {
					$i=$sep+1;
				}
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			} else{
				$i++;
			}
		}
        //log::logger($nl,"qjanon");
		return $nl;
	}	
	
	/** Renvoi le nombre de ligne maximum qu'une ligne de tableau va prendre.
	* @author Quentin JANON
	* @param array $data Les données de la ligne
	* @param int $w Largeur du multicell
	* @param array $style Styles éventuels a appliquer aux textes des cellules
	*/
	function maxHeightLine($data,$w,$style=false) {
		if (!is_array($data)) return false;
		foreach ($data as $k=>$i) {
			if (preg_match("/IMAGE\:/",$i)) continue;
			if ($style[$k]) {
				$this->ATFsetStyle($style[$k]);
			}
			if ($pos = strpos($i,"€")) {
				$i = str_replace("€","[FLAG_EUROS]",$i);
				$i = utf8_decode($i);
				$i = str_replace("[FLAG_EUROS]",chr(128),$i);
			}
			// Pour ajuster le calcul du nombre de ligne en focntion des retour a la ligne utilisateur.
			$i = str_replace("[nl]","\n",$i);
			$return[$k] = $this->NbLines($w[$k],$i);
			$return['max'] = max($return['max'],$return[$k]);
		}

		return $return;
	}
	
	/** Cell bipassé pour la gestion de l'UTF8 et des caractère spéciaux provenant de WORD
	* @author Quentin JANON
	* @param multi CF FPDF->CELL
	*/
	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='') {
		$txt = str_replace("’","'",$txt);
		$txt = str_replace("–","-",$txt);
		if ($pos = strpos($txt,"€")) {
			$txt = str_replace("€","[FLAG_EUROS]",$txt);
			$txt = utf8_decode($txt);
			$txt = str_replace("[FLAG_EUROS]",chr(128),$txt);
		} else {
			$txt = utf8_decode($txt);
		}
		parent::Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
	}
	
	function enteteRoundedRect($x, $y, $w, $h, $r, $style = '') {
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='B';
		else
			$op='S';
		$MyArc = 4/3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2f %.2f m', ($x+$r)*$k, ($hp-$y)*$k ));

		$xc = $x+$w-$r;
		$yc = $y+$r;
		//Ligne du haut
		$this->_out(sprintf('%.2f %.2f l', $xc*$k, ($hp-$y)*$k ));
		// Coin Haut droit arrondis
		$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
		// Ligne du bas + Coin gauche
		$xc = $x+$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-$yc)*$k ));
		$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}
	
	function RoundedRect($x, $y, $w, $h, $r, $style = '', $angle = '1234') {
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='B';
		else
			$op='S';
		$MyArc = 4/3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2f %.2f m', ($x+$r)*$k, ($hp-$y)*$k ));

		$xc = $x+$w-$r;
		$yc = $y+$r;
		// Ligne du haut
		$this->_out(sprintf('%.2f %.2f l', $xc*$k, ($hp-$y)*$k ));
		// Coin Haut droit arrondis
		if (strpos($angle, '2')===false)
			$this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-$y)*$k ));
		else
			$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
		
		// Ligne Droite + arrondis Bas Droit
		$xc = $x+$w-$r;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-$yc)*$k));
		if (strpos($angle, '3')===false)
			$this->_out(sprintf('%.2f %.2f l', ($x+$w)*$k, ($hp-($y+$h))*$k));
		else
			$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
		// Ligne du bas + Coin bas gauche
		$xc = $x+$r;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2f %.2f l', $xc*$k, ($hp-($y+$h))*$k));
		if (strpos($angle, '4')===false)
			$this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-($y+$h))*$k));
		else
			$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

		$xc = $x+$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-$yc)*$k ));
		if (strpos($angle, '1')===false)
		{
			$this->_out(sprintf('%.2f %.2f l', ($x)*$k, ($hp-$y)*$k ));
			$this->_out(sprintf('%.2f %.2f l', ($x+$r)*$k, ($hp-$y)*$k ));
		}
		else
			$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}
	
	function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
		$h = $this->h;
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
			$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
	}

	function photo($filename,&$x,&$y,$w,$h,$type="jpg",$ln=1) {
		if ($type=="") $type="jpg";
		if ($y>265) {
			$y = 15;
			$this->addpage();
		}
		if (($filename && file_exists($filename)) || preg_match("/http/",$filename) && $type) {
			$this->setxy($x,$y);
			$this->cell($w,$h,"",$this->border,$ln);
			
//			$filename = "/home/inventaire/core/../data/inventaire/societe/54.logo";
			$this->image($filename,$x,$y,$w,$h,$type);
		}
		return $w;
	}
	
	function SetMargins($left,$top,$right=-1) {
		//Set left, top and right margins
		parent::SetMargins($left,$top,$right);
		$this->x=$left;
	}
	
	function SetLeftMargin($margin) {
		parent::SetLeftMargin($margin);
		$this->x=$margin;
	}
	
	function Circle($x,$y,$r,$style='')	 {
		$col = $this->html2rgb($this->colorReponse);
		$this->setFillColor($col["R"],$col["G"],$col["B"]);
    	$this->Ellipse($x,$y,$r,$r,$style);
		$this->setFillColor(255,255,255);
	}

	function Ellipse($x,$y,$rx,$ry,$style='D')	{
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='B';
		else
			$op='S';
		$lx=4/3*(M_SQRT2-1)*$rx;
		$ly=4/3*(M_SQRT2-1)*$ry;
		$k=$this->k;
		$h=$this->h;
		$this->_out(sprintf('%.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c',
			($x+$rx)*$k,($h-$y)*$k,
			($x+$rx)*$k,($h-($y-$ly))*$k,
			($x+$lx)*$k,($h-($y-$ry))*$k,
			$x*$k,($h-($y-$ry))*$k));
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
			($x-$lx)*$k,($h-($y-$ry))*$k,
			($x-$rx)*$k,($h-($y-$ly))*$k,
			($x-$rx)*$k,($h-$y)*$k));
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
			($x-$rx)*$k,($h-($y+$ly))*$k,
			($x-$lx)*$k,($h-($y+$ry))*$k,
			$x*$k,($h-($y+$ry))*$k));
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c %s',
			($x+$lx)*$k,($h-($y+$ry))*$k,
			($x+$rx)*$k,($h-($y+$ly))*$k,
			($x+$rx)*$k,($h-$y)*$k,
			$op));
	}

	function Checkbox($fill,$h=2) {
		$Plus = ($h/2)-1;
		$this->y+=$Plus;

		if ($fill) {
			$col = $this->html2rgb($this->colorReponse);
			$this->setFillColor($col["R"],$col["G"],$col["B"]);
			$this->setTextColor(255,255,255);
			$this->cMargin = 0.2;
			$this->cell($h,$h,"x",1,0,"L",1);
			$this->setTextColor(0,0,0);
			$this->setFillColor(255,255,255);
		} else {
			$this->cell($h,$h,"",1);
		}
		$this->y-=$Plus;
	}
			
	/** Permet de mémoriser quelle font est courante
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $font
	* @param string $deco
	* @param int $size
	*/
	function setFont($font,$deco=NULL,$size=NULL) {
		$this->infos["font"]=$font;
		return parent::setFont($font,$deco,$size);
	}
			
	/** Applique une décoration Gras, Italique ou Souligné
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $deco
	*/
	function setFontDecoration($deco) {
		if ($this->infos["font"]) return parent::setFont($this->infos["font"],$deco);
	}
			
	/** Applique une décoration Gras, Italique ou Souligné
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $deco
	*/
	function unsetFontDecoration() {
		return parent::setFont($this->infos["font"],'');
	}
			
	/** Applique un style, mais prend soin de mémoriser le style précédent
	* @author Yann GAUTHERON <ygautheron@absystech.fr>, Quentin JANON <qjanon@absystech.fr>
	* @param string $deco
	*/
	function ATFsetStyle($style, $unsetStyle=false) {
		if ($unsetStyle) self::unsetStyle();
			
		if ($this->infos["style"]) { // Sauvegarde de l'ancien style
			$this->infos["old_style"] = $this->infos["style"];
		}
		
		// Nouveau style dans la place !
		$this->infos["style"] = $style;

		// Application de toutes les propriétés du style "non-élémental" (Qui ne sont pas des paramètres d'autres méthode, comme MultiCell ou Cell par exemple)

		if ($style["font"]) { // Police de caractère
			$this->setFont($style["font"]);
		}
		if ($style["decoration"]) { // G,I,U
			$this->setFontDecoration(str_replace(",","",$style["decoration"]));
		} else {
			$this->setFont($style["font"]?$style["font"]:"arial","",$style["size"]?$style["size"]:9);
		}
		if ($style["size"]) { // Taille de police
			$this->setFontSize($style["size"]);
		}

		if ($style["color"]) { // Couleur du texte
//			$col = $this->html2rgb($style["color"]);
			$this->SetTextColor($style["color"]);
		} else {
			$this->SetTextColor(0,0,0);
		}
		if ($style["bgcolor"]) { // Couleur du fond
			$col = $this->html2rgb($style["bgcolor"]);
			$this->SetFillColor($col["R"],$col["G"],$col["B"]);
			$this->fill = 1;
		} else {
			$this->SetFillColor(255,255,255);
			$this->fill = 0;
		}
		if ($style["addpage"]=="oui" && $this->gety()>15) { // Couleur du texte
			$this->addpage();
		}

		
		$this->height = $style["height"] ? $style["height"] : 4;
		$this->align = $style["align"] ? $style["align"] : 'L';
		$this->border = $style["border"]?str_replace(",","",$style["border"]):0;

	}
			
	/** Annule le style courant et remet le précédent
	* ATTENTION : ne gère pas de pile de styles : on ne peut pas faire plusieurs unsetStyle en cascade !
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	*/
	function unsetStyle() {		
		if ($this->infos["old_style"]) {
			unset($this->infos["style"]);
			$this->infos["old_style"]["addpage"] = "non";
			$this->ATFsetStyle($this->infos["old_style"]);
		}
	}
	
	/**
	* Retourne une valeur des infos diverses
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @return mixed
	*/
	public function get() {
		$nfo = $this->infos;
		for ($i=0;$i<func_num_args();$i++) {
			$nfo = $nfo[func_get_arg($i)];
		}
		return $nfo;
	}

	/**
	* Retourne le defaultStyle
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return array
	*/
	public function getDefaultStyle() {
		return $this->defaultStyle;
	}
	
	/**
	* Retourne le defaultStyle
	* @author Quentin JANON <qjanon@absystech.fr>
	* @return array
	*/
	public function getDefaultHeadStyle() {
		return $this->defaultHeadStyle;
	}
	
	/**
	* SetFillColor upgradé pour gérer les couleur hexadecimal en premier paramètre
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function setfillcolor($R,$G=0,$B=0) {
		if (is_string($R) && !$G && !$B) {
			$color = $this->html2rgb($R);
		} else {
			$color = array("R"=>$R,"G"=>$G,"B"=>$B);
		}
		parent::SetFillColor($color["R"],$color["G"],$color["B"]);
	}
	
	/**
	* SetTextColor upgradé pour gérer les couleur hexadecimal en premier paramètre
	* Et aussi des couleurs de base : black, white, green, red, blue
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function settextcolor($R,$G=0,$B=0) {
		if (is_string($R) && !$G && !$B) {
			switch ($R) {
				case "black":
					$color["R"] = 0;
					$color["G"] = 0;
					$color["B"] = 0;
				break;
				case "white":
					$color["R"] = 255;
					$color["G"] = 255;
					$color["B"] = 255;
				break;
				case "red":
					$color["R"] = 255;
					$color["G"] = 0;
					$color["B"] = 0;
				break;
				case "green":
					$color["R"] = 0;
					$color["G"] = 255;
					$color["B"] = 0;
				break;
				case "blue":
					$color["R"] = 0;
					$color["G"] = 0;
					$color["B"] = 255;
				break;
				default:
					$color = $this->html2rgb($R);
				break;
			}
		} else {
			$color = array("R"=>$R,"G"=>$G,"B"=>$B);
		}
		parent::SetTextColor($color["R"],$color["G"],$color["B"]);
	}
	
	public function Rotate($angle,$x=-1,$y=-1) { 

        if($x==-1) 
            $x=$this->x; 
        if($y==-1) 
            $y=$this->y; 
        if($this->angle!=0) 
            $this->_out('Q'); 
        $this->angle=$angle; 
        if($angle!=0) 

        { 
            $angle*=M_PI/180; 
            $c=cos($angle); 
            $s=sin($angle); 
            $cx=$x*$this->k; 
            $cy=($this->h-$y)*$this->k; 
             
            $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy)); 
        } 
    } 
	
	public function cadre($x,$y,$w,$h,$data,$head=false,$arrondix=5,$onlyHead=false) {
//		if(!$x || !$y || !$w || !$h || !$data) {
//			throw new errorATF(ATF::$usr->trans("data_manquante","pdf_cadre"));
//		}
		if ($head && !$data && $onlyHead) {
			$this->setxy($x,$y);
			$this->multicell($w,5,$head,0,"C");
			$this->RoundedRect($x,$y,$w,5,$arrondix);
		} else {
			$this->SetFillColor($this->Rentete,$this->Gentete,$this->Bentete);
			$this->settextcolor($this->REnteteTextColor,$this->GEnteteTextColor,$this->BEnteteTextColor);
			$this->sety($y);
			if ($head) {
				$this->setx($x);
				$this->enteteRoundedRect($x,$y,$w,$h,$arrondix,"F");
				$this->multicell($w,5,$head,"B","C");
			}
			$this->settextcolor(0,0,0);

			$this->RoundedRect($x,$y,$w,$h,$arrondix);
			if (!$head) {
				$this->ln(5);
			}
			foreach ($data as $k=>$i) {
				$this->setx($x);
				if (count($data)===1) {
					$this->setxy($x,$this->gety()-5);
					$hTXT = $h;
				} else {
					$hTXT = 5;
				}
				if (is_array($i)) {
					if ($i['bgColor']) $this->setfillcolor($i['bgColor']); 
					if ($i['color']) $this->settextcolor($i['color']); 
					if ($i['size']) $this->setFontSize($i['size']); 
					else $this->setFontSize(8);
					if ($i['bold']) $this->setFontDecoration("B"); 
					if ($i['italic']) $this->setFontDecoration("I"); 
					$tmp = $i['w']?$i['w']:$w;
					if ($i['txt']) {
						$this->cell($i['indent']?$i['indent']:1,$i['h']?$i['h']:$hTXT," ");
						$this->multicell($tmp-$i['indent'],$i['h']?$i['h']:$hTXT,$i['txt'],$i['border'],$i['align'],$i['fill']);
					} else {
						$this->multicell($tmp-$i['indent'],$i['h']?$i['h']:$hTXT,"",$i['border'],$i['align'],$i['fill']);
					}
					if ($i['bold']) $this->unsetFontDecoration("B"); 
					if ($i['italic']) $this->unsetFontDecoration("I"); 
				} elseif ($i) {
					$this->multicell($w,$hTXT," ".$i,0,"L");
				}
				$this->settextcolor('black'); 
			}
			$this->setxy(15,$y+$h+5);
		}

	}
	
	public function setEnteteBGColor($R,$G=0,$B=0) {
		if (is_string($R) && !$G && !$B) {
			switch ($R) {
				case "black":
					$this->Rentete = 0;
					$this->Gentete = 0;
					$this->Bentete = 0;
				break;
				case "white":
					$this->Rentete = 255;
					$this->Gentete = 255;
					$this->Bentete = 255;
				break;
				case "red":
					$this->Rentete = 255;
					$this->Gentete = 0;
					$this->Bentete = 0;
				break;
				case "green":
					$this->Rentete = 0;
					$this->Gentete = 255;
					$this->Bentete = 0;
				break;
				case "blue":
					$this->Rentete = 0;
					$this->Gentete = 0;
					$this->Bentete = 255;
				break;
				case "base":
					$this->Rentete = 199;
					$this->Gentete = 235;
					$this->Bentete = 181;
				break;
				default:
					$color = $this->html2rgb($R);
					$this->Rentete = $color['R'];
					$this->Gentete = $color['G'];
					$this->Bentete = $color['B'];
				break;
			}
		} else {
			$this->Rentete = $R;
			$this->Gentete = $G;
			$this->Bentete = $B;
		}
	}
	
	function getHeightTableau($head,$data,$width=false,$c_height=5,$style=false) {
		if (!$width) $width=180;
		$height_max = count($data)*$c_height + 5;
		
		// On s'occupe d'initialiser les largeurs de colonnes
		if (is_array($width)) {
			foreach ($width as $k=>$i) {
				$w[$k] = $i;
			}
		} else {
			for ($z=0;$z<count($head);$z++) {
				$w[$z] = $width/count($head);
			}
		}
		
		if (is_array($data)) {
			foreach ($data as $k=>$i) {
				unset($details);
				if ($i['details'] && $i['details']!="") {
					$details = $i['details'];
					unset($i['details']);	
				}
				
				$H = $this->maxHeightLine($i,$w,$style[$k]);
				
				$return += $H['max']*$c_height;
								
				if ($details) {
					$return += 5* substr_count ($details,"\n")+5;
				}
			}
		}
		return $return;
	}
	
	public function AddPage($orientation='') {
		parent::AddPage($orientation);
		if ($this->tMargin) $this->sety($this->tMargin);
	}
	
	function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $indent=0){
		//Output text with automatic or explicit line breaks
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
	
		$wFirst = $w-$indent;
		$wOther = $w;
	
		$wmaxFirst=($wFirst-2*$this->cMargin)*1000/$this->FontSize;
		$wmaxOther=($wOther-2*$this->cMargin)*1000/$this->FontSize;
	
		$s=str_replace("\r", '', $txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$b=0;
		if($border)
		{
			if($border==1)
			{
				$border='LTRB';
				$b='LRT';
				$b2='LR';
			}
			else
			{
				$b2='';
				if(is_int(strpos($border, 'L')))
					$b2.='L';
				if(is_int(strpos($border, 'R')))
					$b2.='R';
				$b=is_int(strpos($border, 'T')) ? $b2.'T' : $b2;
			}
		}
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$ns=0;
		$nl=1;
			$first=true;
		while($i<$nb)
		{
			//Get next character
			$c=$s[$i];
			if($c=="\n")
			{
				//Explicit line break
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$ns=0;
				$nl++;
				if($border and $nl==2)
					$b=$b2;
				continue;
			}
			if($c==' ')
			{
				$sep=$i;
				$ls=$l;
				$ns++;
			}
			$l+=$cw[$c];
	
			if ($first)
			{
				$wmax = $wmaxFirst;
				$w = $wFirst;
			}
			else
			{
				$wmax = $wmaxOther;
				$w = $wOther;
			}
	
			if($l>$wmax)
			{
				//Automatic line break
				if($sep==-1)
				{
					if($i==$j) $i++;
					if($this->ws>0) {
						$this->ws=0;
						$this->_out('0 Tw');
					}
					$SaveX = $this->x; 
					if ($first and $indent >0)	{
						$this->SetX($this->x + $indent);
						$first=false;
					}
					$this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
					$this->SetX($SaveX);
				}
				else
				{
					if($align=='J')
					{
						$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
						$this->_out(sprintf('%.3f Tw', $this->ws*$this->k));
					}
					$SaveX = $this->x; 
					if ($first and $indent >0)
					{
						$this->SetX($this->x + $indent);
						$first=false;
					}
					$this->Cell($w, $h, substr($s, $j, $sep-$j), $b, 2, $align, $fill);
						$this->SetX($SaveX);
					$i=$sep+1;
				}
				$sep=-1;
				$j=$i;
				$l=0;
				$ns=0;
				$nl++;
				if($border and $nl==2)
					$b=$b2;
			} else $i++;
		}
		//Last chunk
		if($this->ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		if($border and is_int(strpos($border, 'B'))) $b.='B';
		$this->Cell($w, $h, substr($s, $j, $i), $b, 2, $align, $fill);
		$this->x=$this->lMargin;
	}

	public function image($file,$x,$y,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0) {
		$path_parts = pathinfo($file);
		if ($path_parts['extension'] == "png") {
			parent::A_image($file,$x,$y,$w,$h,$type,$link, $isMask, $maskImg);
		} else {
			parent::image($file,$x,$y,$w,$h,$type,$link);
		}
	}

}

?>