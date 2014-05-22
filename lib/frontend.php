<?php
/**
 * Prenotazioni
 * Codice di gestione della componente Pubblica
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @since 3.8
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
  global $Gest_Prenotazioni,$G_Spaces;

if (!is_user_logged_in()){
	echo $G_Spaces->get_ListaSpaziDiv();
	exit;
}

if (isset($_POST['navigazioneGiorni']) and $_POST['navigazioneGiorni']=="Prenota"){
	$Gest_Prenotazioni->newPrenotazione($_POST['DataPrenotazione'],$_POST['OraInizioPrenotazione'],$_POST['NumOrePren'],$_POST['SpazioP'],$_POST['notePrenotazione']);
}
	$Parametri=get_Pre_Parametri();
	$Stat="
 	<strong>Ultime 5 prenotazione passate</strong>
	<table class=\"TabellaFE\">
 		<thead>
	    	<tr>
	        	<th>Spazio</th>
	        	<th>Data</th>
	        	<th>Ora Inizio</th>
	        	<th>Ora Fine</th>
	        </tr>
	     </thead>
	     <tbody>";
	$Elenco=$Gest_Prenotazioni->get_Prenotazioni("<");
	foreach ($Elenco as $Elemento) {
		$Stat.='
	    	<tr>
	        	<td>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</td>
	        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
	        	<td>'.$Elemento->OraInizio.'</td>
	        	<td>'.$Elemento->OraFine.'</td>
	        </tr>';
	}
	$Stat.= "
			</tbody>
		</table>
	<strong>Prenotazioni di oggi</strong>
	<table class=\"TabellaFE\">
 		<thead>
	    	<tr>
	        	<th>Spazio</th>
	        	<th>Data</th>
	        	<th>Ora Inizio</th>
	        	<th>Ora Fine</th>
	        </tr>
	     </thead>
	     <tbody>";
	$Elenco=$Gest_Prenotazioni->get_Prenotazioni("=");
	foreach ($Elenco as $Elemento) {
		$Stat.= '
	    	<tr>
	        	<td>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</td>
	        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
	        	<td>'.$Elemento->OraInizio.'</td>
	        	<td>'.$Elemento->OraFine.'</td>
	        </tr>';
	}
	$Stat.= "
			</tbody>
		</table>
	<strong>Prossime 5 Prenotazioni</strong>
	<table class=\"TabellaFE\">
 		
 		<thead>
	    	<tr>
	        	<th>Spazio</th>
	        	<th>Data</th>
	        	<th>Ora Inizio</th>
	        	<th>Ora Fine</th>
	        </tr>
	     </thead>
	     <tbody>";
	$Elenco=$Gest_Prenotazioni->get_Prenotazioni(">");
	foreach ($Elenco as $Elemento) {
		$Stat.= '
	    	<tr>
	        	<td>'.$G_Spaces->get_NomeSpazio($Elemento->IdSpazio).'</td>
	        	<td>'.DataVisualizza($Elemento->DataPrenotazione).'</td>
	        	<td>'.$Elemento->OraInizio.'</td>
	        	<td>'.$Elemento->OraFine.'</td>
	        </tr>';
	}
	$Stat.= "
			</tbody>
		</table>";
	$FinPren='	    
	<div id="AreaDatiPrenotazioniSpazi">
    	<form name="Memo_Prenotazioni"  action="'.$_SERVER["REQUEST_URI"].'" method="post">
            <fieldset id="CampiPrenotazioniSpazi">
            	<legend>Dati della prenotazione:</legend>
				<table id="TabellaDatiPrenotazioni">
					<tr>
						<td colspan="2" style="width:100px;"><label>Spazio:</label> '.$G_Spaces->get_ListaSpazi("SpazioP","SpazioP","").'</td>
						<td rowspan="3"><img src="'.$G_Spaces->get_Foto().'" id="imgSpazio"/></td>
					</tr>
					<tr>
						<td colspan="2">
							Data prenotazione: <input type="text" id="DataPrenotazione" name="DataPrenotazione" style="width: 100px;" value="'.get_pre_Oggi().'">
						<td>
					</tr>					
					<tr>
						<td colspan="2" style="height:60px">
							<div id="loading">LOADING!</div>
						<td>
					</tr>					
					<tr>
						<td rowspan="3" style="text-align:center;">
							<label>Ora Inizio:</label>
							<div id="InizioPre">
								'.createTablePrenotazioniSpazio($G_Spaces->get_FirstID()).'
								<input type="hidden" id="OraInizioPrenotazione" value="'.$Parametri['OraInizio'].'" name="OraInizioPrenotazione"/>
							</div>
						</td>
						<td style="text-align: left">
							<label>N&deg; ore:</label> <select id="NumOrePren" name="NumOrePren">
								<option value="0">----</option>		
							</select>
						</td>
					</tr>
					<tr>
						<td style="text-align:center;" colspan="2">
							<label>Motivo Prenotazione:</label><br />
							<textarea rows="10"  id="notePrenotazione" style="width:100%;" name="notePrenotazione"></textarea>
						</td>
					</tr>
					<tr>
						<td style="text-align:center;" colspan="2"> 
							<input type="hidden" id="ColPrenotato" value="'.$Parametri['ColPrenotato'].'" />
							<input type="hidden" id="OranIzio" value="'.$Parametri['OraInizio'].'" />
							<input type="hidden" id="OraFine" value="'.$Parametri['OraFine'].'" />
							<input type="hidden" id="NumMaxOre" value="'.$Parametri['MaxOrePrenotabili'].'" />
							<input type="hidden" id="MinOrePrima" value="'.$Parametri['PrenEntro'].'" />
							<input type="submit" class="navigazioneGiorni" value="Prenota" name="navigazioneGiorni" />
						</td>
					</tr>
				</table>
            </fieldset>
        </form>
	</div>
';
	echo "
		<div id=\"CartellePrenotazioni\">
			<ul>
				<li><a href=\"#CartellaP1\">Nuova</a></li>
				<li><a href=\"#CartellaP2\">Statistiche</a></li>
				<li><a href=\"#CartellaP3\">Catalogo Spazi</a></li>
			</ul>
			<div id=\"CartellaP1\">
			       $FinPren
	        </div>
			<div id=\"CartellaP2\">
	              $Stat
			</div>			
			<div id=\"CartellaP3\">
	              ".$G_Spaces->get_ListaSpaziDiv()."
			</div>			
		</div>
	<div>";



?>