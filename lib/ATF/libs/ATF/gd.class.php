<?php
/**
* La classe gd est conçue pour faciliter la gestion des images
* Elle permettra de redimensionner et de stocker dans un répertoire /photos/ toutes les images.
*
* @date 2008-11-03
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
class gd {
	
	/**
	* Le moteur de cache peut rendre les miniatures accessibles via le portail web,
	* simplement en créant des liens symboliques dans le répertoire dédié à la cache (évite de complexifier la méthode cleanThumb)
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var string
	* @example "www/images/cache/"
	*/
	private static $cachePath = "";
	
	/**
	* Redirection vers une image de "?" lorsqu'aucun n'est trouvée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @var string
	* @example "/images/unknown.png"
	*/
	private static $noImagePath = "";
	
	/**
	* Définir le chemin de cache publique
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $path
	*/	
	public static function setCachePath($path) {
		self::$cachePath = $path;
	}
	
	/**
	* Définir le chemin de l'image de garde générique
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $path
	*/	
	public static function setNoImagePath($path) {
		self::$noImagePath = $path;
	}
	
	/**
	* Donne le chemin de l'image de garde générique
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return string Le path
	*/	
	public static function getNoImagePath() {
		return self::$noImagePath;
	}
	
	/**
	* Stock une photo sur le serveur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $item $_FILES
	* @param string $table 
	* @param integer $id Id de l'enregistrement
	* @param string $field Nom du champ 
	* @return boolean Vrai si cela s'est correctement passé, Faux sinon
	*/	
	public static function storePhoto($item,$table,$id,$field) {
		$filepath = ATF::$table()->filepath($id,$field);
		unlink($filepath);
		$url = $filepath."*";
		$bash = `rm -f $url`;
		return util::copy($item["tmp_name"],$filepath);
	}
	
	/**
	* Vérifie l'existence d'
	* une photo sur le serveur
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $item $_FILES
	* @param string $table 
	* @param integer $id Id de l'enregistrement
	* @param string $field Nom du champ 
	* @return boolean Vrai si cela s'est correctement passé, Faux sinon
	*/	
	public static function photoExists($table,$id,$field) {	
		$url = ATF::$table()->filepath($id,$field);
		return file_exists($url)?$url:false;
	}
	
	public static function photoUnlink($table,$id,$field) {
		$url = ATF::$table()->filepath($id,$field);
		return unlink($url);
	}
	
	/**
	* Supprime toutes les miniatures de cette photo
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param integer $id_societe Société
	* @param integer $id_partie Partie
	* @param integer $filename Dernière partie du nom de fichier, peut être un id_propriete_option ou autre...
	* @return boolean Vrai si cela s'est correctement passé, Faux sinon
	*/	
	public static function cleanThumbs($table,$id,$field) {
		$url = ATF::$table()->filepath($id,$field);
		return util::rm($url."*");
	}
	
	/**
	* Créer une miniature à partir d'un nom de table, de champ et une clé d'enregistrement
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $table
	* @param integer $id
	* @param string $field
	* @param integer $width Largeur max
	* @param integer $height Hauteur max
	* @param string $format Extension du format de sortie  gif|png|jpg
	* @return boolean Vrai si cela s'est correctement passé, Faux sinon
	*/	
	public static function createThumb($table,$id,$field,$width=NULL,$height=NULL,$format="png",$codename=false) {
		$format = strtolower($format);
		if(method_exists(ATF::$table(),"getFilepath")){
			$url_orig = ATF::$table()->getFilepath($id);
		}else{
			$url_orig = ATF::$table()->filepath($id,$field,false,$codename);
		}
		
		// Image de garde
		if (self::$noImagePath && !file_exists($url_orig)) {
			$url_orig = self::$noImagePath;
		}
		
		$url = ATF::$table()->filepath($id,$field,false,$codename).".".$width."-".$height;
		
				
		if (!file_exists($url) || filectime($url_orig)>filectime($url)) {
			return self::conversion($url_orig,$url,$width,$height,$format);
		} else {
			self::makeCacheSymlink($url,$format);
			return $url;
		}
	}
	
	/**
	* Renvoi les dimensions d'une image
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param string $fp Chemin de l'image
	* @return array Toute les infos sur l'image
	*/	
	public function getDimension($fp) {
		$info = getimagesize($fp);
		$r = array(
			"w"=>$info[0]
			,"h"=>$info[1]
		);
		return $r;
	}
	
	
	/**
	* Créer une miniature ou conversion de fichier
	* ATTENTION : Ecrase la target !
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $source Chemin source
	* @param string $target Chemin cible
	* @param integer $width Largeur max
	* @param string $format Extension du format de sortie  gif|png|jpg
	* @return boolean Vrai si cela s'est correctement passé, Faux sinon
	*/	
	public static function conversion($source,$target,$width=NULL,$height=NULL,$format="png") {
		$return = false;
		if (file_exists($target)) {
			// Si la cible existe, on la supprime !
			unlink($target);
		}
		if ($src_info = getimagesize($source)) {
			switch ($src_info[2]) {
				case 1:
					$src_img = imagecreatefromgif($source);
					break;
					
				case 2:
					$src_img = imagecreatefromjpeg($source);
					break;
					
				case 3:
					$src_img = imagecreatefrompng($source);
					break;
				
				default:
					return false;
			}
			
			// Pas de redimensionnement
			$final=$src_info;
			$ratio=array(1,1);
			if ($width && $final[0]>$width) {
				// Si la largeur trop grande, on réduit en se calant sur la largeur max
				$finalW[0]=$width;
				$finalW[1]=$width;
				$ratioW[0] = $finalW[0]/$src_info[0];
				$ratioW[1] = $finalW[1]/$src_info[1];
				$finalW[1] = $src_info[1]*$ratioW[0];
				$finalW[0] = $src_info[0]*$ratioW[0];
				$final=$finalW;
				$ratio=$ratioW;
			}
			if ($height && $final[1]>$height) {

				// Si la hauteur trop grande, on réduit en se calant sur la hauteur max
				$finalH[0]=$height;
				$finalH[1]=$height;
				$ratioH[0] = $finalH[0]/$src_info[0];
				$ratioH[1] = $finalH[1]/$src_info[1];
				$finalH[1] = $src_info[1]*$ratioH[1];
				$finalH[0] = $src_info[0]*$ratioH[1];
				$final=$finalH;
				$ratio=$ratioH;
			}
			
			$dst_img = imagecreatetruecolor($final[0],$final[1]);
			imagealphablending($dst_img, false);
			imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $final[0],$final[1], $src_info[0], $src_info[1]);
			imagealphablending($dst_img, true);
			imagesavealpha($dst_img, true);
			util::mkdir(dirname($target));
			if (is_writable(dirname($target))) {
				switch ($format) {
					case "gif":
						imagegif($dst_img, $target);
						break;
						
					case "jpg":
						imagejpeg($dst_img, $target, 100);
						break;
						
					case "png":
						imagepng($dst_img, $target, 9);
						break;
				}
				imagedestroy($src_img);
				imagedestroy($dst_img);
				if (file_exists($target)) {
					$return = $target;
					self::makeCacheSymlink($target,$format);
				}
			} else {
				throw new errorATF(dirname($target)." is not writable !",777);
			}
		}
		return $return;
	}
	
	/**
	* Créer ule lien symbolique dans la cache
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $target
	* @param string $format
	*/	
	function makeCacheSymlink($target,$format) {		
		// Création du lien symbolique de cache publique
		if (self::$cachePath && is_dir(self::$cachePath)) {
			$module = basename(dirname($target));
			$symlinkPath = self::$cachePath.$module."/".basename($target).".".$format;
			if (!file_exists($symlinkPath)) {
				util::mkdir(dirname($symlinkPath));
				symlink($target,$symlinkPath);
			}
		}
	}
	
	function chart($infos,$s) {
		$type = $infos['type']?$infos['type']:ATF::_g("type");
		$w = $infos['w']?$infos['w']:400;
		$h = $infos['h']?$infos['h']:400;
		if (!$type) return false;

		switch ($type) {
			case 'camembert':
				require_once(__LIBS_PATH__.'ATF/libs/Artichow-php5/Pie.class.php');
				$graph = new Graph($w, $h);
				
				//Change la position de l'ombre. $position peut prendre les valeurs Shadow::LEFT_TOP, Shadow::RIGHT_TOP, Shadow::LEFT_BOTTOM ou Shadow::RIGHT_BOTTOM.
				//$graph->shadow->setPosition(Shadow::RIGHT_BOTTOM);
				//$graph->shadow->setSize(40);
				
				// Background color du graph en mode dégradé.
				$graph->setBackgroundGradient(
					new LinearGradient(
						new Color(200, 200, 200, 0),
						new White,
						0
					)
				);
				
				
				// On affecte les data au camembert				
				$pie = new Pie(array_values($infos['data']),Pie::EARTH);
				
				// Bordure noire		
				$pie->setBorderColor(new Black);
				
				// Change la précision des étiquettes associées à chaque part du camembert. Par défaut à 0, cette précision donne le nombre de chiffres après la virgule à afficher.
				$pie->setLabelPrecision(0);
				
				// Création et Positionnement de la légende
				$pie->setLegend(array_keys($infos['data']));
				$pie->legend->setPosition(1.23, 0);
				
				// Positionnement du centre du camembert en %
				$pie->setCenter(.40, .55);
				
				// Taille du camembert en %
				$pie->setSize(.80, .80);
				
				// Ecriture et positionnement du titre
				if (!isset($infos['title'])) $infos['title']="Graph";
				$pie->title->set($infos['title']);
				$pie->title->move(-65, -40);
				$pie->title->setFont(new TuffyBold(14));
				$pie->title->setBackgroundColor(new White(50));
				$pie->title->setPadding(5, 5, 2, 2);
				$pie->title->border->setColor(new Black());
				
				$graph->add($pie);
				
				if (isset($infos['filename'])) {
					$graph->draw($infos['filename']);
				} else {
					$graph->draw();
				}
			break;
		}
	}
};
?>