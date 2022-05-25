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

    public function updateFactureRejetPayment() {
        ATF::slimpay_transaction()->q->reset()->where("executionStatus", "rejected");
        $rejectedPayments = ATF::slimpay_transaction()->select_all();
        foreach ($rejectedPayments as $key => $value) {
            $customKey;
            switch (json_decode($value['retour'])->code) {
                case 'MS02':
                    $customKey = 'contestation_debiteur';
                    break;
                case 'AM04':
                case '411':
                    $customKey = 'provision_insuffisante';
                    break;
                case '641':
                case 'C11':
                    $customKey = 'opposition_compte';
                    break;
                case '903':
                    $customKey = 'decision_judiciaire';
                    break;
                case 'AC04':
                    $customKey = 'compte_cloture';
                    break;
                case '134':
                    $customKey = 'coor_banc_inexploitable';
                    break;
                case '2011':
                    $customKey = 'pas_dordre_de_payer';
                    break;
                default:
                    $customKey = 'non_preleve';
                    break;
            }
            ATF::facture()->updateEnumRejet(
                array(
                    "id_facture" => $value['id_facture'],
                    "key" => "rejet",
                    "value" => $customKey
                )
            );
        }
    }
}
?>

