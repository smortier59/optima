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


    public function simulateIssue($mandate, $montant){
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
        $payment = $hapiClient->sendFollow($follow);

    }


     public function recup_pdf_slimpay($id_affaire){
        log::logger("RECUP PDF SLIMPAY" , "mfleurquin");
        log::logger($id_affaire , "mfleurquin");

        $hapiClient = self::connection();

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        // Follow get-orders
        $rel = new Hal\CustomRel($relNs .'get-orders');
        $follow = new Http\Follow($rel, 'GET', [
            'creditorReference' => __CREDITOR_REFERENCE__,
            'reference' => ATF::affaire()->select($id_affaire , "ref_slimpay ")
        ]);


        $res = $hapiClient->sendFollow($follow);

        // Follow get-document
        $rel = new Hal\CustomRel($relNs .'get-document');
        $follow = new Http\Follow($rel, 'GET');
        $res = $hapiClient->sendFollow($follow, $res);

        // Follow get-binary-content
        $rel = new Hal\CustomRel($relNs .'get-binary-content');
        $follow = new Http\Follow($rel, 'GET');
        $res = $hapiClient->sendFollow($follow, $res);

        $state = $res->getState();

        $pdf = $state["content"];

        ATF::commande()->q->reset()->where("commande.id_affaire",$id_affaire);
        $commande =  ATF::commande()->select_row();

        log::logger($commande , "mfleurquin");

        $data = array("save_contrat"=>true,
                      "id_affaire"=>$id_affaire,
                      "id_commande"=>$commande["id_commande"],
                      "pdf"=>$pdf
                     );



        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, __MANUAL_WEB_PATH__.'devis_lm_insert.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        curl_close($ch);

    }

}
?>