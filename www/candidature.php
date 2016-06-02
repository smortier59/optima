<?php
	include(dirname(__FILE__)."/../global.inc.php");

	if ($_POST['competence_p'])
	{
		foreach ($_POST['competence_p'] as $key => $checked) {
			$_POST['competences'] .= $checked." " ;
		}
		unset($_POST['competence_p']);
	}
	if ($_POST['competence_r'])
	{
		foreach ($_POST['competence_r'] as $key => $checked) {
		   $_POST['competences'].= $checked." ";
		}
		unset($_POST['competence_r']);
	}
	if ($_POST['competence_s'])
	{
		foreach ($_POST['competence_s'] as $key => $checked) {
			$_POST['competences'].=$checked." ";
		}
		unset($_POST['competence_s']);
	}
	if ($_POST['competence_bdd'])
	{
		foreach ($_POST['competence_bdd'] as $key => $checked) {
			$_POST['competences'].=$checked." ";
		}
		unset($_POST['competence_bdd']);
	}

	try{
		//enregistrement des infos
		$id_candidat=ATF::candidat()->insert($_POST);
		
		//enregistrement du cv
		$zipfile = new ZipArchive();

		$fichier_temp=__TEMP_PATH__."absystech/candidat/".$id_candidat.".fichier_joint";
		util::mkdir(dirname($fichier_temp));
		
		util::rename($_FILES['fichier_joint']['tmp_name'],$fichier_temp);
//		file_put_contents($fichier_temp,file_get_contents($_FILES['fichier_joint']['tmp_name']));
		
		$target=__DATA_PATH__."absystech/candidat/".$id_candidat.".fichier_joint";
		util::mkdir(dirname($target));
		touch($target); // Nécessaire pour créer le fichier avant de l'open
		
		$zipfile->open($target);
		$zipfile->addFromString($_FILES['fichier_joint']['name'], file_get_contents($fichier_temp));
		$zipfile->close();
		unlink($fichier_temp);
		
		//envoi d'un mail pour prévenir de l'ajout d'un candidat
		$mail = new mail(array( "recipient"=>"jobs@absystech.fr", 
							"objet"=>"Nouvelle candidature".($_POST['id_jobs']?" pour l'offre : ".ATF::jobs()->select($_POST['id_jobs'],'intitule'):""),
							"template"=>"candidat",
							"donnees"=>$_POST,
							"from"=>"Site web AbsysTech <noreply@absystech.fr>"));
		$mail->addFile($target,"cv.zip",true);						
		$mail->send();					
		header('Location: http://www.absystech.fr/candidature-valide');
	}catch(error $e){
		$mail = new mail(array( "recipient"=>"jobs@absystech.fr", 
							"objet"=>"Un probleme sur une candidature : ".$e->getMessage(),
							"template"=>"candidat",
							"donnees"=>$_POST,
							"from"=>"Site web AbsysTech <noreply@absystech.fr>"));
		$mail->addFile($_FILES['fichier_joint']['tmp_name'],$_FILES['fichier_joint']['name'],true);						
		$mail->send();					
		header('Location: http://www.absystech.fr/candidature-erreur?'.$e->getMessage());  
	}

?>