<?php
/**
 * Albo Pretorio AdminPanel - Amministrazione Albo Pretorio
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 1.4
 */

require_once(ABSPATH . 'wp-includes/pluggable.php'); 

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
	
				$('a.dc').click(function(){
					var answer = confirm("Confermi la cancellazione della Categoria `" + $(this).attr('rel') + '` ?')
					if (answer){
						return true;
					}
					else{
						return false;
					}					
				});

				$('a.da').click(function(){
					var answer = confirm("Confermi la cancellazione del\'Allegato `" + $(this).attr('rel') + '` ?')
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
				
			});
		
		})(jQuery);

	/* ]]> */
	</script>
<?php
}
add_action('admin_head', 'ap_head');



switch ( $_REQUEST['action'] ) {
		case 'updatecss':
			$location = "?page=editorcss";
			$file=stripslashes( $_REQUEST['file']);
			$newcontent = stripslashes($_REQUEST['newcontent']);
			if (is_writeable($file)) {
				//is_writable() not always reliable, check return value. see comments @ http://uk.php.net/is_writable
				
				$f = fopen($file, 'w+');
				if ($f !== FALSE) {
					fwrite($f, $newcontent);
					fclose($f);
					$location.="&a=1";
				} 
			}
			wp_redirect( $location );
		break;	
		case "delete-allegato-atto" :
		$location = "?page=categorie" ;
		ap_del_allegato_atto($_REQUEST['idAllegato'],$_REQUEST['idAtto'],$_REQUEST['Allegato']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('idAllegato'), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('Allegato'), $_SERVER['REQUEST_URI']);
		$location= add_query_arg( array ( 'action' => 'allegati-atto', 'id' => $_REQUEST['idAtto'] ));
		wp_redirect( $location );
		break;
	case 'add-categorie':
		$location = "?page=categorie" ;
		$ret=ap_insert_categoria($_POST['cat-name'],$_POST['cat-parente'],$_POST['cat-descrizione'],$_POST['cat-durata']);
		if ( !$ret && !is_wp_error( $ret ) )
			$location = add_query_arg( 'message', 1, $location );
		else
			$location = add_query_arg( 'message', 4, $location );
		wp_redirect( $location );
		break;
	case 'delete-categorie':
		$location = "?page=categorie" ;
		$res=ap_del_categorie($_GET['id']);
		if (!is_array($res))
			$location = add_query_arg( 'message', 2, $location );
		else{
			if ($res['atti']>0) {
				$location = add_query_arg( 'message', 8, $location );
			}else{
				if ($res['figli']>0) {
					$location = add_query_arg( 'message', 7, $location );
				}
			}
		}
		wp_redirect( $location );
		break;
	case 'edit-categorie':
		$location = "?page=categorie" ;
		$location = add_query_arg( 'id', $_GET['id'], $location );
		$location = add_query_arg( 'action', 'edit', $location );
		wp_redirect( $location );
		break;
 	case "delete-atto":
 		$location = "?page=atti" ;
		$res=ap_del_atto($_GET['id']);
		if (!is_array($res))
			$location = add_query_arg( 'message', 2, $location );
		else{
			if ($res['allegati']>0) {
				$location = add_query_arg( 'message', 7, $location );
			}
		}
		wp_redirect( $location );
		break;
	case 'memo-categoria':
		if (!is_wp_error( ap_memo_categorie($_REQUEST['id'],
							  $_REQUEST['cat-name'],
							  $_REQUEST['cat-parente'],
							  $_REQUEST['cat-descrizione'],
							  $_REQUEST['cat-durata'])))
			$location = add_query_arg( 'message', 3, $location );
		else
			$location = add_query_arg( 'message', 5, $location );
			
//		global $wpdb;
//		echo $wpdb->last_query;exit; 
		wp_redirect( $location );
		break;
	case "add-atto" :
		$location = "?page=atti" ;
		$ret=ap_insert_atto($_POST['Data'],
		                    $_POST['Riferimento'],
							$_POST['Oggetto'],
							$_POST['DataInizio'],
							$_POST['DataFine'],
							$_POST['Note'],
							$_POST['Categoria']);
		if ( !$ret && !is_wp_error( $ret ) )
			$location = add_query_arg( 'message', 1, $location );
		else
			$location = add_query_arg( 'message', 4, $location );
		wp_redirect( $location );
		break;
	case "memo-atto" :
		$location = "?page=atti" ;
		$ret=ap_memo_atto($_REQUEST['id'],
		                  $_POST['Data'],
		                  $_POST['Riferimento'],
						  $_POST['Oggetto'],
						  $_POST['DataInizio'],
						  $_POST['DataFine'],
						  $_POST['Note'],
						  $_POST['Categoria']);
		if ( !$ret && !is_wp_error( $ret ) )
			$location = add_query_arg( 'message', 3, $location );
		else
			$location = add_query_arg( 'message', 5, $location );
		wp_redirect( $location );
		break;
	case "memo-allegato-atto":
		$location = "?page=atti" ;
		$messaggio =addslashes(str_replace(" ","%20",Memo_allegato_atto()));
		$location = add_query_arg(array ( 'action' => 'allegati-atto', 
		                                  'messaggio' => $messaggio,
										  'id' => $_REQUEST['id']) , $location );
		wp_redirect( $location );
		break;	
	case "update-allegato-atto":
		$location='?page=atti&action=allegati-atto&id='.$_REQUEST['id'];
		if ($_REQUEST['submit']=="Annulla"){
			wp_redirect( $location );
		}else{
			$ret=ap_memo_allegato($_REQUEST['idAlle'],$_REQUEST['titolo'],$_REQUEST['id']);
			if ( is_object($ret)){
				$location = add_query_arg( 'messaggio', str_replace(' ',"%20",$ret->get_error_message()), $location );	
			}
			else{
			 	$location = add_query_arg( 'messaggio', "Allegato%20Aggiornato", $location );
			}
			wp_redirect( $location );		
		}
		break;
}

function Memo_allegato_atto(){
	if ($_REQUEST["operazione"]=="upload"){
	 	if ((($_FILES["file"]["size"] / 1024)/1024)<1){
			$DimFile=number_format($_FILES["file"]["size"] / 1024,2);
			$UnitM=" KB";
		}else{
			$DimFile=number_format(($_FILES["file"]["size"] / 1024)/1024,2);	
			$UnitM=" MB";
		}
	    $dime= "Dimensione: " . $DimFile . " ".$UnitM;
		if ($_FILES['file']['tmp_name']==''){
			$messages[4]= "Fine non selezionato Oppure operazione annullata";
		}else{
			if ($_FILES["file"]["type"] != "application/pdf"){
				$messages= "Tipo file non valido, sono ammessi soltanto i file in formato PDF";
			}else{
			 	
				if (($DimFile>20) and ($UnitM==" MB")){
					$messages= "Il file caricato è di ".$DimFile." Mb, il limite massimo &egrave; di 20 Mb";
				}else{
				  if ($_FILES["file"]["error"] > 0){
					$messages= "Errore: " . $_FILES["file"]["error"];
		    	}else{
					$destination_path = get_option('opt_AP_FolderUpload').'/';
			   		$result = 0;
			   		UniqueFileName(stripcslashes($destination_path) . basename( $_FILES['file']['name']));
				   	$target_path = UniqueFileName(stripcslashes($destination_path) . basename( $_FILES['file']['name']));
					   if(@move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
	    				$messages= "File caricato%%br%%Nome: " . basename( $target_path);
	    				ap_insert_allegato($_REQUEST['Descrizione'],$target_path,$_REQUEST['id']);
			   		}else{
						$messages= "Il File non caricato: " . $target_path;
					}
				}
		  	}
		  }
		}
		$msg=($messages!="") ? ($messages): ""; 
		$msg.=($dime!="") ?   "%%br%%" .($dime): "";
		$messages=$msg;
	}
	return $messages;
}
class APAdminPanel{
	
	// constructor
	function APAdminPanel() {

		// Add the admin menu
		add_action( 'admin_menu', array (&$this, 'add_menu') ); 
		$role =& get_role( 'amministratore_albo' );

	}

	static function add_menu(){
  		add_menu_page('Panoramica', 'Albo Pretorio', 'gest_atti_albo', 'Albo_Pretorio',array ('APAdminPanel', 'show_menu'));
		add_submenu_page( 'Albo_Pretorio', 'Atti', 'Atti', 'gest_atti_albo', 'atti', array ('APAdminPanel', 'show_menu'));
//		add_submenu_page( 'albo-options', 'Allegati', 'Allegati', 'manage_options', 'allegati', array('APAdminPanel', 'show_menu'));
		add_submenu_page( 'Albo_Pretorio', 'Categorie', 'Categorie', 'gest_atti_albo', 'categorie', array ('APAdminPanel', 'show_menu'));
		add_submenu_page( 'Albo_Pretorio', 'Generale', 'Parametri', 'admin_albo', 'config', array('APAdminPanel', 'show_menu'));
		add_submenu_page( 'Albo_Pretorio', 'Css', 'Css', 'admin_albo', 'editorcss', array('APAdminPanel', 'show_menu'));
	}

	// load the script for the defined page and load only this code	
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
				include_once ( dirname (__FILE__) . '/categorie.php' );	// admin functions
				break;
			case "atti" :
				include_once ( dirname (__FILE__) . '/atti.php' );	// admin functions
				break;
			case "allegati" :
				include_once ( dirname (__FILE__) . '/allegati.php' );	// admin functions
				break;
			case "editorcss":
				include_once ( dirname (__FILE__) . '/editor.php' );	// admin functions
				break;
		}
	}
	


}
?>