<?php
/**
* Moteur d'erreur ATF5
* Le moteur d'erreur possde une gestion des erreurs Fatales et des Exceptions non catchées
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
*/

/*Initialisation du Moteur de message*/
define("__GENERIC_ERROR__",10);
define("__SQL_ERROR__",11);
define("__PROGRAM_ERROR__",12);
define("__LOGIN_ERROR__",13);
define("__AJAX_ERROR__",14);
define("__REDIRECTION_EXCEPTION__",15);

/** 
* Constantes d'erreurs - Gestion dans le moteur d'erreur
* True => gre l'erreur dans le moteur d'ATF5
* @var array
*/
$GLOBALS["errors"]=array(
	E_ERROR => true,
	E_WARNING => false,
	E_PARSE => false,
	E_NOTICE => false,
	E_CORE_ERROR => false,
	E_CORE_WARNING => false,
	E_COMPILE_ERROR => false,
	E_COMPILE_WARNING => false,
	E_USER_ERROR => false,
	E_USER_WARNING => false,
	E_USER_NOTICE => false,
	E_STRICT => false,
	E_RECOVERABLE_ERROR => ATF::$debug,
	E_DEPRECATED => false,
	E_USER_DEPRECATED => false,
	E_ALL => false,
	__GENERIC_ERROR__ => true,
	__SQL_ERROR__ => true,
	__PROGRAM_ERROR__ => true,
	__LOGIN_ERROR__ => true,
	__AJAX_ERROR__ => true,
	__REDIRECTION_EXCEPTION__ => true,
	"view_error" => true // Affichage des exceptions non catches
);
/*Pour infos voici les contantes d'erreurs php*/
/*define('1',E_ERROR);
define('2',E_WARNING);
define('4',E_PARSE);
define('8',E_NOTICE);
define('16',E_CORE_ERROR);
define('32',E_CORE_WARNING);
define('64',E_COMPILE_ERROR);
define('128',E_COMPILE_WARNING);
define('256',E_USER_ERROR);
define('512',E_USER_WARNING);
define('1024',E_USER_NOTICE);
define('2048',E_STRICT);
define('4096',E_RECOVERABLE_ERROR);
define('8192',E_DEPRECATED);
define('16384',E_USER_DEPRECATED);
define('30719',E_ALL);*/

/*CLASSES ERROR ATF*/
//require_once("/home/devapps/asteriskadmin3/core/libs/ATF/error.class.php");

/*GESTION DES EXCEPTIONS NON CATCHEES*/

/**
* Fonction d'erreur personnalisée
* les arguments sont utilisés automatiquement par les erreurs php aprs le passage de cette fonction en entête des erreurs (set_error_handler);
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* @date 2009/03/23
* @param $errno le numéro de l'erreur
* @param $errstr le libellé de l'erreur
* @param $errfile le fichier concerné
* @param la ligne concerné
*/
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
		if($GLOBALS["errors"][$errno]){
			//Affichage de l'erreur
			if($GLOBALS["errors"]["view_error"]){
				echo "Erreur FATALE ! : ".$errstr." - ligne ".$errline." - fichier ".$errfile."<br />";
			}
			$error=new errorATF($errstr,$errno,NULL,$errfile,$errline);
			$error->setError();
		}
}
//Passage de la fonction exception_error_handler en entte des erreurs
set_error_handler("exception_error_handler");

/*GESTION DES ERREURS DE SCRIPTS*/

/**
* Fonction de fin de script
* Cette fonction permet de détecter les erreurs bloquantes php et de générer une erreur
* Elle ne renvoie pas de résultat, c'est un bout de script qui s'éxcute à la fin de tout script php
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
* @date 2009/03/23
*/
function atfError(){
	//Sauvegarde de la session
	//ATF::env()->saveSession();
   $isError = false;
   if ($error = error_get_last()){
	   switch($error["type"]){
		   case E_ERROR:
		   case E_PARSE:
		   case E_CORE_ERROR:
		   case E_COMPILE_ERROR:
		   case E_USER_ERROR:
		   case E_RECOVERABLE_ERROR:
			   $isError = true;
			   break;
	   }
   }

   if ($isError){
   	   //Récupération de la bonne pile (avant l'exécution de shutdown)
	   $stack="null stack";
	   if(function_exists(xdebug_get_function_stack)){
	   		$stack=xdebug_get_function_stack();
	   }
	   //Génération de l'erreur
	   $error=new errorATF($error["message"],1,NULL,$error["file"],$error["line"],$stack);
	   if($_REQUEST["method"]=="ajax"){
	   		 ATF::errorProcess($error,true);
			 ATF::$cr->generate();
			 ATF::$json->send();
		}else{
			 ATF::errorProcess($error);
		}
   } else {
	   //echo "Execution sans erreurs Fatales!<br />";
   }
}
//Passage de la fonction shutdown en fin de script
register_shutdown_function("atfError");
?>