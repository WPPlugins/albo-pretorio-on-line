<?php
/**
 * Amministrazione richieste delle singole pagine.
 * @link       http://www.eduva.org
 * @since      4.0.3
 *
 * @package    ALbo On Line
 */
// require_once(ABSPATH . 'wp-includes/pluggable.php'); 
add_action( 'init', 'albo_post');
function DownloadFile($file_path){
	$chunksize	= 2*(1024*1024);
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
}
function albo_post() {
if(isset($_REQUEST['action'] )){
	switch ( $_REQUEST['action'] ) {		
			case "ToCsv":
				$Testata="Nome Ente;Numero Atto;Riferimento;Oggetto;Data Inizio;Data Fine;Informazioni;Categoria;Data Annullamento;Motivo Annullamento";
				$Atti="";
				$Righe=ap_Repertorio($_REQUEST['Anno'],FALSE);
				foreach($Righe as $Riga){
					$Atti.=stripcslashes($Riga->NomeEnte).";";
					$Atti.=$Riga->Numero.";";
					$Atti.=stripcslashes($Riga->Riferimento).";";
					$Atti.=stripcslashes($Riga->Oggetto).";";
					$Atti.=$Riga->DataInizio.";";
					$Atti.=$Riga->DataFine.";";
					$Atti.=stripcslashes($Riga->Informazioni).";";
					$Atti.=$Riga->Categoria.";";
					$Atti.=$Riga->DataAnnullamento.";";
					$Atti.=stripcslashes($Riga->MotivoAnnullamento).";\n";					
				}	
				$Dir=str_replace("\\","/",Albo_DIR.'/Repertori');
				if (!is_dir ( $Dir))
					if (!mkdir($Dir, 0755)) 
						break;
				$file_path=$Dir."/repertorio_".$_REQUEST['Anno'].".csv";
				$file = fopen($file_path, "w") or die;
				fwrite($file, $Testata."\n".$Atti);
				fclose($file);
				DownloadFile($file_path);
				break;
			case "ToXML":
				$xml=new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes" ?><repertorio></repertorio>'); 
				$MetaData=$xml->addChild('metadata');
				$MetaData->addChild('anno',$_REQUEST['Anno']);
				$Righe=ap_Repertorio($_REQUEST['Anno'],FALSE);
				$Atti=$xml->addChild('Atti');
				foreach($Righe as $Riga){
					$Atto=$Atti->addChild('Atto');
					$riga=$Atto->addChild('NomeEnte', stripcslashes($Riga->NomeEnte));
					$riga=$Atto->addChild('Numero', $Riga->Numero);
					$riga=$Atto->addChild('Riferimento', stripcslashes($Riga->Riferimento));
					$riga=$Atto->addChild('Oggetto', stripcslashes($Riga->Oggetto));
					$riga=$Atto->addChild('DataInizio', $Riga->DataInizio);
					$riga=$Atto->addChild('DataFine', $Riga->DataFine);
					$riga=$Atto->addChild('Informazioni',stripcslashes($Riga->Informazioni));
					$riga=$Atto->addChild('Categoria',$Riga->Categoria);
					$riga=$Atto->addChild('DataAnnullamento',$Riga->DataAnnullamento);
					$riga=$Atto->addChild('MotivoAnnullamento',stripcslashes($Riga->MotivoAnnullamento));					
				}	
				$Dir=str_replace("\\","/",Albo_DIR.'/Repertori');
				if (!is_dir ( $Dir))
					if (!mkdir($Dir, 0755)) 
						break;
				$file_path=$Dir."/repertorio_".$_REQUEST['Anno'].".xml";
				$file = fopen($file_path, "w") or die;
				fwrite($file, $xml->asXML());
				fclose($file);
				DownloadFile($file_path);
				break;
			case "ToJson": 
				$Repertorio=ap_Repertorio($_REQUEST['Anno'],FALSE);
				$Dir=str_replace("\\","/",Albo_DIR.'/Repertori');
				if (!is_dir ( $Dir))
					if (!mkdir($Dir, 0755)) 
						break;
				$file_path=$Dir."/repertorio_".$_REQUEST['Anno'].".json";
				$file = fopen($file_path, "w") or die;
				$txt = json_encode($Repertorio);
				fwrite($file, $txt);
				fclose($file);
				DownloadFile($file_path);
				break;
			case "ToPdf":
				if (isset($_GET['Anno']))
					$AnnoRepertorio=$_GET['Anno'];
				else
					$AnnoRepertorio=date("Y");
				$ToPdf= new ap_cls_Repertorio("Portrait","mm","A4");
				$ToPdf->ToTable($AnnoRepertorio);
				break;			
			case "delete_bulk_atti":
		        if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
	            	$nonce  = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
	            	$action = 'bulk-atti' ;
		            if ( ! wp_verify_nonce( $nonce, $action ) )
		                wp_die( 'Attenzione! Verfica sicurezza non riuscita!' );
		        }
			 	$Msg=ap_oblio_atti($_GET['IdAtto']);
			 	$location = "?page=atti&stato_atti=Eliminare&message=".urlencode($Msg);
				wp_redirect( $location );
				break;
 			case "elimina-atto":
				if ( isset( $_GET['cancellatto'] ) && ! empty( $_GET['cancellatto'] ) ) {
		            $nonce  = filter_input( INPUT_GET, 'cancellatto', FILTER_SANITIZE_STRING );
		            $action = 'operazionecancelaatto';
		            if ( ! wp_verify_nonce( $nonce, $action ) )
		                wp_die( 'Attenzione! Verfica sicurezza non riuscita!',"Problemi di sicurezza",array("back_link" => "?page=atti") );
			 		$location = "?page=atti&stato_atti=Eliminare" ;
			 		$MessaggiRitorno=ap_oblio_atti((int)$_GET['id']);
					$location = add_query_arg( 'message',$MessaggiRitorno["Message"], $location );
					$location = add_query_arg( 'message2',$MessaggiRitorno["Message2"], $location );
					wp_redirect( $location );
				}else
					wp_die( 'Attenzione! Verfica sicurezza non riuscita!',"Problemi di sicurezza",array("back_link" => "?page=atti") );					
			break;
		case "annulla-atto":
			if (!isset($_REQUEST['annatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['annatto'],'annatto')){
				Go_Atti();
				break;
			} 
			if ($_REQUEST['Motivo']=="null") {
				$NumMsg=8;
			}else{
				$Allegati=array();
				foreach($_REQUEST as $Parametro=>$ValoreParametro){
					if(substr($Parametro,0,5)=="Alle:")
						$Allegati[]=$ValoreParametro;
				}
				$Risultato=ap_annulla_atto((int)$_REQUEST['id'],$_REQUEST['Motivo'],$Allegati);
			}
	 		$location = "?page=atti&p=1" ;
			$location = add_query_arg( 'message', $Risultato, $location );
			wp_redirect( $location );
			break;
		case "ExportBackupData":
			if (!isset($_REQUEST['exportbckdata'])) {
				Go_Utility();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['exportbckdata'],'EsportaBackupDatiAlbo')){
				Go_Utility();
				break;
			} 		
			$location='Location: '.$_REQUEST['elenco_Backup_Expo'];
			wp_redirect( ap_DaPath_a_URL($location) );
			break;
		case "delete-allegato-atto" :
			$location = "?page=atti" ;
			ap_del_allegato_atto((int)$_REQUEST['idAllegato'],(int)$_REQUEST['idAtto'],htmlentities($_REQUEST['Allegato']));
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('idAllegato'), $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('Allegato'), $_SERVER['REQUEST_URI']);
			$location= add_query_arg( array ( 'action' => 'allegati-atto', 
									          'id' => $_REQUEST['idAtto'],
									          'allegatoatto'=>wp_create_nonce('gestallegatiatto')));
			wp_redirect( $location );
			break;
		case 'add-responsabile':
			if (!isset($_REQUEST['responsabili'])) {
				Go_Responsabili();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['responsabili'],'elabresponsabili')){
				Go_Responsabili();
				break;
			} 	
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
				$ret=ap_insert_responsabile(strip_tags($_POST['resp-cognome']),strip_tags($_POST['resp-nome']),strip_tags($_POST['resp-email']),strip_tags($_POST['resp-telefono']),strip_tags($_POST['resp-orario']),strip_tags($_POST['resp-note']));
				if ( !$ret && !is_wp_error( $ret ) )
					$location = add_query_arg( 'message', 1, $location );
				else
					$location = add_query_arg( 'message', 4, $location );
			}
			wp_redirect( $location );
			break;
		case 'edit-responsabile':
			if (!isset($_REQUEST['modresp'])) {
				Go_Responsabili();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['modresp'],'editresponsabile')){
				Go_Responsabili();
				break;
			} 		
			$location = "?page=responsabili" ;
			$location = add_query_arg( 'id', (int)$_GET['id'], $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_redirect( $location );
			break;
		case 'memo-responsabile':
			if (!isset($_REQUEST['responsabili'])) {
				Go_Responsabili();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['responsabili'],'elabresponsabili')){
				Go_Responsabili();
				break;
			} 		
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
				$location = add_query_arg( 'id', (int)$_REQUEST['id'], $location );
			}
			else
				if (!is_wp_error(ap_memo_responsabile((int)$_REQUEST['id'],
									  strip_tags($_REQUEST['resp-cognome']),
									  strip_tags($_REQUEST['resp-nome']),
									  strip_tags($_REQUEST['resp-email']),
									  strip_tags($_REQUEST['resp-telefono']),
									  strip_tags($_REQUEST['resp-orario']),
									  strip_tags($_REQUEST['resp-note']))))
					$location = add_query_arg( 'message', 3, $location );
				else
					$location = add_query_arg( 'message', 5, $location );
	//		global $wpdb;
	//		echo $wpdb->last_query;exit; 
			wp_redirect( $location );
			break;
		case 'delete-ente':
			if (!isset($_REQUEST['cancellaente'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['cancellaente'],'deleteente')){
				Go_Enti();
				break;
			} 			
			$location = "?page=enti" ;
			$res=ap_del_ente((int)$_GET['id']);
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
			if (!isset($_REQUEST['enti'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['enti'],'enti')){
				Go_Enti();
				break;
			} 		
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
				$ret=ap_insert_ente(strip_tags($_REQUEST['ente-nome']),strip_tags($_REQUEST['ente-indirizzo']),strip_tags($_REQUEST['ente-url']),strip_tags($_REQUEST['ente-email']),strip_tags($_REQUEST['ente-pec']),strip_tags($_REQUEST['ente-telefono']),strip_tags($_REQUEST['ente-fax']),strip_tags($_REQUEST['ente-note']));
				if ( !$ret && !is_wp_error( $ret ) )
					$location = add_query_arg( 'message', 1, $location );
				else
					$location = add_query_arg( 'message', 4, $location );
			}
			wp_redirect( $location );
			break;
		case 'edit-ente':
			if (!isset($_REQUEST['modificaente'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['modificaente'],'editente')){
				Go_Enti();
				break;
			} 		
			$location = "?page=enti" ;
			$location = add_query_arg( 'id', (int)$_GET['id'], $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_redirect( $location );
			break;
		case 'memo-ente':
			if (!isset($_REQUEST['enti'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['enti'],'enti')){
				Go_Enti();
				break;
			} 			
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
				$location = add_query_arg( 'action', $_REQUEST['action2'], $location );
			}
			else
				if (!is_wp_error(ap_memo_ente((int)$_REQUEST['id'],
									  strip_tags($_REQUEST['ente-nome']),
									  strip_tags($_REQUEST['ente-indirizzo']),
									  strip_tags($_REQUEST['ente-url']),
									  strip_tags($_REQUEST['ente-email']),
									  strip_tags($_REQUEST['ente-pec']),
									  strip_tags($_REQUEST['ente-telefono']),
									  strip_tags($_REQUEST['ente-fax']),
									  strip_tags($_REQUEST['ente-note']))))
					$location = add_query_arg( 'message', 3, $location );
				else
					$location = add_query_arg( 'message', 5, $location );
	//		global $wpdb;
	//		echo $wpdb->last_query;exit; 
			wp_redirect( $location );
			break;
		case 'add-categorie':
			if (!isset($_POST['categoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce($_POST['categoria'],'categoria')){
				Go_Categorie();
				break;
			} 		
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
			if (!isset($_GET['canccategoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce($_GET['canccategoria'],'delcategoria')){
				Go_Categorie();
				break;
			} 		
			$location = "?page=categorie" ;
			$res=ap_del_categorie((int)$_GET['id']);
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
			if (!isset($_GET['modcategoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce($_GET['modcategoria'],'editcategoria')){
				Go_Categorie();
				break;
			} 		
			$location = "?page=categorie" ;
			$location = add_query_arg( 'id', (int)$_GET['id'], $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_redirect( $location );
			break;
		case 'memo-categoria':
			if (!isset($_POST['categoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce($_POST['categoria'],'categoria')){
				Go_Categorie();
				break;
			} 		
			$location = "?page=categorie" ;
			if (!is_wp_error( ap_memo_categorie((int)$_REQUEST['id'],
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
			if (!isset($_GET['cancellaatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce($_GET['cancellaatto'],'deleteatto')){
				Go_Atti();
				break;
			} 		
			$location = "?page=atti" ;
			if(ap_del_allegati_atto((int)$_GET['id']))
				$location = add_query_arg( 'message2',10, $location );
			else
				$location = add_query_arg( 'message2',11, $location );
			$res=ap_del_atto($_GET['id']);
			if (!is_array($res))
				$location = add_query_arg( 'message', 2, $location );
			else{
				if ($res['allegati']>0) {
					$location = add_query_arg( 'message', 7, $location );
				}else
					$location = add_query_arg( 'message', 6, $location );
			}
			wp_redirect( $location );
			break;
		case "add-atto" :
			if (!isset($_POST['nuovoatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce($_POST['nuovoatto'],'nuovoatto')){
				Go_Atti();
				break;
			} 
			$location = "?page=atti" ;
			$ret=ap_insert_atto($_POST['Ente'],
					            $_POST['Data'],
			                    $_POST['Riferimento'],
								$_POST['Oggetto'],
								$_POST['DataInizio'],
								$_POST['DataFine'],
								$_POST['DataOblio'],
								$_POST['Note'],
								$_POST['Categoria'],
								$_POST['Responsabile']);
			if ( !$ret)
				$location = add_query_arg( 'message', 1, $location );
			else{
				$location = add_query_arg( 'message', 4, $location );
				$location = add_query_arg( 'errore', $ret , $location );		
			}
			wp_redirect( $location );
			break;
		case "memo-atto" :
			if (!isset($_REQUEST['modificaatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['modificaatto'],'editatto')){
				Go_Atti();
				break;
			} 		
			$location = "?page=atti" ;
			$ret=ap_memo_atto((int)$_REQUEST['id'],
							  $_REQUEST['Ente'],
			                  $_POST['Data'],
			                  $_POST['Riferimento'],
							  $_POST['Oggetto'],
							  $_POST['DataInizio'],
							  $_POST['DataFine'],
							  $_POST['DataOblio'],
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
			$location = site_url()."/wp-admin/admin.php?page=atti" ;
			$location='?page=atti&action=allegati-atto&id='.(int)$_REQUEST['id'].'&allegatoatto='.wp_create_nonce('gestallegatiatto');
			if (!isset($_REQUEST['uploallegato'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['uploallegato'],'uploadallegato')){
				Go_Atti();
				break;
			}
			if (isset($_REQUEST['annulla'])){
				wp_redirect( $location );
			}else{
				$messaggio =addslashes(str_replace(" ","%20",Memo_allegato_atto()));
				if (isset($_REQUEST['ref']))
					$location = add_query_arg(array ( 'action' => $_REQUEST['ref'], 
												  'messaggio' => $messaggio,
												  'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
												  'id' => (int)$_REQUEST['id']) , $location );
				else
					$location = add_query_arg(array ( 'action' => 'allegati-atto', 
				                                  'messaggio' => $messaggio,
												  'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
												  'id' => (int)$_REQUEST['id']) , $location );
			}
			wp_redirect( $location );	
			break;	
		case "update-allegato-atto":
			if (!isset($_REQUEST['modificaallegatoatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce($_REQUEST['modificaallegatoatto'],'editallegatoatto')){
				Go_Atti();
				break;
			}		
			$location='?page=atti&action=allegati-atto&id='.(int)$_REQUEST['id'].'&allegatoatto='.wp_create_nonce('gestallegatiatto');
			if ($_REQUEST['submit']=="Annulla"){
				wp_redirect( $location );
			}else{
				$ret=ap_memo_allegato($_REQUEST['idAlle'],$_REQUEST['titolo'],(int)$_REQUEST['id']);
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
}
}


function Memo_allegato_atto(){
	if ($_REQUEST["operazione"]=="upload"){
		if (!isset($_REQUEST['uploallegato'])) {
			return "ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, operazione annullata";
		}
		if (!wp_verify_nonce($_REQUEST['uploallegato'],'uploadallegato')){
			return "ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, operazione annullata";
		} 		
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
			if (!ap_isAllowedExtension(strtolower($_FILES["file"]["name"]))){
				$messages= "Tipo file non valido, sono ammessi soltanto i file in formato PDF e p7m";
			}else{
				if (($DimFile>(int)ini_get('upload_max_filesize')) and ($UnitM==" MB")){
					$messages= "Il file caricato &egrave; di ".$DimFile." Mb, il limite massimo &egrave; di 20 Mb";
				}else{
				  	if ($_FILES["file"]["error"] > 0){
						$messages= "Errore: " . $_FILES["file"]["error"];
		    		}else{
					$destination_path = AP_BASE_DIR.get_option('opt_AP_FolderUpload').'/';
			   		$result = 0;
				   	$target_path = ap_UniqueFileName($destination_path . basename(sanitize_file_name(remove_accents ( $_FILES['file']['name']))));
					if(@move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
	    				$messages= "File caricato%25%25br%25%25Nome: " . basename( $target_path)." %25%25br%25%25Percorso completo : ".str_replace("\\","/",$target_path);
	    				ap_insert_allegato($_REQUEST['Descrizione'],str_replace("\\","/",$target_path),$_REQUEST['id']);
			   		}else{
						$messages= "Il File non caricato: " .str_replace("\\","/",$target_path)."%25%25br%25%25 Errore:".$_FILES['file']['error'];
						//print($messages);exit;
					}
				}
		  	}
		  }
		}
		$msg=($messages!="") ? ($messages): ""; 
		$msg.=($dime!="") ?   "%25%25br%25%25" .($dime): "";
		$messages=$msg;
	}
//	echo $messages." ".(int)ini_get('upload_max_filesize');die;
	return $messages;
}
function Go_Atti(){
	$location = "?page=atti&p=1" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_redirect( $location );
}
function Go_Enti(){
	$location = "?page=enti" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_redirect( $location );
}
function Go_Categorie(){
	$location = "?page=categorie" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_redirect( $location );
}
function Go_Responsabili(){
	$location = "?page=responsabili" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_redirect( $location );
}
function Go_Utility(){
	$location = "?page=utilityAlboP" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_redirect( $location );
}

?>
