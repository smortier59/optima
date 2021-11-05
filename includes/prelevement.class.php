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
              $array[$row]['montant'] = $data[4];

              if (ATF::$codename == "att") {
                $pattern = '/ASLI[0-9]{8}/';
              } else {
                  $pattern = '/SLI[0-9]{8}/';
              }
              preg_match($pattern, $data[29], $sli);
              $m = preg_split($pattern, $data[29]);
              $ref_client = $sli[0];
              $name_societe = $m[0];

              $array[$row]['name_client'] = $name_societe;
              $array[$row]['ref_client'] = $ref_client;
              $array[$row]['canPayment'] = false;
            $row++;
          
          }
        
          fclose($open);
        }

        array_shift($array);

        foreach ($array as $key=>$value) {
          $refs_facture = "";

           //verifier si la ref n'existe pas dans le RUM au niveau du fichier si oui, on recupére le ref_client de la societe via la ref_factures
          if(!$value['ref_client']){

            ATF::facture()->q->reset()->where('facture.ref',$value['refs_facture']);
            $factures = ATF::facture()->select_all();
            foreach($factures as $facture){
              ATF::societe()->q->reset()->where('societe.id_societe',$facture['facture.id_societe_fk']);
              $societes = ATF::societe()->select_all();
              foreach($societes as $item){
                $array[$key]['ref_client'] = $item['ref'];
              }
            }

          }

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
                            if($item['facture.prix'] == $value['montant'] && $item['facture.etat'] == "impayee"){
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
        log::logger("Fichier inexistant", "qjanon");
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
            $dt = DateTime::createFromFormat('d/m/Y', $item['date']);
            $paiement = array(
              "id_facture" => $facture['facture.id_facture'],
              "montant" => $facture['prix_ttc'],
              "date" => $dt->format('Y-m-d H:i:s'),
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
  
};

?>