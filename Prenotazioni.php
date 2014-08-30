<?php
/*
Plugin Name:Prenotazioni
Plugin URI: http://plugin.sisviluppo.info
Description: Plugin utilizzato per delle risorse Aule, Sale conferenza, Laboratori, etc...
Version:1.1.1
Author: Scimone Ignazio
Author URI: http://plugin.sisviluppo.info
License: GPL2
    Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : info@sisviluppo.info)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
include_once(dirname (__FILE__) .'/functions.inc.php');				/* Various functions used throughout */
//include_once(dirname (__FILE__) .'/AlboPretorio.widget.inc');
define("Prenotazioni_URL",plugin_dir_url(dirname (__FILE__).'/Prenotazioni.php'));
define("Prenotazioni_DIR",dirname (__FILE__));
include_once ( dirname (__FILE__) . '/lib/class_spazi.inc.php' );
include_once ( dirname (__FILE__) . '/lib/class_prenotazioni.inc.php' );
include_once ( dirname (__FILE__) . "/lib/tab_pre_gg.php");
include_once (dirname (__FILE__) . "/Prenotazioni.widget.php");
if (!class_exists('Plugin_Prenotazioni')) {
 class Plugin_Prenotazioni {
	
	var $version;
	var $minium_WP   = '3.8';
	var $options     = '';
	
	function Plugin_Prenotazioni() {
		global $G_Spaces;
		if ( ! function_exists( 'get_plugins' ) )
	 		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	    $plugins = get_plugins( "/".plugin_basename( dirname( __FILE__ ) ) );
    	$plugin_nome = basename( ( __FILE__ ) );
	    $this->version=$plugins[$plugin_nome]['Version'];
		// Inizializzazioni
		$this->define_tables();
		$this->plugin_name = plugin_basename(dirname( __FILE__ ) );
		// Hook per attivazione/disattivazione plugin
		$G_Spaces=new Spazi();
		register_activation_hook( __FILE__, array(&$this, 'activate'));
		register_deactivation_hook(__FILE__, array(&$this, 'deactivate') );	
//		register_uninstall_hook( __FILE__, array(&$this, 'uninstall') );
		// Hook di inizializzazione che registra il punto di avvio del plugin
		add_action( 'admin_enqueue_scripts',  array(&$this,'enqueue_scripts') );
		add_action('init', array(&$this, 'update_Prenotazioni_settings'));
		add_action( 'wp_enqueue_scripts', array(&$this,'head_Front_End'));
		add_action( 'admin_menu', array (&$this, 'add_menu') ); 
		add_action( 'wp_ajax_prenSpazi', array(&$this,'getPrenotazioniSpazi'));
		add_action( 'wp_ajax_FEprenSpazi', array(&$this,'getPrenotazioniSpazi'));
		//add_action( 'wp_ajax_nopriv_FEprenSpazi', array(&$this,'getPrenotazioniSpazi'));
		add_action( 'wp_ajax_delPren', array(&$this,'deletePrenotazioniSpazi'));
		add_action( 'wp_ajax_newPren', array(&$this,'nuovaPrenotazioneSpazi'));
		add_shortcode('Prenotazioni', array(&$this, 'FrontEndPrenotazioni'));
		if (!is_admin()) 
			add_action('wp_print_styles', array(&$this,'Prenotazioni_styles'));
	}
	function FrontEndPrenotazioni(){
		include_once ( dirname (__FILE__) . '/lib/frontend.php' );	
	}
	
	function getPrenotazioniSpazi(){
		if($_POST['sorg']=="FE")
			echo createTablePrenotazioniSpazio($_POST['spazio'],$_POST['data']);
		else
			echo createTablePrenotazioni($_POST['data']);
		die();
	}
	function deletePrenotazioniSpazi(){
		global $Gest_Prenotazioni;
		$ris=$Gest_Prenotazioni->delPrenotazione($_POST['id']);
		echo "Ho cancellato '.$ris.' appuntamenti";
		die();
	}
	function nuovaPrenotazioneSpazi(){
		global $Gest_Prenotazioni;
		$ris=$Gest_Prenotazioni->newPrenotazione($_POST['data'],$_POST['OraI'],$_POST['Ore'],$_POST['IdS'],$_POST['Note']);
		echo "Ho creato '.$ris.' appuntamento";
		die();
	}
	function enqueue_scripts( $hook_suffix ) {
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-ui-datepicker', '', array('jquery'));
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		//wp_enqueue_style('jquery-ui-dialog');
		//wp_enqueue_script('jquery-ui-selectable');
		wp_enqueue_script('jquery-ui-slider', false, array('jquery'), false, false);
		wp_enqueue_style( 'jquery.ui.theme', plugins_url( 'css/jquery-ui-custom.css', __FILE__ ) );
		wp_register_style($this->plugin_name,  plugins_url( 'css/style.css', __FILE__ ));
        wp_enqueue_style( $this->plugin_name);
		wp_enqueue_script( 'Prenotazioni-admin-fields', plugins_url('js/Prenotazioni.js', __FILE__ ));
	}
	function Prenotazioni_styles() {
        $myStyleUrl = plugins_url('css/style.css', __FILE__); 
        $myStyleFile = Prenotazioni_DIR.'/css/style.css';
        if ( file_exists($myStyleFile) ) {
            wp_register_style($this->plugin_name, $myStyleUrl);
            wp_enqueue_style( $this->plugin_name);
        }
 		$handle = 'jquery.ui.theme';
   		$list = 'enqueued';
     	if (!wp_script_is( $handle, $list )) 
			wp_enqueue_style( 'jquery.ui.theme', plugins_url( 'css/jquery-ui-custom.css', __FILE__ ) );
   }
	function head_Front_End() {
		//wp_enqueue_script( 'Prenotazioni_FrontEnd', plugins_url('js/Prenotazioni_FrontEnd.js', __FILE__ ));
		//echo "<script type='text/javascript' src='".Prenotazioni_URL."js/Prenotazioni_FrontEnd.js'></script>";
  	    wp_enqueue_script('jquery');
	    wp_enqueue_script('jquery-ui-core');
	    wp_enqueue_script('jquery-ui-widget');
	    wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-datepicker', '', array('jquery'));
		wp_enqueue_script('jquery-ui-tooltip');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style('wp-jquery-ui-dialog' );
		wp_enqueue_script('Prenotazioni-FrontEnd', plugins_url('js/Prenotazioni_FrontEnd.js', __FILE__ ));
	}
	static function add_menu(){
  		add_menu_page('Panoramica', 'Prenotazioni', 'manage_options', 'Prenotazioni',array( 'Plugin_Prenotazioni','show_menu'),Prenotazioni_URL."img/logo.png");
  		$parametri_page=add_submenu_page( 'Prenotazioni', 'Parametri', 'Parametri', 'manage_options', 'config', array( 'Plugin_Prenotazioni','show_menu'));
		$prenotazioni_page=add_submenu_page( 'Prenotazioni', 'Prenotazioni', 'Prenotazioni', 'read', 'prenotazioni', array( 'Plugin_Prenotazioni','show_menu'));
}
	
	function show_menu() {
		global $App_Prenotazioni,$Gest_Prenotazioni;
		switch ($_REQUEST['page']){
			case "prenotazioni" :
				$Gest_Prenotazioni->Tabella_Giornaliera_Prenotazioni();		
				break;
			case "config" :
				$App_Prenotazioni->Prenotazioni_config();
				break;
		}
	}
	function Prenotazioni_config(){
      $Parametri=array("OraInizio" =>7,
      				   "OraFine" => 20,
      				   "Giorni" => array(1,1,1,1,1,0,0),
      				   "ColNonPrenotabile" =>"#EBEBEB",
       				   "ColNonDisponibile" =>"#b6b5b5",
     				   "ColRiservato" =>"#FF0000",
      				   "ColPrenotato" =>"#0000FF",
      				   "MaxOrePrenotabili" => 6,
      				   "PrenEntro" => 12);
	  $P  =  get_option('opt_PrenotazioniParametri');
	  if($P!==false)
	  	$Parametri=unserialize($P);
	  for($i=0;$i<7;$i++)
		 if($Parametri['Giorni'][$i]==1)
		  	${"GD_".$i."_SEL"}=" checked='checked'";
//	  var_dump($Parametri);
      echo '
	  <div class="wrap">
	  	<img src="'.Prenotazioni_URL.'/img/opzioni.png" alt="Icona configurazione" style="display:inline;float:left;margin-top:10px;"/>
	  	<h2 style="margin-left:40px;">Parametri Prenotazioni</h2>
	  <form name="Prenotazioni_Parametri" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
	  <table class="form-table">
		<tr valign="top">
			<th scope="row">Fascia oraria disponibilità risorse</th>
			<td>
				<input type="hidden" id="OI" name="OraInizio" value="'.$Parametri['OraInizio'].'">
				<input type="hidden" id="OF" name="OraFine" value="'.$Parametri['OraFine'].'">
				<input type="text" id="dispo-valore-range" style="width:60px;background-color: inherit;border: none;">
			<div id="dispo-range" style="width:200px;"></div></td>
		</tr>
		<tr valign="top">
			<th scope="row">Giorni disponibili per la prenotazione</th>
			<td>
				<input type="checkbox" name="GD_l" value="1" '.$GD_0_SEL.' id="GD_1"/>Lun
				<input type="checkbox" name="GD_m" value="1" '.$GD_1_SEL.' id="GD_2"/>Mar
				<input type="checkbox" name="GD_e" value="1" '.$GD_2_SEL.'  id="GD_3"/>Mer
				<input type="checkbox" name="GD_g" value="1" '.$GD_3_SEL.'  id="GD_4"/>Gio
				<input type="checkbox" name="GD_v" value="1" '.$GD_4_SEL.'  id="GD_5"/>Ven
				<input type="checkbox" name="GD_s" value="1" '.$GD_5_SEL.'  id="GD_6"/>Sab
				<input type="checkbox" name="GD_d" value="1" '.$GD_6_SEL.'  id="GD_7"/>Dom
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row">Colore Spazio non disponibile</th>
			<td> 
				<input type="text" id="ColNonDisponibile" name="ColNonDisponibile" size="5" value="'.$Parametri["ColNonDisponibile"].'"/>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Colore Ore Riservate</th>
			<td> 
				<input type="text" id="coloreRiservato" name="coloreRiservato" size="5" value="'.$Parametri["ColRiservato"].'"/>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Colore Ore Prenotate</th>
			<td> 
				<input type="text" id="colorePrenotato" name="colorePrenotato" size="5" value="'.$Parametri["ColPrenotato"].'"/>
			</td>
		</tr>		
		<tr valign="top">
			<th scope="row">Colore Ore non Prenotabili</th>
			<td> 
				<input type="text" id="colorenonprenotabile" name="colorenonprenotabile" size="5" value="'.$Parametri["ColNonPrenotabile"].'"/>
			</td>
		</tr>		
		<tr valign="top">
			<th scope="row">Numero Massimo di ore prenotabili</th>
			<td> 
				<input type="text" id="max-ore-valore" name="maxOre" style="width:60px;background-color: inherit;border: none;" value="'.$Parametri["MaxOrePrenotabili"].'" />
				<div id="max-ore-range" style="width: 100px;"></div>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label for="entro">Numero ore entro cui bisogna fare le prenotazioni</label></th>
			<td> 
				<input type="text" id="entro" name="entro" style="width:60px;background-color: inherit;border: none;" value="'.$Parametri["PrenEntro"].'" />
				<div id="max-ore-range" style="width: 100px;"></div>
			</td>
		</tr>			</table>
	    <p class="submit">
	    	<input type="hidden" id="origine" name="origine" value="Salva_Opzioni_Prenotazioni">
	        <input type="submit" name="Prenotazioni_submit_button" value="Salva Modifiche" />
	    </p> 
	    </form>
	    </div>';
	}
	function define_tables() {		
		global $wpdb,$table_prefix;
		$wpdb->table_prenotazioni = $table_prefix . "prenotazioni_spazi";
	}
	static function activate() {
		global $wpdb;
		create_Pre_Tabelle($wpdb->table_prenotazioni); 
	}  	 
	
	
	static function deactivate() {
	}
	static function uninstall() {
	}
	function update_Prenotazioni_settings(){
      $Parametri=array("OraInizio" =>7,
      				   "OraFine" => 20,
      				   "Giorni" => array(0,0,0,0,0,0,0),
      				   "ColNonPrenotabile" =>"#EBEBEB",
      				   "ColNonDisponibile" =>"#b6b5b5",
      				   "ColRiservato" =>"#FF0000",
      				   "ColPrenotato" =>"#0000FF",
      				   "MaxOrePrenotabili" => 6,
      				   "PrenEntro" => 12);
	    if($_POST['origine'] == 'Salva_Opzioni_Prenotazioni'){
		    $Parametri['OraInizio']=$_POST['OraInizio'];
		    $Parametri['OraFine']=$_POST['OraFine'];
		    $Parametri['ColNonPrenotabile']=$_POST['colorenonprenotabile'];
		    $Parametri['ColNonDisponibile']=$_POST['ColNonDisponibile'];
		    $Parametri['ColRiservato']=$_POST['coloreRiservato'];
		    $Parametri['ColPrenotato']=$_POST['colorePrenotato'];
		    $Parametri['MaxOrePrenotabili']=$_POST['maxOre'];
		    $Parametri['PrenEntro']=$_POST['entro'];
			if (isset($_POST['GD_l']))
				$Parametri['Giorni'][0]=1;
			if (isset($_POST['GD_m']))
				$Parametri['Giorni'][1]=1;
			if (isset($_POST['GD_e']))
				$Parametri['Giorni'][2]=1;
			if (isset($_POST['GD_g']))
				$Parametri['Giorni'][3]=1;
			if (isset($_POST['GD_v']))
				$Parametri['Giorni'][4]=1;
			if (isset($_POST['GD_s']))
				$Parametri['Giorni'][5]=1;
			if (isset($_POST['GD_d']))
				$Parametri['Giorni'][6]=1;
			$P=serialize($Parametri);
			update_option('opt_PrenotazioniParametri',$P);
			header('Location: '.get_bloginfo('wpurl').'/wp-admin/admin.php?page=config&update=true'); 
 		}
	}
}
	global $App_Prenotazioni,$Gest_Prenotazioni,$G_Spaces;
	$App_Prenotazioni = new Plugin_Prenotazioni();
	$Gest_Prenotazioni = new Prenotazioni();
}
?>