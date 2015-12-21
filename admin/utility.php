<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Gestione Permessi
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 3.3
 */


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
$Stato="";
if (isset($_REQUEST['message']))
	if($_REQUEST['message']==80)
		$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l\'operazione &egrave; stata annullata";
switch($_REQUEST['action']){

	case "Crearobots":
		if (!isset($_REQUEST['creasic'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['creasic'],'creasicurezza')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 			ap_crearobots();
		menu();
		break;
	case "rip":
		ap_ripubblica_atti_correnti(htmlentities($_GET['Data']));
		menu();
		break;
	case "menu":
		menu(str_replace("%%br%%","<br />",htmlentities($_GET['stato'])));
		unset($_GET['action']);
		break;
	case "creafsic":
		menu(ap_NoIndexNoDirectLink(AP_BASE_DIR.get_option('opt_AP_FolderUpload')));
		unset($_POST['action']);
		break;
	case "posttrasf":
		if (!isset($_REQUEST['posttrasf'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['posttrasf'],'posttrasferimento')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 			
		$Msg=ap_NoIndexNoDirectLink(AP_BASE_DIR.get_option('opt_AP_FolderUpload'))."<br />";
		$Msg.=ap_allinea_allegati();
		unset($_POST['action']);
		menu($Msg);
		break;
	case "BackupData":
		if (!isset($_REQUEST['bckdata'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['bckdata'],'BackupDatiAlbo')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 			
		$Data=date('Ymd_H_i_s');
		$nf=ap_BackupDatiFiles($Data);
		$filename=Albo_DIR."/BackupDatiAlbo/tmp/msg.txt";
		$fpmsg = @fopen($filename, "rb");
			$Stato=fread($fpmsg,filesize($filename));
		fclose($fpmsg);
		menu($Stato);
		unset($_POST['action']);
		break;
	case "setData":
		if (!isset($_REQUEST['ripub'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['ripub'],'ripubblicaatti')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 	
		if ($_REQUEST['Data']> date("d/m/Y")){
			$Stato="La Data dell'interruzione del serzio deve essere nel passato";
			menu($Stato);
		}else
			menu("","1",$_REQUEST['Data']);
		break;
	case "verificaproc":
		if (!isset($_REQUEST['verproc'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['verproc'],'verificaprocedura')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 			
		TestProcedura();
		break;
	case "oblio":
		MSGOblio();  
		break;		
	case "creaninf":
		if (!isset($_REQUEST['rigenera'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['rigenera'],'rigenerasic')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 		
		ImplementaNINF();
		break;
	case "imploblio":
		ImplementaOblio();
		break;
	case "creaTabella":
		creaTabella(htmlentities($_REQUEST['Tabella']));
		TestProcedura();
		break;
	case "creacategorie":
		CreaCategorie();
		break;
	case "svuotalog":
		if (!isset($_REQUEST['svuotalog'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['svuotalog'],'svuotafilelog')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 		
		$Msg=SvuotaLog(0);
		menu($Msg);
		break;
	case "puliscilog":
		if (!isset($_REQUEST['puliscilog'])) {
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		}
		if (!wp_verify_nonce($_REQUEST['puliscilog'],'puliscifilelog')){
			$Stato="ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione &egrave; stata annullata";
			menu($Stato);
			break;
		} 		
		$Msg=SvuotaLog(11);
		menu($Msg);
		break;
	default:
		menu($Stato);
}
function CreaCategorie(){
echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/utility.png" alt="Icona Permessi" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Creazione Categorie</h2>
		<div class="widefat">
			<table style="width:99%;">
				<thead>
					<tr>
						<th style="text-align:left;width:380px;">Categoria</th>
						<th style="text-align:left;width:100px;">Stato</th>
					</tr>
					</thead>
					<tbody>';
echo AP_CreaCategorieBase().'
					</tbody>
				</thead>
			</table>
		</div>';
}
function SvuotaLog($Tipo){
	$NumRow=ap_svuota_log($Tipo);
	if ($NumRow==0)
		return ("Non sono state cancellate righe dal file di Log");
	else
		return("Log cancellato correttamente, sono state cancellate ".$NumRow." righe");	
}
function MSGOblio(){
echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/utility.png" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Implementazione Oblio</h2>
		<div class="widefat">
			<p>
				Prima di implementare il diritto all\'oblio &egrave; importante fare un BACKUP dei seguenti elementi:
				<ul>
					<li>Tabelle del Data Base relative all\'Albo</li>
					<li>Files allegati agli atti</li>
				</ul>  
			per proseguire ed attivare il diritto all\'oblio, clicca su <a href="?page=utilityAlboP&amp;action=imploblio" class="add-new-h2 tornaindietro">Prosegui</a> altrimenti <a href="'.home_url().'/wp-admin/admin.php?page=Albo_Pretorio" class="add-new-h2 tornaindietro">Torna indietro</a>
			</p>
		</div>';	
}
function ImplementaNINF(){
	$newPathAllegati=AP_BASE_DIR."AllegatiAttiAlboPretorio";
	ap_NoIndexNoDirectLink($newPathAllegati);
	echo'<div id="message" class="updated"> 
				<p><strong>File .htaccess e index.php necessari per il diritto all\'oblio sono stati ricreati.</strong></p>
				<p>Operazione terminata&nbsp;&nbsp;
				<a href="'.home_url().'/wp-admin/admin.php?page=Albo_Pretorio" class="add-new-h2 tornaindietro">Torna indietro</a>
				</p>
				</div>';
}
function ImplementaOblio(){
	$uploads = wp_upload_dir(); 
	$oldPathAllegati=substr($uploads['basedir'],0,strpos($uploads['basedir'],"wp-content", 0)).get_option('opt_AP_FolderUpload');
	$newPathAllegati=AP_BASE_DIR."AllegatiAttiAlboPretorio";
	$tmpdir=str_replace("\\","/",$oldPathAllegati);
	$posizione=stripos($tmpdir,$uploads['basedir']);	
	if (($posizione==0) and (strlen($tmpdir)>strlen($uploads['basedir'])))
		$elimina=TRUE;
	else
		$elimina=FALSE;
	if(!is_dir($newPathAllegati)){   
		mkdir($newPathAllegati, 0755);
	}
	update_option('opt_AP_FolderUpload',"AllegatiAttiAlboPretorio");
//	echo $uploads['basedir']."<br />".$oldPathAllegati."! <br />".$newPathAllegati."!<br />";
	if($oldPathAllegati!=$newPathAllegati)
		ap_sposta_allegati($oldPathAllegati,$elimina);	
	else
		ap_NoIndexNoDirectLink($newPathAllegati);
	$nomeFile=Albo_DIR."/BackupDatiAlbo/tmp/msg.txt";
	$fpmsg = @fopen($nomeFile, "r");
	if ($fpmsg) {
		$contenuto=fread($fpmsg,filesize($nomeFile));
		$contenuto=nl2br($contenuto);
		fclose($fpmsg);
		echo'<div id="message" class="updated"> 
				<p><strong>Impostazioni salvate.</strong></p>
				<p><strong>'.str_replace("%%br%%", "<br />", $contenuto).'</strong></p>
				<p>Operazione terminata&nbsp;&nbsp;
				<a href="'.home_url().'/wp-admin/admin.php?page=Albo_Pretorio" class="add-new-h2 tornaindietro">Torna indietro</a>
				</p>
				</div>';
	}else{
		echo'<div id="message" class="updated"> 
				<p><strong>Impostazioni salvate.</strong></p>
				<p>Operazione terminata&nbsp;&nbsp;
				<a href="'.home_url().'/wp-admin/admin.php?page=Albo_Pretorio" class="add-new-h2 tornaindietro">Torna indietro</a>
				</p>
				</div>';
	}
}

function menu($Stato="",$passo="",$Data=""){
global $wpdb;
$upload_dir = wp_upload_dir();
$basedir=substr( $upload_dir['basedir'],0,strlen($upload_dir['basedir'])-19);
echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/utility.png" alt="Icona Permessi" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Utility Albo</h2>';
if ($Stato!="") 
	echo '<div id="message" class="updated"><p>'.str_replace("%%br%%","<br />",$Stato).'</p></div>
      <meta http-equiv="refresh" content="5;url=admin.php?page=utilityAlboP"/>';
echo '
		<div class="postbox-container" style="margin-top:20px;">
			<div class="widefat" style="padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">Attenzione!!!!!<br />
				Operazione di ripubblicazione degli atti in corso di validit&agrave; a causa di interruzione del servizio di pubblicazione</p>
				<p>Questa operazione Annulla gli atti gi&agrave; pubblicati ed in corso di validit&agrave; con motivazione <span style="font-size:1.1em;font-weight: bold;font-style: italic;color:red;">Annullamento per interruzione del sevizio di pubblicazione</span><br />Ripubblica gli atti in corso di validit&agrave; annullati per un periodo di tempo (n. giorni) uguale a quello degli atti originali</p>
				<p style="font-size:1.1em;font-weight: bold;font-style: italic;color:red;">Questa &egrave; una operazione che pu&ograve; modificare una grosa quantit&agrave; di dati, si consiglia di eseguire un backup prima di procedere, per poter recuperare i dati originali in caso di errori.</p>';
switch ($passo){
	case "":
		echo '<form action="?page=utilityAlboP" id="ripub" method="post"  class="validate">
				<input type="hidden" name="action" value="setData" />
				<input type="hidden" name="ripub" value="'.wp_create_nonce('ripubblicaatti').'" />
				Data Interruzione: <input name="Data" id="Calendario1" type="text" size="8" />
				<input type="submit" name="submit" id="submit" class="button" value="Avvia Procedura"  />
				</form>
				';
		break;
	case "1":
		$TotAtti=ap_get_all_atti(1,0,0,'',0,$Data,'',0,0,true,false);
		echo'<p><span style="font-size:1.1em;font-style: italic;color:green;"><strong>'.$TotAtti.'</strong> Atti in pubblicazione in data '.$Data.'.</span> <a href="?page=utilityAlboP&action=rip&Data='.$_REQUEST['Data'].'" class="ripubblica" rel="'.$TotAtti.'">Ripubblica gli atti a causa dell\' interruzione del servizio</a>?
			</p>';
}
echo '		</div> 
		<p></p>
		<div class="widefat" style="padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
				Verifica procedura
				</p>
				<p style="text-align:left;font-size:1em;font-style: italic;">
Questa procedura esegue un test generale della procedura e riporta eventuali anomalie nei dati e nelle impostazioni.</spam><br />Operazioni eseguite:</p>
<p style="font-size:1em;font-style: italic;margin-left:10px;font-weight: bold;">
	Verifica permessi cartella di Upload degli allegati
	<br />Verifica dati del Data Base e viene riportata una breve statistica sui dati
</p>
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
 					<a href="?page=utilityAlboP&action=verificaproc&amp;verproc='.wp_create_nonce('verificaprocedura').'">Verifica</a>
				</p>
		</div>
				<div class="widefat" style="padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
				Procedura post trasferimento sito
				</p>
				<p style="text-align:left;font-size:1em;font-style: italic;">
Questa procedura esegue le seguenti operazioni:
					<ul style="list-style: circle inside;">
						<li>Aggiornamento dei file 
							<ul style="list-style: disc inside;padding-left:10px;">
								<li><span style="font-weight: bold;">.htaccess</span> e <span style="font-weight: bold;">index.php</span> nella cartella <span style="font-style: italic;font-weight: bold;"> '.AP_BASE_DIR.'AllegatiAttiAlboPretorio</span></li>
								<li> <span style="font-weight: bold;">robots.txt</span> nella cartella <span style="font-style: italic;font-weight: bold;">'.$basedir.'</span></li>
							</ul>
							 per il diritto all\'oblio</li>
						<li>Aggiornamento del percorso nella tabella degli allegati nel Data Base</li>
					</ul>
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
 					<a href="?page=utilityAlboP&action=posttrasf&amp;posttrasf='.wp_create_nonce('posttrasferimento').'">Avvia operazione</a>
				</p>
		</div>
		
		
		<div class="widefat" style="padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
				Procedura pulitura file di Log
				</p>
				<p style="font-style: italic;font-weight: bold;">Queste procedure possono cancellare una grossa quantit&agrave; di dati, se non si vuole perderli si consiglia di fare un backup del DataBase o della tabella <span style="font-style: normal;">'.$wpdb->table_name_Log.'</span>
				</p>
					<ul style="list-style: none;">
						<li>Questa procedura cancella tutte le registrazioni presenti nel file di log&nbsp;&nbsp;
							<span style="font-size:1.5em;font-weight: bold;">
								<a href="?page=utilityAlboP&action=svuotalog&amp;svuotalog='.wp_create_nonce('svuotafilelog').'">Svuota file di Log</a>
							</span>
						</li>
						<li>Questa procedura cancella tutte le registrazioni di gestione dal file di log mantenendo le statistiche di accesso
							<span style="font-size:1.5em;font-weight: bold;">
								<a href="?page=utilityAlboP&action=puliscilog&amp;puliscilog='.wp_create_nonce('puliscifilelog').'">Pulisci file di Log</a>
								  </span>
						</li>
					</ul>
		</div>';
$elenco="<option value='' selected='selected'>Nessuno</option>";
$elencoExpo="";
$Dir=str_replace("\\","/",Albo_DIR.'/BackupDatiAlbo');
if (is_dir($Dir)){
	$files_bck = scandir($Dir, 1);
	foreach($files_bck as $fileinfo) {
		if (is_file($Dir."/".$fileinfo)) {
				$elenco.="<option value='".$Dir."/".$fileinfo."'>".$fileinfo."</option>"; 
				$elencoExpo.="<option value='".$Dir."/".$fileinfo."'>".$fileinfo."</option>"; 
		}
	}
}
echo '
		<p></p>
		<div class="widefat" style="margin-top:20px;padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
					Procedura di Backup dei dati dell\'Albo Pretorio
				</p>
				<p>
				<form action="?page=utilityAlboP" id="backup" method="post"  class="validate">
					<input type="hidden" name="action" value="BackupData" />
					<input type="hidden" name="bckdata" value="'.wp_create_nonce('BackupDatiAlbo').'" />
					Backup dei Dati:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" id="submit" class="button" value="Avvia Backup"  />
				</form>
				</p>
				<p>
					<form action="?page=utilityAlboP" id="exportBackup" method="post"  class="validate">
					Esporta file di Backup: 
						<input type="hidden" name="action" value="ExportBackupData" />
						<input type="hidden" name="exportbckdata" value="'.wp_create_nonce('EsportaBackupDatiAlbo').'" />
						<select name="elenco_Backup_Expo" id="elenco_Backup_Expo" >\n'
						.$elencoExpo.'
						</select>
						<input type="submit" name="submitExpo" id="submitExpo" class="button" value="Esporta Backup"  />
					</form>
				</p>
				
			</div>
	</div>';
}
	
function TestCampiTabella($Tabella,$Ripara=false){
	global $wpdb;
switch ($Tabella){
	case $wpdb->table_name_Atti:
		$Par=array("IdAtto" => array("Tipo" => "int(11)",
								     "Null" =>"NO",
									 "Key" => "PRI",
									 "Default" => "",
									 "Extra" =>"auto_increment"),
			  		"Numero" => array("Tipo" => "int(4)",
					  				  "Null" =>"NO",
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""),
					"Anno" => array("Tipo" => "int(4)", 
									"Null" =>"NO", 
									"Key" => "", 
									"Default" => "0", 
									"Extra" =>""),
					"Data" => array("Tipo" => "date", 
									"Null" =>"NO", 
									"Key" => "", 
									"Default" => "0000-00-00", 
									"Extra" =>""),
					"Riferimento" => array("Tipo" => "varchar(100)", 
										   "Null" =>"No", 
										   "Key" => "", 
										   "Default" => "", 
										   "Extra" =>""),
					"Oggetto" => array("Tipo" => "varchar(200)", 
									   "Null" =>"No", 
									   "Key" => "", 
									   "Default" => "", 
									   "Extra" =>""),
					"DataInizio" => array("Tipo" => "date", 
										  "Null" =>"NO", 
										  "Key" => "", 
										  "Default" => "0000-00-00", 
										  "Extra" =>""),
					"DataFine" => array("Tipo" => "date", 
										"Null" =>"YES", 
										"Key" => "", 
										"Default" => "0000-00-00", 
										"Extra" =>""),
					"Informazioni" => array("Tipo" => "text", 
											"Null" =>"YES", 
											"Key" => "", 
											"Default" => "", 
											"Extra" =>""),
					"IdCategoria" => array("Tipo" => "int(11)", 
										   "Null" =>"NO", 
										   "Key" => "", 
										   "Default" => "0", 
										   "Extra" =>""),
					"RespProc" => array("Tipo" => "int(11)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"DataAnnullamento" => array("Tipo" => "date", 
												"Null" =>"YES", 
												"Key" => "", 
												"Default" => "0000-00-00", 
												"Extra" =>""),
					"MotivoAnnullamento" => array("Tipo" => "varchar(200)", 
												  "Null" =>"YES", 
												  "Key" => "", 
												  "Default" => "", 
												  "Extra" =>""),
					"Ente" => array("Tipo" => "int(11)",
									"Null" =>"NO", 
									"Key" => "", 
									"Default" => "0", 
									"Extra" =>""));
		break;
	case $wpdb->table_name_Allegati:
		$Par=array("IdAllegato" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "TitoloAllegato" => array("Tipo" => "varchar(255)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Allegato" => array("Tipo" => "varchar(255)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"IdAtto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_Categorie:
		$Par=array("IdCategoria" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "Nome" => array("Tipo" => "varchar(255)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Descrizione" => array("Tipo" => "varchar(255)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Genitore" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""),
					"Giorni" => array("Tipo" => "smallint(3)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_Log:
		$Par=array("Data" => array("Tipo" => "timestamp", 
										 "Null" =>"NO", 
										 "Key" => "", 
										 "Default" => "CURRENT_TIMESTAMP", 
										 "Extra" =>""),
		  		   "Utente" => array("Tipo" => "varchar(60)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"IPAddress" => array("Tipo" => "varchar(16)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Oggetto" => array("Tipo" => "int(1)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"IdOggetto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"IdOggetto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"IdAtto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""),
					"TipoOperazione" => array("Tipo" => "int(1)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"Operazione" => array("Tipo" => "text", 
									  "Null" =>"Yes", 
									  "Key" => "", 
									  "Default" => "", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_RespProc:
		$Par=array("IdResponsabile" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "Cognome" => array("Tipo" => "varchar(20)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
		  		   "Nome" => array("Tipo" => "varchar(20)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Email" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Telefono" => array("Tipo" => "varchar(30)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Orario" => array("Tipo" => "varchar(60)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Note" => array("Tipo" => "text", 
									  "Null" =>"YES", 
									  "Key" => "", 
									  "Default" => "", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_Enti:
		$Par=array("IdEnte" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "Nome" => array("Tipo" => "varchar(100)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
		  		   "Indirizzo" => array("Tipo" => "varchar(150)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Url" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Email" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Pec" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Telefono" => array("Tipo" => "varchar(40)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Fax" => array("Tipo" => "varchar(40)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Note" => array("Tipo" => "text", 
									  "Null" =>"Yes", 
									  "Key" => "", 
									  "Default" => "", 
									  "Extra" =>""));
		break;
}
        $wpdb->flush();
        $result=$wpdb->get_results("Describe $Tabella");
        $Verificato=true;
        $Msg="";
		foreach ( $result as $campo ){
			if (strtolower($Par[$campo->Field]["Tipo"])!=strtolower($campo->Type)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Tipo DB <strong>". $campo->Type . "</strong><br />&nbsp;&nbsp;&nbsp;Tipo Originale <strong>".$Par[$campo->Field]["Tipo"]."</strong><br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Null"])!=strtolower($campo->Null)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Null DB <strong>". $campo->Null . "</strong><br />&nbsp;&nbsp;&nbsp;Null Originale <strong>".$Par[$campo->Field]["Null"]."</strong><br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Default"])!=strtolower($campo->Default)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Default DB <strong>". $campo->Default . "</strong><br />&nbsp;&nbsp;&nbsp;Default Originale <strong>".$Par[$campo->Field]["Default"]."</strong><br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Extra"])!=strtolower($campo->Extra)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Extra DB <strong>". $campo->Extra . "</strong><br />&nbsp;&nbsp;&nbsp;Extra Originale <strong>".$Par[$campo->Field]["Extra"]."</strong><br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Key"])!=strtolower($campo->Key)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Key DB <strong>". $campo->Key . "</strong><br />&nbsp;&nbsp;&nbsp;Key Originale <strong>".$Par[$campo->Field]["Key"]."</strong><br />";
				$Verificato=false;
			}
		}
		if ($Verificato == True)
			$Msg.= '<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;margin-top:5px;"/>';
		return $Msg;
}
function TestCongruitaDati($Tabella){
global $wpdb;
	switch ($Tabella){
		case $wpdb->table_name_Atti:
		    $n_atti = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti;");	 
		  	$n_atti_dapub = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti Where Numero=0;");	
		  	$n_atti_attivi = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti Where DataInizio <= now() And DataFine>= now() And Numero>0;");	
		  	$n_atti_storico=$n_atti-$n_atti_attivi-$n_atti_dapub; 
			$Analisi.='<em>Atti In corso di Validit&agrave;:</em><strong>'.$n_atti_attivi.'</strong><br />';
			$Analisi.='<em>Atti Scaduti:</em><strong>'.$n_atti_storico.'</strong><br />';
			$Analisi.='<em>Atti da Pubblicare:</em><strong>'.$n_atti_dapub.'</strong><br />';
			// Verifica Atti con Categorie Orfane
			$CategorieOrfane=ap_categorie_orfane();
			if ($CategorieOrfane){
				foreach ($CategorieOrfane as $CategoriaOrfana){
					$Analisi.="<em>Atto N. </em><strong>".$CategoriaOrfana->Numero."/".$CategoriaOrfana->Anno."</strong> <em>riporta la Categoria con Codice </em><strong>".$CategoriaOrfana->IdCategoria."</strong> <em>NON TROVATA nella tabella Categorie <br />";
				}
			}
			$EntiOrfani=ap_enti_orfani();
			if ($EntiOrfani){
				foreach ($EntiOrfani as $EnteOrfano){
					$Analisi.="<em>Atto N. </em><strong>".$EnteOrfano->Numero."/".$EnteOrfano->Anno."</strong> <em>riporta l'ente con Codice </em><strong>".$EnteOrfano->Ente."</strong> <em>NON TROVATO nella tabella Enti <br />";
				}
			}
			$ResponsabiliOrfani=ap_responsabili_orfani();
			if ($ResponsabiliOrfani){
				foreach ($ResponsabiliOrfani as $ResponsabileOrfan0){
					$Analisi.="<em>Atto N. </em><strong>".$ResponsabileOrfan0->Numero."/".$ResponsabileOrfan0->Anno."</strong> <em>riporta il responsabile con Codice </em><strong>".$ResponsabileOrfan0->RespProc."</strong> <em>NON TROVATO nella tabella Responsabili <br />";
				}
			}
			return $Analisi;
			break;
		case $wpdb->table_name_Allegati:
			$NumAllegati=ap_num_allegati();
			$AllegatiOrfani=ap_allegati_orfani();
			$Analisi= "<em>N. Allegati </em><strong>".$NumAllegati."</strong> <em>di cui orfani</em><strong> ".count($AllegatiOrfani)."</strong>";
			if (count($AllegatiOrfani)>0)
				$Analisi.="<br /><strong>Allegati Orfani</strong><br />";
			foreach ($AllegatiOrfani as $AllegatoOrfano){
				$Analisi.="<em>Allegato </em><strong>".$AllegatoOrfano->TitoloAllegato."</strong> <em>Associato all'Atto con id n.</em><strong> ".$AllegatoOrfano->IdAtto."</strong> <br />";
			}
			return $Analisi;
			break;
		case $wpdb->table_name_Categorie:
			$NumCategorie=ap_num_categorie();
			$NumCategorieInutilizzate=ap_num_categorie_inutilizzate();
			$Categorie=ap_get_categorie();
			$UsoCategorie="";
			foreach ($Categorie as $Categoria){
				$NCategorie=ap_num_categoria_atto($Categoria->IdCategoria);
				$NCategorie=$NCategorie ? $NCategorie : 0;
				$UsoCategorie.="<em>".$Categoria->Nome." Presente in </em><strong>".$NCategorie ."</strong> <em>Atti</em><br />";	
			}
			return "<em>Categorie codificate </em><strong>".$NumCategorie."</strong> <em>di cui inutilizzate</em><strong> ".$NumCategorieInutilizzate."</strong> <br />".$UsoCategorie; 
			break;
		case $wpdb->table_name_Log:
			$LogPerOggetti=ap_get_Stat_Log("Oggetto");
			$Statistiche="<strong>Numero record per Oggetto</strong><br />";
			foreach ($LogPerOggetti as $LogPerOggetto){
				$Statistiche.="<em>".$LogPerOggetto->NomeOggetto." => </em><strong>".$LogPerOggetto->Numero ."</strong><br />";	
			}
			$LogPerTipoOperazioni=ap_get_Stat_Log("TipoOperazione");
			$Statistiche.="<strong>Numero record per Tipo Operazione</strong><br />";
			foreach ($LogPerTipoOperazioni as $LogPerTipoOperazione){
				$Statistiche.="<em>".$LogPerTipoOperazione->NomeTipoOperazione." => </em><strong>".$LogPerTipoOperazione->Numero ."</strong><br />";	
			}
			return $Statistiche;
			break;
		case $wpdb->table_name_RespProc:
			$NumResp=ap_num_responsabili();
			$NumResponsabiliInutilizzate=ap_num_responsabili_inutilizzati();
			$Responsabili=ap_get_responsabili();
			$UsoResponsabili="";
			foreach ($Responsabili as $Responsabile){
				$NResponsabile=ap_num_responsabili_atto($Responsabile->IdResponsabile);
				$NResponsabile=$NResponsabile ? $NResponsabile : 0;
				$UsoResponsabili.="<em>".$Responsabile->Cognome." ".$Responsabile->Nome." Presente in </em><strong>".$NResponsabile ."</strong> <em>Atti</em><br />";	
			}
			return "<em>Responsabili codificati </em><strong>".$NumResp."</strong> <em>di cui inutilizzati</em><strong> ".$NumResponsabiliInutilizzate."</strong> <br />".$UsoResponsabili;
			break;
		case $wpdb->table_name_Enti:
			$NumEnti=ap_num_enti();
			$NumEntiInutilizzati=ap_num_enti_Inutilizzati();
			$Enti=ap_get_enti();
			$UsoEnti="";
			foreach ($Enti as $Ente){
				$NAtti=ap_num_enti_atto($Ente->IdEnte);
				$NAtti=$NAtti ? $NAtti : 0;
				$UsoEnti.="<em>".$Ente->Nome." Presente in </em><strong>".$NAtti ."</strong> <em>Atti</em><br />";	
			}
			return "<em>Enti codificati </em><strong>".$NumEnti."</strong> <em>di cui inutilizzati</em><strong> ".$NumEntiInutilizzati."</strong> <br />".$UsoEnti; 
			break;
	}	
}


function TestProcedura(){
	global $wpdb;
$Tabelle=array($wpdb->table_name_Atti,
			   $wpdb->table_name_Categorie,
			   $wpdb->table_name_Allegati,
			   $wpdb->table_name_Log,
			   $wpdb->table_name_RespProc,
			   $wpdb->table_name_Enti);
if(is_file(AP_BASE_DIR.get_option('opt_AP_FolderUpload')."/.htaccess"))
	$ob1=TRUE;
else	
	$ob1=FALSE;
if(is_file(AP_BASE_DIR.get_option('opt_AP_FolderUpload')."/index.php"))
	$ob2=TRUE;
else	
	$ob2=FALSE;
if(is_file(APHomePath."/robots.txt"))
	$ob3=TRUE;
else	
	$ob3=FALSE;
echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/utility.png" alt="Icona Permessi" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Analisi Procedura</h2>
		<div class="postbox-container" style=";margin-top:20px;">
			<a class="add-new-h2 tornaindietro" href="'.htmlentities($_SERVER[PHP_SELF]).'?page=utilityAlboP" >
			Torna indietro
			</a>
		<h3>Librerie</h3>
			<div class="widefat">
				<table style="width:99%;">
					<thead>
						<tr>
							<th style="text-align:left;width:200px;">Libreria</th>
							<th style="text-align:left;width:50px;">Stato</th>
							<th style="text-align:left;width:230px;">Note</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>PclZip</td>
							<td>';
if (is_file(Albo_DIR.'/inc/pclzip.php')) 
 		echo'<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;"/></td><td>--</td>';
	else
		echo'<img src="'.Albo_URL.'/img/cross.png" alt="Icona Non Verificato" style="display:inline;float:left;"/></td>
		<td>Senza questa libreria non puoi eseguire i Backup</td>';							
echo '							
						</tr>
					</tbody>
				</table>
		</div>						
		<h3>Diritto all\'OBLIO</h3>
			<div class="widefat">
				<table style="width:99%;">
					<thead>
						<tr>
							<th style="text-align:left;width:440px;">Cartella</th>
							<th style="text-align:left;width:80px;">.htaccess</th>
							<th style="text-align:left;width:80px;">index.php</th>
							<th style="text-align:left;width:80px;">robots.txt</th>
							<th style="text-align:left;width:100px;">Operazione</th>';
echo'
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>'.AP_BASE_DIR.get_option('opt_AP_FolderUpload').'</td>
							<td>';
if($ob1)
 		echo'<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;"/>';
	else
		echo'<img src="'.Albo_URL.'/img/cross.png" alt="Icona Non Verificato" style="display:inline;float:left;"/>';							
echo '							</td>
							<td>';
if($ob2)
 		echo'<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;"/>';
	else
		echo'<img src="'.Albo_URL.'/img/cross.png" alt="Icona Non Verificato" style="display:inline;float:left;"/>';							
echo '							</td>
			<td></td>';
//if (!$ob1 or !$ob2)
echo '							<td><a href="?page=utilityAlboP&amp;action=creaninf&amp;rigenera='.wp_create_nonce('rigenerasic').'">Rigenera</a></td>';
echo '
						</tr>
						<tr>
							<td>'.APHomePath.'</td>
							<td></td>
							<td></td>
							<td>';
if($ob3)
 		echo'<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;"/>';
	else
		echo'<img src="'.Albo_URL.'/img/cross.png" alt="Icona Non Verificato" style="display:inline;float:left;"/>';							
echo '							</td>';
//if (!$ob3)
echo '							<td><a href="?page=utilityAlboP&amp;action=Crearobots&amp;creasic='.wp_create_nonce('creasicurezza').'">Crea</a></td>';
echo '
						</tr>
					</tbody>
				</table>
		</div>			
		<h3>Permessi Cartella Upload</h3>
			<div class="widefat">
				<table style="width:99%;">
					<thead>
						<tr>
							<th style="text-align:left;width:380px;">Cartella</th>
							<th style="text-align:left;width:100px;">Permessi</th>
							<th style="text-align:left;width:100px;">Stato</th>
						</tr>
					</thead>
					<tbody>';
$CartellaUp=str_replace("\\","/",AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
$permessi=ap_get_fileperm($CartellaUp);		
if(substr(ap_get_fileperm_Gruppo($CartellaUp,"Proprietario"),1,1)=="w")
 		$StatoCartella='<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;"/>';
	else
		$StatoCartella='<img src="'.Albo_URL.'/img/cross.png" alt="Icona Non Verificato" style="display:inline;float:left;"/>';
 						
echo '				<tr>
						<td>'.$CartellaUp.'</td>
						<td>'.$permessi.'</td>
						<td>'.$StatoCartella.'</td>
					</tr>
					</tbody>
				</table>
		</div>
		<div class="postbox-container" style="margin-top:20px;">
		<h3>Analisi Data Base</h2>
	<div class="widefat">
		<table style="width:99%;">
			<thead>
				<tr>
					<th style="text-align:left;width:15%;">Tabella</th>
					<th style="text-align:left;width:10%;">Esistenza</th>
					<th style="text-align:left;width:25%;">Struttura</th>
					<th style="text-align:left;width:50%;">Analisi dati</th>
				</tr>
			</thead>
			<tbody>
';
foreach($Tabelle as $Tabella){
	$TestCampi="";
	if (ap_existTable($Tabella)) 
 		$EsisteTabella='<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;margin-top:5px;"/>';
	else
		$EsisteTabella='<a href="admin.php?page=utilityAlboP&action=creaTabella&Tabella='.$Tabella.'">Crea Tabella</a>';

$TestCampi=TestCampiTabella($Tabella);
$DatiTabella=TestCongruitaDati($Tabella);
	echo'
					<tr class="first">
					<td>'.$Tabella.'</td>
					<td>'.$EsisteTabella.'</td>
					<td>'.$TestCampi.'</td>
					<td>'.$DatiTabella.'</td>
				</tr>
		';
	
}
echo'
			</tbody>
		</table>
	</div>
</div>';
}			
?>