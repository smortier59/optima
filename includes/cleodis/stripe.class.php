<?php
require_once dirname(__FILE__)."/../../libs/Stripe/vendor/autoload.php";

class stripe {

    /**
    * Permet de s'authentifier pour nos appels STRIPE
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
    public function authentification() {
        ATF::constante()->q->reset()->where("constante", "%STRIPE%", "AND", false, "LIKE");
        $stripeConstante = ATF::constante()->select_all();

        $apiData["__API_STRIPE_URL__"] = NULL;
        $apiData["__API_STRIPE_ID__"] = NULL;
        $apiData["__API_STRIPE_SECRET__"] = NULL;

        foreach ($stripeConstante as $key => $value) {
            if ($value["constante"] === '__API_STRIPE_URL__')      $apiData["__API_STRIPE_URL__"] = $value["valeur"];
            if ($value["constante"] === '__API_STRIPE_ID__')       $apiData["__API_STRIPE_ID__"] = $value["valeur"];
            if ($value["constante"] === '__API_STRIPE_SECRET__')   $apiData["__API_STRIPE_SECRET__"] = $value["valeur"];
        }

        if ($apiData["__API_STRIPE_URL__"] !== NULL &&  $apiData["__API_STRIPE_ID__"] !== NULL &&  $apiData["__API_STRIPE_SECRET__"] !== NULL) {
            return new \Stripe\StripeClient($apiData["__API_STRIPE_SECRET__"]);
        } else {
            throw new errorATF("stripe_constante_missing",500);
        }
    }

    public function createClient($data) {
        $stripe = $this->authentification();
        return $stripe->customers->create([
            'name' => 'Jenny Rosen',
            'email' => 'jennyrosen@example.com',
        ]);
    }

    public function paiementCB($data) {
        $stripe = $this->authentification();
        return $stripe->payouts->create([
            'amount' => 1100,
            'currency' => 'usd',
            'description' => 'Test'
        ]);
    }
}