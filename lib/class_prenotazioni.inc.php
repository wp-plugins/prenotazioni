<?php
/**
 * Prenotazioni
 * Classe Prenotazioni
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.2
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
	
	function Tabella_Mie_Prenotazioni(){
		global $Gest_Prenotazioni,$G_Spaces;
		$Parametri=get_Pre_Parametri();
		$OraC=$data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"o",$Parametri["PrenEntro"]);
		$StatPre="
		<table class=\"TabellaFE\">
	 		<thead>
		    	<tr>
		        	<th style='width:60%;'>Spazio</th>
		        	<th style='width:20%;'>Data</th>
		        	<th style='width:10%;'>Ora Inizio</th>
		        	<th style='width:10%;'>Ora Fine</th>
		        </tr>
		     </thead>
		     <tbody>";
		$Elenco=$Gest_Prenotazioni->get_Prenotazioni("<",-1,"Desc");
		if (count($Elenco)>0){
			foreach ($Elenco as $Elemento) {
				$StatPre.='
			    	<tr>
			        	<td><img src="'.$G_Spaces->get_Foto_By_ID($Elemento->IdSpazio).'" style="width: 75px;height= 75px;margin-right:10px;" class="alignleft"/><h4>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</h4></td>
			        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
			        	<td>'.$Elemento->OraInizio.'</td>
			        	<td>'.$Elemento->OraFine.'</td>
			        </tr>';
			}
			$StatPre.= "
					</tbody>
				</table>";
		}else{
			$StatPre='<p style="margin-left:50px;margin-top:50px;font-style: italic;font-weight: bold;color: #F00000;">Non ci sono prenotazioni presenti in questa cartella</p>';			
		}
			$StatCor="
			<table class=\"TabellaFE\">
		 		<thead>
			    	<tr>
			        	<th style='width:50%;'>Spazio</th>
			        	<th style='width:20%;'>Data</th>
			        	<th style='width:10%;'>Ora Inizio</th>
			        	<th style='width:10%;'>Ora Fine</th>
			        	<th style='width:10%;'>Operazioni</th>
			        </tr>
			     </thead>
			     <tbody>";
		$Elenco=$Gest_Prenotazioni->get_Prenotazioni("=",-1);
		if (count($Elenco)>0){
			foreach ($Elenco as $Elemento) {
				$D1=$Elemento->DataPrenotazione."-".$Elemento->OraInizio;
				$StatCor.= '
			    	<tr>
			        	<td>'.$OraC."-".$D1.'<img src="'.$G_Spaces->get_Foto_By_ID($Elemento->IdSpazio).'" style="width: 75px;height= 75px;margin-right:10px;" class="alignleft"/><h4>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</h4></td>
			        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
			        	<td>'.$Elemento->OraInizio.'</td>
			        	<td>'.$Elemento->OraFine.'</td>';
		        if($OraC<$D1)
		        	$StatCor.= '
		        	<td><img src="'.Prenotazioni_URL.'img/del.png" alt="Icona cancella prenotazione" class="CancMiaPren" id="'.$Elemento->IdPrenotazione.'"/></td>';
		        else
		        	$StatCor.= '<td></td>';
			    $StatCor.= '
			        </tr>';
			}
			$StatCor.= "
				</tbody>
			</table>";
		}else{
			$StatCor='<p style="margin-left:50px;margin-top:50px;font-style: italic;font-weight: bold;color: #F00000;">Non ci sono prenotazioni presenti in questa cartella</p>';			
		}
		$StatFut="
		<table class=\"TabellaFE\">	
	 		<thead>
		    	<tr>
		        	<th style='width:50%;'>Spazio</th>
		        	<th style='width:20%;'>Data</th>
		        	<th style='width:10%;'>Ora Inizio</th>
		        	<th style='width:10%;'>Ora Fine</th>
		        	<th style='width:10%;'>Operazioni</th>
		        </tr>
		     </thead>
		     <tbody>";
		$Elenco=$Gest_Prenotazioni->get_Prenotazioni(">",-1);
		if (count($Elenco)>0){
			foreach ($Elenco as $Elemento) {
				$D1=$Elemento->DataPrenotazione."-".$Elemento->OraInizio;
				$StatFut.= '
			    	<tr>
			        	<td><img src="'.$G_Spaces->get_Foto_By_ID($Elemento->IdSpazio).'" style="width: 75px;height= 75px;margin-right:10px;" class="alignleft"/><h4>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</h4></td>
			        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
			        	<td>'.$Elemento->OraInizio.'</td>
			        	<td>'.$Elemento->OraFine.'</td>';
		        if($OraC<$D1)
		        	$StatFut.= '
		        	<td><img src="'.Prenotazioni_URL.'img/del.png" alt="Icona cancella prenotazione" class="CancMiaPren" id="'.$Elemento->IdPrenotazione.'"/></td>';
		        else
		        	$StatFut.= '<td></td>';
			    $StatFut.= '
			        </tr>';
			}
			$StatFut.= '
				</tbody>
			</table>';
		}else{
			$StatFut='<p style="margin-left:50px;margin-top:50px;font-style: italic;font-weight: bold;color: #F00000;">Non ci sono prenotazioni presenti in questa cartella</p>';			
		}
		echo "
		<div id='dialog-confirm' title='Cancellazione Prenotazione' style='display:none;'></div> 
		<div id='loading'>LOADING!</div>
			<h2>Le mie prenotazioni</h2>
			<div id=\"CartellePrenotazioni\">
				<ul>
					<li><a href=\"#CartellaP1\">Passate</a></li>
					<li><a href=\"#CartellaP2\">Di Oggi</a></li>
					<li><a href=\"#CartellaP3\">Prossime</a></li>
				</ul>
				<div id=\"CartellaP1\">
				       $StatPre
		        </div>
				<div id=\"CartellaP2\">
		              $StatCor
				</div>			
				<div id=\"CartellaP3\">
		              $StatFut
				</div>			
			</div>";
	}	
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
	
	function get_Prenotazioni($SegnoFiltro="=",$Numero=5,$OrderData="Asc",$OrderOra="Asc"){
		global $wpdb,$table_prefix;
		if ($Numero==-1)
			$Limite="";
		else
			$Limite=" LIMIT 0,".$Numero;
		if (current_user_can( 'manage_options' ))
			$FiltroUtente="";
		else{
			$MyID =get_current_user_id();
			$FiltroUtente=" And IdUtente=\"$MyID\" ";		
		}	
		$Oggi=date('Y-m-d');
		$Sql="SELECT IdSpazio,IdUtente,OraInizio,OraFine,Note,DataPrenotazione,IdPrenotazione FROM $wpdb->table_prenotazioni WHERE DataPrenotazione$SegnoFiltro\"$Oggi\" $FiltroUtente Order By DataPrenotazione $OrderData, OraInizio $OrderOra".$Limite;
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
	function IsPossibilePrenotare($IDSpazio,$Data,$DaOre,$nOre){
		global $wpdb;
		$Sql="SELECT OraInizio,OraFine FROM $wpdb->table_prenotazioni WHERE DataPrenotazione='$Data' And IdSpazio=$IDSpazio Order By OraInizio";
/*		echo $Sql."<br />";
		echo $IDSpazio."  ".$Data." ".$DaOre."  ".$nOre."<br />";*/
		$re=$wpdb->get_results($Sql);
		$orep=array();
		foreach($re as $prenotazione){
			for ($i=$prenotazione->OraInizio;$i<$prenotazione->OraFine;$i++)
				$orep[]=$i;
		}
		for($i=$DaOre;$i<$DaOre+$nOre;$i++)
			if(in_array($i,$orep))
				return false;
//		print_r($re);
//		echo "<br />";
//		print_r($orep);
		return true;
	}
	
	function newPrenotazione($data,$orai,$ore,$IdSpazio,$Nset,$note){
		global $wpdb;
    	$dataS=explode("/",$data);
    	$Data=$dataS[2]."-".$dataS[1]."-".$dataS[0];
    	$PrenCre="";
		for($i=0;$i<$Nset;$i++){
			if($this->IsPossibilePrenotare($IdSpazio,$Data,$orai,$ore))
			 	if ( false === $wpdb->insert($wpdb->table_prenotazioni,array('IPAddress' => $_SERVER['REMOTE_ADDR'],
			                                                                 'IdUtente' => get_current_user_id(),
			                                                              	 'IdSpazio' => $IdSpazio,
			                                                         'DataPrenotazione' => $Data,
																	 		'OraInizio' => $orai,
																		  	  'OraFine' => $orai+$ore,
																		         'Note' => $note)))
		 			$PrenCre.="Prenotazione del ".$Data." non è stata creata<br />";
		 		else
		 			$PrenCre.="Prenotazione del ".$Data." è stata creata<br />";
		 	else
		 		$PrenCre.="Prenotazione del ".$Data." non è stata creata perch&egrave; gi&agrave; occupata<br />";
			$Data=explode("-",$Data);
			$Data = date('Y-m-d', strtotime("+1 week",mktime(0, 0, 0, $Data[1], $Data[2], $Data[0])));
	 	}
	 	return $PrenCre;
	}
}
?>