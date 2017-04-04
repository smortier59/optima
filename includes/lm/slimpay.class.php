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
                'reference' => 'lma'
            ],
            'mandate' => [
                'reference' => $ref_mandate
            ]
        ]
        ));
        log::logger($follow,"mfleurquin");
        $res = $hapiClient->sendFollow($follow);
        log::logger($res , "mfleurquin");
        $state = $res->getState();
        log::logger($state , "mfleurquin");
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

    /**
    * Permet de créer une demande de débit, signature de document sur SLIMPAY
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
    public function create_order($get, $post){

        log::logger("Create Order","slimpay.log");

        $hapiClient = self::connection();
        log::logger("--HAPI Client","slimpay.log");
        log::logger($hapiClient,"slimpay.log");

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        // Follow create-orders
        $rel = new Hal\CustomRel($relNs .'create-orders');




        $follow = new Http\Follow($rel, 'POST', null, new Http\JsonBody(
        [
            'amount' => '20',
            'currency' => 'EUR',
            'paymentReference' => 'Payment 123',
            'label' => 'The label',
            'creditor' => [
                'reference' => 'lma'
            ],
            'mandate' => [
                'reference' => 'SLMP003362622'
            ]
        ]
        ));

        log::logger($follow, "follow_slimpay");

        log::logger("--follow","slimpay.log");

        try {
            $order = $hapiClient->sendFollow($follow);
            log::logger("--order","slimpay.log");
            log::logger( $order,"slimpay.log");
            // Persistance de $order['reference'] en liaison avec l'abonné connecté
        } catch (\HapiClient\Exception\HttpException $e) {
            log::logger( $e,"slimpay.log");
            $statusCode = $e->getStatusCode();
            $body = $e->getResponse()->json();
            // Plus de détails sur l'erreur dans $body['message']
            throw new Exception($body["message"] , 500);

            //return json_encode(array("error"=>));
        }


        // Redirection vers SlimPay
        $rel = new Hal\CustomRel($relNs .'user-approval');

        $state = $order->getState($rel);
        $ref = $state["reference"];

        //$_SESSION["referenceSlimPay"] = $ref;

        $id_affaire = ATF::commande()->select($_SESSION["commande"] , "id_affaire");

        ATF::affaire()->u(array("id_affaire"=>$id_affaire,
                                "ref_slimpay"=>$ref,
                                "etat"=>"slimpay_en_cours"
                               )
                          );


        $checkoutUrl = $order->getLink($rel)->getHref();
        log::logger("CheckOut URL","slimpay.log");
        log::logger($checkoutUrl,"slimpay.log");

        return $checkoutUrl;
    }


    /**
    * Permet de recevoir les notifications SLIMPAY (paiement accepté ou non, retour du contrat signé)
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
    public function _notification($get, $post){
        log::logger("NOTIFICATION","slimpay.log");


        // The HAPI Client
        $hapiClient = self::connection();

        // The Relations Namespace
        $relNs = self::getRelationNamespace();

        /*try{
            $order->getLink($relNs . 'get-card-transaction');

            log::logger("Facture" , "mfleurquin");
            //On est dans Notification facture

        } catch (\HapiClient\Exception\RelNotFoundException $e) {*/

            log::logger("Souscription" , "mfleurquin");

            $res = Hal\Resource::fromJson(file_get_contents("php://input"));

            $order = $res->getState();
            $orderReference = $order['reference'];
            $state = $order['state'];

            ATF::affaire()->q->reset()->where("ref_slimpay", $orderReference);
            $affaire = ATF::affaire()->select_row();
            $id_affaire = $affaire["id_affaire"];

            log::logger($state,"slimpay.log");

            if (strpos($state, 'closed.completed') === 0) {
                $res = $hapiClient->refresh($res);
                $rel = new Hal\CustomRel($relNs .'get-mandate');
                $follow = new Http\Follow($rel, 'GET');

                try {
                    $mandate = $hapiClient->sendFollow($follow, $res)->getState();
                    log::logger("MANDATE","slimpay.log");
                    log::logger($mandate,"slimpay.log");
                    ATF::suivi()->i(array(
                        "id_affaire" => $id_affaire,
                        "id_societe" => ATF::affaire()->select($id_affaire , "id_societe"),
                        "type" => "notification_slimpay",
                        "public" => "oui",
                        "texte" => json_encode($mandate)
                    ));
                    // Persistance de $order['reference'] en liaison avec l'abonné connecté
                } catch (\HapiClient\Exception\HttpException $e) {
                    log::logger("MANDATE ERROR","slimpay.log");
                    log::logger($e,"slimpay.log");
                    $statusCode = $e->getStatusCode();
                    $body = $e->getResponse()->json();
                }

                // La référence du mandat nécessaire aux futurs prélèvements
                $mandateReference = $mandate['reference'];

                ATF::affaire()->u(array("id_affaire"=>$id_affaire,
                                        "ref_mandate"=>$mandateReference,
                                        "etat"=>"attente_comite"
                                       )
                                  );

                //On ne recupere plus le PDF du mandat (#14498)
                //log::logger("APPEL RECUP MANDAT PDF SLIMPAY" , "mfleurquin");
                //self::recup_pdf_mandate($id_affaire);

                log::logger("APPEL RECUP PDF SLIMPAY" , "mfleurquin");
                self::recup_pdf_slimpay($id_affaire);

                log::logger("APPEL CREATION DE LA TACHE DANS OPTIMA" , "mfleurquin");
                self::create_tache_contrat_ok($id_affaire);


            } else {
                // Aborted
               log::logger("ABORTED","slimpay.log");
               log::logger($state,"slimpay.log");
            }


             ATF::suivi()->i(array("id_affaire"=>$id_affaire,
                                  "id_societe"=>ATF::affaire()->select($id_affaire , "id_societe"),
                                  "type"=>"notification_slimpay",
                                  "public"=>"oui",
                                  "texte"=>json_encode($order)
                                 )
                           );
        //}

    }









}



?>