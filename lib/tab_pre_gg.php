<?php

function createTablePrenotazioni($data="",$visOreDisp="n"){
	global $Gest_Prenotazioni;
	$Parametri=get_Pre_Parametri();
	if($data=="")
		$data_p=date("d/m/Y");
	else
		$data_p=$data;
	$spazi = get_posts(array('post_type'=> 'spazi'));
	$numSpazi=1;
	foreach ( $spazi as $spazio ){
		$StatoPrenotazioni[$numSpazi]=$Gest_Prenotazioni->getPreGioSpa($data_p,$spazio->ID);
		$numSpazi++;
	}
//	print_r($StatoPrenotazioni[3]);
	$numSpazi=count($spazi);
	$MyID =get_current_user_id();
	$HTML= '
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
		                <label>Numero Ore </label><select id="NumOrePren"></select><br />
		                <label>Motivo Prenotazione</label><textarea rows="4" cols="40" id="notePrenotazione"></textarea>
		                
		            </fieldset>
		        </form>
		</div>
		<div id="dialog-confirm" title="Cancellazione Prenotazione" style="display:none;"></div> 
		<div id="dialog-help" title="Informazioni di utilizzo" style="display:none;">
			<ul>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Cancellare</span> una prenotazione bisogna posizionarsi sul giorno della prenotazione attraverso il sitema di scorrimento o selezionando la data dal calendario e cliccare sull\'icona <img src="'.Prenotazioni_URL.'img/del.png" /></li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">inserire</span> una nuova prenotazione bisogna cliccare sulla cella della prima ora di prenotazione, al rilascio si aprir&agrave; una finestra nella quale bisogna inserire il numero delle ore da prenotare, il motivo della prenotazione e confermare</li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Visualizzare le Informazioni</span> di una prenotazione bisogna cliccare sull\'icona <img src="'.Prenotazioni_URL.'img/info.png" /></li>
				<li>Per <span style="font-weight:bold;color: #ff0000;">Visualizzare Informazioni di uno spazio</span> basta posizionare il mouse sul nome dello spazio presente ulla prima riga della tabella</li>
			</ul>
		</div>	 
		<table class="settimanale" id="selectable" style="width:95%;height:450px;" >
 		    <thead>
	          	<tr>
	                <th style="background-color:#00FFCC;width:5%">Ora</th>';
	          	$i=0;
	          	$dimeColonna=95 / $numSpazi;
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
				if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID)
					$Info='abbr="'.$StatoPrenotazioni[$ns][$i]["Note"].'"';
				else 
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
				if($StatoPrenotazioni[$ns][$i]['OreCons']==1 and $StatoPrenotazioni[$ns][$i]['Impegno']==0)
					$HTML.= '
					<td id="'.$i.'-0'.$IdSpazi[$ns].'" class="adminpreStyle">
					</td>';
				elseif($StatoPrenotazioni[$ns][$i]['OreCons']==1 and $StatoPrenotazioni[$ns][$i]['Impegno']==2){
					$HTML.= '
					<td id="'.$i.'-0'.$IdSpazi[$ns].'" class="adminpre" '.$Info.'>
						<div style="margin:1px;border: thin dotted blue;height:28px;'.${'bg'.$ns}.'">
							<div style="display:inline;float:left;">
								<img src="'.Prenotazioni_URL.'img/utente.png" alt="Icona utente"/>
							</div>
							<div style="display:inline;float:left;margin-top:3px;margin-left:5px;">
								<span style="margin-left:3px;">'.$StatoPrenotazioni[$ns][$i]["Motivo"].'</span>
							</div>';
							if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID)
								$HTML.= '
							<div style="display:inline;float:left">
								<img src="'.Prenotazioni_URL.'img/info.png" alt="Icona info" title="Prenotazione effettuata il: '.$StatoPrenotazioni[$ns][$i]["DataPren"].' <br />da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].' <br />Note: '.$StatoPrenotazioni[$ns][$i]["Note"].'" class="InfoPren"/>		
							<div style="display:inline;float:left;margin-top:3px;margin-left:5px;">
								<img src="'.Prenotazioni_URL.'img/del.png" alt="Icona utente" class="DelPren" id="'.$StatoPrenotazioni[$ns][$i]["ID"].'"/>
							</div>';
					$HTML.='
						</div>
					</td>';
				}elseif($StatoPrenotazioni[$ns][$i]['OreCons']>1){
					$Altezza=$StatoPrenotazioni[$ns][$i]['OreCons']*35;
					if($StatoPrenotazioni[$ns][$i]['Impegno']==2){
						$HTML.= '
						<td id="'.$i.'-0'.$IdSpazi[$ns].'" class="adminpre" '.$Info.'  rowspan="'.$StatoPrenotazioni[$ns][$i]['OreCons'].'" style="'.${'bg'.$ns}.'">
							<div style="display:inline;float:left;">
									<img src="'.Prenotazioni_URL.'img/utente.png" alt="Icona utente" style="display:inline;"/>
								</div>
								<div style="display:inline;float:left;">	
									<span style="margin-left:3px;">'.$StatoPrenotazioni[$ns][$i]["Motivo"].'</span>
								</div>';
							if (current_user_can( 'manage_options' ) or $StatoPrenotazioni[$ns][$i]["IDUser"]==$MyID)
								$HTML.= '
								<div style="display:inline;float:left">
									<img src="'.Prenotazioni_URL.'img/info.png" alt="Icona info" title="Prenotazione effettuata il: '.$StatoPrenotazioni[$ns][$i]["DataPren"].' <br />da: '.$StatoPrenotazioni[$ns][$i]["Motivo"].' <br />Note: '.$StatoPrenotazioni[$ns][$i]["Note"].'" class="InfoPren"/>
								</div>
								<div style="display:inline;float:left;margin-top:3px;">
									<img src="'.Prenotazioni_URL.'img/del.png" alt="Icona utente" class="DelPren" id="'.$StatoPrenotazioni[$ns][$i]["ID"].'"/>
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
	$StatoPrenotazioni=$Gest_Prenotazioni->getPreGioSpa($data_p,$IDSpazio);
	$HTML= '
		<table class="settimanale" id="selectable" style="height:450px;" >
 		    <input type="hidden" id="OldSel" value="" />
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
	    	if($i<$Parametri['OraInizio'] or $i>$Parametri['OraFine'])
	    		continue;		
   			$HTML.= '          
     		<tr>
                <th style="background-color:#00FFCC">'.$i.'</th>';
                if ($StatoPrenotazioni[$i]['OreCons']>0){
					if($StatoPrenotazioni[$i]['Impegno']==0)
						$HTML.= '
						<td id="'.$i.'" class="adminpreStyle">
						</td>';
					elseif($StatoPrenotazioni[$i]['OreCons']==1){
						$HTML.= '
						<td id="'.$i.'" class="adminpre" style="background-color:'.$Parametri['ColNonDisponibile'].'">
						</td>';
					}else{
							$HTML.= '
							<td id="'.$i.'" class="adminpre" rowspan="'.$StatoPrenotazioni[$i]['OreCons'].'" style="background-color:'.$Parametri['ColNonDisponibile'].'">
								
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