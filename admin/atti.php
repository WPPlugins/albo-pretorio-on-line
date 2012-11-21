<?php
/**
 * Albo Pretorio AdminPanel - Gestione Atti
 * 
 * @package Albo Pretorio On line
 * @author Scimone Ignazio
 * @copyright 2011-2014
 * @since 2.3
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

switch ($_REQUEST['action']){
	case "annulla-atto":
		Lista_Atti(($_REQUEST['motivo']=="null") ? "Operazione di Annullamento Atto <strong>Annullata</strong>" :ap_annulla_atto($_REQUEST['id'],$_REQUEST['motivo']));
		break;
	case "logatto" :
		echo json_encode(CreaLog(1,$IdAtto,0));
		die();
		break;
	case "view-atto" :
		View_atto($_REQUEST['id']);
		break;
	case "new-atto" :
		Nuovo_atto();
		break;
	case "edit-atto" :
		Edit_atto($_REQUEST['id']);
		break;
	case "pubblica-atto":
		Lista_Atti(ap_approva_atto($_REQUEST['id']));
		break;
	case "setta-anno":
		update_option('opt_AP_AnnoProgressivo',date("Y") );
	  	update_option('opt_AP_NumeroProgressivo',0 );
		PreApprovazione($_REQUEST['id'],"Anno Albo settato a ".date("Y")." Numero prograssivo settato a 0");
		break;
	case "approva-atto" :
		if ($_REQUEST['apa']){
			$ret=ap_update_selettivo_atto($_REQUEST['id'],array('Anno' => $_REQUEST['apa']),array('%s'),"Modifica in Approvazione\n");
		}
		if ($_REQUEST['pnp']){
			update_option( 'opt_AP_NumeroProgressivo', $_REQUEST['pnp']);
		}
		if ($_REQUEST['udi']){
			$ret=ap_update_selettivo_atto($_REQUEST['id'],array('DataInizio' => $_REQUEST['udi']),array('%s'),"Modifica in Approvazione\n");	
		}
		if ($_REQUEST['udf']){
			$ret=ap_update_selettivo_atto($_REQUEST['id'],array('DataFine' => $_REQUEST['udf']),array('%s'),"Modifica in Approvazione\n");	
		}
		PreApprovazione($_REQUEST['id'],$ret);
		break;
	case "allegati-atto" :
		Allegati_atto($_REQUEST['id'],$_REQUEST['messaggio']);
		break;
	case "edit-allegato-atto" :
		Allegati_atto($_REQUEST['id'],$_REQUEST['messaggio'],$_REQUEST['idAlle']);
		break;
	case "UpAllegati":
		include_once ( dirname (__FILE__) . '/allegati.php' );
		break;
	default:
		Lista_Atti();
		break;
}
unset($_REQUEST['action']);

function PreApprovazione($id,$ret=''){
global $wpdb;
if (!current_user_can('admin_albo')){
	echo '<div id="message" class="updated"><p>Questa Operazione non ti &egrave; consentita, operazione di pertinenza dell\'amministratore dell\' Albo</p></div>';
	return;
}
if ($ret!=""){
	$ret=str_replace("%%br%%","<br />",$ret);
}
	$NumeroDaDb=ap_get_last_num_anno(date("Y"));
	$atto=ap_get_atto($id);
	$atto=$atto[0];
	$dif=datediff("d",cvdate($atto->DataInizio),cvdate($atto->DataFine));
	$NumeroOpzione=get_option('opt_AP_NumeroProgressivo');
echo'
<div class="wrap">
<div style="display:inline;float:left;"><img src="'.Albo_URL.'/img/approva32.png" alt="Icona Approvazione Atto"/></div>
	<h2 style="display:inline;margin-left:10px;">Approvazione Atto</h2>
	<a href="#" class="add-new-h2 tornaindietro" rel="'.$_SERVER[PHP_SELF].'?page=atti" >Torna indietro</a>';
	if ( $ret!="" ) {
		echo '<div id="message" class="updated"><p>'.$ret.'</p></div>';
	}
echo'<br class="clear" />';
if(get_option('opt_AP_AnnoProgressivo')!=date("Y")){
	echo '<div style="border: medium groove Blue;margin-top:10px;">
			<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
				<form id="agg_anno_progressivo" method="post" action="?page=atti">
				<input type="hidden" name="action" value="setta-anno" />
				<input type="hidden" name="id" value="'.$id.'" />
				<input type="submit" name="submit" id="submit" class="button" value="Aggiorna Anno Albo ed Azzera numero Progressivo"  />
				</form>
			</div>
		</div>';
}else
{
echo'<br />
<table class="widefat">
	<thead>	
	<tr>
		<th colspan="2" style="text-align:center;font-size:2em;">Informazioni</th>
		<th>Stato</th>
		<th>Operazioni</th>
	</tr>
	</thead>
    <tbody id="dati-atto">
	<tr>
		<td>Anno Atto</td>
		<td>'.$atto->Anno.'</td>';
		if ($atto->Anno==date("Y")){
		 	$Passato=true;
			echo '<td colspan="2">Ok</td>';
		}else{
		 	$Passato=false;
			echo '<td>Verificata incongruenza, bisogna rimediare prima di proseguire</td>
			      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;apa='.date("Y").'" class="add-new-h2">Imposta Anno Pubblicazione a '.date("Y").'</td>';
		}
		echo '</tr>';
		if($Passato){
			echo '<tr>
			<td>Numero Atto</td>
			<td>da Parametri '.get_option('opt_AP_NumeroProgressivo').' Progressivo da ultima pubblicazione '.$NumeroDaDb.'</td>';
			if ($NumeroDaDb==$NumeroOpzione){
			 	$Passato=true;
				echo '<td colspan="2">Ok</td>';
			}else{
			 	$Passato=false;
				echo '<td>Verificata incongruenza, bisogna rimediare prima di proseguire</td>
				      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;pnp='.$NumeroDaDb.'" class="add-new-h2">Imposta Parametro a '.$NumeroDaDb.'</td>';
			}
			echo '</tr>';
		}
		if($Passato){
			echo '<tr>
					<td>Data Inizio Pubblicazione</td>
					<td>'.$atto->DataInizio.'</td>';
			if($atto->DataInizio==oggi()){
				$Passato=true;
				echo '<td colspan="2">Ok</td>';
			}else{
	 			$Passato=false;
	   			echo '<td>Aggiornare la data di Inizio Pubblicazione</td>
			      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;udi='.oggi().'" class="add-new-h2">Aggiorna a '.oggi().'</td>';
			}
			echo "</tr>";
		}
		if($Passato){
 			$categoria=ap_get_categoria($atto->IdCategoria);
 			$incrementoStandard=$categoria[0]->Giorni;
 			$newDataFine=DateAdd($atto->DataInizio,$incrementoStandard);
 			$differenza=datediff("d", $atto->DataInizio, $atto->DataFine);
			$differenza=($differenza==-1) ? 0 : $differenza;
			echo '<tr>
					<td>Data Fine Pubblicazione</td>
					<td>'.$atto->DataFine.' Giorni Pubblicazione Atto '.$differenza .' Giorni Pubblicazione standard Categoria '.$categoria[0]->Giorni.'</td>';
				//	echo $atto->DataFine.' '.$atto->DataInizio. ' '.SeDate("<=",$atto->DataFine,$atto->DataInizio);
			if(SeDate(">=",$atto->DataFine,$atto->DataInizio)){
				$Passato=true;
				if (datediff("d", $atto->DataInizio, $atto->DataFine)== $categoria[0]->Giorni){
					echo '<td colspan="2">Ok</td>';
				}else{
					echo '<td>Ok</td>';
					echo '<td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;udf='.$newDataFine.'" class="add-new-h2">Aggiorna a '.$newDataFine.'</a></td>';
				}
			}else{
	 			$Passato=false;
	   			echo '<td>Aggiornare la data di Fine Pubblicazione</td>
			      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;udf='.$newDataFine.'" class="add-new-h2">Aggiorna a '.$newDataFine.'</a></td>';
			}
			echo '</tr>';
		}
		if($Passato){
 			$numAllegati=ap_get_num_allegati($id);
			echo '<tr>
					<td>Allegati</td>
					<td>N. '.$numAllegati.'</td>';
			if($numAllegati>0){
				$Passato=true;
					echo '<td colspan="2">Ok</td>';
				}else{
					$Passato=false;
					echo '<td>Da revisionare</td>
					      <td><a href="?page=atti&amp;id='.$id.'&amp;action=UpAllegati&amp;ref=approva-atto" class="add-new-h2">Inserisci Allegato</a></td>';
				}
			echo '</tr>';
		}
echo '</tbody>
	</table>';
if ($Passato){
echo'
<div style="border: medium groove Blue;margin-top:10px;">
	<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
		<form id="approva-atto" method="post" action="?page=atti">
		<input type="hidden" name="action" value="pubblica-atto" />
		<input type="hidden" name="id" value="'.$id.'" />
		<input type="submit" name="submit" id="submit" class="button" value="Pubblica Atto"  />
		</form>
	</div>
</div>
<div id="col-right">
<div class="col-wrap">
<h3>Allegati</h3>';
$righe=ap_get_all_allegati_atto($id);
echo'
	<table class="widefat">
	    <thead>
		<tr>
			<th style="font-size:2em;">Operazioni</th>
			<th style="font-size:2em;">Allegato</th>
			<th style="font-size:2em;">File</th>
		</tr>
	    </thead>
	    <tbody id="righe-log">';
foreach ($righe as $riga) {
	echo '<tr>
			<td>	
					<a href="'.DaPath_a_URL($riga->Allegato).'" target="_parent">
							<img src="'.Albo_URL.'/img/view.png" alt="View" title="View" />
					</a>
			</td>
			<td >'.$riga->TitoloAllegato.'</td>
			<td >'. basename( $riga->Allegato).'</td>
		</tr>';
}
echo '    </tbody>
	</table>
</div>
</div>
<div id="col-left">
<div class="col-wrap">
<h3>Dati Atto</h3>
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="2" style="text-align:center;font-size:2em;">Dati atto</th>
		</tr>
	    </thead>
	    <tbody id="dati-atto">
		<tr>
			<th style="width:20%;">Numero Albo</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->Numero."/".$atto->Anno.'</td>
		</tr>
		<tr>
			<th>Data</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->Data.'</td>
		</tr>
		<tr>
			<th>Codice di Riferimento</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($atto->Riferimento).'</td>
		</tr>
		<tr>
			<th>Oggetto</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($atto->Oggetto).'</td>
		</tr>
		<tr>
			<th>Data inizio Pubblicazione</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->DataInizio.'</td>
		</tr>
		<tr>
			<th>Data fine Pubblicazione</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->DataFine.'</td>
		</tr>
		<tr>
			<th>Note</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($atto->Informazioni).'</td>
		</tr>
		<tr>
			<th>Categoria</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($categoria[0]->Nome).'</td>
		</tr>
	    </tbody>
	</table></div>
</div>';
}
}
echo '</div>';
}


function Nuovo_atto(){
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	if ($_REQUEST['Data']=="")
		$dataCorrente=date("d/m/Y");
	else
		$dataCorrente=$_REQUEST['Data'];
	echo '
<div class="wrap">
<div style="display:inline;float:left;">
	<img src="'.Albo_URL.'/img/atti32.png" alt="Icona Nuovo Atto"/>
</div>';
echo '	<h2 style="display:inline;margin-left:10px;">Nuovo Atto</h2>
	<a href="#" class="add-new-h2 tornaindietro" rel="'.$_SERVER[PHP_SELF].'?page=atti" >Torna indietro</a>';
if ( $_REQUEST['msg'] !="") {
	echo '<div id="message" class="updated"><p>'.stripslashes($_REQUEST['msg']).'</p></div>';
}
echo'<div id="col-container">
<form id="addatto" method="post" action="?page=atti" class="validate">
<input type="hidden" name="action" value="add-atto" />
<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />
'.wp_nonce_field('add-tag', '_wpnonce_add-tag').'
<br />
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="3" style="text-align:center;font-size:2em;">Dati atto</th>
		</tr>
	    </thead>
	    <tbody id="dati-atto">
		<tr>
			<th>Ente Emittente</th>
			<td>'.ap_get_dropdown_enti('Ente','Ente','postform','',$_REQUEST['Ente']).'</td>
			<td>Ente che pubblica l\'atto;<br />potrebbe essere diverso dall\'ente titolare del sito web se la pubblicazione avviene per conto di altro ente</td>
		</tr>
		<tr>
			<th>Numero Albo</th>
			<td>&nbsp;&nbsp;/'.date("Y").'</td>
			<td>Numero progressivo generato dal programma</td>
		</tr>
		<tr>
			<th>Data</th>
			<td><input name="Data" id="Calendario1" type="text" value="'.$dataCorrente.'" size="8" aria-required="true" /></td>
			<td>Data di codifica dell\'atto</td>
		</tr>
		<tr>
			<th>Codice di Riferimento</th>
			<td><input name="Riferimento" id="riferimento-atto" type="text" size="20" aria-required="true" value="'.$_REQUEST['Riferimento'].'" /></td>
			<td>Numero di riferimento dell\'atto, es. N. Protocollo</td>
		</tr>
		<tr>
			<th>Oggetto</th>
			<td><textarea name="Oggetto" id="oggetto-atto" rows="2" cols="60" maxlength="150" aria-required="true">'.$_REQUEST['Oggetto'].'</textarea></td>
			<td colspan="2">Oggetto, descrizione sintetica dell\'atto</td>
		</tr>
		<tr>
			<th>Data inizio Pubblicazione</th>
			<td><input name="DataInizio" id="Calendario2" type="text" size="8" aria-required="true" value="'.$_REQUEST['DataInizio'].'" /></td>
			<td>Data Inizio Pubblicazione dell\'atto</td>
		</tr>
		<tr>
			<th>Data fine Pubblicazione</th>
			<td><input name="DataFine" id="Calendario3" type="text" size="8" aria-required="true" value="'.$_REQUEST['DataFine'].'" /></td>
			<td>Data Fine Pubblicazione dell\'atto</td>
		</tr>
		<tr>
			<th>Note</th>
			<td><textarea  name="Note" id="Note-atto" rows="5" cols="60" wrap="ON" maxlength="255">'.$_REQUEST['Note'].'</textarea></td>
			<td>Descrizione dell\'atto</td>
		</tr>
		<tr>
			<th>Categoria</th>
			<td>'.ap_get_dropdown_categorie('Categoria','Categoria','postform', '',$_REQUEST['Categoria']).'</td>
			<td>Categoria in cui viene collocato l\'atto, questo sistema permette di ragguppare gli oggetti in base alla loro natura</td>
		</tr>
		<tr>
			<th>Responsabile Procedimento</th>
			<td>'.ap_get_dropdown_responsabili('Responsabile','Responsabile','postform','',$_REQUEST['Responsabile']).'</td>
			<td>Persona preposta dall\'ente alla gestione del procedimento che ha generato l\'atto</td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" name="submit" id="submit" class="button" value="Aggiungi Atto"  /></td>
		</tr>
	    </tbody>
	</table>
</form>
</div>
</div>';	
}

function Edit_atto($id){
$atto=ap_get_atto($id);
$atto=$atto[0];
	echo '
<div class="wrap">
<div style="display:inline;float:left;"><img src="'.Albo_URL.'/img/atti32.png" alt="Icona Nuovo Atto" /></div>
<h2 style="display:inline;margin-left:10px;">Modifica Atto</h2><a <a href="'.$_SERVER[HTTP_REFERER].'" class="add-new-h2">Torna indietro</a>
<div id="col-container">
<form id="addatto" method="post" action="?page=atti" class="validate">
<input type="hidden" name="action" value="memo-atto" />
<input type="hidden" name="id" value="'.$_REQUEST['id'].'" />
'.wp_nonce_field('add-tag', '_wpnonce_add-tag').'
<br />
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="3" style="text-align:center;font-size:2em;">Dati atto</th>
		</tr>
	    </thead>
	    <tbody id="dati-atto">
		<tr>
			<th>Ente</th>
			<td>'.ap_get_dropdown_enti('Ente','Ente','postform','',$atto->Ente).'</td>
			<td>Ente che pubblica l\'atto;<br />potrebbe essere diverso dall\'ente titolare del sito web se la pubblicazione avviene per conto di altro ente</td>
		</tr>
		<tr>
			<th>Numero Albo</th>
			<td>'.$atto->Numero.'/'.$atto->Anno.'</td>
			<td>Numero progressivo generato dal programma</td>
		</tr>
		<tr>
			<th>Data</th>
			<td><input name="Data" id="Calendario1" type="text" value="'.$atto->Data.'" size="8" aria-required="true" /></td>
			<td>Data di codifica dell\'atto</td>
		</tr>
		<tr>
			<th>Codice di Riferimento</th>
			<td><input name="Riferimento" id="riferimento-atto" type="text" value="'.stripslashes($atto->Riferimento).'" size="20" aria-required="true" /></td>
			<td>Numero di riferimento dell\'atto, es. N. Protocollo</td>
		</tr>
		<tr>
			<th>Oggetto</th>
			<td><textarea name="Oggetto" id="oggetto-atto" rows="2" cols="60" maxlength="150" aria-required="true">'.stripslashes($atto->Oggetto).'</textarea></td>
			<td colspan="2">Oggetto, descrizione sintetica dell\'atto</td>
		</tr>
		<tr>
			<th>Data inizio Pubblicazione</th>
			<td><input name="DataInizio" id="Calendario2" type="text" value="'.$atto->DataInizio.'" size="8" aria-required="true" /></td>
			<td>Data Inizio Pubblicazione dell\'atto</td>
		</tr>
		<tr>
			<th>Data fine Pubblicazione</th>
			<td><input name="DataFine" id="Calendario3" type="text" value="'.$atto->DataFine.'" size="8" aria-required="true" /></td>
			<td>Data Fine Pubblicazione dell\'atto</td>
		</tr>
		<tr>
			<th>Note</th>
			<td><textarea  name="Note" rows="5" cols="60" wrap="ON" maxlength="255">'.stripslashes($atto->Informazioni).'</textarea></td>
			<td>Descrizione dell\'atto</td>
		</tr>
		<tr>
			<th>Categoria</th>
			<td>'.ap_get_dropdown_categorie('Categoria','Categoria','postform','',$atto->IdCategoria).'</td>
			<td>Categoria in cui viene collocato l\'atto, questo sistema permette di ragguppare gli oggetti in base alla lor natura</td>
		</tr>
		<tr>
			<th>Responsabile Procedimento </th>
			<td>'.ap_get_dropdown_responsabili('Responsabile','Responsabile','postform','',$atto->RespProc).'</td>
			<td>Persona preposta dall\'ente alla gestione del procedimento che ha generato l\'atto</td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" name="submit" id="submit" class="button" value="Memorizza Modifiche Atto" /></td>
		</tr>
	    </tbody>
	</table>
</form>
</div>
</div>';	
}

function Allegati_atto($IdAtto,$messaggio="",$IdAllegato=0){
	$risultato=ap_get_atto($IdAtto);
	$risultato=$risultato[0];
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$dirUpload =  get_option('opt_AP_FolderUpload').'/';
	echo '
<div class="wrap">
<div style="display:inline;float:left;">
	<img src="'.Albo_URL.'/img/view32.png" alt="Icona Visualizza Atto"/>
</div>	
	<h2  style="display:inline;margin-left:10px;">Atto</h2>
	<a href="#" class="add-new-h2 tornaindietro" rel="'.$_SERVER[PHP_SELF].'?page=atti" >Torna indietro</a>';
	if ( $messaggio!="" ) {
	 	$messaggio=str_replace("%%br%%", "<br />", $messaggio);
		print('<div id="message" class="updated"><p>'.$messaggio.'</p></div>');
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('messaggio'), $_SERVER['REQUEST_URI']);
	}
echo'
<div id="col-container">
<div id="col-right">
<div class="col-wrap">';
if ($IdAllegato!=0){
 	$allegato=ap_get_allegato_atto($IdAllegato);
 	$allegato=$allegato[0];
	echo '<h3>Modifica Allogato</h3>
	<form id="allegato"  method="post" action="?page=atti" class="validate">
	<input type="hidden" name="action" value="update-allegato-atto" />
	<input type="hidden" name="id" value="'.$IdAtto.'" />
	<input type="hidden" name="idAlle" value="'.$IdAllegato.'" />
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="3" style="text-align:center;font-size:1.2em;">Dati Allegato</th>
		</tr>
	    </thead>
	    <tbody id="dati-allegato">
		<tr>
			<th>Descrizione Allegato</th>
			<td><textarea  name="titolo" rows="4" cols="50" wrap="ON" maxlength="255">'.$allegato->TitoloAllegato.'</textarea></td>
		</tr>
		<tr>
			<th>File:</th>
			<td>'.$allegato->Allegato.'</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" id="submit" class="button" value="Aggiorna Allegato"  />&nbsp;&nbsp;
			    <input type="submit" name="annulla" id="annulla" class="button" value="Annulla"  />
		    </td>
		</tr>
	    </tbody>
	</table>
	</form>';	
}else{
	echo'
	<h3>Allegati <a href="?page=atti&amp;id='.$IdAtto.'&amp;action=UpAllegati" class="add-new-h2">Aggiungi nuovo</a> </h3>';
	$righe=ap_get_all_allegati_atto($IdAtto);
	echo'
		<table class="widefat">
		    <thead>
			<tr>
				<th style="font-size:1.2em;">Operazioni</th>
				<th style="font-size:1.2em;">Allegato</th>
				<th style="font-size:1.2em;">File</th>
			</tr>
		    </thead>
		    <tbody id="righe-log">';
	foreach ($righe as $riga) {
		echo '<tr>
				<td>	
					<a href="?page=atti&amp;action=delete-allegato-atto&amp;idAllegato='.$riga->IdAllegato.'&amp;idAtto='.$IdAtto.'&amp;Allegato='.$riga->TitoloAllegato.'" rel="'.$riga->TitoloAllegato.'" class="da">
						<img src="'.Albo_URL.'/img/cross.png" alt="Delete" title="Delete" />
					</a>
					<a href="?page=atti&amp;action=edit-allegato-atto&amp;id='.$IdAtto.'&amp;idAlle='.$riga->IdAllegato.'" rel="'.$riga->TitoloAllegato.'">
						<img src="'.Albo_URL.'/img/edit.png" alt="Edit" title="Edit" />
					</a>
					<a href="'.DaPath_a_URL($riga->Allegato).'" target="_parent">
							<img src="'.Albo_URL.'/img/view.png" alt="View" title="View" />
					</a>
				</td>
				<td >'.$riga->TitoloAllegato.'</td>
				<td >'. basename( $riga->Allegato).'</td>
			</tr>';
	}
	echo '    </tbody>
		</table>';
}
echo'</div>
</div>
<div id="col-left">
<div class="col-wrap">
<h3>Dati Atto</h3>
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="2" style="text-align:center;font-size:1.2em;">Dati atto</th>
		</tr>
	    </thead>
	    <tbody id="dati-atto">
		<tr>
			<th style="width:20%;">Numero Albo</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
		</tr>
		<tr>
			<th>Data</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.VisualizzaData($risultato->Data).'</td>
		</tr>
		<tr>
			<th>Codice di Riferimento</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Riferimento).'</td>
		</tr>
		<tr>
			<th>Oggetto</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Oggetto).'</td>
		</tr>
		<tr>
			<th>Data inizio Pubblicazione</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.VisualizzaData($risultato->DataInizio).'</td>
		</tr>
		<tr>
			<th>Data fine Pubblicazione</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.VisualizzaData($risultato->DataFine).'</td>
		</tr>
		<tr>
			<th>Note</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Informazioni).'</td>
		</tr>
		<tr>
			<th>Categoria</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultatocategoria->Nome).'</td>
		</tr>
	    </tbody>
	</table></div>
</div>
</div>
</div>';	
}
function View_atto($IdAtto){
	global $AP_OnLine;
?>
<style type='text/css'>
#loading { clear:both; background:url(images/loading.gif) center top no-repeat; text-align:center;padding:33px 0px 0px 0px; font-size:12px;display:none; font-family:Verdana, Arial, Helvetica, sans-serif; }
</style>
<?php
	$risultato=ap_get_atto($IdAtto);
	$risultato=$risultato[0];
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$responsabile=ap_get_responsabile($risultato->RespProc);
	$responsabile=$responsabile[0];
	$NomeEnte=ap_get_ente($risultato->Ente);
	$NomeEnte=$NomeEnte->Nome;
	echo '
<div class="wrap nosubsub">
<img src="'.Albo_URL.'/img/view32.png" alt="Icona Visualizza Atto" style="display:inline;float:left;margin-top:10px;"/>
<h2 style="margin-left:40px;">Atto<a href="'.$_SERVER[HTTP_REFERER].'" class="add-new-h2" >Torna indietro</a></h2>

<div id="col-container">
	<div id="col-right">
		<div class="col-wrap">
			<h3>Log</h3>
			<form action="" method="POST" id="formlog">
			<input type="submit" name="action" id="LogAtti" value="Atti" class="infolog"/>
			<input type="submit" name="action" id="LogAllegati" value="Allegati" class="infolog"/>
			<input type="submit" name="action" id="LogStatisticheVisite" value="Statistiche Visite" class="infolog"/>
			<input type="submit" name="action" id="LogStatisticheDownload" value="Statistiche Download" class="infolog"/>
			</form>
			<div id="loading">LOADING!</div>
				<div id="DatiLog">'.$AP_OnLine->CreaLog(1,$IdAtto,0).'</div> 
		</div>
	</div>
<div id="col-left">
	<div class="col-wrap">
		<br class="clear" />	
		<table class="widefat">
		    <thead>
			<tr>
				<th colspan="2" style="text-align:center;font-size:1.2em;">Dati atto</th>
			</tr>
		    </thead>
		    <tbody id="dati-atto">
			<tr>
				<th style="width:20%;">Ente emittente</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$NomeEnte.'</td>
			</tr>';
		if($risultato->DataAnnullamento!='0000-00-00')		
			echo '		<tr>
				<th style="width:20%;">Data Annullamento</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:middle;">'.VisualizzaData($risultato->DataAnnullamento).'</td>
			</tr>
	    	<tr>
				<th style="width:20%;">Motivo Annullamento</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:top;">'.$risultato->MotivoAnnullamento.'</td>
			</tr>';
		echo '		<tr>
				<th style="width:20%;">Numero Albo</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
			</tr>
			<tr>
				<th>Data</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.VisualizzaData($risultato->Data).'</td>
			</tr>
			<tr>
				<th>Codice di Riferimento</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Riferimento).'</td>
			</tr>
			<tr>
				<th>Oggetto</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Oggetto).'</td>
			</tr>
			<tr>
				<th>Data inizio Pubblicazione</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.VisualizzaData($risultato->DataInizio).'</td>
			</tr>
			<tr>
				<th>Data fine Pubblicazione</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.VisualizzaData($risultato->DataFine).'</td>
			</tr>
			<tr>
				<th>Note</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Informazioni).'</td>
			</tr>
			<tr>
				<th>Categoria</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultatocategoria->Nome).'</td>
			</tr>
			<tr>
				<th>Responsabile procedimento</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($responsabile->Cognome.' '.$responsabile->Nome.' '.$responsabile->Email).'</td>
			</tr>	    </tbody>
		</table>
	</div>';
echo '<h3>Allegati</h3>
	<div class="Visalbo">';
$allegati=ap_get_all_allegati_atto($IdAtto);
foreach ($allegati as $allegato) {
 	switch (ExtensionType($allegato->Allegato)){
		case 'pdf':
			$Estensione="Pdf.png";
			$Verifica="";
			break;
		case "p7m":
			$Estensione="firmato.png";
			$Verifica='<br /><a href="http://postecert.poste.it/verificatore/servletverificatorep7m?tipoOp=10" onclick="window.open(this.href,\'_blank\');return false;">Verifica firma con servizio fornito da poste.it</a>';
			break;
	}
	echo '<div style="border: thin dashed;font-size: 1em;">
			<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
				    <img src="'.Albo_URL.'img/'.$Estensione.'" alt="Icona tipo allegato" />
			</div>
			<div style="margin-top:0;">
				<p style="margin-top:0;">'.$allegato->TitoloAllegato.' <br />';
			if (is_file($allegato->Allegato))
				echo '        <a href="'.DaPath_a_URL($allegato->Allegato).'" >'. basename( $allegato->Allegato).'</a> ('.Formato_Dimensione_File(filesize($allegato->Allegato)).')'.$Verifica;
			else
				echo basename( $allegato->Allegato)." File non trovato, il file &egrave; stao cancellato o spostato!";
echo'				</p>
			</div>
			<div style="clear:both;"></div>
		</div>';
	}
echo '	</div>
		</div>
	</div>
</div>';	
}

function Lista_Atti($Msg_op=""){
$messages[1] = __('Item added.');
$messages[2] = __('Item deleted.');
$messages[3] = __('Item updated.');
$messages[4] = __('Item not added.');
$messages[5] = __('Item not updated.');
$messages[6] = __('Item not deleted.');
$messages[7] = __('Impossibile cancellare un Atto che contiene Allegati<br />Cancellare prima gli Allegati e poi riprovare');
$N_A_pp=10;
//Paginazione Inizializzazione
	if (!isset($_REQUEST['Pag'])){
		$Da=0;
		$A=$N_A_pp;
	}else{
		$Da=($_REQUEST['Pag']-1)*$N_A_pp;
		$A=$N_A_pp;
	}
	$TotAtti=ap_get_all_atti(0,0,0,'',0,0,'',0,0,true);
//Gestione Messaggi di stato
if (isset($_REQUEST['message'])) 
	$msg = (int) $_REQUEST['message'];
if ($Msg_op!=""){
	$msg =9;
	$messages[9]=str_replace("%%br%%","<br />",$Msg_op);
}
// Inizio interfaccia
	echo' <div class="wrap">
	      <img src="'.Albo_URL.'/img/atti32.png" alt="Icona Atti" style="display:inline;float:left;margin-top:10px;"/>
<h2 style="margin-left:40px;">Atti <a href="?page=atti&amp;action=new-atto" class="add-new-h2">Aggiungi nuovo</a></h2>';
	if ( $msg ) {
		echo '<div id="message" class="updated"><p>'.$messages[$msg].'</p></div>';
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), $_SERVER['REQUEST_URI']);
	}
	if ($_REQUEST['action']=="edit"){
		$risultato=ap_get_categoria($_REQUEST['id']);
		$edit=True;
	}else{
		$edit=False;
	}
	if(isset($_REQUEST['orderby']))
		$orderby=$_REQUEST['orderby'];
	else
		$orderby='';
	if(isset($_REQUEST['order']))
		$order=$_REQUEST['order'];
	else
		$order='desc';
	if($orderby=='')
		$ordina="Data DESC";
	else
		$ordina=$orderby." ".$order;

	echo '
		<br class="clear" />
		<div id="col-container">
		<div class="col-wrap">
		<h3>Elenco Atti gi&agrave; codificati</h3>';
//Paginazione
	if ($TotAtti>$N_A_pp){
	    $Para='';
	    foreach ($_REQUEST as $k => $v){
			if ($k!="Pag")
				if ($Para=='')
					$Para.=$k.'='.$v;
				else
					$Para.='&amp;'.$k.'='.$v;
		}
		if ($Para=='')
			$Para="?Pag=";
		else
			$Para="?".$Para."&amp;Pag=";
		$Npag=(int)$TotAtti/$N_A_pp;
		if ($TotAtti%$N_A_pp>0){
			$Npag++;
		}
		echo '  	<div class="tablenav" style="float:right;margin-right:20px;margin-bottom:20px;" id="risultati">
		<div class="tablenav-pages">
    		<p><strong>N. Atti '.$TotAtti.'</strong>&nbsp;&nbsp; Pagine';
    	if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']>1 ){
			$Pagcur=$_REQUEST['Pag'];
			$PagPre=$Pagcur-1;
			echo '&nbsp;<a href="'.$Para.$PagPre.'" class="next page-numbers">&laquo;</a>';
		}else{
			$Pagcur=1;
		}
		for($i=1;$i<=$Npag;$i++){
			if ($i==$Pagcur){
				echo '&nbsp;<span class="page-numbers current">'.$i.'</span>';
			}else{
				echo '&nbsp;<a href="'.$Para.$i.'" class="page-numbers" >'.$i.'</a>';		
			}
		}
		$PagSuc=$Pagcur+1;
	   	if ($PagSuc<=$Npag){
			echo '&nbsp;<a href="'.$Para.$PagSuc.'" class="next page-numbers">&raquo;</a>';
		}
	echo'			</p>
    	</div>
	</div>';
	}		
//Fine Paginazione	
	echo '	
		<table class="widefat" id="elenco-atti"> 
	    <thead>
	    	<tr>
	        	<th scope="col" style="width:20px;">Operazioni</th>
	        	<th scope="col" style="width:10px;">Stato</th>
	        	<th scope="col" style="width:100px;">Ente</th>
	        	<th scope="col">Numero</th>';
	    if ($orderby=="Data"){
	    	if($order=="asc"){
			 	$titolo="Ordina in modo decrescente in base alla Data Creazione Atto";
			 	$altorder= "desc";
			}else{
				$titolo="Ordina in modo crescente in base alla Data Creazione Atto";
				$altorder= "asc";
			}
		}else{
				$titolo="Ordina in modo crescente in base alla Data di Inizio Pubblicazione";
				$altorder= "desc";
		}
	    echo ' 	<th scope="col" ><a href="?page=atti&orderby=Data&order='.$altorder.'" title="'.$titolo.'">Del</a></th>
	        	<th scope="col">Riferimento</th>
	        	<th scope="col">Oggetto</th>';
	    if ($orderby=="DataInizio"){
	    	if($order=="asc"){
			 	$titolo="Ordina in modo decrescente in base alla Data di Inizio Pubblicazione";
			 	$altorder= "desc";
			}else{
				$titolo="Ordina in modo crescente in base alla Data di Inizio Pubblicazione";
				$altorder= "asc";
			}
		}else{
				$titolo="Ordina in modo crescente in base alla Data di Inizio Pubblicazione";
				$altorder= "desc";
		}
	      echo '   <th scope="col" style="width:40px;"><a href="?page=atti&orderby=DataInizio&order='.$altorder.'"  title="'.$titolo.'">Inizio/Fine Pub.</a></th>
	        	<th scope="col">Categoria</th>
			</tr>
	    </thead>
	    <tbody id="the-list">';
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$lista=ap_get_all_atti(0,0,0,'', 0,0,$ordina,$Da,$A); 
	if ($lista){
		foreach($lista as $riga){
		$categoria=ap_get_categoria($riga->IdCategoria);
		$cat=$categoria[0]->Nome;
		$NumeroAtto=ap_get_num_anno($riga->IdAtto);
		$Ente=ap_get_ente($riga->Ente);
		$Ente=$Ente->Nome; 
		if($riga->DataAnnullamento!='0000-00-00')
			$Annullato='style="background-color: '.$coloreAnnullati.';" title="'.$riga->MotivoAnnullamento.'" ';
		else
			$Annullato='';
		echo '<tr '.$Annullato.'>
	        	<td style="width:80px;">';
		if ($NumeroAtto ==0 )
			echo'	<a href="?page=atti&amp;action=delete-atto&amp;id='.$riga->IdAtto.'" rel="'.$riga->Oggetto.'" tag="" class="ac">
						<img src="'.Albo_URL.'/img/cross.png" alt="Delete" title="Delete" />
					</a>
					<a href="?page=atti&amp;action=edit-atto&amp;id='.$riga->IdAtto.'" ">
						<img src="'.Albo_URL.'/img/edit.png" alt="Edit" title="Edit" />
					</a>
					<a href="?page=atti&amp;action=allegati-atto&amp;id='.$riga->IdAtto.'" ">
						<img src="'.Albo_URL.'/img/up.png" alt="Attach" title="Allegati" />
					</a>
					<a href="?page=atti&amp;action=view-atto&amp;id='.$riga->IdAtto.'"  >
						<img src="'.Albo_URL.'/img/view.png" alt="View" title="View" />
					</a>';
		else{
			echo '	<a href="?page=atti&amp;action=view-atto&amp;id='.$riga->IdAtto.'"  >
						<img src="'.Albo_URL.'/img/view.png" alt="View" title="View" />
					</a>';
			if ($Annullato!='')
				echo ' <strong><span style="color:red;">Annullato</span></strong>';
			else{
			if ((cvdate($riga->DataInizio) <= cvdate(date("Y-m-d"))) and (cvdate($riga->DataFine) >= cvdate(date("Y-m-d"))) and current_user_can('admin_albo'))
				echo ' <a class="annullaatto" href="?page=atti&amp;action=annulla-atto&amp;id='.$riga->IdAtto.'"  rel="'.$riga->Oggetto.'">
						<img src="'.Albo_URL.'/img/annullato32.png" alt="Annulla atto" title="Annulla atto" />
					</a>';
			}
		}
		echo '				</td>
				<td>';
		if ($NumeroAtto == 0)
			if  (current_user_can('admin_albo')){
				echo '<a href="?page=atti&amp;action=approva-atto&amp;id='.$riga->IdAtto.'"  >
<img src="'.Albo_URL.'/img/approva32.png" alt="Approva" title="Approva" style="margin-top:-4px;"/>
					</a>';
			}else
				echo "Bozza";
		else{
		 	$Stato="Pub.";
			if (cvdate($riga->DataFine) < cvdate(date("Y-m-d")))
				$Stato="Scaduto";
			echo $Stato;		
		}
		echo '  </td> 
		        <td>
					'.$Ente.'
				</td>
		        <td>
					'.$NumeroAtto.'/'.$riga->Anno .'
				</td>
				<td>
					'.VisualizzaData($riga->Data) .'
				</td>
				<td>
					'.$riga->Riferimento .'
				</td>
				<td>
					'.$riga->Oggetto .'  
				</td>
				<td>
					'.VisualizzaData($riga->DataInizio) .'  
					'.VisualizzaData($riga->DataFine) .'  
				</td>
				<td>
					'.$cat .'  
				</td>
			</tr>'; 
		}
	} else {
			echo '<td colspan="6">Nessun Atto Codificato</li>';
	}
	echo '</td>
		</tr>
	 </tbody>
    </table>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->	';
}

?>
