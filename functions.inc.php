<?php
/*
* Plugin URI: http://www.sisviluppo.info
* Description: Widget utilizzato per la pubblicazione degli atti da inserire nell'albo pretorio dell'ente.
* Version:3.0.9
* Author: Scimone Ignazio
* Author URI: http://www.sisviluppo.info
*/
 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

################################################################################
// Funzioni 
################################################################################
function ap_get_fileperm($dir){
	$perms = fileperms($dir);
	if (($perms & 0xC000) == 0xC000) {
	    // Socket
	    $info = 's';
	} elseif (($perms & 0xA000) == 0xA000) {
	    // Symbolic Link
	    $info = 'l';
	} elseif (($perms & 0x8000) == 0x8000) {
	    // Regular
	    $info = '-';
	} elseif (($perms & 0x6000) == 0x6000) {
	    // Block special
	    $info = 'b';
	} elseif (($perms & 0x4000) == 0x4000) {
	    // Directory
	    $info = 'd';
	} elseif (($perms & 0x2000) == 0x2000) {
	    // Character special
	    $info = 'c';
	} elseif (($perms & 0x1000) == 0x1000) {
	    // FIFO pipe
	    $info = 'p';
	} else {
	    // Unknown
	    $info = 'u';
	}

	// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ?
	            (($perms & 0x0800) ? 's' : 'x' ) :
	            (($perms & 0x0800) ? 'S' : '-'));

	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ?
	            (($perms & 0x0400) ? 's' : 'x' ) :
	            (($perms & 0x0400) ? 'S' : '-'));

	// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ?
	            (($perms & 0x0200) ? 't' : 'x' ) :
	            (($perms & 0x0200) ? 'T' : '-'));
	
	return $info;
}
function ap_is_dir_empty($dir){
	$files=@scandir($dir);
	if ( count($files) > 2 )
		return FALSE;
	else
		return TRUE;
}
function ap_get_fileperm_Gruppo($dir,$Gruppo){
		 
	$perms = fileperms($dir);

	switch($Gruppo){
		case "Proprietario":
			$info = (($perms & 0x0100) ? 'r' : '-');
			$info .= (($perms & 0x0080) ? 'w' : '-');
			$info .= (($perms & 0x0040) ?
			         (($perms & 0x0800) ? 's' : 'x' ) :
			         (($perms & 0x0800) ? 'S' : '-'));
			break;
		case "Gruppo":
			$info  = (($perms & 0x0020) ? 'r' : '-');
			$info .= (($perms & 0x0010) ? 'w' : '-');
			$info .= (($perms & 0x0008) ?
			            (($perms & 0x0400) ? 's' : 'x' ) :
			            (($perms & 0x0400) ? 'S' : '-'));
			break;
		case "Altri":
			$info  = (($perms & 0x0004) ? 'r' : '-');
			$info .= (($perms & 0x0002) ? 'w' : '-');
			$info .= (($perms & 0x0001) ?
			            (($perms & 0x0200) ? 't' : 'x' ) :
			            (($perms & 0x0200) ? 'T' : '-'));
			break;
	}
	return $info;
}

function ap_crearobots(){
	$cartellabase=str_replace("\\","/",AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
	$cartella=substr($cartellabase,strlen(APHomePath));
	$robot="User-agent: *
	Disallow: ".$cartella."/";
	$id = fopen(APHomePath."/robots.txt", "wt");
	if (!fwrite($id,$robot )){
		$Stato.="Non risco a Creare il file robots.txt in ".APHomePath."%%br%%";
	}else{
		$Stato.="File robots.txt creato con successo in ".APHomePath."%%br%%";
	}
	fclose($id);
	return $Stato;
}
function ap_NoIndexNoDirectLink($dir){
    $sito=$_SERVER[HTTP_HOST];
	$Stato="";
	if (substr($sito,0,4)=="www.")
		$sito=substr($sito,4);
	$htaccess="#Blocco Accesso diretto Allegati Albo Pretorio
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{HTTP_REFERER} !^http://www.".$sito.".* [NC]
	RewriteCond %{HTTP_REFERER} !^http://".$sito.".* [NC]
	RewriteRule \. ".home_url()."/index.php [R,L]
</IfModule>";
$index="<?php
/**
 * Albo Pretorio AdminPanel - Gestione Allegati Atto
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.4
 */

die('Non hai il permesso di accedere a questa risorsa');
?>";
//Creazione .htaccess
	$id = fopen($dir."/.htaccess", "wt");
	if (!fwrite($id,$htaccess )){
		$Stato.="Non risco a Creare il file .htaccess in ".$dir."%%br%%";
	}else{
		$Stato.="File .htaccess creato con successo in ".$dir."%%br%%";
	}
	fclose($id);
//Creazione robots.txt
	$Stato.=ap_crearobots();
//Creazione index.php
	$id = fopen($dir."/index.php", "wt");
	if (!fwrite($id,$index )){
		$Stato.="Non risco a Creare il file index.php in ".$dir;
	}else{
		$Stato.="File index.php creato con successo in ".$dir;
	}
	fclose($id);
	return $Stato;
}



function ap_Formato_Dimensione_File($a_bytes)
{
    if ($a_bytes < 1024) {
        return $a_bytes .' Byte';
    } elseif ($a_bytes < 1048576) {
        return round($a_bytes / 1024) .' KB';
    } elseif ($a_bytes < 1073741824) {
        return round($a_bytes / 1048576) . ' MB';
    } elseif ($a_bytes < 1099511627776) {
        return round($a_bytes / 1073741824) . ' GB';
    } elseif ($a_bytes < 1125899906842624) {
        return round($a_bytes / 1099511627776) .' TB';
    } elseif ($a_bytes < 1152921504606846976) {
        return round($a_bytes / 1125899906842624) .' PB';
    } elseif ($a_bytes < 1180591620717411303424) {
        return round($a_bytes / 1152921504606846976) .' EB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        return round($a_bytes / 1180591620717411303424) .' ZB';
    } else {
        return round($a_bytes / 1208925819614629174706176) .' YB';
    }
}

################################################################################
// Funzioni DataBase
################################################################################

function AP_CreaCategoriaBase($CatNome,$Des,$Durata){
	$ret=ap_insert_categoria($CatNome,0,$Des,$Durata);
	$Risultato ='
	<tr>
		<td>'.$CatNome.'</td>
		<td>';
	if ( !$ret && !is_wp_error( $ret ) )
		$Risultato .='<img src="'.Albo_URL.'/img/verificato.png" style="display:inline;float:left;"/>';
	else
		echo'<img src="'.Albo_URL.'/img/cross.png" style="display:inline;float:left;"/>';
	$Risultato .='		</td>
	</tr>';
	return $Risultato;
}

function AP_CreaCategorieBase(){
	$Risultato=AP_CreaCategoriaBase("Bandi e gare","Bandi e gare",30);
	$Risultato.=AP_CreaCategoriaBase("Contratti - Personale ATA","Contratti - Personale ATA",30);
	$Risultato.=AP_CreaCategoriaBase("Contratti - Personale Docente","Contratti - Personale Docente",30);
	$Risultato.=AP_CreaCategoriaBase("Contratti e convenzioni","Contratti e convenzioni",30);
	$Risultato.=AP_CreaCategoriaBase("Convocazioni","Convocazioni",30);
	$Risultato.=AP_CreaCategoriaBase("Delibere Consiglio di Istituto","Delibere Consiglio di Istituto",30);
	$Risultato.=AP_CreaCategoriaBase("Documenti altre P.A.","Documenti altre P.A.",30);
	$Risultato.=AP_CreaCategoriaBase("Esiti esami","Esiti esami",30);
	$Risultato.=AP_CreaCategoriaBase("Graduatorie","Graduatorie",365);
	$Risultato.=AP_CreaCategoriaBase("Organi collegiali","Organi collegiali",30);
	$Risultato.=AP_CreaCategoriaBase("Organi collegiali - Elezioni","Organi collegiali - Elezioni",30);
	$Risultato.=AP_CreaCategoriaBase("Privacy","Privacy",365);
	$Risultato.=AP_CreaCategoriaBase("Programmi annuali e Consuntivi","Programmi annuali e Consuntivi",365);
	$Risultato.=AP_CreaCategoriaBase("Regolamenti","Regolamenti",365);
	$Risultato.=AP_CreaCategoriaBase("Sicurezza","Sicurezza",365);
	return $Risultato;
}

function ap_CreaTabella($Tabella){
global $wpdb;

	switch ($Tabella){
		case $wpdb->table_name_Atti:
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Atti." (
			  `IdAtto` int(11) NOT NULL auto_increment,
			  `Numero` int(4) NOT NULL default 0,
			  `Anno` int(4) NOT NULL default 0,
			  `Data` date NOT NULL default '0000-00-00',
			  `Riferimento` varchar(100) NOT NULL,
			  `Oggetto` varchar(200) NOT NULL default '',
			  `DataInizio` date NOT NULL default '0000-00-00',
			  `DataFine` date default '0000-00-00',
			  `Informazioni` text NOT NULL default '',
			  `IdCategoria` int(11) NOT NULL default 0,
			  `RespProc` int(11) NOT NULL,
  			  `DataAnnullamento` date DEFAULT '0000-00-00',
  			  `MotivoAnnullamento` varchar(200) DEFAULT '',
  			  `Ente` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY  (`IdAtto`));";
			break;
		case $wpdb->table_name_Allegati:
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Allegati." (
			  `IdAllegato` int(11) NOT NULL auto_increment,
			  `TitoloAllegato` varchar(255) NOT NULL default '',
			  `Allegato` varchar(255) NOT NULL default '',
			  `IdAtto` int(11) NOT NULL default 0,
			  PRIMARY KEY  (`IdAllegato`));";
			break;
		case $wpdb->table_name_Categorie:
			$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Categorie." (
			  `IdCategoria` int(11) NOT NULL auto_increment,
			  `Nome` varchar(255) NOT NULL default '',
			  `Descrizione` varchar(255) NOT NULL default '',
			  `Genitore` int(11) NOT NULL default 0,
			  `Giorni` smallint(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY  (`IdCategoria`));";
			break;
		case $wpdb->table_name_Log:
			$sql= "CREATE TABLE  IF NOT EXISTS ".$wpdb->table_name_Log." (
	  		  `Data` timestamp NOT NULL default CURRENT_TIMESTAMP,
	  		  `Utente` varchar(60) NOT NULL default '',
	          `IPAddress` varchar(16) NOT NULL default '',
	          `Oggetto` int(1) NOT NULL default 1,
	          `IdOggetto` int(11) NOT NULL default 1,
	          `IdAtto` int(11) NOT NULL default 0,
	          `TipoOperazione` int(1) NOT NULL default 1,
	          `Operazione` text);";
	 		break;
	 	case $wpdb->table_name_RespProc:
		    $sql = "CREATE TABLE  IF NOT EXISTS ".$wpdb->table_name_RespProc." (
	  		  `IdResponsabile` int(11) NOT NULL auto_increment,
	  		  `Cognome` varchar(20) NOT NULL default '',
	          `Nome` varchar(20) NOT NULL default '',
	          `Email` varchar(100) NOT NULL default '',
	          `Telefono` varchar(30) NOT NULL default '',
	          `Orario` varchar(60) NOT NULL default '',
	          `Note` text,
			   PRIMARY KEY  (`IdResponsabile`));";   
			break;
		case $wpdb->table_name_Enti:
	 		$sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->table_name_Enti." (
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
			  break;
	}
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

/*
function isTable($NomeTabella){
	global $wpdb;
	$Tabella=$wpdb->get_row("SHOW TABLES LIKE '$NomeTabella'", ARRAY_A);
	if(count($Tabella)>0 ) 
		return true;
	else
		return false;
}
*/

function ap_existFieldInTable($Tabella, $Campo){
	global $wpdb;
//	echo "SHOW COLUMNS FROM $Tabella LIKE '$Campo'";exit;
	$ris=$wpdb->get_row("SHOW COLUMNS FROM $Tabella LIKE '$Campo'", ARRAY_A);
	if(count($ris)>0 ) 
		return true;
	else
		return false;	
}
function ap_existTable($Tabella){
	global $wpdb;
	$ris=$wpdb->get_row("show tables like '$Tabella' ", ARRAY_A);
	if(count($ris)>0 ) 
		return true;
	else
		return false;	
}

/*function NFieldInTable($Tabella){
	global $wpdb;
	return$wpdb->get_var("Select count(*) FROM $Tabella");
}
*/
function ap_AggiungiCampoTabella($Tabella, $Campo, $Parametri){
	global $wpdb;
	if ( false === $wpdb->query("ALTER TABLE $Tabella ADD $Campo $Parametri")){
		return new WP_Error('db_insert_error', 'Non sono riuscito a creare il campo '.$Campo.' Nella Tabella '.$Tabella.' Errore '.$wpdb->last_error, $wpdb->last_error);
	} else{
		return true;
	}
}

function ap_typeFieldInTable($Tabella, $Campo){
	global $wpdb;
//	echo "SHOW COLUMNS FROM $Tabella LIKE '$Campo'";exit;
	$ris=$wpdb->get_row("SHOW COLUMNS FROM $Tabella LIKE '$Campo'", ARRAY_A);
	if(count($ris)>0 )
		return $ris["Type"];
	else
		return false;	
}

function ap_EstraiParametriCampo($Tabella,$Campo){
	global $wpdb;
//	echo "SHOW COLUMNS FROM $Tabella LIKE $Campo <br />";
	$ris=$wpdb->get_row("SHOW COLUMNS FROM $Tabella LIKE '$Campo'", ARRAY_A);
	if(count($ris)>0 )
		return($ris);
	else
		return FALSE;
}

function ap_ModificaTipoCampo($Tabella, $Campo, $NuovoTipo){
	global $wpdb;
//	echo "ALTER TABLE $Tabella MODIFY $Campo $NuovoTipo <br />";
	if ( false === $wpdb->query("ALTER TABLE $Tabella MODIFY $Campo $NuovoTipo")){
		return new WP_Error('db_insert_error', 'Non sono riuscito a modificare il campo '.$Campo.' Nella Tabella '.$Tabella.' Errore '.$wpdb->last_error, $wpdb->last_error);
	} else{
		return true;
	}
}

function ap_ModificaParametriCampo($Tabella, $Campo, $Tipo, $Parametro){
	global $wpdb;
//	echo "ALTER TABLE $Tabella CHANGE $Campo $Campo $Tipo $Parametro";exit;
	if ( false === $wpdb->query("ALTER TABLE $Tabella CHANGE $Campo $Campo $Tipo $Parametro")){
		return new WP_Error('db_insert_error', 'Non sono riuscito a modificare il campo '.$Campo.' Nella Tabella '.$Tabella.' Errore '.$wpdb->last_error, $wpdb->last_error);
	} else{
		return true;
	}
}

/*function SvuotaTabelle(){
	global $wpdb;
	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Allegati"));
	ap_insert_log(3,7,$id,"Svuotamento Tabella Allegati");
	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Atti"));
	ap_insert_log(1,7,$id,"Svuotamento Tabella Atti");
	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Categorie"));
	ap_insert_log(2,7,$id,"Svuotamento Tabella Categorie");
	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Enti"));
	ap_insert_log(7,7,$id,"Svuotamento Tabella Enti");
	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_RespProc"));
	ap_insert_log(4,7,$id,"Svuotamento Tabella Responsabili Procedura");
}
*/
function ap_DaPath_a_URL($File){
	$base=substr(WP_PLUGIN_URL,0,strpos(WP_PLUGIN_URL,"wp-content", 0));
	$allegato=$base.strstr($File, "wp-content");
	//$Url=$base.stripslashes(get_option('opt_AP_FolderUpload')).'/'.basename($File);
	return str_replace("\\","/",$allegato);
	
}

function ap_UniqueFileName($filename,$inc=0){
	$baseName=$filename;
	while (file_exists($filename)){
		$inc++;
		$filename=substr($baseName,0,strrpos($baseName,".")).$inc.substr($baseName,strrpos($baseName,"."),strlen($baseName)-strrpos($baseName,"."));
	}
	return $filename;	
}

function ap_isAllowedExtension($fileName) {
	$EstensioniValide = array("pdf", "p7m");
  return in_array(end(explode(".", $fileName)), $EstensioniValide);
}
function ap_ExtensionType($fileName) {
  return strtolower(end(explode(".", $fileName)));
}

function ap_Bonifica_Url(){
	foreach( $_REQUEST as $key => $value){
		if ($key!="page_id")	
			$_SERVER['REQUEST_URI'] = remove_query_arg($key, $_SERVER['REQUEST_URI']);		
	}
	$url='?';
	foreach( $_REQUEST as $key => $value)
		$url.=$key."=".$value;
	return $url;
}
function ap_Estrai_PageID_Url(){
	foreach( $_REQUEST as $key => $value){
		if (strpos( $key,"page_id")!== false)		
			return $value;
	}
	return 0;
}
function ap_ListaElementiArray($var) {
     foreach($var as $key => $value) {
            $output .= $key . "==>".$value . "\n";
     }
     return $output;
}

function ap_cvdate($data){
	$rsl = explode ('-',$data);
//print("mm=".$rsl[1]." gg=". $rsl[2]."  aaaa=".$rsl[0]);
	return mktime(0,0,0,$rsl[1], $rsl[2],$rsl[0]);
}

function ap_oggi(){
	return date('Y-m-d');
}

function ap_DateAdd($data,$incremento){
	$secondi=ap_cvdate($data)+($incremento*86400);
	return date("Y-m-d",$secondi);
}

function ap_SeDate($test,$data1,$data2){
	$data1=ap_cvdate($data1);
	$data2=ap_cvdate($data2);
	switch ($test){
		case "=": 
			if ($data1==$data2)
				return true;
			break;
		case "<": 
			if ($data1<$data2)
				return true;
			break;
		case ">": 
			if ($data1>$data2)
				return true;
			break;
		case ">=": 
			if ($data1>=$data2)
				return true;
			break;
		case "<=": 
			if ($data1<=$data2)
				return true;
			break;

	}
	return false;
}
function ap_datediff($interval, $date1, $date2) {
    if(($date2==0) Or ($date2<$date1))
    	return -1;
	$seconds = ap_cvdate($date2) - ap_cvdate($date1);
    switch ($interval) {
        case "y":    // years
            list($year1, $month1, $day1) = split('-', date('Y-m-d', $date1));
            list($year2, $month2, $day2) = split('-', date('Y-m-d', $date2));
            $time1 = (date('H',$date1)*3600) + (date('i',$date1)*60) + (date('s',$date1));
            $time2 = (date('H',$date2)*3600) + (date('i',$date2)*60) + (date('s',$date2));
            $diff = $year2 - $year1;
            if($month1 > $month2) {
                $diff -= 1;
            } elseif($month1 == $month2) {
                if($day1 > $day2) {
                    $diff -= 1;
                } elseif($day1 == $day2) {
                    if($time1 > $time2) {
                        $diff -= 1;
                    }
                }
            }
            break;
        case "m":    // months
            list($year1, $month1, $day1) = split('-', date('Y-m-d', $date1));
            list($year2, $month2, $day2) = split('-', date('Y-m-d', $date2));
            $time1 = (date('H',$date1)*3600) + (date('i',$date1)*60) + (date('s',$date1));
            $time2 = (date('H',$date2)*3600) + (date('i',$date2)*60) + (date('s',$date2));
            $diff = ($year2 * 12 + $month2) - ($year1 * 12 + $month1);
            if($day1 > $day2) {
                $diff -= 1;
            } elseif($day1 == $day2) {
                if($time1 > $time2) {
                    $diff -= 1;
                }
            }
            break;
       case "w":    // weeks
            // Only simple seconds calculation needed from here on
            $diff = floor($seconds / 604800);
            break;
       case "d":    // days
            $diff = floor($seconds / 86400);
            break;
       case "h":    // hours
            $diff = floor($seconds / 3600);
            break;
       case "i":    // minutes
            $diff = floor($seconds / 60);
            break;
       case "s":    // seconds
            $diff = $seconds;
            break;
    }
    return $diff;
}

function ap_convertiData($dataEur){
$rsl = explode ('/',$dataEur);
$rsl = array_reverse($rsl);
return implode($rsl,'-');
}
function ap_VisualizzaData($dataDB){
	$dataDB=substr($dataDB,0,10);
	$rsl = explode ('-',$dataDB);
	$rsl = array_reverse($rsl);
	return implode($rsl,'/');
}
function ap_VisualizzaOra($dataDB){
return substr($dataDB,10);
}
################################################################################
// Funzioni Log
################################################################################
/* 
Oggetto int(1)
	1=> Atti
	2=> Categorie
	3=> Allegati
	4=> Responsabili
	5=> Statistiche Visualizzazioni
	6=> Statistiche Download Allegati
	7=> Enti
	
TipoOperazione int(1)
	1=> Inserimento
	2=> Modifica
	3=> Cancellazione
	4=> Pubblicazione
	5=> Incremento (solo per le statistiche)
	6=> Annullamento
*/
				  
function ap_insert_log($Oggetto,$TipoOperazione,$IdOggetto,$Operazione,$IdAtto=0){
global $wpdb, $current_user;
    get_currentuserinfo();
	$wpdb->insert($wpdb->table_name_Log,array('IPAddress' => $_SERVER['REMOTE_ADDR'],
	                                          'Utente' => $current_user->user_login,
											  'Oggetto' => $Oggetto,
										      'IdOggetto' => $IdOggetto,
											  'IdAtto' => $IdAtto,
											  'TipoOperazione' => $TipoOperazione,
											  'Operazione' => $Operazione));	
}

function ap_get_all_Oggetto_log($Oggetto,$IdOggetto=0,$IdAtto=0){
global $wpdb;
	$condizione="WHERE Oggetto=". (int)$Oggetto;
	if ($IdOggetto!=0)
		$condizione.=" and IdOggetto=". (int)$IdOggetto ;
	if ($IdAtto!=0 and $IdOggetto!=0)
		$condizione.=" or IdAtto=".(int)$IdAtto;
	if ($IdAtto!=0 and $IdOggetto==0)
		$condizione.=" and IdAtto=".(int)$IdAtto;
//	echo "SELECT * FROM $wpdb->table_name_Log ".$condizione." order by Data;";
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Log ".$condizione." order by Data DESC;");	
}

function ap_get_Stat_Visite($IdAtto){
global $wpdb;
	return $wpdb->get_results("SELECT date( `Data` ) AS Data, count( `Data` ) AS Accessi
							   FROM $wpdb->table_name_Log
							   WHERE `Oggetto` =5
							   AND `IdOggetto` =".$IdAtto."
							   GROUP BY date( `Data` )
							   ORDER BY Data DESC;");	
}
function ap_get_Stat_Num_log($IdAtto,$Oggetto){
global $wpdb;
	switch ($Oggetto){
		case 5:
			return (int)($wpdb->get_var( $wpdb->prepare( "SELECT COUNT(IdOggetto) FROM $wpdb->table_name_Log WHERE Oggetto = %d AND IdOggetto = %d",(int) $Oggetto,(int)$IdAtto)));	
			break;
		case 6:
			return (int)($wpdb->get_var( $wpdb->prepare( "SELECT COUNT(IdOggetto) FROM $wpdb->table_name_Log WHERE Oggetto = %d AND IdAtto = %d",(int) $Oggetto,(int)$IdAtto)));	
			break;
	}	
}

function ap_get_Stat_Download($IdAtto){
global $wpdb;


	return $wpdb->get_results("SELECT date( `Data` ) AS Data, TitoloAllegato, Allegato, count( `Data` ) AS Accessi
							   FROM `wp_albopretorio_log`
							   INNER JOIN $wpdb->table_name_Allegati 
							   	ON $wpdb->table_name_Log.`IdOggetto` = $wpdb->table_name_Allegati.IdAllegato
							   WHERE `Oggetto` =6
							   AND $wpdb->table_name_Allegati.`IdAtto` =$IdAtto
							   GROUP BY date( `Data` ) , IdOggetto
							   ORDER BY Data DESC");	
}
function ap_get_Stat_Log($TipoInformazione){
global $wpdb;

switch ($TipoInformazione){
	case "Oggetto":
		$Sql="SELECT 
				CASE Oggetto
					WHEN 0 THEN 'Tutte le Tabelle'
					WHEN 1 THEN 'Atti'
					WHEN 2 THEN 'Categorie'
					WHEN 3 THEN 'Allegati'
					WHEN 4 THEN 'Responsabili'
					WHEN 5 THEN 'Statistiche Visualizzazioni'
					WHEN 6 THEN 'Statistiche Download Allegati'
					WHEN 7 THEN 'Enti'
				END as NomeOggetto, COUNT( * ) as Numero
				FROM $wpdb->table_name_Log
				GROUP BY Oggetto";
		break;	
	case "TipoOperazione":
		$Sql="SELECT 
				CASE TipoOperazione
					WHEN 0 THEN 'Tutte le Tabelle'
					WHEN 1 THEN 'Inserimento'
					WHEN 2 THEN 'Modifica'
					WHEN 3 THEN 'Cancellazione'
					WHEN 4 THEN 'Pubblicazione'
					WHEN 5 THEN 'Incremento (solo per le statistiche)'
					WHEN 6 THEN 'Annullamento'
					WHEN 7 THEN 'Svuotamento Tabella'
					WHEN 8 THEN 'Restore Dati'
					WHEN 9 THEN 'Allineamento Riga Allegato con File'
					WHEN 10 THEN 'Spostamento Allegati'
				END as NomeTipoOperazione, COUNT( * ) as Numero
				FROM $wpdb->table_name_Log
				GROUP BY TipoOperazione";
		break;	
}
	return $wpdb->get_results($Sql);
}
################################################################################
// Funzioni Categorie
################################################################################
function ap_get_num_categorie(){
	global $wpdb;
	return (int)($wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->table_name_Categorie"));
}

function ap_insert_categoria($cat_name,$cat_parente,$cat_descrizione,$cat_durata){
	global $wpdb;
	if ( false === $wpdb->insert($wpdb->table_name_Categorie,array('Nome' => $cat_name,'Genitore' => $cat_parente,'Descrizione' => $cat_descrizione,'Giorni' => $cat_durata)))	
        return new WP_Error('db_insert_error', 'Non sono riuscito ad inserire la Nuova Categoria'.$wpdb->last_error, $wpdb->last_error);
    else{
    	$NomeCategoria=ap_get_categoria($Categoria);
    	$NomeCategoria=$NomeCategoria[0];
		ap_insert_log(2,1,$wpdb->insert_id,"{IdCategoria}==> $wpdb->insert_id
		                                    {Nome}==> $cat_name 
		                                    {Descrizione}==> $cat_descrizione 
											{Durata}==> $cat_durata
											{IdGenitore}==> $cat_parente
											{Genitore}==> $NomeCategoria->Nome");
	}
}
function ap_get_categorie(){
	global $wpdb;
	return $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie;");
}
function ap_memo_categorie($id,$cat_name,$cat_parente,$cat_descrizione,$cat_durata){
	global $wpdb;
	$Categoria=ap_get_categoria($id);
	$Categoria=$Categoria[0];
	$Log='{Id}==>'.$id .' ' ;
	if ($Categoria->Nome!=$cat_name)
		$Log.='{Nome}==> '.$cat_name.' ';
	if ($Categoria->Genitore!=$cat_parente){
		$Log.='{IdGenitore}==> '.$cat_parente.' ';
		$CategoriaPadre=ap_get_categoria($cat_parente);
		$CategoriaPadre=$CategoriaPadre[0];
		$Log.='{Genitore}==> '.$CategoriaPadre->Nome.' ';
	}
	if ($Categoria->Descrizione!=$cat_descrizione)
		$Log.='{Descrizione}==> '.$cat_descrizione.' ';
	if ($Categoria->Giorni!=$cat_durata)
		$Log.='{Giorni}==> '.$cat_durata.' ';
	if ( false === $wpdb->update($wpdb->table_name_Categorie,
					array('Nome' => $cat_name,
						  'Genitore' => $cat_parente,
						  'Descrizione' => $cat_descrizione,
						  'Giorni' => $cat_durata),
						  array( 'IdCategoria' => $id ),
						  array('%s',
								'%d',
								'%s',
								'%d'),
						  array('%d')		 
						  ))
    	return new WP_Error('db_update_error', 'Non sono riuscito a modifire la Categoria'.$wpdb->last_error, $wpdb->last_error);
    else
    	ap_insert_log(2,2,$id,$Log);
	
}


function ap_get_dropdown_categorie($select_name,$id_name,$class,$tab_index_attribute, $default="Nessuna", $DefVisId=true, $ConAtti=false  ) {
	global $wpdb;
	if ($ConAtti)
		$categorie = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie WHERE IdCategoria in (SELECT IdCategoria FROM wp_albopretorio_atti) GROUP BY `IdCategoria`ORDER BY nome;");	
	else
		$categorie = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie ORDER BY nome;");	
	$output = "<select name='$select_name' id='$id_name' class='$class' $tab_index_attribute>\n";
	if ($default=="Nessuno"){
		$output .= "\t<option value='0' selected='selected'>Nessuno</option>\n";
	}else{
		$output .= "\t<option value='0' >Nessuno</option>\n";
	}
	if ( ! empty( $categorie ) ) {	
		foreach ($categorie as $c) {
			$output .= "\t<option value='$c->IdCategoria'";
			if ($c->IdCategoria==$default){
				$output .= " selected=\"selected\"";
			}
			if ($DefVisId)
				$output .=" >($c->IdCategoria) $c->Nome</option>\n";
			else
				$output .=" >$c->Nome</option>\n";
		}
	}
	$output .= "</select>\n";
	return $output;
}

function ap_num_atti_categoria($IdCategoria,$Stato=0){
/*
 $Stato 
 	0 tutti
 	1 attivi
 	2 storici
*/
	global $wpdb;
	$Sql=$Sql="SELECT COUNT(*) FROM $wpdb->table_name_Atti WHERE IdCategoria=$IdCategoria";
	switch ($Stato){
		case 1:
			$Sql.=" And Numero >0 AND DataFine >= '".ap_oggi()."' AND DataInizio <= '".ap_oggi()."'";
			break;
		case 2:
			$Sql.=" And Numero >0 AND DataFine <= '".ap_oggi()."'";
			break;
	}
	$Sql.=";";
	return $wpdb->get_var($Sql);
	
}
function ap_get_dropdown_ricerca_categorie($select_name,$id_name,$class,$tab_index_attribute,$default="Nessuna",$Stato ) {
/*
 $Stato 
 	0 tutti
 	1 attivi
 	2 storici
*/
	global $wpdb;
	$categorie = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie ORDER BY nome;");	
	$output = "<select name='$select_name' id='$id_name' class='$class' $tab_index_attribute>\n";
	if ($default=="Nessuno"){
		$output .= "\t<option value='0' selected='selected'>Nessuno</option>\n";
	}else{
		$output .= "\t<option value='0' >Nessuno</option>\n";
	}
	if ( ! empty( $categorie ) ) {	
		foreach ($categorie as $c) {
			$numAtti=ap_num_atti_categoria($c->IdCategoria,$Stato);
			if ($numAtti){
				$output .= "\t<option value='$c->IdCategoria' ";
				if ($c->IdCategoria==$default){
					$output .= 'selected="selected" ';
				}
				$output .=">$c->Nome ($numAtti)</option>\n";
			}
		}
		$output .= "</select>\n";
	}
	return $output;
}

function ap_get_nuvola_categorie($link,$Stato ) {
/*
 $Stato 
 	0 tutti
 	1 attivi
 	2 storici
*/
	global $wpdb;
	$categorie = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie ORDER BY nome;");	
	if ( ! empty( $categorie ) ) {	
		$TotAtti=count(ap_get_all_atti($Stato));
		foreach ($categorie as $c) {
			$numAtti=ap_num_atti_categoria($c->IdCategoria,$Stato);
			if ($numAtti){
				$pix=(int) 1 + ($numAtti /$TotAtti);
				$output .= "<a href='".$link."=".$c->IdCategoria."' title='Ci sono ".$numAtti." Atti nella Categoria ".$c->Nome."'><span style='font-size:".$pix."em;'>".$c->Nome."</span></a><br />\n";	
			}
				
		}
	}
	return $output;
}

function ap_get_categoria($id){
 	global $wpdb;
	return $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie WHERE IdCategoria=".(int)$id.";");
}	
function ap_get_categorie_figlio($id, &$elenco, $livello){
	global $wpdb;
	$categorie_figlio = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie WHERE genitore=".(int)$id."  ORDER BY nome;");	
//		echo "SELECT DISTINCT * FROM $wpdb->table_name_Categorie WHERE IdCategoria=$id  ORDER BY nome;";
foreach ( $categorie_figlio as $cf ) 
{
//		echo "Id ".$cf->IdCategoria ."  Nome ". $cf->Nome. " <br />";
	if ($cf){
		array_push($elenco,array($cf->IdCategoria,$cf->Nome,$livello));
		if ($cf->Genitore>0){
		 	$livello+=1;
			ap_get_categorie_figlio($cf->IdCategoria,$elenco, $livello);
			$livello-=1;
		}
	}
}
}

function ap_get_categorie_gerarchica() {
	global $wpdb;
	$elenco = array();
	$categorie_primarie = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Categorie WHERE genitore<1  ORDER BY Nome;");	
	foreach ($categorie_primarie as $cp) {
//		echo "Ci passo";
//		echo "Id ".$cp->IdCategoria ."  Nome ". $cp->Nome. " <br />";
		array_push($elenco,array($cp->IdCategoria,$cp->Nome,0));
		ap_get_categorie_figlio($cp->IdCategoria,$elenco, 1);
	}
	return $elenco;
}

function ap_del_categorie($id) {
	global $wpdb;
	if ((ap_num_atti_categoria($id)>0) or (ap_num_figli_categorie($id)>0)){
		return array("atti" => ap_num_atti_categoria($id),
		             "figli" => ap_num_figli_categorie($id));
	}
	else{
	 	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Categorie WHERE	IdCategoria=%d",$id));
		ap_insert_log(2,3,$id,"Cancellazione Categoria");

		return True;
	}
}
function ap_num_figli_categorie($id){
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Categorie WHERE Genitore=%d",$id));
	
}
function ap_num_categorie(){
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Categorie");
	
}
function ap_num_categorie_inutilizzate(){
	global $wpdb;
	$Sql="SELECT count(*) 
	      FROM $wpdb->table_name_Categorie left join  $wpdb->table_name_Atti on 
		  		$wpdb->table_name_Atti.IdCategoria =  $wpdb->table_name_Categorie.IdCategoria 
		  WHERE $wpdb->table_name_Atti.IdAtto is null";
	return $wpdb->get_var($Sql);
}
function ap_num_categoria_atto($id){
	global $wpdb;
	$Sql="SELECT count(*) 
	      FROM $wpdb->table_name_Atti  
		  WHERE $wpdb->table_name_Atti.IdCategoria =%d;";
	return $wpdb->get_var($wpdb->prepare($Sql,$id));
}
function ap_categorie_orfane(){
	global $wpdb;
	$Sql="SELECT $wpdb->table_name_Atti.Numero,$wpdb->table_name_Atti.Anno, $wpdb->table_name_Atti.IdCategoria 
	      FROM $wpdb->table_name_Atti left join  $wpdb->table_name_Categorie on 
		  		$wpdb->table_name_Atti.IdCategoria =  $wpdb->table_name_Categorie.IdCategoria 
		  WHERE $wpdb->table_name_Categorie.IdCategoria is null";
	return $wpdb->get_results($Sql);
}
################################################################################
// Funzioni Atti
################################################################################

function ap_insert_atto($Ente,$Data,$Riferimento,$Oggetto,$DataInizio,$DataFine,$Note,$Categoria,$Responsabile){
	global $wpdb;
	$Anno=date("Y");
	$Numero=0;
	$Data=ap_convertiData($Data);
	$DataInizio=ap_convertiData($DataInizio);
	$DataFine=ap_convertiData($DataFine);
	if ( false === $wpdb->insert(
		$wpdb->table_name_Atti,array(
				'Ente' => $Ente,
				'Numero' => $Numero,
				'Anno' =>  $Anno,
				'Data' => $Data,
				'Riferimento' => $Riferimento,
				'Oggetto' => $Oggetto,
				'DataInizio' => $DataInizio,
				'DataFine' => $DataFine,
				'Informazioni' => $Note,
				'IdCategoria' => $Categoria,
				'RespProc' => $Responsabile)))	{
 
// echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;exit;
        return new WP_Error('db_insert_error', 'Non sono riuscito ad inserire il nuovo Atto'.$wpdb->last_error, $wpdb->last_error);}
    else{
    	$NomeCategoria=ap_get_categoria($Categoria);
    	$NomeCategoria=$NomeCategoria[0];
		$NomeResponsabile=ap_get_responsabile($Responsabile);
		$NomeResponsabile=$NomeResponsabile[0];
		$NomeEnte=ap_get_ente($Ente);
		$NomeEnte=$NomeEnte->Nome;
		ap_insert_log(1,1,$wpdb->insert_id,"{IdAtto}==> $wpdb->insert_id
											{IdEnte} $Ente
											{Ente} $NomeEnte
											{Numero} $Numero/$Anno 
											{Data}==> $Data 
						                    {Riferimento}==> $Riferimento 
											{Oggetto}==> $Oggetto 
											{IdOggetto}==> $wpdb->insert_id
											{DataInizio}==> $DataInizio
											{DataFine}==> $DataFine
											{Note}=> $Note
											{Categoria}==> $NomeCategoria->Nome
											{IdCategoria}==> $Categoria
											{Responsabile}==> $NomeResponsabile->Cognome $NomeResponsabile->Nome
											{IdResponsabile}==>$Responsabile"
							  );
	}
}
function ap_del_atto($id) {
	global $wpdb;
	$N_allegati=ap_num_allegati_atto($id);
	if ($N_allegati>0){
		return array("allegati" => $N_allegati);
	}
	else{
	 	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Atti WHERE	IdAtto=%d",$id));
		ap_insert_log(1,3,$id,"Cancellazione Atto",$id);

		return True;
	}
}

function ap_memo_atto($id,$Ente,$Data,$Riferimento,$Oggetto,$DataInizio,$DataFine,$Note,$Categoria,$Responsabile){
	global $wpdb;
	$Atto=ap_get_atto($id);
	$Atto=$Atto[0];
	$Log='' ;
	if ($Atto->Ente!=$Ente)
    	$NEnte=ap_get_ente($Ente);
		$Log.='{IdEnte}==> '.$Ente.' ';
		$Log.='{Ente}==> '.$NEnte->Nome.' ';
	if ($Atto->Data!=$Data)
		$Log.='{Data}==> '.$Data.' ';
	if ($Atto->Riferimento!=$Riferimento)
		$Log.='{Riferimento}==> '.$Riferimento.' ';
	if ($Atto->Oggetto!=$Oggetto)
		$Log.='{Oggetto}==> '.$Oggetto.' ';
	if ($Atto->DataInizio!=$DataInizio)
		$Log.='{DataInizio}==> '.$DataInizio.' ';
	if ($Atto->DataFine!=$DataFine)
		$Log.='{DataFine}==> '.$DataFine.' ';
	if ($Atto->Informazioni!=$Note)
		$Log.='{Informazioni}==> '.$Note.' ';
	if ($Atto->IdCategoria!=$Categoria){
    	$NomeCategoria=ap_get_categoria($Categoria);
    	$NomeCategoria=$NomeCategoria[0];
		$Log.='{IdCategoria}==> '.$Categoria.' ';
		$Log.='{Categoria}==> '.$NomeCategoria->Nome.' ';
	}
	if ($Atto->RespProc!=$Responsabile){
 		$NomeResponsabile=ap_get_responsabile($Responsabile);
		$NomeResponsabile=$NomeResponsabile[0];
		$Log.='{IdRespProc}==> '.$Responsabile.' ';
		$Log.='{RespProc}==> '.$NomeResponsabile->Cognome .' '. $NomeResponsabile->Nome.' ';
	}
	$Data=ap_convertiData($Data);
	$DataInizio=ap_convertiData($DataInizio);
	$DataFine=ap_convertiData($DataFine);
	if ( false === $wpdb->update($wpdb->table_name_Atti,
					array('Ente' => $Ente,
						  'Data' => $Data,
						  'Riferimento' => $Riferimento,
						  'Oggetto' => $Oggetto,
						  'DataInizio' => $DataInizio,
						  'DataFine' => $DataFine,
						  'Informazioni' => $Note,
						  'IdCategoria' => $Categoria,
						  'RespProc' => $Responsabile),
						  array( 'IdAtto' => $id ),
						  array('%d',
						        '%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%d',
								'%d'),
						  array('%d')))
    	return new WP_Error('db_update_error', 'Non sono riuscito a modifire l\' Atto'.$wpdb->last_error, $wpdb->last_error);
    else
    	ap_insert_log(1,2,$id,$Log);
	
}

function ap_update_selettivo_atto($id,$ArrayCampiValori,$ArrayTipi,$TestaMsg){
	global $wpdb;
	if ( false === $wpdb->update($wpdb->table_name_Atti,$ArrayCampiValori,array( 'IdAtto' => $id ),$ArrayTipi))
    	return new WP_Error('db_update_error', 'Non sono riuscito a modifire l\' Atto'.$wpdb->last_error, $wpdb->last_error);
    else{
		ap_insert_log(1,2,$id,$TestaMsg.ap_ListaElementiArray($ArrayCampiValori));
		return 'Atto Aggiornato: %%br%%'.ap_ListaElementiArray($ArrayCampiValori);	
	}
    	
}

function ap_approva_atto($IdAtto){
	global $wpdb;
	$NumeroDaDb=ap_get_last_num_anno(date("Y"));
	$risultato=ap_get_atto($IdAtto);
	$risultato=$risultato[0];
	$NumeroOpzione=get_option('opt_AP_NumeroProgressivo');
	if($risultato->Numero!=0)
		return "Atto gia' PUBBLICATO con Numero Progressivo ".$risultato->Numero;
	if ($NumeroDaDb!=$NumeroOpzione){
		return "Atto non PUBBLICATO:%%br%%Progressivo da ultima pubblicazione=$NumeroDaDb%%br%% Progressivo da parametri=$NumeroOpzione";
	}else{
		$x=$wpdb->update($wpdb->table_name_Atti,
									 array('Numero' => $NumeroOpzione),
									 array( 'IdAtto' => $IdAtto ),
									 array('%d'),
									 array('%d'));
	//  visualizza Sql Updateecho $wpdb->print_error();exit;
	 	if ($x==0){
	    	return 'Atto non PUBBLICATO:%%br%%Errore: '.$wpdb->last_error;
	    }
	    else{
			ap_insert_log( 1,4,$IdAtto,"{Stato Atto}==> Pubblicato 
			 							{Numero Assegnato}==> $NumeroOpzione ");	
			$NumeroOpzione+=1;
			update_option('opt_AP_NumeroProgressivo',$NumeroOpzione );
			return 'Atto PUBBLICATO';
		}
	}
}

function ap_annulla_atto($IdAtto,$Motivo){
	global $wpdb;
	$risultato=ap_get_atto($IdAtto);
	$risultato=$risultato[0];
	if($x=$wpdb->update($wpdb->table_name_Atti,array('DataAnnullamento' => date('Y-m-d'),'MotivoAnnullamento' =>$Motivo),
									 array( 'IdAtto' => $IdAtto ),
									 array('%s','%s'),
									 array('%d'))){
		ap_insert_log(1,6,$IdAtto,"{Stato Atto}==> Annullato");
		return 'Atto ANNULLATO';
	}else
		return "Atto non annullato errore: ".$wpdb->last_error;exit;		
}

function ap_get_dropdown_anni_atti($select_name,$id_name,$class,$tab_index_attribute, $default="Nessuno",$Stato=0) {
/*
 $Stato 
 	0 tutti
 	1 attivi
 	2 storici
*/
	global $wpdb;
	switch ($Stato){
		case 1:
			$Sql="SELECT Anno FROM $wpdb->table_name_Atti WHERE Numero >0 AND DataFine >= '".ap_oggi()."' AND DataInizio <= '".ap_oggi()."' GROUP BY Anno;";
			break;
		case 2:
			$Sql="SELECT Anno FROM $wpdb->table_name_Atti WHERE Numero >0 AND DataFine < '".ap_oggi()."' GROUP BY Anno;";
			break;
		default:
			$Sql="SELECT Anno FROM $wpdb->table_name_Atti GROUP BY Anno;";
			break;
	}
	$anni = $wpdb->get_results($Sql);	
	$output = "<select name='$select_name' id='$id_name' class='$class' $tab_index_attribute>\n";
	if ($default=="Nessuno"){
		$output .= "\t<option value='0' selected='selected'>Nessuno</option>\n";
	}else{
		$output .= "\t<option value='0' >Nessuno</option>\n";
	}
	if ( ! empty( $anni ) ) {	
		foreach ($anni as $c) {
			$output .= "\t<option value='$c->Anno'";
			if ($c->Anno==$default){
				$output .= " selected=\"selected\"";
			}
			$output .=" >$c->Anno</option>\n";
		}
	}
	$output .= "</select>\n";
	return $output;
}

function ap_get_last_num_anno($Anno){
	global $wpdb;
	return (int)($wpdb->get_var( $wpdb->prepare( "SELECT MAX(Numero) FROM $wpdb->table_name_Atti WHERE Anno=%d",(int)$Anno)))+1;
}
function ap_get_num_anno($IdAtto){
	global $wpdb;
	return (int)($wpdb->get_var( $wpdb->prepare( "SELECT Numero FROM $wpdb->table_name_Atti WHERE IdAtto=%d",$IdAtto)));
}

function ap_get_all_atti($Stato=0,$Anno=0,$Categoria=0,$Oggetto='',$Dadata=0,$Adata=0,$OrderBy="",$DaRiga=0,$ARiga=20,$Conteggio=false,$Annullati=true){

/* Stato:
		 0 - tutti
		 1 - in corso di validità
		 2 - scaduti
		 3 - da pubblicare
		 9 - tutti tranne quelli da pubblicare
	$Conteggio:
		 false - Estrazione Dati	
		 true - Conteggio
*/	
	global $wpdb;
	if ($OrderBy!=""){
		$OrderBy=" Order By ".$OrderBy;
	}
	
	if ($DaRiga==0 AND $ARiga==0)
		$Limite="";
	else
		$Limite=" Limit ".$DaRiga.",".$ARiga;
	
	switch ($Stato){
		case 0:
			$Selezione=' WHERE 1';
			break;
		case 9:
			$Selezione=' WHERE Numero<>0';
			break;
		case 1:
			if ($Dadata!=0 and ap_SeDate("<",ap_convertiData($Dadata),ap_oggi()))
				$Selezione.=' WHERE DataInizio>="'.ap_convertiData($Dadata).'" ';
			else
				$Selezione.=' WHERE DataInizio<="'.ap_oggi().'" ';
			if ($Adata!=0  and ap_SeDate(">",ap_convertiData($Adata),ap_oggi()))
				$Selezione.=' AND DataFine<="'.ap_convertiData($Adata).'" And DataFine>="'.ap_oggi();
			else
				$Selezione.=' AND DataFine>="'.ap_oggi();
			$Selezione.='" AND Numero>0'; 
			break;
		case 2:
			if ($Dadata!=0  and ap_SeDate("<",ap_convertiData($Dadata),ap_oggi()))
				$Selezione.=' WHERE DataInizio>="'.ap_convertiData($Dadata).'" ';
			else
				$Selezione.=' WHERE DataInizio<="'.ap_oggi().'" ';
			if ($Adata!=0   and ap_SeDate("<",ap_convertiData($Adata),ap_oggi()))
				$Selezione.=' AND DataFine<="'.ap_convertiData($Adata);
			else
				$Selezione.=' AND DataFine<="'.ap_oggi();
			$Selezione.='" AND Numero>0'; 
			break;
		case 3:
			$Selezione=' WHERE Numero=0'; 
			break;
	}
	if (!$Annullati)
		$Selezione.=' And DataAnnullamento="0000-00-00"';
	if ($Anno!=0)
		$Selezione.=' And Anno="'.$Anno.'"';
	if ($Categoria!=0)
		$Selezione.=' And IdCategoria="'.$Categoria.'"';
	if ($Oggetto!='')
		$Selezione.=' And Oggetto like "%'.$Oggetto.'%"';
//echo "<BR /><BR />SELECT * FROM $wpdb->table_name_Atti $Selezione $OrderBy $Limite;";
	if ($Conteggio){
		return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti $Selezione;");	
	}else{
		return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Atti $Selezione $OrderBy $Limite;");	
	}
	
}	

function ap_get_atto($id){
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Atti Where IdAtto=$id;");
}	

function ap_get_dropdown_atti($select_name,$id_name,$class,$tab_index_attribute,$default="Nessuno") {
	global $wpdb;
	$taxonomy_list = array();
	$atti =ap_get_all_atti( 0 ,0,0,'',0,0,"Numero Desc");
	$output = "<select name='$select_name' id='$id_name' class='$class' $tab_index_attribute>\n";
	if ($default=="Nessuno"){
		$output .= "\t<option value='0' selected='selected'>Nessuno</option>\n";
	}else{
		$output .= "\t<option value='0' >Nessuno</option>\n";
	}
	if ( ! empty( $atti ) ) {	
		foreach ($atti as $a) {
			$output .= "\t<option value='$a->IdAtto'";
			if ($a->IdAtto==$default){
				$output .= " selected='selected'";
			}
			$output .=" >($a->IdAtto) $a->Nome del $a->Numero/$a->Anno </option>\n";
		}
	}
	$output .= "</select>\n";
	return $output;
}

function ap_ripubblica_atti_correnti(){
	global $wpdb;
	$SqlAttiDaR='SELECT IdAtto, Numero, Anno, Data, DataInizio, DataFine  
				 FROM '.$wpdb->table_name_Atti.' 
				 WHERE DataInizio <= curdate() AND DataFine >= curdate() AND DataAnnullamento="0000-00-00" AND Numero>0 
				 order by Anno, Numero';
	$SqlDuplicaAtto='INSERT INTO '.$wpdb->table_name_Atti.' ( Data, Riferimento, Oggetto, DataInizio, DataFine, Informazioni, IdCategoria, RespProc, Ente)
					 SELECT Data, Riferimento, Oggetto, curdate(), adddate(curdate(),datediff(DataFine,DataInizio)), Informazioni, IdCategoria, RespProc, Ente FROM '.$wpdb->table_name_Atti.' 
					 WHERE IdAtto='; 
	$AttiDaR = $wpdb->get_results($SqlAttiDaR);
	if(get_option('opt_AP_AnnoProgressivo')!=date("Y")){
		update_option('opt_AP_AnnoProgressivo',date("Y") );
	  	update_option('opt_AP_NumeroProgressivo',1 );
		$Anno=get_option('opt_AP_AnnoProgressivo');
	}else{
		$Anno=get_option('opt_AP_AnnoProgressivo');
	}
	$StatoOperazioni='';
	foreach($AttiDaR as $AttoDaR){
		$wpdb->query($SqlDuplicaAtto.$AttoDaR->IdAtto.';');
		$StatoOperazioni.='Atto Originale Id '.$AttoDaR->IdAtto.' Numero '.$AttoDaR->Numero.'/'.$AttoDaR->Anno.' del '.$AttoDaR->Data.' Pubblicazione dal '.$AttoDaR->DataInizio.' al '.$AttoDaR->DataFine.'%%br%%';
		$IdNewAtto=$wpdb->insert_id;
		ap_insert_log(1,1,$IdNewAtto,"{IdAtto}==> $IdNewAtto
		                              {AttoOriginale}==>$AttoDaR->IdAtto
									  {Motivo}==>Ripubblicazione Atto");		
		ap_update_selettivo_atto($IdNewAtto,array('Anno' => $Anno),array('%s'),"Modifica in Ripubblicazione Atto\n");
		$RisApprovazione=ap_approva_atto($IdNewAtto);
		$Atto=ap_get_atto($IdNewAtto);
		$Atto=$Atto[0];
		$StatoOperazioni.='Atto Duplicato Id '.$IdNewAtto.' Numero '.$Atto->Numero.'/'.$Atto->Anno.' del '.$Atto->Data.' Pubblicazione dal '.$Atto->DataInizio.' al '.$Atto->DataFine.'%%br%%';
		$StatoOperazioni.=$RisApprovazione.' %%br%%';
		if ($RisApprovazione!='Atto PUBBLICATO'){
			ap_del_atto($IdNewAtto);
		}else{
			$SqlDuplicaAllegato='INSERT INTO '.$wpdb->table_name_Allegati.' ( TitoloAllegato,Allegato,IdAtto)
						 SELECT TitoloAllegato,Allegato,'.$IdNewAtto.' as IdNuovoAtto FROM '.$wpdb->table_name_Allegati.'
						 WHERE IdAllegato=';
			$AllegatiAtto=ap_get_all_allegati_atto($AttoDaR->IdAtto);
			foreach ($AllegatiAtto as $AllegatoAtto) {
				$wpdb->query($SqlDuplicaAllegato.$AllegatoAtto->IdAllegato.';');
				$IdNewAllegato=$wpdb->insert_id;
				ap_insert_log(3,1,$wpdb->insert_id,"{IdAllegato}==> $IdNewAllegato
												{VecchioAtto}==> $AllegatoAtto->IdAtto 
												{Allegato}==> $Allegato 
												{IdAtto}==> $IdNewAtto
												{Motivo}==>Ripubblicazione Atto", $IdNewAtto);
				$StatoOperazioni.='    Allegato Originale Id '.$AllegatoAtto->IdAllegato.' Duplicato Id '.$IdNewAllegato.' Allegato '.$Allegato.' %%br%%';
			}
			$StatoOperazioni.='Atto Id '.$AttoDaR->IdAtto.' Numero '.$AttoDaR->Numero.'/'.$AttoDaR->Anno.' del '.$AttoDaR->Data.' '.ap_annulla_atto($AttoDaR->IdAtto,"Annullamento per interruzione del sevizio di pubblicazione").'%%br%%';		
		}
	}
	if ($wpdb->last_error==''){
		return $StatoOperazioni."Ripubblicazione effettuata con successo";
	}else{
		return $StatoOperazioni."Ripubblicazione non effettuata a causa del seguente errore:".$wpdb->last_error;
	}
}

################################################################################
// Funzioni Allegati
################################################################################

function ap_get_num_allegati($id){
	global $wpdb;
	return (int)($wpdb->get_var( $wpdb->prepare( "SELECT COUNT(IdAllegato) FROM $wpdb->table_name_Allegati WHERE IdAtto=%d",(int)$id)));
}

function ap_allegati_orfani(){
	global $wpdb;
	$Sql="SELECT $wpdb->table_name_Allegati.IdAllegato, $wpdb->table_name_Allegati.TitoloAllegato, $wpdb->table_name_Allegati.IdAtto
		  FROM $wpdb->table_name_Allegati
			LEFT JOIN $wpdb->table_name_Atti ON $wpdb->table_name_Atti.IdAtto = $wpdb->table_name_Allegati.IdAtto
		  WHERE $wpdb->table_name_Atti.IdAtto IS NULL 
		  ORDER BY $wpdb->table_name_Allegati.IdAtto";
	return $wpdb->get_results($Sql);
}
function ap_num_allegati(){
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Allegati");
	
}
function ap_num_allegati_atto($id){
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Allegati WHERE IdAtto=%d",$id));
	
}
function ap_get_all_allegati_atto($idAtto){
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Allegati WHERE IdAtto=". (int)$idAtto." ORDER BY IdAllegato;");
}

function ap_get_allegato_atto($idAllegato){
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Allegati WHERE IdAllegato=". (int)$idAllegato.";");
}

function ap_memo_allegato($idAllegato,$Titolo,$idAtto){
	global $wpdb;
//		echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;
	if ($num=$wpdb->update($wpdb->table_name_Allegati,
					array('TitoloAllegato' => $Titolo),
						  array( 'IdAllegato' => $idAllegato ),
						  array('%s'),
						  array('%d'))){
		ap_insert_log(3,2,$id,"{Titolo Allegato}==> $Titolo",idAtto);
		return true;
	}else{
		return new WP_Error('db_update_error', 'Non sono riuscito a modifire l\'allegato '.$wpdb->last_error, $wpdb->last_error);
	}
}

function ap_insert_allegato($TitoloAllegato,$Allegato,$IdAtto){
global $wpdb;
	$IdAtto=(int)$IdAtto;
	if ( false === $wpdb->insert(
		$wpdb->table_name_Allegati,array(
				'TitoloAllegato' => $TitoloAllegato,
				'Allegato' =>  $Allegato,
				'IdAtto' => $IdAtto)))	
        return new WP_Error('db_insert_error', 'Non sono riuscito ad inserire il nuovo allegato'.$wpdb->last_error, $wpdb->last_error);
    else
    	ap_insert_log(3,1,$wpdb->insert_id,"{IdAllegato}==> $wpdb->insert_id
											{Titolo}==> $TitoloAllegato 
											{Allegato}==> $Allegato 
											{IdAtto}==> $IdAtto", $IdAtto);
}

function ap_del_allegato_atto($idAllegato,$idAtto=0,$nomeAllegato=''){
global $wpdb;
	$allegato=ap_get_allegato_atto($idAllegato);
	if (file_exists($allegato[0]->Allegato) && is_file($allegato[0]->Allegato))
		if (unlink($allegato[0]->Allegato)){
			$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Allegati WHERE IdAllegato=%d",$idAllegato));
			ap_insert_log(3,3,$allegato[0]->IdAllegato,"{Nome Allegato}==> $nomeAllegato ",$idAtto);
			return True;
		}else{
			return FALSE;
		}
}
function ap_del_allegati_atto($idAtto){
global $wpdb;
	$Del=FALSE;
	$Allegati=ap_get_all_allegati_atto($idAtto);
	foreach($Allegati as $allegato){
		if (file_exists($allegato->Allegato) && is_file($allegato->Allegato))
			if (unlink($allegato->Allegato)){
				$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Allegati WHERE IdAllegato=%d",$allegato->IdAllegato));
				ap_insert_log(3,3,$allegato->IdAllegato,"{Nome Allegato}==> ".$allegato->Allegato,$idAtto);
				$Del=TRUE;
			}else{
				$Del=FALSE;
			}
	}
	return $Del;
}

function ap_get_all_allegati(){
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Allegati;");
}

function ap_sposta_allegati($OldPathAllegati,$eliminareOrigine=FALSE){
	global $wpdb;
//	echo $OldPathAllegati;exit;
//Backup Automatico dati e allegati
	$msg="";
	ap_BackupDatiFiles("Sposta_Allegati","Automatico");
	$DirLog=str_replace("\\","/",Albo_DIR.'/BackupDatiAlbo/log');
	$nomefileLog=$DirLog."/Backup_Automatico_AlboPretorio_Sposta_Allegati.log";
	$fplog = @fopen($nomefileLog, "ab");
	fwrite($fplog,"____________________________________________________________________________\n");
	fwrite($fplog,"Inizio spostamento file\n");
	$allegati=$wpdb->get_results("SELECT * FROM $wpdb->table_name_Allegati ;",ARRAY_A );
// Nuova directory Allegati Albo Pretorio
	$BaseCurDir=str_replace("\\","/",AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
// Inizo Blocco che sposta gli allegati e sincronizza la tabella degli Allegati
	foreach( $allegati as $allegato){
		$NewAllegato=$BaseCurDir."/".basename($allegato['Allegato']);
		if (is_file($allegato['Allegato'])){
			if (!copy($allegato['Allegato'], $NewAllegato)) {
				ap_insert_log(3,10,$allegato['IdAllegato'] ,"{Errore nello spostamento Allegato}==> ".$allegato['Allegato']." => $NewAllegato",0);	
				$msg.='<spam style="color:red;">Errore</spam> nello spostamento dell\'Allegato '.$allegato['Allegato'].' in '. $NewAllegato."%%br%%";
				fwrite($fplog,"Non sono riuscito a copiare il file ".$allegato['Allegato']." in ". $NewAllegato."\n");
			}
			else{
				if (!unlink($allegato['Allegato'])){
					ap_insert_log(3,10,$allegato['IdAllegato'] ,"{Errore nella cancellazione Allegato}==> ".$allegato['Allegato'],0);
					$msg.='<spam style="color:red;">Errore</spam> errata cancellazione dell\'Allegato </spam>'.$allegato['Allegato']."%%br%%";
					fwrite($fplog,"Non sono riuscito a cancelalre il file ".$allegato['Allegato']."\n");
			}
			$msg.='<spam style="color:green;">File</spam> '.$allegato['Allegato'].' <spam style="color:green;">spostato in</spam> '.$NewAllegato.'%%br%%';
			fwrite($fplog,"File ".$allegato['Allegato']." spostato in ".$NewAllegato."\n");
			if ($wpdb->update($wpdb->table_name_Allegati,
									array('Allegato' => $NewAllegato),
									array('IdAllegato' => $allegato['IdAllegato'] ),
									array('%s'),
									array('%d'))>0){
				ap_insert_log(3,9,$allegato['IdAllegato'] ,"{Allegato}==> ".$allegato['Allegato']." spostato in $NewAllegato",0);
				$msg.='<spam style="color:green;">Aggiornamento Link Allegato</spam> '.$allegato['Allegato']."%%br%%";
				fwrite($fplog,"Aggiornato il link nel Data Base per ".$allegato['Allegato']." in ".$NewAllegato."\n");
			}
		}
	}					
}
// Fine Blocco che sposta gli allegati e sincronizza la tabella degli Allegati
	$msg.="%%br%%";
	$tmpdir=str_replace("\\","/",$OldPathAllegati);
	$PathAllegati=AP_BASE_DIR;
	fwrite($fplog,"__________________________________________________________________\n");
	fwrite($fplog,"Svuotamento e cancellazione Vecchia Directory ".$OldPathAllegati." \n");
	if ($tmpdir!=$PathAllegati and $eliminareOrigine){
		$fName=str_replace("\\","/",$OldPathAllegati)."/index.php";
		if (is_file($fName))
			if (unlink($fName))
				fwrite($fplog,"File ".$fName." Cancellato\n");
			else
				fwrite($fplog,"Errore nella Cancellazione del file ".$fName."\n");
		else
			fwrite($fplog,"File ".$fName." inesistente\n");
		$fName=str_replace("\\","/",$OldPathAllegati)."/.htaccess";
		if (is_file($fName))
			if (unlink($fName))
				fwrite($fplog,"File ".$fName." Cancellato\n");
			else
				fwrite($fplog,"Errore nella Cancellazione del file ".$fName."\n");
		else
			fwrite($fplog,"File ".$fName." inesistente\n");
		if($tmpdir==AP_BASE_DIR){
			$msg.="Directory ".$tmpdir." non cancellata%%br%%";
			fwrite($fplog,"Directory ".$tmpdir." non cancellata \n");	
		}else{
			if (is_dir($tmpdir)){
				if (!ap_is_dir_empty($tmpdir)){
					$msg.="La directory ".$tmpdir." non vuota%%br%%";
					fwrite($fplog,"La directory ".$tmpdir." non vuota \n");					
				}else{
					if (rmdir($tmpdir)){
						$msg.="Directory ".$tmpdir." cancellata%%br%%";
						fwrite($fplog,"Directory ".$tmpdir." cancellata \n");	
					}else{
						$msg.="La directory ".$tmpdir." non e' stata cancellata%%br%%";
						fwrite($fplog,"La directory ".$tmpdir." non e' stata cancellata \n");
					}
				}
			}else{
					$msg.="La directory ".$tmpdir." non esiste%%br%%";
					fwrite($fplog,"La directory ".$tmpdir." non esiste \n");		
			}			
		}
	}
	if (!$eliminareOrigine){
		$msg.="La directory ".$tmpdir." non essendo una sottocartella della cartella Uploads di sistema, non deve essere cancellata%%br%%";
		fwrite($fplog,"La directory ".$tmpdir." non essendo una sottocartella della cartella Uploads di sistema, non deve essere cancellata \n");	
	}
	fclose($fplog);
	if (stripslashes(get_option('opt_AP_FolderUpload'))!="wp-content/uploads"){
		ap_NoIndexNoDirectLink(AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
	}
	$fpmsg = @fopen(Albo_DIR."/BackupDatiAlbo/tmp/msg.txt", "wb");
	fwrite($fpmsg,$msg);
	fclose($fpmsg);
}

function ap_allinea_allegati(){
	global $wpdb;
	$msg="";
	$allegati=$wpdb->get_results("SELECT * FROM $wpdb->table_name_Allegati ;",ARRAY_A );
// Nuova directory Allegati Albo Pretorio
	$BaseCurDir=str_replace("\\","/",AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
	foreach( $allegati as $allegato){
		$NewAllegato=$BaseCurDir."/".basename($allegato['Allegato']);
		if ($wpdb->update($wpdb->table_name_Allegati,
									array('Allegato' => $NewAllegato),
									array('IdAllegato' => $allegato['IdAllegato'] ),
									array('%s'),
									array('%d'))>0){
				ap_insert_log(3,9,$allegato['IdAllegato'] ,"{Allegato}==> ".$allegato['Allegato']." spostato in $NewAllegato",0);
				$msg.='<spam style="color:green;">Aggiornamento Link Allegato</spam> '.$allegato['Allegato']."%%br%%";
			}
//	echo "<p>Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error."</p>";
	}
	return $msg;
}

################################################################################
// Funzioni Responsabili
################################################################################
function ap_get_dropdown_responsabili($select_name,$id_name,$class,$tab_index_attribute="", $default="Nessuno") {
	global $wpdb;
	$responsabili = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_RespProc ORDER BY nome;");	
	$output = "<select name='$select_name' id='$id_name' class='$class' $tab_index_attribute>\n";
	if ($default=="Nessuno"){
		$output .= "\t<option value='0' selected='selected'>Nessuno</option>\n";
	}else{
		$output .= "\t<option value='0' >Nessuno </option>\n";
	}
	if ( ! empty( $responsabili ) ) {	
		foreach ($responsabili as $c) {
			$output .= "\t<option value='$c->IdResponsabile'";
			if ($c->IdResponsabile==$default){
				$output .= " selected=\"selected\" ";
			}
			$output .=" >$c->Cognome $c->Nome</option>\n";
		}
	}
	$output .= "</select>\n";
	return $output;
}

function ap_num_responsabili_atto($id){
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti WHERE RespProc=%d",$id));
}
function ap_num_responsabili(){
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_RespProc;");	
}
function ap_num_responsabili_inutilizzati(){
	global $wpdb;
	$Sql="SELECT count(*) 
	      FROM $wpdb->table_name_RespProc left join  $wpdb->table_name_Atti on 
		  		$wpdb->table_name_Atti.RespProc =  $wpdb->table_name_RespProc.IdResponsabile
		  WHERE $wpdb->table_name_Atti.IdAtto is null";
	return $wpdb->get_var($Sql);

}
function ap_get_responsabili(){
	global $wpdb;
//	echo "SELECT * FROM $wpdb->table_name_Atti $Selezione $OrderBy $Limite;";
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_RespProc ORDER BY Cognome , Nome;");	
}
function ap_get_responsabile($Id){
	global $wpdb;
//	echo "SELECT * FROM $wpdb->table_name_RespProc WHERE IdResponsabile=$Id;";
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_RespProc WHERE IdResponsabile=$Id;");	
}
function ap_insert_responsabile($resp_cognome,$resp_nome,$resp_email,$resp_telefono,$resp_orario,$resp_note){
	global $wpdb;
	if ( false === $wpdb->insert($wpdb->table_name_RespProc,array('Cognome' => $resp_cognome,
	                                                              'Nome' => $resp_nome,
																  'Email' => $resp_email,
																  'Telefono' => $resp_telefono,
																  'Orario' => $resp_orario,
																  'Note' => $resp_note)))	
        return new WP_Error('db_insert_error', 'Non sono riuscito ad inserire il Nuovo Responsabile'.$wpdb->last_error, $wpdb->last_error);
    else
    	ap_insert_log(4,1,$wpdb->insert_id,"{IdResponsabile}==> $wpdb->insert_id
		                                    {Cognome}==> $resp_cognome 
		                                    {Nome}==> $resp_nome 
											{Email}==> $resp_email
											{Telefono}==> $resp_telefono
											{Orario}==> $resp_orario
											{Note}==> $resp_note");
}
function ap_memo_responsabile($Id,$resp_cognome,$resp_nome,$resp_email,$resp_telefono,$resp_orario,$resp_note){
	global $wpdb;
	$Responsabile=ap_get_responsabile($Id);
	$Responsabile=$Responsabile[0];
	$Log='{Id}==>'.$Id .' ' ;
	if ($Responsabile->Cognome!=$resp_cognome)
		$Log.='{Cognome}==> '.$resp_cognome.' ';
	if ($Responsabile->Nome!=$resp_nome)
		$Log.='{Nome}==> '.$resp_nome.' ';
	if ($Responsabile->Email!=$resp_email)
		$Log.='{Email}==> '.$cat_parente.' ';
	if ($Responsabile->Telefono!=$resp_telefono)
		$Log.='{Telefono}==> '.$resp_telefono.' ';
	if ($Responsabile->Orario!=$resp_orario)
		$Log.='{Orario}==> '.$resp_orario.' ';
	if ($Responsabile->Note!=$resp_note)
		$Log.='{Note}==> '.$resp_note.' ';
	
	if ( false === $wpdb->update($wpdb->table_name_RespProc,
					array('Cognome' => $resp_cognome,
	                      'Nome' => $resp_nome,
						  'Email' => $resp_email,
						  'Telefono' => $resp_telefono,
						  'Orario' => $resp_orario,
						  'Note' => $resp_note),
					array('IdResponsabile' => $Id),
					array( '%s',
						   '%s',
						   '%s',
						   '%s',
						   '%s',
						   '%s')))
	    	return new WP_Error('db_update_error', 'Non sono riuscito a modifire il resposnabile del Trattamento'.$wpdb->last_error, $wpdb->last_error);
	else 
		ap_insert_log(4,2,$id,$Log);
}

function ap_del_responsabile($id) {
	global $wpdb;
	$resp=ap_get_responsabile($id);
	$responsabile= "Cancellazione Responsabile {IdResponsabile}==> $id {Cognome}==> ".$resp[0]->Cognome." {Nome}==> ".$resp[0]->Nome; 
	$N_atti=ap_num_responsabili_atto($id);
	if ($N_atti>0){
		return array("atti" => $N_atti);
	}else{
	 	$result=$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_RespProc WHERE IdResponsabile=%d",$id));
		ap_insert_log(4,3,$id,$responsabile,$id);
		return $result;
	}
}
function ap_responsabili_orfani(){
	global $wpdb;
	$Sql="SELECT $wpdb->table_name_Atti.Numero,$wpdb->table_name_Atti.Anno, $wpdb->table_name_Atti.RespProc 
	      FROM $wpdb->table_name_Atti left join  $wpdb->table_name_RespProc on 
		  		$wpdb->table_name_Atti.RespProc =  $wpdb->table_name_RespProc.IdResponsabile
		  WHERE $wpdb->table_name_RespProc.IdResponsabile is null";
	return $wpdb->get_results($Sql);
}
################################################################################
// Funzioni Permessi
################################################################################

function ap_get_users(){
	global $wpdb;  
	$users = $wpdb->get_results('SELECT ID, user_login FROM '.$wpdb->users); 
    return $users;  
}
################################################################################
// Funzioni Enti
################################################################################
function ap_get_dropdown_enti($select_name,$id_name,$class,$tab_index_attribute="", $default=0) {
	global $wpdb;
	$enti = $wpdb->get_results("SELECT DISTINCT * FROM $wpdb->table_name_Enti ORDER BY IdEnte;");	
	$output = "<select name='$select_name' id='$id_name' class='$class' $tab_index_attribute>\n";
	if ( ! empty( $enti ) ) {	
		foreach ($enti as $c) {
			$output .= "\t<option value='$c->IdEnte'";
			if ($c->IdEnte==$default){
				$output .= " selected=\"selected\" ";
			}
			$output .=" >$c->Nome</option>\n";
		}
	}
	$output .= "</select>\n";
	return $output;
}
function ap_num_enti(){
	global $wpdb;
	return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Enti");
}
function ap_num_enti_Inutilizzati(){
	global $wpdb;
	$Sql="SELECT COUNT(*)
			FROM $wpdb->table_name_Enti
			LEFT JOIN $wpdb->table_name_Atti ON $wpdb->table_name_Enti.IdEnte = $wpdb->table_name_Atti.Ente
			WHERE $wpdb->table_name_Atti.IdAtto IS NULL";
	return $wpdb->get_var($Sql);
}
function ap_num_enti_atto($id){
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti WHERE Ente=%d",$id));
}

function ap_get_enti(){
	global $wpdb;
//	echo "SELECT * FROM $wpdb->table_name_Enti ORDER BY Nome;";
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Enti ORDER BY Nome;");	
}
function ap_get_ente($Id){
	global $wpdb;
//	echo "SELECT * FROM $wpdb->table_name_RespProc WHERE IdResponsabile=$Id;";
	$ente=$wpdb->get_results("SELECT * FROM $wpdb->table_name_Enti WHERE IdEnte=$Id;");
	return $ente[0];	
}
function ap_get_ente_me(){
	global $wpdb;
	$ente=$wpdb->get_results("SELECT Nome FROM $wpdb->table_name_Enti WHERE IdEnte=0;");	
	return $ente[0]->Nome;
}
function ap_set_ente_me($ente_nome){
	global $wpdb;
	if (!ap_create_ente_me($ente_nome))
		if (true==$wpdb->update($wpdb->table_name_Enti,
						array('Nome' => $ente_nome),
						array('IdEnte' => 0),
						array( '%s')))
			ap_insert_log(7,2,0,"Aggiornamento Ente Sito");	
//echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;exit;
}
function ap_create_ente_me($nome="Ente non definito"){
	global $wpdb;
		if ($wpdb->get_var("SELECT COUNT(IdEnte) FROM $wpdb->table_name_Enti  WHERE IdEnte=0;")==0){
			$wpdb->insert($wpdb->table_name_Enti,array('Nome' =>$nome));
			$wpdb->update($wpdb->table_name_Enti,
									 array('IdEnte' => 0),
									 array( 'IdEnte' => $wpdb->insert_id),
									 array('%d'),
									 array('%d'));	
			return TRUE;
		}
		return FALSE;
}

function ap_insert_ente($ente_nome,$ente_indirizzo,$ente_url,$ente_email,$ente_pec,$ente_telefono,$ente_fax,$ente_note){
	global $wpdb;
	if ( false === $wpdb->insert($wpdb->table_name_Enti,array('Nome' => $ente_nome,
	                                                              'Indirizzo' => $ente_indirizzo,
	                                                              'Url' => $ente_url,
																  'Email' => $ente_email,
																  'Pec' => $ente_pec,
																  'Telefono' => $ente_telefono,
																  'Fax' => $ente_fax,
																  'Note' => $ente_note))){
//		echo "Sql==".$wpdb->last_query ."    Ultimo errore==".$wpdb->last_error;exit;
        return new WP_Error('db_insert_error', 'Non sono riuscito ad inserire il Nuovo Ente'.$wpdb->last_error, $wpdb->last_error);}
    else
    	ap_insert_log(7,1,$wpdb->insert_id,"{IdEnte}==> $wpdb->insert_id
		                                    {Nome}==> $ente_nome 
											{Indirizzo}=> $ente_indirizzo
											{Url}=> $ente_url
											{Email}==> $ente_email
											{Pec}==> $ente_pec
											{Telefono}==> $ente_telefono
											{fax}==> $ente_orario
											{Note}==> $ente_note");
}

function ap_memo_ente($Id,$ente_nome,$ente_indirizzo,$ente_url,$ente_email,$ente_pec,$ente_telefono,$ente_fax,$ente_note){
	global $wpdb;
	$EnteL=ap_get_ente($Id);
	$Log='{Id}==>'.$Id .' ' ;
	if ($EnteL->Nome!=$ente_nome)
		$Log.='{Nome}==> '.$ente_nome.' ';
	if ($EnteL->Indirizzo!=$ente_indirizzo)
		$Log.='{Indirizzo}==> '.$ente_indirizzo.' ';
	if ($EnteL->Url!=$ente_url)
		$Log.='{Url}==> '.$ente_url.' ';
	if ($EnteL->Email!=$ente_email)
		$Log.='{Email}==> '.$ente_email.' ';
	if ($EnteL->Pec!=$ente_pec)
		$Log.='{Pec}==> '.$ente_pec.' ';
	if ($EnteL->Telefono!=$ente_telefono)
		$Log.='{Telefono}==> '.$ente_telefono.' ';
	if ($EnteL->Fax!=$ente_fax)
		$Log.='{Fax}==> '.$ente_fax.' ';
	if ($EnteL->Note!=$ente_note)
		$Log.='{Note}==> '.$ente_note.' ';
	
	if ( false === $wpdb->update($wpdb->table_name_Enti,
					array('Nome' => $ente_nome,
						  'Indirizzo' => $ente_indirizzo,
						  'Url' => $ente_url,
						  'Email' => $ente_email,
						  'Pec' => $ente_pec,
						  'Telefono' => $ente_telefono,
						  'Fax' => $ente_fax,
						  'Note' => $ente_note),
					array('IdEnte' => $Id),
					array( '%s',
						   '%s',
						   '%s',
						   '%s',
						   '%s',
						   '%s'),
					array( '%d' )))
	    	return new WP_Error('db_update_error', 'Non sono riuscito a modifire l\'Ente'.$wpdb->last_error, $wpdb->last_error);
	else 
		ap_insert_log(7,2,$id,$Log);
}

function ap_del_ente($id) {
	global $wpdb;
	$N_atti=ap_num_enti_atto($id);
	if ($N_atti>0){
		return array("atti" => $N_atti);
	}else{
	 	$result=$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Enti WHERE IdEnte=%d",$id));
		ap_insert_log(7,3,$id,"Cancellazione Ente {IdEnte}==> $id",$id);
		return $result;
	}
}
function ap_enti_orfani(){
	global $wpdb;
	$Sql="SELECT $wpdb->table_name_Atti.Numero,$wpdb->table_name_Atti.Anno, $wpdb->table_name_Atti.Ente 
	      FROM $wpdb->table_name_Atti left join  $wpdb->table_name_Enti on 
		  		$wpdb->table_name_Atti.Ente =  $wpdb->table_name_Enti.IdEnte 
		  WHERE $wpdb->table_name_Enti.IdEnte is null";
	return $wpdb->get_results($Sql);
}


/**
* Backup 
*/
function ap_sql_addslashes($a_string = '', $is_like = false) {
	if ($is_like) $a_string = str_replace('\\', '\\\\\\\\', $a_string);
	else $a_string = str_replace('\\', '\\\\', $a_string);
	return str_replace('\'', '\\\'', $a_string);
} 

function ap_backup_table($table,$fp) {
	global $wpdb;
	if($table==$wpdb->table_name_Enti){
		@fwrite($fp,"SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO';"."\r\n");
	}
	$table_structure = $wpdb->get_results("DESCRIBE $table");
	if (! $table_structure) {
		echo 'Errore nell\'estrazione della struttura della tabella : '.$table;
		return false;
	}
	// Table structure
	// Comment in SQL-file
	$create_table = $wpdb->get_results("SHOW CREATE TABLE $table", ARRAY_N);
	if (false === $create_table) {
		$err_msg = 'Errore in SHOW CREATE TABLE per la labella '. $table;
	}else{
		$create_table[0][1]=str_replace("CREATE TABLE","CREATE TABLE IF NOT EXISTS",$create_table[0][1]);
		@fwrite($fp,$create_table[0][1] . ' ;'."\r\n");
	}
	$defs = array();
	$ints = array();
	foreach ($table_structure as $struct) {
		if ( (0 === strpos($struct->Type, 'tinyint')) ||
			(0 === strpos(strtolower($struct->Type), 'smallint')) ||
			(0 === strpos(strtolower($struct->Type), 'mediumint')) ||
			(0 === strpos(strtolower($struct->Type), 'int')) ||
			(0 === strpos(strtolower($struct->Type), 'bigint')) ) {
				$defs[strtolower($struct->Field)] = ( null === $struct->Default ) ? 'NULL' : $struct->Default;
				$ints[strtolower($struct->Field)] = "1";
		}
	}
	@fwrite($fp,"Delete From $table ;"."\r\n");
	$table_data = $wpdb->get_results("SELECT * FROM $table ", ARRAY_A);
	$entries = 'INSERT INTO ' . ap_backquote($table) . ' VALUES (';	
	//    \x08\\x09, not required
	$search = array("\x00", "\x0a", "\x0d", "\x1a");
	$replace = array('\0', '\n', '\r', '\Z');
	if($table_data) {
		foreach ($table_data as $row) {
			$values = array();
			foreach ($row as $key => $value) {
				if ($ints[strtolower($key)]) {
					// make sure there are no blank spots in the insert syntax,
					// yet try to avoid quotation marks around integers
					$value = ( null === $value || '' === $value) ? $defs[strtolower($key)] : $value;
					$values[] = ( '' === $value ) ? "''" : $value;
				} else {
					$values[] = "'" . str_replace($search, $replace, ap_sql_addslashes($value)) . "'";
				}
			}
			@fwrite($fp, $entries . implode(', ', $values) . ');'."\r\n");
		}
	}
} // end backup_table()

function ap_SvuotaDirectory($Dir,$fplog){
	//Svuoto cartella tmp che contiene i files dati
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($Dir));
	fwrite($fplog,"Svuotamento Directory ".$Dir."\n");
	foreach ($iterator as $key=>$value) {
		if (is_file(realpath($key)))
			if (unlink(realpath($key)))
				fwrite($fplog,"       File ".$key." cancellato\n");
			else
				fwrite($fplog,"       File ".$key." non pu&ograve; essere cancellato\n");
	}
}

function ap_BackupDatiFiles($NomeFile,$Tipo="",$Destinazione=Albo_DIR){
global $wpdb;
	$tables=array(	$wpdb->table_name_Allegati,
					$wpdb->table_name_Atti,
					$wpdb->table_name_Categorie,
					$wpdb->table_name_Enti,
					$wpdb->table_name_RespProc);
	$Dir=str_replace("\\","/",$Destinazione.'/BackupDatiAlbo');
	$DirTmp=$Dir."/tmp";
	$DirLog=$Dir."/log";
	$DirAllegati=str_replace("\\","/",AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
	$ControlloDir="";
	require_once('inc/pclzip.lib.php');
	if ($Tipo==""){
			$nomefileZip=$Dir."/".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_AlboPretorio_".$NomeFile.".log";
		}else{
			$nomefileZip=$Dir."/".$Tipo."_".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_".$Tipo."_AlboPretorio_".$NomeFile.".log";
	}	
	$Risultato="Risultato del Backup:<br />";
	if (class_exists('PclZip')) {
//		echo $Dir." <br />".$DirTmp." <br />".$DirAllegati." <br />".$DirLog."";exit;
		if (!is_dir ( $Dir)){
			if (!mkdir($Dir, 0755)) 
				$ControlloDir.="Non sono riuscito a creare la directory ".$Dir."\n Fine Operazione";
			else
				if (!mkdir($DirTmp, 0755))
					$ControlloDir.="Non sono riuscito a creare la directory ".$DirTmp."\n Fine Operazione";
				if (!mkdir($DirLog, 0755)) 
					$ControlloDir.="Non sono riuscito a creare la directory ".$DirLog."\n Fine Operazione";
		}
		if (!is_dir ($DirTmp) and $ControlloDir=="")
			if (!mkdir($DirTmp, 0755))
				  $ControlloDir.="Non sono riuscito a creare la directory ".$DirTmp."\n Fine Operazione";
		if (!is_dir ($DirLog) and $ControlloDir=="")
			if (!mkdir($DirLog, 0755))
				  $ControlloDir.="Non sono riuscito a creare la directory ".$DirLog."\n Fine Operazione";
		if ($ControlloDir!=""){
			return $ControlloDir;
		}
/*		if ($Tipo==""){
			$nomefileZip=$Dir."/".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_AlboPretorio_".$NomeFile.".log";
		}else{
			$nomefileZip=$Dir."/".$Tipo."_".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_".$Tipo."_AlboPretorio_".$NomeFile.".log";
		}*/	
		$fplog = @fopen($nomefileLog, "wb");
		fwrite($fplog,"Avvio Backup Dati ed Allegati Albo Pretrorio \n effettuato in data ".date("Ymd_Hi")."\n");
		ap_SvuotaDirectory($DirTmp,$fplog);
		fwrite($fplog,"Svuotamento tabella ".$DirTmp."\n");
		$fp = @fopen($DirTmp."/AlboPretorio".date("Ymd_Hi").".sql", "wb");
		$Risultato="";
		foreach ($tables as $table) {
			ap_backup_table($table,$fp);
			$Risultato.='<span style="color:green;">Tabella '.ap_backquote($table).' Aggiunta</span> <br />';
			fwrite($fplog,"Sql Tabella ".ap_backquote($table)." Aggiunta\n");
		}
		$UpdateProgressivo="UPDATE `".$wpdb->options."` SET `option_value` = '".get_option('opt_AP_AnnoProgressivo')."'	WHERE `option_name` ='opt_AP_AnnoProgressivo';\n";
		$UpdateProgressivo.="UPDATE `".$wpdb->options."` SET `option_value` = '".get_option('opt_AP_NumeroProgressivo')."' WHERE `option_name` ='opt_AP_NumeroProgressivo';";
		fwrite($fplog,"Sql Aggiornamento Tabella ".$wpdb->options." per Progressivo ed Anno Progressivo Aggiunti\n");
		fwrite($fp,$UpdateProgressivo);
		fclose($fp);
		if (is_dir($Dir) And is_dir($DirTmp)){
			// Crea l'archivio
		 	$zip = new PclZip($nomefileZip);
			// Inizializzazione dell'iterator a cui viene passato 
			// l'iteratore ricorsivo delle directory a cui viene passata la directory da zippare
			$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($DirTmp));
			// Ciclo tutti gli elementi dell'iteratore, i files estratti dall'iteratore
			foreach ($iterator as $key=>$value) {
				if (substr($key,-1)!="."){
					$zip->add(realpath($key),PCLZIP_OPT_REMOVE_PATH,dirname($key));
					$Risultato.='<span style="color:green;">Aggiunto all\'archivio:</span> '.$key.'<br />';
					fwrite($fplog,"File ".$key." Aggiunto\n");
				}
			}
			$allegati=ap_get_all_allegati();
			foreach ($allegati as $allegato) {
			//echo $allegato->Allegato;
				if (substr(basename( $allegato->Allegato ),-4)==".pdf" or 
					substr(basename( $allegato->Allegato ),-4)==".p7m") 
					$zip->add(realpath($allegato->Allegato),PCLZIP_OPT_REMOVE_PATH,dirname($allegato->Allegato));
				$Risultato.='<span style="color:green;">Aggiunto all\'allegato:</span> '.$allegato->Allegato.'<br />';
				fwrite($fplog,"File ".$allegato->Allegato." Aggiunto\n");					
			}
			// Chiusura e momorizzazione del del file
			$Risultato.= "Archivio creato con successo: ".$Dir."/".$NomeFile.".zip";
			fwrite($fplog,"Archivio creato con successo: ".$Dir."/".$NomeFile.".zip\n");
		}
	}else{
		$DirLog=str_replace("\\","/",$Destinazione);
		$nomefileLog=$DirLog."/msg.txt";
		$fplog = @fopen($nomefileLog, "wb");
		$Risultato.="Non risulta Installata la libreria per Zippare i files indispensabile per la procedura<br />";
		fwrite($fplog,"Non risulta Installata la libreria per Zippare i files indispensabile per la procedura\n");
		return;	
	}
	//Svuoto cartella tmp che contiene i files dati
	ap_SvuotaDirectory($DirTmp,$fplog);
	fclose($fplog);
	$fpmsg = @fopen(Albo_DIR."/BackupDatiAlbo/tmp/msg.txt", "wb");
	fwrite($fpmsg,$Risultato);
	fclose($fpmsg);
	return $nomefileZip;
}


//***********************************************************************
function ap_BackupDatiFilesOLD($NomeFile,$Tipo="",$Destinazione=Albo_DIR){
global $wpdb;
	$tables=array(	$wpdb->table_name_Allegati,
					$wpdb->table_name_Atti,
					$wpdb->table_name_Categorie,
					$wpdb->table_name_Enti,
					$wpdb->table_name_RespProc);
	$Dir=str_replace("\\","/",$Destinazione.'/BackupDatiAlbo');
	$DirTmp=$Dir."/tmp";
	$DirLog=$Dir."/log";
	$DirAllegati=str_replace("\\","/",AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
	$ControlloDir="";
	
	if ($Tipo==""){
			$nomefileZip=$Dir."/".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_AlboPretorio_".$NomeFile.".log";
		}else{
			$nomefileZip=$Dir."/".$Tipo."_".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_".$Tipo."_AlboPretorio_".$NomeFile.".log";
		}	
	$Risultato="Risultato del Backup:<br />";
	if (class_exists('ZipArchive')) {
//		echo $Dir." <br />".$DirTmp." <br />".$DirAllegati." <br />".$DirLog."";exit;
		if (!is_dir ( $Dir)){
			if (!mkdir($Dir, 0755)) 
				$ControlloDir.="Non sono riuscito a creare la directory ".$Dir."\n Fine Operazione";
			else
				if (!mkdir($DirTmp, 0755))
					$ControlloDir.="Non sono riuscito a creare la directory ".$DirTmp."\n Fine Operazione";
				if (!mkdir($DirLog, 0755)) 
					$ControlloDir.="Non sono riuscito a creare la directory ".$DirLog."\n Fine Operazione";
		}
		if (!is_dir ($DirTmp) and $ControlloDir=="")
			if (!mkdir($DirTmp, 0755))
				  $ControlloDir.="Non sono riuscito a creare la directory ".$DirTmp."\n Fine Operazione";
		if (!is_dir ($DirLog) and $ControlloDir=="")
			if (!mkdir($DirLog, 0755))
				  $ControlloDir.="Non sono riuscito a creare la directory ".$DirLog."\n Fine Operazione";
		if ($ControlloDir!=""){
			return $ControlloDir;
		}
/*		if ($Tipo==""){
			$nomefileZip=$Dir."/".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_AlboPretorio_".$NomeFile.".log";
		}else{
			$nomefileZip=$Dir."/".$Tipo."_".$NomeFile.".zip";
			$nomefileLog=$DirLog."/Backup_".$Tipo."_AlboPretorio_".$NomeFile.".log";
		}*/	
		$fplog = @fopen($nomefileLog, "wb");
		fwrite($fplog,"Avvio Backup Dati ed Allegati Albo Pretrorio \n effettuato in data ".date("Ymd_Hi")."\n");
		ap_SvuotaDirectory($DirTmp,$fplog);
		fwrite($fplog,"Svuotamento tabella ".$DirTmp."\n");
		$fp = @fopen($DirTmp."/AlboPretorio".date("Ymd_Hi").".sql", "wb");
		$Risultato="";
		foreach ($tables as $table) {
			ap_backup_table($table,$fp);
			$Risultato.='<span style="color:green;">Tabella '.ap_backquote($table).' Aggiunta</span> <br />';
			fwrite($fplog,"Sql Tabella ".ap_backquote($table)." Aggiunta\n");
		}
		$UpdateProgressivo="UPDATE `".$wpdb->options."` SET `option_value` = '".get_option('opt_AP_AnnoProgressivo')."'	WHERE `option_name` ='opt_AP_AnnoProgressivo';\n";
		$UpdateProgressivo.="UPDATE `".$wpdb->options."` SET `option_value` = '".get_option('opt_AP_NumeroProgressivo')."' WHERE `option_name` ='opt_AP_NumeroProgressivo';";
		fwrite($fplog,"Sql Aggiornamento Tabella ".$wpdb->options." per Progressivo ed Anno Progressivo Aggiunti\n");
		fwrite($fp,$UpdateProgressivo);
		fclose($fp);
		if (is_dir($Dir) And is_dir($DirTmp)){
		 	$zip = new ZipArchive();
			// Crea l'archivio
			if ($zip->open($nomefileZip, ZIPARCHIVE::CREATE) === TRUE) {
				// Inizializzazione dell'iterator a cui viene passato 
				// l'iteratore ricorsivo delle directory a cui viene passata la directory da zippare
				$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($DirTmp));
				// Ciclo tutti gli elementi dell'iteratore, i files estratti dall'iteratore
				foreach ($iterator as $key=>$value) {
					if ($zip->addFile(realpath($key), basename($key)) === TRUE) {
						$Risultato.='<span style="color:green;">Aggiunto all\'archivio:</span> '.$key.'<br />';
						fwrite($fplog,"File ".$key." Aggiunto\n");
					}
					else{
						$Risultato.='<span style="color:red;">ERRORE</span>: il file '.$key.' non pu&ograve; essere aggiunto';
						fwrite($fplog,"File ".$key." non pu&ograve; essere Aggiunto\n");			
					}
				}
				$allegati=ap_get_all_allegati();
				foreach ($allegati as $allegato) {
				//echo $allegato->Allegato;
				if (substr(basename( $allegato->Allegato ),-4)==".pdf" or 
					substr(basename( $allegato->Allegato ),-4)==".p7m") 
					if ($zip->addFile(realpath($allegato->Allegato), basename($allegato->Allegato)) === TRUE){
						$Risultato.='<span style="color:green;">Aggiunto all\'allegato:</span> '.$allegato->Allegato.'<br />';
						fwrite($fplog,"File ".$allegato->Allegato." Aggiunto\n");					
					} 
					else{
						$Risultato.='<span style="color:red;">ERRORE</span>: il file '.$allegato->Allegato. ' non pu&ograve; essere aggiunto <br />';
						fwrite($fplog,"File ".$allegato->Allegato." non pu&ograve; essere Aggiunto\n");				
					}
				}
			// Chiusura e momorizzazione del del file
				$zip->close();
				$Risultato.= "Archivio creato con successo: ".$Dir."/".$NomeFile.".zip";
				fwrite($fplog,"Archivio creato con successo: ".$Dir."/".$NomeFile.".zip\n");
			}
		}
	}else{
		$DirLog=str_replace("\\","/",$Destinazione);
		$nomefileLog=$DirLog."/msg.txt";
		$fplog = @fopen($nomefileLog, "wb");
		$Risultato.="Non risulta Installata la libreria per Zippare i files indispensabile per la procedura<br />";
		fwrite($fplog,"Non risulta Installata la libreria per Zippare i files indispensabile per la procedura\n");
		return;	
	}
	//Svuoto cartella tmp che contiene i files dati
	ap_SvuotaDirectory($DirTmp,$fplog);
	fclose($fplog);
	$fpmsg = @fopen(Albo_DIR."/BackupDatiAlbo/tmp/msg.txt", "wb");
	fwrite($fpmsg,$Risultato);
	fclose($fpmsg);
	return $nomefileZip;
}

function ap_backquote($a_name) {
	if (!empty($a_name) && $a_name != '*') {
		if (is_array($a_name)) {
			$result = array();
			reset($a_name);
			while(list($key, $val) = each($a_name)) 
				$result[$key] = '`' . $val . '`';
			return $result;
		} else {
			return '`' . $a_name . '`';
		}
	} else {
		return $a_name;
	}
} 

?>