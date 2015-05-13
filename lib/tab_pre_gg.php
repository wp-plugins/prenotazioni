<?php
/**
 * Prenotazioni
 * Generazione tabelle prenotazione
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.3
 */

function createTablePrenotazioni($data="",$visOreDisp="n"){
	global $Gest_Prenotazioni;
	$Parametri=get_Pre_Parametri();
	if($data=="")
		$data_p=date("d/m/Y");
	else
		$data_p=$data;
	$spazi = get_posts(array('post_type'=> 'spazi','posts_per_page'   => -1));
	$numSpazi=1;
	foreach ( $spazi as $spazio ){
		$StatoPrenotazioni[$numSpazi]=$Gest_Prenotazioni->getPreGioSpa($data_p,$spazio->ID);
		$numSpazi++;
	}
	$data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"o",$Parametri["PrenEntro"]);
//	print_r($StatoPrenotazioni[3]);

	$numSpazi=count($spazi);
	$MyID =get_current_user_id();
	$HTML='
	    <div id="tabPrenotazioniSpazi">
	    <input type="hidden" id="NumMaxOre" value="'.$Parametri['MaxOrePrenotabili'].'" />
		<div id="dialog-form" title="Edit User" style="display:none;">  
			<div style="font-size: 12px;font-weight:bold;color: #ff0000;text-align:center;">Dati della Prenotazione</div>
			<div>
				<table>
					<tr>
						<td style="font-size: 12px;font-weight:bold;text-align:right;">Data:</td>
						<td><span id="dataPre"></span></td>
					</tr>
					<tr>
						<td style="font-size: 12px;font-weight:bold;text-align:right;">Ora Inizio:</td>
						<td><span id="InizioPre"></span></td>
					</tr>
					<tr>
						<td style="font-size: 12px;font-weight:bold;text-align:right;">Spazio:</td>
						<td><span id="SpazioPre"></span></td>
					</tr>
				</table>
			</div>
		          <form>
		            <fieldset>
		            	<legend>Dati della prenotazione:</legend>
		                <label>Numero Ore &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp:</label><select id="NumOrePren"></select><br />
		                <label>Numero Settimane:</label><input type="number" min="1" max="20" id="NumeroSettimane" value="1"></select><br />
		                <label>Motivo Prenotazione</label><textarea rows="4" cols="40" id="notePrenotazione"></textarea>
		            </fieldset>
		        </form>
		</div>
		<div id="dialog-confirm" title="Cancellazione Prenotazione" style="display:none;"></div> 
		<div id="dialog-infonew" title="Prenotazioni Memorizzate" style="display:none;"></div> 
		<div id="dialog-help" title="Informazioni di utilizzo" style="display:none;">
			<ul>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Cancellare</span> una prenotazione bisogna posizionarsi sul giorno della prenotazione attraverso il sitema di scorrimento o selezionando la data dal calendario e cliccare sull\'icona <img src="'.Prenotazioni_URL.'img/del.png" /></li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">inserire</span> una nuova prenotazione bisogna cliccare sulla cella della prima ora di prenotazione, al rilascio si aprir&agrave; una finestra nella quale bisogna inserire il numero delle ore da prenotare, il motivo della prenotazione e confermare</li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Visualizzare le Informazioni</span> di una prenotazione bisogna cliccare sull\'icona <img src="'.Prenotazioni_URL.'img/Info.png" /></li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Visualizzare Informazioni di uno spazio</span> basta posizionare il mouse sul nome dello spazio presente ulla prima riga della tabella</li>
			</ul>
		</div>	 
		<table class="settimanale" id="selectable" style="width:95%;height:450px;" >
 		    <thead>
	          	<tr>
	                <th style="background-color:#00FFCC;width:5%">Ora</th>';
	          	$i=0;
	          	$dimeColonna=95 / $numSpazi;
	          	//echo $numSpazi;exit;
	          	$IdSpazi=array();
	          	foreach ( $spazi as $spazio ){
	          		$IdSpazi[$i+1]=$spazio->ID;
	          		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($spazio->ID), 'medium' );
	                if($i % 2 ==1)
	                	$colore="#00FFCC";
	                else	
	                	$colore="#33CCFF";
	                $HTML.= '
	                <th style="background-color:'.$colore.';width:'.$dimeColonna.'%" abbr="img('.$thumb['0'].')'.$spazio->post_excerpt .'"  id="Spazio_'.$spazio->ID.'">'.$spazio->post_title.'</th>';
					$i++;				
				}
				$HTML.= '
	          </tr>
 	    </thead>
	    <tbody>';
	    for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
	    	if($visOreDisp=="n" and ($i<$Parametri['OraInizio'] or $i>$Parametri['OraFine']))
	    		continue;		
   			$HTML.= '          
     		<tr>
                <th style="background-color:#00FFCC">'.$i.'</th>';
  			for ($ns=1;$ns<=$numSpazi;$ns++){
	            $D1=explode("/",$data_p);
	            if($i<10)
	            	$Hore="0".$i;
	            else
	            	$Hore=$i;
	            $D1=$D1[2]."-".$D1[1]."-".$D1[0]."-".$Hore;
//	            echo $D1."<->".$data."<br />";
				if($D1>$data)
	  				$Cancella='
	  					<div style="display:inline;float:left;margin-top:3px;margin-left:5px;cursor: pointer;">
									<img src="'.Prenotazioni_URL.'img/del.png" alt="Icona utente" class="DelPren" id="'.$StatoPrenotazioni[$ns][$i]["ID"].'"/>';
				else
					$Cancella="";
/*				if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID)
					$Info='abbr="'.str_replace('"',"'",$StatoPrenotazioni[$ns][$i]["Motivo"]).'"';
				else */
					$Info="";
		    	switch ($StatoPrenotazioni[$ns][$i]['Impegno']){
					case 2:
						${'bg'.$ns}='background-color:'.$Parametri['ColPrenotato'].';';
						break;
					case 1:
						${'bg'.$ns}='background-color:'.$Parametri['ColRiservato'].';';
						break;
					case 3:
						${'bg'.$ns}='background-color:'.$Parametri['ColNonDisponibile'].';';
						$selctable=' ui-widget-content';
						break;
					default:
						${'bg'.$ns}='background-color:#FFFFFF;';
						break;				
				}
				//'.${'bg'.$ns}.'	
				//echo strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . " +1 day");
				if($StatoPrenotazioni[$ns][$i]['OreCons']==1 and $StatoPrenotazioni[$ns][$i]['Impegno']!=2){
					$appo = explode ('/',$data_p);
					$dataOC=mktime($i,0,0,$appo[1],$appo[0],$appo[2]);				
					if ($dataOC<pren_cvdate($data))
						$classe="style='background-color:".$Parametri['ColNonPrenotabile']."'";
					else	
						if ($StatoPrenotazioni[$ns][$i]['Impegno']==0) 
							$classe="class='adminpreStyle' style='".${'bg'.$ns}."'";
						else
							$classe="style='".${'bg'.$ns}."'";						
					$HTML.= '
					<td id="'.$i.'-0'.$IdSpazi[$ns].'" '.$classe.'>
					</td>';
				}
				elseif($StatoPrenotazioni[$ns][$i]['OreCons']==1 and $StatoPrenotazioni[$ns][$i]['Impegno']==2){
					$HTML.= '
					<td id="'.$i.'-0'.$IdSpazi[$ns].'" class="adminpre" '.$Info.' style="'.${'bg'.$ns}.'">
							<div style="display:inline;float:left;margin-right:5px;cursor: pointer;">
								<img src="'.Prenotazioni_URL.'img/utente.png" alt="Icona utente" abr="Prenotazione effettuata da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].'" class="UserPren"/>
							</div>';
/*							<div style="display:inline;float:left;margin-top:3px;margin-left:5px;">
								<span style="margin-left:3px;">'.$StatoPrenotazioni[$ns][$i]["Motivo"].'</span>
							</div>';*/
							if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID)
								$HTML.= '
							<div style="display:inline;float:left;cursor: pointer;">
								<img src="'.Prenotazioni_URL.'img/Info.png" alt="Icona info" abr="Prenotazione effettuata il: '.$StatoPrenotazioni[$ns][$i]["DataPren"].' <br />da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].' <br />Note: '.str_replace('"',"'",$StatoPrenotazioni[$ns][$i]["Note"]).'" class="InfoPren"/>
							</div>'.$Cancella;
					$HTML.='
						</div>
					</td>';
				}elseif($StatoPrenotazioni[$ns][$i]['OreCons']>1){
					$Altezza=$StatoPrenotazioni[$ns][$i]['OreCons']*35;
					if($StatoPrenotazioni[$ns][$i]['Impegno']==2){
						$HTML.= '
						<td id="'.$i.'-0'.$IdSpazi[$ns].'" class="adminpre" '.$Info.'  rowspan="'.$StatoPrenotazioni[$ns][$i]['OreCons'].'" style="'.${'bg'.$ns}.'">
							<div style="display:inline;float:left;margin-right:5px;cursor: pointer;">
									<img src="'.Prenotazioni_URL.'img/utente.png" alt="Icona utente" style="display:inline;" abr="Prenotazione effettuata da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].'" class="UserPren"/>
								</div>';
/*								<div style="display:inline;float:left;">
									<span style="margin-left:3px;">'.$StatoPrenotazioni[$ns][$i]["Motivo"].'</span>
								</div>';*/
							if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID)
								$HTML.= '
								<div style="display:inline;float:left;cursor: pointer;">
									<img src="'.Prenotazioni_URL.'img/Info.png" alt="Icona info" abr="Prenotazione effettuata il: '.$StatoPrenotazioni[$ns][$i]["DataPren"].' <br />da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].' <br />Note: '.str_replace('"',"'",$StatoPrenotazioni[$ns][$i]["Note"]).'" class="InfoPren"/>
									
								</div>'.$Cancella.'
								</div>';
							$HTML.='
						</td>';	
					}else
						$HTML.= '
						<td id="'.$i.'-0'.$IdSpazi[$ns].'" style="'.${'bg'.$ns}.'" '.$Info.' rowspan="'.$StatoPrenotazioni[$ns][$i]['OreCons'].'" >
						</td>';					
				}					
			}
			$HTML.= '
			</tr>';
		}
	 $HTML.= '
	    </tbody>
	   </table>
	  </div> 
	  ';
	return $HTML;
}

function createTablePrenotazioniSpazio($IDSpazio=0,$data=""){
	global $Gest_Prenotazioni;
	$Parametri=get_Pre_Parametri();
	if($data=="")
		$data_p=date("d/m/Y");
	else
		$data_p=$data;
	$data=pren_DateAdd(date("Y-m-d-H",current_time( 'timestamp', 0 ) ),"o",$Parametri["PrenEntro"]);
	$StatoPrenotazioni=$Gest_Prenotazioni->getPreGioSpa($data_p,$IDSpazio);
	$HTML= '
 		<input type="hidden" id="OldSel" value="" />
		<table class="settimanale" id="selectable" style="height:600px;" >
 		    <thead>
	          	<tr>
	                <th style="background-color:#00FFCC;width:5%">Ora</th>';
	                	$colore="#33CCFF";
	                $HTML.= '
	                <th style="background-color:#33CCFF">Occupazione</th>
 	           </tr>
 	    </thead>
	    <tbody>';
	    for($i=$Parametri['OraInizio'];$i<=$Parametri['OraFine'];$i++){
/*	    	if($i<$Parametri['OraInizio'] or $i>$Parametri['OraFine'])
	    		continue;		*/
			switch ($StatoPrenotazioni[$i]['Impegno']){
				case 2:
					$colore=$Parametri['ColPrenotato'];
					break;
				case 1:
					$colore=$Parametri['ColRiservato'];
					break;
				case 3:
					$colore=$Parametri['ColNonDisponibile'];
					break;
				case 0:
					$colore="#FFFFFF";
			} 
			$HTML.= '          
     		<tr>
                <th style="background-color:#00FFCC">'.$i.'</th>';
                if ($StatoPrenotazioni[$i]['OreCons']>0){
					$appo = explode ('/',$data_p);
					$dataOC=mktime($i,0,0,$appo[1],$appo[0],$appo[2]);				
					if ($dataOC<pren_cvdate($data))
						$classe="style='background-color:".$Parametri['ColNonPrenotabile']."'";
					else	
						$classe="class='adminpreStyle'";
					if($StatoPrenotazioni[$i]['Impegno']==0)
						$HTML.= '
						<td id="'.$i.'" '.$classe.' style="background-color:'.$colore.'">
						</td>';
					elseif($StatoPrenotazioni[$i]['OreCons']==1){
						$HTML.= '
						<td id="'.$i.'" class="adminpre" style="background-color:'.$colore.'">
						</td>';
					}else{
							$HTML.= '
							<td id="'.$i.'" class="adminpre" rowspan="'.$StatoPrenotazioni[$i]['OreCons'].'" style="background-color:'.$colore.'">
								
							</td>';	
					}
				}	
			$HTML.= '
			</tr>';
		}
	 $HTML.= '
	    </tbody>
	   </table>
	  ';
	return $HTML;
}
?>