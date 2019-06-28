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

        // The HAPI Client
        return new Http\HapiClient(
                    __API_URL__,
                    '/',
                    'https://api.slimpay.net/alps/v1',
                    new Http\Auth\Oauth2BasicAuthentication(
                        '/oauth/token',
                        __APP_ID__,
                        __APP_SECRET__
                    )
                );

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
        log::logger("--HAPI Client","slimpay.log");
        log::logger($hapiClient,"slimpay.log");

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        $rel = new Hal\CustomRel($relNs . 'create-payins');
        $follow = new Http\Follow($rel, 'POST', null, new Http\JsonBody(
        [
            'scheme' => 'SEPA.DIRECT_DEBIT.CORE',
            'amount' => $amount,
            'currency' => 'EUR',
            'label' =>  $libelle,
            'executionDate'=>$date."T13:00:00.000+0000",
            'creditor' => [
                'reference' => __CREDITOR_REFERENCE__
            ],
            'mandate' => [
                'reference' => $ref_mandate
            ]
        ]
        ));
        log::logger($follow , "mfleurquin");

        log::logger($follow,"slimpay.log");
        $res = $hapiClient->sendFollow($follow);
        log::logger($res , "slimpay.log");
        $state = $res->getState();
        log::logger($state , "slimpay.log");
        return $state;
    }



    public function getPaymentIssue(){
        log::logger("Payment Issue" , "mfleurquin");

        $hapiClient = self::connection();
        log::logger("--HAPI Client","slimpay.log");
        log::logger($hapiClient,"slimpay.log");

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        $rel = new Hal\CustomRel($relNs . 'search-payment-issues');

        $follow = new Http\Follow(new Hal\CustomRel($rel), 'GET', [
            'creditorReference' => __CREDITOR_REFERENCE__,
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

    }


    public function getStatutDebit($id_slimpay){
        $hapiClient = self::connection();
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

        log::logger($res , "mfleurquin");

        // The Resource's state
        $state = $res->getState();
        return $state;
    }


    public function simulateIssue($mandate, $montant){
        log::logger("Simulate ISSUE" , "mfleurquin");

        $hapiClient = self::connection();
        // The Relations Namespace
        $relNs = self::getRelationNamespace();


        $rel = new Hal\CustomRel(self::getRelationNamespace().'create-payins');
        $follow = new Http\Follow($rel, 'POST', null, new Http\JsonBody(
        [
            'scheme' => 'SEPA.DIRECT_DEBIT.CORE',
            'amount' => $montant,
            'creditor' => [
                'reference' => __CREDITOR_REFERENCE__
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
    }

}
?>

