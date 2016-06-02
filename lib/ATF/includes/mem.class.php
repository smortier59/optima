<?php
/** 
* Classe memcached pour Optima
* @package ATF
*/
class mem {
	private static $host = "localhost";
	private static $port = 11211;
	private static $prefixKey = "optima-";
	private static $m; // objet memcached

	/**
	* Constructeur
	*/
	public static function cached($key=NULL,$value=NULL, $ttl=300) {
		if (!self::$m) {
			self::$m = new Memcached();
			self::$m->addServer(self::$host, self::$port);
		}
		if ($value !== NULL) {
			return self::$m->set(self::$prefixKey.$key, $value, time() + $ttl);
		} elseif ($key !== NULL) {
			return self::$m->get(self::$prefixKey.$key);
		} else {
			return self::$m;
		}
	}
}