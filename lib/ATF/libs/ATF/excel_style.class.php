<?php
/**
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
* @package ATF
* @date 04-05-2011
*/ 
class excel_style{
	
	public function __construct() {
		$this->table = __CLASS__;
		$this->styles; //contiendra tous les styles � appliquer
	}
	
	public function setBold(){
		$this->styles["font"]["bold"]=true;
		return $this;
	}
	
	public function setUnderline(){
		$this->styles["font"]["underline"]="single";
		return $this;
	}
	
	public function setBgColor($couleur){
		$this->styles["fill"]["type"]=PHPExcel_Style_Fill::FILL_SOLID;
		$this->styles["fill"]["color"]["rgb"]=$this->palette($couleur);
		return $this;
	}
	
		
	public function setColor($couleur){
		$this->styles["font"]["color"]["rgb"]=$this->palette($couleur);
		return $this;
	}

	
	public function alignement($vertical="center",$horizontal="center"){
		$this->styles["alignment"]["vertical"]=$vertical;
		$this->styles["alignment"]["horizontal"]=$horizontal;
		return $this;
	}
	
	public function palette($couleur){
		switch($couleur){
			case "jaune"  : return 'FFFF00';
			case "rouge"  : return 'FF0000';
			case "bleu"   : return '3333FF';
			case "vert"   : return '00FF00';
			case "orange" : return 'FF9900';
			case "gris"   : return 'CDCDCD';
			default       : return $couleur;
		}
	}
	
	public function getStyle(){
		return $this->styles;
	}

	public function setSize($size){
		$this->styles["font"]["size"]=$size;
		return $this;
	}
	
	public function setBorder($style="medium",$border="allborders"){
		$this->styles["borders"][$border]["style"]=$style;
		return $this;
	}
	
	/* retour � la ligne */
	public function setWrap(){
		$this->styles["alignment"]["wrap"]=true;
		return $this;
	}
	
	public function setShrinkToFit(){
		$this->styles["alignment"]["shrinkToFit"]=true;
		return $this;
	}
	
	public function setCode($code){
		$this->styles["numberformat"]["code"]=$code;
		return $this;
	}
	
	public function setName($police='Arial'){
		$this->styles["font"]["name"]=$police;
		return $this;
	}
	
};
?>