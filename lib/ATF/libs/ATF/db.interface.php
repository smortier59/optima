<?php
/**
* L'accès aux données persistantes dans les bases de données utilise cette classe
* Un trousseau d'accès à plusieurs base de données est possible
*
* @date 2008-10-30
* @package ATF
* @version 5
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 

/**
* Interface obligatoire pour la base de données
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/ 
interface db {
	public function setCharset($charset="utf8");
	public function report();
	public function query($query,$resultmode=MYSQLI_STORE_RESULT);
	public function all_tables();
	public function enum2array(&$class,$field);
	public function fetch_first_cell($query);
	public function ffc($query);
	public function fetch_array_once($query);
	public function farro($query);
	public function fetch_assoc_once($query);
	public function fasso($query);
	public function res2array(&$result,$id=NULL,$infos=NULL,$assoc=true);
	public function sql2array($query,$id=NULL,$infos=NULL,$assoc=true);
	public function multi2array(&$query,$id=NULL,$infos=NULL,$assoc=true);
	public function truncate($table);
	public function optimize($table=NULL);
	public function table2htmltable(&$colonnes,$filtre=false,$filtre_=NULL);
}
?>