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
        $hapiClient = self::connection();
        log::logger("--HAPI Client","slimpay.log");
        log::logger($hapiClient,"slimpay.log");

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        // Follow create-direct-debits
        $rel = new Hal\CustomRel($relNs . 'create-direct-debits');
        $follow = new Http\Follow($rel, 'POST', null, new Http\JsonBody(
        [
            'amount' => $amount,
            'currency' => 'EUR',
            'paymentReference' => $paymentReference,
            'label' => $libelle,
            'executionDate'=>$date."T13:00:00.000+0000",
            'creditor' => [
                'reference' => __CREDITOR_REFERENCE__
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

        // The Resource's state
        $state = $res->getState();
        return $state;
    }





    public function simulateIssue($mandate){
        $hapiClient = self::connection();
        // The Relations Namespace
        $relNs = self::getRelationNamespace();


        //$rel = new Hal\CustomRel('https://api.slimpay.net/alps#create-payins');
        /*$follow = new Http\Follow($rel, 'POST', null, new Http\JsonBody(
        [
            'scheme' => 'SEPA.DIRECT_DEBIT.CORE',
            'amount' => 29.99,
            'creditor' => [
                'reference' => __CREDITOR_REFERENCE__
            ],
            'mandate' => [
                'reference' => $mandate
            ]
        ]
        ));

        for ($i = 1; $i <= 4; $i++) {
            $payment = $hapiClient->sendFollow($follow);
            echo "$i. Payment " . $payment->getState()['id'] . ' created.\n';
        }*/

        $follow = new Http\Follow(new Hal\CustomRel(self::getRelationNamespace().'search-payment-issues'), 'GET', [
            'creditorReference' => __CREDITOR_REFERENCE__,
            'scheme' => 'SEPA.DIRECT_DEBIT.CORE',
            'executionStatus' => 'toprocess'
        ]);
        $collection = $hapiClient->sendFollow($follow);

        while ($collection->getState()['page']['totalElements'] > 0) {
            foreach ($collection->getEmbeddedResources('paymentIssues') as $issue) {
                // Some information about the issue itself
                $issueState = $issue->getState();
                echo '<pre>Issue:<br>' . print_r($issueState, true) . '</pre>';

                log::logger($issueState , "mfleurquin");

                /*$id = $issueState['id'];
                $rejectAmount = $issueState['rejectAmount'];
                $currency = $issueState['currency'];
                $returnReasonCode = $issueState['returnReasonCode'];*/
            }
        }



    }

}
?>