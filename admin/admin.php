<?php
/**
 * Albo Pretorio AdminPanel - Amministrazione Albo Pretorio
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.5
 */

require_once(ABSPATH . 'wp-includes/pluggable.php'); 

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
		$location = "?page=atti" ;
		ap_del_allegato_atto($_REQUEST['idAllegato'],$_REQUEST['idAtto'],$_REQUEST['Allegato']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('idAllegato'), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('Allegato'), $_SERVER['REQUEST_URI']);
		$location= add_query_arg( array ( 'action' => 'allegati-atto', 'id' => $_REQUEST['idAtto'] ));
		wp_redirect( $location );
		break;
	case 'add-responsabile':
		$location = "?page=responsabili" ;
		if (!is_email( $_REQUEST['resp-email']) or $_POST['resp-cognome']==''){
			$location = add_query_arg( 'errore', !is_email( $_REQUEST['resp-email']) ? 'Email non valida': "Bisogna valorizzare il Cognome del Responsabile", $location );
			$location = add_query_arg( 'message', 4, $location );
			$location = add_query_arg( 'resp-cognome', $_POST['resp-cognome'], $location );
			$location = add_query_arg( 'resp-nome', $_POST['resp-nome'], $location );
			$location = add_query_arg( 'resp-email', $_POST['resp-email'], $location );
			$location = add_query_arg( 'resp-telefono', $_POST['resp-telefono'], $location );
			$location = add_query_arg( 'resp-orario', $_POST['resp-orario'], $location );
			$location = add_query_arg( 'resp-note', $_POST['resp-note'], $location );
			$location = add_query_arg( 'action', 'add', $location );
		}
		else{
			$ret=ap_insert_responsabile($_POST['resp-cognome'],$_POST['resp-nome'],$_POST['resp-email'],$_POST['resp-telefono'],$_POST['resp-orario'],$_POST['resp-note']);
			if ( !$ret && !is_wp_error( $ret ) )
				$location = add_query_arg( 'message', 1, $location );
			else
				$location = add_query_arg( 'message', 4, $location );
		}
		wp_redirect( $location );
		break;
	case 'edit-responsabile':
		$location = "?page=responsabili" ;
		$location = add_query_arg( 'id', $_GET['id'], $location );
		$location = add_query_arg( 'action', 'edit', $location );
		wp_redirect( $location );
		break;
	case 'memo-responsabile':
		$location = "?page=responsabili" ;
		if (!is_email( $_REQUEST['resp-email'] )){
			$location = add_query_arg( 'errore', 'Email non valida', $location );
			$location = add_query_arg( 'message', 5, $location );
			$location = add_query_arg( 'resp-cognome', $_REQUEST['resp-cognome'], $location );
			$location = add_query_arg( 'resp-nome', $_REQUEST['resp-nome'], $location );
			$location = add_query_arg( 'resp-email', $_REQUEST['resp-email'], $location );
			$location = add_query_arg( 'resp-telefono', $_REQUEST['resp-telefono'], $location );
			$location = add_query_arg( 'resp-orario', $_REQUEST['resp-orario'], $location );
			$location = add_query_arg( 'resp-note', $_REQUEST['resp-note'], $location );
			$location = add_query_arg( 'action', 'edit_err', $location );
			$location = add_query_arg( 'id', $_REQUEST['id'], $location );
		}
		else
			if (!is_wp_error(ap_memo_responsabile($_REQUEST['id'],
								  $_REQUEST['resp-cognome'],
								  $_REQUEST['resp-nome'],
								  $_REQUEST['resp-email'],
								  $_REQUEST['resp-telefono'],
								  $_REQUEST['resp-orario'],
								  $_REQUEST['resp-note'])))
				$location = add_query_arg( 'message', 3, $location );
			else
				$location = add_query_arg( 'message', 5, $location );
//		global $wpdb;
//		echo $wpdb->last_query;exit; 
		wp_redirect( $location );
		break;
	case 'delete-ente':
		$location = "?page=enti" ;
		$res=ap_del_ente($_GET['id']);
		if (!is_array($res))
			$location = add_query_arg( 'message', 2, $location );
		else{
			if ($res['atti']>0)
				$location = add_query_arg( 'message', 7, $location );
			else
				$location = add_query_arg( 'message', 6, $location );
		}
		wp_redirect( $location );
		break;
	case 'add-ente':
		$location = "?page=enti" ;
		$errore="";
		if ($_REQUEST['ente-nome']=='') $errore.="Bisogna valorizzare il Nome dell' Ente <br />";
		if (!is_email( $_REQUEST['ente-email'])) $errore.="Email non valida <br />"; 
		if (!is_email( $_REQUEST['ente-pec'])) $errore.="Pec non valida <br />"; 
		if (strlen($errore)>0){
			$location = add_query_arg( 'errore', $errore, $location );
			$location = add_query_arg( 'message', 4, $location );
			$location = add_query_arg( 'ente-nome', $_REQUEST['ente-nome'], $location );
			$location = add_query_arg( 'ente-indirizzo', $_REQUEST['ente-indirizzo'], $location );
			$location = add_query_arg( 'ente-url', $_REQUEST['ente-url'], $location );
			$location = add_query_arg( 'ente-email', $_REQUEST['ente-email'], $location );
			$location = add_query_arg( 'ente-pec', $_REQUEST['ente-pec'], $location );
			$location = add_query_arg( 'ente-telefono', $_REQUEST['ente-telefono'], $location );
			$location = add_query_arg( 'ente-fax', $_REQUEST['ente-fax'], $location );
			$location = add_query_arg( 'ente-note', $_REQUEST['ente-note'], $location );
			$location = add_query_arg( 'action', 'add', $location );
		}
		else{
			$ret=ap_insert_ente($_REQUEST['ente-nome'],$_REQUEST['ente-indirizzo'],$_REQUEST['ente-url'],$_REQUEST['ente-email'],$_REQUEST['ente-pec'],$_REQUEST['ente-telefono'],$_REQUEST['ente-fax'],$_REQUEST['ente-note']);
			if ( !$ret && !is_wp_error( $ret ) )
				$location = add_query_arg( 'message', 1, $location );
			else
				$location = add_query_arg( 'message', 4, $location );
		}
		wp_redirect( $location );
		break;
	case 'edit-ente':
		$location = "?page=enti" ;
		$location = add_query_arg( 'id', $_GET['id'], $location );
		$location = add_query_arg( 'action', 'edit', $location );
		wp_redirect( $location );
		break;
	case 'memo-ente':
		$location = "?page=enti" ;
		$errore="";
		if ($_REQUEST['ente-nome']=='') $errore.="Bisogna valorizzare il Nome dell' Ente <br />";
		if (!is_email( $_REQUEST['ente-email'])) $errore.="Email non valida <br />"; 
		if (!is_email( $_REQUEST['ente-pec'])) $errore.="Pec non valida <br />"; 
		if (strlen($errore)>0){
			$location = add_query_arg( 'errore', $errore, $location );
			$location = add_query_arg( 'message', 4, $location );
			$location = add_query_arg( 'ente-nome', $_REQUEST['ente-nome'], $location );
			$location = add_query_arg( 'ente-indirizzo', $_REQUEST['ente-indirizzo'], $location );
			$location = add_query_arg( 'ente-url', $_REQUEST['ente-url'], $location );
			$location = add_query_arg( 'ente-email', $_REQUEST['ente-email'], $location );
			$location = add_query_arg( 'ente-pec', $_REQUEST['ente-pec'], $location );
			$location = add_query_arg( 'ente-telefono', $_REQUEST['ente-telefono'], $location );
			$location = add_query_arg( 'ente-fax', $_REQUEST['ente-fax'], $location );
			$location = add_query_arg( 'ente-note', $_REQUEST['ente-note'], $location );
			$location = add_query_arg( 'action', 'add', $location );
		}
		else
			if (!is_wp_error(ap_memo_ente($_REQUEST['id'],
								  $_REQUEST['ente-nome'],
								  $_REQUEST['ente-indirizzo'],
								  $_REQUEST['ente-url'],
								  $_REQUEST['ente-email'],
								  $_REQUEST['ente-pec'],
								  $_REQUEST['ente-telefono'],
								  $_REQUEST['ente-fax'],
								  $_REQUEST['ente-note'])))
				$location = add_query_arg( 'message', 3, $location );
			else
				$location = add_query_arg( 'message', 5, $location );
//		global $wpdb;
//		echo $wpdb->last_query;exit; 
		wp_redirect( $location );
		break;
	case 'add-categorie':
		$location = "?page=categorie" ;
		if ($_POST['cat-name']=='')
			$location = add_query_arg( 'message', 9, $location );
		else{
			$ret=ap_insert_categoria($_POST['cat-name'],$_POST['cat-parente'],$_POST['cat-descrizione'],$_POST['cat-durata']);
			if ( !$ret && !is_wp_error( $ret ) )
				$location = add_query_arg( 'message', 1, $location );
			else
				$location = add_query_arg( 'message', 4, $location );
		}			
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
	case 'memo-categoria':
		$location = "?page=categorie" ;
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
	case "add-atto" :
		$NonValidato=false;
		$message="Impossibile memorizzare l'atto:";
		if ($_POST['Categoria']==0){
			$NonValidato=true;
			$message.="<br />Bisogna selezionare una Categoria ";
		}
		if ($_POST['Responsabile']==0){
			$NonValidato=true;
			$message.="<br />Bisogna selezionare un Responsabile del Procedimento ";
		}
		$location = "?page=atti" ;
		if ($NonValidato){
			$location = add_query_arg( 'msg', $message, $location );
			$location = add_query_arg( 'action', "new-atto", $location );	
			$location = add_query_arg( 'id', $_POST['id'], $location );	
			$location = add_query_arg( 'Ente', $_POST['Ente'], $location );	
			$location = add_query_arg( 'Data', $_POST['Data'], $location );	
			$location = add_query_arg( 'Riferimento', $_POST['Riferimento'], $location );	
			$location = add_query_arg( 'Oggetto', $_POST['Oggetto'], $location );	
			$location = add_query_arg( 'DataInizio', $_POST['DataInizio'], $location );	
			$location = add_query_arg( 'DataFine', $_POST['DataFine'], $location );	
			$location = add_query_arg( 'Note', $_POST['Note'], $location );	
			$location = add_query_arg( 'Categoria', $_POST['Categoria'], $location );	
			$location = add_query_arg( 'Responsabile', $_POST['Responsabile'], $location );	
		}else{
			$ret=ap_insert_atto($_POST['Ente'],
					            $_POST['Data'],
			                    $_POST['Riferimento'],
								$_POST['Oggetto'],
								$_POST['DataInizio'],
								$_POST['DataFine'],
								$_POST['Note'],
								$_POST['Categoria'],
								$_POST['Responsabile']);
			if ( !$ret && !is_wp_error( $ret ) )
				$location = add_query_arg( 'message', 1, $location );
			else
				$location = add_query_arg( 'message', 4, $location );
		}
		wp_redirect( $location );
		break;
	case "memo-atto" :
		$location = "?page=atti" ;
		$ret=ap_memo_atto($_REQUEST['id'],
						  $_REQUEST['Ente'],
		                  $_POST['Data'],
		                  $_POST['Riferimento'],
						  $_POST['Oggetto'],
						  $_POST['DataInizio'],
						  $_POST['DataFine'],
						  $_POST['Note'],
						  $_POST['Categoria'],
						  $_POST['Responsabile']);
		if ( !$ret && !is_wp_error( $ret ) )
			$location = add_query_arg( 'message', 3, $location );
		else
			$location = add_query_arg( 'message', 5, $location );
		wp_redirect( $location );
		break;
	case "memo-allegato-atto":
		$location = "?page=atti" ;
		$messaggio =addslashes(str_replace(" ","%20",Memo_allegato_atto()));
		if (isset($_REQUEST['ref']))
			$location = add_query_arg(array ( 'action' => $_REQUEST['ref'], 
										  'messaggio' => $messaggio,
										  'id' => $_REQUEST['id']) , $location );
		else
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
//			if ($_FILES["file"]["type"] != "application/pdf"){
//				$messages= "Tipo file non valido, sono ammessi soltanto i file in formato PDF e p7m";
			if (!isAllowedExtension(strtolower($_FILES["file"]["name"]))){
				$messages= "Tipo file non valido, sono ammessi soltanto i file in formato PDF e p7m";
			}else{
				if (($DimFile>20) and ($UnitM==" MB")){
					$messages= "Il file caricato è di ".$DimFile." Mb, il limite massimo &egrave; di 20 Mb";
				}else{
				  if ($_FILES["file"]["error"] > 0){
					$messages= "Errore: " . $_FILES["file"]["error"];
		    	}else{
					$destination_path = AP_BASE_DIR.get_option('opt_AP_FolderUpload').'/';
			   		$result = 0;
				   	$target_path = UniqueFileName($destination_path . basename( $_FILES['file']['name']));
					if(@move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
	    				$messages= "File caricato%%br%%Nome: " . basename( $target_path)." %%br%%Percorso completo : ".str_replace("\\","/",$target_path);
	    				ap_insert_allegato($_REQUEST['Descrizione'],str_replace("\\","/",$target_path),$_REQUEST['id']);
			   		}else{
						$messages= "Il File non caricato: " .str_replace("\\","/",$target_path)."%%b%% Errore:".$_FILES['file']['error'];
						//print($messages);exit;
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

?>