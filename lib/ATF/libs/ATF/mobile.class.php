<?php
/**
* @date 2011-01-23
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class mobile {
    
    protected $accept;
    protected $userAgent;
    
    protected $isMobile     = false;
    protected $isAndroid    = null;
    protected $isBlackberry = null;
    protected $isOpera      = null;
    protected $isPalm       = null;
    protected $isWindows    = null;
    protected $isGeneric    = null;

    protected $devices = array(
        "android"       => "android",
        "blackberry"    => "blackberry",
        "iphone"        => "(iphone|ipod)",
        "opera"         => "opera mini",
        "palm"          => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)",
        "windows"       => "windows ce; (iemobile|ppc|smartphone)",
        "generic"       => "(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)"
    );

    public function __construct() {
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->accept    = $_SERVER['HTTP_ACCEPT'];

        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])|| isset($_SERVER['HTTP_PROFILE'])) {
            $this->isMobile = true;
        } elseif (strpos($this->accept,'text/vnd.wap.wml') > 0 || strpos($accept,'application/vnd.wap.xhtml+xml') > 0) {
            $this->isMobile = true;
        } else {
            foreach ($this->devices as $device => $regexp) {
                if ($this->isDevice($device)) {
                    $this->isMobile = true;
                }
            }
        }
    }

    /**
     * Overloads isAndroid() | isBlackberry() | isOpera() | isPalm() | isWindows() | isGeneric() through isDevice()
     *
     * @param string $name
     * @param array $arguments
     * @return bool
     */
    public function __call($name, $arguments) {
        $device = substr($name, 2);
        if ($name == "is" . ucfirst($device)) {
            return $this->isDevice($device);
        } else {
            throw new errorATF("Method $name not defined", 9028);
        }
    }


    /**
     * Returns true if any type of mobile device detected, including special ones
     * @return bool
     */
    public function isMobile() {
        return $this->isMobile;
    }


    protected function isDevice($device) {
        $var    = "is" . ucfirst($device);
        $return = $this->$var === null ? (bool) preg_match("/" . $this->devices[$device] . "/i", $this->userAgent) : $this->$var;

        if ($device != 'generic' && $return == true) {
            $this->isGeneric = false;
        }

        return $return;
    }
	
    /**
	* Portail qui redirige vers la bonne commande demandé par l'utilisateur
	* @return bool
	*/
	public function cmd($cmd) {
		switch ($cmd) {
			case 's':
				self::getSociete();
			break;
			default:
				if (method_exists($this,$cmd)) {
					self::$cmd();
				}
			break;
		}
	}
	 
    /**
	* Renvoi toutes les sociétés pour la page société
	* @return array
	*/
	private function getSociete() {
		if (ATF::$usr->getID()) {
			ATF::societe()->q->reset()
								->addField("societe")
								->addField("id_societe")
								->addOrder("societe");
			$data=ATF::societe()->select_all();
			foreach ($data as $k => $i) {
				$societe = substr(ucfirst(util::removeAccents(trim($i["societe"]))),0,1);
				$data[$k]["indexAlpha"]=$societe;
			}
		}

		echo json_encode($data);
	}
	 
	
}
?>