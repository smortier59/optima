<?
class Importer {
    public static $files = [];
 
/**
 * importer.class.php
 *
 * Met en memoire les fichiers d'une extension donnee
 *
 * @uses glob()
 *
 * @param string $extension
 *      Extension des fichiers a mettre en memoire
**/
    public static function configure(string $path = '', string $extension) {    
        $constructedPath = $path == '' ? "$path*.$extension" : "./*.$extension";

        foreach (glob($constructedPath) as $file) {
            $fileMetadata = pathinfo($file);
            $object = (object) [ $fileMetadata['filename'] => $fileMetadata['basename']];
            array_push(self::$files, $object);
        }

        return self;
    }
}
?>