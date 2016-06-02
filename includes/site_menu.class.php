<?
/** Classe site_menu
* @package Optima
*/
class site_menu extends classes_optima {
	public function __construct() { 
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'site_menu.titre_menu',
			'site_menu.visible'
		);
		
		$this->fieldstructure();

		$this->colonnes['bloquees']['insert'] =  
		$this->colonnes['bloquees']['cloner'] =  
		$this->colonnes['bloquees']['update'] = 
		$this->colonnes['bloquees']['select'] = array('url');

		$this->field_nom="titre_menu";

		$this->onglets = array(		
			 "site_article"			
		);
	}


	/** 
	* Surcharge de l'insert
	* @author Morgan FLEURQUIN <mfleurquind@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$link = $infos["menu_site"]["titre_menu"];

        //Désaccentue la chaîne passée en paramètre
        $link = utf8_decode($link);
        $link = strtolower(strtr($link, utf8_decode('ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ()[]\'"~$&%*@ç!?;,:/\^¨€{}<>|+.- '),  'aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn    --      c  ---            --'));
        $link = str_replace('--', '-', $link);
        $link = str_replace(' ', '', $link);

        $infos["menu_site"]["url"] = $link;

        parent::insert($infos,$s,NULL,$var=NULL,NULL,true);


	}
};
?>