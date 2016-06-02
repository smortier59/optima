<?php
/**
* La classe curl permet de surcharger les méthodes php pour éviter les problèmes de TU
*
* @date 2011-02-02
* @package ATF
* @version 5
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
*/ 
class curl {

	public $ch;

	public function curlInit($data){
		$this->ch = curl_init($data);
	}
	public function curlSetopt($param1,$param2){
		curl_setopt($this->ch, $param1, $param2);
	}
	public function curlExec(){
		return curl_exec($this->ch);
	}
	public function curlClose(){
		curl_close($this->ch);
	}
}
?>