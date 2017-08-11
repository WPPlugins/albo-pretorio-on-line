<?php
/**
 *
 * @link              eduva.org
 * @since             4.0.3
 * @package           Gestione_Corsi
 *
 * @wordpress-plugin
 * Plugin Name:       Albo Pretorio On line
 * Plugin URI:        https://it.wordpress.org/plugins/albo-pretorio-on-line/
 * Description:       Plugin utilizzato per la pubblicazione degli atti da inserire nell'albo pretorio dell'ente.
 * Version:           4.0.3
 * Author:            Ignazio Scimone
 * Author URI:        eduva.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       albo-pretorio-on-line
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }


include_once(dirname (__FILE__) .'/AlboPretorioFunctions.php');			/* libreria delle funzioni */
include_once(dirname (__FILE__) .'/AlboPretorioWidget.php');
//include_once( dirname (__FILE__) .'/admin/webservice.php' );	
//include_once( dirname (__FILE__) .'/admin/albowebservice.php' );	

define("Albo_URL",plugin_dir_url(dirname (__FILE__).'/AlboPretorio.php'));
define("Albo_DIR",dirname (__FILE__));
define("APHomePath",substr(plugin_dir_path(__FILE__),0,strpos(plugin_dir_path(__FILE__),"wp-content")-1));
define("AlboBCK",WP_CONTENT_DIR."/AlboOnLine");

$uploads = wp_upload_dir(); 
define("AP_BASE_DIR",$uploads['basedir']."/");

if (!class_exists('AlboPretorio')) {
 class AlboPretorio {
	
	var $version;
	var $minium_WP   = '3.1';
	var $options     = '';

	function AlboPretorio() {
		if ( ! function_exists( 'get_plugins' ) )
	 		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	    $plugins = get_plugins( "/".plugin_basename( dirname( __FILE__ ) ) );
    	$plugin_nome = basename( ( __FILE__ ) );
	    $this->version=$plugins[$plugin_nome]['Version'];
		// Inizializzazioni
		$this->define_tables();
		$this->load_dependencies();
		$this->plugin_name = plugin_basename(__FILE__);
		// Hook per attivazione/disattivazione plugin
		register_activation_hook( $this->plugin_name, array('AlboPretorio', 'activate'));
		register_deactivation_hook( $this->plugin_name, array('AlboPretorio', 'deactivate') );	
		add_action( 'plugins_loaded', array('AlboPretorio', 'activate') );	

		// Hook disinstallazione
		register_uninstall_hook( $this->plugin_name, array('AlboPretorio', 'uninstall') );

		// Hook di inizializzazione che registra il punto di avvio del plugin
		add_action( 'admin_enqueue_scripts', array( 'AlboPretorio','Albo_Admin_Enqueue_Scripts' )  );
		add_action('init', array('AlboPretorio', 'update_AlboPretorio_settings'));
		add_action('init', array('AlboPretorio', 'init') );
		add_action('init', array('AlboPretorio', 'add_albo_button'));
		
		if (!is_admin()) 
			if (!function_exists('albo_styles'))
				add_action('wp_print_styles', array('AlboPretorio','albo_styles'));
		
		add_shortcode('Albo', array('AlboPretorio', 'VisualizzaAtti'));
		add_action('wp_head', array('AlboPretorio','head_Front_End'));
		add_action( 'admin_menu', array (&$this, 'add_menu') ); 
		add_action('template_redirect', array('AlboPretorio', 'Gestione_Link'));
		add_filter('set-screen-option', array('AlboPretorio', 'atti_set_option'), 10, 3);
		$role =get_role( 'amministratore_albo' );
	}
	
	static function Gestione_Link(){
		if(isset($_REQUEST['action'])){
			switch ($_REQUEST['action']){
			case "dwnalle":
				$chunksize	= 2*(1024*1024);
				$file_path	= ap_get_allegato_atto($_REQUEST['id']);
				$file_path	=$file_path[0]->Allegato;
				$stat 		= @stat($file_path);
				$etag		= sprintf('%x-%x-%x', $stat['ino'], $stat['size'], $stat['mtime'] * 1000000);
				$path 		= pathinfo($file_path);
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private', FALSE);
				header('Content-Type: application/force-download', FALSE);
				header('Content-Type: application/octet-stream', FALSE);
				header('Content-Type: application/download', FALSE);
				header('Content-Disposition: attachment; filename="'.basename($file_path).'";');
				header('Content-Transfer-Encoding: binary');
				header('Last-Modified: ' . date('r', $stat['mtime']));
				header('Etag: "' . $etag . '"');
				header('Content-Length: '.$stat['size']);
				header('Accept-Ranges: bytes');
				ob_flush();
				flush();
				if ($stat['size'] < $chunksize) {
					@readfile($file_path);
				}
				else {
					$handle = fopen($file_path, 'rb');
					while (!feof($handle)) {
						echo fread($handle, $chunksize);
						ob_flush();
						flush();
					}
					fclose($handle);
				}
				if(is_numeric($_REQUEST['id']) and is_numeric($_REQUEST['idAtto']))
					ap_insert_log(6,5,(int)$_REQUEST['id'],"Download",(int)$_REQUEST['idAtto']);
			break;

				exit();
				break;
			}			
		}
	}
	static function Albo_Admin_Enqueue_Scripts( $hook_suffix ) {
	    if(strpos($hook_suffix,"albo-pretorio")===false)
			return;
	    wp_enqueue_script('jquery');
	    wp_enqueue_script('jquery-ui-core');
	    wp_enqueue_script('jquery-ui-tabs', '', array('jquery'));
	    wp_enqueue_script('jquery-ui-dialog', '', array('jquery'));    
		wp_enqueue_script( 'jquery-ui-datepicker', '', array('jquery'));
		wp_enqueue_script( 'wp-color-picker', '', array('jquery'));
	    wp_enqueue_script( 'my-admin-fields', plugins_url('js/Fields.js', __FILE__ ));
	    wp_enqueue_script( 'my-admin', plugins_url('js/Albo.admin.js', __FILE__ ));
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'jquery.ui.theme', plugins_url( 'css/jquery-ui-custom.css', __FILE__ ) );	
		wp_register_style('AdminAlbo', plugins_url( 'css/styleAdmin.css', __FILE__ ) );
        wp_enqueue_style( 'AdminAlbo');

	}

	function CreaStatistiche($IdAtto,$Oggetto){
		$righeVisiteAtto=ap_get_Stat_Visite($IdAtto);
		$righeVisiteDownload=ap_get_Stat_Download($IdAtto);
		$HtmlTesto='';
		if ($Oggetto==5){
			$HtmlTesto='
				<h3>Totale Visite Atto '.ap_get_Stat_Num_log($IdAtto,5).'</h3>
				<table class="widefat">
				    <thead>
					<tr>
						<th style="font-size:1.2em;">Data</th>
						<th style="font-size:1.2em;">Numero Visite</th>
					</tr>
				    </thead>
				    <tbody>';
			foreach ($righeVisiteAtto as $riga) {
				$HtmlTesto.= '<tr >
							<td >'.ap_VisualizzaData($riga->Data).'</td>
							<td >'.$riga->Accessi.'</td>
						</tr>';
				}
			$HtmlTesto.= '    </tbody>
				</table>';
		}else{
			$HtmlTesto.='
				<h3>Totale Download Allegati '.ap_get_Stat_Num_log($IdAtto,6).'</h3>
				<table class="widefat">
				    <thead>
					<tr>
						<th style="font-size:1.2em;">Data</th>
						<th style="font-size:1.2em;">Nome Allegato</th>
						<th style="font-size:1.2em;">File</th>
						<th style="font-size:1.2em;">Numero Download</th>
					</tr>
				    </thead>
				    <tbody>';
			foreach ($righeVisiteDownload as $riga) {
				$HtmlTesto.= '<tr >
							<td >'.ap_VisualizzaData($riga->Data).'</td>
							<td >'.$riga->TitoloAllegato.'</td>
							<td >'.$riga->Allegato.'</td>
							<td >'.$riga->Accessi.'</td>
						</tr>';
				}
			$HtmlTesto.= '    </tbody>
				</table>';
		}
		return $HtmlTesto;	
	}
/*TINY MCE Quote Button*/
static function add_albo_button() {  
  if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
 {  
   add_filter('mce_external_plugins',array('AlboPretorio', 'add_albo_plugin'));  
   add_filter('mce_buttons', array('AlboPretorio','register_albo_button'));  
  }  
}  
static function register_albo_button($buttons) {  
    array_push($buttons, "separator", "albo");  
    return $buttons;  
 }  
static function add_albo_plugin($plugin_array) {  
  $plugin_array['albo'] =Albo_URL.'/js/ButtonEditor.js';  
   return $plugin_array;  
}
	
	function CreaLog($Tipo,$IdOggetto,$IdAtto){
	//	echo $Tipo;
		$HtmlTesto='';
		switch ($Tipo){
			case 1:
				$righe=ap_get_all_Oggetto_log($Tipo,$IdOggetto);
				break;
			case 3:
				$righe=ap_get_all_Oggetto_log($Tipo,0,$IdOggetto);
				break;
			case 5:
			case 6:
				return $this->CreaStatistiche($IdOggetto,$Tipo);
				break;
		}
		if ($Tipo!=5 or $Tipo!=6){
			$HtmlTesto.='<br />';
		}
		$HtmlTesto.='
			<table class="widefat">
			    <thead>
				<tr>
					<th style="font-size:1.2em;">Data</th>
					<th style="font-size:1.2em;">Operazione</th>
					<th style="font-size:1.2em;">Informazioni</th>
				</tr>
			    </thead>
			    <tbody>';
		$Operazione="";
		foreach ($righe as $riga) {
			switch ($riga->TipoOperazione){
			 	case 1:
			 		$Operazione="Inserimento";
			 		break;
			 	case 2:
			 		$Operazione="Modifica";
					break;
			 	case 3:
			 		$Operazione="Cancellazione";
					break;
			 	case 4:
			 		$Operazione="Approvazione";
					break;
			}
			$HtmlTesto.= '<tr  title="'.$riga->Utente.' da '.$riga->IPAddress.'">
						<td >'.ap_VisualizzaData($riga->Data)." ".ap_VisualizzaOra($riga->Data).'</th>
						<td >'.$Operazione.'</th>
						<td >'.stripslashes($riga->Operazione).'</td>
					</tr>';
		}
		$HtmlTesto.= '    </tbody>
				</table>';
		return $HtmlTesto;	
	}

		static function add_menu(){
  		add_menu_page('Panoramica', 'Albo Pretorio', 'gest_atti_albo', 'Albo_Pretorio',array( 'AlboPretorio','show_menu'),Albo_URL."img/logo.png");
		$atti_page=add_submenu_page( 'Albo_Pretorio', 'Atti', 'Atti', 'gest_atti_albo', 'atti', array( 'AlboPretorio','show_menu'));
		$categorie_page=add_submenu_page( 'Albo_Pretorio', 'Categorie', 'Categorie', 'gest_atti_albo', 'categorie', array( 'AlboPretorio', 'show_menu'));
		$enti=add_submenu_page( 'Albo_Pretorio', 'Enti', 'Enti', 'admin_albo', 'enti', array('AlboPretorio', 'show_menu'));
		$responsabili_page=add_submenu_page( 'Albo_Pretorio', 'Responsabili', 'Responsabili', 'admin_albo', 'responsabili', array( 'AlboPretorio','show_menu'));
		$parametri_page=add_submenu_page( 'Albo_Pretorio', 'Generale', 'Parametri', 'admin_albo', 'configAlboP', array( 'AlboPretorio','show_menu'));
		$permessi=add_submenu_page( 'Albo_Pretorio', 'Permessi', 'Permessi', 'admin_albo', 'permessiAlboP', array('AlboPretorio', 'show_menu'));
		$utility=add_submenu_page( 'Albo_Pretorio', 'Utility', 'Utility', 'admin_albo', 'utilityAlboP', array('AlboPretorio', 'show_menu'));		
//		$testrestapi=add_submenu_page( 'Albo_Pretorio', 'Rest API', 'Rest API', 'admin_albo', 'test_rest_api', array('AlboPretorio', 'show_menu'));		
//		add_action( 'admin_head-'. $atti_page, array( 'AlboPretorio','ap_head' ));
/*		$utility=add_submenu_page( 'Albo_Pretorio', 'REST-API', 'Rest-API', 'admin_albo', 'RESTAlboP', array('AlboPretorio', 'show_menu'));		
*/
		add_action( "load-$atti_page", array('AlboPretorio', 'screen_option'));

}
	static function screen_option() {
		if(!isset($_GET['action'])){
			$args=array('label'   => 'Atti per pagina',
				   'default' => 25,
				   'option'  => 'atti_per_page');
			add_screen_option( 'per_page', $args );			
		}
	}

	static function atti_set_option($status, $option, $value) {
	    if ( 'atti_per_page' == $option ) 
	    	return $value;
	}	

	static function show_menu() {
		global $AP_OnLine;

		switch ($_REQUEST['page']){
			case "test_rest_api":
				include_once ( dirname (__FILE__) . '/inc/restAPI.php' );
			case "Albo_Pretorio" :
				$AP_OnLine->AP_menu();
				break;
			case "configAlboP" :
				$AP_OnLine->AP_config();
				break;
			case "categorie" :
			// interfaccia per la gestione delle categorie
				include_once ( dirname (__FILE__) . '/admin/categorie.php' );	
				break;
			case "responsabili" :
			// interfaccia per la gestione dei responsabili
				include_once ( dirname (__FILE__) . '/admin/responsabili.php' );	
				break;
			case "enti" :
			// interfaccia per la gestione dei responsabili
				include_once ( dirname (__FILE__) . '/admin/enti.php' );	
				break;
			case "atti" :
			// interfaccia per la gestione degli atti
				include_once ( dirname (__FILE__) . '/admin/atti.php' );
				break;
			case "allegati" :
			// interfaccia per la gestione degli allegati
				include_once ( dirname (__FILE__) . '/admin/allegati.php' );
				break;
			case "permessiAlboP":
			// interfaccia per la gestione dei permessi
				include_once ( dirname (__FILE__) . '/admin/permessi.php' );
				break;
			case "utilityAlboP":
			// interfaccia per la gestione dei permessi
				include_once ( dirname (__FILE__) . '/admin/utility.php' );
				break;
			case "RESTAlboP":
			// interfaccia per la gestione dei permessi
			//	include_once ( dirname (__FILE__) . '/admin/rest.php' );
				break;
		}
	}
	
	static function init() {
		if (is_admin()) return;
		wp_enqueue_script('jquery');

	}

################################################################################
// ADMIN HEADER
################################################################################


	static function ap_head() {
//		global $wp_db_version, $wp_dlm_root;
		?>
<script language="JavaScript">
	function change(html){
		description.innerHTML=html
	}
</script>
	<?php
	}

	static function head_Front_End() {
		global $wp_query;
		$postObj=$wp_query->get_queried_object();
//		echo $postObj->post_content;
		if (is_object($postObj) And strpos(strtoupper($postObj->post_content),"[ALBO STATO=")!== false){
			echo "
	<!--HEAD Albo Preotrio On line -->
	";
			if(get_option('blog_public')==1)
				echo "	<meta name='robots' content='noindex, nofollow, noarchive' />
	<!--HEAD Albo Preotrio On line -->
			";
			else
				echo "	<meta name='robots' content='noarchive' />
	<!--HEAD Albo Preotrio On line -->
			";
		wp_enqueue_script( 'jquery-ui-datepicker', '', array('jquery'));
	    wp_enqueue_script('jquery');
		wp_enqueue_script( 'Albo-Public', plugins_url('js/Albo.public.js', __FILE__ ));
	echo "<!--FINE HEAD Albo Preotrio On line -->";	
		}
	}
	
	function load_dependencies() {
			// Load backend libraries
			if ( is_admin() ) {	
				require_once (dirname (__FILE__) . '/admin/admin.php');
			}	
		}
	
	static function VisualizzaAtti($Parametri){
		$ret="";
	extract(shortcode_atts(array(
		'Stato' => '1',
		'Cat' => 'All',
		'Filtri' => 'si',
		'MinFiltri' =>'si',
		'per_page' =>'10'
	), $Parametri));
	require_once ( dirname (__FILE__) . '/admin/frontend.php' );
	return $ret;
	}

	function AP_menu(){
	global $wpdb;
	  
	  if (isset($_REQUEST['action']) And $_REQUEST['action']=="setta-anno"){
		update_option('opt_AP_AnnoProgressivo',date("Y") );
		update_option('opt_AP_NumeroProgressivo',1 );
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
	  }
	  $n_atti = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti;");	 
	  $n_atti_dapub = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti Where Numero=0;");	
	  $n_atti_attivi = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti Where DataInizio <= now() And DataFine>= now() And Numero>0;");	
	  $n_atti_storico=$n_atti-$n_atti_attivi-$n_atti_dapub; 
	  $n_allegati = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Allegati;");	 
	  $n_categorie = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Categorie;");	 
      $n_atti_oblio = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti Where DataOblio < now() And Numero>0;");	
	echo ' <div class="welcome-panel" class="welcome-panel" style="padding: 5px;5px; 20px; 5px;">
	         	<div class="welcome-panel-content" style="display:inline;float:left;">
					<p style="text-align: center;">
						<img src="'.Albo_URL.'/img/LogoAlbo.png" alt="Logo Albo on line pubblicità legale" />
					<br />Versione <strong>'.$this->version.'</strong></p>
					<p style="font-size:1.2em;text-align: center;">Plugin sviluppato da <strong>Scimone Ignazio</strong>
					<br /><small>con il contributo della Comunità di Pratica</small>
					<a href="http://www.porteapertesulweb.it" target="_blank" title="Porte Aperte sul Web">
					<br /><img src="'.Albo_URL.'/img/pasw.png"></a>	<br />				
					<a href="https://www.youtube.com/channel/UCjV2LoFIVcoUKux6VeHWohg" target="_blank" title="vai al canale YouTube">
						<img src="'.Albo_URL.'/img/canaleyoutube.png" alt="Logo Canale Youtube Albo" />
					</a>
					</p>	

				</div>
				<div class="welcome-panel-content"  style="display:inline;float:right;width:60%;">
					<div class="widefat" style="display:inline;">
						<table style="margin-bottom:20px;border: 1px solid #e5e5e5;">
							<caption style="font-size:1.2em;font-weight:bold;">Sommario</caption>
							<thead>
								<tr>
									<th>Oggetto</th>
									<th>N.</th>
									<th>In Attesa di Pubblicazione</th>
									<th>Attivi</th>
									<th>Scaduti</th>
									<th>Da eliminare</th>
								</tr>
							</thead>
							<tbody>
								<tr class="first">
									<td style="text-align:left;width:200px;" >Atti</td>
									<td style="text-align:left;width:200px;">'.$n_atti.'</td>
									<td style="text-align:left;width:200px;">'.$n_atti_dapub.'</td>
									<td style="text-align:left;width:200px;">'.$n_atti_attivi.'</td>
									<td style="text-align:left;width:200px;">'.$n_atti_storico.'</td>
									<td style="text-align:left;width:200px;">'.$n_atti_oblio.'</td>
								</tr>
								<tr>
									<td>Categorie</td>
									<td colspan="4">'.$n_categorie.'</td>
								</tr>
								<tr>
									<td>Allegati</td>
									<td colspan="4">'.$n_allegati.'</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div style="width: 400px;margin: auto;padding:0;">
						<a href="http://eduva.org" target="_blank">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Sito di suporto">
						</a>
						<a href="http://www.eduva.org/wp-content/uploads/2014/02/Albo-Pretorio-On-line.pdf" target="_blank">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Manuale Albo Pretorio">
						</a>
						<a href="http://www.eduva.org/io-utilizzo-il-plugin"target="_blank">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Io utilizzo il plugin">
						</a>
						<br />

		 				<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FAlbo-Pretorio%2F1487571581520684%3Fref%3Dhl&amp;width&amp;height=290&amp;colorscheme=light&amp;show_faces=true&amp;header=true&amp;stream=false&amp;show_border=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden;height:290px; width: 300px; margin-top:20px;margin-left: 50px;" allowTransparency="true"></iframe>
		 			</div>	
				</div>
			</div>
	';
	if ($this->version>=3.0 and !is_file(AP_BASE_DIR.get_option('opt_AP_FolderUpload')."/.htaccess")){
	echo'<div class="welcome-panel" >
		<div class="widefat" >
			<p style="text-align:center;font-size:1.2em;font-weight: bold;color: red;">Questa versione dell plugin implementa il diritto all\'oblio, questo meccanismo permette agli utenti di accedere agli allegati degli atti pubblicati all\'albo pretorio solo dal sito che ospita l\'albo e non con link diretti al file<br />Non risulta ancora attivato il diritto all\'oblio,<br /><a href="?page=utilityAlboP&amp;action=oblio">Attivalo</a></p>
			</div>
		</div>';
	}
if (ap_get_num_categorie()==0){
echo'<div class="welcome-panel" >
		<div class="widefat" >
				<p style="text-align:center;font-size:1.2em;font-weight: bold;color: green;">
				Non risultano categorie codificate, se vuoi posso impostare le categorie di default &ensp;&ensp;<a href="?page=utilityAlboP&amp;action=creacategorie">Crea Categorie di Default</a></p>
			</div>
		</div>';
}
if (ap_num_responsabili()==0){
echo'<div class="welcome-panel" >
		<div class="widefat" >
				<p style="text-align:center;font-size:1.2em;font-weight: bold;color: green;">
				Non risultano <strong>Responsabili</strong> codificati, devi crearne almeno uno prima di iniziare a codificare gli Atti &ensp;&ensp;<a href="?page=responsabili">Crea Responsabile</a></p>
			</div>
		</div>';
}
if(get_option('opt_AP_AnnoProgressivo')!=date("Y")){
	echo '<div style="border: medium groove Blue;margin-top:10px;">
			<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
				<form id="agg_anno_progressivo" method="post" action="?page=configAlboP">
					<input type="hidden" name="action" value="setta-anno" />
				<input type="submit" name="submit" id="submit" class="button" value="Aggiorna Anno Albo ed Azzera numero Progressivo"  />
				</form>
			</div>
		 </div>';
}
}	

	function AP_config(){
	$stato="";
	  if (isset($_REQUEST['action']) And $_REQUEST['action']=="setta-anno"){
		update_option('opt_AP_AnnoProgressivo',date("Y") );
		update_option('opt_AP_NumeroProgressivo',1 );
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
	  }
	  
	  if (isset($_GET['update']))
	  	if($_GET['update'] == 'true')
			$stato="<div id='setting-error-settings_updated' class='updated settings-error'> 
				<p><strong>Impostazioni salvate.</strong></p></div>";
		  else
			$stato="<div id='setting-error-settings_updated' class='updated settings-error'> 
				<p><strong>ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata.</strong></p></div>";
	  $current_user = wp_get_current_user();
	  $ente   = stripslashes(ap_get_ente_me());
	  $nprog  =  get_option('opt_AP_NumeroProgressivo');
	  $nanno=get_option('opt_AP_AnnoProgressivo');
	  $visente=get_option('opt_AP_VisualizzaEnte');
	  $livelloTitoloEnte=get_option('opt_AP_LivelloTitoloEnte');
	  $livelloTitoloPagina=get_option('opt_AP_LivelloTitoloPagina');
	  $livelloTitoloFiltri=get_option('opt_AP_LivelloTitoloFiltri');
	  $colAnnullati=get_option('opt_AP_ColoreAnnullati');
	  $colPari=get_option('opt_AP_ColorePari');
	  $colDispari=get_option('opt_AP_ColoreDispari');
	  $stileTableFE=get_option('opt_AP_stileTableFE');
	  $LogOperazioni=get_option('opt_AP_LogOp');
	  //$TempoOblio=get_option('opt_AP_GiorniOblio');
	  $FEColsOption=get_option('opt_AP_ColonneFE',array(
	  										"Ente"=>0,
	  										"Riferimento"=>0,
	  										"Oggetto"=>0,
	  										"Validita"=>0,
	  										"Categoria"=>0,
											"Note"=>0,
											"RespProc"=>0,
											"DataOblio"=>0));
	  if(!is_array($FEColsOption)){
	  	$FEColsOption=json_decode($FEColsOption,TRUE);
	  }
	  $LOStatoS="";
	  $LOStatoN=" checked='checked' ";
	  if($LogOperazioni=="Si"){
	  		$LOStatoS=" checked='checked' ";
	  		$LOStatoN="";
	  }	  
	  $LogAccessi=get_option('opt_AP_LogAc');
	  $LOAccessiS="";
	  $LOAccessiN=" checked='checked' ";
	  if($LogAccessi=="Si"){
	  		$LOAccessiS=" checked='checked' ";
	  		$LOAccessiN="";
	  }	  
	  $LogAccessi=get_option('opt_AP_LogAc');
	  $selstiletab=" checked='checked' ";
	  $selstiledatatab="";
	  if($stileTableFE=="DataTables"){
	  		$selstiledatatab=" checked='checked' ";
	  }
	  if ($visente=="Si")
	  	$ve_selezionato='checked="checked"';
	  else
	  	$ve_selezionato='';
	  if (!$nanno){
		$nanno=date("Y");
		}
	  $dirUpload =  stripslashes(get_option('opt_AP_FolderUpload'));
	  echo '
	  <div class="wrap">
	  	<h2 style="font-size:2em;">Parametri</h2>
	  '.$stato.'

	 <form name="AlboPretorio_cnf" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
	  <input type="hidden" name="c_AnnoProgressivo" value="'.$nanno.'"/>
	  <input type="hidden" name="confAP" value="'.wp_create_nonce('configurazionealbo').'" />
	  <div id="config-tabs-container" style="margin-top:20px;">
		<ul>
			<li><a href="#Conf-tab-1">Impostazioni Generali</a></li>
			<li><a href="#Conf-tab-2">Colori</a></li>
			<li><a href="#Conf-tab-3">Log</a></li>
			<li><a href="#Conf-tab-4">Colonne Tabella Front End</a></li>
		</ul>	 
		<div id="Conf-tab-1">
		  <table class="albo_cell">
			<tr>
				<th scope="row"><label for="nomeente">Nome Ente</label></th>
				<td><input type="text" name="c_Ente" value=\''.$ente.'\' size="100" id="nomeente"/></td>
			</tr>
			<tr>
				<th scope="row"><label for="visente">Visualizza Nome Ente</label></th>
				<td><input type="checkbox" name="c_VEnte" value="Si" '.$ve_selezionato.' id="visente"/></td>
			</tr>
			<tr>
				<th scope="row"><label for="LivelloTitoloEnte">Titolo Nome Ente</label></th>
				<td>
					<select name="c_LTE" id="LivelloTitoloEnte" >';
				for ($i=2;$i<5;$i++){
					echo '<option value="h'.$i.'"';
					if($livelloTitoloEnte=='h'.$i) 
						echo 'selected="selected"';
					echo '>h'.$i.'</option>';	
				}
			echo '</select></td>
			</tr>		
			<tr>
				<th scope="row"><label for="LivelloTitoloPagina">Titolo Pagina Albo</label></th>
				<td>
					<select name="c_LTP" id="LivelloTitoloPagina" >';
				for ($i=2;$i<5;$i++){
					echo '<option value="h'.$i.'"';
					if($livelloTitoloPagina=='h'.$i) 
						echo 'selected="selected"';
					echo '>h'.$i.'</option>';	
				}
			echo '</select></td>
			</tr>		
			<tr>
				<th scope="row"><label for="LivelloTitoloFiltri">Titolo Filtri</label></th>
				<td>
					<select name="c_LTF" id="LivelloTitoloFiltri" >';
				for ($i=2;$i<5;$i++){
					echo '<option value="h'.$i.'"';
					if($livelloTitoloFiltri=='h'.$i) 
						echo 'selected="selected"';
					echo '>h'.$i.'</option>';	
				}
			echo '</select></td>
			</tr>		
			<tr>
				<th scope="row"><label>Numero Progressivo</label></th>
				<td><strong> ';
				if(ap_get_all_atti(0,0,0,'',0,0,"",0,0,TRUE,TRUE)==0)
					echo '<input type="text" id="progressivo" name="progressivo" value="'.$nprog.'" size="5"/>';
				else
					echo $nprog;
			echo ' / '.$nanno.'</strong>	
				</td>
			</tr>
			<tr>
				<th scope="row"><label>Cartella Upload</label></th>
				<td><strong> '.AP_BASE_DIR.get_option('opt_AP_FolderUpload').'</strong></td>
			</tr>
		</table>
		</div>
		<div id="Conf-tab-2">		  
			<table class="albo_cell">
			<tr>
				<th scope="row"><label for="color">Righe Atti Annullati</label></th>
				<td> 
					<input type="text" id="color" name="color" value="'.$colAnnullati.'" size="5"/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="colorp">Righe Pari</label></th>
				<td> 
					<input type="text" id="colorp" name="colorp" value="'.$colPari.'" size="5"/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="colord">Righe Dispari</label></th>
				<td> 
					<input type="text" id="colord" name="colord" value="'.$colDispari.'" size="5"/>
				</td>
			</tr>
		</table>
		</div>
		<div id="Conf-tab-3">		  
			<table class="albo_cell">
			<tr>
				<th scope="row"><label for="LogOperazioni">Abilita il Log sulle Operazioni di gestione degli Oggetti dell\'Albo</label></th>
				<td> 
					<input type="radio" id="LogOperazioniSi" name="LogOperazioni" value="Si" '.$LOStatoS.'>Si<br>
					<input type="radio" id="LogOperazioniNo" name="LogOperazioni" value="No" '.$LOStatoN.'>No
				</td>		
			</tr>
			<tr>
				<th scope="row"><label for="LogOperazioni">Abilita il Log sulle Visualizzazioni/Download degli atti pubblicati</label></th>
				<td> 
					<input type="radio" id="LogAccessiSi" name="LogAccessi" value="Si" '.$LOAccessiS.'>Si<br>
					<input type="radio" id="LogAccessiNo" name="LogAccessi" value="No" '.$LOAccessiN.'>No
				</td>		
			</tr>
		</table>
		</div>
		<div id="Conf-tab-4">		  
			<table class="albo_cell">
			<tr>
				<th scope="row"><label for="ente">Ente</label></th>
				<td> 
					<input type="checkbox" id="ente" name="Ente" value="1" '.($FEColsOption['Ente']==1?"checked":"").'/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="riferimento">Riferimento</label></th>
				<td> 
					<input type="checkbox" id="riferimento" name="Riferimento" value="1" '.($FEColsOption['Riferimento']==1?"checked":"").'/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="oggetto">Oggetto</label></th>
				<td> 
					<input type="checkbox" id="oggetto" name="Oggetto" value="1" '.($FEColsOption['Oggetto']==1?"checked":"").'/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="validita">Validit&agrave;</label></th>
				<td> 
					<input type="checkbox" id="validita" name="Validita" value="1" '.($FEColsOption['Validita']==1?"checked":"").'/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="categoria">Categoria</label></th>
				<td> 
					<input type="checkbox" id="categoria" name="Categoria" value="1" '.($FEColsOption['Categoria']==1?"checked":"").'/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="note">Note</label></th>
				<td> 
					<input type="checkbox" id="note" name="Note" value="1" '.($FEColsOption['Note']==1?"checked":"").'/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="respproc">Responsabile Procedura</label></th>
				<td> 
					<input type="checkbox" id="respproc" name="RespProc" value="1" '.($FEColsOption['RespProc']==1?"checked":"").'/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="oblio">Data Oblio</label></th>
				<td> 
					<input type="checkbox" id="oblio" name="DataOblio" value="1" '.($FEColsOption['DataOblio']==1?"checked":"").'/>
				</td>
			</tr>
			</table>
		</div>
	</div>
	    <p class="submit">
	        <input type="submit" name="AlboPretorio_submit_button" value="Salva Modifiche" />
	    </p> 
   	
	    </form>
	    </div>';
		if(get_option('opt_AP_AnnoProgressivo')!=date("Y")){
			echo '<div style="border: medium groove Blue;margin-top:10px;margin-right:250px;">
					<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
						<form id="agg_anno_progressivo" method="post" action="?page=configAlboP">
						<input type="hidden" name="action" value="setta-anno" />
	  					<input type="hidden" name="confAP" value="'.wp_create_nonce('configurazionealbo').'" />
						<input type="submit" name="submit" id="submit" class="button" value="Aggiorna Anno Albo ed Azzera numero Progressivo"  />
						</form>
					</div>
				  </div>';
		}

	}
	function define_tables() {		
		global $wpdb,$table_prefix;
		
		// add database pointer 
		$wpdb->table_name_Atti = $table_prefix . "albopretorio_atti";
		$wpdb->table_name_Categorie = $table_prefix . "albopretorio_categorie";
		$wpdb->table_name_Allegati = $table_prefix . "albopretorio_allegati";
		$wpdb->table_name_Log=$table_prefix . "albopretorio_log";
		$wpdb->table_name_RespProc=$table_prefix . "albopretorio_resprocedura";
		$wpdb->table_name_Enti=$table_prefix . "albopretorio_enti";
	}

	static function activate() {
	global $wpdb;
	if(get_option('opt_AP_ColonneFE')  == '' || !get_option('opt_AP_ColonneFE')){
		$FEColsOption=array("Ente"=>0,"Riferimento"=>0,"Oggetto"=>1, "Validita"=>1,
					  "Categoria"=>1,"Note"=>0,"RespProc"=>0, "DataOblio"=>0);
		add_option('opt_AP_ColonneFE',json_encode($FEColsOption));
		}
	if(get_option('opt_AP_Versione')  == '' || !get_option('opt_AP_Versione')){
			add_option('opt_AP_Versione', '0');
		}
	$PData = get_plugin_data( __FILE__ );
	$PVer = $PData['Version'];	
	if (get_option( 'opt_AP_Versione' ) == $PVer) {
		return;
	} 
	update_option('opt_AP_Versione', $PVer);
	if (file_exists(Albo_DIR."/js/gencode.php")){
		chmod(Albo_DIR."/js/gencode.php", 0755);
	}
		$role = get_role( 'administrator' );

        /* Aggiunta dei ruoli all'Amministratore */
        if ( !empty( $role ) ) {
            $role->add_cap( 'admin_albo' );
            $role->add_cap( 'gest_atti_albo' );
        }

        /* Creazione ruolo di Amministratore */
        add_role(
            'amministratore_albo',
            'Amministratore Albo',
            array(
				'read' => true, 
                'admin_albo' => true,
                'gest_atti_albo' => true)
        );
        
        
        /* Creazione del ruolo di Redattore */
        add_role(
			'gestore_albo',
            'Redattore Albo',
            array('read' => true,
				  'gest_atti_albo' => true)
        );
		
				// Add the admin menu

		if(get_option('opt_AP_AnnoProgressivo')  == '' || !get_option('opt_AP_AnnoProgressivo')){
			add_option('opt_AP_AnnoProgressivo', ''.date("Y").'');
		}
		if(get_option('opt_AP_NumeroProgressivo')  == '' || !get_option('opt_AP_NumeroProgressivo')){
			add_option('opt_AP_NumeroProgressivo', '1');
		}
		if(get_option('opt_AP_FolderUpload') == '' || !get_option('opt_AP_FolderUpload')){
			if(!is_dir(AP_BASE_DIR.'AllegatiAttiAlboPretorio')){   
				mkdir(AP_BASE_DIR.'AllegatiAttiAlboPretorio', 0755);
				ap_NoIndexNoDirectLink(AP_BASE_DIR.'AllegatiAttiAlboPretorio');
			}
			add_option('opt_AP_FolderUpload', 'AllegatiAttiAlboPretorio');
		}else{
			if (get_option('opt_AP_FolderUpload')=='wp-content/uploads')
				update_option('opt_AP_FolderUpload', '');
		}
			
		if(get_option('opt_AP_VisualizzaEnte') == '' || !get_option('opt_AP_VisualizzaEnte')){
			add_option('opt_AP_VisualizzaEnte', 'Si');
		}

		if(get_option('opt_AP_LivelloTitoloEnte') == '' || !get_option('opt_AP_LivelloTitoloEnte')){
			add_option('opt_AP_LivelloTitoloEnte', 'h2');
		}
		if(get_option('opt_AP_LivelloTitoloPagina') == '' || !get_option('opt_AP_LivelloTitoloPagina')){
			add_option('opt_AP_LivelloTitoloPagina', 'h3');
		}
		if(get_option('opt_AP_LivelloTitoloFiltri') == '' || !get_option('opt_AP_LivelloTitoloFiltri')){
			add_option('opt_AP_LivelloTitoloFiltri', 'h4');
		}
		if(get_option('opt_AP_ColoreAnnullati') == '' || !get_option('opt_AP_ColoreAnnullati')){
			add_option('opt_AP_ColoreAnnullati', '#FFCFBD');
		}
		if(get_option('opt_AP_ColorePari') == '' || !get_option('opt_AP_ColorePari')){
			add_option('opt_AP_ColorePari', '#ECECEC');
		}
		if(get_option('opt_AP_ColoreDispari') == '' || !get_option('opt_AP_ColoreDispari')){
			add_option('opt_AP_ColoreDispari', '#FFF');
		}
		if(get_option('opt_AP_stileTableFE') == '' || !get_option('opt_AP_stileTableFE')){
			add_option('opt_AP_stileTableFE', 'Table');
		}
		if(get_option('opt_AP_LogOp') == '' || !get_option('opt_AP_LogOp')){
			add_option('opt_AP_LogOp', 'Si');
		}
		if(get_option('opt_AP_LogAc') == '' || !get_option('opt_AP_LogAc')){
			add_option('opt_AP_LogAc', 'Si');
		}
		if(get_option('opt_AP_GiorniOblio') == '' || !get_option('opt_AP_GiorniOblio')){
			add_option('opt_AP_GiorniOblio', '1825');
		}/**
* Eliminazione Opzioni
* 
*/
		if(get_option('opt_AP_EffettiTesto') !==TRUE){
			delete_option('opt_AP_EffettiTesto');
		}
		if(get_option('opt_AP_EffettiCSS3') !==TRUE){
			delete_option('opt_AP_EffettiCSS3');
		}
		
		ap_CreaTabella($wpdb->table_name_Atti);
		ap_CreaTabella($wpdb->table_name_Categorie);
		ap_CreaTabella($wpdb->table_name_Allegati);
		ap_CreaTabella($wpdb->table_name_Log);
		ap_CreaTabella($wpdb->table_name_RespProc);
		ap_CreaTabella($wpdb->table_name_Enti);
     
/*************************************************************************************
** Area riservata per l'aggiunta di nuovi campi in una delle tabelle dell' albo ******
*************************************************************************************/
 		if(ap_get_ente_me() == '' || !ap_get_ente(0)){
			ap_create_ente_me();
		}         
	
	
		if (!ap_existFieldInTable($wpdb->table_name_Atti, "RespProc")){
			ap_AggiungiCampoTabella($wpdb->table_name_Atti, "RespProc", " INT NOT NULL");				
		}
		if (!ap_existFieldInTable($wpdb->table_name_Atti, "DataOblio")){
			ap_AggiungiCampoTabella($wpdb->table_name_Atti, "DataOblio", " date NOT NULL DEFAULT '0000-00-00'");
			ap_SetDefaultDataScadenza();
		}
		if (!ap_existFieldInTable($wpdb->table_name_Atti, "MotivoAnnullamento")){
			ap_AggiungiCampoTabella($wpdb->table_name_Atti, "MotivoAnnullamento", " varchar(100) default ''");
		}
		if (!ap_existFieldInTable($wpdb->table_name_Atti, "Ente")){
			ap_AggiungiCampoTabella($wpdb->table_name_Atti, "Ente", " INT NOT NULL default 0");
		}
		if (strtolower(ap_typeFieldInTable($wpdb->table_name_Atti,"Riferimento"))!="varchar(255)"){
			ap_ModificaTipoCampo($wpdb->table_name_Atti, "Riferimento", "varchar(255)");
		}
		if (strtolower(ap_typeFieldInTable($wpdb->table_name_Atti,"Oggetto"))!="text"){
			ap_ModificaTipoCampo($wpdb->table_name_Atti, "Oggetto", "TEXT");
		}
		if (strtolower(ap_typeFieldInTable($wpdb->table_name_Atti,"MotivoAnnullamento"))!="varchar(255)"){
			ap_ModificaTipoCampo($wpdb->table_name_Atti, "MotivoAnnullamento", "varchar(255)");
		}
		if (strtolower(ap_typeFieldInTable($wpdb->table_name_Atti,"Informazioni"))!="text"){
			ap_ModificaTipoCampo($wpdb->table_name_Atti, "Informazioni", "TEXT");
		}

//		ap_ModificaParametriCampo($Tabella, $Campo, $Tipo $Parametro)
		$par=ap_EstraiParametriCampo($wpdb->table_name_Atti,"Riferimento");
		if(strtolower($par["Null"])=="yes")
			ap_ModificaParametriCampo($wpdb->table_name_Atti, "Riferimento",$par["Type"] ,"NOT NULL");
		$par=ap_EstraiParametriCampo($wpdb->table_name_Atti,"Oggetto");
		if(strtolower($par["Null"])=="yes")
			ap_ModificaParametriCampo($wpdb->table_name_Atti, "Oggetto",$par["Type"] ,"NOT NULL");

	}  	 
	
	
	static function deactivate() {
		
	remove_shortcode('Albo');
	
	}
	static function uninstall() {
		global $wpdb;

// Backup di sicurezza
// creo copia dei dati e dei files allegati prima di disinstallare e cancellare tutto
		$uploads = wp_upload_dir(); 
		$Data=date('Ymd_H_i_s');
		$nf=ap_BackupDatiFiles($Data);
		copy($nf, $uploads['basedir']."/BackupAlboPretorioUninstall".$Data.".zip");
// Eliminazioni capacità
        
		$role =& get_role( 'administrator' );
		if ( !empty( $role ) ) {
        	$role->remove_cap( 'admin_albo' );
            $role->remove_cap( 'gest_atti_albo' );
        }

// Eliminazioni ruoli
        $roles_to_delete = array(
            'admin_albo',
            'gest_atti_albo');

        foreach ( $roles_to_delete as $role ) {

            $users = get_users( array( 'role' => $role ) );
            if ( count( $users ) <= 0 ) {
                remove_role( $role );
            }
        }		
		
// Eliminazione Tabelle data Base
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Atti);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Allegati);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Categorie);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Log);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_RespProc);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Enti);
		
// Eliminazioni Opzioni
		delete_option( 'opt_AP_Ente' );
		delete_option( 'opt_AP_NumeroProgressivo' );
		delete_option( 'opt_AP_AnnoProgressivo' );
		delete_option( 'opt_AP_NumeroProtocollo' );
		delete_option( 'opt_AP_LivelloTitoloEnte' );
		delete_option( 'opt_AP_LivelloTitoloPagina' );
		delete_option( 'opt_AP_LivelloTitoloFiltri' );
		delete_option( 'opt_AP_FolderUpload' );
		delete_option( 'opt_AP_VisualizzaEnte' );  
		delete_option( 'opt_AP_ColoreAnnullati' );  
		delete_option( 'opt_AP_ColorePari' );  
		delete_option( 'opt_AP_ColoreDispari' );  
		delete_option( 'opt_AP_EffettiTesto' );  
		delete_option( 'opt_AP_GiorniOblio' );  
		delete_option( 'opt_AP_LogAc' );  
		delete_option( 'opt_AP_LogOp' );  
		delete_option( 'opt_AP_stileTableFE' );  
		delete_option( 'opt_AP_Versione' );  
	}

	static function update_AlboPretorio_settings(){
	    if(isset($_POST['AlboPretorio_submit_button']) And $_POST['AlboPretorio_submit_button'] == 'Salva Modifiche'){
	    	if (!isset($_POST['confAP'])) {
	    		header('Location: '.get_bloginfo('wpurl').'/wp-admin/admin.php?page=configAlboP&update=false'); 		
	    	}
			if (!wp_verify_nonce($_POST['confAP'],'configurazionealbo')){
				header('Location: '.get_bloginfo('wpurl').'/wp-admin/admin.php?page=configAlboP&update=false'); 
			} 		
		    ap_set_ente_me(strip_tags($_POST['c_Ente']));
			if ($_POST['c_VEnte']=='Si')
			    update_option('opt_AP_VisualizzaEnte','Si' );
			else
				update_option('opt_AP_VisualizzaEnte','No' );
			if (isset($_POST['progressivo']))
			    update_option('opt_AP_NumeroProgressivo',(int)$_POST['progressivo'] );
		    update_option('opt_AP_Ente',$_POST['c_Ente'] );
		    update_option('opt_AP_AnnoProgressivo',$_POST['c_AnnoProgressivo'] );
		    update_option('opt_AP_EffettiTesto',$_POST['c_TE'] );
		    update_option('opt_AP_LivelloTitoloPagina',$_POST['c_LTP'] );
		    update_option('opt_AP_LivelloTitoloFiltri',$_POST['c_LTF'] );
			update_option('opt_AP_ColoreAnnullati',strip_tags($_POST['color']) );
			update_option('opt_AP_ColorePari',strip_tags($_POST['colorp']) );
			update_option('opt_AP_ColoreDispari',strip_tags($_POST['colord']) );
			update_option('opt_AP_stileTableFE',$_POST['stileTableFE']);
			update_option('opt_AP_LogOp', $_POST['LogOperazioni']);
			update_option('opt_AP_LogAc', $_POST['LogAccessi']);
		  	$FEColsOption=array("Ente"=>(isset($_POST['Ente'])?1:0),
		  					  "Riferimento"=>(isset($_POST['Riferimento'])?1:0),
		  					  "Oggetto"=>(isset($_POST['Oggetto'])?1:0),
		  					  "Validita"=>(isset($_POST['Validita'])?1:0),
		  					  "Categoria"=>(isset($_POST['Categoria'])?1:0),
							  "Note"=>(isset($_POST['Note'])?1:0),
							  "RespProc"=>(isset($_POST['RespProc'])?1:0),
							  "DataOblio"=>(isset($_POST['DataOblio'])?1:0)
			);
			update_option('opt_AP_ColonneFE', json_encode($FEColsOption)); 
			header('Location: '.get_bloginfo('wpurl').'/wp-admin/admin.php?page=configAlboP&update=true'); 
  		}
	}

	static function albo_styles() {
        $myStyleUrl = plugins_url('css/style.css', __FILE__); 
        $myStyleFile = Albo_DIR.'/css/style.css';
        if ( file_exists($myStyleFile) ) {
            wp_register_style('AlboPretorio', $myStyleUrl);
            wp_enqueue_style( 'AlboPretorio');
        }
    }
}
	global $AP_OnLine;
	$AP_OnLine = new AlboPretorio();
//	$AlboWS= new AlboWebService();
//	$AlboWS->register_routes();

/*	function InserisciAlboPretorio($Stato=1,$Per_Page=10,$Cat=0){
	 global $AP_OnLine;
	 $Parametri=array("Stato" => $Stato,
                  "Per_Page" => $Per_Page,
				  "Cat" => $Cat);
	require_once ( dirname (__FILE__) . '/admin/frontend.php' );
	echo $ret;

	echo $AP_OnLine->VisualizzaAtti($Parametri);
	
}*/
}
?>