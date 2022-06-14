<?php
// Instructions: https://github.com/SlimPay/hapiclient-php
require_once dirname(__FILE__)."/../../libs/SlimPay/vendor/autoload.php";

use \HapiClient\Http;
use \HapiClient\Hal;

class slimpay {


    /**
    * Permet creer une connection pour nos appels SLIMPAY
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
    public function connection(){

        ATF::constante()->q->reset()->where("constante", "%SLIMPAY%", "AND", false, LIKE);
        $slimpay_constante = ATF::constante()->select_all();

        $api_data["__API_SLIMPAY_URL__"] = NULL;
        $api_data["__API_SLIMPAY_ID__"] = NULL;
        $api_data["__API_SLIMPAY_SECRET__"] = NULL;


        foreach ($slimpay_constante as $key => $value) {
            if ($value["constante"] === '__API_SLIMPAY_URL__')      $api_data["__API_SLIMPAY_URL__"] = $value["valeur"];
            if ($value["constante"] === '__API_SLIMPAY_ID__')       $api_data["__API_SLIMPAY_ID__"] = $value["valeur"];
            if ($value["constante"] === '__API_SLIMPAY_SECRET__')   $api_data["__API_SLIMPAY_SECRET__"] = $value["valeur"];
        }

        if ($api_data["__API_SLIMPAY_URL__"] !== NULL &&  $api_data["__API_SLIMPAY_ID__"] !== NULL &&  $api_data["__API_SLIMPAY_SECRET__"] !== NULL) {

            // The HAPI Client
            $hapi_client =  new Http\HapiClient(
                $api_data["__API_SLIMPAY_URL__"],
                '/',
                'https://api.slimpay.net/alps/v1',
                new Http\Auth\Oauth2BasicAuthentication(
                    '/oauth/token',
                    $api_data["__API_SLIMPAY_ID__"],
                    $api_data["__API_SLIMPAY_SECRET__"]
                )
            );
            log::logger("API DATA" , "slimpay.log");
            log::logger($api_data , "slimpay.log");
            return $hapi_client;

        }
        else {
            throw new errorATF("slimpay_constante_missing",500);
        }



    }

    /**
    * Retourne de le relation Namespace
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
    public function getRelationNamespace(){
        return 'https://api.slimpay.net/alps#';
    }


    public function createDebit($ref_mandate,$amount,$libelle,$date,$paymentReference){
        log::logger("createDebit" , "mfleurquin");

        $hapiClient = self::connection();

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        ATF::constante()->q->reset()->where("constante", "__API_SLIMPAY_CREDITOR_REFERENCE__");
        $creditor_slimpay = ATF::constante()->select_row();

        if ($creditor_slimpay) {


            $rel = new Hal\CustomRel($relNs . 'create-payins');
            $follow = new Http\Follow($rel, 'POST', null, new Http\JsonBody(
            [
                'scheme' => 'SEPA.DIRECT_DEBIT.CORE',
                'amount' => $amount,
                'currency' => 'EUR',
                'label' =>  $libelle,
                'executionDate'=>$date."T13:00:00.000+0000",
                'creditor' => [
                    'reference' => $creditor_slimpay["valeur"],
                ],
                'mandate' => [
                    'reference' => $ref_mandate
                ]
            ]
            ));
            log::logger($follow,"slimpay.log");
            $res = $hapiClient->sendFollow($follow);
            log::logger($res , "slimpay.log");
            $state = $res->getState();
            log::logger($state , "slimpay.log");
            return $state;
        } else {
            throw new errorATF("slimpay_creditor_reference_missing", 500);
        }

    }



    public function getPaymentIssue(){
        log::logger("Payment Issue" , "mfleurquin");

        try{
            $hapiClient = self::connection();
        }catch(errorATF $e) {
            return $e;
        }

        log::logger("--HAPI Client","slimpay.log");
        log::logger($hapiClient,"slimpay.log");

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        $rel = new Hal\CustomRel($relNs . 'search-payment-issues');

        ATF::constante()->q->reset()->where("constante", "__API_SLIMPAY_CREDITOR_REFERENCE__");
        $creditor_slimpay = ATF::constante()->select_row();

        if ($creditor_slimpay) {
            $follow = new Http\Follow(new Hal\CustomRel($rel), 'GET', [
                'creditorReference' => $creditor_slimpay["valeur"],
                'scheme' => 'SEPA.DIRECT_DEBIT.CORE'
            ]);
            $collection = $hapiClient->sendFollow($follow);


            while ($collection->getState()['page']['totalElements'] > 0) {
                foreach ($collection->getEmbeddedResources('paymentIssues') as $issue) {
                    log::logger($issue , "mfleurquin");


                    $issueState = $issue->getState();
                    log::logger('Issue: ' , "mfleurquin");
                    log::logger($issueState , "mfleurquin");

                    $id = $issueState['id'];
                    $rejectAmount = $issueState['rejectAmount'];
                    $currency = $issueState['currency'];
                    $returnReasonCode = $issueState['returnReasonCode'];

                    // You may need the initial payment which caused the issue
                    $rel = new Hal\CustomRel($relNs . 'get-payment');
                    $follow = new Http\Follow(new Hal\CustomRel($rel), 'GET');
                    $initialPayment = $hapiClient->sendFollow($follow, $issue);
                    $initialPaymentState = $initialPayment->getState();
                    log::logger('Initial payment:' , "mfleurquin");
                    log::logger( $initialPaymentState , "mfleurquin");

                    // You may also need the subscriber related to it
                    $rel = new Hal\CustomRel($relNs . 'get-subscriber');
                    $follow = new Http\Follow(new Hal\CustomRel($rel), 'GET');
                    $subscriber = $hapiClient->sendFollow($follow, $initialPayment);
                    $subscriberReference = $subscriber->getState()['reference'];
                    log::logger('Subscriber reference: ' , "mfleurquin");
                    log::logger($subscriberReference , "mfleurquin");

                    // Trigger some action regarding this issue
                    // ...

                    // Then tell us that you acknowledged this issue so it doesn't show in the search again
                    $rel = new Hal\CustomRel($relNs . 'ack-payment-issue');
                    $follow = new Http\Follow(new Hal\CustomRel($rel), 'POST');
                    $hapiClient->sendFollow($follow, $issue);
                    log::logger("Issue $id of $rejectAmount $currency for reason $returnReasonCode has been acknowledged.", "mfleurquin");
                    log::logger("=========================================.", "mfleurquin");
                }
                // Follow the 'self' link to get a fresh list of issues
                $collection = $hapiClient->refresh($collection);
            }
        } else {
            throw new errorATF("slimpay_creditor_reference_missing", 500);
        }



    }


    public function getStatutDebit($id_slimpay){
        try{
            $hapiClient = self::connection();
        }catch(errorATF $e) {
            return $e;
        }
        log::logger("--HAPI Client","slimpay.log");
        log::logger($hapiClient,"slimpay.log");

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        // Follow get-direct-debits
        $rel = new Hal\CustomRel($relNs . 'get-direct-debits');
        $follow = new Http\Follow($rel, 'GET', [
            'id' => $id_slimpay
        ]);
        $res = $hapiClient->sendFollow($follow);

        // The Resource's state
        $state = $res->getState();
        return $state;
    }


    public function simulateIssue($mandate, $montant){
        log::logger("Simulate ISSUE" , "mfleurquin");

        try{
            $hapiClient = self::connection();
        }catch(errorATF $e) {
            return $e;
        }
        // The Relations Namespace
        $relNs = self::getRelationNamespace();


        ATF::constante()->q->reset()->where("constante", "__API_SLIMPAY_CREDITOR_REFERENCE__");
        $creditor_slimpay = ATF::constante()->select_row();

        if ($creditor_slimpay) {
            $rel = new Hal\CustomRel(self::getRelationNamespace().'create-payins');
            $follow = new Http\Follow($rel, 'POST', null, new Http\JsonBody(
            [
                'scheme' => 'SEPA.DIRECT_DEBIT.CORE',
                'amount' => $montant,
                'creditor' => [
                    'reference' => $creditor_slimpay["valeur"],
                ],
                'mandate' => [
                    'reference' => $mandate
                ]
            ]
            ));
            //$payment = $hapiClient->sendFollow($follow);

            for ($i = 1; $i <= 14; $i++) {
                $payment = $hapiClient->sendFollow($follow);
                log::logger($i." Payment " . $payment->getState()['id'] . ' created.' , "mfleurquin");
            }
        } else {
            throw new errorATF("slimpay_creditor_reference_missing", 500);
        }


    }

    public function updateAllBankMobilityEntites($path)
    {
        $dir = new DirectoryIterator($path);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $path .= "/{$fileinfo->getFilename()}";
                $this->readBmnCsv($path);
            }
        }
    }

    private function readBmnCsv($csvPath)
    {
        function read($csv){
            $file = fopen($csv, 'r');
            while (!feof($file) ) {
                $line[] = fgetcsv($file, 1024);
            }
            fclose($file);
            return $line;
        }
        
        // Définir le chemin d'accès au fichier CSV
        $csv = $csvPath;
        
        $csv = read($csv);
        array_shift($csv);
        array_splice($csv, -2);
        
        // Keys des champs du CSV correspondant aux :
        // - RUM
        // - IBAN et BIC (ancien et nouveau)
        $allowed = [2, 10, 11, 13, 14];
        
        $filteredArray = [];
        foreach ($csv as $key => $value) {
            $valueAsString = implode("", $value);
            $tmpFieldsAsArray = explode(";", $valueAsString);

            array_push($filteredArray, array_values(array_filter($tmpFieldsAsArray, 
            function($k) use ($allowed) {
                return in_array($k, $allowed);
            },
            ARRAY_FILTER_USE_KEY)));
        }

        $this->updateAffaireAndSocieteIbanAndBic($filteredArray);
    }

    private function updateAffaireAndSocieteIbanAndBic($filteredArray) {
        try {
            foreach ($filteredArray as $key => $value) {
                ATF::societe()->q->reset()->where("RUM", $value[0]);
                $societe = ATF::societe()->select_row();
		    	ATF::societe()->u(array("id_societe" => $societe["id_societe"], "IBAN"=> $value[4], "BIC"=> $value[3]));

                ATF::affaire()->q->reset()->where("RUM", $value[0]);
                $affaires = ATF::affaire()->sa();
                foreach ($affaires as $key => $affaire) {
                    ATF::affaire()->u(array("id_affaire" => $affaire["id_affaire"], "IBAN"=> $value[4], "BIC"=> $value[3]));
                }
            }
        } catch (errorATF $e) {
            echo $e->getMessage();
        }
    }
}
?>

