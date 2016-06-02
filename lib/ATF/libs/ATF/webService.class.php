<?php
/** 
* Client WebService
* @package ATF
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* Dépend de la librairie NuSoap
* Testé sur la version 0.7.3 de NuSoap
*
* Maj : On peut passer en paramètre un object nusoap_client. Cela peut servir pour mette un mok oject dans les Tus...
*/
require_once __ATF_PATH__."libs/nusoap/nusoap.php"; // Include NuSoap , Bibliothèque PHP pour faire du SOAP

class webService{		
		/**
		* Nom de l'objet nusoap (variable fonction)
		* @var mixed
		*/
        protected $nusoap_client=false;
		
		/**
		* Indique si le client est loggé
		* @var boolean
		*/
		protected $logged=false;

        /** 
		* Créé un appel entre deux peers, cela peut être des peers distants
		* @param mixed $nusoap_client Un client NuSoap (facultatif)
		* @param string $url Une url de connexion au serveur Soap
        */
        public function __construct($url,$nusoap_client="nusoap_client"){
			if(class_exists($nusoap_client)){
				$this->nusoap_client=new $nusoap_client($url);
			}else{
				throw new errorATF($nusoap_client."_not_an_object");
			}
        }
		
		/** 
		* Génère une exception par rapport aux erreurs NuSoap
		*/
		private function setException(){
			if ($err=$this->nusoap_client->getError()) {
				throw new Exception($err);
			}
		}
		
		/** 
		* Se connecte au WebService
		* @param string $login le Login de connexion au WebService
		* @param string $pass le mot de passe de connexion au WebService
		*/
		public function connect($login,$pass){					
			//Login basic au niveau du serveur
			$this->nusoap_client->setCredentials($login,$pass);
			
			//Mise en place d'une connexion persistante
			//$this->nusoap_client->useHTTPPersistentConnection();
			
			//Check des erreurs
			$this->setException();
			return true;
		}
		

        /** 
		* Appel une méthode du WebService
		* @param string $method le nom de la méthode distante à appeler
		* @param type namearg1, arg1, namearg2, arg2, namearg3, arg3,... Les arguments supplémentaires
        */
        public function call($method){
				if(!$method) throw new Exception("Le nom de la méthode n'est pas définie.");
                //Création de la liste des arguments
				$args_list=array();
				$num_args=func_num_args();
				$cpt=1;
				while($cpt<$num_args){
					$args_list[func_get_arg($cpt)]=func_get_arg($cpt+1);
					$cpt+=2;
				}
				//Appel de la méthode
				//print_r($args_list);
				$result=$this->nusoap_client->call($method,$args_list);
				if ($this->nusoap_client->fault) {
					throw new Exception("Fault : ".$this->nusoap_client->fault);
				} else {
					$this->setException();
					return $result;
				}
        }
						
		/**
		* Donne le nuSoapClient
		* @return mixed nusoap_client
		*/
		public function getNusoapClient(){
			return $this->nusoap_client;
		}
};
?>