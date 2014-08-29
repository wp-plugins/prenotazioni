<?php
/**
 * Prenotazioni
 * Classe Prenotazioni
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.0
 */

class Prenotazioni{
	private $Riservato=array("Giorno" => 1,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 2,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 3,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 4,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 5,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 6,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
							 "Giorno" => 7,"Occupazione"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)
							 );
	private $PrenotazioniGiorno=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);	
	
	function Tabella_Giornaliera_Prenotazioni(){

		$Parametri=get_Pre_Parametri();
		echo '		
		<div style="margin-top: 40px;margin-right: 50px;float: right;width:120px;color: #000;padding:5px;">
			<span style="background-color:'.$Parametri['ColPrenotato'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Prenotato<br />
	 		<span style="background-color:'.$Parametri['ColRiservato'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Riservato<br />
	 		<span style="background-color:'.$Parametri['ColNonDisponibile'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Non disponibile<br />
	 		<span style="background-color:'.$Parametri['ColNonPrenotabile'].';">&nbsp;&nbsp;&nbsp;&nbsp;</span> Prenotazione chiusa<br />
	 	</div>
		<div class="wrap" style="width:99%" >
	  	<img src="'.Prenotazioni_URL.'img/spazi.png" alt="Icona configurazione" style="display:inline;float:left;margin-top:10px;"/>
	  	<h2 style="margin-left:40px;">Gestione Prenotazioni</h2>
		</div>		<div>
	 		<table>
	 			<tr>
	 				<td><input type="button" class="navigazioneGiorni" value="<<" /></td>
	 				<td style="width:70px;text-align: center;"><span id="giornodataCal">'.giornoSettimana(date("d/m/Y"),"l").' </span></td>
	 				<td style="width:70px;text-align: center;"><span id="dataCal">'.date("d/m/Y").'</span>
	 					<input type="hidden" id="dataCalVal" value="" />
	 				</td>
	 				<td><input type="button" class="navigazioneGiorni" value=">>" /></td>
	 				<td><input id="preSelDay" type="image" class="calendarioGiorni" src="'.Prenotazioni_URL.'img/icocalendario.png" /></td>
	 				<td><input id="helpPren" type="image" class="HelpPrenotazioni" src="'.Prenotazioni_URL.'img/help.png" /></td>
	 				<td><p style="font-weight: bold;text-shadow: 0px 0px 8px #000;font-size:24px;">Occupazione giornaliera degli Spazi</p></td>
	 				<td><div id="loading">LOADING!</div></td>
	 			</tr>
	 		</table>
		</div>

		 ';
		 echo createTablePrenotazioni();
	}
	
	private function GetNumOrePren($Riservato,$giorno,$i,$OraInizio,$OraFine){
//echo $giorno."-".$i."<br />";
		if ($i>=$OraInizio and ($Riservato[$giorno][$i-1]==$Riservato[$giorno][$i]) and ($Riservato[$giorno][$i]!=0))
			return 0;
		if ($Riservato[$giorno][$i]==0)
			return 1;
		$NumCons=1;
		while ($Riservato[$giorno][$i]==$Riservato[$giorno][$i+1] and $i<$OraFine){
			$NumCons++;
			$i++;
		}
//		echo $NumCons." - ";
		return $NumCons;
	}
	
	function get_Prenotazioni($SegnoFiltro="=",$Numero=5){
		global $wpdb,$table_prefix;
		if (current_user_can( 'manage_options' ))
			$FiltroUtente="";
		else{
			$MyID =get_current_user_id();
			$FiltroUtente=" And IdUtente=\"$MyID\" ";		
		}	
		$Oggi=date('Y-m-d');
		$Sql="SELECT IdSpazio,IdUtente,OraInizio,OraFine,Note,DataPrenotazione FROM $wpdb->table_prenotazioni WHERE DataPrenotazione$SegnoFiltro\"$Oggi\" $FiltroUtente Order By Data, OraInizio LIMIT 0,$Numero";
		return $wpdb->get_results($Sql);
	}
	
	function getPreGioSpa($data,$IdSpazio){
		global $wpdb,$table_prefix;
		$giornoS=giornoSettimana($data);
		$Riservato=get_post_meta( $IdSpazio, "_riservato",true);
		$Riservato=unserialize($Riservato);
		$Parametri=get_Pre_Parametri();
		if($Parametri['Giorni'][$giornoS-1]==0){
			for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
				if ($i==$Parametri['OraInizio'])
					$NumO=$Parametri['OraFine']-$Parametri['OraInizio']+1;
				else
					$NumO=0;
				$PrenotazioniGiorno[$i]=array("Impegno"=>1,
										  "Motivo"=>"",
										  "Note"=>"",
										  "OreCons"=>$NumO);
			}
			return $PrenotazioniGiorno;
		}
		for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
			$PrenotazioniGiorno[$i]=array("Impegno"=>$Riservato[$giornoS][$i],
										  "Motivo"=>"",
										  "Note"=>"",
										  "OreCons"=>$this->GetNumOrePren($Riservato,$giornoS,$i,$Parametri['OraInizio'],$Parametri['OraFine']));
		}
/*		echo $IdSpazio." <br />";
		print_r($Riservato[6]);
		echo " <br />";
		print_r($PrenotazioniGiorno);
		echo " <br />";*/
		$pezziData=explode("/",$data);
		$newData=$pezziData[2]."-".$pezziData[1]."-".$pezziData[0];
		$Sql="SELECT IdPrenotazione,IdSpazio,IdUtente,OraInizio,OraFine,Note,Data FROM $wpdb->table_prenotazioni WHERE DataPrenotazione=\"$newData\" and IdSpazio=$IdSpazio Order By OraInizio";
		$Prenotazioni=$wpdb->get_results($Sql);
		foreach($Prenotazioni as $Prenotazione){
//			print_r($Prenotazione);
			$user_info = get_userdata($Prenotazione->IdUtente);
			$numOre=1;
			for($ora=$Prenotazione->OraInizio;$ora<$Prenotazione->OraFine;$ora++){
				if($Prenotazione->OraFine-$Prenotazione->OraInizio>1 and $numOre==1)
					$numOre=$Prenotazione->OraFine-$Prenotazione->OraInizio;
				else
					$numOre=0;
				if($Prenotazione->OraFine-$Prenotazione->OraInizio==1)
					$numOre=1;
				$PrenotazioniGiorno[$ora]=array("ID"=>$Prenotazione->IdPrenotazione,
												"Impegno"=>"2",
											    "Motivo"=>$user_info->display_name,
											    "IDUser"=>$user_info->ID,
										  		"Note"=>$Prenotazione->Note,
										  		"OreCons"=>$numOre,
										  		"DataPren"=>date("d/m/Y H:i"));
				
			}
		}
//		print_r($PrenotazioniGiorno);
		return $PrenotazioniGiorno;
	}
	function delPrenotazione($IdPrenotazione){
		global $wpdb;
	 	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_prenotazioni WHERE IdPrenotazione=%d",$IdPrenotazione));
	 	return $wpdb->$num_rows;
	}
	function newPrenotazione($data,$orai,$ore,$IdSpazio,$note){
		global $wpdb;
    	$dataS=explode("/",$data);
    	$dataA=$dataS[2]."-".$dataS[1]."-".$dataS[0];
	 	if ( false === $wpdb->insert($wpdb->table_prenotazioni,array('IPAddress' => $_SERVER['REMOTE_ADDR'],
	                                                                 'IdUtente' => get_current_user_id(),
	                                                              	 'IdSpazio' => $IdSpazio,
	                                                         'DataPrenotazione' => $dataA,
															 		'OraInizio' => $orai,
																  	  'OraFine' => $orai+$ore,
																         'Note' => $note)))
	 		return false;
	 	else
	 		return true;
	}
}
?>