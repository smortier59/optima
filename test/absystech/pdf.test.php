<?
/**
* @author Quentin JANON <qjanon@absystech.fr>
* @date 11-08-2010
*/ 
class pdf_test extends ATF_PHPUnit_Framework_TestCase {

	/**
	* Chemin où est stocké le fichier temporaire pour les tests
	* @access private
	* @var string
	*/
	private $tmpFile = "/tmp/TUPDFTEMPORAIRE_PDF_CLASS.pdf";
	/**
	* Commande Ghost Script permettant la conversion du PDF en image
	* @access private
	* @var string
	*/
	private $GScmd = "";
	/**
	* Commande SHELL pour avoir la résultante d'un fichier en md5
	* @access private
	* @var string
	*/
	private $MD5cmd = "";
	
	/* Ne pas toucher cette fonction ! */
	public function __construct() {
		parent::__construct();
		
		$this->GScmd = "gs -dQUIET -dNOPAUSE -dBATCH -sDEVICE=jpeg -sOutputFile=".str_replace(".pdf",".jpg",$this->tmpFile)." ".$this->tmpFile." 2>&1";
		$this->MD5cmd = "md5sum ".str_replace(".pdf",".jpg",$this->tmpFile);
	}
	
	public function setUp() {
		$this->assertFalse(file_exists($this->tmpFile),"Erreur : Les fichiers temporaires sont déjà présents :/");
	}
	
	/** 
	* Méthode qui supprime les fichiers générés a la fin du TU
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 11-08-2010
	*/
	public function tearDown(){
		ob_start();
		system("rm ".str_replace(".pdf","",$this->tmpFile).".* 2>&1");
		ob_end_clean();
	}
 
	/** 
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 11-08-2010
	*/ 
	public function test_pied() {
		$this->obj->Open();
		$this->obj->addpage();
		$this->obj->setAuthor("Quentin JANON <qjanon@absystech.fr>");
		$this->obj->sety($sety?$sety:15);
		
		$infos = array(
			"adresse"=>"Adresse1"
			,"structure"=>"SARL"
			,"capital"=>"10000"
			,"cp"=>"59390"
			,"ville"=>"LYS"
			,"id_pays"=>1
			,"tel"=>"0320000000"
			,"fax"=>"0320000001"
		);
		$this->obj->pied($infos);
		
		$this->obj->Close();
		$this->obj->Output($this->tmpFile);
		ob_start();
		// Commande SHELL pour générer le fichier
		system($this->GScmd);
		$md5 = system($this->MD5cmd);
		$md5 = substr($md5,0,32);
		ob_get_clean();
		$this->assertEquals("36c28e16caa1cb09044a8a6c1fdff23a",$md5,"Erreur de génération du piede de page du PDF.");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 07-01-2011
	*/ 
	public function test_setFooter() {
		$this->obj->setFooter();
		$this->assertFalse($this->obj->getFooter(),"Ca devrait être false");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 07-01-2011
	*/ 
	public function test_unsetFooter() {
		$this->obj->unsetFooter();
		$this->assertTrue($this->obj->getFooter(),"Ca devrait être true");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 07-01-2011
	*/ 
	public function test_setHeader() {
		$this->obj->setHeader();
		$this->assertFalse($this->obj->getHeader(),"Ca devrait être false");
	}
	
	/**
	* @author Quentin JANON <qjanon@absystech.fr>
	* @date 07-01-2011
	*/ 
	public function test_unsetHeader() {
		$this->obj->unsetHeader();
		$this->assertTrue($this->obj->getHeader(),"Ca devrait être true");
	}
};

?>