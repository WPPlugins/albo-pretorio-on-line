<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Gestione Permessi
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.8
 */


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
switch($_REQUEST['action']){
//			case "ExportBackupData":
//			in admin.php
//			case "ImportBackupData":
//			in admin.php
	
	case "rip":
		ap_ripubblica_atti_correnti($_GET['Data']);
		menu();
		break;
	case "menu":
		menu(str_replace("%%br%%","<br />",$_GET['stato']));
		unset($_GET['action']);
		break;
	case "creafsic":
		menu(ap_NoIndexNoDirectLink(AP_BASE_DIR.get_option('opt_AP_FolderUpload')));
		unset($_POST['action']);
		break;
	case "BackupData":
		ap_BackupDatiFiles(date('Ymd_H_i_s'));
		$filename=Albo_DIR."/BackupDatiAlbo/tmp/msg.txt";
		$fpmsg = @fopen($filename, "rb");
			$Stato=fread($fpmsg,filesize($filename));
		fclose($fpmsg);
		menu($Stato);
		unset($_POST['action']);
		break;
	case "setData":
		if ($_REQUEST['Data']> date("d/m/Y")){
			$Stato="La Data dell'interruzione del serzio deve essere nel passato";
			menu($Stato);
		}else
			menu("","1",$_REQUEST['Data']);
		break;
	case "verificaproc":
		TestProcedura();
		break;
	case "creaTabella":
		creaTabella($_REQUEST['Tabella']);
		TestProcedura();
		break;
	default:
		menu();
}

function menu($Stato="",$passo="",$Data=""){
echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/utility.png" alt="Icona Permessi" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Utility Albo</h2>';
if ($Stato!="") 
	echo '<div id="message" class="updated"><p>'.str_replace("%%br%%","<br />",$Stato).'</p></div>
      <meta http-equiv="refresh" content="5;url=admin.php?page=utility"/>';
echo '
		<div class="postbox-container" style="margin-top:20px;">
			<div class="widefat" style="padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">Attenzione!!!!!<br />
				Operazione di ripubblicazione degli atti in corso di validit&agrave; a causa di interruzione del servizio di pubblicazione</p>
				<p>Questa operazione Annulla gli atti gi&agrave; pubblicati ed in corso di validit&agrave; con motivazione <span style="font-size:1.1em;font-weight: bold;font-style: italic;color:red;">Annullamento per interruzione del sevizio di pubblicazione</span><br />Ripubblica gli atti in corso di validit&agrave; annullati per un periodo di tempo (n. giorni) uguale a quello degli atti originali</p>
				<p style="font-size:1.1em;font-weight: bold;font-style: italic;color:red;">Questa &egrave; una operazione che pu&ograve; modificare una grosa quantit&agrave; di dati, si consiglia di eseguire un backup prima di procedere, per poter recuperare i dati originali in caso di errori.</p>';
switch ($passo){
	case "":
		echo '<form action="?page=utility" id="ripub" method="post"  class="validate">
				<input type="hidden" name="action" value="setData" />
				Data Interruzione: <input name="Data" id="Calendario1" type="text" size="8" />
				<input type="submit" name="submit" id="submit" class="button" value="Avvia Procedura"  />
				</form>
				';
		break;
	case "1":
		$TotAtti=ap_get_all_atti(1,0,0,'',0,$Data,'',0,0,true,false);
		echo'<p><span style="font-size:1.1em;font-style: italic;color:green;"><strong>'.$TotAtti.'</strong> Atti in pubblicazione in data '.$Data.'.</span> <a href="?page=utility&action=rip&Data='.$_REQUEST['Data'].'" class="ripubblica" rel="'.$TotAtti.'">Ripubblica gli atti a causa dell\' interruzione del servizio</a>?
			</p>';
}
echo '		</div>';
echo '	<p></p>
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
 					<a href="?page=utility&action=verificaproc">Verifica</a>
				</p>
		</div>';
/*echo '	<p></p>
		<div class="widefat" style="padding:10px;">
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
				Diritto all\'Oblio
				</p>
				<p style="text-align:left;font-size:1em;font-weight: bold;">
L\'albo pretorio implementa un sistema che permette di garantire la riservatezza degli allegati agli atti in modo cehe gli stessi possano essere visionati e scaricati solo dal sito che lo ospita ospita.</p>
<p style="text-align:left;font-size:1em;font-style: italic;">In particolare:<br/>
le pagine dell\'albo non vengono indicizzate ed archivate dai motori di ricerca attraverso l\'inserimento del meta tag &lt;meta name=\'robots\' content=\'noindex, nofollow, noarchive\' /&gt;<br />
Nella cartella di Upload degli allegati viene creato il file .htaccess per evitare accessi diretti ai file della cartella<br />
Nella radice del sito viene inserito il file robots.txt con l\'esclusione della cartella di Upload degli allegati da parte dei crawler dei motori di ricerca</p>
				<p style="text-align:center;font-size:1.5em;font-weight: bold;">
					<a href="?page=utility&action=creafsic">Crea/Aggiorna</a>
				</p>
		</div>';
*/
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
				<form action="?page=utility" id="backup" method="post"  class="validate">
					<input type="hidden" name="action" value="BackupData" />
					Backup dei Dati:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" id="submit" class="button" value="Avvia Backup"  />
				</form>
				</p>
				<p>
					<form action="?page=utility" id="exportBackup" method="post"  class="validate">
					Esporta file di Backup: 
						<input type="hidden" name="action" value="ExportBackupData" />
						<select name="elenco_Backup_Expo" id="elenco_Backup_Expo" >\n'
						.$elencoExpo.'
						</select>
						<input type="submit" name="submitExpo" id="submitExpo" class="button" value="Esporta Backup"  />
					</form>
				</p>
			</div>
';	
echo '</div>';
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
										   "Null" =>"NO", 
										   "Key" => "", 
										   "Default" => "", 
										   "Extra" =>""),
					"Oggetto" => array("Tipo" => "varchar(200)", 
									   "Null" =>"NO", 
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
					"Informazioni" => array("Tipo" => "varchar(255)", 
											"Null" =>"NO", 
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
				$Msg.= $campo->Field." Tipo DB ". $campo->Type . " Tipo Originale ".$Par[$campo->Field]["Tipo"]."<br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Null"])!=strtolower($campo->Null)){
				$Msg.= $campo->Field." Null DB ". $campo->Null . " Null Originale ".$Par[$campo->Field]["Null"]."<br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Default"])!=strtolower($campo->Default)){
				$Msg.= $campo->Field." Default DB ". $campo->Default . " Default Originale ".$Par[$campo->Field]["Default"]."<br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Extra"])!=strtolower($campo->Extra)){
				$Msg.= $campo->Field." Extra DB ". $campo->Extra . " Extra Originale ".$Par[$campo->Field]["Extra"]."<br />";
				$Verificato=false;
			}
			if (strtolower($Par[$campo->Field]["Key"])!=strtolower($campo->Key)){
				$Msg.= $campo->Field." Key DB ". $campo->Key . " Key Originale ".$Par[$campo->Field]["Key"]."<br />";
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
				$NAtti=$Natti ? $Natti : 0;
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
echo '<div class="wrap">
	<img src="'.Albo_URL.'/img/utility.png" alt="Icona Permessi" style="display:inline;float:left;margin-top:5px;"/>
		<h2 style="margin-left:40px;">Analisi Procedura</h2>
		<div class="postbox-container" style=";margin-top:20px;">
			<a class="add-new-h2 tornaindietro" href="'.$_SERVER[PHP_SELF].'?page=utility" >
			Torna indietro
			</a>
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
$CartellaUp=str_replace("\\","/",AP_BASE_DIR."/".get_option('opt_AP_FolderUpload'));
$permessi=ap_get_fileperm($CartellaUp);		
if(substr(ap_get_fileperm_Gruppo($CartellaUp,"Proprietario"),1,1)=="w")
 		$StatoCartella='<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;"/>';
	else
		$StatoCartella='<img src="'.Albo_URL.'/img/cross.png" alt="Icona Non Verificato" style="display:inline;float:left;"/>';
 						
echo '				<tr>
						<td>'.$CartellaUp.'</td>
						<td>'.$permessi.'</td>
						<td>'.$StatoCartella.'</td>
					</tbody>
				</table>
			';

echo'		</div>
		<div class="postbox-container" style="margin-top:20px;">
		<h3>Analisi Data Base</h2>
	<div class="widefat" style="width:850px;">
		<table style="width:99%;">
			<thead>
				<tr>
					<th style="text-align:left;">Tabella</th>
					<th style="text-align:left;">Esistenza</th>
					<th style="text-align:left;">Struttura</th>
					<th style="text-align:left;">Analisi dati</th>
				</tr>
			</thead>
			<tbody>
';
foreach($Tabelle as $Tabella){
	$TestCampi="";
	if (existTable($Tabella)) 
 		$EsisteTabella='<img src="'.Albo_URL.'/img/verificato.png" alt="Icona Verificato" style="display:inline;float:left;margin-top:5px;"/>';
	else
		$EsisteTabella='<a href="admin.php?page=utility&action=creaTabella&Tabella='.$Tabella.'">Crea Tabella</a>';
/*switch ($Tabella){
	case 'wp_albopretorio_atti':
	    $n_atti = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti;" ) );	 
	  	$n_atti_dapub = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti Where Numero=0;"));	
	  	$n_atti_attivi = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti Where DataInizio <= now() And DataFine>= now() And Numero>0;"));	
	  	$n_atti_storico=$n_atti-$n_atti_attivi-$n_atti_dapub; 
		$Analisi.='Atti In corso di Validit&agrave;:'.$n_atti_attivi.'<br />';
		$Analisi.='Atti Scaduti:'.$n_atti_storico.'<br />';
		$Analisi.='Atti da Pubblicare:'.$n_atti_dapub.'<br />';
		$TestCampi=TestCampiTabella($Tabella);
		break;
	default;
		$Analisi='Niente';
}*/
$TestCampi=TestCampiTabella($Tabella);
$DatiTabella=TestCongruitaDati($Tabella);
/*
					<td>'.NFieldInTable($Tabella).'</td>
*/
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