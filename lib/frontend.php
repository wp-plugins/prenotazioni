<?php
/**
 * Prenotazioni
 * Codice di gestione della componente Pubblica
 * @package Prenotazioni
 * @author Scimone Ignazio
 * @copyright 2014-2099
 * @version 1.2
 **/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
  global $Gest_Prenotazioni,$G_Spaces;

if (!is_user_logged_in()){
	echo $G_Spaces->get_ListaSpaziDiv();
}else{
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
		$Spazio=$G_Spaces->get_ListaSpazi("SpazioP","SpazioP","");
		$FinPren='	    
		<div id="AreaDatiPrenotazioniSpazi">
	    	<form name="Memo_Prenotazioni"  action="'.$_SERVER["REQUEST_URI"].'" method="post">
	            <fieldset id="CampiPrenotazioniSpazi">
	            	<div style="float:left;margin-left:5px;margin-top:10px;">
	            		<img src="'.$G_Spaces->get_Foto().'" id="imgSpazio" style="border:none;"/>
	            	</div>
	            	<p style="text-align:center;font-weight: bold;font-size: large;">Dati della prenotazione:</p>
	            	<div style="float:left;margin-left:5px;">
						<div style="float:left;">
							<p>
								<label>Spazio:</label> '.$Spazio.'
							</p>	
							<p>
								<label>Data prenotazione:</label>
								<input type="text" id="DataPrenotazione" name="DataPrenotazione" style="width: 100px;" value="'.get_pre_Oggi().'">
							</p>
						</div>
						<div id="loading" style="float:left;margin-left:15px;margin-top:15px;">LOADING!</div>
					</div>
					<div style="clear:both;"></div>
					<div style="float:left;">
						<label>Ora Inizio:</label>
						<div id="InizioPre">
							'.createTablePrenotazioniSpazio($G_Spaces->get_FirstID()).'
						</div>
					</div>
					<div style="float:left;margin-left:20px;">
						<p>
							<label>N&deg; ore:</label> 
							<select id="NumOrePren" name="NumOrePren">
								<option value="0">----</option>		
							</select>
						</p>
						<p>
							<label>Motivo Prenotazione:</label><br />
							<textarea rows="8"  cols="32" id="notePrenotazione" style="width:100%;" name="notePrenotazione"></textarea>
						</p>
						<p>					
							<input type="hidden" id="OraInizioPrenotazione" value="" name="OraInizioPrenotazione"/>
							<input type="hidden" id="UrlAjax" value="'.home_url().'/wp-admin/admin-ajax.php" name="UrlAjax"/>
							<input type="hidden" id="ColPrenotato" value="'.$Parametri['ColPrenotato'].'" />
							<input type="hidden" id="OranIzio" value="'.$Parametri['OraInizio'].'" />
							<input type="hidden" id="OraFine" value="'.$Parametri['OraFine'].'" />
							<input type="hidden" id="NumMaxOre" value="'.$Parametri['MaxOrePrenotabili'].'" />
							<input type="hidden" id="MinOrePrima" value="'.$Parametri['PrenEntro'].'" />
							<input type="submit" class="navigazioneGiorni" value="Prenota" name="navigazioneGiorni" />
						</p>
					</div>				
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
			</div>";
}
?>