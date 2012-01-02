<?php
/*
Plugin Name:Albo Pretorio On line
Plugin URI: http://www.sisviluppo.info
Description: Plugin utilizzato per la pubblicazione degli atti da inserire nell'albo pretorio dell'ente.
Version:1.6
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
define("Albo_URL",plugin_dir_url(dirname (__FILE__).'/AlboPretorio.php'));
define("Albo_DIR",dirname (__FILE__));
define("AP_BASE_DIR",substr(WP_PLUGIN_DIR,0,strpos(WP_PLUGIN_DIR,"wp-content", 0)));

if (!class_exists('AlboPretorio')) {
 class AlboPretorio {
	
	var $version     = '1.4';
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

		if (!is_admin()) 
			if (!function_exists('albo_styles'))
				add_action('wp_print_styles', array('AlboPretorio','albo_styles'));
		
		add_shortcode('Albo', array('AlboPretorio', 'VisualizzaAtti'));
		add_action('wp_head', array('AlboPretorio','head_Front_End'));
	}
	
	function head_Front_End() {
		?>
		<script type="text/javascript" src="<?php echo Albo_URL; ?>/js/epoch_classes.js"></script>
		<script type="text/javascript">
			var Cal1, Cal2; 
			window.onload = function() {
				Cal1 = new Epoch('cal1','popup',document.getElementById('Calendario1'),false);
				Cal2 = new Epoch('cal2','popup',document.getElementById('Calendario2'),false);
			};
		</script>
	

	<?php
	}


	function load_dependencies() {
	
			// Load backend libraries
			if ( is_admin() ) {	
				require_once (dirname (__FILE__) . '/admin/admin.php');
				$this->APAdminPanel = new APAdminPanel();
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
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
	  }
	  $n_atti = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti;" ) );	 
	  $n_atti_dapub = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti Where Numero=0;"));	
	  $n_atti_attivi = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti Where DataInizio <= now() And DataFine>= now() And Numero>0;"));	
	  $n_atti_storico=$n_atti-$n_atti_attivi-$n_atti_dapub; 
	  $n_allegati = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Allegati;" ) );	 
	  $n_categorie = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Categorie;" ) );	 
    
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
					<input type="submit" name="submit" id="submit" class="button" value="Aggiorna Anno Albo"  />
					</form>
				</div>
			 </div>';
	}
	}	

	function AP_config(){
	   global $current_user,$stato;
	  if ($_REQUEST['action']=="setta-anno"){
		update_option('opt_AP_AnnoProgressivo',date("Y") );
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
	  }
	  get_currentuserinfo();
	  $ente   = stripslashes(get_option('opt_AP_Ente'));
	  $nprog  =  get_option('opt_AP_NumeroProgressivo');
	  $nanno=get_option('opt_AP_AnnoProgressivo');
	  $visente=get_option('opt_AP_VisualizzaEnte');
	  $efftext=get_option('opt_AP_EffettiTesto');
	  $effcss3=get_option('opt_AP_EffettiCSS3');
	  $livelloTitoloEnte=get_option('opt_AP_LivelloTitoloEnte');
	  $livelloTitoloPagina=get_option('opt_AP_LivelloTitoloPagina');
	  $livelloTitoloFiltri=get_option('opt_AP_LivelloTitoloFiltri');
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
			<th scope="row"><label for="blogname">Nome Ente</label></th>
			<td><input type="text" name="c_Ente" value="'.$ente.'" size="100"/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="blogname">Visualizza Nome Ente</label></th>
			<td><input type="checkbox" name="c_VEnte" value="Si" '.$ve_selezionato.'/></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="blogname">Utilizza effetti testo Shadow</label></th>
			<td><input type="checkbox" name="c_TE" value="Si" '.$ve_efftext.'/></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="blogname">Utilizza effetti CSS3</label></th>
			<td><input type="checkbox" name="c_CSS3" value="Si" '.$ve_effcss3.'/></td>
		</tr>		
		<tr valign="top">
			<th scope="row"><label for="blogname">Titolo Nome Ente</label></th>
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
			<th scope="row"><label for="blogname">Titolo Pagina Albo</label></th>
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
			<th scope="row"><label for="blogname">Titolo Filtri</label></th>
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
			<th scope="row"><label for="blogname">Numero Progressivo</label></th>
			<td><input type="text" name="c_NumeroProgressivo" value="'.$nprog.'" size="5"/>/ '.$nanno.'</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="blogname">Cartella Upload</label></th>
			<td><input type="text" name="c_dirUpload" value="'.$dirUpload.'" size="80"/><input type="button" value="Valore di Default" onclick="this.form.c_dirUpload.value="wp-content/uploads"><br />inserire una cartella valida partendo da <strong>'.AP_BASE_DIR.'</strong></td>
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
						<input type="submit" name="submit" id="submit" class="button" value="Aggiorna Anno Albo"  />
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

		if(get_option('opt_AP_Ente' ) == '' || !get_option('opt_AP_Ente')){
			add_option('opt_AP_Ente', 'Ente non definito');
		}
		if(get_option('opt_AP_AnnoProgressivo')  == '' || !get_option('opt_AP_AnnoProgressivo')){
			add_option('opt_AP_AnnoProgressivo', ''.date("Y").'');
		}
		if(get_option('opt_AP_NumeroProgressivo')  == '' || !get_option('opt_AP_NumeroProgressivo')){
			add_option('opt_AP_NumeroProgressivo', '0');
		}
		if(get_option('opt_AP_FolderUpload') == '' || !get_option('opt_AP_FolderUpload')){
			add_option('opt_AP_FolderUpload', $dirUpload);
		}
		if(get_option('opt_AP_VisualizzaEnte') == '' || !get_option('opt_AP_VisualizzaEnte')){
			add_option('opt_AP_VisualizzaEnte', 'Si');
		}
		if(get_option('opt_AP_EffettiTesto') == '' || !get_option('opt_AP_EffettiTesto')){
			add_option('opt_AP_EffettiTesto', 'Si');
		}
		if(get_option('opt_AP_EffettiCSS3') == '' || !get_option('opt_AP_EffettiCSS3')){
			add_option('opt_AP_EffettiCSS3', 'Si');
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
		  
/*************************************************************************************
** Area riservata per l'aggiunta di nuovi campi in una delle tabelle dell' albo ******
*************************************************************************************/
		if (!existFieldInTable($wpdb->table_name_Atti, "RespProc")){
			if (!($ret=AddFiledTable($wpdb->table_name_Atti, "RespProc", "INT NOT NULL")))
				echo $ret;
		}
          
 		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		     dbDelta($sql_Atti);
		     dbDelta($sql_Allegati);
		     dbDelta($sql_Categorie);
		     dbDelta($sql_Log);
		     dbDelta($sql_RespProc);
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

        /* Ciclo che elimona i ruoli solo se non ci sono utenti assegnati a quel ruolo, altrimenti non disinstalla */
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
	}
	function update_AlboPretorio_settings(){
	    if($_POST['AlboPretorio_submit_button'] == 'Salva Modifiche'){
		    update_option('opt_AP_Ente',$_POST['c_Ente'] );
		    if ($_POST['c_VEnte']=='Si')
			    update_option('opt_AP_VisualizzaEnte','Si' );
			else
				update_option('opt_AP_VisualizzaEnte','No' );
		    update_option('opt_AP_AnnoProgressivo',$_POST['c_AnnoProgressivo'] );
		    update_option('opt_AP_NumeroProgressivo',$_POST['c_NumeroProgressivo'] );
		    update_option('opt_AP_EffettiTesto',$_POST['c_TE'] );
		    update_option('opt_AP_EffettiCSS3',$_POST['c_CSS3'] );
		    update_option('opt_AP_LivelloTitoloEnte',$_POST['c_LTE'] );
		    update_option('opt_AP_LivelloTitoloPagina',$_POST['c_LTP'] );
		    update_option('opt_AP_LivelloTitoloFiltri',$_POST['c_LTF'] );
		    update_option('opt_AP_FolderUpload',$_POST['c_dirUpload'] );
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