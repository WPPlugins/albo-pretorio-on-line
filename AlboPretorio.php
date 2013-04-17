<?php
/*
Plugin Name:Albo Pretorio On line
Plugin URI: http://www.sisviluppo.info
Description: Plugin utilizzato per la pubblicazione degli atti da inserire nell'albo pretorio dell'ente.
Version:2.6
Author: Scimone Ignazio
Author URI: http://www.sisviluppo.info
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
$uploads = wp_upload_dir(); 
define("AP_BASE_DIR",substr($uploads['basedir'],0,strpos($uploads['basedir'],"wp-content", 0)));

if (!class_exists('AlboPretorio')) {
 class AlboPretorio {
	
	var $version     = '2.2';
	var $minium_WP   = '3.1';
	var $options     = '';

	function AlboPretorio() {
		// Inizializzazioni
		$this->define_tables();
		$this->load_dependencies();
		$this->plugin_name = plugin_basename(__FILE__);
		
		// Hook per attivazione/disattivazione pluggin
		register_activation_hook( $this->plugin_name, array('AlboPretorio', 'activate'));
		register_deactivation_hook( $this->plugin_name, array('AlboPretorio', 'deactivate') );	

		// Hook disinstallazione
		register_uninstall_hook( $this->plugin_name, array('AlboPretorio', 'uninstall') );

		// Hook di inizializzazione che registra il punto di avvio del pluggin
		add_action('init', array('AlboPretorio', 'update_AlboPretorio_settings'));
		add_action('init', array('AlboPretorio', 'init') );
		add_action('init', array('AlboPretorio', 'ilc_farbtastic_script'));
		if (!is_admin()) 
			if (!function_exists('albo_styles'))
				add_action('wp_print_styles', array('AlboPretorio','albo_styles'));
		
		add_shortcode('Albo', array('AlboPretorio', 'VisualizzaAtti'));
		add_action('wp_head', array('AlboPretorio','head_Front_End'));
		add_action( 'admin_menu', array (&$this, 'add_menu') ); 
		add_action( 'wp_ajax_logdati', array('AlboPretorio','get_crealog'));
		$role =& get_role( 'amministratore_albo' );
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

	function ilc_farbtastic_script() {
 		wp_enqueue_style( 'farbtastic' );
  		wp_enqueue_script( 'farbtastic' );
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
							<td >'.VisualizzaData($riga->Data).'</td>
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
							<td >'.VisualizzaData($riga->Data).'</td>
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
						<td >'.VisualizzaData($riga->Data)." ".VisualizzaOra($riga->Data).'</th>
						<td >'.$Operazione.'</th>
						<td >'.stripslashes($riga->Operazione).'</td>
					</tr>';
		}
		$HtmlTesto.= '    </tbody>
				</table>';
		return $HtmlTesto;	
	}

		static function add_menu(){
  		add_menu_page('Panoramica', 'Albo Pretorio', 'gest_atti_albo', 'Albo_Pretorio',array( 'AlboPretorio','show_menu'));
		$atti_page=add_submenu_page( 'Albo_Pretorio', 'Atti', 'Atti', 'gest_atti_albo', 'atti', array( 'AlboPretorio','show_menu'));
		$categorie_page=add_submenu_page( 'Albo_Pretorio', 'Categorie', 'Categorie', 'gest_atti_albo', 'categorie', array( 'AlboPretorio', 'show_menu'));
		$responsabili_page=add_submenu_page( 'Albo_Pretorio', 'Responsabili', 'Responsabili', 'admin_albo', 'responsabili', array( 'AlboPretorio','show_menu'));
		$enti=add_submenu_page( 'Albo_Pretorio', 'Enti', 'Enti', 'admin_albo', 'enti', array('AlboPretorio', 'show_menu'));
		$parametri_page=add_submenu_page( 'Albo_Pretorio', 'Generale', 'Parametri', 'admin_albo', 'config', array( 'AlboPretorio','show_menu'));
		$css_page=add_submenu_page( 'Albo_Pretorio', 'Css', 'Css', 'admin_albo', 'editorcss',array( 'AlboPretorio', 'show_menu'));
		$permessi=add_submenu_page( 'Albo_Pretorio', 'Permessi', 'Permessi', 'admin_albo', 'permessi', array('AlboPretorio', 'show_menu'));
		$utility=add_submenu_page( 'Albo_Pretorio', 'Utility', 'Utility', 'admin_albo', 'utility', array('AlboPretorio', 'show_menu'));		
		add_action( 'admin_head-'. $atti_page, array( 'AlboPretorio','ap_head' ));
		add_action( 'admin_head-'. $categorie_page, array( 'AlboPretorio','ap_head'));
		add_action( 'admin_head-'. $responsabili_page, array( 'AlboPretorio','ap_head'));
		add_action( 'admin_head-'. $enti, array( 'AlboPretorio','ap_head'));
		add_action( 'admin_head-'. $parametri_page,array( 'AlboPretorio', 'ap_head'));
		add_action( 'admin_head-'. $css_page, array( 'AlboPretorio','ap_head'));
		add_action( 'admin_head-'. $permessi, array( 'AlboPretorio','ap_head'));
		add_action( 'admin_head-'. $utility, array( 'AlboPretorio','ap_head'));
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
			case "editorcss":
			// interfaccia per la gestione dell'editor dei CSS
				include_once ( dirname (__FILE__) . '/admin/editor.php' );
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
		<link rel="stylesheet" type="text/css" href="<?php echo Albo_URL; ?>css/epoch_styles.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo Albo_URL; ?>css/styles.css" />
		<script type="text/javascript" src="<?php echo Albo_URL; ?>js/epoch_classes.js"></script>
		<script type="text/javascript">
			var Cal1, Cal2, Cal3; 
			window.onload = function() {
				Cal1 = new Epoch('cal1','popup',document.getElementById('Calendario1'),false);
				Cal2 = new Epoch('cal2','popup',document.getElementById('Calendario2'),false);
				Cal3 = new Epoch('cal3','popup',document.getElementById('Calendario3'),false);
			};
		</script>
		<script language="JavaScript">
			function change(html){
				description.innerHTML=html
			}
		</script>
		
		<script type="text/javascript">
		/* <![CDATA[ */
			jQuery.noConflict();
			(function($) {
			
				$(function() {
					$('a.ripubblica').click(function(){
						var answer = confirm("Confermi la ripubblicazione dei " + $(this).attr('rel') + ' atti in corso di validita?')
						if (answer){
							return true;
						}
						else{
							return false;
						}					
					});
					$('a.annullaatto').click(function(){
						var answer = confirm("Confermi l'annullamento dell'atto `" + $(this).attr('rel') + '` ?\nATTENZIONE L\'OPERAZIONE E\' IRREVERSIBILE!!!!!')
						if (answer){
							var Testoannullamento;
							Testoannullamento=prompt("Motivo Annullamento Atto "+ $(this).attr('rel'),"Atto Annullato per");				
							location.href=$(this).attr('href')+"&motivo="+Testoannullamento;
							return false;
						}
						else{
							return false;
						}					
					});
					$('a.dc').click(function(){
						var answer = confirm("Confermi la cancellazione della Categoria `" + $(this).attr('rel') + '` ?')
						if (answer){
							return true;
						}
						else{
							return false;
						}					
					});
	
					$('a.dr').click(function(){
						var answer = confirm("Confermi la cancellazione del Responsabile del Trattamento `" + $(this).attr('rel') + '` ?')
						if (answer){
							return true;
						}
						else{
							return false;
						}					
					});
	
					$('a.da').click(function(){
						var answer = confirm("Confermi la cancellazione del\'Allegato `" + $(this).attr('rel') + '` ?\n\nATTENZIONE questa operazione cancellera\' anche il file sul server!\n\nSei sicuro di voler CANCELLARE l\'allegato?')
						if (answer){
							return true;
						}
						else{
							return false;
						}					
					});
					$('a.ac').click(function(){
						var answer = confirm("Confermi la cancellazione dell' Atto: `" + $(this).attr('rel') + '` ?')
						if (answer){
							return true;
						}
						else{
							return false;
						}					
					});
					$('a.ap').click(function(){
						var answer = confirm("approvazione Atto: `" + $(this).attr('rel') + '`\nAttenzione la Data Pubblicazione verra` impostata ad oggi ?')
						if (answer){
							return true;
						}
						else{
							return false;
						}					
					});
					$('input.update').click(function(){
						var answer = confirm("confermi la modifica della Categoria " + $(this).attr('rel') + '?')
						if (answer){
							return true;
						}
						else{
							return false;
						}					
					});
					$('a.addstatdw').click(function() {
					 var link=$(this).attr('rel');
					 $.get(link,function(data){
     					$('#DatiLog').html(data);
						}, "json");
					});
					
				});
			
			})(jQuery);
	
		/* ]]> */
		</script>
		<script type="text/javascript">
		 
		  jQuery(document).ready(function() {
		  
		  	jQuery('input.infolog').click(function() { //start function when Random button is clicked
		var tipo;
		switch (jQuery(this).attr('value')){
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
		jQuery.ajax({
			type: "post",url: "admin-ajax.php",data: { action: 'logdati', Tipo: tipo, IdOggetto: '<?php echo $_REQUEST['id']; ?>', IdAtto: '<?php echo $IdAtto; ?>'},
			beforeSend: function() {
			 jQuery("#DatiLog").html('');
			 jQuery("#loading").fadeIn('fast');}, 
			success: function(html){
				jQuery("#loading").fadeOut('fast');
				jQuery("#DatiLog").html(html); 
			}
		}); //close jQuery.ajax
		return false;
	})

		    jQuery('#ilctabscolorpicker').hide();
		    jQuery('#ilctabscolorpicker').farbtastic("#color");
		    jQuery("#color").click(function(){jQuery('#ilctabscolorpicker').slideToggle()});
		  
		    jQuery('#ilctabscolorpickerp').hide();
		    jQuery('#ilctabscolorpickerp').farbtastic("#colorp");
		    jQuery("#colorp").click(function(){jQuery('#ilctabscolorpickerp').slideToggle()});
		  
		    jQuery('#ilctabscolorpickerd').hide();
		    jQuery('#ilctabscolorpickerd').farbtastic("#colord");
		    jQuery("#colord").click(function(){jQuery('#ilctabscolorpickerd').slideToggle()});
		  });
		 
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
				echo " 	
	<script type='text/javascript' src='".Albo_URL."js/epoch_classes.js'></script>
	<script type='text/javascript'>
		var Cal1, Cal2; 
		window.onload = function() {
			Cal1 = new Epoch('cal1','popup',document.getElementById('Calendario1'),false);
			Cal2 = new Epoch('cal2','popup',document.getElementById('Calendario2'),false);
		};
	</script>
	
	<script type='text/javascript'>
		jQuery.noConflict();
		(function($) {
			$(function() {
					$('a.addstatdw').click(function() {
					 var link=$(this).attr('rel');
						jQuery.ajax({type: 'get',url: $(this).attr('rel')}); //close jQuery.ajax
					return true;		 
					});
			});
		})(jQuery);
	</script>
<!--FINE HEAD Albo Preotrio On line -->
";
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
		'Per_Page' => '20'
	), $Parametri));
	require_once ( dirname (__FILE__) . '/admin/frontend.php' );
//		return "Albo Pretorio".$Correnti." ".$Paginazione." ".$per_page." ".$default_order;
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
	echo '<div class="wrap">
			<p>Plugin realizzato da Scimone Ignazio, per avere informazioni e supporto potete visitare il sito ufficiale del plugin <a href="http://www.sisviluppo.info">http://www.sisviluppo.info</a>
			</p>
			<p>Documentazione del plugin <a href="'.Albo_URL.'documenti/Albo_Pretorio_On_line.pdf">Manuale Albo Pretorio</a></p>
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
	  $efftext=get_option('opt_AP_EffettiTesto');
	  $effcss3=get_option('opt_AP_EffettiCSS3');
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
	  if ($efftext=="Si")
	  	$ve_efftext='checked="checked"';
	  else
	  	$ve_efftext='';
	  if ($effcss3=="Si")
	  	$ve_effcss3='checked="checked"';
	  else
	  	$ve_effcss3='';
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
			<th scope="row"><label for="effShadow">Utilizza effetti testo Shadow</label></th>
			<td><input type="checkbox" name="c_TE" value="Si" '.$ve_efftext.' id="effShadow"/></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="effCSS3">Utilizza effetti CSS3</label></th>
			<td><input type="checkbox" name="c_CSS3" value="Si" '.$ve_effcss3.'  id="effCSS3"/></td>
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
			<td><input type="text" name="c_dirUpload" value="'.$dirUpload.'" size="80" id="dirUpload"/><input type="button" value="Valore di Default" onclick="this.form.c_dirUpload.value=\'wp-content/uploads\'"><br />inserire una cartella valida partendo da <strong>'.AP_BASE_DIR.'</strong></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size:1.2em;"><strong>Colori di sfondo delle righe della Tabella Elenco Atti</strong></td>
		<tr>
		<tr>
			<th scope="row"><label for="color">Righe Atti Annullati</label></th>
			<td> <input type="text" id="color" name="color" value="'.$colAnnullati.'" size="5"/>
			    <div id="ilctabscolorpicker"></div>
			</td>
		</tr>
			<th scope="row"><label for="colorpari">Righe Pari</label></th>
			<td> <input type="text" id="colorp" name="colorp" value="'.$colPari.'" size="5"/>
			    <div id="ilctabscolorpickerp"></div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="colordispari">Righe Dispari</label></th>
			<td> <input type="text" id="colord" name="colord" value="'.$colDispari.'" size="5"/>
			    <div id="ilctabscolorpickerd"></div>
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
			add_option('opt_AP_FolderUpload', 'wp-content/uploads');
		}
		if(get_option('opt_AP_VisualizzaEnte') == '' || !get_option('opt_AP_VisualizzaEnte')){
			add_option('opt_AP_VisualizzaEnte', 'Si');
		}
		if(get_option('opt_AP_EffettiTesto') == '' || !get_option('opt_AP_EffettiTesto')){
			add_option('opt_AP_EffettiTesto', 'No');
		}
		if(get_option('opt_AP_EffettiCSS3') == '' || !get_option('opt_AP_EffettiCSS3')){
			add_option('opt_AP_EffettiCSS3', 'No');
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

		$sql_Atti = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Atti." (
		  `IdAtto` int(11) NOT NULL auto_increment,
		  `Numero` int(4) NOT NULL default 0,
		  `Anno` int(4) NOT NULL default 0,
		  `Data` date NOT NULL default '0000-00-00',
		  `Riferimento` varchar(20) NOT NULL,
		  `Oggetto` varchar(150) NOT NULL default '',
		  `DataInizio` date NOT NULL default '0000-00-00',
		  `DataFine` date default '0000-00-00',
		  `Informazioni` varchar(255) NOT NULL default '',
		  `IdCategoria` int(11) NOT NULL default 0,
		  PRIMARY KEY  (`IdAtto`));";

		$sql_Allegati = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Allegati." (
		  `IdAllegato` int(11) NOT NULL auto_increment,
		  `TitoloAllegato` varchar(255) NOT NULL default '',
		  `Allegato` varchar(255) NOT NULL default '',
		  `IdAtto` int(11) NOT NULL default 0,
		  PRIMARY KEY  (`IdAllegato`));";
		
		$sql_Categorie = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Categorie." (
		  `IdCategoria` int(11) NOT NULL auto_increment,
		  `Nome` varchar(255) NOT NULL default '',
		  `Descrizione` varchar(255) NOT NULL default '',
		  `Genitore` int(11) NOT NULL default 0,
		  `Giorni` smallint(3) NOT NULL DEFAULT '0',
		  PRIMARY KEY  (`IdCategoria`));";

		$sql_Log = "CREATE TABLE  IF NOT EXISTS ".$wpdb->table_name_Log." (
  		  `Data` timestamp NOT NULL default CURRENT_TIMESTAMP,
  		  `Utente` varchar(60) NOT NULL default '',
          `IPAddress` varchar(16) NOT NULL default '',
          `Oggetto` int(1) NOT NULL default 1,
          `IdOggetto` int(11) NOT NULL default 1,
          `IdAtto` int(11) NOT NULL default 0,
          `TipoOperazione` int(1) NOT NULL default 1,
          `Operazione` text);";
 
 		$sql_RespProc = "CREATE TABLE  IF NOT EXISTS ".$wpdb->table_name_RespProc." (
  		  `IdResponsabile` int(11) NOT NULL auto_increment,
  		  `Cognome` varchar(20) NOT NULL default '',
          `Nome` varchar(20) NOT NULL default '',
          `Email` varchar(100) NOT NULL default '',
          `Telefono` varchar(30) NOT NULL default '',
          `Orario` varchar(60) NOT NULL default '',
          `Note` text,
		   PRIMARY KEY  (`IdResponsabile`));";   
		  
 		$sql_Enti = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Enti." (
		  `IdEnte` int(11) NOT NULL auto_increment,
		  `Nome` varchar(100) NOT NULL,
		  `Indirizzo` varchar(150) NOT NULL default '',
		  `Url` varchar(100) NOT NULL default '',
		  `Email` varchar(100) NOT NULL default '',
		  `Pec` varchar(100) NOT NULL default '',
		  `Telefono` varchar(40) NOT NULL default '',
		  `Fax` varchar(40) NOT NULL default '',
          `Note` text,
		  PRIMARY KEY  (`Idente`));";
		  
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		     dbDelta($sql_Atti);
		     dbDelta($sql_Allegati);
		     dbDelta($sql_Categorie);
		     dbDelta($sql_Log);
		     dbDelta($sql_RespProc);
		     dbDelta($sql_Enti);
		     
		if(ap_get_ente_me() == '' || !ap_get_ente_me()){
			ap_create_ente_me();
		}
		     
/*************************************************************************************
** Area riservata per l'aggiunta di nuovi campi in una delle tabelle dell' albo ******
*************************************************************************************/
          
	
	
	if (!existFieldInTable($wpdb->table_name_Atti, "RespProc")){
			if (!($ret=AddFiledTable($wpdb->table_name_Atti, "RespProc", " INT NOT NULL")))
				echo $ret;
		}
		if (!existFieldInTable($wpdb->table_name_Atti, "DataAnnullamento")){
			if (!($ret=AddFiledTable($wpdb->table_name_Atti, "DataAnnullamento", " date default '0000-00-00'")))
				echo $ret;
		}
		if (!existFieldInTable($wpdb->table_name_Atti, "MotivoAnnullamento")){
			if (!($ret=AddFiledTable($wpdb->table_name_Atti, "MotivoAnnullamento", " varchar(100) default ''")))
				echo $ret;
		}
		if (!existFieldInTable($wpdb->table_name_Atti, "Ente")){
			if (!($ret=AddFiledTable($wpdb->table_name_Atti, "Ente", " INT NOT NULL default 0"))){
				
				echo $ret;
			}
		}

			
	}  	 
	static function deactivate() {
		
	remove_shortcode('Albo');
	
	}
	static function uninstall() {
		global $wpdb;
		
		// Eliminazioni ruoli
        
        //Amministratore
		$role =& get_role( 'administrator' );
		if ( !empty( $role ) ) {
        	$role->remove_cap( 'admin_albo' );
            $role->remove_cap( 'gest_atti_albo' );
        }

        /* Array dei ruoli da eliminare */
        $roles_to_delete = array(
            'admin_albo',
            'gest_atti_albo');

        /* Ciclo che elimina i ruoli solo se non ci sono utenti assegnati a quel ruolo, altrimenti non disinstalla */
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
//		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Log);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_RespProc);
		
		// Eliminazioni Opzioni
		delete_option( 'opt_AP_Ente' );
		delete_option( 'opt_AP_NumeroProgressivo' );
		delete_option( 'opt_AP_AnnoProgressivo' );
		delete_option( 'opt_AP_NumeroProtocollo' );
		delete_option( 'opt_AP_EffettiTesto' );
		delete_option( 'opt_AP_EffettiCSS3' );
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
		    //update_option('opt_AP_NumeroProgressivo',$_POST['c_NumeroProgressivo'] );
		    update_option('opt_AP_EffettiTesto',$_POST['c_TE'] );
		    update_option('opt_AP_EffettiCSS3',$_POST['c_CSS3'] );
		    update_option('opt_AP_LivelloTitoloEnte',$_POST['c_LTE'] );
		    update_option('opt_AP_LivelloTitoloPagina',$_POST['c_LTP'] );
		    update_option('opt_AP_LivelloTitoloFiltri',$_POST['c_LTF'] );
		    update_option('opt_AP_FolderUpload',$_POST['c_dirUpload'] );
		    if(!is_dir(AP_BASE_DIR.$_POST['c_dirUpload']))   
				mkdir(AP_BASE_DIR.$_POST['c_dirUpload'], 0777);
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
        if (get_option('opt_AP_EffettiTesto')=="Si"){
			$myStyleUrl = plugins_url('css/styleTE.css', __FILE__); 
	        $myStyleFile = Albo_DIR.'/css/styleTE.css';
	        if ( file_exists($myStyleFile) ) {
	            wp_register_style('AlboPretorioTextEffect', $myStyleUrl);
	            wp_enqueue_style( 'AlboPretorioTextEffect');
	        }	
		}
        if (get_option('opt_AP_EffettiCSS3')=="Si"){
			$myStyleUrl = plugins_url('css/style3.css', __FILE__); 
	        $myStyleFile = Albo_DIR.'/css/style3.css';
	        if ( file_exists($myStyleFile) ) {
	            wp_register_style('AlboPretorioCSS3', $myStyleUrl);
	            wp_enqueue_style( 'AlboPretorioCSS3');
	        }	
		}
        $myStyleUrl = plugins_url('css/epoch_styles.css', __FILE__); 
        $myStyleFile = Albo_DIR.'/css/epoch_styles.css';
        if ( file_exists($myStyleFile) ) {
            wp_register_style('AlboPretorioCalendario', $myStyleUrl);
            wp_enqueue_style( 'AlboPretorioCalendario');
    	}
    }
   

}
	global $AP_OnLine;
	$AP_OnLine = new AlboPretorio();
		
}
?>