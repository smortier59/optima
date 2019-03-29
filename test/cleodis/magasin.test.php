<?
class magasin_test  extends ATF_PHPUnit_Framework_TestCase {

    /* @author Morgan FLEURQUIN <mfleurquin@absystech.fr> */
    public function test_construct(){
        $c = new magasin();

        $this->assertTrue($c instanceOf magasin, "L'objet magasin n'est pas de bon type");

    }
};

?>