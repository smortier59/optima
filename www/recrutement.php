<?php
  error_reporting(0);
  $pi_valid = true;
  $pp_valid = true;

  if ($_POST) {
    unset($_POST['valid']);
    include(dirname(__FILE__)."/../global.inc.php");
    if ($_SERVER['SERVER_NAME']=="pre.optima.absystech.net") {
      ATF::define_db("db","pre_extranet_v3_manala");
    } else {
      ATF::define_db("db","extranet_v3_manala");
    }
    ATF::$codename = "manala";
    try {
      ATF::db()->begin_transaction();
      // Check des fichiers joints
      if (!is_array($_FILES["photo_identite"]) || $_FILES["photo_identite"]['error'] || !$_FILES["photo_identite"]['size']) {
        $pi_valid = false;
        throw new errorATF("Photo d'identité incompatible.",8002);
      }    
      if (!is_array($_FILES["photo_pleine"]) || $_FILES["photo_pleine"]['error'] || !$_FILES["photo_pleine"]['size']) {
        $pp_valid = false;
        throw new errorATF("Photo plein pied incompatible.",8001);
      }

      if ($pi_valid && $pp_valid) {
        try {
          if (is_array($_POST['type_mission'])) {
            $_POST['type_mission'] = implode(", ",$_POST['type_mission']);
          }
          $id_p = ATF::personnel()->insert($_POST);

          // Envoi du mail
          $m["objet"] = "[Optima] Nouvelle candidature";
          $m["from"] = "Optima MANALA <no-reply@optima.fr>";
          $m["data"] = $_POST;
          $m["template"] = 'candidature';
          $m["recipient"] = "eo.manala@hotmail.fr";
            
          $mail = new mail($m);
          // Traitement des fichiers joints
          foreach ($_FILES as $k=>$i) {
            //$fn = ATF::personnel()->filepath($id_p,$k);
            ATF::personnel()->store(ATF::_s(),$id_p,$k,file_get_contents($i['tmp_name']),false);
            //rename($i['tmp_name'],$fn);
            $mail->addFile($i['tmp_name'],$i['name'],true);
          }
          $mail->send();

          $m["recipient"] = "qjanon@absystech.fr";
          $mail2 = new mail($m);
          // Traitement des fichiers joints
          foreach ($_FILES as $k=>$i) {
            //$fn = ATF::personnel()->filepath($id_p,$k);
            //ATF::personnel()->store(ATF::_s(),$id_p,$k,file_get_contents($i['tmp_name']),false);
            //rename($i['tmp_name'],$fn);
            $mail2->addFile($i['tmp_name'],$i['name'],true);
          }
          $mail2->send();

          $_REQUEST['success'] = true;
          unset($_POST);
        } catch(errorATF $e) {
          $_REQUEST['success'] = false;
          $data = "";
          foreach ($_POST as $k=>$val) {
            $data .= $k." : ".$val."\n";
          }
          mail("qjanon@absystech.fr","[OPTIMA MANALA] Erreur sur formulaire de candidature","Erreur ".$e->getCode()." : ".$e->getMessage()."\n\nDATA : \n\n".$data);
          ATF::db()->rollback_transaction();
          throw $e;
        }
        ATF::db()->commit_transaction();
      }    
    } catch (errorATF $e) { 
      ATF::db()->rollback_transaction();
      if ($e->getErrNo()==1062) { 
        $_REQUEST['erreur'] = "Vous êtes déjà enregistré dans notre base de donnée. Merci de votre attention.";
      } else {
       if (preg_match("/generic message : /",$e->getMessage())) {
          $tmp = json_decode(str_replace("generic message : ","",$e->getMessage()),true);
          $_REQUEST['erreur'] = $tmp['params']['title']." : ".$tmp['text'];
        } else {
          $_REQUEST['erreur'] = $e->getCode()." : ".$e->getMessage();
        }
        
      }
      $_REQUEST['success'] = false;

    }

    //header("Location: recrutement.php");
   // die();

  }

  function generatePasswd($numAlpha=6,$numNonAlpha=2) {
     $listAlpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
     $listNonAlpha = '123456789';
     return str_shuffle(
        substr(str_shuffle($listAlpha),0,$numAlpha) .
        substr(str_shuffle($listNonAlpha),0,$numNonAlpha)
      );
  }


?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
    <title>Recrutement Manala</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-language" content="fr" />
    <meta name="expires" content="never" />
    <meta name="rating" content="general" />
    <meta name="revisit-after" content="2 days" />
    <meta name="ROBOTS" content="index,follow" />
    <!-- Latest compiled and minified CSS -->
    <link href="https://static.absystech.net/css/manala/bootstrap.min.css" rel="stylesheet">
    <link href="https://static.absystech.net/css/manala/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="https://static.absystech.net/css/manala/custom.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">

      <div class="row clearfix">
        <? if (isset($_REQUEST['success'])) { ?>
          <div class="span6">
            <? if ($_REQUEST['success']) { ?>
              <div class="alert alert-success" role="alert">Votre candidature a bien été pris en compte.</div>
            <? } else { ?>
              <div class="alert alert-danger" role="alert">
                <span style="font-weight: bold">Un problème est suvenu lors de la création de votre demande. Veuillez suivre les indications ci dessous ou contacter l'agence pour indiquer ce problème.</span>
                <br>
                <em>Détail de l'erreur : <? echo $_REQUEST['erreur']; ?></em>
              </div>
            <? } ?>
          </div>
        <? } ?>
      </div>

        <!-- Building Form. -->
      <div class="row clearfix">
        <div class="span6">
          <div class="clearfix">
            <form class="form-horizontal" name="recrutement_manala" id="recrutement_manala" action="./recrutement.php" method="post" enctype="multipart/form-data" >
              <fieldset>
                <!-- Form Name -->
                <legend>Formulaire de recrutement</legend>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="nom">Identité <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls form-inline">
                    <input id="nom" name="nom" type="text" placeholder="Nom de famille" class="input-large" style="margin-right: 20px;" value="<? echo $_POST['nom']; ?>">
                  </div>
                </div>

                <div class="control-group">
                  <label class="control-label" for="prenom"></label>
                  <div class="controls form-inline">
                    <input id="prenom" name="prenom" type="text" placeholder="Prénom" class="input-large"  style="margin-right: 20px;" value="<? echo $_POST['prenom']; ?>">
                  </div>
                </div>


                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="adresse">Adresse <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="adresse" name="adresse" type="text" placeholder="Adresse de la rue" class="input-xxlarge"  value="<? echo $_POST['adresse']; ?>">
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="adresse_2"></label>
                  <div class="controls">
                    <input id="adresse_2" name="adresse_2" type="text" placeholder="Adresse (complément)" class="input-xxlarge" value="<? echo $_POST['adresse_2']; ?>">
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="cp"></label>
                  <div class="controls form-inline">
                    <input id="cp" name="cp" type="text" placeholder="Code postal" class="input-large" style="margin-right: 20px;" value="<? echo $_POST['cp']; ?>">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="cp"></label>
                  <div class="controls form-inline">
                    <input id="ville" name="ville" type="text" placeholder="Ville" class="input-large"  style="margin-right: 20px;" value="<? echo $_POST['ville']; ?>">
                    
                  </div>
                </div>


                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="province"></label>
                  <div class="controls form-inline">
                    <input id="province" name="province" type="text" placeholder="Etat/Province" class="input-large" style="margin-right: 20px" value="<? echo $_POST['province']; ?>">
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label" for="province"></label>
                  <div class="controls form-inline">
                    <select id="id_pays" name="id_pays" class="input-large" style="margin-right: 20px">
                      <option value="FR">France</option>
                      <option value="BE">Belgique</option>
                    </select>                    
                  </div>
                </div>



                <!-- Select Basic -->
                <div class="control-group">
                  <label class="control-label" for="date_naissance">Date de naissance (format attendu : AAAA-MM-JJ)<span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls form-inline">
                    <input id="date_naissance" name="date_naissance" type="text" placeholder="ex: 2014-12-31" class="input-large" style="margin-right: 20px" value="<? echo $_POST['date_naissance']; ?>">
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="lieu_naissance">Lieu de naissance <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="lieu_naissance" name="lieu_naissance" type="text" placeholder="" class="input-xxlarge" value="<? echo $_POST['lieu_naissance']; ?>" >
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="num_secu">Numéro de sécurité sociale <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="num_secu" name="num_secu" type="text" placeholder="" class="input-xxlarge" value="<? echo $_POST['num_secu']; ?>" >
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="nationalite">Nationalité <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="nationalite" name="nationalite" type="text" placeholder="" class="input-xxlarge" value="<? echo $_POST['nationalite']; ?>" >
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="taille">Quel est votre taille ? <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="taille" name="taille" type="text" placeholder="ex : 1m70" class="input-xxlarge" value="<? echo $_POST['taille']; ?>" >
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="mensuration_haut">Taille vêtement (Haut) <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="mensuration_haut" name="mensuration_haut" type="text" placeholder="" class="input-xxlarge" value="<? echo $_POST['mensuration_haut']; ?>" >
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="mensuration_bas">Taille vêtement (Bas) <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="mensuration_bas" name="mensuration_bas" type="text" placeholder="" class="input-xxlarge" value="<? echo $_POST['mensuration_bas']; ?>" >
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="email">Email <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="email" name="email" type="text" placeholder="ex : mon.nom@email.com" class="input-xxlarge" value="<? echo $_POST['email']; ?>" >
                    
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="tel">Téléphone <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <input id="tel" name="tel" type="text" placeholder="" class="input-xxlarge" value="<? echo $_POST['tel']; ?>" >
                    
                  </div>
                </div>

                <!-- Multiple Radios (inline) -->
                <div class="control-group">
                  <label class="control-label" for="permis">Avez-vous le permis de conduire ? <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <label class="radio inline" for="permis-0">
                      <input type="radio" name="permis" id="permis-0" value="oui" <? if ($_POST['permis']=="oui") echo 'checked="checked"'; ?> >
                      Oui
                    </label>
                    <label class="radio inline" for="permis-1">
                      <input type="radio" name="permis" id="permis-1" value="non" <? if ($_POST['permis']=="non") echo 'checked="checked"'; ?>>
                      Non
                    </label>
                    <label for="permis" class="error" style="display: none; height:30px">Ce champ est obligatoire</label>
                  </div>
                </div>

                <!-- Multiple Radios (inline) -->
                <div class="control-group">
                  <label class="control-label" for="voiture">Avez-vous une voiture ? <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <label class="radio inline" for="voiture-0">
                      <input type="radio" name="voiture" id="voiture-0" value="oui" <? if ($_POST['voiture']=="oui") echo 'checked="checked"'; ?>>
                      Oui
                    </label>
                    <label class="radio inline" for="voiture-1">
                      <input type="radio" name="voiture" id="voiture-1" value="non" <? if ($_POST['voiture']=="non") echo 'checked="checked"'; ?>>
                      Non
                    </label>
                    <label for="voiture" class="error" style="display: none; height:30px">Ce champ est obligatoire</label>
                  </div>
                </div>

                <!-- Multiple Radios -->
                <div class="control-group">
                  <label class="control-label" for="anglais">Parlez-vous Anglais ? <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <label class="radio" for="anglais-0">
                      <input type="radio" name="anglais" id="anglais-0" value="base"  <? if ($_POST['anglais']=="base") echo 'checked="checked"'; ?>>
                      Niveau de base
                    </label>
                    <label class="radio" for="anglais-1">
                      <input type="radio" name="anglais" id="anglais-1" value="maitrise" <? if ($_POST['anglais']=="maitrise") echo 'checked="checked"'; ?>>
                      Bonne maîtrise écrite et orale
                    </label>
                    <label class="radio" for="anglais-2">
                      <input type="radio" name="anglais" id="anglais-2" value="bilingue" <? if ($_POST['anglais']=="bilingue") echo 'checked="checked"'; ?>>
                      Bilingue
                    </label>
                    <label for="anglais" class="error" style="display: none; height:30px">Ce champ est obligatoire</label>
                  </div>
                </div>

                <!-- Text input-->
                <div class="control-group">
                  <label class="control-label" for="langues">Maîtriser vous une autre langue étrangère ?</label>
                  <div class="controls">
                    <input id="langues" name="langues" type="text" placeholder="" class="input-xxlarge" value="<? echo $_POST['langues']; ?>">
                    
                  </div>
                </div>

                <!-- Multiple Checkboxes -->
                <div class="control-group">
                  <label class="control-label" for="type_mission">Pour quel type de mission souhaitez-vous postuler ? <span style="color: red; font-weight: bold; font-size: 15px;">*</span></label>
                  <div class="controls">
                    <label class="checkbox" for="type_mission-0">
                      <input type="checkbox" name="type_mission[]" id="type_mission-0" value="Evènementiel" <? if (preg_match("/Evènementiel/",$_POST['type_mission']))  echo 'checked="checked"';  ?>>
                      Evènementiel
                    </label>
                    <label class="checkbox" for="type_mission-1">
                      <input type="checkbox" name="type_mission[]" id="type_mission-1" value="Animations et Promotion des ventes" <? if (preg_match("/Animations et Promotion des ventes/",$_POST['type_mission'])) echo 'checked="checked"'; ?>>
                      Animations et Promotion des ventes
                    </label>
                    <label class="checkbox" for="type_mission-2">
                      <input type="checkbox" name="type_mission[]" id="type_mission-2" value="Accueil en entreprise" <? if (preg_match("/Accueil en entreprise/",$_POST['type_mission'])) echo 'checked="checked"'; ?>>
                      Accueil en entreprise
                    </label>
                    <label for="type_mission" class="error" style="display: none; height:30px">Ce champ est obligatoire</label>
                  </div>
                </div>

                <!-- File Button --> 
                <div class="control-group">
                  <label class="control-label" for="photo_identite">
                    Photo d'identité <span style="color: red; font-weight: bold; font-size: 15px;">*</span><br>
                    <span style="font-size: 10px; font-style: italic; font-weight: none;">[JPG ou PNG ou GIF]</span>
                  </label>
                  <div class="controls">
                    <input id="photo_identite" name="photo_identite" class="input-file" type="file">
                  </div>
                </div>

                <!-- File Button --> 
                <div class="control-group">
                  <label class="control-label" for="photo_pleine">
                    Photo (Plein Pieds) <span style="color: red; font-weight: bold; font-size: 15px;">*</span><br>
                    <span style="font-size: 10px; font-style: italic; font-weight: none;">[JPG ou PNG ou GIF]</span>
                  </label>
                  <div class="controls">
                    <input id="photo_pleine" name="photo_pleine" class="input-file" type="file">
                  </div>
                </div>

                <!-- File Button --> 
                <div class="control-group">
                  <label class="control-label" for="cv">
                    Votre CV <span style="color: red; font-weight: bold; font-size: 15px;">*</span><br>
                    <span style="font-size: 10px; font-style: italic; font-weight: none;">[PDF ou DOC]</span>
                  </label>
                  <div class="controls">
                    <input id="cv" name="cv" class="input-file" type="file">
                  </div>
                </div>

                <!-- Button -->
                <div class="control-group">
                  <label class="control-label" for="valid"></label>
                  <div class="controls">
                    <button id="valid" name="valid" class="btn btn-default">Soumettre</button>
                  </div>
                </div>

              </fieldset>
            </form>

            <span style="color: red; font-weight: bold; font-size: 15px;">*</span> : Champs obligatoire.


          </div>
        </div>
      </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <!-- jQuery via Google + local fallback, see h5bp.com -->
    <script src="https://static.absystech.net/js/manala/jquery-1.7.1.min.js"></script>
    
    <!-- Validate plugin -->
    <script src="https://static.absystech.net/js/manala/jquery.validate.js"></script>    
    
    <!-- Scripts specific to this page -->
    <script src="https://static.absystech.net/js/manala/script.js?v=<? echo rand(0,100000); ?>"></script>
    

  </body>
</html>