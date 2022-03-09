<?
/**
* @package Optima
*/
class prelevement extends classes_optima{
	function __construct(){ //PHP5
		parent::__construct();
		$this->table=__CLASS__;
		$this->files["fichier_joint"] = array("type"=>"xml","no_generate"=>true);

	}

    public function _uploadAndParseXml($get, $post, $files){
        try {
           $filepath = $this->filepath(ATF::$usr->getID(), "fichier_joint", true);
          //  unlink($filepath);
            $r = ATF::file()->_POST($get, $post, $files);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function _parseXML() {
      $filepath = $this->filepath(ATF::$usr->getID(), "fichier_joint", true);
      if(file_exists($filepath) && filesize($filepath) !=0){
        $content = file_get_contents($filepath);

        if(ATF::$codename == "absystech"){
          $pattern = '/AFLI[0-9]{8}/';
          preg_match($pattern, $content, $matches);
          if(count($matches)>0){
            throw new errorATF('Votre fichier est non conforme pour absystech', 500);
          }

        }elseif(ATF::$codename == "att"){
          $pattern = '/[^A]FLI[0-9]{8}/';
          preg_match($pattern, $content, $matches);
          if(count($matches)>0){
            throw new errorATF('Votre fichier est non conforme pour absystech telecom', 500);
          }
        }

        $array = [];
        $row = 0;

        if (($open = fopen($filepath, "r")) !== FALSE)
        {
          while (($data = fgetcsv($open, 1000, ";")) !== FALSE)
          {
              $dateInput = explode('/',$data[8]);
              $array[$row]['date'] = $dateInput[2].'-'.$dateInput[1].'-'.$dateInput[0];
              $array[$row]['refs_facture'] = $data[2];
              $array[$row]['rum'] = $data[29];
              $array[$row]['montant'] = preg_replace('/\s+/', '', $data[4]);

              if (ATF::$codename == "att") {
                $pattern = '/ASLI[0-9]{8}/';
              } else {
                 $pattern = '/SLI[0-9]{8}/';
              }

              preg_match($pattern, $data[29], $sli);
              $m = preg_split($pattern, $data[29]);
              $ref_client = NULL;
              if ($sli[0]) {
                $ref_client = $sli[0];
              } else {
                // On recherche la ref Client par rapport à la facutre
                ATF::facture()->q->reset()->where("facture.ref", $data[2]);
                $fac = ATF::facture()->select_row();
                $id_soc = $fac["facture.id_societe_fk"];
                $ref_client = ATF::societe()->select($id_soc, "ref");
              }



              $name_societe = $m[0];

              $array[$row]['name_client'] = $name_societe;
              $array[$row]['ref_client'] = $ref_client;
              $array[$row]['canPayment'] = false;
            $row++;

          }

          fclose($open);
        }

        array_shift($array);

        // log::logger($array , "mfleurquin");
        foreach ($array as $key=>$value) {
          $refs_facture = "";


          // verifier si les factures si les sociétés existent en base
          if ($array[$key]['ref_client']) {
              ATF::societe()->q->reset()->where('societe.ref',$ref_client);
              $societe = ATF::societe()->select_row();
              if($societe){
                  ATF::facture()->q->reset()
                      ->where('facture.ref',$value['refs_facture']);
                  $factures = ATF::facture()->select_all();
                  if(count($factures) == 1 ){
                        foreach($factures as $item){
                          $refs_facture = $item['facture.ref'];
                            if($item['prix_ttc'] == $value['montant'] && $item['facture.etat'] == "impayee"){
                                  $array[$key]['canPayment'] = true;

                                  // Mettre les réfs facture, séparés par virgule, dans une variable $refs_facture
                                  if ($item != end($factures)) $refs_facture .= ',';
                            }else{
                              $array[$key]['canPayment'] = false;
                            }
                        }
                  }else{
                    $array[$key]['canPayment'] = false;
                  }
              }
          }
        }
        unlink($filepath);
        $this->facturesFile = $array;
        return $array;
      }else{
        throw new errorATF("Le fichier n'existe pas", 500);
      }
    }

    public function _payments($get,$post){

      try {
        ATF::db()->begin_transaction();
        if (!$post['factureSelected']) throw new errorATF("Aucune références de factures", 500);
        foreach ($post['factureSelected'] as $key=>$item) {
          $refs = explode(',',$item['refs_facture']);
          foreach ($refs as $r) {
            $facture = ATF::facture()->getByRef($r);
            if (!$facture) throw new errorATF("Facture non trouvée", 500);
            if ($facture['facture.etat'] != "impayee") throw new errorATF("Facture déjà payé ou alors pas en impayée.", 500);
            $paiement = array(
              "id_facture" => $facture['facture.id_facture'],
              "montant" => $facture['prix_ttc'],
              "date" => $item['date'],
              "mode_paiement" => "prelevement",
              "remarques"=> "Rapprochement comptable via import Telescope - ".number_format($post['total'],2, ',', ' ')." €",
            );
            // Appel de l'insert pour gérer les traitements post paiements : passage de la facture en payé, de la commande en terminée et de l'affaire en terminée. Puis calcul des intérêts
            $id = ATF::facture_paiement()->insert($paiement);
          }
        }
      } catch (errorATF $e){
        ATF::db()->rollback_transaction();
        throw $e;
      }
      ATF::db()->commit_transaction();
    }

    public function _fetchFactureImpayeesNonPrelevement($get){

      ATF::facture()->q->reset()
                              ->where('facture.etat','impayee')
                              ->where('facture.id_termes',24,'AND',false,"!=")
                              ->where('facture.id_termes',25,'AND',false,"!=");

      $response = ATF::facture()->select_all();

      foreach ($response as $k=>$lines) {
        foreach ($lines as $k_=>$val) {
          if (strpos($k_,".")) {
            $tmp = explode(".",$k_);
            if($tmp[0] == "facture" && $tmp[1]=="id_societe_fk"){
              $response[$k]["ref_client"] = ATF::societe()->select($val,"ref");
            }
            $response[$k][$tmp[1]] = $val;

            unset($response[$k][$k_]);
          }
        }

      }

      return $response;
    }

    public function _search($get,$post) {
      $result = [];
      ATF::facture()->q->reset()
                              ->where('facture.etat','impayee')
                              ->where('facture.id_termes',24,'AND',false,"!=")
                              ->where('facture.id_termes',25,'AND',false,"!=");

      $result = ATF::facture()->select_all();

      foreach ($result as $k=>$lines) {
        foreach ($lines as $k_=>$val) {
          if (strpos($k_,".")) {
            $tmp = explode(".",$k_);
            if($tmp[0] == "facture" && $tmp[1]=="id_societe_fk"){
              $result[$k]["ref_client"] = ATF::societe()->select($val,"ref");
            }
            $result[$k][$tmp[1]] = $val;

            unset($result[$k][$k_]);
          }
        }

      }

      $return = [];

      if($get['numero_facture'] && (!$get['id_societe']  && !$get['date_debut'] && !$get['date_fin']) ){
        
        
       

       
          foreach($result as $item=>$value){
            if(strlen($get['numero_facture']) == 7){
              if(substr($value['ref'], 0, 7) == substr($get['numero_facture'], 0, 7)){
                array_push($return,$value);
              }
            }else{
              if($value['ref']  == $get['numero_facture']){
                array_push($return,$value);
              }
            }
           
         
        }
       
      }
      elseif($get["date_debut"] && $get['date_fin'] && !$get['id_societe'] && !$get['numero_facture']){
        $datedebut = explode('-',$get['date_debut']);
        $datefin = explode('-',$get['date_fin']);

        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];
        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_debut_periode,"AND",false,">=")
            ->where('facture.date',$date_fin_periode,"AND",false,"<=");
        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();
          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }


          foreach($societes as $item){
            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'] ;
            }
          }

        }

        foreach($result as $item){
            array_push($return,$item);
        }

      }
      elseif($get["date_debut"] && $get['date_fin'] && $get['id_societe'] && !$get['numero_facture']){
        $datedebut = explode('-',$get['date_debut']);
        $datefin = explode('-',$get['date_fin']);

        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];
        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_debut_periode,"AND",false,">=")
            ->where('facture.date',$date_fin_periode,"AND",false,"<=");
        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();
          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }


          foreach($societes as $item){
            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'] ;
            }
          }

        }

        foreach($result as $item=>$value){
          if($value['id_societe_fk'] == $get['id_societe']){
            array_push($return,$value);
          }
       }


      }
      elseif($get["date_debut"] && !$get['date_fin'] && !$get['id_societe'] && !$get['numero_facture']){
        $datedebut = explode('-',$get['date_debut']);
        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_debut_periode,"AND",false,">=");

        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();

          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }

          foreach($societes as $item){
            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'] ;
            }
          }

        }

        foreach($result as $item){
            array_push($return,$item);
        }

      }elseif($get["date_fin"] && !$get['date_debut'] && !$get['id_societe'] && !$get['numero_facture']){
        $datefin = explode('-',$get['date_fin']);

        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_fin_periode,"AND",false,"<=");

        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();

          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }

          foreach($societes as $item){
            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'] ;
            }
          }

        }

        foreach($result as $item){
            array_push($return,$item);
        }

      }
      elseif(!$get['id_societe'] && !$get["date_fin"] && !$get['date_debut'] && !$get['numero_facture']){
        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=");

        $response = ATF::facture()->select_all();

        foreach ($response as $k=>$lines) {
          foreach ($lines as $k_=>$val) {
            if (strpos($k_,".")) {
              $tmp = explode(".",$k_);
              if($tmp[0] == "facture" && $tmp[1]=="id_societe_fk"){
                $response[$k]["ref_client"] = ATF::societe()->select($val,"ref");
              }
              $response[$k][$tmp[1]] = $val;

              unset($response[$k][$k_]);
            }
          }

        }

        $return =  $response;
      }

       elseif(($get['id_societe'] ) && (!$get['date_debut'] && !$get['date_fin'] && !$get['numero_facture']) ){
        foreach($result as $item=>$value){
           if($value['id_societe_fk'] == $get['id_societe']){
             array_push($return,$value);
           }
        }
      }elseif($get['id_societe'] && ($get['numero_facture'] && !$get["date_debut"] && !$get['date_fin'])){
        foreach($result as $item=>$value){

          if(strlen($get['numero_facture']) == 7){
            if(($value['id_societe_fk'] == $get['id_societe']) && (substr($value['ref'], 0, 7) == substr($get['numero_facture'], 0, 7))){
              array_push($return,$value);
            }
          }else{
            if(($value['id_societe_fk'] == $get['id_societe']) && ($value['ref'] == $get['numero_facture'])){
              array_push($return,$value);
            }
          }
        }
      }elseif($get['id_societe'] && $get["date_debut"] && $get['date_fin'] && !$get['numero_facture']){
        $datedebut = explode('-',$get['date_debut']);
        $datefin = explode('-',$get['date_fin']);

        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];
        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_debut_periode,"AND",false,">=")
            ->where('facture.date',$date_fin_periode,"AND",false,"<=");
        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();

          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }
          foreach($societes as $item){
            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'];
            }
          }

        }

        foreach($result as $item){
          if($item['id_societe_fk'] == $get['id_societe']){
            array_push($return,$item);
          }
        }

      }elseif($get['numero_facture'] && $get["date_debut"] && $get['date_fin'] && !$get['id_societe']){
        $datedebut = explode('-',$get['date_debut']);
        $datefin = explode('-',$get['date_fin']);

        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];
        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_debut_periode,"AND",false,">=")
            ->where('facture.date',$date_fin_periode,"AND",false,"<=");
        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();

          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }


          foreach($societes as $item){

            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'];
            }
          }

        }


        foreach($result as $item){
          if(strlen($get['numero_facture']) == 7){
            if(substr($item['ref'], 0, 7) == substr($get['numero_facture'], 0, 7) ){
              array_push($return,$item);
            }
          }else{
            if($item['ref'] == $get['numero_facture']){
              array_push($return,$item);
            }
          }
           
         
        }


      }elseif($get['numero_facture'] && $get["date_debut"] && $get['date_fin'] && $get['id_societe']){
        $datedebut = explode('-',$get['date_debut']);
        $datefin = explode('-',$get['date_fin']);

        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];
        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_debut_periode,"AND",false,">=")
            ->where('facture.date',$date_fin_periode,"AND",false,"<=");
        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();

          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }


          foreach($societes as $item){

            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'];
            }
          }

        }


        foreach($result as $item){
          if(strlen($get['numero_facture']) == 7){
            if(substr($item['ref'], 0, 7) == substr($get['numero_facture'], 0, 7) && $item['id_societe_fk'] == $get['id_societe']){
              array_push($return,$item);
            }
          }else{
            if($item['ref'] == $get['numero_facture'] && $item['id_societe_fk'] == $get['id_societe']){
              array_push($return,$item);
            }
          }
            
        }


      }elseif($get["date_debut"] && $get['id_societe'] && !$get['date_fin']  && !$get['numero_facture']){
        $datedebut = explode('-',$get['date_debut']);
        $datefin = explode('-',$get['date_fin']);

        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];
        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_debut_periode,"AND",false,">=");

        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();

          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }

          foreach($societes as $item){

            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'];
            }
          }

        }

        foreach($result as $item){
          if($item['id_societe_fk'] == $get['id_societe']){
            array_push($return,$item);
          }
        }


      }elseif($get['date_fin'] && $get['id_societe'] && !$get["date_debut"]  && !$get['numero_facture']){
        $datedebut = explode('-',$get['date_debut']);
        $datefin = explode('-',$get['date_fin']);

        $date_debut_periode = $datedebut[2]."-".$datedebut[1]."-".$datedebut[0];
        $date_fin_periode = $datefin[2]."-".$datefin[1]."-".$datefin[0];

        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->where('facture.date',$date_fin_periode,"AND",false,"<=");

        $result= ATF::facture()->sa();

        foreach($result as $key=>$value){
          ATF::societe()->q->reset()->where('id_societe',$value['id_societe']);
          $societes = ATF::societe()->select_all();

          if($value['id_affaire']){
            ATF::affaire()->q->reset()->where('affaire.id_affaire',$value['id_affaire']);
            $affaires = ATF::affaire()->sa();
          }

          foreach($societes as $item){

            $result[$key]['ref_client'] = $item['ref'];
            $result[$key]['id_societe_fk'] = $item['id_societe'];
            $result[$key]['prix_ttc'] = $result[$key]['prix'];
            $result[$key]['id_societe'] = $item['societe'];

            foreach($affaires as $affaire){
              $result[$key]['id_affaire'] = $affaire['affaire'];
            }
          }

        }

        foreach($result as $item){
          if($item['id_societe_fk'] == $get['id_societe']){
            array_push($return,$item);
          }
        }


      }




      return  $return;
    }

    public function _sortable($get){

      if($get['tri'] == "id_societe" && $get['trid']=="desc"){
        ATF::facture()->q->reset()
            ->where('facture.etat','impayee')
            ->where('facture.id_termes',24,'AND',false,"!=")
            ->where('facture.id_termes',25,'AND',false,"!=")
            ->addOrder("facture.id_societe", "DESC");

      }elseif($get['tri'] == "id_societe" && $get['trid']=="asc"){
        ATF::facture()->q->reset()
        ->where('facture.etat','impayee')
        ->where('facture.id_termes',24,'AND',false,"!=")
        ->where('facture.id_termes',25,'AND',false,"!=")
        ->addOrder("facture.id_societe", "ASC");
      }


      $response = ATF::facture()->select_all();


      foreach ($response as $k=>$lines) {
        foreach ($lines as $k_=>$val) {
          if (strpos($k_,".")) {
            $tmp = explode(".",$k_);
            if($tmp[0] == "facture" && $tmp[1]=="id_societe_fk"){
              $response[$k]["ref_client"] = ATF::societe()->select($val,"ref");
            }
            $response[$k][$tmp[1]] = $val;

            unset($response[$k][$k_]);
          }
        }

      }


      return $response;

    }


    public function _paymentLettrageFacture($get,$post,$files){

      $date = explode('-',$post['date_paiement']);

      $date_payment = $date[2]."-".$date[1]."-".$date[0];

      try {
        ATF::db()->begin_transaction();
        if (!$post['refs_facture']) throw new errorATF("Aucune références de factures", 500);
        if (!$post['date_paiement']) throw new errorATF("Aucune date de paiement", 500);
        if (!$post['mode_paiement']) throw new errorATF("Mode de paiement manquant", 500);
        $total =0;
        foreach ($post['refs_facture'] as $key=>$r) {
          $facture = ATF::facture()->getByRef($r);
            if (!$facture) throw new errorATF("Facture non trouvée", 500);
           $total +=$facture['prix_ttc'];
        }

        //if ($mode_paiement == "cheque" && !$numero_cheque)  throw new errorATF("Veuillez renseigner le numero de cheque", 500);
        foreach ($post['refs_facture'] as $key=>$r) {

            $facture = ATF::facture()->getByRef($r);
            if (!$facture) throw new errorATF("Facture non trouvée", 500);
            if ($facture['facture.etat'] != "impayee") throw new errorATF("Facture déjà payé ou alors pas en impayée.", 500);

            $paiement = array(
              "id_facture" => $facture['facture.id_facture'],
              "montant" => $facture['prix_ttc'],
              "date" => $date_payment,
              "mode_paiement" => $post['mode_paiement'],
              "num_cheque"=> $post['mode_paiement'] == "cheque"?  $post['numero_cheque']: "",
              "remarques"=> $post['remarque']? $post['remarque']: "Rapprochement comptable via import Telescope - ".number_format($total,2, ',', ' ')." €",
            );

            $paiement["filestoattach"]["fichier_joint"] = true;


            // Appel de l'insert pour gérer les traitements post paiements : passage de la facture en payé, de la commande en terminée et de l'affaire en terminée. Puis calcul des intérêts
            $id = ATF::facture_paiement()->insert($paiement);



        }
      } catch (errorATF $e){
        ATF::db()->rollback_transaction();
        throw $e;
      }
      ATF::db()->commit_transaction();
    }

};

?>