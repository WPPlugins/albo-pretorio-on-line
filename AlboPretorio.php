<?php
/*
Plugin Name:Albo Pretorio On line
Plugin URI: http://plugin.sisviluppo.info
Description: Plugin utilizzato per la pubblicazione degli atti da inserire nell'albo pretorio dell'ente.
Version:3.0.4
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

if ($_GET['update'] == 'true')
	$stato="<div id='setting-error-settings_updated' class='updated settings-error'> 
			<p><strong>Impostazioni salvate.</strong></p></div>";

include_once(dirname (__FILE__) .'/functions.inc.php');				/* Various functions used throughout */
include_once(dirname (__FILE__) .'/AlboPretorio.widget.inc');
define("Albo_URL",plugin_dir_url(dirname (__FILE__).'/AlboPretorio.php'));
define("Albo_DIR",dirname (__FILE__));
define("APHomePath",substr(plugin_dir_path(__FILE__),0,strpos(plugin_dir_path(__FILE__),"wp-content")-1));
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

		// Hook disinstallazione
		register_uninstall_hook( $this->plugin_name, array('AlboPretorio', 'uninstall') );

		// Hook di inizializzazione che registra il punto di avvio del plugin
		add_action( 'admin_enqueue_scripts',  array('AlboPretorio','enqueue_scripts') );
		add_action('init', array('AlboPretorio', 'update_AlboPretorio_settings'));
		add_action('init', array('AlboPretorio', 'init') );
		add_action('init', array('AlboPretorio', 'add_albo_button'));
		
		if (!is_admin()) 
			if (!function_exists('albo_styles'))
				add_action('wp_print_styles', array('AlboPretorio','albo_styles'));
		
		add_shortcode('Albo', array('AlboPretorio', 'VisualizzaAtti'));
		add_action('wp_head', array('AlboPretorio','head_Front_End'));
		add_action( 'admin_menu', array (&$this, 'add_menu') ); 
		add_action( 'wp_ajax_logdati', array('AlboPretorio','get_crealog'));
		$role =& get_role( 'amministratore_albo' );
	}
	function enqueue_scripts( $hook_suffix ) {
	    if(strpos($hook_suffix,"albo-pretorio")===false)
			return;
	    wp_enqueue_script('jquery');
	    wp_enqueue_script( 'my-admin-fields', plugins_url('js/Fields.js', __FILE__ ));
		wp_enqueue_script( 'jquery-ui-datepicker', '', array('jquery'));
		wp_enqueue_script( 'wp-color-picker');
	    wp_enqueue_script( 'my-admin', plugins_url('js/Albo.admin.js', __FILE__ ));
		wp_enqueue_script( 'my-admin-dtpicker-it', plugins_url('js/jquery.ui.datepicker-it.js', __FILE__ ));
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'jquery.ui.theme', plugins_url( 'css/jquery-ui-custom.css', __FILE__ ) );
	}

/*TINY MCE Quote Button*/
function add_albo_button() {  
  if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  
 {  
   add_filter('mce_external_plugins',array('AlboPretorio', 'add_albo_plugin'));  
   add_filter('mce_buttons', array('AlboPretorio','register_albo_button'));  
  }  
}  
function register_albo_button($buttons) {  
    array_push($buttons, "separator", "albo");  
    return $buttons;  
 }  
function add_albo_plugin($plugin_array) {  
  $plugin_array['albo'] =Albo_URL.'/js/ButtonEditor.js';  
   return $plugin_array;  
}

	function get_crealog(){
		global $AP_OnLine;
		$Tipo=$_POST['Tipo'];
		$IdOggetto=$_POST['IdOggetto'];
		$IdAtto=$_POST['IdAtto'];
		if ($Tipo==99){
			echo $AP_OnLine->CreaStatistiche($IdAtto);
		}else{
			echo $AP_OnLine->CreaLog($Tipo,$IdOggetto,$IdAtto);
		}
		die();
	}

	function CreaStatistiche($IdAtto,$Oggetto){
		$righeVisiteAtto=ap_get_Stat_Visite($IdAtto);
		$righeVisiteDownload=ap_get_Stat_Download($IdAtto);
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
		$parametri_page=add_submenu_page( 'Albo_Pretorio', 'Generale', 'Parametri', 'admin_albo', 'config', array( 'AlboPretorio','show_menu'));
		$permessi=add_submenu_page( 'Albo_Pretorio', 'Permessi', 'Permessi', 'admin_albo', 'permessi', array('AlboPretorio', 'show_menu'));
		$utility=add_submenu_page( 'Albo_Pretorio', 'Utility', 'Utility', 'admin_albo', 'utility', array('AlboPretorio', 'show_menu'));		
		add_action( 'admin_head-'. $atti_page, array( 'AlboPretorio','ap_head' ));
}
	
	function show_menu() {
		global $AP_OnLine;

		switch ($_REQUEST['page']){
			case "Albo_Pretorio" :
				$AP_OnLine->AP_menu();
				break;
			case "config" :
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
			case "permessi":
			// interfaccia per la gestione dei permessi
				include_once ( dirname (__FILE__) . '/admin/permessi.php' );
				break;
			case "utility":
			// interfaccia per la gestione dei permessi
				include_once ( dirname (__FILE__) . '/admin/utility.php' );
				break;
		}
	}
	
	function init() {
		if (is_admin()) return;
		wp_enqueue_script('jquery');

	}

################################################################################
// ADMIN HEADER
################################################################################

	function ap_head() {
		global $wp_db_version, $wp_dlm_root;
		?>
<script language="JavaScript">
	function change(html){
		description.innerHTML=html
	}
</script>
<script type="text/javascript">
		jQuery.noConflict();
		(function($) {
			$(function() {		 
		  	$('input.infolog').click(function() { //start function when Random button is clicked
				var tipo;
				switch ($(this).attr('value')){
					case "Atti":
						tipo=1;
						break;
					case "Allegati":
						tipo=3;
						break;
					case "Statistiche Visite":
						tipo=5;
						break;
					case "Statistiche Download":
						tipo=6;
						break;
				}
				$.ajax({
					type: "post",url: "admin-ajax.php",data: { action: 'logdati', Tipo: tipo, IdOggetto: '<?php echo $_REQUEST['id']; ?>', IdAtto: '<?php echo $IdAtto; ?>'},
					beforeSend: function() {
					 $("#DatiLog").html('');
					 $("#loading").fadeIn('fast');}, 
					success: function(html){
						$("#loading").fadeOut('fast');
						$("#DatiLog").html(html); 
					}
				}); //close jQuery.ajax
				return false;
			})
		 });
		})(jQuery);
</script>	
	<?php
	}

	function head_Front_End() {
		global $wp_query;
		$postObj=$wp_query->get_queried_object();
		if (strpos(strtoupper($postObj->post_content),"[ALBO STATO=")!== false){
			echo "
	<!--HEAD Albo Preotrio On line -->
	";
			if(get_option(blog_public)==1)
				echo "	<meta name='robots' content='noindex, nofollow, noarchive' />
	<!--HEAD Albo Preotrio On line -->
			";
			else
				echo "	<meta name='robots' content='noarchive' />
			<!--HEAD Albo Preotrio On line -->
			";
		wp_enqueue_script( 'jquery-ui-datepicker', '', array('jquery'));
		//wp_enqueue_style( 'jquery.ui.theme', plugins_url( 'css/jquery-ui-custom.css', __FILE__ ) );
	    wp_enqueue_script( 'my-admin-dtpicker-it', plugins_url('js/jquery.ui.datepicker-it.js', __FILE__ ));

//		wp_enqueue_script( 'Albo-Public', plugins_url('js/Albo.public.js', __FILE__ ));

		wp_enqueue_script( 'AlboP-DataTable', plugins_url('js/jquery.dataTables.js', __FILE__ ), array('jquery' ));
		wp_enqueue_script( 'AlboP-DataTable-Tools', plugins_url('js/dataTables.tableTools.js', __FILE__ ), array('jquery' ));

	    wp_register_style('AlboP_datatable_style' ,plugins_url( 'css/jquery.dataTables.css', __FILE__ ));
	    wp_enqueue_style('AlboP_datatable_style');   

	    wp_register_style('AlboP_datatable_theme_Tools' ,plugins_url( 'css/dataTables.tableTools.css', __FILE__ ));
	    wp_enqueue_style('AlboP_datatable_theme_Tools');   

/*	<script type='text/javascript' src='<?php echo plugins_url('js/jquery.dataTables.js', __FILE__ );?>'></script>
	<script type='text/javascript' src='<?php echo plugins_url('js/dataTables.tableTools.js', __FILE__ );?>'></script>
	<link rel="stylesheet" href="<?php echo Albo_URL.'css/jquery.dataTables.css';?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo Albo_URL.'css/dataTables.tableTools.css';?>" type="text/css" media="screen" />
*/


?>	
	<script type='text/javascript'>
		jQuery.noConflict();
		(function($) {
			$(function() {
				$('#paginazione').change(function(){
					location.href=$(this).attr('rel')+$("#paginazione option:selected" ).text();				});
				$('#Calendario1').datepicker();
				$('#Calendario2').datepicker();
				$('a.addstatdw').click(function() {
					 var link=$(this).attr('rel');
						jQuery.ajax({type: 'get',url: $(this).attr('rel')}); //close jQuery.ajax
					return true;		 
					});
			    var l = window.location;
				var url = l.protocol + "//" + l.host +"/"+  l.pathname.split('/')[1]+"/wp-content/plugins/albo-pretorio-on-line/swf/copy_csv_xls_pdf.swf";
				$('#elenco-atti').dataTable(         
			   	{
			   	"dom": 'lT<f>rtip',
			   	"ordering": false,
			   	"tableTools": {
		         	"sSwfPath": url,
		          	"aButtons": [ 
		          		{
							"sExtends": "copy",
							"sButtonText": "Copia negli Appunti"
						},
		          		{
							"sExtends": "print",
							"sButtonText": "Stampa"
						},
						{
		                    "sExtends":    "collection",
		                    "sButtonText": "Salva",
		                    "aButtons":    [ "csv", "xls",                 
		                    {
		                    	"sExtends": "pdf",
		                    	"sPdfOrientation": "landscape",
		                    	"sPdfMessage": "Tabella generata con il plugin Gestione Circolari."
		                	},]
		                }
					]
		         },
			   	"language":{
				    "sEmptyTable":     "Nessun dato presente nella tabella",
				    "sInfo":           "Vista da _START_ a _END_<br />di _TOTAL_ elementi",
				    "sInfoEmpty":      "Vista da 0 a 0 di 0 elementi",
				    "sInfoFiltered":   "(filtrati da _MAX_ elementi totali)",
				    "sInfoPostFix":    "",
				    "sInfoThousands":  ",",
				    "sLengthMenu":     "Visualizza _MENU_ elementi",
				    "sLoadingRecords": "Caricamento...",
				    "sProcessing":     "Elaborazione...",
				    "sSearch":         "Cerca:",
				    "sZeroRecords":    "La ricerca non ha portato alcun risultato.",
				    "oPaginate": {
				        "sFirst":      "Inizio",
				        "sPrevious":   "Precedente",
				        "sNext":       "Successivo",
				        "sLast":       "Fine"
				    },
				    "oAria": {
				        "sSortAscending":  ": attiva per ordinare la colonna in ordine crescente",
				        "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
				    }
				}
			   });
		});
	})(jQuery);
</script>     
<!--FINE HEAD Albo Preotrio On line -->
<?php		
		}
	}
	
	function load_dependencies() {
			// Load backend libraries
			if ( is_admin() ) {	
				require_once (dirname (__FILE__) . '/admin/admin.php');
			}	
		}
	
	function VisualizzaAtti($Parametri){
	extract(shortcode_atts(array(
		'Stato' => '1',
		'Per_Page' => '20',
		'Cat' => 'All',
		'Filtri' => 'si',
		'MinFiltri' =>'si'
	), $Parametri));
	require_once ( dirname (__FILE__) . '/admin/frontend.php' );
//		return "Albo Pretorio".$Correnti." ".$Paginazione." ".$per_page." ".$default_order;
return $ret;
	}

	function AP_menu(){
	global $wpdb;
	  
	  if ($_REQUEST['action']=="setta-anno"){
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
    
	echo '<div class="wrap">
		<img src="'.Albo_URL.'/img/albo32.png" alt="Icona Atti" style="display:inline;float:left;margin-top:5px;"/>
			<h2 style="margin-left:40px;">Albo Pretorio</h2>
		<div class="postbox-container" style="width:80%;margin-top:20px;">
		<h3>Sommario</h3>
	<div class="widefat">
		<table>
			<caption>Statistiche</caption>
			<thead>
				<tr>
					<th>Oggetto</th>
					<th>N.</th>
					<th>In Attesa di Pubblicazione</th>
					<th>Attivi</th>
					<th>Scaduti</th>
				</tr>
			</thead>
			<tbody>
				<tr class="first">
					<td style="text-align:left;width:200px;" >Atti</td>
					<td style="text-align:left;width:200px;">'.$n_atti.'</td>
					<td style="text-align:left;width:200px;">'.$n_atti_dapub.'</td>
					<td style="text-align:left;width:200px;">'.$n_atti_attivi.'</td>
					<td style="text-align:left;width:200px;">'.$n_atti_storico.'</td>
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
	';
	if ($this->version==3.0 and !is_file(AP_BASE_DIR.get_option('opt_AP_FolderUpload')."/.htaccess")){
	echo'<p> </p>
		<div class="widefat" >
			<p style="text-align:center;font-size:1.2em;font-weight: bold;color: red;">Questa versione dell plugin implementa il diritto all\'oblio, questo meccanismo permette agli utenti di accedere agli allegati degli atti pubblicati all\'albo pretorio solo dal sito che ospita l\'albo e non con link diretti al file<br />Non risulta ancora attivato il diritto all\'oblio,<br /><a href="?page=utility&amp;action=oblio">Attivalo</a></p>
			</div>';
	}
if (ap_get_num_categorie()==0){
echo'<p> </p>
		<div class="widefat" >
				<p style="text-align:center;font-size:1.2em;font-weight: bold;color: green;">
				Non risultano categorie codificate, se vuoi posso impostare le categorie di default &ensp;&ensp;<a href="?page=utility&amp;action=creacategorie">Crea Categorie di Default</a></p>
			</div>';
}
if (ap_num_responsabili()==0){
echo'<p> </p>
		<div class="widefat" >
				<p style="text-align:center;font-size:1.2em;font-weight: bold;color: green;">
				Non risultano <strong>Responsabili</strong> codificati, devi crearne almeno uno prima di iniziare a codificare gli Atti &ensp;&ensp;<a href="?page=responsabili">Crea Responsabile</a></p>
			</div>';
}
if(get_option('opt_AP_AnnoProgressivo')!=date("Y")){
	echo '<div style="border: medium groove Blue;margin-top:10px;">
			<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
				<form id="agg_anno_progressivo" method="post" action="?page=config">
					<input type="hidden" name="action" value="setta-anno" />
				<input type="submit" name="submit" id="submit" class="button" value="Aggiorna Anno Albo ed Azzera numero Progressivo"  />
				</form>
			</div>
		 </div>';
}
echo '<div  style="margin-left: 10px;">
			<h3>Informazioni generali</h3>
			<p style="margin-left:10px;font-size:1.2em;">Versione Plugin: '.$this->version.'</p>
			<h3 style="margin-top: 5px;">A proposito ...</h3>
			<ul style="margin-left: 25px;list-style-type: circle;">
				<li>Plugin realizzato da Scimone Ignazio, per avere informazioni e supporto potete visitare il sito ufficiale del plugin <a href="http://plugin.sisviluppo.info">http://plugin.sisviluppo.info</a></li>
				<li>Documentazione del plugin <a href="http://plugin.sisviluppo.info/wp-content/uploads/2014/02/Albo-Pretorio-On-line.pdf">Manuale Albo Pretorio</a></li>
				<li><strong>In caso di problemi contattatemi all\'indirizzo</strong> <a href="mailto://ignazios@gmail.com">ignazios@gmail.com</a></li>
			</ul>
			<h3 style="margin-bottom: 5px;">Ti piace il Plugin?</h3>
			<ul  style="margin-left: 25px;list-style-type: circle;">
				<li>Facci sapere che utilizzi il plugin <strong>Albo Pretorio On Line</strong>, compila il form a questo indirizzo <a href="http://www.sisviluppo.info/?page_id=315">Io utilizzo il plugin</a></li>
				<li><img src="'.Albo_URL.'/img/star.png" alt="Stella dorata" style="display:inline;float:left;margin-top:-5px;margin-right:5px;"/><a href="http://wordpress.org/extend/plugins/albo-pretorio-on-line/">Vota questo plugin sul sito WordPress.org</a> richiede la registrazione sul sito http://www.wordpress.org</li>
			</ul>
			</div>  
		';
	}	

	function AP_config(){
	   global $current_user,$stato;
	  if ($_REQUEST['action']=="setta-anno"){
		update_option('opt_AP_AnnoProgressivo',date("Y") );
		update_option('opt_AP_NumeroProgressivo',1 );
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
	  }
	  get_currentuserinfo();
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
	  	<img src="'.Albo_URL.'/img/opzioni32.png" alt="Icona configurazione" style="display:inline;float:left;margin-top:10px;"/>
	  	<h2 style="margin-left:40px;">AlboPretorio Configurazione</h2>
	  '.$stato.'
	  <form name="AlboPretorio_cnf" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
	  <input type="hidden" name="c_AnnoProgressivo" value="'.$nanno.'"/>
	  <table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="nomeente">Nome Ente</label></th>
			<td><input type="text" name="c_Ente" value="'.$ente.'" size="100" id="nomeente"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="visente">Visualizza Nome Ente</label></th>
			<td><input type="checkbox" name="c_VEnte" value="Si" '.$ve_selezionato.' id="visente"/></td>
		</tr>
		<tr valign="top">
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
		<tr valign="top">
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
		<tr valign="top">
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
		<tr valign="top">
			<th scope="row">Numero Progressivo</th>
			<td><strong> '.$nprog. ' / '.$nanno.'</strong></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="dirUpload">Cartella Upload</label></th>
			<td><strong> '.AP_BASE_DIR.get_option('opt_AP_FolderUpload').'</strong></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size:1.2em;"><strong>Colori di sfondo delle righe della Tabella Elenco Atti</strong></td>
		<tr>
		<tr>
			<th scope="row"><label for="color">Righe Atti Annullati</label></th>
			<td> 
				<input type="text" id="color" name="color" value="'.$colAnnullati.'" size="5"/>
			</td>
		</tr>
			<th scope="row"><label for="colorpari">Righe Pari</label></th>
			<td> 
				<input type="text" id="colorp" name="colorp" value="'.$colPari.'" size="5"/>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="colordispari">Righe Dispari</label></th>
			<td> 
				<input type="text" id="colord" name="colord" value="'.$colDispari.'" size="5"/>
			</td>
		</tr>
		</table>
	    <p class="submit">
	        <input type="submit" name="AlboPretorio_submit_button" value="Salva Modifiche" />
	    </p> 
   
	    </form>
	    </div>';
		if(get_option('opt_AP_AnnoProgressivo')!=date("Y")){
			echo '<div style="border: medium groove Blue;margin-top:10px;margin-right:250px;">
					<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
						<form id="agg_anno_progressivo" method="post" action="?page=config">
						<input type="hidden" name="action" value="setta-anno" />
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
	
	if (file_exists(Albo_DIR."/js/gencode.php")){
		chmod(Albo_DIR."/js/gencode.php", 0755);
	}
		$role =& get_role( 'administrator' );

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

/**
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
		if (!ap_existFieldInTable($wpdb->table_name_Atti, "DataAnnullamento")){
			ap_AggiungiCampoTabella($wpdb->table_name_Atti, "DataAnnullamento", " date default '0000-00-00'");
		}
		if (!ap_existFieldInTable($wpdb->table_name_Atti, "MotivoAnnullamento")){
			ap_AggiungiCampoTabella($wpdb->table_name_Atti, "MotivoAnnullamento", " varchar(100) default ''");
		}
		if (!ap_existFieldInTable($wpdb->table_name_Atti, "Ente")){
			ap_AggiungiCampoTabella($wpdb->table_name_Atti, "Ente", " INT NOT NULL default 0");
		}
		if (strtolower(ap_typeFieldInTable($wpdb->table_name_Atti,"Riferimento"))=="varchar(20)"){
			ap_ModificaTipoCampo($wpdb->table_name_Atti, "Riferimento", "varchar(100)");
		}
		if (strtolower(ap_typeFieldInTable($wpdb->table_name_Atti,"Oggetto"))=="varchar(150)"){
			ap_ModificaTipoCampo($wpdb->table_name_Atti, "Oggetto", "varchar(200)");
		}
		if (strtolower(ap_typeFieldInTable($wpdb->table_name_Atti,"MotivoAnnullamento"))=="varchar(100)"){
			ap_ModificaTipoCampo($wpdb->table_name_Atti, "MotivoAnnullamento", "varchar(200)");
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
	}

	function update_AlboPretorio_settings(){
	    if($_POST['AlboPretorio_submit_button'] == 'Salva Modifiche'){
		    ap_set_ente_me($_POST['c_Ente']);
			if ($_POST['c_VEnte']=='Si')
			    update_option('opt_AP_VisualizzaEnte','Si' );
			else
				update_option('opt_AP_VisualizzaEnte','No' );
		    update_option('opt_AP_AnnoProgressivo',$_POST['c_AnnoProgressivo'] );
		    update_option('opt_AP_EffettiTesto',$_POST['c_TE'] );
		    update_option('opt_AP_LivelloTitoloPagina',$_POST['c_LTP'] );
		    update_option('opt_AP_LivelloTitoloFiltri',$_POST['c_LTF'] );
			update_option('opt_AP_ColoreAnnullati',$_POST['color'] );
			update_option('opt_AP_ColorePari',$_POST['colorp'] );
			update_option('opt_AP_ColoreDispari',$_POST['colord'] );
			header('Location: '.get_bloginfo('wpurl').'/wp-admin/admin.php?page=config&update=true'); 
  		}
	}

	function albo_styles() {
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
	
	function InserisciAlboPretorio($Stato=1,$Per_Page=10,$Cat=0){
	 global $AP_OnLine;
	 $Parametri=array("Stato" => $Stato,
                  "Per_Page" => $Per_Page,
				  "Cat" => $Cat);
/*	require_once ( dirname (__FILE__) . '/admin/frontend.php' );
	echo $ret;
*/
	echo $AP_OnLine->VisualizzaAtti($Parametri);
	
}
}
?>