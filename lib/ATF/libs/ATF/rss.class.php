<?php
/**
* Classe RSS
* Génération d'un fichier RSS 2.0
* @package ATF
* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
*/
abstract class rss implements rssInterface{
	/**
	* Entête du fichier xml
	* @var string
	*/
	private $head='<?xml version="1.0" encoding="UTF-8"?>
				   <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
			
	/**
	* Pied de page du fichier xml
	* @var string
	*/
	private $footer='</rss>';
	
	/**
	* Résultat final (xml)
	* @var string
	*/
	protected $result="";

	/**
	* Titre du flux
	* @var string
	*/
	protected $channelTitle;

	/**
	* Description du flux
	* @var string
	*/
	protected $channelDescription;

	/**
	* Url du flux
	* @var string
	*/
	protected $channelLink;
	
	/**
	* Langue du flux
	* @var string
	*/
	protected $channelLang;
	
	/**
	* Elements du flux
	* @var mixed
	*/
	protected $items;

	
	/**
	* Créé le header
	*/
	protected final function genHeader(){
		$this->result.=$this->head;
	}
	
	/**
	* Créé le footer
	*/
	protected final function genFooter(){
		$this->result.=$this->footer;	
	}

	/**
	* Créé l'entête de la description du flux
	*/
	protected final function genHeaderChannel(){
		$this->result.='<channel>';
	}

	/**
	* Créé le pied de page de la description du flux
	*/
	protected final function genFooterChannel(){
		$this->result.='</channel>';
	}

	/**
	* Indique une description du flux
	* @param string $title Le titre du flux
	* @param string $description Une brève description du flux. Pas un roman !
	* @param string $link L'url du flux
	* @param string $language la langue du flux
	*/
	protected function setChannel($title,$description,$link,$language="fr"){
		$this->channelTitle=$title;
		$this->channelDescription=$description;
		$this->channelLink=$link;
		$this->channelLang=$language;
	}
	
	/**
	* Créé la description du flux
	*/
	protected function genChannel(){
		$this->result.='<title>'.$this->channelTitle.'</title>';
		$this->result.='<atom:link href="'.$this->channelLink.'" rel="self" type="application/rss+xml" />';
		$this->result.='<description><![CDATA['.$this->channelDescription.']]></description>';
		$this->result.='<link>'.$this->channelLink.'</link>';
		$this->result.='<language>'.$this->channelLang.'</language>';
		$this->result.='<pubDate>'.date(DATE_RSS).'</pubDate>';
	}
	
	/**
	* Crée un item
	*/
	protected function genItem($title,$description,$link,$guid,$date=NULL,$authorNoun=NULL,$authorMail=NULL,$category=NULL){
		$this->result.='<item>';
		$this->result.='<title>'.($title?$title:"unknow").'</title>';
		$this->result.='<description><![CDATA['.$description.']]></description>';
		$this->result.='<link>'.$link.'</link>';
		$this->result.='<guid>'.$guid.'</guid>';
		if($date && $date!="0000-00-00 00:00:00") $this->result.='<pubDate>'.date(DATE_RSS,strtotime($date)).'</pubDate>';
		if($authorNoun && $authorMail) $this->result.='<author>'.$authorMail.' ('.$authorMail.')</author>';
		if($category) $this->result.='<category>'.$category.'</category>';
		$this->result.='</item>';
	}
	
	/**
	* Crée des items à partir d'une liste
	*/
	public function genItems(){
		foreach($this->items as $item){
			$this->genItem($item["title"],$item["description"],$item["link"],$item["guid"],$item["date"],$item["authorNoun"],$item["authorEmail"],$item["category"]);
		}
	}

	/**
	* Indique les éléments du flux
	* @param array $items
	*/
	public function setItems($items){
		$this->items=$items;
	}
	
	/**
	* Affiche le flux
	* @return string le flux
	*/
	public function display(){
		$this->result="";
		$this->genHeader();
		$this->genHeaderChannel();
		$this->genChannel();
		$this->genItems();
		$this->genFooterChannel();
		$this->genFooter();
		return $this->result;
	}
}
?>