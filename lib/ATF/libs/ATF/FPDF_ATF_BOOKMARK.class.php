<?php
/**
* @package ATF
* @version 5
* @author Quentin JANON <qjanon@absystech.fr>
*/ 
class FPDF_ATF_BOOKMARK extends FPDF_ATF {
	var $outlines=array();
	var $OutlineRoot;
	
	function Bookmark($txt,$level=0,$y=0) {
		if  (!$y) {
			$y=$this->GetY()-10;
		}
		$this->outlines[]=array('t'=>$txt,'l'=>$level,'y'=>$y,'p'=>$this->PageNo());
	}

	function _putbookmarks() {
//		$this->outlines = array_slice($this->outlines, 6, 5); 
		$nb=count($this->outlines);
		if($nb==0)
			return;
		$lru=array();
		$level=0;
		foreach($this->outlines as $i=>$o) {
//			echo "==========O".$i."===========\n";
//			print_r($o);
//			echo "==========LRU===========\n";
//			print_r($lru);
//			echo "==========LEVEL===========\n";
//			print_r($level);
//			echo "\n";
			if($o['l']>0) {
				$parent=$lru[$o['l']-1];
				//Set parent and last pointers é
				$this->outlines[$i]['parent']=$parent;
				$this->outlines[$parent]['last']=$i;
				if($o['l']>$level) {
					//Level increasing: set first pointer
					$this->outlines[$parent]['first']=$i;
				}
			} else {
				$this->outlines[$i]['parent']=$nb;
			}
			if($o['l']<=$level and $i>0) {
				//Set prev and next pointers
				$prev=$lru[$o['l']];
				$this->outlines[$prev]['next']=$i;
				$this->outlines[$i]['prev']=$prev;
			}
			$lru[$o['l']]=$i;
			$level=$o['l'];
//			echo "==========FINAL===========\n";
//			print_r($this->outlines[$i]);
		}
		//Outline items
		$n=$this->n+1;
		//print_r($this->outlines);die();
		
		foreach($this->outlines as $i=>$o) {
			$this->_newobj();
			$this->_out('<</Title '.$this->_textstring(utf8_decode($o['t'])));
			$this->_out('/Parent '.($n+$o['parent']).' 0 R');
			if(isset($o['prev']))
				$this->_out('/Prev '.($n+$o['prev']).' 0 R');
			if(isset($o['next']))
				$this->_out('/Next '.($n+$o['next']).' 0 R');
			if(isset($o['first']))
				$this->_out('/First '.($n+$o['first']).' 0 R');
			if(isset($o['last']))
				$this->_out('/Last '.($n+$o['last']).' 0 R');
			$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]',1+2*$o['p'],($this->h-$o['y'])*$this->k));
			$this->_out('/Count 0>>');
			$this->_out('endobj');
		}
		//Outline root  
		$this->_newobj();
		$this->OutlineRoot=$this->n;
		$this->_out('<</Type /Outlines /First '.$n.' 0 R');
		$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
		$this->_out('endobj');
	}

	function _putresources() {
		parent::_putresources();
		$this->_putbookmarks();
	}
	
	function _putcatalog() {
		parent::_putcatalog();
		if(count($this->outlines)>0) {
			$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
			$this->_out('/PageMode /UseOutlines');
		}
	}
	
	function sommaire($max_width=190) {
		$this->addpage('P');
		$this->setleftmargin(10);
		$this->setrightmargin(10);
		$this->setx(10);
		$this->SetFont('Arial','B',15);
		$this->setfillcolor(220,220,220);
		$this->setdrawcolor(220,220,220);
		$this->settextcolor(0,0,0);
		$this->Bookmark("INDEX");
		$this->multicell(0,10,"INDEX",1,'C',1);
		$this->ln(5);
		$this->SetFont('Arial','B',10);
		foreach ($this->outlines as $k=>$i) {
			$txt_w = $this->getstringwidth($i['t'])+5;
			$page_w = $this->getstringwidth($i['p'])+5;
			$reste = $max_width - $txt_w - $page_w - ($i['l']*10);
			if ($i['l']) {
				$this->cell($i['l']*10,5," ",0,0);
				$this->setdrawcolor(200,200,200);
				$begin = 10 + $i['l']*10;
			} else {
				$this->setdrawcolor(0,0,0);
				$begin = 10;
			}
			$this->line($begin?$begin:10,$this->gety(),200,$this->gety());
			$this->cell($txt_w,5,$i['t'],0,0);
			$this->cell($reste,5,"",0,0);
			$this->cell($page_w,5,"p.".$i['p'],0,1);
		}
		
	}
}
?>