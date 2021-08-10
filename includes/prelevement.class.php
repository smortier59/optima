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
           unlink($filepath);
            $r = ATF::file()->_POST($get, $post, $files);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function _parseXML() {
      $canPayment = false;
      $filepath = $this->filepath(ATF::$usr->getID(), "fichier_joint", true);
      if(file_exists($filepath) && filesize($filepath) !=0){

        $content = file_get_contents($filepath);
        if(ATF::$codename == "absystech"){
          $pattern = '/ASLI[0-9]{8}/';
          preg_match($pattern, $content, $matches);
          if(count($matches)>0){
            throw new errorATF('Votre fichier est non conforme pour absystech', 500);
          }

        }elseif(ATF::$codename == "att"){
          $pattern = '/[^A]SLI[0-9]{8}/';
          preg_match($pattern, $content, $matches);
          if(count($matches)>0){
            throw new errorATF('Votre fichier est non conforme pour absystech telecom', 500);
          }
        }

        $arr = array();
        $xmlData = json_decode(json_encode(simplexml_load_file($filepath)),true);
        
        $PmtInf = $xmlData['CstmrDrctDbtInitn']['PmtInf'];
        $DrctDbtTxInf = [];
        if (count($PmtInf) > 0 ) {
          $pmt = $PmtInf;
          if (array_key_exists("PmtInfId", $PmtInf)) {
            $pmt = array();
            $pmt[0] = $PmtInf;
          }
          foreach ($pmt as $k=>$i) {
            if ($i['DrctDbtTxInf'] && is_array($i['DrctDbtTxInf'])) {
              foreach ($i['DrctDbtTxInf'] as $k_=>$DrctDbtTxInfElem) {
                $DrctDbtTxInf[] = $DrctDbtTxInfElem;
              }
            }
          }
  
          foreach ($DrctDbtTxInf as $DrctDbtTxInfElem) {					
            $rum = $DrctDbtTxInfElem['DrctDbtTx']['MndtRltdInf']['MndtId'];
            $montant = $DrctDbtTxInfElem['InstdAmt'];
            $date_facture = $DrctDbtTxInfElem['DrctDbtTx']['MndtRltdInf']['DtOfSgntr'];
            $id_facture = $DrctDbtTxInfElem['PmtId']['InstrId'];
  
            // On éclate la RUM pour chopper le SLI de la société
            if (ATF::$codename == "att") {
              $pattern = '/ASLI[0-9]{8}/';
            } else {
              $pattern = '/SLI[0-9]{8}/';
              
            }

            preg_match($pattern, $rum, $sli);
            $m = preg_split($pattern, $rum);
            $ref_client = $sli[0];
            $name_societe = $m[0];

            if ($ref_client) {
              ATF::societe()->q->reset()->where('societe.ref',$ref_client);
              $societe = ATF::societe()->select_row();

              if($societe){
                ATF::facture()->q->reset()
                              ->where('facture.id_societe', $societe['id_societe'])
                              ->where("facture.etat", "impayee")
                              ->where('facture.id_termes',24)
                              ->where('facture.id_termes',25)
                              ->where('facture.id_termes',38)
                              ->where('facture.id_termes',31);
                
                $factures = ATF::facture()->select_all();

              }
            }else{
              $canPayment = false;
            }
            $arr[] = array(
              'montant'=>$montant,
              'date_facture'=>$date_facture,
              'rum'=>$rum,
              'id_facture'=>$id_facture,
              'name_client'=>$name_societe,
              'ref_client'=>$ref_client,
              'canPayment'=>$canPayment
            );
          
          }
          return $arr ;
        }else{
          log::logger("Fichier vide", "qjanon");
          throw new errorATF('Votre fichier est vide', 500);
        }
      }else{
        log::logger("Fichier inexistant", "qjanon");
        throw new errorATF("Le fichier n'existe pas", 500);
      }
  
    }

    public function _payments($get,$post){
      try {
        if (!$post['refs_factures']) throw new errorATF("Aucune références de factures", 500);
  
        foreach ($post['refs_factures'] as $k=>$ref) {
          // On retrouve la facture
          $facture = ATF::facture()->getByRef($ref);
          if (!$facture) throw new errorATF("Facture non trouvée", 500);
  
          $paiement = array(
            "id_facture" => $facture['id_facture'],
            "montant" => $facture['montant'],
            "date" => date("Y-m-d H:i:s"),
            "mode_paiement" => "prelevement"
          );
  
          // Appel de l'insert pour gérer les traitements post paiements : passage de la facture en payé, de la commande en terminée et de l'affaire en terminée. Puis calcul des intérêts
          $id = ATF::facture_paiement()->insert($paiement);
          
        }
      } catch (errorATF $e){
        throw $e;
      }
      }
  
};


?>