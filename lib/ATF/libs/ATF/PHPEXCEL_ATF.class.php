<?php
/**
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
* @package ATF
* @date 04-05-2011
*/ 
class PHPEXCEL_ATF{	
	public function __construct($workbook,$numberOrText) {
		if(is_numeric($numberOrText)){
			$this->sheet=$workbook->getSheet($numberOrText);
		}else{
			$this->sheet = $workbook->createSheet();
			$this->sheet->setTitle($numberOrText);
		}
	}
		
	public function write($cell,$valeur,$style=NULL){
		$this->sheet->setCellValue($cell, $valeur);
		if($style){
			$this->sheet->getStyle($cell)->applyFromArray($style);
		}
	}
	
	public function applyStyle($cell,$style){
		$this->sheet->duplicateStyleArray($style,$cell);
	}
	
};
?>