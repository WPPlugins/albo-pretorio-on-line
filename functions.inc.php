<?php
/**
 * Albo Pretorio AdminPanel - Funzioni Accesso ai dati
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 1.3
 */
 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

################################################################################
// Funzioni 
################################################################################

function Bonifica_Url(){
	foreach( $_REQUEST as $key => $value){
		if ($key!="page_id")	
			$_SERVER['REQUEST_URI'] = remove_query_arg($key, $_SERVER['REQUEST_URI']);		
	}
	$url='?';
	foreach( $_REQUEST as $key => $value)
		$url.=$key."=".$value;
	return $url;
}
function Estrai_PageID_Url(){
	foreach( $_REQUEST as $key => $value){
		if (strpos( $key,"page_id")!== false)		
			return $value;
	}
	return 0;
}
function ListaElementiArray($var) {
     foreach($var as $key => $value) {
            $output .= $key . "==>".$value . "\n";
     }
     return $output;
}

function cvdate($data){
	$rsl = explode ('-',$data);
	return mktime(0,0,0,$rsl[1], $rsl[2],$rsl[0]);
}

function oggi(){
	return date('Y-m-d');
}

function DateAdd($data,$incremento){
	$secondi=cvdate($data)+($incremento*86400);
	return date("Y-m-d",$secondi);
}

function SeDate($test,$data1,$data2){
	$data1=cvdate($data1);
	$data2=cvdate($data2);
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
function datediff($interval, $date1, $date2) {
    if(($date2==0) Or ($date2<$date1))
    	return -1;
	$seconds = cvdate($date2) - cvdate($date1);
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

function convertiData($dataEur){
$rsl = explode ('/',$dataEur);
$rsl = array_reverse($rsl);
return implode($rsl,'-');
}
function VisualizzaData($dataDB){
$rsl = explode ('-',$dataDB);
$rsl = array_reverse($rsl);
return implode($rsl,'/');
}
################################################################################
// Funzioni Log
################################################################################
/* 
Oggetto int(1)
	1=> Atti
	2=> Categorie
	3=> Allegati
	
TipoOperazione int(1)
	1=> Inserimento
	2=> Modifica
	3=> Cancellazione
	4=> Pubblicazione
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

function ap_get_all_Oggetto_log($Oggetto,$IdOggetto,$IdAtto=0){
global $wpdb, $current_user;
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Log WHERE Oggetto=". (int)$Oggetto ." and IdOggetto=".(int)$IdOggetto." or IdAtto=".(int)$IdAtto." order by Data;");	
}

################################################################################
// Funzioni Categorie
################################################################################


function ap_insert_categoria($cat_name,$cat_parente,$cat_descrizione,$cat_durata){
	global $wpdb;
	if ( false === $wpdb->insert($wpdb->table_name_Categorie,array('Nome' => $cat_name,'Genitore' => $cat_parente,'Descrizione' => $cat_descrizione,'Giorni' => $cat_durata)))	
        return new WP_Error('db_insert_error', 'Non sono riuscito ad inserire la Nuova Categoria'.$wpdb->last_error, $wpdb->last_error);
    else
    	ap_insert_log(2,1,$wpdb->insert_id,"{IdOggetto}==> $wpdb->insert_id
		                                    {Nome}==> $cat_name 
		                                    {Descrizione}==> $cat_descrizione 
											{Durata}==> $cat_durata 
											{Parente}==> $cat_parente");
}

function ap_memo_categorie($id,$cat_name,$cat_parente,$cat_descrizione,$cat_durata){
	global $wpdb;
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
    	ap_insert_log(2,2,$id,"{Nome}==> $cat_name {Descrizione}==> $cat_descrizione {Durata}==> $cat_durata {Parente}==> $cat_parente");
	
}


function ap_get_dropdown_categorie($select_name,$id_name,$class,$tab_index_attribute, $default="Nessuno", $DefVisId=true, $ConAtti=false  ) {
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
			$Sql.=" And Numero >0 AND DataFine >= '".oggi()."' AND DataInizio <= '".oggi()."'";
			break;
		case 2:
			$Sql=" And Numero >0 AND DataFine < '".oggi()."'";
			break;
	}
	$Sql.=";";
	return $wpdb->get_var( $wpdb->prepare( $Sql ) );
	
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
				$pix=(int) 8 + ($numAtti /$TotAtti)*10;
				$output .= "<a href='".$link."=".$c->IdCategoria."' title='Ci sono ".$numAtti." Atti nella Categoria ".$c->Nome."'><span style='font-size:".$pix."px;'>".$c->Nome."</span></a><br />\n";	
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
		return array("atti" => ap_num_atti_categorie($id),
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
################################################################################
// Funzioni Atti
################################################################################

function ap_insert_atto($Data,$Riferimento,$Oggetto,$DataInizio,$DataFine,$Note,$Categoria){
	global $wpdb;
	$Anno=date("Y");
	$Numero=0;
	$Data=convertiData($Data);
	$DataInizio=convertiData($DataInizio);
	$DataFine=convertiData($DataFine);
	if ( false === $wpdb->insert(
		$wpdb->table_name_Atti,array(
				'Numero' => $Numero,
				'Anno' =>  $Anno,
				'Data' => $Data,
				'Riferimento' => $Riferimento,
				'Oggetto' => $Oggetto,
				'DataInizio' => $DataInizio,
				'DataFine' => $DataFine,
				'Informazioni' => $Note,
				'IdCategoria' => $Categoria)))	
        return new WP_Error('db_insert_error', 'Non sono riuscito ad inserire il nuovo Atto'.$wpdb->last_error, $wpdb->last_error);
    else
    	ap_insert_log(1,1,$wpdb->insert_id,"{Numero} $Numero/$Anno 
							 {Data}==> $Data 
		                     {Riferimento}==> $Riferimento 
							 {Oggetto}==> $Oggetto 
							 {IdOggetto}==> $wpdb->insert_id
							 {DataInizio}==> $DataInizio
							 {DataFine}==> $DataFine"
							  );
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

function ap_memo_atto($id,$Data,$Riferimento,$Oggetto,$DataInizio,$DataFine,$Note,$Categoria){
	global $wpdb;
	$Data=convertiData($Data);
	$DataInizio=convertiData($DataInizio);
	$DataFine=convertiData($DataFine);
	if ( false === $wpdb->update($wpdb->table_name_Atti,
					array('Data' => $Data,
						  'Riferimento' => $Riferimento,
						  'Oggetto' => $Oggetto,
						  'DataInizio' => $DataInizio,
						  'DataFine' => $DataFine,
						  'Informazioni' => $Note,
						  'IdCategoria' => $Categoria),
						  array( 'IdAtto' => $id ),
						  array('%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%d'),
						  array('%d')))
    	return new WP_Error('db_update_error', 'Non sono riuscito a modifire l\' Atto'.$wpdb->last_error, $wpdb->last_error);
    else
    	ap_insert_log(1,2,$id,"{Numero} $Numero/$Anno 
							 {Data}==> $Data 
		                     {Riferimento}==> $Riferimento 
							 {Oggetto}==> $Oggetto 
							 {DataInizio}==> $DataInizio
							 {DataFine}==> $DataFine
							 {Note}==> $Note
							 {IdCategoria}==> $Categoria");
	
}

function ap_update_selettivo_atto($id,$ArrayCampiValori,$ArrayTipi,$TestaMsg){
	global $wpdb;
	if ( false === $wpdb->update($wpdb->table_name_Atti,$ArrayCampiValori,array( 'IdAtto' => $id ),$ArrayTipi))
    	return new WP_Error('db_update_error', 'Non sono riuscito a modifire l\' Atto'.$wpdb->last_error, $wpdb->last_error);
    else{
		ap_insert_log(1,2,$id,$TestaMsg.ListaElementiArray($ArrayCampiValori));
		return 'Atto Aggiornato: %%br%%'.ListaElementiArray($ArrayCampiValori);	
	}
    	
}

function ap_approva_atto($IdAtto){
	global $wpdb;
	$NumeroDaDb=ap_get_last_num_anno(date("Y"));
	$risultato=ap_get_atto($IdAtto);
	$risultato=$risultato[0];
	$NumeroOpzione=get_option('opt_AP_NumeroProgressivo');
	if ($NumeroDaDb!=$NumeroOpzione){
		return "Atto non PUBBLICATO:%%br%%Progressivo da ultima pubblicazione=$NumeroDaDb%%br%% Progressivo da parametri=$NumeroOpzione";
	}else{
		$x=$wpdb->update($wpdb->table_name_Atti,
									 array('Numero' => $NumeroOpzione),
									 array( 'IdAtto' => $IdAtto ),
									 array('%d',
										   '%s'),
									 array('%d'));
	//  visualizza Sql Updateecho $wpdb->print_error();exit;
	 	if ($x==0){
	    	return 'Atto non PUBBLICATO:%%br%%Errore: '.$wpdb->last_error;
	    }
	    else{
			ap_insert_log( 2,4,$IdAtto,"{Numero Assegnato}==> $NumeroOpzione ");	
			$NumeroOpzione+=1;
			update_option('opt_AP_NumeroProgressivo',$NumeroOpzione );
			return 'Atto PUBBLICATO';
		}
	}
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
			$Sql="SELECT Anno FROM $wpdb->table_name_Atti WHERE Numero >0 AND DataFine >= '".oggi()."' AND DataInizio <= '".oggi()."' GROUP BY Anno;";
			break;
		case 2:
			$Sql="SELECT Anno FROM $wpdb->table_name_Atti WHERE Numero >0 AND DataFine < '".oggi()."' GROUP BY Anno;";
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

function ap_get_all_atti($Stato=0,$Anno=0,$Categoria=0,$Oggetto='',$Dadata=0,$Adata=0,$OrderBy="",$DaRiga=0,$ARiga=20,$Conteggio=false){
/* Stato:
		 0 - tutti
		 1 - in corso di validità
		 2 - scaduti
		 3 - da pubblicare
	$Conteggio:
		 false - Estrazione Dati	
		 true - Conteggio
*/	
	global $wpdb;
	if ($OrderBy!=""){
		$OrderBy=" Oerder By ".$OrderBy;
	}
	$Limite=" Limit ".$DaRiga.",".$ARiga;
	switch ($Stato){
		case 0:
			$Selezione=' WHERE 1';
			break;
		case 1:
			$Selezione=' WHERE DataInizio<="'.oggi().'" AND DataFine>="'.oggi().'" AND Numero>0'; 
			break;
		case 2:
			$Selezione=' WHERE DataInizio<="'.oggi().'" AND DataFine<="'.oggi().'" AND Numero>0'; 
			break;
		case 3:
			$Selezione=' WHERE Numero=0'; 
			break;
	}
	if ($Anno!=0){
		$Selezione.=' And Anno="'.$Anno.'"';
	}
	if ($Categoria!=0){
		$Selezione.=' And IdCategoria="'.$Categoria.'"';
	}
	if ($Oggetto!=''){
		$Selezione.=' And Oggetto like "%'.$Oggetto.'%"';
	}	
	if ($Dadata!=0){
		$Selezione.=' And DataInizio>="'.convertiData($Dadata).'"';
	}	
	if ($Adata!=0){
		$Selezione.=' And DataFine<="'.convertiData($Adata).'"';
	}	
//	echo "SELECT * FROM $wpdb->table_name_Atti $Selezione $OrderBy $Limite;";
	if ($Conteggio){
		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Atti $Selezione;" ));	
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
################################################################################
// Funzioni Allegati
################################################################################
function ap_num_allegati_atto($id){
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->table_name_Allegati WHERE IdAtto=%d",$id));
	
}
function ap_get_all_allegati_atto($idAtto){
	global $wpdb;
	return $wpdb->get_results("SELECT * FROM $wpdb->table_name_Allegati WHERE IdAtto=". (int)$idAtto.";");
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
    	ap_insert_log(3,1,$wpdb->insert_id,"{Titolo} $TitoloAllegato 
							 {Allegato}==> $Allegato 
		                     {IdAtto}==> $IdAtto" 
							  ,$IdAtto);
}

function ap_del_allegato_atto($idAllegato,$idAtto=0,$nomeAllegato=''){
global $wpdb;
	$allegato=ap_get_allegato_atto($idAllegato);
	if (file_exists($allegato[0]->Allegato) && is_file($allegato[0]->Allegato))
		unlink($allegato[0]->Allegato);
	$wpdb->query($wpdb->prepare( "DELETE FROM $wpdb->table_name_Allegati WHERE	IdAllegato=%d",$idAllegato));
	ap_insert_log(3,3,$id,"{Nome Allegato}==> $nomeAllegato ",$idAtto);

	return True;

}
function UniqueFileName($filename,$inc=0){
	$baseName=$filename;
	while (file_exists($filename)){
		$arrfile=explode(".", $baseName);
		$ext=end($arrfile);
		$fname='';
		for ($i=0;$i<count($arrfile)-1;$i++){
			$fname.=$arrfile[$i];
		}
		$inc++;
		$filename=$fname.$inc.'.'.$ext;
	}
	return $filename;	
}
function ap_get_num_allegati($id){
	global $wpdb;
	return (int)($wpdb->get_var( $wpdb->prepare( "SELECT COUNT(IdAllegato) FROM $wpdb->table_name_Allegati WHERE IdAtto=%d",(int)$id)));
}
?>