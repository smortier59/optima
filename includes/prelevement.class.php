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
            // $r = ATF::file()->_POST($get, $post, $files);

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

        $xmlData = simplexml_load_file($filepath);
        // log::logger($xmlData, "dsarr");
        log::logger($xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->InstdAmt[0], "dsarr");
        $montantTTC = $xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->InstdAmt[0];
        $rum = $xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->MndtRltdInf->MndtId[0];
        $date_facture = $xmlData->CstmrDrctDbtInitn->PmtInf->DrctDbtTxInf->DrctDbtTx->MndtRltdInf->DtOfSgntr[0];

        log::logger($rum,"dsarr");
        log::logger($date_facture,"dsarr");
        

        $retour = array();

        // Il me faut : Date facture / Num Facture / RUM / Montant TTC / Ref Client / Nom Client

// CstmrDrctDbtInitn > PmtInf > DrctDbtTxInf => Ligne de paiement
// CstmrDrctDbtInitn > PmtInf > DrctDbtTxInf > InstdAmt => Montant TTC
// CstmrDrctDbtInitn > PmtInf > DrctDbtTxInf > DrctDbtTx > MndtRltdInf > MndtId => RUM
// CstmrDrctDbtInitn > PmtInf > ReqdColltnDt => Date de facture ?
// CstmrDrctDbtInitn > PmtInf > DrctDbtTxInf > DrctDbtTx > MndtRltdInf > DtOfSgntr => Date de facture ?
        
    


        // $retour = array(
        //     array(
        //         "date_facture"=>$date_facture,
        //         "num_facture"=>$num_facture,
        //         "rum"=>$rum,
        //         ...
        //     ),
        //     array(
        //         "date_facture"=>$date_facture,
        //         "num_facture"=>$num_facture,
        //         "rum"=>$rum,
        //         ...
        //     ),
        //     array(
        //         "date_facture"=>$date_facture,
        //         "num_facture"=>$num_facture,
        //         "rum"=>$rum,
        //         ...
        //     ),
        // );

        return $xmlData ;

        // Traiter le fichier XML
    }
	
};
?>