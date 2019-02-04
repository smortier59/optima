<?
class produit_fournisseur_test  extends ATF_PHPUnit_Framework_TestCase {

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_construct(){
        $c = new produit_fournisseur();
        $this->assertTrue($c instanceOf produit_fournisseur, "L'objet produit_fournisseur n'est pas de bon type");
    }



};

?>