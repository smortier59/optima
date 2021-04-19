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
	
        $filepath = $this->filepath(ATF::$usr->getID(), "fichier_joint", true);

        if(file_exists($filepath) && filesize($filepath) !=0){
						$arr = array();

						$xmlData = json_decode(json_encode(simplexml_load_file($filepath)),true);
            
            if (count($xmlData) !=0 ) {
                $i=0;
                foreach ($xmlData['CstmrDrctDbtInitn']['PmtInf']['DrctDbtTxInf'] as $elem) {
                  ATF::facture()->q->reset()->where("facture.ref", $elem['PmtId']['InstrId']);
			            $facture = ATF::facture()->select_row();

                  $id_societe = $facture["facture.id_societe_fk"];

                  if(!empty($id_societe)){
                    ATF::societe()->q->reset()->where("id_societe",$id_societe);
                    $societe = ATF::societe()->select_row();
                  }
                  

                  $ref_client = $societe["ref"];
                  $name_societe = $societe["societe"];

                  $arr[$i]['montant'] = $elem['InstdAmt'];
                  $arr[$i]['date_facture'] = $elem['DrctDbtTx']['MndtRltdInf']['DtOfSgntr'];
                  $arr[$i]['rum'] = $elem['DrctDbtTx']['MndtRltdInf']['MndtId'];
                  $arr[$i]['id_facture'] = $elem['PmtId']['InstrId'];


                  if($elem['PmtId']['InstrId'] == $facture["facture.ref"]){
                    $arr[$i]['name_client'] = $name_societe;
                    $arr[$i]['ref_client'] = $ref_client;
                  }else{
                    $arr[$i]['name_client'] = $elem['Dbtr']['Nm'];
                  }
                 
                 ++$i;
                }
								return $arr ;
            }else{
              throw new errorATF('Votre fichier est vide', 500);
						}
        }else{
					throw new errorATF("Le fichier n'existe pas", 500);
        }


        // Traiter le fichier XML
    }

    public function _payments($get,$post){
     if($post){
      return true;
     }

    }
    
	
};


?>