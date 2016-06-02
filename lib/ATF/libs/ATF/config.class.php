<?php
/**
* Constantes de configuration ATF
* @date 2011-02-11
* @package ATF
* @version 5
* @author  Jérémie Gwiazdowski <jgw@absystech.fr>
*/ 
class config{
	/**
	* Stockage de la configuration
	* @var mixed
	*/
	private $config;
	
	/**
	* Ajoute une nouvelle configuration
	* Attention comme une constante php on ne peut pas modifier une constante déjà créée. Il faut utiliser la méthode delete au-préalable
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $key Le nom de la constante
	* @param string $valeur La valeur de la constante
	*/
	public function set($key,$value){
		if(isset($this->config[$key])){
			throw new errorATF("config_already_exist");
		}else{
			$this->config[$key]=$value;
		}
	}
	
	/**
	* Donne la valeur d'une valeur de configuration
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $key Le nom de la constante
	* @return string La valeur de la constante
	*/
	public function get($key){
		if(isset($this->config[$key])){
			return $this->config[$key];
		}else{
			return NULL;
		}
	}
	
	/**
	* Supprime une valeur de configuration
	* @author Jérémie Gwiazdowski <jgw@absystech.fr>
	* @param string $key Le nom de la constante
	*/
	public function delete($key){
		if(isset($this->config[$key])){
			unset($this->config[$key]);
		}
	}
};
?>