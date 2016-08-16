<?php
/** 
* Crontab gérant l'envoi des mails speedmail
* @author Quentin JANON <qjanon@absystech.fr>
* @date 03-11-2010
*/
define("__BYPASS__",true);
//$_GET["debug"]=1;
try {
    include(dirname(__FILE__)."/../global.inc.php");
    
    ATF::emailing_job()->majEtatSending();
    
    if ($_SERVER["argv"][2]=="toSent") {
    	echo ATF::emailing_job()->{$_SERVER["argv"][2]}(true);
    } else {
    	ATF::emailing_job()->send($_SERVER["argv"][2]);
    }
     
    ATF::emailing_job()->majEtatSent();
} catch (errorATF $e) {
    if ($e->getErrno()==1142) {
        echo "\n/!\Le user n'a pas les droits pour faire les modifications/!\ \n";
    } else {
        echo "\n/!\ERREUR ".$e->getCode()." : ".$e->getMessage()."/!\ \n";
    }  
}
?>