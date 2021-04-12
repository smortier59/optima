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
        log::logger("HERE", "dsarr");
        try {
            $r = ATF::file()->_POST($get, $post, $files);

            // Deuxième partie : Parsing du XML + renvoi d'un tableau exploitable avec les données
            $this->parseXML();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function parseXML() {
        log::logger('1ère méthode' , 'dsarr');
        $filepath = $this->filepath(ATF::$usr->getID(), "fichier_joint", true);
        log::logger($filepath, "dsarr");

        $arr = array();

        $xmlData = json_decode(json_encode(simplexml_load_file($filepath)),true);

        if ( ! empty($xmlData)) {
            $i=0;
            foreach ($xmlData['CstmrDrctDbtInitn']['PmtInf']['DrctDbtTxInf'] as $elem) {
              $arr[$i]['montant'] = $elem['InstdAmt'];
              $arr[$i]['date_facture'] = $elem['DrctDbtTx']['MndtRltdInf']['DtOfSgntr'];
              $arr[$i]['rum'] = $elem['DrctDbtTx']['MndtRltdInf']['MndtId'];
              $arr[$i]['id_facture'] = $elem['PmtId']['InstrId'];
              $arr[$i]['name_client'] = $elem['Dbtr']['Nm'];
             ++$i;
            }
        }

        // log::logger($xmlData, "dsarr");
        // log::logger($xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->InstdAmt[0], "dsarr");
        // $montantTTC = $xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->InstdAmt[0];
        // $rum = $xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->MndtRltdInf->MndtId[0];
        // $date_facture = $xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->DrctDbtTx->MndtRltdInf->DtOfSgntr[0];

        // log::logger($rum,"dsarr");
        // log::logger($date_facture,"dsarr");
        


        return $arr ;

        // Traiter le fichier XML
    }

    public function _payments($get,$post){
     if($post){
         return true;
     }

    }
	
};
?>