<?php
/**
 * Prenotazioni
 * Libreria funzioni generali
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.2
 */
 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function pren_cvdate($data){
	$rsl = explode ('-',$data);
	return mktime($rsl[3],0,0,$rsl[1], $rsl[2],$rsl[0]);
}

function pren_DateAdd($data,$cosa="g",$quanto=1){
	//data in formato Anno-Mese-Giorno-ora
	$tempo=pren_cvdate($data);
	switch($cosa){
		case "o": $incremento=$quanto*3600;
			break;
		case "g": $incremento=$quanto*86400;
			break;
	}
	$secondi=$tempo+$incremento;
	return date("Y-m-d-H",$secondi);
}

function get_Pre_Parametri(){
	$Parametri=get_option('opt_PrenotazioniParametri');
	return unserialize($Parametri);
}

function get_pre_Oggi(){
	return date('d/m/Y');
}

function create_Pre_Tabelle($Tabella){
global $wpdb;

	switch ($Tabella){
		case $wpdb->table_prenotazioni:
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_prenotazioni." (
			  `IdPrenotazione` int(11) NOT NULL auto_increment,
	  		  `Data` timestamp NOT NULL default CURRENT_TIMESTAMP,
	          `IPAddress` varchar(16) NOT NULL default '000.000.000.000',
	  		  `IdUtente` bigint(20) NOT NULL,
	          `IdSpazio` bigint(20) NOT NULL,
	          `DataPrenotazione` date NOT NULL,
	          `OraInizio` int(2) NOT NULL,
	          `OraFine` int(2) NOT NULL,
	          `Note` text,
			  PRIMARY KEY  (`IdPrenotazione`));";
			break;
	}
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

function giornoSettimana($data,$Tris="n"){
	list($g, $m, $a) = explode('/', $data);
	$days = array ("","Luned&igrave;", "Marted&igrave;", "Mercoled&igrave;", "Gioved&igrave;","Venerd&igrave;","Sabato","Domenica");  
	if($Tris=="n")
		return date("N",mktime(0,0,0,$m, $g, $a));  
	else
		return $days[date("N", mktime(0,0,0,$m, $g, $a))];
}
function DataVisualizza($data){
	$dataDB=substr($data,0,10);
	$rsl = explode ('-',$data);
	$rsl = array_reverse($rsl);
	return implode($rsl,'/');
}
?>